
<div class="container main content">

    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        Новый пароль
    </div>
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Новый пароль</h1>
    </div>

    <?php if(isset($successMessage) && !empty($successMessage)):?>
        <div class="sixteen columns page">
            <div class="alert alert-success">
                <strong><?=$successMessage;?></strong>
            </div>
        </div>
    <?php else:?>
        <?php if(isset($errorMessage) && !empty($errorMessage)):?>
            <div class="sixteen columns page">
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong><?=$errorMessage;?></strong>
                </div>
            </div>
        <?php endif; ?>

        <div class="sixteen columns page">
            <form role="form" method="post" action="/user/newpassw" id="recoverForm">
                <div class="form-group">
                    <label for="password">Пароль <span style="color: red">*</span></label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <div class="form-group">
                    <label for="cpassword">Подтверждение пароля <span style="color: red">*</span></label>
                    <input type="password" name="cpassword" class="form-control" id="cpassword" required>
                </div>
                <input type="hidden" name="username" value="<?=$username?>">
                <input type="hidden" name="recover" value="<?=$recover_passw?>">
                <button type="submit" class="btn btn-lg">Изменить пароль</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<script>

    $(function() {
        $("#recoverForm").hzBootstrapValidator({
            fields: {
                password: {
                    validators: {
                        identical: {
                            field: 'cpassword',
                            message: 'Пароль и подтверждение должны быть одинаковы'
                        }
                    }
                },
                cpassword: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: 'Пароль и подтверждение должны быть одинаковы'
                        }
                    }
                }
            }
        });

        $("#user_phone").inputmask("+1(999) 999-9999");

    });

</script>