<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.12.2014
 * Time: 16:12
 */


namespace App\Payment;


use App\Model\Order;
use App\Model\Payment;
use App\Model\PaymentOperation;
use App\Pixie;
use App\Utils\BigInteger;

/**
 * Class PaymentService
 * @package App\Payment
 */
class PaymentService
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var string
     */
    protected $gatewayUrl;

    /**
     * @var string
     */
    protected $returnUrl;

    /**
     * @var string
     */
    protected $macSecret = 'Very secret key!';

    /**
     * @var string
     */
    protected $terminal;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $merchantName;

    /**
     * @var string
     */
    protected $merchantUrl;

    /**
     * @var string
     */
    protected $merchantGMT;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $component1;

    /**
     * @var string
     */
    protected $component2;

    /**
     * @var string
     */
    protected $brands;

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        $config = $this->pixie->config->get('payment');

        $this->gatewayUrl = $config['gateway_url'];
        $this->returnUrl = $config['return_url'];
        $this->macSecret = $config['mac_secret'];
        $this->terminal = $config['terminal'];
        $this->merchantName = $config['merchant_name'];
        $this->merchantId = $config['merchant_id'];
        $this->merchantUrl = $config['merchant_url'];
        $this->merchantGMT = $config['merchant_gmt'];
        $this->currency = $config['currency'];
        $this->country = $config['country'];
        $this->brands = $config['brands'];
        $this->component1 = $config['mac_component1'];
        $this->component2 = $config['mac_component2'];
    }

    /**
     * @param int $orderId
     * @param null $errorCallback
     * @throws \RuntimeException
     */
    public function sendPayOrderRequest($orderId, $errorCallback = null)
    {
        $order = $this->getOrder($orderId);

        if (!in_array($order->status, [Order::STATUS_NEW, Order::STATUS_WAITING_PAYMENT])) {
            if (is_callable($errorCallback)) {
                call_user_func($errorCallback, $order);
                return;
            }
        }

        $payment = $order->payment;
        if (!$payment || !$payment->loaded()) {
            $payment = $this->createOrderPayment($order);
        }

        $operation = $payment->payment_operation;
        if (!$operation || !$operation->loaded() || $operation->status != PaymentOperation::STATUS_COMPLETED) {
            $operation = $this->createImmediatePaymentOperation($payment);
            $payment->payment_operation_id = $operation->id();
            $payment->save();
        }

        $operation->setStatus(PaymentOperation::STATUS_IN_PROGRESS);
        $operation->save();
        $request = $this->createRequestFromPaymentOperation($operation);
        $url = $this->gatewayUrl . '?' . http_build_query($request->getParametersArray());
        header('Location: ' . $url);
        exit;
    }

    /**
     * @param int $orderId
     * @return Order
     */
    protected function getOrder($orderId)
    {
        /** @var Order $order */
        $order = $this->pixie->orm->get('Order', $orderId);

        if (!$order || !$order->loaded()) {
            throw new \RuntimeException("Order with id=$orderId doesn't exist.");
        }

        return $order;
    }

    /**
     * Calculates request MAC code
     * @param Request $request
     * @return string
     */
    public function calculateRequestMAC(Request $request)
    {
        $source = $request->calculateMACSourceString();
        $key = $this->getKey();
        $result = hash_hmac('sha1', $source, pack("H*", $key));
        return $result;
    }

    /**
     * @return String MAC key computed from components
     */
    public function getKey()
    {
        $comp1 = new BigInteger($this->component1, 16);
        $comp2 = new BigInteger($this->component2, 16);
        $key = $comp1->bitwiseXOR($comp2)->toHex();
        return $key;
    }

    /**
     * @param Order $order
     * @return Payment
     */
    public function createOrderPayment(Order $order)
    {
        if (!$order->loaded()) {
            throw new \InvalidArgumentException("Order must be persistent. Fresh one given.");
        }
        $payment = new Payment($this->pixie);
        $payment->amount = $order->amount;
        $payment->order_number = $order->uid;
        $payment->currency = $this->currency;
        $payment->type = 'immediate';
        $payment->status = Payment::STATUS_NEW;
        $payment->order_id = $order->id();
        $payment->save();

        return $payment;
    }

    private function createImmediatePaymentOperation(Payment $payment)
    {
        if (!$payment->loaded()) {
            throw new \InvalidArgumentException("The payment must be a persistent object.");
        }

        $operation = new PaymentOperation($this->pixie);
        $operation->payment_id = $payment->id();
        $this->fillPaymentOperationWithStandardValues($operation);
        $this->fillPaymentOperationWithOrderData($operation, $payment->order);

        $operation->setTransactionType(Request::TR_TYPE_IMMEDIATE_PAYMENT);
        $operation->save();

        return $operation;
    }

    /**
     * @param PaymentOperation $operation
     */
    protected function fillPaymentOperationWithStandardValues(PaymentOperation $operation)
    {
        $operation->setBackReference($this->returnUrl);
        $operation->setBrands($this->brands);
        $operation->setCountry($this->country);
        $operation->setCurrency($this->currency);
        $operation->setMerchant($this->merchantId);
        $operation->setMerchantGMTTimezoneOffset($this->merchantGMT);
        $operation->setMerchantName($this->merchantName);
        $operation->setMerchantUrl($this->merchantUrl);
        $operation->setTerminal($this->terminal);
        $operation->setTimestamp(gmdate('YmdHis'));
        $operation->setNonce(strtoupper(uniqid()));
        $operation->setStatus(PaymentOperation::STATUS_NEW);
    }

    /**
     * @param PaymentOperation $operation
     * @param Order $order
     */
    public function fillPaymentOperationWithOrderData(PaymentOperation $operation, Order $order)
    {
        $operation->setOrder($order->uid);
        $operation->setAmount($order->amount);
        $operation->setCustomerEmail($order->customer_email);
        $operation->setDescription($order->getItemsDescription());
    }

    /**
     * @param PaymentOperation $operation
     * @return Request
     */
    public function createRequestFromPaymentOperation(PaymentOperation $operation)
    {
        return Request::createFromPaymentOperation($operation);
    }
}