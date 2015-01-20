<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.12.2014
 * Time: 10:49
 */


namespace PaymentTest\Test;


use PaymentTest\BaseTest;

class RefundTest extends BaseTest
{

    /**
     * @inheritdoc
     */
    protected function submitTransactionForm($orderId)
    {
        $crawler = $this->client->request('GET', $this->host . '/account/orders/' . $orderId . $this->getMacFieldsQuery());
        $formNode = $crawler->filter('#refundForm'); // #paymentForm
        $form = $formNode->form();
        $form = $this->augmentSubmitForm($form);
        $crawler = $this->client->submit($form);
        return $crawler;
    }
}