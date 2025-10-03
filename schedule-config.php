<?php
$requirePath = __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';

$year = get_blog_slug();

$sched_config = getSchedConfig($year);

$sched_config['lang'] = of_get_lang();

# we are using clarion
if($year !== '2025') {
	require $requirePath . 'load-clarion.php';
	require $requirePath . 'parse-clarion.php';

	$data = loadData($sched_config);

	if ( preg_match('/^workshop/', $pagename) ) {
		$sched_config['filterEventType'] = "workshop";
	} else if (!preg_match('/^full/', $pagename)) {
		$sched_config['filterEventType'] = "lecture";
	}
	$content = parseData($sched_config, $data);

}
if($year === '2025') { # pretalx
	require $requirePath . 'load-pretalx.php';
	require $requirePath . 'parse-pretalx.php';

	$type = 'all';
	if ( preg_match('/^workshop/', $pagename) ) {
		$type = "workshop";
	} else if (!preg_match('/^full/', $pagename)) {
		$type = "lecture";
	}

	$lang = $sched_config['lang'];

	$sched_config['roomFilter'] = array_key_exists($type, $sched_config['roomTypes']) ? $sched_config['roomTypes'][$type] : NULL;

	$data = loadData($sched_config, $lang);
	$result = parseData($sched_config, $data);

	$config = $sched_config;
}
