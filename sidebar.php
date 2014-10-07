<?php
if ($wp->query_vars['pagename']!='home' && $wp->query_vars['pagename']!='home-2')  {
?>
<div class="col-right">

    <h3>1-и и 2-и Ноември, 2014г. <br /> Интерпред, София, България</h3>
    <div class="separator"></div>
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <div class="separator"></div>
    <?php echo do_shortcode( '[transport]' ); ?>
</div>
<?php
}
?>
