<div class="container main content">
    <div class="sixteen columns">
        <div class="clearfix breadcrumb">
            <?php
            $navLinks = [];
            if (isset($prevNews) && $prevNews) {
                $navLinks[] = '<a title="Предыдущая новость" href="/blogs/news/'.$_esc($prevNews->hurl).'">← Назад</a>';
            }
            if (isset($nextNews) && $nextNews) {
                $navLinks[] = '<a title="Следующая новость" href="/blogs/news/'.$_esc($nextNews->hurl).'">Вперёд →</a>';
            }
            ?>
            <?php if (count($navLinks)): ?>
            <div class="right mobile_hidden">
                <?php echo implode('&nbsp; | &nbsp;', $navLinks); ?>
            </div>
            <?php endif; ?>

            <div class="breadcrumb">
                <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a itemprop="url" title="" href="<?php $_($host); ?>"><span itemprop="title">Главная</span></a></span>
                &nbsp; / &nbsp;
                <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a itemprop="url" title="News" href="/blogs/news"><span itemprop="title">Новости</span></a></span>
                &nbsp; / &nbsp;
                <span itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><?php $_($newsItem->title); ?></span>
            </div>
        </div>
    </div>

    <div class="sixteen columns">
        <div class="five columns alpha omega blog_meta">
            <h6><?php echo $_local_date($newsItem->date, 'ru'); ?></h6>
            <hr>
            <div class="social_buttons">
                <?php include __DIR__ . '/_social_buttons.php'; ?>
            </div>

            <?php if (isset($news) && count($news)): ?>
                <h4 style="margin-top:10px" class="sidebar_title">Последние новости</h4>
                <ul class="none recent_articles">
                <?php foreach ($news as $item): ?>
                    <li>
                        <a title="The Evolvement of Longboarding"
                           href="/blogs/news/<?php $_($item->hurl); ?>"><?php $_($item->title); ?></a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="/search" class="search">
                <input type="hidden" value="article" name="type">
                <input type="text" value="" placeholder="Найти новости ..." class="search_box" name="q" style="width: 90%">
            </form>
        </div>

        <div class="ten columns alpha blog_content">
            <h1><a title="<?php $_($newsItem->title); ?>" href="/blogs/news/<?php $_($newsItem->hurl); ?>"><?php $_($newsItem->title); ?></a></h1>
            <?php echo $newsItem->text; ?>
        </div>
    </div>

    <script type="text/javascript">
        // &lt;![CDATA[
        $(function() {
            if(window.location.pathname.indexOf('/comments') != -1) {
                $('html,body').animate({scrollTop: $("#new-comment").offset().top-110},'slow');
            }
        });
        // ]]&gt;
    </script>
</div>