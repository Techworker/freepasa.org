<?php

include './../../bootstrap.php';

header('Content-Type: application/json');

// current data, either from GET or then from POST
$data = [
    'iso' => null,
    'phone' => ''
];

if (isset($_GET['phone_country_iso'])) {
    $data['iso'] = $_GET['phone_country_iso'];
}
if (isset($_GET['phone_country_number'])) {
    $number = (int)$_GET['phone_country_number'];
    foreach($countries as $country) {
        if($number === $country['number']) {
            $data['iso'] = $country['iso'];
        }
    }
}

if (isset($_GET['phone'])) {
    $data['phone'] = $_GET['phone'];
}

if($data['iso'] !== null) {
    $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
}
try {
    $phoneInstance = $phoneUtil->parse($data['phone'], $data['iso']);
}
catch(\Exception $ex) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid phone number'
    ]);
    return;
}
$val = $phoneUtil->isValidNumber($phoneInstance);
if ($val === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid phone number'
    ]);
    return;
}

$verification = \Database\Verifications\exists($phoneInstance);
if($verification === false) {
    echo json_encode([
        'success' => false
    ]);

    return;
}


$response = [
    'success' => true,
    'disbursed' => $verification['type'] === \Database\Verifications\EXISTS_DISBURSED
];
echo json_encode($response);