<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    // require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    require_once( '../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    // https://wordpress.stackexchange.com/questions/69184/how-to-load-wordpress-on-non-wp-page
    parse_str($_SERVER['QUERY_STRING'], $parameters);
    // 判断url传参或form表单参数
    if($parameters){
        $url = $parameters['url'];
        $title = $parameters['title'];
        $image = $parameters['image'];
        $name = $parameters['name'];
        $mail = $parameters['mail'];
        $content = $parameters['content'];
    }else{
        $url = $_POST['url'];
        $title = $_POST['title'];
        $image = $_POST['image'];
        $name = $_POST['name'];
        $mail = $_POST['mail'];
        $content = $_POST['content'];
        // 定义id和secret
        // $corpid = $_POST['site_wpwx_id'];
        // $corpsecret = $_POST['site_wpwx_secret'];
        // $msgtype = $_POST['site_wpwx_type'];
        // $agentid = $_POST['site_wpwx_agentid'];
    }
    $avatar = match_mail_avatar($mail);
    $description = "昵称: $name \n邮箱: $mail \n评论: $content";
    header("Content-type:text/html;charset=utf-8");  // 声明页面header
    function getToken(){
        // 定义id和secret
        $corpid = get_option('site_wpwx_id');
        $corpsecret = get_option('site_wpwx_secret');
        $dir = get_option('site_chatgpt_dir') ? get_option('site_chatgpt_dir').'/' : './';
        $file = $dir . '/access_token.php';
        if(!file_exists($file)) {
            // 新建 token 文件
            ob_start();
            echo '<?php'.PHP_EOL.'$access_token = NULL;'.PHP_EOL.'?>';
            $content = ob_get_contents();
            ob_end_clean();
            file_put_contents($file, $content);
        }
        include $file;  // 读取access_token
        // 判断是否过期或空值token
        if (time() > $access_token['expires'] || !isset($access_token['access_token']) || $access_token['access_token']==NULL){
            $access_token = array();
            $access_token['access_token'] = getNewToken($corpid,$corpsecret);
            $access_token['expires']=time()+7000;
            // 将数组写入php文件
            $arr = '<?php'.PHP_EOL.'$access_token = '.var_export($access_token,true).';'.PHP_EOL.'?>';
            $arrfile = fopen($file, "w");
            fwrite($arrfile,$arr);
            fclose($arrfile);
        }
        return $access_token['access_token'];  // 返回当前的access_token
    }
    // 获取新的access_token
    function getNewToken($corpid,$corpsecret){
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}";
        $access_token_Arr =  https_request($url);
        return $access_token_Arr['access_token'];
    }
    // curl请求函数
    function https_request ($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        return  json_decode($out,true);
    }
    // 发送应用消息函数
    function send($data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.getToken());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
    // $msgdata = file_get_contents(get_bloginfo('template_directory') . '/plugin/notify-datatype.php?title='.$title.'&desc='.$description.'&url='.$url.'&img='.$image.'&btn=查看详情&type='.$type, false);   // require var_export after file_get_contents return
    // print_r(var_export(json_decode($msgdata, true), true));
    $msgtype = get_option('site_wpwx_type');
    switch ($msgtype) {
        case 'news':
            $datatype = array(
               "articles" => array(
                   array(
                       "title" => $title,
                       "description" => $description,
                       "url" => $url,
                       "picurl" => $image
                   )
                ),
            );
            break;
        case 'template_card':
            $datatype = array(
                'card_type' => 'news_notice',
                'source' => array(
                    'icon_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/favicon.ico',
                    'desc' => $url,
                    'desc_color' => 1,
                ),
                'main_title' => array(
                    'title' => $title,
                    'desc' => $description
                ),
                'image_text_area' => array(
                    'type' => 1,
                    'url' => $url,
                    'title' => $name,
                    'desc' => $content,
                    'image_url' => $avatar
                ),
                'card_image' => array(
                    'url' => $image,
                    'aspect_ratio' => 1.3
                ),
                'jump_list' => array(
                    array(
                        'type' => 1,
                        'title' => '查看详情',
                        'url' => $url
                    )
                ),
                'card_action' => array(
                    'type' => 1,
                    'url' => $url
                )
            );
            break;
        default:  // textcard
            $datatype = array(
                'title' => $title,
                'description' => $description, //$description
                'url' => $url,
                'btntxt' => $parameters['btn'],
            );
            break;
    }
    // 卡片消息体
    $postdata = array(
        'touser' => '@all',  // 指定用户 @all "UserID1|UserID2|UserID3",
        'toparty' => '',  // 指定部门
        'msgtype' => $msgtype,
        'agentid' => get_option('site_wpwx_agentid'),
        $msgtype => $datatype,//json_decode($msgdata),//var_export(json_decode($msgdata, true), true),//,  // 解析 json 对象为数组并导出为合法php结构数据
        'enable_id_trans' => 0,
        'enable_duplicate_check' => 0,
        'duplicate_check_interval' => 1800
    );
    // 调用发送函数
    echo send(json_encode($postdata));
?>