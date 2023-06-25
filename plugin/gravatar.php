<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once('../../../../wp-load.php');  // Load WordPress Core $_SERVER['DOCUMENT_ROOT'].'/wp-load.php'
    $QUERY_STRING = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : false;
    if(!$QUERY_STRING){
        api_err_handle('Param missing, Perhaps the QUERY_STRING not exist.',400);
        exit;
    }
    parse_str($QUERY_STRING, $params);
    $email = !empty($params['email']) ? $params['email'] : false;
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        api_err_handle('Param error, email not valid or exist.',400);
        exit;
    }
    $url = get_option("site_avatar_mirror").'avatar/'.md5($email);
    $jump = !empty($params['jump']) ? $params['jump'] : false;
    $jump ? header("Location: $url") : api_err_handle($url);
    // if($jump){
    //     // header('Content-Type: text/html');
    //     echo $url;
    //     // header('Content-Type: image/jpeg');
    //     // header("Location: $url"); // header("HTTP/1.1 301 Moved Permanently");
    // }else{
    //     api_err_handle($url);
    // }
    // $json ? api_err_handle($url) : header("Location: $url");
?>