<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    // require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    require_once( '../../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    // print_r($_SERVER);
    parse_str($_SERVER['QUERY_STRING'], $params);
    // 判断url传参或form表单参数
    $pid = array_key_exists('pid', $params) ? $params['pid'] : $_POST['pid'];
    $pids = get_post($pid);
    $post_type = $pids->post_type;
    $post_exist = get_post_status($pid); //!is_null(get_post($pid)); //post_exists($title);//
    if($pid&&$post_exist){
        //分析以上提供的信息简述文章用意  分析梳理以上信息的逻辑与结构，简述文章用意
        $requirements = '标题：'.$pids->post_title.'，作者：'.get_the_author_meta('display_name', get_post_field('post_author', $pid)).'，内容：'.str_replace(array("\r\n", "\r", "\n"), " ", wp_strip_all_tags($pids->post_content)).'。'.get_post_meta($pid, "post_feeling", true); 
        // $requirements = $post_content.'。'.get_option('site_chatgpt_require','分析以上信息，简述文章用意'); 
        define('OPENAI_API_KEY', get_option('site_chatgpt_apikey'));
        define('OPENAI_PROXY', get_option('site_chatgpt_proxy','https://api.openai.com'));
        define('OPENAI_MODEL', get_option('site_chatgpt_model','text-davinci-003'));
        function curlRequest($question, $maxlen=1024) {
            $gpt_turbo = OPENAI_MODEL==='gpt-3.5-turbo';
            $post_data = array(
                "model" => OPENAI_MODEL, //ada
                'temperature' => 0.8,
                "max_tokens" => $maxlen,  // works for completion_tokens only
                "prompt" => $question.'。分析上述内容，简述文章用意，注意精简字数',
            );
            if($gpt_turbo){
                unset($post_data['prompt']);
                $post_data = array_merge($post_data, array('messages' => [["role" => "system", "content" => '分析并简述文章用意，注意精简内容'],["role" => "user", "content" => $question]]));
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $gpt_turbo ? OPENAI_PROXY.'/v1/chat/completions' : OPENAI_PROXY.'/v1/completions', //聊天模型
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($post_data),
              CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . OPENAI_API_KEY
              ),
            ));
            $res = curl_exec($curl);
            curl_close($curl);
            return $res;
        }
        
        
        header("Content-type:text/html;charset=utf-8");  // 声明页面header
        $cached_post = array();
        $cached_path = './chat_data.php';
        // 初始化php文件，返回记录
        function chatGPT_init($caches, $new=false){
            global $pid, $requirements, $cached_path, $response, $post_exist, $post_type;
            switch (true) {
                case !$post_exist:
                    $err_msg = 'Unabled to reach request post: not found';
                    break;
                case $post_type!=='post':
                    $err_msg = 'Unsupported request post type found: pid';
                    break;
                default:
                    $err_msg = NULL;
                    break;
            }
            $request_ip = $_SERVER["REMOTE_ADDR"];
            $request_ua = $_SERVER["HTTP_USER_AGENT"];
            // 创建临时记录，防止多请求并发
                $caches['chat_pid_'.$pid] = array('error' => array ('message' => 'standby, another requesting in busy..','type' => 'request_inqueue_busy','created'=>time(),'ip'=>$request_ip,'ua'=>$request_ua));
                $temp = '<?php'.PHP_EOL.'$cached_post = '.var_export($caches,true).';'.PHP_EOL.'?>';
                $newfile = fopen($cached_path,"w");
                fwrite($newfile, $temp);
                fclose($newfile);
            // 生成 chatGPT 请求数据
            $caches['chat_pid_'.$pid] = $post_exist&&$post_type==='post' ? json_decode(curlRequest($requirements, 512), true) : array('error' => array ('message' => $err_msg,'type' => 'invalid_request_param','created'=>time(),'ip'=>$request_ip,'ua'=>$request_ua));
            $arr = '<?php'.PHP_EOL.'$cached_post = '.var_export($caches,true).';'.PHP_EOL.'?>';
            $arrfile = fopen($cached_path,"w");
            fwrite($arrfile, $arr);
            fclose($arrfile);
            // 读取临时/已请求记录
            $response = json_encode($caches['chat_pid_'.$pid]); //$caches['chat_pid_'.$pid]
        }
        
        //overwrite response record
        if(!file_exists($cached_path)){
            chatGPT_init($cached_post);  // 文件不存在，创建文件后新增记录
        }else{
            include $cached_path;  // 读取文件记录
            if(array_key_exists('chat_pid_'.$pid, $cached_post)){
                $response = json_encode($cached_post['chat_pid_'.$pid]); //$cached_post['chat_pid_'.$pid]
            }else{
                chatGPT_init($cached_post);  // 记录不存在，新增记录
            }
        }
        // formart responses text-result
        $response = json_decode($response);
        if(array_key_exists('error', $response)){
            $response = $response->error->message;
        }else{
            $choices = $response->choices[0];
            $response = array_key_exists('message', $choices) ? $choices->message->content : $choices->text;
            $response = preg_replace('/.*\n/','', $response);
        }
    }else{
        $response = 'Required pid parms missing or not exists';
    }
    
    // test only // http://www.edbiji.com/doccenter/showdoc/3572/nav/92809.html
    if(array_key_exists('debug', $params)){
?>
        <style>
            @keyframes footerHot{50%{opacity:0}100%{opacity:1}}p.response.load:after{animation-duration:.35s!important;-webkit-animation-duration:.35s!important;}p.response.load:after,p.response.done:after{animation:footerHot 1s step-end infinite normal;-webkit-animation:footerHot 1s step-end infinite normal;}p.response:after{content:'';width:4px;height:20px;display:inline-block;background:currentColor;vertical-align:middle;margin:0 0 2px 5px;}
        </style>
        <blockquote class="chatGPT"><p><b>ABSTRACT</b></p><p class="response load">standby chatGPT responsing..</p></blockquote>
        <script>
            function words_typer(el,str,speed=100){try{if(!str||typeof(str)!='string'||str.replace(/^\s+|\s+$/g,"").replace(/^\s*/,'')=="")throw new Error("invalid string");new Promise(function(resolve,reject){setTimeout(()=>{el.classList.remove('load');for(let i=0,textLen=el.innerText.length;i<textLen;i++){let elText=el.innerText,elLen=elText.length-1;setTimeout(()=>{el.innerText=elText.slice(0,elLen-i);if(i===elLen)resolve(el)},i*5)}},700)}).then((res)=>{setTimeout(()=>{res.classList.remove('load');for(let i=0,strLen=str.length;i<strLen;i++){setTimeout(()=>{res.innerText+=str[i];if(i+1===strLen)res.classList.add('done')},i*speed)}},300)}).catch(function(err){console.log(err)})}catch(err){console.log(err)}};
            const responser = document.querySelector('.chatGPT .response'),
                  result = "<?php echo $response; ?>";
            words_typer(responser, result.replace(/.*\n/g,""), 25);
        </script>
<?php
    }else{
        print_r($response); // print_r(json_encode($response));
    }
?>