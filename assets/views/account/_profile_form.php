<?php $baseImgPath = $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/'; ?>
<form role="form" method="post" class="profile-edit-form" action="/account/profile/edit" id="editProfileForm" enctype="multipart/form-data">
    <?php $_token('profile'); ?>
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
        <input type="text" name="user_phone" id="user_phone" class="form-control input-lg" placeholder="Телефон" tabindex="3" value="<?php $_($user_phone, 'user_phone'); ?>">
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <p>Изменить пароль:</p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="password" maxlength="100" name="password" id="password"
                       class="form-control input-lg" placeholder="Password" tabindex="5" value="<?php $_($password, 'password'); ?>">
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="password" maxlength="100" name="password_confirmation"
                       id="password_confirmation" class="form-control input-lg" placeholder="Confirm Password"
                       tabindex="6" value="<?php $_($password_confirmation, 'password_confirmation'); ?>">
            </div>
        </div>
    </div>

    <?php if (isset($photo) && $photo): ?>
        <div class="form-group">
            <img src="<?php echo $baseImgPath; $_($photo); ?>" alt="" class="profile-picture" /> <br>
            <label><input type="checkbox" name="remove_photo" /> Удалить фото</label>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <input type="file" name="photo" id="photo" class="file-input button btn btn-default btn-primary btn-lg" title="Выберите аватарку" tabindex="4" value="<?php $_($photo, 'photo'); ?>">
    </div>

    <hr class="colorgraph">
    <div class="row">
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit" value="Сохранить" class="btn btn-block btn-lg" tabindex="7">
        </div>
        <div class="col-xs-6 col-md-6">
            <input type="submit" name="_submit2" value="Сохранить и выйти" class="btn btn-block btn-lg" tabindex="8">
        </div>
    </div>
</form>

<script>
    $(function() {

        jQuery(function($) {
            $('#editProfileForm').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                container: 'tooltip',
                fields: {
                    password: {
                        validators: {
                            identical: {
                                field: 'password_confirmation',
                                message: 'Пароль и подтверждение не совпадают'
                            }
                        }
                    },
                    password_confirmation: {
                        validators: {
                            identical: {
                                field: 'password',
                                message: 'Пароль и подтверждение не совпадают'
                            }
                        }
                    }
                }
            });
        });
    });
</script>