<div class="nav-wrap">
  <div class="top-bar-tips">
    <div class="tips-switch">
      <div class="tipsbox">
        <div class="tips">
            <p>
                <?php 
                    $nick = get_option('site_nick');
                    echo  is_single() ? "<b>".$nick."</b> の ".get_the_category()[1]->parent==0 ? get_the_category()[1]->name : get_the_category()[0]->name : bloginfo('description');
                ?>
            </p>
            <p><?php 
                if(!is_single()) echo "<b>".$nick."</b> の ";
                switch (true) {
                    case is_home():
                        echo bloginfo('name');
                        break;
                    case is_category():
                        echo single_cat_title();
                        break;
                    case is_page() || is_single():
                        echo the_title();
                        break;
                    case is_search():
                        echo 'Searching..';
                        break;
                    default:
                        echo "NOT MATCHED";
                        break;
                }
            ?></p>
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
        <div class="logo-area" title="<?php echo get_option('site_nick'); ?> - <?php bloginfo('name') ?>">
          <a href="<?php bloginfo('url') ?>">
            <?php site_logo(false); ?>
          </a>
        </div>
        <nav class="main-nav">
            <ul class="wp_list_cats">
                <li class=""><a href="/" class=""><i class="icom icon-more"></i>首页<?php //print_r(get_category_by_slug()) ?></a></li>
                <?php  // https://www.wordpress.la/512.html
                    global $post;
                    $gloPost_id = $post->ID;
                    $gloPost_parId = $post->post_parent;
                    $gloPost_parParId = get_post($gloPost_parId)->post_parent;
                    $pages = get_pages(array(
                        'sort_column' => 'menu_order',
                        'sort_order' => 'DESC',
                        'child_of ' => 0,
                        'parent' => '',
                        'exclude_tree' => '',
                        'meta_key' => '',
                        'meta_value' => '',
                        'authors' => '',
                    ));
                    if(!empty($pages)){
                        foreach ($pages as $page){
                            $page_id = $page->ID;
                            $slug_icon = $page->post_name!="/" ? $page->post_name : "more";
                            $pagess = get_pages(array(
                                'sort_column' => 'menu_order',
                                'sort_order' => 'DESC',
                                'child_of ' => $page_id,
                                'parent' => $page_id,
                            ));
                            // 全局 post 向上遍历父级是否匹配当前 page（parParId层级取决于根部子类层级，一般仅根目录多级匹配）
                            if($page_id===$gloPost_id || $page_id===$gloPost_parId || $page_id===$gloPost_parParId || in_category($page_id)&&is_single()) $choosen="choosen";else $choosen = "";  // detect 1st choosen $post->post_parent->post_parent
                            if(!empty($pagess)) $level="sec_level";else $level="top_level";  // check 2nd level
                            echo '<li class="page_'.$page_id.' '.$level.'"><a href="'.get_page_link($page).'" class="'.$choosen.'"><i class="icom icon-'.$slug_icon.'"></i>'.$page->post_title.'</a>';
                            if(!empty($pagess)){
                                $page_metanav = get_post_meta($page_id, "page_metanav", true);
                                if(get_option('site_metanav_switcher') && $page_metanav!='none'){
                                    if($page_metanav=='image') $metaImgCls = "metaboxes";else $metaImgCls = "";  // use else to each page
                                    echo '<div class="additional metabox '.$metaImgCls.'"><ol class="links-more">';
                                    foreach($pagess as $posts){
                                        $pages_id = $posts->ID;
                                        $pages_title = $posts->post_title;
                                        $pagesss = get_pages(array(
                                            'sort_column' => 'menu_order',
                                            'sort_order' => 'DESC',
                                            'child_of ' => $pages_id,
                                            'parent' => $pages_id,
                                        ));
                                        if($pages_id===$gloPost_id || $pages_id===$gloPost_parId) $choosen="choosen";else $choosen = "";  // detect 2nd choosen
                                        if(!empty($pagesss)) $level="trd_level";else $level="sec_child";  // check 3rd level(meta)
                                        if($metaImgCls){
                                            $meta_image = get_the_post_thumbnail_url($pages_id,'medium');
                                            if(!$meta_image) $meta_image = get_option('site_bgimg');
                                            echo '<li class="metaimg_page_'.$pages_id.' '.$level.'"><a href="'.get_page_link($pages_id).'" style="background:url('.$meta_image.') center center /cover;" class="'.$choosen.'"><b>'.$pages_title.'</b></a>';
                                        }else{
                                            $pages_desc = strip_tags(trim($posts->post_content),"");
                                            if(!$pages_desc) $pages_desc="Empty Page Content Description";
                                            echo '<li class="metatxt_page_'.$pages_id.' '.$level.'"><a href="'.get_page_link($pages_id).'" class="'.$choosen.'"><b>'.$pages_title.'</b><p>'.$pages_desc.'</p></a>';
                                        }
                                        if(!empty($pagesss)){
                                            echo '<div class="sub-additional metabox"><ol class="links-more">';
                                            foreach($pagesss as $postss){
                                                $pagess_id = $postss->ID;
                                                if($pagess_id===$gloPost_id || $pagess_id===$gloPost_parId) $choosen="choosen";else $choosen = "";  // detect 3rd choosen
                                                echo '<li class="page_'.$pagess_id.'"><a href="'.get_page_link($pagess_id).'" class="'.$choosen.'"><b>'.$postss->post_title.'</b></a>';  //$catss_desc
                                            };
                                            echo "</ol></div>";
                                        }
                                    }
                                    echo "</ol></div>";
                                }else{
                                    echo '<div class="additional"><ol class="links-more">';
                                    foreach($pagess as $posts){
                                        $pages_id = $posts->ID;
                                        $pagesss = get_pages(array(
                                            'sort_column' => 'menu_order',
                                            'sort_order' => 'DESC',
                                            'child_of ' => $pages_id,
                                            'parent' => $pages_id,
                                        ));
                                        if($pages_id===$gloPost_id) $choosen="choosen";else $choosen = "";  // detect 2nd choosen
                                        if(!empty($pagesss)) $level="trd_level";else $level="sec_child";  // check 3rd level(list)
                                        echo '<li class="child_'.$pages_id.' '.$level.'""><a href="'.get_page_link($pages_id).'" class="'.$choosen.'">'.$posts->post_title.'</a>';  //liwrapper
                                        if(!empty($pagesss)){
                                            echo '<div class="sub-additional"><ol class="links-more">';
                                            foreach($pagesss as $postss){
                                                $pagess_id = $postss->ID;
                                                $pagessss = get_pages(array(
                                                    'sort_column' => 'menu_order',
                                                    'sort_order' => 'DESC',
                                                    'child_of ' => $pagess_id,
                                                    'parent' => $pagess_id,
                                                ));
                                                if($pagess_id===$gloPost_id) $choosen="choosen";else $choosen = "";  // detect 3rd choosen
                                                if(!empty($pagessss)) $level="th_level";else $level="trd_child";  // check 3rd level(list)
                                                echo '<li class="page_'.$pagess_id.' '.$level.'""><a href="'.get_page_link($pagess_id).'" class="'.$choosen.'">'.$postss->post_title.'</a>';  //liwrapper
                                                if(!empty($pagessss)){
                                                    echo '<div class="sub-additional"><ol class="links-more">';
                                                    foreach($pagessss as $postsss){
                                                        $pagesss_id = $postsss->ID;
                                                        if($pagesss_id===$gloPost_id) $choosen="choosen";else $choosen = "";  // detect 4th choosen
                                                        echo '<li class="page_'.$pagesss_id.'"><a href="'.get_page_link($pagesss_id).'" class="'.$choosen.'">'.$postsss->post_title.'</a></li>';  //no wrapper
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
                            }
                            echo "</li>";
                        }
                    }
                ?>
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
            <?php site_logo(false); ?>
          </div>
        </a>
        <span class="m-search">
          <i class="BBFontIcons"></i>
        </span>
      </div>
    </div>
  </div>
  <div class="mobile-search">
    <div class="ms-inside-block">
      <div class="ms-inside">
        <div class="ms-inside-searchBox">
            <form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
            	<div>
            		<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
            		<input type="submit" id="searchsubmit" value="Search" />
            	</div>
            </form>
          <span class="BBFontIcons ms-close-btn">&#xe91e;</span>
        </div>
      </div>
    </div>
  </div>
</div>