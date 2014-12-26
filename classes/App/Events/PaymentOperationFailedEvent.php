<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:40
 */


namespace App\Events;


use App\Core\Request;
use App\EventDispatcher\Event;
use App\Model\Payment;
use App\Model\PaymentOperation;

class PaymentOperationFailedEvent extends Event
{
    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var PaymentOperation
     */
    protected $paymentOperation;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var Request
     */
    protected $request;

    function __construct(Payment $payment = null, PaymentOperation $operation = null, $message = null, $request = null)
    {
        $this->payment = $payment;
        $this->paymentOperation = $operation;
        $this->message = $message;
        $this->request = $request;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return PaymentOperation
     */
    public function getPaymentOperation()
    {
        return $this->paymentOperation;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}