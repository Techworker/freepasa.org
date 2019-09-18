<?php

use function Helper\jsonApiMessage;

include './../../../bootstrap.php';

header('Content-Type: application/json');

$submitErrors = [];

// current data, either from GET or then from POST
$data = [
    'phone_iso' => '',
    'phone_number' => '',
    'public_key' => '',
];

// check that the api key is given
if(!isset($_GET['api_key'])) {
    jsonApiMessage('error', ['missing_api_key'], null);
}

// check that the api key exists
if(!in_array($_GET['api_key'], API_KEYS, true)) {
    jsonApiMessage('error', ['wrong_api_key'], null);
}

// check that the iso is given
if (!isset($_GET['phone_iso'])) {
    jsonApiMessage('error', ['missing_phone_iso'], null);
}

if(!isset($countries[$_GET['phone_iso']])) {
    jsonApiMessage('error', ['invalid_phone_iso'], null);
}

if(!isset($_GET['public_key'])) {
    jsonApiMessage('error', ['missing_public_key'], null);
}

if(!isset($_GET['phone_number'])) {
    jsonApiMessage('error', ['missing_phone_number'], null);
}

$data['public_key'] = $_GET['public_key'];

// get the iso code
$data['phone_iso'] = strtoupper(substr($_GET['phone_iso'], 0, 2));


// get the phone number
$data['phone_number'] = preg_replace('/[^0-9]/', '', (string)$_GET['phone_number']);

try {
    $phoneInstance = $phoneUtil->parse($data['phone_number'], $data['phone_iso']);
    $val = $phoneUtil->isValidNumber($phoneInstance);
    if ($val === false) {
        jsonApiMessage('error', ['invalid_phone_number'], null);
    }
} catch (\Exception $ex) {
    jsonApiMessage('error', ['invalid_phone_number'], null);
}

$data['public_key'] = $_GET['public_key'];
try {
    \Pascal\decodePublicKey($data['public_key']);
    $existing = \Database\Verifications\hasPublicKey($data['public_key']);
    if($existing !== false) {
        jsonApiMessage('error', ['public_key_already_used'], $existing->id);
    }

    $pasaCount = \Pascal\hasPasa($data['public_key']);
    if($pasaCount > 0) {
        jsonApiMessage('error', ['public_key_has_accounts'], null);
    }
}
catch(\Exception $ex) {
    jsonApiMessage('error', ['invalid_public_key'], null);
}

$result = \Database\Verifications\exists($phoneInstance);
if($result !== false) {
    if($result['type'] === \Database\Verifications\EXISTS_RUNNING) {
        jsonApiMessage('pending', [], $result['verification']->id);
    } else {
        jsonApiMessage('error', ['already_disbursed'], $result['verification']->id);
    }
}

$verification = \Database\Verifications\addVerification($phoneInstance, [
    'state' => '',
    'iso' => $data['phone_iso'],
    'phone' => $data['phone_number'],
    'redirect' => '',
    'origin' => $_GET['api_key'],
    'public_key' => $data['public_key'],
    'affiliate_account' => null
], $countries[$data['phone_iso']]['number']);

$data = Twilio\sendVerificationCode($verification->phone_number, $verification->country_number);
if($data !== false) {
    \Database\Verifications\setVerificationData($verification->id, $data['uuid'], $data['seconds']);
    jsonApiMessage('success', [], $verification->id);
} else {
    jsonApiMessage('error', ['unknown'], null);
}
