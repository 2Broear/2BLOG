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
        @keyframes spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}
        #loading{position:relative;padding:20px;display:block;height:80px}
        #loading:before{-webkit-box-sizing:border-box;box-sizing:border-box;content:"";position:absolute;display:inline-block;top:20px;left:50%;margin-left:-20px;width:40px;height:40px;border:6px double #a0a0a0;border-top-color:transparent;border-bottom-color:transparent;border-radius:50%;-webkit-animation:spin 1s infinite linear;animation:spin 1s infinite linear}
        .weblog-tree-box .tree-box-title h3:before{color:inherit;opacity:.5;text-decoration:none;}
        .vquote{border-left:none!important;}
        blockquote{margin:10px 0 auto!important};
        .tk-content{overflow: hidden;}
        .pageSwitcher span{color: var(--preset-9);}
        .vcontent blockquote{opacity:.75}
        body.dark #comment_txt h2{color:var(--preset-c)}
        .weblog-tree-core.reply .tree-box-title h3:after{
            content: "回复此片段";
        }
        .wp_comments_list .children{border:none;}
        figure img,figure video{border-radius:var(--radius)}
        figure{text-align:left}
        .weblog-tree-box .tree-box-content p{
            margin-bottom: 10px;
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
                    // echo basename(__FILE__);
                    $baas = get_option('site_leancloud_switcher')&&strpos(get_option('site_leancloud_category'), basename(__FILE__))!==false;  //use post as category is leancloud unset
                    if(!$baas){
                        $current_page = max(1, get_query_var('paged')); //current paged
                        $left_query = new WP_Query(array_filter(array(
                            'cat' => $cat,  //get_template_bind_cat(basename(__FILE__))->term_id;
                            'meta_key' => 'post_orderby',
                            'orderby' => array(
                                'meta_value_num' => 'DESC',
                                'date' => 'DESC'
                            ),
                            'paged' => $current_page,  //current paged
                            'posts_per_page' => get_option('posts_per_page'),  //use left_query counts
                            // 'post__not_in' => get_option('sticky_posts'),  //output posts without topset
                        )));
                        $total_pages = $left_query->max_num_pages;  //total pages
                        // Empty card if null reponsed
                        if(!$left_query->have_posts()){
                            echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t="'.current_slug(true).'"></i><h1> 这里，<b>空空的！</b> </h1></div>';  //<b>'.current_slug(true).'</b> 
                        }
                        while ($left_query->have_posts()):
                            $left_query->the_post();
                            $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                ?>
                            <div class="<?php if($post_orderby>1) echo 'topset '; ?>weblog-tree-core-record">
                                <div class="weblog-tree-core-l">
                                    <span id="weblog-timeline">
                                        <?php 
                                            echo $rich_date = get_the_tag_list() ? get_the_time('Y年n月j日').' - ' : get_the_time('Y年n月j日');
                                            the_tag_list($post->ID,2,'');
                                        ?>
                                    </span>
                                    <span id="weblog-circle"></span>
                                </div>
                                <div class="weblog-tree-core-r">
                                    <div class="weblog-tree-box">
                                        <div class="tree-box-title">
                                            <a href="<?php echo get_option('site_single_switcher') ? get_the_permalink() : 'javascript:;' ?>" id="<?php echo 'pid_'.get_the_ID() ?>" target="_self">
                                                <h3><?php the_title() ?></h3>
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
                                            <p id="sub"><?php echo $rich_date;the_tag_list($post->ID,2,''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <?php
                        endwhile;
                        wp_reset_query();
                    }
                    if($baas){
                        echo '<div class="weblog-tree-etc load"><button>加载更多</button></div>';
                    }
                ?>
                <div class="pageSwitcher">
                    <?php 
                        echo paginate_links(array(
                            'prev_text' => __('上一页'),
                            'next_text' => __('下一页'),
                            'type' => 'plaintext',
                            'screen_reader_text' => null,
                            'total' => $total_pages,  //总页数
                            'current' => $current_page, //当前页数
                        ));
                    ?>
                </div>
                <div id="comment_txt" class="wow fadeInUp" data-wow-delay="0.25s">
                    <?php 
                        // the_content();  // cancel for hidding sub-cat content
                        dual_data_comments();  // query comments from database before include
                    ?>
                </div>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
    if($baas){
?>
        <script type="text/javascript">
            const curTab = "<?php echo(current_slug()); ?>",
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
                        loadContent.innerHTML += '<div class="weblog-tree-core-record i'+index+'" data-type="'+type+'"><div class="weblog-tree-core-l"><span id="weblog-timeline" data-year="'+today.y+'" data-month="'+today.m+'" data-day="'+today.d+'">'+dates+'</span><span id="weblog-circle"></span></div><div class="weblog-tree-core-r"><div class="weblog-tree-box"><div class="tree-box-title"><h3 id="'+res.id+'">'+title+'</h3></div><div class="tree-box-content"><span id="core-info"><p>'+main+'</p></span><span id="other-info"><h4> Ps. </h4><p>'+ps+'</p><p id="sub">'+dates+'</p></span></div></div></div></div>';
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
            // BLOCKQUOTE Reply support
            const weblog = document.querySelector(".weblog-tree-core"),
                  replier = weblog.querySelectorAll('.weblog-tree-box .tree-box-title h3'),
                  editor = document.querySelector('textarea');
            weblog.onclick=(e)=>{
                var e = e || window.event,
                    t = e.target || e.srcElement;
                while(t!=weblog){
                    if(t.nodeName.toLowerCase()=="h3"){
                        let content = t.parentElement.parentElement.parentElement.querySelector('#core-info p');
                        editor.focus();
                        editor.value = '';
                        editor.setAttribute('placeholder', '回复片段：'+t.innerText);
                        var quote = `\n> __${t.innerText}__ \n> ${content.innerText.substr(0,88)}...`;
                            // delay = setTimeout(function(){
                                editor.style.cssText="min-height:150px;opacity:.75;";//editor.style.minHeight = '150px';
                                editor.value = '\n'+quote;//this.id;
                                editor.setSelectionRange(0,0);
                                // clearTimeout(delay);
                            // }, 1000);
                        editor.oninput=function(){
                            if(this.value.indexOf(quote)==-1 || this.value.substr(this.value.length-3,this.value.length)!='...'){  //
                                this.value = quote;
                                editor.setSelectionRange(0,0);
                            }
                        }
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