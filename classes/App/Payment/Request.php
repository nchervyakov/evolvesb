<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.12.2014
 * Time: 16:40
 */


namespace App\Payment;


use App\Model\PaymentOperation;

class Request
{
    const TR_TYPE_AUTHORIZED_PAYMENT = 0;
    const TR_TYPE_IMMEDIATE_PAYMENT = 1;
    const TR_TYPE_PAYMENT_COMPLETION = 21;
    const TR_TYPE_AUTH_CANCELLATION = 22;
    const TR_TYPE_REFUND = 24;

    /**
     * @var ParameterSet
     */
    protected $parameters;

    function __construct()
    {
        $this->parameters = new ParameterSet();
        $this->parameters['TIMESTAMP'] = gmdate('YmdHis');
        $this->parameters['TIMESTAMP'] = gmdate('YmdHis');
        $this->parameters['PAYMENT_TO'] = 'sdfsd';
    }

    public function getRequiredFields()
    {
        $fields = [
            'AMOUNT', 'CURRENCY', 'ORDER', 'MERCH_NAME', 'MERCH_URL', 'MERCH_GMT', 'COUNTRY', 'BRANDS', 'DESC', 'NONCE',
            'TRTYPE'
        ];

        if ($this->parameters->containsKey('P_SIGN')) {
            $fields[] = 'NONCE';
        }

        return $fields;
    }

    public function getAvailableFields()
    {
        return [
            'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'MERCHANT', 'CVC2', 'EMAIL', 'BACKREF',
            'P_SIGN', 'TIMESTAMP', 'MERCH_NAME', 'COUNTRY', 'MERCH_URL', 'MERCH_GMT', 'BRANDS', 'DESC', 'NONCE',
            'REMOTE_ADDR', 'HTT_REFERER', 'HTTP_USER_AGENT', 'HTTPS', 'ACCEPT'
        ];
    }

    public function getFields()
    {
        return $this->getRequiredFields();
    }

    /**
     * @param number $amount Order total amount
     */
    public function setAmount($amount)
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than zero. Provided: $amount");
        }

        $this->parameters['AMOUNT'] = number_format((float) $amount, 2, '.', '');
    }

    /**
     * @return float Order total amount
     */
    public function getAmount()
    {
        return (float) $this->parameters['AMOUNT'];
    }

    /**
     * @param string $currency Order currency code of length 3
     */
    public function setCurrency($currency)
    {
        if (is_object($currency) || is_array($currency)) {
            throw new \InvalidArgumentException('Currency must be a string.');
        }

        $currency = strtoupper(trim($currency));

        $len = mb_strlen($currency, 'utf-8');
        if ($len != 3) {
            throw new \InvalidArgumentException("The length of currency argument must be 3. Provided: $currency");
        }

        $this->parameters['CURRENCY'] = $currency;
    }

    /**
     * @return string Order currency code of length 3
     */
    public function getCurrency()
    {
        return (string) $this->parameters['CURRENCY'];
    }

    /**
     * @param number|string $orderId Shop internal order number
     */
    public function setOrder($orderId)
    {
        if (!$orderId || !is_numeric($orderId) || strlen($orderId) < 6 || strlen($orderId) > 20) {
            throw new \InvalidArgumentException("Order Id must be a number (length 6-20). Provided: $orderId");
        }

        $orderId = (int) $orderId;

        $this->parameters['ORDER'] = $orderId;
    }

    /**
     * @return int Shop internal order number
     */
    public function getOrder()
    {
        return $this->parameters['ORDER'];
    }

    /**
     * @param string $description Order description of length from 1 to 80
     */
    public function setDescription($description)
    {
        if (is_object($description) || is_array($description)) {
            throw new \InvalidArgumentException('Description must be a string.');
        }

        $this->parameters['DESC'] = mb_substr($description, 0, 80, 'utf-8');
    }

    /**
     * @return string Order description
     */
    public function getDescription()
    {
        return $this->parameters['DESC'];
    }

    /**
     * @param string $email Cardholder e-mail for invoice
     */
    public function setCustomerEmail($email)
    {
        if (is_object($email) || is_array($email)) {
            throw new \InvalidArgumentException('Email must be a string.');
        }

        $this->parameters['EMAIL'] = mb_substr($email, 0, 80, 'utf-8');
    }

    /**
     * @return string Cardholder e-mail for invoice
     */
    public function getCustomerEmail()
    {
        return $this->parameters['EMAIL'];
    }

    /**
     * @param string $name
     */
    public function setMerchantName($name)
    {
        if (is_object($name) || is_array($name)) {
            throw new \InvalidArgumentException('Merchant name must be a string.');
        }

        $this->parameters['MERCH_NAME'] = mb_substr($name, 0, 80, 'utf-8');
    }

    /**
     * @return string
     */
    public function getMerchantName()
    {
        return $this->parameters['MERCH_NAME'];
    }

    /**
     * @param string $url Merchant web site URL
     */
    public function setMerchantUrl($url)
    {
        if (is_object($url) || is_array($url)) {
            throw new \InvalidArgumentException('Merchant must be a string.');
        }

        $this->parameters['MERCH_URL'] = mb_substr($url, 0, 250, 'utf-8');
    }

    /**
     * @return string Merchant web site URL
     */
    public function getMerchantUrl()
    {
        return $this->parameters['MERCH_URL'];
    }

    /**
     * @param string $id Merchant ID assigned by bank
     */
    public function setMerchant($id)
    {
        if (is_object($id) || is_array($id)) {
            throw new \InvalidArgumentException('Merchant id must be a string.');
        }

        $this->parameters['MERCHANT'] = mb_substr($id, 0, 15, 'utf-8');
    }

    /**
     * @return string Merchant ID assigned by bank
     */
    public function getMerchant()
    {
        return $this->parameters['MERCHANT'];
    }

    /**
     * @param string $terminal Merchant Terminal ID assigned by bank
     */
    public function setTerminal($terminal)
    {
        if (is_object($terminal) || is_array($terminal)) {
            throw new \InvalidArgumentException('Terminal must be a string.');
        }

        $this->parameters['TERMINAL'] = mb_substr($terminal, 0, 15, 'utf-8');
    }

    /**
     * @return string Merchant Terminal ID assigned by bank
     */
    public function getTerminal()
    {
        return $this->parameters['TERMINAL'];
    }

    /**
     * @param number|string $timestamp Timestamp in format YYYYMMDDHHMMSS
     */
    public function setTimestamp($timestamp)
    {
        if (is_object($timestamp) || is_array($timestamp)
            || mb_strlen($timestamp, 'utf-8') != 14 || !preg_match('/^\d{14}$/', $timestamp)
        ) {
            throw new \InvalidArgumentException('Timestamp must be a string in format YYYYMMDDHHMMSS.');
        }

        $this->parameters['TIMESTAMP'] = $timestamp;
    }

    /**
     * @return number|string Timestamp in format YYYYMMDDHHMMSS
     */
    public function getTimestamp()
    {
        return $this->parameters['TIMESTAMP'];
    }

    /**
     * @param number|string $offset Merchant UTC/GMT time zone offset (e.g. -3)
     */
    public function setMerchantGMTTimezoneOffset($offset)
    {
        if (is_object($offset) || is_array($offset)
            || !preg_match('/^[-+]\d+$/', $offset)
        ) {
            throw new \InvalidArgumentException('Merchant timezone offset must be a number prefixed with sign.');
        }

        $this->parameters['MERCH_GMT'] = $offset;
    }

    /**
     * @return string|number Merchant UTC/GMT time zone offset (e.g. -3)
     */
    public function getMerchantGMTTimezoneOffset()
    {
        return $this->parameters['MERCH_GMT'];
    }

    /**
     * @param int $type
     */
    public function setTransactionType($type)
    {
        if (is_object($type) || is_array($type)
            || !preg_match('/^\d+$/', $type)
        ) {
            throw new \InvalidArgumentException('Timestamp must be a string in format YYYYMMDDHHMMSS.');
        }

        $this->parameters['TRTYPE'] = (int) $type;
    }

    /**
     * @return int
     */
    public function getTransactionType()
    {
        return $this->parameters['TRTYPE'];
    }

    /**
     * @param string $country Merchant shop country code (2 letters)
     */
    public function setCountry($country)
    {
        if (is_object($country) || is_array($country)
            || !preg_match('/^\w{2}$/', $country)
        ) {
            throw new \InvalidArgumentException('Country code must be a string of 2 letters.');
        }

        $this->parameters['COUNTRY'] = strtoupper($country);
    }

    /**
     * @return string Merchant shop country code (2 letters)
     */
    public function getCountry()
    {
        return $this->parameters['COUNTRY'];
    }

    /**
     * @param array|string $brands Comma-separated list of brands, or array thereof (e.g. VISA,ECMC).
     */
    public function setBrands($brands)
    {
        if (is_object($brands)) {
            throw new \InvalidArgumentException('Brands must be a comma-separated valuses as string (e.g. VISA,ECMC).');
        }

        if (is_array($brands)) {
            $brandsArray = $brands;
        } else {
            $brandsArray = preg_split('/\s*,\s*/', $brands, -1, PREG_SPLIT_NO_EMPTY);
        }

        $this->parameters['BRANDS'] = implode(',', $brandsArray);
    }

    /**
     * @return string Comma-separated list of brands, or array thereof (e.g. VISA,ECMC).
     */
    public function getBrands()
    {
        return $this->parameters['BRANDS'];
    }

    /**
     * @param string $url Merchant URL for posting authorization result
     */
    public function setBackReference($url)
    {
        if (is_object($url) || is_array($url)) {
            throw new \InvalidArgumentException('Return URL must be a valid URL string.');
        }

        $this->parameters['BACKREF'] = mb_substr($url, 0, 250, 'utf-8');;
    }

    /**
     * @return string Merchant URL for posting authorization result
     */
    public function getBackReference()
    {
        return $this->parameters['BACKREF'];
    }

    /**
     * @param string $nonce Merchant nonce. Random string of length 8-32
     */
    public function setNonce($nonce)
    {
        if (is_object($nonce) || is_array($nonce)) {
            throw new \InvalidArgumentException('Nonce must be a string of length 8-32.');
        }

        $length = mb_strlen($nonce, 'utf-8');

        if ($length < 8 || $length > 32) {
            throw new \InvalidArgumentException('Nonce must be a string of length 8-32.');
        }

        $this->parameters['NONCE'] = $nonce;
    }

    /**
     * @return string Merchant nonce. Random string of length 8-32
     */
    public function getNonce()
    {
        return $this->parameters['NONCE'];
    }

    /**
     * @param string $sign Merchant MAC in hexadecimal form
     */
    public function setPSign($sign)
    {
        if (is_object($sign) || is_array($sign)) {
            throw new \InvalidArgumentException('P_SIGN must be a string of length 1-256.');
        }

        $length = mb_strlen($sign, 'utf-8');

        if ($length < 1 || $length > 256) {
            throw new \InvalidArgumentException('P_SIGN must be a string of length 1-256.');
        }

        $this->parameters['P_SIGN'] = $sign;
    }

    /**
     * @return string Merchant MAC in hexadecimal form
     */
    public function getPSign()
    {
        return $this->parameters['P_SIGN'];
    }

    /**
     * @return string Raw mac source string
     */
    public function calculateMACSourceString()
    {
        //$macValues = array_values($this->parameters->getArrayCopy());
        $fields = $this->getFields();
        $macSource = [];
        foreach ($fields as $field) {
            if ($field == 'P_SIGN') {
                continue;
            }
            $length = strlen($this->parameters[$field]);
            $macSource[] = $length ? $length . $this->parameters[$field] : '-';
        }
          //var_dump($macSource);
        return implode('', $macSource);
    }

    /**
     * @return array Parameter array to put in request
     */
    public function getParametersArray()
    {
        $params = $this->parameters->getArrayCopy();

        // Put P_SIGN to the end of the param list
        if (array_key_exists('P_SIGN', $params)) {
            $pSign = $params['P_SIGN'];
            unset($params['P_SIGN']);
            $params['P_SIGN'] = $pSign;
        }

        return $params;
    }

    /**
     * @return array
     */
    public static function getRequestTypes()
    {
        return [
            self::TR_TYPE_IMMEDIATE_PAYMENT,
            self::TR_TYPE_AUTHORIZED_PAYMENT,
            self::TR_TYPE_AUTH_CANCELLATION,
            self::TR_TYPE_PAYMENT_COMPLETION,
            self::TR_TYPE_REFUND
        ];
    }

    public static function createFromPaymentOperation(PaymentOperation $operation)
    {
        if ($operation->transaction_type == Request::TR_TYPE_IMMEDIATE_PAYMENT) {
            $request = new PaymentRequest();

        } else {
            throw new \InvalidArgumentException("Incorrect operation type: " . $operation->transaction_type);
        }

        $request->setAmount($operation->amount);
        $request->setBackReference($operation->back_reference);
        $request->setBrands($operation->brands);
        $request->setCountry($operation->country);
        $request->setCurrency($operation->currency);
        $request->setCustomerEmail($operation->email);
        $request->setDescription($operation->description);
        $request->setMerchant($operation->merchant);
        $request->setMerchantGMTTimezoneOffset($operation->merchant_gmt);
        $request->setMerchantName($operation->merchant_name);
        $request->setMerchantUrl($operation->merchant_url);
        $request->setNonce($operation->nonce);
        $request->setOrder($operation->order);
        $request->setTerminal($operation->terminal);
        $request->setTimestamp($operation->timestamp);
        $request->setTransactionType($operation->transaction_type);

        return $request;
    }
}