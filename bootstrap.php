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
define('WALLET_PASSWORD', getenv('WALLET_PASSWORD'));

if(!DEBUG) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// start session
session_name('pascal');
session_start();

register_shutdown_function(function() {
    \Pascal\lock();
});


$link = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($link);
parse_str($parsedUrl['query'], $parsedParams);
$parsedParams['lang'] = '--LANG--';
$link = DOMAIN . $parsedUrl['path'] . '?' . http_build_query($parsedParams);

$supportedLanguages = include(__DIR__ . '/lang/supported.php');
$_t = ['fallback' => include(__DIR__ . '/lang/' . $supportedLanguages['fallback'] . '.php')];

if(!isset($_GET['lang']))
{
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $headerLocales = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($headerLocales as $locale) {
            $language = substr($locale, 0, 2);
            if(isset($supportedLanguages['all'][$locale])) {
                return header('Location: ' . str_replace('--LANG--', $locale, $link));
            }

            if(isset($supportedLanguages['all'][$language])) {
                return header('Location: ' . str_replace('--LANG--', $locale, $link));
            }
        }
    }

    return header('Location: ' . str_replace('--LANG--', $supportedLanguages['fallback'], $link));
}


if(isset($_GET['lang']) && isset($supportedLanguages['all'][$_GET['lang']])) {
    $_t['active'] = include(__DIR__ . '/lang/' . $supportedLanguages['all'][$_GET['lang']]['folder'] . '.php');
    $_t['lang'] = $supportedLanguages['all'][$_GET['lang']];
} else {
    $_t['active'] = include(__DIR__ . '/lang/' . $supportedLanguages['fallback'] . '.php');
    $_GET['lang'] = $supportedLanguages['fallback'];
    $_t['lang'] = $supportedLanguages['all'][$supportedLanguages['fallback']];
}


include(__DIR__ . '/lang/translate.php');

// clean up database
\Database\Verifications\deleteOld();
\Database\Verifications\updateMissingBlocks();

try {
    $nodeStatus = \Pascal\nodeStatus();
    $nodeAccount = \Pascal\getAccount(ACCOUNT_SIGNER);
    $ctSystem = count(array_unique([ACCOUNT_SIGNER, ACCOUNT_AFFILIATE, ACCOUNT_FAUCET]));
    $accountsAvailable = \Pascal\getWalletAccountsCount() - $ctSystem;
}
catch(\Exception $ex) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 300');//300 seconds
    die('Node not running, please inform an admin on discord.');
}

\Pascal\unlock();

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
