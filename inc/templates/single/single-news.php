<?php
/*
    Template Name: 文章模板
    Template Post Type: post, news
*/
    global $src_cdn, $img_cdn;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/articles.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/fancybox.css?v=<?php echo get_theme_info(); ?>" />
    <style>
        figure > figure{width:30%;vertical-align:middle}
        .news-article-container img {
            height: auto;
        }
        blockquote.chatGPT{
            margin-left: -15px;
        }
        p.has-text-align-right{
            text-align: right;
        }
        figure.wp-block-gallery img {
            width: 100%;
            height: 100%!important;
        }
        figure.wp-block-gallery > figure {
            max-height: 50vh;
        }
        /*@media screen and (max-width:600px){*/
        /*    figure.wp-block-gallery img {*/
        /*        max-height: 300px!important;*/
        /*    }*/
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
    <div class="content-all-windows">
        <div class="news-article-window<?php $sidebar = !array_key_exists('sidebar_status',$_COOKIE) ? 1 : $_COOKIE['sidebar_status']; echo !$sidebar ? " fullview" : ""; 
        ?>">
            <?php breadcrumb_switch(false,true); ?>
            <div class="news-article-core">
                <div class="news-article-inside">
                    <div id="news-article-head">
                        <div class="news-article-head-tools">
                            <div class="tools-inside-block">
                                <?php if(get_option('site_not_ai_switcher')) echo '<span><a href="//notbyai.fyi" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/not-by-ai.svg" alt="notbyai" style="height: 15px;filter:invert(0.5);padding:5px"></a></span>'; ?>
                                <span id="full-view" title="满屏切换" style="<?php echo !$sidebar ? 'pointer-events:none;opacity:.5;' : false; ?>">
                                    <em><?php echo $sidebar  ? "全屏阅读" : "展开边栏"; ?></em>
                                </span>
                                <span id="font-plus" title="字体大小">
                                    <em><?php $fontsize = !array_key_exists('article_fontsize',$_COOKIE) ? 0 : $_COOKIE['article_fontsize'];echo $fontsize ? "A-" : "A+"; ?></em>
                                </span>
                                <!--<span id="s2t2s-switch" title="简繁切换"><em>简</em></span>-->
                                <?php $rights = get_post_meta($post->ID, "post_rights", true);if($rights&&$rights!='请选择') echo '<span id="copyright-sign" title="版权声明"><em>' . $rights . '</em></span>'; ?>
                            </div>
                        </div>
                        <h1> <?php the_title(); ?> </h1>
                        <div id="news-article-head_tail">
                            <span class="article-copyright-notice"><q>文章由<?php $source = get_post_meta($post->ID, "post_source", true);if($source!="") echo '<a href="/">'.$source.'</a>';else echo '<a href="/">'.get_option("site_nick").'</a>';?> 创作，遵循 <?php echo get_option("site_copyright") ?> 协议</q></span>
                            <ul>
            	  	            <!--<li id="post-level" title="时态等级"><i class="icom"></i>L-LV</li>-->
            	  	            <li id="post-views" title="浏览信息"><i class="icom"></i><?php $cat=get_the_ID();setPostViews($cat);echo getPostViews($cat); ?>
                              </li>
            	  	            <li id="post-date" title="发布日期"><i class="icom"></i><?php the_time('d-m-Y'); ?></li>
            	  	          </ul>
                        </div>
                    </div>
                    <div class="news-article-container<?php echo $fontsize ? " AfontPlus" : ""; ?>">
                        <div class="content">
                            <?php the_content(); ?>
                        </div>
                        <br />
                        <h5> 本文完结 </h5>
                        <?php 
                            //https://stackoverflow.com/questions/7052297/wp-list-comments-not-working
                            dual_data_comments();  // query comments from database before include
                            //DO NOT INCLUDE AFTER CALLING comments_template, cause fatal error,called twice
                            // include_once(TEMPLATEPATH . '/comments.php')
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php get_sidebar(); ?>
    </div>
    <footer>
        <?php get_footer(); ?>
    </footer>
</div>
<!-- siteJs-->
<!-- pluginJs-->
<!--<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/s2t.js"></script>-->
<!-- inHtmlJs -->
<?php get_foot(); ?>
</body></html>