<?php
/*
    Template name: 搜索页面模版
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/notes.css?v=<?php //echo(mt_rand()) ?>" />
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
    <video src="" poster="<?php custom_cdn_src('img'); ?>/images/search.jpg" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query, $page_flag;
            $res_num = $wp_query->found_posts;
            $searchString=esc_html(get_search_query());
            // $page_flag = strpos(get_option('site_search_includes'), 'page')!==false ? '/page' : '';
            $res_array = explode(',',trim(get_option('site_search_includes','post')));  // NO "," Array
            foreach ($res_array as $each){
                if(trim($each)=='page') $page_flag='/页面';
            }
            echo '<b> '.$res_num.' </b>篇有关“<span>'.$searchString.'</span>”の内容'.$page_flag;//printf(esc_html__('%d条关于“%s”的文章', ''),$res_num,'<span>'.esc_html(get_search_query()).'</span>');
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php 
                    the_posts_with_styles($searchString);
                    // print_r($post);
                    // print_r($wp_query->have_posts());
                    // print_r($wp_query)
                ?>
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