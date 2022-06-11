<?php
/*
    Template name: 搜索页面模版
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php
        $search_style = get_option('site_search_style_switcher');
        if($search_style){
    ?>
        	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/news.css" />
            <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/weblog.css" />
            <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/acg.css" />
    <?php
        }
    ?>
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
    <em class="digital_mask" style="background: url(<?php custom_cdn_src(); ?>/images/svg/digital_mask.svg)"></em>
    <video src="<?php echo get_option('site_search_video'); ?>" poster="<?php custom_cdn_src(); ?>/images/search.jpg" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query;
            $res_num = $wp_query->found_posts;
            $queryString=esc_html(get_search_query());
            // $page_flag = strpos(get_option('site_search_includes'), 'page')!==false ? '/page' : '';
            $res_array = explode(',',trim(get_option('site_search_includes','post')));  // NO "," Array
            foreach ($res_array as $each){
                if(trim($each)=='page') $page_flag='/页面';
            }
            echo '<b> '.$res_num.' </b>篇有关“<span>'.$queryString.'</span>”の文章'.$page_flag;//printf(esc_html__('%d条关于“%s”的文章', ''),$res_num,'<span>'.esc_html(get_search_query()).'</span>');
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php
                    $total_pages = $left_query->max_num_pages;  //total pages
                    if(have_posts()) {
                        // print_r($wp_query);
                        while (have_posts()): the_post();
                            if(!$search_style){
                ?>
                                <div class="cid-<?php the_ID(); ?>">
                                    <h1>
                                        <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                        <?php $postmeta=get_post_meta($post->ID, "post_rights", true); echo $postmeta ? '<sup>'.$postmeta.'</sup>' : false; ?>
                                    </h1>
                                    <p><?php the_excerpt() ?></p>
                                    <div class="info">
                                        <span class="valine-comment-count" data-xid="<?php the_permalink() ?>"><?php echo $post->comment_count; ?></span>
                                        <span class="date"><?php the_time('d-m-Y'); ?></span>
                                    </div>
                                </div>
                    <?php
                            }else{
                                if(in_category('news')){
                    ?>
                                    <!--<link type="text/css" rel="stylesheet" href="<?php //custom_cdn_src(); ?>/style/news.css" />-->
                                    <article class="news-window wow" data-wow-delay="0.1s">
                                        <div class="news-window-inside">
                                            <span class="news-window-img">
                                                <a href="<?php the_permalink() ?>" target="_blank">
                                                    <img class="lazy" src="<?php echo get_postimg(); ?>" />
                                                </a>
                                            </span>
                                            <div class="news-inside-content">
                                                <h2 class="entry-title">
                                                    <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title() ?>"><?php the_title() ?></a>
                                                </h2>
                                                <span class="news-core_area entry-content"><?php the_excerpt(); ?></span>
                                                <?php
                                                    $postmeta = get_post_meta($post->ID, "post_feeling", true);
                                                    if($postmeta) echo '<span class="news-personal_stand" unselectable="on"><dd>'.$postmeta.'</dd></span>';
                                                ?>
                                                <div id="news-tail_info">
                                                    <ul class="post-info">
                                                        <li class="tags author"><?php $tag = get_the_tag_list();if($tag) echo($tag);else echo '<a href="javascript:;" target="_blank" rel="nofollow">'.get_option('site_nick').'</a>'; ?></li>
                                                        <li title="评论人数"><?php if(!get_option('site_comment_switcher')) $count=$post->comment_count;else $count=0; echo '<span class="valine-comment-count" data-xid="'.get_the_permalink().'">'.$count.'</span>'; ?></li>
                                                        <li id="post-date" class="updated" title="发布日期">
                                                            <i class="icom"></i><?php the_time('d-m-Y'); ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                    <?php
                                }elseif(in_category(array("weblog"))){
                    ?>
                                    <article class="weblog-tree-core-record i<?php the_ID() ?>">
                                        <div class="weblog-tree-core-l">
                                            <span id="weblog-timeline"><?php the_time('d-m-Y'); ?></span>
                                            <span id="weblog-circle"></span>
                                        </div>
                                        <div class="weblog-tree-core-r">
                                            <div class="weblog-tree-box">
                                                <div class="tree-box-title">
                                                    <a href="<?php //the_permalink() ?>" id="<?php the_title(); ?>" target="_self">
                                                        <h3><?php the_title() ?></h3>
                                                    </a>
                                                </div>
                                                <div class="tree-box-content">
                                                    <span id="core-info">
                                                        <p class="excerpt"><?php custom_excerpt(100) ?></p>
                                                    </span>
                                                    <span id="other-info">
                                                        <h4> Ps. </h4>
                                                        <p class="feeling"><?php echo get_post_meta($post->ID, "post_feeling", true); ?></p>
                                                        <p id="sub"><?php the_time('Y-n-j'); ?></p>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                    <?php  
                                }elseif(in_category('acg')){
                    ?>
                                    <div class="rcmd-boxes flexboxes">
                                        <div class="info anime flexboxes">
                                            <div class="inbox flexboxes">
                                                <div class="inbox-headside flexboxes">
                                                    <span class="author"><?php echo get_post_meta($post->ID, "post_feeling", true); ?></span>
                                                    <img class="bg" src="<?php echo get_postimg(); ?>">
                                                    <img src="<?php echo get_postimg(); ?>">
                                                </div>
                                                <div class="inbox-aside">
                                                    <span class="lowside-title">
                                                        <h4><a href="<?php echo get_post_meta($post->ID, "post_source", true); ?>" target="_blank"><?php the_title(); ?></a></h4>
                                                    </span>
                                                    <span class="lowside-description">
                                                        <p><?php the_content(); ?></p>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    <?php
                                }else{
                    ?>
                                    <article class="cat-<?php the_ID(); ?>">
                                        <h1>
                                            <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                            <?php $postmeta=get_post_meta($post->ID, "post_rights", true); echo $postmeta&&$postmeta!="请选择" ? '<sup>'.$postmeta.'</sup>' : false; ?>
                                        </h1>
                                        <p><?php the_excerpt() ?></p>
                                        <div class="info">
                                            <span class="classify" id="<?php $cpar = get_the_category()[1]->parent==0 ? get_the_category()[1] : get_the_category()[0];echo $cpar->slug; ?>">
                                                <i class="icom"></i><?php echo $cpar->name; ?>
                                            </span>
                                            <span class="valine-comment-count" data-xid="<?php the_permalink() ?>"><?php echo $post->comment_count; ?></span>
                                            <span class="date"><?php the_time('d-m-Y'); ?></span>
                                            <span id="slider"></span>
                                        </div>
                                    </article>
                    <?php
                                }
                            }
                        endwhile;
                            $pages = paginate_links(array(
                                'prev_text' => __('上一页'),
                                'next_text' => __('下一页'),
                                'type' => 'plaintext',
                                'screen_reader_text' => null,
                                'total' => $wp_query -> max_num_pages,  //总页数
                                'current' => max(1, get_query_var('paged')), //当前页数
                            ));
                            if($pages) echo '<div class="pageSwitcher" style="width:100%;display:inline-block;user-select: none;">'.$pages.'</div>';
                    }else{
                        echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.$queryString.' </h1></div>';  //<b>'.current_slug(true).'</b> 
                    }
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