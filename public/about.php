<?php include './../bootstrap.php'; ?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline"><?=t_('about', 'title')?></div>
        </div>
    </div>
    <div class="container content">
        <p><?=t_('about', 'para1')?></p>
        <p><?=t_('about', 'para2')?></p>
        <p><?=t_('about', 'para3')?></p>
        <p><?=t_('about', 'para4')?></p>

        <button class="button-primary u-pull-right" onclick="window.location.href='<?=DOMAIN?>?lang=<?=$_GET['lang']?>'"><?=t_('about', 'back')?></button>
    </div>

<?php include __DIR__ . '/include/foot.php'?>
