		<div class="clear"></div>

		<footer class="clear">
			<div class="content grid footer">
				<div class="col4">
					<h3>OpenFest</h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-openfest', 'items_wrap' => '%3$s' )); ?>
					</p>
				</div>
				<div class="col4">
				<h3><?php e_('Програма')?></h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-schedule', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
                <?php
                    $blog_details = get_blog_details();
                    $blog_slug = str_replace('/', '', $blog_details->path);
                    if ( $blog_slug != "2020" ) {
                ?>
				<div class="col4">
				<h3><?php e_('Други')?></h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-others', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
                <?php } ?>
				<div class="col4">
				<h3><?php e_('Последвайте ни в:')?></h3>
					<p>
						<?php wp_nav_menu(array('theme_location' => 'footer-followus', 'items_wrap' => '%3$s<br/>' )); ?>
					</p>
				</div>
			</div>
			<div id="copyright">
			<?php echo sprintf( __( '%1$s %2$s %3$s. Some Rights Reserved.', 'initfest' ), '&copy;', date( 'Y' ), 'OpenFest' ); ?>
			</div>
		</footer>
		<?php wp_footer(); ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-56306862-1', 'auto');
  ga('send', 'pageview');

</script>
	</body>
</html>
