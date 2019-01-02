<?php

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load(true);

define('DEBUG', filter_var(getenv('DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('DEBUG_TWILIO', filter_var(getenv('DEBUG_TWILIO'), FILTER_VALIDATE_BOOLEAN));
define('DOMAIN', getenv('DOMAIN'));
define('NODE', getenv('NODE'));
define('WALLET_PUBKEY', getenv('WALLET_PUBKEY'));
define('ACCOUNT_SIGNER', (int)getenv('ACCOUNT_SIGNER'));
define('ACCOUNT_AFFILIATE', (int)getenv('ACCOUNT_AFFILIATE'));
define('ACCOUNT_FAUCET', (int)getenv('ACCOUNT_FAUCET'));
define('AUTHY_API_KEY', getenv('AUTHY_API_KEY'));
define('HASHIDS_SALT', getenv('HASHIDS_SALT'));
define('AFFILIATE_AMOUNT', (int)getenv('AFFILIATE_AMOUNT'));
define('FAUCET_AMOUNT', (int)getenv('FAUCET_AMOUNT'));


if(!DEBUG) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// start session
session_name('pascal');
session_start();

// clean up database
\Database\Verifications\deleteOld();
\Database\Verifications\updateMissingBlocks();

try {
    $nodeStatus = \Pascal\nodeStatus();
    $nodeAccount = \Pascal\getAccount(ACCOUNT_SIGNER);
}
catch(\Exception $ex) {
    die('Node not running, please inform an admin on discord.');
}

foreach($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}
foreach($_GET as $key => $value) {
    $_GET[$key] = trim($value);
}

// utility to check bad phone numbers
$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$countries = [];
$countryNamesDb = include __DIR__ . '/vendor/umpirsky/country-list/data/en/country.php';
foreach($phoneUtil->getSupportedRegions() as $iso) {
    $countries[$iso] = [
        'name' => $countryNamesDb[$iso],
        'iso' => $iso,
        'number' => $phoneUtil->getCountryCodeForRegion($iso)
    ];
}

uasort($countries, function($r1, $r2) {
    return $r1['name'] <=> $r2['name'];
});

$hashids = new Hashids\Hashids(HASHIDS_SALT, 10);