<?php

namespace Database\Verifications;

// setup database
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;

\ORM::configure('sqlite:' . __DIR__ . '/data.db');
\ORM::configure('logging', true);
$db = \ORM::getDb();

$db->exec('
CREATE TABLE IF NOT EXISTS verifications (
    id INTEGER PRIMARY KEY, 
    origin VARCHAR(255),
    pasa VARCHAR(20),
    state VARCHAR(255),
    redirect VARCHAR(255),
    ophash VARCHAR(255),
    block INTEGER,
    phone_formatted VARCHAR(255),
    phone_number VARCHAR(255),
    phone_enc VARCHAR(255),
    phone_last4 VARCHAR(4),
    country_number INTEGER,
    affiliate_account INTEGER,
    affiliate_ophash VARCHAR(255),
    affiliate_amount INTEGER,
    tries INTEGER,
    country_iso CHAR(2),
    b58_pubkey TEXT,
    twilio_uuid CHAR(36),
    twilio_expires UNSIGNED INTEGER,
    verification_code varchar(10),
    verification_success INTEGER DEFAULT(0),
    dt UNSIGNED INTEGER 
)');

/**
 * Gets a value indicating whether the account was already disbursed.
 *
 * @param $account
 * @return bool
 *
 */
function isDisbursed($account) {
    return (\ORM::forTable('verifications')
            ->where('pasa', $account)
        ->findOne() !== false);
}

/**
 * Updates the block number for all verified transactions.
 */
function updateMissingBlocks()
{
    $records = \ORM::forTable('verifications')
        ->whereNotNull('ophash')
        ->whereNull('block')
        ->findMany();
    foreach($records as $record) {
        try {
            $op = \Pascal\findOperation($record->ophash);
            if($op && (int)$op['block'] !== 0) {
                $record->ophash = $op['ophash'];
                $record->block = (int)$op['block'];
                $record->save();
            }
        }
        catch(\Exception $ex) {
            // do nothing.
        }

    }
}

/**
 * Gets a value indicating whether the account was already disbursed.
 *
 * @return array
 */
function getDisbursed() {
    return \ORM::forTable('verifications')
            ->where('verification_success', 1)
            ->findMany();
}

/**
 * Encrypts all "old" database records older than 1 hour.
 *
 * @throws \Exception
 */
function deleteOld()
{
    $oldRecords = \ORM::forTable('verifications')
        ->where('verification_success', 0)
        ->whereAnyIs([
            ['twilio_expires' => time()], // twilio expired
            ['dt' => time() - (60*60)] // older than 1 hour
        ], ['dt' => '<', 'twilio_expires' => '<'])
        ->findMany();

    foreach($oldRecords as $oldRecord) {
        $oldRecord->delete();
    }
}

const EXISTS_RUNNING = 'exists_running';
const EXISTS_DISBURSED = 'exists_disbursed';
function exists(PhoneNumber $phone)
{
    global $phoneUtil;
    $phoneFormatted = $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
    $runningVerification = \ORM::forTable('verifications')
        ->where('phone_formatted', $phoneFormatted)
        ->findOne();
    if($runningVerification !== false) {
        return [
            'type' => EXISTS_RUNNING,
            'verification' => $runningVerification
        ];
    }

    $disbursedVeridications = \ORM::forTable('verifications')
        ->where('phone_last4', substr($phoneFormatted, -4))
        ->findMany();

    foreach($disbursedVeridications as $disbursedVeridication) {
        if(password_verify($phoneFormatted, $disbursedVeridication->phone_enc)) {
            return [
                'type' => EXISTS_DISBURSED,
                'verification' => $disbursedVeridication
            ];
        }
    }

    return false;
}

/**
 * This will encrypt the phone number.
 *
 * @param int $verificationId
 */
function encryptPhone(int $verificationId)
{
    global $phoneUtil;

    $verification = getVerification($verificationId);
    $phoneInstance = $phoneUtil->parse($verification->phone_number, $verification->country_iso);

    $verification->phone_enc = password_hash(
        $phoneUtil->format($phoneInstance, PhoneNumberFormat::INTERNATIONAL),
        PASSWORD_DEFAULT
    );
    $verification->phone_last4 = substr($verification->phone_number, -4);
    $verification->phone_number = null;
    $verification->phone_formatted = null;
    $verification->save();
}

function dropAll()
{
    $vers = \ORM::forTable('verifications')
        ->findMany();
    foreach($vers as $ver) {
        $ver->delete();
    }
}

/**
 * Sets the disbursed pasa and ophash.
 *
 * @param int $verificationId
 */
function setPasa(int $verificationId, string $pasa, string $opHash)
{
    $verification = getVerification($verificationId);
    $verification->pasa = $pasa;
    $verification->ophash = $opHash;
    $verification->save();
}

/**
 * Adds a new verification record.
 *
 * @param string $phone
 * @param int $countryNumber
 * @param string $countryIso
 * @param string $publicKey
 * @return \ORM
 * @throws \Exception
 */
function addVerification(PhoneNumber $phone, array $data, int $countryNumber)
{
    global $phoneUtil;

    $verification = \ORM::forTable('verifications')->create();
    $verification->phone_formatted = $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
    $verification->country_number = $countryNumber;
    $verification->phone_number = $data['phone'];
    $verification->state = $data['state'];
    $verification->origin = $data['origin'];
    $verification->redirect = $data['redirect'];
    $verification->country_iso = $data['iso'];
    $verification->b58_pubkey = $data['public_key'];
    $verification->twilio_uuid = null;
    $verification->affiliate_account = $data['affiliate_account'];
    $verification->dt = time();
    $verification->save();

    return $verification;
}

/**
 * Checks whether the public key was already used.
 *
 * @param string $publicKey
 * @return \ORM
 * @throws \Exception
 */
function hasPublicKey($publicKey)
{
    return \ORM::forTable('verifications')
        ->where('b58_pubkey', $publicKey)
        ->where('verification_success', 1)
        ->findOne();
}

/**
 * Sets the twilio UUID.
 *
 * @param int $verificationId
 * @param string $uuid
 * @return bool|\ORM
 */
function setVerificationData(int $verificationId, string $uuid, int $secondsToExpire) {
    $verification = getVerification($verificationId);
    if($verification === null) {
        return false;
    }

    $verification->twilio_uuid = $uuid;
    $verification->twilio_expires = time() + $secondsToExpire;
    $verification->save();
    return $verification;
}

function updateTries(int $verificationId) {
    $verification = getVerification($verificationId);
    if($verification === null) {
        return false;
    }

    $verification->tries = $verification->tries + 1;
    $verification->save();
}

/**
 * Sets the twilio UUID.
 *
 * @param int $verificationId
 * @param string $uuid
 * @return bool|\ORM
 */
function setVerificationSuccess(int $verificationId, string $code) {
    $verification = getVerification($verificationId);
    if($verification === null) {
        return false;
    }

    $verification->verification_code = $code;
    $verification->verification_success = true;
    $verification->save();

    return $verification;
}

/**
 * Sets the twilio UUID.
 *
 * @param int $verificationId
 * @param string $uuid
 * @return bool|\ORM
 */
function setAffiliateSuccess(int $verificationId, string $opHash) {
    $verification = getVerification($verificationId);
    if($verification === null) {
        return false;
    }

    $verification->affiliate_ophash = $opHash;
    $verification->affiliate_amount = AFFILIATE_AMOUNT;
    $verification->save();

    return $verification;
}

/**
 * Gets a single verification
 *
 * @param int $verificationId
 * @return bool|\ORM
 */
function getVerification(int $verificationId) {
    return \ORM::forTable('verifications')->findOne($verificationId);
}

function getByStateAndOrigin($state, $origin) {
    return \ORM::forTable('verifications')
        ->where('state', $state)
        ->where('origin', $origin)
        ->findOne();
}

/**
 * Gets the list of successful affiliate verifications.
 *
 * @param $affiliateAccount
 * @return array|\IdiormResultSet
 */
function getAffiliateVerifications($affiliateAccount)
{
    return \ORM::forTable('verifications')
        ->where('affiliate_account', $affiliateAccount)
        ->whereNotNull('affiliate_ophash')
        ->findMany();
}

function isVerified(int $account) {
    return \ORM::forTable('verifications')
        ->where('pasa', $account)
        ->where('verification_success', 1)
        ->findOne();
}
