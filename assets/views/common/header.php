<script>
    $(function() {
        var tabs = $('ul.menu');
        $('ul.menu > li > a.top-link, a.sub-menu').each(function(i) {
            $(this).removeClass('active');
            if ($(this).attr('href') == location.pathname)
                $(this).addClass('active');
        });
    });
</script>

<?php /*<div id="cart" class="mm-menu mm-horizontal mm-dark mm-ismenu mm-right">
    <ul class="mm-list mm-panel mm-opened mm-current" id="mm-m1-p0">
        <li class="mm-subtitle"><a class="mm-subclose continue" href="#cart">Продолжить</a></li>
        <?php include __DIR__ . '/_sidebar_cart.php'; ?>
    </ul>
</div>  */ ?>
<div>
    <div id="header" class="mm-fixed-top">
        <a href="#nav" class="icon-menu"><span>Menu</span></a>
        <a href="#cart" class="icon-cart right"><span>Cart</span></a>
    </div>

    <div class="header mm-fixed-top <?= ((!isset($bodyClass) || $bodyClass != 'index') ? 'header_bar' : '') ?>">
        <div class="container">
            <div class="four columns logo">
                <a href="/" title="Evolve Skateboards Россия">
                    <img src="/images/logo.png" alt="Evolve Skateboards Россия" data-src="/images/logo.png" data-src-home="/images/logo_home.png" />
                </a>
                <a href="mailto:info@evolveskateboards.ru"><div class="fa fa-inbox fa-lg" style="font-size: 16px; color:#FE642E"> info@evolveskateboards.ru</div></a>
                <div class="fa fa-phone fa-lg" style="font-size: 16px; color:#FE642E">  8-800-500-12-46 (бесплатно)</div>
            </div>

            <div class="twelve columns nav mobile_hidden">
                <ul class="menu">
                    <li><a href="/" title="Домой" class="top-link ">Домой</a></li>
                    <li><a href="/pages/about-us" title="О нас" class="top-link ">О нас</a></li>
                    <li><a href="/collections/all" title="Магазин" class="sub-menu  ">Магазин
                            <span class="arrow">▾</span></a>
                        <div class="dropdown ">
                            <ul>
                                <?php foreach ($this->sidebar as $item): ?>
                                <li><a href="/collections/<?=$item->hurl?>" title="Electric Skateboards"><?=$item->name?></a></li>
                                <?php endforeach; ?>
                                <!--
                                <li><a href="/collections/electric-skateboards" title="Electric Skateboards">Electric Skateboards</a></li>
                                <li><a href="/collections/wheels" title="Skateboard Wheels">Skateboard Wheels</a></li>
                                <li><a href="/collections/apparel" title="Apparel">Apparel</a></li>
                                <li><a href="/collections/accessories" title="Accessories">Accessories</a></li>
                                <li><a href="/collections/spare-parts" title="Spare Parts">Spare Parts</a></li>
                                -->
                            </ul>
                        </div>
                    </li>
                    <li><a href="/pages/compare-evolve-skateboard-models" title="Сравнить модели" class="top-link ">Сравнить модели</a></li>
                    <li><a href="/pages/demo-locations" title="Дилеры" class="top-link ">Дилеры</a></li>
                   <?php // <li><a href="/pages/reviews" title="Обзоры" class="top-link ">Обзоры</a></li> ?>
                    <li><a href="/pages/shipping" title="Shipping & Transit Times">Оплата и доставка</a></li>
                    <?php /*<li><a href="/pages/information" title="Информация" class="sub-menu  ">Информация
                        <span class="arrow">▾</span></a>
                        <div class="dropdown ">
                            <ul>
                                <li><a href="/pages/videos" title="Видео">Видео</a></li>
                                <li><a href="/blogs/news" title="Новости">Новости</a></li>
                                <li><a href="/pages/testimonials" title="Отзывы">Отзывы</a></li>
                                <li><a href="/pages/shipping" title="Shipping & Transit Times">Оплата и доставка</a></li>
                                <!--li><a href="/pages/frequently-asked-questions" title="FAQ">FAQ</a></li-->
                                <li><a href="/pages/rider-tips-maintenance" title="Tips & Maintenance">Советы</a></li>
                                <li><a href="/pages/troubleshooting" title="Troubleshooting">Устранение проблем</a></li>
                            </ul>
                        </div>
                    </li>*/ ?>
                    <li><a href="/pages/contact-us" title="Контакты" class="top-link ">Контакты</a></li>

                    <?php if (in_array(strtolower($this->controller->get_real_class()),
                            ['cart', 'category', 'product', 'checkout', 'account'])
                    ): ?>
                        <?php if (!is_null($this->pixie->auth->user())): ?>
                            <li><a href="#" class="login-window sub-menu" style="z-index:0">Личный кабинет <span class="arrow">▾</span></a>
                                <div class="dropdown ">
                                <ul>
                                    <li><a href="/account">Личный кабинет</a></li>
                                    <li><a href="/account/orders">Заказы</a></li>
                                    <li><a href="/account#profile">Профиль</a></li>
                                </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="hw-login-item">
                            <?php if (!is_null($this->pixie->auth->user())): ?>
                                <a href="/user/logout" class="login-window cart-button">Выйти</a>
                            <?php else: ?>
                                <a href="#login-box" class="login-window cart-button">Войти / Зарегистрироваться</a>
                            <?php endif ?>
                        </li>
                        <li><a href="<?php echo $this->pixie->config->get('parameters.social_links.twitter'); ?>" title="Evolve Skateboards Россия в Twitter" rel="me" target="_blank" class="icon-twitter"></a></li>
                        <li><a href="<?php echo $this->pixie->config->get('parameters.social_links.facebook'); ?>" title="Evolve Skateboards Россия в Facebook" rel="me" target="_blank" class="icon-facebook"></a></li>
                        <li><a href="<?php echo $this->pixie->config->get('parameters.social_links.youtube'); ?>" title="Evolve Skateboards Россия на YouTube" rel="me" target="_blank" class="icon-youtube"></a></li>
                        <!--li><a href="https://vimeo.com/user14906646" title="Evolve Skateboards USA on Vimeo" rel="me" target="_blank" class="icon-vimeo-2"></a></li>
                        <li><a href="http://instagram.com/evolveskateboards" title="Evolve Skateboards USA on Instagram" rel="me" target="_blank" class="icon-instagram"></a></li-->
                        <li>
                            <a href="/search" title="Search" class="icon-search" id="search-toggle"></a>
                        </li>
                        <li>
                            <a href="/cart" class="icon-cart cart-button">
                                <?php if (isset($cart) && $cart && $cart->items_qty > 0): ?>
                                    <div class="cart_count"><?php $_($cart->items_qty); ?></div>
                                <?php endif; ?>
                                <span>Корзина</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="/collections/all" class="cart-button">В магазин</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
