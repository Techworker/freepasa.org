<footer class="foot-outer <?=(isset($noFootAd) && $noFootAd === true) ? 'no-foot' : ''?>">
    <?php if(!isset($noFootAd) || $noFootAd === false) : ?>
    <div style="background-color: white; padding: 5px;">
        <div class="container">
            <a href="<?=AFF_TWILIO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #F22F46;">
                <small>Powered by</small><br />
                <img src="/assets/twilio.svg" style="height: 40px; float: left;">
            </a>
            <a href="<?=AFF_DO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #0080FF">
                <small>Powered by</small><br />
                <img src="/assets/DO_Logo_horizontal_blue.svg" style="height: 40px;; float: left;">
            </a>
        </div>
    </div>

    <?php endif; ?>
    <div class="container">
        <a href="https://www.github.com/techworker">&copy; Benjamin Ansbach</a>
        <div class="u-pull-right">
            <a href="<?=DOMAIN?>/help.php?lang=<?=@$_GET['lang'] ?>">Help!</a> | <a href="<?=DOMAIN?>/imprint.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_imprint') ?></a> | <a href="<?=DOMAIN?>/affiliate.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_affiliate') ?></a> | <a href="<?=DOMAIN?>/developers.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_developers') ?></a>
        </div>
    </div>
</footer>
<?php if(!isset($noHelp) || $noHelp === false) : ?>
<script>
    setTimeout(function() {
      document.getElementById('helper-wrapper').classList.add('show');
    }, 5000);
</script>
<?php endif; ?>
</body>
</html>
