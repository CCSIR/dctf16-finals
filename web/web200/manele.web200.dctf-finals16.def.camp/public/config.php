<?php

/* fake "IP" */
function setSession() {
	$length = 20;
	$value = sha1(substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length));
	setcookie("_session", $value, time()+(3600*24*7*365));
	return $value;
}

function isSessionValid($s) {
	if(strlen($s) != 40) {
		return FALSE;
	}
	if(!preg_match('/^[a-f0-9]{40}$/i', $s)) {
		return FALSE;
	}
	return TRUE;
}

$cookie = isset($_COOKIE['_session'])?$_COOKIE['_session']:setSession();
if(!isSessionValid($cookie)) {
	$cookie = setSession();
}
$_SERVER['REMOTE_ADDR'] = $cookie;

/* end fake "IP" */
$db = mysqli_connect('localhost', 'web', 'pwd', 'web');