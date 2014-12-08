<div class="container main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        Корзина
    </div>
    <!-- /.row -->
    <?php
    $menuTabs = array(
    	'overview' => array('/cart/view' => '1</span> <em>Корзина</em>'),
    	'shipping' => array('/checkout/shipping' => '2</span> <em>Адрес доставки</em>'),
    	'billing' => array('/checkout/billing' => '3</span> <em>Адрес оплаты</em>'),
    	'confirmation' => array('/checkout/confirmation' => '4</span> <em>Подтверждение</em>'),
    	'order' => array('/checkout/order' => '5</span> <em>Заказ</em>'),
    );?>
    <div class="sixteen columns page">
        <ul class="nav nav-pills nav-justified hw-steps-nav">
            <?php
            $disabled = false;
            foreach ($menuTabs as $key => $tab) {
                $class = $disabled ? 'grey' : '';
                if ($key == $this->tab) {
                    $class = 'active';
                }
                foreach ($tab as $href => $caption) {
                    if ($class == 'active' || $disabled) {
                        echo '<li class="' . $class . '"><a href="#" onclick="return false"><span class="badge badge-info">' . $caption . '</a></li>';
                    } else {
                        echo '<li><a href="' . $href .'"><span class="badge badge-info">' . $caption . '</a></li>';
                    }
                }
                if ($key == $this->step) {
                    $disabled = true;
                }
            }
            ?>
        </ul>
        <div class="tab-content">