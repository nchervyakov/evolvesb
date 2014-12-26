<div class="container order-page main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/account#my-orders" title="Личный кабинет" itemprop="url"><span itemprop="title">Личный кабинет</span></a></span> &nbsp; / &nbsp;
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/account/orders" title="Заказы" itemprop="url"><span itemprop="title">Заказы</span></a></span> &nbsp; / &nbsp;
        Заказ №<?php echo $order->uid; ?>
    </div>
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Заказы</h1>
    </div>
    <div class="sixteen columns page">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Информация о заказе</h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Номер: </dt>
                    <dd><?php echo $order->uid; ?></dd>

                    <dt>Дата</dt>
                    <dd><?php echo date('Y.m.d', strtotime($order->created_at)); ?></dd>

                    <dt>Статус</dt>
                    <dd><?php echo $_order_status($order->status); ?></dd>

                    <dt>Итого</dt>
                    <dd><span class="label label-danger"><?php echo $_format_price($order->amount); ?></span></dd>
                </dl>

                <?php if ($order->status == \App\Model\Order::STATUS_PROCESSING || ($this->pixie->config->get('payment.testing') && $order->status == \App\Model\Order::STATUS_PROCESSING)) { ?>
                    <br/>
                    <form action="/payment/refund" method="post">
                        <input type="hidden" name="uid" value="<?php echo $order->uid; ?>" />
                        <input type="submit" class="btn btn-default" value="Отменить заказ и вернуть оплату" />
                    </form>

                <?php }

                if ($order->status == \App\Model\Order::STATUS_WAITING_PAYMENT || $this->pixie->config->get('payment.testing')) { ?>
                    <br/>
                    <a href="/checkout/payment/<?php echo $order->uid; ?>" class="btn btn-primary btn-lg">Оплатить</a>

                <?php } ?>

                <?php if ($order->isCancellable()): ?>
                    <form action="/account/cancel_order" method="post" class="cancel-order-form">
                        <input type="hidden" name="uid" value="<?php echo $order->uid; ?>" />
                        <input type="submit" class="btn btn-default" value="Отменить заказ" />
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="2">Товар</th>
                    <th width="50">Количество</th>
                    <th width="70">Стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items) : ?>
                    <?php foreach ($items as $item) : ?>
                        <?php $item->product->find(); ?>
                        <tr>
                            <td class="product-image">
                                <div class="img-thumbnail-wrapper">
                                    <a href="/product/view?id=<?php echo $item->product->id(); ?>"><img src="/products_pictures/<?php $_($item->product->picture); ?>" alt=""/></a>
                                </div>
                            </td>
                            <td><a href="/product/view?id=<?php echo $item->product_id; ?>"><?php echo $item->name; ?></a></td>
                            <td align="center"><?php echo $item->qty ?></td>
                            <td align="right"><?php echo $_format_price($item->price * $item->qty); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!--tr>
                    <th colspan="4">Services</th>
                </tr>
                <tr class="info">
                    <td colspan="2">Shipping: <?php echo $order->shipping_method; ?></td>
                    <td align="right" colspan="2">$0</td>
                </tr>
                <tr class="info">
                    <td colspan="2">Payment: <?php echo $order->payment_method; ?></td>
                    <td align="right" colspan="2">$0</td>
                </tr-->
                <tr class="danger">
                    <td align="right" colspan="4"><strong><?php echo $_format_price($order->orderItems->getItemsTotal()); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>