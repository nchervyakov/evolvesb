<div class="container main content">
    <div class="sixteen columns clearfix collection_nav">
        <h1 class="collection_title "><a href="/blogs/news" title="Новости">Новости</a></h1>
    </div>
    <?php if (!isset($news) || !count($news)): ?>
        <div class="sixteen columns article">
            <p>На данный момент новостей нет.</p>
        </div>
    <?php else: ?>

        <?php foreach ($news as $newsItem): ?>
        <div class="sixteen columns article">
            <div class="five columns alpha omega blog_meta">
                <h6><?php echo $_local_date($newsItem->date, 'ru'); ?></h6>
                <hr>
                <div class="social_buttons">
                    <?php include __DIR__ . '/_social_buttons.php'; ?>
                </div>
            </div>

            <div class="ten columns alpha blog_content">
                <h2><a href="/blogs/news/<?php $_($newsItem->hurl); ?>" title="<?php $_($newsItem->title); ?>"><?php $_($newsItem->title); ?></a></h2>
                <?php $_trim($newsItem->brief ?: strip_tags($newsItem->text), 512); ?>
                <a href="/blogs/news/<?php $_($newsItem->hurl); ?>" title="<?php $_($newsItem->title); ?>">Смотреть всю новость →</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="sixteen columns"></div>
</div>