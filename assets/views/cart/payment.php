<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active checkout-payment" id="step5">
    <div class="col-xs-12 col-sm-12 bg-info text-center checkout-final-notice">
        <br/>
        <h2>Ваш заказ успешно сформирован</h2>
        <br/>
        <?php if (isset($flash)): ?>
            <div class="alert alert-warning"><?php $_($flash); ?></div>
        <?php endif; ?>
        <br/>
        <br/>
        <?php if ($usePost): ?>
            <form action="<?php echo $gatewayUrl; ?>" method="post">
                <?php foreach ($gatewayParameters as $pName => $pValue) { ?>
                    <input type="hidden" name="<?php echo $pName; ?>" value="<?php echo $pValue; ?>"/>
                <?php } ?>

                <input type="submit" value="Оплатить заказ" class="btn btn-danger btn-lg"/>
            </form>
        <?php else: ?>
            <p><a href="/payment/pay/<?php $_($orderUid); ?>" class="btn btn-danger btn-lg">Оплатить заказ</a></p>
        <?php endif; ?>
        <br/>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
