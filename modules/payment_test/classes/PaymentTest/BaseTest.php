<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.12.2014
 * Time: 10:26
 */


namespace PaymentTest;


use App\Pixie;
use Goutte\Client;
use Symfony\Component\DomCrawler\Form;

abstract class BaseTest
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var PaymentTest
     */
    protected $service;

    protected $cardType = 'mastercard';

    protected $card;

    protected $name;

    protected $cvv2;

    protected $expiresMonth;

    protected $expiresYear;

    protected $code3DSecure = '12345678';

    protected $cancel3DSecure = false;

    protected $amount;

    protected $email;

    protected $originalAmount;

    protected $macFields;

    protected $data;

    protected $excludedFields = [];

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;

        // Create client
        $this->client = new Client();
        $this->client->getClient()->setDefaultOption('verify', false);
        $this->client->getClient()->setDefaultOption('timeout', 120);

        $this->service = $this->pixie->paymentTest;
    }

    /**
     * @param $testId
     * @param int $operationId
     * @param array $testData
     * @return array
     */
    public function run($testId, $operationId = 0, $testData = [])
    {
        //$opData = $testData['operations'][$operationId];
        $orderId = $testData['orderId'];

        // Log in
        $crawler = $this->client->request('GET', $this->host . '/user/login');
        $loginFormNode = $crawler->filter('#loginPageForm');
        $loginForm = $loginFormNode->form();
        $this->client->submit($loginForm, ['username' => $this->username, 'password' => $this->password]);

        // Find a page with transaction form and submit it (override in subclasses)
        $crawler = $this->submitTransactionForm($orderId);
        //var_dump($this->client->getHistory(), parse_url($this->client->getHistory()->current()->getUri())); exit;

        // Go home
        $homeForm = $crawler->filter('form')->first()->form();

        $result = [
            'ACTION' => $homeForm->get('ACTION')->getValue(),
            'RC' => $homeForm->get('RC')->getValue(),
            'TRTYPE' => $homeForm->get('TRTYPE')->getValue(),
            'RRN' => $homeForm->get('RRN')->getValue(),
            'operation' => $operationId,
            'test' => $testId,
            'ORDER' => $homeForm->get('ORDER')->getValue(),
            'date' => date('m/d/Y')
        ];
        $this->client->submit($homeForm);

        ob_start();
        var_dump($this->client->getHistory());
        $result['history'] = ob_get_clean();

        return $result;
    }

    /**
     * @param Form $form
     * @return mixed
     */
    protected function augmentSubmitForm($form)
    {
        $form['AMOUNT'] = $this->getAmount();
        if ($this->originalAmount !== null) {
            $form['ORG_AMOUNT'] = $this->originalAmount;
        }
        if ($this->email !== null) {
            $form['EMAIL'] = $this->email;
        }

        if (count($this->excludedFields)) {
            foreach ($this->excludedFields as $field) {
                unset($form[$field]);
            }
        }

        return $form;
    }

    /**
     * @return string
     */
    public function getCode3DSecure()
    {
        return $this->code3DSecure;
    }

    /**
     * @param string $code3DSecure
     */
    public function setCode3DSecure($code3DSecure)
    {
        $this->code3DSecure = $code3DSecure;
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    abstract protected function submitTransactionForm($orderId);

    /**
     * @return mixed
     */
    public function getExpiresMonth()
    {
        return $this->expiresMonth;
    }

    /**
     * @param mixed $expiresMonth
     */
    public function setExpiresMonth($expiresMonth)
    {
        $this->expiresMonth = $expiresMonth;
    }

    /**
     * @return mixed
     */
    public function getCvv2()
    {
        return $this->cvv2;
    }

    /**
     * @param mixed $cvv2
     */
    public function setCvv2($cvv2)
    {
        $this->cvv2 = $cvv2;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param mixed $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getExpiresYear()
    {
        return $this->expiresYear;
    }

    /**
     * @param mixed $expiresYear
     */
    public function setExpiresYear($expiresYear)
    {
        $this->expiresYear = $expiresYear;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getOriginalAmount()
    {
        return $this->originalAmount;
    }

    /**
     * @param mixed $originalAmount
     */
    public function setOriginalAmount($originalAmount)
    {
        $this->originalAmount = $originalAmount;
    }

    /**
     * @return boolean
     */
    public function isCancel3DSecure()
    {
        return $this->cancel3DSecure;
    }

    /**
     * @param boolean $cancel3DSecure
     */
    public function setCancel3DSecure($cancel3DSecure)
    {
        $this->cancel3DSecure = $cancel3DSecure;
    }

    /**
     * @return mixed
     */
    public function getMacFields()
    {
        return $this->macFields;
    }

    /**
     * @param mixed $macFields
     */
    public function setMacFields($macFields)
    {
        $this->macFields = $macFields;
    }

    public function getMacFieldsQuery()
    {
        $fields = ['amount' => $this->amount];
        if (is_array($this->macFields)) {
            $fields['mac_fields'] = empty($this->macFields) ? 'none' : $this->macFields;
        }
        $macFieldsQuery = '?' . http_build_query($fields);
        return $macFieldsQuery;
    }

    /**
     * @return array
     */
    public function getExcludedFields()
    {
        return $this->excludedFields;
    }

    /**
     * @param array $excludedFields
     */
    public function setExcludedFields(array $excludedFields)
    {
        $this->excludedFields = $excludedFields;
    }
}