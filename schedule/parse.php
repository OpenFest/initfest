<?php
function parseData($config, $data) {
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
	
	// We need to set these so we actually parse properly the dates. WP fucks up both.
	date_default_timezone_set('Europe/Sofia');
	setlocale(LC_TIME, $languages[$config['lang']]['locale']);

	// Filter out invalid slots
	$data['slots'] = array_filter($data['slots'], function($slot) {
		return isset($slot['starts_at'], $slot['ends_at'], $slot['hall_id'], $slot['event_id']);
	});
	
	// Collect the slots for each hall, sort them in order of starting
	$slots = [];
	$timestamps = [];
	
	foreach ($data['halls'] as $hall_id => $hall) {
		$slots[$hall_id] = [];
		
		foreach ($data['slots'] as $slot_id => $slot) {
			if ($slot['hall_id'] !== $hall_id) {
				continue;
			}
			
			if (!in_array($slot['starts_at'], $timestamps)) {
				$timestamps[] = $slot['starts_at'];
			}
			
			if (!in_array($slot['ends_at'], $timestamps)) {
				$timestamps[] = $slot['ends_at'];
			}

			$slots[$hall_id][$slot['starts_at']] = $slot;
		}
		
		ksort($slots[$hall_id]);
	}
	
	sort($timestamps);
	
	// Find all microslots (the smallest time unit)
	$microslots = [];
	$lastTs = 0;
	$first = true;
	
	foreach ($timestamps as $ts) {
		if ($first) {
			$lastTs = $ts;
			$first = false;
			continue;
		}
		
		if (date('d.m', $lastTs) !== date('d.m', $ts)) {
			$lastTs = $ts;
			continue;
		}
		
		$microslots[] = [$lastTs, $ts];
		$lastTs = $ts;
	}
	
	// Fill in the event ID for each time slot in each hall
	$events = [];
	$filtered_type_id =
		array_key_exists('filterEventType', $config) &&
		array_key_exists($config['filterEventType'], $config['eventTypes']) ?
			$config['eventTypes'][$config['filterEventType']] :
			null;

	foreach ($data['halls'] as $hall_id => $hall) {
		$hall_data = [];
		
		foreach ($microslots as $timestamps) {
			$found = false;
			
			foreach ($data['slots'] as $slot_id => $slot) {
				if (
					$slot['hall_id'] === $hall_id &&
					$slot['starts_at'] <= $timestamps[0] &&
					$slot['ends_at'] >= $timestamps[1] &&
					array_key_exists($slot['event_id'], $data['events'])
				) {
					if (!is_null($filtered_type_id)) {
						if ($data['events'][$slot['event_id']]['event_type_id'] !== $filtered_type_id) {
							continue;
						}
					}
					
					$found = true;
					$hall_data[] = [
						'event_id' => $slot['event_id'],
						'hall_id' => $slot['hall_id'],
						'edge' => $slot['starts_at'] === $timestamps[0] || $slot['ends_at'] === $timestamps[1],
					];
					break;
				}
			}
			
			if (!$found) {
				$hall_data[] = null;
			}
		}
		
		$events[] = $hall_data;
	}
	
	// Remove halls with no events after filtering
	$count = count($events);
	for ($i = 0; $i < $count; ++$i) {
		$hasEvents = false;
		foreach ($events[$i] as $event_info) {
			if (!is_null($event_info)) {
				$hasEvents = true;
				break;
			}
		}
		if (!$hasEvents) {
			unset($events[$i]);
		}
	}

	// Transpose the matrix
	// rows->halls, cols->timeslots ===> rows->timeslots, cols->halls
	$events = array_map(null, ...$events);

	// Filter empty slots
	$count = count($events);
	for ($i = 0; $i < $count; ++$i) {
		$hall_count = count($events[$i]);
		$hasEvents = false;
		
		for ($j = 0; $j < $hall_count; ++$j) {
			if (!is_null($events[$i][$j]) && $events[$i][$j]['edge']) {
				$hasEvents = true;
				continue 2;
			}
		}
		
		if (!$hasEvents) {
			unset($events[$i]);
		}
	}
	
	// Merge events longer than one slot
	$prevEventId = [];
	$prevEventSlot = [];
	$prevSlotIndex = 0;
	$first = true;
	
	foreach ($events as $slot_index => &$events_data) {
		if ($first) {
			$prevEventId = array_map(function($event_info) {
				return is_null($event_info) ? null : $event_info['event_id'];
			}, $events_data);
			$prevEventSlot = array_fill(0, count($events_data), null);
			$prevSlotIndex = $slot_index;
			$first = false;
			continue;
		}
		
		foreach ($events_data as $hall_index => &$event_info) {
			if (is_null($event_info)) {
				$prevEventId[$hall_index] = null;
				$prevEventSlot[$hall_index] = null;
				continue;
			}
			
			if ($event_info['event_id'] !== $prevEventId[$hall_index]) {
				$prevEventId[$hall_index] = $event_info['event_id'];
				$prevEventSlot[$hall_index] = null;
				continue;
			}
			
			// We have a long event
			if (is_null($prevEventSlot[$hall_index])) {
				$prevEventSlot[$hall_index] = $prevSlotIndex;
			}
			
			$master_slot = &$events[$prevEventSlot[$hall_index]][$hall_index];
			
			if (!array_key_exists('rowspan', $master_slot)) {
				$master_slot['rowspan'] = 2;
			}
			else {
				++$master_slot['rowspan'];
			}
			
			unset($master_slot);
			
			$event_info = false;
		}
		
		unset($event_info);
		
		$prevSlotIndex = $slot_index;
	}
	
	unset($events_data);
	
	// Build the HTML
	$schedule_body = '';
	$lastTs = 0;
	$fulltalks = '';
	$hall_ids = [];
	
	foreach ($events as $slot_index => $events_data) {
		$columns = [];
		
		if (date('d.m', $microslots[$slot_index][0]) !== date('d.m', $lastTs)) {
			$schedule_body .= '<tr><th colspan="' . (count($events_data) + 1) . '">' . strftime('%d %B - %A', $microslots[$slot_index][0]) . '</th></tr>';
		}
		
		$lastTs = $microslots[$slot_index][0];
		$lastEventId = 0;
		$colspan = 1;
		
		foreach ($events_data as $event_info) {
			if ($event_info === false) {
				continue;
			}
			
			if (is_null($event_info['event_id'])) {
				$columns[] = '<td>&nbsp;</td>';
				continue;
			}
			
			if (!in_array($event_info['hall_id'], $hall_ids)) {
				$hall_ids[] = $event_info['hall_id'];
			}
			
			$eid = &$event_info['event_id'];
			$event = &$data['events'][$eid];

			$title = mb_substr($event['title'], 0, $config['cut_len']) . (mb_strlen($event['title']) > $config['cut_len'] ? '...' : '');
			$speakers = '';
			
			if (count($event['participant_user_ids']) > 0) {
				$spk = [];

				foreach ($event['participant_user_ids'] as $uid) {
					if (in_array($uid, $config['hidden_speakers']) || empty($data['speakers'][$uid])) {
						continue;
					}

					$name = $data['speakers'][$uid]['first_name'] . ' ' . $data['speakers'][$uid]['last_name'];
					$spk[] = '<a class="vt-p" href="#' . $name . '">' . $name . '</a>';
				}
				
				$speakers = implode (', ', $spk);
			}
			
			$content = '<a href="#lecture-' . $eid . '">' . htmlspecialchars($title) . '</a><br>' . $speakers;

			// these are done by $eid, as otherwise we get some talks more than once (for example the lunch)
			// TODO: fix this, it's broken
			$fulltalks .= '<section id="lecture-' . $eid . '">';
			
			// We don't want '()' when we don't have a speaker name
			$fulltalk_spkr = strlen($speakers) > 0 ? (' (' . $speakers . ')') : '';
			$fulltalks .= '<p><strong>' . $event['title'] . ' ' . $fulltalk_spkr . '</strong></p>';
			$fulltalks .= '<p>' . $event['abstract'] . '</p>';
			$fulltalks .= '<div class="separator"></div></section>';

			if ($eid === $lastEventId) {
				array_pop($columns);
				++$colspan;
			}
			else {
				$colspan = 1;
			}

			$rowspan = array_key_exists('rowspan', $event_info) ? (' rowspan="' . $event_info['rowspan'] . '"') : '';
			
			// CSS
			$cssClasses = [];
			
			if (!in_array($event['track_id'], $config['hidden_language_tracks'])) {
				$cssClasses[] = 'schedule-' . $event['language'];
			}
			
			$cssClass = $data['tracks'][$event['track_id']]['css_class'];
			
			if (strlen($cssClass) > 0) {
				$cssClasses[] = $cssClass;
			}
			
			$cssClasses = count($cssClasses) > 0 ? (' class="' . implode(' ', $cssClasses) . '"') : '';

			// Render cell
			$columns[] = '<td' . ($colspan > 1 ? ' colspan="' . $colspan . '"' : $rowspan) . $cssClasses . '>' . $content . '</td>';
			
			$lastEventId = $eid;
			unset($eid, $event);
		}
		
		$schedule_body .= '<tr><td>';
		$schedule_body .= strftime('%H:%M', $microslots[$slot_index][0]) . ' - ' . strftime('%H:%M', $microslots[$slot_index][1]);
		$schedule_body .= '</td>';
		$schedule_body .= implode('', $columns);
		$schedule_body .= '</tr>';
	}
	
	$schedule = '<table border="1"><thead><tr><th></th>';
	
	foreach ($data['halls'] as $hall_id => $hall) {
		if (!in_array($hall_id, $hall_ids)) {
			continue;
		}
		
		$schedule .= '<th>' . $hall['bg'] . '</th>';
	}
	
	$schedule .= '</tr></thead><tbody>';
	$schedule .= $schedule_body;
	$schedule .= '</tbody></table>';
	
	// Create the legend
	$legend = '';

	foreach($data['tracks'] as $track) {
		$legend .= '<tr><td class="' . $track['css_class'] . '">' . $track['name'][$config['lang']] . '</td></tr>';
	}

	foreach ($languages as $code => $lang) {
		$legend .= '<tr><td class="schedule-' . $code . '">' . $lang['name'] . '</td></tr>';
	}
	
	// Speaker list
	$gspk = '<div class="grid members">';
	$fspk = '';
	$types = [
		'twitter' => [
			'class' => 'twitter',
			'url' => 'https://twitter.com/',
		],
		'github' => [
			'class' => 'github',
			'url' => 'https://github.com/',
		],
		'email' => [
			'class' => 'envelope',
			'url' => 'mailto:',
		],
	];

	foreach ($data['speakers'] as $speaker) {
		$name = $speaker['first_name'] . ' ' . $speaker['last_name'];

		$gspk .= '<div class="member col4">';
		$gspk .= '<a href="#' . $name . '">';
		$gspk .= '<img width="100" height="100" src="' . $config['cfp_url'] . $speaker['picture']['schedule']['url'] . '" class="attachment-100x100 wp-post-image" alt="' . $name . '" />';
		$gspk .= '</a> </div>';

		$fspk .= '<div class="speaker" id="' . $name . '">';
		$fspk .= '<img width="100" height="100" src="' . $config['cfp_url'] . $speaker['picture']['schedule']['url'] . '" class="attachment-100x100 wp-post-image" alt="' . $name . '" />'; 
		$fspk .= '<h3>' . $name . '</h3>';
		$fspk .= '<div class="icons">';
		
		foreach ($types as $type => $param) {
			if (!empty($speaker[$type])) {
				$fspk .= '<a href="' . $param['url'] . $speaker[$type] . '"><i class="fa fa-' . $param['class'] . '"></i></a>';
			}
		}
		
		$fspk .= '</div>';
		$fspk .= '<p>' . $speaker['biography'] . '</p>';
		$fspk .= '</div><div class="separator"></div>';
	}

	$gspk .= '</div>';

	return compact('schedule', 'fulltalks', 'gspk', 'fspk', 'legend');
}
