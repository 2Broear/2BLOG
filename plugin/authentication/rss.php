<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once('../../../../../wp-load.php');  // Load WordPress Core
    
    // 检查并返回 xhr 请求携带参数
    function get_request_param(string $param) {
        $res = null;
        if(!isset($_REQUEST[$param])) return $res;
        switch (true) {
            case isset($_GET[$param]):
                $res = $_GET[$param];
                break;
            case isset($_POST[$param]):
                $res = $_POST[$param];
                break;
            default:
                $res = false;
                break;
        }
        return trim($res);
    }
    
    $link_slug = get_request_param('cat');
    $use_clear = get_request_param('clear');
    $use_cache = get_request_param('cache');
    // $use_sse = get_request_param('sse');
    if ($use_clear) {
        echo "Clear all caches before updating $link_slug, standby..";
        // 清除（全部） rss 订阅
        $link_cats = get_links_category();
        foreach ($link_cats as $link_cat) {
            update_option('site_rss_' . $link_cat->slug . '_cache', '');  //清除（重建）聚合内容
        }
        // declear $use_cache as false by force
        $use_cache = 0;
    }
    if (!$link_slug) {
        echo 'Empty request category, Please specific a cat params!';
        exit;
    }
    $link_apis = get_api_refrence('rss', true) . "cat=$link_slug&limit=3&output=0&cache=0&clear=0";
    $link_limit = get_request_param('limit');
    $use_output = get_request_param('output');
    $use_chunk = get_request_param('chunk');
    
    // use of mysql caches
    $output_json = '';
    $output_sw = false;
    $caches_sw = get_option('site_cache_switcher');
    $caches_inc = get_option('site_cache_includes');
    $caches_name = 'site_rss_' . $link_slug . '_cache';
    
    if($caches_sw) {
        $output_sw = in_array('rssfeeds', explode(',', $caches_inc));
        $output_caches = get_option($caches_name);
        if ($output_sw && $output_caches && $use_cache) {
            if ($use_output) {
                echo "<p style='text-align:right'>Loaded from $caches_name, <a href='javascript:;' class='fetch-reload' data-api='$link_apis'>reload $link_slug?</a></p>";
                the_rss_feeds(json_decode($output_caches));
            } else {
                print_r(json_decode($output_caches));
            }
            exit;
        }
    }
    
    // updating instantly
    if(strlen($output_json)===0 || !$output_sw) {
        
        $linked_urls = array();
        $link_marks = get_site_bookmarks($link_slug);
        
        foreach ($link_marks as $link_mark) {
            if ($link_mark->link_rss && $link_mark->link_visible==='Y') array_push($linked_urls, $link_mark);
        }
        
        // fetch_rss_feeds_via_url plus array_chunk limits
        $output_json = parse_rss_data($linked_urls, $link_limit, $use_chunk||10);
        if($output_json && $output_sw) { // && !$use_cache
            // echo "updating caches..";
            update_option($caches_name, wp_kses_post(preg_replace( "/\s(?=\s)/","\1", $output_json )));
        }else{
            echo '<p style="text-align:center">No rss feeds found on category ' . $link_slug . '</p>';
        }
    }
    
    // // 计算 JSON 数据的长度
    // $contentLength = strlen($output_json);
    // // 设置 Content-Type 为 application/json，这是发送 JSON 数据的标准 MIME 类型
    // header("Content-Type: application/json");
    // // 设置 Content-Length 响应头
    // header("Content-Length: $contentLength");
    // print_r($output_json);
    if (!$use_output) {
        print_r($output_json);
        exit;
    }
    the_rss_feeds(json_decode($output_json));
?>