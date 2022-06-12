<?php
/*
 * Template name: 漫游影视（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/acg.css?v=" />
    <?php get_head(); ?>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background:url() center center /cover ">
            <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/acg.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video><!-- bf2_240p_main forest -->
            <div class="counter">
                <?php
                    $curslug = current_slug();
                    $preset = get_template_bind_cat('category-acg.php')->slug;//'acg';
                    $baas = get_option('site_leancloud_switcher');  //use post as category is leancloud unset
                    if(!$baas){
                        $cats = get_categories(meta_query_categories(get_category_by_slug($preset)->term_id, 'ASC', 'seo_order'));
                        if(!empty($cats) && $curslug==$preset){
                            foreach($cats as $the_cat){
                                $cat_slug = $the_cat->slug;  // print_r($the_cat);
                ?>
                                <div class="<?php echo $cat_slug ?>">
                                    <a href="<?php echo $cat_slug ?>" rel="nofollow">
                                        <h2><?php echo $the_cat->count; ?><sup>+</sup></h2>
                                        <p><?php echo $the_cat->name.'/'.$cat_slug; ?></p>
                                    </a>
                                </div>
                <?php
                            }
                        }else{
                            $the_cat = get_category($cat);
                ?>
                            <div class="">
                                <a href="" rel="nofollow">
                                    <h2 class="single"><?php echo $the_cat->count; ?><sup>+</sup></h2>
                                    <p><?php echo $the_cat->name.'/'.$the_cat->slug; ?></p>
                                </a>
                            </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
        <div class="content-all-windows">
            <div class="rcmd-boxes flexboxes">
                <?php
                    if(!$baas){
                        if(!empty($cats) && $curslug==$preset){
                            foreach($cats as $the_cat){
                                acg_posts_query($the_cat, $preset); // // if($cat_slug!=$preset)
                            }
                        }else{
                            acg_posts_query(get_category($cat), $preset);  //get_category_by_slug($curslug)
                        }
                    }
                ?>
                <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                    <?php 
                        the_content();  // the_page_content(current_slug());
                        dual_data_comments();  // query comments from database before include
                    ?>
                </div>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
	</div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
<?php
    if($baas){
?>
        <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/acgn.js"></script>
<?php
    }
?>
</body></html>