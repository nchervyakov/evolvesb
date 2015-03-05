<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:40
 */


namespace App\Events;


use App\EventDispatcher\Event;
use App\Model\Product;

class ProductStatusChangedEvent extends Event
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
     * @var Product
     */
    protected $product;

    function __construct(Product $order, $statusNew, $statusOld = null)
    {
        $this->product = $order;
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
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}