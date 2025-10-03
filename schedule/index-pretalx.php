<?php
error_reporting(~0);
ini_set('display_errors', 1);

$requirePath = __DIR__ . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load-pretalx.php';
require $requirePath . 'parse-pretalx.php';

$config = getSchedConfig(date('Y'));
$lang = array_key_exists('lang', $_GET) ? $_GET['lang'] : 'en';

$config['roomFilter'] = array_key_exists('type', $_GET) && array_key_exists($_GET['type'], $config['roomTypes']) ? $config['roomTypes'][$_GET['type']] : NULL;

$data = loadData($config, $lang);
$result = parseData($config, $data);

?>
<html>
	<head>
		<title>Test schedule</title>
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div class="separator"></div>
<div class="schedule">

<?php foreach($result as $content) { ?>
<h1><?php echo $content['date']; ?></h1>
<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule" >
<thead>
<tr>
<th></th>
<?php
foreach($content['rooms'] as $room) {
	echo '<th>' . $room . '</th>';
}

?>
</tr>
</thead>
		<tbody>
<?php
$span = 1;
foreach($content['slots'] as $slot => $rooms) {
	echo '<tr>';
	$begin = $slot;
	$end = nextTimepoint($content['timepoints'], $slot);

	if(--$span < 1)  {
		$span = diffSpan($begin, $end, $content['slotLength']);
		echo '<td rowspan="' . $span . '">' . $slot . ' - ' . nextTimepoint($content['timepoints'], $slot) . '</td>';
	}
	for($i = 0; $i < count($content['rooms']); $i++) {
		if(!array_key_exists($i, $rooms)) continue;
		$talk = $rooms[$i];

		if(empty($talk)) {
			echo '<td></td>';
			continue;
		}

		$persons = [];
		foreach($talk['talk']['persons'] as $person) {
			$persons[] = '<a class="vt-p" href="' . $config['baseUrl'] . 'speaker/' . $person['code'] . '/">' . $person['public_name'] . '</a>';
		}
		$speakers = implode(', ', $persons);

		$title = mb_substr($talk['talk']['title'], 0, $config['cut_len']) . (mb_strlen($talk['talk']['title']) > $config['cut_len'] ? '...' : '');

		echo '<td class="schedule-' . $talk['talk']['language'] . '" rowspan="'. $talk['span'] .'" style="background-color: ' . getTrackColor($talk['talk']['track'], $content['colors']). '">';
		echo '<a href="' . $talk['talk']['url'] . '">' . $title . '</a>';
		echo '<br/>' . $speakers;
	        echo '</td>';

	}
	echo '</tr>';
}
?>
		</tbody>
	</table>
<?php
}
?>

</div>
</body>
</html>
