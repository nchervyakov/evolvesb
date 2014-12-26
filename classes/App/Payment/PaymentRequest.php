<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.12.2014
 * Time: 17:29
 */


namespace App\Payment;


use App\Model\PaymentOperation;

class PaymentRequest extends Request
{
    public function getFields()
    {
        return array_unique(array_merge(parent::getFields(), [
            'MERCHANT', 'TERMINAL', 'EMAIL', 'TIMESTAMP', 'BACKREF', 'P_SIGN'
        ]));
    }

    public function initFromOperation(PaymentOperation $operation)
    {
        parent::initFromOperation($operation);
        $this->setBrands($operation->brands);
        $this->setDescription($operation->description);
    }
}