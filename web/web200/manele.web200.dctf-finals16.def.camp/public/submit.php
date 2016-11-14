<!DOCTYPE html>
<html>
<body>

<?php
include_once('config.php');
if(isset($_POST["submit"])) {
    $name = @basename($_FILES["mp3"]["name"]);
    if(@strrpos($name, '.') !== FALSE && @end(explode(".", $name)) == 'mp3') {
    	#print_r($_FILES);
    	#echo '/usr/bin/id3v2 -l '.$_FILES["mp3"]["tmp_name"];
    	$answer = shell_exec('/usr/bin/id3v2 -l '.$_FILES["mp3"]["tmp_name"]);
    	if(stripos($answer, 'No ID3 tag') !== FALSE) {
    		echo 'No ID3 tag found.';
    		exit();
    	} else {
    		if(stripos($answer, 'No ID3v2 tag') !== FALSE) {
    			echo 'No ID3v2 tag found.';
    		} else {
    			$answer = @explode('id3v2 tag info', $answer)[1];
    			$lines  = @explode("\n", $answer);
    			foreach($lines as $line) {
    				if(stripos($line, 'TIT2') !== FALSE) {
    					$song = @trim(explode(":", $line)[1]);
    					$db->query('INSERT INTO `songs` (`ip`,`title`, `opened`) VALUES ("'.$_SERVER['REMOTE_ADDR'].'", "'.$db->real_escape_string($song).'", 0)');
    					echo 'Song Successfully saved.';
    					echo $db->error;
    					exit();
    				}
    			}
    			echo 'An error ocurred. Maybe you\'re trying to hack us the wrong way.';
    		}
    	}
    } else {
    	echo 'Invalid .mp3 extension';
    	exit();
    }
}

?>

<form action="submit.php" method="post" enctype="multipart/form-data">
    Select .mp3 to upload:
    <input type="file" name="mp3" id="mp3">
    <input type="submit" value="Upload song" name="submit">
</form>

</body>
</html>


<p>Thank you! I value your effort! I'll add this song to the website once I review it!</p>
