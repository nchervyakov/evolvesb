<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.12.2014
 * Time: 12:40
 */


namespace App\Events;


use App\EventDispatcher\Event;
use App\Model\Payment;
use App\Model\PaymentOperation;

class PaymentOperationSucceededEvent extends Event
{
    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var PaymentOperation
     */
    protected $paymentOperation;

    function __construct(Payment $payment, PaymentOperation $operation)
    {
        $this->payment = $payment;
        $this->paymentOperation = $operation;
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
}