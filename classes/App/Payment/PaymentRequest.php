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
        $this->setCountry($operation->country);
        $this->setCustomerEmail($operation->email);
        $this->setDescription($operation->description);
        $this->setMerchant($operation->merchant);
        $this->setMerchantGMTTimezoneOffset($operation->merchant_gmt);
        $this->setMerchantName($operation->merchant_name);
        $this->setMerchantUrl($operation->merchant_url);
    }
}