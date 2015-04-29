<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 29.04.2015
 * Time: 11:44
  */



namespace App\Events;


use App\EventDispatcher\Event;
use App\Model\Order;

class OrderEvent extends Event
{
    protected $order;

    /**
     * OrderEvent constructor.
     * @param $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}