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
	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/notes.css?v=<?php echo get_theme_info('Version'); ?>" />
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
    <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
    <video src="<?php //echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/1llusion.gif'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
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
					<h2> <?php echo get_option('site_nick'); ?> </h2>
					<p> <?php bloginfo('description'); ?> </p>
					<small> <?php echo get_option('site_support'); ?> </small>
					<?php
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
                            $temp = get_cat_by_template(str_replace('.php',"",substr(basename(__FILE__),9)));
                            $cats = get_categories(meta_query_categories($temp->term_id, 'ASC', 'seo_order'));
                            if(!empty($cats)){
                                foreach($cats as $the_cat){
                                    $the_cat_id = $the_cat->term_id;
                                    $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
                                    $level = !empty($catss) ? "seclevel" : "toplevel";
                                    $choosen = $the_cat_id==$cat || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single() ? "choosen" : "";  // current choosen detect
                                    echo '<li class="cat_'.$the_cat_id.' '.$level.'"><a href="'.get_category_link($the_cat).'" id="'.$the_cat->slug.'" class="'.$choosen.'">'.$the_cat->name.'</a>';
                                    if(!empty($catss)){  //expect category id "notes": &&$the_cat_id!=3
                                        echo '<div class="sub-root"><ol>';
                                        foreach($catss as $the_cats){
                                            $the_cats_id = $the_cats->term_id;
                                            $catsss = get_categories(meta_query_categories($the_cats_id, 'ASC', 'seo_order'));
                                            $level = !empty($catsss) ? "trdlevel" : "seclevel";
                                            $choosen = $the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single() ? "choosen 2nd" : "2nd";  // current choosen detect
                                            echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats->category_parent.' '.$level.'"><a href="'.get_category_link($the_cats).'" id="'.$the_cats->slug.'" class="'.$choosen.'"> — '.$the_cats->name.'</a></li>';
                                            // DISABLED 4 LEVLE.
                                            // if(!empty($catsss)){
                                            //     echo '<div class="sub-root"><ol>';
                                            //     foreach($catsss as $the_catss){
                                            //         $the_catss_id = $the_catss->term_id;
                                            //         $level = !empty($catsss) ? "th_level" : "trdlevel";
                                            //         $choosen = $the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single() ? "choosen 3rd" : "3rd";  // current choosen detect
                                            //         echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent.'"><a href="'.get_category_link($the_catss).'" id="'.$the_catss->slug.'" class="'.$choosen.'"> — '.$the_catss->name.'</a></li>';
                                            //     };
                                            //     echo "</ol></div>";
                                            // }
                                        };
                                        echo "</ol></div>";
                                    };
                                    echo "</li>";
                                }
                            }
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
                        <article class="<?php if($post_orderby>1) echo 'topset'; ?> cat-<?php echo $post->ID ?>">
                            <h1>
                                <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                <?php 
                                    if($post_orderby>1) echo '<sup>置顶</sup>';
                                    if($post_rights&&$post_rights!="请选择") echo '<sup>'.$post_rights.'</sup>';
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<!-- inHtmlJs -->
</body></html>