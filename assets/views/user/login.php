<div class="container main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        Вход на сайт
    </div>
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Пожалуйста, введите ваши данные для входа</h1>
    </div>
    <!-- /.row -->
    <div class="sixteen columns page">
        <form role="form" class="signin" method="POST" action="/user/login<?php echo $returnUrl ? '?return_url=' . rawurlencode($returnUrl) : ''; ?> " id="loginPageForm">
            <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong>
                <?= $errorMessage; ?>
              </strong>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="text" maxlength="100" required name="username" class="form-control input-lg" id="username" placeholder="Логин или email" value="<?= (isset($username) ? $_($username, 'username') : null) ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <input type="password" maxlength="100" required name="password" class="form-control input-lg" placeholder="Пароль" id="password">
                    </div>
                </div>
            </div>
            <hr class="colorgraph">
            <div class="row">
                <div class="col-xs-6 col-md-6"><button id="loginbtn"  type="submit" class="btn btn-block btn-lg">Войти</button></div>
                <div class="col-xs-6 col-md-6">
                    <div>
                        <span class="login-social-span">Или зайти через</span>
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
                <div class="col-xs-6 col-md-6"><a href="/user/password" class="btn btn-default btn-lg" style="width:100%">Забыли пароль?</a></div>
                <div class="col-xs-6 col-md-6"><a href="/user/register" class="btn btn-default btn-lg" style="width:100%">Новый пользователь?</a></div>
            </div>
        </form>
    </div>
</div>
<!-- /.container -->
<script>
    jQuery(function($) {
        $('#loginPageForm').hzBootstrapValidator();
    });
</script>
<!-- /.container -->