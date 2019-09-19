<footer class="foot-outer">
    <div style="background-color: #7289DA; padding: 5px;">
        <div class="container">
            <p style="margin: 0; ">
                <img src="/assets/Discord.svg" style="height: 40px; float: left;">
                <a style="color: white;" href="https://discord.gg/sJqcgtD"><?=t_('global', 'discord') ?></a>
            </p>
        </div>
    </div>
    <div style="background-color: #0080FF; padding: 5px;">
        <div class="container">
            <p style="margin: 0; ">
                <img src="/assets/DO_Logo_Horizontal_White.png" style="height: 30px; float: left;">
                <a style="color: white;" href="https://m.do.co/c/c897b29739f1">&nbsp; <?=t_('global', 'digitalocean') ?></a>
            </p>
        </div>
    </div>
    <div class="container">
        <a href="https://www.pascalcoin.org">&copy; Benjamin Ansbach</a>
        <div class="u-pull-right">
            <a href="<?=DOMAIN?>/help.php?lang=<?=@$_GET['lang'] ?>">Help!</a> | <a href="<?=DOMAIN?>/imprint.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_imprint') ?></a> | <a href="<?=DOMAIN?>/affiliate.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_affiliate') ?></a> | <a href="<?=DOMAIN?>/developers.php?lang=<?=@$_GET['lang'] ?>"><?=t_('global', 'footer_developers') ?></a>
        </div>
    </div>
</footer>
</body>
</html>
