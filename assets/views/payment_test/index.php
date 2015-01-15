<div class="payment-test-page js-payment-test-page">

    <h2>Инициализация</h2>
    <a href="#" class="btn btn-primary js-initialize">Провести инициализацию</a>
    <?php if ($initialized): ?>
        <span class="js-init-label init-label is-initialized">Инициализация проведена</span>
    <?php else: ?>
        <span class="js-init-label init-label">Инициализация не проведена</span>
    <?php endif; ?>
    <br/><br/><br/><br/>

    <h2>Тесты</h2>

    <a href="#" class="btn btn-primary js-start-tests" <?php if (!$initialized) echo "disabled"; ?>>Запустить c начала</a>
    <a href="#" class="btn btn-primary js-continue-tests" <?php if (!$initialized) echo "disabled"; ?>>Продолжить</a>
    <a href="#" class="btn btn-primary js-stop-tests" disabled>Остановить</a>

    <?php foreach ($groups as $groupName => $groupData): ?>
    <br/><br/>
    <h3><?php echo $groupName; ?></h3>
    <table class="payment-test-table js-payment-test-table">
        <thead>
        <tr>
            <th>#</th>
            <th>TRTYPE</th>
            <th>Описание операйции</th>
            <th>карта</th>
            <th>amount</th>
            <th>комментарий к прохождению теста</th>
            <th>Id заказа</th>
            <th>Host</th>
            <th>RC</th>
            <th>ACTION</th>
            <th>Result</th>
            <th>Date</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($groupData['tests'] as $testId => $testData): ?>
            <?php $isOdd ^= 1; ?>
            <?php $renderedId = false; ?>
            <?php foreach ($testData['operations'] as $opId => $operationData): ?>
                <?php $expectedResult = $operationData['expected']['Host'] == '00' && $operationData['expected']['RC'] == '00'
                        && $operationData['expected']['ACTION'] == '0' ? 'expected-pass' : 'expected-fail'; ?>
                <tr class="test-row <?php echo $isOdd ? 'odd' : 'even'; ?> <?php echo $operationData['status']; ?> <?php
                        echo $initialized && $operationData['status'] == 'pass' ? 'completed' : ''; ?>" data-id="<?php echo $testId . '_' . $opId; ?>">
                    <?php if (!$renderedId): ?>
                        <td rowspan="<?php echo count($testData['operations']); ?>"><?php echo $testId; ?></td>
                        <?php $renderedId = true; ?>
                    <?php endif; ?>
                    <td><?php echo $operationData['TRTYPE']; ?></td>
                    <td><?php echo $operationData['description']; ?></td>
                    <td><?php echo is_array($operationData['card']) ? implode(', ', $operationData['card']) : $operationData['card']; ?></td>
                    <td><?php echo $operationData['amount']; ?></td>
                    <td><?php echo $operationData['comment']; ?></td>
                    <td><?php echo $testData['ORDER'] ? '<a href="' . $this->pixie->paymentTest->get . '/account/orders/' . $testData['ORDER'] . '" target="_blank">' . $testData['ORDER'] . '</a>' : ''; ?></td>
                    <td class="code-field <?php echo $expectedResult; ?>"><?php echo $operationData['expected']['Host']; ?></td>
                    <td class="code-field <?php echo $expectedResult; ?>"><?php echo $operationData['expected']['RC']; ?> / <span class="real-rc"><?php echo array_key_exists('RC', $operationData) ? $operationData['RC'] : '-'; ?></span></td>
                    <td class="code-field <?php echo $expectedResult; ?>"><?php echo $operationData['expected']['ACTION']; ?> / <span class="real-action"><?php echo array_key_exists('ACTION', $operationData) ? $operationData['ACTION'] : '-'; ?></span></td>
                    <td class="expected-result status"><span class="status"><?php echo $operationData['status'] ?: '-'; ?></span></td>
                    <td class="expected-result date"><?php echo $operationData['date'] ?: '---'; ?></td>
                    <td><a href="#" class="btn btn-primary js-run-test <?php echo !$initialized || $operationData['status'] == 'pass' ? 'disabled' : ''; ?>">Запустить</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php endforeach; ?>

    <br/><br/><br/><br/>

    <h2>Карты</h2>

    <table class="simple">
        <thead>
            <tr>
                <th>Type</th>
                <th>№</th>
                <th>Card</th>
                <th>Expires</th>
                <th>Cardholder Name</th>
                <th>CVV2</th>
                <th>3DSecure</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cards as $cardGroup => $cardGroupData): ?>
            <?php foreach ($cardGroupData as $cardId => $cardData): ?>
            <tr>
                <td><?php echo $cardGroup; ?></td>
                <td><?php echo $cardId; ?></td>
                <td><?php echo $cardData['card']; ?></td>
                <td><?php echo $cardData['expires']; ?></td>
                <td><?php echo $cardData['name']; ?></td>
                <td><?php echo $cardData['cvv2']; ?></td>
                <td><?php echo $cardData['3DSecure']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>


    <br/><br/><br/><br/>

    <h2>Ошибки RC</h2>

    <table class="simple">
        <thead>
            <tr>
                <th>Код</th>
                <th>Название</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($rcErrors as $errorCode => $errorData): ?>
            <tr>
                <td><?php echo $errorCode; ?></td>
                <td><?php echo $errorData; ?></td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <h2>Ошибки Action</h2>

    <table class="simple">
        <thead>
            <tr>
                <th>Код</th>
                <th>Название</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($actionErrors as $errorCode => $errorData): ?>
            <tr>
                <td><?php echo $errorCode; ?></td>
                <td><?php echo $errorData; ?></td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

</div>

<script type="text/javascript">
jQuery(function ($) {
    $('.js-initialize').on('click', function (ev) {
        ev.preventDefault();

        $.ajax({
            url: '/paymentTest/init',
            type: 'POST',
            dataType: 'json',
            timeout: 100000,
            success: function () {
                var el = $('.js-init-label');
                el.addClass('is-initialized');
                el.text('Инициализация проведена');
                location.reload();
            }
        });
    });

    var isRunning = false,
        completed = false,
        $startTestsLink = $('.js-start-tests'),
        $continueTestsLink = $('.js-continue-tests'),
        $stopTestsLink = $('.js-stop-tests');

    var runNextTest = function () {
        if (completed || !isRunning) {
            return;
        }

        var $row = $('.js-payment-test-table .test-row').not('.completed, .ran').first();
        if (!$row.length) {
            completed = true;
            stopTests();
        }

        $row.find('.js-run-test').click();
    };

    var startTests = function (continueTests) {
        continueTests = !!continueTests;
        $startTestsLink.attr('disabled', 'disabled');
        $continueTestsLink.attr('disabled', 'disabled');
        $stopTestsLink.removeAttr('disabled');
        if (!continueTests) {
            $('.js-payment-test-table .test-row').removeClass('ran');
        }
        isRunning = true;
        runNextTest();
    };

    var stopTests = function () {
        $stopTestsLink.attr('disabled', 'disabled');
        $startTestsLink.removeAttr('disabled');
        $continueTestsLink.removeAttr('disabled');
        isRunning = false;
    };

    $startTestsLink.on('click', function (ev) {
        ev.preventDefault();
        startTests();
    });

    $continueTestsLink.on('click', function (ev) {
        ev.preventDefault();
        startTests(true);
    });

    $stopTestsLink.on('click', function (ev) {
        ev.preventDefault();
        stopTests();
    });

    $('.js-payment-test-page').on('click', '.js-run-test', function (ev) {
        ev.preventDefault();
        var $link = $(this),
            $row = $link.closest('tr'),
            id = $row.data('id');

        $link.attr('disabled', 'disabled');
        $row.addClass('running');

        $.ajax({
            url: '/paymentTest/run_test',
            type: 'POST',
            data: {id: id},
            dataType: 'json',
            timeout: 100000,
            success: function (result) {
                if (!result) {
                    $row.find('.status').html('error');
                    $row.addClass('error');
                    if (isRunning) {
                        runNextTest();
                    }
                    return;
                }
                $row.removeClass('error');

                console.log(result);
                console.log($row.find('.real-rc'));
                $row.find('.real-rc').html(result.RC);
                $row.find('.real-action').html(result.ACTION);
                $row.find('.status').html(result.status);
                $row.find('.date').html(result.date);

                $row.removeClass('pass');
                $row.removeClass('fail');
                $row.addClass(result.status);

                if (result.status == 'pass') {
                    $row.addClass('completed');
                }
            },

            fail: function () {

            },

            complete: function () {
                if (!$row.is('.pass')) {
                    $link.removeAttr('disabled');
                }
                $row.removeClass('running');
                $row.addClass('ran');

                if (isRunning) {
                    runNextTest();
                }
            }
        });

    });
});
</script>

