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
	
	// PATCH WHILE CLARION RETURNS WRONG DATA
	$data['slots'][188]['ends_at'] = strtotime('2016-11-06T13:15:00.000+02:00');
	
	$moments = [];
	
	$data['slots'] = array_map(function($slot) {
		$slot['start'] = date('d.m H:i', $slot['starts_at']);
		$slot['end'] = date('d.m H:i', $slot['ends_at']);
		return $slot;
	}, $data['slots']);
	
	$events = [];
	
	foreach ($data['halls'] as $hall_id => $hall) {
		$events[$hall_id] = [];
		
		foreach ($data['slots'] as $slot_id => $slot) {
			if ($slot['hall_id'] !== $hall_id) {
				continue;
			}
			
			if (!in_array($slot['starts_at'], $moments)) {
				$moments[] = $slot['starts_at'];
			}
			
			if (!in_array($slot['ends_at'], $moments)) {
				$moments[] = $slot['ends_at'];
			}
			
			$events[$hall_id][$slot['starts_at']] = $slot;
		}
		
		ksort($events[$hall_id]);
	}
	
	sort($moments);
	
	$times = [];
	
	foreach ($moments as $moment) {
		$times[$moment] = date('d.m H:i', $moment);
	}
	
	$intervals = [];
	$lastTs = 0;
	$last = '';
	$first = true;
	
	foreach ($times as $ts => $time) {
		if ($first) {
			$last = $time;
			$lastTs = $ts;
			$first = false;
			continue;
		}
		
		if (date('d.m.Y', $lastTs) !== date('d.m.Y', $ts)) {
			//echo PHP_EOL;
			
			$last = $time;
			$lastTs = $ts;
			continue;
		}
		
		//echo count($intervals), '. ', $last, ' - ', $time, PHP_EOL;
		$intervals[] = [$lastTs, $ts];
		
		$lastTs = $ts;
		$last = $time;
	}
	
	$schedule = [];
	$hall_ids = array_keys($data['halls']);
	
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
		
		$schedule[] = $hall_data;
	}
	
	$schedule = array_map(null, ...$schedule);
	$table = '<table border="1"><thead><tr><th></th>';
	
	foreach ($hall_ids as $hall_id) {
		$table .= '<th>' . $data['halls'][$hall_id]['bg'] . '</th>';
	}
	
	$table .= '</tr></thead><tbody>';
	
	foreach ($schedule as $slot_index => $events) {
		$columns = [];
		$hasEvents = false;
		
		foreach ($events as $hall_index => $event) {
			if (is_null($event['event_id'])) {
				$columns[] = '<td>&nbsp;</td>';
				continue;
			}

			if ($event['edge']) {
				$hasEvents = true;
			}
			
			$columns[] = '<td>' . $data['events'][$event['event_id']]['title'] . ' (' . $event['event_id'] . ')</td>';
		}
		
		if (!$hasEvents) {
			continue;
		}
		
		$table .= '<tr><td>';
		$table .= date('H:i', $intervals[$slot_index][0]) . ' - ' . date('H:i', $intervals[$slot_index][1]);
		$table .= '</td>';
		$table .= implode('', $columns);
		$table .= '</tr>';
	}
	
	$table .= '</tbody></table>';
	
	echo $table;
	//var_dump($schedule);
	exit;

	/* We need to set these so we actually parse properly the dates. WP fucks up both. */
	date_default_timezone_set('Europe/Sofia');
	setlocale(LC_TIME, $languages[$config['lang']]['locale']);

	foreach ($data['slots'] as $slot) {
		$slotTime = $slot['starts_at'];
		$slotDate = date('d', $slotTime);
			
		if ($slotDate !== $date) {
			$lines[] = '<tr>';
			$lines[] = '<td>' . strftime('%d %B - %A', $slotTime) . '</td>';
			$lines[] = '<td colspan="3">&nbsp;</td>';
			$lines[] = '</tr>';
			
			$date = $slotDate;
		}
		
		if ($slotTime !== $time) {
			if ($time !== 0) {
				$lines[] = '</tr>';
			}
			
			$lines[] = '<tr>';
			$lines[] = '<td>' . date('H:i', $slot['starts_at']) . ' - ' . date('H:i', $slot['ends_at']) . '</td>';
			
			$time = $slotTime;
		}
		
		$eid = &$slot['event_id'];
		
		if (!array_key_exists($eid, $data['events'])) {
			continue;
		}
		
		$event = &$data['events'][$eid];
		
		if (
			array_key_exists('filterEventType', $config) &&
			array_key_exists($config['filterEventType'], $config['eventTypes'])
		) {
			if ($config['eventTypes'][$config['filterEventType']] !== $event['event_type_id']) {
				continue;
			}
		}
		
		if (is_null($eid)) {
			$lines[] = '<td>TBA</td>';
		}
		else {
			$title = mb_substr($event['title'], 0, $config['cut_len']) . (mb_strlen($event['title']) > $config['cut_len'] ? '...' : '');
			$speakers = '';
			
			if (count($event['participant_user_ids']) > 0) {
				$speakers = json_encode($event['participant_user_ids']) . '<br>';

				$spk = array();
				$speaker_name = array();
				foreach ($event['participant_user_ids'] as $uid) {
					/* The check for uid==4 is for us not to show the "Opefest Team" as a presenter for lunches, etc. */
					if ($uid == 4 || empty ($data['speakers'][$uid])) {
						continue;
					} else {
						/* TODO: fix the URL */
						$name = $data['speakers'][$uid]['first_name'] . ' ' . $data['speakers'][$uid]['last_name'];
						$spk[$uid] = '<a class="vt-p" href="#' . $name . '">' . $name . '</a>';
					}
				}
				$speakers = implode (', ', $spk);
			}
			
			
			/* Hack, we don't want language for the misc track. This is the same for all years. */
			if ('misc' !== $data['tracks'][$event['track_id']]['name']['en']) {
				$csslang = 'schedule-' . $event['language'];
			} else {
				$csslang = '';
			}
			$cssclass = &$data['tracks'][$event['track_id']]['css_class'];
			$style = ' class="' . $cssclass . ' ' . $csslang . '"';
			$content = '<a href=#lecture-' . $eid . '>' . htmlspecialchars($title) . '</a> <br>' . $speakers;


			/* these are done by $eid, as otherwise we get some talks more than once (for example the lunch) */
			$fulltalks .= '<section id="lecture-' . $eid . '">';
			/* We don't want '()' when we don't have a speaker name */
			$fulltalk_spkr = strlen($speakers)>1 ? ' (' . $speakers . ')' : '';
			$fulltalks .= '<p><strong>' . $event['title'] . ' ' . $fulltalk_spkr . '</strong></p>';
			$fulltalks .= '<p>' . $event['abstract'] . '</p>';
			$fulltalks .= '<div class="separator"></div></section>';

			if ($slot['event_id'] === $prev_event_id) {
				array_pop($lines);
				$lines[] = '<td' . $style . ' colspan="' . ++$colspan . '">' . $content . '</td>';
			}
			else {
				$lines[] = '<td' . $style . '>' . $content . '</td>';
				$colspan = 1;
			}
		}
		
		$prev_event_id = $slot['event_id'];
	}

	$lines[] = '</tr>';

	/* create the legend */
	$legend = '';

	foreach($data['tracks'] as $track) {
		$legend .= '<tr><td class="' . $track['css_class'] . '">' . $track['name'][$config['lang']] . '</td></tr>';
	}

	foreach ($languages as $code => $lang) {
		$legend .= '<tr><td class="schedule-' . $code . '">' . $lang['name'] . '</td></tr>';
	}

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

	return compact('lines', 'fulltalks', 'gspk', 'fspk', 'legend');
}
