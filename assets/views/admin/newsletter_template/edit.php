<div class="panel panel-default">
    <div class="panel-heading">
        <a href="/admin/<?php $_(strtolower($alias)); ?>">&larr; Вернуться к списку</a>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <?php
        /** @var \App\Admin\FieldFormatter $formatter */
        $formatter->renderFormStart();
        $formatter->renderFields();
        ?>
        <div class="form-group">
            <p>В письме вы можете использовать следующие теги:</p>
            <ul>
<!--                <li>%username% - логин пользователя</li>
                <li>%first_name% - имя пользователя</li>
                <li>%last_name% - фамилия пользователя</li>
                <li>%display_name% - либо имя и фамилия, либо логин пользователя, в зависимости от наличия</li>-->
                <li>%email% - E-mail пользователя</li>
                <li>%content% - содержание письма, если указано в рассылке.</li>
                <li>%date% - текущая дата ("YYYY.MM.DD")</li>
                <li>%date_local% - текущая дата ("DD MMMM, YYYY")</li>
                <li>%title% - название шаблона</li>
                <li>%subject% - тема письма, или название шаблона (если тема отсутствует)</li>
            </ul>
        </div>
        <?php
        $formatter->renderSubmitButtons();
        $formatter->renderFormEnd();
        ?>
    </div>
    <!-- /.panel-body -->
</div>

<script type="text/javascript">
jQuery(function ($) {
    var $typeField = $('#field_type'),
        $textField = $('#field_text');


    var typeChangeHandler = function () {
        var type = $typeField.val();

        if (type != 'html') {
            $('body').addClass('js-has-editor');
            $textField.removeClass('js-editor');
            if ($textField.data('ckeditorInstance')) {
                $textField.data('ckeditorInstance').destroy();
            }
        } else {
            $textField.addClass('js-editor');
            $.fn.ckeditor && $textField.ckeditor();
        }
    };
    typeChangeHandler();
    $typeField.on('change', typeChangeHandler);
});
</script>