Была произведена неудачная попытка провести финансовую операцию по заказу №<?php echo $order->uid; ?>.

<?php /** @var \App\Model\PaymentOperation $paymentOperation */ ?>
Тип операции: <?php echo $_format_payment_op($transaction_type) . "\n"; ?>
Дата: <?php echo date('Y.m.d H:i:s') . "\n"; ?>
Сумма: <?php echo $_format_price($amount) . "\n"; ?>
Идентификатор операции: <?php echo $paymentOperationId. "\n"; ?>

<?php if (isset($isAdmin) && $isAdmin) { ?>
Робот evolveskateboards.ru
<?php } else { ?>
С наилучшими пожеланиями.
Команда evolveskateboards.ru
<?php } ?>