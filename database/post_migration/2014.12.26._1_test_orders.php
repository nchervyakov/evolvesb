<?php
/**
 * @var \App\Pixie $pixie
 */
$testPrices = [
    21.1,
    21.2,
    21.3,
    21.4,
    21.5,
    21.6,
    21.7,
    21.8,
    21.9,
    22.0,
    22.1,
    22.2,
    22.3,
    22.4,
    22.5,
    22.6,
    22.7,
    22.8,
    22.9,
    23.1,

    26.1,
    26.2,
    26.3,
    26.4,
    26.5,
    26.6,
    26.7,
    26.8,
    26.9,
    27.0,
    27.1,
    27.2,
    27.3,
    27.4,
    27.5,
    27.6,
    27.7,
    27.8,
    27.9,
    28.1,

    30.1,
    30.2,
    30.3,
    30.4,
    30.5,
    31.1,
    31.2,
    31.3,
    31.4,
    31.5,
];

$ts = time();
$orderBase = $ts * 10000;

$addOrder = function ($price) use ($pixie, &$orderBase) {
    $order = new \App\Model\Order($pixie);
    $order->amount = $price;
    $order->created_at = date('Y-m-d H:i:s');
    $order->customer_email = 'nick.chervyakov@gmail.com';
    $order->customer_firstname = 'Николай';
    $order->customer_id = 1;
    $order->customer_lastname = 'Червяков';
    $order->payment_method = 'credit_card';
    $order->shipping_method = 'post';
    $order->status = \App\Model\Order::STATUS_WAITING_PAYMENT;
    $order->uid = $orderBase + $price * 10;
    $order->updated_at = date('Y-m-d H:i:s');
    $order->save();

    $orderItem = new \App\Model\OrderItems($pixie);
    $orderItem->product_id = 22;
    $orderItem->order_id = $order->id();
    $orderItem->price = 38;
    $orderItem->name = "Запасной ремень All Terrain";
    $orderItem->created_at = date('Y-m-d H:i:s');
    $orderItem->updated_at = date('Y-m-d H:i:s');
    $orderItem->qty = 1;
    $orderItem->save();
};

foreach ($testPrices as $price) {
    $addOrder($price);
}