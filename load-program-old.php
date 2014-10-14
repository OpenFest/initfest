<?php

	require("cli-header.php");

	/* fugly hardcoding */
	$sched_en=3263;
	$sched_bg=10;

	$en = $bg = '<style type="text/css"><!--
		.lecture-description {
			        display: none;
					}
--></style><script type="text/javascript">// <![CDATA[
	jQuery( document ).ready(function($) {
		    $( \'.program\' ).on( \'click\', \'.lecture-title\', function() {
				               $(this).nextAll( \'.lecture-description\' ).toggle( \'slow\' );
							       });
});
// ]]></script><div class="program">';

$prg = pg_query("select
	h.name as hallname,
	to_char(starts_at, 'DD FMMonth - FMDay') as dt,
	to_char(s.starts_at,'HH24:MI') as start, to_char(s.ends_at,'HH24:MI') as end,
	e.title, e.language,
	e.abstract,
	t.name as tname, t.color as tcolor,
	sp.first_name || ' ' || sp.last_name as spname
	from 
	slots s join halls h on h.id=s.hall_id 
	join events e on s.event_id = e.id
	join tracks t on t.id=e.track_id
	join users u on u.id=e.user_id
	join speaker_profiles sp on sp.user_id=u.id
	where 
	not s.event_id is null 
	order by date(s.starts_at),s.hall_id, s.starts_at
");

	$bgpost = array();
	$enpost = array();

	$bgpost['ID'] = $sched_bg;
	$enpost['ID'] = $sched_en;

	$bgpost['post_title'] = "Програма";
	$enpost['post_title'] = "Schedule";

	$bgpost['post_name'] = "schedule";
	$enpost['post_name'] = "schedule";

	$cdate='';
	$chall='';

	while ($row = pg_fetch_object($prg)) {

		if ($chall!=$row->hallname && strlen($chall)>1) {
			$bg.= "</table>\n";
			$en.= "</table>\n";
		}

		if ($cdate!=$row->dt) {
			$cdate = $row->dt;
			$bg.= "<h2>$cdate</h2>\n";
			$en.= "<h2>$cdate</h2>\n";
		}

		if ($chall!=$row->hallname) {
			$chall = $row->hallname;
			echo pll_translate_string($chall, 'en_US')."\n";
			$en.= "<table><caption>".pll_translate_string($chall, 'en')." Hall</caption><tbody>";
			$bg.= "<table><caption>Зала ".pll_translate_string($chall,'bg')."</caption><tbody>";
		}



		$bg .= '<tr><td class="time">'.$row->start.' - '.$row->end.'</td>';
		$bg .= '<td><span class="lecture-title"><a href="javascript: void0">'.htmlentities($row->title).'</a></span><br/>';
		$bg .= '<a class="vt-p" href="/bg/schedule-3/speakers/#'.htmlentities($row->spname).'">'.htmlentities($row->spname).'</a><br/>';
		$bg .= '<font color="#'.$row->tcolor.'">'.$row->tname.'</font>';
		$bg .= '<div class="lecture-description">'.htmlentities($row->abstract).'</div></td></tr>';
		
		$en .= '<tr><td class="time">'.$row->start.' - '.$row->end.'</td>';
		$en .= '<td><span class="lecture-title"><a href="javascript: void0">'.htmlentities($row->title).'</a></span><br/>';
		$en .= '<a class="vt-p" href="/en/schedule/speakers/#'.htmlentities($row->spname).'">'.htmlentities($row->spname).'</a><br/>';
		$en .= '<font color="#'.$row->tcolor.'">'.$row->tname.'</font>';
		$en .= '<div class="lecture-description">'.htmlentities($row->abstract).'</div></td></tr>';
		/*
		<tr>
			<td class="time">10:15 – 11:00</td>
			<td><span class="lecture-title"><a href="javascript: void0">Open-source hardware от България</a></span>
			<a class="vt-p" href="/lecturers/#tzvetan">Цветан Узунов</a>
			<div class="lecture-description">Какво е OSHW (Open Source Hardware)?
			Какви са приликите и разликите между OSHW и FOSS?
			Може ли да се прави бизнес с OSHW?
			Кога да правим и кога да не правим OSHW?
			OLinuXino boards roadmap.
			Olimex's Arduino boards roadmap.</div></td>
			</tr>
		 */

	}
	$bg.= "</table></div>\n";
	$en.= "</table></div>\n";


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

	/*
		$newpost = array();
		$newpost['post_type'] = 'speakers';
		$newpost['post_status'] = 'publish';
		$newpost['post_title'] = $row->first_name." ".$row->last_name;
		$newpost['post_content'] = $row->biography;
		$newpost['post_excerpt'] = $row->biography;

		$postid=wp_insert_post($newpost);
		$url = 'https://cfp.openfest.org/uploads/speaker_profile/picture/'.$row->id.'/schedule_'.$row->picture;
		echo $url."\n";
		$att = media_sideload_image($url, $postid, "photo_spk_".$row->user_id);
		preg_match("%src='(http://[^']*)'%", $att,  $matches);
		var_dump($matches);
		$wpurl = $matches[1];
		$attid = pn_get_attachment_id_from_url($wpurl);
		add_post_meta($postid, '_thumbnail_id', $attid);
		pll_set_post_language($postid, 'bg');
	}*/

