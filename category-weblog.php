<?php
/*
   Template name: 日志模板（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/weblog.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        figure{text-align:left}
        figure img,figure video{border-radius:var(--radius);width: 66%;}
        .anchor{
            position: relative;
            top: -15px;
        }
        #comment_txt{padding:0 15px;box-sizing:border-box;}
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-tops bg" style="background:url() center center /cover ">
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <?php
                $winbg = get_option('site_weblog_bg');
                if($winbg){
            ?>
                    <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
                    <video src="<?php echo get_option('site_weblog_video'); ?>" poster="<?php echo $winbg; ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <?php
                }
            ?>
        </div>
        <?php get_inform(); ?>
        <div class="weblog-tree-all">
            <div class="weblog-tree-core <?php $third_cmt = get_option('site_third_comments');echo $third_cmt&&$third_cmt!='' ? 'reply' : false; ?>">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $weblog_slug = get_cat_by_template('weblog','slug'); //current_slug();
                    $async_array = explode(',', get_option('site_async_includes'));
                    $use_async = $async_sw ? in_array($weblog_slug, $async_array) : false;
                    $async_loads = $async_sw&&$use_async ? get_option("site_async_weblog", 14) : get_option('posts_per_page');
                    $baas = get_option('site_leancloud_switcher')&&in_array(basename(__FILE__), explode(',', get_option('site_leancloud_category')));  //use post as category is leancloud unset //&&strpos(get_option('site_leancloud_category'), basename(__FILE__))!==false
                    if(!$baas){
                        $current_page = max(1, get_query_var('paged')); //current paged
                        $log_query = new WP_Query(array_filter(array(
                            'cat' => $cat,  //get_template_bind_cat(basename(__FILE__))->term_id;
                            'meta_key' => 'post_orderby',
                            'orderby' => array(
                                'meta_value_num' => 'DESC',
                                'date' => 'DESC'
                            ),
                            'paged' => $current_page,  //current paged
                            'posts_per_page' => $async_loads,  //use left_query counts
                        )));
                        // Empty card if null reponsed
                        if(!$log_query->have_posts()) echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t="'.current_slug(true).'"></i><h1> 这里，<b>空空的！</b> </h1></div>';  //<b>'.current_slug(true).'</b> 
                        while ($log_query->have_posts()):
                            $log_query->the_post();
                            // $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                ?>
                            <div class="<?php if($post_orderby>1) echo 'topset '; ?>weblog-tree-core-record">
                                <div class="weblog-tree-core-l">
                                    <span id="weblog-timeline">
                                        <?php 
                                            echo $rich_date = get_the_tag_list() ? get_the_time('Y年n月j日').' - ' : get_the_time('Y年n月j日');
                                            echo get_tag_list($post->ID,2,'');
                                        ?>
                                    </span>
                                    <span id="weblog-circle"></span>
                                </div>
                                <div class="weblog-tree-core-r">
                                    <!--<a class="anchor"></a>-->
                                    <div id="<?php echo 'pid_'.get_the_ID() ?>" class="weblog-tree-box">
                                        <div class="tree-box-title">
                                            <a href="<?php echo get_option('site_single_switcher') ? get_the_permalink() : 'javascript:;' ?>" target="_self">
                                                <h3 class="reply_quote"><?php the_title() ?></h3>
                                            </a>
                                        </div>
                                        <div class="tree-box-content">
                                            <span id="core-info">
                                                <?php 
                                                    // echo get_the_content();//custom_excerpt(200); 
                                                    echo apply_filters('the_content', get_the_content());
                                                ?>
                                            </span>
                                            <?php
                                                $ps = get_post_meta($post->ID, "post_feeling", true);
                                                if($ps) echo '<span id="other-info"><h4> Ps. </h4><p class="feeling">'.$ps.'</p></span>';
                                            ?>
                                            <p id="sub"><?php echo $rich_date . get_tag_list($post->ID,2,''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <?php       
                        endwhile;
                        wp_reset_query();
                    }
                    if($baas) echo '<div class="weblog-tree-etc load"><button>加载更多</button></div>';
                    $async_array = explode(',', get_option('site_async_includes'));
                    if(get_option('site_async_switcher')&&in_array($weblog_slug, $async_array)){
                        // preset all acg query
                        $all_query = new WP_Query(array_filter(array(
                            'cat' => $cat,
                            'posts_per_page' => -1,
                            'fields' => 'ids',
                            'no_found_rows' => true,
                        )));
                        $all_count = $all_query->post_count;
                        $posts_count = $log_query->post_count;
                        $disable_statu = $posts_count==$all_count ? ' disabled' : false; //>=
                        echo '<div class="load'.$disable_statu.'"><button class="load-more" href="javascript:;" data-counts="'.$all_count.'" data-load="'.$posts_count.'" data-click="0" data-cid="'.$cat.'" data-cat="'.$weblog_slug.'" data-nonce="'.wp_create_nonce(current_slug()."_posts_ajax_nonce").'" title="加载更多">加载更多</button></div>';
                    }else{
                ?>
                        <div class="pageSwitcher">
                            <?php
                                echo paginate_links(array(
                                    'prev_text' => __('上一页'),
                                    'next_text' => __('下一页'),
                                    'type' => 'plaintext',
                                    'screen_reader_text' => null,
                                    'total' => $log_query->max_num_pages,  //总页数
                                    'current' => $current_page, //当前页数
                                ));
                            ?>
                        </div>
                <?php
                    }
                ?>
            </div>
            <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                <?php 
                    // the_content();  // cancel for hidding sub-cat content
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
    require_once(TEMPLATEPATH. '/foot.php');
    if($baas){
?>
        <script type="text/javascript">
            const curTab = "<?php echo $weblog_slug; ?>",
                  loadbtn = document.querySelector(".load button");
            var limiter = 15,
                curSkip = 0;  //preSkip = 1;
            // console.log('leancloud(weblog-page) cross-app(init) ok');
            //request AV.Query
            const query_log = new AV.Query(curTab);
            // function QUERY(curTab,curSkip,preSkip){
            function QUERY(curTab,curSkip,limiter){
                const loadcore = document.querySelector(".weblog-tree-core"),
                      loadbox = document.querySelector(".weblog-tree-etc.load"),
                      loading = document.createElement("span");
                var loadContent = document.createElement("div");
                loading.id="loading";
                loadbox.insertBefore(loading,loadbox.firstChild);
                // console.log("cur: "+dateLimit(date,skipStart)+", pre: "+dateLimit(date,-skipEnd));
                query_log.addDescending("index").addDescending("createdAt").skip(curSkip).limit(limiter).find().then(result=>{
                    for (let i=0; i<result.length;i++) {
                        let res = result[i],
                            title = res.attributes.title,
                            main = res.attributes.content,
                            ps = res.attributes.ps,
                            type = res.attributes.type_weblog,  //+"_"+curTab
                            index = res.attributes.index,
                            dates = res.attributes.dates,
                            today = res.attributes.today;
                        loadContent.innerHTML += '<div class="weblog-tree-core-record i'+index+'" data-type="'+type+'"><div class="weblog-tree-core-l"><span id="weblog-timeline" data-year="'+today.y+'" data-month="'+today.m+'" data-day="'+today.d+'">'+dates+'</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div class="weblog-tree-box"><div class="tree-box-title"><h3 id="'+res.id+'" class="reply_quote">'+title+'</h3></div><div class="tree-box-content"><span id="core-info"><p>'+main+'</p></span><span id="other-info"><h4> Ps. </h4><p>'+ps+'</p><p id="sub">'+dates+'</p></span></div></div></div></div>';
                        // loadcore.appendChild(loadContent);
                        loadcore.insertBefore(loadContent, loadbox);
                    }
                    loading.remove();
                })
            }
            QUERY(curTab,curSkip,limiter);  //QUERY(curTab,curSkip,preSkip);
            loadbtn.onclick = function(){
                curSkip+=limiter;  // preSkip++;
                QUERY(curTab,curSkip,limiter);  //QUERY(curTab,curSkip,preSkip);
            }
        </script>
<?php
    };
    if($third_cmt && $third_cmt!=''){
?>
        <script>
            // BLOCKQUOTE Reply
            const editor = document.querySelector('textarea'),
                  weblog = document.querySelector(".weblog-tree-core"),
                  preset_loads = <?php echo $async_loads; ?>;
            weblog.onclick=(e)=>{
                e = e || window.event;
                let t = e.target || e.srcElement;
                if(!t) return;
                while(t!=weblog){
                    if(t.classList && t.classList.contains("reply_quote")){
                        editor.focus();
                        editor.value = "";
                        editor.setAttribute('placeholder', '回复片段：'+t.innerText);
                        const cores = getParByCls(t, 'weblog-tree-box').querySelector('#core-info'),
                              quote = `\n> __${t.innerText}__ \n> ${cores ? cores.innerText.replace(/\n/g,"").substr(0,88) : ".."}...`;
                        editor.style.cssText="min-height:150px;opacity:.75;";
                        editor.value = quote;//this.id; '\n'+
                        editor.setSelectionRange(0,0);
                        editor.oninput=function(){
                            if(this.value.indexOf(quote)==-1 || this.value.substr(this.value.length-3,this.value.length)!='...'){  //
                                this.value = quote;
                                editor.setSelectionRange(0,0);
                            }
                        }
                        break;
                    }else if(t.classList && t.classList.contains("load-more")){ //t.id && t.id=="load"
                        <?php
                            if($async_sw&&$use_async){
                        ?>
                                load_ajax_posts(t, 'weblog', preset_loads, function(each_post, load_box){
                                    let each_temp = document.createElement("div"),
                                        each_tags = each_post.tag ? " - "+each_post.tag : "";
                                    each_temp.id = "pid_"+each_post.id;
                                    each_temp.classList.add("weblog-tree-core-record");
                                    each_temp.innerHTML = `<div class="weblog-tree-core-l"><span id="weblog-timeline">${each_post.date} ${each_tags}</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div id="pid_4314" class="weblog-tree-box"><div class="tree-box-title"><a href="javascript:;" target="_self"><h3 class="reply_quote">${each_post.title}</h3></a></div><div class="tree-box-content"><span id="core-info"><p>${each_post.content}</p></span><span id="other-info"><h4> Ps. </h4><p class="feeling">${each_post.subtitle}</p></span><p id="sub">${each_post.date} ${each_tags}</p></div></div></div>`;
                                    weblog.insertBefore(each_temp, load_box);
                                });
                        <?php
                            }
                        ?>
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