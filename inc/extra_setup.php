<?php
    // Gutenberg editor
    load_theme_partial('/inc/wp_blocks.php');  // if(is_edit_page() || is_single()) 
    /*
     *--------------------------------------------------------------------------
     * API Plugin Setup.
     *--------------------------------------------------------------------------
    */
    
    // API接口调用验证，错误处理
    function api_illegal_auth($auth_array=array(), $auth_string=''){
        $is_illegal = false;
        foreach ($auth_array as $path){
            // echo $path.'<br/>';
            $is_illegal = strpos($path, $auth_string)!==false;
        }
        return $is_illegal;
    }
    function api_err_handle($msg='ok', $code=200, $var=false){
        $err_msg = new stdClass();
        $err_msg->code = $code;
        $code===200 ? $err_msg->msg=$msg : $err_msg->err=$msg;
        $res = json_encode($err_msg);
        if($var) return $res;else print_r($res);
    }
    function api_get_resultText($res_cls_obj, $decode=false){
        $formart = $decode ? json_decode($res_cls_obj) : $res_cls_obj;
        if(isset($formart->error)){
            return $formart->error->message;
        }
        $choices = $formart->choices[0];
        return isset($choices->message) ? $choices->message->content : $choices->text;
        // if(!isset($choices->message)){
        //     return trim($choices->text);
        // }
        // print_r(preg_replace('/\n/',"", $choices->message->content));
    }
    // API调用接口，接受三个参数：调用 api 文件名、api 代理访问（使用 api.php 文件中的 curl 携带鉴权参数二次请求（速度影响），适用前端异步调用、返回请求api或返回sign签名（如开启cdn鉴权
    function get_api_refrence($api='', $xhr=false, $exe=1, $pid=false){
        global $src_cdn;
        $res = 'unknown_api_refrence';
        if(!$api){
            return api_err_handle(200,$res,true);
        }
        global $post, $cdn_switch;
        $exe = $exe ? $exe : 0;
        $cdn_api = get_option('site_cdn_api');
        $pid = $pid ? $pid : $post->ID;
        $api_file = '/'.$api.'.php';
        $authentication = get_option('site_chatgpt_dir','authentication');
        $request_url = $cdn_switch&&$cdn_api ? custom_cdn_src('api', true) : $src_cdn.'/plugin/'.$authentication;
        $auth_url = $request_url.$api_file.'?pid='.$pid;
        $cdn_auth = get_option('site_chatgpt_auth');
        // 如出现访问403可能是由于CDN服务器开启了鉴权但后台面板中未填写 API Auth Sign 选项鉴权密钥（无法判断远程服务器是否开启鉴权）
        if($cdn_switch&&$cdn_api&&$cdn_auth){
            $stamp10x = time();
            $stamp16x = dechex($stamp10x);
            $auth_url = $auth_url.'&sign='.md5($cdn_auth.$api_file.$stamp16x).'&t='.$stamp16x;
        }
        $res = $xhr ? $src_cdn.'/plugin/api.php?auth='.$api.'&exec='.$exe.'&pid='.$pid : $auth_url;
        // $res = $xhr ? $src_cdn.'/plugin/'.$authentication.$api_file.'?pid='.$pid : $auth_url; //||!$cdn_api
        return $res;
    }
    
    /*
     *--------------------------------------------------------------------------
     * custom site query
     *--------------------------------------------------------------------------
    */
    
    // 返回友链指定分类 html
    function get_site_links($links, $frame=false){
        if(!$links) return 'unreachable links provide';
        global $lazysrc, $loadimg;
        $output = '';
        foreach ($links as $link){
            $link_notes = $link->link_notes;
            $link_target = $link->link_target;
            $link_rating = $link->link_rating;
            $link_url = $link->link_url;
            $link_name = $link->link_name;
            $link_desc = $link->link_description;
            $statu = ' standby';
            $status = $link->link_visible!='Y' ? $statu : '';
            $sex = $link_rating==1||$link_rating==10 ? ' girl' : '';
            $ssl = $link_rating>=9 ? ' https' : '';
            $rel = $link->link_rel ? $link->link_rel : false;
            $target = !$link_target ? '_blank' : $link_target;
            $impress = $link_notes&&$link_notes!='' ? '<span class="ssl'.$ssl.'"> '.$link_notes.' </span>' : false;
            $avatar = !$link->link_image ? 'https:' . get_option('site_avatar_mirror') . 'avatar/' . md5(mt_rand().'@rand.avatar') . '?s=300' : $link->link_image;
            $lazyhold = "";
            if($lazysrc!='src'){
                $lazyhold = 'data-src="'.$avatar.'"';
                $avatar = $loadimg;
            }
            switch ($frame) {
                case 'full':
                    $avatar_statu = $status==$statu ? '<img alt="近期访问出现问题" data-err="true" draggable="false">' : '<img '.$lazyhold.' src="'.$avatar.'" alt="'.$link_name.'" draggable="false">';
                    $rel_statu = $rel ? $rel : 'friends';
                    $output .= '<div class="inbox flexboxes'.$status.$sex.'"><div class="inbox-headside flexboxes">'.$avatar_statu.'</div>'.$impress.'<a href="'.$link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$rel_statu.'" title="'.$link_desc.'"><span class="lowside-title"><h4>'.$link_name.'</h4></span><span class="lowside-description"><p>'.$link_desc.'</p></span></a></div>';
                    break;
                case 'half':
                    $rel_statu = $rel ? $rel : 'recommends';
                    $output .= '<div class="inbox flexboxes'.$status.$sex.'">'.$impress.'<a href="'.$link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$rel_statu.'" title="'.$link_desc.'"><span class="lowside-title"><h4>'.$link_name.'</h4></span><span class="lowside-description"><p>'.$link_desc.'</p></span></a></div>'; //<em></em>
                    break;
                default:
                    $rel_statu = $status==$statu ? 'nofollow' : 'marked';
                    $output .= '<a href="'.$link_url.'" class="'.$status.'" title="'.$link_desc.'" target="'.$target.'" rel="'.$rel_statu.'" >'.$link_name.'</a>';
                    break;
            }
        }
        // unset($lazysrc, $loadimg);
        return $output;
    }
    
    // search/tag page posts with styles
    function the_posts_with_styles($queryString, $rewrite_query=false){
        if(is_archive() || is_search() || check_request_param('cid')){
            global $post, $lazysrc, $loadimg, $src_cdn;
            if($rewrite_query){
                $wp_query = $rewrite_query;
            }else{
                global $wp_query;
            };
            // print_r($wp_query);
            // $current_page = max(1, get_query_var('paged'));
            $maximun_page = $wp_query -> max_num_pages;  // record $maximun_page ouside the loop
            // print_r($current_page.' / '.$maximun_page);
            $post_styles = get_option('site_search_style_switcher');
            if($post_styles){
        ?>
            	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/news.css?v=2" />
                <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/weblog.css" />
                <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/acg.css" />
        <?php
            }
        ?>
        	<style>
        	    .news-inside-content h2{overflow:hidden}
        	    .win-content.main,
        	    .news-inside-content .news-core_area p,
        	    .empty_card{margin:0 auto;}
        	    .news-inside-content .news-core_area p{padding-left:0}
            	.win-content{width:100%;padding:0;display:initial}
                .win-top h5:before{content:none}
                .win-top h5{font-size:3rem;color:var(--preset-e)}
                .win-top h5 span:before{content:'';display:inherit;width:88%;height:36%;background-color:var(--theme-color);position:absolute;left:15px;bottom:1px;z-index:-1}
                .win-top h5 span{position:relative;background:inherit;color:white;font-weight:bolder;max-width: 10em;overflow: hidden;text-overflow: ellipsis;display: inline-block;vertical-align:middle}
                .win-top h5 b{font-family:var(--font-ms);font-weight:bolder;color:var(--preset-f);/*padding:0 10px;vertical-align:text-top;*/}
                .win-content article{max-width:88%;margin-top:auto}
                .win-content article.news-window{padding:0;margin-bottom:25px;/*border:1px solid rgb(100 100 100 / 10%);*/}
                .win-content article .info span{margin-left:10px}
                .win-content article .info span#slider{margin:auto}
        	    .news-window-img{max-width:16%}
        	    .rcmd-boxes{width:19%;display:inline-block;vertical-align:middle}
        	    .empty_card h1{max-width: 88%;overflow: hidden;text-overflow: ellipsis;display: block;margin: 25px auto;}
        	    .rcmd-boxes .info .inbox{max-width:none;margin: 5px}
        	    .main h2{font-weight: 600;font-size:1.25rem};
                #core-info p{padding:0}
                @media screen and (max-width:760px){
                    .win-content article{
                        width: 100%;
                    }
                    .rcmd-boxes{width:49%!important}
                }
                .main h2{margin-bottom: 0}
        	</style>
        <?php
            if(have_posts()) {
                while (have_posts()): the_post();
                    $postimg = get_postimg(0,$post->ID,true);
                    $lazyhold = "";
                    if($lazysrc!='src'){
                        $lazyhold = 'data-src="'.$postimg.'"';
                        $postimg = $loadimg;
                    }
                    $post_feeling = get_post_meta($post->ID, "post_feeling", true);
                    $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                    $post_rights = get_post_meta($post->ID, "post_rights", true);
                    $notes_slug = get_cat_by_template('notes','slug');
                    $news_slug = get_cat_by_template('news','slug');
                    $weblog_slug = get_cat_by_template('weblog','slug');
                    $acg_slug = get_cat_by_template('acg','slug');
                    if(!$post_styles){
        ?>
                        <article class="<?php if($post_orderby>1) echo 'topset'; ?> cat-<?php echo $post->ID ?>">
                            <h1>
                                <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                <?php if($post_rights&&$post_rights!="原创") echo '<sup>'.get_post_meta($post->ID, "post_rights", true).'</sup>'; ?>
                            </h1>
                            <p><?php custom_excerpt(150); ?></p>
                            <div class="info">
                                <span class="classify" id="">
                                    <i class="icom"></i>
                                    <?php 
                                        $cats = get_the_category();
                                        foreach ($cats as $cat){
                                            if($cat->slug!=$notes_slug) echo '<em>'.$cat->name.'</em> ';  //leave a blank at the end of em
                                        }
                                    ?>
                                </span>
                                <span class="valine-comment-count icom" data-xid="<?php echo parse_url(get_the_permalink(), PHP_URL_PATH) ?>"> <?php echo $post->comment_count; ?></span>
                                <span class="date"><?php the_time("d-m-Y"); ?></span>
                                <span id="slider"></span>
                            </div>
                        </article>
            <?php
                    }else{
                        if(in_category($news_slug)){
            ?>
                            <article class="<?php if($post_orderby>1) echo 'topset icom'; ?> news-window wow" data-wow-delay="0.1s" post-orderby="<?php echo $post_orderby; ?>">
                                <div class="news-window-inside">
                                    <?php
                                        if(has_post_thumbnail() || get_option('site_default_postimg_switcher')) echo '<span class="news-window-img"><a href="'.get_the_permalink().'"><img class="lazy" '.$lazyhold.' src="'.$postimg.'" /></a></span>';
                                    ?>
                                    <div class="news-inside-content">
                                        <h2 class="entry-title">
                                            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
                                        </h2>
                                        <span class="news-core_area entry-content"><p><?php custom_excerpt(66); ?></p></span>
                                        <span class="news-personal_stand" unselectable="on">
                                            <dd><?php echo $post_feeling ? $post_feeling : '...'; ?></dd>
                                        </span>
                                        <div id="news-tail_info">
                                            <ul class="post-info">
                                                <li class="tags author"><?php echo get_tag_list($post->ID); ?></li>
                                                <li title="讨论人数">
                                                    <?php 
                                                        $count = get_option('site_third_comments') ? 0 : $post->comment_count;
                                                        echo '<span class="valine-comment-count icom" data-xid="'.parse_url(get_the_permalink(), PHP_URL_PATH).'">'.$count.'</span>';
                                                    ?>
                                                </li>
                                                <li id="post-date" class="updated" title="发布日期">
                                                    <i class="icom"></i><?php the_time('d-m-Y'); ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </article>
            <?php
                        }elseif(in_category($weblog_slug)){
            ?>
                            <article class="weblog-tree-core-record i<?php the_ID() ?>">
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
                                    <div class="weblog-tree-box">
                                        <div class="tree-box-title">
                                            <a href="<?php //the_permalink() ?>" id="<?php the_title(); ?>" target="_self">
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
                                            <p id="sub"><?php echo $rich_date;echo get_tag_list($post->ID,2,''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </article>
            <?php  
                        }elseif(in_category($acg_slug)){
            ?>
                            <div class="rcmd-boxes flexboxes">
                                <div class="info anime flexboxes">
                                    <div class="inbox flexboxes">
                                        <div class="inbox-headside flexboxes">
                                            <span class="author"><?php echo $post_feeling = get_post_meta($post->ID, "post_feeling", true); ?></span>
                                            <?php
                                                echo '<img '.$lazyhold.' src="'.$postimg.'" alt="'.$post_feeling.'" crossorigin="Anonymous">'; //<img class="bg" '.$lazyhold.' src="'.$postimg.'" alt="'.$post_feeling.'">
                                            ?>
                                        </div>
                                        <div class="inbox-aside">
                                            <span class="lowside-title">
                                                <h4><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h4>
                                            </span>
                                            <span class="lowside-description">
                                                <p><?php custom_excerpt(66); ?></p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
            <?php
                        }else{
                            // results doen't match in_category template, like pages..
            ?>
                            <article class="<?php if($post_orderby>1) echo 'topset'; ?> cat-<?php echo $post->ID ?>">
                                <h1>
                                    <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                                    <?php if($post_rights&&$post_rights!="原创") echo '<sup>'.get_post_meta($post->ID, "post_rights", true).'</sup>'; ?>
                                </h1>
                                <p><?php custom_excerpt(150); ?></p>
                                <div class="info">
                                    <span class="classify" id="">
                                        <i class="icom"></i>
                                        <?php 
                                            $cats = get_the_category();
                                            foreach ($cats as $cat){
                                                if($cat->slug!=$notes_slug) echo '<em>'.$cat->name.'</em> ';  //leave a blank at the end of em
                                            }
                                        ?>
                                    </span>
                                    <span class="valine-comment-count icom" data-xid="<?php echo parse_url(get_the_permalink(), PHP_URL_PATH) ?>"> <?php echo $post->comment_count; ?></span>
                                    <span class="date"><?php the_time("d-m-Y"); ?></span>
                                    <span id="slider"></span>
                                </div>
                            </article>
            <?php
                        }
                    }
                endwhile;
                wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
                $current_page = max(1, get_query_var('paged'));  // update $current_page inside the loop
                // print_r($current_page.' / '.$maximun_page);
                // if($current_page > $maximun_page) return;
                $pages = paginate_links(array(
                    'prev_text' => __('上一页'),
                    'next_text' => __('下一页'),
                    'type' => 'plaintext',
                    'screen_reader_text' => null,
                    'total' => $maximun_page,  //总页数
                    'current' => $current_page, //当前页数
                ));
                if($pages) echo '<div class="pageSwitcher">'.$pages.'</div>';
                // unset($post, $lazysrc, $loadimg, $wp_query);
            }else{
                echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.$queryString.' </h1></div>';  //<b>'.current_slug(true).'</b> 
            }
        }
    }
    
    
    
    /*
     *---------------------------------------------------------------------------------------------------------------------------------
     * theme_setup
     *---------------------------------------------------------------------------------------------------------------------------------
    */
    
    
    
    /*****   页面缓存刷新   *****/
    function site_clear_db_caches() {
        // // 仅适用于不存在 wp_ajax_nopriv_my_ajax_action 请求验证的数据
        // remove_action('wp_ajax_my_ajax_action', 'my_ajax_callback');
        // remove_action('wp_ajax_nopriv_my_ajax_action', 'my_ajax_callback');
        // // 未解决BUG：data-nonce验证数据[24h有效，根据用户会话单独生成验证数据]被db缓存导致其他xhr请求会话返回403
        update_option('site_archive_contributions_cache', ''); //解决bug：切换全年报表后无法判断db数据库中是否已存在全年记录
        // //清除（重建）ACG 缓存
        // update_option('site_acg_stats_cache', '');
        update_option('site_archive_count_cache', '');  //清除（重建）归档统计
        update_option('site_archive_list_cache', '');  
        update_option('site_acg_post_cache', '');
    }
    add_action('save_post', 'site_clear_db_caches'); 
    add_action('delete_post', 'site_clear_db_caches');
    //清除（重建）更新链接
    function site_update_link_cache(){
        update_option('site_link_list_cache', '');  //清除（重建）友情链接
    }
    add_action('add_link', 'site_update_link_cache');
    add_action('edit_link', 'site_update_link_cache');
    add_action('delete_link', 'site_update_link_cache');
    //清除（重建）指定分类
    function update_category_post_cache($pid, $temp_slug, $cache) {
        $cat_temp = get_cat_by_template($temp_slug);
        $categories = wp_get_post_categories($pid);
        if (in_array($cat_temp->term_id, $categories)) update_option($cache, '');
    }
    function site_update_specific_caches($post_id) {
        //清除（重建）更新下载
        update_category_post_cache($post_id, 'download', 'site_download_list_cache');
        //清除（重建）归档统计
        update_category_post_cache($post_id, 'acg', 'site_archive_count_cache');
    }
    add_action('save_post', 'site_update_specific_caches');
    add_action('delete_post', 'site_update_specific_caches');
    
    /*--------------------------------------------------------------------------
     * wp_schedule_event 定时任务
     *--------------------------------------------------------------------------
    */
    function schedule_my_cronjob(){
        if(!wp_next_scheduled('db_caches_cronjob_hook')){
            // 设定定时作业执行时间（东八区时间）
            $timestamp = strtotime('today 09:00am Asia/Shanghai'); // 设置每天上午执行一次定时作业
            wp_schedule_event($timestamp, 'daily', 'db_caches_cronjob_hook'); 
        }
    }
    //定时清除（重建）缓存
    function site_clear_timeout_caches(){
        update_option('site_archive_count_cache', '');  //清除（重建）归档统计
        update_option('site_archive_contributions_cache', ''); //解决bug：切换全年报表后无法判断db数据库中是否已存在全年记录
        update_option('site_acg_stats_cache', ''); //定时清除（重建）ACG 缓存
        update_option('site_rank_list_cache', ''); //定时清除（重建）排行缓存
    }
    add_action('wp', 'schedule_my_cronjob');
    add_action('db_caches_cronjob_hook', 'site_clear_timeout_caches'); //定时更新 db caches
    // function schedule_acg_cronjob(){
    //     if(!wp_next_scheduled('acg_caches_cronjob_hook')){
    //         // 晚上更新一次ACG
    //         $timestamp = strtotime('today 17:30am Asia/Shanghai'); // 设置每天上午执行一次定时作业
    //         wp_schedule_event($timestamp, 'daily', 'acg_caches_cronjob_hook'); 
    //     }
    // }
    // add_action('wp', 'schedule_acg_cronjob');
    // add_action('acg_caches_cronjob_hook', 'site_clear_db_caches'); //定时更新 db caches
    // 自定义定时作业回调函数 //https://www.shephe.com/2023/07/no-pluglin-wordpress-archive-page/
    
    /*
     *--------------------------------------------------------------------------
     * 全局初始化操作
     *--------------------------------------------------------------------------
    */
    
    // 初始化 wordpress 执行函数
    function custom_theme_setup(){
        $expire = time() + 1209600;  // 自定义 cookie 函数 darkmode cookie set
        $theme_manual = array_key_exists('theme_manual',$_COOKIE) ? $_COOKIE['theme_manual'] : false;
        if(!isset($theme_manual)){  //auto set manual 0 (reactive with javascript manually)
            setcookie('theme_manual', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        if(!$theme_manual){  //if theme_manual actived
            $hour = current_time('G');
            $start = get_option('site_darkmode_start');
            $end = get_option('site_darkmode_end');
            $hour>=$end&&$hour<$start || $hour==$end&&current_time('i')>=0&&current_time('s')>=0 ? setcookie('theme_mode', 'light', $expire, COOKIEPATH, COOKIE_DOMAIN, false) : setcookie('theme_mode', 'dark', $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        // ARTICLE FULL-VIEW SET
        // if(!isset($_COOKIE['article_fullview'])){
        //     setcookie('article_fullview', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        // };
        // ARTICLE FONT-PLUS SET
        if(!isset($_COOKIE['article_fontsize'])){
            setcookie('article_fontsize', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        
        // SETUP sidebar FULL-VIEW status(default 1 enabled)
        if(!isset($_COOKIE['sidebar_status'])){
            setcookie('sidebar_status', 1, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        $sidebar_status = array_key_exists('sidebar_status',$_COOKIE) ? $_COOKIE['sidebar_status'] : false;
        if(!get_option('site_ads_switcher')&&!get_option('site_countdown_switcher')&&!get_option('site_pixiv_switcher')&&!get_option('site_mostview_switcher')){
            setcookie('sidebar_status', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        }else{
            setcookie('sidebar_status', 1, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        }
    };
    add_action('after_setup_theme', 'custom_theme_setup');
    
    /*
     *--------------------------------------------------------------------------
     * 主题通用功能控制
     *--------------------------------------------------------------------------
    */
    // 自动主题模式
    function theme_mode(){
        if(get_option('site_darkmode_switcher')){
            if(!array_key_exists('sidebar_status',$_COOKIE)){
                if(!$theme_manual){  //if theme_manual actived
                    $hour = current_time('G');
                    $start = get_option('site_darkmode_start');
                    $end = get_option('site_darkmode_end');
                    echo $hour>=$end&&$hour<$start || $hour==$end&&current_time('i')>=0&&current_time('s')>=0 ? 'light' : 'dark';
                };
            }else{
                echo $_COOKIE['theme_mode'];
            }
        }
    }
    //lazyload 图片<img>懒加载
    if(get_option('site_lazyload_switcher')){
        $lazysrc = 'data-src';
        add_filter('the_content', 'lazyload_images', 10);  // 设置 priority 低于 custom_cdn_src
        function lazyload_images($content){
            return preg_replace('/\<img(.*)src=("[^"]*")/i', '<img $1 data-src=$2', $content);
        }
        // replace comments images url
        add_filter('comment_text' , 'lazyload_images', 20, 2);
    }
    // 站点logo
    function site_logo($dark=false){
        if(get_option('site_logo_switcher')){
            // echo $_COOKIE['theme_mode']=='dark' ? '<span style="background: url('.get_option('site_logos').') no-repeat center center /cover;"></span>' : '<span style="background: url('.get_option('site_logo').') no-repeat center center /cover;"></span>';
            $logo = $dark ? get_option('site_logos') : get_option('site_logo');
            echo '<span style="background: url('.$logo.') no-repeat center center /cover;"></span>';
        }else{
            echo '<span>'.get_bloginfo('name').'</span>';
        }
    }
    // 站点公告
    function get_inform(){
        if(get_option('site_inform_switcher')){
            $inform_max = get_option('site_inform_num');
            echo '<div class="scroll-inform"><p><b>近期公告&nbsp;</b><i class="icom inform"></i>:&nbsp;</p><div class="scroll-block" id="informBox">';
            if(get_option('site_leancloud_switcher')){ //strpos(get_option('site_leancloud_category'), 'site_leancloud_inform')!==false
                $leancloud_arr = explode(',', get_option('site_leancloud_category'));
                if(in_array('site_leancloud_inform', $leancloud_arr)){
    ?>
                    <script type="text/javascript">  //addAscending("createdAt")
                        new AV.Query("inform").addDescending("createdAt").limit(<?php echo $inform_max; ?>).find().then(result=>{
                            for (let i=0,resLen=result.length,infobox=document.querySelector("#informBox"); i<resLen;i++) {
                                infobox.innerHTML += `<span>${result[i].attributes.title}</span>`;
                            }
                            const informs = document.querySelectorAll('.scroll-inform div.scroll-block span');
                            informs[0].classList.add("showes");  //init first show(no trans)
                            if(informs.length>1){
                                const cls_move = "move",
                                      cls_show = "show";
                                (function(els,count,delay){
                                    setInterval(() => {
                                        declear(els, cls_move, count)
                                        els[count].className = cls_move;  //current
                                        els[count+1] ? els[count+1].classList.add(cls_show) : els[0].classList.add(cls_show);
                                        count<els.length-1 ? count++ : count=0;
                                    }, delay);
                                })(informs, 0, 3000);
                            }
                        });
                    </script>
    <?php
                }
            }else{
                // $cid = get_option('site_inform_cid');
                query_posts(array(
                    'post_type' => 'inform',
                    // 'meta_key' => 'post_orderby',
                    'orderby' => array(
                        // 'meta_value_num' => 'DESC',
                        'date' => 'DESC',
                        'modified' => 'DESC'
                    ),
                    'posts_per_page' => $inform_max,  //use left_query counts
                    'post_status' => 'publish, draft'  //including all type but trash
                ));
                while(have_posts()) : the_post();
                    echo '<span>'.get_the_title().'</span>';
                endwhile; 
                wp_reset_query();  // 重置 wp 查询
            }
            echo '</div></div>';
        }
    }
    //面包屑导航（site_breadcrumb_switcher开启并传参true时启用）
    function breadcrumb_switch($switch=false, $frame=false){
        if(get_option('site_breadcrumb_switcher')&&$switch){
            if($frame){
                echo '<div class="news-cur-position wow fadeInUp"><ul>';
                    echo(the_breadcrumb());
                echo '</ul></div>';
            }else echo(the_breadcrumb());
        }
    };
    // 面包屑导航 https://www.thatweblook.co.uk/tutorial-wordpress-breadcrumb-function/
    if(get_option('site_breadcrumb_switcher')){
        function the_breadcrumb() {
            $sep = ' » ';
            if (!is_front_page()) {
                echo '<div class="breadcrumbs">';
                echo '<a href="';
                echo get_option('home');
                echo '">';
                bloginfo('name');
                echo '</a>' . $sep;
                if (is_category() || is_single() ){
                    the_category('title_li=');
                } elseif (is_archive() || is_single()){
                    if ( is_day() ) {
                        printf( __( '%s', 'text_domain' ), get_the_date() );
                    } elseif ( is_month() ) {
                        printf( __( '%s', 'text_domain' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'text_domain' ) ) );
                    } elseif ( is_year() ) {
                        printf( __( '%s', 'text_domain' ), get_the_date( _x( 'Y', 'yearly archives date format', 'text_domain' ) ) );
                    } else {
                        _e( 'Blog Archives', 'text_domain' );
                    }
                }
                if (is_single()) {
                    echo $sep;
                    the_title();
                }
                if (is_page()) {
                    echo the_title();
                }
                if (is_home()){
                    $page_for_posts_id = get_option('page_for_posts');
                    if ( $page_for_posts_id ) { 
                        global $post;
                        $post = get_page($page_for_posts_id);
                        setup_postdata($post);
                        // unset($post);
                        the_title();
                        rewind_posts();
                    }
                }
                echo '</div>';
            }
        };
    }
    // 文章 TOC 目录 https://www.ludou.org/wordpress-content-index-plugin.html/comment-page-3#comment-16566
    function article_index($content) {
        if(is_single() && preg_match_all('/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is', $content, $matches) && get_option('site_indexes_switcher')) {
            $match_h = $matches[1];
            $match_m = count($match_h);
            $ul_li = '';
            for($i=0;$i<$match_m;$i++){
                $value = $match_h[$i];
                $title = trim(strip_tags($matches[2][$i]));
                $content = str_replace($matches[0][$i], '<a href="javascript:;" id="title-'.$i.'" class="index_anchor" aria-label="anchor"></a><h'.$value.' id="title_'.$i.'">'.$title.'</h'.$value.'>', $content);
                $value = $match_h[$i];
                $pre_val = array_key_exists($i-1,$match_h) ? $match_h[$i-1] : 9;
                $ul_li .= $value>$pre_val || $value>=3 ? '<li class="child" id="t'.$i.'"><a href="#title-'.$i.'" title="'.$title.'">'.$title.'</a></li>' : '<li id="t'.$i.'"><a href="#title-'.$i.'" title="'.$title.'">'.$title.'</a></li>';
            }
            $article_index = array_key_exists('article_index',$_COOKIE) ? $_COOKIE['article_index'] : false;
            $auto_fold = !$article_index ? 'fold' : '';
            $index_array = explode(',', get_option('site_indexes_includes'));
            $index_array_count = count($index_array);
            for($i=0;$i<$index_array_count;$i++){
                $each_index = trim($index_array[$i]);
                if($each_index){
                    if(in_category($each_index)){
                        $content = '<div class="article_index '.$auto_fold.'" data-index="'.$match_m.'"><div class="in_dex"><p title="折叠/展开"><b>文章目录</b><i class="icom"></i></p><ul>' . $ul_li . '</ul></div></div>' . $content;
                    }
                }
            }
        }
        return $content;
    }
    add_filter( 'the_content', 'article_index');
    // 指定分类文章启用 chatgpt
    function in_chatgpt_cat($post=null){
        $chatgpt_cat = false;
        if(get_option('site_chatgpt_switcher')){  //&&is_single() //canceled for api calling
            if(!$post) global $post;  // global $post;
            $chatgpt_array = explode(',', get_option('site_chatgpt_includes'));
            $chatgpt_array_count = count($chatgpt_array);
            if($chatgpt_array_count>=1){
                for($i=0;$i<$chatgpt_array_count;$i++){
                    if(in_category($chatgpt_array[$i], $post)) $chatgpt_cat=true;
                }
            }
        }
        return $chatgpt_cat;
    }
    // 挂载文章 chatGPT AI 摘要 mount article chatgpt
    function article_ai_abstract($content) {
        global $src_cdn; //custom_cdn_src(0, true)
        $chatgpt_cat = in_chatgpt_cat();
        return $chatgpt_cat&&is_single() ? '<blockquote class="chatGPT" status="'.$chatgpt_cat.'"><p><b>文章摘要</b><span>chatGPT</span></p><p class="response load">standby chatGPT responsing..</p></blockquote><script type="module">const responser = document.querySelector(".chatGPT .response");try {import("'.$src_cdn.'/js/module.js").then((module)=>send_ajax_request("get", "'.get_api_refrence("gpt").'", false, (res)=>module.words_typer(responser, res, 25)));}catch(e){console.warn("dom responser not found, check backend.",e)}</script>'.$content : $content; //get_api_refrence("gpt", true)
    }
    add_filter( 'the_content', 'article_ai_abstract', 10);
    
    /*
     *--------------------------------------------------------------------------
     * WP Comment email/wechat notify, ajax/pagination etc
     *--------------------------------------------------------------------------
    */
    
    // 双数据页面类型（分类、页面）切换评论
    function dual_data_comments(){
        if(!is_category()){
            comments_template();
            return;
        }
        if(get_option('site_third_comments')=='Wordpress'){
            echo '<div class="main"><span><h2> 评论留言 </h2></span><p>分类页面无法调用 WP 评论，<b> 开启移除 CATEGORY 后 </b>请前往页面指定当前页面父级，<small>亦可前往后台启用第三方评论。</small></p></div>';
            return;
        }
        load_theme_partial('/comments.php');
    }
    
    // 评论企业微信应用通知
    if(get_option('site_wpwx_notify_switcher')){  //微信推送消息
        function push_weixin($comment_id){
            global $src_cdn;
            $comment = get_comment($comment_id);
            $post_id = $comment->comment_post_ID;
            $mail = $comment->comment_author_email;
            $admin_mail = get_option('site_smtp_mail', get_bloginfo('admin_email'));
            // 一个 POST 请求
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query(
                        array(
                            'name' => $comment->comment_author,
                            'mail' => $mail,  // 'avatar' => match_mail_avatar($mail),
                            'content' => strip_tags($comment->comment_content),
                            'title' => '《' . get_the_title($post_id) . '》 上有新评论啦~',
                            'url' => get_bloginfo('url')."/?p=$post_id#comments",
                            'image' => get_postimg(0,$post_id,true),
                            // 'corpid' => get_option('site_wpwx_id'),  // id
                            // 'corpsecret' => get_option('site_wpwx_secret'),  // secret
                            // 'msgtype' => get_option('site_wpwx_type'),  //type
                            // 'agentid' => get_option('site_wpwx_agentid'),  //aid
                        )
                    )
                )
            );
            // 评论邮件不为博主邮件时，返回 notify 接口（$postdata）不可使用 cdn，wpwx-notify.php 需调用 wp core
            if($mail!=$admin_mail) return file_get_contents($src_cdn . '/wpwx-notify.php',false,stream_context_create($options));else return false; //get_bloginfo('template_directory') custom_cdn_src('api', true)
        }
        // 挂载 WordPress 评论提交的接口
        add_action('comment_post', 'push_weixin', 19, 2);
    }
    
    //*****  WordPress AJAX Comments Setup etc (comment reply/paginate)  *****//
    
    // AJAX 回复评论
    if(get_option('site_ajax_comment_switcher')){
        // Loop-back child-comments (recursive)
        function wp_child_comments_loop($cur_comment){
            $child_comment = $cur_comment->get_children(array(
                'hierarchical' => 'threaded',
                // 'status'       => 'approve',
                'order'        => 'ASC',
                // 'orderby'=>'order_clause',
                // 'meta_query'=>array(
                //   'order_clause' => 'comment_parent'
                // )
            ));
            if(count($child_comment)<=0) return;
            foreach ($child_comment as $child) {
                wp_comments_template($child);
                wp_child_comments_loop($child);
            }
        }
        // Direct comments output
        function wp_comments_template($comment){
            global $lazysrc, $post;
            $id = $comment->comment_ID;
            $nick = $comment->comment_author;
            $link = $comment->comment_author_url;
            $email = $comment->comment_author_email;
            $userAgent = get_userAgent_info($comment->comment_agent);
            $approved = $comment->comment_approved;
            $content = $comment->comment_content; //esc_html();// //strip_tags(); XSS Secure Issues!!!
            $parent = $comment->comment_parent;
            if($approved=='0') $content = '<small style="opacity:.5">[ 评论未审核，通过后显示 ]</small>';
            if($parent>0) $content = '<a href="#comment-'.$parent.'">@'. get_comment_author($parent) . '</a> , ' . $content;
    ?>
            <div class="wp_comments" id="comment-<?php echo $id; ?>">
                <div class="vh" rootid="<?php echo $id; ?>">
                    <div class="vhead">
                        <a rel="nofollow" href="<?php echo $link; ?>" target="_blank">
                            <?php if(get_option('show_avatars')) echo '<img class="avatar" '.$lazysrc.'="'.match_mail_avatar($email).'" width=50 height=50 />'; ?>
                        </a>
                    </div>
                    <div class="vcontent">
                        <div class="vinfo">
                            <a rel="nofollow" href="<?php echo $link; ?>" target="_blank"><?php echo $nick; ?></a>
                            <?php
                                if($email==get_option('site_smtp_mail', get_bloginfo('admin_email'))) echo '<span class="admin">admin</span>';
                                echo '<span class="useragent">'.$userAgent['browser'].' / '.$userAgent['system'].'</span>';
                                if($approved=="0") echo '<span class="auditing">待审核</span>';
                            ?>
                            <div class="vtime"><?php echo date('Y-m-d', strtotime($comment->comment_date)); ?></div>
                            <?php 
                                if($approved){
                                    // global $wp;
                                    // $current_url = home_url( add_query_arg( array(), $wp->request ) );
                                    $locate = 'comment-'.$id;
                                    echo '<a rel="nofollow" class="comment-reply-link" href="javascript:void(0);" data-commentid="'.$id.'" data-postid="'.$post->ID.'" data-belowelement="'.$locate.'" data-respondelement="respond" data-replyto="'.$nick.'" aria-label="正在回复给：@'.$nick.'">回复</a>'; //'.$current_url.'?replytocom='.$id.'#respond  //'.$locate.'
                                    // unset($wp);
                                }
                            ?>
                        </div>
                        <?php echo '<p>'.$content.'</p>'; ?>
                    </div>
                </div>
            </div>
    <?php
            // unset($lazysrc,  $post);
        }
    }
    
    // AJAX 加载评论
    if(get_option('site_ajax_comment_paginate')){
        // Childs comment Loop-load method (recursive)
        function ajax_child_comments_loop($cur_comment){
            $child_comment = $cur_comment->get_children(array(
                'hierarchical' => 'threaded',
                'order'        => 'ASC',
                // 'orderby'=>'order_clause',
                // 'meta_query'=>array(
                //   'order_clause' => 'comment_parent'
                // )
            ));
            if(count($child_comment)>=1){
                // $child_comment = json_decode(json_encode($child_comment), true); // Objects to Array object
                foreach ($child_comment as $child) {
                    if($child->comment_approved=='0') $child->comment_content = '评论未审核，通过后显示';
                    // use privacy data encryption
                    $child->comment_author_IP = sha1($child->comment_author_IP);
                    $child->comment_author_email = md5($child->comment_author_email);
                    // add Objects for frontend calls
                    $child->_comment_reply = get_comment_author($child->comment_parent);
                    $child->_comment_agent = get_userAgent_info($child->comment_agent);
                    $cur_comment->_comment_childs = $child_comment; //$child_comment;//load all-childs but single[$child];
                    ajax_child_comments_loop($child);
                }
            }
            // return first-level(contains sub-more) only
            if($cur_comment->comment_parent==0) return $cur_comment; //$child_comment
        }
        // Ajax request comments output
        function ajaxLoadComments(){
            $pid = $_POST['pid'];
            check_ajax_referer($pid.'_comment_ajax_nonce');  // 检查 nonce
            $comments_array = [];
            $comments = get_comments(array(
                'post_id' => $pid,
                'orderby' => 'comment_date',
                'order'   => 'DESC',
                // 'status'  => 'approve',
                'number'  => $_POST['limit'],
                'offset'  => $_POST['offset'],
                'parent'  => 0,
                // 'comment__not_in' => [2,14],
            ));
            foreach($comments as $each){
                // user privacy data crypt
                $each->comment_author_IP = sha1($each->comment_author_IP);
                $each->comment_author_email = md5($each->comment_author_email);
                // add Objects for frontend calls
                $each->_comment_agent = get_userAgent_info($each->comment_agent);
                if($each->comment_parent==0) array_push($comments_array, ajax_child_comments_loop($each));
            }
            print_r(json_encode($comments_array));
            die();
        }
        add_action('wp_ajax_ajaxLoadComments', 'ajaxLoadComments');
        add_action('wp_ajax_nopriv_ajaxLoadComments', 'ajaxLoadComments');
    }
    
    /*
     *--------------------------------------------------------------------------
     * 额外功能
     *--------------------------------------------------------------------------
    */
    // 自动创建视频截图预览 // Automatic-Generate images captures(jpg/gif) while uploading a video file.(whether uploading inside the article)
    if(get_option('site_video_capture_switcher')){
        $execmd = ['shell_exec','system','exec'];
        $shell = false;
        foreach($execmd as $cmd){
            if(function_exists($cmd)) $shell=$cmd;
        }
        if($shell){
            // https://wp-kama.com/hook/wp_embed_handler_video
            function add_video_attachment_capture($attachment_ID){
                global $shell; //$current_user, 
                get_currentuserinfo();
                function ratio($a, $b){
                    $gcd = function($a, $b) use (&$gcd) {
                        return ($a % $b) ? $gcd($b, $a % $b) : $b;
                    };
                    $g = $gcd($a, $b);
                    return $a/$g . ':' . $b/$g;
                };
                $file = get_post($attachment_ID); // get_post_mime_type($attachment_ID);
                // DO WHAT YOU NEED 
                $fileURI = get_attached_file($attachment_ID); // wp_get_upload_dir()["basedir"]
                if(file_exists($fileURI)){
                    $dirURI = substr($fileURI, 0, strrpos($fileURI,'/')); //wp_upload_dir()["path"];
                    $fileName = $file->post_title;
                    $filePath = $dirURI.'/'.$fileName;// with file-name
                    preg_match('/video\/.+/', $file->post_mime_type, $vdo_upload);
                    //attachment_url_to_postid($file->guid)// get_post_like_slug($fileName)
                    if (array_key_exists(0,$vdo_upload)) {
                        $fileWidth = $shell("ffmpeg -i ".$fileURI." 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f1");
                        $fileHeight = $shell("ffmpeg -i ".$fileURI." 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f2");
                        $file_ratio = ratio($fileWidth,$fileHeight);
                        $preset_ratio = '16:9';
                        $calcH = $fileHeight;
                        $calcW = $fileWidth;
                        if($file_ratio!=$preset_ratio){
                            list($scaleW, $scaleH) = explode(':', $preset_ratio);
                            if($fileHeight < $fileWidth){
                                $calcW = round($fileHeight / $scaleH * $scaleW); //根据高计算比例宽
                            }else{
                                $calcH = round($fileWidth / $scaleW * $scaleH); //根据宽计算比例高
                            }
                        }
                        mkdir($filePath, 0777);
                        $savePath = $filePath.'/'.$fileName;
                        file_put_contents($savePath.'.json', json_encode($file, JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
                        // file_put_contents($savePath.'.txt', substr($fileURI, 0, strrpos($fileURI,'/')+1));
                        $fileList = glob($savePath.'*.jpeg');
                        // USE FFMPEG CAPTURE
                        if(count($fileList)<=0){
                            $shell('ffmpeg -i '.$fileURI.' -vf "scale='.$calcW.':'.$calcH.',setdar=16:9" -r 0.25 -f image2 "'.$savePath.'_%2d.jpeg"');
                            $fileList = glob($savePath.'*.jpeg');
                            $shell('ffmpeg -i '.$savePath.'_%2d.jpeg -filter_complex "scale=iw:-1,tile='.count($fileList).'x1" "'.$savePath.'.jpg"');
                            $shell('ffmpeg -r 1 -f image2 -i '.$savePath.'_%2d.jpeg -vf "scale=iw/2:-1" '.$savePath.'.gif');
                        }
                        unset($shell);
                    }
                }
            }
            add_action("add_attachment", 'add_video_attachment_capture');
            function delete_video_attachment_capture($attachment_ID){
                $attachment_file = get_post($attachment_ID);
                preg_match('/video\/.+/', $attachment_file->post_mime_type, $vdo_upload);
                if (!array_key_exists(0,$vdo_upload)) {
                    return;
                }else{
                    $fileURI = get_attached_file($attachment_ID); // wp_get_upload_dir()["basedir"]
                    if(file_exists($fileURI)){
                        $dirURI = substr($fileURI, 0, strrpos($fileURI,'/')); //wp_upload_dir()["path"];
                        $fileName = $attachment_file->post_title;
                        $filePath = $dirURI.'/'.$fileName;
                        // https://zhuanlan.zhihu.com/p/557484268
                        if(is_dir($filePath)){
                            $p = scandir($filePath);
                            foreach($p as $val){
                                if($val !="." && $val !=".."){
                                    if(is_dir($filePath.'/'.$val)){
                                        deldir($filePath.'/'.$val);
                                        // @rmdir($filePath.'/'.$val);
                                    }else{
                                        unlink($filePath.'/'.$val);
                                    }
                                }
                            }
                        }
                        @rmdir($filePath);
                    }
                }
            }
            add_action("delete_attachment", 'delete_video_attachment_capture');
        }
    };
    
?>