<?php
    $USE_SSE = isset($_GET['sse'])&&$_GET['sse'] || isset($_POST['sse'])&&$_POST['sse'];
    if($USE_SSE) {
        define('WP_USE_THEMES', false);  // No need for the template engine
        require_once( '../../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
        // define('USE_STREAM', get_option('site_stream_switcher'));
    }
    if (!function_exists('get_request_param')) {
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
    }
    
    define('CACHED_PATH', './mark_data.php');
    define('REQUEST_pid', get_request_param('pid'));
    define('REQUEST_rid', get_request_param('rid'));
    define('REQUEST_uid', get_request_param('uid'));
    define('REQUEST_ts', get_request_param('ts'));
    define('SECURED_ts', md5(REQUEST_ts));  // secure REQUEST_ts for local SECURED_ts compare
    define('REQUEST_mail', get_request_param('mail')); // local compare for mail-requests
    define('SECURED_mid', md5(REQUEST_mail));  // Exposed on public
    define('REQUEST_nick', get_request_param('nick'));
    define('REQUEST_text', get_request_param('text'));
    define('REQUEST_note', get_request_param('note'));
    define('REQUEST_like', get_request_param('like'));
    define('REQUEST_liked', get_request_param('liked'));
    define('SAVE_prefix', 'marker-' . REQUEST_pid);
    define('EXEC_fetch', get_request_param('fetch'));
    define('EXEC_count', get_request_param('count'));
    define('EXEC_delete', get_request_param('del'));
    define('iLEGAL_request', !REQUEST_ts && !REQUEST_mail);
    
    function get_update_status($msg='okay', $code=200){
        return array('msg' => $msg,'code' => $code); //json_encode(array('msg'=>$msg, 'code'=>$code));
    }
    
    function purify_marker_data($scan_mark, $scan_post=false){
        if(!$scan_mark || !is_array($scan_mark)) {
            return false;
        }
        $scan_temp = $scan_mark; // $scan_temp = json_decode(json_encode($scan_mark), true);
        foreach ($scan_temp as $items) {
            if(!is_array($items)) continue;
            foreach ($items as $item) {
                if($scan_post) {
                    // scan specificed post markers
                    if(!is_object($item)) continue;
                    unset($item->mail);
                    unset($item->ts);
                    unset($item->ip);
                }else{
                    // scal full-dir posts markers
                    if(!is_array($item)) continue;
                    foreach ($item as $obj) {
                        if(!is_object($obj)) continue;
                        unset($obj->mail);
                        unset($obj->ts);
                        unset($obj->ip);
                    }
                }
            }
        };
        return $scan_temp;
    }
    
    // 更新本地数据函数
    function update_marker_record($path=CACHED_PATH, $records=array(), $res_stats=false) {
        // $file = fopen($path, "a+"); // 以追加模式打开文件
        $file = fopen($path, "w"); // 获取文件锁
        if (flock($file, LOCK_EX)) {
            $arr = '<?php' . PHP_EOL . '$cached_mark = ' . var_export($records, true) . ';' . PHP_EOL . '?>';
            fwrite($file, $arr);
            fflush($file); // 将缓冲区的内容立刻写入文件
            // sleep(1); // wait for 1 second then unlock file.
            flock($file, LOCK_UN); // 释放文件锁
        } else {
            $res_stats = get_update_status('Standby for a few seconds, another progress(previous updates) on processing..', 500);
        }
        fclose($file); // 关闭文件
        if($res_stats) return $res_stats; // sleep(1); // wait for(fwrite complete) 1 second then return stats
    }
    // 操作本地数据函数
    function output_marker_records() {
        // 删除指定文章标记
        if(EXEC_delete) { 
            include CACHED_PATH;  // 读取本地记录
            $memory_caches = &$cached_mark;
            $_marker = &$memory_caches[SAVE_prefix];
            if(!isset($_marker)) {
                return get_update_status('marker #' . SAVE_prefix . ' not found.', 404); // 不存在文章记录
            }
            if(iLEGAL_request) {
                return get_update_status('request failed! user(ts&mail) identification failure. (note that if you are the ownner, then you might want to exec the deletion at the browser-environment that you marked before)', 403); // 非法请求
            }
            $marked_secured = &$_marker[SECURED_mid];
            if(isset($marked_secured)) {
                foreach ($marked_secured as $index => &$obj) {
                    if(!is_object($obj)) continue;
                    // 用户校验（用户存在，本地ts验证 / 远程mail验证）
                    if(SECURED_ts==$obj->ts && REQUEST_mail==$obj->mail) { //SECURED_tid === $obj->tid
                        // 标记检查（使用 $index 确定标记用户）
                        if(REQUEST_rid === $obj->rid){
                            unset($marked_secured[$index]); // 移除标记用户
                            $marked_secured = array_values($marked_secured); // 重新索引数组（避免二次新增 array_push 覆盖现有数据）
                            return update_marker_record(CACHED_PATH, $memory_caches, get_update_status(SAVE_prefix . '-' . SECURED_mid . '['.$index.'] deleted.')); // 写入记录
                            break;
                        }
                    }
                }
            };
            // 用户不存在，遍历文章内所有记录
            foreach ($_marker as $key => &$item) {
                if(!is_array($item)) continue;
                foreach ($item as $index => &$obj) {
                    if(!is_object($obj)) continue;
                    if(SECURED_ts==$obj->ts && REQUEST_mail==$obj->mail) { // 用户校验（用户匿名，本地ts验证 / 远程mail验证）
                        if(REQUEST_rid === $obj->rid) { // 标记检查（使用 $key 确定标记范围）
                            unset($_marker[$key][$index]);
                            $_marker[$key] = array_values($_marker[$key]);
                            return update_marker_record(CACHED_PATH, $memory_caches, get_update_status('marker #' . SAVE_prefix . '-' . '['.$index.'] deleted.'));
                            break 2; // 退出二级循环（避免向后查询）
                        }
                    }else{
                        return get_update_status('request failed! user(ts&mail) identification failure.', 400); // 非法请求
                    }
                }
            };
            return get_update_status('request user('.REQUEST_mail.') not exist', 404);
        };
        
        // 初始化文件
        if(!file_exists(CACHED_PATH)) {
            update_marker_record(CACHED_PATH); // 初始化文件
            include CACHED_PATH; // 加载文件
            return $cached_mark; // 返回记录
        };
        
        // 加载文件
        include CACHED_PATH; 
        // 获取指定文章标记 // sleep(1);
        if(EXEC_fetch) {
            $cached_mark = purify_marker_data($cached_mark); // clear all unique id (no public exposed vars)
            if(isset($cached_mark[SAVE_prefix])) {
                if(EXEC_count) {
                    return isset($cached_mark[SAVE_prefix][SECURED_mid]) ? get_update_status(count($cached_mark[SAVE_prefix][SECURED_mid])) : get_update_status('no user['.SECURED_mid.'] records found on #'.SAVE_prefix, 404);
                }
                return $cached_mark[SAVE_prefix];
            }
            return get_update_status('no records found on #'.SAVE_prefix, 404);
        };
        // 新增文章标记
        if(iLEGAL_request) {
            return get_update_status('request failed! user(ts&mail) identification failure.', 400); // 非法请求
        };
        $new_mark = new stdClass();
        $new_mark->rid = REQUEST_rid;
        $new_mark->uid = REQUEST_uid;
        $new_mark->nick = REQUEST_nick;
        $new_mark->mail = REQUEST_mail;
        $new_mark->text = REQUEST_text;
        if(REQUEST_note) $new_mark->note = REQUEST_note;
        $new_mark->date = date('Y-m-d');
        $new_mark->ts = SECURED_ts;
        $new_mark->ip = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $new_mark->ua = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : (get_request_param('ua') ? get_request_param('ua') : NULL);
        // add new RECORD TO 'menmory quotes'
        $memory_caches = &$cached_mark;
        $_marker = &$memory_caches[SAVE_prefix]; // post records
        // 已标记文章
        if(isset($_marker)) {
            $exists_code = 403; // guest
            $exists_records = false; // not exist
            foreach ($_marker as $index => $item) {
                if(!is_array($item)) continue;
                foreach ($item as $obj) {
                    if(!is_object($obj)) continue;
                    if(REQUEST_text === $obj->text) {
                        if(REQUEST_mail === $obj->mail) $exists_code = 400; // admin
                        $exists_records = $obj;
                        break 2;
                    }
                }
            }
            // return $exists_records;
            if($exists_records) {
                $exists_rid = $exists_records->rid;
                $exists_date = $exists_records->date;
                switch ($exists_code) {
                    case 400:
                        $exists_msg = 'exists context detected! you might marked this content already? (rid#'.$exists_rid.' in '.$exists_date.')';
                        break;
                    case 403:
                    default:
                        $exists_nick = $exists_records->nick;
                        $exists_msg = 'mark already exists! record (#'.$exists_rid.'): "'.rawurldecode($exists_records->text).'" has already marked by '.$exists_nick.' at '.$exists_date.' on '.SAVE_prefix;
                        // non-notes requires..
                        // additional like button no longer required noted marks
                        if(REQUEST_like) { // && !isset($exists_records->note)
                            $exists_like = &$exists_records->like; // $memory_quote of like
                            $exists_code = 200; // no alert on abort
                            $exists_msg = 'you liked the mark(#'.$exists_rid.') by '.$exists_nick;
                            if (REQUEST_liked) {
                                $exists_like = array_filter($exists_like, function($value) {
                                    return $value !== REQUEST_like;
                                });
                                $exists_msg = 'you dis-liked the mark(#'.$exists_rid.')';
                                break;
                            }
                            if(!isset($exists_like) || !is_array($exists_like)) {
                                $exists_like = array();
                                array_push($exists_like, REQUEST_like);
                                break;
                            }
                            if(in_array(REQUEST_like, $exists_like)) {
                                $exists_code = 400; // permission granted but server rejected
                                $exists_msg = 'you liked this mark(#'.$exists_rid.') already!';
                                break;
                            }
                            array_push($exists_like, REQUEST_like);
                            break;
                        }
                        // break;
                }
                return update_marker_record(CACHED_PATH, $memory_caches, get_update_status($exists_msg, $exists_code));
            };
            // 已存在用户（mid）且不为“空”
            $result_stats = get_update_status('marker(add) saved on #'.SECURED_mid.' successfully');
            $exists_marker = &$_marker[SECURED_mid]; // user records(local compare)
            if(isset($exists_marker)) {
                $exists_marker = array_values($exists_marker); // 重新索引数组，避免数组索引混乱（手动删除 mark_data ）时导致新增用户数据被覆盖
                if(!isset($exists_marker[0]->mail) || REQUEST_mail !== $exists_marker[0]->mail) { // 请求 mail 参数匹配本地用户 mail
                    // $result_stats = get_update_status('user mail verification failure #'.REQUEST_mail, 403);
                }
                array_push($exists_marker, $new_mark); // push current user
                return update_marker_record(CACHED_PATH, $memory_caches, $result_stats);
            };
            // 新增用户数据
            $_marker[SECURED_mid] = array(); // create new user
            array_push($_marker[SECURED_mid], $new_mark);
            return update_marker_record(CACHED_PATH, $memory_caches, $result_stats);
        };
        // 初始化/新增文章/用户标记
        $_marker = array();
        $_marker[SECURED_mid] = array();
        array_push($_marker[SECURED_mid], $new_mark);
        // 写入本地记录
        return update_marker_record(CACHED_PATH, $memory_caches, get_update_status('marker(new) saved on #'.SAVE_prefix.' by '.SECURED_mid));
    }
    
    $response = output_marker_records();
    print_r(json_encode($response));
?>