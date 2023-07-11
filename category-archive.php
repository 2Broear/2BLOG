<?php
/*
 * Template name: 归档模板
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/archive.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        .archive-tree ul:hover{
            /*max-height: 368px;*/
        }
        @keyframes blinker {
            0% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
            100% {
                opacity: 1;
            }
        }
        div.blink{
            animation: blinker .5s infinite alternate ease;
            -webkit-animation: blinker .5s infinite alternate ease;
        }
    </style>
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
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/archive.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <div class="counter">
                <?php the_archive_stats(); ?>
            </div>
        </div>
        <div class="archive-tree">
            <div class="cs-tree">
                <?php 
                    $curYear = gmdate('Y', time() + 3600*8);
                    the_archive_contributions(); 
                ?>
            </div>
            <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;" style="float:right;">
                <option value=""><?php esc_attr( _e( 'monthly overview', 'textdomain' ) ); ?></option> 
                <?php 
                    wp_get_archives(array(
                        'type' => 'monthly',
                        'format' => 'option',
                        'show_post_count' => 1,
                        'limit' => '',
                        // 'year' => '2018',
                		// 'monthnum' => '',
                		// 'day' => '',
                		// 'w' => '',
                    )); 
                ?>
            </select>
            <?php
                $async_sw = get_option('site_async_switcher');
                $archive_temp_slug = get_cat_by_template('archive','slug');
                $async_array = explode(',', get_option('site_async_includes'));
                $use_async = $async_sw ? in_array($archive_temp_slug, $async_array) : false;
                $async_loads = $async_sw&&$use_async ? get_option("site_async_archive", 99) : 999;
                the_archive_lists();
            ?>
            <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                <?php 
                    the_content();  // the_page_content(current_slug());
                    dual_data_comments();  // query comments from database before include
                ?>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
	</div>
<!-- siteJs -->
<?php
    require_once(TEMPLATEPATH. '/foot.php');
    if($async_sw&&$use_async){
?>
        <script>
            const archive_tree = document.querySelector(".archive-tree"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(archive_tree, 'call', function(t){
                load_ajax_posts(t, 'archive', preset_loads, function(each_post, load_box, last_offset){
                    let each_temp = document.createElement("LI");
                    each_temp.innerHTML = `${each_post.date}<a class="link${each_post.mark}" href="${each_post.link}" target="_blank">${each_post.title}<sup>${each_post.cat}</sup></a>`;
                    load_box.appendChild(each_temp);
                    // scrollTo lastest (archive only)
                    load_box.scrollTo(0, last_offset);
                });
            });
        </script>
<?php
    }
?>
</body></html>