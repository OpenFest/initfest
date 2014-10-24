<?php

	require("cli-header.php");

	/* fugly hardcoding */
	$sched_en=5650;
	$sched_bg=5648;
/*
	$sched_en=3942;
$sched_bg=3940;*/

	$bg = '<section class="content">&nbsp;<table cellpadding="0" cellspacing="0" class="schedule"><tr><th>&nbsp;</th><th>Зала Пловдив</th><th>Зала Бургас</th></tr>';
	$en = '<section class="content">&nbsp;<table cellpadding="0" cellspacing="0" class="schedule"><tr><th>&nbsp;</th><th>Plovdiv Hall</th><th>Burgas Hall</th></tr>';

	$tracks=array(8 => 'open-biz', 9 => 'open-art', 2 => 'technical', 6 => 'civic', 5 => 'social', 3 => 'advanced-technical');

	$prg = pg_query("select
		h.name as hallname,h.id as hallid,
		to_char(starts_at, 'DD FMMonth - FMDay') as dt,
		to_char(s.starts_at,'HH24:MI')|| ' - ' || to_char(s.ends_at,'HH24:MI') as slot,
		e.title, e.subtitle, e.language, e.id as eventid,
		e.abstract, e.description,
		t.name as tname,t.id as trackid,
		array_agg(sp.first_name || ' ' || sp.last_name)::text as spname
		from
		slots s join halls h on h.id=s.hall_id
		join events e on s.event_id = e.id
		join tracks t on t.id=e.track_id
		left join events_speaker_profiles esp on esp.event_id=e.id
		left join speaker_profiles sp on esp.speaker_profile_id=sp.id
		where
		not s.event_id is null and h.id in (4,5)
		group by h.name, h.id, starts_at, ends_at, t.id, e.title, e.subtitle, e.language, e.id, s.hall_id
		order by date(s.starts_at),s.starts_at, s.hall_id;
		");


		$dtrans = array('01 November - Saturday' => '01 ноември - събота', '02 November - Sunday' => '02 ноември - неделя');

$p = array();
while ($row = pg_fetch_object($prg)) {
	$p[$row->dt][$row->slot][$row->hallname]=$row;
}


	$bgpost = array();
	$enpost = array();

	$bgpost['ID'] = $sched_bg;
	$enpost['ID'] = $sched_en;

	$bgpost['post_title'] = "Workshop-и";
	$enpost['post_title'] = "Workshops";

	$bgpost['post_name'] = "workshopsbg";
	$enpost['post_name'] = "workshops";

	$bgpost['post_author'] = 2;
	$enpost['post_author'] = 2;

	$bgpost['post_date'] = "2014-10-13 00:01:02";
	$enpost['post_date'] = "2014-10-13 00:01:02";

	$cdate='';
	$chall='';


	$clearsmb=array('{', '}', '"');

	$events = array();

	foreach ($p as $day => $dayv) {	
		$bg .='<tr><td class="schedule-day">'.$dtrans[$day].'</td><td colspan="4" class="schedule-empty"></td></tr>'."\n";
		$en .='<tr><td class="schedule-day">'.$day.'</td><td colspan="4" class="schedule-empty"></td></tr>'."\n";
		foreach ($dayv as $slot => $slotv) {
			$bg .= '<tr><td>'.$slot.'</td>'."\n";
			$en .= '<tr><td>'.$slot.'</td>'."\n";
			$h=0;
			foreach ($slotv as $hall => $event){
				$h++;
				while ($h+3 < $event->hallid)	{
					$h++;
					$bg .= "<td></td>\n";
					$en .= "<td></td>\n";
				}

				$spkarr = explode(',', str_replace($clearsmb, '' ,$event->spname));
				$spkbgarr = array();
				$spkenarr = array();
				foreach ($spkarr as $val){ 
					if ($val == 'NULL') continue;
					$spkbgarr[] = '<a class="vt-p" href="/bg/programa/speakers/#'.htmlentities($val).'">'.htmlentities($val).'</a>';
					$spkenarr[] = '<a class="vt-p" href="/en/schedule/speakers/#'.htmlentities($val).'">'.htmlentities($val).'</a>';
				}
				$spkbg = implode(", ", $spkbgarr);
				$spken = implode(", ", $spkenarr);

				if (count($spkbgarr)>0) {
					$event->spken = '('.$spken.')';
					$event->spkbg = '('.$spkbg.')';
				} else {
					$event->spken = '';
					$event->spkbg = '';
				}
			
				$events[] = $event;	

				$bg .= '<td class="schedule-'.$tracks[$event->trackid].' schedule-'.$event->language.'"><a href="#lecture-'.$event->eventid.'">'.htmlentities($event->title).'</a>';
				$bg .='<br>'.$spkbg.'</td>'."\n";

				$en .= '<td class="schedule-'.$tracks[$event->trackid].' schedule-'.$event->language.'"><a href="#lecture-'.$event->eventid.'">'.htmlentities($event->title).'</a>';
				$en .='<br>'.$spken.'</td>'."\n";

				#var_dump($event);
				
			}
			while ($h < 2) {
					$h++;
					$bg .= "<td></td>\n";
					$en .= "<td></td>\n";
				}
			$bg .= '</tr>'."\n";
			$en .= '</tr>'."\n";
		}
	}

	$bg .= '</table>';
	$en .= '</table>';
	$legend = '<!-- legend --> 
		<table cllpadding="0" cellspacing="0" class="schedule schedule-legend">
		<tr><td class="schedule-technical">Technical</td></tr>
		<tr><td class="schedule-advanced-technical">Advanced technical</td></tr>
		<tr><td class="schedule-social">Social</td></tr>
		<tr><td class="schedule-open-art">Open art</td></tr>
		<tr><td class="schedule-open-biz">Open biz</td></tr>
		<tr><td class="schedule-civic">Civic hacking</td></tr>
		<tr><td class="schedule-misc">Misc</td></tr>
		<tr><td class="schedule-en">English</td></tr>
		<tr><td class="schedule-bg">Български</td></tr>
		</table>';
	$bg .= $legend;
	$en .= $legend;


	$bg .= '<div class="separator"></div>';
	$en .= '<div class="separator"></div>';



	foreach ($events as $k => $event) {
		if ($event->spkbg=='') continue;
		$bg .= '<section id="lecture-'.$event->eventid.'">';
		$bg .= '<p><strong> '.$event->title.' '.$event->spkbg.'</strong><p>';
		if (strlen($event->subtitle)>2) $bg .= '<p><small>'.htmlentities($event->subtitle).'</small></p>';
		$bg .= '<p>'.htmlentities($event->abstract).'</p>';
#		$bg .= '<p>'.htmlentities($event->description).'</p>';
		$bg .= "</section>";

		$bg .= '<div class="separator"></div>';

		$en .= '<section id="lecture-'.$event->eventid.'">';
		$en .= '<p><strong> '.$event->title.' '.$event->spken.'</strong><p>';
		if (strlen($event->subtitle)>2) $en .= '<p><small>'.htmlentities($event->subtitle).'</small></p>';
		$en .= '<p>'.htmlentities($event->abstract).'</p>';
#		$en .= '<p>'.htmlentities($event->description).'</p>';
		$en .= "</section>";

		$en .= '<div class="separator"></div>';

	};
	$bg .= '</section>';
	$en .= '</section>';


	$bgpost['post_content'] = $bg;
	$enpost['post_content'] = $en;

	$bgpost['post_content_filtered'] = $bg;
	$enpost['post_content_filtered'] = $en;

	$bgpost['post_status'] = 'publish';
	$enpost['post_status'] = 'publish';

	$bgpost['post_type'] = 'page';
	$enpost['post_type'] = 'page';

	$bgpost['filter'] = true;
	$enpost['filter'] = true;

	kses_remove_filters();

	wp_insert_post($bgpost);
	wp_insert_post($enpost);


