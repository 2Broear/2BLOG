<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once( '../../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    define('CDN_SWITCH', get_option('site_cdn_switcher'));
    define('CDN_SRC', get_option('site_cdn_src'));
    define('CDN_API', get_option('site_cdn_api'));
    if(CDN_SWITCH&&CDN_SRC || CDN_SWITCH&&CDN_API){
        $auth_path_array = array($_SERVER['SCRIPT_NAME'],$_SERVER['DOCUMENT_URI'],$_SERVER['REQUEST_URI']);
        $auth_host_array = array($_SERVER['HTTP_HOST'],$_SERVER['SERVER_NAME']);
        api_illegal_auth($auth_path_array, '/wp-content/themes/') || api_illegal_auth($auth_host_array, CDN_API) || api_illegal_auth($auth_host_array, CDN_SRC) ? api_err_handle('request illegal, cdn/api enabled',403) : send_gpt_request();
    }else{
        send_gpt_request();
    }
    function send_gpt_request(){
        $query_string = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : false;
        if($query_string){
            parse_str($query_string, $params);
            // 判断url传参或form表单参数
            $pid = array_key_exists('pid', $params) ? $params['pid'] : ($_POST['pid']||$_GET['pid']);
            $pids = get_post($pid);
            $post_type = $pids->post_type;
            $post_exist = get_post_status($pid); //!is_null(get_post($pid)); //post_exists($title);//
            if($pid&&$post_exist){
                define('CHATGPT_LIMIT', 4096);
                $content = str_replace(array("\r\n", "\r", "\n"), " ", wp_strip_all_tags($pids->post_content));
                //分析以上提供的信息简述文章用意  分析梳理以上信息的逻辑与结构，简述文章用意
                $requirements = '标题：'.$pids->post_title.'，作者：'.get_the_author_meta('display_name', get_post_field('post_author', $pid)).'，内容：'.$content.'。'.get_post_meta($pid, "post_feeling", true); //str_replace(array("\r\n", "\r", "\n"), " ", wp_strip_all_tags($pids->post_content))
                function count_chaters($str,$token=0,$endpoint=false,$prevpoint=false) {
                   $count = 0;
                   //$arr_str = '';
                   for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
                      $char = mb_substr($str, $i, 1, 'UTF-8');  // get the character at the current position
                      if(preg_match("/[a-zA-Z]/", $char)){
                         $count++;
                      }elseif(preg_match("/\p{Han}/u", $char)){
                         $count+=$token ? 2 : 1;  //default 1, gpt 3.5 cost 2
                      }
                      if($endpoint){
                          if($count>CHATGPT_LIMIT){
                              global $requirements;
                              $left_words = mb_substr($str, $i, strlen($str), 'UTF-8');
                              return $prevpoint ? mb_substr($str, 0, intval(count_chaters($requirements)-count_chaters($left_words)), 'UTF-8') : $left_words;//count_chaters($left_words).'(token: '.count_chaters($left_words,1).')</b><br/><br/>'.$left_words;
                          }else{
                            //   echo $char;
                          }
                      }
                   }
                   return $count;
                }
                // define('CHARACTERS_TOTAL', count_chaters($requirements));
                // define('CHARACTERS_TOKEN', count_chaters($requirements,1));
                // define('CHARACTERS_LEFTS', count_chaters($requirements,1,1));
                define('OPENAI_API_KEY', get_option('site_chatgpt_apikey'));
                define('OPENAI_MERGE', get_option('site_chatgpt_merge_sw'));
                define('OPENAI_PROXY', get_option('site_chatgpt_proxy','https://api.openai.com'));
                define('OPENAI_MODEL', get_option('site_chatgpt_model'));
                function get_resultText($res_cls_obj, $decode=false){
                    $formart = $decode ? json_decode($res_cls_obj) : $res_cls_obj;
                    if(isset($formart->error)) return $formart->error->message;
                    $choices = $formart->choices[0];
                    return isset($choices->message) ? $choices->message->content : $choices->text; //property_exists($choices,'message')
                }
                function curlRequest($question, $maxlen=1024, $recursive_request=false) {
                    $total_token = count_chaters($question,1);
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
                    $res = OPENAI_MERGE ? ($total_token<=CHATGPT_LIMIT ? curl_exec($curl) : false) : curl_exec($curl);
                    curl_close($curl);
                    // $response = json_decode($res); || isset($response->error)&&$response->error->code==='context_length_exceeded'
                    // USE count_chaters as current $question string, case for Recursion
                    if(OPENAI_MERGE && $total_token>CHATGPT_LIMIT){
                        //此处已超出 4097 tokens 触发 message 没有 content 的 error
                        // following merge-requests will cost at-least 3 times call.
                        $previous_res = curlRequest(count_chaters($question,1,1,1));
                        $addition_res = curlRequest(count_chaters($question,1,1));
                        return curlRequest(get_resultText($previous_res,true).get_resultText($addition_res,true)); 
                        //error: curlRequest(json_decode($res)->choices[0]->message->content)
                    }
                    return $res;
                }
                
                // print_r(json_decode(curlRequest('测试'))->choices[0]->message->content);
                
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
                        $caches['chat_pid_'.$pid] = array('error' => array ('message' => 'standby, another requesting in busy.. refresh to checkout.','type' => 'request_inqueue_busy','created'=>time(),'ip'=>$request_ip,'ua'=>$request_ua));
                        $temp = '<?php'.PHP_EOL.'$cached_post = '.var_export($caches,true).';'.PHP_EOL.'?>';
                        $newfile = fopen($cached_path,"w");
                        fwrite($newfile, $temp);
                        fclose($newfile);
                    // 生成 chatGPT 请求数据（decode json to php->array）
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
                //property_exists($response,'error') array_key_exists('error', $response)
                $response = isset($response->error) ? $response->error->message : preg_replace('/.*\n/','', get_resultText($response));
            }else{
                $response = api_err_handle('param err, requested pid not found or exists',200,true); //'{"code":200,"msg":"param err, requested pid not found or exists"}';
            }
            // test only // http://www.edbiji.com/doccenter/showdoc/3572/nav/92809.html
            if(array_key_exists('debug', $params)){
?>
                <style>
                    @keyframes footerHot{50%{opacity:0}100%{opacity:1}}p.response.load:after{animation-duration:.35s!important;-webkit-animation-duration:.35s!important;}p.response.load:after,p.response.done:after{animation:footerHot 1s step-end infinite normal;-webkit-animation:footerHot 1s step-end infinite normal;}p.response:after{content:'';width:4px;height:20px;display:inline-block;background:currentColor;vertical-align:middle;margin:0 0 2px 5px;}
                </style>
                <blockquote class="chatGPT"><p><b>ABSTRACT</b></p><p class="response load">standby chatGPT responsing..</p></blockquote>
                <script type="module">
                    const responser = document.querySelector('.chatGPT .response'),
                          result = "<?php echo $response; ?>";
                    import('<?php custom_cdn_src(); ?>/js/module.js').then((module) => {
                        module.words_typer(responser, result.replace(/.*\n/g,""), 25);
                    });
                </script>
<?php
            }else{
                print_r($response); // print_r(json_encode($response));
            }
        }else{
            echo api_err_handle('params missing, SERVER QUERY_STRING NOT EXISTS'); //'{"code":200,"msg":"params missing, SERVER QUERY_STRING NOT EXISTS"}';
        }
    }
?>