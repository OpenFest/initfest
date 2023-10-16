<?php
$requirePath = __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load.php';
require $requirePath . 'parse.php';

$year = get_blog_slug();

$sched_config = getSchedConfig($year);

$sched_config['lang'] = of_get_lang();

$data = loadData($sched_config);

if ( preg_match('/^workshop/', $pagename) ) {
	    $sched_config['filterEventType'] = "workshop";
} else if (!preg_match('/^full/', $pagename)) {
	    $sched_config['filterEventType'] = "lecture";
}
$content = parseData($sched_config, $data);

