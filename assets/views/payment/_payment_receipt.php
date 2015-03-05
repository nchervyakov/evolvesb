<table>
    <thead>
        <tr><td colspan="2" style="font-style: italic; padding: 5px;">Данные о торговой точке.</td></tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">ИМЯ ТОРГОВЦА</td>
            <td><?php $_(trim($data['MERCH_NAME'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">URL ПРОДАВЦА</td>
            <td><?php $_(trim($data['MERCH_URL'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">ТЕРМИНАЛ</td>
            <td><?php $_(trim($data['TERMINAL'])); ?></td>
        </tr>
    </tbody>
    <thead>
        <tr><td colspan="2" style="font-style: italic; padding: 5px;">Данные о заказе</td></tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">НОМЕР ЗАКАЗА</td>
            <td><?php $_(trim($data['ORDER'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">ОПИСАНИЕ</td>
            <td><?php $_(trim($data['DESC'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">ДАТА ОПЕРАЦИИ</td>
            <td><?php $_(DateTime::createFromFormat('YmdHis', trim($data['TIMESTAMP']))->format('Y.m.d H:i:s')); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">СУММА</td>
            <td><?php $_(trim($data['AMOUNT'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">ВАЛЮТА</td>
            <td><?php $_(trim($data['CURRENCY'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">ТИП ОПЕРАЦИИ</td>
            <td><?php $_($_format_payment_op_complete(trim($data['TRTYPE']))); ?></td>
        </tr>
    </tbody>
    <thead>
        <tr><td colspan="2" style="font-style: italic; padding: 5px;">Данные плательщика</td></tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">ИМЯ ДЕРЖАТЕЛЯ</td>
            <td><?php $_(trim($data['NAME'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">EMAIL</td>
            <td><?php $_(trim($data['EMAIL'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">КАРТА</td>
            <td><?php $_(trim($data['CARD'])); ?></td>
        </tr>
    </tbody>
    <thead>
        <tr><td colspan="2" style="font-style: italic; padding: 5px;">Результат операции</td></tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Результат</td>
            <td><?php $_($_format_payment_result(trim($data['RC']))); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Ссылка на операцию</td>
            <td><?php $_(trim($data['RRN'])); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">КОД АВТОРИЗАЦИИ</td>
            <td><?php $_(trim($data['AUTHCODE'])); ?></td>
        </tr>
    </tbody>
</table>