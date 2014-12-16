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

        $emailView = $pixie->view('payment/order_status_change_email');
        $emailView->status = $status;
        $emailView->order = $order;

        $pixie->email->send(
            $order->customer_email,
            'robot@evolveskateboards.ru',
            'Изменился статус вашего заказа №' . $order->uid . ' на "' . $pixie->view_helper()->formatOrderStatus($status) . '" на evolveskateboards.ru',
            $emailView->render()
        );
    }

    public static function onOperationSucceeded(PaymentOperationSucceededEvent $event)
    {
        $pixie = $event->getPixie();
        $order = $event->getPayment()->order;
        $operation = $event->getPaymentOperation();

        $emailView = $pixie->view('payment/payment_operation_success_email');
        $emailView->order = $order;
        $emailView->paymentOperation = $operation;

        $pixie->email->send(
            $order->customer_email,
            'robot@evolveskateboards.ru',
            'Проведена успешная транзакция "' . $pixie->view_helper()->formatPaymentOperation($operation->transaction_type)
                    . '" по заказу №' . $order->uid . '" на evolveskateboards.ru',
            $emailView->render()
        );
    }

    public static function onOperationFailed(PaymentOperationFailedEvent $event)
    {
        $pixie = $event->getPixie();
        $order = $event->getPayment()->order;
        $operation = $event->getPaymentOperation();

        $emailView = $pixie->view('payment/payment_operation_failure_email');
        $emailView->order = $order;
        $emailView->paymentOperation = $operation;

        $pixie->email->send(
            $order->customer_email,
            'robot@evolveskateboards.ru',
            'Произведена неудачная попытка проведения транзакции "' . $pixie->view_helper()->formatPaymentOperation($operation->transaction_type)
                    . '" по заказу №' . $order->uid . ' на evolveskateboards.ru',
            $emailView->render()
        );
    }
}