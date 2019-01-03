<?php

namespace Helper;

function isAjax() {
    /* AJAX check  */
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
}


function decodeId($encoded) {
    global $hashids;
    $ids = $hashids->decode($encoded);
    if(count($ids) > 0) {
        return $ids[0];
    }

    return null;
}

function encodeId($id) {
    global $hashids;
    return $hashids->encode($id);
}