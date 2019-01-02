<?php

include __DIR__ . './../bootstrap.php';

$verificationIdEncoded = $_GET['id'];
$verificationId = \Helper\getId($verificationIdEncoded);
if($verificationId === null) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found');
}
$verification = \Database\Verifications\getVerification($verificationId);

// send to home if there is no such entry
if($verification === false) {
    return header('Location: ' . DOMAIN . '/?error=true&code=not_found');
}

if($verification->verification_success == 1) {
    return header('Location: ' . DOMAIN . '/success.php?id=' . $hashids->encode([$verification->id]));
}

if((int)$verification->tries >= 3) {
    return header('Location: ' . DOMAIN . '/?error=true&code=too_many_tries');
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


        return header('Location: ' . DOMAIN . '/success.php?id=' . $hashids->encode([$verification->id]));
    }
    \Database\Verifications\updateTries($verification->id);
    $error = $verificationResult . '. Please try again.';
}

$_SESSION["crsf_verify"] = md5(uniqid(mt_rand(), true));

?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline">Check your phone.</div>
    </div>
</div>
<div class="container" style="margin-top: 10px;">

    <p>We sent a SMS with a code to <?=$phoneUtil->format($phoneInstance, \libphonenumber\PhoneNumberFormat::INTERNATIONAL)?>.</p>

    <?php if($error !== null) : ?>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=$error?></p>
    <?php endif; ?>

    <!-- The above form looks like this -->
    <form method="post" action="<?=DOMAIN?>/verify.php?id=<?=$_GET['id']?>">
        <input type="hidden" name="crsf" value="<?=$_SESSION["crsf_verify"]?>" />

        <div class="row">
            <div class="twelve columns">
                <label for="code">Enter code from SMS:</label>
                <input type="number" class="u-full-width" name="code" id="code" value="" placeholder="Enter 4 digit code..">
            </div>
        </div>
        <input class="button-primary" type="submit" name="submit" value="Verify">
    </form>
    <p>The code expires in <span id="seconds" data-seconds="<?=$verification->twilio_expires - time()?>"><?= $verification->twilio_expires - time()?></span> seconds. After that you will have to enter your data again.</p></p>
</div>
<script>
    var initialSeconds = parseInt(document.getElementById('seconds').getAttribute('data-seconds'), 10);
    var leftSeconds = initialSeconds;
    setInterval(function() {
        leftSeconds--;
        if(leftSeconds < 0) {
            alert('Code expired, you need to start again. Sorry.');
            window.location.href = '<?=DOMAIN; ?>';
        }
        document.getElementById('seconds').innerText = leftSeconds;
    }, 1000);


</script>
<?php include __DIR__ . '/include/foot.php'?>
