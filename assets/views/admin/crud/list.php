<div class="panel panel-default">
    <div class="panel-heading">
        <?php $_(isset($tableHeader) ? $tableHeader : 'Список ' . ucfirst($modelName)); ?>

        <div class="pull-right">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" type="button">
                    Действия
                    <span class="caret"></span>
                </button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li><a href="/admin/<?php $_(strtolower($alias)); ?>/new">Добавить новый <?php $_($modelNameSingle); ?></a></li>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="table-responsive">
            <table id="itemList" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                    <tr role="row">
                        <?php foreach ($listFields as $field => $data): ?>
                            <th rowspan="1" style="<?php if ($data['width']) { echo 'width: '.$data['width'].'px;'; }?>"><?php $_($data['title']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.panel-body -->
</div>


<?php
$columns = [];
$order = [];
$ci = 0;  // iteration index
foreach ($listFields as $field => $data){
    $columns[] = [
        'className' => $data['column_classes'],
        'data' => $field,
        'dataSrc' => 'data',
        'orderable' => $data['orderable'],
        'searching' => $data['searching'],
    ];
    if ($data['order']) {
        $order[] = [$ci, $data['order']];
    }
    $ci++;
}


?>
<script type="text/javascript">
    jQuery(function () {
        $('#itemList').dataTable({
            ajax: '/admin/<?php echo strtolower($alias); ?>/',
            serverSide: true,
            pageLength: 25,
            columns:  JSON.parse('<?php echo json_encode($columns); ?>'),
            order: JSON.parse('<?php echo json_encode($order); ?>')
        });
    });
</script>