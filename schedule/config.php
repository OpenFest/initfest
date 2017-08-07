<?php
function getSchedConfig($year = 2016) {
	$globalConfig = [
		'lang' => 'bg',
		'cfp_url' => 'https://cfp.openfest.org',
		'cut_len' => 70,
		'hidden_speakers' => [4],
		'hidden_language_tracks' => [],
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
			'hidden_language_tracks' => [16],
		],
		2016 => [
			'conferenceId' => 3,
			'eventTypes' => [
				'lecture' => 5,
				'workshop' => 6,
			],
			'hidden_language_tracks' => [25],
		],
		2017 => [
			'conferenceId' => 4,
			'eventTypes' => [
				'lecture' => 7,
				'workshop' => 8,
			],
			'hidden_language_tracks' => [25],
		],

	];
	
	return array_merge($globalConfig, $config[$year]);
}
