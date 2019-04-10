<?php

use function Twilio\sendVerificationCode;

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

if(!isset($_POST['request_id']) || $_POST['request_id'] === '') {
    jsonMessage('error', ['request_id' => 'Dev-Error: Missing request_id.'], null);
}

if(!isset($_POST['code']) || $_POST['code'] === '') {
    jsonMessage('error', ['request_id' => 'Dev-Error: Missing request_id.'], null);
}

$verificationIdEncoded = $_POST['id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    jsonMessage('error', ['request_id' => 'Dev-Error: Invalid request_id.'], null);
}
$verification = \Database\Verifications\getVerification($verificationId);

// send to home if there is no such entry
if($verification === false) {
    jsonMessage('error', ['request_id' => 'Dev-Error: Unknown request_id.'], null);
}

if($verification->verification_success == 1) {
    jsonMessage('finished', [], $verification->id);
}

if((int)$verification->tries >= 3) {
    jsonMessage('error', ['code' => 'too many tries'], $verification->id);
}
/*
$phoneInstance =  $phoneUtil->parse($verification->phone_number, $verification->country_iso);

$error = null;
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
    $error = $verificationResult . '. Please try again.';
}
*/
