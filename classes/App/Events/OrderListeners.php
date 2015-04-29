<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 29.04.2015
 * Time: 11:45
  */



namespace App\Events;


use App\Model\Order;
use App\Pixie;

class OrderListeners
{
    public static function onOrderCreated(OrderEvent $event)
    {
        $pixie = $event->getPixie();
        $order = $event->getOrder();

        self::sendOrderCreatedNotificationToAdmin($pixie, $order);
    }

    public static function sendOrderCreatedNotificationToAdmin(Pixie $pixie, Order $order)
    {
        $parameters = $pixie->config->get('parameters') ?: [];
        $robotEmail = $parameters['robot_email'] ?: 'robot@evolveskateboards.ru';
        $domain = preg_replace('#^https?://#', '', $parameters['host']);
        $adminEmails = $parameters['admin_email'] ?: [];
        $adminEmails = is_array($adminEmails) ? $adminEmails : [$adminEmails];

        $emailView = $pixie->view('order/order_created_admin_email');
        $emailView->order = $order;
        $address = $order->orderAddress->find_all()->as_array();
        $emailView->address = $address[0];
        $emailView->siteUrl = $_SERVER['HTTP_HOST'] ? 'http://' . $_SERVER['HTTP_HOST'] : $parameters['host'];

        foreach ($adminEmails as $adminEmail) {
            $pixie->email->send(
                $adminEmail,
                $robotEmail,
                'Оформлен заказ №' . $order->uid . ' - ' . $domain,
                $emailView->render(), false
            );
        }
    }
}