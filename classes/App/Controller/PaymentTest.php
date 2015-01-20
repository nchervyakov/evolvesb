<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.12.2014
 * Time: 18:12
 */


namespace App\Controller;


use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Order;
use App\Model\OrderItems;
use App\Page;
use PaymentTest\PaymentTest as PaymentTestModule;

class PaymentTest extends Page
{
    public $mainView = 'payment_testing';

    public $paymentTestInitialized = false;

    protected $data;

    protected $groups;

    protected $tests;

    protected $cards;

    protected $rcErrors;

    protected $actionErrors;

    public function before()
    {
        $isTesting = $this->pixie->config->get('payment.testing');

        if (!$isTesting) {
            throw new NotFoundException();
        }

        $className = $this->get_real_class($this);
        $controllerName = strtolower($className);
        $this->vulninjection = $this->pixie->vulninjection->service($controllerName);
        $this->pixie->setVulnService($this->vulninjection);
        $this->vulninjection->goDown($controllerName);
        $this->initView($this->mainView);

        $session = $this->pixie->session->get();
        if (!is_array($session[PaymentTestModule::SESSION_IDENTIFIER])) {
            $this->pixie->session->set(PaymentTestModule::SESSION_IDENTIFIER, []);
        }
        //var_dump($session[PaymentTestModule::SESSION_IDENTIFIER]);exit;
        $this->loadData();

        $this->paymentTestInitialized = !!$session[PaymentTestModule::SESSION_IDENTIFIER]['initialized'];

    }

    public function action_index()
    {
        //$session = $this->pixie->session->get(PaymentTestModule::SESSION_IDENTIFIER);
        //var_dump($session); exit;

        $this->view->initialized = $this->paymentTestInitialized;
        $this->view->subview = 'payment_test/index';
        $this->view->groups = $this->groups;
        $this->view->cards = $this->cards;
        $this->view->rcErrors = $this->rcErrors;
        $this->view->actionErrors = $this->actionErrors;
        $this->view->initialized = $this->paymentTestInitialized;
    }

    public function action_init()
    {
        $ts = time();
        $orderBase = $ts * 10000;

        $session = $this->pixie->session->get(PaymentTestModule::SESSION_IDENTIFIER);
        $session = is_array($session) ? $session : [];

        $session['orderBase'] = $orderBase;
        $session['tests'] = [];

        foreach ($this->tests as $testId => $testData) {
            $order = $this->addOrder($testData['operations'][0]['amount'], $orderBase);
            $session['tests'][$testId] = [
                'orderId' => $order->uid,
                'operations' => []
            ];   //var_dump($testData);
            foreach ($testData['operations'] as $opId => $opData) {
                $session['tests'][$testId]['operations'][$opId] = [
                    'completed' => false,
                    'log' => '',
                    'status' => 'wait',
                ];
            }
        }

        $session['initialized'] = true;
        $this->pixie->session->set(PaymentTestModule::SESSION_IDENTIFIER, $session);
        $this->paymentTestInitialized = true;

        $this->jsonResponse(['success' => 1]);
    }

    public function action_run_test()
    {
        if (!$this->paymentTestInitialized) {
            throw new HttpException("Please initialize before test");
        }

        $idString = $this->request->post('id');

        if (!$idString) {
            throw new NotFoundException();
        }

        $parts = preg_split('/_/', $idString, -1, PREG_SPLIT_NO_EMPTY);
        $result = $this->pixie->paymentTest->runTest($parts[0], $parts[1]);

        $this->jsonResponse($result);
    }

    private function loadData()
    {
        $this->data = $this->pixie->paymentTest->getData();
        $this->groups = $this->pixie->paymentTest->getGroups();
        $this->tests = $this->pixie->paymentTest->getTests();

        $this->cards = $this->data['cards'];
        $this->rcErrors = $this->data['errors']['rc'];
        $this->actionErrors = $this->data['errors']['action'];
    }

    protected function addOrder($price, $orderBase)
    {
        $order = new Order($this->pixie);
        $order->amount = $price;
        $order->created_at = date('Y-m-d H:i:s');
        $order->customer_email = 'nick.chervyakov@gmail.com';
        $order->customer_firstname = 'Николай';
        $order->customer_id = 1;
        $order->customer_lastname = 'Червяков';
        $order->payment_method = 'credit_card';
        $order->shipping_method = 'post';
        $order->status = Order::STATUS_WAITING_PAYMENT;
        $order->uid = $orderBase + $price * 10;
        $order->updated_at = date('Y-m-d H:i:s');
        $order->save();

        $orderItem = new OrderItems($this->pixie);
        $orderItem->product_id = 22;
        $orderItem->order_id = $order->id();
        $orderItem->price = 38;
        $orderItem->name = "Запасной ремень All Terrain";
        $orderItem->created_at = date('Y-m-d H:i:s');
        $orderItem->updated_at = date('Y-m-d H:i:s');
        $orderItem->qty = 1;
        $orderItem->save();

        return $order;
    }
}