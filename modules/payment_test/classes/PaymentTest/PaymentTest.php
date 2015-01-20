<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.12.2014
 * Time: 12:36
 */

namespace PaymentTest;

use App\Exception\HttpException;


class PaymentTest
{
    const SESSION_IDENTIFIER = 'payment_test';

    /**
     * @var \App\Pixie
     */
    protected $pixie;

    protected $host;

    protected $username = 'test_user';

    protected $password = '123456';

    protected $cards;

    protected $groups;

    protected $tests;

    protected $data;

    function __construct($pixie)
    {
        $this->pixie = $pixie;
        $this->host = preg_replace('#/+$#', '', $this->pixie->config->get('payment.merchant_url'));

        $data = $this->pixie->config->get(self::SESSION_IDENTIFIER);
        $this->cards = $data['cards'];
        $this->testGroups = $data['groups'];

        $this->loadData();
    }

    public function runTest($testId, $operationId = 0)
    {
        $session = $this->pixie->session->get(self::SESSION_IDENTIFIER);
        if (!$session['initialized']) {
            throw new HttpException("Тесты не инициализированы");
        }

        set_time_limit(120);

        $testConfig = $this->tests[$testId];
        $opConfig = $testConfig['operations'][$operationId];
        $testData = $session['tests'][$testId];

        $config = [
            'test' => $testConfig,
            'operation' => $opConfig
        ];

        $test = $this->pixie->paymentTestFactory->create($config);
        $result = $test->run($testId, $operationId, $testData);

        $result['success'] = $result['ACTION'] == $opConfig['expected']['ACTION'] && $result['RC'] == $opConfig['expected']['RC'];
        $result['status'] = $result['success'] ? 'pass' : 'fail';

        $sessOp = $session['tests'][$testId]['operations'][$operationId] ?: [];
        $sessOp['status'] = $result['status'];
        $sessOp['ACTION'] = $result['ACTION'];
        $sessOp['RC'] = $result['RC'];
        $sessOp['date'] = date('m/d/Y');
        $session['tests'][$testId]['operations'][$operationId] = $sessOp;

        $this->pixie->session->set(PaymentTest::SESSION_IDENTIFIER, $session);

        return $result;
    }

    protected function loadData() {
        $this->data = $this->pixie->config->get(self::SESSION_IDENTIFIER);

        $this->groups = $this->data['groups'];

        $this->tests = [];

        $session = $this->pixie->session->get(self::SESSION_IDENTIFIER);

        foreach ($this->groups as $groupName => $groupData) {
            foreach ($groupData['tests'] as $testId => $test) {

                $test['group'] = $groupName;
                $test['card_type'] = $groupData['card_type'];

                if ($session['initialized']) {
                    $test['ORDER'] = $session['tests'][$testId]['orderId'];
                }

                foreach ($test['operations'] as $opId => $operation) {
                    if ($session['initialized']) {
                        $sessOp = $session['tests'][$testId]['operations'][$opId];
                        $operation['status'] = $sessOp['status'];
                        $operation['ACTION'] = $sessOp['ACTION'];
                        $operation['RC'] = $sessOp['RC'];
                        $operation['date'] = $sessOp['date'];
                    }
                    $test['operations'][$opId] = $operation;
                }

                $groupData['tests'][$testId] = $test;
                $this->tests[$testId] = $test;
            }
            $this->groups[$groupName] = $groupData;
        }
    }

    /**
     * @return mixed
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return mixed
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}