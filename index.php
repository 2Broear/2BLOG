<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/main.min.css?v=0.1<?php //echo(mt_rand()) ?>" />
    <?php include_once(TEMPLATEPATH. '/head.php'); ?>
    <style>
        #banner-prev, #banner-next{background:url('<?php custom_cdn_src('img'); ?>/images/css_sprites.png') no-repeat}
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
                            <small><strong> <?php echo get_option('site_nick', get_bloginfo('name')); ?> </strong></small>
                            <p> 「<?php bloginfo('description') ?>」 </p>
                        </span>
                    </div>
                </div>
            </div>
            <div class="banner">
                <div class="banner-inside">
                    <ul>
                        <?php
                            $banner_array = explode(',',get_option('site_banner_array',''));
                            for($i=0;$i<count($banner_array);$i++){
                                $image_url = trim($banner_array[$i]);
                                if($image_url) echo '<li style="background: url('.$image_url.') no-repeat center center /cover;"></li>';
                            }
                        ?>
                        <li style="background: url('https://api.luvying.com/acgimg') no-repeat center center /cover;"></li>
                    </ul>
                    <div class="switcher">
                        <span id="banner-prev"></span>
                        <span id="banner-next"></span>
                    </div>
                    <div class="dots"></div>
                </div>
            </div>
        </div>
        <!-- 右 -->
        <div class="recommendation wow fadeInUp hfeed" data-wow-delay="0.2s">
            <?php
                $rcmd_query = new WP_Query(array_filter(array('cat' => get_option('site_rcmdside_cid'), 'meta_key' => 'post_orderby', 'posts_per_page' => 1,
                    'orderby' => array(
                        'meta_value_num' => 'DESC',
                        'date' => 'DESC',
                        'modified' => 'DESC',
                    )
                )));
                while ($rcmd_query->have_posts()):
                    $rcmd_query->the_post();
                    $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                    $post_orderby = get_post_meta($post->ID, "post_orderby", true);
            ?>
                    <article class="<?php if($post_orderby>1) echo 'topset'; ?> article" id="recommend-inside">
                      <div class="recommend-newsImg">
                        <div>
                          <a href="<?php the_permalink() ?>">
                            <span id="lowerbg" style="background:url('<?php echo get_postimg(0,$post->ID,true); ?>') center 40% no-repeat;background-size:cover;"></span>
                          </a>
                          <a href="<?php the_permalink() ?>" id="rel" rel="bookmark" target="_blank">
                            <b><?php the_title() ?></b>
                          </a>
                        </div>
                      </div>
                      <div class="recommend-newsContent">
                        <span class="content-core entry-content">
                            <p><?php custom_excerpt(170);//echo wp_trim_words(get_the_excerpt(), 10); ?></p>
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
              $cardnav_array = explode(';',get_option('site_cardnav_array'));
              for($i=0;$i<count($cardnav_array);$i++){
                  $each_card = explode('/',$cardnav_array[$i]);
                  if($each_card[0]){
                      $card_slug = trim($each_card[0]);
                      $card_nick = trim($each_card[1]);
                      $card_term = get_category_by_slug($card_slug) ? get_category_by_slug($card_slug) : get_category(1);  // 1 for UNCATEGORIZED
                      if(!$card_nick) $card_nick=get_category_by_slug($card_slug)->name;  //incase non diy nick
                      if($card_slug){  //incase end with ";"
                        echo '<span class="'.$card_slug.'"><a href="'.get_category_link($card_term->term_id).'"> '.$card_nick.'<i class="icom icon-'.$card_slug.'"></i></a></span>';
                      }
                  }
              }
          ?>
        </div>
    </div>
    <div class="main-top-part flexboxes wow fadeInUp" data-wow-delay="0.3s">
        <!-- 左 -->
        <div class="special-display">
            <ul class="flexboxes">
                <li id="special-img" style="background: url(<?php custom_cdn_src('img'); ?>/images/google.gif) center /cover;"><a href="javascript:;" style="width:100%;height:100%;position:absolute;top:0;left:0;"></a></li>
            </ul>
        </div>
        <!-- 右 -->
        <div class="resource-windows flexboxes">
            <div id="news-window">
                <span class='resource-windows-top'>
                <span class='resource-windows-top_inside'>
                    <span id="icon-resource"></span>
                </span>
                <h3><?php $news_temp = !get_template_bind_cat('category-news.php')->errors ? get_template_bind_cat('category-news.php') : false;echo $news_temp ? $news_temp->name : '近期文章'; ?></h3>
                </span>
                <ul class="news-list" id="mainNews">
                    <?php recent_posts_query($news_temp->term_id, true); ?>
                </ul>
            </div>
            <div id="download-window">
                <span id="download-window-top" class='resource-windows-top'>
                <span class='resource-windows-top_inside'>
                    <span id="icon-download"></span>
                </span>
                <h3><?php $note_temp = !get_template_bind_cat('category-notes.php')->errors ? get_template_bind_cat('category-notes.php') : false;echo $note_temp ? $note_temp->name : '笔记栈'; ?></h3>
                </span>
                <ul class="download-list" id="rcmdNewsHside">
                    <?php recent_posts_query($note_temp->term_id, true); ?>
                </ul>
            </div>
        </div>
    </div>
    <!--<div class="main-middle-allpart"></div>-->
    <div class="main-bottom-allpart">
        <!-- 左文窗 ，右图-->
        <div class="main-bottom-ta">
        <?php
            $baas = get_option('site_leancloud_switcher');
            if(get_option('site_techside_switcher')){
                
        ?>
            <div id="tech-acg-inside_tech" class="flexboxes wow fadeInUp" data-wow-delay="0.15s">
                <span id="tech_window" style="width:100%">
                    <div class="newsBox-supTitle flexboxes" id="tech_window-top">
                        <span class="newsBox-supTitle-iDescription" id="icon-technology" title="Tech | 科技资讯">
                            <em>BLOG</em><i class="icom icon-weblog"></i>
                        </span>
                        <h2><?php $blog_temp = !get_template_bind_cat('category-weblog.php')->errors ? get_template_bind_cat('category-weblog.php') : false;echo strtoupper($blog_temp->slug).'「'.$blog_temp->name.'」'; ?></h2>
                    </div>
                    <ul class="tech_window-content">
                        <?php 
                            $query_cid = get_option('site_techside_cid');
                            $baas ? avos_posts_query($query_cid,".tech_window-content") : recent_posts_query($query_cid);
                        ?>
                    </ul>
                    <div class="newsBox-subText-Description" id="tech_window-bottom">
                        <?php
                            // $query_str = get_template_bind_cat('category-weblog.php')->slug;
                            $query_slug = !get_category($query_cid)->errors ? get_category($query_cid)->slug : get_category(1)->slug;
                            echo '<a href="'.get_category_link($query_cid).'" rel="nofollow"><b>'.strtoupper($query_slug).'</b></a>';
                        ?>
                    </div>
                </span>
                <!--<span id="tech_pic" style="background: url(<?php //echo get_option('site_techside_bg'); ?>) center /cover;"></span>-->
            </div>
        <?php
            }
            if(get_option('site_acgnside_switcher')){
        ?>
            <div id="tech-acg-inside_acg" class="wow fadeInUp" data-wow-delay="0.1s">
                <!-- 左图 ，右文窗-->
                <div id="tech-acg-inside_acg-allpart">
                    <div class="newsBox-supTitle flexboxes" id="acg_window-top">
                        <span class="newsBox-supTitle-iDescription" id="icon-acg" title="ACG 宅周报">
                            <em>ACG</em><i class="icom icon-acg"></i>
                        </span>
                        <h2> ACG はすぐに TAG CLOUDS </h2>
                    </div>
                    <ul class="acg_window-content">
                        <!--<div class="ajaxloadMainAcg" ajaxload="ajax/main/ajax-main-acg.html"></div>-->
                        <li class="acg_window-content-inside_left">
                        	<span id="acg_window-content-inside_left-tInfo">
                        		<!--<span id="acg_window-content-inside_left-pic">-->
                        		    <?php 
                        			    $query_cid = get_option('site_acgnside_cid');
                        			    ///echo '<em style="background:url('.cat_metabg($query_cid, custom_cdn_src('img',true).'/images/acg.jpg').') center /cover;width:100%;height:155px;display:block;"></em>';
                    			    ?>
                        		<!--</span>-->
                        		<span id="acg_window-content-inside_left-txt">
                    				<!--<h2>pixivトップ50</h2>-->
                    				<p>pixivで最もホットな2Dクリエイティブドローイングコレクショントップ<span id="acg_window-content-inside_left-txt_hidden" unselectable="on"> 10 </span> &nbsp;以上.<a href="javascript:;"> 漫游影视近期动态 </a></p>
                        		</span>
                        	</span>
                        	<span id="acg_window-content-inside_left-bList">
                        		<ol class="acg_window-content-inside_left-list">
                                    <?php
                                        $query_slug = !get_category($query_cid)->errors ? get_category($query_cid)->slug : get_category(1)->slug;
                                        if($baas&&strpos(get_option('site_leancloud_category'), 'category-acg.php')!==false){
                                    ?>
                                            <script>
                                                new AV.Query("<?php echo 'acg' ?>").addDescending("updatedAt")  // .equalTo('type_acg', 'anime')  // 当 query_slug 为 acg 时使用
                                    <?php
                                                // if($query_slug==get_template_bind_cat('category-acg.php')->slug) echo '.equalTo("type_acg", "anime")'
                                    ?>
                                                .limit(<?php echo get_option('site_per_posts', get_option('posts_per_page')); ?>).find().then(result=>{
                                                    // console.log(result)
                                                    for (let i=0; i<result.length;i++) {
                                                        let res = result[i],
                                                            // src = res.attributes.src,
                                                            type = res.attributes.type_acg,
                                                            title = res.attributes.title,
                                                            subtitle = res.attributes.subtitle,
                                                            updated = res.updatedAt;
                                                        document.querySelector(".acg_window-content-inside_left-list").innerHTML += `<li title="${title}"><a href="/<?php $par_cid = get_category($query_cid)->parent;echo $par_cid!=0&&get_category($par_cid)->slug!='/' ? get_category($par_cid)->slug : get_category($query_cid)->slug; ?>#${type}" target="_blank" rel="nofollow">${subtitle} - （${title}）<sup>${updated}</sup></a></i>`;
                                                    };
                                                })
                                            </script>
                                    <?php
                                        }else{
                                            recent_posts_query($query_cid, false, true, false);
                                        }
                                    ?>
                        		</ol>
                        	</span>
                        </li>
                        <li class="acg_window-content-inside_right">
                            <div class="tags">
                                <?php the_tag_clouds('span'); ?>
                            </div>
                        	<!--<span id="acg-content-area" style="background: url(<?php //echo get_option('site_techside_bg'); ?>) center /cover"></span>-->
                        	<!--<span id="acg-content-area-txt"><p id="hitokoto"> ? </p></span>-->
                        </li>
                    </ul>
                    <div class="newsBox-subText-Description" id="acg_window-bottom">
                        <?php
                            // $query_cat = get_template_bind_cat('category-acg.php');
                            echo '<a href="'.get_category_link($query_cid).'" rel="nofollow"><b>'.strtoupper($query_slug).'</b></a>';
                        ?>
                    </div>
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
<?php if(get_option('site_chat_switcher')) echo '<script src="'.get_option('site_chat').'"></script>'; ?>
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/banner.js"></script>
<!--<script type="text/javascript" src="<?php //custom_cdn_src(); ?>/js/cursor.js"></script>-->
</body></html>