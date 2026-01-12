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
        .additional.metabox li p {font-weight: normal;opacity: .75;}
        body.dark #supports em.warmhole {filter: invert(1);}
        /*blockquote p {color: var(--preset-6) !important}*/
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
        .slider-menu {
            background-image: radial-gradient(var(--preset-3a) 1px, var(--preset-2b) 1px)!important;
            background-size: 10px 10px!important;
        }
        .v .vquote .vcard {
            margin-left: 10px;
        }
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
        .adscene {
            margin-bottom: 15px!important;
        }
        #supports-txt {
            opacity: 1;
        }
        .v .vwrap .vedit .vemojis img {
            overflow: visible;
        }
        .links-more {
            border-width: 2px!important;
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
    if (get_option('site_experimental_switcher')) {
?>
        /**
        **
        **  Experimental Feats
        **  UI 2025
        **
        **/
        :root {
            --radius: 18px;
        }
        /*footer .container,*/
        .win-top:after,
        .content-all {
            background-image: radial-gradient(var(--preset-e) 1px, var(--preset-fa) 1px);
            background-size: 10px 10px;
        }
        /*body.dark footer .container,*/
        body.dark .win-top:after,
        body.dark .content-all {
            background-image: radial-gradient(var(--preset-3a) 1px, var(--preset-2b) 1px);
        }
        /**
        ** index
        **/
        /*.acg_window-content-inside_left-list li a,*/
        #tech_window-bottom,
        #acg_window-bottom {
            background: linear-gradient(180deg, var(--mirror) 0, #fff);
            background: -webkit-linear-gradient(-90deg, var(--mirror) 0, #fff);
        }
        .recommendation #recommend-inside .recommend-newsImg div a#rel {
            color: var(--mirror);
            /*letter-spacing: 1px;*/
            background: var(--preset-2bs);
            border: 1px solid var(--preset-6);
            backdrop-filter: blur(14px);
            margin: 18px auto;
            max-width: 12em;
            line-height: 36px;
            border-radius: 50px;
            transition: all .55s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .recommendation #recommend-inside .recommend-newsImg div a#rel b {
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 10px;
            box-sizing: border-box;
            max-width: 98%;
        }
        .recommendation #recommend-inside:hover > .recommend-newsImg div a#rel {
            color: var(--preset-f);
            /*font-size: 1rem;*/
            border-color: transparent;
            margin: 0px auto;
            line-height: 58px;
            max-width: 100%;
            border-radius: 0;
            letter-spacing: 5px;
            /*border-radius: calc(var(--radius) / 2);*/
        }
        .recommendation #recommend-inside:active > .recommend-newsImg div a#rel {
            transform: scale(1.15);
            /*letter-spacing: 0;*/
            margin: 18px auto;
            max-width: 12em;
            line-height: 36px;
            border-radius: 50px;
            border-color: var(--preset-6);
            transition: .15s ease;
        }
        .resource-windows div ul li a:focus {
            background: var(--preset-fa);
            border-color: transparent!important;
            box-shadow: rgb(0 0 0 / 5%) 0 0 20px!important;
            /*box-shadow: none!important;*/
        }
        body.dark .resource-windows div ul li a:focus {
            background: var(--preset-3a);
        }
        /**
        ** news && weblog && valine
        **/
        .news-article-inside {
            border: 2px solid var(--preset-f);
        }
        .pageSwitcher a:hover {
            color: var(--theme-color)!important;
            background: var(--preset-fa);
        }
        /*.v .vlist .vcard.true,*/
        /*.v .vlist .vcard:hover,*/
        .pageSwitcher a {
            background: var(--preset-f);
            background-color: var(--preset-f);
        }
        .main-root ul li:nth-child(even),
        .weblog-tree-core-r,
        .news-ppt div:first-of-type {
            background: var(--preset-f);
            border-color: var(--preset-f);
            box-shadow: rgb(0 0 0 / 5%) 0 0 20px;
        }
        /*body.dark .v .vlist .vcard.true,*/
        /*body.dark .v .vlist .vcard:hover,*/
        body.dark .weblog-tree-core-r,
        body.dark .news-ppt div:first-of-type,
        body.dark .pageSwitcher a {
            background: var(--preset-3a);
            border-color: var(--preset-3a);
        }
        body.dark .v .vlist .vcard .vh .vat,
        body.dark #comment_txt p,
        body.dark .news-inside-content .news-core_area p {
            color: var(--preset-9)!important;
        }
        .win-content article p,
        .news-content-right-download ol,
        .v .vlist .vcard .vh .vat,
        #comment_txt p,
        .news-inside-content .news-core_area p {
            color: var(--preset-6);
        }
        .news-ppt {
            overflow: visible;
        }
        /**
        ** archive
        **/
        .archive-tree h2 {
            border: none;
        }
        .cs-tree .dayto {
            border: 1px solid var(--preset-9);
        }
        body.dark .cs-tree span,
        body.dark .archive-tree ul li a {
            border-color: var(--preset-4b);
        }
        /**
        ** 2bfriends
        **/
        .friends-boxes .deals .inboxSliderCard {
            color: var(--preset-9);
        }
        body.dark .friends-boxes .deals .inboxSliderCard {
            color: var(--preset-6);
        }
        /**
        ** etc
        **/
        /*.body-basically .Introduce,*/
        .main {
            color: var(--preset-4a);
        }
        footer #footer-support-board {
            box-shadow: 0px -20px 20px rgb(0 0 0 / 5%);
        }
        /*.v .vlist .vcard .vquote .vcard:hover,*/
        .v .vlist .vcard.true,
        .v .vlist .vcard:hover {
            box-shadow: 0 0 20px rgb(0 0 0 / 5%);
        }
        /*.load button:hover {*/
        /*    border-color: transparent!important;*/
        /*}*/
        .load button:active {
            color: var(--theme-color)!important;
        }
        /*.v .vbtn,*/
        .load button {
            /*color: var(--preset-6);*/
            /*border: 2px solid var(--preset-f);*/
            font-weight: bold;
            border-color: var(--preset-f);
            background: linear-gradient(45deg, var(--mirror-start), var(--mirror-end));
            box-shadow: 0 0 20px rgb(0 0 0 / 10%);
        }
        /*body.dark .v .vbtn,*/
        body.dark .load button {
            /*color: var(--preset-c);*/
            border-color: var(--preset-4a);
            background: linear-gradient(45deg, var(--preset-4a), var(--preset-2b));
        }
        /**
        ** 
        ** feat: border-radius
        ** 
        **/
        .inside_of_block nav.main-nav ul li a.choosen::after {
            border-radius: 3px;
        }
        /*.recommendation,*/
        /*.Fresh-ImgBoxs span,*/
        /*.banner .banner-inside,*/
        /*.resource-windows div,*/
        .v .vwrap,
        .v .vlist .vcard {
            /*border-radius: calc(var(--radius) * 2);*/
            border-radius: var(--radius)!important;
        }
        .vheader #avatar {
            border-radius: var(--radius) var(--radius) 0 0!important;
        }
        /**
        ** 
        ** feat: ue/scale transform
        ** 
        **/
        /*.win-content article h1,*/
        /*.news-inside-content h2 a,*/
        .body-basically .Introduce p a,
        .stats a,
        figure a,
        footer a {
            display: inline-block;
        }
        /*.content-all a:active,*/
        .inside_of_block .logo-area:active,
        .recommendation:active,
        .Fresh-ImgBoxs span:active,
        .resource-windows div:active,
        .weBlog-Description .weBlog-Description-inside-content:active,
        .win-top h5:active,
        .win-top .counter a:active,
        #acg_window-bottom:active,
        .news-article-head-tools span:active,
        /*.news-content-right-download ol li a:active,*/
        .load button:active,
        /*.v .vwrap .vedit .vctrl span:active,*/
        .body-basically .Introduce p a:active,
        .rcmd-boxes .info .inbox.more:active,
        .ibox .iboxes:active,
        .friends-boxes .inbox-clip:active,
        .download_boxes .dld_box .dld_box_wrap:active,
        .stats a:active,
        .share a:active,
        form:active {
            transform: scale(0.92);
        }
        /*.inside_of_block nav.main-nav:active,*/
        /*.In-core-head .profile:active,*/
        .main-root:active,
        .pageSwitcher:active,
        .friends-boxes .deals .inboxSliderCard .slideBox a:active,
        .friends-boxes .deals .inboxSliderCard:active,
        .acg_window-content-inside_right .tags:active,
        .acg_window-content-inside_left-list:active,
        .additional.metabox ol:active,
        .news-window:active,
        .news-content-right-download ol li a:active,
        .win-nav .nav-header:active,
        .win-content .notes article:active,
        .about_blocks:active,
        .v .vlist .vcard:active,
        .weblog-tree-core-r:active,
        .v .vwrap:active,
        footer a:active,
        figure a:active {
            transform: scale(0.98);
        }
        .acg_window-content-inside_left-list li:active,
        .inside_of_block nav.main-nav ul li a:active,
        /*.additional.metabox li a:active,*/
        /*.additional.metaboxes li a:active,*/
        .friends-boxes .deals .inbox:active,
        .footer-detector:active,
        .rcmd-boxes .info .inbox:active,
        .v .vbtn:active {
            transform: scale(1.08)!important;
        }
        .v .vbtn.extend_addon:active {
            transform: scale(1.08) translate(-50%, -50%)!important;
        }
        /*.win-content article h1:active,*/
        /*.news-inside-content h2 a:active,*/
        .main-root ul li a:active,
        .pageSwitcher a:active,
        .about_blocks li.intro_right .mbit .mbit_intro a:active,
        .acg_window-content-inside_right .tags span:active,
        .cs-tree span:active,
        .news-window-img a:active,
        .v .vlist .vcard .vh .vat:active,
        .v .vwrap .vedit .vemojis img:active,
        .resource-windows div ul li:active {
            transform: scale(1.15)!important;
        }
        /*.inside_of_block nav.main-nav,*/
        .inside_of_block nav.main-nav ul li a,
        /*.content-all a,*/
        .inside_of_block .logo-area,
        .recommendation,
        .Fresh-ImgBoxs span,
        .resource-windows div,
        .resource-windows div ul li,
        .weBlog-Description .weBlog-Description-inside-content,
        .acg_window-content-inside_right .tags,
        .acg_window-content-inside_right .tags span,
        .acg_window-content-inside_left-list,
        .acg_window-content-inside_left-list li,
        .win-top h5,
        .win-top .counter a,
        .cs-tree span,
        #acg_window-bottom,
        .additional.metabox ol,
        /*.news-content-right-download ol li a,*/
        .news-article-head-tools span,
        .news-window,
        .news-window-img a,
        .news-content-right-download ol li a,
        /*.news-inside-content h2 a,*/
        .win-nav .nav-header,
        .win-content .notes article,
        /*.win-content article h1,*/
        .main-root,
        .main-root ul li a,
        .weblog-tree-core-r,
        .v .vwrap,
        .load button,
        .pageSwitcher,
        .pageSwitcher a,
        .v .vbtn,
        .v .vlist .vcard,
        .v .vlist .vcard .vh .vat,
        /*.v .vwrap .vedit .vctrl span,*/
        .v .vwrap .vedit .vemojis img,
        .rcmd-boxes .info .inbox.more,
        .about_blocks,
        .about_blocks li.intro_right .mbit .mbit_intro a,
        .body-basically .Introduce p a,
        /*.In-core-head .profile,*/
        .ibox .iboxes,
        .friends-boxes .inbox-clip,
        .friends-boxes .deals .inbox,
        .friends-boxes .deals .inboxSliderCard,
        .friends-boxes .deals .inboxSliderCard .slideBox a,
        .download_boxes .dld_box .dld_box_wrap,
        .footer-detector,
        .stats a,
        .share a,
        form,
        figure a,
        footer a {
            transition: .15s ease;
            transition-property: transform, top;
            /*will-change: transform;*/
        }
<?php
    }
?>
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