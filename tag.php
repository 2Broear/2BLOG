<?php
/*
    Template name: 标签页面模版
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/notes.css?v=<?php echo(mt_rand()) ?>" />
    <?php get_head(); ?>
	<style>
	    .win-content.main,
	    .news-inside-content .news-core_area p,
	    .empty_card{margin:0 auto;}
	    .news-inside-content .news-core_area p{padding:0}
    	.win-content{width:100%;padding:0;display:initial}
        .win-top h5:before{content:none}
        .win-top h5{font-size:3rem;color:var(--preset-e)}
        .win-top h5 span:before{content:'';display:inherit;width:88%;height:36%;background-color:var(--theme-color);position:absolute;left:15px;bottom:1px;z-index:-1}
        .win-top h5 span{position:relative;background:inherit;color:white;font-weight:bolder}
        .win-top h5 b{font-family:var(--font-ms);font-weight:bolder;color:var(--preset-f);/*padding:0 10px;vertical-align:text-top;*/}
        .win-content article{max-width:88%;margin-top:auto}
        .win-content article.news-window{padding:0;border:1px solid rgb(100 100 100 / 10%);margin-bottom:25px}
        .win-content article .info span{margin-left:10px}
        .win-content article .info span#slider{margin:auto}
	    .news-window-img{max-width:16%}
	    /*.news-window-img img{padding:10px}*/
	    .rcmd-boxes{width:21%;display:inline-block}
	    .rcmd-boxes .info .inbox{max-width:none}
	    /*.win-top h5:first-letter{
	        font-size: 8rem;
            font-weight: bold;
            margin: var(--pixel-pd);
            margin-bottom: auto;
            float: left;
            opacity: var(--opacity-hi);
	    }*/
	    .main h2{font-weight: 600};
        #core-info p{padding:0}
        @media screen and (max-width:760px){
            .win-content article{
                width: 100%;
            }
            .rcmd-boxes{width:49%!important}
        }
	</style>
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
    <video src="" poster="<?php custom_cdn_src('img'); ?>/images/default.jpg" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query;
            $tagString = single_tag_title('',false);
            echo '<b> '.$wp_query->found_posts.' </b>篇标签“<span>'.$tagString.'</span>”の文章';
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
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
<!-- inHtmlJs -->
</body></html>