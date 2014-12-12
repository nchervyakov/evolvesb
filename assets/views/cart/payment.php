<?php include __DIR__ . '/cart_header.php'; ?>
<div class="tab-pane active" id="step5">
    <div class="row">
        <div class="col-xs-12 col-sm-12 bg-info text-center checkout-final-notice">
            <br/>
            <h2>Заказ успешно сформирован</h2>
            <br/>
            <?php if (isset($flash)): ?>
                <div class="alert alert-warning"><?php $_($flash); ?></div>
            <?php endif; ?>
            <br/>
            <br/>
            <p><a href="/payment/pay/<?php $_($orderUid); ?>" class="btn btn-primary btn-lg">Оплатить заказ</a></p>
            <br/>
        </div>
    </div>
</div>
<?php include __DIR__ . '/cart_footer.php'; ?>
