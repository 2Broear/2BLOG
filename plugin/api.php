<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once( '../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    define('CDN_SWITCH', get_option('site_cdn_switcher'));
    define('CDN_SRC', get_option('site_cdn_src'));
    define('CDN_API', get_option('site_cdn_api'));
    if(CDN_SWITCH&&CDN_SRC){ //||CDN_API
        $auth_path_array = array($_SERVER['SCRIPT_NAME'],$_SERVER['DOCUMENT_URI'],$_SERVER['REQUEST_URI']);
        $auth_host_array = array($_SERVER['HTTP_HOST'],$_SERVER['SERVER_NAME']);
        api_illegal_auth($auth_path_array, '/wp-content/themes/') || api_illegal_auth($auth_host_array, CDN_SRC) ? api_err_handle('request illegal, cdn enabled',403) : send_auth_request();
    }else{
        send_auth_request();
    }
    function send_auth_request(){
        $query_string = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : false;
        if($query_string){
            parse_str($query_string, $params);
            $auth_api = array_key_exists('auth', $params) ? $params['auth'] : $_POST['auth'];
            //setup api authentication
            if($auth_api){
                $res = new stdClass();
                $api_file = '/'.$auth_api.'.php';
                $cdn_auth = get_option('site_chatgpt_auth');
                $request_url = custom_cdn_src(0,true).'/plugin/'.get_option('site_chatgpt_dir','authentication');
                if($cdn_auth){
                    $request_url = CDN_SWITCH&&CDN_API ? custom_cdn_src('api',true) : $request_url;
                    $stamp10x = time();
                    $stamp16x = dechex($stamp10x);
                    $signature = md5($cdn_auth.$api_file.$stamp16x);
                    $res->sign = $signature;
                    $res->ts = $stamp16x;
                }else{
                    $res->err = 'cdn disabled';
                }
                $exec = array_key_exists('exec', $params) ? $params['exec'] : $_POST['exec'];
                if($exec){
                    $pid = array_key_exists('pid', $params) ? $params['pid'] : $_POST['pid'];
                    $auth_url = $request_url.$api_file.'?pid='.$pid;
                    if($cdn_auth) $auth_url = $auth_url.'&sign='.$signature.'&t='.$stamp16x;
                    // curl request
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $auth_url);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    echo $response;
                }else{
                    print_r(json_encode($res));
                }
            }else{
                api_err_handle('param missing, requested api not found'); // echo '{"code":200,"msg":"param missing, requested api not found"}'; // echo 'request api missing';
            }
        }else{
            api_err_handle('params missing, SERVER QUERY_STRING NOT EXISTS'); // echo '{"code":200,"msg":"params missing, SERVER QUERY_STRING NOT EXISTS"}';
        }
    }
?>