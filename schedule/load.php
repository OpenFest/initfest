<?php
require __DIR__ . DIRECTORY_SEPARATOR . 'class.SmartCurl.php';

$base_url = 'https://cfp.openfest.org/api/conferences/2/';

$filenames = [
	'events'			=>	'events.json',
	'speakers'			=>	'speakers.json',
	'tracks'			=>	'tracks.json',
	'event_types'		=>	'event_types.json',
	'halls'				=>	'halls.json',
	'slots'				=>	'slots.json',
];


if (empty($allowedhallids)) {
	$allowedhallids = array (6,7,8);
}
$data = [];

foreach ($filenames as $name => $filename) {
	$curl = new SmartCurl($base_url);
	$json = $curl->getUrl($filename);

	if ($json === false) {
		echo 'get failed: ', $filename, PHP_EOL;
		exit;
	}
	
	$decoded = json_decode($json, true);

	if ($decoded === false) {
		echo 'decode failed: ', $filename, PHP_EOL;
		exit;
	}
	
	$add = true;
	switch ($name) {
		case 'halls':
			$ret = array();
			foreach($decoded as $id => $hall) {
					if (in_array($id, $allowedhallids)) $ret[$id] = $hall['name'];
			}
			$decoded = $ret;
		break;
		case 'slots':
			$decoded = array_map(function($el) {
				foreach (['starts_at', 'ends_at'] as $key) {
					$el[$key] = strtotime($el[$key]);
				}
				
				return $el;
			}, $decoded);
		break;
	}
	
	$data[$name] = $decoded;
}

function compareKeys($a, $b, $key) {
	$valA = &$a[$key];
	$valB = &$b[$key];
	
	return ($valA < $valB) ? -1 : (($valA > $valB) ? 1 : 0);
}

uasort($data['slots'], function($a, $b) {
	return compareKeys($a, $b, 'starts_at') ?: compareKeys($a, $b, 'hall_id');
});

//array_pop($data['halls']);

return $data;
