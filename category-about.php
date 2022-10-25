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
    <style> 
        .player{ box-shadow: none!important;}
        /*.In-core-head .head-inside .user_info{*/
        /*    color: white;*/
        /*}*/
        /*.about_blocks ul{*/
        /*    padding: 0;*/
        /*    display: flex;*/
        /*    flex-flow: row nowrap;*/
        /*    justify-content: space-between;*/
        /*    align-items: center;*/
        /*}*/
        /*.about_blocks li.video{*/
        /*    width: 100%;*/
        /*}*/
        /*.about_blocks li:first-child{*/
        /*    margin: auto;*/
        /*}*/
        /*.about_blocks li{*/
        /*    width: 50%;*/
        /*    color: black;*/
        /*    display: inline-block;*/
        /*    background: var(--preset-s);*/
        /*    border-radius: var(--radius);*/
        /*    margin-left: 15px;*/
        /*}*/
    </style>
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
                                    <video src="<?php $video=get_option('site_about_video');echo $video; ?>" poster="<?php echo cat_metabg($cat, get_option('site_bgimg')); ?>" <?php echo $video ? 'controls=""' : false; ?> preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>
                                </div>
                <!--    <div class="about_blocks">-->
                <!--        <ul>-->
                <!--            <li class="video">-->
                <!--            </li>-->
                <!--            <li>-->
                <!--                INFP-T-->
                <!--            </li>-->
                <!--            <li>-->
                <!--                Profiles-->
                <!--            </li>-->
                <!--        </ul>-->
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
<script>
    const video = document.querySelector('video'),
          user_info = document.querySelector('.user_info');
    video.onplaying=()=>{user_info.classList.remove('pause');user_info.classList.add('playing');}
    video.onpause=()=>{user_info.classList.remove('playing');user_info.classList.add('pause')}
</script>
</body></html>