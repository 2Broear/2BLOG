<?php
/*
    Template name: 归档页面
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/notes.css?v=<?php echo(mt_rand()) ?>" />
    <?php get_head(); ?>
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
<div class="win-top bg" style="background: url() top center /cover">
	<header>
		<nav id="tipson" class="ajaxloadon">
            <?php get_header(); ?>
		</nav>
	</header>
    <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
    <video src="" poster="<?php custom_cdn_src('img'); ?>/images/archive.jpg" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query;
            $dates = $wp_query->query;
            $date_yea = $dates['year'];
            $date_mon = array_key_exists('monthnum',$dates) ? '<b> '.$dates['monthnum'].' </b>月' : '';
            echo '<b>'.$date_yea.'</b> 年'.$date_mon.'中找到<b> '.$wp_query->found_posts.' </b>篇记录';
            // print_r($dates);
            $string = 'Archives of '.$date_yea;
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php the_posts_with_styles($string); ?>
			</div>
		</div>
	</div>
</div>
<footer>
    <?php get_footer(); ?>
</footer>
</div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
<!-- inHtmlJs -->
</body></html>