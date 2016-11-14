<?php

if ($origin === 'null') {
    // phantom.js hack: for some reason CORS requests fail even
    // if the Origin header is "null"
    header("Access-Control-Allow-Origin: *");
} else {
   header("Access-Control-Allow-Origin: null");
}

if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    echo 'DCTF{' . md5('cross origin requests are like magic') . '}' . PHP_EOL;
} else {
    echo 'only admins are allowed to use the API' . PHP_EOL;
}
