<?php
/*
 * Template name: 归档模板
   Template Post Type: page
*/
// 返回（年度）文章归档
function get_post_archives($type="yearly", $post_type="post", $limit=""){
    $archives = wp_get_archives(
        array(
            'type' => $type,
            'limit' => $limit,
            'echo' => false,
            'format' => 'custom',
            'before' => '', 
            'after' => ',',
            // 'before' => '<div><a href="" rel="nofollow"><h2>', 
            // 'after' => '</h2><p></p></a></div>',
            'post_type' => $post_type,
            'show_post_count' => true
        )
    );
    $archive_arr = explode(',', $archives);
    $archive_arr = array_filter($archive_arr, function($i) {
        return trim($i) !== '';  // Remove empty whitespace item from array
    });
    // print_r($archive_arr);
    $array = array();
    foreach($archive_arr as $year_item) {
        $data_row = trim($year_item);
        // print_r($data_row);
        preg_match('/href=["\']?([^"\'>]+)["\']>(.+)<\/a>(.*)/', $data_row, $data_vars);
        // print_r($data_vars);
        if (!empty($data_vars)) {
            preg_match('/\((\d+)\)/', $data_vars[3], $count);
            $array[] = array(
                'title' => $data_vars[2], // Ex: January 2020
                'link' => $data_vars[1], // Ex: http://demo.com/2020/01/
                'count' => $count[1]
            );
        }
    }
    return $array;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/archive.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        .archive-tree ul:hover{
            /*max-height: 368px;*/
        }
        @keyframes blinker {
            0% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
            100% {
                opacity: 1;
            }
        }
        div.blink{
            animation: blinker .5s infinite alternate ease;
            -webkit-animation: blinker .5s infinite alternate ease;
        }
        .stats a:hover{
            color: var(--theme-color);
        }
        .stats a{
            color: inherit;
            text-decoration: underline;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background:url() center center /cover ">
            <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo get_meta_image($cat, $img_cdn.'/images/archive.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <div class="counter">
                <?php 
                    // 输出文章归档统计
                    function the_archive_stats(){
                        $output = '';
                        $output_sw = false;
                        if(get_option('site_cache_switcher')){
                            $caches = get_option('site_cache_includes');
                            $temp_slug = get_cat_by_template('archive','slug');
                            $output_sw = in_array($temp_slug, explode(',', $caches));
                            $output = $output_sw ? get_option('site_archive_count_cache') : '';
                        }
                        if(!$output || !$output_sw){
                            $archive_yearly = get_post_archives('yearly');
                            $blink = get_option('site_animated_counting_switcher') ? ' blink' : false;
                            foreach ($archive_yearly as $archive){
                                $counts = $archive['count'];
                                $output .= '<div class="'.$blink.'" data-count="'.$counts.'"><a href="'.$archive['link'].'" rel="nofollow"><b>'.$archive['title'].'</b><h1>'.$counts.'</h1><p>篇发布记录</p></a></div>';
                            }
                            if($output_sw) update_option('site_archive_count_cache', wp_kses_post($output));
                            // unset($archive_yearly);
                        }
                        echo wp_kses_post($output);
                    }
                    the_archive_stats(); 
                ?>
            </div>
        </div>
        <div class="archive-tree">
            <div class="cs-tree">
                <?php 
                    $curYear = gmdate('Y', time() + 3600*8);
                    // 输出文章归档热度（每日自动更新）
                    function the_archive_contributions(){
                        $output = '';
                        $output_sw = false;
                        if(get_option('site_cache_switcher')){
                            $caches = get_option('site_cache_includes');
                            $temp_slug = get_cat_by_template('archive','slug');
                            $output_sw = in_array($temp_slug, explode(',', $caches));
                            $output = $output_sw ? get_option('site_archive_contributions_cache') : '';
                        }
                        $GLOBALS['color_light'] = '#9be9a8';
                        $GLOBALS['color_middle'] = '#40c463';
                        $GLOBALS['color_heavy'] = '#30a14e';
                        $GLOBALS['color_more'] = '#216e39';
                        echo '<h5><strong> Contributions view </strong><ul class="cs_tips"><li></li><li style="color:'.$GLOBALS['color_light'].'"></li><li style="color:'.$GLOBALS['color_middle'].'"></li><li style="color:'.$GLOBALS['color_heavy'].'"></li><li style="color:'.$GLOBALS['color_more'].'"></li></ul></h5>';
                        if(!$output || !$output_sw){  // no-cache or cache-disabled
                            $GLOBALS['archive_daily'] = get_post_archives('daily','post',9999); //$archive_daily
                            global $curYear; //$curYear = gmdate('Y', time() + 3600*8);
                            $curday = gmdate('md', time() + 3600*8); //date('md'); //$today = date('d');
                            $tomon = date('m');
                            $lastYear = $curYear-1;
                            // calculate number of days in a month // https://stackoverflow.com/questions/49612838/call-to-undefined-function-cal-days-in-month-error-while-running-from-server
                            function days_in_month($month, $year){
                                return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
                            };
                            function archive_contributions_output($days, $the_day, $compare_date, $year){
                                $output = '<span class="'.$the_day.'" data-dates="'.$compare_date.'" data-date="'.$days.'"';
                                foreach ($GLOBALS['archive_daily'] as $archive){
                                    $archive_date = $archive['title'];
                                    preg_match("/$year/", $archive_date, $res);  //output year
                                    if(array_key_exists(0,$res) && $archive_date==$compare_date){
                                        $counts = $archive['count'];
                                        $output .= ' id="edit" data-count="'.$counts.'" style="color:';
                                        if($counts>=4){
                                            $color = $GLOBALS['color_more'];
                                        }else{
                                            switch ($counts) {
                                                case 1:
                                                    $color = $GLOBALS['color_light'];
                                                    break;
                                                case 2:
                                                    $color = $GLOBALS['color_middle'];
                                                    break;
                                                case 3:
                                                    $color = $GLOBALS['color_heavy'];
                                                    break;
                                                default:
                                                    $color = $GLOBALS['color_more'];
                                            };
                                        };
                                        $output .= $color.'"';
                                    }
                                }
                                $output .= '></span>';
                                return $output;
                            }
                            // 全年报表
                            $async_fully_sw = get_option('site_async_archive_contributions');
                            if($async_fully_sw){
                                for($i=1;$i<13;$i++){
                                    $m = days_in_month($i-$tomon, $lastYear);
                                    for($j=1;$j<=$m;$j++){
                                        $days = $j<10 ? $i.'0'.$j : $i.$j;
                                        $the_day = $days==$curday ? 'today' : ($days>$curday ? 'dayto' : false);
                                        $compare_date = $lastYear.'年'.$i.'月'.$j.'日';
                                        if($lastYear<$curYear && $i>$tomon){// && $j>=$otday月份大于等于当前月份，天数大于今天
                                            $output .= archive_contributions_output($days,$the_day,$compare_date,$lastYear);
                                        }
                                    }
                                }
                            }
                            for($i=1;$i<13;$i++){
                                $m = days_in_month($i, $curYear);
                                for($j=1;$j<=$m;$j++){
                                    $days = $j<10 ? $i.'0'.$j : $i.$j;
                                    $the_day = $days==$curday ? 'today' : ($days>$curday ? 'dayto' : false);
                                    $compare_date = $curYear.'年'.$i.'月'.$j.'日';
                                    if($i<$tomon){ // && $j<=date('d')
                                        $output .= archive_contributions_output($days,$the_day,$compare_date,$curYear);
                                    }else if($i==$tomon){
                                        $output .= archive_contributions_output($days,$the_day,$compare_date,$curYear);
                                        // $j<=$today ? archive_contributions_output($days,$the_day,$compare_date,$curYear) : false;
                                    }
                                }
                            }
                            unset($GLOBALS['archive_daily']);
                            if($output_sw) update_option('site_archive_contributions_cache', wp_kses_post($output));
                        }
                        unset($GLOBALS['color_light'],$GLOBALS['color_middle'],$GLOBALS['color_heavy'],$GLOBALS['color_more']);
                        echo wp_kses_post($output);
                    }
                    the_archive_contributions(); 
                ?>
            </div>
            <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;" style="float:right;">
                <option value=""><?php esc_attr( _e( 'monthly overview', 'textdomain' ) ); ?></option> 
                <?php 
                    wp_get_archives(array(
                        'type' => 'monthly',
                        'format' => 'option',
                        'show_post_count' => 1,
                        'limit' => '',
                        // 'year' => '2018',
                		// 'monthnum' => '',
                		// 'day' => '',
                		// 'w' => '',
                    )); 
                ?>
            </select>
            <?php
                $async_sw = get_option('site_async_switcher');
                $archive_temp_slug = get_cat_by_template('archive','slug');
                $async_array = explode(',', get_option('site_async_includes'));
                $use_async = $async_sw ? in_array($archive_temp_slug, $async_array) : false;
                $async_loads = $async_sw&&$use_async ? get_option("site_async_archive", 99) : 999;
                // 文章归档列表（每日更新）
                function the_archive_lists(){
                    $output = '';
                    $output_sw = false;
                    if(get_option('site_cache_switcher')){
                        $caches = get_option('site_cache_includes');
                        $temp_slug = get_cat_by_template('archive','slug');
                        $output_sw = in_array($temp_slug, explode(',', $caches));
                        $output = $output_sw ? get_option('site_archive_list_cache') : '';
                    }
                    global $wpdb;
                    $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );
                    if(!$output || !$output_sw){
                        global $async_sw, $use_async, $async_loads, $curYear;
                        $async_stats_sw = get_option('site_async_archive_stats');
                        $news_temp = get_cat_by_template('news');
                        $note_temp = get_cat_by_template('notes');
                        $blog_temp = get_cat_by_template('weblog');
                        $news_temp_id = $news_temp->term_id;
                        $news_temp_name = $news_temp->name;
                        $note_temp_id = $note_temp->term_id;
                        $note_temp_name = $note_temp->name;
                        $blog_temp_id = $blog_temp->term_id;
                        $blog_temp_name = $blog_temp->name;
                        $output_stats = "";
                        // get years that have posts // https://wordpress.stackexchange.com/questions/46136/archive-by-year
                        foreach ($years as $year) {
                            $cur_year = $year->year;
                            // print_r(get_wpdb_yearly_pids_by_cid($news_temp_id, $cur_year));
                            $cur_posts = get_wpdb_yearly_pids($cur_year, $async_loads, 0);
                            $posts_count = count($cur_posts);
                            $all_pids = get_wpdb_yearly_pids($cur_year, 999, 0);  //list 999+posts
                            $pids_count = count($all_pids);
                            if($async_stats_sw){
                                $news_count = get_yearly_cat_count($cur_year, $news_temp_id);
                                $note_count = get_yearly_cat_count($cur_year, $note_temp_id);
                                $blog_count = get_yearly_cat_count($cur_year, $blog_temp_id);
                                $rest_count = $pids_count - ($news_count+$note_count+$blog_count);
                                $output_stats = '<span class="stat_'.$cur_year.' stats">📈📉统计：<b><a href="'.esc_url(home_url('/?s&cid='.$news_temp_id.'&year='.$cur_year)).'" target="_blank">'.$news_temp_name.'</a></b> '.$news_count.'篇、 <b><a href="'.esc_url(home_url('/?s&cid='.$note_temp_id.'&year='.$cur_year)).'" target="_blank">'.$note_temp_name.'</a></b> '.$note_count.'篇、 <b><a href="'.esc_url(home_url('/?s&cid='.$blog_temp_id.'&year='.$cur_year)).'" target="_blank">'.$blog_temp_name.'</a></b> '.$blog_count.'篇、 <b>其他类型</b> '.$rest_count.'篇。</span>';
                            }
                            // SAME COMPARE AS $found $limit
                            $load_btns = $posts_count>=$async_loads ? '<sup class="call" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="'.wp_create_nonce($cur_year."_posts_ajax_nonce").'">加载更多</sup>' : '<sup class="call disabled" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="disabled">已全部载入</sup>';
                            $load_icon = $curYear==$cur_year ? ' 🚀 ' : ' 📁 ';
                            $output .= $async_sw ? '<h2>' . $cur_year . ' 年度发布'.$load_icon.$load_btns.'</h2>'.$output_stats.'<ul class="list_'.$cur_year.'">' : '<h2>' . $cur_year . ' 年度发布</h2>'.$output_stats.'<ul class="list_'.$cur_year.'">';
                            $output_each = '';
                            for($i=0;$i<$posts_count;$i++){
                                $each_posts = $cur_posts[$i];
                                $prev_posts = $i>0 ? $cur_posts[$i-1] : $cur_posts[$i]; //$i>1 ? $cur_posts[$i-1] : false;
                                $this_post = get_post($each_posts->ID);
                                $prev_post = get_post($prev_posts->ID);
                                $this_cats = get_the_category($this_post);
                                preg_match('/\d{2}-\d{2} /', $this_post->post_date, $this_date);
                                preg_match('/\d{2}-\d{2} /', $prev_post->post_date, $prev_date);
                                // print_r($each_posts->ID);
                                $this_article = $this_cats[0]->slug==$news_temp->slug ? " article" : false;
                                $unique_date = $this_date[0]!=$prev_date[0] || $each_posts->ID==$cur_posts[0]->ID ? '<div class="timeline">'.$this_date[0].'</div>' : '';
                                // print_r($this_cats);
                                $this_title = $this_post->post_title;
                                $output_each .= '<li>'.$unique_date.'<a class="link'.$this_article.'" href="'.get_the_permalink($this_post).'" target="_blank">'.$this_title.'<sup>';
                                $output_cat = '';
                                foreach ($this_cats as $this_cat){
                                    $output_cat .= '<span id="'.$this_cat->term_id.'">'.$this_cat->name.'</span>';
                                }
                                $output_each .= $output_cat.'</sup></a></li>';
                            };
                            $output .= $output_each.'</ul>';
                        }
                        if($output_sw) update_option('site_archive_list_cache', wp_kses_post($output));
                    }else{
                        // always update wp-nonce if db-cached
                        foreach ($years as $year) {
                            $cur_year = $year->year;
                            $cur_nonce = wp_create_nonce($cur_year."_posts_ajax_nonce");
                            // 贪婪匹配(.*)有效（标识符连接处需?非贪婪匹配）
                            $output = preg_replace('/<sup(.*)data-year=("'.$cur_year.'")(.*?)data-nonce=("[^"]*")(.*)<\/sup>/i', '<sup $1data-year=$2$3data-nonce="'.$cur_nonce.'"$5</sup>', $output);
                        }
                    }
                    echo wp_kses_post($output);
                }
                the_archive_lists();
            ?>
            <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                <?php 
                    the_content();  // the_page_content(current_slug());
                    dual_data_comments();  // query comments from database before include
                ?>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
	</div>
<!-- siteJs -->
<?php
    get_foot();
    if($async_sw&&$use_async){
?>
        <script>
            const archive_tree = document.querySelector(".archive-tree"),
                  preset_loads = <?php echo $async_loads; ?>;
            bindEventClick(archive_tree, 'call', function(t){
                load_ajax_posts(t, 'archive', preset_loads, function(res, load_box, last_offset){
                    let fragment = document.createDocumentFragment();
                    res.forEach(item=> {
                        let temp = document.createElement("LI");
                        temp.innerHTML = `${item.date}<a class="link${item.mark}" href="${item.link}" target="_blank">${item.title}<sup>${item.cat}</sup></a>`;
                        fragment.appendChild(temp);
                    });
                    load_box.appendChild(fragment);
                    // scrollTo lastest (archive only)
                    load_box.scrollTo(0, last_offset);
                });
            });
        </script>
<?php
    }
?>
</body></html>