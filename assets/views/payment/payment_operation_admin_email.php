Была произведена попытка провести финансовую операцию по заказу №<?php echo $data['ORDER']; ?>.

<?php /** @var \App\Model\PaymentOperation $paymentOperation */ ?>
Тип операции: <?php echo $_format_payment_op(trim($data['TRTYPE'])) . "\n"; ?>
Дата: <?php echo date('Y.m.d H:i:s') . "\n"; ?>
Сумма: <?php echo $_format_price($data['AMOUNT']) . "\n"; ?>

Данные:
<?php echo $requestData; ?>

Ошибка:
<?php echo $error; ?>

Trace:
<?php echo $trace; ?>

