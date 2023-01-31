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
        .win-top em.digital_mask:before{
            content: "";
        }
        figure > figure{
            vertical-align: bottom;
        }
        .content .index_anchor{
            position: relative;
            top: -65px;
            visibility: hidden;
            opacity: 0;
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
                                <?php the_tag_list($post->ID, 5); ?>
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
        // 截取设置当前页面所有视频 poster 
        setVideoPoster(10);
        // gallery js initiate 'bodyimg' already exists in footer lazyload, use contimg insted.
        const contimg = document.querySelectorAll(".news-article-container .content img");
        if(contimg.length>=1){
            for(let i=0;i<contimg.length;i++){
                let eachimg = contimg[i],
                    datasrc = eachimg.dataset.src,
                    imgbox = document.createElement("a");
                imgbox.setAttribute("data-fancybox","gallery");
                imgbox.setAttribute("href", datasrc);
                imgbox.setAttribute("aria-label", "gallery_images");
                eachimg.parentNode.insertBefore(imgbox, eachimg);
                imgbox.appendChild(eachimg);
            }
        }
    </script>
</body></html>