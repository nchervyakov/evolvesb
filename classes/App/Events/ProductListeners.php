<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.03.2015
 * Time: 17:20
 */


namespace App\Events;


use App\Model\Order;
use App\Model\Product;
use App\Pixie;
use PHPixie\DB\PDOV\Result;

class ProductListeners
{
    public static function onProductStatusChanged(ProductStatusChangedEvent $event)
    {
        $product = $event->getProduct();
        $pixie = $event->getPixie();

        // Stop if product is not available
        if ($event->getStatusNew() != Product::STATUS_AVAILABLE || !$product->enabled || !$product->in_stock) {
            return;
        }

        // Get all not processed orders with this product included
        /** @var Result $result */
        $result = $pixie->db->query('select')->table('tbl_orders', 'o')->fields(['o.id', 'id'])
            ->join(['tbl_order_items', 'oi'], ['o.id', 'oi.order_id'])
            ->where('o.status', 'IN', $pixie->db->expr('("' . implode('", "', [Order::STATUS_NEW, Order::STATUS_WAITING_PAYMENT]) . '")'))
            ->where('and', ['oi.product_id', '=', $product->id()])
            ->where('and', ['o.customer_email', '!=', ''])
            ->execute();

        $rows = $result->as_array();
        $orderIds = [];
        foreach ($rows as $row) {
            $orderIds[] = $row->id;
        }

        if (!count($orderIds)) {
            return;
        }

        /** @var Order[] $orders */
        $orders = $pixie->orm->get('order')->where('id', 'IN', $pixie->db->expr('('.implode(',', $orderIds).')'))->find_all()->as_array();

        foreach ($orders as $order) {
            if ($order->checkProductsAreAvailable()) {
                self::sendAllProductsAvailableInOrderNotification($pixie, $order);
            }
        }
    }

    public static function sendAllProductsAvailableInOrderNotification(Pixie $pixie, Order $order)
    {
        $emailView = $pixie->view('order/all_products_available_email');
        $emailView->order = $order;
        $params = $pixie->config->get('parameters') ?: [];
        $robotEmail = $params['robot_email'] ?: 'robot@evolveskateboards.ru';
        $emailView->siteUrl = $_SERVER['HTTP_HOST'] ? 'http://' . $_SERVER['HTTP_HOST'] : $params['host'];
        $pixie->email->send(
            $order->customer_email,
            $robotEmail,
            'Вы можете оплатить ваш заказ №' . $order->uid . ' - evolveskateboards.ru',
            $emailView->render(), true
        );
    }
}