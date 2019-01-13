<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

if(!isset($_GET['pubkey'])) {
    echo json_encode([
        'error' => 'Missing pubkey value'
    ]);
    return;
}

$pubkey = trim($_GET['pubkey']);

$verification = \Database\Verifications\hasPublicKey($pubkey);
if($verification !== false) {
    echo json_encode([
        'used' => true,
        'pasa' => $verification->pasa,
        'date' => $verification->ts
    ]);
    return;
} else {
    echo json_encode([
        'used' => false
    ]);
    return;
}

