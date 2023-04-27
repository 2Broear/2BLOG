<?php
    parse_str($_SERVER['QUERY_STRING'], $params);
    $auth_api = array_key_exists('auth', $params) ? $params['auth'] : $_POST['auth'];
    //setup api authentication
    if($auth_api){
        define('WP_USE_THEMES', false);  // No need for the template engine
        // require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
        require_once( '../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
        $res = new stdClass();
        $api_file = '/'.$auth_api.'.php';
        $cdn_auth = get_option('site_chatgpt_auth');
        $request_url = custom_cdn_src(0,true).'/plugin/authentication';
        if($cdn_auth){
            $request_url = get_option('site_cdn_api') ? custom_cdn_src('api',true) : $request_url;
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
        echo 'request api missing';
    }
?>