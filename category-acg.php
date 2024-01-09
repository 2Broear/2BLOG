<?php
/*
 * Template name: 漫游影视（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link preload type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/acg.css?v=<?php echo get_theme_info('Version'); ?>" />
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
        .inbox-aside .gamespot h3{
            /*margin: 5px auto 15%;*/
            margin: auto;
            position: inherit;
            top: inherit;
            left: inherit;
            transform: inherit;
        }
        .rcmd-boxes .info .inbox .inbox-aside .game-ratings .hexagon h3{
            margin: 12px auto auto;
        }
        
        .rcmd-boxes .info .inbox .inbox-aside .both .gamespot{
            color: gold;
            /*color: orange;*/
        }
        .rcmd-boxes .info .inbox .inbox-aside .both .gamespot h3{
            color: currentColor;
        }
        .inbox-aside .both .gamespot .range span#before{
            background-color: currentColor;
        }
        .inbox-aside .gamespot .range.RSBIndex:before{
            z-index: inherit;
        }
        .inbox-aside .both .gamespot .range.RSBIndex:before{
            z-index: 4;
        }
        .inbox-aside .both .gamespot .range span#after{
            z-index: -4;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background:url() center center /cover ">
            <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <video src="<?php echo $video = replace_video_url(get_option('site_acgn_video')); ?>" poster="<?php echo $video ? $video : cat_metabg($cat, $img_cdn.'/images/acg.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <div class="counter">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $acg_temp_slug = get_cat_by_template('acg','slug');
                    $async_array = explode(',', get_option('site_async_includes'));
                    $use_async = $async_sw ? in_array($acg_temp_slug, $async_array) : false;
                    $async_loads = $async_sw&&$use_async ? get_option("site_async_acg", 14) : 999;
    		        $basename = basename(__FILE__);
                    $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9)));
                    $baas = get_option('site_leancloud_switcher')&&in_array($basename, explode(',', get_option('site_leancloud_category')));
                    if(!$baas){
                        $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                        the_acg_stats();
                    }
                ?>
            </div>
        </div>
        <div class="content-all-windows">
            <div class="rcmd-boxes flexboxes">
                <?php
                    if(!$baas) the_acg_posts();
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
<script>
    function degreePercentage(range=0, after=false){  //default range 10 score
        if(range<0) return;
        let floats = after ? 10-range : range,
            percent = after ? parseInt(270*(floats/10)) : parseInt(270+180*(floats/5));
        switch(after){
            case true:
                if(percent<=90) return 90;
                break;
            default:
                if(range<=0 || range>=5) return 270+180;
                break;
        }
        return percent;
    }
    // execRotation
    function execRotation(rcmd, ms=450){
        if(!rcmd) return;
        let rcmd_len = rcmd.length;
        if(rcmd_len>=1){
            for(let i=0;i<rcmd_len;i++){
                const range = rcmd[i],
                      score = range.querySelector("#spot h3").innerText;
                new Promise(function(resolve,reject){
                    // setTimeout(()=>{
                    range.querySelector("#before").style.transform = 'rotate('+degreePercentage(score)+'deg)';
                    score>5 ? setTimeout(()=>resolve(), ms) : reject('cancel after');
                    // }, 100);
                }).then(function(res){
                    range.querySelector("#after").style.cssText = 'z-index:4;transform:rotate('+degreePercentage(10-score, true)+'deg)';
                }).catch(function(err){
                    console.log(err)
                });
            }
        }
    }
    const rcmd_range = document.querySelectorAll('.inbox-aside .game-ratings.both');
    execRotation(rcmd_range, 400);
</script>
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
                    let extra_str = "",
                        post_rating = each_post.rating;
                    if(each_post.rcmd){
                        const rcmd_rating = post_rating ? post_rating : '荐',
                              rcmd_title = post_rating ? 'GOLD Recommendation' : 'Personal Recommends',
                              both_class = post_rating ? " both" : "";
                        extra_str = `<div class="game-ratings gs${both_class}"><div class="gamespot" title="${rcmd_title}"><div class="range Essential RSBIndex"><span id="before"></span><span id="after"></span></div><span id="spot"><h3>${rcmd_rating}</h3></span></div></div>`;
                    }else{
                        extra_str = post_rating ? '<div class="game-ratings ign"><div class="ign hexagon" title="IGN High Grades"><h3>'+post_rating+'</h3></div></div>' : '';
                    }
                    each_temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${each_post.subtitle}</span><img src="${each_post.poster}" alt="${each_post.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="javascript:;" target="_self">${each_post.title}</a></h4></span><span class="lowside-description"><p>${each_post.excerpt}</p></span>${extra_str}</div>`; //<img class="bg" src="${each_post.poster}">
                    load_box.insertBefore(each_temp, load_box.lastElementChild); //lastChild
                    execRotation(load_box.querySelectorAll('.inbox-aside .game-ratings.both'));
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
    if($baas) echo '<script type="text/javascript" src="'.$src_cdn.'/js/jquery-1.9.1.min.js"></script><script type="text/javascript" src="'.$src_cdn.'/js/acgn.js?v='.get_theme_info("Version").'"></script>';
?>
</body></html>