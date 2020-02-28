<?php

use function Helper\jsonApiMessage;

include './../../bootstrap.php';

header('Content-Type: application/json');

$submitErrors = [];

// current data, either from GET or then from POST
$data = [
    'origin' => '',
    'user_id' => '',
    'public_key' => '',
];

// check origin existance
if(!isset($_GET['origin'])) {
    jsonApiMessage('error', ['missing_origin'], null);
}

// check exchange config with origin
$data['origin'] = strtoupper($_GET['origin']);
if(!defined($data['origin'] . '__API_KEY')) {
    jsonApiMessage('error', ['invalid_origin'], null);
}

$exchangeApiKey = constant($data['origin'] . '__API_KEY');

if(!defined($data['origin'] . '__PSEUDO_COUNTRY_CODE')) {
    jsonApiMessage('server_error', ['missing_pseudo_country_code'], null);
}

$exchangeCountryCode = constant($data['origin'] . '__PSEUDO_COUNTRY_CODE');

// check that the api key is given
if(!isset($_GET['api_key'])) {
    jsonApiMessage('error', ['missing_api_key'], null);
}

// check that the api key exists
if($_GET['api_key'] !== $exchangeApiKey) {
    jsonApiMessage('error', ['invalid_api_key'], null);
}

// check that the iso is given
if (!isset($_GET['user_id'])) {
    jsonApiMessage('error', ['missing_user_id'], null);
}

$data['user_id'] = (int)$_GET['user_id'];
if($data['user_id'] === 0) {
    jsonApiMessage('error', ['user_id_should_be_an_int32_gt_0'], null);
}

if(!isset($_GET['public_key'])) {
    jsonApiMessage('error', ['missing_public_key'], null);
}

$data['public_key'] = $_GET['public_key'];

$isTest = false;
if(in_array($data['public_key'], TEST_PUBKEYS, true)) {
    $isTest = true;
}

$data['public_key'] = $_GET['public_key'];
try {
    \Pascal\decodePublicKey($data['public_key']);
    $existing = \Database\Verifications\hasPublicKey($data['public_key']);
    if($existing !== false && $isTest === false) {
        jsonApiMessage('success', ['account' => $existing->pasa, 'ophash' => $existing->ophash, 'link' => DOMAIN . '/success.php?id=' . \Helper\encodeId($existing->id)], $existing->id);
    }

    $pasaCount = \Pascal\hasPasa($data['public_key']);
    if($pasaCount > 0 && $isTest === false) {
        jsonApiMessage('error', ['public_key_has_accounts'], null);
    }
}
catch(\Exception $ex) {
    jsonApiMessage('error', ['invalid_public_key'], null);
}

$phoneFormatted = $exchangeCountryCode . str_pad($data['user_id'], 10, '0', STR_PAD_LEFT);

$result = \Database\Verifications\exists($phoneFormatted, $phoneFormatted);
if($result !== false && $isTest === false) {
    jsonApiMessage('success', ['account' => $result['verification']->pasa, 'ophash' => $result['verification']->ophash, 'link' => DOMAIN . '/success.php?id=' . \Helper\encodeId($result['verification']->id)], $result['verification']->id);
}

$verification = \Database\Verifications\addVerification($phoneFormatted, [
    'state' => '',
    'iso' => 'EX',
    'phone' => $phoneFormatted,
    'redirect' => '',
    'origin' => $_GET['api_key'],
    'public_key' => $data['public_key'],
    'affiliate_account' => null
], $exchangeCountryCode);

\Database\Verifications\setVerificationData($verification->id, uniqid(), 300);

$verification = \Database\Verifications\getVerification($verification->id);
\Database\Verifications\setVerificationSuccess($verification->id, 1234);
$verification = \Database\Verifications\getVerification($verification->id);
\Database\Verifications\encryptExchange($verification->id);
$verification = \Database\Verifications\getVerification($verification->id);
$op = \Pascal\sendPasa($verification->b58_pubkey, $verification->phone_last4);
\Database\Verifications\setPasa($verification->id, $op['account'], $op['ophash']);

jsonApiMessage('success', ['account' => $op['account'], 'ophash' => $op['ophash'], 'link' => DOMAIN . '/success.php?id=' . \Helper\encodeId($verification->id)], $verification->id);
