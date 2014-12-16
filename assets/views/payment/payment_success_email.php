Вы успешно оплатили заказ №<?php echo $order->uid; ?>:

Дата: <?php echo \DateTime::createFromFormat('YmdHis', $paymentOperation->timestamp)->format('Y.m.d H:i:s'); ?>
Сумма: <?php $_format_price($paymentOperation->amount); ?>
Состав заказа:
<?php echo $order->getItemsDescription(); ?>

Ваш заказ находится в обработке.

С наилучшими пожеланиями.
Команда evolveskateboards.ru