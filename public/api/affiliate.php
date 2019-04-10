<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

if(!isset($_GET['afac'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing afac parameter'
    ]);

    return;
}

$afac = (int)$_GET['afac'];
if($afac === 0) {
    die('no');
}

$verifications = \Database\Verifications\getAffiliateVerifications($afac);
$data = [];
foreach($verifications as $verification) {
    $data[] = [
        'time' => $verification['dt'],
        'block' => $verification['block'],
        'account' => $verification['pasa'],
        'ophash' => $verification['affiliate_ophash'],
        'amount' => $verification['affiliate_amount'] / 10000,
    ];
}

echo json_encode($data);
