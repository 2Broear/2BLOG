<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once('../../../../../wp-load.php');  // Load WordPress Core
    
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
        $error_msg = $req_cat ? '<pre>Unknown category: "' . $req_cat . '", Please try again.</pre>' : '<pre>Empty category! Please specify a cat param.</pre>';
        report_logs($error_msg); // 记录日志
        echo $error_msg;
        exit;
    }
    if ($use_clear) {
        $message = "<pre>Clear all caches before updating $req_cat, standby..</pre>";
        report_logs($message); // 记录日志
        echo $message;
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
        if ($output_sw && !$do_update) {
            if ($output_caches === '') {
                print_r([]);
                exit;
            }
            if (!$do_output) {
                print_r($output_caches);
                exit;
            }
            $link_apis = get_api_refrence('rss', true) . "cat=$req_cat&limit=3&update=1&output=0&format=0";
            echo "<p style='text-align:right'>Loaded from $caches_name, <a href='javascript:;' class='fetch-reload' data-api='$link_apis'>reload $req_cat?</a></p>";
            the_rss_feeds(json_decode($output_caches));
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
    
    // 计算 JSON 数据的长度
    $contentLength = strlen($output_json);
    // 设置 Content-Length 响应头
    header("Content-Length: $contentLength");
    // // 使用 Transfer-Encoding: chunked !502
    // header("Transfer-Encoding: chunked");
    // print_r($output_json);
    if ($do_output) {
        // report_logs("已成功输出（更新）RSS HTML."); // 记录日志
        header('Content-Type: text/html; charset=utf-8');
        the_rss_feeds(json_decode($output_json));
    } else {
        // report_logs("已成功输出 RSS JSON."); // 记录日志
        header("Content-Type: application/json");
        print_r($output_json);
    }
?>