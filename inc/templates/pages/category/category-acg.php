<?php
/*
 * Template name: （BaaS）漫游影视
   Template Post Type: page
*/
// acg post query(single)
function get_acg_posts($the_cat, $pre_cat=false, $limit=99){
    $output = '';
    global $post, $lazysrc, $loadimg;
    $acg_slug = get_cat_by_template('acg','slug');
    $acg_single_sw = get_option('site_single_switcher');
    $target = "_blank";
    $rel = "";
    if($acg_single_sw){
        $includes = get_option('site_single_includes');
        $acg_single_sw = in_array($acg_slug, explode(',', $includes));
        if($acg_single_sw){
            $target = "_self";
            $rel = "nofollow";
        }
    }
    $sub_cat = current_slug()!=$pre_cat ? 'subcat' : '';
    $cat_slug = $the_cat->slug;
    // start acg query
    $acg_query = new WP_Query(array_filter(array(
        'cat' => $the_cat->term_id,  //$acg_cat
        'meta_key' => 'post_orderby',
        'orderby' => array(
            'meta_value_num' => 'DESC',
            'date' => 'DESC',
            'modified' => 'DESC'
        ),
        'posts_per_page' => $limit,
    )));
    $output .= '<div class="inbox-clip wow fadeInUp '.$sub_cat.'"><h2 id="'.$cat_slug.'">'.$the_cat->name.'</h2></div><div class="info loadbox flexboxes">'; //'<sup> '.$cat_slug.' </sup>
    while ($acg_query->have_posts()):
        $acg_query->the_post();
        $post_feeling = get_post_meta($post->ID, "post_feeling", true);
        $post_source = get_post_meta($post->ID, "post_source", true);
        $post_rcmd = get_post_meta($post->ID, "post_rcmd", true);
        $post_rating = get_post_meta($post->ID, "post_rating", true);
        $postimg = get_postimg(0, $post->ID, true);
        $lazyhold = '';
        $loadimg = $postimg;
        if($lazysrc!='src'){
            $lazyhold = 'data-src="'.$postimg.'"';
            $postimg = $loadimg;
        }
        $href = $post_source ? $post_source : ($acg_single_sw ? "javascript:;" : get_the_permalink());
        $output .= '<div class="inbox flexboxes" id="pid_'.get_the_ID().'"><div class="inbox-headside flexboxes"><img '.$lazyhold.' src="'.$loadimg.'" alt="'.$post_feeling.'" crossorigin="Anonymous" /><span class="author">'.$post_feeling.'</span></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="'.$href.'" target="'.$target.'" rel="'.$rel.'">'.get_the_title().'</a></h4></span><span class="lowside-description"><p>'.custom_excerpt(66,true).'</p></span>';
        if($post_rcmd){
            $rcmd_title = 'Personal Recommends';
            $rcmd_class = '';
            $rcmd_text = '荐';
            if($post_rating){
                $rcmd_title = 'GOLD Recommendation';
                $rcmd_class = ' both';
                $rcmd_text = $post_rating;
            }
            $output .= '<div class="game-ratings gs'.$rcmd_class.'"><div class="gamespot" title="'.$rcmd_title.'"><div class="range Essential RSBIndex"><span id="before"></span><span id="after"></span></div><span id="spot"><h3>'.$rcmd_text.'</h3></span></div></div>';
        }else{
            if($post_rating) $output .=  '<div class="game-ratings ign"><div class="ign hexagon" title="IGN High Grades"><h3>'.$post_rating.'</h3></div></div>';
        }
        $output .= '</div></div>';
    endwhile;
    wp_reset_query();  // reset wp query incase following code occured query err
    unset($post, $lazysrc, $loadimg);
    // 单独判断当前查询文章数量
    if(get_option('site_async_switcher')){
        $async_array = explode(',', get_option('site_async_includes'));
        if(in_array($acg_slug, $async_array)){
            $cid = $the_cat->term_id;// $cat_name = current_slug(); //$acg_query->query['cat']
            $slug = $the_cat->slug;
            // preset all acg query
            $all_query = new WP_Query(array_filter(array(
                'cat' => $the_cat->term_id,
                'posts_per_page' => -1,
                'fields' => 'ids',
                'no_found_rows' => true,
            )));
            $all_count = $all_query->post_count;
            $posts_count = $acg_query->post_count;  //count($acg_query->posts) //mailto:'.get_bloginfo("admin_email").' 发送邮件，荐你所见
            $disable_statu = $posts_count==$all_count ? ' disabled' : false; //>=
            $output .= '<div class="inbox more flexboxes'.$disable_statu.'"><div class="inbox-more flexboxes"><a class="load-more" href="javascript:;" data-counts="'.$all_count.'" data-load="'.$posts_count.'" data-click="0" data-cid="'.$cid.'" data-nonce="'.wp_create_nonce($slug."_posts_ajax_nonce").'" data-cat="'.strtoupper($slug).'" title="加载更多 '.$the_cat->name.'"></a></div></div>';
            // unset($cid, $slug, $all_count, $posts_count, $disable_statu);
        }
    }
    $output .= '</div>';
    return $output;
};
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link preload type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/acg.css?v=<?php echo get_theme_info(); ?>" />
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
            will-change: initial;
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
        .rcmd-boxes .fade-item .inbox-clip:first-child {
            margin: 55px auto 35px!important;
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
        	<?php 
        	    $video_src = replace_video_url(get_option('site_acgn_video'));
        	    $poster_src = $video_src ? $video_src : get_meta_image($cat, $img_cdn.'/images/acg.jpg');
        	    echo do_shortcode('[custom_video src="' . $video_src . '" poster="' . $poster_src . '"]');
            ?>
            <div class="counter">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $acg_temp_slug = get_cat_by_template('acg','slug');
                    $async_array = explode(',', get_option('site_async_includes'));
                    $use_async = $async_sw ? in_array($acg_temp_slug, $async_array) : false;
                    $async_loads = $async_sw&&$use_async ? get_option("site_async_acg", 14) : 999;
    		        $basename = basename(__FILE__);
                    $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9)));
                    $baas = get_option('site_leancloud_switcher') && strpos(get_option('site_leancloud_category'), $basename)!==false; //in_array($basename, explode(',', get_option('site_leancloud_category')))
                    if(!$baas){
                        $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                        // acg post stats
                        function the_acg_stats(){
                            global $cat, $cats, $preset;
                            $preslug = $preset->slug;
                            $output = '';
                            if(!empty($cats) && current_slug()==$preslug){
                                $output_sw = false;
                                if(get_option('site_cache_switcher')){
                                    $caches = get_option('site_cache_includes');
                                    $temp_slug = get_cat_by_template('acg','slug');
                                    $output_sw = in_array($temp_slug, explode(',', $caches));
                                    // echo $output_sw;
                                    $output = $output_sw ? get_option('site_acg_stats_cache') : '';
                                }
                                if(!$output || !$output_sw){
                                    $datadance = get_option('site_animated_counting_switcher');
                                    foreach($cats as $the_cat){
                                        $cat_slug = $the_cat->slug;
                                        $cat_count = $the_cat->count;
                                        $cat_num = $cat_count;
                                        $dataCls = '';
                                        if($datadance){
                                            $dataCls = ' blink';
                                            $cat_num = '0';
                                        }
                                        $output .= '<div class="'.$cat_slug.$dataCls.'" data-count="'.$cat_count.'"><a href="'.get_category_link($the_cat->term_id).'" rel="nofollow"><h2>'.$cat_num.'<sup>+</sup></h2><p>'.$the_cat->name.'/'.strtoupper($cat_slug).'</p></a></div>';
                                    }
                                    if($output_sw) update_option('site_acg_stats_cache', wp_kses_post($output));
                                }
                            }else{
                                $the_cat = get_category($cat);
                                $cat_count = $the_cat->count;
                                $output .= '<div class="" data-count='.$cat_count.'><h2 class="single">'.$cat_count.'<sup>+</sup></h2><p>'.$the_cat->name.'/'.$the_cat->slug.'</p></div>';
                            }
                            echo wp_kses_post($output);
                        }
                        the_acg_stats();
                    }
                ?>
            </div>
        </div>
        <div class="content-all-windows">
            <div class="rcmd-boxes flexboxes">
                <?php
                    if(!$baas){
                        //acg post list(multi)
                        function the_acg_posts() {
                            global $cat, $cats, $preset, $async_loads;
                            $preslug = $preset->slug;
                            $output = '';
                            if(!empty($cats) && current_slug()==$preslug) {
                                // cache db only if not-single sub-page
                                $output_sw = false;
                                if(get_option('site_cache_switcher')) {
                                    $caches = get_option('site_cache_includes');
                                    $temp_slug = get_cat_by_template('acg','slug');
                                    $output_sw = in_array($temp_slug, explode(',', $caches));
                                    $output = $output_sw ? get_option('site_acg_post_cache') : '';
                                }
                                if(!$output || !$output_sw) {
                                    foreach($cats as $the_cat) {
                                        $output .= '<div class="fade-item">' . get_acg_posts($the_cat, $preslug, $async_loads) . '</div>';
                                    }
                                    // wp_kses_post() filted javascript:; href
                                    if($output_sw) update_option('site_acg_post_cache', $output); //wp_kses_post($output)
                                } else {
                                    // always update wp-nonce if db-cached
                                    foreach($cats as $the_cat){
                                        $cat_slug = $the_cat->slug;
                                        $cur_nonce = wp_create_nonce($cat_slug."_posts_ajax_nonce");
                                        // (.*?) 会在匹配到 data-nonce 或 data-cat 属性后停止匹配
                                        $output = preg_replace('/<a(.*)data-nonce=("[^"]*")(.*)data-cat=("'.strtoupper($cat_slug).'")(.*)<\/a>/i', '<a$1data-nonce="'.$cur_nonce.'"$3data-cat=$4$5</a>', $output);
                                    }
                                }
                            } else {
                                $output .= get_acg_posts(get_category($cat), $preslug, $async_loads);
                            }
                            // wp_kses_post() caused setupBlurColor() unabled to setup
                            echo $output; //wp_kses_post($output)
                        }
                        the_acg_posts();
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
<script>
    // easeCounter(0, 100, 1, 0.5, (from, delay)=> {
    //     console.log(from, delay)
    // })
    
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
    get_foot();
    if($async_sw&&$use_async){
?>
        <script>
            const rcmd_boxes = document.querySelector(".rcmd-boxes"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(rcmd_boxes, 'load-more', function(t){
                load_ajax_posts(t, 'acg', preset_loads, function(res, load_box){
                    let fragment = document.createDocumentFragment();
                    res.forEach(item=> {
                        let temp = document.createElement("DIV"),
                            extra_str = "",
                            post_rating = item.rating;
                        temp.id = "pid_"+item.id;
                        temp.classList.add("inbox", "flexboxes");
                        if(item.rcmd){
                            const both_class = post_rating ? " both" : "",
                                  rcmd_title = post_rating ? 'GOLD Recommendation' : 'Personal Recommends'
                                  rcmd_rating = post_rating ? post_rating : '荐';
                            extra_str = `<div class="game-ratings gs${both_class}"><div class="gamespot" title="${rcmd_title}"><div class="range Essential RSBIndex"><span id="before"></span><span id="after"></span></div><span id="spot"><h3>${rcmd_rating}</h3></span></div></div>`;
                        }else{
                            extra_str = post_rating ? '<div class="game-ratings ign"><div class="ign hexagon" title="IGN High Grades"><h3>'+post_rating+'</h3></div></div>' : '';
                        }
                        temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${item.subtitle}</span><img src="${item.poster}" alt="${item.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="${item.link || 'javascript:void(0);'}" target="_self">${item.title}</a></h4></span><span class="lowside-description"><p>${item.excerpt}</p></span>${extra_str}</div>`; //<img class="bg" src="${item.poster}">
                        fragment.appendChild(temp);
                        // setup ajax-load images blur-color
                        if(item.poster) {
                            let tempimg = new Image();
                            tempimg.src = item.poster;
                            tempimg.setAttribute('crossorigin','Anonymous');
                            tempimg.onload=()=>setupBlurColor(tempimg, temp);
                            // setupBlurColor(item.poster, temp);
                        }
                    });
                    load_box.insertBefore(fragment, load_box.lastElementChild); //lastChild
                    execRotation(load_box.querySelectorAll('.inbox-aside .game-ratings.both'));
                });
            });
        </script>
<?php
    };
    if($baas) echo '<script type="text/javascript" src="'.$src_cdn.'/js/jquery-1.9.1.min.js"></script><script type="text/javascript" src="'.$src_cdn.'/js/acgn.js?v='.get_theme_info("Version").'"></script>';
?>
</body></html>