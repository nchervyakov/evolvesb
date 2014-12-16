<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 15:10
 */


namespace App\Events;

use App\EventDispatcher\Event;
use App\Model\Order;

/**
 * Class GetResponseEvent
 * @package App\Events
 */
class PaymentGatewayResponseEvent extends Event
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param Order $order
     * @param string $transactionType
     * @param array $data
     */
    public function __construct(Order $order, $transactionType, $data)
    {
        $this->order = $order;
        $this->type = $transactionType;
        $this->data = $data;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}