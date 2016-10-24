<?php
$requirePath = __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load.php';
require $requirePath . 'parse.php';

$siteurl = get_option('siteurl');
$year = preg_replace('%.*/([0-9]*)$%', '\1', $siteurl);

$sched_config = getSchedConfig($year);

$data = loadData($sched_config);

if ( preg_match('/^workshop/', $pagename) ) {
	    $sched_config['filterEventType'] = "workshop";
} else {
	    $sched_config['filterEventType'] = "lecture";
}
$content = parseData($sched_config, $data);

