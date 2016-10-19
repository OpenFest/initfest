<?php
function compareKeys($a, $b, $key) {
	$valA = &$a[$key];
	$valB = &$b[$key];
	
	return ($valA < $valB) ? -1 : (($valA > $valB) ? 1 : 0);
}

function loadData($config) {
	$filenames = [
		'events'			=>	'events.json',
		'speakers'			=>	'speakers.json',
		'tracks'			=>	'tracks.json',
		'event_types'		=>	'event_types.json',
		'halls'				=>	'halls.json',
		'slots'				=>	'slots.json',
	];

	$data = [];
	$curl = new SmartCurl($config['cfp_url'] . '/api/conferences/');
	
	foreach ($filenames as $name => $filename) {
		$json = $curl->getUrl($config['conferenceId'] . '/' . $filename);

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
				$decoded = array_map(function($el) {
					return $el['name'];
				}, $decoded);
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

	uasort($data['slots'], function($a, $b) {
		return compareKeys($a, $b, 'starts_at') ?: compareKeys($a, $b, 'hall_id');
	});

	return $data;
}
