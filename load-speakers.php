<?php

 	require("cli-header.php");

	foreach ( array('en', 'bg') as $lang) {
		$speakers_args = array( 'post_type' => 'speakers','lang' => $lang, 'nopaging' => 'true');
		$speakers = new WP_Query( $speakers_args );
		
		$result = $speakers->get_posts();
	#	var_dump($result);

		foreach ($result as $k=>$v) {
			$args = array(
				'post_parent' => $v->id,
				'post_type'   => 'attachment', 
				'posts_per_page' => -1,
				'post_status' => 'any' );

			$chld = get_children($args);
			foreach ($chld as $k => $att) {
				if (preg_match('/^photo_spk/', $att->post_name))
					wp_delete_post($att->ID, true);
			}
			wp_delete_post($v->ID, true);
		}
	}

	$spk = pg_query("select 
		distinct sp.user_id, sp.id, sp.first_name, sp.last_name, e.language, picture, biography, github, twitter, public_email
		from 
		speaker_profiles sp join events_speaker_profiles esp on sp.id=esp.speaker_profile_id 
		join events e on esp.event_id=e.id
		where e.state=1                          
		order by sp.first_name, sp.last_name;
");

	while ($row = pg_fetch_object($spk)) {
		$newpost = array();
		$newpost['post_type'] = 'speakers';
		$newpost['post_status'] = 'publish';
		$newpost['post_title'] = $row->first_name." ".$row->last_name;
		$newpost['post_content'] = $row->biography;
		$newpost['post_excerpt'] = $row->biography;

		$postid=wp_insert_post($newpost);
		$url = 'https://cfp.openfest.org/uploads/speaker_profile/picture/'.$row->id.'/schedule_'.$row->picture;
		echo $url."\n";
		$att = media_sideload_image($url, $postid, "photo_spk_bg_".$row->user_id);
		preg_match("%src='(http://[^']*)'%", $att,  $matches);
		$wpurl = $matches[1];
		$attid = pn_get_attachment_id_from_url($wpurl);
		add_post_meta($postid, '_thumbnail_id', $attid);
		pll_set_post_language($postid, 'bg');

		$postid_en=wp_insert_post($newpost);
		$url = 'https://cfp.openfest.org/uploads/speaker_profile/picture/'.$row->id.'/schedule_'.$row->picture;
		echo $url."\n";
		$att = media_sideload_image($url, $postid_en, "photo_spk_en_".$row->user_id);
		preg_match("%src='(http://[^']*)'%", $att,  $matches);
		$wpurl = $matches[1];
		$attid = pn_get_attachment_id_from_url($wpurl);
		add_post_meta($postid_en, '_thumbnail_id', $attid);
		pll_set_post_language($postid_en, 'en');

		foreach (array($postid, $postid_en) as $v) {
			if (strlen($row->github)>1)  add_post_meta ($v, 'github', $row->github);
			if (strlen($row->twitter)>1)  add_post_meta ($v, 'twitter', $row->twitter);
			if (strlen($row->public_email)>1)  add_post_meta ($v, 'public_email', $row->public_email);
		}

		pll_save_post_translations(array($postid => 'bg', $postid_en => '$en'));

	}

