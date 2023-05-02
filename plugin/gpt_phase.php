<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    define('CHATGPT_LIMIT', 4096);
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
        $pids = get_post($pid);  //5418 //2872
        $content = wp_strip_all_tags($pids->post_content);
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
                  if($count>CHATGPT_LIMIT){
                      $used_words = mb_substr($str, 0, $i, 'UTF-8');
                      $left_words = mb_substr($str, $i, CHARACTERS_TOTAL, 'UTF-8'); //mb_strlen($str, 'UTF-8')
                      $left_count = count_chaters($left_words);
                    //   echo '<h2>0-'.intval(CHARACTERS_TOTAL-$left_count).'</h2>';
                    //   echo mb_substr($str, 0, intval(CHARACTERS_TOTAL-$left_count), 'UTF-8').'<br/><br/>';
                      return $echopoint ? $i.' (token: '.count_chaters($used_words,1).')<h2>0-'.$i.'</h2>'.$used_words : $left_count.' (token: '.count_chaters($left_words,1).')</b><h2>'.$i.'-'.CHARACTERS_TOTAL.'</h2>'.$left_words;
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