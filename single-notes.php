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
        .child{
            padding-left: 20px;
        }
        .bg h1:hover{
            background-size: 88% 32%;
        }
        .bg h1{
            background: linear-gradient(var(--theme-color), var(--theme-color)) no-repeat center 100%/0 50%;
            background-size: 88% 12%;
            color: white;
            padding: 5px;
            border-radius: initial;
            transition: background-size .35s ease;
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
            <h1><?php the_title() ?><span></span> </h1>
        </div>
        <div class="content-all-windows">
            <div class="win-nav-content">
                <div class="win-content">
                    <article class="news-article-container">
                        <div class="infos">
                            <span id="classify">
                                <?php 
                                    $tags = get_the_tag_list('','、','');
                                    echo $tags ? $tags : '<a href="javascript:;" target="_blank" rel="nofollow">'.get_option('site_nick').'</a>';
                                ?>
                            </span>
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
        const bodyimg = document.querySelectorAll(".news-article-container .content img");
        if(bodyimg.length>=1){
            for(let i=0;i<bodyimg.length;i++){
                let imgsrc = bodyimg[i].src,
                    imgbox = document.createElement("a");
                imgbox.setAttribute("data-fancybox","gallery");
                imgbox.setAttribute("href",imgsrc);
                bodyimg[i].parentNode.insertBefore(imgbox, bodyimg[i]);
                imgbox.appendChild(bodyimg[i]);
            }
        };
    </script>
</body></html>