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
            return;

        } catch (HttpException $e) {
            if ($this->order) {
                $message = "При оплате произошла ошибка. Попробуйте снова.";
                if ($this->pixie->config->get('parameters.display_errors')) {
                    $message .= "\n" . $e->getMessage() . "\n" . $e->getStatus();
                }
                $this->pixie->session->flash("payment_error", $message);
                $this->redirect("/checkout/payment/" . $this->order->uid);
                $this->execute = false;
                return;

            } else {
                throw new HttpException('', 0, $e);
            }
        }
    }

    /**
     * Handles
     * @throws ForbiddenException
     */
    public function processResponse()
    {
        if (!$this->request->method == 'POST') {
            throw new HttpException("Request type must be POST");
        }

        $data = $this->request->post();
        $orderUid = $data['ORDER'];
        $transactionType = (int) $data['TRTYPE'];

        if (!$orderUid) {
            throw new HttpException("Order uid is missing.");
        }

        /** @var Order $orderModel */
        $orderModel = $this->pixie->orm->get('Order');
        $order = $orderModel->getByUid($orderUid);

        if (!$order || !$order->payment || !$order->payment->loaded()) {
            throw new NotFoundException("Order or payment are missing.");
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
            $operation->rrn = (string) $data['RRN'];
            $operation->action = (string) $data['ACTION'];
            $operation->rc = (string) $data['RC'];
            $operation->int_ref = (string) $data['INT_REF'];
            $operation->status = PaymentOperation::STATUS_COMPLETED;
            $operation->save();
        }

        if (($data['ACTION'] == '0' || $data['ACTION'] == '1') && $data['RC'] == '00') {
            $this->pixie->dispatcher->dispatch(Events::PAYMENT_OPERATION_SUCCEEDED, new PaymentOperationSucceededEvent($order->payment, $operation));

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
            $this->pixie->dispatcher->dispatch(Events::PAYMENT_OPERATION_FAILED, new PaymentOperationFailedEvent($order->payment, $operation));
            throw new HttpException("Ошибка при выполнении транзакции.");
        }
    }


    protected function checkFieldsMatch(Order $order, PaymentModel $payment, PaymentOperation $operation, $data)
    {
        return $order->amount == $data['AMOUNT']
            && $order->uid == $data['ORDER']
            && $payment->currency == $data['CURRENCY']
            && $operation->terminal == $data['TERMINAL'];
    }


    public function action_pay() {
        $orderUid = $this->request->param('id');

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()) {
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
        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid request method: " . $this->request->method);
        }

        $orderUid = (string)$this->request->post('uid');
        if (!$orderUid) {
            throw new HttpException("Не указан номер заказа.");
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()) {
            throw new NotFoundException("Заказа с номером '$orderUid' не существует.");
        }

        if (!$order->isRefundable()) {
            throw new HttpException("Для заказа №" . $order->uid . " невозможно выполнить возврат платежа.");
        }

        $this->pixie->payments->sendRefundOrderRequest($order->id());
    }
}