<div class="panel panel-default">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($alias)); ?>">&larr; Вернуться к списку</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <?php
        /** @var \App\Admin\FieldFormatter $formatter */
        $formatter->renderForm();
        ?>
    </div>
    <!-- /.panel-body -->
</div>

