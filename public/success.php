<?php

include __DIR__ . './../bootstrap.php';

$verificationIdEncoded = $_GET['id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found&lang=' . $_GET['lang']);
}

$verification = \Database\Verifications\getVerification($verificationId);
if($verification === false) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found&lang=' . $_GET['lang']);
}

// TODO: check
?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline"><?=t_('success', 'title')?></div>
    </div>
</div>
<div class="container" style="margin-top: 10px;">
    <p>
        <?=t_('success', 'sent', \Pascal\withChecksum($verification->pasa)) ?>
        <?php if($verification->block !== null) : ?>
            <?=t_('success', 'title', $verification->block) ?>
        <?php else: ?>
            <?=t_('success', 'next') ?>
        <?php endif; ?>
        <br />
        <?=t_('success', 'also', '0.0010') ?>
    </p>
    <hr />
    <p style="font-weight: 700;">This is a free service that pascal enthusiasts pay for. Please support the project by checking out the <a href="<?=AFF_DO?>" target="_blank">digitalocean</a> and <a href="<?=AFF_TWILIO?>" target="_blank">twilio.com</a> services that
        freepasa.org uses to make it work - or consider a <a href="<?=DOMAIN?>/help.php?lang=<?=$_GET['lang']?>">donation</a></p>
    <p>
        <a href="<?=AFF_TWILIO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #F22F46;">
            <small>Powered by</small><br />
            <img src="/assets/twilio.svg" style="height: 40px; float: left;">
        </a>
        <a href="<?=AFF_DO?>" target="_blank" style="display: inline-block; padding: 5px; margin: 5px; color: #0080FF">
            <small>Powered by</small><br />
            <img src="/assets/DO_Logo_horizontal_blue.svg" style="height: 40px;; float: left;">
        </a>

    </p>
    <hr />
    <?php if(DEBUG) : ?>
    <button class="button-primary" onclick="window.location.href='<?=DOMAIN?>/delete.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>'">DELETE</button>
    <?php endif; ?>

    <button class="button-primary" onclick="window.location.href='<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>'"><?=t_('success', 'refresh') ?></button>
    <?php if($verification->redirect !== '') : ?>
            <p style="margin-bottom: 0"><?=t_('success', 'redirect') ?></p>
            <button class="button-primary" onclick="window.location.href='<?=$verification->redirect?>?state=<?=$verification->state?>&pasa=<?=$verification->pasa?>&ophash=<?=$verification->ophash?>&block=<?=$verification->block?>&id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>'"><?=t_('success', 'redirect_title') ?></button>
    <?php endif; ?>
    <button class="button-primary" onclick="window.location.href='<?=DISCORD_INVITE?>'" style=" display: block; line-height: 25px; background-color: #7289DA; border-color: #7289DA;"><img src="/assets/Discord-Logo-White.svg" style="height: 25px; float: left;">&nbsp;<?=t_('success', 'discord') ?></button>

    <p><b><?=t_('success', 'tx_details') ?></b></p>
    <p style="margin-bottom: 0">
        <strong><?=t_('success', 'phone') ?></strong>
    </p>
    <code>+<?=str_pad($verification->country_number, 4, '0', STR_PAD_LEFT)?> ********<?=$verification->phone_last4?></code>

    <p style="margin-bottom: 0">
        <strong><?=t_('success', 'pasa') ?></strong>
    </p>
    <code><?=\Pascal\withChecksum($verification->pasa) ?></code>
    <a href="http://explore.pascalcoin.org/accounts/<?=$verification->pasa?>"><?=t_('success', 'view_pasa')?></a>
    <p style="margin-bottom: 0">
        <strong><?=t_('success', 'date') ?></strong>
    </p>
    <code><?=date('Y-m-d H:i:s', $verification->dt) ?></code>
    <p style="margin-bottom: 0">
        <strong><?=t_('success', 'pubkey') ?></strong>
    </p>
    <code><?=$verification->b58_pubkey ?></code>
    <p style="margin-bottom: 0">
        <strong><?=t_('success', 'block') ?></strong>
    </p>
    <code><?php if($verification->block !== null) : ?><?=$verification->block?><?php else: ?><?=t_('success', 'not_mined')?> <a href="<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>"><?=t_('success', 'refresh')?></a>.<?php endif; ?></code>
    <?php if($verification->block !== null) :?>
        <a href="http://explore.pascalcoin.org/blocks/<?=$verification->block?>"><?=t_('success', 'view_explorer')?></a>
    <?php endif; ?>
    <p style="margin-bottom: 0"><strong><?=t_('success', 'ophash') ?></strong></p>
    <code><?=$verification->ophash ?></code>
    <a href="http://explore.pascalcoin.org/operations/<?=$verification->ophash?>"><?=t_('success', 'view_op_explorer')?></a>

</div>
<?php $noFootAd = true; ?>

<?php include __DIR__ . '/include/foot.php'?>
