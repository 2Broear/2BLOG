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
    
    $req_cat = get_request_param('cat');
    $link_limit = get_request_param('limit');
    $use_clear = get_request_param('clear');
    $do_update = get_request_param('update');
    $do_output = get_request_param('output');
    // $do_refresh = get_request_param('refresh');
    // if ($do_refresh) {
    //     wp_clear_scheduled_hook('scheduled_rss_feeds_updates_hook');
    //     echo 200;
    //     exit;
    // }
    // $do_format = get_request_param('format');
    // $use_cache = get_request_param('cache');
    // $use_sse = get_request_param('sse');
    $links_slug = get_links_category('slug');
    if (!in_array($req_cat, $links_slug)) {
        echo $req_cat ? '<pre>Unknown category: "' . $req_cat . '", Please try again.</pre>' : '<pre>Empty category! Please specify a cat param.</pre>';
        exit;
    }
    if ($use_clear) {
        echo "<pre>Clear all caches before updating $req_cat, standby..</pre>";
        // 清除（全部） rss 订阅
        foreach ($links_slug as $link_cat) {
            update_option('site_rss_' . $link_cat . '_cache', '');  //清除（重建）聚合内容
        }
        // declear $use_cache as false by force
        $do_update = 1; //$use_cache = 0;
    }
    
    // use of mysql caches
    $output_sw = false;
    $caches_sw = get_option('site_cache_switcher');
    $caches_inc = get_option('site_cache_includes');
    $caches_name = 'site_rss_' . $req_cat . '_cache';
    
    if($caches_sw) {
        $output_sw = in_array('rssfeeds', explode(',', $caches_inc));
        $output_caches = get_option($caches_name);
        if ($output_sw && $output_caches && !$do_update) {
            if ($do_output) {
                $link_apis = get_api_refrence('rss', true) . "cat=$req_cat&limit=3&update=1&output=0&format=0";
                echo "<p style='text-align:right'>Loaded from $caches_name, <a href='javascript:;' class='fetch-reload' data-api='$link_apis'>reload $req_cat?</a></p>";
                the_rss_feeds(json_decode($output_caches));
            } else {
                print_r($output_caches);
            }
            exit;
        }
    }
    
    // updating instantly
    $output_json = '';
    // if(strlen($output_json)===0 || !$output_sw) {
    $use_chunk = get_request_param('chunk');
    
    $linked_urls = array();
    $link_marks = get_site_bookmarks($req_cat);
    
    foreach ($link_marks as $link_mark) {
        if ($link_mark->link_rss && $link_mark->link_visible==='Y') array_push($linked_urls, $link_mark);
    }
    
    // fetch_rss_feeds_via_url plus array_chunk limits
    $output_json = parse_rss_data($linked_urls, $link_limit, $use_chunk||10);
    if($output_json && $output_sw) { // && !$use_cache
        // echo "updating caches..";
        update_option($caches_name, wp_kses_post(preg_replace( "/\s(?=\s)/","\1", $output_json )));
    } else {
        echo '<p style="text-align:center">No rss feeds found on category ' . $req_cat . '</p>';
    }
    // }
    
    // // 计算 JSON 数据的长度
    // $contentLength = strlen($output_json);
    // // 设置 Content-Type 为 application/json，这是发送 JSON 数据的标准 MIME 类型
    // header("Content-Type: application/json");
    // // 设置 Content-Length 响应头
    // header("Content-Length: $contentLength");
    // print_r($output_json);
    if ($do_output) {
        the_rss_feeds(json_decode($output_json));
    } else {
        print_r($output_json);
    }
?>