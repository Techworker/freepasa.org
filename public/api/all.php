<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

$verifications = \Database\Verifications\getDisbursed();
$data = [];
foreach($verifications as $verification) {
    $data[] = [
        'time' => $verification['dt'],
        'afac' => $verification['affiliate_account'],
        'block' => $verification['block'],
        'ophash' => $verification['affiliate_ophash'],
        'amount' => $verification['affiliate_amount'] / 10000,
    ];
}

echo json_encode($data);