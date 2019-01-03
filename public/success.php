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
        <strong>Congratulations!</strong>
    </div>
</div>
<div class="container" style="margin-top: 10px;">
    <p>
        PASA <strong><?=\Pascal\withChecksum($verification->pasa) ?></strong> was sent to the submitted public key.
        <?php if($verification->block !== null) : ?>
            It was mined in block <?=$verification->block?>.
        <?php else: ?>
            It will be available when the next block is mined. <a href="<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>">Refresh page</a>.
        <?php endif; ?>
    </p>
    <?php if(DEBUG) : ?>
    <button class="button-primary" onclick="window.location.href='delete.php?id=<?=$_GET['id']?>'">DELETE</button>
    <?php endif; ?>

    <?php if($verification->redirect !== '') : ?>
            <p style="margin-bottom: 0">Your service requested to be redirected. Click the button to get redirected:</p>
            <button class="button-primary" onclick="window.location.href='<?=$verification->redirect?>?state=<?=$verification->state?>&pasa=<?=$verification->pasa?>&ophash=<?=$verification->ophash?>&block=<?=$verification->block?>&id=<?=$_GET['id']?>'">Redirect</button>
    <?php endif; ?>

    <p><b>Transaction details:</b></p>
    <p style="margin-bottom: 0">
        <strong>Phone:</strong>
    </p>
    <code>+<?=str_pad($verification->country_number, 4, '0', STR_PAD_LEFT)?> ********<?=$verification->phone_last4?></code>

    <p style="margin-bottom: 0">
        <strong>Pasa:</strong>
    </p>
    <code><?=\Pascal\withChecksum($verification->pasa) ?></code>

    <p style="margin-bottom: 0">
        <strong>Block:</strong>
    </p>
    <code><?php if($verification->block !== null) : ?><?=$verification->block?><?php else: ?>Block not mined yet, <a href="<?=DOMAIN?>/success.php?id=<?=$_GET['id']?>">refresh page</a>.<?php endif; ?></code>
    <?php if($verification->block !== null) :?>
        <a href="http://explorer.pascalcoin.org/block.php?block=<?=$verification->block?>">view in explorer</a>
    <?php endif; ?>
    <p style="margin-bottom: 0"><strong>OpHash:</strong></p>
    <code><?=$verification->ophash ?></code>
    <a href="http://explorer.pascalcoin.org/findoperation.php?ophash=<?=$verification->ophash?>">view in explorer</a>

    <br /><br />
    <p class="error-info">Now visit <a href="http://www.pascalcoin.org">http://www.pascalcoin.org</a> to learn what you can do with it now.</p>

</div>
<?php include __DIR__ . '/include/foot.php'?>
