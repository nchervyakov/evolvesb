<?php if (isset($cart) && isset($cartItems) && count($cartItems)): ?>
    <?php foreach ($cartItems as $item):?>
        <?php
        $cProduct = $item->product;
        if (!$cProduct || !$cProduct->loaded()) {
            continue;
        }
        ?>
        <?php $pImg = $cProduct->images->find(); ?>
        <li class="cart_item">
            <a href="/products/<?php $_($cProduct->hurl); ?>">
                <div class="cart_image">
                    <img src="/products_pictures/<?php $_(isset($pImg->file_name) ? $pImg->file_name : ''); ?>"
                         alt="<?php $_($cProduct->name); ?>">
                </div>
                <div><strong><?php echo $item->qty; ?> x</strong> <?php $_($cProduct->name); ?></div>
                <strong class="price"><?php echo $_format_price($item->price * $item->qty); ?></strong>
            </a>
        </li>
    <?php endforeach; ?>

    <li class="mm-selected">
        <em class="mm-counter"><?php echo $_format_price($cart->total_price, '<span class="total-price">%PRICE%</span> %SYMBOL%'); ?></em>
        <a href="/cart">
            <strong>Итого</strong>
        </a>
    </li>

    <li class="mm-subtitle clearfix cart-buttons">
        <a href="/checkout" class="action_button right">Оформить заказ</a>
        <a href="/cart" class="action_button edit_cart left">Корзина</a>
    </li>

<?php else: ?>
    <li class="mm-label">Ваша корзина пуста</li>
<?php endif; ?>


