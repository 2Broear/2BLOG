<?php
/*
    Template name: 标签页面
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/notes.css?v=<?php echo get_theme_info(); ?>" />
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
    <video src="" poster="<?php custom_cdn_src('img'); ?>/images/tag.png" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query;
            $tagString = single_tag_title('',false);
            echo '<b> '.$wp_query->found_posts.' </b>篇标签“<span>'.$tagString.'</span>”の内容';
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php the_posts_with_styles($tagString); ?>
			</div>
		</div>
	</div>
</div>
<footer>
    <?php get_footer(); ?>
</footer>
</div>
<?php get_foot(); ?>
<!-- siteJs -->
<!--<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/main.js"></script>-->
<!-- inHtmlJs -->
</body></html>