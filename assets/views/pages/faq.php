<?php if ($success = $this->pixie->session->flash('success')): ?>
    <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
<?php endif; ?>
<p><strong>If you're new to eboarding check out our blog post:&nbsp;<a
            title="5 Essential Things to Consider When Buying an Electric Skateboard"
            href="http://evolveskateboardsusa.com/blogs/news/9019519-5-essential-things-to-consider-when-buying-an-electric-skateboard"
            target="_blank">5 Essential things to consider when buying an electric skateboard.</a></strong><strong>
        &nbsp;</strong></p>
<p><strong>View our online manual here:</strong></p>
<p>Follow the link below.</p>
<p>http://cdn.shopify.com/s/files/1/0150/5168/files/Evolve-User-Manual.pdf?1779</p>

<?php if (isset($entries) && !is_null($entries)): ?>
    <?php foreach ($entries as $obj): ?>
        <h4><?php $_($obj->question, 'userQuestion'); ?></h4>
        <p><?php if (!empty($obj->answer)) echo $obj->answer;?></p>
    <?php endforeach; ?>
<?php endif ?>
