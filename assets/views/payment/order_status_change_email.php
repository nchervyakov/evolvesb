Статус вашего заказ №<?php echo $order->uid; ?> изменился на "<?php echo $_format_order_status($order->status) ?>".

Дата: <?php echo date('Y.m.d H:i:s') . "\n"; ?>

Состав:
<?php echo $order->getItemsDescription() . "\n"; ?>

С наилучшими пожеланиями.
Команда evolveskateboards.ru