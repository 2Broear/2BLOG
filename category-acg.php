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
        .rcmd-boxes .info .inbox .inbox-more.disabled a:before,
        .rcmd-boxes .info .inbox .inbox-more.disabled a:after{
            /*content: attr(data-load)' 'attr(data-cat)'s ';*/
            content: attr(data-load)' 'attr(data-cat)'S LOAD ';
            /*content: attr(data-load)' 'attr(data-cat)'S NO ';*/
        }
        .rcmd-boxes .info .inbox .inbox-more.disabled a:after{
            /*content: "LOAD";*/
            content: "DONE";
        }
        .rcmd-boxes .info .inbox .inbox-more.disabled{
            border-width: 0px;
        }
        .rcmd-boxes .info .inbox .inbox-more a{
            color: inherit;
            padding: 15% 15px;
            box-sizing: border-box;
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
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/acg.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video><!-- bf2_240p_main forest -->
            <div class="counter">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $async_loads = $async_sw ? get_option("site_async_acg", 14) : 999;
                    $filename = basename(__FILE__);
                    $curslug = current_slug();
                    $preset = get_template_bind_cat($filename)->slug;//'acg';
                    $baas = get_option('site_leancloud_switcher')&&strpos(get_option('site_leancloud_category'), $filename)!==false;  //use post as category is leancloud unset
                    if(!$baas){
                        $cats = get_categories(meta_query_categories(get_category_by_slug($preset)->term_id, 'ASC', 'seo_order'));
                        if(!empty($cats) && $curslug==$preset){
                            foreach($cats as $the_cat){
                                $cat_slug = $the_cat->slug;  // print_r($the_cat);
                                $cat_count = $the_cat->count;
                ?>
                                <div class="<?php echo $cat_slug ?>" data-res="<?php echo $cat_count; ?>">
                                    <a href="<?php echo get_category_link($the_cat->term_id) ?>" rel="nofollow">
                                        <h2><?php echo $cat_count; ?><sup>+</sup></h2>
                                        <p><?php echo $the_cat->name.'/'.strtoupper($cat_slug); ?></p>
                                    </a>
                                </div>
                <?php
                            }
                        }else{
                            $the_cat = get_category($cat);
                            $cat_count = $the_cat->count;
                ?>
                            <div class="" data-res="<?php echo $cat_count; ?>">
                                <!--<a href="javascript:;" rel="nofollow">-->
                                    <h2 class="single"><?php echo $cat_count; ?><sup>+</sup></h2>
                                    <p><?php echo $the_cat->name.'/'.$the_cat->slug; ?></p>
                                <!--</a>-->
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
                                acg_posts_query($the_cat, $preset, $async_loads); // // if($cat_slug!=$preset)
                            }
                        }else{
                            acg_posts_query(get_category($cat), $preset, $async_loads);  //get_category_by_slug($curslug)
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
<script>
    const counterList = document.querySelectorAll('.win-top .counter div');
    function insideLoop(counter,init,limit,i){
        let times = -limit;
        var inOrder = function(){
                clearInterval(noOrder);
                init<=limit ? counter.innerHTML=`${init++}<sup>+</sup>` : clearInterval(noOrder);
                times>=0 ? times=0 : times++;
                console.log(init+times);
                noOrder = setInterval(inOrder, init+times);
            };
        var noOrder = setInterval(inOrder, 0);
    };
    for(let i=0;i<counterList.length;i++){
        let count = parseInt(counterList[i].getAttribute('data-res')),
            counter = counterList[i].querySelector('h2'),
            limit = parseInt(counter.innerText);
        insideLoop(counter,0,limit,i);
        // var noOrder = setInterval(function(){
        //     init<=limit ? counter.innerHTML = `${init++}<sup>+</sup>` : clearInterval(noOrder);
        // }, 10*i);
    }
</script>
<?php
    if($async_sw){
?>
        <script>
            const rcmd_loadbox = document.querySelector(".rcmd-boxes"),
                  preset_loads = <?php echo $async_loads; ?>;
            rcmd_loadbox.onclick=(e)=>{
                var e = e || window.event,
                    t = e.target || e.srcElement;
                while(t!=rcmd_loadbox){
                    if(t.id=="more"){
                        let _this = t,
                            cid = parseInt(_this.getAttribute('data-cid')),
                            loads = parseInt(_this.getAttribute("data-load")),  // $posts_count in acg_posts_query() function
                            click_count = parseInt(_this.getAttribute('data-count')),
                            load_box = _this.parentNode.parentNode.parentNode;
                        click_count++;
                        // console.log(load_box);
                        _this.setAttribute('data-count', click_count);
                        _this.innerText = "NOW LOADING..";
                        send_ajax_request("post", "<?php echo admin_url('admin-ajax.php'); ?>", 
                            parse_ajax_parameter({
                                "action": "ajaxCallAcg",
                                "cid": cid,
                                "limit": preset_loads,
                                "offset": preset_loads*click_count,
                            }, true), function(res){
                                var posts_array = JSON.parse(res),
                                    posts_count = posts_array.length;
                                console.log(posts_array);  //response
                                posts_count<=0 ? _this.parentNode.classList.add("disabled") : _this.setAttribute('data-load', loads+posts_count);
                                for(let i=0;i<posts_count;i++){
                                    let each_post = posts_array[i],
                                        each_temp = document.createElement("div");
                                    each_temp.id = "pid_"+each_post.id;
                                    each_temp.classList.add("inbox", "flexboxes");
                                    each_temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${each_post.subtitle}</span><img class="bg" src="${each_post.poster}"><img src="${each_post.poster}"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="javascript:;" target="_self">${each_post.title}</a></h4></span><span class="lowside-description"><p>${each_post.excerpt}</p></span></div>`;
                                    // console.log(each_post)
                                    load_box.insertBefore(each_temp, load_box.lastChild);
                                };
                                _this.innerText = "";
                            }
                        );
                        break;
                    }else{
                        t = t.parentNode;
                    }
                }
            }
        </script>
<?php
    };
    if($baas){
?>
        <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/acgn.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
    }
?>
</body></html>