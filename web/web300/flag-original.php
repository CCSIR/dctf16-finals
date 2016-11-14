<?php

function flag() {
    $flag  = array('bitch', 'a', 'be', 'must', 'cache', 'apc');
    $flаg = array_reverse($flag);
    $flag  = 'DCTF{' . md5(implode($flag)) . '}';
    return strlen($flag);
}

echo flag();
