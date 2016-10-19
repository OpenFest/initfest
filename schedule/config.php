<?php
function getSchedConfig($year = 2015) {
	$globalConfig = [
		'lang' => 'bg',
		'cfp_url' => 'https://cfp.openfest.org',
		'cut_len' => 70,
	];

	$config = [
		2014 => [
			'conferenceId' => 1,
			'eventTypes' => [
				'lecture' => 1,
				'workshop' => 2,
			],
		],
		2015 => [
			'conferenceId' => 2,
			'eventTypes' => [
				'lecture' => 3,
				'workshop' => 4,
			],
		],
		2016 => [
			'conferenceId' => 3,
			'eventTypes' => [
				'lecture' => 5,
				'workshop' => 6,
			],
		],
	];
	
	return array_merge($globalConfig, $config[$year]);
}
