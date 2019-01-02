<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

if(!isset($_GET['state'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing state parameter'
    ]);

    return;
}

if(!isset($_GET['origin'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing origin parameter'
    ]);

    return;
}

$state = $_GET['state'];
$origin = $_GET['origin'];

$verification = \Database\Verifications\getByStateAndOrigin($state, $origin);
if($verification === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Unknown verification'
    ]);

    return;
}


echo json_encode([
    'success' => true,
    'finished' => $verification->block !== null,
    'ophash' => $verification->ophash,
    'block' => $verification->block,
    'pasa' => $verification->pasa
]);