<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    define('CHATGPT_LIMIT', 4096);
    define('COMPLETION_REVERSE', 196);  //preset response-token offset for merge-ingored situation.
    define('CHATGPT_LIMIT_RESERVED', CHATGPT_LIMIT-COMPLETION_REVERSE);
    $query_string = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : false;
    if($query_string){
        parse_str($query_string, $params);
        // 判断url传参或form表单参数
        $pid = array_key_exists('pid', $params) ? $params['pid'] : 3886;  //5418 //2872
        get_phase($pid);
    }else{
        // echo 'QUERY_STRING ERR';
        get_phase(1714);
    }
    function get_phase($pid){
        $pids = get_post($pid);  // //2872
        $content = $pids->post_content;
        $content = preg_replace('/<pre.*?><code>(.*?)<\/code><\/pre>/si', "：<pre><code>[ code example ]</code></pre>。", $content);
        $content = wp_strip_all_tags($content);
        // print_r(wp_strip_all_tags($content));
        // echo $content;
        function count_chaters($str,$token=0,$endpoint=false,$echopoint=false) {
           $count = 0;
           //$arr_str = '';
           for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
              $char = mb_substr($str, $i, 1, 'UTF-8');  // get the character at the current position
              if(preg_match("/[a-zA-Z]/", $char)){
                 $count++;
              }elseif(preg_match("/\p{Han}/u", $char)){
                 $count+=$token ? 2 : 1;  //default 1, gpt 3.5 cost 2
              }
              //if($echopoint&&$count<=4096) $arr_str.=$char;
              if($endpoint){
                  $completion_holder = 296;  //preset response-token offset for merge-ingored situation.
                  if($count>CHATGPT_LIMIT){
                      $used_words = mb_substr($str, 0, $i, 'UTF-8');
                      $left_words = mb_substr($str, $i, CHARACTERS_TOTAL, 'UTF-8'); //strrev($str)
                      $left_token = count_chaters($left_words,1);
                      $reverse_words = $left_token>CHATGPT_LIMIT ? mb_substr($str, -$i) : 'none'; //-4096
                      $reverse_token = count_chaters($reverse_words,1);
                      if($reverse_token>CHATGPT_LIMIT_RESERVED){
                          $reverse_words = mb_substr($reverse_words, -$i+(COMPLETION_REVERSE)); //reserve completion_token place
                          $reverse_token = count_chaters($reverse_words,1); //update reserved left_token
                      }
                    //   print_r(mb_substr($str, -$i)); //mb_substr($str, -$i) //mb_substr($str, -mb_strlen($left_words))
                      return $echopoint ? $i.' (token: '.count_chaters($used_words,1).')</b><h2>0-'.$i.'</h2>'.$used_words : count_chaters($left_words).' (token: '.$left_token.')</b><h2>'.$i.'-'.CHARACTERS_TOTAL.'</h2>'.$left_words.'<h3>reverse_left_words_in_token_limit</h3><p><b>reverse prompt_words: '.count_chaters($reverse_words).' (token: '.$reverse_token.')</b> reverse substr start from the end of left_words, perhaps ingored multiple text but full-article-end</p>'.$reverse_words;
                  }else{
                    //   echo $char;
                  }
              }
           }
           return $count;
        }
        define('CHARACTERS_TOTAL', count_chaters($content));
        define('CHARACTERS_TOKEN', count_chaters($content,1));
        echo '<h1>total words: '.CHARACTERS_TOTAL.' (token: '.count_chaters($content,1).')</h1>';
        echo '<small><b>used words: '.count_chaters($content,1,1,1).'</b><br/><br/>';
        echo CHARACTERS_TOKEN>CHATGPT_LIMIT ? '<b>left words: '.count_chaters($content,1,1) : '<b>left words: 0';
    }
?>