<div class="container main content">
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title ">Поиск</h1>
        <?php if (isset($currentItems) && count($currentItems)): ?>
            <ul class="collection_menu">
                <li>
                    результатов для <strong>&laquo;<?php $_($searchString); ?>&raquo;</strong>: <?php echo $pager->num_items; ?>
                </li>
            </ul>
        <?php endif; ?>
    </div>

    <div class="sixteen columns">
        <?php if (isset($currentItems) && count($currentItems)): ?>
            <?php foreach ($currentItems as $curItem): ?>
                <?php $item = $curItem->item; ?>
                <?php $modelName = strtolower($item->model_name); ?>
                <div class="product_row">
                    <?php if ($modelName == 'product'): ?>
                        <?php $pImg = $item->images->find(); ?>
                        <?php $category = $item->category->find(); ?>

                        <div class="four columns alpha">
                            <a title="Evolve Skateboards Hat"
                               href="/collections/<?php $_($category->getAlias()); ?>/products/<?php $_($item->hurl); ?>">
                                <img alt="<?php $_($item->name); ?>" src="/products_pictures/<?php $_($pImg->file_name); ?>">
                            </a>
                        </div>
                        <div class="twelve columns omega align_left">
                            <p><a title="<?php $_($item->name); ?>"
                                  href="/collections/<?php $_($category->getAlias()); ?>/products/<?php $_($item->hurl); ?>"><?php
                                    echo $_hl_search($item->name, $searchValues); ?></a></p>

                            <div class="info">
                                <span class="price">$<?php echo $_format_price($item->Price); ?></span>
                            </div>
                            <p><?php $_trim($_hl_search(strip_tags($item->description), $searchValues), 256); ?></p>
                        </div>

                    <?php elseif ($modelName == 'page'): ?>
                        <div class="sixteen columns omega align_left">
                            <p><a title="<?php $_($item->title); ?>" href="/pages/<?php $_($item->alias); ?>"><?php echo $_hl_search($item->title, $searchValues); ?></a></p>
                            <p><?php $_trim($_hl_search(strip_tags($item->text), $searchValues), 256); ?></p>
                        </div>

                    <?php elseif ($modelName == 'news'): ?>
                        <div class="sixteen columns omega align_left">
                            <p><a title="<?php $_($item->title); ?>" href="/blogs/news/<?php $_($item->hurl); ?>"><?php echo $_hl_search($item->title, $searchValues); ?></a></p>

                            <p><?php $_trim($_hl_search(strip_tags($item->text), $searchValues), 256); ?></p>
                        </div>

                    <?php elseif ($modelName == 'faq'): ?>
                        <div class="sixteen columns omega align_left">
                            <p><a title="Frequently Asked Questions" href="/pages/frequently-asked-questions">Frequently Asked Questions</a></p>
                            <p><?php echo $_hl_search($item->question, $searchValues); ?></p>
                            <p><?php $_trim($_hl_search(strip_tags($item->answer), $searchValues), 256); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endforeach; ?>

            <div class="clearfix"></div>
            <?php echo $_pager($pager, null); ?>
        <?php else: ?>
            <p>Ничего не найдено</p>
        <?php endif; ?>
    </div>
</div>

