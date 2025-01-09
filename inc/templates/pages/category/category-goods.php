<?php
/*
 * Template name: 得物好物
   Template Post Type: page
*/
// acg post query(single)
function get_cat_posts($the_cat, $pre_cat=false, $limit=99){
    $output = '';
    global $post, $lazysrc, $loadimg;
    $cat_slug = get_cat_by_template('goods','slug');
    $cat_single_sw = get_option('site_single_switcher');
    $target = "_blank";
    $rel = "";
    if($cat_single_sw){
        $includes = get_option('site_single_includes');
        $cat_single_sw = in_array($cat_slug, explode(',', $includes));
        if($cat_single_sw){
            $target = "_self";
            $rel = "nofollow";
        }
    }
    $sub_cat = current_slug()!=$pre_cat ? 'subcat' : '';
    $cat_slug = $the_cat->slug;
    // start acg query
    $cat_query = new WP_Query(array_filter(array(
        'cat' => $the_cat->term_id,  //$cat_cat
        'meta_key' => 'post_orderby',
        'orderby' => array(
            'meta_value_num' => 'DESC',
            'date' => 'DESC',
            'modified' => 'DESC'
        ),
        'posts_per_page' => $limit,
    )));
    $output .= '<div class="inbox-clip wow fadeInUp '.$sub_cat.'"><h2 id="'.$cat_slug.'">'.$the_cat->name.'</h2></div><div class="info loadbox flexboxes">'; //'<sup> '.$cat_slug.' </sup>
    while ($cat_query->have_posts()):
        $cat_query->the_post();
        $post_feeling = get_post_meta($post->ID, "post_feeling", true);
        $post_source = get_post_meta($post->ID, "post_source", true);
        $post_rcmd = get_post_meta($post->ID, "post_rcmd", true);
        $post_rating = get_post_meta($post->ID, "post_rating", true);
        $postimg = get_postimg(0, $post->ID, true);
        if($lazysrc!='src'){
            $lazyhold = 'data-src="'.$postimg.'"';
            $postimg = $loadimg;
        }
        $href = $post_source ? $post_source : ($cat_single_sw ? "javascript:;" : get_the_permalink());
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
        if(in_array($cat_slug, $async_array)){
            $cid = $the_cat->term_id;// $cat_name = current_slug(); //$cat_query->query['cat']
            $slug = $the_cat->slug;
            // preset all acg query
            $all_query = new WP_Query(array_filter(array(
                'cat' => $the_cat->term_id,
                'posts_per_page' => -1,
                'fields' => 'ids',
                'no_found_rows' => true,
            )));
            $all_count = $all_query->post_count;
            $posts_count = $cat_query->post_count;  //count($cat_query->posts) //mailto:'.get_bloginfo("admin_email").' 发送邮件，荐你所见
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
        .container {
            width: 50vw;
            height: 200px;
        }
        .container canvas {
            width: 100% !important;
            height: 100% !important;
        }
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
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo get_meta_image($cat, $img_cdn.'/images/1llusion.gif'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload" playsinline -webkit-playsinline></video>
        	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s"><span></span> <?php $cat_desc = get_category($cat)->category_description;echo $cat_desc ? $cat_desc : '...'; ?> </h5>
        </div>
        <div class="content-all-windows">
            <!--<div class="intro-boxes">-->
            <!--    <p><?php echo get_term_meta($cid, 'seo_description', true); ?></p>-->
            <!--</div>-->
            <div class="rcmd-boxes flexboxes">
                <?php
                    $preset = get_cat_by_template(str_replace('.php',"",substr(basename(__FILE__),9)));
                    //acg post list(multi)
                    function the_cat_posts(){
                        global $cat, $cats, $preset, $async_loads;
                        $preslug = $preset->slug;
                        $output = '';
                        if(!empty($cats) && current_slug()==$preslug){
                            // cache db only if not-single sub-page
                            $output_sw = false;
                            if(get_option('site_cache_switcher')){
                                $caches = get_option('site_cache_includes');
                                $temp_slug = get_cat_by_template('goods','slug');
                                $output_sw = in_array($temp_slug, explode(',', $caches));
                                $output = $output_sw ? get_option('site_cat_post_cache') : '';
                            }
                            if(!$output || !$output_sw){
                                foreach($cats as $the_cat) $output .= get_cat_posts($the_cat, $preslug, $async_loads);
                                // wp_kses_post() filted javascript:; href
                                if($output_sw) update_option('site_cat_post_cache', $output); //wp_kses_post($output)
                            }else{
                                // always update wp-nonce if db-cached
                                foreach($cats as $the_cat){
                                    $cat_slug = $the_cat->slug;
                                    $cur_nonce = wp_create_nonce($cat_slug."_posts_ajax_nonce");
                                    // (.*?) 会在匹配到 data-nonce 或 data-cat 属性后停止匹配
                                    $output = preg_replace('/<a(.*)data-nonce=("[^"]*")(.*)data-cat=("'.strtoupper($cat_slug).'")(.*)<\/a>/i', '<a$1data-nonce="'.$cur_nonce.'"$3data-cat=$4$5</a>', $output);
                                }
                            }
                        }else{
                            $output .= get_cat_posts(get_category($cat), $preslug, $async_loads);
                        }
                        // wp_kses_post() caused setupBlurColor() unabled to setup
                        echo $output; //wp_kses_post($output)
                    }
                    the_cat_posts();
                ?>
                <div class="container module"></div>
                <div id="comment_txt">
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
<script type="importmap">
    {
        "imports": {
            "three": "../wp-content/themes/2BLOG-main/js/threejs/node_modules/three/build/three.module.js",
            "three/addons/": "../wp-content/themes/2BLOG-main/js/threejs/node_modules/three/examples/jsm/",
            "three/source/": "../wp-content/themes/2BLOG-main/js/threejs/src/"
        }
    }
</script>
<script type="module">
    import * as THREE from 'three';
    import { three_obj, _utilBasics, _utilEvents, _utilClosure } from 'three/source/three.js';
    const module = new three_obj();
    // console.log(module);
    module.init({
        _scene: {
            antialias: true,
        },
        _camera: {
            far: 1500,
            // fov: 90,
        },
        _lights: {
            hemisphere: {
                intensity: 2,
            },
        },
        _control: {
            enablePan: false,
            // enableRotate: false,
            autoRotateSpeed: 1,
            maxPolarAngle: Math.PI / 2, //0.95 * 
        },
        map: {
            x: 1,
            y: 500,
            z: 0,
        },
        load: {
            container: document.querySelector('.container'),
            module: '<?php echo $src_cdn; ?>/js/threejs/dist/assets/3d/tesla_2018_model_3/scene.gltf', //apple_macbook_pro_16_inch_2021
            texture: {
                cubeImgs: ['px.png','nx.png','ny.png','py.png','pz.png','nz.png'],
            }
        },
        animation: {
        }
    }, (three, target)=> {
        console.log(three);
    });
</script>
<?php
    get_foot();
    if($async_sw&&$use_async){
?>
        <script>
            const rcmd_boxes = document.querySelector(".rcmd-boxes"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(rcmd_boxes, 'load-more', function(t){
                load_ajax_posts(t, 'goods', preset_loads, function(res, load_box){
                    let fragment = document.createDocumentFragment();
                    res.forEach(item=> {
                        let temp = document.createElement("DIV"),
                            extra_str = "",
                            post_rating = item.rating;
                        temp.id = "pid_"+item.id;
                        temp.classList.add("inbox", "flexboxes");
                        temp.innerHTML = `<div class="inbox-headside flexboxes"><span class="author">${item.subtitle}</span><img src="${item.poster}" alt="${item.subtitle}" crossorigin="Anonymous"></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="${item.link || 'javascript:void(0);'}" target="_self">${item.title}</a></h4></span><span class="lowside-description"><p>${item.excerpt}</p></span>${extra_str}</div>`; //<img class="bg" src="${item.poster}">
                        fragment.appendChild(temp);
                        // setup ajax-load images blur-color
                        if(item.poster) {
                            let tempimg = new Image();
                            tempimg.src = item.poster;
                            tempimg.setAttribute('crossorigin','Anonymous');
                            tempimg.onload=()=>setupBlurColor(tempimg, temp);
                        }
                    });
                    load_box.insertBefore(fragment, load_box.lastElementChild); //lastChild
                });
            });
        </script>
<?php
    };
?>
</body></html>