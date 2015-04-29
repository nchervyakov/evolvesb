<?php /** @var \App\Model\OrderAddress $address */?>
Оформлен заказ №<?php echo $order->uid; ?>:

Дата: <?php echo \DateTime::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('Y.m.d H:i:s')."\n"; ?>
Сумма: <?php echo $_format_price($order->amount)."\n\n"; ?>

Клиент:
Пользователь сайта: <?php echo $order->customer_firstname . ' ' . $order->customer_lastname . "\n"; ?>
Получатель: <?php echo $address->full_name . "\n"; ?>
E-mail: <?php echo $order->customer_email . "\n"; ?>
Телефон: <?php echo $address->phone . "\n"; ?>

Состав заказа:
<?php echo $order->getItemsDescription()."\n"; ?>

Адрес доставки:
Регион: <?php echo $address->region . "\n"; ?>
Город: <?php echo $address->city . "\n"; ?>
Адрес: <?php echo implode("\n", array_filter([trim($address->address_line_1), trim($address->address_line_2)])) . "\n"; ?>
Индекс: <?php echo $address->zip . "\n"; ?>

Заказ находится в обработке.
