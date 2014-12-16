<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.12.2014
 * Time: 17:29
 */


namespace App\Payment;


use App\Model\PaymentOperation;

class RefundRequest extends Request
{
    public function getFields()
    {
        return array_unique(array_merge(parent::getFields(), [
            'ORG_AMOUNT', 'RRN', 'INT_REF', 'TERMINAL', 'TIMESTAMP', 'BACKREF', 'P_SIGN'
        ]));
    }

    public function initFromOperation(PaymentOperation $operation)
    {
        parent::initFromOperation($operation);
        $this->setRRN($operation->getRrn());
        $this->setOriginalAmount($operation->amount);
        $this->setInternalReference($operation->getInternalReference());
    }
}