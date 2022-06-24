<style>
    body.dark #supports em.warmhole{
        background: url(<?php custom_cdn_src('img'); ?>/images/wormhole_2_tp.gif) no-repeat center center /cover!important;
    }
</style>
<div class="nav-wrap">
  <div class="top-bar-tips">
    <div class="tips-switch">
      <div class="tipsbox">
        <div class="tips">
            <p>
            <?php 
                $nick = get_option('site_nick', get_bloginfo('name'));
                echo  is_single() ? "<b>".$nick."</b> の ".get_the_category()[0]->name : bloginfo('description');
            ?></p>
            <p><?php current_tips($nick); ?></p>
        </div>
        <div class="nav-tools">
          <span class="imtl-content-right-inside-search">
           <?php get_search_form(); ?> 
          </span>
        </div>
      </div>
    </div>
    <span id="doc-progress-bar"></span>
  </div>
  <div class="main-header-all">
    <div class="block_of_down_element">
      <div class="inside_of_block" isBottom="no">
        <div class="logo-area" title="<?php echo get_option('site_nick', get_bloginfo('name')); ?> - <?php bloginfo('name') ?>">
          <a href="<?php bloginfo('url') ?>">
            <?php site_logo(); ?>
          </a>
        </div>
        <nav class="main-nav">
            <ul class="wp_list_cats">
            <?php
                $use_icon = get_option('site_icon_switcher');
                $site_icon = $use_icon ? '<i class="icom icon-more"></i>' : '';
                $choosen = is_home() ? 'choosen' : '';
                echo '<li class="cat_0 top_level"><a href="/" class="'.$choosen.'">'.$site_icon.'首页</a></li>';
                global $cat;  //变量提升
                $cat = $cat ? $cat : get_page_cat_id(current_slug());  // if is_page() then rewrite cat to cid // echo $cat;
                // print_r(get_category($cat));
                $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));
                if(!empty($cats)){
                    foreach($cats as $the_cat){
                        $the_cat_id = $the_cat->term_id;
                        $the_cat_slug = $the_cat->slug;  //use slug compare current category
                        $the_cat_par = get_category($the_cat->category_parent);
                        $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
                        $slug_icon = $the_cat_slug!="/" ? $the_cat_slug : "more";
                        if(!empty($catss)) $level="sec_level";else $level="top_level";
                        // 当前选中栏目 || 当前选中栏目下子栏目 || 当前栏目下文章&&文章单页
                        if($the_cat_id==$cat&&!is_single() || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single()) $choosen="choosen";else $choosen = "";
                        // if($the_cat_id==$cat || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single()) $choosen="choosen";else $choosen = "";  //current category/page detect (bychild) DO NOT USE ID DETECT, because all cat are page(post) type;
                        $cur_link = get_category_link($the_cat_id);
                        $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? 'javascript:void(0)' : $cur_link;  // detect if use $slash_link
                        $site_icon = $use_icon ? '<i class="icom icon-'.$slug_icon.'"></i>' : '';
                        if($the_cat_slug!='uncategorized') echo '<li class="cat_'.$the_cat_id.' '.$level.'"><a href="'.$slash_link.'" class="'.$choosen.'">' . $site_icon . $the_cat->name.'</a>';  //liwrapper
                        if(!empty($catss)){
                            if(get_option('site_metanav_switcher') && strpos(get_option('site_metanav_array'),$the_cat_slug)!==false){ //https://blog.csdn.net/ArthurBryant/article/details/6581833
                                if(strpos(get_option('site_metanav_image'), $the_cat_slug)!==false) $metaCls = "metaboxes";else $metaCls="";  // must else for each-loop
                                //METABOX RICH INFO
                                echo '<div class="additional metabox '.$metaCls.'"><ol class="links-more">';
                                foreach($catss as $the_cats){
                                    $the_cats_id = $the_cats->term_id;
                                    $the_cats_par = $the_cats->category_parent;
                                    $the_cats_name = $the_cats->name;
                                    $catsss = get_categories(meta_query_categories($the_cats_id, 'ASC', 'seo_order'));
                                    if(!empty($catsss)) $level="trd_level";else $level="sec_child";  // check level before sub-additionaln
                                    if($the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single()) $choosen = "choosen 2rd";else $choosen="2nd";  // current choosen detect
                                    if($metaCls){
                                        $meta_image = get_term_meta($the_cats_id, 'seo_image', true);
                                        if(!$meta_image) $meta_image = custom_cdn_src('img',true).'/images/default.jpg';//get_option('site_bgimg');
                                        echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" style="background:url('.$meta_image.') center center /cover;" class="'.$choosen.'"><b>'.$the_cats_name.'</b></a>';
                                    }else{
                                        $cats_desc = $the_cats->description;
                                        if(!$cats_desc) $cats_desc="Category Description";
                                        echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'"><b>'.$the_cats_name.'</b><p>'.$cats_desc.'</p></a>';
                                    }
                                    if(!empty($catsss)){
                                        echo '<div class="sub-additional metabox"><ol class="links-more">';
                                        foreach($catsss as $the_catss){
                                            $the_catss_id = $the_catss->term_id;
                                            $catssss = get_categories(meta_query_categories($the_catss_id, 'ASC', 'seo_order'));
                                            if(!empty($catssss)) $level="th_level";else $level="trd_child";
                                            if($the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single()) $choosen = "choosen 3rd";else $choosen="3rd";  // current choosen detect
                                            echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'"><b>'.$the_catss->name.'</b></a>';  //$catss_desc
                                        };
                                        echo "</ol></div>";
                                    }
                                }
                                echo "</ol></div>";
                            }else{  //elseif($the_cat_slug!=$metaArray[$i]){
                                echo '<div class="additional"><ol class="links-more">';
                                foreach($catss as $the_cats){
                                    $the_cats_id = $the_cats->term_id;
                                    $catsss = get_categories(meta_query_categories($the_cats_id, 'ASC', 'seo_order'));
                                    if(!empty($catsss)) $level="trd_level";else $level="sec_child";
                                    if($the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single()) $choosen = "choosen 2rd";else $choosen="2nd";  // current choosen detect
                                    $cur_link = get_category_link($the_cats_id);
                                    $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? 'javascript:void(0)' : $cur_link;  // detect if use $slash_link
                                    echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats->category_parent." ".$level.'"><a href="'.$slash_link.'" class="'.$choosen.'">'.$the_cats->name.'</a>';  //liwrapper
                                    if(!empty($catsss)){
                                        echo '<div class="sub-additional"><ol class="links-more">';
                                        foreach($catsss as $the_catss){
                                            $the_catss_id = $the_catss->term_id;
                                            $catssss = get_categories(meta_query_categories($the_catss_id, 'ASC', 'seo_order'));
                                            if(!empty($catssss)) $level="th_level";else $level="trd_child";
                                            if($the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single()) $choosen = "choosen 3rd";else $choosen="3rd";  // current choosen detect
                                            echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'">'.$the_catss->name.'</a>';  //liwrapper
                                            if(!empty($catssss)){
                                                echo '<div class="sub-additional"><ol class="links-more">';
                                                foreach($catssss as $the_catsss){
                                                    $the_catsss_id = $the_catsss->term_id;
                                                    if($the_catsss_id==$cat || cat_is_ancestor_of($the_catsss_id, $cat) || in_category($the_catsss_id)&&is_single()) $choosen = "choosen 4th";else $choosen="4th";  // current choosen detect
                                                    echo '<li class="cat_'.$the_catsss_id.' par_'.$the_catsss->category_parent.'"><a href="'.get_category_link($the_catsss_id).'" class="'.$choosen.'">'.$the_catsss->name.'</a></li>';  //no wrapper
                                                };
                                                echo "</ol></div>";
                                            };
                                            echo "</li>";
                                        };
                                        echo "</ol></div>";
                                    };
                                    echo "</li>";
                                };
                                echo "</ol></div>";
                            }
                        };
                        echo "</li>";
                    }
                }
            ?>
            <!--<li><small style="padding: 0 15px;display: none;">oop<strong>S</strong>ays..</small></li>-->
          </ul>
          <div class="nav-slider">
            <span id="slide-target"></span>
          </div>
        </nav>
      </div>
      <div class="mobile-vision">
        <span class="m-menu">
          <i class="BBFontIcons"></i>
        </span>
        <a href="/" rel="nofollow">
          <div class="m-logo">
            <?php site_logo(); ?>
          </div>
        </a>
        <span class="m-search">
          <i class="BBFontIcons"></i>
        </span>
          <div class="mobile-search">
            <div class="ms-inside-block">
              <div class="ms-inside">
                <div class="ms-inside-searchBox">
                    <form id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="text" class="search-field" name="s" placeholder="Searching.." value="<?php echo get_search_query(); ?>">
                		<input type="submit" id="searchsubmit" value="Search" />
                    </form>
                  <!--<span class="BBFontIcons ms-close-btn">&#xe91d;</span>-->
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>