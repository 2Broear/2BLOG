<?php
/*
 * Template name: 漫游影视（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link preload type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/acg.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        .rcmd-boxes .inbox-clip h2{
            padding: 20px 15px;
            letter-spacing: 0;
            text-decoration: underline;
        }
        .rcmd-boxes .inbox-clip.subcat h2{
            padding: 15px 10px;
            margin-bottom: 15px;
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
        /***  decrease 3d layers(cancelable)  ***/
        .rcmd-boxes .info .inbox,
        .rcmd-boxes .info .inbox .inbox-headside img{
            /*transform: none;*/
            /*will-change: initial;*/
        }
        .rcmd-boxes .info .inbox .inbox-headside img{
            /*transform: none;*/
            will-change: auto;
        }
        .inbox-aside .gamespot h3{
            margin: 5px auto 15%;
        }
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings .hexagon:before,
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings .hexagon:after{
            content: none;
        }
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings .hexagon{
            width: 40px;
            height: calc(40px * 1.1547);
            clip-path: polygon(0% 25%, 0% 75%, 50% 100%, 100% 75%, 100% 25%, 50% 0%);
            background: red;
            margin: -12px auto auto;
        }
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings .hexagon h3{
            margin: 12px auto auto;
        }
        .inbox-aside .both .gamespot .range span#before{
            background-color: currentColor;
        }
        .inbox-aside .gamespot .range span#after{
            transform: rotate(270deg);
            z-index: 4;
        }
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings h3{
            color: white;
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
            <video src="<?php echo $video = replace_video_url(get_option('site_acgn_video')); ?>" poster="<?php echo $video ? $video : cat_metabg($cat, custom_cdn_src('img',true).'/images/acg.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <div class="counter">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $acg_temp_slug = get_cat_by_template('acg','slug');
                    $async_array = explode(',', get_option('site_async_includes'));
                    $use_async = $async_sw ? in_array($acg_temp_slug, $async_array) : false;
                    $async_loads = $async_sw&&$use_async ? get_option("site_async_acg", 14) : 999;
    		        $basename = basename(__FILE__);
                    $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9)));
                    $preslug = $preset->slug;
                    $curslug = current_slug();
                    $baas = get_option('site_leancloud_switcher')&&in_array($basename, explode(',', get_option('site_leancloud_category')));  //use post as category is leancloud unset //strpos(get_option('site_leancloud_category'), $basename)!==false
                    $datadance = get_option('site_animated_counting_switcher');
                    if(!$baas){
                        $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                        if(!empty($cats) && $curslug==$preslug){
                            foreach($cats as $the_cat){
                                $cat_slug = $the_cat->slug;  // print_r($the_cat);
                                $cat_count = $the_cat->count;
                ?>
                                <div class="<?php echo $cat_slug ?> blink" data-count="<?php echo $cat_count; ?>">
                                    <a href="<?php echo get_category_link($the_cat->term_id) ?>" rel="nofollow">
                                        <h2><?php echo $datadance ? "0" : $cat_count; ?><sup>+</sup></h2>
                                        <p><?php echo $the_cat->name.'/'.strtoupper($cat_slug); ?></p>
                                    </a>
                                </div>
                <?php
                            }
                        }else{
                            $the_cat = get_category($cat);
                            $cat_count = $the_cat->count;
                            echo "<div class='blink' data-count='$cat_count'><h2 class='single'>$cat_count<sup>+</sup></h2><p>$the_cat->name/$the_cat->slug</p></div>";
                        }
                    }
                ?>
            </div>
        </div>
        <div class="content-all-windows">
            <div class="rcmd-boxes flexboxes">
                <?php
                    if(!$baas){
                        if(!empty($cats) && $curslug==$preslug){
                            foreach($cats as $the_cat){
                                acg_posts_query($the_cat, $preslug, $async_loads); // // if($cat_slug!=$preslug)
                            }
                        }else{
                            acg_posts_query(get_category($cat), $preslug, $async_loads);  //get_category_by_slug($curslug)
                        }
                    }
                ?>
                <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                    <?php 
                        the_page_content(current_slug());  //the_content();
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
<?php
    require_once(TEMPLATEPATH. '/foot.php');
    if($async_sw&&$use_async){
?>
        <script>
            const rcmd_boxes = document.querySelector(".rcmd-boxes"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(rcmd_boxes, 'load-more', function(t){
                load_ajax_posts(t, 'acg', preset_loads, function(each_post, load_box){
                    let each_temp = document.createElement("div");
                    each_temp.id = "pid_"+each_post.id;
                    each_temp.classList.add("inbox", "flexboxes");
                    extra_str = "";
                    post_rating = each_post.rating;
                    if(each_post.rcmd){
                        post_rating = post_rating ? post_rating : '荐';
                        extra_str = `<div class="game-ratings gs">
                                <div class="gamespot" title="GameSpot Ratings">
                                    <div class="range Essential RSBIndex">
                                        <span id="before" style=""></span>
                                        <span id="after" style="transform: rotate(270deg); z-index: 4;"></span>
                                    </div>
                                    <span id="spot">
                                        <h3 style="color: #fff;">${post_rating}</h3>
                                    </span>
                                </div>
                            </div>`;
                    }else{
                        extra_str = post_rating ? '<div class="game-ratings ign"><div class="ign hexagon" title="Recommended"><h3 style="color: #fff;">'+post_rating+'</h3></div></div>' : '';
                    }
                    each_temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${each_post.subtitle}</span><img src="${each_post.poster}" alt="${each_post.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="javascript:;" target="_self">${each_post.title}</a></h4></span><span class="lowside-description"><p>${each_post.excerpt}</p></span>${extra_str}</div>`; //<img class="bg" src="${each_post.poster}">
                    load_box.insertBefore(each_temp, load_box.lastElementChild); //lastChild
                    // setup ajax-load images blur-color
                    let tempimg = new Image();
                        tempimg.src = each_post.poster;
                        tempimg.setAttribute('crossorigin','Anonymous');
                    tempimg.onload=()=>setupBlurColor(tempimg, each_temp);
                });
            });
        </script>
<?php
    };
    if($baas){
        $cdnsrc = custom_cdn_src("src",true);
        echo '<script type="text/javascript" src="'.$cdnsrc.'/js/jquery-1.9.1.min.js"></script><script type="text/javascript" src="'.$cdnsrc.'/js/acgn.js?v='.get_theme_info("Version").'"></script>';
    }
?>
</body></html>