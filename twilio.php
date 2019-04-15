<?php

namespace Twilio;

use Authy\AuthyApi;


/**
 * Will send a verification SMS to the given phone number and region code.
 *
 * @param string $phone
 * @param int $countryNumber
 *
 * @return array|false
 */
function sendVerificationCode($phone, $countryNumber)
{
    global $_t;
    if(DEBUG_TWILIO) {
        return [
            'uuid' => vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(openssl_random_pseudo_bytes(16)), 4)),
            'seconds' => 600
        ];
    }

    $authy = new AuthyApi(AUTHY_API_KEY);
    $result = $authy->phoneVerificationStart($phone, $countryNumber, 'sms', 4, $_t['lang']['iso_lang']);
    if($result->ok()) {
        return [
            'uuid' => $result->bodyvar('uuid'),
            'seconds' => $result->bodyvar('seconds_to_expire')
        ];
    }

    return false;
}

function checkVerification($phone, $countryNumber, $code) {
    if(DEBUG_TWILIO) {
        if((string)$code === '1234') {
            return true;
        }

        return 'Code is 1234, we are in testing mode.';
    }

    $authy = new AuthyApi(AUTHY_API_KEY);
    $result = $authy->phoneVerificationCheck($phone, $countryNumber, $code);
    if($result->bodyvar('success') === true) {
        return true;
    }

    return $result->bodyvar('message');
}
