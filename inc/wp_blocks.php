<?php
    function custom_netease_shortcode($atts){
        $id = isset($atts['id']) ? $atts['id'] : 'id';
        $width = isset($atts['width']) ? $atts['width'] : '';
        $height = isset($atts['height']) ? $atts['height'] : '350';
        $class = isset($atts['class']) ? $atts['class'] : 'netease_embed';
        return '<iframe class="'.$class.'" src="//music.163.com/outchain/player?id='.$id.'&&type=0&auto=0" width="'.$width.'" height="'.$height.'" frameborder="no" marginwidth="0" marginheight="0" title="163"></iframe>';
    }
    function custom_bilibili_shortcode($atts){
        $vid = isset($atts['vid']) ? $atts['vid'] : 'vid';
        $class = isset($atts['class']) ? $atts['class'] : 'bilibili_embed';
        return '<iframe class="'.$class.'" src="//player.bilibili.com/player.html?bvid='.$vid.'&autoplay=0&t=0" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>';
    }
    function custom_title_shortcode($atts, $content = null) {
        $statu = isset($atts['statu']) ? $atts['statu'] : 'normal';
        $title = isset($atts['title']) ? $atts['title'] : 'Example';
        $tag = isset($atts['tag']) ? $atts['tag'] : 'h3';
        return "<span id='normal' class='$statu'><$tag>$title</$tag></span>";
    }
    function custom_imgbox_shortcode($atts, $content = null) {
        $img = isset($atts['img']) ? $atts['img'] : '';
        $title = isset($atts['title']) ? $atts['title'] : 'No Text';
        return '<div class="ibox"><div class="iboxes"><img src="'.$img.'" alt="'.$title.'" decoding="async"><mark>'.$title.'</mark></div></div>';
    }
    function custom_sidebar_ad_shortcode($atts){
        $sup = isset($atts['sup']) ? $atts['sup'] : '中意此款主题吗';
        $title = isset($atts['title']) ? $atts['title'] : '';
        $sub = isset($atts['sub']) ? $atts['sub'] : '现在体验<b> BETA </b>版';
        $src= isset($atts['src']) ? $atts['src'] : 'https://github.com/2Broear/2BLOG';
        $img = isset($atts['img']) ? $atts['img'] : 'https://img.2broear.com/2022/08/2BLOG-rainbow666.jpg';
        return '<div class="countdown-box" style="margin-bottom: 15px"><a href="'.$src.'" target="_blank" title="'.$title.'"><div id="countdown" class="countdowns" style="background-image:url('.$img.')"><p class="title">'.$sup.'</p><div class="time"><span class="timesup">'.$title.'</span></div><p class="today" style="text-decoration: underline;">'.$sub.'</p></div><sup id="ads">ads</sup></a></div>';
    }
    function custom_article_embed_shortcode($atts){
        switch (true) {
            case isset($atts['pid']):
                $url = get_the_permalink($atts['pid']);
                break;
            case isset($atts['url']):
                $url = $atts['url'];
                break;
            default:
                $url = get_the_permalink(1);
                break;
        }
        return '<iframe style="width: 100%;min-height: 200px;" src="'.$url.'/embed#?secret=" scrolling="auto" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>';
    }
    function custom_article_quote_shortcode($atts, $content = null) {
        switch (true) {
            case isset($atts['pid']):
                $pid = $atts['pid'];
                break;
            case isset($atts['url']):
                $pid = url_to_postid($atts['url']);
                break;
            default:
                global $post;
                $pid = $post->ID;
                break;
        }
        $len = isset($atts['length']) ? $atts['length'] : 80;
        $title = get_the_title($pid);
        $content = get_post($pid)->post_content; //get_the_excerpt($pid); //custom_excerpt(99, true);
        $excerpt = mb_substr(strip_tags($content), 0, $len).'...';
        $author = get_option('site_nick') ? get_option('site_nick') : get_bloginfo('name');
        $avatar = false;
        if(isset($atts['avatar'])){
            $icon = get_option('site_avatar') ? get_option('site_avatar') : get_site_icon_url();
            $avatar = '<em style="background:url('.$icon.') center center /cover;width: 23px;height: 23px;border-radius: 50%;display: inline-block;vertical-align: middle;"></em>';
        }
        return '<div class="ibox quotes"><div class="iboxes" style="background:url() center center /cover;"><img src="'.get_postimg(0,$pid,true).'" alt="'.$title.'"><h3><a href="'.get_the_permalink($pid).'" target="_blank">'.$title.'</a></h3><div class="content"><p>'.$excerpt.'</p></div><mark>'.$avatar.' '.$author.' '.get_the_time('d/m/Y').' '.get_tag_list($pid, 1, "/").' | '.getPostViews($pid).' views.</mark></div></div>';
    }
    // 注册短代码
    add_shortcode('netease_embed', 'custom_netease_shortcode');
    add_shortcode('bilibili_embed', 'custom_bilibili_shortcode');
    add_shortcode('custom_title', 'custom_title_shortcode');
    add_shortcode('custom_imgbox', 'custom_imgbox_shortcode');
    add_shortcode('sidebar_ads', 'custom_sidebar_ad_shortcode');
    add_shortcode('article_quote', 'custom_article_quote_shortcode');
    add_shortcode('article_embed', 'custom_article_embed_shortcode');
    
    function enqueue_block_script() {
      wp_enqueue_script('custom-block-script', get_theme_file_uri('/inc/themes/custom_blocks.js'),  array('wp-blocks', 'wp-editor', 'wp-element'), filemtime(get_theme_file_path('/inc/themes/custom_blocks.js')) // 替换为实际脚本文件的路径
      );
    }
    add_action('enqueue_block_editor_assets', 'enqueue_block_script');
    
    // function register_pandastudio_tips() {
    //     wp_register_script(
    //         'pandastudio-tips',
    //         get_theme_file_uri().'/inc/themes/tips.js',
    //         array( 'wp-blocks', 'wp-element' )
    //     );
     
    //     wp_register_style(
    //         'pandastudio-tips',
    //         get_theme_file_uri().'/inc/themes/tips.css',
    //         array( 'wp-edit-blocks' )
    //     );
     
    //     register_block_type( 'pandastudio/tips', array(
    //         'editor_script' => 'pandastudio-tips',
    //         'editor_style'  => 'pandastudio-tips',
    //     ) );
    // }
    // if (function_exists('register_block_type')) {
    //     add_action( 'init', 'register_pandastudio_tips' );
    // }
?>