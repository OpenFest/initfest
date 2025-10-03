<?php
function compareKeys($a, $b, $key) {
	$valA = &$a[$key];
	$valB = &$b[$key];
	
	return ($valA < $valB) ? -1 : (($valA > $valB) ? 1 : 0);
}

function loadData($config, $lang = 'en') {
	$data = [];
	$curl = new SmartCurl($config['baseUrl']);
	$json = $curl->getUrl('schedule/export/schedule.json?lang=' . $lang);

	if ($json === false) {
		echo 'get failed: ', $filename, PHP_EOL;
		exit;
	}
		
	$decoded = json_decode($json, true);

	if ($decoded === false) {
		echo 'decode failed: ', $filename, PHP_EOL;
		exit;
	}

	return $decoded;
}
