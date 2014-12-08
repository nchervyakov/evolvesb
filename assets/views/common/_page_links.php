<div class="row">
    <div class="col-lg-12">
        <ul>
        <?php foreach ($pages as $page): ?>
             <li><a href="/pages/<?php $_($page->alias); ?>"><?php $_($page->title); ?></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>