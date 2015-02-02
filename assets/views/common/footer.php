<div class="sub-footer">
    <div class="container">

        <div class="four columns">

            <h6 class="title">Дополнительно</h6>
            <ul class="footer_menu">

                <li><a href="/search" title="Поиск">Поиск</a></li>

                <li><a href="/pages/about-us" title="О нас">О нас</a></li>

                <?php // <li><a href="/pages/store-policies" title="Правила магазина">Правила магазина</a></li>?>

                <li><a href="/pages/shipping" title="Информация о доставке">Информация о доставке</a></li>

                <li><a href="/pages/contact-us" title="Контакты">Контакты</a></li>

            </ul>

        </div>

        <div class="four columns">

            <h6 class="title">Главное</h6>
            <ul class="footer_menu">

                <li><a href="/" title="Home">Домой</a></li>

                <li><a href="/pages/about-us" title="О нас">О нас</a></li>

                <li><a href="/collections/all" title="Магазин">Магазин</a></li>

                <li><a href="/pages/compare-evolve-skateboard-models" title="Сравнить модели">Сравнить модели</a></li>

                <li><a href="/pages/demo-locations" title="Дилеры">Дилеры</a></li>

                <?php /* <li><a href="/pages/reviews" title="Обзоры">Обзоры</a></li>

                <li><a href="/pages/information" title="Информация">Информация</a></li> */ ?>

                <li><a href="/pages/contact-us" title="Контакты">Контакты</a></li>

            </ul>

        </div>

        <div class="four columns">
            <ul class="social_icons">
                <li><a href="https://twitter.com/EvolveSk8boards" title="Evolve Skateboards Россия в Twitter" rel="me" target="_blank" class="icon-twitter"></a></li>
                <li><a href="https://www.facebook.com/EvolveSkateboardsUSA" title="Evolve Skateboards Россия в Facebook" rel="me" target="_blank" class="icon-facebook"></a></li>
                <li><a href="https://www.youtube.com/user/evolveSkateboards" title="Evolve Skateboards Россия на YouTube" rel="me" target="_blank" class="icon-youtube"></a></li>
                <!--li><a href="https://vimeo.com/user14906646" title="Evolve Skateboards USA on Vimeo" rel="me" target="_blank" class="icon-vimeo-2"></a></li>
                <li><a href="http://instagram.com/evolveskateboards" title="Evolve Skateboards USA on Instagram" rel="me" target="_blank" class="icon-instagram"></a></li-->
            </ul>

        </div>

        <div class="four columns">
            <h6 class="title">Новости и обновления</h6>
            <p>Подпишитесь, чтобы получать последние новости, сведения о распродажах и многое другое...</p>

            <div class="newsletter">
                <p class="message"></p>
                <form accept-charset="UTF-8" action="/newsletter/signup" class="contact-form" method="post">
                    <input type="email" name="email" required pattern="[^ @]*@[^ @]*" placeholder="Введите ваш адрес e-mail..." />
                    <input type="submit" class="action_button sign_up" value="Подписаться" />
                </form>
            </div>
        </div>

        <div class="sixteen columns mobile_only">
            <p class="mobile_only">
            </p>
        </div>
    </div>
</div>

<div class="footer">
    <div class="container">
        <div class="ten columns">
            <div class="payment_methods ">
                <img src="/images/cc-visa.png" alt="Visa" />
                <img src="/images/cc-mastercard.png" alt="Mastercard" />
            </div>

            <p class="credits">
                &copy; 2014 http://evolveskateboards.ru/
            </p>
            <div>ООО «ИВОЛВ РУС» ИНН 6671468126 ОГРН 1146671028063
                    Телефон/факс: 8-800-500-12-46 (бесплатно)
                    г. Екатеринбург, ул. Куйбышева 55 - 509
                    e-mail для связи: info@evolveskateboards.ru
            </div>

        </div>

        <div class="six columns credits_right">
            <img src="/images/visuel_3dsecure.png" alt="3DSecure" />
        </div>
    </div>
</div>

<div id="search">
    <div class="container">
        <div class="sixteen columns center">
            <div class="right search-close">
                X
            </div>
            <form action="/search">
                <input type="text" name="q" placeholder="Искать на Evolve Skateboards..." value="" autocapitalize="off" autocomplete="off" autocorrect="off" />
            </form>
        </div>
    </div>
</div>

<?php if (is_null($this->pixie->auth->user())): ?>
    <div id="login-box" class="login-popup">
        <a href="#" class="close" data-toggle="tooltip" data-placement="top" title="Закрыть"><i class="glyphicon glyphicon-remove"></i></a>
        <form role="form" method="post" class="signin" action="/user/login" id="loginForm">
            <h2>Введите логин и пароль <small></small></h2>
            <hr class="colorgraph">
            <div class="form-group">
                <input type="text" maxlength="100" required name="username" id="username" autocomplete="off" class="form-control input-lg" placeholder="Имя пользователя или email" tabindex="1">
            </div>
            <div class="form-group">
                <input type="password" maxlength="100" required name="password" autocomplete="off" id="password" class="form-control input-lg" placeholder="Пароль" tabindex="5">
            </div>
            <hr class="colorgraph">
            <div class="row">
                <div class="col-xs-6 col-md-6"><button id="loginbtn" type="submit" class="btn btn-block btn-lg">Войти</button></div>
                <div class="col-xs-6 col-md-6">
                    <div>
                        <span class="login-social-span">Или через</span>
                        <ul class="list-unstyled list-inline list-social-icons">
                            <li class="tooltip-social facebook-link"><a href="/facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook-square fa-3x"></i></a></li>
                            <li class="tooltip-social twitter-link"><a href="/twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter-square fa-3x"></i></a></li>
                        </ul>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-default btn-lg" style="width:100%">Забыли ваш пароль?</a></div>
                <div class="col-xs-6 col-md-6"><a href="/user/register" class="btn btn-default btn-lg" style="width:100%">Новый пользователь?</a></div>
            </div>
        </form>
    </div>

    <script>
        //A very basic way to open a popup

        function popup(link, windowname) {
            window.open(link.href, windowname, 'width=400,height=200,scrollbars=yes');
            return false;
        }
        jQuery(function ($) {
            $('#loginForm').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                container: 'tooltip'
            });
        });
    </script>
<?php endif ?>