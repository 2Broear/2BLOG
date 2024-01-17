<title><?php 
        global $src_cdn, $img_cdn; 
        $cat = $cat ? $cat : get_page_cat_id(current_slug()); 
        $set = get_term_meta($cat, 'seo_title', true);
        $is_cat = is_category();
        echo $set ? $set : custom_title(); 
    ?></title>
    <meta name="keywords" content="<?php echo $is_cat ? get_term_meta($cat, 'seo_keywords', true) : get_option('site_keywords', "no keywords yet"); ?>">
    <meta name="description" content="<?php 
        $desc = "";
        switch (true) {
            case is_category():
                $desc = get_term_meta($cat, 'seo_description', true);
                break;
            case is_single():
                if(in_chatgpt_cat()){
                    $dir = get_option('site_chatgpt_dir') ? get_option('site_chatgpt_dir').'/' : '';
                    include 'plugin/'.$dir.'chat_data.php';  // 读取文件记录
                    global $post;
                    $pid = $post->ID;
                    if($cached_post['chat_pid_'.$pid]['error']){
                        $desc = $cached_post['chat_pid_'.$pid]['error']['message'];
                    }else if(isset($cached_post['chat_pid_'.$pid]['choices'][0])){
                        $desc = isset($cached_post['chat_pid_'.$pid]['choices'][0]['message']) ? $cached_post['chat_pid_'.$pid]['choices'][0]['message']['content'] : $cached_post['chat_pid_'.$pid]['choices'][0]['text'];
                    }
                }else{
                    $desc = custom_excerpt();
                }
                break;
            default:
                $desc = get_option('site_description');
                break;
        }
        echo trim($desc);
    ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0"><!--, maximum-scale=1.0, user-scalable=no-->
    <meta name="theme-color" content="<?php echo $theme_color = get_option('site_theme','#eb6844'); ?>">
    <meta name="renderer" content="webkit">
    <meta name="msapplication-TileColor" content="<?php echo $theme_color; ?>" />
    <meta name="msapplication-TileImage" content="<?php 
        $site_favico = get_site_icon_url() ? get_site_icon_url() : $img_cdn.'/images/favicon/favicon.ico';
        echo $site_favico; 
    ?>" />
    <link rel="shortcut icon" href="<?php echo $site_favico; ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/universal.min.css?v=<?php echo get_theme_info('Version'); ?>" />
    <style>
        <?php if(get_option('site_logo_switcher')) echo 'body.dark .mobile-vision .m-logo span,body.dark .logo-area span{background: url('.get_option('site_logos').') no-repeat center center /cover!important;}'; ?>
        body.dark #supports em.warmhole{filter: invert(1);}
    </style>
    <script>
        document.documentElement.style.setProperty('--theme-color','<?php echo $theme_color; ?>');
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