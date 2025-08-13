<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once('../../../../../wp-load.php');  // Load WordPress Core
    
    $query_cat = get_request_param('cat');
    $query_num = get_request_param('limit');
    $query_key = urldecode(get_request_param('key'));
    $query_val = urldecode(get_request_param('value'));
    $do_query = $query_key && $query_val;
    $do_clear = get_request_param('clear');
    $do_output = get_request_param('output');
    $do_update = get_request_param('update');
    
    if ($do_query) {
        /**
         * 在对象数组中搜索指定键值匹配的元素
         * @param array $array 要搜索的数组
         * @param string $key 要匹配的键名（如 "author"、"title"、"date"）
         * @param mixed $value 要匹配的值
         * @return array 返回匹配的所有元素（数组形式）
         */
        function searchByKeyValue($array, $key, $value) {
            // return array_filter($array, function($item) use ($key, $value) {
            //     return isset($item->$key) && $item->$key === $value;
            // });
            foreach ($array as $item) {
                if (isset($item->$key) && strcasecmp($item->$key , $value) === 0) { //$item->$key === $value
                    return $item; // 直接返回匹配的 stdClass 对象
                }
            }
            return null; // 未找到返回 null
        }
    }
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
    if (!in_array($query_cat, $links_slug)) {
        $error_msg = $query_cat ? '<pre>Unknown category: "' . $query_cat . '", Please try again.</pre>' : '<pre>Empty category! Please specify a cat param.</pre>';
        report_logs($error_msg); // 记录日志
        echo $error_msg;
        exit;
    }
    if ($do_clear) {
        $message = "<pre>Clear all caches before updating $query_cat, standby..</pre>";
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
    $caches_name = 'site_rss_' . $query_cat . '_cache';
    
    if($caches_sw) {
        $output_sw = in_array('rssfeeds', explode(',', $caches_inc));
        $output_caches = get_option($caches_name);
        if ($output_sw && !$do_update) {
            if ($output_caches === '') {
                print_r([]);
                exit;
            }
            if (!$do_output) {
                if ($do_query) {
                    $output_caches = json_decode($output_caches);
                    $output_caches = searchByKeyValue($output_caches, $query_key, $query_val);
                }
                print_r(json_encode($output_caches)); //print_r("Empty query key/value [$query_key: $query_val]");
                exit;
            }
            $link_apis = get_api_refrence('rss', true) . "cat=$query_cat&limit=3&update=1&output=0";
            echo "<p style='text-align:right'>Loaded from $caches_name, <a href='javascript:;' class='fetch-reload' data-api='$link_apis'>reload $query_cat?</a></p>";
            the_rss_feeds(json_decode($output_caches), $query_num);
            exit;
        }
    }
    
    // updating instantly
    $output_json = '';
    // if(strlen($output_json)===0 || !$output_sw) {
    $use_chunk = get_request_param('chunk') || 10;
    
    $linked_urls = array();
    $link_marks = get_site_bookmarks($query_cat);
    
    foreach ($link_marks as $link_mark) {
        if ($link_mark->link_rss && $link_mark->link_visible==='Y') array_push($linked_urls, $link_mark);
    }
    
    // fetch_rss_feeds_via_url plus array_chunk limits
    $output_json = parse_rss_data($linked_urls, $query_num, $use_chunk);
    if($output_json && $output_sw) { // && !$use_cache
        // echo "updating caches..";
        update_option($caches_name, wp_kses_post(preg_replace( "/\s(?=\s)/","\1", $output_json )));
    } else {
        echo '<p style="text-align:center">No rss feeds found on category ' . $query_cat . '</p>';
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