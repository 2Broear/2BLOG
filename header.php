<?php
    global $img_cdn;
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
                        $curcat = get_the_category() ? get_the_category()[0] : false;
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
                                if(is_a($prev_post , 'WP_Post')){
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
                        <!--<li><small style="padding: 0 15px;display: none;">oop<strong>S</strong>ays..</small></li>-->
                    </ul>
                    <div class="nav-slider">
                        <span id="slide-target"></span>
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
    <?php //require_once(TEMPLATEPATH. '/mobile.php'); ?>
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