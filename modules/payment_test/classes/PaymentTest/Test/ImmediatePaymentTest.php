<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.12.2014
 * Time: 10:49
 */


namespace PaymentTest\Test;


use PaymentTest\BaseTest;

class ImmediatePaymentTest extends BaseTest
{

    /**
     * @inheritdoc
     */
    protected function submitTransactionForm($orderId)
    {
        // Go to order and pay it
        $crawler = $this->client->request('GET', $this->host . '/checkout/payment/' . $orderId . $this->getMacFieldsQuery());
        $formNode = $crawler->filter('.checkout-payment')->filter('form'); // #paymentForm
        $form = $formNode->form();
        $form = $this->augmentSubmitForm($form);
        $crawler = $this->client->submit($form);

        // Fill credit card form
        $creditForm = $crawler->filter('form')->first()->form();
        $creditForm->setValues([
            'CARD' => $this->getCard(),
            'EXP' => $this->getExpiresMonth(),
            'EXP_YEAR' => $this->getExpiresYear(),
            'NAME' => $this->getName(),
            'CVC2' => $this->getCvv2()
        ]);

        $crawler = $this->client->submit($creditForm);

        // Invisible processing form
        $processingForm = $crawler->filter('form')->first()->form();
        if ($processingForm->has('ACTION')) {
            return $crawler;
        }
        $crawler = $this->client->submit($processingForm);

        $uriParts = parse_url($this->client->getHistory()->current()->getUri());

        // Check whether there is a need to enter 3DSecure code
        if (preg_match('#^/way4acs#', $uriParts['path'])) {
            $counter = 3;

            do {
                // 3DSecure
                $secureFormNode = $crawler->filter('form[name=frm]')->first();

                if (!$secureFormNode->count()) {
                    break;
                }

                $secureForm = $secureFormNode->form();

                $crawler = $this->client->submit($secureForm, [
                    'password' => $this->code3DSecure,
                    'formaction' => $this->cancel3DSecure ? 'pa.exit' : 'pa.submit'
                ]);

                $uriParts2 = parse_url($this->client->getHistory()->current()->getUri());
                $counter--;

            } while (preg_match('#^/way4acs#', $uriParts2['path']));

            // UI Dispatcher form
            $uiDispatcherForm = $crawler->filter('form')->first()->form();
            $crawler = $this->client->submit($uiDispatcherForm);

            //var_dump($this->client->getHistory(), parse_url($this->client->getHistory()->current()->getUri())); exit;

        } else {
            // Pass-through helper form
            $secureFormNode = $crawler->filter('form[name=downloadForm]')->first();
            if (!$secureFormNode->count()) {
                return $crawler;
            }
            $secureForm = $secureFormNode->form();
            $crawler = $this->client->submit($secureForm);
        }

        return $crawler;
    }
}