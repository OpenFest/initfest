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

$eventStart = new DateTimeImmutable($eventsConfig[$blog_slug]['startTime'] . ' Europe/Sofia');
$eventStartInterval = $now->diff($eventStart);
$isBeforeEvent = $eventStartInterval instanceof DateInterval && $eventStartInterval->invert === 0;

$eventEnd = new DateTimeImmutable($eventsConfig[$blog_slug]['endTime'] . ' Europe/Sofia');
$eventEndInterval = $now->diff($eventEnd);
$isAfterEvent = $eventEndInterval instanceof DateInterval && $eventEndInterval->invert === 1;

if ($isBeforeEvent) {
?>
    <style>
	.countdown {
		text-align: center;
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
    <br><br>
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

if ($isAfterEvent) {
?>
    <?php e_('after_event'); ?>
<?php
}
