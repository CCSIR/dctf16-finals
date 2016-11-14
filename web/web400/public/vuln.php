<?php

include_once('config.php');
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

$q = $db->query('SELECT * FROM rows where id="'.$_GET['id'].'"');
if($q && $q->num_rows) {
	$r = $q->fetch_array();
	echo $r['content'];
}

echo $db->error;
$ips->analyze();

if(sizeof($ips->anomalies)) {
	ob_end_clean();
	
	if(DEBUG == TRUE) {
		$ips->debug();
	}
	die('Attacker detected.');
}
