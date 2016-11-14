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

define('MODE_TRAINING', 1337);
define('MODE_OPERATIONAL', 1338);
define('MODE_MIXED', 1339);
define('DEBUG', TRUE);
 
$db = mysqli_connect('localhost','web','pwd', 'web');

include_once('ips.php');
$ips = new IPS();

function &getSuperGlobal($name) {
    return $GLOBALS["$name"];
}


///init default for this ip
$q = $db->query('SELECT * FROM knowledge WHERE type="GET" and ip="'.$_SERVER['REMOTE_ADDR'].'"');
if(!$q->num_rows) {
	$db->query("INSERT INTO `knowledge` (`request`, `name`, `value`, `type`, `ip`) VALUES
('vuln.php', 'html', '{\"totallen\":774.5,\"alpha\":546.7,\"alphanums\":588.5,\"nums\":41.8,\"special\":186.1,\"ascii\":771.9,\"nonascii\":2.7}', 'RESPONSE', '".$_SERVER["REMOTE_ADDR"]."'),
('vuln.php', 'text', '{\"totallen\":774.5,\"alpha\":546.7,\"alphanums\":588.5,\"nums\":41.8,\"special\":186.1,\"ascii\":771.9,\"nonascii\":2.7}', 'RESPONSE', '".$_SERVER["REMOTE_ADDR"]."'),
('vuln.php', 'id', '{\"totallen\":1,\"alpha\":0,\"alphanums\":1,\"nums\":1,\"special\":0,\"ascii\":1,\"nonascii\":0}', 'GET', '".$_SERVER["REMOTE_ADDR"]."');");	
}

