<?php
/*
 * Template name: 归档模板
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/archive.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        .win-top .counter{
            margin: 8% auto 7%;
        }
        .archive-tree h2{
            margin: 35px auto 5px;
        }
        .archive-tree ul{
            max-height: 518px;
            /*max-height: 368px;*/
        }
        .archive-tree ul li a:hover{
            padding-left: 10px;
        }
        .archive-tree ul li a.link{
            transition: padding .35s ease;
        }
        .archive-tree ul li .article + a,
        .archive-tree ul li a.article{
            font-weight: bold;
        }
        @keyframes dot {
            33.33% {
                content: ".";
            }
            66.67% {
                content: "..";
            }
            100% {
                content: "...";
            }
        }
        h2 sup.loading:after{
            animation: dot .5s infinite steps(2, start);
            -webkit-animation: dot .5s infinite steps(2, start);
        }
        h2 sup{
            font-size: 12px;
            text-decoration: underline;
        }
        .stats{
            font-weight: bold;
            border-top: 1px dashed rgb(100 100 100 / 30%);
            /*padding: 5px 15px;*/
            padding: 10px 2px;
            /* margin-top: 5px; */
            /*border-radius: 50px;*/
            font-size: 12px;
            /* float: right; */
            border-top-left-radius: 0;
            /*display: inline-block;*/
            display: block;
            /*background: rgb(200 200 200 / 10%);*/
        }
        .stats b{
            opacity: .75;
            font-weight: normal;
        }
        
        .cs-tree{
            margin: 15px auto;
            text-align: left;
        }
        .cs-tree .contributions{
            display: inline-block;
        }
        body.dark .cs-tree span{
            color: var(--preset-3a);
            border: 1px solid var(--preset-3a);
            /*border-color: var(--preset-4a);*/
        }
        body.dark .cs-tree span:before{
            border-color: var(--preset-2b);
            /*color: #9be9a8;*/
        }
        /*.cs-tree .today:hover::before,*/
        .cs-tree span#edit:hover::before{
            content: attr(data-count)' posted on 'attr(data-dates);
        }
        .cs-tree span.today:hover::before{
            content: "today's contribution";
        }
        .cs-tree span.dayto:hover::before{
            content: "future contributions";
        }
        .cs-tree span:before{
            content: none;
            color: white;
            background: var(--preset-3a);
            position: absolute;
            top: 100%;
            left: 100%;
            z-index: 9;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 50px;
            text-align: center;
            white-space: nowrap;
            border: 2px solid currentColor;
            /*font-weight: bold;*/
            /*-webkit-backdrop-filter: blur(10px);*/
            /*backdrop-filter: blur(10px);*/
        }
        .cs-tree .dayto,
        .cs-tree .today,
        .cs-tree span:hover{
            /*border-color: transparent;*/
            border-radius: 50%;
            z-index: 9;
        }
        .cs-tree span{
            display: inline-block;
            width: 10px;
            height: 10px;
            color: var(--preset-s);
            background: currentColor;
            border: 1px solid var(--preset-e);
            margin: 2px;
            border-radius: 2px;
            position: relative;
        }
        .cs-tree span#edit{
            border-color: currentColor;
        }
        body.dark .cs-tree span.today,
        .cs-tree .today{
            color: var(--theme-color);
            /*color: var(--theme-color)!important;*/
            /*border-color: currentColor!important;*/
        }
        .cs-tree .dayto{
            opacity: .75;
            color: var(--preset-e);
            z-index: 0;
        }
        /*.cs-tree span:last-child{*/
        /*    margin-right: auto;*/
        /*}*/
        /*.cs-tree span{*/
        /*    margin: 2px 24px 2px 0;*/
        /*}*/
        .cs_tips::before{
            content: 'Less';
        }
        .cs_tips::after{
            content: 'More';
        }
        .cs_tips::before,
        .cs_tips::after{
            font-size: 12px;
            /*font-weight: bold;*/
            opacity: .5;
        }
        .cs_tips{
            margin: auto;
            padding: 0;
            float: right;
        }
        body.dark .cs_tips li{
            color: var(--preset-3a);
            /*border-color: currentColor!important;*/
        }
        body.dark .cs_tips li:first-child{
            border-color: var(--preset-6);
        }
        .cs_tips li{
            width: 10px;
            height: 10px;
            margin: -2px 3px!important;
            /*width: 3px;*/
            /*height: 12px;*/
            /*margin: -3px 1px!important;*/
            display: inline-block;
            color: var(--preset-s);
            background: currentColor;
            border: 1px solid var(--preset-e);
            border-radius: 2px;
        }
        .cs_tips li:not(:first-child){
            border-color: currentColor!important;
        }
        /*.cs_tips li:nth-child(2){*/
        /*    color: #9be9a8!important;*/
        /*}*/
        /*.cs_tips li:nth-child(3){*/
        /*    color: #40c463!important;*/
        /*}*/
        /*.cs_tips li:nth-child(4){*/
        /*    color: #30a14e!important;*/
        /*}*/
        /*.cs_tips li:last-child{*/
        /*    color: #216e39!important;*/
        /*}*/
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
                ?>
                        <div>
                            <a href="<?php echo $archive['link']; ?>" rel="nofollow">
                                <b><?php echo $archive['title']; ?></b>
                                <h1><?php echo $archive['count']; ?></h1>
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
                    <strong><?php echo $toyea = gmdate('Y', time() + 3600*8);//date('Y'); ?> CONTRIBUTIONS </strong>
                    <ul class="cs_tips">
                        <?php
                            $color_light = '#9be9a8';$color_middle = '#40c463';
                            $color_heavy = '#30a14e';$color_more = '#216e39';
                            echo '<li></li><li style="color:'.$color_light.'"></li><li style="color:'.$color_middle.'"></li><li style="color:'.$color_heavy.'"></li><li style="color:'.$color_more.'"></li>';
                        ?>
                    </ul>
                </h5>
                <?php
                    // $res = cal_days_in_month(CAL_GREGORIAN, 3, 2018);
                    // https://stackoverflow.com/questions/49612838/call-to-undefined-function-cal-days-in-month-error-while-running-from-server
                    function days_in_month($month, $year){
                        // calculate number of days in a month
                        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
                    };
                    // foreach ($archive_array as $archive){
                    //     $year = $archive['title'];
                    //     for($i=1;$i<13;$i++){
                    //         echo days_in_month($i,$year).' , ';
                    //     }
                    //     echo '<br/>';
                    // };
                    $today = gmdate('md', time() + 3600*8);//date('md'); 
                    $archive_daily = get_post_archives('daily','post',9999);
                    // foreach ($archive_daily as $archive){
                    //     preg_match("/$toyea/", $archive['title'], $res);
                    //     // print_r($res[0]);
                    //     echo $archive['title'].' : '.$archive['count'].' , ';
                    // }
                    for($i=1;$i<13;$i++){
                        $m = days_in_month($i, $toyea);
                        // echo '<div class="m'.$i.'_'.$m.'d contributions">';
                        for($j=1;$j<$m;$j++){
                            $days = $j<10 ? $i.'0'.$j : $i.$j;
                            $compare_date = $toyea.'年'.$i.'月'.$j.'日';
                            $the_day = $days==$today ? 'today' : ($days>$today ? 'dayto' : false);
                            echo '<span class="'.$the_day.'" data-dates="'.$compare_date.'" data-date="'.$days.'"';
                                foreach ($archive_daily as $archive){
                                    $archive_date = $archive['title'];
                                    preg_match("/$toyea/", $archive_date, $res);  //cur year only
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
                                                    break;
                                            };
                                        };
                                        echo $color.'"';
                                    }
                                }
                            echo '></span>';
                        }
                        // echo '</div>';
                    }
                ?>
            </div>
            <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;" style="float:right;">
                <option value=""><?php esc_attr( _e( 'Monthly Overview', 'textdomain' ) ); ?></option> 
                <?php 
                    wp_get_archives(array(
                        'type' => 'monthly',
                        'format' => 'option',
                        'show_post_count' => 1,
                        'limit' => '',
                //         'year' => '2018',
                // 		'monthnum' => '',
                // 		'day' => '',
                // 		'w' => '',
                    )); 
                ?>
            </select>
            <?php
                $news_temp = !get_template_bind_cat('category-news.php')->errors ? get_template_bind_cat('category-news.php') : false;
                $note_temp = !get_template_bind_cat('category-notes.php')->errors ? get_template_bind_cat('category-notes.php') : false;
                $blog_temp = !get_template_bind_cat('category-weblog.php')->errors ? get_template_bind_cat('category-weblog.php') : false;
                $async_sw = get_option('site_async_switcher');
                $async_loads = $async_sw ? get_option("site_async_archive", 99) : 999;
                // https://wordpress.stackexchange.com/questions/46136/archive-by-year
                // get years that have posts
                global $wpdb;
                $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );
                // get posts for each year
                foreach ( $years as $year ) {
                    $cur_year = $year->year;
                    $cur_posts = get_wpdb_yearly_pids($cur_year, $async_loads, 0);
                    $posts_count = count($cur_posts);
                    // $news_records = 0;
                    $all_posts = get_wpdb_yearly_pids($cur_year, 9999, 0);
                    $all_count = count($all_posts);
                    $news_array = array();
                    $note_array = array();
                    $blog_array = array();
                    // detect if post in category
                    for($i=0;$i<$all_count;$i++){
                        $pid = $all_posts[$i]->ID;
                        // in_category($news_temp->term_id, $pid) ? $news_records++ : false;
                        in_category($news_temp->term_id, $pid) ? array_push($news_array, $pid) : false; //get_post($pid)->post_title
                        in_category($note_temp->term_id, $pid) ? array_push($note_array, $pid) : false;
                        in_category($blog_temp->term_id, $pid) ? array_push($blog_array, $pid) : false;
                    };
                    $news_count = count($news_array);
                    $note_count = count($note_array);
                    $blog_count = count($blog_array);
                    $rest_count = $all_count-($news_count+$note_count+$blog_count);
                    $output_stats = '<span class="stat_'.$cur_year.' stats">📈📉统计：<b>'.$news_temp->name.'</b> '.$news_count.'篇、 <b>'.$note_temp->name.'</b> '.$note_count.'篇、 <b>'.$blog_temp->name.'</b> '.$blog_count.'篇、 <b>其他类型</b> '.$rest_count.'篇。</span>';
                    $head_emoji = $toyea==$cur_year ? '🚀' : '📁';
                    // SAME COMPARE AS $found $limit
                    if($posts_count>=$async_loads){
                        echo $async_sw ? '<h2>' . $cur_year . ' 年度发布'.$head_emoji.'<sup id="call" data-year="'.$cur_year.'" data-count="0" data-load="'.$posts_count.'">加载更多</sup></h2>'.$output_stats.'<ul class="call_'.$cur_year.'">' : '<h2>' . $cur_year . ' 年度发布</h2><ul class="call_'.$cur_year.'">';
                    }else{
                        // $head_emoji = '📂';
                        echo $async_sw ? '<h2>' . $cur_year . ' 年度发布'.$head_emoji.'<sup id="call" data-year="'.$cur_year.'" data-count="0" data-load="'.$posts_count.'" class="disabled">已全部载入</sup></h2>'.$output_stats.'<ul class="call_'.$cur_year.'">' : '<h2>' . $cur_year . ' 年度发布</h2><ul class="call_'.$cur_year.'">';
                    };
                    // print_r($cur_posts[0]->ID);
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
<script>
    const counterList = document.querySelectorAll('.win-top .counter div');
    function insideLoop(counter,init,limit,i){
        let times = -limit-200;
        var inOrder = function(){
                clearInterval(noOrder);
                init<=limit ? counter.innerHTML=init++ : clearInterval(noOrder);
                times>=0 ? (times=0,clearInterval(noOrder)) : times++;
                // console.log(init+times);
                noOrder = setInterval(inOrder, init+times);
            };
        var noOrder = setInterval(inOrder, 0);
    };
    for(let i=0;i<counterList.length;i++){ //counterList.length
        let count = parseInt(counterList[i].dataset.res), //getAttribute('data-res')
            counter = counterList[i].querySelector('h1'),
            limit = parseInt(counter.innerText);
        insideLoop(counter,0,limit,i);
        // console.log(i);
    }
</script>
<?php
    if($async_sw){
?>
        <script>
            const archive_tree = document.querySelector(".archive-tree"),
                  preset_loads = <?php echo $async_loads; ?>;
            archive_tree.onclick=(e)=>{
                var e = e || window.event,
                    t = e.target || e.srcElement;
                while(t!=archive_tree){
                    if(t.id=="call" && t.nodeName.toLowerCase()=="sup"){
                        let _this = t,
                            years = _this.dataset.year, //.getAttribute("data-year"),
                            loads = parseInt(_this.dataset.load), //.getAttribute("data-load")
                            click_count = parseInt(_this.dataset.count), //.getAttribute('data-count')
                            load_box = archive_tree.querySelector('.call_'+years),//_this.parentNodenextSibling,
                            last_load = load_box.lastChild.offsetTop;  // preset lastChild offsetTop record
                        click_count++;
                        _this.innerText="加载中";
                        _this.classList.add('loading','disabled');
                        _this.setAttribute('data-count', click_count);
                        // console.log(click_count)
                        send_ajax_request("post", "<?php echo admin_url('admin-ajax.php'); ?>", 
                            parse_ajax_parameter({
                                "action": "updateArchive",
                                "key": years, 
                                "limit": preset_loads,
                                "offset": preset_loads*click_count,
                            }, true), function(res){
                                // console.log(res);  //response
                                var posts_array = JSON.parse(res),
                                    posts_count = posts_array.length,
                                    lasts_loads = load_box.lastChild.offsetTop;  // same as preset, define last_load before insert
                                // console.log(load_box.lastChild.offsetTop);
                                if(posts_count<=0){
                                    _this.classList.add("disabled");
                                    _this.innerText="已加载全部";
                                }else{
                                    _this.classList.remove('disabled');
                                    _this.setAttribute('data-load', loads+posts_count);
                                    _this.innerText = "加载更多";
                                };
                                _this.classList.remove('loading');
                                for(let i=0;i<posts_count;i++){
                                    let each_post = posts_array[i];
                                    // console.log(each_post)
                                    load_box.innerHTML += `<li>${each_post.date}<a class="link${each_post.mark}" href="${each_post.link}" target="_blank">${each_post.title}<sup>${each_post.cat}</sup></a></li>`;
                                };
                                // console.log(load_box.lastChild.offsetTop);  // offsetTop = 0
                                load_box.scrollTo(0, lasts_loads); //+load_box.lastChild.offsetHeight
                            }
                        );
                        break;
                    }else{
                        t = t.parentNode;
                    }
                }
            }
        </script>
<?php
    }
?>
</body></html>