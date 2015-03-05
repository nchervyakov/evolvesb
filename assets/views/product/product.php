<script>
    $(function () {
        var form = $("#product-form-<?php $_($product->id()); ?>"),
            link = $("#add_to_cart"),
            counter = 0;

        link.click(function (ev) {
//            ev.preventDefault();
//
//            link.blur();
//
//            $.ajax({
//                url: form.attr('action'),
//                data: form.serialize(),
//                dataType: 'json',
//                timeout: 15000,
//                type: 'POST',
//                beforeSend: function () {
//                    link.attr('disabled', 'disabled');
//                },
//                complete: function () {
//                    link.removeAttr('disbled');
//                }
//
//            }).success(function (res) {
//                link.removeAttr('disabled');
//                link.blur();
//
//                if (!(res && res.product)) {
//                    return;
//                }
//
//                addTopCartItem(res);
//            });
//
//            return false;
        });
    });
</script>

<div class="container main content">
    <div class="sixteen columns">
        <div class="clearfix breadcrumb">
            <?php
            $navLinks = [];
            if (isset($prevItem) && $prevItem) {
                $navLinks[] = '<a title="Предыдущий продукт" href="' . ($category ? '/collections/'.$category->getAlias() : '').'/products/'.$_esc($prevItem->hurl).'">← Предыдущий</a>';
            }
            if (isset($nextItem) && $nextItem) {
                $navLinks[] = '<a title="Следующий продукт" href="'.($category ? '/collections/'.$category->getAlias() : '').'/products/'.$_esc($nextItem->hurl).'">Следующий →</a>';
            }
            ?>
            <?php if (count($navLinks)): ?>
                <div class="right mobile_hidden">
                    <?php echo implode('&nbsp; | &nbsp;', $navLinks); ?>
                </div>
            <?php endif; ?>

            <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a
                    itemprop="url" title="Evolve Skateboards" href="<?php $_($host); ?>"><span
                    itemprop="title">Главная</span></a></span>  &nbsp; / &nbsp;
            <?php if ($category): ?>
            <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
                <a title="All" href="/collections/<?php $_($category->getAlias()); ?>"><?php $_($category->getPrintName()); ?></a>
            </span> &nbsp; / &nbsp;
            <?php endif; ?>
            <?php $_($product->name); ?>
        </div>
    </div>

    <div id="product-<?php $_($product->id()); ?>" itemtype="http://data-vocabulary.org/Product" itemscope="" class="sixteen columns">
        <div class="section product_section clearfix">
            <div class="eight columns alpha">
                <div id="product-<?php $_($product->id()); ?>-gallery" class="flexslider product_slider">
                    <ul class="slides">
                        <?php foreach($productImages as $pImg) : ?>
                        <li data-title="<?php $_($pImg->title); ?>" data-thumb="/products_pictures/<?php $_($pImg->file_name); ?>" class="flex-active-slide">
                            <a title="Electric Skateboards" data-fancybox-group="<?php $_($product->id()); ?>" class="fancybox" href="/products_pictures/<?php $_($pImg->file_name_big); ?>">
                                <img class="cloudzoom" data-cloudzoom="zoomImage: '/products_pictures/<?php $_($pImg->file_name_big); ?>', tintColor: '#ffffff', zoomPosition: 'inside', zoomOffsetX: 0, hoverIntentDelay: 100"
                                     alt="<?php $_($pImg->title); ?>" data-src-retina="/products_pictures/<?php $_($pImg->file_name_big); ?>"
                                     data-src="/products_pictures/<?php $_($pImg->file_name); ?>"
                                     src="/products_pictures/<?php $_($pImg->file_name); ?>" draggable="false">
                            </a>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>

            <div class="eight columns omega">
                <h1 itemprop="name" class="product_name"><?php $_($product->name); ?>
                    <small class="label label-<?php $_($product->in_stock ? $product->status : 'missing'); ?>"><?php
                        $_($product->in_stock ? \App\Model\Product::getStatusLabel($product->status) : 'Отсутствует'); ?></small>
                </h1>

                <p itemtype="http://data-vocabulary.org/Offer" itemscope="" itemprop="offerDetails" class="modal_price"
                   style="display: inline; ">
                    <meta content="RUR" itemprop="currency">
                    <meta content="Evolve Skateboards" itemprop="seller">
                    <meta content="in_stock" itemprop="availability">

                    <span class="sold_out"></span>
                     <span class="" content="<?php echo $_format_price($product->Price, '%PRICE%'); ?>" itemprop="price">
                        <span class="current_price">
                          <?php echo $_format_price($product->Price); ?>
                        </span>
                     </span>
                     <span class="was_price"></span>
                </p>

                <?php if ($product->in_stock): ?>
                    <form id="product-form-<?php $_($product->id()); ?>" data-option-index="0" action="/cart/add"
                          data-shop-currency="RUR" data-money-format="${{amount}}" class="product_form" method="post"
                          style="display: inline;">
                        <input type="hidden" value="<?php $_($product->id()); ?>" name="product_id">
                        <input type="hidden" value="1" name="qty">
                        <div class="purchase" style="display:inline; padding-left: 15px;">
                            <input id="add_to_cart" type="submit" class="action_button add_to_cart" style="width: auto; padding: 10px 20px;" value="<?php
                            $_($product->status == \App\Model\Product::STATUS_AVAILABLE ? "Купить" : "Заказать")?>" name="add">
                        </div>
                    </form>
                <?php endif; ?>
                <br/><br/>

                <?php //include __DIR__.'/_notify_product_block.php'; ?>

                <div itemprop="description" class="description">
                    <?php echo $product->description; ?>
                </div>

                <div class="meta">
                </div>
                <hr>

                <span class="social_buttons">
                    Поделиться:
                    <a title="Поделиться на Twitter" class="icon-twitter" target="_blank" href="https://twitter.com/intent/tweet?text=Зацени <?php $_($product->name); ?>. от @evolverussia: <?php $_($host); ?>/products/<?php $_($product->hurl); ?>"></a>
                    <a title="Поделиться на Facebook" class="icon-facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php $_($host); ?>/products/<?php $_($product->hurl); ?>"></a>
                    <a title="Поделиться на Pinterest" class="icon-pinterest" target="_blank" href="//pinterest.com/pin/create/button/?url=<?php $_($host); ?>/products/<?php $_($product->hurl); ?>&amp;media=<?php $_($host); ?>/products_pictures/<?php $_($product->big_picture); ?>&amp;description=<?php $_($product->name); ?>. from Evolve Skateboards"></a>&nbsp;
                    <a title="Отправить другу на email" class="icon-mail" target="_blank" href="mailto:?subject=<?php $_($product->name); ?>.&amp;body=Привет, я лазил на Evolve Skateboards и нашёл <?php $_($product->name); ?>.. Хотел поделиться с тобой этой новостью.%0D%0A%0D%0A<?php $_($host); ?>/products/<?php $_($product->hurl); ?>"></a>
                </span>
            </div>
        </div>


        </div>

        <br class="clear">
        <div class="sixteen columns">
            <h4 class="title center">Похожие товары</h4>
        </div>

        <?php if (isset($relatedProducts) && count($relatedProducts)): ?>
        <div class="sixteen columns">
            <?php $oldProduct = $product; ?>
            <?php foreach ($relatedProducts as $product): ?>
                <?php include __DIR__.'/_product_item.php'; ?>
            <?php endforeach; ?>
            <?php $product = $oldProduct; ?>
            <br class="clear product_clear">
        </div>
    <?php endif; ?>
</div>















<?php return; ?>
<div class="section">
    <div class="container">

        <div class="row">
            <div class="col-xs-9">
                <h1><?= $product->name; ?></h1>
            </div>
            <div class="col-xs-3">
                <div class="social-icons pull-right">
                    <!-- Replace with something like:
                    <div class="fb-like fb_edge_widget_with_comment fb_iframe_widget" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false" data-font="arial">
                        <span style="height: 20px; width: 107px; ">
                            <iframe id="f36680bf28" name="f1bd6447bc" scrolling="no" style="border: none; overflow: hidden; height: 20px; width: 107px; " title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php"></iframe>
                        </span>
                    </div>
                    -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php include($common_path . "multiple_breadcrumbs.php") ?>
            </div>
        </div>
        <div class="row product-detail" data-id="<?= $product->productID; ?>">
            <div class="col-xs-12 col-sm-5 col-md-4">
                <a data-toggle="lightbox" data-title="<?php $_($product->name, 'name'); ?>"
                   href="/products_pictures/<?php $_($product->big_picture, 'picture'); ?>">
                    <img class="img-responsive product-image img-thumbnail" src="/products_pictures/<?php $_($product->picture, 'picture'); ?>" alt="">
                </a>
            </div>
            <div class="hidden-xs col-sm-2 col-md-1">
                <!-- Additional pictures -->
            </div>
            <div class="col-xs-12 col-sm-5 col-md-7">
                <!-- START CONTENT ITEM -->
                <div class="well">
                    <div class="row">
                        <div class="col-xs-6 col-sm-5 col-md-7">
                            <?php if (count($options) > 0) { ?>
                            <div class="option-variants">
                                <?php foreach ($options as $variant) { ?>
                                    <strong><?= $variant->parentOption->name; ?>:</strong> <span><?= $variant->name; ?></span>
                                    <br>

                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="ratings product-item-ratings">
                                <p class="pull-right"><?= $product->customer_votes ?> reviews</p>

                                <p>
                                    <?php include($common_path . "rating_stars.php") ?>
                                    <?php $_($product->customers_rating, 'customers_rating') ?> stars
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-7 col-md-5">
                            <span class="label label-important price">$<?= $product->Price ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h3>Description</h3>

                            <p><?php $_($product->description, 'description'); ?></p>

                            <?php include __DIR__ . '/_wishlist_button.php'; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-md-3">
                            <a class="btn btn-block btn-default"><span class="glyphicon glyphicon-chevron-left"></span>
                                Back</a>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <form id="cart_form" action="/cart/add" method="post" class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="count"
                                           class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">Count</label>

                                    <div class="col-xs-12 col-sm-9 col-md-9 col-lg-10">
                                        <div class="text-right">
                                            <input type="hidden" name="product_id" value="<?= $product->productID ?>">
                                            <select class="form-control" id="qty" name="qty">
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <a class="btn btn-block btn-primary ladda-button" id="add_to_cart" href="#" data-style="expand-right"
                               data-size="xs" data-spinner-size="16"><span class="glyphicon glyphicon-shopping-cart"></span> Add to cart</a>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT ITEM -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <!-- START CONTENT ITEM -->
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#offers" data-toggle="tab">Special Offers</a></li>
                        <li class=""><a href="#bestsell" data-toggle="tab">Best selling products</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="row tab-pane offers-tab-pane active" id="offers">
                            <?php include __DIR__ . '/small_productlist.php'; ?>
                        </div>
                        <div class="row tab-pane bestsell-tab-pane" id="bestsell">
                            <?php include __DIR__ . '/big_productlist.php'; ?>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT ITEM -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="well">
                    <div class="text-right">
                        <?php
                        include($common_path . "review_form.php")
                        ?>
                        <button class="btn btn-success" data-toggle="modal" data-target="#reviewForm">Leave a Review
                        </button>
                    </div>
                    <?php foreach ($product->getReviews() as $review) { ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <?php for ($i = 1; $i < 6; $i++) {
                                    if ($i > $review->rating) {
                                        ?>
                                        <span class="glyphicon glyphicon-star-empty"></span>
                                    <?php } else { ?>
                                        <span class="glyphicon glyphicon-star"></span>
                                    <?php
                                    }
                                }
                                $_($review->username, 'username'); ?>
                                <span class="pull-right"><?php echo $review->getDateLabel(); ?></span>

                                <p><?php $_($review->review, 'review'); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
