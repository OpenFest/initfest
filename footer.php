		<div class="clear"></div>

		<footer>
			<div class="content grid">
				<div class="col4">
					<h3>OpenFest</h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-openfest', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
				<div class="col4">
					<h3>Програма</h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-schedule', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
				<div class="col4">
					<h3>Други</h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-others', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
				<div class="col4">
					<h3>Последвайте ни в:</h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-followus', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
			</div>
			<div id="copyright">
			<?php echo sprintf( __( '%1$s %2$s %3$s. Some Rights Reserved.', 'initfest' ), '&copy;', date( 'Y' ), esc_html( get_bloginfo( 'name' ) ) ); ?>
			</div>
		</footer>
		<?php wp_footer(); ?>
	</body>
</html>
