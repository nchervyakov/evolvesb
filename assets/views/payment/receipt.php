<?php
$numeral = \App\Utils\RUtils::numeral();
$df = \App\Utils\RUtils::dt();

$items = $order->orderItems->with('product')->find_all()->as_array();
$receiptDate = new \DateTime($order->created_at);

?><style type="text/css">
    body {
        font-size: 0.3cm;
        line-height: 1.15em;
    }

    body .print {
        color: #000000;
    }

    table td {
        padding: 0.06cm;
        border: 1pt solid #000000;
    }

    table td:first-child,
    table th:first-child {
        border-left: 1pt solid #000000;
    }

    h1,
    h2 {
        text-transform: none;
        border-bottom: 1pt solid #000000;
        letter-spacing: normal;
    }

    small {
        font-size: 0.8em;
    }

    .product-items-table {
        text-align: left;
        margin: 0 auto 0.4cm;
        width: 100%;
        border-collapse: separate;
    }

    .product-items-table thead {
        font-weight: bold;
        text-align: center;
        border: 1.5pt solid #000000;
        border-bottom-width: 1pt;
    }

    .product-items-table thead td,
    .product-items-table tbody td {
        border-width: 1pt;
        border-left: 0 none;
        border-top: 0 none;
        border-color: #000000;
        color: #000000;
    }

    .print .product-items-table thead td,
    .print .product-items-table tbody td {
        border-width: 1pt;
        border-color: #000000;
    }

    .product-items-table thead tr:first-child td {
        border-top: 1.5pt solid;
    }
    .product-items-table tbody tr td:first-child,
    .product-items-table thead tr td:first-child {
        border-left: 1.5pt solid;
    }

    .product-items-table tbody tr td:last-child,
    .product-items-table thead tr td:last-child {
        border-right-width: 1.5pt;
    }

    .product-items-table tbody tr:last-child td {
        border-bottom-width: 1.5pt;
    }

    .product-items-table tfoot tr,
    .product-items-table tfoot td {
        border: 0 none;
    }

    .product-items-table tbody td {
        text-align: right;
    }

    .print-container {
        width: 20cm;
        margin: 0 auto;
        padding: 0.5cm 0;
    }

    .print-container .header-table {
        text-align: left;
        margin-bottom: 1cm;
        width: 100%;
        border: 0 none #ffffff;
    }

    .print-container .header-table tr,
    .print-container .header-table td {
        border: 0 none;
    }

    .contragents-table tr:first-child,
    .contragents-table tr:first-child td,
    .print-container .header-table tr:first-child,
    .print-container .header-table tr:first-child td {
        border-top-width: 0;
    }

    .header-table tr td,
    .header-table tr {
        border: 0 none #ffffff;
        padding: 0.2cm
    }

    .sample-paragraph {
        font-weight: bold;
        text-align: center;
        margin-bottom: 0.2cm;
    }

    .contragents-table {
        text-align: left;
        margin: 0 auto 0.4cm;
        width: 100%;
        border-collapse: collapse;
        border: 0 solid #fff;
    }

    .contragents-table tr,
    .contragents-table td {
        border: 0 solid #fff;
    }

    .contragents-table td:first-child,
    .contragents-table th:first-child {
        border-left: 0 none;
    }

    @media print {
        .button-placeholder {
            display: none;
        }
    }
</style>

<div class="print-container <?php echo $print ? 'print' : ''; ?>">
    <?php if (!$print) {?>
    <p class="button-placeholder">
        <button onclick="print();">Распечатать</button>
    </p>
    <?php } ?>
    <table class="header-table">
        <tr>
            <td style="width: 3cm;"><img src="/images/logo.png" alt="" style="width: 3cm; vertical-align: middle;"/></td>
            <td style="width: 13cm; text-align: center;">
                Внимание! Счет действителен до <?php  echo $df->ruStrFTime(['format' => 'j F Y', 'monthInflected' => true, 'date' => new DateTime('+1 week')]); ?>.<br/>
                Оплата данного счета означает согласие с условиями поставки товара. <br/>
                Уведомление об оплате обязательно, в противном случае не гарантируется <br/>
                наличие товара на складе. Товар отпускается по факту прихода денег <br/>
                на р/с Поставщика, самовывозом, при наличии доверенности и паспорта. <br/>
            </td>
            <td style="width: 4cm;">&nbsp;</td>
        </tr>
    </table>

    <p class="sample-paragraph">Образец заполнения платежного поручения</p>
    <table width="100%" style="border: 0 solid #fff;">
        <tr style="border: 0 solid #fff;">
            <td style="border: 0 solid #fff; vertical-align: top; padding-top: 0.1cm;">
                <table style="text-align: left; margin: 0 auto 0.4cm; width: 100%; border-collapse: collapse;">
                    <tr>
                        <td rowspan="2" colspan="4">
                            <?php $_($receiptCredentials['bank_name']); ?> <br/>
                            <small>Банк получателя</small>
                        </td>
                        <td>БИК</td>
                        <td><?php $_($receiptCredentials['bank_bic']); ?></td>

                    </tr>
                    <tr>
                        <td>Сч. №</td>
                        <td><?php $_($receiptCredentials['bank_account']); ?></td>
                    </tr>
                    <tr>
                        <td>ИНН</td>
                        <td><?php $_($receiptCredentials['company_inn']); ?></td>
                        <td>КПП</td>
                        <td><?php $_($receiptCredentials['company_kpp']); ?></td>
                        <td rowspan="2" style="vertical-align: top;">Сч. №</td>
                        <td rowspan="2" style="vertical-align: top;"><?php $_($receiptCredentials['company_account']); ?></td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="4">
                            <?php $_($receiptCredentials['company_name']); ?> <br/>
                            <small>Получатель</small>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <?php echo "Оплата по счету №" . $order->uid . " от " . $df->ruStrFTime([
                                    'format' => 'j F Y',
                                    'monthInflected' => true,
                                    'date' => $receiptDate
                                ]);?> <br/>
                            <small>Назначение платежа</small>
                        </td>
                    </tr>
                </table>

            </td>
            <td style="width: 4cm; border: 0 solid #fff; text-align: center; vertical-align: top;">
                <img src="/payment/order_qr_code/<?php $_($order->uid); ?>?code=<?php $_($print_code); ?>" alt="" style="width: 3cm; margin-bottom: 0.1cm;"/> <br/>
                <small style="line-height: 0.5em; font-size: 0.7em;">Оплатите, отсканировав код в платежном
                    терминале или передав сотруднику банка</small>
            </td>
        </tr>
    </table>

    <h2>Счёт на оплату № <?php $_($order->uid); ?> от <?php echo $df->ruStrFTime(['format' => 'j F Y', 'monthInflected' => true, 'date' => $receiptDate]); ?></h2>
    <table class="contragents-table">
        <tr>
            <td>Поставщик:</td>
            <td><strong><?php $_($receiptCredentials['company_name']); ?>, ИНН <?php $_($receiptCredentials['company_inn']); ?>,
                    КПП <?php $_($receiptCredentials['company_kpp']); ?>, <?php $_($receiptCredentials['company_address']); ?></strong></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>Покупатель:</td>
            <td><strong><?php $_($order->buyer_name); ?></strong></td>
        </tr>
    </table>

    <table class="product-items-table">
        <thead>
            <tr>
                <td>№</td>
                <td>Товары (работы, услуги)</td>
                <td colspan="2">Количество</td>
                <td>Цена</td>
                <td>Сумма</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $num => $item) { ?>
            <tr>
                <td><?php echo $num + 1; ?></td>
                <td style="text-align: left;"><?php $_($item->product->name); ?></td>
                <td><?php $_($item->qty); ?></td>
                <td>шт</td>
                <td><?php echo \php_rutils\RUtils::formatNumber($item->price, 2); ?></td>
                <td><?php echo \php_rutils\RUtils::formatNumber($item->price * $item->qty, 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot style="text-align: right; font-weight: bold;">
            <tr>
                <td colspan="5">Итого:</td>
                <td><?php echo \php_rutils\RUtils::formatNumber($order->amount, 2); ?></td>
            </tr>
            <tr>
                <td colspan="5">Без налога (НДС)</td>
                <td>&mdash;</td>
            </tr>
        </tfoot>
    </table>

    <p>
        Всего наименований <?php echo count($items); ?>, на сумму <?php echo \php_rutils\RUtils::formatNumber($order->amount, 2);; ?> RUB <br/>
        <strong><?php echo \App\Utils\Strings::ucfirst($numeral->getRubles($order->amount, true, true)); ?></strong>
    </p>

    <img src="<?php $_($this->pixie->config->get('parameters.receipt.facsimile', '')); ?>" alt="" style="width: 100%;"/>
</div>