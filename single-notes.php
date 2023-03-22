<?php
/*
    Template Name: 笔记模板
    Template Post Type: post, notes
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/n.css?v=<?php echo get_theme_info('Version'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/highlight/agate.m.css" />
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/fancybox.css" />
    <style>
        .win-top em.digital_mask{
            background-size: 2px 2px!important;
        }
        .bg h1{
            background: none;
        }
        .bg h1 a{
            background: linear-gradient(var(--theme-color), var(--theme-color)) no-repeat left 97%/0 30%;
            background-size: 100% 30%;
            color: inherit;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background: url(<?php echo get_postimg(0,$post->ID,true); ?>) center center /cover;">
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
            <h1><a href="javascript:;" rel="nofollow"><?php the_title(); ?></a><!--<span></span>--></h1>
        </div>
        <div class="content-all-windows">
            <div class="win-nav-content">
                <div class="win-content">
                    <article class="news-article-container">
                        <div class="infos">
                            <span id="classify">
                                <?php echo get_tag_list($post->ID, 5); ?>
                            </span>
                            <span id="view"><?php $cat=get_the_ID();setPostViews($cat);echo getPostViews($cat); ?>°C </span>
                            <span id="date"><i class="icom"></i> <?php the_time('d-m-Y'); ?> </span>
                            <span id="slider"></span>
                        </div>
                        <sup>最近更新于：<?php echo $post->post_modified; ?></sup>
                        <div class="content">
                            <?php the_content();//print_r(get_post_parent($post->ID)); ?>
                        </div>
                        <br />
                        <?php dual_data_comments();  //DO NOT INCLUDE AFTER CALLING comments_template, cause fatal error,called twice?>
                    </article>
                </div>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
    <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
    <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/highlight/highlight.pack.js"></script>
    <!-- plugins -->
    <script>hljs.initHighlightingOnLoad();</script>
    <script src="<?php custom_cdn_src(); ?>/js/fancybox.umd.js"></script>
    <script>
        // gallery js initiate 'bodyimg' already exists in footer lazyload, use contimg insted.
        fancyImages(document.querySelectorAll(".news-article-container .content img"));
        <?php
            if(get_option('site_video_poster_switcher')){
                echo 'setupVideoPoster(2);';  // 截取设置当前页面所有视频 poster 
            }
        ?>
    </script>
</body></html>