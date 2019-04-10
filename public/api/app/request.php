<?php

include './../../../bootstrap.php';

header('Content-Type: application/json');

function jsonMessage($status, $errors, $id)
{
    die(json_encode([
        'request_id' => $id !== null ? \Helper\encodeId($id) : null,
        'status' => $status,
        'errors' => $errors
    ]));
}

$submitErrors = [];

// current data, either from GET or then from POST
$data = [
    'origin' => 'API',
    'iso' => '',
    'phone' => '',
    'public_key' => ''
];

if (isset($_POST['phone_country_iso'])) {
    $data['iso'] = $_POST['phone_country_iso'];
}
if (isset($_POST['phone_country_number'])) {
    $number = (int)$_POST['phone_country_number'];
    foreach($countries as $country) {
        if($number === $country['number']) {
            $data['iso'] = $country['iso'];
        }
    }
}

if($data['iso'] === '') {
    jsonMessage('error', ['iso' => 'Dev-Error: Missing phone_country_iso or phone_country_number parameter'], null);
}

if(!isset($_POST['origin']) || $_POST['origin'] === '') {
    jsonMessage('error', ['origin' => 'Dev-Error: Missing origin parameter'], null);
}

if(!isset($_POST['public_key']) || $_POST['public_key'] === '') {
    jsonMessage('error', ['public_key' => 'Dev-Error: Missing public_key parameter'], null);
}

if(!isset($_POST['phone']) || $_POST['phone'] === '') {
    jsonMessage('error', ['phone' => 'Dev-Error: Missing phone parameter'], null);
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
        jsonMessage('exists_pubkey', [], $existing->id);
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
            jsonMessage('pending', [], $result['verification']->id);
        } else {
            jsonMessage('exists_phone', [], $result['verification']->id);
        }
    }
}

if(count($submitErrors) === 0) {
    $verification = \Database\Verifications\addVerification($phoneInstance, $data, $countries[$data['iso']]['number']);
    $data = Twilio\sendVerificationCode($verification->phone_number, $verification->country_number);
    if($data !== false) {
        \Database\Verifications\setVerificationData($verification->id, $data['uuid'], $data['seconds']);
        jsonMessage('success', [], $verification->id);
    } else {
        jsonMessage('error', [], $verification->id);
    }
} else {
    jsonMessage('error', $submitErrors, null);
}
