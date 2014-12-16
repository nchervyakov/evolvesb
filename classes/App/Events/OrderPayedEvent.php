<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:40
 */


namespace App\Events;


use App\EventDispatcher\Event;
use App\Model\Order;
use App\Model\Payment;

class OrderPayedEvent extends Event
{
    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var Order
     */
    protected $order;

    function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}