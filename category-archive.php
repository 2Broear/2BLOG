<?php
/*
 * Template name: 归档模板
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/archive.css" />
    <?php get_head(); ?>
    <style>
        h2 sup.disabled{
            pointer-events: none;
            opacity: .32;
        }
        h2 sup{
            font-size: small;
            opacity: .88;
            margin-left: 5px;
            cursor: pointer;
        }
        h2 sup:after{
            content: '['attr(data-load)']';
            opacity: .75;
            font-size: smaller;
            vertical-align: text-top;
        }
        .archive-tree ul{
            position: relative;
            scroll-behavior: smooth;
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
            <video src="<?php echo get_option('site_acgn_video'); ?>" poster="<?php echo cat_metabg($cat, custom_cdn_src('img',true).'/images/archives.jpg'); ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
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
                global $wpdb;
                $preset_loads = 99;
                // get years that have posts
                $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );
                // get posts for each year
                foreach ( $years as $year ) {
                    $cur_year = $year->year;
                    $cur_posts = get_wpdb_posts($cur_year, $preset_loads, 0);
                    $posts_count = count($cur_posts);
                    echo '<h2>' . $cur_year . '年度发布<sup id="call" data-year="'.$cur_year.'" data-count="0" data-load="'.$posts_count.'"> 加载更多 </sup></h2><ul>'; // ['.$posts_count.']
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
                        echo '<li>'.$unique_date.'<a class="link" href="'.get_the_permalink($this_post).'" target="_blank">' . $this_post->post_title.'<sup>';
                        foreach ($this_cats as $this_cat){
                            echo '<span>'.$this_cat->name.'</span>';
                        }
                        echo '</sup></a></li>';
                    }
                    echo '</ul>'; //<div class="ajax"></div>
                }
            ?>
            <script>
                const archive_tree = document.querySelector(".archive-tree"),
                      preset_loads = <?php echo $preset_loads; ?>;
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
                                load_box = _this.parentNode.nextSibling,
                                last_load = load_box.lastChild.offsetTop;  // preset lastChild offsetTop record
                            click_count++;
                            _this.innerText="加载中..";
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
                                    posts_count<=0 ? (_this.classList.add("disabled"), _this.innerText="已全部加载！") : (_this.setAttribute('data-load', loads+posts_count), _this.innerText="加载更多");
                                    // console.log(posts_array);
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js"></script>
</body></html>
