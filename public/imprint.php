<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline"><?=t_('imprint', 'title')?> &amp; <?=t_('disclaimer', 'title') ?></div>
        </div>
    </div>
    <div class="container" style="margin-top: 30px;">
        <?= t_('imprint', 'text'); ?>

        <div>Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" 		    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>

        <strong><?=t_('disclaimer', 'title') ?></strong><br />
        <p><?=t_('disclaimer', 'text') ?></p>

    </div>

<?php include __DIR__ . '/include/foot.php'?>
