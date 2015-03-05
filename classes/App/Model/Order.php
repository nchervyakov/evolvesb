<?php

namespace App\Model;

/**
 * Class Order
 * @package App\Model
 * @property OrderAddress[]|OrderAddress $orderAddress
 * @property OrderItems[]|OrderItems $orderItems
 * @property User $customer
 * @property Payment $payment
 * @property int $id
 * @property int $customer_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $customer_firstname
 * @property string $customer_lastname
 * @property string $customer_email
 * @property string $status
 * @property string $comment
 * @property string $payment_method
 * @property string $shipping_method
 * @property number $amount
 * @property string $uid
 * @property int $success_notified
 */
class Order extends BaseModel
{
    const INCREMENT_BASE = 10000000;

    const STATUS_NEW = 'new';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    public $table = 'tbl_orders';
    public $id_field = 'id';

    protected $has_many = array(
        'orderItems' => array(
            'model' => 'orderItems',
            'key' => 'order_id'
        ),
        'orderAddress' => array(
            'model' => 'orderAddress',
            'key' => 'order_id'
        ),
    );

    protected $belongs_to = [
        'customer' => [
            'model' => 'User',
            'key' => 'customer_id'
        ]
    ];

    protected $has_one = [
        'payment' => [
            'model' => 'Payment',
            'key' => 'order_id'
        ],
    ];

    public function __construct($pixie)
    {
        parent::__construct($pixie);
        $this->created_at = date('Y-m-d H:i:s');
        $this->status = self::STATUS_NEW;
        $this->amount = 0;
    }

    public function getMyOrders()
    {
        $rows = $this->where('customer_id', $this->pixie->auth->user()->id())->find_all()->as_array();
        return $rows;
    }

    public function getMyOrdersPager($page = 1, $perPage = 10)
    {
        $query = $this->where('customer_id', $this->pixie->auth->user()->id());
        $pager = $this->pixie->paginate->orm($query, $page, $perPage);
        $currentItems = $pager;
        return $currentItems;
    }

    public function get($propertyName)
    {
        if ($propertyName == 'increment_id') {
            return $this->id + self::INCREMENT_BASE;
        }
        return null;
    }

    /**
     * @param $incrementId
     * @return mixed|Order
     * @throws \InvalidArgumentException
     */
    public function getByIncrement($incrementId)
    {
        return $this->where('uid', $incrementId)->find();
    }

    public static function getOrderStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_WAITING_PAYMENT,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED
        ];
    }

    public function getItemsDescription()
    {
        /** @var OrderItems[] $items */
        $items = $this->orderItems->find_all()->as_array();
        $description = [];
        foreach ($items as $item) {
            $description[] = $item->name . " x " . $item->qty . ', ' . number_format($item->qty * $item->price, 2) . ' руб.';
        }

        $description[] = '';
        $description[] = "Итого: " . number_format($this->amount, 2) . ' руб.';

        return implode(PHP_EOL, $description);
    }

    /**
     * @param int|string $uid
     * @return Order|null
     */
    public function getByUid($uid)
    {
        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $uid)->find();
        return $order && $order->loaded() ? $order : null;
    }

    public function isPayable()
    {
        return $this->loaded() && in_array($this->status, [Order::STATUS_NEW, Order::STATUS_WAITING_PAYMENT]);
    }

    public function isRefundable()
    {
        return $this->loaded() && in_array($this->status, [self::STATUS_SHIPPING, self::STATUS_PROCESSING, self::STATUS_COMPLETED]);
    }

    public function isCancellable()
    {
        return $this->loaded() && in_array($this->status, [self::STATUS_NEW, self::STATUS_WAITING_PAYMENT]);
    }

    /**
     * @return bool
     */
    public function checkProductsAreAvailable()
    {
        if (!$this->loaded()) {
            return false;
        }

        if (!in_array($this->status, [self::STATUS_NEW, self::STATUS_WAITING_PAYMENT])) {
            return true;
        }

        /** @var OrderItems[]|OrderItems $items */
        $items = $this->orderItems->with('product')->find_all()->as_array();
        foreach ($items as $item) {
            $product = $item->product;
            if (!$product->enabled || !$product->in_stock || $product->status == Product::STATUS_EXPECTED) {
                return false;
            }
        }

        return true;
    }
}