<?php

include './../bootstrap.php';

// current data, either from GET or then from POST
$data = [
    'origin' => 'web',
    'iso' => $supportedLanguages['all'][$_GET['lang']]['iso'],
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
    if(!is_numeric($_GET['afac'])) {
        die(t_('index', 'err_1'));
    }

    $data['affiliate_account'] = (int)$_GET['afac'];

    try {
        \Pascal\getAccount($data['affiliate_account']);
    } catch(\Exception $ex) {
        die(t_('index', 'err_2', $_GET['afac']));
    }
}

// a list of collected errors
$submitErrors = [];
if(isset($_POST['submit']))
{
    // check submit
    $crsf = $_POST['crsf'];
    if($crsf !== $_SESSION['crsf_index']) {
        $submitErrors['crsf'] = die(t_('index', 'err_3'));
    }

    $data['origin'] = $_POST['origin'];
    $data['state'] = $_POST['state'];
    $data['redirect'] = $_POST['redirect'];
    $data['affiliate_account'] = (int)$_POST['afac'];

    $data['iso'] = $_POST['iso'];
    if (!isset($countries[$data['iso']])) {
        $submitErrors['phone'] = t_('index', 'err_5');
    }

    $data['phone'] = preg_replace('/[^0-9]/', '', (string)$_POST['phone']);
    try {
        $phoneInstance = $phoneUtil->parse($data['phone'], $data['iso']);
        $val = $phoneUtil->isValidNumber($phoneInstance);
        if ($val === false) {
            $submitErrors['phone'] = t_('index', 'err_4');
        }
    } catch (\Exception $ex) {
        $submitErrors['phone'] = $ex->getMessage();
    }

    $data['public_key'] = $_POST['public_key'];
    try {
        \Pascal\decodePublicKey($data['public_key']);
        $existing = \Database\Verifications\hasPublicKey($data['public_key']);
        if($existing !== false) {
            $submitErrors['pubkey'] = t_('index', 'err_6', DOMAIN . '/success.php?id=' . \Helper\encodeId($existing->id) . '&lang=' . $_GET['lang']);
        }

        $pasaCount = \Pascal\hasPasa($data['public_key']);
        if($pasaCount > 0) {
            $submitErrors['pubkey'] = t_('index', 'err_7', $pasaCount);
        }
    }
    catch(\Exception $ex) {
        $submitErrors['pubkey'] = t_('index', 'err_8');
    }

    // if there are no errors, we need to check if the number already
    // successfully requested a pasa and there are no ongoing requests
    if(count($submitErrors) === 0) {
        $result = \Database\Verifications\exists($phoneInstance);
        if($result !== false) {
            if($result['type'] === \Database\Verifications\EXISTS_RUNNING) {
                return header('Location: ' . DOMAIN . '/submit.php?id=' . \Helper\encodeId($result['verification']->id) . '&lang=' . $_GET['lang']);
            } else {
                $submitErrors['disbursed'] = $result['verification'];
            }
        }
    }

    if(count($submitErrors) === 0) {
        $verification = \Database\Verifications\addVerification($phoneInstance, $data, $countries[$data['iso']]['number']);
        return header('Location: ' . DOMAIN . '/submit.php?id=' . \Helper\encodeId($verification->id) . '&lang=' . $_GET['lang']);
    }
}

$redirectError = null;
if(isset($_GET['error'])) {
    switch($_GET['code']) {
        case 'too_many_tries':
            $redirectError = t_('index', 'err_9');
            break;
        case 'not_found':
            $redirectError = t_('index', 'err_10');
            break;
    }
}

// the last thing before anything is displayed
$_SESSION["crsf_index"] = md5(uniqid(mt_rand(), true));

?>
<?php
$headTitle = t_('index', 'title');
$headSubTitle = t_('index', 'subtitle', $accountsAvailable);
?>
<?php include __DIR__ . '/include/head.php'?>
<div class="container" style="margin-top: 30px;">
        <p><?=t_('index', 'intro_1') ?></p>
        <p><?=t_('index', 'intro_more', DOMAIN . '/about.php?lang=' . $_GET['lang']); ?></p>
        <?php if(count($submitErrors) > 0) : ?>
            <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=t_('index', 'err_11');?></p>
        <?php endif; ?>
        <?php if($redirectError !== null) : ?>
            <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=$redirectError?></p>
        <?php endif; ?>

        <?php if(isset($submitErrors['crsf'])) : ?>
            <p class="error"><?=$submitErrors['crsf']?></p>
        <?php endif; ?>

        <?php if(isset($submitErrors['running'])): ?>
            <?php if((int)$submitErrors['running']->tries >= 3) : ?>
                <p class="error"><?=t_('index', 'err_wait')?></p>
            <?php else: ?>
                <?php if($submitErrors['running']->twilio_uuid !== null) : ?>
                    <p class="error"><?=t_('index', 'err_ver', DOMAIN . '/verify.php?id=' . \Helper\encodeId($submitErrors['running']->id) . '&lang=' . $_GET['lang'])?></p>
                <?php else: ?>
                    <p class="error"><?=t_('index', 'err_submit', DOMAIN . '/submit.php?id=' . \Helper\encodeId($submitErrors['running']->id) . '&lang=' . $_GET['lang'])?></p>
                <?php endif; ?>
        <?php endif; ?>

        <?php endif; ?>
        <?php if(isset($submitErrors['disbursed'])): ?>
            <p class="error"><?=t_('index', 'err_pasa_used', DOMAIN . '/success.php?id=' . \Helper\encodeId($submitErrors['disbursed']->id) . '&lang=' . $_GET['lang'])?></p>
        <?php endif; ?>
        <!-- The above form looks like this -->
        <form method="post" action="<?=DOMAIN?>?lang=<?=$_GET['lang'] ?>">
            <input type="hidden" name="crsf" value="<?=htmlentities($_SESSION["crsf_index"])?>" />
            <input type="hidden" name="origin" value="<?=htmlentities($data["origin"])?>" />
            <input type="hidden" name="state" value="<?=htmlentities($data["state"])?>" />
            <input type="hidden" name="redirect" value="<?=htmlentities($data["redirect"])?>" />
            <input type="hidden" name="afac" value="<?=htmlentities($data["affiliate_account"])?>" />
            <div class="row">
                <div class="three columns">
                    <label for="iso" class="<?=isset($submitErrors['iso']) ? 'error' : ''?>"><?=t_('index', 'country_code')?></label>
                    <select class="u-full-width<?=isset($submitErrors['iso']) ? ' error' : ''?>" name="iso" id="iso">
                        <?php foreach($countries as $country) : ?>
                            <option <?=$data['iso'] === $country['iso'] ? ' selected="selected"' : ''?> value="<?=$country['iso']?>"><?=$country['name']?> (+<?=$country['number']?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="nine columns">
                    <label for="phone" class="<?=isset($submitErrors['phone']) ? 'error' : ''?>"><?=t_('index', 'phone') ?></label>
                    <input type="text" class="u-full-width<?=isset($submitErrors['phone']) ? ' error' : ''?>" name="phone" id="phone" value="<?=htmlentities($data['phone'])?>">
                    <?php if(isset($submitErrors['phone'])) : ?>
                        <p class="error"><?=$submitErrors['phone'];?></p>
                    <?php endif; ?>
                </div>
            </div>
            <label for="public_key" class="<?=isset($submitErrors['pubkey']) ? 'error' : ''?>"><?=t_('index', 'base58') ?></label>
            <textarea class="u-full-width<?=isset($submitErrors['pubkey']) ? ' error' : ''?>" placeholder="<?=t_('index', 'insert_pubkey_here');?>" id="public_key" name="public_key"><?=htmlentities($data['public_key']) ?></textarea>
            <?php if(isset($submitErrors['pubkey'])) : ?>
                <p class="error"><?=$submitErrors['pubkey'];?></p>
            <?php endif; ?>
            <input class="button-primary u-pull-right" type="submit" name="submit" value="<?=t_('index', 'send')?>">
    </form>
    </div>

<?php include __DIR__ . '/include/foot.php'?>
