<div class="panel panel-default">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Вернуться к списку</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="col-xs-6 col-md-6">
            <?php
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderFormStart();
            $formatter->renderFields(['productID', 'hurl', 'name', 'categoryID', 'status']); ?>
            <div class="form-group">
                <label for="field_brief_description">Дополнительные категории</label><br>
                <div class="additional-categories">
                <?php
                foreach ($categories as $catId => $catName) {?>
                    <label><input type="checkbox" name="additional_categories[]" value="<?php echo $catId; ?>"
                        <?php if (array_key_exists($catId, $productCats)): ?>checked <?php endif; ?>/>
                        <?php echo $catName; ?></label>
                <?php
                } ?>
                </div>
            </div><?php

            $formatter->renderFields(['description', 'brief_description', 'Price']);
            $formatter->renderFields();
            $formatter->renderSubmitButtons();
            $formatter->renderFormEnd();
            ?>
        </div>
        <div class="col-xs-6 col-md-6 option-variants-pane js-option-variant-pane">
            <!--div>
                <h4>Свойства</h4>
                <table id="variantList" class="table table-striped table-bordered table-hover dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Свойство</th>
                        <th>Вариант</th>
                        <th>Наценка</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="variant-edit-block js-variant-edit-block">
                    <h4>Добавить свойство</h4>
                    <div class="js-add-variant-errors alert alert-danger add-variant-errors"></div>
                    <form action="/admin/product-option-value/save" class="js-add-option-variant-form" method="post">
                        <input type="hidden" name="ID" id="field_id" />
                        <input type="hidden" name="productID" id="field_product_id" value="<?php echo $item->id(); ?>" />
                        <?php $fieldFormatter->renderField('optionID'); ?>
                        <?php $fieldFormatter->renderField('variantID'); ?>
                        <div class="form-group">
                            <label for="field_price_surplus">Наценка</label><input type="text" class="form-control " required id="field_price_surplus" name="price_surplus">
                        </div>
                        <button type="submit" class="btn btn-primary pull-right js-save-variant-button">Добавить</button>
                    </form>
                </div>
                <div class="buttons-panel">
                    <button type="submit" class="btn btn-primary pull-right js-add-variant-button">Добавить</button>
                </div>
            </div>

            <div class="clearfix"></div-->
            <div>
                <h4>Изображения</h4>
                <table id="productImagesList" class="table table-striped table-bordered table-hover dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Изображение</th>
                        <th>Подпись</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="image-edit-block js-image-edit-block">
                    <h4>Добавить изображение</h4>
                    <div class="js-add-image-errors alert alert-danger add-image-errors"></div>
                    <form action="/admin/product-image/save" class="js-add-image-form" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="field_id" />
                        <input type="hidden" name="product_id" id="field_product_id" value="<?php echo $item->id(); ?>" />
                        <div class="form-group js-image-row">
                            <img src="" alt=""/>
                        </div>
                        <div class="form-group js-file-row">
                            <input type="file" name="file_name_big" class="file-input btn btn-default btn-primary btn-lg" title="Выбрать файл" />
                        </div>
                        <div class="form-group">
                            <label for="field_title">Подпись:</label>
                            <input type="text" name="title" id="field_title" value="" class="form-control" />
                        </div>
                        <button type="submit" class="btn btn-primary pull-right js-save-image-button">Добавить</button>
                    </form>
                </div>
                <div class="buttons-panel">
                    <button type="submit" class="btn btn-primary pull-right js-add-image-button">Добавить</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>


<script type="text/javascript">
    jQuery(function ($) {
        var table = $('#variantList');

        table.dataTable({
            ajax: '/admin/product-option-value?product_id=<?php echo $item->id(); ?>',
            serverSide: true,
            searching: false,
            paging: true,
            order: [ 0, 'asc' ],
            columns: [
                {
                    data: "optionVariant___parentOption___name"
                },
                {
                    data: "optionVariant___name"
                },
                {
                    data: "price_surplus"
                },
                {
                    data: "edit",
                    orderable: false,
                    searching: false
                },
                {
                    data: "delete",
                    orderable: false,
                    searching: false
                }
            ]
        });

        var $optionVariantsPane = $('.js-option-variant-pane'),
            dataTable = table.DataTable(),
            $variantEditBlock = $('.js-variant-edit-block'),
            $variantEditButton = $('.js-add-variant-button'),
            $variantSaveButton = $('.js-save-variant-button'),
            $variantForm = $('.js-add-option-variant-form'),
            $variantFormErrors = $('.add-variant-errors'),
            $optionSelect = $('#field_optionID'),
            $optionVariantsSelect = $('#field_variantID'),
            showEditBlock, hideEditBlock, removeVariant, loadOptionVariants,
            saveButtonText = 'Добавить';


        showEditBlock = function (data) {
            $variantEditBlock.show();
            $variantEditButton.text('Скрыть');
            $variantSaveButton.text(saveButtonText);
            $variantFormErrors.html('').hide();
            var populate = {
                ID: '',
                price_surplus: 0
            };

            if ('object' === typeof data && data) {
                $.extend(populate, data);
                populate.optionID = data.optionVariant___optionID;
            }
            $.each(populate, function (name, value) {
                $variantForm.find('[name="' + name + '"]').val(value);
            });
            $variantForm.data('bootstrapValidator').resetForm();
            if (populate.ID) {
                var id = parseInt(populate.variantID, 10);
                loadOptionVariants(populate.optionID, id)
                .then(function () {
                    $optionVariantsSelect.val(id);
                    $variantForm.data('bootstrapValidator').resetForm();
                });
            }
        };

        hideEditBlock = function () {
            $variantEditBlock.hide();
            $variantEditButton.text('Добавить');
        };

        removeVariant = function (id, confirmDeletion) {
            confirmDeletion = !!confirmDeletion;

            $.ajax({
                url: '/admin/product-option-value/delete',
                type: 'post',
                dataType: 'json',
                data: {id: id, confirm: +confirmDeletion}
            }).success(function (res) {
                if (res.error) {
                    if (confirm(res.message)) {
                        removeVariant(id, true);
                    }
                } else {
                    dataTable.ajax.reload();
                }
            }).error(function (/*xhr, responseType, statusText*/) {
                alert('Ошибка при удалении свойства');
            });
        };

        loadOptionVariants = function (optionId, varToSelectId) {
            var $toDisable = $('.js-add-variant-button, .js-save-variant-button').add($optionSelect),
                deferred = $.Deferred();
            varToSelectId = varToSelectId || null;

            $.ajax({
                url: '/admin/option-value/get-option-values',
                type: 'post',
                dataType: 'json',
                data: {option_id: optionId},
                beforeSend: function () {
                    $toDisable.attr('disabled', 'disabled');
                },
                complete: function () {
                    $toDisable.removeAttr('disabled');
                }
            }).success(function (res) {
                var opts = res.optionVariants, optionsHtml;
                if (opts) {
                    optionsHtml = $.map(opts, function (name, varId) {
                        return '<option value="' + varId + '"'
                            + (varToSelectId && varToSelectId == varId ? " selected" : '') + '>' + name + '</option>';
                    }).join('');
                    $optionVariantsSelect.html(optionsHtml);
                }
                deferred.resolve();

            }).error(function (/*xhr, responseType, statusText*/) {
                deferred.reject();
                alert('Ошибка при загрузке вариантов');
            });

            return deferred;
        };

        $optionVariantsPane.off('click', '.js-edit-variant');
        $optionVariantsPane.on('click', '.js-edit-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data();

            saveButtonText = 'Сохранить';
            showEditBlock(row);
        });

        $optionVariantsPane.on('click', '.js-delete-variant', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data(),
                id = row.ID;

            removeVariant(id);
        });

        $optionVariantsPane.on('click', '.js-add-variant-button', function (ev) {
            ev.preventDefault();
            if ($variantEditBlock.is(':hidden')) {
                saveButtonText = 'Добавить';
                showEditBlock();
            } else {
                hideEditBlock();
            }
        });

        $variantForm.hzBootstrapValidator().on('success.form.bv', function(ev) {
            ev.preventDefault();
            $variantSaveButton.attr('disabled', 'disabled');

            $.ajax({
                url: $variantForm.attr('action'),
                type: 'post',
                dataType: 'json',
                data: $variantForm.serialize()
            }).success(function (res) {
                if (res.error) {
                    $variantFormErrors.html(res.message).show();
                } else {
                    dataTable.ajax.reload();
                    hideEditBlock();
                }
            }).error(function (xhr, responseType, statusText) {
                if (statusText) {
                    $variantFormErrors.html(statusText).show();
                }
            }).complete(function () {
                $variantSaveButton.removeAttr('disabled');
            });
        });

        $optionSelect.on('change', function (ev) {
            var id = $optionSelect.val();
            loadOptionVariants(id);
        });
    });

    $(function () {
        var table = $('#productImagesList');

        table.dataTable({
            ajax: '/admin/product-image?product_id=<?php echo $item->id(); ?>',
            serverSide: true,
            searching: false,
            paging: true,
            order: [ 0, 'asc' ],
            columns: [
                {
                    data: "id"
                },
                {
                    data: "file_thumb",
                    orderable: false,
                    searching: false
                },
                {
                    data: "title"
                },
                {
                    data: "edit",
                    orderable: false,
                    searching: false
                },
                {
                    data: "delete",
                    orderable: false,
                    searching: false
                }
            ]
        });


        var $optionVariantsPane = $('.js-option-variant-pane'),
            dataTable = table.DataTable(),
            $imageEditBlock = $('.js-image-edit-block'),
            $imageEditButton = $('.js-add-image-button'),
            $imageSaveButton = $('.js-save-image-button'),
            $imageForm = $('.js-add-image-form'),
            $imageFormErrors = $('.add-image-errors'),
            showEditBlock, hideEditBlock, removeImage,
            saveButtonText = 'Добавить';


        showEditBlock = function (data) {
            $imageEditBlock.show();
            $imageEditButton.text('Скрыть');
            $imageSaveButton.text(saveButtonText);
            $imageFormErrors.html('').hide();
            var populate = {
                id: '',
                title: ''
            };

            if ('object' === typeof data && data) {
                $.extend(populate, data);
                populate.title = data.title_full;
            }
            $.each(populate, function (name, value) {
                var field = $imageForm.find('[name="' + name + '"]');
                if (field.length && field.attr('type') != 'file') {
                    field.val(value);
                }
            });
            $imageForm.data('bootstrapValidator').resetForm();

            var $img = $imageEditBlock.find('img'),
                $imgRow = $imageEditBlock.find('.js-image-row'),
                $fileRow = $imageEditBlock.find('.js-file-row');

            if (populate.id) {
                $imgRow.show();
                $fileRow.hide();
                $img.attr('src', '/products_pictures/' + data.file_name_big);
            } else {
                $imgRow.hide();
                $fileRow.show();
            }
        };

        hideEditBlock = function () {
            $imageEditBlock.hide();
            $imageEditButton.text('Добавить');
        };

        removeImage = function (id, confirmDeletion) {
            confirmDeletion = !!confirmDeletion;

            $.ajax({
                url: '/admin/product-image/delete',
                type: 'post',
                dataType: 'json',
                data: {id: id, confirm: +confirmDeletion}
            }).success(function (res) {
                if (res.error) {
                    if (confirm(res.message)) {
                        removeImage(id, true);
                    }
                } else {
                    dataTable.ajax.reload();
                }
            }).error(function (/*xhr, responseType, statusText*/) {
                alert('Ошибка при удалении изображения');
            });
        };

        $optionVariantsPane.off('click', '.js-edit-image');
        $optionVariantsPane.on('click', '.js-edit-image', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data();

            saveButtonText = 'Сохранить';
            showEditBlock(row);
        });

        $optionVariantsPane.on('click', '.js-delete-image', function (ev) {
            ev.preventDefault();
            var $link = $(ev.target),
                row = dataTable.row($link.closest('tr')).data(),
                id = row.id;

            removeImage(id);
        });

        $optionVariantsPane.on('click', '.js-add-image-button', function (ev) {
            ev.preventDefault();
            if ($imageEditBlock.is(':hidden')) {
                saveButtonText = 'Добавить';
                showEditBlock();
            } else {
                hideEditBlock();
            }
        });

        $imageForm.hzBootstrapValidator().on('success.form.bv', function(ev) {
            ev.preventDefault();
            $imageSaveButton.attr('disabled', 'disabled');

            $imageForm.ajaxSubmit({
                dataType: 'json',
                timeout: 15000,
                success: function (res) {
                    if (res.error) {
                        $imageFormErrors.html(res.message).show();
                    } else {
                        dataTable.ajax.reload();
                        hideEditBlock();
                    }
                },
                error: function (xhr, responseType, statusText) {
                    if (statusText) {
                        $imageFormErrors.html(statusText).show();
                    }
                },
                complete: function () {
                    $imageSaveButton.removeAttr('disabled');
                }
            });
        });
    });

</script>