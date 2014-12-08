<?php include($common_path . "start.php") ?>
<?php include($common_path . "header.php") ?>
<div class="container main content">
    <div class="sixteen columns clearfix collection_nav">
        <?php if (isset($pageHeader)): ?>
            <h1 class="collection_title"><?php $_($pageHeader); ?></h1>
        <?php endif; ?>
    </div>
    <div class="sixteen columns page">
        <?php include($subview . ".php") ?>
    </div>
</div>
<?php include($common_path . "footer.php") ?>
<?php include($common_path . "end.php") ?>