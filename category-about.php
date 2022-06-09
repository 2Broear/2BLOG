<?php
/*
    Template name: 关于模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/about.css?v=2" />
    <style> .player{ box-shadow: none!important;}</style>
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
    <header>
        <nav id="tipson" class="ajaxloadon">
            <?php get_header(); ?>
        </nav>
    </header>
    <?php //get_inform(); ?>
    <div class="content-all-windows">
        <div class="Introduce-window" style="width: 100%;">
            <div class="Introduce-core">
                <div class="In-core-head">
                    <div class="head-inside wow fadeInUp" data-wow-delay="0.15s">
                        <div class="user_info">
                            <span id="head-photo">
                                <img src="<?php echo get_option('site_avatar'); ?>" style="width: 100%;max-height: 100%;border-radius: inherit;" /><span></span></span>
                            <span id="head-nickname"><strong><?php echo get_option('site_nick'); ?></strong></span>
                            <span id="head-sign" style="opacity: .75;"> <?php bloginfo('description'); ?> </span>
                        </div>
                        <video src="<?php echo get_option('site_about_video'); ?>" poster="<?php echo cat_metabg($cat); ?>" preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>
                    </div>
                </div>
                <div class="In-core-body">
                    <div class="body-basically wow fadeInUp" data-wow-delay="0.1s">
                        <div class="Introduce">
                            <?php 
                                the_content();  // the_page_content(current_slug());
                                dual_data_comments();  // include_once(TEMPLATEPATH. '/comments.php');
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
</body></html>