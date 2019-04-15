<?php

function t_($area, $key, ...$params) {
    global $_t;

    $areaValues = null;
    if(!isset($_t['active'][$area])) {
        $areaValues = $_t['fallback'][$area];
    } else {
        $areaValues = $_t['active'][$area];
    }

    if(isset($areaValues[$key])) {
        return vsprintf($areaValues[$key], $params);
    }

    return vsprintf($_t['fallback'][$area][$key], $params);
}
