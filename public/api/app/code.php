<?php

use function Helper\jsonApiMessage;

throw new \Exception("not active");

include './../../../bootstrap.php';

header('Content-Type: application/json');

// check that the api key is given
if(!isset($_GET['api_key'])) {
    jsonApiMessage('error', ['missing_api_key'], null);
}

// check that the api key exists
if(!in_array($_GET['api_key'], API_KEYS, true)) {
    jsonApiMessage('error', ['wrong_api_key'], null);
}

if(!isset($_GET['request_id'])) {
    jsonApiMessage('error', ['missing_request_id'], null);
}

if(!isset($_GET['code'])) {
    jsonApiMessage('error', ['missing_code'], null);
}

$verificationIdEncoded = $_GET['request_id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    jsonApiMessage('error', ['invalid_request_id'], null);
}
$verification = \Database\Verifications\getVerification($verificationId);

// send to home if there is no such entry
if($verification === false) {
    jsonMessage('error', ['invalid_request_id'], null);
}

if($verification->verification_success == 1) {
    jsonApiMessage('success', [
        'account' => $verification->pasa,
        'ophash' => $verification->ophash,
        'link' => DOMAIN . '/success.php?id=' . \Helper\encodeId($verification->id)
    ], $verification->id);
}

if((int)$verification->tries >= 3) {
    jsonApiMessage('error', ['error_too_many_tries'], $verification->id);
}

$code = $_GET['code'];

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

    jsonApiMessage('success', ['account' => $op['account'], 'ophash' => $op['ophash'], 'link' => DOMAIN . '/success.php?id=' . \Helper\encodeId($verification->id)]);
}

jsonApiMessage('error', ['verification_failed'], $_GET['request_id']);
