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
        .rcmd-boxes .info .inbox .inbox-aside span{
            max-height: 75%;
        }
        .rcmd-boxes .info .inbox{
            /*transition-property: background-color, transform;*/
        }
    </style>
    <script>
        function getAverageRGB(imgEl){var blockSize=5,defaultRGB={r:255,g:255,b:255},canvas=document.createElement('canvas'),context=canvas.getContext&&canvas.getContext('2d'),data,width,height,i=-4,length,rgb={r:0,g:0,b:0},count=0;if(!context){return defaultRGB}height=canvas.height=imgEl.naturalHeight||imgEl.offsetHeight||imgEl.height;width=canvas.width=imgEl.naturalWidth||imgEl.offsetWidth||imgEl.width;context.drawImage(imgEl,0,0);try{data=context.getImageData(0,0,width,height)}catch(e){return defaultRGB}length=data.data.length;while((i+=blockSize*4)<length){++count;rgb.r+=data.data[i];rgb.g+=data.data[i+1];rgb.b+=data.data[i+2]}rgb.r=~~(rgb.r/count);rgb.g=~~(rgb.g/count);rgb.b=~~(rgb.b/count);return rgb}
    </script>
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
                    if(!$baas){
                        $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                        if(!empty($cats) && $curslug==$preslug){
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
<script>
    // setup images rgb-color bg-cover
    window.onload=function(){
        const inboxes = document.querySelectorAll('.rcmd-boxes .info .inbox');
        for(let i=0;i<inboxes.length;i++){
            let inbox = inboxes[i],
                tempimg = document.createElement('img'),
                poster = inbox.querySelector('img');
            if(poster){
                tempimg.src = poster.dataset.src;
                tempimg.setAttribute('crossorigin','Anonymous');
                let rgb = getAverageRGB(tempimg),
                    rgba = rgb['r']+' '+rgb['g']+' '+rgb['b']+' / 50%';
                // console.log(poster, rgba, tempimg);
                inbox.setAttribute('style','background-color: rgb('+rgba+')');
            }
        }
    }
    <?php
        if(get_option('site_animated_counting_switcher')){
            echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h2", "<sup>+</sup>");';
        }
    ?>
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
                            cid = parseInt(_this.dataset.cid),//getAttribute('data-cid')),
                            loads = parseInt(_this.dataset.load),//getAttribute("data-load")),  // $posts_count in acg_posts_query() function
                            click_count = parseInt(_this.dataset.count),//getAttribute('data-count')),
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
                                    // console.log(each_post)
                                    each_temp.id = "pid_"+each_post.id;
                                    each_temp.classList.add("inbox", "flexboxes");
                                    each_temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${each_post.subtitle}</span><img src="${each_post.poster}" alt="${each_post.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="javascript:;" target="_self">${each_post.title}</a></h4></span><span class="lowside-description"><p>${each_post.excerpt}</p></span></div>`; //<img class="bg" src="${each_post.poster}">
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
    };
?>
</body></html>