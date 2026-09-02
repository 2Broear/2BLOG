<?php
    // 评论弹幕
    if (get_option('site_comment_barrage')) {
        add_shortcode('comment_barrage', 'custom_comment_barrage_shortcode');
        function custom_comment_barrage_shortcode($atts) {
            // $count = isset($atts['count']) ? $atts['count'] : 50;
            $row = isset($atts['row']) ? $atts['row'] : 10;
            $min = isset($atts['min']) ? $atts['min'] : 5;
            $max = isset($atts['max']) ? $atts['max'] : 20;
            $speed = isset($atts['speed']) ? $atts['speed'] : 15;
            $cat_id = isset($atts['cid']) ? $atts['cid'] : 0;
            $post_id = isset($atts['pid']) ? $atts['pid'] : 0;
            // $post_only = isset($atts['post']) ? $atts['post'] : false;
            $tag_barrage = isset($atts['tag']) ? $atts['tag'] : 0;
            $excludes = isset($atts['excludes']) ? $atts['excludes'] : '';
            $thoughtful = isset($atts['thoughtful']) ? $atts['thoughtful'] : 0;
            $ai_comments = isset($atts['ai']) ? 0 : 1;
            // if ($post_only && is_single()) {
            //     global $post;
            //     $post_id = $post->ID;
            // }
            return '
<div id="comment-barrage-container"></div>
<style>
#comment-barrage-container::before {
    /*content: "";*/
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: rgb(255 255 255 / 25%);
    z-index: 3;
}
#comment-barrage-container {
    position: absolute;
    width: 100%;
    height: 88%;
    transform: translate(-50%, -50%);
    top: 50%;
    left: 50%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}
body.dark #comment-barrage-container.tag .barrage-item {
    color: var(--preset-c);
}
#comment-barrage-container.tag .barrage-item {
    color: var(--preset-5a);
    padding: 4px 15px 5px;
    mask: none;
}
#comment-barrage-container.tag .barrage-item i {
    opacity: .75;
}

body.dark .barrage-item {
    color: var(--preset-f);
    background: linear-gradient(45deg, var(--preset-2b), transparent);
    box-shadow: var(--preset-f) 1px 2.2px 1px -1.8px inset, transparent -1px -2.2px 1px -1.8px inset;
    border-color: transparent;
}
.barrage-item {
    position: absolute;
    white-space: nowrap;
    font-size: var(--min-size);
    color: var(--preset-2b);
    padding: 4px 16px 4px 4px;
    border-radius: 20px;
    pointer-events: auto;
    cursor: default;
    animation-name: barrageMove;
    animation-timing-function: linear;
    animation-iteration-count: 1;
    animation-fill-mode: forwards;
    z-index: 1;
    opacity: 1;
    user-select: none;
    animation-duration: var(--duration);
    /*
    transition: background 0.2s;
    background: var(--preset-4b);
    box-shadow: rgba(0,0,0,0.12) 0 1px 18px;
    */
    border: 1px solid var(--preset-e);
    background: linear-gradient(45deg, var(--preset-f), transparent);
    backdrop-filter: blur(10px) saturate(1);
    mask: linear-gradient(45deg, var(--preset-2b) 66%, transparent 88%);
    mask: -webkit-linear-gradient(0deg, var(--preset-2b) 66%, transparent 88%);
}

.barrage-item:hover {
    /*background: var(--preset-2b);*/
    animation-play-state: paused !important;
    opacity: 1!important;
    z-index: 9999 !important;
    mask: none;
}

#comment-barrage-container.slow-others .barrage-item:not(:hover) {
    /*animation-play-state: paused !important;*/
}

.barrage-item img,
.barrage-item a,
.barrage-item strong {
    vertical-align: middle;
}
.barrage-item img {
    width: 25px;
    height: 25px;
    display: inline-block!important;
    border-radius: 50%!important;
    margin: 0 2px 0 0!important;
    border: 2px solid var(--preset-2bs);
}
.barrage-item a:hover {
    text-decoration: underline;
    /*color: var(--theme-color);
    font-weight: bold;
    text-decoration: none;*/
}
.barrage-item a {
    display: inline-block;
    max-width: 50em;
    overflow: hidden;
    text-overflow: ellipsis;
    color: inherit;
}
.barrage-item a#content {
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
    const API_URL = "/wp-json/two-ber/v1/comment-barrage?post_id='.$post_id.'&cid='.$cat_id.'&tag='.$tag_barrage.'&thoughtful='.$thoughtful.'&excludes='.$excludes.'";
    const MAX_VISIBLE = '.$max.';
    const TRACK_COUNT = '.$row.';
    const MIN_BARRAGE = '.$min.';
    let pendingItems = [];
    let activeCount = 0;
    let trackNextAvailable = new Array(TRACK_COUNT).fill(0);
    let scheduleTimer = null;
    let isFetching = false;
    let allDone = false;
    let hasPreloaded = false;       // 防止重复预加载

    '.($tag_barrage ? "container.classList.add('tag');" : '').'
    function getEarliestTrack() {
        let minTime = Infinity;
        let minIndex = 0;
        for (let i = 0; i < TRACK_COUNT; i++) {
            if (trackNextAvailable[i] < minTime) {
                minTime = trackNextAvailable[i];
                minIndex = i;
            }
        }
        const now = Date.now();
        const wait = Math.max(0, minTime - now);
        return { index: minIndex, wait: wait };
    }

    function scheduleNext() {
        if (pendingItems.length === 0) return;
        if (activeCount >= MAX_VISIBLE) return;
    
        const { index, wait } = getEarliestTrack();
        if (scheduleTimer) clearTimeout(scheduleTimer);
        scheduleTimer = setTimeout(() => {
            scheduleTimer = null;
            // 从队列中取出有效项（跳过 AI 评论）
            let item = null;
            while (pendingItems.length > 0) {
                const candidate = pendingItems.shift();
                if ('.$ai_comments.' && candidate._ai_comment) {
                    continue; // 丢弃 AI 评论
                }
                item = candidate;
                break;
            }
            if (item) {
                spawnBarrage(item, index);
            } else {
                // 队列已空或全部被过滤，重新尝试调度（稍后 fetch 会补充）
                scheduleNext();
            }
        }, wait);
    }

    function spawnBarrage(item, trackIndex) {
        trackNextAvailable[trackIndex] = Infinity;
        activeCount++;
    
        const el = document.createElement("div");
        el.className = "barrage-item";
    
        const base = (100 / TRACK_COUNT) * trackIndex;
        const offset = Math.random() * 4;
        el.style.top = (base + offset) + "%";
    
        // ========== 快速弹幕设置 ==========
        const FAST_PROBABILITY = 0.03;                // 3% 概率
        const isFast = Math.random() < FAST_PROBABILITY;
        
        let duration;
        if (isFast) {
            duration = 2 + Math.random() * 3;          // 快速弹幕持续时间 2~5 秒
            el.classList.add("barrage-fast");          // 可选：添加特殊样式
        } else {
            duration = '.$speed.' + Math.random() * 10; // 正常速度
        }
        
        el.style.setProperty("--duration", duration + "s");
        // ================================
        '.($tag_barrage ? 'let html = `<a href="${item.link}" target="_blank"><i>tag</i> <strong>#${item.name}</strong></a>`' : '
        el.title = `${item.content}\n\n——该评论取自《${item.post_title}》，发布于 ${item.date}`;
        let html = `<a href="${item.author_url}" target="_blank"><img src="${item.avatar}" alt=""> <strong>${item.author}</strong>：</a><a id="content" href="${item.post_url}#comment-${item.id || 0}" target="_self">`;
        if (item.parent_author) html += `@${item.parent_author}，`;
        html += `${item.content}</a>`;').'
    
        el.innerHTML = html;
        container.appendChild(el);
    
        const width = el.offsetWidth;
        const viewWidth = window.innerWidth;
        const visibleProgress = width / (viewWidth + width);
        const visibleDelay = duration * visibleProgress * 1000;
    
        const now = Date.now();
        const randomGap = 1000 + Math.random() * 2000;
        
        // 快速弹幕释放轨道更快
        if (isFast) {
            trackNextAvailable[trackIndex] = now + visibleDelay + 500; // 仅等待 0.5 秒
        } else {
            trackNextAvailable[trackIndex] = now + visibleDelay + randomGap;
        }
    
        el.addEventListener("pointerenter", () => {
            // container.classList.add("slow-others");
            // el.style.background = "rgba(0, 0, 0, 0.9)";
            el.style.zIndex = "9999";
        });
    
        el.addEventListener("pointerleave", () => {
            // container.classList.remove("slow-others");
            // el.style.background = "";
            el.style.zIndex = "";
        });
    
        el.addEventListener("animationend", () => {
            el.remove();
            activeCount--;
            scheduleNext();
            if (pendingItems.length === 0 && activeCount === 0 && allDone) {
                allDone = false;
                hasPreloaded = false;
                setTimeout(fetchData, 1000);
            }
        });
    
        if (pendingItems.length === 0 && allDone && !isFetching && !hasPreloaded) {
            hasPreloaded = true;
            setTimeout(fetchData, 2000);
        }
    
        scheduleNext();
    }

    function createItems(data) {
        const items = data.slice(0, 50);
        if (!items.length || items.length <= MIN_BARRAGE) return;
        pendingItems = pendingItems.concat(items);
        allDone = true;
        hasPreloaded = false;      // 新数据到达，重置预加载标志
        scheduleNext();
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