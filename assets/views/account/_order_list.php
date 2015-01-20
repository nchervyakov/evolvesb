<?php
if (count($myOrders) == 0) {
    if ($pager->num_items == 0) {
        echo '<h2>У вас нет заказов.</h2>';
    } else {
        echo '<h2>Неверная страница.</h2>';
    }
}
?>

<?php if (count($myOrders) > 0): ?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Заказ №</th>
        <th>Дата</th>
        <!--th>Способ оплаты</th>
        <th>Способ доставки</th-->
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($myOrders as $order) : ?>
    <tr>
        <td><a href="/account/orders/<?php $_($order->uid);?>"><?php $_($order->uid);?></a></td>
        <td><?php echo date('Y.m.d', strtotime($order->created_at));?></td>
        <!--td><?php $_($order->payment_method);?> </td>
        <td><?php $_($order->shipping_method);?> </td-->
        <td><?php echo $_order_status($order->status);?>
            <?php if ($order->status == \App\Model\Order::STATUS_WAITING_PAYMENT):?>
                <a href="/checkout/payment/<?php echo $order->uid; ?>" class="btn btn-sm btn-default">Оплатить</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php $_pager($pager, '/account/orders/?page=#page#'); ?>
