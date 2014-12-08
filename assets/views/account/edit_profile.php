<div class="container profile-edit main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/account#profile" title="Личный кабинет" itemprop="url"><span itemprop="title">Личный кабинет</span></a></span> &nbsp; / &nbsp;
        Редактировать профиль
    </div>
    <!-- /.row -->
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Редактировать профиль</h1>
    </div>

    <div class="sixteen columns page">
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php include __DIR__ . '/_profile_form.php'; ?>
    </div>
</div>