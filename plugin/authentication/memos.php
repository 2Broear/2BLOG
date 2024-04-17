<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once('../../../../../wp-load.php');  // Load WordPress Core
    define('API_ADDRESS', get_option('site_memos_proxy'));
    define('API_PATTERN', get_option('site_memos_pattern'));
    define('API_QUERIES', $_SERVER['QUERY_STRING']);
    $api_type = API_PATTERN==='/' ? "" : API_PATTERN;
    define('API_URL', API_ADDRESS . '/api/v1/memo'.$api_type.'?' . API_QUERIES);
    define('API_KEY', get_option('site_memos_apikey'));
    // print_r(API_URL);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => API_URL,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . API_KEY
        ),
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 0,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ));
    $res = curl_exec($curl);
    if ($res === false) {
        $errno = curl_errno($curl); //$ch
        $error = curl_error($curl); //$ch
        $res = json_encode(array('error' => array ('message' => 'cURL Error ('.$errno.'): '.$error.' (check CURLOPT_URL?)','type' => 'curl_request_error','created'=>time())),true);
    }
    curl_close($curl);
    print_r($res);
?>