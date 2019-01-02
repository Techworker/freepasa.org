<?php

include './../bootstrap.php';

// current data, either from GET or then from POST
$data = [
    'origin' => 'web',
    'iso' => 'US',
    'phone' => '',
    'public_key' => '',
    'state' => '',
    'redirect' => '',
    'affiliate_account' => ''
];

// check what is send via query params
if(isset($_GET['public_key'])) {
    $data['public_key'] = $_GET['public_key'];
}
if(isset($_GET['origin'])) {
    $data['origin'] = $_GET['origin'];
}
if (isset($_GET['phone_country_iso'])) {
    $data['iso'] = $_GET['phone_country_iso'];
}
if (isset($_GET['phone_country_number'])) {
    $number = (int)$_GET['phone_country_number'];
    foreach($countries as $country) {
        if($number === $country['number']) {
            $data['iso'] = $country['iso'];
        }
    }
}

if (isset($_GET['phone'])) {
    $data['phone'] = $_GET['phone'];
}
if (isset($_GET['state'])) {
    $data['state'] = $_GET['state'];
}
if (isset($_GET['redirect'])) {
    $data['redirect'] = $_GET['redirect'];
}
if (isset($_GET['afac'])) {
    $data['affiliate_account'] = (int)$_GET['afac'];
    try {
        \Pascal\getAccount($data['affiliate_account']);
    } catch(\Exception $ex) {
        die('Invalid affiliate account ' . (int)$_GET['afac']);
    }
}

// a list of collected errors
$submitErrors = [];
if(isset($_POST['submit']))
{
    // check submit
    $crsf = $_POST['crsf'];
    if($crsf !== $_SESSION['crsf_index']) {
        $submitErrors['crsf'] = 'Your request was cancelled to protect against SPAM, please submit again without refreshing the page. If this error occurs again, make sure you have cookies enabled.';
    }

    $data['origin'] = $_POST['origin'];
    $data['state'] = $_POST['state'];
    $data['redirect'] = $_POST['redirect'];
    $data['affiliate_account'] = (int)$_POST['afac'];

    $data['iso'] = $_POST['iso'];
    if (!isset($countries[$data['iso']])) {
        $submitErrors['phone'] = 'invalid country selected';
    }

    $data['phone'] = preg_replace('/[^0-9]/', '', (string)$_POST['phone']);
    try {
        $phoneInstance = $phoneUtil->parse($data['phone'], $data['iso']);
        $val = $phoneUtil->isValidNumber($phoneInstance);
        if ($val === false) {
            $submitErrors['phone'] = 'Invalid phone number.';
        }
    } catch (\Exception $ex) {
        $submitErrors['phone'] = $ex->getMessage();
    }

    $data['public_key'] = $_POST['public_key'];
    try {
        \Pascal\decodePublicKey($data['public_key']);
    }
    catch(\Exception $ex) {
        $submitErrors['pubkey'] = 'The public key you provided was not valid. Please check again.';
    }

    // if there are no errors, we need to check if the number already
    // successfully requested a pasa and there are no ongoing requests
    if(count($submitErrors) === 0) {
        $result = \Database\Verifications\exists($phoneInstance);
        if($result !== false) {
            if($result['type'] === \Database\Verifications\EXISTS_RUNNING) {
                return header('Location: ' . DOMAIN . '/submit.php?id=' . \Helper\getId($result['verification']->id));
            } else {
                $submitErrors['disbursed'] = $result['verification'];
            }
        }
    }

    if(count($submitErrors) === 0) {
        $verification = \Database\Verifications\addVerification($phoneInstance, $data, $countries[$data['iso']]['number']);
        return header('Location: ' . DOMAIN . '/submit.php?id=' . \Helper\getId($verification->id));
    }
}

$redirectError = null;
if(isset($_GET['error'])) {
    switch($_GET['code']) {
        case 'too_many_tries':
            $redirectError = 'You tried to enter the code more than 3 times and never supplied the correct code. Try again in 1 hour.';
            break;
        case 'not_found':
            $redirectError = 'Somehow the page you were trying to access was not found. Try again.';
            break;
    }
}

// the last thing before anything is displayed
$_SESSION["crsf_index"] = md5(uniqid(mt_rand(), true));

?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline">Official PascalCoin Account Distribution</div>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;">
        <p>Please enter your phone number and your public key and follow the instructions to get a free PascalCoin PASA account. We will send a verification code to your provided phone number.</p>
        <p>For more info about privacy concerns see <a href="<?=DOMAIN?>/about.php">here</a>.</p>
        <?php if(count($submitErrors) > 0) : ?>
            <p class="error-info"><i class="fas fa-exclamation-circle"></i> An error occured, please see the messages below.</p>
        <?php endif; ?>
        <?php if($redirectError !== null) : ?>
            <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=$redirectError?></p>
        <?php endif; ?>

        <?php if(isset($submitErrors['crsf'])) : ?>
            <p class="error"><?=$submitErrors['crsf']?></p>
        <?php endif; ?>

        <?php if(isset($submitErrors['running'])): ?>
            <?php if((int)$submitErrors['running']->tries >= 3) : ?>
                <p class="error">There was an ongoing request for the provided phone number, but you entered the code wrong at least 3 times. Wait at least 60 minutes to try again.</p>
            <?php else: ?>
                <?php if($submitErrors['running']->twilio_uuid !== null) : ?>
                <p class="error">There is an ongoing request four your phone number. <a href="verify.php?id=<?=\Helper\getId($submitErrors['running']->id)?>">Click here</a> to go to the verification page.</p>
                <?php else: ?>
                <p class="error">There is an ongoing request four your phone number. <a href="submit.php?id=<?=\Helper\getId($submitErrors['running']->id)?>">Click here</a> to go to the submission page.</p>
                <?php endif; ?>
        <?php endif; ?>

        <?php endif; ?>
        <?php if(isset($submitErrors['disbursed'])): ?>
            <p class="error">This number was already used to successfully request a PASA. <a href="success.php?id=<?=\Helper\getId($submitErrors['disbursed']->id)?>">Click here</a> to see the disburse info.</p>
        <?php endif; ?>

        <!-- The above form looks like this -->
        <form method="post" action="<?=DOMAIN?>">
            <input type="hidden" name="crsf" value="<?=htmlentities($_SESSION["crsf_index"])?>" />
            <input type="hidden" name="origin" value="<?=htmlentities($data["origin"])?>" />
            <input type="hidden" name="state" value="<?=htmlentities($data["state"])?>" />
            <input type="hidden" name="redirect" value="<?=htmlentities($data["redirect"])?>" />
            <input type="hidden" name="afac" value="<?=htmlentities($data["affiliate_account"])?>" />
            <div class="row">
                <div class="three columns">
                    <label for="iso" class="<?=isset($submitErrors['iso']) ? 'error' : ''?>">Country-Code</label>
                    <select class="u-full-width<?=isset($submitErrors['iso']) ? ' error' : ''?>" name="iso" id="iso">
                        <?php foreach($countries as $country) : ?>
                            <option <?=$data['iso'] === $country['iso'] ? ' selected="selected"' : ''?> value="<?=$country['iso']?>"><?=$country['name']?> (+<?=$country['number']?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="nine columns">
                    <label for="phone" class="<?=isset($submitErrors['phone']) ? 'error' : ''?>">Phone Number</label>
                    <input type="text" class="u-full-width<?=isset($submitErrors['phone']) ? ' error' : ''?>" name="phone" id="phone" value="<?=htmlentities($data['phone'])?>">
                    <?php if(isset($submitErrors['phone'])) : ?>
                        <p class="error"><?=$submitErrors['phone'];?></p>
                    <?php endif; ?>
                </div>
            </div>
            <label for="public_key" class="<?=isset($submitErrors['pubkey']) ? 'error' : ''?>">Base58 Public Key</label>
            <textarea class="u-full-width<?=isset($submitErrors['pubkey']) ? ' error' : ''?>" placeholder="Insert your public key here.." id="public_key" name="public_key"><?=htmlentities($data['public_key']) ?></textarea>
            <?php if(isset($submitErrors['pubkey'])) : ?>
                <p class="error"><?=$submitErrors['pubkey'];?></p>
            <?php endif; ?>
            <input class="button-primary u-pull-right" type="submit" name="submit" value="Send verification code">
    </form>
</div>

<?php include __DIR__ . '/include/foot.php'?>