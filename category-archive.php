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
            margin: 35px auto 10px;
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
        #stats{
            font-weight: bold;
            border: 1px solid rgb(100 100 100 / 30%);
            padding: 5px 15px;
            /* margin-top: 5px; */
            border-radius: 50px;
            font-size: 12px;
            /* float: right; */
            border-top-left-radius: unset;
            background: rgb(200 200 200 / 10%);
            display: inline-block;
        }
        #stats b{
            opacity: .75;
            font-weight: normal;
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
                    $archive_array = get_post_archives('yearly');
                    foreach ($archive_array as $archive){
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
            <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;" style="/*float:right;*/">
                <option value=""><?php esc_attr( _e( 'Select Month', 'textdomain' ) ); ?></option> 
                <?php 
                    wp_get_archives(array(
                        'type' => 'monthly',
                        'format' => 'option',
                        'show_post_count' => 1,
                        'limit' => ''
                    )); 
                ?>
            </select>
            <?php
                $news_temp = !get_template_bind_cat('category-news.php')->errors ? get_template_bind_cat('category-news.php') : false;
                $note_temp = !get_template_bind_cat('category-notes.php')->errors ? get_template_bind_cat('category-notes.php') : false;
                $blog_temp = !get_template_bind_cat('category-weblog.php')->errors ? get_template_bind_cat('category-weblog.php') : false;
                $async_sw = get_option('site_async_switcher');
                $async_loads = $async_sw ? get_option("site_async_archive", 99) : 999;
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
                    $etc_count = $all_count-($news_count+$note_count+$blog_count);
                    $output_count = '<span id="stats"><b>'.$news_temp->name.'</b> *'.$news_count.'，<b>'.$note_temp->name.'</b> *'.$note_count.'，<b>'.$blog_temp->name.'</b> *'.$blog_count.'、<b>其他</b> *'.$etc_count.'</span>';
                    // SAME COMPARE AS $found $limit
                    if($posts_count>=$async_loads){
                        echo $async_sw ? '<h2>' . $cur_year . '年度发布<sup id="call" data-year="'.$cur_year.'" data-count="0" data-load="'.$posts_count.'"> 加载更多 </sup></h2>'.$output_count.'<ul class="call_'.$cur_year.'">' : '<h2>' . $cur_year . '年度发布</h2><ul class="call_'.$cur_year.'">';
                    }else{
                        echo $async_sw ? '<h2>' . $cur_year . '年度发布<sup id="call" data-year="'.$cur_year.'" data-count="0" data-load="'.$posts_count.'" class="disabled"> 已全部载入 </sup></h2>'.$output_count.'<ul class="call_'.$cur_year.'">' : '<h2>' . $cur_year . '年度发布</h2><ul> class="call_'.$cur_year.'"';
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
                        $unique_date = $this_date[0]!=$prev_date[0] || $each_posts->ID==$cur_posts[0]->ID ? '<div class="timeline">'.$this_date[0].'</div>' : '';
                        // print_r($this_cats);
                        $this_title = $this_post->post_title;
                        echo '<li>'.$unique_date.'<a class="link" href="'.get_the_permalink($this_post).'" target="_blank">'; //$this_cats[0]->slug
                        echo $this_cats[0]->slug==$news_temp->slug ? '<u>'.$this_title.'</u>' : $this_title;
                        echo '<sup>';
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
        let times = -10;
        var noOrder = setInterval(function(){
            times = limit<20 ? 1200 : times;
            init<=limit ? counter.innerHTML = init++ : clearInterval(noOrder);
            // console.log(init);
        }, times*i);
    };
    for(let i=0;i<counterList.length;i++){
        let count = parseInt(counterList[i].getAttribute('data-res')),
            counter = counterList[i].querySelector('h1'),
            limit = parseInt(counter.innerText);
        insideLoop(counter,0,limit,i);
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
                            years = _this.getAttribute("data-year"),
                            loads = parseInt(_this.getAttribute("data-load")),
                            click_count = parseInt(_this.getAttribute('data-count')),
                            // load_ajax = load_box.querySelector(".ajax"),
                            load_box = archive_tree.querySelector('.call_'+years),//_this.parentNodenextSibling,
                            last_load = load_box.lastChild.offsetTop;  // preset lastChild offsetTop record
                        click_count++;
                        _this.innerText=" 加载中 ";
                        _this.classList.add('loading','disabled');
                        _this.setAttribute('data-count', click_count);
                        // console.log(click_count)
                        send_ajax_request("post", "<?php echo admin_url('admin-ajax.php'); ?>", 
                            parse_ajax_parameter({
                                "action": "updateCont",
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
                                    _this.innerText=" 已加载全部 ";
                                }else{
                                    _this.classList.remove('disabled');
                                    _this.setAttribute('data-load', loads+posts_count);
                                    _this.innerText = " 加载更多 ";
                                };
                                _this.classList.remove('loading');
                                for(let i=0;i<posts_count;i++){
                                    let each_post = posts_array[i];
                                    // console.log(each_post)
                                    load_box.innerHTML += `<li>${each_post.date}<a class="link" href="${each_post.link}" target="_blank">${each_post.title}<sup>${each_post.cat}</sup></a></li>`;
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