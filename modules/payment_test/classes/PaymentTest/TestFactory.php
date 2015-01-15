<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.12.2014
 * Time: 10:47
 */


namespace PaymentTest;


use App\Model\PaymentOperation;
use App\Pixie;
use PaymentTest\Test\ImmediatePaymentTest;
use PaymentTest\Test\RefundTest;

class TestFactory
{
    /**
     * @var Pixie
     */
    protected $pixie;

    protected $service;

    protected $map = [
        PaymentOperation::TR_TYPE_IMMEDIATE_PAYMENT => 'ImmediatePaymentTest',
        PaymentOperation::TR_TYPE_REFUND => 'RefundTest',
    ];

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        $this->service = $pixie->paymentTest;
    }

    /**
     * @param array $config
     * @return BaseTest
     */
    public function create($config = [])
    {
        //$session = $this->pixie->session->get(PaymentTest::SESSION_IDENTIFIER);
        //var_dump($config); exit;

        $opConfig = $config['operation'];
        $testConfig = $config['test'];
        $transactionType = $opConfig['TRTYPE'];

        $test = null;
        if ($transactionType == PaymentOperation::TR_TYPE_IMMEDIATE_PAYMENT) {
            $test = new ImmediatePaymentTest($this->pixie);

        } else if ($transactionType == PaymentOperation::TR_TYPE_REFUND) {
            $test = new RefundTest($this->pixie);
        }

        if (!$test) {
            throw new \InvalidArgumentException("Unknown type of transaction: " . $transactionType);
        }

        $test->setHost($this->service->getHost());
        $test->setUsername($this->service->getUsername());
        $test->setPassword($this->service->getPassword());
                                        //var_dump($testConfig);exit;
        $test->setCardType($testConfig['card_type']);
        $cards = $this->service->getCards();

        $cardId = is_array($opConfig['card']) ? current($opConfig['card']) : $opConfig['card'];
        $card = $cards[$testConfig['card_type']][$cardId];
        $test->setCard($card['card']);
        $test->setExpiresMonth(substr($card['expires'], 2, 2));
        $test->setExpiresYear(substr($card['expires'], 0, 2));
        $test->setName($card['name']);
        $test->setCvv2($card['cvv2']);

        $test->setAmount($opConfig['amount']);

        $opData = $opConfig['data'] ?: [];

        if ($opData['3DSecure']) {
            $test->setCode3DSecure($opData['3DSecure']);
        }

        if ($opData['CVV2']) {
            $test->setCvv2($opData['CVV2']);
        }

        if ($opData['EMAIL']) {
            $test->setEmail($opData['EMAIL']);
        }

        if ($opData['ORG_AMOUNT']) {
            $test->setOriginalAmount($opData['ORG_AMOUNT']);
        }

        if ($opData['EXP']) {
            $test->setExpiresMonth(substr($opData['EXP'], 2, 2));
            $test->setExpiresYear(substr($opData['EXP'], 0, 2));
        }

        if ($opData['card_add']) {
            $test->setCard($test->getCard() . $opData['card_add']);
        }

        if (array_key_exists('cancel_3DSecure', $opData)) {
            $test->setCancel3DSecure(!!$opData['cancel_3DSecure']);
        }

        if (array_key_exists('mac_fields', $opData)) {
            $test->setMacFields($opData['mac_fields']);
        }

        if (array_key_exists('exclude_fields', $opData) && is_array($opData['exclude_fields'])) {
            //$test->setExcludedFields($opData['exclude_fields']);
        }

        return $test;
    }
}