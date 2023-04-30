<?php
    define('WP_USE_THEMES', false);  // No need for the template engine
    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    define('CHATGPT_LIMIT', 4096);
    $pids = get_post(2872);  //5418 //3886
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
                  $left_words = mb_substr($str, $i, strlen($str), 'UTF-8');
                  $left_count = count_chaters($left_words);
                  echo '<h2>0-'.intval(CHARACTERS_TOTAL-$left_count).'</h2>';
                  echo mb_substr($str, 0, intval(CHARACTERS_TOTAL-$left_count), 'UTF-8').'<br/><br/>';
                  return $left_count.'(token: '.count_chaters($left_words,1).')</b><br/><br/>'.$left_words;
              }else{
                  echo $char;
              }
          }
       }
       return $count;
    }
    define('CHARACTERS_TOTAL', count_chaters($content));
    define('CHARACTERS_TOKEN', count_chaters($content,1));
    echo '<small><b>total words: '.CHARACTERS_TOTAL.'(token: '.count_chaters($content,1).')</b><br/><br/>';
    // echo '<small><b>used words: '.count_chaters($content,1,1,1).'</b><br/><br/>';
    echo CHARACTERS_TOKEN>CHATGPT_LIMIT ? '<b>left words: '.count_chaters($content,1,1) : '<b>left words: 0';
?>