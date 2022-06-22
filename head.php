<title><?php custom_title(); ?></title>
<meta name="keywords" content="<?php if(is_home()) echo get_option('site_keywords');else echo get_option('seo_keywords'); ?>">
<meta name="description" content="<?php if(is_home()) echo get_option('site_description');else echo get_term_meta($cat, 'seo_description', true); ?>">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset') ?>">
<meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="<?php $theme_color=get_option('site_theme','#eb6844');echo $theme_color; ?>">
<meta name="renderer" content="webkit">
<meta name="msapplication-TileColor" content="<?php echo $theme_color; ?>" />
<meta name="msapplication-TileImage" content="<?php custom_cdn_src(); ?>/favicon/favicon.ico" />
<link rel="shortcut icon" href="<?php custom_cdn_src('img'); ?>/images/favicon/favicon.ico"/>
<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/universal.min.css?v=0.8<?php //echo(mt_rand()); ?>" />
<style>:root{--theme-color: <?php echo $theme_color; ?>}</style>
<?php
    if(get_option('site_leancloud_switcher')){
?>
        <script src="<?php custom_cdn_src(); ?>/js/leancloud/av-min.js?v=headcall"></script>
        <script>
            AV.init({
                appId: "<?php echo get_option('site_leancloud_appid') ?>",
                appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
    	        serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
            });
        </script>
<?php
    }
    if(get_option('site_comment_switcher')){  // 全站加载
?>
        <script src="<?php custom_cdn_src(); ?>/js/Valine/Valine.m.js"></script>
<?php
    }
?>