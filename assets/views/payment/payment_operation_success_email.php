Была произведена успешная финансовая операция по заказу №<?php echo $order->uid; ?>.

<?php /** @var \App\Model\PaymentOperation $paymentOperation */ ?>
<?php /*Тип операции: <?php echo $_format_payment_op($paymentOperation->transaction_type) . "\n"; ?>
Дата: <?php echo \DateTime::createFromFormat('YmdHis', $paymentOperation->timestamp)->format('Y.m.d H:i:s') . "\n"; ?>
Сумма: <?php echo $_format_price($paymentOperation->amount) . "\n"; ?>
Идентификатор операции: <?php echo $paymentOperation->id(). "\n"; */ ?>

<br><br>
<?php include __DIR__ . '/_payment_receipt.php'; ?>
<br><br>

С наилучшими пожеланиями.<br>
Команда evolveskateboards.ru