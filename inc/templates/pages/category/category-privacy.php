<?php
/*
    Template name: 隐私政策
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/about.css?v=<?php echo get_theme_info(); ?>" />
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
    <div class="win-top bg">
        <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
        <video src="<?php echo $video = replace_video_url(get_option('site_privacy_video')); ?>" poster="<?php echo $video ? $video : get_meta_image($cat, $img_cdn.'/images/privacy.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload" playsinline -webkit-playsinline></video>
	    <h5><?php $cat_desc = get_category($cat)->category_description;echo $cat_desc ? $cat_desc : '<span> 隐私</span> 协议'; ?></h5>
    </div>
    <div class="content-all-windows">
        <div class="Introduce-window" style="width: 100%;">
            <div class="Introduce-core">
                <div class="In-core-body">
                    <div class="body-basically">
                        <div class="Introduce">
                            <?php 
                                the_content();  // the_page_content(current_slug());
                                dual_data_comments();  // query comments from database before include
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <?php get_footer(); ?>
    </footer>
</div>
<!-- siteJs -->
<?php get_foot(); ?>
</body></html>