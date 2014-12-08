<div class="panel panel-default">
    <div class="panel-heading">
        <a href="/admin/<?php $_(strtolower($alias)); ?>">&larr; Вернуться к списку</a>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <?php
        if (!$item->loaded() || $item->status == \App\Model\Newsletter::STATUS_NEW) {
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderFormStart();
            $formatter->renderFields();
            $shouldSendToAll = !isset($item->send_to_all) || $item->send_to_all ? true : false;
            $subscribersLoaded = isset($subscribers) && count($subscribers) && !$shouldSendToAll;
            ?>
            <div class="form-group">
                <p>Кому:</p>
                <label>Всем <input type="radio" name="send_to_all" value="1" class="js-recipient-variant"
                        <?php echo $shouldSendToAll ? ' checked' : '' ;?> /></label>&nbsp;&nbsp;&nbsp;
                <label>Некоторым <input type="radio" name="send_to_all" value="" class="js-recipient-variant"
                        <?php echo !$shouldSendToAll ? ' checked' : '' ;?> /></label>
                <input type="hidden" name="subscriber_ids" />
            </div>

            <div class="subscribers js-subscribers" data-loaded="<?php echo (int) $subscribersLoaded; ?>">
                <div class="panel panel-default">
                    <div class="panel-body">
                    <?php if ($subscribersLoaded): ?>
                        <?php include __DIR__.'/_subscribers_table.php'; ?>
                    <?php else: ?>
                        Загрузка получателей <img src="/images/ajax-loader.gif" alt=""/>
                    <?php endif; ?>
                    </div>
                </div>
                
            </div>

            <div class="form-group">
                <p>Нажав "Отправить", вы больше не сможете править данное письмо. Убедитесь, что всё указано корректно.</p>
            </div>
            <?php
            $formatter->renderSubmitButtons();

            if ($item->id()) {
                echo ' <button class="btn btn-primary" type="submit" name="send" value="1">Отправить</button> ';
            }

            $formatter->renderFormEnd();
        } else { ?>
            <table class="table newsletter-overview">
                <tr>
                    <td>Тема</td>
                    <td><?php $_($item->subject); ?></td>
                </tr>
                <tr>
                    <td>Шаблон</td>
                    <td><a href="/admin/newsletter-template/edit/<?php $_($item->template->id()); ?>"><?php $_($item->template->title); ?></a></td>
                </tr>
                <tr>
                    <td>Получателей</td>
                    <td><?php $_($item->recipient_count); ?></td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td class="js-status-field"><?php $_($item->status); ?></td>
                </tr>
            </table>

            <?php if ($item->status == \App\Model\Newsletter::STATUS_SENDING): ?>
                <div class="form-group js-sending-block">
                    <img src="/images/ajax-loader.gif" alt=""/> Отправка писем:
                    <span class="js-remaining-newsletters"><?php echo $item->completed_count; ?></span> / <?php echo $item->recipient_count; ?>
                </div>


                <script type="text/javascript">
                    var sendData = <?php echo json_encode([
                        'totalCount' => (int)$item->recipient_count,
                        'completedCount' => (int)$item->completed_count
                    ]); ?>;
                </script>
            <?php endif; ?>
            <?php
        }
        ?>
    </div>
    <!-- /.panel-body -->
</div>
<script type="text/javascript">
jQuery(function ($) {
    var $templateSelect = $('#field_template_id'),
        $subjectField = $('#field_subject'),
        $recipientRadios = $('.js-recipient-variant'),
        $subscribersTable = $('.js-subscribers'),
        onTemplateSelect, onSelectRecipientVariant;

    onTemplateSelect = function () {
        if ($subjectField.val() == '') {
            $subjectField.val($templateSelect.find('option[value="' + $templateSelect.val() + '"]').text());
        }
    };

    onTemplateSelect();
    $templateSelect.on('change', onTemplateSelect);

    onSelectRecipientVariant = function () {
        var showRecipients = !$recipientRadios.filter(':checked').val();

        if (showRecipients) {
            $subscribersTable.show();
            var loaded = !!parseInt($subscribersTable.data('loaded'), 10);
            if (!loaded) {
                $.ajax({
                    url: '/admin/newsletter/subscribers-table?newsletter_id=<?php echo $item->loaded() ? $item->id() : '' ?>',
                    type: 'GET',
                    dataType: 'json',
                    timeout: 10000
                }).success(function (res) {
                    $subscribersTable.find('.panel-body').html(res.html);
                    $subscribersTable.data('loaded', 1);
                });
            }
        } else {
            $subscribersTable.hide();
        }
    };
    if ($subscribersTable.length) {
        onSelectRecipientVariant();
    }
    $recipientRadios.on('change', onSelectRecipientVariant);

    $('.model-newsletter-form').on('submit', function (ev) {
       // ev.preventDefault();
        var $form = $(this),
            $idsField = $('input[name="subscriber_ids"]');

        var ids = $('.js-subscriber').filter(':checked').map(function () { return $(this).val(); }).toArray().join(',');
        $idsField.val(ids);
    });

    // Send newsletters
    if (typeof sendData !== 'undefined' && sendData) {
        var sendNewsletterPart = function () {
            if (sendData.completedCount >= sendData.totalCount) {
                return;
            }
            $.ajax({
                url: '/admin/newsletter/send?newsletter_id=<?php echo $item->loaded() ? $item->id() : '' ?>',
                type: 'POST',
                dataType: 'json',
                timeout: 30000
            }).success(function (res) {
                sendData.completedCount = res.completedCount;
                if (!res.complete) {
                    $('.js-remaining-newsletters').text(res.completedCount);
                    sendNewsletterPart();
                } else {
                    $('.js-status-field').text('<?php echo \App\Model\Newsletter::STATUS_COMPLETE; ?>');
                    $('.js-sending-block').text('Отправка сообщений завершена!');
                }
            });
        };
        sendNewsletterPart();
    }
});
</script>
