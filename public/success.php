<?php

include __DIR__ . './../bootstrap.php';

$verificationIdEncoded = $_GET['id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found');
}

$verification = \Database\Verifications\getVerification($verificationId);
if($verification === false) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found');
}

// TODO: check
?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline">Congratulations!</div>
    </div>
</div>
<div class="container" style="margin-top: 30px;">
    <p>
        PASA <strong><?=\Pascal\withChecksum($verification->pasa) ?></strong> was sent to the submitted public key.
        <?php if($verification->block !== null) : ?>
            It was mined in block <?=$verification->block?>.
        <?php else: ?>
            It will be available when the next block is mined.
        <?php endif; ?>
        <br />We also sent <strong>0.0010 PASC</strong> to your account to get you started.
    </p>
    <?php if(DEBUG) : ?>
    <button class="button-primary" onclick="window.location.href='delete.php?id=<?=$_GET['id']?>'">DELETE</button>
    <?php endif; ?>

    <button class="button-primary" onclick="window.location.href='<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>'">Refresh page</button>
    <?php if($verification->redirect !== '') : ?>
            <p style="margin-bottom: 0">Your service requested to be redirected. Click the button to get redirected:</p>
            <button class="button-primary" onclick="window.location.href='<?=$verification->redirect?>?state=<?=$verification->state?>&pasa=<?=$verification->pasa?>&ophash=<?=$verification->ophash?>&block=<?=$verification->block?>&id=<?=$_GET['id']?>'">Redirect</button>
    <?php endif; ?>
    <button class="button-primary" onclick="window.location.href='<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>'" style=" display: block; line-height: 25px; background-color: #7289DA; border-color: #7289DA;"><img src="/assets/Discord-Logo-White.svg" style="height: 25px; float: left;">&nbsp;JOIN US ON DISCORD!</button>

    <p><b>Transaction details:</b></p>
    <p style="margin-bottom: 0">
        <strong>Phone:</strong>
    </p>
    <code>+<?=str_pad($verification->country_number, 4, '0', STR_PAD_LEFT)?> ********<?=$verification->phone_last4?></code>

    <p style="margin-bottom: 0">
        <strong>Pasa:</strong>
    </p>
    <code><?=\Pascal\withChecksum($verification->pasa) ?></code>
    <a href="http://new-explorer.pascalcoin.org/accounts/<?=$verification->pasa?>">view PASA in explorer</a>
    <p style="margin-bottom: 0">
        <strong>Date:</strong>
    </p>
    <code><?=date('Y-m-d H:i:s', $verification->dt) ?></code>
    <p style="margin-bottom: 0">
        <strong>Public Key:</strong>
    </p>
    <code><?=$verification->b58_pubkey ?></code>
    <p style="margin-bottom: 0">
        <strong>Block:</strong>
    </p>
    <code><?php if($verification->block !== null) : ?><?=$verification->block?><?php else: ?>Block not mined yet, <a href="<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>">refresh page</a>.<?php endif; ?></code>
    <?php if($verification->block !== null) :?>
        <a href="http://new-explorer.pascalcoin.org/blocks/<?=$verification->block?>">view block in explorer</a>
    <?php endif; ?>
    <p style="margin-bottom: 0"><strong>OpHash:</strong></p>
    <code><?=$verification->ophash ?></code>
    <a href="http://new-explorer.pascalcoin.org/operations/<?=$verification->ophash?>">view operation in explorer</a>

</div>
<?php include __DIR__ . '/include/foot.php'?>
