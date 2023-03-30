<title><?php $cat = $cat ? $cat : get_page_cat_id(current_slug()); $set=get_term_meta($cat, 'seo_title', true);echo $set ? $set : custom_title(); ?></title>
<meta name="keywords" content="<?php echo is_home() ? get_option('site_keywords') : get_term_meta($cat, 'seo_keywords', true); ?>">
<meta name="description" content="<?php echo is_home() ? get_option('site_description') : get_term_meta($cat, 'seo_description', true); ?>">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset') ?>">
<meta name="viewport" content="width=device-width,initial-scale=1.0"><!--, maximum-scale=1.0, user-scalable=no-->
<meta name="theme-color" content="<?php echo $theme_color = get_option('site_theme','#eb6844'); ?>">
<meta name="renderer" content="webkit">
<meta name="msapplication-TileColor" content="<?php echo $theme_color; ?>" />
<meta name="msapplication-TileImage" content="<?php custom_cdn_src('img'); ?>/images/favicon/favicon.ico" />
<link rel="shortcut icon" href="<?php custom_cdn_src('img'); ?>/images/favicon/favicon.ico"/>
<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(0); ?>/style/universal.min.css?v=<?php echo get_theme_info('Version'); ?>" />
<style>
    body.dark #supports em.warmhole{
        background: url(<?php custom_cdn_src('img'); ?>/images/wormhole_2_tp.gif) no-repeat center center /cover!important;
    }
    <?php 
        if(get_option('site_logo_switcher')){
    ?>
                body.dark .mobile-vision .m-logo span,
                body.dark .logo-area span{
                    background: url(<?php echo get_option('site_logos'); ?>) no-repeat center center /cover!important;
                }
    <?php
        }
    ?>
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
    			if(callback&&typeof callback=='function') callback();
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
                if(t.classList && t.classList.contains(cls)){
                    if(callback&&typeof callback=='function') callback(t,e); //callback(t) || callback(t); // callback.apply(this, ...arguments);
                    break;
                }else{
                    t = t.parentNode;
                }
            }
        }
    }
    function getParByCls(curEl, parCls){
        //!curEl.classList incase if dnode oes not have any classes (null occured)
        while(!curEl || !curEl.classList || !curEl.classList.contains(parCls)){
          if(!curEl) break;  //return undefined
          curEl = curEl.parentNode; //parentElement
        };
        return curEl;
    };
</script>
<?php
    if(get_option('site_leancloud_switcher')){
        //DO NOT use "defer" in script
?>
        <script src="<?php custom_cdn_src(); ?>/js/leancloud/av-min.js?v=headcall"></script>
        <script>
            // function asyncCallback(){
            // asyncLoad('<?php custom_cdn_src(); ?>/js/leancloud/av-min.js?v=headcall', function(){
                AV.init({
                    appId: "<?php echo get_option('site_leancloud_appid') ?>",
                    appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
        	        serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
                });
            // });
            // }
        </script>
<?php
    }
?>