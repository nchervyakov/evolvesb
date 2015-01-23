<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:28
 */


namespace App\Events;


use App\Admin\EventListeners;
use App\Exception\HttpException;

class PaymentListeners extends EventListeners
{
    /**
     * Processes situation when the site receives correct response from the gateway
     * @param PaymentGatewayResponseEvent $event
     * @throws HttpException
     */
    public static function onOperationCompleted(PaymentGatewayResponseEvent $event) {
//        $data = $event->getData();
//        $order = $event->getOrder();
//        $transactionType = $event->getType();
    }

    public static function onPaymentPayed(PaymentPayedEvent $event)
    {
//        $order = $event->getPayment()->order;
    }

    public static function onPaymentRefunded()
    {

    }

    public static function onOrderPayed(OrderPayedEvent $event)
    {

    }

    public static function onOrderRefunded(OrderRefundedEvent $event)
    {

    }

    public static function onOrderStatusChanged(OrderStatusChangedEvent $event)
    {
        $pixie = $event->getPixie();
        $status = $event->getStatusNew();
        $order = $event->getOrder();

        $from = $pixie->config->get('parameters.robot_email', 'robot@evolveskateboards.ru');

        $emailView = $pixie->view('payment/order_status_change_email');
        $emailView->status = $status;
        $emailView->order = $order;
        $emailView->isAdmin = false;

        $pixie->email->send(
            $order->customer_email, $from,
            'Изменился статус вашего заказа №' . $order->uid . ' на "' . $pixie->view_helper()->formatOrderStatus($status) . '" на evolveskateboards.ru',
            $emailView->render()
        );

        $adminEmail = $pixie->config->get('parameters.admin_email');

        if ($adminEmail) {
            if (!is_array($adminEmail)) {
                $adminEmail = [$adminEmail];
            }

            $emailView->isAdmin = true;

            foreach ($adminEmail as $email) {
                $pixie->email->send($email, $from,
                    'Изменился статус заказа №' . $order->uid . ' на "' . $pixie->view_helper()->formatOrderStatus($status) . '" на evolveskateboards.ru',
                    $emailView->render()
                );
            }
        }
    }

    public static function onOperationSucceeded(PaymentOperationSucceededEvent $event)
    {
        $pixie = $event->getPixie();
        $order = $event->getPayment()->order;
        $operation = $event->getPaymentOperation();
        $request = $event->getRequest();

        $from = $pixie->config->get('parameters.robot_email', 'robot@evolveskateboards.ru');
        $subject =  'Проведена успешная транзакция "' . $pixie->view_helper()->formatPaymentOperation(trim($request->post('TRTYPE')))
            . '" по заказу №' . $order->uid . '" на evolveskateboards.ru';

        $emailView = $pixie->view('payment/payment_operation_success_email');
        $emailView->order = $order;
        $emailView->data = $request->post(null, []);
        $emailView->paymentOperation = $operation;
        $emailView->isAdmin = false;

        $pixie->email->send($order->customer_email, $from, $subject, $emailView->render(), true);


        if ($pixie->config->get('payment.debug_payment_gateway_response', false)) {
            $adminEmail = $pixie->config->get('parameters.admin_email');

            if ($adminEmail) {
                if (!is_array($adminEmail)) {
                    $adminEmail = [$adminEmail];
                }

                $emailView->isAdmin = true;

                $subjectAdmin = 'Проведена успешная транзакция "'
                    . $pixie->view_helper()->formatPaymentOperation($operation->transaction_type)
                    . '" по заказу №' . $order->uid . '" на evolveskateboards.ru';

                foreach ($adminEmail as $email) {
                    $emailView->requestData = self::dumpRequestDataAsString();
                    $pixie->email->send($email, $from, $subjectAdmin, $emailView->render(), true);
                }
            }
        }
    }

    public static function onOperationFailed(PaymentOperationFailedEvent $event)
    {
        $pixie = $event->getPixie();
        $order = $event->getPayment()->order;
        $operation = $event->getPaymentOperation();
        $request = $event->getRequest();

        $opType = trim($request->post('TRTYPE'));

        $from = $pixie->config->get('parameters.robot_email', 'robot@evolveskateboards.ru');
        $subject = 'Произведена неудачная попытка проведения транзакции "'
            . $pixie->view_helper()->formatPaymentOperation($opType)
            . '" по заказу №' . $order->uid . ' на evolveskateboards.ru';

        $emailView = $pixie->view('payment/payment_operation_failure_email');
        $emailView->order = $order ?: false;
        $emailView->paymentOperation = $operation ?: false;
        $emailView->isAdmin = false;
        $emailView->transaction_type = trim($request->post('TRTYPE'));
        $emailView->amount = trim($request->post('AMOUNT'));
        $emailView->data = $request->post();
        $emailView->paymentOperationId = $operation && $operation->loaded() ? $operation->id() : '-';

        $pixie->email->send($order->customer_email, $from, $subject, $emailView->render(), true);

        if ($pixie->config->get('payment.debug_payment_gateway_response', false)) {
            $adminEmail = $pixie->config->get('parameters.admin_email');

            if ($adminEmail) {
                if (!is_array($adminEmail)) {
                    $adminEmail = [$adminEmail];
                }

                $emailView->isAdmin = true;

                $subjectAdmin = 'Произведена неудачная попытка проведения транзакции "'
                    . $pixie->view_helper()->formatPaymentOperation($opType)
                    . '" по заказу №' . $order->uid . ' на evolveskateboards.ru';

                foreach ($adminEmail as $email) {
                    $emailView->requestData = self::dumpRequestDataAsString();
                    $pixie->email->send($email, $from, $subjectAdmin, $emailView->render(), true);
                }
            }
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
}