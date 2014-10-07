<?php
if ($wp->query_vars['pagename']!='home' && $wp->query_vars['pagename']!='home-2')  {
?>
<div class="col-right">

<h3><?php pll_e('1-ви и 2-ри ноември 2014 г.');?> <br /> <?php pll_e('Интерпред, София, България');?></h3>
    <div class="separator"></div>
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <div class="separator"></div>
    <?php echo do_shortcode( '[transport]' ); ?>
</div>
<?php
}
?>
