<?php
    global $src_cdn;
?>
<script src="<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/js/main.js?v=<?php echo get_theme_info(); ?>"></script>
<script type="text/javascript">
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
                            fancybox.setAttribute("data-fancybox","gallery");
                            fancybox.setAttribute("aria-label", "gallery_images");
                            eachimg.src ? fancybox.setAttribute("href", eachimg.src) : fancybox.setAttribute("href", eachimg.dataset.src);
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
                asyncLoad('<?php echo custom_cdn_src(0,1);//$src_cdn; ?>/js/marker.js', function(){
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
                // // dynamicLoad("https://blog.2broear.com/wp-content/themes/2BLOG-main/plugin/test.php");
                // if(typeof(EventSource) !== "undefined") {
                //     var source = new EventSource("<?php echo $src_cdn; ?>/plugin/sse.php");
                //     //监听事件
                //     source.addEventListener('open', function (event) {
                //         console.log('SSE Open Connected.');
                //     });
                //     source.addEventListener('message', function (event) {
                //          // if (event.origin != 'https://blog.2broear.com') return;
                //         console.log('Reciving SSE Data..', JSON.parse(event.data));
                //     });
                //     source.addEventListener('error', function (event) {
                //         console.log(`SSE Error Closed. (statu: ${event.target.readyState})`, event);
                //         source.close();
                //     });
                // } else {
                //     console.warn("您的浏览器不支持SSE");
                // }
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
                    if($datadance) {
                        echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h2", -15, 5, "<sup>+</sup>");';
    ?>
                        // const list = document.querySelectorAll(".win-top .counter div");
                        // if (list.length > 0) {
                        //     for (let i=0,listLen=list.length; i<listLen; i++) {
                        //         let each = list[i],
                        //             counter = each.querySelector('h2'),
                        //             limit = parseInt(each.dataset.count);
                        //         easeCounter(0, limit, 1, 0.5, (num)=> {
                        //             let counted =parseInt(num);
                        //             counter.innerHTML = counted + "<sup>+</sup>"; //init counts
                        //             if (counted >= limit) each.classList.remove('blink');
                        //         });
                        //     }
                        // }
    <?php
                    }
                    break;
                case get_cat_by_template('archive','term_id'):
                    if($datadance) echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h1", 200, 25);';
                    break;
                case get_cat_by_template('about','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(2);';  // 截取设置当前页面所有视频 poster 
    ?>
                    const list = document.querySelectorAll('.mbit .mbit_range li');
                    async_enqueue(list, true, (i)=>list[i].classList.add('active'), 200);
    <?php
                    break;
            }
        }
    ?>
</script>