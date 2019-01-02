<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

echo json_encode([
    'available_accounts' => \Pascal\getWalletAccountsCount() - 2,
    'account_funds' => \Pascal\getAccount(ACCOUNT_SIGNER)['balance'],
    'account_pubkey' => WALLET_PUBKEY,
    'affiliate_funds' => \Pascal\getAccount(ACCOUNT_AFFILIATE)['balance'],
    'affiliate_amount' => AFFILIATE_AMOUNT / 10000,
    'affiliate_account' => ACCOUNT_AFFILIATE,
    'debug' => DEBUG,
    'debug_twilio' => DEBUG_TWILIO,
]);

return;