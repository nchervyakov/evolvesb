Была произведена неудачная попытка провести финансовую операцию по заказу №<?php echo $order->uid; ?>.

<?php /** @var \App\Model\PaymentOperation $paymentOperation */ ?>
<?php /*Тип операции: <?php echo $_format_payment_op($transaction_type) . "\n"; ?>
Дата: <?php echo date('Y.m.d H:i:s') . "\n"; ?>
Сумма: <?php echo $_format_price($amount) . "\n"; ?>
Идентификатор операции: <?php echo $paymentOperationId. "\n"; */ ?>
<br/><br/>
<?php include __DIR__ . '/_payment_receipt.php'; ?>
<br/><br/>

<?php if (isset($isAdmin) && $isAdmin) { ?>
Робот evolveskateboards.ru
<?php } else { ?>
С наилучшими пожеланиями.<br>
Команда evolveskateboards.ru
<?php } ?>