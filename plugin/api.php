<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once( '../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    define('CDN_SWITCH', get_option('site_cdn_switcher'));
    define('CDN_SRC', get_option('site_cdn_src'));
    define('CDN_API', get_option('site_cdn_api'));
    $USE_SSE = isset($_GET['sse'])&&$_GET['sse'] || isset($_POST['sse'])&&$_POST['sse'];
    define('USE_STREAM', get_option('site_stream_switcher') && $USE_SSE);
    if(USE_STREAM) {
        header('X-Accel-Buffering: no');
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        set_time_limit(0); //防止超时
        ob_end_clean(); //清空（擦除）缓冲区并关闭输出缓冲
        ob_implicit_flush(1); //这个函数强制每当有输出的时候，即刻把输出发送到浏览器。这样就不需要每次输出（echo）后，都用flush()来发送到浏览器了
        function returnEventData($returnData, $event='message', $id=0, $retry=0, $delay=0){
            // if(!$returnData) return;
            $id = $id ? $id : time();
            $str = "id: {$id}" . PHP_EOL;
            if($event) $str.= "event: {$event}" . PHP_EOL;
            if($retry>0) $str .= "retry: {$retry}" . PHP_EOL;
            if(is_array($returnData) || is_object($returnData)) $returnData = json_encode($returnData);
            $str .= "data: " . $returnData . PHP_EOL;
            echo $str . PHP_EOL;
            if($delay>0) usleep($delay*1000*1000);
        }
    }
    $QSTR = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : false;
    if($QSTR) {
        parse_str($QSTR, $params);
        function send_auth_request(){
            global $params;
            $auth_api = get_request_param('auth');
            //setup api authentication
            if($auth_api){
                /*
                use admin-ajax.php instead of api if referer check included
                if ($auth_api === 'dirscaner') {
                    $scan_path = urldecode(get_request_param('path'));
                    $scan_deepscan = urldecode(get_request_param('deep'));
                    $scan_dironly = urldecode(get_request_param('dironly'));
                    $scan_extends = urldecode(get_request_param('extends'));
                    $scan_res = dirScaner($scan_path, $scan_deepscan, $scan_dironly, $scan_extends, true);
                    return $scan_res;
                }
                */
                $res = new stdClass();
                $api_file = '/'.$auth_api.'.php';
                $cdn_auth = get_option('site_chatgpt_auth');
                $request_url = custom_cdn_src(0, true).'/plugin/'.get_option('site_chatgpt_dir');
                if($cdn_auth){
                    // 覆写 api 路径可能导致请求鉴权失败（时效过期）
                    // $request_url = CDN_SWITCH&&CDN_API ? custom_cdn_src('api',true) : $request_url;
                    $stamp10x = time();
                    $stamp16x = dechex($stamp10x);
                    $signature = md5($cdn_auth.$api_file.$stamp16x);
                    $res->sign = $signature;
                    $res->ts = $stamp16x;
                }else{
                    $res->err = 'cdn disabled';
                }
                $exec = get_request_param('exec');
                if($exec){
                    // 通过此 api 发送代理请求，返回客户端为 server 端（custom server request-header）
                    $ch = curl_init();
                    $auth_url = $request_url.$api_file;
                    switch ($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $params = $_POST;
                            if($cdn_auth){
                                $params['sign'] = $signature;
                                $params['t'] = $stamp16x;
                            }
                            // 设置 cURL 选项
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 允许 cURL 自动跳转
                            curl_setopt($ch, CURLOPT_POST, true); // 设置为 POST 请求
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // 设置 POST 数据
                            break;
                        case 'GET':
                        default:
                            // cdn_auth MIGHT caused api call failure.
                            $auth_url = $auth_url.'?'.http_build_query($params);
                            if($cdn_auth) $auth_url = $auth_url.'&sign='.$signature.'&t='.$stamp16x;
                            // curl request
                            break;
                    }
                    // print_r($auth_url);
                    curl_setopt($ch, CURLOPT_URL, $auth_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // 连接时间
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // 连接超时
                    // curl_setopt($ch, CURLOPT_RETRIES, 3);  // 重试连接
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // custom server request-header
                    $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : $_SERVER["HTTP_X_FORWARDED_FOR"];
                    $ua = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : $params['ua'];
                    $headers = array(
                        "X-Forwarded-For: $ip",
                        "User-Agent: $ua",
                    );
                    // if($params['usememos']) {
                    //     array_push($headers, ['Authorization: Bearer ' . get_option('site_memos_apikey')]);
                    // }
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $response = curl_exec($ch);
                    // print_r($params);
                    $response = $response===false ? 'cURL Error ('.curl_errno($ch).'): '.curl_error($ch).'\n' : $response;
                    if(USE_STREAM) {
                        if(json_decode($response)!==null) $response = json_decode($response);
                        if (is_string($response)) {
                            print_r($response);
                        } else {
                            if(isset($response) && empty($response->code)) {
                                foreach($response as $key => $value) {
                                    if(!$value) continue; // ingore empty data
                                    // returnEventData($value, 'message', $key, 0, 1); // $md5.'!=='.$res_md5
                                    // output each-single-data
                                    foreach($value as $k => $val) {
                                        returnEventData($val, 'message', $key, 0, 1);
                                    }
                                }
                            }else{
                                returnEventData('null');
                            }
                        }
                    }else{
                        echo $response;
                    }
                    curl_close($ch);
                }else{
                    if(USE_STREAM) {
                        returnEventData($res);
                    }else{
                        print_r(json_encode($res));
                    }
                }
            }else{
                api_err_handle('param missing, requested api not found');
            }
        }
        if(CDN_SWITCH&&CDN_SRC || CDN_SWITCH&&CDN_API){
            $auth_path_array = array($_SERVER['SCRIPT_NAME'],$_SERVER['DOCUMENT_URI'],$_SERVER['REQUEST_URI']);
            $auth_host_array = array($_SERVER['HTTP_HOST'],$_SERVER['SERVER_NAME']);
            switch (true) {
                case CDN_API:
                    $cdn_auth = api_illegal_auth($auth_host_array, CDN_API);
                    break;
                default:
                    $cdn_auth = api_illegal_auth($auth_host_array, CDN_SRC);
                    break;
            }
            api_illegal_auth($auth_path_array, '/wp-content/themes/') || $cdn_auth ? api_err_handle('request illegal, cdn/api enabled(api)',403) : send_auth_request();
        }else{
            send_auth_request();
        }
    }else{
        api_err_handle('params missing, SERVER QUERY_STRING NOT EXISTS');
    }
?>