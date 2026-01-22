<?php
    global $img_cdn;
    //自定义当前滚动提示
    function current_tips($nick){
        if(!is_single()) echo "<b>".$nick."</b> の ";
        switch (true) {
            case is_home():
                echo bloginfo('name');
                break;
            case is_category():
                echo single_cat_title();  // get_cat_title();
                break;
            case is_page() || is_single():  // in_category($single):
                echo the_title();
                break;
            case is_search():
                echo 'Searchs';
                break;
            case is_tag():
                echo single_tag_title('',false) . ' Tag';
                break;
            case is_archive():
                echo 'Archives';
                break;
            default:
                echo "NOT MATCHED";
                break;
        }
    }
    // 分类导航（PC/MOBILE）
    function category_navigation($mobile=false, $deepth=0){
        global $cat;
        $deepth = $deepth ? $deepth : get_option('site_catnav_deepth', 9);  //default output 9-level nav-cats if catnav_lv unset
        $use_icon = get_option('site_icon_switcher');
        $site_icon = $use_icon ? '<i class="icom icon-more"></i>' : '';
        $choosen = is_home() ? 'choosen' : '';
        echo '<li class="cat_0 top_level"><a href="/" class="'.$choosen.'">'.$site_icon.'首页</a></li>';
        $cat = $cat ? $cat : get_page_cat_id(current_slug());  // if is_page() then rewrite cat to cid // echo $cat;
        // print_r(get_category($cat));
        $cats = get_categories(meta_query_categories(0));
        if(!empty($cats)){
            global $img_cdn, $cdn_switch, $images_cdn;
            $slash_href = 'javascript:void(0)';
            foreach($cats as $the_cat){
                $the_cat_id = $the_cat->term_id;
                $the_cat_slug = $the_cat->slug;  //use slug compare current category
                $the_cat_par = get_category($the_cat->category_parent);
                $catss = get_categories(meta_query_categories($the_cat_id));
                $slug_icon = $the_cat_slug!="/" ? $the_cat_slug : "more";
                $level = !empty($catss) ? "sec_level" : "top_level";
                $choosen = $the_cat_id==$cat&&!is_single() || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single() ? "choosen" : "";  // 当前选中栏目 || 当前选中栏目下子栏目 || 当前栏目下文章&&文章单页
                $cur_link = get_category_link($the_cat_id);
                $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? $slash_href : $cur_link;  // detect if use $slash_link
                // $slash_name = $slash_link===$slash_href
                $site_icon = $use_icon ? '<i class="icom icon-'.$slug_icon.'"></i>' : '';
                if($the_cat_slug!='uncategorized') echo '<li class="cat_'.$the_cat_id.' '.$level.'"><a href="'.$slash_link.'" class="'.$choosen.'" rel="nofollow">' . $site_icon . $the_cat->name.'</a>';  //liwrapper
                if(!empty($catss) && $deepth>=2){
                    $metanav_array = explode(',', get_option('site_metanav_array'));
                    if(get_option('site_metanav_switcher') && in_array($the_cat_slug, $metanav_array)){ //strpos(get_option('site_metanav_array'),$the_cat_slug)!==false
                        $metaimg_array = explode(',', get_option('site_metanav_image'));
                        $metaCls = in_array($the_cat_slug, $metaimg_array) ? "metaboxes" : "";  // must else for each-loop //strpos(get_option('site_metanav_image'), $the_cat_slug)!==false
                        //METABOX RICH INFO
                        echo $mobile ? '<ul class="links-mores '.$metaCls.'">' : '<div class="additional metabox '.$metaCls.'"><ol class="links-more">';
                        foreach($catss as $the_cats){
                            $the_cats_id = $the_cats->term_id;
                            $the_cats_par = $the_cats->category_parent;
                            $catsss = get_categories(meta_query_categories($the_cats_id));
                            $the_cats_name = !$mobile ? '<b>'.$the_cats->name.'</b>' : $the_cats->name;
                            $level = "sec_child";  // check level before sub-additionaln
                            if(!empty($catsss)){
                                $level = "trd_level";
                                $the_cats_name = '<b>'.$the_cats->name.'</b>';
                            }
                            $choosen = $the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single() ? "choosen 3rd" : "2nd";  // current choosen detect
                            if($metaCls&&!$mobile){
                                $meta_image = get_term_meta($the_cats_id, 'seo_image', true);
                                if($meta_image){
                                    if($cdn_switch){
                                        $upload_url = wp_get_upload_dir()['baseurl'];
                                        $meta_image = str_replace($upload_url, $images_cdn, $meta_image); //get_option('site_cdn_img',$upload_url)
                                    }
                                }else{
                                    $meta_image = $img_cdn.'/images/default.jpg';
                                }
                                echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'" style="background:url('.$meta_image.') center center /cover;">'.$the_cats_name.'</a>'; // style="--data-background:'.$meta_image.'" data-background="'.$meta_image.'" <style>.inside_of_block nav.main-nav .metaboxes li:hover > a{background-image: var(--data-background);}</style>
                            }else{
                                $cats_desc = $mobile ? '' : ($the_cats->description ? '<p>'.$the_cats->description.'</p>' : "<p>Category Description</p>");
                                echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'">'.$the_cats_name.$cats_desc.'</a>';
                            }
                            if(!empty($catsss) && $deepth>=3){
                                echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional metabox"><ol class="links-more">';
                                foreach($catsss as $the_catss){
                                    $the_catss_id = $the_catss->term_id;
                                    $the_catss_name = $mobile ? $the_catss->name : '<b>'.$the_catss->name.'</b>';
                                    $catssss = get_categories(meta_query_categories($the_catss_id));
                                    $level = !empty($catssss) ? "th_level" : "trd_child";  // check level before sub-additionaln
                                    $choosen = $the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single() ? "choosen 3rd" : "3rd";  // current choosen detect
                                    echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'">'.$the_catss_name.'</a>';  //$catss_desc
                                };
                                echo $mobile ? "</ul>" : "</ol></div>";
                            }
                        }
                        echo $mobile ? "</ul>" : "</ol></div>";
                    }else{  //elseif($the_cat_slug!=$metaArray[$i]){
                        echo $mobile ? '<ul class="links-mores">' : '<div class="additional"><ol class="links-more">';
                        foreach($catss as $the_cats){
                            $the_cats_id = $the_cats->term_id;
                            $catsss = get_categories(meta_query_categories($the_cats_id));
                            $the_cats_name = $the_cats->name;
                            $level = "sec_child";  // check level before sub-additionaln
                            if(!empty($catsss)){
                                $level = "trd_level";
                                $the_cats_name = '<b>'.$the_cats_name.'</b>';
                            }
                            $choosen = $the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single() ? "choosen 2nd" : "2nd";  // current choosen detect
                            $cur_link = get_category_link($the_cats_id);
                            $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? $slash_href : $cur_link;  // detect if use $slash_link
                            echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats->category_parent." ".$level.'"><a href="'.$slash_link.'" class="'.$choosen.'" rel="nofollow">'.$the_cats_name.'</a>';  //liwrapper
                            if(!empty($catsss) && $deepth>=3){
                                echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional"><ol class="links-more">';
                                foreach($catsss as $the_catss){
                                    $the_catss_id = $the_catss->term_id;
                                    $catssss = get_categories(meta_query_categories($the_catss_id));
                                    $the_catss_name = $the_catss->name;
                                    $level = "trd_child";  // check level before sub-additionaln
                                    if(!empty($catssss)){
                                        $level = "th_level";
                                        $the_catss_name = '<b>'.$the_catss_name.'</b>';
                                    }
                                    $choosen = $the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single() ? "choosen 3rd" : "3rd";  // current choosen detect
                                    echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'">'.$the_catss_name.'</a>';  //liwrapper
                                    if(!empty($catssss) && $deepth>=4){
                                        echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional"><ol class="links-more">';
                                        foreach($catssss as $the_catsss){
                                            $the_catsss_id = $the_catsss->term_id;
                                            if($the_catsss_id==$cat || cat_is_ancestor_of($the_catsss_id, $cat) || in_category($the_catsss_id)&&is_single()) $choosen = "choosen 4th";else $choosen="4th";  // current choosen detect
                                            echo '<li class="cat_'.$the_catsss_id.' par_'.$the_catsss->category_parent.'"><a href="'.get_category_link($the_catsss_id).'" class="'.$choosen.'">'.$the_catsss->name.'</a></li>';  //no wrapper
                                        };
                                        echo $mobile ? "</ul>" : "</ol></div>";
                                    };
                                    echo "</li>";
                                };
                                echo $mobile ? "</ul>" : "</ol></div>";
                            };
                            echo "</li>";
                        };
                        echo $mobile ? "</ul>" : "</ol></div>";
                    }
                };
                echo "</li>";
            }
        }
        unset($cat);
    }
?>
<div class="nav-wrap">
    <div class="top-bar-tips">
        <div class="tips-switch">
            <div class="tipsbox">
                <div class="tips">
                    <?php
                        // mobile searchform
                        get_search_form();
                        echo '<p>';
                        $nick = get_option('site_nick', get_bloginfo('name'));
                        $curcat = get_the_category() ? get_the_category()[0] : get_category(0);
                        echo  is_single() ? "<b>".$nick."</b> の ".$curcat->name : bloginfo('description');
                        echo '</p><p>';
                        current_tips($nick);
                        echo '</p>';
                        if(is_single()){
                            $next_post = get_next_post(true, '', 'category');  // same category posts
                            $prev_post = get_previous_post(true, '', 'category');  // same category posts
                            // print_r($next_post->post_title);
                            if($prev_post){
                                $prev_pid = $prev_post->ID;
                                echo '<p id="np"><b>下一篇：</b>';
                                if (is_a($prev_post , 'WP_Post')) {
                                    echo '<a href="'.get_permalink($prev_pid).'">'.get_the_title($prev_pid).'</a>';
                                }
                                echo '</p>';
                            }else{
                                if($next_post){
                                    $next_pid = $next_post->ID;
                                    echo '<p id="np"><b>上一篇：</b>';
                                    if(is_a($next_post , 'WP_Post')){
                                        echo '<a href="'.get_permalink($next_pid).'">'.get_the_title($next_pid).'</a>';
                                    }
                                    echo '</p>';
                                }
                            }
                        };
                    ?>
                </div>
                <div class="nav-tools">
                    <span class="imtl-content-right-inside-search"><?php get_search_form(); ?> </span>
                </div>
            </div>
        </div>
        <span id="doc-progress-bar"></span>
    </div>
    <div class="main-header-all">
        <div class="block_of_down_element">
            <div class="inside_of_block" isBottom="no">
                <div class="logo-area" title="<?php echo get_option('site_nick', get_bloginfo('name')); ?> - <?php bloginfo('name') ?>">
                    <a href="<?php bloginfo('url') ?>" aria-label="logo"><?php site_logo(); ?></a>
                </div>
                <nav class="main-nav">
                    <ul class="wp_list_cats">
                        <?php category_navigation(); ?>
                    </ul>
                    <div class="nav-slider">
                        <!--<span id="slide-target"></span>-->
                    </div>
                </nav>
            </div>
            <div class="mobile-vision">
                <span class="m-menu"><i class="BBFontIcons"></i></span>
                <a href="/" rel="nofollow" aria-label="weibo">
                    <div class="m-logo"><?php site_logo(); ?></div>
                </a>
                <span class="m-search search-pop"><i class="BBFontIcons"></i></span>
            </div>
        </div>
    </div>
    <div class="slider-menu" tabindex="1" style="background:url(<?php echo $img_cdn; ?>/images/bg3.png) repeat center center">
        <div class="slider-menu-inside">
            <div class="slider-menu_header">
                <span class="slider-tips">
                    <p> <?php echo get_option('site_nick', get_bloginfo('name')); ?> の <strong><?php bloginfo('name') ?></strong> </p>
                </span>
                <span class="slider-close" title="Close Menu">
                    <i class="BBFontIcons close-btn"></i>
                </span>
                <div class="slider-logo">
                    <a href="<?php bloginfo('url') ?>" data-instant style="display: inline-block;" rel="nofollow">
                        <?php site_logo(true); ?><!--<svg class="svg-symbols" width="115px" height="41px" viewBox="0 0 115 41"></svg>-->
                    </a>
                </div>
            </div>
            <div class="slider-menu_body">
                <div class="body-main">
                    <ul>
                        <?php category_navigation(true); ?>
                    </ul>
                </div>
            </div>
            <div class="slider-menu_footer">
                <span class="footer-tips">找什么？搜搜看<i class="BBFontIcons check-hook"></i></span>
            </div>
        </div>
    </div>
    <div class="windowmask"></div>
</div>