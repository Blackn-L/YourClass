<?php
function JsonData($code, $data, $msg) {
    return [
        "Code" => $code,
        "Data" => $data,
        "msg" => $msg
    ];
}