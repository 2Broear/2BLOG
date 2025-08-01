<?php
    // 谷歌 Adscene 广告简码
    if (get_option('site_ads_switcher')) {
        function custom_adscene_sidebar_square_shortcode($atts) {
            $autoWidth = isset($atts['autoWidth']) ? $atts['autoWidth'] : true;
            return '<div class="adscene"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <!-- 方形侧栏 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="5163357376"
                     data-ad-format="auto"
                     data-full-width-responsive="' . $autoWidth . '"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
        }
        function custom_adscene_sidebar_long_shortcode($atts) {
            $autoWidth = isset($atts['autoWidth']) ? $atts['autoWidth'] : true;
            return '<div class="adscene"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <!-- 纵向侧栏 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="9174538970"
                     data-ad-format="auto"
                     data-full-width-responsive="' . $autoWidth . '"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
        }
        function custom_adscene_list_richtext_shortcode($atts) {
            return '<article class="adscene"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <!-- 富文本列表 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-format="fluid"
                     data-ad-layout-key="-e0+6t-24-4z+h3"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="6093295669"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></article>';
        }
        function custom_adscene_list_context_shortcode($atts) {
            return '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <!-- 纯文本列表 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-format="fluid"
                     data-ad-layout-key="-gw-3+1f-3d+2z"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="1000751085"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>';
        }
        function custom_adscene_article_embed_shortcode($atts) {
            return '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="8804053635"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>';
        }
        
        // ads.0.0
        function add_adscene_shortcodes() {
            add_shortcode('adscene_sidebar_square', 'custom_adscene_sidebar_square_shortcode');
            add_shortcode('adscene_sidebar_long', 'custom_adscene_sidebar_long_shortcode');
            add_shortcode('adscene_list_richtext', 'custom_adscene_list_richtext_shortcode');
            add_shortcode('adscene_list_context', 'custom_adscene_list_context_shortcode');
            add_shortcode('adscene_article_embed', 'custom_adscene_article_embed_shortcode');
        }
        
        // if (is_single()) {
        //     if (get_option('site_ads_arsw')) add_adscene_shortcodes();
        // } else {
            add_adscene_shortcodes();
        // }
    }
    
    function custom_video_shortcode($atts){
        $src = isset($atts['src']) ? $atts['src'] : '/';
        $poster = isset($atts['poster']) ? $atts['poster'] : $src;
        $autoplay = isset($atts['autoplay']) ? $atts['autoplay'] : 'autoplay';
        $muted = isset($atts['muted']) ? $atts['muted'] : 'muted';
        $loop = isset($atts['loop']) ? $atts['loop'] : 'loop';
        $type = isset($atts['type']) ? $atts['type'] : 'video/mp4';
        $is_video = preg_match("/mp4|mov|avi/i", substr(strrchr($src,'.'),1));
        if (!$is_video) $src = '';
        return '<video src="' . $src . '" poster="' . $poster . '" preload ' . $autoplay . ' ' . $muted . ' ' . $loop . ' x5-video-player-type="h5" controlsList="nofullscreen nodownload" playsinline -webkit-playsinline></video>'; //<source  src="' . $src . '" type="' . $type . '"></source>
    }
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
        return '<iframe class="'.$class.'" src="//bilibili.com/blackboard/html5mobileplayer.html?bvid='.$vid.'&t=0&hideCoverInfo=1&danmaku=0" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>';
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
        $len = isset($atts['len']) ? $atts['len'] : 80;
        $title = get_the_title($pid);
        $content = get_post($pid)->post_content; //get_the_excerpt($pid); //custom_excerpt(99, true);
        $excerpt = mb_substr(strip_tags($content), 0, $len).'...';
        $author = get_option('site_nick') ? get_option('site_nick') : get_bloginfo('name');
        $avatar = false;
        if(isset($atts['avatar'])){
            $icon = get_option('site_avatar') ? get_option('site_avatar') : get_site_icon_url();
            $avatar = '<em style="background:url('.$icon.') center center /cover;width: 23px;height: 23px;border-radius: 50%;display: inline-block;vertical-align: middle;"></em>';
        }
        return '<div class="ibox quotes"><div class="iboxes" style="background:url() center center /cover;"><img src="'.get_postimg(0,$pid,true).'" alt="'.$title.'"><h3><a href="'.get_the_permalink($pid).'" target="_blank">'.$title.'</a></h3><div class="content"><p>'.$excerpt.'</p></div><mark>'.$avatar.' '.$author.' '.get_the_time('d/m/Y', $pid).' '.get_tag_list($pid, 1, "/").' | '.getPostViews($pid).' views.</mark></div></div>';
    }
    
    // 注册短代码
    add_shortcode('custom_video', 'custom_video_shortcode');
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
?>