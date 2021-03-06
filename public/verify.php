<?php

include __DIR__ . './../bootstrap.php';

$verificationIdEncoded = $_GET['id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found&lang=' . $_GET['lang']);
}
$verification = \Database\Verifications\getVerification($verificationId);

// send to home if there is no such entry
if($verification === false) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found&lang=' . $_GET['lang']);
}

if($verification->verification_success == 1) {
    return header('Location: ' . DOMAIN . '/success.php?id=' . \Helper\encodeId($verification->id) . '&lang=' . $_GET['lang']);
}

if((int)$verification->tries >= 3) {
    return header('Location: ' . DOMAIN . '/?error=true&code=too_many_tries&lang=' . $_GET['lang']);
}

$phoneInstance =  $phoneUtil->parse($verification->phone_number, $verification->country_iso);

$error = null;
if(isset($_POST['submit']))
{
    $code = $_POST['code'];

    $verificationResult = \Twilio\checkVerification($verification->phone_number, $verification->country_number, $code);
    if($verificationResult === true)
    {
        \Database\Verifications\setVerificationSuccess($verification->id, $code);
        \Database\Verifications\encryptPhone($verification->id);
        $op = \Pascal\sendPasa($verification->b58_pubkey, $verification->phone_last4);
        \Database\Verifications\setPasa($verification->id, $op['account'], $op['ophash']);

        if($verification->affiliate_account !== '' && $verification->affiliate_account !== '0') {
            \Database\Verifications\setAffiliateSuccess(
                $verification->id,
                \Pascal\sendAffiliate($verification->affiliate_account, $op['account'])
            );
        }


        return header('Location: ' . DOMAIN . '/success.php?id=' . \Helper\encodeId($verification->id) . '&lang=' . $_GET['lang']);
    }
    \Database\Verifications\updateTries($verification->id);
    $error = $verificationResult . '. ' . t_('verify', 'try_again');
}

$_SESSION["crsf_verify"] = md5(uniqid(mt_rand(), true));

?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline"><?= t_('verify', 'headline') ?></div>
    </div>
</div>
<div class="container" style="margin-top: 30px;">

    <p><?=t_('verify', 'message', $phoneUtil->format($phoneInstance, \libphonenumber\PhoneNumberFormat::INTERNATIONAL))?></p>

    <?php if($error !== null) : ?>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=$error?></p>
    <?php endif; ?>

    <!-- The above form looks like this -->
    <form method="post" action="<?=DOMAIN?>/verify.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>">
        <input type="hidden" name="crsf" value="<?=$_SESSION["crsf_verify"]?>" />

        <div class="row">
            <div class="twelve columns">
                <label for="code"><?=t_('verify', 'enter_code')?></label>
                <input type="number" class="u-full-width" name="code" id="code" value="">
            </div>
        </div>
        <input class="button-primary" type="submit" name="submit" value="<?=t_('verify', 'button_verify'); ?>">
    </form>
    <p>
        <?=t_('verify', 'code_expires_in', '<span id="seconds" data-seconds="' . ($verification->twilio_expires - time()) . '">' . ($verification->twilio_expires - time()) . '</span>')?>
    </p>
</div>
<script>
    var initialSeconds = parseInt(document.getElementById('seconds').getAttribute('data-seconds'), 10);
    var leftSeconds = initialSeconds;
    setInterval(function() {
        leftSeconds--;
        if(leftSeconds < 0) {
            alert('<?=t_('verify', 'expired')?>');
            window.location.href = '<?=DOMAIN; ?>?lang=<?=$_GET['lang']?>';
            }
            document.getElementById('seconds').innerText = leftSeconds;
        }, 1000);


    </script>
    <?php include __DIR__ . '/include/foot.php'?>
