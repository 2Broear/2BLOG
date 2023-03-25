<?php
/*
 * Template name: 归档模板
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(0); ?>/style/archive.css?v=<?php echo get_theme_info('Version'); ?>" />
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
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background:url() center center /cover ">
            <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/archive.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <div class="counter">
                <?php
                    $archive_yearly = get_post_archives('yearly');
                    foreach ($archive_yearly as $archive){
                        $archive_count = $archive['count'];
                ?>
                        <div class="blink" data-count="<?php echo $archive_count; ?>">
                            <a href="<?php echo $archive['link']; ?>" rel="nofollow">
                                <b><?php echo $archive['title']; ?></b>
                                <h1>0<?php //echo $archive_count; ?></h1>
                                <p>篇发布记录</p>
                            </a>
                        </div>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="archive-tree">
            <div class="cs-tree">
                <h5>
                    <strong><?php $curYear = gmdate('Y', time() + 3600*8);$lastYear = $curYear-1;//echo "$lastYear-$curYear"; ?> Contributions view </strong>
                    <ul class="cs_tips">
                        <?php
                            $color_light = '#9be9a8';$color_middle = '#40c463';
                            $color_heavy = '#30a14e';$color_more = '#216e39';
                            echo '<li></li><li style="color:'.$color_light.'"></li><li style="color:'.$color_middle.'"></li><li style="color:'.$color_heavy.'"></li><li style="color:'.$color_more.'"></li>';
                        ?>
                    </ul>
                </h5>
                <?php
                    // https://stackoverflow.com/questions/49612838/call-to-undefined-function-cal-days-in-month-error-while-running-from-server
                    function days_in_month($month, $year){
                        // calculate number of days in a month
                        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
                    };
                    // echo cal_days_in_month(CAL_GREGORIAN,1,2023);
                    // function days_in_month($month, $year){
                    //     if($month>=1){
                    //         $date_str = "$year-$month-".$today;
                    //         return date('t', strtotime($date_str));
                    //     }
                    // }
                    $curday = gmdate('md', time() + 3600*8);//date('md'); 
                    $today = date('d');
                    $tomon = date('m');
                    $lastYear = $curYear-1;
                    $archive_daily = get_post_archives('daily','post',9999);
                    function archive_contributions_output($days, $the_day, $compare_date, $year){
                        global $color_light, $color_middle, $color_heavy, $color_more, $archive_daily;
                        echo '<span class="'.$the_day.'" data-dates="'.$compare_date.'" data-date="'.$days.'"';
                            foreach ($archive_daily as $archive){
                                $archive_date = $archive['title'];
                                preg_match("/$year/", $archive_date, $res);  //output year
                                if(array_key_exists(0,$res) && $archive_date==$compare_date){
                                    $counts = $archive['count'];
                                    echo ' id="edit" data-count="'.$counts.'" style="color:';
                                    if($counts>=4){
                                        $color = $color_more;
                                    }else{
                                        switch ($counts) {
                                            case 1:
                                                $color = $color_light;
                                                break;
                                            case 2:
                                                $color = $color_middle;
                                                break;
                                            case 3:
                                                $color = $color_heavy;
                                                break;
                                            default:
                                                $color = '';
                                        };
                                    };
                                    echo $color.'"';
                                }
                            }
                        echo '></span>';
                        unset($color_light, $color_middle, $color_heavy, $color_more, $archive_daily);
                    }
                    $async_stats_sw = get_option('site_async_archive_stats');
                    $async_fully_sw = get_option('site_async_archive_contributions');
                    if($async_fully_sw){
                        for($i=1;$i<13;$i++){
                            $m = days_in_month($i-$tomon, $lastYear);
                            for($j=1;$j<=$m;$j++){
                                $days = $j<10 ? $i.'0'.$j : $i.$j;
                                $the_day = $days==$curday ? 'today' : ($days>$curday ? 'dayto' : false);
                                $compare_date = $lastYear.'年'.$i.'月'.$j.'日';
                                if($lastYear<$curYear && $i>$tomon){// && $j>=$otday月份大于等于当前月份，天数大于今天
                                    archive_contributions_output($days,$the_day,$compare_date,$lastYear);
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
                                archive_contributions_output($days,$the_day,$compare_date,$curYear);
                            }else if($i==$tomon){
                                archive_contributions_output($days,$the_day,$compare_date,$curYear);
                                // $j<=$today ? archive_contributions_output($days,$the_day,$compare_date,$curYear) : false;
                            }
                        }
                    }
                ?>
            </div>
            <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;" style="float:right;">
                <option value=""><?php esc_attr( _e( 'monthly previews', 'textdomain' ) ); ?></option> 
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
                $news_temp = get_cat_by_template('news');
                $note_temp = get_cat_by_template('notes');
                $blog_temp = get_cat_by_template('weblog');
                $news_temp_id = $news_temp->term_id;
                $note_temp_id = $note_temp->term_id;
                $blog_temp_id = $blog_temp->term_id;
                $news_temp_name = $news_temp->name;
                $note_temp_name = $note_temp->name;
                $blog_temp_name = $blog_temp->name;
                $async_sw = get_option('site_async_switcher');
                $archive_temp_slug = get_cat_by_template('archive','slug');
                $async_array = explode(',', get_option('site_async_includes'));
                $use_async = $async_sw ? in_array($archive_temp_slug, $async_array) : false;
                $async_loads = $async_sw&&$use_async ? get_option("site_async_archive", 99) : 999;
                $output_stats = "";
                // get years that have posts // https://wordpress.stackexchange.com/questions/46136/archive-by-year
                global $wpdb;
                $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );
                // get posts for each year
                foreach ($years as $year) {
                    $cur_year = $year->year;
                    $cur_posts = get_wpdb_yearly_pids($cur_year, $async_loads, 0);
                    $posts_count = count($cur_posts);
                    $all_pids = get_wpdb_yearly_pids($cur_year, 999, 0);  //list 999+posts
                    $pids_count = count($all_pids);
                    if($async_stats_sw){
                        // // Categorize Posts (Performance Issues!!!)
                        // $news_count = $note_count = $blog_count = 0;
                        // //$news_array = $note_array = $blog_array = [];
                        // for($i=0;$i<$pids_count;$i++){
                        //     $pst = $all_pids[$i];
                        //     if(in_category($news_temp_id, $pst)) $news_count++; //array_push($news_array, $pst);
                        //     if(in_category($note_temp_id, $pst)) $note_count++; //array_push($note_array, $pst);
                        //     if(in_category($blog_temp_id, $pst)) $blog_count++; //array_push($blog_array, $pst);
                        // };
                        // // $news_count = count($news_array); // $note_count = count($note_array); // $blog_count = count($blog_array);
                        $news_count = get_yearly_cat_count($cur_year, $news_temp_id);
                        $note_count = get_yearly_cat_count($cur_year, $note_temp_id);
                        $blog_count = get_yearly_cat_count($cur_year, $blog_temp_id);
                        $rest_count = $pids_count - ($news_count+$note_count+$blog_count);
                        $output_stats = '<span class="stat_'.$cur_year.' stats">📈📉统计：<b>'.$news_temp_name.'</b> '.$news_count.'篇、 <b>'.$note_temp_name.'</b> '.$note_count.'篇、 <b>'.$blog_temp_name.'</b> '.$blog_count.'篇、 <b>其他类型</b> '.$rest_count.'篇。</span>';
                    }
                    // SAME COMPARE AS $found $limit
                    $load_btns = $posts_count>=$async_loads ? '<sup class="call" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="'.wp_create_nonce($cur_year."_posts_ajax_nonce").'">加载更多</sup>' : '<sup class="call disabled" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="disabled">已全部载入</sup>';
                    $load_icon = $curYear==$cur_year ? ' 🚀 ' : ' 📁 ';
                    echo $async_sw ? '<h2>' . $cur_year . ' 年度发布'.$load_icon.$load_btns.'</h2>'.$output_stats.'<ul class="call_'.$cur_year.'">' : '<h2>' . $cur_year . ' 年度发布</h2>'.$output_stats.'<ul class="call_'.$cur_year.'">';
                    // $year_posts = get_posts(array(
                    //     "year"        => $year,
                    //     "numberposts" => $posts_count,
                    // ));
                    // foreach ($year_posts as $yearpost){
                    //     echo '<li><a class="link" href="'.get_the_permalink($yearpost).'" target="_blank">'.$yearpost->post_title.'<sup>';
                    //     echo '</sup></a></li>';
                    // }
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
                        echo '<li>'.$unique_date.'<a class="link'.$this_article.'" href="'.get_the_permalink($this_post).'" target="_blank">'.$this_title.'<sup>';
                            foreach ($this_cats as $this_cat){
                                echo '<span id="'.$this_cat->term_id.'">'.$this_cat->name.'</span>';
                            }
                        echo '</sup></a></li>';
                    };
                    echo '</ul>'; //<div class="ajax"></div>
                }
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
    if(get_option('site_animated_counting_switcher')){
?>
        <script>
            const counters = document.querySelectorAll(".win-top .counter div");
            dataDancing(counters, "h1", 200, 25);
        </script>
<?php
    }
    if($async_sw&&$use_async){
?>
        <script>
            const archive_tree = document.querySelector(".archive-tree"),
                  preset_loads = <?php echo $async_loads>=99 ? $async_loads : 99; ?>;
            bindEventClick(archive_tree, 'call', function(t){
                load_ajax_posts(t, 'archive', preset_loads, function(each_post, load_box, last_offset){
                    let each_temp = document.createElement("LI");
                    each_temp.innerHTML = `${each_post.date}<a class="link${each_post.mark}" href="${each_post.link}" target="_blank">${each_post.title}<sup>${each_post.cat}</sup></a>`;
                    load_box.appendChild(each_temp);
                    // scrollTo lastest (archive only)
                    load_box.scrollTo(0, last_offset);
                });
            });
        </script>
<?php
    }
?>
</body></html>