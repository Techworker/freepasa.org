<?php

namespace Pascal;

function rpc(string $method, array $params = [])
{
    static $id = 0;

    $rpc = [
        'id' => $id++,
        'jsonrpc' => '2.0',
        'method' => $method,
        'params' => $params,
    ];
    if(!isset($rpc['params']['fee'])) {
        $rpc['params']['fee'] = '0.0000';
    } else {
        $rpc['params']['fee'] = '0.0001';
    }

    $ch = curl_init(NODE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rpc));

    $response = curl_exec($ch);
    \curl_close($ch);
    if ($response === false) {
        throw new \Exception('Unable to connect to node ' . NODE, 100);
    }

    $result = json_decode($response, true);
    if(isset($result['result'])) {
        return $result['result'];
    }

    if(isset($result['error']))
    {
        // if free didn't work out, try with fee
        if($rpc['params']['fee'] === '0.0000') {
            return rpc($method, $rpc['params']);
        }

        throw new \Exception($result['error']['message'], $result['error']['code']);
    }

    die('Invalid result: ' . print_r($result, true));
}

function hasPasa($publicKey)
{
    return count(rpc('findaccounts', ['b58_pubkey' => $publicKey]));
}


function getDisbursableAccount()
{
    $availableAccounts = rpc('getwalletaccounts', [
        'b58_pubkey' => WALLET_PUBKEY,
        'start' => 0,
        'max' => 10
    ]);

    foreach($availableAccounts as $account) {
        $pubKey = rpc('decodepubkey', ['enc_pubkey' => $account['enc_pubkey']]);
        if($pubKey['b58_pubkey'] !== WALLET_PUBKEY) {
            continue;
        }
        if(\Database\Verifications\isDisbursed($account['account']) === false &&
            $account['account'] != ACCOUNT_SIGNER &&
            $account['account'] != ACCOUNT_FAUCET &&
            $account['account'] != ACCOUNT_AFFILIATE)
        {
            return $account;
        }
    }

    return null;
}

function sendPasa($b58Pubkey, $last4)
{
    $account = getDisbursableAccount();
    $result = rpc('changekey', [
        'account' => $account['account'],
        'account_signer' => ACCOUNT_SIGNER,
        'new_b58_pubkey' => $b58Pubkey,
        'payload' => encodePayload('freepasa.org'),
        'payload_method' => 'none'
    ]);

    addPascalToAccount($account['account']);

    return [
        'ophash' => $result['ophash'],
        'account' => $account['account']
    ];
}

function lock() {
    rpc('lock', []);
}

function unlock() {
    rpc('unlock', ['pwd' => WALLET_PASSWORD]);
}

function addPascalToAccount($account)
{
    // fetch account balance
    $accountData = getAccount($account);
    $balance = (float)$accountData['balance'];
    // if the account has a balance..
    if($balance > 0)
    {
        // and the amount is bigger than the faucet amount
        if($balance * 10000 > FAUCET_AMOUNT) {
            // ..send the diff to the faucet
            $result = rpc('sendto', [
                'target' => ACCOUNT_FAUCET,
                'sender' => $account,
                'amount' => (($balance * 10000) - (FAUCET_AMOUNT)) / 10000,
                'payload' => encodePayload('freepasa.org / getting started with 0.0010'),
                'payload_method' => 'none'
            ]);
        } else {
            // just send the delat
            $result = rpc('sendto', [
                'target' => $account,
                'sender' => ACCOUNT_FAUCET,
                'amount' => (FAUCET_AMOUNT / 10000) - $balance,
                'payload' => encodePayload('freepasa.org / getting started with 0.0010'),
                'payload_method' => 'none'
            ]);
        }
    } else {
        $result = rpc('sendto', [
            'target' => $account,
            'sender' => ACCOUNT_FAUCET,
            'amount' => FAUCET_AMOUNT / 10000,
            'payload' => encodePayload('freepasa.org / getting started with 0.0010'),
            'payload_method' => 'none'
        ]);
    }

    return $result['ophash'];
}

function sendAffiliate($affiliateAccount, $disbursedAccount)
{
    $result = rpc('sendto', [
        'target' => $affiliateAccount,
        'sender' => ACCOUNT_AFFILIATE,
        'amount' => AFFILIATE_AMOUNT / 10000,
        'payload' => encodePayload('affiliate for ' . $disbursedAccount),
        'payload_method' => 'none'
    ]);

    return $result['ophash'];
}

function encodePayload($payload) {
    $hex = '';
    for ($i = 0; $i < strlen($payload); $i++)
    {
        $ord = ord($payload[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strtoupper($hex);
}

function nodeStatus() {
    return rpc('nodestatus');
}

function decodePublicKey($b58PubKey) {
    rpc('decodepubkey', ['b58_pubkey' => $b58PubKey]);
}

function withChecksum($account) {
    return $account . '-' . ((((int)$account * 101) % 89)+10);
}

function findOperation($opHash) {
    return rpc('findoperation', ['ophash' => $opHash]);
}

function getAccount(int $account) {
    return rpc('getaccount', ['account' => $account]);
}

function getWalletAccountsCount() {
    return rpc('getwalletaccountscount', ['b58_pubkey' => WALLET_PUBKEY]);
}
