<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once( '../../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    $query_string = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : false;
    if($query_string){
        parse_str($query_string, $params);
        // 判断url传参或form表单参数
        $pid = array_key_exists('pid', $params) ? $params['pid'] : false; //($_POST['pid']||$_GET['pid'])
        $pids = get_post($pid);
        $post_type = $pids->post_type;
        $post_exist = get_post_status($pid); //!is_null(get_post($pid)); //post_exists($title);//
        if($pid&&$post_exist){
            define('CACHED_PATH', './chat_data.php');
            // header("Access-Control-Allow-Credentials: true");//携带cookie
            $del = array_key_exists('del', $params) ? $params['del'] : false; //($_POST['del']||$_GET['del'])
            if($del){
                if(is_user_logged_in()&&current_user_can('administrator')){
                    // echo 'Logged as admin verified, Processing action.. ';
                    include CACHED_PATH;  // 读取文件记录
                    if(isset($cached_post['chat_pid_'.$pid])){
                        unset($cached_post['chat_pid_'.$pid]); // 删除指定记录
                        // 写入本地储存
                        $temp = '<?php'.PHP_EOL.'$cached_post = '.var_export($cached_post,true).';'.PHP_EOL.'?>';
                        $newfile = fopen(CACHED_PATH,"w");
                        fwrite($newfile, $temp);
                        fclose($newfile);
                        echo 200;//'200 : chat_pid_'.$pid.' deleted.';
                    }else{
                        echo 404;//'404: chat_pid_'.$pid.' not found.';
                    }
                    return; //terminate after exec unset.
                }else{
                    api_err_handle('request illegal, login wordpress as admin required.',403);
                }
            }else{
                define('CHATGPT_LIMIT', 4096); //296 for completion_tokens placeholder
                define('COMPLETION_REVERSE', 196);  //preset response-token offset for merge-ingored situation.
                define('CHATGPT_LIMIT_RESERVED', CHATGPT_LIMIT-COMPLETION_REVERSE);
                $cached_post = array();
                $content = preg_replace('/<pre.*?><code>(.*?)<\/code><\/pre>/s', "：<pre><code>[ code example ]</code></pre>。", $pids->post_content); // remove code block
                // $content = str_replace(array("\r\n", "\r", "\n"), " ", wp_strip_all_tags($content));
                $allowed_tags = '<br>';
                $content = preg_replace('/(<h\d.+>)/', "【$1】", $content); // 在标题标签前添加一个换行符
                $content = str_replace($allowed_tags, "\n", strip_tags($content, $allowed_tags));  // 删除所有 HTML 标签并将保留的标签转换为换行符
                $content = preg_replace("/\n+/", "\n", $content);  // 删除多余换行符
                
                //分析以上提供的信息简述文章用意  分析梳理以上信息的逻辑与结构，简述文章用意
                $requirements = '标题：'.$pids->post_title.'，作者：'.get_the_author_meta('display_name', get_post_field('post_author', $pid)).'，内容：'.$content.'。'.get_post_meta($pid, "post_feeling", true);
                
                function count_chaters($str,$token=0,$endpoint=false,$prevpoint=false,$text=false,$ingore=false) {
                   $count = 0;
                   for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
                      $char = mb_substr($str, $i, 1, 'UTF-8');  // get the character at the current position
                      if(preg_match("/[a-zA-Z]/", $char)){
                         $count++;
                      }elseif(preg_match("/\p{Han}/u", $char)){
                         $count+=$token ? 2 : 1;  //default 1, gpt 3.5 cost 2
                      }
                      if($endpoint){
                          if($count>CHATGPT_LIMIT){ //CHATGPT_LIMIT_RESERVED
                              $used_words = mb_substr($str, 0, $i, 'UTF-8');
                              $left_words = mb_substr($str, $i, mb_strlen($str), 'UTF-8');
                              $left_token = count_chaters($left_words,1);
                              // reverse lastest words if left token over limit
                              if($ingore&&$left_token>CHATGPT_LIMIT){ //CHATGPT_LIMIT_RESERVED
                                  $left_words = mb_substr($str, -$i); //-4096
                                  $left_token = count_chaters($left_words,1);
                                  if($left_token>CHATGPT_LIMIT_RESERVED){
                                      $left_words = mb_substr($left_words, -$i+COMPLETION_REVERSE); //reserve completion_token place
                                      $left_token = count_chaters($left_words,1); //update reserved left_token
                                  }
                              }
                              return $text ? ($prevpoint ? $used_words : $left_words) : ($prevpoint ? count_chaters($used_words,1) : $left_token);
                          }
                      }
                   }
                   return $count;
                }
                
                // print_r('second request token: '.count_chaters($requirements,1,1)); //.count_chaters($requirements,1,1,0,true))
                // print_r(count_chaters($requirements,1,1,0,true,true));
                
                function curlRequest($question, $maxlen=1024, $additional='，注意精简字数') {
                    $merge_ingore = get_option('site_chatgpt_merge_ingore');
                    $openai_model = get_option('site_chatgpt_model');
                    $openai_proxy = get_option('site_chatgpt_proxy','https://api.openai.com');
                    $chat_model = $openai_model==='gpt-3.5-turbo';
                    $post_data = array(
                        "model" => $openai_model, //ada
                        'temperature' => 0.8,
                        "max_tokens" => $maxlen,  // works for completion_tokens only
                        "prompt" => '分析文章内容，简述文章用意'.$additional.'。
文章："""
'.$question.'
"""', //$question.'。分析上述内容，简述文章用意'.$additional
                    );
                    if($chat_model){
                        unset($post_data['prompt']);
                        $post_data = array_merge($post_data, array('messages' => [
                            ["role" => "system", "content" => '分析并简述文章用意'.$additional], //分析并简述文章用意
                            ["role" => "user", "content" => $question]
                        ]));
                    }
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $chat_model ? $openai_proxy.'/v1/chat/completions' : $openai_proxy.'/v1/completions', //聊天模型
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
                        "Authorization: Bearer " . get_option('site_chatgpt_apikey')
                      ),
                    ));
                    // $res = curl_exec($curl);
                    $question_token = count_chaters($question,1);
                    if($question_token>CHATGPT_LIMIT){
                        // curl_exec($curl); // following merge-requests will cost at-least 2 times call.
                        $used_words = count_chaters($question,1,1,1,true);
                        if(get_option('site_chatgpt_merge_sw')){
                            $left_token = count_chaters($question,1,1);
                            if($merge_ingore || $left_token<=CHATGPT_LIMIT){
                                $previous_res = curlRequest($used_words); //0-4096 context
                                $previous_txt = api_get_resultText($previous_res,true).'。';
                            }
                            if($left_token<=CHATGPT_LIMIT_RESERVED){
                                $left_words = count_chaters($question,1,1,0,true);
                                $addition_res = curlRequest($left_words);
                                return curlRequest($previous_txt.api_get_resultText($addition_res,true)); //, 392, '。'
                            }else{
                                if($merge_ingore){
                                    $left_words_ingored = count_chaters($question,1,1,0,true,true);
                                    $addition_res = curlRequest($left_words_ingored); // end of context(4096-max)
                                    return curlRequest($previous_txt.api_get_resultText($addition_res,true)); //, 512, '。'
                                }else{
                                    $res = json_encode(array('error' => array ('message' => 'article is too long to abstract (context token: '.$question_token.', inqueue token: '.$left_token.')','type' => 'request_context_too_long','created'=>time())),true);
                                }
                            }
                        }else{
                            $res = curlRequest($used_words);
                        }
                        // curl_close($curl);
                    }else{
                        $res = curl_exec($curl);
                        if ($res === false) {
                            $errno = curl_errno($ch);
                            $error = curl_error($ch);
                            $res = "cURL Error ($errno): $error\n";
                        }
                    }
                    curl_close($curl);
                    return $res;
                }
                
                
                // 初始化php文件，返回记录
                function chatGPT_init($caches, $new=false){
                    global $pid, $requirements, $response, $post_exist, $post_type;
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
                    $request_ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER["REMOTE_ADDR"] : NULL;
                    $request_ua =array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER["HTTP_USER_AGENT"] : NULL;
                    // 创建临时记录，防止多请求并发
                        $caches['chat_pid_'.$pid] = array('error' => array ('message' => 'standby, another requesting in busy... (refresh to check perhaps the context_length_exceeded was occured.','type' => 'request_inqueue_busy','created'=>time(),'ip'=>$request_ip,'ua'=>$request_ua));
                        $temp = '<?php'.PHP_EOL.'$cached_post = '.var_export($caches,true).';'.PHP_EOL.'?>';
                        $newfile = fopen(CACHED_PATH,"w");
                        fwrite($newfile, $temp);
                        fclose($newfile);
                    // 生成 chatGPT 请求数据（decode json to php->array）
                    $caches['chat_pid_'.$pid] = $post_exist&&$post_type==='post' ? json_decode(curlRequest($requirements, 512), true) : array('error' => array ('message' => $err_msg,'type' => 'invalid_request_param','created'=>time(),'ip'=>$request_ip,'ua'=>$request_ua));
                    $arr = '<?php'.PHP_EOL.'$cached_post = '.var_export($caches,true).';'.PHP_EOL.'?>';
                    $arrfile = fopen(CACHED_PATH,"w");
                    fwrite($arrfile, $arr);
                    fclose($arrfile);
                    // 读取临时/已请求记录
                    $response = json_encode($caches['chat_pid_'.$pid]); //$caches['chat_pid_'.$pid]
                }
                
                //overwrite response record
                function overwrite_request_record(){
                    global $cached_post, $response;  // READ GLOBAL RESPONSE
                    if(!file_exists(CACHED_PATH)){
                        chatGPT_init($cached_post);  // 文件不存在，创建文件后新增记录
                    }else{
                        include CACHED_PATH;  // 读取文件记录
                        global $pid;
                        if(array_key_exists('chat_pid_'.$pid, $cached_post)){
                            $response = json_encode($cached_post['chat_pid_'.$pid]); //$cached_post['chat_pid_'.$pid]
                        }else{
                            chatGPT_init($cached_post);  // 记录不存在，新增记录
                        }
                    }
                    // formart responses text-result
                    $response = json_decode($response);
                    //property_exists($response,'error') array_key_exists('error', $response)
                    $response = isset($response->error) ? $response->error->message : preg_replace('/.*\n/','', api_get_resultText($response));
                }
                
                // overwrite_request_record();
                $cdn_sw = get_option('site_cdn_switcher');
                $cdn_src = get_option('site_cdn_src');
                $cdn_api = get_option('site_cdn_api');
                if($cdn_sw&&$cdn_src || $cdn_sw&&$cdn_api){
                    $auth_path_array = array($_SERVER['SCRIPT_NAME'],$_SERVER['DOCUMENT_URI'],$_SERVER['REQUEST_URI']);
                    $auth_host_array = array($_SERVER['HTTP_HOST'],$_SERVER['SERVER_NAME']);
                    switch (true) {
                        case $cdn_api:
                            $cdn_auth = api_illegal_auth($auth_host_array, $cdn_api);
                            break;
                        default:
                            $cdn_auth = api_illegal_auth($auth_host_array, $cdn_src);
                            break;
                    }
                    api_illegal_auth($auth_path_array, '/wp-content/themes/') || $cdn_auth ? api_err_handle('request illegal, cdn/api enabled(gpt)',403) : overwrite_request_record();
                }else{
                    overwrite_request_record();
                }
            }
        }else{
            $response = api_err_handle('param err, requested pid not found or exists',200,true);
        }
        // test only // http://www.edbiji.com/doccenter/showdoc/3572/nav/92809.html
        print_r($response); // print_r(json_encode($response));
    }else{
        api_err_handle('params missing, SERVER QUERY_STRING NOT EXISTS');
    }
?>