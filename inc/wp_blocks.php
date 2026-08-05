<?php
    // 评论弹幕
    if (get_option('site_comment_barrage')) {
        add_shortcode('comment_barrage', 'custom_comment_barrage_shortcode');
        function custom_comment_barrage_shortcode($atts) {
            $count = isset($atts['count']) ? $atts['count'] : 50;
            $post_id = isset($atts['pid']) ? $atts['pid'] : 0;
            $row = isset($atts['row']) ? $atts['row'] : 10;
            $thoughtful = isset($atts['thoughtful']) ? $atts['thoughtful'] : false;
            return '
<div id="comment-barrage-container"></div>
<style>
#comment-barrage-container {
    position: absolute;
    width: 100%;
    height: 100%;
    max-height: 88%;
    transform: translate(-50%, -50%);
    top: 50%;
    left: 50%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.barrage-item {
    position: absolute;
    white-space: nowrap;
    font-size: var(--min-size);
    background: var(--preset-4b);
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    pointer-events: auto;
    cursor: default;
    animation-name: barrageMove;
    animation-timing-function: linear;
    animation-iteration-count: 1;
    animation-fill-mode: forwards;
    z-index: 1;
    opacity: 1;
    transition: background 0.2s;
    user-select: none;
    animation-duration: var(--duration);
}

.barrage-item:hover {
    background: var(--preset-2b);
    animation-play-state: paused !important;
    z-index: 9999 !important;
}

#comment-barrage-container.slow-others .barrage-item:not(:hover) {
    /*animation-play-state: paused !important;*/
}

.barrage-item img {
    width: 20px; height: 20px;
    vertical-align: middle;
    border-radius: 50%;
    margin-right: 6px;
}
.barrage-item a:hover {
    color: var(--theme-color)
}
.barrage-item a {
    display: inline-block;
    max-width: 50em;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: text-top;
    color: inherit;
    opacity: .75;
}

.barrage-item .post-link-tooltip {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-80%);
    /*background: #333;*/
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    font-size: 12px;
    z-index: 10;
    pointer-events: auto;
}
.barrage-item:hover {
    /*opacity: 1;*/
    z-index: 999;
}
.barrage-item:hover .post-link-tooltip {
    display: block;
}

@keyframes barrageMove {
    from { 
        transform: translateX(100vw); 
        /*opacity: 1;*/
    } to  { 
        transform: translateX(-100%);
        /*opacity: 0.5;*/
    }
}
</style>
<script>
(function() {
    const container = document.getElementById("comment-barrage-container");
    const API_URL = "/wp-json/two-ber/v1/comment-barrage?post_id='.$post_id.'";
    const MAX_VISIBLE = 20;          // 同屏最大弹幕数
    const TRACK_COUNT = '.$row.';          // 轨道数
    let pendingItems = [];
    let activeCount = 0;
    let trackOccupied = new Array(TRACK_COUNT).fill(false);
    let isFetching = false;
    let allDone = false;

    // 找一个空闲轨道索引，若全占用则返回 -1
    function findFreeTrack() {
        for (let i = 0; i < TRACK_COUNT; i++) {
            if (!trackOccupied[i]) return i;
        }
        return -1;
    }

    function spawnBarrage(item) {
        const trackIndex = findFreeTrack();
        if (trackIndex === -1) {
            // 所有轨道繁忙，100ms后重试
            setTimeout(() => spawnBarrage(item), 100);
            return;
        }

        // 标记轨道占用
        trackOccupied[trackIndex] = true;
        activeCount++;

        const el = document.createElement("div");
        el.className = "barrage-item";

        // 垂直位置
        const base = (100 / TRACK_COUNT) * trackIndex;
        const offset = Math.random() * 4;
        el.style.top = (base + offset) + "%";

        // 动画时长 15~25 秒
        const duration = 10 + Math.random() * 10;
        el.style.setProperty("--duration", duration + "s");
        el.title = `该评论来自：${item.post_title}`;

        let html = `<img src="${item.avatar}" alt=""> <strong>${item.author}</strong>：<a href="${item.post_url}#comment-${item.id || 0}" target="_blank" title="${item.content}">`;
        if (item.parent_author) html += `@${item.parent_author}，`;
        html += `${item.content}</a>`; /*<span class="post-link-tooltip">
            ${item.post_title}</span>*/

        el.innerHTML = html;

        // 动画结束：释放轨道，补充新弹幕
        el.addEventListener("animationend", () => {
            el.remove();
            trackOccupied[trackIndex] = false;
            activeCount--;
            replenish();
        });

        // 悬停暂停其他弹幕
        el.addEventListener("pointerenter", () => {
            container.classList.add("slow-others");
        });
        el.addEventListener("pointerleave", () => {
            container.classList.remove("slow-others");
        });

        container.appendChild(el);
        // 播放下一个弹幕前短暂延迟，避免瞬间充满
        setTimeout(replenish, 200);
    }

    function replenish() {
        // 当屏幕未满且有待播项，并且有空闲轨道时，播放下一项
        while (pendingItems.length > 0 && activeCount < MAX_VISIBLE && findFreeTrack() !== -1) {
            const item = pendingItems.shift();
            spawnBarrage(item);
        }
        // 全部播完且无活跃弹幕，请求新数据
        if (pendingItems.length === 0 && activeCount === 0 && allDone) {
            allDone = false;
            setTimeout(fetchData, 2000);
        }
    }

    function createItems(data) {
        const items = data.slice(0, 50);
        if (!items.length) return;
        pendingItems = items;
        allDone = true;
        replenish();
    }

    function fetchData() {
        if (isFetching) return;
        isFetching = true;
        fetch(API_URL)
            .then(res => res.json())
            .then(data => createItems(data))
            .catch(() => {})
            .finally(() => { isFetching = false; });
    }

    fetchData();
})();
</script>';
        }
    }
    // 谷歌 Adsense 广告简码
    if (get_option('site_ads_switcher')) {
        function custom_adsense_sidebar_square_shortcode($atts) {
            $autoWidth = isset($atts['autoWidth']) ? $atts['autoWidth'] : true;
            return '<div class="adsense fade-item"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
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
        function custom_adsense_sidebar_long_shortcode($atts) {
            $autoWidth = isset($atts['autoWidth']) ? $atts['autoWidth'] : true;
            return '<div class="adsense fade-item"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
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
        function custom_adsense_list_richtext_shortcode($atts) {
            return '<article class="adsense fade-item"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
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
        function custom_adsense_list_context_shortcode($atts) {
            return '<div class="adsense fade-item"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <!-- 纯文本列表 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-format="fluid"
                     data-ad-layout-key="-gw-3+1f-3d+2z"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="1000751085"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
        }
        function custom_adsense_article_embed_shortcode($atts) {
            return '<div class="adsense fade-item"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7117066844426823" crossorigin="anonymous"></script>
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="ca-pub-7117066844426823"
                     data-ad-slot="8804053635"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
        }
        
        // ads.0.0
        function add_adsense_shortcodes() {
            add_shortcode('adsense_sidebar_square', 'custom_adsense_sidebar_square_shortcode');
            add_shortcode('adsense_sidebar_long', 'custom_adsense_sidebar_long_shortcode');
            add_shortcode('adsense_list_richtext', 'custom_adsense_list_richtext_shortcode');
            add_shortcode('adsense_list_context', 'custom_adsense_list_context_shortcode');
            add_shortcode('adsense_article_embed', 'custom_adsense_article_embed_shortcode');
        }
        
        // if (is_single()) {
        //     if (get_option('site_ads_arsw')) add_adsense_shortcodes();
        // } else {
            add_adsense_shortcodes();
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
        return '<div class="ibox"><div class="iboxes magnetic" data-magnet-scale="1" data-magnet-step="0.05"><img src="'.$img.'" alt="'.$title.'" decoding="async"><mark>'.$title.'</mark></div></div>';
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
        return '<div class="ibox quotes"><div class="iboxes magnetic" data-magnet-scale="1" data-magnet-step="0.05"><img src="'.get_postimg(0,$pid,true).'" alt="'.$title.'"><h3><a href="'.get_the_permalink($pid).'" target="_blank">'.$title.'</a></h3><div class="content"><p>'.$excerpt.'</p></div><mark>'.$avatar.' '.$author.' '.get_the_time('d/m/Y', $pid).' '.get_tag_list($pid, 1, "/").' | '.getPostViews($pid).' views.</mark></div></div>';
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