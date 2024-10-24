<?php get_header(); ?>

<section class="content subtitle_content">
<?php
e_('about_event');

$blog_slug = get_blog_slug();

if ($blog_slug === '2024') {
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
	}

	.countdown > .units > td {
	    font-size: 12px;
	}
    </style>
    <br><br>
    <div class="countdown">
        <?php e_('countdown_text_before'); ?>
        <table>
            <tbody>
                <tr class="digits">
                    <td>08</td>
                    <td>10</td>
                    <td>21</td>
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
?>
</section>
<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
<div class="separator"></div>
<div class="col-right sponsors sponsors-frontpage">
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <?php echo do_shortcode( '[partners]' ); ?>
</div>
<div class="separator"></div>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>
