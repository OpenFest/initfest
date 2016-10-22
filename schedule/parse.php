<?php
function parseData($config, $data) {
	$time = 0;
	$date = 0;
	$lines = [];
	$fulltalks = '';
	$prev_event_id = 0;
	$colspan = 1;

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
	
	// Collect the events for each hall, sort them in order of starting
	$events = [];
	$microslots = [];
	
	foreach ($data['halls'] as $hall_id => $hall) {
		$events[$hall_id] = [];
		
		foreach ($data['slots'] as $slot_id => $slot) {
			if ($slot['hall_id'] !== $hall_id) {
				continue;
			}
			
			if (!in_array($slot['starts_at'], $microslots)) {
				$microslots[] = $slot['starts_at'];
			}
			
			if (!in_array($slot['ends_at'], $microslots)) {
				$microslots[] = $slot['ends_at'];
			}
			
			$events[$hall_id][$slot['starts_at']] = $slot;
		}
		
		ksort($events[$hall_id]);
	}
	
	sort($microslots);
	
	// Find all microslots (the smallest time unit)
	$intervals = [];
	$lastTs = 0;
	$first = true;
	
	foreach ($microslots as $ts) {
		if ($first) {
			$lastTs = $ts;
			$first = false;
			continue;
		}
		
		if (date('d.m', $lastTs) !== date('d.m', $ts)) {
			$lastTs = $ts;
			continue;
		}
		
		$intervals[] = [$lastTs, $ts];
		$lastTs = $ts;
	}
	
	// Fill in the event ID for each time slot in each hall
	$slot_list = [];
	
	foreach ($data['halls'] as $hall_id => $hall) {
		$hall_data = [];
		
		foreach ($intervals as $timestamps) {
			$found = false;
			
			foreach ($data['slots'] as $slot_id => $slot) {
				if (
					$slot['hall_id'] === $hall_id &&
					$slot['starts_at'] <= $timestamps[0] &&
					$slot['ends_at'] >= $timestamps[1]
				) {
					$found = true;
					$hall_data[] = [
						'event_id' => $slot['event_id'],
						'edge' => $slot['starts_at'] === $timestamps[0] || $slot['ends_at'] === $timestamps[1],
					];
					break;
				}
			}
			
			if (!$found) {
				$hall_data[] = null;
			}
		}
		
		$slot_list[] = $hall_data;
	}
	
	// Transpose the matrix
	// rows->halls, cols->timeslots ===> rows->timeslots, cols->halls
	$slot_list = array_map(null, ...$slot_list);
	
	// Build the HTML
	$schedule = '<table border="1"><thead><tr><th></th>';
	
	foreach ($data['halls'] as $hall_id => $hall) {
		$schedule .= '<th>' . $hall['bg'] . '</th>';
	}
	
	$schedule .= '</tr></thead><tbody>';
	$lastTs = 0;
	
	foreach ($slot_list as $slot_index => $events) {
		$columns = [];
		$hasEvents = false;
		
		if (date('d.m', $intervals[$slot_index][0]) !== date('d.m', $lastTs)) {
			$schedule .= '<tr><th colspan="' . (count($events) + 1) . '">' . strftime('%d %B - %A', $intervals[$slot_index][0]) . '</th></tr>';
		}
		
		$lastTs = $intervals[$slot_index][0];
		$lastEventId = 0;
		$colspan = 1;
		
		foreach ($events as $hall_index => $hall_data) {
			if (is_null($hall_data['event_id']) || !array_key_exists($hall_data['event_id'], $data['events'])) {
				$columns[] = '<td>&nbsp;</td>';
				continue;
			}

			if ($hall_data['edge']) {
				$hasEvents = true;
			}
			
			$eid = &$hall_data['event_id'];
			$event = &$data['events'][$eid];

			$title = mb_substr($event['title'], 0, $config['cut_len']) . (mb_strlen($event['title']) > $config['cut_len'] ? '...' : '');
			$speakers = '';
			
			if (count($event['participant_user_ids']) > 0) {
				$spk = array();
				$speaker_name = array();
				foreach ($event['participant_user_ids'] as $uid) {
					if (in_array($uid, $config['hidden_speakers']) || empty($data['speakers'][$uid])) {
						continue;
					}

					$name = $data['speakers'][$uid]['first_name'] . ' ' . $data['speakers'][$uid]['last_name'];
					$spk[$uid] = '<a class="vt-p" href="#' . $name . '">' . $name . '</a>';
				}
				$speakers = implode (', ', $spk);
			}
			
			if (in_array($event['track_id'], $config['hidden_language_tracks'])) {
				$csslang = '';
			} else {
				$csslang = 'schedule-' . $event['language'];
			}
			
			$cssclass = &$data['tracks'][$event['track_id']]['css_class'];
			$style = ' class="' . $cssclass . ' ' . $csslang . '"';
			$content = '<a href="#lecture-' . $eid . '">' . htmlspecialchars($title) . '</a><br>' . $speakers;

			/* these are done by $eid, as otherwise we get some talks more than once (for example the lunch) */
			$fulltalks .= '<section id="lecture-' . $eid . '">';
			/* We don't want '()' when we don't have a speaker name */
			$fulltalk_spkr = strlen($speakers) > 0 ? (' (' . $speakers . ')') : '';
			$fulltalks .= '<p><strong>' . $event['title'] . ' ' . $fulltalk_spkr . '</strong></p>';
			$fulltalks .= '<p>' . $event['abstract'] . '</p>';
			$fulltalks .= '<div class="separator"></div></section>';
/*
			if ($eid === $lastEventId) {
				array_pop($columns);
				++$colspan;
			}
			else {
				$colspan = 1;
			}
*/
			$columns[] = '<td' . $style . ($colspan > 1 ? ' colspan="' . $colspan . '"' : '') . '>' . $content . '</td>';
			$lastEventId = $eid;
		}
		
		if (!$hasEvents) {
			continue;
		}
		
		$schedule .= '<tr><td>';
		$schedule .= strftime('%H:%M', $intervals[$slot_index][0]) . ' - ' . strftime('%H:%M', $intervals[$slot_index][1]);
		$schedule .= '</td>';
		$schedule .= implode('', $columns);
		$schedule .= '</tr>';
	}
	
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
