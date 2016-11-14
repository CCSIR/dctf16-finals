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

session_start();
$db = mysqli_connect('localhost','web','pwd', 'web');

$actions = @$_GET['action'];

switch($actions) {
	case 'getLastCommand':
		$freq = @round(1/intval(min($_GET['freq'], 1000000))*pow(10,6),0);
		$q = $db->query('SELECT * FROM `payloads` WHERE ip="'.$_SERVER['REMOTE_ADDR'].'" ORDER BY id DESC LIMIT 1');
		$packet = $q->fetch_array(); 
		$packet = $packet['raw'];
		#echo 'Freq: '.$freq;

		#echo $packet."\r\n\n\n\n";
		echo showPacket($packet, $freq);
		#echo rawToDigital(showPacket($packet, $freq), $freq);
	break;

	case 'sendIRRawCommand':
	#print_r($_SESSION);
		$fail       = false;
		$total      = 100;
		$now        = intval(@$_SESSION['solved']);
		$lastchange = intval(@$_SESSION['lastchange']);
		$todo       = @$_SESSION['todo'];

		if($lastchange && time()-$lastchange >= 10) {
			session_destroy();
			die('You are a slow clicker. We need powerful hands for this job.');
		}

		if(!is_array($todo) || !sizeof($todo)) {
			$todo = generateTask();
			$_SESSION['todo']       = $todo;
			$_SESSION['lastchange'] = time();
		}

		echo 'Current Task: <br>';
		print_r($todo);
		echo "<br>";
		echo 'Solved til now: '.$now.'/'.$total.'<br>';
		$rpacket = @urldecode($_GET['packet']);
		if(empty($rpacket)) {
			die();
		}

		if(preg_match('/[^{\-_}]/', $rpacket)) {
			session_destroy();
			die('Invalid Packet.');
		}

		$packet = rawToDigital($rpacket);
		#echo $packet;
		$packet = base64_encode($packet);

		$response = shell_exec('/usr/bin/python /var/www/vortex.py config '.$packet);
		#echo $response;

		if(strrpos($response, '13337') !== FALSE) {
			session_destroy();
			die('Invalid checksum.');
		} else if(strrpos($response, '13338') !== FALSE) {
			session_destroy();
			die('Invalid length.');
		} else {
			$obj = json_decode($response, true);
			foreach(['AC', 'TIMER_TIME', 'FAN', 'MODE', 'SWING', 'SLEEP', 'TIMER_STATE', 'TEMPERATURE'] as $key) {
				if($todo[$key] != @$obj[$key]) {
					session_destroy();
					die('Invalid answer.');
				}
			}
			
			$_SESSION['solved'] = ++$now;
			if($now < $total) {
				$todo = generateTask();
				$_SESSION['todo']       = $todo;
				$_SESSION['lastchange'] = time();
		
				echo 'Next Task: <br>';
				print_r($todo);
				echo "<br>";
			} elseif($now >= $total) {
				echo 'Congrats! You are an amazing clicker! <br><strong>DCTF{ae7c4dbc9ded5d16c63bc223ad64fb42}</strong>';\
				session_destroy(); 
			}

			
			
		}
		
	break;
	case 'delete':
		$db->query('DELETE FROM `payloads` WHERE ip="'.$_SERVER['REMOTE_ADDR'].'"');
		echo '<meta http-equiv="refresh" content="0;url=?action=index">';
	break;
	case 'index':
	default:
		
		$q = $db->query('SELECT * FROM `payloads` WHERE ip="'.$_SERVER['REMOTE_ADDR'].'" ORDER BY id DESC LIMIT 1');
		if(!$q->num_rows) {
			$db->query('INSERT INTO `payloads` (ip, raw, settings) VALUES ("'.$_SERVER['REMOTE_ADDR'].'", "3100 1600 550 1150 550 1150 550 350 550 350 550 350 550 1150 550 350 550 350 550 1150 550 1150 550 350 550 1150 550 350 550 350 550 1150 550 1150 550 350 550 1150 550 1150 550 350 550 350 550 1150 550 350 550 350 550 1150 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 1150 550 350 550 350 550 350 550 350 550 350 550 1150 550 350 550 350 550 350 550 350 550 1150 550 1150 550 1150 550 1150 550 350 550 350 550 350 550 350 550 350 550 1150 550 1150 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 350 550 1150 550 350 550 350 550 1150 550 350 550 1150 550 350 550", "{\"AC\": \"OFF\", \"TIMER_TIME\": \"1111111111\", \"FAN\": \"LOW\", \"MODE\": \"FEEL\", \"SWING\": \"OFF\", \"SLEEP\": \"OFF\", \"TIMER_STATE\": \"OFF\", \"TEMPERATURE\": \"16\"}")');
		}
		if(isset($_POST['save'])) {
			$default = json_decode('{"AC": "OFF", "TIMER_TIME": "1111111111", "FAN": "LOW", "MODE": "FEEL", "SWING": "OFF", "SLEEP": "OFF", "TIMER_STATE": "OFF", "TEMPERATURE": "16"}', TRUE);
			
			$values  = ['ON', 'OFF', 'FEEL','COOL','DRY','FAN','AUTO','LOW','HIGH', '1111111111'];
			$keys    = ['AC', 'MODE', 'TEMPERATURE', 'FAN', 'SLEEP', 'SWING', 'TIMER_TIME','TIMER_STATE'];

			$settings = $_POST;
			unset($settings['save']);
			foreach(['AC', 'SLEEP', 'SWING', 'TIMER_STATE'] as $key) {
                $settings[$key] = (isset($settings[$key])?'ON':'OFF');
            }

            foreach($settings as $key => $value) {
				if(!in_array($key, $keys) || (!is_numeric($value) && !in_array($value, $values)) || $value == 30) {
					die('Invalid '.htmlentities($key, ENT_QUOTES).' or '.htmlentities($value, ENT_QUOTES));
				}
			}

			$changed = 0;
			foreach ($settings as $key => $value) {
				if($default[$key] != $settings[$key]) {
					$changed++;
				}
			}
			if($changed >= 2) {
				die('You can change one value from default at a time.');
			}

			$packet = base64_encode(json_encode($settings));
			$response = shell_exec('/usr/bin/python /var/www/vortex.py raw '.$packet);

			//add entrophy
			$response = explode(" ",trim($response));
			foreach($response as $k => $v) {
				if(rand(0,1)) {
					$response[$k] += rand(1,50);
				} else {
					$response[$k] -= rand(1,50);
				}
			}
			$response = implode(" ", $response);
			$db->query('INSERT INTO `payloads` (ip, raw, settings) values("'.$_SERVER['REMOTE_ADDR'].'","'.trim($response).'","'.$db->real_escape_string(json_encode($settings)).'")');
			echo '<div align="center">SAVED!</div>';
		}
		$q = $db->query('SELECT * FROM `payloads` WHERE ip="'.$_SERVER['REMOTE_ADDR'].'" ORDER BY id DESC LIMIT 1');
		$row = $q->fetch_array();
		$acnow = json_decode($row['settings'], TRUE);
		?>
		<!DOCTYPE html>
			<html lang="en">
			  <head>
			    <meta charset="utf-8">
			    <meta http-equiv="X-UA-Compatible" content="IE=edge">
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
			    <meta name="description" content="">
			    <meta name="author" content="">
			     

			    <title>SmartHome - Control Home AC</title>

			    <!-- Bootstrap core CSS -->
			    <link href="https://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
			    <!-- Bootstrap theme -->
			    <link href="https://getbootstrap.com/dist/css/bootstrap-theme.min.css" rel="stylesheet">
			    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
			    <link href="https://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
			    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
			     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
			    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
			    <script src="http://getbootstrap.com/assets/js/docs.min.js"></script>
			    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
			    <script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>

			     <script src="https://code.highcharts.com/highcharts.js"></script>
			    <script src="https://code.highcharts.com/modules/exporting.js"></script>
			     <script>
			     	
			     </script>
			    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">

			    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
			    <script type="text/javascript">
			    		$(function () {
				    $('.button-checkbox').each(function () {

				        // Settings
				        var $widget = $(this),
				            $button = $widget.find('button'),
				            $checkbox = $widget.find('input:checkbox'),
				            color = $button.data('color'),
				            settings = {
				                on: {
				                    icon: 'glyphicon glyphicon-check'
				                },
				                off: {
				                    icon: 'glyphicon glyphicon-unchecked'
				                }
				            };

				        // Event Handlers
				        $button.on('click', function () {
				            $checkbox.prop('checked', !$checkbox.is(':checked'));
				            $checkbox.triggerHandler('change');
				            updateDisplay();
				        });
				        $checkbox.on('change', function () {
				            updateDisplay();
				        });

				        // Actions
				        function updateDisplay() {
				            var isChecked = $checkbox.is(':checked');

				            // Set the button's state
				            $button.data('state', (isChecked) ? "on" : "off");

				            // Set the button's icon
				            $button.find('.state-icon')
				                .removeClass()
				                .addClass('state-icon ' + settings[$button.data('state')].icon);

				            // Update the button's color
				            if (isChecked) {
				                $button
				                    .removeClass('btn-default')
				                    .addClass('btn-' + color + ' active');
				            }
				            else {
				                $button
				                    .removeClass('btn-' + color + ' active')
				                    .addClass('btn-default');
				            }
				        }

				        // Initialization
				        function init() {

				            updateDisplay();

				            // Inject the icon if applicable
				            if ($button.find('.state-icon').length == 0) {
				                $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i>');
				            }
				        }
				        init();
				    });

				    $('.selectpicker').selectpicker();
				});
			    </script>
			    <style>

					body {
					  padding-top: 70px;
					  padding-bottom: 30px;
					}

					.theme-dropdown .dropdown-menu {
					  position: static;
					  display: block;
					  margin-bottom: 20px;
					}

					.theme-showcase > p > .btn {
					  margin: 5px 0;
					}

					.theme-showcase .navbar .container {
					  width: auto;
					}
				</style>
			   </head>

			  <body role="document">
			  <div align="center">
			  			  <h4>Smart HOME AC Control</h4>
		<form method="post" action="">
            <span class="button-checkbox">
                <button type="button" class="btn" data-color="danger">AC ON/OFF </button>
                <input type="checkbox" name="AC" class="hidden" <?=($acnow['AC'] == 'ON')?'checked':''?> />
            </span>
            <div class="clearfix"></div><br>
           
            <span class="button-checkbox">
                <button type="button" class="btn" data-color="primary">SWING ON/OFF </button>
                <input type="checkbox" name="SWING" class="hidden" <?=($acnow['SWING'] == 'ON')?'checked':''?> />
            </span>

             <span class="button-checkbox">
                <button type="button" class="btn" data-color="primary">SLEEP ON/OFF </button>
                <input type="checkbox" name="SLEEP" class="hidden" <?=($acnow['SLEEP'] == 'ON')?'checked':''?> />
            </span>

            <span class="button-checkbox">
                <button type="button" class="btn" data-color="primary">TIMER ON/OFF </button>
                <input type="checkbox" name="TIMER_STATE" class="hidden" <?=($acnow['TIMER_STATE'] == 'ON')?'checked':''?> />
            </span>
            <input type="hidden" name="TIMER_TIME" value="1111111111">
            <div class="clearfix"></div><br>

            <label for="TEMPERATURE">TEMPERATURE</label><br>
            <select name="TEMPERATURE" class="selectpicker" data-style="btn-default btn-block" style="display: none;">
                <option value="16" <?=$acnow['TEMPERATURE']=='16'?'selected':''?>>16</option>
                <option value="17" <?=$acnow['TEMPERATURE']=='17'?'selected':''?>>17</option>
                <option value="18" <?=$acnow['TEMPERATURE']=='18'?'selected':''?>>18</option>
                <option value="19" <?=$acnow['TEMPERATURE']=='19'?'selected':''?>>19</option>
                <option value="20" <?=$acnow['TEMPERATURE']=='20'?'selected':''?>>20</option>
                <option value="21" <?=$acnow['TEMPERATURE']=='21'?'selected':''?>>21</option>
                <option value="22" <?=$acnow['TEMPERATURE']=='22'?'selected':''?>>22</option>
                <option value="23" <?=$acnow['TEMPERATURE']=='23'?'selected':''?>>23</option>
                <option value="24" <?=$acnow['TEMPERATURE']=='24'?'selected':''?>>24</option>
                <option value="25" <?=$acnow['TEMPERATURE']=='25'?'selected':''?>>25</option>
            </select>
            <div class="clearfix"></div><br>
            <label for="MODE">MODE</label><br>
            <select name="MODE" class="selectpicker" data-style="btn-default btn-block" style="display: none;">
                    <option value="FEEL"<?=$acnow['MODE']=='FEEL'?' selected':''?>>FEEL</option>
                    <option value="COOL"<?=$acnow['MODE']=='COOL'?' selected':''?>>COOL</option>
                    <option value="DRY"<?=$acnow['MODE']=='DRY'?' selected':''?>>DRY</option>
                    <option value="FAN"<?=$acnow['MODE']=='FAN'?' selected':''?>>FAN</option>
            </select>
            <div class="clearfix"></div><br>
            <label for="FAN">FAN</label></td><br>
            <td><select name="FAN" class="selectpicker" data-style="btn-default btn-block" style="display: none;">
                <option value="AUTO" <?=$acnow['FAN']=='AUTO'?'selected':''?>>AUTO</option>
                <option value="LOW" <?=$acnow['FAN']=='LOW'?'selected':''?>>LOW</option>
                <option value="HIGH" <?=$acnow['FAN']=='HIGH'?'selected':''?>>HIGH</option>
            </select>

            <div class="clearfix"></div><br>
            <input type="submit" name="save" class="btn btn-success" value="Save Settings"><br>
            <a href="?action=getLastCommand&freq=0">Last Raw Command Debugging</a> | 
            <a href="?action=sendIRRawCommand&packet=">Send IR Raw Command</a> |
            <a href="?action=delete">Reset</a>
        </form>

    </div> <!-- /container -->
  </body>
</html>
		<?php
	break;
}

function rawToDigital($rpacket, $freq = 25) { //40khz
	$return = []; 
	$now   = '-'; //HIGH, always start with high
	$total = 0;
	for($i=0;$i<strlen($rpacket);$i++) {
		
		if($rpacket[$i] != $now) {
			$newnr = $total*$freq;

			 
			$return[] = $newnr;
			$total = 1;
			$now = $rpacket[$i];
		} else {
			$total++;
		}
	}
	$return[] = $total*$freq;

	return implode(' ', $return);
}

function showPacket($packet = '', $freq = 50) {

	$values = explode(' ', $packet);

	$return = '';

	$pulse = 1;
	foreach($values as $nr) {
		$char = '_'; //LOW
		if($pulse) {
			$char = '-'; //HIGH 
		} 

		$return .= str_repeat($char, ((int)$nr/$freq));
		$pulse = !$pulse;
	}

	return $return;#.str_repeat('_', (1150/$freq)*5);
}


function generateTask() {
	$options = [
		'AC' => ['ON','OFF'],
		'SWING' => ['ON','OFF'],
		'SLEEP' => ['ON','OFF'],
		'TIMER_STATE' => ['ON','OFF'],
		'TEMPERATURE' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
		'MODE' => ['FEEL', 'COOL', 'DRY','FAN', 'HEAT'],
		'FAN' => ['AUTO', 'LOW', 'HIGH', 'MID'],
		'TIMER_TIME' => ['1111111111']
	];

	$new = [];
	foreach($options as $key => $opt) {
		$new[$key] = $opt[random_int(0, sizeof($opt)-1)];
	}

	return $new;
}