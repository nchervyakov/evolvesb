<?php $pImg = $product->images->find(); ?>
<div class="one-third column alpha thumbnail <?php echo isset($oddClass) ? $oddClass : ''; ?>">
    <a href="<?php if ($category): ?>/collections/<?php $_($category->getAlias()); ?><?php endif; ?>/products/<?php $_($product->hurl); ?>"
       title="<?php $_($product->name); ?>">
        <div class="relative">
            <?php $imgName = $pImg->file_name_big ? $pImg->file_name_big : $pImg->file_name; ?>
            <img style="max-height:200px"
                 src="<?php echo  $this->pixie->thumb->getImageThumb($imgName, 'small'); ?>"
                 data-src="<?php echo  $this->pixie->thumb->getImageThumb($imgName, 'small'); ?>"
                 data-src-retina="/products_pictures/<?php $_($imgName); ?>"
                 alt="<?php $_($pImg->title); ?>">
            <span style="display: none;" data-fancybox-href="#product-<?php $_($product->id()); ?>" class="quick_shop action_button"
                  data-gallery="product-<?php $_($product->id()); ?>-gallery"> Посмотреть
            </span>

        </div>
        <div class="info">
            <span class="title"><?php $_($product->name); ?></span>
            <span class="price "><?php echo $_format_price($product->Price);?> руб.</span>
        </div>
    </a>
</div>