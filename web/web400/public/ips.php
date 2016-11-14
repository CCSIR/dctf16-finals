<?php

class IPS {
	protected $listenPort   = 80;
	protected $mode         = MODE_TRAINING;
	protected $req_keys     = ['RESPONSE','POST','GET','COOKIE'];//FILE, SERVER
	protected $data         = ['RESPONSE' => [],'POST' => [],'GET' => [],'COOKIE' => []];
	protected $learned      = ['RESPONSE' => [],'POST' => [],'GET' => [],'COOKIE' => []];
	protected $tolerance    = [
								//'OVERALL'  => 0.20,
								'RESPONSE' => 5.15,
								'POST' 	   => 0.15,
								'GET'	   => 0.15,
								'SERVER'   => 0.70,
								'COOKIE'   => 0.15
							];
	protected $request           = NULL;
	public $anomalies         = [];
	protected $anomalyCount      = 0;
	protected $learningThreshold = 1.1;

	public function __construct($mode = MODE_MIXED, $listenPort = 80) {
		global $db;
		$this->mode       = $mode;
		$this->listenPort = $listenPort;
		$this->request    = $db->real_escape_string($_SERVER['PHP_SELF']);

		$this->obStart();
		$this->getKnowledge();
		$this->doRequestForensics();
	}

	public function __destruct() {
		global $db;
		if($this->mode == MODE_TRAINING || $this->mode == MODE_MIXED) {

			foreach($this->req_keys as $key) {
				foreach($this->data[$key] as $k => $v) {
					if(isset($this->learned[$key][$k])) {
						foreach($this->data[$key][$k] as $fk => $fv) {
							$t = round((float)($this->learned[$key][$k][$fk]+$fv)/2,1); //easy learn new things

							if($this->learned[$key][$k][$fk]) {
								$thres = (float)(abs($this->learned[$key][$k][$fk]-$fv)/$this->learned[$key][$k][$fk]);
							} else {
								$thres = 0;
							}

							if($thres <= $this->learningThreshold) {
								$this->learned[$key][$k][$fk] = $t;
							}
						} 
						$db->query('UPDATE knowledge SET value = "'.$db->real_escape_string(json_encode($this->learned[$key][$k])).'" WHERE type="'.$key.'" AND name="'.$db->real_escape_string($k).'" AND request="'.$this->request.'" and ip="'.$_SERVER["REMOTE_ADDR"].'"');
					} else {
						$this->learned[$key][$k] = $this->data[$key][$k];
						$db->query('INSERT INTO knowledge (name, value, type, request, ip) VALUES ("'.$db->real_escape_string($k).'","'.$db->real_escape_string(json_encode($this->learned[$key][$k])).'", "'.$key.'","'.$this->request.'","'.$_SERVER["REMOTE_ADDR"].'")');
					}
				}	
			}
		}		
	}

	protected function doRequestForensics() {
		foreach(['_POST','_GET','_SERVER','_COOKIE'] as $globalKey) {
			$globalObj = getSuperGlobal($globalKey);

			if(is_array($globalObj) && sizeof($globalObj)) {
				foreach($globalObj as $key => $value) {
					if(is_string($value)) {
						$this->data[substr($globalKey, 1)][strtolower($key)] = $this->gatherIntelligenceFromData($value);
					}
				}	
			}
		}
		//print_r($this->data);
	}

	protected function gatherIntelligenceFromData($content) {
		$intel = [
			'totallen' => strlen($content),
			'alpha'    => 0,
			'alphanums'=> 0,
			'nums'     => 0,
			'special'  => 0,
			'ascii'    => 0,
			'nonascii' => 0,
		];

		for($i=0;$i<$intel['totallen'];$i++) {
			$intel['alpha']     += (int)ctype_alpha($content[$i]);
			$intel['alphanums'] += (int)(ctype_alpha($content[$i])||ctype_digit($content[$i]));
			$intel['nums']      += (int)ctype_digit($content[$i]);
			$intel['special']   += (int)(!ctype_alpha($content[$i])&&!ctype_digit($content[$i]));
			$intel['ascii']		+= (int)ctype_print($content[$i]);
			$intel['nonascii']  += (int)!ctype_print($content[$i]);
		}

		//print_r($intel);

		return $intel;
	}

	protected function obStart() {
		ob_start();
	}

	public function analyze() {
		$anomalies = 0;

		$this->gatherIntelligenceFromResponse();

		foreach($this->req_keys as $key) {
			$o = $this->data[$key];
			foreach($o as $k => $v) {
				foreach($v as $fk => $fv) {
					if(isset($this->learned[$key][$k][$fk])) {
						//if has at least one learn
						if($this->learned[$key][$k][$fk]) {
							//get percent difference
							$t = (float)(abs($this->learned[$key][$k][$fk]-$fv)/$this->learned[$key][$k][$fk]);
						} else {
							$t = 0;
						}

						//if it's not tolerable, it's anomaly
						if($t > (float)$this->tolerance[$key]) {
							$anomalies++;
							$this->anomalies[htmlentities($key.'_'.$k.'_'.$fk, ENT_QUOTES)] = [
									'tolerance' 	  => $t,
									'learnedVal'	  => $this->learned[$key][$k][$fk],
									'learnNew' 		  => $t<=$this->learningThreshold];
						}
					}	
				}
			}
		}

		$this->anomalyCount = $anomalies;
		return $anomalies;
	}

	protected function gatherIntelligenceFromResponse() {
		$response = ob_get_contents();
		$this->data['RESPONSE'] = ['html' => $this->gatherIntelligenceFromData($response),
								   'text' => $this->gatherIntelligenceFromData(strip_tags($response))
								   ];
	}

	protected function getKnowledge() {
		global $db;
		foreach($this->req_keys as $key) {
			$query = $db->query('SELECT * FROM `knowledge`  WHERE request="'.$this->request.'" AND type="'.$key.'" and ip="'.$_SERVER["REMOTE_ADDR"].'"');
			while($row = $query->fetch_array()) {
				$this->learned[$key][$row['name']] = json_decode($row['value'], TRUE);
			}
		}
	}

	public function debug() {

		$response = '';
		if(sizeof($this->anomalies)) {
			$response .= "\r\nTotal: ".$this->anomalyCount."\r\n";
			$response .= "Anomalies:\r\n"; 
			$response .= json_encode($this->anomalies);
			$response .= "\r\nTolerance:\r\n";
			$response .= json_encode($this->tolerance);
			$response .= "\r\nLearning Threshold:".$this->learningThreshold;
		}
		#echo str_replace("\r\n", "<br>",$response);
		header('X-Debug: '.base64_encode($response));	
	}
}

