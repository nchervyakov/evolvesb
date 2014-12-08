<!-- Page Content -->
<div class="container account-page main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        Личный кабинет
    </div>
    <!-- /.row -->
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title">Личный кабинет</h1>
    </div>
    <!-- Service Tabs -->
    <div class="sixteen columns page">
        <div class="">
            <?php if ($success = $this->pixie->session->flash('success')): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>
            <ul id="myTab" class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#my-orders" data-toggle="tab">Мои последние заказы</a></li>
                <li><a href="#profile" data-toggle="tab">Профиль</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane active latest-orders" id="my-orders">
                    <?php include __DIR__.'/_order_list.php'; ?>
                    <p class="text-right">
                        <a href="/account/orders" id="order_link" class="button ladda-button" data-style="expand-right" style="width: auto;"><span class="ladda-label">Посмотреть мои заказы</span></a>
                    </p>
                </div>
                <div class="tab-pane profile-show" id="profile">
                    <?php include __DIR__ . '/_profile_info.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container -->
    
<script>
    $(function() {
        Ladda.bind( '#order_link' );
        
        $('#order_link').on('click', function(e) {
            var l = Ladda.create(document.querySelector( '#order_link' ));
            l.start();
            window.location.href = "/account/orders";
            return false; // Will stop the submission of the form
        });
    });
</script>