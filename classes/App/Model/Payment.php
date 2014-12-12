<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.12.2014
 * Time: 18:32
 */


namespace App\Model;

/**
 * Class Payment
 * @package App\Model
 *
 * @property int $id
 * @property int $order_id
 * @property int|string $order_number
 * @property number|string $amount
 * @property string $currency
 * @property string $type
 * @property string $status
 * @property int $payment_operation_id Immediate payment operation
 * @property int $auth_operation_id Authorization of payment (blocks money on client's card)
 * @property int $confirm_operation_id Confirmation of authorized request
 * @property int $cancel_operation_id Cancellation of authorized request (not completed)
 * @property int $refund_operation_id Refund made after payment was fully completed.
 *
 * @property Order $order
 * @property PaymentOperation $payment_operation
 * @property PaymentOperation $auth_operation
 * @property PaymentOperation $confirm_operation
 * @property PaymentOperation $cancel_operation
 * @property PaymentOperation $refund_operation
 */
class Payment extends BaseModel
{
    const STATUS_NEW = 'new';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_PAYED = 'payed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    const TYPE_IMMEDIATE = 'immediate';
    const TYPE_AUTHORIZED = 'authorized';

    public $table = 'tbl_payments';

    protected $has_one = [
        'payment_operation' => [
            'model' => 'PaymentOperation',
            'foreignKey' => 'payment_operation_id',
            'key' => 'id'
        ],
        'auth_operation' => [
            'model' => 'PaymentOperation',
            'foreignKey' => 'auth_operation_id',
            'key' => 'id'
        ],
        'confirm_operation' => [
            'model' => 'PaymentOperation',
            'foreignKey' => 'confirm_operation_id',
            'key' => 'id'
        ],
        'cancel_operation' => [
            'model' => 'PaymentOperation',
            'foreignKey' => 'cancel_operation_id',
            'key' => 'id'
        ],
        'refund_operation' => [
            'model' => 'PaymentOperation',
            'foreignKey' => 'refund_operation_id',
            'key' => 'id'
        ],
    ];

    protected $belongs_to = [
        'order' => [
            'model' => 'Order',
            'key' => 'order_id'
        ]
    ];

}