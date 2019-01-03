<?php

include './../bootstrap.php';

if(DEBUG === false) {
    die('blocked');
}

$verificationIdEncoded = $_GET['id'];
$verificationId = \Helper\decodeId($verificationIdEncoded);
if($verificationId === null) {
    die('not found');
}
$verification = \Database\Verifications\getVerification($verificationId);
if($verification === false) {
    die('unable to delete');
}

$verification->delete();

die('deleted..');