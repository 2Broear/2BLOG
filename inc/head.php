<?php 
    global $src_cdn, $img_cdn, $cat;
    $cat = $cat ? $cat : get_page_cat_id(current_slug());
    $theme = get_option('site_theme','#eb6844');
    $title = get_term_meta($cat, 'seo_title', true);
    // 自动匹配首页、分类、文章、页面标题
    function custom_title(){
        global $wp_query;//$cat, $post;
        $nick = get_option('site_nick');
        $name = !is_home() ? ' - '.get_bloginfo('name') : '';
        $surfix = $nick ? " | " . get_option('site_nick') . $name : $nick;
        // $founds = $wp_query->found_posts;
        switch (true) {
            case is_search():
                $cid = check_request_param('cid');
                $of_year = ' of '.check_request_param('year');
                echo $cid ? get_category($cid)->slug.$of_year.$surfix : 'Search result for "' . esc_html(get_search_query()) .'"'. $surfix; //$founds . 
                break;
            case is_archive():
                $dates = $wp_query->query;
                $date_mon = array_key_exists('monthnum',$dates) ? ' - '.$dates['monthnum'].' ' : '';
                echo 'Archives of ' . $dates['year'] . $date_mon . $surfix; //$founds . 
                break;
            case is_tag():
                echo 'Posts of tag "'. single_tag_title('',false) .'"'. $surfix; //$founds . 
                break;
            case is_home():
                echo bloginfo('name');
                echo $surfix . " - ";
                echo bloginfo('description');
                break;
            case is_category():
                echo single_cat_title() . $surfix;
                break;
            case is_page() || is_single()://in_category(array('news')):
                echo the_title() . $surfix;
                break;
            case in_category(array('notes','weblog')):
                echo the_title() . $surfix . " - " . get_the_category()[0]->name;
                break;
            default:
                echo "NOTHING MATCHED" . $surfix;
                break;
        }
        unset($wp_query);//$cat, $post
    }
?>
<title><?php echo $title ? $title : custom_title(); ?></title>
    <meta name="keywords" content="<?php echo is_category() ? get_term_meta($cat, 'seo_keywords', true) : get_option('site_keywords', "no keywords yet"); ?>">
    <meta name="description" content="<?php echo get_site_description(); ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0"><!--, maximum-scale=1.0, user-scalable=no-->
    <meta name="theme-color" content="<?php echo $theme; ?>">
    <meta name="renderer" content="webkit">
    <meta name="msapplication-TileColor" content="<?php echo $theme; ?>" />
    <meta name="msapplication-TileImage" content="<?php echo get_site_favico(); ?>" />
    <link rel="shortcut icon" href="<?php echo get_site_favico(); ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/universal.min.css?v=<?php echo get_theme_info(); ?>" />
    <style>
        body.dark #supports em.warmhole{filter: invert(1);}
        <?php if(get_option('site_logo_switcher')) echo 'body.dark .mobile-vision .m-logo span,body.dark .logo-area span{background: url('.get_option('site_logos').') no-repeat center center /cover!important;}'; ?>
    </style>
    <script>
        document.documentElement.style.setProperty('--theme-color','<?php echo $theme; ?>');
        function asyncLoad(url, callback){
        	const head = document.getElementsByTagName('head')[0],
        		  script = document.createElement('script');
    	    script.setAttribute('type', 'text/javascript');
    	    script.setAttribute('async', true);
    	    script.setAttribute('src', url);
    		head.appendChild(script);
        	script.onload = script.onreadystatechange = function(){
        		if(!this.readyState || this.readyState=='loaded' || this.readyState=='complete'){
        			if(callback&&typeof callback==='function') callback();
        		}
        		script.onload = script.onreadystatechange = null;
        	};
        };
        function bindEventClick(parent, cls, callback){
            parent.onclick=(e)=>{
                e = e || window.event;
                let t = e.target || e.srcElement;
                if(!t) return;
                while(t!=parent){
                    if(!t) break;
                    if(t.classList && t.classList.contains(cls)){
                        // callback?.();
                        if(callback&&typeof callback==='function') callback(t,e); //callback(t) || callback(t); // callback.apply(this, ...arguments);
                        break;
                    }else{
                        t = t.parentNode;
                    }
                }
            }
        }
        function getParByCls(curEl, parCls){ //!curEl.classList incase if dnode oes not have any classes (null occured)
            while(!curEl || !curEl.classList || !curEl.classList.contains(parCls)){
                if(!curEl) break;  //return undefined
                curEl = curEl.parentNode; //parentElement
            };
            return curEl;
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