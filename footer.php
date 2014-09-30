<div class="clear"></div>

<footer>
            <div class="content grid">
                <div class="col4">
                    <h3>OpenFest</h3>
                    <p>
                        <?php wp_nav_menu(array('theme_location' => 'footer-openfest', 'items_wrap' => '%3$s<br/>' )); ?>
                        <!--a href="#">Начало</a><br />
                        <a href="#">Идеи и препоръки</a><br />
                        <a href="#">За събитието</a><br />
                        <a href="#">Спонсори</a><br />
                        <a href="#">Програма</a><br />
                        <a href="#">Екип</a><br />
                        <a href="#">Историята</a><br />
                        <a href="#">Контакти</a-->
                    </p>
                </div>
                <div class="col4">
                    <h3>Програма</h3>
                    <p>
                        <?php wp_nav_menu(array('theme_location' => 'footer-schedule', 'items_wrap' => '%3$s<br/>' )); ?>
                        <!--a href="#">Информация</a><br />
                        <a href="#">Календар</a><br />
                        <a href="#">Зали</a><br /-->
                    </p>
                </div>
                <div class="col4">
                    <h3>Други</h3>
                    <p>
                        <?php wp_nav_menu(array('theme_location' => 'footer-others', 'items_wrap' => '%3$s<br/>' )); ?>
                        <!--a href="#">Хотели</a><br />
                        <a href="#">Заведения</a><br />
                        <a href="#">Beer Events</a><br />
                        <a href="#">After party</a><br /-->
                    </p>
                </div>
                <div class="col4">
                    <h3>Последвайте ни в:</h3>
                    <p>
                        <?php wp_nav_menu(array('theme_location' => 'footer-follow', 'items_wrap' => '%3$s<br/>' )); ?>
                        <!--a href="#"><i class="fa fa-twitter"></i> Twitter</a><br />
                        <a href="#"><i class="fa fa-facebook"></i> Facebook</a><br />
                        <a href="#"><i class="fa fa-youtube"></i> YouTube</a-->
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
