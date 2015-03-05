<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:00
 */


namespace App\Events;


class Events 
{
    const PAYMENT_OPERATION_COMPLETED = 'payment.operation_completed';
    const PAYMENT_OPERATION_FAILED = 'payment.operation_failed';
    const PAYMENT_OPERATION_SUCCEEDED = 'payment.operation_succeeded';

    const PAYMENT_PAYED = 'payment.payed';
    const PAYMENT_REFUNDED = 'payment.refunded';

    const ORDER_PAYED = 'order.payed';
    const ORDER_REFUNDED = 'order.refunded';
    const ORDER_STATUS_CHANGED = 'order.status_changed';

    const PRODUCT_STATUS_CHANGED = 'product.status_changed';
}