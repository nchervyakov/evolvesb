<div class="row">
    <div class="col-lg-6">
<form class="settings-form receipt-settings-form" enctype="multipart/form-data" action="/admin/receipt-settings"
      method="post">
    <div class="form-group">
        <label for="field_bank_name">Банк</label>
        <input type="text" class="form-control" required id="field_bank_name" name="bank_name" value="<?php $_($data['bank_name']); ?>">
    </div>
    <div class="form-group">
        <label for="field_bank_bic">БИК</label>
        <input type="text" class="form-control" required id="field_bank_bic" name="bank_bic" value="<?php $_($data['bank_bic']); ?>">
    </div>
    <div class="form-group">
        <label for="field_bank_account">Счёт банка</label>
        <input type="text" class="form-control" required id="field_bank_account" name="bank_account" value="<?php $_($data['bank_account']); ?>">
    </div>
    <div class="form-group">
        <label for="field_company_account">Счёт компании</label>
        <input type="text" class="form-control" required id="field_company_account" name="company_account" value="<?php $_($data['company_account']); ?>">
    </div>
    <div class="form-group">
        <label for="field_company_name">Компания</label>
        <input type="text" class="form-control" required id="field_company_name" name="company_name" value="<?php $_($data['company_name']); ?>">
    </div>
    <div class="form-group">
        <label for="field_company_address">Адрес компании</label>
        <input type="text" class="form-control" required id="field_company_address" name="company_address" value="<?php $_($data['company_address']); ?>">
    </div>
    <div class="form-group">
        <label for="field_company_inn">ИНН компании</label>
        <input type="text" class="form-control" required id="field_company_inn" name="company_inn" value="<?php $_($data['company_inn']); ?>">
    </div>
    <div class="form-group">
        <label for="field_company_kpp">КПП компании</label>
        <input type="text" class="form-control" required id="field_company_kpp" name="company_kpp" value="<?php $_($data['company_kpp']); ?>">
    </div>



    <div class="form-group">
        <label for="field_facsimile">Факсимиле</label>
        <?php if ($data['facsimile']): ?>
            <span><?php $_($data['facsimile']); ?></span>
            <br/>
            <img src="<?php $_($data['facsimile']); ?>" alt="" class="facsimile-picture" /> <br>
        <?php else: ?>
            <br/>
        <?php endif; ?>
        <input type="file" name="facsimile" id="field_facsimile" class="file-input button btn btn-default btn-primary"
               title="Изменить факсимиле" tabindex="4" value="<?php $_($data['facsimile']); ?>">
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
        <br/><br/>
</div>
</div>

<script type="text/javascript">
    var form = $('.receipt-settings-form');
    form.hzBootstrapValidator();
</script>