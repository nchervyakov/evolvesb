<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 12.12.2014
 * Time: 17:16
 */


namespace App\Model;
use App\Payment\Request;

/**
 * Class PaymentOperation
 * @package App\Model
 * @property int $id
 * @property int $payment_id
 * @property int $transaction_type
 * @property number $amount
 * @property string $currency
 * @property string|number $order
 * @property string $description
 * @property string|null $rrn
 * @property int|null $rc
 * @property int|null $action
 * @property string $merchant_name
 * @property string $merchant_url
 * @property string $merchant_gmt
 * @property string $country
 * @property string $merchant
 * @property string $terminal
 * @property string $email
 * @property string $back_reference
 * @property string $status
 * @property string $timestamp
 * @property string $nonce
 * @property string $brands
 */
class PaymentOperation extends BaseModel
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public $table = 'tbl_payment_operations';

    protected $belongs_to = [
        'payment' => [
            'model' => 'Payment',
            'key' => 'payment_id'
        ]
    ];

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED
        ];
    }

    /**
     * @param number $amount Order total amount
     */
    public function setAmount($amount)
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than zero. Provided: $amount");
        }

        $this->amount = number_format((float) $amount, 2, '.', '');
    }

    /**
     * @return float Order total amount
     */
    public function getAmount()
    {
        return (float) $this->amount;
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

        $this->currency = $currency;
    }

    /**
     * @return string Order currency code of length 3
     */
    public function getCurrency()
    {
        return (string) $this->currency;
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

        $this->order = $orderId;
    }

    /**
     * @return int Shop internal order number
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $description Order description of length from 1 to 80
     */
    public function setDescription($description)
    {
        if (is_object($description) || is_array($description)) {
            throw new \InvalidArgumentException('Description must be a string.');
        }

        $this->description = mb_substr($description, 0, 80, 'utf-8');
    }

    /**
     * @return string Order description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $email Cardholder e-mail for invoice
     */
    public function setCustomerEmail($email)
    {
        if (is_object($email) || is_array($email)) {
            throw new \InvalidArgumentException('Email must be a string.');
        }

        $this->email = mb_substr($email, 0, 80, 'utf-8');
    }

    /**
     * @return string Cardholder e-mail for invoice
     */
    public function getCustomerEmail()
    {
        return $this->email;
    }

    /**
     * @param string $name
     */
    public function setMerchantName($name)
    {
        if (is_object($name) || is_array($name)) {
            throw new \InvalidArgumentException('Merchant name must be a string.');
        }

        $this->merchant_name = mb_substr($name, 0, 80, 'utf-8');
    }

    /**
     * @return string
     */
    public function getMerchantName()
    {
        return $this->merchant_name;
    }

    /**
     * @param string $url Merchant web site URL
     */
    public function setMerchantUrl($url)
    {
        if (is_object($url) || is_array($url)) {
            throw new \InvalidArgumentException('Merchant must be a string.');
        }

        $this->merchant_url = mb_substr($url, 0, 250, 'utf-8');
    }

    /**
     * @return string Merchant web site URL
     */
    public function getMerchantUrl()
    {
        return $this->merchant_url;
    }

    /**
     * @param string $id Merchant ID assigned by bank
     */
    public function setMerchant($id)
    {
        if (is_object($id) || is_array($id)) {
            throw new \InvalidArgumentException('Merchant id must be a string.');
        }

        $this->merchant = mb_substr($id, 0, 15, 'utf-8');
    }

    /**
     * @return string Merchant ID assigned by bank
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @param string $terminal Merchant Terminal ID assigned by bank
     */
    public function setTerminal($terminal)
    {
        if (is_object($terminal) || is_array($terminal)) {
            throw new \InvalidArgumentException('Terminal must be a string.');
        }

        $this->terminal = mb_substr($terminal, 0, 15, 'utf-8');
    }

    /**
     * @return string Merchant Terminal ID assigned by bank
     */
    public function getTerminal()
    {
        return $this->terminal;
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

        $this->timestamp = $timestamp;
    }

    /**
     * @return number|string Timestamp in format YYYYMMDDHHMMSS
     */
    public function getTimestamp()
    {
        return $this->timestamp;
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

        $this->merchant_gmt = $offset;
    }

    /**
     * @return string|number Merchant UTC/GMT time zone offset (e.g. -3)
     */
    public function getMerchantGMTTimezoneOffset()
    {
        return $this->merchant_gmt;
    }

    /**
     * @param int $type
     */
    public function setTransactionType($type)
    {
        if (!is_int($type) && !in_array((int) $type, Request::getRequestTypes())) {
            throw new \InvalidArgumentException('Request type must be one of the following values: ' . implode(', ', Request::getRequestTypes()));
        }

        $this->transaction_type = (int) $type;
    }

    /**
     * @return int
     */
    public function getTransactionType()
    {
        return $this->transaction_type;
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

        $this->country = strtoupper($country);
    }

    /**
     * @return string Merchant shop country code (2 letters)
     */
    public function getCountry()
    {
        return $this->country;
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

        $this->brands = implode(',', $brandsArray);
    }

    /**
     * @return string Comma-separated list of brands, or array thereof (e.g. VISA,ECMC).
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param string $url Merchant URL for posting authorization result
     */
    public function setBackReference($url)
    {
        if (is_object($url) || is_array($url)) {
            throw new \InvalidArgumentException('Return URL must be a valid URL string.');
        }

        $this->back_reference = mb_substr($url, 0, 250, 'utf-8');;
    }

    /**
     * @return string Merchant URL for posting authorization result
     */
    public function getBackReference()
    {
        return $this->back_reference;
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

        $this->nonce = $nonce;
    }

    /**
     * @return string Merchant nonce. Random string of length 8-32
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::getStatuses())) {
            throw new \InvalidArgumentException("Incorrect status '$status'. Status must be one of the following values: "
                . implode(', ', self::getStatuses()));
        }
        $this->status = $status;
    }
}