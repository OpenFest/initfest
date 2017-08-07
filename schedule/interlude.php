<?php
$requirePath = __DIR__ . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load.php';
require $requirePath . 'parse.php';

$sched_config = getSchedConfig();

$data = loadData($sched_config);

$sched_config['filterEventType'] = 'lecture';

$content = parseData($sched_config, $data);

header('Content-Type:application/json; charset=utf-8');
header('Cache-Control:max-age=0, private, must-revalidate');
header('Access-Control-Allow-Origin: *');

$events = array_map(function($hallData) use ($data) {
	return array_map(function($event) use ($data) {
		unset($event['hall_id']);

		if (!array_key_exists($event['event_id'], $data['events'])) {
			unset($event['event_id']);
			return $event;
		}
		
		$eventData = &$data['events'][$event['event_id']];
		$event['title'] = $eventData['title'];
		$event['speakers'] = array_map(function($speaker_id) use ($data) {
			if (!array_key_exists($speaker_id, $data['speakers'])) {
				return [];
			}
			
			$speakerData = &$data['speakers'][$speaker_id];
			
			return [
				'name' => $speakerData['first_name'] . ' ' . $speakerData['last_name'],
				'description' => $speakerData['biography'],
			];
		}, array_filter($eventData['participant_user_ids'], function($speaker_id) {
			return !in_array($speaker_id, [4]);
		}));
		
		unset($event['event_id']);
		return $event;
	}, $hallData);
}, $content['slots']);

echo json_encode($events);
