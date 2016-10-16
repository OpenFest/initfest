<?php
function getSchedConfig($year = 2015) {
	$globalConfig = [
		'lang' => 'bg',
		'cfp_url' => 'https://cfp.openfest.org',
		'cut_len' => 70,
	];
	
	$config = [
		2015 => [
			'allowedHallIds' => [6, 7, 8],
			
		],
	];
	
	return array_merge($globalConfig, $config[$year]);
}
