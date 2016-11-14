<!DOCTYPE html>
<html>
<head>
	<title>Moderate songs</title>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
</head>
<body>

<?php

include_once('config.php');
if(!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
	die('You are not allowed.');
}

if(!isset($_GET['id'])) {
	$query = $db->query('SELECT * FROM songs WHERE opened=0');
	while($row = $query->fetch_array()) {
		echo '<a href="moderate.php?id='.$row['id'].'">'.htmlentities($row['title'], ENT_QUOTES).'</a><br>';
	}
} else {
	$query = $db->query('SELECT * FROM songs where id="'.intval($_GET['id']).'"');
	echo $query->fetch_array()['title'];
	$db->query('UPDATE songs set opened=1 WHERE id="'.intval($_GET['id']).'"');
}

?>
</body>
</html>
