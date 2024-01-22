<?php
/*
    Template name: 留言模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <style>
        #reverse_rotate{direction: rtl;unicode-bidi: bidi-override;transform:rotate(180deg);display: inline-block;}
        .content-all-windows{display: block!important;}
        .win-top h5{font-family: "Playfair Display",宋体 , serif!important;}
        #vcomments{padding:0!important;}
        /*@media screen and (max-width: 600px) {*/
        /*    .win-top h5{padding: 15% 5%;font-size: 2.33rem!important;}*/
        /*}*/
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" attr-bg="">
            <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
			<header>
				<nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
				</nav>
			</header>
            <video src="<?php echo $video = replace_video_url(get_option('site_guestbook_video')); ?>" poster="<?php echo $video ? $video : get_meta_image($cat, $img_cdn.'/images/guestbook.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <h5> <?php $cat_desc = get_category($cat)->category_description;echo $cat_desc ? '<span>'.$cat_desc.'</span>' : '畅所<b style="font-family:sans-serif;">，</b><span>你の欲言。</span>'; ?> </h5>
        </div>
		<div class="content-all-windows" style="padding-top:0;">
            <?php 
                the_content();  // the_page_content(current_slug());
                dual_data_comments();  // include_once(TEMPLATEPATH. '/comments.php');
            ?>
		</div>
		<footer>
            <?php get_footer(); ?>
		</footer>
	</div>
<!-- siteJs -->
<?php get_foot(); ?>
<!-- pluginJs -->
</body></html>