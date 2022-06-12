<div class="slider-menu" tabindex="1" style="background:url(<?php custom_cdn_src('img'); ?>/images/bg3.png) repeat center center">
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
                    <?php site_logo(); ?><!--<svg class="svg-symbols" width="115px" height="41px" viewBox="0 0 115 41"></svg>-->
                </a>
            </div>
        </div>
        <div class="slider-menu_body">
            <div class="body-main">
                <ul>
                    <?php
                        $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));
                        if(!empty($cats)){
                            foreach($cats as $the_cat){
                                $the_cat_id = $the_cat->term_id;
                                $the_cat_slug = $the_cat->slug;  //use slug compare current category
                                $the_cat_par = get_category($the_cat->category_parent);
                                $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
                                $slug_icon = $the_cat->slug!="/" ? $the_cat->slug : "more";
                                if(!empty($catss)) $level="sec_level";else $level="top_level";
                                if($the_cat_id==$cat || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single()) $choosen="choosen";else $choosen = "";  //current category/page detect (bychild) DO NOT USE ID DETECT, because all cat are page(post) type;
                                echo '<li class="cat_'.$the_cat_id.' '.$level.'"><a href="'.get_category_link($the_cat_id).'" class="'.$choosen.'"><i class="icom icon-'.$slug_icon.'"></i>'.$the_cat->name.'</a>';  //liwrapper
                                if(!empty($catss)){
                                    echo '<ul class="links-mores">';
                                    foreach($catss as $the_cats){
                                        $the_cats_id = $the_cats->term_id;
                                        $catsss = get_categories(meta_query_categories($the_cats_id, 'ASC', 'seo_order'));
                                        if(!empty($catsss)) $level="trd_level";else $level="sec_child";
                                        if($the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single()) $choosen = "choosen 2rd";else $choosen="2nd";  // current choosen detect
                                        echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats->category_parent." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'">— '.$the_cats->name.'</a></li>';  //liwrapper
                                    }
                                    echo "</ul>";
                                }
                                echo '</li>';
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
        <div class="slider-menu_footer">
            <span class="footer-tips">找什么？搜搜看<i class="BBFontIcons check-hook"></i></span>
        </div>
    </div>
</div>
<div class="windowmask"></div>
<div class="windowmask-s2"></div>
<!--<div class="slider-menu-s" tabindex="1">-->
<!--    <div class="slider-menu-inside-s">-->
<!--        <div class="slider-menu_body">-->
<!--            <div class="body-main">-->
<!--                <ul>-->
<!--                    <li><a href="//googled.top" rel="nofollow" data-instant> Googled </a></li>-->
<!--                    <li><a href="//app.2broear.com/mikutap" rel="nofollow" data-instant> Mikutap </a></li>-->
<!--                    <li><a href="//design.2broear.com" rel="nofollow" data-instant> Design </a></li>-->
<!--                </ul>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->