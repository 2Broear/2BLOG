<!DOCTYPE html>
<?php
    // wp自定义（含置顶无分页）查询函数
    function the_recent_posts($cid=0, $specific_link=false, $detail=false, $limit=null, $random=false){
        // cache db only if not-single sub-page
        $output = '';
        $output_sw = false;
        if(get_option('site_cache_switcher')) {
            $temp = get_category($cid);
            if (isset($temp->slug)) {
                $temp_slug = $temp->slug;
                $cache = 'site_recent_'.$temp_slug.'_cache';
                $caches = get_option('site_cache_includes');
                $output_sw = in_array($temp_slug, explode(',', $caches));
                $output = $output_sw ? get_option($cache) : '';
            }
        }
        if(!$output || !$output_sw){
            // $output = get_recent_posts($cid, $specific_link, $detail, $limit, $random);
            global $post;
            $acg_slug = get_cat_by_template('acg','slug');
            $acg_single_sw = get_option('site_single_switcher');
            if($acg_single_sw){
                $includes = get_option('site_single_includes');
                $acg_single_sw = in_array($acg_slug, explode(',', $includes));
            }
            $orderby = $random ? 'rand' : array(
                'date' => 'DESC',
                'meta_value_num' => 'DESC',
                'modified' => 'DESC',
            );
            $limit = $limit ? $limit : get_option('posts_per_page');
            $query_array = $cid ? array('cat' => $cid, 'meta_key' => 'post_orderby', 'posts_per_page' => $limit, 'orderby' => $orderby) : array('cat' => $cid, 'posts_per_page' => $limit, 'order' => 'DESC', 'orderby' => $orderby);
            $left_query = new WP_Query(array_filter($query_array));
            while ($left_query->have_posts()):
                $left_query->the_post();
                $topset = get_post_meta($post->ID, "post_orderby", true)>1 ? 'topset' : false;
                $title = $detail ? trim(get_the_title()).' -（'.get_post_meta($post->ID, "post_feeling", true).'）<sup>'.$post->post_date.'</sup>' : trim(get_the_title());
                // print_r(get_category($cid)->parent);
                $cid = !get_category($cid)->errors ? $cid : 1; //php8
                $par_cid = get_category($cid)->parent;
                $par_slug = $par_cid!=0&&get_category($par_cid)->slug!='/' ? get_category($par_cid)->slug : get_category($cid)->slug;
                $post_cat = get_the_category($post->ID);
                $loc_id = $par_slug==$acg_slug ? ($post_cat[0]->parent!=0 ? $post_cat[0]->slug : $post_cat[1]->slug) : 'pid_'.get_the_ID();
                $pre_link = $specific_link || !$acg_single_sw ? '<a href="'.get_the_permalink().'" title="'.$title.'" target="_blank">' : '<a href="'.get_category_link($cid).'#'.$loc_id.'" target="_self" rel="nofollow">';
                $output .= '<li class="'.$topset.'">'.$pre_link . $title . '</a></li>';
            endwhile;
            wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
            if($output_sw) update_option($cache, $output); //wp_kses_post($output) caused parse issue
        }
        echo $output;
    }
?>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/main.min.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        #banner-prev,#banner-next{
            cursor: pointer;
            background:url("<?php echo $img_cdn; ?>/images/css_sprites.png") no-repeat;
        }
        .banner .banner-inside ul{cursor:grab;}
        .acg_window-content-inside_right::before{
            opacity: .5;
            content: none;
        }
        .main-bottom-ta{
            display: block;
        }
        .Fresh-ImgBoxs span a{
            font-family: cursive,monospace,serif,fangsong;
            font-size: 4.5em;
            padding: 10% 0;
        }
        .Fresh-ImgBoxs span i{
            font-size: 7rem;
            right: -15%;
        }
        @keyframes colorfull{
            0%{
                filter: hue-rotate(0deg);
            }
            100%{
                filter: hue-rotate(360deg);
            }
        }
        .banner .banner-inside {
            height: 250px;
        }
        .banner .banner-inside ul{
            max-height: 268px;
            /*filter: opacity(0.15);*/
            /*filter: invert(1);*/
            /*animation: colorfull ease 3s .5s;*/
        }
        <?php
            $baas = get_option('site_leancloud_switcher');
            $weblog = get_option('site_techside_switcher');
            if($weblog) echo '.special-display{width:30.5%;/*width:32%*/}';
        ?>
        body.dark{
            --mirror-end: var(--preset-2b);
        }
        .Fresh-ImgBoxs span:first-child a{
            color: #4285f4;
            background: linear-gradient(-90deg, rgb(67 133 245 / 99%) 0, var(--mirror-end));
        }
        .Fresh-ImgBoxs span:nth-child(2) a{
            color: #ea4335;
            background: linear-gradient(-90deg, rgb(234 69 55 / 99%) 0, var(--mirror-end));
        }
        .Fresh-ImgBoxs span:nth-child(3) a{
            color: #fbbc05;
            background: linear-gradient(-90deg, rgb(251 189 7 / 99%) 0, var(--mirror-end));
        }
        .Fresh-ImgBoxs span:last-child a{
            color: #34a853;
            background: linear-gradient(-90deg, rgb(53 169 83 / 99%) 0, var(--mirror-end));
        }
        #special-img{
            position: absolute;
            top: 0;
            /*left: 15px;*/
            /*left: 0;*/
            /*right: auto;*/
            right: 0;
            height: 100%;
            /*animation: dancing ease-in-out 2.5s 0s infinite;*/
            /*animation-fill-mode: both;*/
            min-width: 256px;
            max-width: 538px;
        }
        #special-img video{
            height: 100%;
            width: 100%;
            border-radius: inherit;
            object-fit: cover;
        }
        .resource-windows div ul{
            width: 100%;
        }
        .resource-windows div:last-of-type{
            margin-right: auto;
        }
        .weBlog-Description .weBlog-Description-inside-content span {
            font-family: math;
            margin-bottom: 5px;
            /*letter-spacing: 5px;*/
            /*font-weight: 100;*/
        }
        .weBlog-Description .weBlog-Description-inside-content span p{
            display: none;
        }
        video {
            width: 100%;
            object-fit: cover;
        }
        .banner {
            max-height:250px;
            /*margin-bottom: 15px;*/
        }
        .banner video {
            height: 100%;
        }
        .main-top-part:first-child {
            /*margin: 15px auto auto;*/
        }
        .weBlog-Description .weBlog-Description-inside-content span small strong {
            background: linear-gradient(to right, var(--theme-color), transparent);
            -webkit-background-clip: text;
            color: transparent;
            font-size: xx-large;
        }
        @media screen and (max-width: 960px) {
            .banner .banner-inside {
                max-height: 150px;
            }
        }
        .Fresh-ImgBoxs span a b {
            filter: url(#x);
        }
        .recommendation #recommend-inside span.content-tail aside.personal_stand p {
            opacity: .55;
        }
        
        .acg_window-content li.acg_window-content-inside_left {
            /*display: none;*/
        }
        .acg_window-content li.acg_window-content-inside_right {
            /*width: auto;*/
            /*max-width: 88%;*/
            /*position: relative;*/
            margin: 0 auto;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
<div class="main-content">
<header>
    <nav id="tipson" class="ajaxloadon">
        <?php get_header(); ?>
    </nav>
</header>
<!-- 顶 -->
<?php get_inform(); ?>
<div class="main-top-allpart flexboxes">
    <div class="main-top-part flexboxes" <?php if(!get_option("site_inform_switcher")) echo 'style="margin-top:15px"'; ?>>
        <!-- 左 -->
        <div class="weBlog-banner flexboxes wow fadeInUp" data-wow-delay="0.1s">
            <div class="weBlog-Description" style="margin-top:0;">
                <div class="weBlog-Description-inside">
                    <div class="weBlog-Description-inside-content">
                        <span>
                            <small style="filter: url(#x);"><b style="font-size:xx-large;">👋</b><strong> 你好，hoooola!!! <?php //echo get_option('site_nick', get_bloginfo('name')); ?> </strong></small>
                            <p> 「<?php bloginfo('description') ?>」 </p>
                        </span>
                    </div>
                </div>
            </div>
            <div class="banner">
                <div class="banner-inside">
                    <iframe id="panorama" frameborder="no" style="/*min-height: 250px;*/"></iframe>
                    <!--<ul>-->
                        <?php
                            // $banner_array = explode(',',get_option('site_banner_array',''));
                            // $banner_array_count = count($banner_array);
                            // for($i=0;$i<$banner_array_count;$i++){
                            //     $banner_url = trim($banner_array[$i]);
                            //     if($banner_url) echo '<li style="background: url() no-repeat center center /cover;">' . do_shortcode('[custom_video src="' . $banner_url . '" poster]') . '</li>'; //'.$banner_url.'
                            // }
                        ?>
                    <!--</ul>-->
                    <div class="switcher">
                        <span id="banner-prev" class="banner_prew"></span>
                        <span id="banner-next" class="banner_next"></span>
                    </div>
                    <div class="dots"></div>
                </div>
            </div>
        </div>
        <!-- 右 -->
        <div class="recommendation wow fadeInUp hfeed" data-wow-delay="0.2s">
            <?php
                $rcmd_cat = get_option('site_rcmdside_cid');
                $rcmd_arr = array(
                    'cat' => $rcmd_cat,
                    'posts_per_page' => 1,
                    'meta_key' => 'post_orderby',
                    'orderby' => array(
                        'meta_value_num' => 'DESC',
                        'date' => 'DESC',
                        'modified' => 'DESC',
                    )
                );
                if (!$rcmd_cat) {
                    unset($rcmd_arr['meta_key']);
                    $rcmd_arr['cat'] = 1;
                    $rcmd_arr['orderby'] = array(
                        'date' => 'DESC',
                        'modified' => 'DESC',
                    );
                }
                $rcmd_query = new WP_Query(array_filter($rcmd_arr));
                while ($rcmd_query->have_posts()):
                    $rcmd_query->the_post();
                    $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                    $post_orderby = get_post_meta($post->ID, "post_orderby", true);
            ?>
                    <article class="<?php if($post_orderby>1) echo 'topset'; ?> article" id="recommend-inside">
                      <div class="recommend-newsImg">
                        <div>
                          <a href="<?php the_permalink() ?>" aria-label="bg">
                            <span id="lowerbg" style="background:url('<?php echo get_postimg(0,$post->ID,true); ?>') center 40% no-repeat;background-size:cover;"></span>
                          </a>
                          <a href="<?php the_permalink() ?>" id="rel" rel="bookmark" target="_blank">
                            <b><?php the_title() ?></b>
                          </a>
                        </div>
                      </div>
                      <div class="recommend-newsContent">
                        <span class="content-core entry-content">
                            <p><?php echo wp_trim_words(get_the_excerpt(), 250); //custom_excerpt(150); ?></p>
                        </span>
                        <span class="content-tail">
                          <aside class="personal_stand">
                            <p><?php echo $post_feeling ? $post_feeling : " ...... "; ?></p>
                          </aside>
                        </span>
                      </div>
                    </article>
            <?php
                endwhile;
                wp_reset_query();  // reset wp query incase following code occured query err
            ?>
        </div>
    </div>
</div>
    <div class="main-top-part pixiv flexboxes wow fadeInUp" data-wow-delay="0.2s">
        <div class="Fresh-ImgBoxs flexboxes">
          <?php
              $cardnav_array = explode(';', get_option('site_cardnav_array'));
              $cardnav_array_count = count($cardnav_array);
              for($i=0;$i<$cardnav_array_count;$i++){
                  $each_card = explode('/', $cardnav_array[$i]);
                  if($each_card[0]){
                      $card_slug = trim($each_card[0]);
                      $card_nick = trim($each_card[1]);
                      $card_term = get_category_by_slug($card_slug) ? get_category_by_slug($card_slug) : get_category(1);  // 1 for UNCATEGORIZED
                      if(!$card_nick) $card_nick=get_category_by_slug($card_slug)->name;  //incase non diy nick
                      if($card_slug){  //incase end with ";"
                        echo '<span class="'.$card_slug.'"><a href="'.get_category_link($card_term->term_id).'"> <b>' . $card_nick . '</b><i class="icom icon-'.$card_slug.'"></i></a></span>';
                      }
                  }
              }
          ?>
        </div>
    </div>
    <div class="main-top-part flexboxes wow fadeInUp" data-wow-delay="0.3s">
        <!-- 右 -->
        <div class="resource-windows flexboxes">
            <?php
                $load_arr = [get_cat_by_template('news'), get_cat_by_template('notes')];
                if($weblog)  array_push($load_arr, get_cat_by_template('weblog'));
                $load_arr_count = count($load_arr);
                $site_per_posts = get_option('site_per_posts');
                $rand_count = $site_per_posts; //mt_rand($site_per_posts, $site_per_posts+1);
                for($i=0;$i<$load_arr_count;$i++){
                    if (empty($load_arr[$i])) continue;
            ?>
                    <div id="news-window">
                        <span class='resource-windows-top'>
                            <span class='resource-windows-top_inside'></span>
                            <h3><?php echo $load_arr[$i]->name; ?></h3>
                        </span>
                        <ul class="news-list" id="mainNews">
                            <?php 
                                the_recent_posts($load_arr[$i]->term_id, true, false, $rand_count); //6-$i
                            ?>
                        </ul>
                    </div>
            <?php
                }
            ?>
            <div id="news-window">
                <span class='resource-windows-top'>
                    <span class='resource-windows-top_inside'></span>
                    <h3>随机 · 链</h3>
                </span>
                <ul class="news-list special_display" id="mainNews">
                    <?php 
                        if($baas && strpos(get_option('site_leancloud_category'), 'category-2bfriends.php')!==false){
                    ?>
                            <script type="text/javascript"> //addAscending createdAt
                                new AV.Query("link").addDescending("updatedAt").equalTo('mark','friends').find().then(result=>{
                                    for (let i=0,resLen=<?php echo $site_per_posts; ?>; i<resLen;i++) {
                                        document.querySelector(".special_display").innerHTML += `<li><a href="${result[i].attributes.link}" class="inbox-aside" target="_blank" rel="randlink">${result[i].attributes.name}</a></li>`;
                                    };
                                })
                            </script>
                    <?php
                        }else{
                            $ranklink = get_site_bookmarks(get_option('site_list_links_category'), 'rand', 'ASC', $rand_count);
                            $ranklinks = get_site_links($ranklink, 'list'); //, true
                            echo empty($ranklink) ? '<li><a href="">' . $ranklinks . '</a></li>' : $ranklinks;
                        }
                    ?>
                </ul>
            </div>
        </div>
        <!-- 左 -->
        <!--<div class="special-display">-->
        <!--    <ul class="flexboxes">-->
        <!--        <li id="special-img" style="background: url() center /cover;">-->
        <!--            <video src="<?php //echo get_option('site_list_bg'); ?>" poster="<?php //echo get_option('site_list_bg'); ?>" preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>-->
        <!--        </li>-->
        <!--    </ul>-->
        <!--</div>-->
    </div>
    <!--<div class="main-middle-allpart"></div>-->
    <div class="main-bottom-allpart">
        <!-- 左文窗 ，右图-->
        <div class="main-bottom-ta">
        <?php
            if(!$weblog){
        ?>
            <div id="tech-acg-inside_tech" class="flexboxes wow fadeInUp" data-wow-delay="0.15s">
                <span id="tech_window" style="width:100%">
                    <div class="newsBox-supTitle flexboxes" id="tech_window-top">
                        <span class="newsBox-supTitle-iDescription" id="icon-technology">
                            <em>LOG</em><i class="icom icon-weblog"></i>
                        </span>
                        <h2><?php echo $blog_temp = get_cat_by_template('weblog');if (!empty($blog_temp)) echo $blog_temp->name; ?></h2>
                    </div>
                    <ul class="tech_window-content">
                        <?php 
                            $query_cid = get_option('site_techside_cid');
                            if($baas&&strpos(get_option('site_leancloud_category'), 'category-weblog.php')!==false){
                                // leancloud avos（标准li结构）查询
                                function avos_posts_query($cid=0, $els=null){
                                    $slug = get_category($cid)->slug;
                            ?>
                                    <script type="text/javascript">
                                        new AV.Query("<?php echo $slug; ?>").addDescending("createdAt").limit(<?php echo get_option('site_per_posts', get_option('posts_per_page')); ?>).find().then(result=>{
                                            for (let i=0,resLen=result.length; i<resLen;i++) {
                                                let res = result[i],
                                                    title = res.attributes.title,
                                                    content = res.attributes.content.replace(/</g,"&lt;").replace(/>/g,"&gt;");
                                                document.querySelector("<?php echo $els ?>").innerHTML += `<li title='${content}'><a href="/<?php echo $slug ?>#${res.id}" target="_self" rel="nofollow">${title}</a></i>`;
                                            };
                                        })
                                    </script>
                            <?php
                                }
                                avos_posts_query($query_cid,".tech_window-content");
                            }else{
                                the_recent_posts($query_cid);
                            }
                            // $baas&&strpos(get_option('site_leancloud_category'), 'category-weblog.php')!==false ? avos_posts_query($query_cid,".tech_window-content") : the_recent_posts($query_cid);
                        ?>
                    </ul>
                    <div class="newsBox-subText-Description" id="tech_window-bottom">
                        <?php
                            // $query_str = get_template_bind_cat('category-weblog.php')->slug;
                            $query_slug = !isset(get_category($query_cid)->errors) ? get_category($query_cid)->slug : get_category(1)->slug;
                            echo '<a href="'.get_category_link($query_cid).'" rel="nofollow"><b>'.strtoupper($query_slug).'</b></a>';
                        ?>
                    </div>
                </span>
            </div>
        <?php
            }
            $acg_sw = get_option('site_acgnside_switcher');
            $tag_sw = get_option('site_tagcloud_switcher');
            if($acg_sw||$tag_sw){
        ?>
            <div id="tech-acg-inside_acg" class="wow fadeInUp" data-wow-delay="0.1s">
                <!-- 左图 ，右文窗-->
                <div id="tech-acg-inside_acg-allpart">
                    <div class="newsBox-supTitle flexboxes" id="acg_window-top">
                        <span class="newsBox-supTitle-iDescription" id="icon-acg">
                            <em><?php echo $acg_sw ? 'ACG' : 'TAG'; ?></em><i class="icom icon-acg"></i>
                        </span>
                        <h2> ACG はすぐに TAG </h2><!-- ACG · TAG -->
                    </div>
                    <ul class="acg_window-content">
                    <?php
                        if($acg_sw) {
                    ?>
                        <li class="acg_window-content-inside_left"<?php if(!$tag_sw) echo ' style="width: 98%;margin: 15px auto;"'; ?>>
                        <!--	<span id="acg_window-content-inside_left-tInfo">-->
                    		  <!--  <?php $query_cid = get_option('site_acgnside_cid'); ?>-->
                        <!--		<span id="acg_window-content-inside_left-txt">-->
                    				<!--<p>pixivで最もホットな2Dクリエイティブドローイングコレクショントップ<span id="acg_window-content-inside_left-txt_hidden" unselectable="on"> 10 </span> &nbsp;以上.</p>-->
                        <!--		</span>-->
                        <!--	</span>-->
                        	<span id="acg_window-content-inside_left-bList">
                        		<ol class="acg_window-content-inside_left-list">
                                    <?php
                                        // adsense_shortcode('adsense_list_richtext');
                                        $query_slug = !isset(get_category($query_cid)->errors) ? get_category($query_cid)->slug : get_category(1)->slug;
                                        if($baas&&strpos(get_option('site_leancloud_category'), 'category-acg.php')!==false){
                                    ?>
                                            <script>
                                                new AV.Query("<?php echo 'acg' ?>").addDescending("updatedAt")  // .equalTo('type_acg', 'anime')  // 当 query_slug 为 acg 时使用
                                                .limit(<?php echo get_option('site_per_posts', get_option('posts_per_page')); ?>).find().then(result=>{
                                                    for (let i=0; i<result.length;i++) {
                                                        let res = result[i],
                                                            type = res.attributes.type_acg,
                                                            title = res.attributes.title,
                                                            subtitle = res.attributes.subtitle,
                                                            updated = res.updatedAt;
                                                        document.querySelector(".acg_window-content-inside_left-list").innerHTML += `<li title="${title}"><a href="/<?php $par_cid = get_category($query_cid)->parent;echo $par_cid!=0&&get_category($par_cid)->slug!='/' ? get_category($par_cid)->slug : get_category($query_cid)->slug; ?>#${type}" target="_blank" rel="nofollow">${subtitle} - （${title}）<sup>${updated}</sup></a></i>`;
                                                    };
                                                })
                                            </script>
                                    <?php
                                        } else {
                                            the_recent_posts($query_cid, false, true, get_option('site_per_posts'));
                                        }
                                    ?>
                        		</ol>
                        	</span>
                        </li>
                    <?php
                        }else{
                            echo '<style>.acg_window-content li.acg_window-content-inside_right{display:block}.acg_window-content-inside_right .tags{font-family: math, cursive,monospace,serif,fangsong;padding: 0 15px;margin-top: 15px;}</style>';
                        }
                        if($tag_sw){
                    ?>
                        <li class="acg_window-content-inside_right"<?php if(!$acg_sw) echo ' style="width: 100%;position: relative;"'; ?>>
                            <div class="tags">
                                <?php 
                                    // 自定义标签云
                                    function the_tag_clouds($html_tag="li") {
                                        $output_sw = false;
                                        $caches_sw = get_option('site_cache_switcher');
                                        $caches_inc = get_option('site_cache_includes');
                                        $caches_name = 'site_tag_clouds_cache';
                                        if ($caches_sw && !get_option('site_tagcloud_auto_caches')) {
                                            $output_sw = in_array('tagclouds', explode(',', $caches_inc));
                                            $output_caches = get_option($caches_name);
                                            if ($output_sw && $output_caches) {
                                                echo $output_caches;
                                                return;
                                            }
                                        }
                                        $min_font = 10;
                                        $max_font = get_option('site_tagcloud_max');
                                        $num = get_option('site_tagcloud_num');
                                        $tags = get_tags(array(
                                            'taxonomy' => 'post_tag',
                                            'orderby' => 'count', //name
                                            'hide_empty' => true // for development,
                                            // 'number' => $num
                                        ));
                                        $tags_count = count($tags);
                                        $tags_count = $tags_count<=$num ? $tags_count : $num;
                                        shuffle($tags);  // random tags
                                        if($tags_count>0) {
                                            $output_string = '';
                                            global $bold_font;
                                            for($i=0; $i<$tags_count; $i++) {
                                                $tag = $tags[$i];
                                                $tag_count = $tag->count;
                                                $rand_font = mt_rand($min_font, $max_font);
                                                if($rand_font>=$max_font/1.25){
                                                    $rand_opt = mt_rand(5,10);  // highlight big_font
                                                    $bold_font = $rand_opt>9 || $rand_font==$max_font ? 'bold' : 'normal';  // max bold_font
                                                    $color_font = $rand_opt==10 && $rand_font==$max_font ? 'color:var(--theme-color)' : '';
                                                }else{
                                                    $rand_opt = mt_rand(2,10);
                                                    $color_font = $rand_opt<=5 && $rand_font<=$max_font/2 ? 'color:var(--theme-color)' : '';
                                                }
                                                $rand_opt = $rand_opt==10 ? $rand_opt=1 : '0.'.$rand_opt;  // use dot
                                                $output_string .= '<'.$html_tag.' data-count="'.$tag_count.'"><a href="'.get_tag_link($tag->term_id).'" target="_blank" style="font-size:'.$rand_font.'px;opacity:'.$rand_opt.';font-weight:'.$bold_font.';'.$color_font.'" title="'.$tag_count.' 篇<'.$tag->name.'>文章">'.$tag->name.'</a></'.$html_tag.'>'; //<sup>'.$tag->count.'</sup>
                                            };
                                            // standard updates
                                            if ($output_sw) {
                                                update_option($caches_name, $output_string); //wp_kses_post($output_string)
                                                return;
                                            }
                                            // standard echo
                                            echo $output_string;
                                            return;
                                        }
                                        echo '<span id="acg-content-area" style="background: url(//api.uuz.bid/random/?image) center /cover"></span><span id="acg-content-area-txt"><p id="hitokoto"> NO Tags Found.  </p></span>';
                                    }
                                    the_tag_clouds('span'); 
                                ?>
                            </div>
                        </li>
                    <?php
                        }
                    ?>
                    </ul>
                    <?php 
                        adsense_shortcode('adsense_list_context');
                        echo $acg_sw ? '<div class="newsBox-subText-Description" id="acg_window-bottom"><a href="'.get_category_link($query_cid).'" rel="nofollow"><b>'.strtoupper($query_slug).'</b></a></div>' : ''; //<a href="javascript:;"><b>TAGCLOUDS</b></a>
                    ?>
                    <!--<div class="newsBox-subText-Description" id="acg_window-bottom">-->
                    <!--    <?php //echo $acg_sw ? '<a href="'.get_category_link($query_cid).'" rel="nofollow"><b>'.strtoupper($query_slug).'</b></a>' : '<a href="javascript:;"><b>CLOUDS</b></a>'; ?>-->
                    <!--</div>-->
                </div>
            </div>
        <?php
            }
        ?>
        </div>
    </div>
</div>
<footer>
    <?php get_footer(); ?>
</footer>
<?php 
    if(get_option('site_chat_switcher')) echo '<script src="'.get_option('site_chat').'"></script>';
    get_foot();
?>
<!--<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/banner.js?v=<?php echo get_theme_info(); ?>"></script>-->
<!--<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/cursor.js"></script>-->
</body></html>