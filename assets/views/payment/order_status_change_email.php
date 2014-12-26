Статус <?php if (isset($isAdmin) && $isAdmin) { ?>заказа вашего клиента<?php } else { ?>вашего заказа<?php }
?> №<?php echo $order->uid; ?> изменился на "<?php echo $_format_order_status($order->status) ?>".

Дата: <?php echo date('Y.m.d H:i:s') . "\n"; ?>

Состав:
<?php echo $order->getItemsDescription() . "\n"; ?>

<?php if (isset($isAdmin) && $isAdmin) { ?>
Робот evolveskateboards.ru
<?php } else { ?>
С наилучшими пожеланиями.
Команда evolveskateboards.ru
<?php } ?>