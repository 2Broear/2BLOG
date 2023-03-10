<?php
/*
 * Template name: 漫游影视（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/acg.css?v=<?php echo get_theme_info('Version'); ?>" />
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
                    $async_loads = $async_sw ? get_option("site_async_acg", 14) : 999;
    		        $basename = basename(__FILE__);
                    $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9)));
                    $preslug = $preset->slug;
                    $curslug = current_slug();
                    $baas = get_option('site_leancloud_switcher')&&strpos(get_option('site_leancloud_category'), $basename)!==false;  //use post as category is leancloud unset
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
    if($datadance){
?>
        <script>
            dataDancing(document.querySelectorAll(".win-top .counter div"), "h2", 0, 10, "<sup>+</sup>");
        </script>
<?php
    }
    if($async_sw){
?>
        <script>
            const rcmd_boxes = document.querySelector(".rcmd-boxes"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(rcmd_boxes, 'load-more', function(t){
                let tp = t.parentNode,
                    cid = parseInt(t.dataset.cid),
                    loads = parseInt(t.dataset.load),
                    counts = parseInt(t.dataset.counts),
                    clicks = parseInt(t.dataset.click);
                if(loads>=counts){
                    tp.classList.add("disabled");
                    return;
                }
                clicks++;
                t.innerText = "Loading..";
                t.setAttribute('data-click', clicks);
                send_ajax_request("post", "<?php echo admin_url('admin-ajax.php'); ?>", 
                    parse_ajax_parameter({
                        "action": "ajaxCallAcg",
                        "cid": cid,
                        "limit": preset_loads,
                        "offset": preset_loads*clicks,
                        _ajax_nonce: t.dataset.nonce,
                    }, true), function(res){
                        var posts_array = JSON.parse(res),
                            load_box = tp.parentNode.parentNode,
                            posts_count = posts_array.length,
                            loads_count = loads+posts_count;
                        t.innerText = "";
                        loads_count>=counts ? t.setAttribute('data-load', counts) :  t.setAttribute('data-load', loads_count);  // update current loaded(limit judge)
                        for(let i=0;i<posts_count;i++){
                            let each_post = posts_array[i],
                                each_temp = document.createElement("div");
                            each_temp.id = "pid_"+each_post.id;
                            each_temp.classList.add("inbox", "flexboxes");
                            each_temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${each_post.subtitle}</span><img src="${each_post.poster}" alt="${each_post.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="javascript:;" target="_self">${each_post.title}</a></h4></span><span class="lowside-description"><p>${each_post.excerpt}</p></span></div>`; //<img class="bg" src="${each_post.poster}">
                            load_box.insertBefore(each_temp, load_box.lastChild);
                            // setup ajax-load images blur-color
                            let tempimg = new Image();
                                tempimg.src = each_post.poster;
                                tempimg.setAttribute('crossorigin','Anonymous');
                            tempimg.onload=()=>setupBlurColor(tempimg, each_temp);
                        };
                        // compare updated load counts
                        if(parseInt(t.dataset.load)>=counts){
                            tp.classList.add("disabled");
                        }
                    }
                );
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