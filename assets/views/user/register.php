<!-- Page Content -->

<div class="container main content">

    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        Регистрация
    </div>
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Регистрация</h1>
    </div>
    <!-- Service Paragraphs -->

    <div class="sixteen columns page" >

        <form role="form" method="post" class="signin" action="/user/register" id="registerForm">
            <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>
                <?= $errorMessage; ?>
              </strong>
            </div>
            <?php endif; ?>
          
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="Имя" tabindex="1" value="<?php $_($first_name, 'first_name'); ?>">
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="Фамилия" tabindex="2" value="<?php $_($last_name, 'last_name'); ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="username" id="username" required class="form-control input-lg" placeholder="Логин" tabindex="3" value="<?php $_($username, 'username'); ?>">
            </div>
            <div class="form-group">
                <input type="email" maxlength="100" required name="email" id="email" class="form-control input-lg" placeholder="Email" tabindex="4" value="<?php $_($email, 'email'); ?>">
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password" id="password"
                               class="form-control input-lg" placeholder="Пароль" tabindex="5" value="<?php $_($password, 'password'); ?>">
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password_confirmation"
                               id="password_confirmation" class="form-control input-lg" placeholder="Подтвердить пароль"
                               tabindex="6" value="<?php $_($password_confirmation, 'password_confirmation'); ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    Нажимая на кнопку <strong class="label label-primary">Зарегистрироваться</strong>, Вы соглашаетесь с <a href="/pages/store-policies" >Правилами магазина</a>, включая использование куки в вашем браузере.
                </div>
            </div>

            <hr class="colorgraph">
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" value="Зарегистрироваться" class="btn button btn-block btn-lg" tabindex="7"></div>
                <div class="col-xs-6 col-md-6">
                    <div style="padding-top: 10px;">
                        <span class="login-social-span">Или войти через</span>
                        <ul class="list-unstyled list-inline list-social-icons">

                            <li class="tooltip-social facebook-link"><a href="/facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook-square fa-4x"></i></a></li>
                            <li class="tooltip-social twitter-link"><a href="/twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter-square fa-4x"></i></a></li>
                            <li class="tooltip-social vkontakte-link"><a href="/vkontakte" data-toggle="tooltip" data-placement="top" title="Vkontakte"><i class="fa fa-vk fa-4x"></i></a></li>
                            <li class="tooltip-social google-plus-link"><a href="/google" data-toggle="tooltip" data-placement="top" title="Google Plus"><i class="fa fa-google-plus-square fa-4x"></i></a></li>
                            <li class="tooltip-social odnoklassniki-link"><a href="/odnoklassniki" data-toggle="tooltip" data-placement="top" title="Odnoklassniki"><i class="fa fa-ok fa-4x icon-odnoklassniki-rect"></i></a></li>
                        </ul>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-lg btn-default" style="width:100%">Забыли пароль?</a></div>
                <div class="col-xs-6 col-md-6"><a href="/user/login" class="btn btn-lg btn-default" style="width:100%">Уже зарегистрированы?</a></div>
            </div>
        </form>
    </div>
    <!-- /.row -->
</div>
<!-- /.container -->

<script>
    $(function() {

        jQuery(function($) {
            $('#registerForm').hzBootstrapValidator({
                fields: {
                    password: {
                        validators: {
                            identical: {
                                field: 'password_confirmation',
                                message: 'Пароль и подтверждение должны быть одинаковы'
                            }
                        }
                    },
                    password_confirmation: {
                        validators: {
                            identical: {
                                field: 'password',
                                message: 'Пароль и подтверждение должны быть одинаковы'
                            }
                        }
                    }
                }
            });
        });

       // $("#user_phone").inputmask("+1(999) 999-9999");
    });
</script>