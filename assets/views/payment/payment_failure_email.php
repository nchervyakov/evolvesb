Была произведена неудачная попытка оплаты заказ №<?php echo $order->uid; ?>:

Дата: <?php echo \DateTime::createFromFormat('YmdHis', $paymentOperation->timestamp)->format('Y.m.d H:i:s'); ?>
Сумма: <?php $_format_price($paymentOperation->amount); ?>

Попробуйте произвести оплату ещё раз.

С наилучшими пожеланиями.
Команда evolveskateboards.ru