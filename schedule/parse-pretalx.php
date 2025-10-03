<?php
$strftimeDeprecated = version_compare(PHP_VERSION, '8.1.0', '>=');

if ($strftimeDeprecated) {
	require 'php-8.1-strftime.php';
}

$strftime = function (...$args) {
	global $strftimeDeprecated;

	return $strftimeDeprecated ? PHP81_BC\strftime(...$args) : \strftime(...$args);
};

function getTrackColor($track, $colors) {
        if(array_key_exists($track, $colors)) return $colors[$track];
        else return '#fff';
}

function getTalks($conference, $day, $room) {
	return $conference['days'][$day]['rooms'][$room];
}
function getRooms($conference, $day, $filter = NULL) {
	$rooms = [];
	foreach($conference['rooms'] as $room) {
		if(!array_key_exists($room['name'], $conference['days'][$day]['rooms'])) continue;
		if(!empty($filter) && !preg_match($filter, $room['name'])) continue;
		$rooms[] = $room['name'];
	}

	return $rooms;
}
function getTracks($conference) {
	return array_map(function($r) { return $r['name']; }, $conference['tracks']);
}

function getTrackColors($conference) {
	$tracks = [];
	foreach($conference['tracks'] as $track) {
		$tracks[$track['name']] = $track['color'];
	}
	return $tracks;
}

function addMinutes($time, $duration) {
	return date('H:i', strtotime('+'. $duration . ' minutes', strtotime($time)));
}

function timeDiff($point1, $point2) {
	if($point1 > $point2) list($point1, $point2) = array($point2, $point1);
	return addMinutes('00:00', durationToMinutes($point2) - durationToMinutes($point1));
}

function addDuration($time, $duration) {
	return addMinutes($time, durationToMinutes($duration));
}

function getSpan($duration, $slotLength) {
	return durationToMinutes($duration) / durationToMinutes($slotLength);
}

function diffSpan($point1, $point2, $slotLength) {
	
	return getSpan(timeDiff($point1, $point2), $slotLength);
}

function durationToMinutes($duration) {
	list($hr, $min) = explode(':', $duration);
	if(!is_numeric($min) || !is_numeric($hr)) die();
	return $min + $hr * 60;
}

function generateSlots($begin, $length, $end) {
	$slots = [];
	for($cur = $begin; $cur < $end; $cur = addDuration($cur, $length)) $slots[] = $cur;
	return $slots;
}

function lectureTimes($conference, $day, $room) {
	if(!array_key_exists($room, $conference['days'][$day]['rooms'])) return [];
	return array_map(function($t) { return [$t['start'], $t['duration']]; }, getTalks($conference, $day, $room));
}

function getAllTimePoints($conference, $day, $rooms) {
	$points = [];
	foreach($rooms as $room) {
		foreach(getTalks($conference, $day, $room) as $talk) {
			$points[] = $talk['start'];
			$points[] = addDuration($talk['start'], $talk['duration']);
		}
	}
	$points = array_unique($points);
	sort($points);

	return $points;
}

function nextTimePoint($timepoints, $current) {
	foreach($timepoints as $timepoint) if(durationToMinutes($current) < durationToMinutes($timepoint)) return $timepoint;
	return end($timepoints);
}

function gcd($a, $b) {
    while ($b != 0) {
        $m = $a % $b;
        $a = $b;
        $b = $m;
    }
    return $a;
}

function shortestTimescale($timepoints) {
	# this is actually GCD of all timestamps
	
	assert(count($timepoints) >= 2);
	$deltas = array_map(function($t) { return durationToMinutes($t); }, $timepoints);

	$gcd = $deltas[0];

	for($i = 1; $i < count($deltas); $i++) {
		$gcd = gcd($gcd, $deltas[$i]);
#		if($deltas[$i] - $deltas[$i-1] < $min) $min = $deltas[$i] - $deltas[$i-1];
	}

	return addMinutes('00:00', $gcd);
}

#function allRoomTimes($conference, $day) {
#	return array_map(function($room) use ($conference, $day) { return lectureTimes($conference, $day, $room); }, getRooms($conference, $day));
#}
function slotTalks($conference, $day, $rooms) {
	# Note: this will break if the slots don't match the timestamps

	$timepoints = getAllTimePoints($conference, $day, $rooms);
	$slotLength = shortestTimescale($timepoints);

	$roomCount = count($rooms);

	$slotTimes = generateSlots($timepoints[0], $slotLength, end($timepoints));
	$slots = array_fill_keys($slotTimes, array_fill(0, $roomCount, NULL));

	$toRemove = [];

	foreach($rooms as $roomId => $room) {
		if(!array_key_exists($room, $conference['days'][$day]['rooms'])) continue;

		foreach(getTalks($conference, $day, $room) as $talk) {
			$span = getSpan($talk['duration'], $slotLength);

			# add the talk
			$slots[$talk['start']][$roomId] = ['talk' => $talk, 'span' => $span];

			# mark next rows for deletion
			$toRemove[] = ['slot' => $talk['start'], 'room' => $roomId, 'span' => $span];
		}
	}

	foreach($toRemove as $removal) {
		$span = $removal['span'];
		$room = $removal['room'];
		$slot = $removal['slot'];
		while(--$span > 0) {
			$slot = addDuration($slot, $slotLength);
			unset($slots[$slot][$room]);
		}
	}

	return $slots;
}


function parseData($config, $data) {
	global $strftime;

	$languages = array(
		'en' => array(
			'name' => 'English',
			'locale' => 'en_US.UTF8'
		),
		'bg' => array(
			'name' => 'Български',
			'locale' => 'bg_BG.UTF8'
		)
	);

	if ($data === false) return false;

	// We need to set these so we actually parse properly the dates. WP fucks up both.
	date_default_timezone_set('Europe/Sofia');
	setlocale(LC_TIME, $languages[$config['lang']]['locale']);

	$conference = $data['schedule']['conference'];

	$days = [];
	foreach($conference['days'] as $day) {
		$days[] = $day['date'];
	}

	$result = [];

	foreach($days as $day => $date) {
		$rooms = getRooms($conference, $day, $config['roomFilter']);
		$timepoints = getAllTimePoints($conference, $day, $rooms);
		$slotLength = shortestTimescale($timepoints);

		$result[] = [
			'date' => $date,
			'rooms' => $rooms,
			'slots' => slotTalks($conference, $day, $rooms), 
			'timepoints' => $timepoints,
			'tracks' => getTracks($conference),
			'colors' => getTrackColors($conference),
			'slotLength' => $slotLength,
		];
	}

	return $result;
}
