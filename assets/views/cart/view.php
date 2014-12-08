<script>
    function update_qty(itemId, qty, el)
    {
        //var priceItem = parseInt($("#row_span_item_" + itemId).html());
        //var priceTotal = priceItem * qty;
        //$("#row_span_total_" + itemId).empty().append(priceTotal);

        $.ajax({
            url:'/cart/update',
            type:"POST",
            data: {qty: qty, itemId: itemId},
            dataType:"json",
            success: function(data){
                //location.reload();
                if (qty <= 0) {
                    $("#tr_item_" + itemId).remove();
                }

                $(el).closest('.cart-product-item').find('.price-total-span').html(data.item_price_formatted);
                $("#total_price_span").html(data.total_price_formatted);
                var cartButton = $('.header .cart-button'),
                    cartCount = cartButton.find('.cart_count');

                if (!cartCount.length && data.items_qty) {
                    cartButton.prepend('<div class="cart_count">');
                    cartCount = cartButton.find('.cart_count');
                }

                if (data.items_qty) {
                    cartCount.text(data.items_qty);
                } else {
                    cartCount.remove();
                }
            },
            fail: function() {
                alert( "error" );
            },
            complete: function () {
                if ($('.cart-product-item').length) {
                    onUpdateCart();
                } else {
                    location.reload();
                }
            }
        });
    }

    $(function () {
        $(".plus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) + 1;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });
        $(".minus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) - 1;
            if (qty < 0) return;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });

        $('.js-remove-item').on('click', function (ev) {
            ev.preventDefault();
            var $link = $(this),
                id = parseInt($link.closest('.cart-product-item').data('id'), 10);

            update_qty(id, 0, this);
        });

        $("#step1_next").click(function(ev) {
            ev.preventDefault();
            var el = $(this);
            el.attr('disabled', 'disabled');

            $.ajax({
                url:'/cart/setMethods',
                type:"POST",
                data: $("#methods, #methods2, #message, input[name=\"_csrf_checkout_step_1\"]").serialize(),
                timeout: 10000,
                complete: function () {
                    el.removeAttr('disabled');
                }
            }).success(function() {
                <?php if (is_null($this->pixie->auth->user())): ?>
                location.href = "<?php echo '/user/login?return_url=' . rawurlencode('/checkout/shipping');?>";
                <?php else : ?>
                location.href = "/checkout/shipping";
                <?php endif;?>
            }).fail(function() {
                alert( "error" );
            });
        });

    });

</script>


<div class="container main content">
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Корзина</h1>
        <?php if (count($items)) :?>
        <div class="continue_shopping">
            <a href="/collections/all">Продолжить покупки →</a>
        </div>
        <?php endif; ?>
    </div>

    <?php if (count($items) == 0) :?>
        <div class="section clearfix">
            <p class="quote">В вашей корзине нет товаров. <a href="/collections/all">Продолжить покупки →</a></p>
            <br class="clear">
        </div>
    <?php else: ?>
    <form id="cart_form" method="post" action="/cart/setMethods">
        <div class="section clearfix">
            <div class="ten columns cart_items">
                <h4 class="title">Продукты</h4>
                <?php
                foreach ($items as $item):?>
                    <?php
                    $product = $item->getProduct(); // var_dump($product);exit;
                    ?>
                    <div class="cart-product-item" data-price="<?php $_($item->price); ?>" data-id="<?php $_($item->id()); ?>" id="tr_item_<?php $_($item->id()); ?>">
                        <div class="five columns alpha">
                            <a title="<?php $_($product['name']); ?>."
                               href="/products/all-terrain-carbon-series-electric-skateboard" class="cart_page_image">
                                <img alt="<?php $_($product['name']); ?>."
                                    data-src-retina="/products_pictures/<?php $_($item->product->picture); ?>"
                                    data-src="/products_pictures/<?php $_($item->product->picture); ?>"
                                    src="/products_pictures/<?php $_($item->product->picture); ?>">
                            </a>
                        </div>

                        <div class="five columns omega">
                            <p>
                                <a title="<?php $_($product['name']); ?>"
                                   href="/products/all-terrain-carbon-series-electric-skateboard"><?php $_($product['name']); ?></a>
                            </p>


                            <p class="price_total">$<span class="price-total-span"><?php echo $_format_price($item->price * $item->qty); ?></span> USD</p>

                            <p id="quantity_1">
                                <label class="quantity_label" for="updates_<?php $_($item->id()); ?>">Количество:</label>
                                <input type="number" value="<?php echo $item->qty; ?>"
                                       id="input_<?php echo $item->id?>" onchange="update_qty(<?php echo $item->id?>, this.value, this);"
                                       class="quantity" maxlength="3" size="3" min="0">
                            </p>

                            <p class="remove_item">
                                <a title="Remove Item" class="js-remove-item" href="/cart/change?line=1&amp;quantity=0">Удалить</a>
                            </p>
                        </div>
                        <br class="clear">
                    </div>
                    <br class="clear">
                <?php endforeach; ?>
            </div>

            <div class="five columns offset-by-one">
                <h4 class="subtotal">
                    Итого
                </h4>

                <p class="subtotal_amount">
                    <strong><span id="total_price_span"><?php echo $_format_price($totalPrice); ?></span> руб.</strong>
                    <small style="display:none" id="estimated-shipping">+ <em>$0.00 - примерная стоимость доставки</em></small>
                    <small class="excluding_tax"><em>Без учета доставки</em></small>
                </p>

                <label for="note">Информация для Evolve Skateboards:</label>
                <textarea rows="2" name="message" id="message"><?php echo isset($message) ? $message : ''; ?></textarea>
                <?php $_token('checkout_step_1'); ?>
                <p>
                    <input type="submit" value="Оформить заказ" name="checkout" id="step1_next"
                           class="action_button add_to_cart">
                </p>
            </div>
        </div>
    </form>
    <?php endif; ?>

    <br class="clear">
</div>