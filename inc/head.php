<?php 
    global $cat, $src_cdn, $img_cdn;
    $cat = $cat ? $cat : get_page_cat_id(current_slug());
    $theme = get_option('site_theme', "#eb6844");
    $viewmode = get_request_param('viewmode');
    if (is_single() && $viewmode && $viewmode === 'map') {
        echo '<style>img,video,figure {max-width: 100%;height: auto;object-fit: cover;margin: auto auto 10px;padding: 0;}.chatGPT {display: none;}p {opacity: .75;line-height: 23px;font-size: 14px;}</style>';
        $content = get_the_content();
        // replace lazy-img attr-string
        echo $content = str_replace('data-src', 'loading="lazy" src', $content);
        // exit page load
        exit;
    }
?>
<title><?php echo get_site_title(); ?></title>
    <meta name="keywords" content="<?php echo get_site_keywords(); ?>">
    <meta name="description" content="<?php echo get_site_description($cat); ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- 如果有安装 Google Chrome Frame 插件则强制为Chromium内核，否则强制本机支持的最高版本IE内核，作用于IE浏览器 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- 强制Chromium内核，作用于360浏览器、QQ浏览器等国产双核浏览器 -->
    <meta name="renderer" content="webkit">
    <meta name="force-rendering" content="webkit" />
    <meta name="theme-color" content="<?php echo $theme; ?>">
    <meta name="msapplication-TileColor" content="<?php echo $theme; ?>" />
    <meta name="msapplication-TileImage" content="<?php echo get_site_favico(); ?>" />
    <link rel="shortcut icon" href="<?php echo get_site_favico(); ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/style/universal.min.css?v=<?php echo get_theme_info(); ?>" />
<?php 
    if (get_option('site_experimental_switcher')) {
?>
    <link type="text/css" rel="stylesheet" href="<?php echo custom_cdn_src(0,1);//$src_cdn;// ?>/style/experimental.css" />
    <style>
        html, body {
            font: normal 16px/normal system-ui,"Microsoft YaHei","微软雅黑","Microsoft JhengHei","Hiragino Sans GB","WenQuanYi Micro Hei",Arial,Helvetica,Lucida Grande,Tahoma,sans-serif;
        }
        body.dark .vquote .vheader #avatar {
            color: var(--preset-4a)!important;
        }
        body.dark .vquote .vwrap .vheader .vinput:focus,
        body.dark .vquote .vedit {
            background: var(--preset-4a)!important;
        }
        body.dark .weblog-tree-core-r, 
        body.dark .news-ppt div:first-of-type, 
        body.dark .pageSwitcher a {
            background: var(--preset-3b);
        }
        body.dark .topic, 
        body.dark .ppt-blocker, 
        body.dark .news-content-right-download, 
        body.dark .news-content-right-download ol,
        body.dark .news-content-right-recommend ul {
            border-color: var(--preset-3a);
        }
        body.dark .friends-boxes .deals .inbox .inbox-inside.aside a#loadRSSFeeds {
            background: var(--preset-2bs);
        }
        .footer-contact a .preview img {
            width: auto;
            height: auto;
        }
        .footer-contact a:hover > .preview {
            display: block!important;
            left: 0;
        }
        @keyframes zoomer {
            0% {
                transform: translate(-50%, -50%) scale(1.15);
            }
            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }
        .win-top video {
            /*transform: translate(-50%, -50%) scale(1.25);*/
            animation: zoomer 5s 1 forwards ease-in-out;
            -webkit-animation: zoomer 5s 1 forwards ease-in-out;
        }
        .win-top .counter h1, 
        .win-top .counter h2 {
            animation-timing-function: linear(0 0%, 0 1.8%, 0.01 3.6%, 0.03 6.35%, 0.07 9.1%, 0.13 11.4%, 0.19 13.4%, 0.27 15%, 0.34 16.1%, 0.54 18.35%, 0.66 20.6%, 0.72 22.4%, 0.77 24.6%, 0.81 27.3%, 0.85 30.4%, 0.88 35.1%, 0.92 40.6%, 0.94 47.2%, 0.96 55%, 0.98 64%, 0.99 74.4%, 1 86.4%, 1 100%)!important;
        }
        /*body { background-size: 10px 10px; }*/
        @media (prefers-color-scheme: dark) {
            /** theme_mode[auto]: dark  ***/
            /*body,*/
            .content-all,
            .win-top:after {
                background-image: radial-gradient(var(--preset-3a) 1px, var(--preset-2b) 1px);
            }
            /** theme_mode[manual]: light  ***/
            /*body.light,*/
            body.light .content-all,
            body.light .win-top:after {
                background-image: radial-gradient(var(--preset-e) 1px, var(--preset-fa) 1px);
            }
        }
    </style>
<?php
    }
    // echo '<link type="text/css" rel="stylesheet" href="' . custom_cdn_src(0,1) . '/style/experimental.css?v=' . get_theme_info() . '" />';
?>
    <style>
        .inside_of_block nav.main-nav ul li a {font-weight: bold;}
        .additional.metabox li p {font-weight: normal;opacity: .75;}
        body.dark #supports em.warmhole {filter: invert(1);}
        details > * {margin-left: 20px!important;}
        details > summary {margin: auto!important;}
<?php 
        if (get_option('site_logo_switcher')) {
            $logos = get_option('site_logos');
            if ($logos) echo 'body.dark .mobile-vision .m-logo span,body.dark .logo-area span{background: url(' . $logos . ') no-repeat center center /cover!important;}'; 
        }
?>
        .win-top em.digital_mask:before {
            background: linear-gradient(0deg, rgb(0 0 0 / 18%) 0%, transparent);
            background: -webkit-linear-gradient(90deg, rgb(0 0 0 / 18%) 0%, transparent);
            background-size: auto!important;
        }
<?php
    if (get_option('site_animated_scrolling_switcher')) {
?>
        /**
        **
        **  scroll to view
        **
        **/
        .content-all-windows {
            overflow-y: clip;
        }
        @keyframes scale-view {
            0% {
                transform: scale(.85);
                transform-origin: top;
                opacity: 0
            }
            to {
                transform: none;
                opacity: 1
            }
        }
        @supports (animation-timeline:view()) {
            /*.archive-tree .archive-item,*/
            /*.friends-boxes .inbox-item,*/
            /*.rcmd-boxes .loadbox,*/
            .fade-item,
            .news-article-list article,
            .win-content .notes article,
            .download_boxes .dld_box,
            .weblog-tree-core-record,
            .vlist > .vcard {
                animation: scale-view 1s;
                transform: none;
                animation-timeline: view(block);
                animation-range: cover 0 30%;
                /*will-change: transform;*/
            }
            .vlist > .vcard,
            .rcmd-boxes .fade-item,
            .archive-tree .fade-item {
                animation-range: cover 0 20%
            }
            .ranking .fade-item {
                animation-range: cover 0 50%
            }
        }
<?php
    }
?>
    </style>
    <script>
        // setup default theme
        document.documentElement.style.setProperty('--theme-color','<?php echo $theme; ?>');
        // 闭包节流器
        function closure_throttle(callback=false, delay=200){
            let closure_variable = true;  //default running
            return function(){
                if(!closure_variable) return;  //now running..
                closure_variable = false;  //stop running
                setTimeout(()=>{
                    callback.apply(this, arguments);
                    closure_variable = true;  //reset running
                }, delay);
            };
        }
        function bindEventClick(parent, ids, callback){
            if (!parent) {
                console.warn('bindEventClick failed', parent);
                return;
            }
            parent.onclick=(e)=>{
                e = e || window.event;
                let t = e.target || e.srcElement;
                if(!t) return;
                while(t!=parent){
                    if(!ids || ids==="") {
                        callback(t,e);
                        break;
                    }
                    if(t.id===ids || t.classList && t.classList.contains(ids) || t.nodeName.toUpperCase()===ids.toUpperCase()){
                        // callback?.();
                        if(callback&&typeof callback==='function') callback(t,e); //callback(t) || callback(t); // callback.apply(this, ...arguments);
                        break;
                    }
                    // console.log('origin', t);
                    t = t.parentNode;
                }
            };
        }
        function getParByCls(curEl, parCls){ //!curEl.classList incase if dnode oes not have any classes (null occured)
            while(!curEl || !curEl.classList || !curEl.classList.contains(parCls)){
                if(!curEl) break;  //return undefined
                curEl = curEl.parentNode; //parentElement
            };
            return curEl;
        };
        // 自动根据时段设置主题
        function automode() {
            const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            function handleColorSchemeChange(e) {
                // console.log(e.matches)
                if (getCookie('theme_manual')) setCookie('theme_manual', 0);  // disable manual mode/prefers
                if (e.matches) {
                    // 用户偏好深色模式优先 (dark)
                    document.body.className = 'dark';
                    setCookie('theme_mode','dark');  // record for manual switch
                    console.log('theme_mode[auto] prefers-color-scheme: dark');
                } else {
                    // 默认调用内部主题判定规则/系统不支持
            <?php
                if (get_option('site_darkmode_switcher')) {
            ?>
                    const start = <?php echo get_option('site_darkmode_start',17); ?>,
                          end = <?php echo get_option('site_darkmode_end',9); ?>;
                    let date = new Date(),
                        hour = date.getHours();
                    hour >= end && hour < start || hour==end && date.getMinutes() >= 0 && date.getSeconds() >= 0 ? setCookie('theme_mode','light') : setCookie('theme_mode','dark');
            <?php
                } else {
            ?>
                    setCookie('theme_mode','light');
            <?php
                }
            ?>
                    const theme_mode = getCookie('theme_mode');
                    document.body.className = theme_mode;
                    console.log('theme_mode[auto] switch-color-scheme: ' + theme_mode);
                }
            }
            // 3. 初始执行一次，设置当前主题
            handleColorSchemeChange(colorSchemeQuery);
            // 4. 监听媒体查询的变化
            colorSchemeQuery.addEventListener('change', handleColorSchemeChange);
        };
        function asyncLoad(url, callback, defer = false){
        	const head = document.getElementsByTagName('head')[0],
        		  script = document.createElement('script');
    	    script.setAttribute('type', 'text/javascript');
    	    script.setAttribute('async', true);
    	    script.setAttribute('defer', defer);
    	    script.setAttribute('src', url);
    		head.appendChild(script);
        	script.onload = script.onreadystatechange = function(){
        		if(!this.readyState || this.readyState=='loaded' || this.readyState=='complete'){
        			if(callback&&typeof callback==='function') callback();
        		}
        		script.onload = script.onreadystatechange = null;
        	};
        };
    <?php
        if (get_option('site_progress_bar_switcher')) {
    ?>
        asyncLoad("<?php echo $src_cdn; ?>/js/nprogress.js", function(){
    	    NProgress.start();
    	    const NProgressLoaded = function(){
        		NProgress.done();
        	    window.removeEventListener('load', NProgressLoaded, true);
    	    }
        	window.addEventListener('load', NProgressLoaded, true);
        });
    <?php
        }
    ?>
    </script>
<?php
    if(get_option('site_leancloud_switcher')){ //DO NOT use "defer" in script
?>
        <script src="<?php echo $src_cdn; ?>/js/leancloud/av-min.js?v=headcall"></script>
        <script>
            AV.init({
                appId: "<?php echo get_option('site_leancloud_appid') ?>",
                appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
    	        serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
            });
        </script>
<?php
    }
?>