<?php
/*
    Template name: 笔记模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/notes.css?v=<?php echo get_theme_info('Version'); ?>" />
	<style> 
	    .win-top h5{font-weight: 800;}
	    article .info span.valine-comment-count:before{margin-right: 3px;opacity: .75}
    </style>
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
<div class="win-top bg" style="background: url() center center /cover">
	<header>
		<nav id="tipson" class="ajaxloadon">
            <?php get_header(); ?>
		</nav>
	</header>
    <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
    <video src="<?php //echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, $img_cdn.'/images/1llusion.gif'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<!--<span id="fixed" style="background:inherit"></span>-->
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s"><span></span> <?php $cat_desc = get_category($cat)->category_description;echo $cat_desc ? $cat_desc : '好记性不如烂键盘'; ?><!--<strong>烂键盘</strong>--> </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-nav">
			<div class="nav-header workRange wow fadeInUp" style="background: url(<?php echo get_option('site_bgimg'); ?>) center center / cover">
				<span>
					<a href="/" target="_blank" style="border-radius: inherit;display: block">
					    <?php echo '<img '.$lazysrc.'="'.get_option('site_avatar').'" alt="'.get_bloginfo('name').'" />'; ?>
					</a>
					<em></em>
				</span>
				<div>
					<?php
					    echo '<h2>'.get_option('site_nick').'</h2><p>'.get_bloginfo('description').'</p><small>'.get_option('site_support').'</small>';
                        // $tags = get_tags(array('taxonomy' => 'post_tag'));
                        // echo '<b>'.count($tags).'<small>TAG</small></b><b>'.count($tags).'<small>POST</small></b><b>'.count($tags).'<small>COMMENTS</small></b>';
					?>
				</div>
			</div>
			<div class="nav-fixes">
				<div class="nav-body workRange wow fadeInUp" data-wow-delay="0.25s">
					<div class="main-root">
                        <ul class="wp_list_cats">
                        <?php 
                            $cats = get_sibling_categories();
                            $temp = get_cat_by_template(str_replace('.php',"",substr(basename(__FILE__),9)));
                            function sub_recursive_navigator($cats, $deepth=0){
                                if(!empty($cats)){
                                    global $cat;
                                    $deepth++;
                                    foreach($cats as $the_cat){
                                        $the_cat_id = $the_cat->term_id;
                                        $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
                                        $deepth = !empty($catss) ? $deepth++ : $deepth; //$level = !empty($catss) ? $deepth++ : $deepth;
                                        $choosen = $the_cat_id==$cat || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single() ? "choosen" : "";
                                        echo '<li class="cat_'.$the_cat_id.' level_'.$deepth.'"><a href="'.get_category_link($the_cat).'" id="'.$the_cat->slug.'" class="'.$choosen.'">'.$the_cat->name.'</a>';
                                        if(!empty($catss)){
                                            echo '<div class="sub-root"><ol>';
                                            sub_recursive_navigator($catss, $deepth);
                                            echo "</ol></div>";
                                        };
                                        echo "</li>";
                                    }
                                }
                            }
                            sub_recursive_navigator($cats);
                        ?>
                      </ul>
					</div>
				</div>
				<div class="nav-footer"></div>
			</div>
		</div>
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php
                    $current_page = max(1, get_query_var('paged')); //current paged
                    $left_query = new WP_Query(array_filter(array(
                        'cat' => $cat, //$page_cat->term_id;  // 可变 cid
                        'meta_key' => 'post_orderby',
                        'orderby' => array(
                            'meta_value_num' => 'DESC',
                            // 'modified' => 'DESC',
                            'date' => 'DESC'
                        ),
                        'paged' => $current_page,  //current paged
                        'posts_per_page' => get_option('posts_per_page'),  //use left_query counts
                    )));
                    $total_pages = $left_query->max_num_pages;  //total pages
                    // Empty card if null reponsed
                    if(!$left_query->have_posts()){
                        echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.current_slug(true).' </h1></div>';  //<b>'.current_slug(true).'</b> 
                    }
                    while ($left_query->have_posts()):
                        $left_query->the_post();
                        $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                        $post_rights = get_post_meta($post->ID, "post_rights", true);
                ?>
                        <article class="<?php if($post_orderby>1) echo 'topset '; ?>cat-<?php echo $post->ID ?>">
                            <h1>
                                <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                <?php 
                                    if($post_orderby>1) echo '<sup>置顶</sup>';
                                    if($post_rights&&$post_rights!="原创") echo '<sup>'.$post_rights.'</sup>';
                                ?>
                            </h1>
                            <p><?php custom_excerpt(150); ?></p>
                            <div class="info">
                                <span class="classify" id="">
                                    <i class="icom"></i>
                                    <?php 
                                        $cats = get_the_category();
                                        foreach ($cats as $cat){
                                            if($cat->slug!=$temp->slug) echo '<em> '.$cat->name.' </em>';  //leave a blank at the end of em
                                        }
                                    ?>
                                </span>
                                <span class="valine-comment-count icom" data-xid="<?php echo parse_url(get_the_permalink(), PHP_URL_PATH) ?>"><?php echo $post->comment_count; ?></span>
                                <span class="date"><?php the_time("d-m-Y"); ?></span>
                                <span id="slider"></span>
                            </div>
                        </article>
                <?php
                    endwhile;
                    wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
                ?>
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
		</div>
	</div>
</div>
<footer>
    <?php get_footer(); ?>
</footer>
<!--<div class="ajaxloadmvision" ajaxload="ajax/ajax-mvisionloader.html"></div>-->
</div>
<!-- siteJs -->
<?php require_once(TEMPLATEPATH. '/foot.php'); ?>
<!-- inHtmlJs -->
</body></html>