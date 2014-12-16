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

class OrderStatusChangedEvent extends Event
{
    /**
     * @var string
     */
    protected $statusNew;

    /**
     * @var string
     */
    protected $statusOld;

    /**
     * @var Order
     */
    protected $order;

    function __construct(Order $order, $statusNew, $statusOld = null)
    {
        $this->order = $order;
        $this->statusNew = $statusNew;
        $this->statusOld = $statusOld;
    }

    /**
     * @return string
     */
    public function getStatusNew()
    {
        return $this->statusNew;
    }

    /**
     * @return string
     */
    public function getStatusOld()
    {
        return $this->statusOld;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}