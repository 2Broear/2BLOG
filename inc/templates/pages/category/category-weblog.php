<?php
/*
   Template name: （BaaS）日志模板
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/weblog.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        figure{text-align:left}
        figure img,figure video{border-radius:var(--radius);width: 66%;}
        .anchor{
            position: relative;
            top: -15px;
        }
        #comment_txt{padding:0 15px;box-sizing:border-box;}
        button.switch-to-memos{
            font-size: var(--min-size);
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .memos-tree-core{
            padding: 3% 15px 0;
            display: none;
        }
        .memos-tree-core .weblog-tree-core-l{
            max-width: 12%;
        }
        .weblog-tree-all.useMemos .weblog-tree-core{
            display: none;
        }
        .weblog-tree-all.useMemos .memos-tree-core{
            display: block;
        }
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
                    <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
                    <video src="<?php echo get_option('site_weblog_video'); ?>" poster="<?php echo $winbg; ?>" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
            <?php
                }
            ?>
        </div>
        <?php get_inform(); ?>
        <div class="weblog-tree-all">
            <?php
                $memos_sw = get_option('site_memos_switcher');
                if($memos_sw) {
            ?>
                <div style="width:100%;text-align: right;padding: 10px 20px 5px 15px;box-sizing: border-box;">
                    <button class="switch-to-memos" href="javascript:;" title="加载更多">切换 Memos 记录</button>
                </div>
                <div class="memos-tree-core">
                    <div class="load">
                        <button class="load-more load-memos" href="javascript:;" data-click="0">加载更多</button>
                    </div>
                </div>
            <?php
                }
            ?>
            <div class="weblog-tree-core <?php $origin_cmt='Wordpress';$third_cmt = get_option('site_third_comments');echo $third_cmt!=$origin_cmt ? 'reply' : ''; ?>" style="padding-top:15px;">
                <?php
                    $async_sw = get_option('site_async_switcher');
                    $weblog_slug = get_cat_by_template('weblog','slug'); //current_slug();
                    $async_array = explode(',', get_option('site_async_includes'));
                    $use_async = $async_sw ? in_array($weblog_slug, $async_array) : false;
                    $async_loads = $async_sw&&$use_async ? get_option("site_async_weblog") : 15;
                    $baas = get_option('site_leancloud_switcher') && strpos(get_option('site_leancloud_category'), basename(__FILE__))!==false; //in_array(basename(__FILE__), explode(',', get_option('site_leancloud_category')))
                    $log_single_sw = get_option('site_single_switcher');
                    if($log_single_sw){
                        $log_slug = get_cat_by_template('weblog','slug');
                        $includes = get_option('site_single_includes');
                        $log_single_sw = in_array($log_slug, explode(',', $includes));
                    }
                    $reply_quote = $log_single_sw ? 'reply_quote' : '';
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
                                            <?php
                                                $target = '_blank';
                                                $href = get_the_permalink();
                                                $rel = '';
                                                if($log_single_sw){
                                                    $target = '_self';
                                                    $href = 'javascript:;';
                                                    $rel = 'nofollow';
                                                }
                                                echo '<a href="'.$href.'" target="'.$target.'" rel="'.$rel.'"><h3 class="'.$reply_quote.'">'.get_the_title().'</h3></a>';
                                            ?>
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
                        echo $baas ? '<div class="weblog-tree-etc load"><button>加载更多</button></div>' : '<div class="load'.$disable_statu.'"><button class="load-more" href="javascript:;" data-counts="'.$all_count.'" data-load="'.$posts_count.'" data-click="0" data-cid="'.$cat.'" data-cat="'.$weblog_slug.'" data-nonce="'.wp_create_nonce(current_slug()."_posts_ajax_nonce").'" title="加载更多">加载更多</button></div>';
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
    get_foot();
    if($baas){
?>
        <script type="text/javascript">
            const curTab = "<?php echo $weblog_slug; ?>",
                  loadbtn = document.querySelector(".load button");
            var limiter = <?php echo $async_loads; ?>,
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
                        loadContent.innerHTML += '<div class="weblog-tree-core-record i'+index+'" data-type="'+type+'"><div class="weblog-tree-core-l"><span id="weblog-timeline" data-year="'+today.y+'" data-month="'+today.m+'" data-day="'+today.d+'">'+dates+'</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div class="weblog-tree-box"><div class="tree-box-title"><h3 id="'+res.id+'" class="<?php echo $reply_quote; ?>">'+title+'</h3></div><div class="tree-box-content"><span id="core-info"><p>'+main+'</p></span><span id="other-info"><h4> Ps. </h4><p>'+ps+'</p><p id="sub">'+dates+'</p></span></div></div></div></div>';
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
    }
?>
    <script>
        const weblog = document.querySelector(".weblog-tree-all"),
              preset_loads = <?php echo $async_loads; ?>;
<?php
    if($memos_sw){
?>
        const memosClass = "useMemos",
              memoLoaded = "usedMemos";
        let memos_tree = weblog.querySelector('.memos-tree-core'),
            memos_load = memos_tree.querySelector('.load'),
            memos_more = memos_load.querySelector('.load-more'),
            memos_url = "<?php echo get_api_refrence('memos', true); ?>",
            memos_params = {
                'creatorId': 1,
                'rowStatus': "", //ARCHIVED
                'tag': "",
                'limit': preset_loads,
                'offset': 0,
            };
<?php
    }
?>
    </script>
<?php
    // if($third_cmt!=$origin_cmt){
?>
        <script>
            // BLOCKQUOTE Reply
            const editor = document.querySelector('textarea'),
                selectText = function(textbox=null, startIndex=0, stopIndex=0){
                    if (textbox.setSelectionRange){
                        textbox.setSelectionRange(startIndex, stopIndex);
                    } else if (textbox.createTextRange){
                        var range = textbox.createTextRange();
                        range.collapse(true);
                        range.moveStart("character", startIndex);
                        range.moveEnd("character", stopIndex - startIndex);
                        range.select();
                    }
                };
            weblog.onclick=(e)=>{
                e = e || window.event;
                let t = e.target || e.srcElement;
                if(!t) return;
                while(t!=weblog){
                    if(t.classList && t.classList.contains("reply_quote")){
                    <?php
                        if($log_single_sw){
                    ?>
                        editor.focus();
                        editor.value = "";
                        editor.setAttribute('placeholder', '回复片段：'+t.innerText);
                        const cores = getParByCls(t, 'weblog-tree-box').querySelector('#core-info'),
                              quote = `\n> __${t.innerText}__ \n> ${cores ? cores.innerText.replace(/\n/g,"").substr(0,88) : ".."}...`;
                        editor.style.cssText="min-height:150px;opacity:.75;";
                        editor.value = quote;//this.id; '\n'+
                        selectText(editor, 0, 0); //editor.setSelectionRange(0,0);
                        editor.oninput=function(){
                            if(this.value.indexOf(quote)==-1 || this.value.substr(this.value.length-3,this.value.length)!='...'){
                                this.value = quote;
                                selectText(editor, 0, 0); //editor.setSelectionRange(0,0);
                            }
                        }
                    <?php
                        }
                    ?>
                        break;
                    }else if(t.classList && t.classList.contains("load-more")){
                    <?php
                        if($memos_sw){
                    ?>
                        if(t.classList && t.classList.contains("load-memos")){
                            // update offsets
                            memos_params.offset = preset_loads*parseInt(memos_more.dataset.click);
                            // exec updates
                            load_ajax_posts(t, 'weblog', preset_loads, function(res){
                                let fragment = document.createDocumentFragment();
                                res.forEach(item=> {
                                    let each_temp = document.createElement("DIV");
                                    each_temp.id = "pid_"+item.id;
                                    each_temp.classList.add("weblog-tree-core-record");
                                    each_temp.innerHTML = `<div class="weblog-tree-core-l"><span id="weblog-timeline">${item.creatorName}</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div id="${item.id}" class="weblog-tree-box"><div class="tree-box-content"><span id="core-info">${item.content}</span><p id="sub">${item.createdTs}</p></div></div></div>`;
                                    fragment.appendChild(each_temp);
                                });
                                memos_tree.insertBefore(fragment, memos_load);
                            }, false, memos_url, parse_ajax_parameter(memos_params, true));
                            break;
                        }
                    <?php
                        }
                        if($async_sw&&$use_async){
                    ?>
                        load_ajax_posts(t, 'weblog', preset_loads, function(res, load_box){
                            let fragment = document.createDocumentFragment();
                            res.forEach(item=> {
                                let temp = document.createElement("DIV"),
                                    tags = item.tag ? " - "+item.tag : "";
                                temp.id = "pid_"+item.id;
                                temp.classList.add("weblog-tree-core-record");
                                temp.innerHTML = `<div class="weblog-tree-core-l"><span id="weblog-timeline">${item.date} ${tags}</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div id="${item.id}" class="weblog-tree-box"><div class="tree-box-title"><a href="javascript:;" target="_self"><h3 class="<?php echo $reply_quote; ?>">${item.title}</h3></a></div><div class="tree-box-content"><span id="core-info">${item.content}</span><span id="other-info"><h4> Ps. </h4><p class="feeling">${item.subtitle}</p></span><p id="sub">${item.date} ${tags}</p></div></div></div>`;
                                fragment.appendChild(temp);
                            });
                            document.querySelector('.weblog-tree-core').insertBefore(fragment, load_box);
                        });
                        break;
                    <?php
                        }
                    ?>
                    }else if(t.classList && t.classList.contains("switch-to-memos")){
                    <?php
                        if($memos_sw){
                    ?>
                        t.style.pointerEvents = "none"; // t.textContent = "正在切换记录..";
                        if(weblog.classList.contains(memosClass)) {
                            weblog.classList.remove(memosClass);
                            t.textContent = "切换 Memos 记录";
                            t.style.pointerEvents = "";
                            return;
                        }else{
                            t.style.pointerEvents = "";
                            const memos_ctx = "返回 Weblog 记录";
                            weblog.classList.add(memosClass);
                            if(weblog.classList.contains(memoLoaded)) {
                                t.textContent = memos_ctx;
                                console.debug(memoLoaded);
                                return;
                            }
                            send_ajax_request("GET", memos_url, '', function(res){
                                const memos_res = JSON.parse(res);
                                memos_more.dataset.counts = memos_res.length;
                                if(memos_res.error) {
                                    console.warn('an error occured', memos_res);
                                    return;
                                }
                            }, (err)=>console.warn(err));
                            // preload loads before load_ajax_posts(loads required)
                            memos_more.dataset.load = preset_loads;
                            load_ajax_posts(t, 'weblog', preset_loads, function(res){
                                memos_more.dataset.click = 1;
                                weblog.classList.add(memoLoaded);
                                t.textContent = memos_ctx;
                                let fragment = document.createDocumentFragment();
                                res.forEach(item=> {
                                    let temp = document.createElement("DIV"),
                                        tags = item.tag ? " - "+item.tag : "";
                                    temp.id = "pid_"+item.id;
                                    temp.classList.add("weblog-tree-core-record");
                                    temp.innerHTML = `<div class="weblog-tree-core-l"><span id="weblog-timeline">${item.creatorName}</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div id="${item.id}" class="weblog-tree-box"><div class="tree-box-content"><span id="core-info">${item.content}</span><p id="sub">${item.createdTs}</p></div></div></div>`;
                                    fragment.appendChild(temp);
                                });
                                memos_tree.insertBefore(fragment, memos_load);
                            }, false, memos_url, parse_ajax_parameter(memos_params, true));
                        }
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
    // }
?>
</body></html>