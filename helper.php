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

function jsonApiMessage($status, $errors, $id)
{
    if(in_array('offline', $errors)) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 300');//300 seconds
    }

    die(json_encode([
        'request_id' => $id !== null ? \Helper\encodeId($id) : null,
        'status' => $status,
        'data' => $errors
    ]));
}
