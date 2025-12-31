<?php 
    global $cat, $src_cdn, $img_cdn;
    $cat = $cat ? $cat : get_page_cat_id(current_slug());
    $theme = get_option('site_theme', "#eb6844");
    $viewmode = get_request_param('viewmode');
    if (is_single() && $viewmode && $viewmode === 'map') {
        echo '<style>
            img,
            video,
            figure {
                max-width: 100%;
                height: auto;
                object-fit: cover;
                margin: auto auto 10px;
                padding: 0;
            }
            .chatGPT {
                display: none;
            }
            p {
                opacity: .75;
                line-height: 23px;
                font-size: 14px;
            }
        </style>';
        $content = get_the_content();
        // replace lazy-img attr-string
        echo $content = str_replace('data-src', 'loading="lazy" src', $content);
        // echo '<script>
        //     const imgs = document.querySelectorAll("img");
        //     window.parent.postMessage(imgs, "http://blog.2broear.com");
        // </script>';
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
    <style>
        .inside_of_block nav.main-nav ul li a {font-weight: bold;}
        body.dark #supports em.warmhole {filter: invert(1);}
        <?php if(get_option('site_logo_switcher')) echo 'body.dark .mobile-vision .m-logo span,body.dark .logo-area span{background: url(' . get_option('site_logos') . ') no-repeat center center /cover!important;}'; ?>
        .win-top em.digital_mask {
            /*bottom: -50px;*/
        }
        .win-top em.digital_mask:before {
            background: linear-gradient(0deg, rgb(0 0 0 / 18%) 0%, transparent);
            background: -webkit-linear-gradient(90deg, rgb(0 0 0 / 18%) 0%, transparent);
            background-size: auto!important;
        }
        #footer-copyright li {
            color: var(--preset-6);
        }
        /*
        ** experimental style futures
        */
        .v .vlist .vcard .vcontent p {
            color: var(--preset-6);
        }
        body.dark .v .vlist .vcard .vcontent p {
            color: var(--preset-c);
        }
        body.dark #footer-copyright li {
            color: var(--preset-9);
        }
        .article_index .in_dex,
        header>nav#tipson .top-bar-tips {
            backdrop-filter: saturate(150%) blur(5px);
            -webkit-backdrop-filter: saturate(150%) blur(5px);
            background-image: radial-gradient(rgb(255 255 255 / 55%) 2px, rgb(255 255 255) 2px);
            background-size: 4px 4px;
            /*background-color: rgba(255,255,255, .55);*/
            /*backdrop-filter: saturate(150%) blur(0px);*/
            /*-webkit-backdrop-filter: saturate(150%) blur(0px);*/
        }
        body.dark .article_index .in_dex,
        body.dark header>nav#tipson .top-bar-tips {
            background-image: radial-gradient(var(--preset-2b) 2px, var(--preset-2bs) 2px);
            background-size: 4px 4px;
            background-color: var(--preset-2bs);
        }
        <?php
            if (get_option('site_animated_scrolling_switcher')) echo '
        /**
        * scroll to view
        */
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
        }'; ?>
        .adscene {
            margin-bottom: 15px!important;
        }
    </style>
    <script>
        document.documentElement.style.setProperty('--theme-color','<?php echo $theme; ?>');
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
        console.info("<?php echo get_num_queries().'次查询，耗时'.timer_stop(0).'秒。'; ?>");
        asyncLoad("<?php echo $src_cdn; ?>/js/nprogress.js", function(){
    	    NProgress.start();
    	    const NProgressLoaded = function(){
        		NProgress.done();
        	    window.removeEventListener('load', NProgressLoaded, true);
    	    }
        	window.addEventListener('load', NProgressLoaded, true);
        });
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
        // 自动根据时段设置主题
        function automode(){
            if (getCookie('theme_manual')) setCookie('theme_manual', 0);  // disable manual mode
            let date = new Date(),
                hour = date.getHours(),
                min = date.getMinutes(),
                sec = date.getSeconds(),
                start = <?php echo get_option('site_darkmode_start',17); ?>,
                end = <?php echo get_option('site_darkmode_end',9); ?>;
            hour>=end&&hour<start || hour==end&&min>=0&&sec>=0 ? setCookie('theme_mode','light') : setCookie('theme_mode','dark');
            document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
        };
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