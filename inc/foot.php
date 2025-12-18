<?php
    global $src_cdn;
?>
<script src="<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/js/main.js?v=<?php echo get_theme_info(); ?>"></script>
<script type="text/javascript">
    document.body.className = '';
    // 自动执行一次以更正缓存(after load main.js)
    if ( + getCookie('theme_manual')) { // use + force string to number
        console.log(`user theme_manual enabled, switch theme mode: ${getCookie('theme_mode')}..`);
        document.body.className = getCookie('theme_mode');// darkmode(); // automode();
    } else {
        console.debug(`user theme_manual disabled, switch theme auto-mode: ${getCookie('theme_mode')}..`);
        // reset to default(timezone) mode to clear EOCaches
        automode();
    }
    <?php
        global $cat;
        $vdo_poster_sw = get_option('site_video_poster_switcher');
        $datadance = get_option('site_animated_counting_switcher');
        $news_temp_id = get_cat_by_template('news','term_id');
        $note_temp_id = get_cat_by_template('notes','term_id');
        $acg_temp_id = get_cat_by_template('acg','term_id');
        if(is_single()){
            if(in_category($news_temp_id) || in_category($note_temp_id)){
                if($vdo_poster_sw) echo 'setupVideoPoster(3);'; // 截取设置当前页面所有视频 poster
    ?>
                //dynamicLoad
                asyncLoad('<?php echo $src_cdn; ?>/js/fancybox.umd.js', function(){
                    console.log('fancybox init.');
                    // gallery js initiate 'bodyimg' already exists in footer lazyload, use contimg insted.
                    let fancyImages = function(imgs){
                        if(imgs.length<=0) return;
                        for(let i=0,imgsLen=imgs.length;i<imgsLen;i++){
                            let eachimg = imgs[i],
                                eachpar = eachimg.parentNode,
                                fancybox = document.createElement("a");
                            // eachimg.src ? fancybox.setAttribute("href", eachimg.src) : fancybox.setAttribute("href", eachimg.dataset.src);
                            const datasrc = eachimg.dataset?.src || eachimg.src; 
                            fancybox.setAttribute("href", datasrc);
                            fancybox.setAttribute("data-fancybox","gallery");
                            fancybox.setAttribute("aria-label", "gallery_images");
                            fancybox.appendChild(eachimg);
                            eachpar.insertBefore(fancybox, eachpar.firstChild);
                        }
                    }
                    fancyImages(document.querySelectorAll(".news-article-container .content img"));
                });
    <?php
            }
            // marker
            if(get_option('site_marker_switcher')){
    ?>
                asyncLoad('<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/js/marker.js', function(){
                    // use keyword "new" to point to init method.
                    new marker.init({
                        static: {
                            // dataDelay: 3000,
                            // lineAnimate: false,
                            // useQuote: false,
                            // lineKeepTop: true,
                            lineColor: "var(--theme-color)",
                            lineColors: "transparent",
                            lineDegrees: "6",
                            dataStream: <?php $use_sse = get_option('site_stream_switcher');echo $use_sse ? '"'.$use_sse.'"' : "false"; ?>,
                            dataMax: "<?php echo get_option('site_marker_max', 3); ?>",
                            postId: "<?php global $post;echo $post->ID; ?>",
                            apiUrl: "<?php echo get_api_refrence('mark', true); //get_api_refrence('mark'); ?>",
                            md5Url: "<?php echo $src_cdn; ?>/js/md5.min.js",
                            avatar: "<?php echo get_option('site_avatar_mirror'); ?>",
                        },
                        class: {
                            blackList: ['chatGPT','article_index','ibox'], //'', 'chatGPT,article_index',
                        },
                        element: {
                            effectsArea: document.querySelector('.content'),
                            commentArea: document.querySelector('#vcomments textarea') || document.querySelector('#twikoo textarea') || document.querySelector('.wp_comment_box textarea'),
                            commentInfo: {
                                userNick: document.querySelector('input[name=nick]'),
                                userMail: document.querySelector('input[name=mail]'),
                            }
                        },
                    });
                });
    <?php
            }
        }
        if($cat){
            switch ($cat) {
                case get_cat_by_template('privacy','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(1);'; // 截取设置当前页面所有视频 poster
                    break;
                case $acg_temp_id:
                case cat_is_ancestor_of($acg_temp_id, $cat):
                case get_cat_by_template('archive','term_id'):
                    if($datadance) {
    ?>
                        if (window.CSS.registerProperty) {
                            window.CSS.registerProperty({
                                name: "--counter-num",
                                syntax: "<integer>",
                                inherits: false,
                                initialValue: 0,
                            });
                            document.head.getElementsByTagName('style')[0].textContent += `
    /*@property --counter-num {
        syntax: "<integer>";
        initial-value: 0;
        inherits: false;
    }*/
    @keyframes counts {
        0% {
            --counter-num: 0;
        }
        100% {
            --counter-num: var(--data-count);
        }
    }
    .win-top .counter h1,
    .win-top .counter h2 {
        transition: --counter-num 1s;
        counter-reset: counter-num var(--counter-num);
        animation: counts calc(var(--data-count) * 0.025s) forwards ease-in-out;
        -webkit-animation: counts calc(var(--data-count) * 0.025s) forwards ease-in-out;
    }
    .win-top .counter h1:before,
    .win-top .counter h2:before {
        content: counter(counter-num);
    }`;
                        } else {
                            dataDancing(document.querySelectorAll(".win-top .counter div"), "h1", 200, 25);  // dom reflow performance issue
                        }
    <?php
                    }
                    break;
                case get_cat_by_template('about','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(2);';  // 截取设置当前页面所有视频 poster 
    ?>
                    const list = document.querySelectorAll('.mbit .mbit_range li');
                    async_enqueue(list, true, (i)=>{
                        const item = list[i];
                        const span = item.querySelector('span');
                        const data = item.classList.contains('before') ? 'before' : 'after';
                        item.classList.add('active');
                        easeCounter(0, parseInt(span.dataset[data]), 3, 1, (num)=> {
                            // console.log(counted, item)
                            span.dataset[data] = parseInt(num);
                        }, (x) => Math.pow(1 - x, 2));
                    }, 200);
    <?php
                    break;
            }
        }
    ?>
    // window.onload = ()=> {
        const iframe = document.getElementById('panorama');
        if (iframe && iframe instanceof HTMLElement) {
            // iframe.frameborder = 'no';
            setTimeout(window.queueMicrotask(()=> {
                iframe.width = '100%';
                iframe.height = '100%';
                iframe.src = 'https://node.2broear.com/'; //indexs.html
            }), 0);
            ;
        }
    // }
</script>
<svg style="display: none;">
    <defs>
        <filter id="x" height="500%">
            <feTurbulence baseFrequency="0.01 0.02" numOctaves="2" result="t0"></feTurbulence>
            <feDisplacementMap in="SourceGraphic" in2="t0" result="d0" scale="5"></feDisplacementMap>
            <feComposite in="SourceGraphic" in2="d0" operator="atop" result="0"></feComposite>
            <feTurbulence baseFrequency="1" numOctaves="2" result="t1"></feTurbulence>
            <feDisplacementMap in="0" in2="t1" result="d1" scale="2"></feDisplacementMap>
            <feComposite in="0" in2="d1" operator="atop" result="1"></feComposite>
            <feOffset dx="-3" dy="-3" in="1"></feOffset>
        </filter>
    </defs>
</svg>