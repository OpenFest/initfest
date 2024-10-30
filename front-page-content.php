<?php
if (!function_exists('get_blog_slug')) {
    exit;
}

require __DIR__ . '/config-events.php';

$blog_slug = get_blog_slug();

if (!array_key_exists($blog_slug, $eventsConfig)) {
    return;
}

$now = new DateTimeImmutable('now');

function interval_to_now(string $dateTimeStr) {
    global $now;
    return $now->diff(new DateTimeImmutable($dateTimeStr . ' Europe/Sofia'));
}

function is_before_interval(DateInterval $interval) {
    return $interval->invert === 0;
}

function is_after_interval(DateInterval $interval) {
    return $interval->invert === 1;
}

$eventStartInterval = interval_to_now($eventsConfig[$blog_slug]['startTime']);
$isBeforeEvent = is_before_interval($eventStartInterval);

$eventEndInterval = interval_to_now($eventsConfig[$blog_slug]['endTime']);
$isAfterEvent = is_after_interval($eventEndInterval);

$activeStreams = array_filter($eventsConfig[$blog_slug]['streams'], function($stream) {
    $streamStartInterval = interval_to_now($stream['startTime']);
    $streamEndInterval = interval_to_now($stream['endTime']);

    return is_after_interval($streamStartInterval) && is_before_interval($streamEndInterval);
});
$activeStream = reset($activeStreams);

$noCurrentStreams = !$isBeforeEvent && !$isAfterEvent && $activeStream === false;

if ($isBeforeEvent) {
?>
    <style>
	.countdown {
		text-align: center;
		margin-top: 18px;
	}

	.countdown > table {
	    margin: 0 auto;
	}

	.countdown .digits > td {
	    font-size: 30px;
	    padding: 0 10px;
	}

	.countdown .units > td {
	    font-size: 12px;
	}
    </style>
    <div class="countdown">
        <?php e_('countdown_text_before'); ?>
        <table>
            <tbody>
                <tr class="digits">
                    <td><?php echo $eventStartInterval->format('%a'); ?></td>
                    <td><?php echo $eventStartInterval->format('%H'); ?></td>
                    <td><?php echo $eventStartInterval->format('%I'); ?></td>
                </tr>
                <tr class="units">
                    <td><?php e_('countdown_days'); ?></td>
                    <td><?php e_('countdown_hours'); ?></td>
                    <td><?php e_('countdown_minutes'); ?></td>
                </tr>
            </tbody>
        </table>
        <?php e_('countdown_text_after'); ?>
    </div>
<?php
}

if ($activeStream) {
    // TODO
}

if ($noCurrentStreams) {
?>
    <style>
	.no_current_streams {
		text-align: center;
		margin-top: 18px;
	}
    </style>
    <div class="no_current_streams">
        <?php e_('no_current_streams'); ?>
    </div>
<?php
}

if ($isAfterEvent) {
?>
    <style>
	.after_event {
		text-align: center;
		margin-top: 18px;
	}
    </style>
    <div class="after_event">
        <?php e_('after_event'); ?>
    </div>
<?php
}
