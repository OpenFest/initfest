<?php
// 'halfnarp_friendly'
// 'events'
// 'speakers'
// 'tracks' [en/bg]
// 'event_types' [en/bg]
// 'halls'
// 'slots'

$data = require __DIR__ . DIRECTORY_SEPARATOR . 'load.php';

/* sensible default */
if (empty($lang)) $lang = 'bg';

$cut_len = 70;
$cfp_url = 'http://cfp.openfest.org';
$time = 0;
$date = 0;
$lines = [];
$fulltalks = [];
$prev_event_id = 0;
$colspan = 1;
$hall_ids = array_keys($data['halls']);
$first_hall_id = min($hall_ids);
$last_hall_id = max($hall_ids);

date_default_timezone_set('Europe/Sofia');

foreach ($data['slots'] as $slot_id => $slot) {
	$slotTime = $slot['starts_at'];
	$slotDate = date('d', $slotTime);
		
	if ($slotDate !== $date) {
		$lines[] = '<tr>';
		$lines[] = '<td>' . date('d F - l', $slotTime) . '</td>';
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
	$event = &$data['events'][$eid];
	
	if (is_null($eid)) {
		$lines[] = '<td>TBA</td>';
	}
	else {
		$title = mb_substr($event['title'], 0, $cut_len) . (mb_strlen($event['title']) > $cut_len ? '...' : '');
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
					$spk[$uid] = '<a class="vt-p" href="SPKURL#'. $name . '">' . $name . '</a>';
				}
			}
			$speakers = implode (', ', $spk);
		}
		
		
		/* Hack, we don't want language for the misc track. This is the same for all years. */
		if ('misc' !== $data['tracks'][$event['track_id']]['name']['en']) {
			$csslang = "schedule-".$event['language'];
		} else {
			$csslang = "";
		}
		$cssclass = &$data['tracks'][$event['track_id']]['css_class'];
		$style = ' class="' . $cssclass . ' ' . $csslang . '"';
		$content = '<a href=#lecture-' . $eid . '>' . htmlspecialchars($title) . '</a> <br>' . $speakers;


		/* these are done by $eid, as otherwise we get some talks more than once (for example the lunch) */
		$fulltalks[$eid] = '';
		$fulltalks[$eid] .= '<section id="lecture-' . $eid . '">';
		/* We don't want '()' when we don't have a speaker name */
		$fulltalk_spkr = strlen($speakers)>1 ? ' (' . $speakers . ')' : '';
		$fulltalks[$eid] .= '<p><strong>' . $event['title'] . ' ' . $fulltalk_spkr . '</strong>';
		$fulltalks[$eid] .= '<p>' . $event['abstract'] . '</p>';
		$fulltalks[$eid] .= '<div class="separator"></div></section>';

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

$legend = [];

foreach($data['tracks'] as $track) {
	$legend[] = '<tr><td class="' . $track['css_class'] . '">' . $track['name'][$lang] . '</td></tr>';
}
foreach (array('en' => 'English', 'bg' => 'Български') as $l => $n) {
	$legend[] = '<tr><td class="schedule-' . $l . '">' . $n . '</td></tr>';
}

$gspk = [];
$fspk = [];
$types = [];
$types['twitter']['url']='https://twitter.com/';
$types['twitter']['class']='fa fa-twitter';
$types['github']['url']='https://github.com/';
$types['github']['class']='fa fa-github';
$types['email']['url']='mailto:';
$types['email']['class']='fa fa-envelope';

$gspk[] = '<div class="grid members">';

foreach ($data['speakers'] as $speaker) {
	$name = $speaker['first_name'] . ' ' . $speaker['last_name'];

	$gspk[] = '<div class="member col4">';
	$gspk[] = '<a href="#' . $name . '">';
	$gspk[] = '<img width="100" height="100" src="' . $cfp_url . $speaker['picture']['schedule']['url'].'" class="attachment-100x100 wp-post-image" alt="' . $name .'" />';
	$gspk[] = '</a> </div>';

	$fspk[] = '<div class="speaker" id="' . $name . '">';
	$fspk[] = '<img width="100" height="100" src="' . $cfp_url . $speaker['picture']['schedule']['url'].'" class="attachment-100x100 wp-post-image" alt="' . $name .'" />'; 
	$fspk[] = '<h3>' . $name . '</h3>';
	$fspk[] = '<div class="icons">';
	foreach ($types as $type => $parm) {
		if (!empty($speaker[$type])) {
			$fspk[] = '<a href="'. $parm['url'] . $speaker[$type] . '"><i class="' . $parm['class'] . '"></i></a>';
		}
	}
	$fspk[] = '</div>';
	$fspk[] = '<p>' . $speaker['biography'] . '</p>';
	$fspk[] = '</div><div class="separator"></div>';
}

$gspk[] = '</div>';

return array_merge($data, compact('lines', 'fulltalks', 'gspk', 'fspk', 'legend'));
