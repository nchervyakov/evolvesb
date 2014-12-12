<?php

namespace App\Model;

/**
 * Class OrderItems
 * @package App\Model
 * @property int $id
 * @property int $order_id
 * @property int $cart_id
 * @property int $product_id
 * @property string $name
 * @property int $qty
 * @property number $price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Product $product
 * @property Order $order
 */
class OrderItems extends BaseModel {

    public $table = 'tbl_order_items';
    public $id_field = 'id';

    protected $belongs_to=array(
        'product' => array(
            'model' => 'Product',
            'key' => 'product_id',
        ),
        'order' => array(
            'model' => 'Order',
            'key' => 'order_id',
        )
    );

    public function getItemsTotal()
    {
        $total = 0;
        $items = $this->find_all()->as_array();
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        return $total;
    }
}