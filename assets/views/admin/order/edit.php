<div class="panel panel-default product-page">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Вернуться к списку</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="col-xs-6 col-md-6">
            <?php
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderForm();
            ?>
        </div>
        <div class="col-xs-6 col-md-6">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th colspan="2">Элементы</th>
                    <th width="50">Кол-во</th>
                    <th width="70">Сумма</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($orderItems) : ?>
                    <?php foreach ($orderItems as $item) : ?>
                        <?php $item->product->find(); ?>
                        <tr>
                            <td class="product-image">
                                <div class="img-thumbnail-wrapper">
                                    <a href="/product/view?id=<?php echo $item->product->id(); ?>"><img src="/products_pictures/<?php $_($item->product->picture); ?>" alt=""/></a>
                                </div>
                            </td>
                            <td><a href="/product/view?id=<?php echo $item->product_id; ?>"><?php echo $item->name ?></a></td>
                            <td align="center"><?php echo $item->qty ?></td>
                            <td align="right"><?php echo $_format_price($item->price * $item->qty); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr>
                    <th colspan="4">Дополнительно</th>
                </tr>
                <tr class="info">
                    <td colspan="2">Доставка: <?php echo $order->shipping_method; ?></td>
                    <td align="right" colspan="2"><?php echo $_format_price(0); ?></td>
                </tr>
                <!--tr class="info">
                    <td colspan="2">Payment: <?php echo $order->payment_method; ?></td>
                    <td align="right" colspan="2">$0</td>
                </tr-->
                <tr class="danger">
                    <td align="right" colspan="4"><strong><?php echo $_format_price($order->amount); ?></strong></td>
                </tr>
                </tbody>
            </table>

            <?php $orderAddresses = $order->orderAddress->find_all()->as_array(); ?>

            <?php if (count($orderAddresses)) { ?>
                <h3>Адрес доставки:</h3>

                <?php
                /** @var \App\Model\OrderAddress $orderAddress */
                foreach ($orderAddresses as $orderAddress) {
                    $oaData = [
                        $orderAddress->full_name,
                        $orderAddress->address_line_1,
                        $orderAddress->address_line_2,
                        $orderAddress->city . ', ' . $orderAddress->region . ', ' . $orderAddress->country_id .  ', ' . $orderAddress->zip,
                        $orderAddress->phone ? 'Тел: ' . $orderAddress->phone : null
                    ]; ?>
                    <ul>
                        <?php echo '<li>' . implode('</li><li>', array_map($_escape, array_filter($oaData))) . '</li>'; ?>
                    </ul>
                <?php } ?>

            <?php } ?>

            <?php if ($order->payment && $order->payment->loaded()): ?>
                <h3>Платёж:</h3>
                <ul>
                    <li>Id: <?php $_($order->payment->id); ?></li>
                    <li>Заказ №: <?php $_($order->payment->order_number); ?></li>
                    <li>Валюта: <?php $_($order->payment->currency); ?></li>
                    <li>Сумма: <?php $_($order->payment->amount); ?></li>
                    <li>Статус: <?php $_($order->payment->status); ?></li>
                </ul>

                <h3>Операции:</h3>

                <?php $operations = $order->payment->operations->order_by('timestamp', 'desc')->find_all()->as_array(); ?>
                <?php if (count($operations)) : ?>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Тип</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Action</th>
                            <th>RC</th>
                            <th align="right" style="text-align: right">RRN</th>
                            <th align="right" style="text-align: right">Int. Ref.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($operations as $item) : ?>
                            <tr>
                                <td><?php echo $item->id(); ?></a></td>
                                <td><?php echo $_format_payment_op($item->transaction_type); ?></a></td>
                                <td><?php echo \DateTime::createFromFormat('YmdHis', $item->timestamp)->format('Y.m.d H:i:s'); ?></a></td>
                                <td align="center"><?php echo $item->status ?></td>
                                <td align="right"><?php echo $item->action ?></td>
                                <td align="right"><?php echo $item->rc; ?></td>
                                <td align="right"><?php echo $item->rrn; ?></td>
                                <td align="right"><?php echo $item->int_ref; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if ($order->isRefundable() || $isTesting ) { ?>
                    <br/>
                    <form action="<?php echo $gatewayUrl; ?>" method="post" id="refundForm">
                        <?php foreach ($gatewayParameters as $pName => $pValue) { ?>
                            <input type="hidden" name="<?php echo $pName; ?>" value="<?php echo $pValue; ?>"/>
                        <?php } ?>
                        <input type="submit" class="btn btn-default" value="Отменить заказ и вернуть оплату" />
                    </form>
                <?php } ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- /.panel-body -->
</div>

