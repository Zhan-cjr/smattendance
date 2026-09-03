<?php
$data = [
    'time' => date('Y-m-d H:i:s'),
    'headers' => getallheaders(),
    'server' => $_SERVER,
    'body' => file_get_contents('php://input'),
];

file_put_contents('dump.log', json_encode($data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

echo "OK";
