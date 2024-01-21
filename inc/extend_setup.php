<?php
    // custom_template_path for custom page templates(disable filter will NOT able to specific template in page)
    add_filter('theme_page_templates', 'custom_template_path');
    function custom_template_path($templates) {
        // global $GET_TEMPLATE_DERECTORY, $CUSTOM_TEMPLATE_PATH;
        $dir = get_template_directory() . '/inc/templates';
        $templates = scan_templates_dir($templates, $dir);
        return $templates;
    }
    function scan_templates_dir($templates, $dir=false) {
        // global $GET_TEMPLATE_DERECTORY, $CUSTOM_TEMPLATE_PATH;
        $dir = $dir ? $dir : get_template_directory() . '/inc/templates';
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $templates = scan_templates_dir($templates, $path);
            } else {
                $file_name = basename($file);
                // MUST specific $template_slug for custom use
                $template_slug = str_replace(get_template_directory(), '', $path);
                $template_data = get_file_data($path, array('Template Name' => 'Template Name')); // 获取模板文件的头部信息
                if (!empty($template_data['Template Name'])) {
                    $template_name = $template_data['Template Name'];
                    // $template_slug = sanitize_title($template_name); // 使用模板名称作为数组键
                    $templates[$template_slug] = $template_name; // 将模板名称存储到数组中
                }else{
                    $templates[$template_slug] = $file_name;
                }
            }
        }
        return $templates;
    }
    
    // 通过分类模板名称获取绑定的分类别名
    function get_template_bind_cat($template=false){
        global $wpdb;
        $rewrite_dir = '/inc/templates/';
        $template = $rewrite_dir . $template; //prefix for custom templates path
        $template_term_id = $wpdb->get_var("SELECT term_id FROM $wpdb->termmeta WHERE meta_value = '$template'");
        // return !get_category($template_term_id)->errors ? get_category($template_term_id) : get_category(1);
        unset($wpdb);
        return get_category($template_term_id);
    }
    // get bind category-template cat by specific binded-temp post_id
    function get_cat_by_template($temp='news', $parm=false){
        $cats = get_template_bind_cat('category-'.$temp.'.php');
        return !$cats->errors ? ($parm ? $cats->$parm : $cats) : false;
    }
    
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