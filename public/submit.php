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

// send to verify if already send code
if($verification->twilio_uuid !== null) {
    return header('Location: ' . DOMAIN . '/verify.php?id=' . $_GET['id']);
}

$phoneInstance =  $phoneUtil->parse($verification->phone_number, $verification->country_iso);

if(isset($_POST['submit'])) {
    try {
        $data = Twilio\sendVerificationCode($verification->phone_number, $verification->country_number);
        if($data !== false) {
            \Database\Verifications\setVerificationData($verification->id, $data['uuid'], $data['seconds']);
            return header('Location: ' . DOMAIN . '/verify.php?id=' . $verificationIdEncoded);
        } else {
            echo 'A';
            var_dump($data);
            exit;
            return header('Location: ' . DOMAIN . '/?error=true&code=verification');
        }
    }
    catch(\Exception $ex) {
        echo 'B';
        var_dump($ex);
        exit;
        return header('Location: ' . DOMAIN . '/?error=true&code=verification');
    }
}

$_SESSION["crsf_submit"] = md5(uniqid(mt_rand(), true));

?>

<?php include __DIR__ . '/include/head.php'?>
<div class="info-top">
    <div class="container">
        <div class="headline">Verify your input</div>
    </div>
</div>
<div class="container" style="margin-top: 10px;">

<!-- The above form looks like this -->
    <form method="post" action="<?=DOMAIN?>/submit.php?id=<?=$_GET['id']?>">
        <input type="hidden" name="crsf" value="<?=$_SESSION["crsf_submit"]?>" />
        <div class="row">
            <div class="twelve columns">
                <p style="margin-bottom: 10px;">Please make sure that the displayed phone number as well as the public key is correct. We will send a four digit code to this number:</p>
                <p style="margin-bottom: 10px;"><code><?=$phoneUtil->format($phoneInstance, \libphonenumber\PhoneNumberFormat::INTERNATIONAL)?></code></p>
                <p style="margin-bottom: 10px;">If the verification is successful, we will transfer 1 PASA to the following public key:</p>
                <p style="margin-bottom: 10px;"><code class="public-key"><?=$verification->b58_pubkey?></code></p>
            </div>
        </div>
        <input class="button-primary" type="submit" name="submit" value="Send verification code">
    </form>
</div>
<?php include __DIR__ . '/include/foot.php'?>
