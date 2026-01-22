<?php
    global $src_cdn;
?>
<script src="<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/js/main.js?v=<?php echo get_theme_info(); ?>"></script>
<script type="text/javascript">
    console.info("<?php echo get_num_queries().'次查询，耗时'.timer_stop(0).'秒。'; ?>");
    // 自动执行一次以更正缓存(after load main.js)
    // document.body.className = '';  // reset to default(timezone) mode to clear EOCaches
    if ( + getCookie('theme_manual')) { // use + force string to number
        document.body.className = getCookie('theme_mode');// darkmode(); // automode();
        console.log(`theme_mode[manual] switch-color-scheme: ${getCookie('theme_mode')}..`);
    } else {
        automode();
    };
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
                            document.head.getElementsByTagName('style')[0].textContent += `/*@property --counter-num {syntax: "<integer>";initial-value: 0;inherits: false;}*/@keyframes counts {0% {--counter-num: 0;}100% {--counter-num: var(--data-count);}}.win-top .counter h1, .win-top .counter h2 {transition: --counter-num 1s;counter-reset: counter-num var(--counter-num);animation: counts calc(var(--data-count) * 0.025s) forwards ease-in-out;-webkit-animation: counts calc(var(--data-count) * 0.025s) forwards ease-in-out;}.win-top .counter h1:before, .win-top .counter h2:before {content: counter(counter-num);}`;
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
<?php
    $acgcid = get_cat_by_template('acg','term_id');
    $acgpage = $acgcid==$cat || cat_is_ancestor_of($acgcid, $cat);
    if ($acgpage) {
?>
        const worker = new Worker('<?php echo custom_cdn_src(0,1); ?>/js/worker.js');
        const getAverageRGB = function(imgEl){var blockSize=5,defaultRGB={r:255,g:255,b:255},canvas=document.createElement('canvas'),context=canvas.getContext&&canvas.getContext('2d'),data,width,height,i=-4,length,rgb={r:0,g:0,b:0},count=0;if(!context){return defaultRGB}height=canvas.height=imgEl.naturalHeight||imgEl.offsetHeight||imgEl.height;width=canvas.width=imgEl.naturalWidth||imgEl.offsetWidth||imgEl.width;context.drawImage(imgEl,0,0);try{data=context.getImageData(0,0,width,height)}catch(e){return defaultRGB}length=data.data.length;while((i+=blockSize*4)<length){++count;rgb.r+=data.data[i];rgb.g+=data.data[i+1];rgb.b+=data.data[i+2]}rgb.r=~~(rgb.r/count);rgb.g=~~(rgb.g/count);rgb.b=~~(rgb.b/count);return rgb},
            setupBlurColor = function(img, tarEl) {
                if (!tarEl) return;
                if (img instanceof HTMLImageElement) {
                    const rgb = getAverageRGB(img);
                    const rgba = `${rgb['r']} ${rgb['g']} ${rgb['b']} / 88%`;
                    tarEl.setAttribute('style','background-color: rgb('+rgba+')');
                    return;
                }
                worker.postMessage({
                    img: img,  // HTMLImageElement object could not be cloned on 'Worker'
                });
                worker.onmessage = function(event) {
                    const { url: objectURL } = event.data;
                    let tempimg = new Image();
                    tempimg.src = objectURL;
                    tempimg.setAttribute('crossorigin', 'Anonymous');
                    const rgb = getAverageRGB(tempimg);
                    const rgba = `${rgb['r']} ${rgb['g']} ${rgb['b']} / 88%`;
                    tarEl.setAttribute('style','background-color: rgb('+rgba+')');
                    // URL.revokeObjectURL(objectURL);
                }
                worker.onerror = function(event){
                    console.warn("ERROR: " + event.filename + " (" + event.lineno + "): " + event.message);
                }
            };
<?php
    }
    if (get_option('site_async_switcher') && !is_single()) {
?>
        function load_ajax_posts(t,type,limit,callback,action=false,url=false,params=false){
            const type_acg = "acg",
                  dis_class = "disabled",
                  load_clss = 'loading',
                  load_done = type===type_acg ? "" : "已加载全部";
            if (t.classList.contains(load_clss) || t.classList.contains(dis_class)) {
                console.warn('another load in progress..');
                return;
            }
            let tp = t.parentElement,
                tpp = tp.parentElement,
                cid = parseInt(t.dataset.cid),
                years = parseInt(t.dataset.year), // add-opts for archive
                loads = parseInt(t.dataset.load),
                counts = parseInt(t.dataset.counts) || 0,
                clicks = parseInt(t.dataset.click) || 0;
            // console.log(loads, counts);
            if(loads >= counts){
                t.innerText = load_done;
                t.classList.add(dis_class);  // add-opts for weblog
                tpp.classList.add(dis_class);
                return;
            }
            clicks++;
            t.innerText = type===type_acg ? "Loading.." : "加载中..";
            t.classList.add(load_clss, dis_class);  // add-opts archive (disable click)
            t.setAttribute('data-click', clicks);
            url = url ? url : "<?php echo admin_url('admin-ajax.php'); ?>";
            params = params ? params : parse_ajax_parameter({
                "action": action || "ajaxGetPosts",
                "key": years, // add-opts for archive params
                "cid": cid || 0,
                "limit": limit,
                "offset": limit*clicks,
                "type": type,
                _ajax_nonce: t.dataset.nonce,
            }, true);
            send_ajax_request("GET", url, params, function(res){
                    t.innerText = type===type_acg ? "" : "加载更多";
                    t.classList.remove(load_clss, dis_class);  // add-opts for archive (enable click)
                    var posts_array = JSON.parse(res),
                        posts_count = posts_array.length,
                        loads_count = loads + posts_count,
                        load_box = tp, //t.parentElement.parentElement.parentElement;
                        last_offset = 0;
                    // console.log(loads, posts_count)
                    switch(type){
                        case type_acg:
                            load_box = getParByCls(t, 'loadbox');
                            break;
                        case 'archive':
                            load_box = archive_tree.querySelector('.list_'+years);
                            last_offset = load_box.lastChild.offsetTop;
                            break;
                        default:
                            break;
                    }
                    loads_count>=counts ? t.setAttribute('data-load', counts) :  t.setAttribute('data-load', loads_count);  // update current loaded(limit judge)
                    if(callback&&typeof callback === 'function') {
                        if(posts_count>=1) callback(posts_array, load_box, last_offset);
                    }
                    // compare updated loads
                    if(parseInt(t.dataset.load) >= counts){
                        t.classList.add(dis_class); // for archive
                        tpp.classList.add(dis_class); // for weblog/acg
                        t.innerText = load_done;
                    }
                }, function(err){
                    t.innerText = err+' err occured';
                }
            );
        }
<?php
    }
?>
</script>
<script type="module">
    try {
        import("<?php echo $src_cdn; ?>/js/utils.js").then((mod)=> {
            const { _EventBus, _Closure, _Basics, VisibilityObserver } = mod;
        <?php
            // nav slider
            if (get_option('site_nav_slider_switcher')) {
        ?>
            const Basics = new _Basics();
            const navContainer = document.querySelector('.main-nav');
            if (Basics.detects.validDom(navContainer)) {
                const navSlider = navContainer?.querySelector('.nav-slider');
                const EventBus = new _EventBus();
                const Closure = new _Closure();
                const Statics = {
                    tag: 'A',
                    show: 'show',
                    move: 'move',
                    duration: 250
                };
                let Transfer = (dom, x = 0, y = 0, width = 0, height = 0, callback = null)=> {
                    if (!Basics.detects.validDom(dom)) return;  // invalid dom
                    dom.style.cssText = `transform: translate(${x}px, ${y}px); transition-duration: ${Statics.duration}ms`;
                    if (width || height) {
                        dom.style.width = width + 'px';
                        dom.style.height = height + 'px';
                    };
                    callback?.();
                };
                let Engager = (e, target = null, callback = null)=> {
                    if (Basics.detects.validDom(target)) {
                        const targetRect = target.getBoundingClientRect();
                        Transfer(navSlider, targetRect.left, targetRect.top, targetRect.width, targetRect.height, callback);
                        return;
                    };
                    const node = e.target;
                    if (node.nodeName !== Statics.tag) return;  // invalid dom or target tag
                    Engager(null, node, engaged); //, engage
                    // navSlider.classList.add(Statics.move);
                    // Engager(null, node, ()=> {
                    //     navSlider.classList.remove(Statics.move);
                    //     const nodeRect = node.getBoundingClientRect();
                    //     const nodeHeight = nodeRect.height;
                    //     engage();
                    //     Transfer(navSlider, nodeRect.left, nodeRect.top, nodeRect.width, nodeHeight, engaged);
                    // });
                };
                let engage = ()=> {
                    navSlider.classList.add(Statics.show);
                };
                let disengage = ()=> {
                    navSlider.classList.remove(Statics.show);
                    // navSlider.style.width = navSlider.style.height = '';  // (remove any-attr to re-active engaged-transitionend events)
                };
                let engaged = ()=> {
                    if (EventBus.has(navSlider, 'transitionend')) return;
                    // Bind transition event from callback to prevent init default position(engage call())
                    EventBus.bind(navSlider, 'transitionend', (e)=> {
                        if (e.propertyName === 'transform') engage();  // (??set as any-transitionend to re-active events)
                    });
                };
                // init event listener
                // EventBus.bind(navSlider, 'transitionstart', (e)=> {
                //     navSlider.classList.add(Statics.move);
                // });
                // EventBus.bind(navSlider, 'transitionend', (e)=> {
                //     navSlider.classList.remove(Statics.move);
                // });
                EventBus.bind(navContainer, 'pointerover', Engager);
                // !! pointer-leave event delay must bigger than transition-duration(Statics.duration)
                EventBus.bind(navContainer, 'pointerleave', Closure.throttler(disengage, Statics.duration + 100));
                // init default position
                Engager(null, navContainer?.querySelector(Statics.tag));
            }
        <?php
            }
            // lazyLoad images
            if(get_option('site_lazyload_switcher')) {
        ?>
            const lazyImgs = document.querySelectorAll("body img[data-src]");
            if (lazyImgs.length) {
                const loadImgSrc = "<?php global $img_cdn;echo $img_cdn; ?>/images/loading_3_color_tp.png";
                const setAcgBackground = (t)=> {<?php echo $acgpage ? 'setupBlurColor(t, getParByCls(t, "inbox"));' : 'console.debug("not in acg page.");'; ?>}
                // visibility observer
                const visibilityObserver = new VisibilityObserver({
                    threshold: 0.1, // 10%可见时触发
                    rootMargin: '10px' // 提前10px检测
                });
                // observer images
                lazyImgs.forEach((img)=> {
                    visibilityObserver.observe(img, (entry) => {
                        const image = entry.target;
                        const datasrc = image.dataset.src;
                        if (!image.dataset.src) {
                            console.warn('no data-src found on img', image);
                            return;
                        }
                        if (entry.target.src === datasrc) {
                            console.debug('image data-src settled.');
                            return;
                        }
                        // entry.target.src = entry.isVisible ? datasrc : loadImgSrc;  // BUG of inVisible loadImgSrc
                        if (entry.isVisible) entry.target.src = datasrc;
                        <?php if ($acgpage) echo 'entry.target.onload = ()=> setupBlurColor(entry.target, getParByCls(entry.target, "inbox"));'; ?>
                    });
                });
            }
        <?php
            } elseif($acgpage) {
        ?>
                // setupBlurColor all acg imgs
                const acg_imgs = document.querySelectorAll('.rcmd-boxes .info .inbox .inbox-headside img');
                if (acg_imgs.length) {
                    acg_imgs.forEach((img)=> {
                        setupBlurColor(img, getParByCls(img, "inbox"));
                        // ??missing load imgs..
                        img.onload = ()=> {
                            // console.log('onload',img)
                            setupBlurColor(img, getParByCls(img, "inbox"));
                        }
                    });
                }
        <?php
            }
        ?>
        });
    } catch(e) {
        console.warn("VisibilityObserver unavailable! check utils.",e)
    }
</script>
<?php
    if(get_option('site_video_capture_switcher')){
        $ffmpeg_sw_gif = get_option('site_video_capture_gif');
?>
        <style>
            video{object-fit: initial;}
            .video_preview_hide:before,.video_preview_hide .preview_bg{content:"";display:none}
            .video_previews:before{content:'';width:100%!important;height:52%;backdrop-filter:blur(15px);position:absolute!important;top:0!important;left:0!important;z-index:1;background:-webkit-linear-gradient(90deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);background:linear-gradient(0deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);}
            .video_previews{cursor: e-resize;}
            .video_preview_hide{cursor: default;}
            .wp-block-video, .video_previews{position:relative;overflow:hidden;border-radius:10px;/*display:inline-block;*/}
            .video_previews .preview_bg{z-index:99!important;opacity:1!important;top:30%;pointer-events:none;}
            .preview_bg .progress{width:32%;height:4px;background:white;border:1px solid black;border-radius:15px;position:absolute!important;bottom:10%;left:50%;transform:translate(-50%,-50%);overflow:hidden}
            .preview_bg .progress em.pause_move{transform:translateX(0%)!important}
            .preview_bg .progress em{width:100%;height:100%;background:var(--theme-color);position:inherit!important;top:1px;left:0;transform:translateX(-100%);will-change:transform}
            .preview_bg{cursor:crosshair;position:absolute!important;left:50%;transform:translate(-50%,-50%);border-radius:10px!important;z-index:-1!important;opacity:0;transition:opacity .35s ease-in;width:90%;height:45%;top:20%;margin:auto!important;/*transition:top 1s ease;width:88%;height:58%;top:38%!important;*/}
        </style>
        <script>
            const videos = document.querySelectorAll('video');
            if(videos[0]){
                for(let i=0,vdosLen=videos.length;i<vdosLen;i++){
                    let video = videos[i];
                    if(video.autoplay) break;
                    let video_src = video.src,
                        video_box = video.parentElement,
                        video_dir = video_src.lastIndexOf('/')+1,
                        video_url = video_src.substr(0, video_dir),
                        video_title = video_src.substr(video_dir, video_src.length),
                        video_name = video_title.substr(0, video_title.lastIndexOf('.')),
                        video_path = video_url+video_name+"/"+video_name,
                        // video_width = video_box.offsetWidth,
                        video_gif = video_path+'.gif',
                        video_timer = null;
                    video.addEventListener('canplay', function () {
                        video = video_box.querySelector('video'); // canplay 内需重新声明 video，否则修改后无法应用到dom
                        video.onplaying=()=>video_box.classList.add('video_preview_hide');
                        video.onpause=()=>video_box.classList.remove('video_preview_hide');
                    });
                    video_box.innerHTML += `<div class="preview_bg"<?php echo $ffmpeg_sw_gif ? ' data-previews="${video_gif}"' : false; ?> style="background:url(${video_path}.jpg) no-repeat 0% 0% /cover"><span class="progress"><em></em></span></div>`;
                    const preview_bg = video_box.querySelector('.preview_bg'),
                          preview_gif = preview_bg.dataset.previews,
                          preview_pg = video_box.querySelector('.progress em');
                    video_box.onmousemove=function(e){
                        var _this = this,
                            video = _this.querySelector("video"),  //update video dom
                            video_offset = e.offsetX,
                            video_width = video_box.offsetWidth;  //always update videoBox width
                        return (function(){
                            if(video_timer==null){
                                <?php echo $ffmpeg_sw_gif ? 'video.poster!=video_gif&&preview_gif ? video.poster=preview_gif : false;' : false; ?>
                                _this.classList.add('video_previews');
                                video_timer = setTimeout(function(){
                                    // e.stopPropagation(); //e.preventDefault(); 
                                    let percentage = Number((Math.round(video_offset/video_width*10000)/100).toFixed(0)),
                                        progressOffset = -100+percentage;
                                    preview_bg.style.backgroundPosition = percentage+"% 0%";
                                    preview_pg.style.transform = 'translateX('+progressOffset+'%)';
                                    // console.log(percentage);
                                    percentage>=100 ? preview_pg.classList.add('pause_move') : preview_pg.classList.remove('pause_move');
                                    _this.onmouseleave = function(){
                                        this.classList.remove("video_previews");
                                        preview_pg.style.transform = "";
                                    }
                                    video_timer = null;  //消除定时器
                                }, 10);
                            }
                        })();
                    }
                }
            };
        </script>
<?php
    }
    // $cat = $cat ? $cat : get_page_cat_id(current_slug());  //rewrite cat to cid (var cat for require php)
    unset($lazysrc, $cat);  //release current file.php global variables
    // require_once(TEMPLATEPATH. '/foot.php');
?>