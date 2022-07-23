<?php
/*
    Template name: 文章模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo(custom_cdn_src()); ?>/style/news.css?v=0.11" />
    <?php get_head(); ?>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
        <?php //echo $cat;//get_inform(); ?>
        <div class="content-all-windows hfeed">
            <div class="news-content-window">
                <?php breadcrumb_switch(true,true); ?>
				<div class="news-article-list-box">
					<div class="news-article-list p1">
                        <?php
                            $current_page = max(1, get_query_var('paged')); //current paged
                            $left_query = new WP_Query(array_filter(array(
                                'cat' => $cat,  //get_template_bind_cat(basename(__FILE__))->term_id;
                                'meta_key' => 'post_orderby',
                                'orderby' => array(
                                    'meta_value_num' => 'DESC',
                                    'date' => 'DESC'
                                ),
                                'paged' => $current_page,  //current paged
                                'posts_per_page' => get_option('posts_per_page'),  //use left_query counts
                                // 'post__in' => get_option('sticky_posts'),  // topset only
                            )));
                            $total_pages = $left_query->max_num_pages;  //total pages
                            // Empty card if null reponsed
                            if(!$left_query->have_posts()){
                                echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t="'.current_slug().'"></i><h1> <b>>_∩</b>0ρ0st </h1></div>';  //<b>'.current_slug(true).'</b> 
                            }
                            while ($left_query->have_posts()):
                                $left_query->the_post();
                                $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                                $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                        ?>
                                <article class="<?php if($post_orderby>1) echo 'topset'; ?> news-window icom wow" data-wow-delay="0.1s" post-orderby="<?php echo $post_orderby; ?>">
                                    <div class="news-window-inside">
                                        <?php
                                            if(has_post_thumbnail() || get_option('site_default_postimg_switcher')) echo '<span class="news-window-img"><a href="'.get_the_permalink().'"><img class="lazy" src="'.get_postimg().'" /></a></span>';
                                        ?>
                                        <div class="news-inside-content" style="<?php echo $hasimg_style; ?>">
                                            <h2 class="entry-title">
                                                <a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
                                            </h2>
                                            <span class="news-core_area entry-content"><p><?php custom_excerpt(66); ?></p></span>
                                            <?php if($post_feeling) echo '<span class="news-personal_stand" unselectable="on"><dd>'.$post_feeling.'</dd></span>'; ?>
                                            <div id="news-tail_info">
                                                <ul class="post-info">
                                                    <li class="tags author"><?php echo get_the_tag_list('','、',''); ?></li>
                                                    <li title="讨论人数"><?php if(!get_option('site_comment_switcher')) $count=$post->comment_count;else $count=0; echo '<span class="valine-comment-count" data-xid="'.parse_url(get_the_permalink(), PHP_URL_PATH).'">'.$count.'</span>'; ?></li>
                                                    <li id="post-date" class="updated" title="发布日期">
                                                        <i class="icom"></i><?php the_time('d-m-Y'); ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                        <?php
                            endwhile;
                            wp_reset_query();
                        ?>
					</div>
				</div>
                <div class="pageSwitcher">
                    <?php 
                        echo paginate_links(array(
                            'prev_text' => __('上一页'),
                            'next_text' => __('下一页'),
                            'type' => 'plaintext',
                            'screen_reader_text' => null,
                            'total' => $total_pages,  //总页数
                            'current' => $current_page, //当前页数
                        ));
                    ?>
                </div>
            </div>
            <div class="news-slidebar-window">
                <?php get_sidebar(); ?>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
    <!-- asyncLoadJs -->
    <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
</body></html>