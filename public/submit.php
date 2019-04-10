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

// send to verify if already send code
if($verification->twilio_uuid !== null) {
    return header('Location: ' . DOMAIN . '/verify.php?id=' . $_GET['id'] . '&lang=' . $_GET['lang']);
}

$phoneInstance =  $phoneUtil->parse($verification->phone_number, $verification->country_iso);

if(isset($_POST['submit'])) {
    try {
        $data = Twilio\sendVerificationCode($verification->phone_number, $verification->country_number);
        if($data !== false) {
            \Database\Verifications\setVerificationData($verification->id, $data['uuid'], $data['seconds']);
            return header('Location: ' . DOMAIN . '/verify.php?id=' . $verificationIdEncoded . '&lang=' . $_GET['lang']);
        } else {
            return header('Location: ' . DOMAIN . '/?error=true&code=verification&lang=' . $_GET['lang']);
        }
    }
    catch(\Exception $ex) {
        return header('Location: ' . DOMAIN . '/?error=true&code=verification&lang=' . $_GET['lang']);
    }
}

$_SESSION["crsf_submit"] = md5(uniqid(mt_rand(), true));

?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline"><?=t_('submit', 'title')?></div>
    </div>
</div>
<div class="container" style="margin-top: 30px;">

<!-- The above form looks like this -->
    <form method="post" action="<?=DOMAIN?>/submit.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>">
        <input type="hidden" name="crsf" value="<?=$_SESSION["crsf_submit"]?>" />
        <div class="row">
            <div class="twelve columns">
                <p style="margin-bottom: 10px;"><?=t_('submit', 'para_1')?></p>
                <p style="margin-bottom: 10px;"><code><?=$phoneUtil->format($phoneInstance, \libphonenumber\PhoneNumberFormat::INTERNATIONAL)?></code></p>
                <p style="margin-bottom: 10px;"><?=t_('submit', 'para_2')?></p>
                <p style="margin-bottom: 10px;"><code class="public-key"><?=$verification->b58_pubkey?></code></p>
            </div>
        </div>
        <input class="button-primary" type="submit" name="submit" value="<?=t_('submit', 'send')?>">
    </form>
</div>
<?php include __DIR__ . '/include/foot.php'?>
