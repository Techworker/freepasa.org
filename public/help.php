<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline">Help freepasa.org</div>
    </div>
</div>
<div class="container" style="margin-top: 30px;">
    <p style="font-weight: 700;">This is a free service that pascal enthusiasts pay for. Please support the project by checking out the <a href="<?=AFF_DO?>" target="_blank">digitalocean</a> and <a href="<?=AFF_TWILIO?>" target="_blank">twilio.com</a> services that
        freepasa.org uses to make it work - or consider a donation:</p>
    <p>

    <p>You can either donate accounts to the public key: <br />
        <code>3Ghhborr2QpDL8dCiUQdJeJ1WRb2ecLqxEojKrjNV5CoNvVnEVNbSRciZrwvsvE6JCGraysuscRpDfwBjesdgFv7bD36QrNDQnRoeN</code>
    </p>
    <p>..or PASC to the account <code>481539-64</code> to help cover the costs.<br />
    </p>


    <a href="<?=AFF_TWILIO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #F22F46;">
        <small>Powered by</small><br />
        <img src="/assets/twilio.svg" style="height: 40px; float: left;">
    </a>
    <a href="<?=AFF_DO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #0080FF">
        <small>Powered by</small><br />
        <img src="/assets/DO_Logo_horizontal_blue.svg" style="height: 40px;; float: left;">
    </a>
    <?php $noFootAd = true; ?>
</div>

<?php $noHelp = true; ?>

<?php include __DIR__ . '/include/foot.php'?>
