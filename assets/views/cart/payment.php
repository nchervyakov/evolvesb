<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active checkout-payment" id="step5">
    <div class="col-xs-12 col-sm-12 bg-info text-center checkout-final-notice">
        <br/>
        <h2>Ваш заказ успешно сформирован <small>(<a href="/account/orders/<?php echo $orderUid; ?>">перейти к заказу</a>)</small></h2>
        <br/>
        <?php if (isset($flash)): ?>
            <div class="alert alert-warning"><?php $_($flash); ?></div>
        <?php endif; ?>
        <br/>
        <br/>
        <?php if ($productsAvailable): ?>
            <?php if ($usePost): ?>
                <form action="<?php echo $gatewayUrl; ?>" method="post" id="paymentForm">
                    <?php foreach ($gatewayParameters as $pName => $pValue) { ?>
                        <input type="hidden" name="<?php echo $pName; ?>" value="<?php echo $pValue; ?>"/>
                    <?php } ?>

                    <input type="submit" value="Оплатить заказ" class="btn btn-danger btn-lg"/>
                    <?php include __DIR__. '/_print_receipt_button.php'; ?>
                </form>

            <?php else: ?>
                <p>
                    <a href="/payment/pay/<?php $_($orderUid); ?>" class="btn btn-danger btn-lg">Оплатить заказ</a>
                    <?php include __DIR__. '/_print_receipt_button.php'; ?>
                </p>
            <?php endif; ?>

        <?php else: ?>
            <p>В данный момент не все продукты в вашем заказе доступны. Как только они станут доступны &mdash; вы сможете оплатить заказ. </p>
            <p><a href="/account/orders/<?php echo $orderUid; ?>" class="btn btn-danger btn-lg">ПЕРЕЙТИ К ЗАКАЗУ</a></p>
        <?php endif; ?>
        <br/>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
