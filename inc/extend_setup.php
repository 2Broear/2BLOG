<?php
    /**
     * is_edit_page 
     * function to check if the current page is a post edit page
     * 
     * @author Ohad Raz <admin@bainternet.info>
     * 
     * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
     * @return boolean
     */
    function is_edit_page($new_edit = null){
        global $pagenow;
        //make sure we are on the backend
        if (!is_admin()) return false;
        if($new_edit == "edit")
            return in_array( $pagenow, array( 'post.php',  ) );
        elseif($new_edit == "new") //check for new post page
            return in_array( $pagenow, array( 'post-new.php' ) );
        else //check for either new or edit
            return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
    }
    /* ------------------------------------------------------------------------ *
     * Plugin Name: Link Manager
     * Description: Enables the Link Manager that existed in WordPress until version 3.5.
     * Author: WordPress
     * Version: 0.1-beta
     * See http://core.trac.wordpress.org/ticket/21307
     * ------------------------------------------------------------------------ */
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
    // 启用 wordpress 特色图片（缩略图）功能
    if(function_exists('add_theme_support')) {
        add_theme_support('post-thumbnails');
    };
    // Disable SrcSet
    function remove_max_srcset_image_width( $max_width ) {
        return 1;
    }
    add_filter( 'max_srcset_image_width', 'remove_max_srcset_image_width' );
    /**
     * Kullanicinin kullandigi internet tarayici bilgisini alir.
     * 
     * @since 2.0
     */
    // 设置文章点赞
    add_action('wp_ajax_nopriv_post_like', 'post_like');
    add_action('wp_ajax_post_like', 'post_like');
    function post_like(){
        $id = check_request_param('um_id'); //$_GET["um_id"];
        check_ajax_referer($id.'_post_like_ajax_nonce');  // 检查 nonce
        // if($_GET["um_action"]=='like'){
            $post_liked = get_post_meta($id,'post_liked',true);
            $expire = time() + 99999999;
            $domain = ($_SERVER['HTTP_HOST']!='localhost') ? $_SERVER['HTTP_HOST'] : false;
            setcookie('post_liked_'.$id,$id,$expire,'/',$domain,false);
            if (!$post_liked || !is_numeric($post_liked)) update_post_meta($id, 'post_liked', 1);else update_post_meta($id, 'post_liked', ($post_liked + 1));
            echo get_post_meta($id,'post_liked',true);
        // }
        die;
    };
    // 设置文章浏览量
    function setPostViews($postID) {
        $count_key = 'post_views';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    };
    // 获取文章浏览量
    function getPostViews($postID){
        $count_key = 'post_views';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return "0";
        }
        return $count.'';
    };
    // https://journalxtra.com/php/browser-os-detection-php/
    // 浏览器user-agent信息（浏览器/版本号、系统/版本号）
    // https://gist.github.com/Balamir/4a19b3b0a4074ff113a08a92908302e2
    function get_userAgent_info($user_agent) {
    	$os_array =   array(
    		'/windows nt 10/i'      =>  'Windows 10',
    		'/windows nt 6.3/i'     =>  'Windows 8.1',
    		'/windows nt 6.2/i'     =>  'Windows 8',
    		'/windows nt 6.1/i'     =>  'Windows 7',
    		'/windows nt 6.0/i'     =>  'Windows Vista',
    		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
    		'/windows nt 5.1/i'     =>  'Windows XP',
    		'/windows xp/i'         =>  'Windows XP',
    		'/windows nt 5.0/i'     =>  'Windows 2000',
    		'/windows me/i'         =>  'Windows ME',
    		'/win98/i'              =>  'Windows 98',
    		'/win95/i'              =>  'Windows 95',
    		'/win16/i'              =>  'Windows 3.11',
    		'/macintosh|mac os x/i' =>  'Mac OS X',
    		'/mac_powerpc/i'        =>  'Mac OS 9',
    		'/linux/i'              =>  'Linux',
    		'/ubuntu/i'             =>  'Ubuntu',
    		'/iphone/i'             =>  'iPhone',
    		'/ipod/i'               =>  'iPod',
    		'/ipad/i'               =>  'iPad',
    		'/android/i'            =>  'Android',
    		'/blackberry/i'         =>  'BlackBerry',
    		'/webos/i'              =>  'Mobile'
    	);
    	$browser_array  = array(
    		'/msie/i'       =>  'Internet Explorer',
    		'/firefox/i'    =>  'Firefox',
    		'/safari/i'     =>  'Safari',
    		'/chrome/i'     =>  'Chrome',
    		'/edge/i'       =>  'Edge',
    		'/opera/i'      =>  'Opera',
    		'/netscape/i'   =>  'Netscape',
    		'/maxthon/i'    =>  'Maxthon',
    		'/konqueror/i'  =>  'Konqueror',
    		'/mobile/i'     =>  'Handheld Browser'
    	);
    	$os_platform = "Unknown";
    	$browser = "Unknown";
    	foreach($os_array as $regex => $value){ 
    		if(preg_match($regex, $user_agent)) $os_platform = $value;
    	}
    	foreach($browser_array as $regex => $value ) {
    		if(preg_match( $regex, $user_agent)) $browser = $value;
    	}
        return ['browser'=>$browser,'system'=>$os_platform];
    }
    // // 提取图片平均色值(耗时)
    // function extract_images_rgb($url){
    //     $im  =  imagecreatefromstring(file_get_contents($url));
    //     $rgb  =  imagecolorat ( $im ,  10 ,  15 );
    //     $r  = ( $rgb  >>  16 ) &  0xFF ;
    //     $g  = ( $rgb  >>  8 ) &  0xFF ;
    //     $b  =  $rgb  &  0xFF ;
    //     return "$r $g $b";
    //     // 加载图片
    //     // $image = imagecreatefrompng($url) or die('ext format err.');
    //     // // 获取图片中指定位置的颜色
    //     // $rgb = imagecolorat($image, 1, 2);
    //     // // 将rgb值转换为hex值
    //     // $hex = "#".str_pad(dechex($rgb), 6, "0", STR_PAD_LEFT); 
    //     // // 获取rgb
    //     // list($r, $g, $b) = array_map('hexdec', str_split($hex, 2));
    //     // return "$hex";
    // }
?>