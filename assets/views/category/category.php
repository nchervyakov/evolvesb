<div class="container main content">
    <div class="sixteen columns clearfix breadcrumb">
        <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a
                itemprop="url" title="Evolve Skateboards" href="<?php echo $host; ?>"><span
                itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
        <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a
                itemprop="url" title="All" href="/collections/<?php $_($category->id() == 1 ? 'all' : (isset($category->hurl) && trim($category->hurl) ? $category->hurl : '')); ?>"><span
                itemprop="title"><?php $_($categoryName); ?></span></a></span> &nbsp; / &nbsp;
        Страница <?php echo $pager->page; ?> из <?php echo $pager->num_pages ?: 1; ?>
    </div>

    <div class="sixteen columns">
        <h1><?php $_($h1); ?></h1>

        <?php if (count($products)): ?>
            <?php
            $counter = 0;
            $lastItem = count($products) - 1
            ?>
            <?php foreach ($products as $product): ?>
                <?php $oddClass = $counter % 2 == 0 ? 'even' : 'odd'; ?>
                <?php include __DIR__.'/../product/_product_item.php'; ?>
                <?php if (($counter + 1) % 3 == 0 || $lastItem == $counter): ?><br class="clear product_clear"><?php endif; ?>
                <?php $counter++; ?>
            <?php endforeach; ?>
            <?php $_pager($pager, '/collections/'.$category->getAlias().'?page=#page#'); ?>
        <?php else: ?>
            <p>Эта категория пуста.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($description) && $description) { ?>
        <div class="sixteen columns category-description">
            <?php echo $description; ?>
        </div>
    <?php } ?>
</div>
