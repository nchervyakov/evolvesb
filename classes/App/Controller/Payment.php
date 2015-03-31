<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.12.2014
 * Time: 13:27
 */


namespace App\Controller;



use App\Events\Events;
use App\Events\OrderPayedEvent;
use App\Events\OrderRefundedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Events\PaymentOperationFailedEvent;
use App\Events\PaymentOperationSucceededEvent;
use App\Events\PaymentPayedEvent;
use App\Events\PaymentRefundedEvent;
use App\Exception\ForbiddenException;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Order;
use App\Model\Payment as PaymentModel;
use App\Model\PaymentOperation;
use App\Page;
use App\Payment\ReceiptService;
use Knp\Snappy\Pdf;

class Payment extends Page
{
    /** @var  Order */
    protected $order;

    public function before()
    {
        $this->secure();
        parent::before();
    }

    public function action_return_url()
    {
        try {
            $this->processResponse();
            $this->informAdmins();
            return;

        } catch (HttpException $e) {
            $this->informAdmins($e);

            if ($this->order) {
                $this->pixie->dispatcher->dispatch(Events::PAYMENT_OPERATION_FAILED, new PaymentOperationFailedEvent($this->order->payment, null, null, $this->request));

                $message = "При оплате произошла ошибка. Попробуйте снова.";
                if ($this->pixie->config->get('parameters.display_errors')) {
                    $message .= "\n" . $e->getMessage() . "\n" . $e->getStatus();
                    $child = $e;
                    while ($child = $child->getPrevious()) {
                        $message .= "\n" . $child->getMessage() . "\n" . $child->getCode();
                    }
                }
                $this->pixie->session->flash("payment_error", $message);
                $this->redirect("/checkout/payment/" . $this->order->uid);
                $this->execute = false;
                return;

            } else {
                throw new HttpException('Заказ отсутствует.', 0, $e);
            }

        } catch (\Exception $e) {
            $this->informAdmins($e);
            throw new \Exception('', 0, $e);
        }
    }

    /**
     * Handles
     * @throws ForbiddenException
     */
    public function processResponse()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException("Должен использоваться POST-запрос");
        }

        $data = $this->request->post();
        $orderUid = trim($data['ORDER']);
        $transactionType = (int) trim($data['TRTYPE']);

        if (!$orderUid) {
            throw new HttpException("Отсутствует идентификатор заказа.");
        }

        if (!is_numeric(trim($data['RESULT'])) || !is_numeric(trim($data['RC'])) || !trim($data['RRN']) || !trim($data['INT_REF'])) {
            throw new HttpException("Некорректный запрос.");
        }

        /** @var Order $orderModel */
        $orderModel = $this->pixie->orm->get('Order');
        $order = $orderModel->getByUid($orderUid);

        if (!$order || !$order->payment || !$order->payment->loaded()) {
            throw new NotFoundException("Заказ или платёж отсутствует.");
        }

        $this->order = $order;

        if (!in_array($transactionType, PaymentOperation::getTypes())) {
            throw new HttpException("Некорректный тип операции: $transactionType");
        }

        $operation = $order->payment->payment_operation;
        //var_dump($operation->as_array(), $order->payment->as_array()); exit;
        if (!$operation || !$operation->loaded()) {
            throw new HttpException("Отсутствует операция оплаты для платежа {$order->payment->id}");
        }

        if (!$this->checkFieldsMatch($order, $order->payment, $operation, $data)) {
            $message = 'Ошибка при проверке полей запроса.';
            if ($this->pixie->config->get('parameters.display_errors')) {
                $message = "Поля не совпадают. Указанные поля: " . var_export($data, true)
                    . "\nОжидаемые: " . implode(', ', [$order->amount, $order->payment->currency, $operation->merchant_name, $operation->terminal]);
            }
            throw new HttpException($message);
        }

        if ($operation->status != PaymentOperation::STATUS_COMPLETED) {
            $operation->rrn = (string) trim($data['RRN']);
            $operation->action = (string) trim($data['RESULT']);
            $operation->rc = (string) trim($data['RC']);
            $operation->int_ref = (string) trim($data['INT_REF']);
            $operation->status = PaymentOperation::STATUS_COMPLETED;
            $operation->save();
        }

        if ((trim($data['RESULT']) == '0' || trim($data['RESULT']) == '1') && trim($data['RC']) == '00') {
            $this->pixie->dispatcher->dispatch(Events::PAYMENT_OPERATION_SUCCEEDED, new PaymentOperationSucceededEvent($order->payment, $operation, $this->request));

            if ($transactionType == PaymentOperation::TR_TYPE_IMMEDIATE_PAYMENT) {
                if ($order->payment->isPayable()) {
                    $order->payment->status = PaymentModel::STATUS_PAYED;
                    $order->payment->save();
                    $this->pixie->dispatcher->dispatch(Events::PAYMENT_PAYED, new PaymentPayedEvent($order->payment));
                }

                if ($order->isPayable()) {
                    $order->status = Order::STATUS_PROCESSING;
                    $order->save();
                    $this->pixie->dispatcher->dispatch(Events::ORDER_STATUS_CHANGED, new OrderStatusChangedEvent($order, $order->status));
                    $this->pixie->dispatcher->dispatch(Events::ORDER_PAYED, new OrderPayedEvent($order, $order->payment));
                }

                $this->redirect('/checkout/order/' . $order->uid);

            } else if ($transactionType == PaymentOperation::TR_TYPE_REFUND) {
                if ($order->payment->isRefundable()) {
                    $order->payment->status = PaymentModel::STATUS_REFUNDED;
                    $order->payment->save();
                    $this->pixie->dispatcher->dispatch(Events::PAYMENT_REFUNDED, new PaymentRefundedEvent($order->payment));
                }

                if ($order->isRefundable()) {
                    $order->status = Order::STATUS_REFUNDED;
                    $order->save();
                    $this->pixie->dispatcher->dispatch(Events::ORDER_STATUS_CHANGED, new OrderStatusChangedEvent($order, $order->status));
                    $this->pixie->dispatcher->dispatch(Events::ORDER_REFUNDED, new OrderRefundedEvent($order, $order->payment));
                }

                $this->redirect('/account/orders/' . $order->uid);
            }

        } else {
            throw new HttpException("Ошибка при выполнении транзакции.");
        }
    }


    protected function checkFieldsMatch(Order $order, PaymentModel $payment, PaymentOperation $operation, $data)
    {
        return $order->amount == trim($data['AMOUNT'])
            && $order->uid == trim($data['ORDER'])
            && $payment->currency == trim($data['CURRENCY'])
            && $operation->terminal == trim($data['TERMINAL']);
    }


    public function action_pay() {
        $user = $this->pixie->auth->user();
        if (!$user) {
            throw new ForbiddenException();
        }

        $orderUid = $this->request->param('id');

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()
            || $user->id() != $order->customer_id
        ) {
            throw new NotFoundException("Заказа с номером $orderUid не существует.");
        }

        if (!$order->isPayable()) {
            if (in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPING, Order::STATUS_COMPLETED])) {
                throw new HttpException("Заказ №" . $order->uid . " уже оплачен.");
            } else {
                throw new HttpException("Заказ №" . $order->uid . " отменён.");
            }
        }

        $this->pixie->payments->sendPayOrderRequest($order->id());
    }

    public function action_refund()
    {
        $user = $this->pixie->auth->user();
        if (!$user) {
            throw new ForbiddenException();
        }

        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid request method: " . $this->request->method);
        }

        $orderUid = (string)$this->request->post('uid');
        if (!$orderUid) {
            throw new HttpException("Не указан номер заказа.");
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()
            || $user->id() != $order->customer_id
        ) {
            throw new NotFoundException("Заказа с номером '$orderUid' не существует.");
        }

        $isTesting = $this->pixie->config->get('payment.testing');

        if ($order->status != Order::STATUS_PROCESSING && !$isTesting) {
            throw new HttpException("Для заказа №" . $order->uid . " невозможно выполнить возврат платежа.");
        }

        $this->pixie->payments->sendRefundOrderRequest($order->id());
    }

    public function action_request_bill()
    {
        $orderUid = (string)$this->request->param('id');
        $code = $this->request->get('code');
        if ($code != $this->generatePrintCode($orderUid)) {
            throw new ForbiddenException;
        }

        $order = $this->prepareOrderAction($orderUid, false);

        $this->initView('print');
        $this->view->subview = 'payment/receipt';
        $this->view->receiptCredentials = $this->pixie->config->get('parameters.receipt');
        $this->view->order = $order;
        $this->view->print_code = $this->generatePrintCode($orderUid);
        $this->view->print = true;
    }

    public function action_print_receipt()
    {
        $orderUid = (string)$this->request->param('id');
        $order = $this->prepareOrderAction($orderUid);

        $this->initView('print');
        $this->view->subview = 'payment/receipt';
        $this->view->receiptCredentials = $this->pixie->config->get('parameters.receipt');
        $this->view->order = $order;
        $this->view->print_code = $this->generatePrintCode($orderUid);
        $this->view->print = false;
    }

    public function action_download_receipt()
    {
        $orderUid = (string)$this->request->param('id');
        $order = $this->prepareOrderAction($orderUid);

        $this->response->add_header('Content-Type: application/pdf');
        $this->response->add_header('Content-Disposition: attachment; filename="receipt_'.$order->uid.'_'.date('Y.m.d').'.pdf"');
        $this->response->body = $this->generatePdfReceipt($orderUid);
        $this->execute = false;
    }

    public function action_send_receipt()
    {
        $orderUid = (string)$this->request->param('id');
        $order = $this->prepareOrderAction($orderUid);

        if ($order->customer_email) {
            $file = $this->generatePdfReceipt($orderUid);

            // Send a message with the file
            $pixie = $this->pixie;
            $emailView = $pixie->view('payment/receipt_email');
            $emailView->order = $order;

            $message = \Swift_Message::newInstance(
                'Квитанция для оплаты заказа №' . trim($orderUid) . '" на evolveskateboards.ru',
                $emailView->render(), 'text/plain', 'utf-8'
            );
            $message->attach(\Swift_Attachment::newInstance($file, 'receipt_'.$order->uid.'_'.date('Y.m.d').'.pdf', 'application/pdf'));
            $pixie->email->send($order->customer_email, 'robot@evolveskateboards.ru', null, $message);
        }

        $this->jsonResponse(['success' => 1]);
    }

    public function action_order_qr_code()
    {
        $orderUid = (string)$this->request->param('id');
        $code = $this->request->get('code');

        if ($code != $this->generatePrintCode($orderUid)) {
            throw new ForbiddenException;
        }

        $order = $this->prepareOrderAction($orderUid, false);

        $receiptService = new ReceiptService($this->pixie);

        header('Content-Type: image/jpeg');
        $receiptService->renderQRCodeForOrder($order);
    }

    protected function prepareOrderAction($orderUid, $checkUser = true)
    {
        $user = $this->pixie->auth->user();
        if ($checkUser && !$user) {
            throw new ForbiddenException();
        }

        if (!$orderUid) {
            throw new HttpException("Не указан номер заказа.");
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()
            || ($checkUser && $user->id() != $order->customer_id)
        ) {
            throw new NotFoundException("Заказа с номером '$orderUid' не существует.");
        }

        return $order;
    }

    private function informAdmins($e = null)
    {
        $pixie = $this->pixie;
        $emailView = $pixie->view('payment/payment_operation_admin_email');

        foreach (['nick.chervyakov@gmail.com', 'dpodgurskiy@ntobjectives.com'] as $email) {
            $emailView->requestData = self::dumpRequestDataAsString();
            $emailView->data = $_POST;
            $emailView->error = '';
            $emailView->trace = '';
            $emailView->data = $this->request->post();
            if ($e instanceof \Exception) {
                $emailView->error = $e->getMessage();
                $emailView->trace = $e->getTraceAsString();
            }

            $pixie->email->send(
                $email,
                'robot@evolveskateboards.ru',
                'Проведена ' . ((int) trim($_POST['RC']) > 0 ? 'неудачная попытка осуществить транзакцию ' : 'транзакция') . ' "'
                    . $pixie->view_helper()->formatPaymentOperation(trim($_POST['TRTYPE']))
                . '" по заказу №' . trim($_POST['ORDER']) . '" на evolveskateboards.ru',
                $emailView->render(), true
            );
        }
    }

    public static function dumpRequestDataAsString()
    {
        ob_start();
        var_dump([
            'GET' => $_GET,
            'POST' => $_POST,
            'COOKIE' => $_COOKIE,
        ]);
        return ob_get_clean();
    }

    /**
     * @param $orderUid
     * @return string
     */
    protected function generatePrintCode($orderUid)
    {
        $salt = 'asdfsadf980yn98324';
        return md5($salt.$orderUid);
    }

    /**
     * @param $orderUid
     * @return string
     * @throws \Exception
     */
    protected function generatePdfReceipt($orderUid)
    {
        $snappy = new Pdf($this->pixie->config->get('parameters.wkhtmltopdf_path'));
        //$snappy->setOption('cookie', $_COOKIE);
        $snappy->setOption('viewport-size', '800x600');
        $snappy->setOption('toc', false);
        $snappy->setOption('outline', false);
        $snappy->setOption('outline-depth', 0);

        $url = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST'] . '/payment/request_bill/' . $orderUid . '?print=1'
            . '&code=' . $this->generatePrintCode($orderUid);

        return $snappy->getOutput($url);
    }
}