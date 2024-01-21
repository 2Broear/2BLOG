<?php
    /*
     *--------------------------------------------------------------------------
     * 页面缓存刷新
     *--------------------------------------------------------------------------
    */
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
    
    /*
     *--------------------------------------------------------------------------
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
    // 邮件 SMTP 初始化
    if(get_option('site_smtp_switcher')){
        add_action('phpmailer_init', 'mail_smtp');
        function mail_smtp( $phpmailer ) {
            $email = get_option('site_smtp_mail');
        	$phpmailer->FromName = get_bloginfo('name'); // 发件人昵称
        	$phpmailer->Host = get_option('site_smtp_host'); // 邮箱SMTP服务器
        	$phpmailer->Port = 465; // SMTP端口，不需要改
        	$phpmailer->Username = $email; // 邮箱账户
        	$phpmailer->Password = get_option('site_smtp_pswd'); // 此处填写邮箱生成的授权码 u5LZ4xWEuuoJdZJX
        	$phpmailer->From = $email; // 邮箱账户同上
        	$phpmailer->SMTPAuth = true;
        	$phpmailer->SMTPSecure = 'ssl'; // 端口25时 留空，465时 ssl，不需要改
        	$phpmailer->IsSMTP();
        }
        // smtp 测试邮件接口
        add_action('wp_ajax_mail_before_submit', 'mycustomtheme_send_mail_before_submit');
        add_action('wp_ajax_nopriv_mail_before_submit', 'mycustomtheme_send_mail_before_submit');
        function mycustomtheme_send_mail_before_submit(){
            check_ajax_referer('my_email_ajax_nonce');
            if(isset($_POST['action']) && $_POST['action'] == "mail_before_submit"){
                wp_mail($_POST['toemail'],'ajax e-mail sent ok','this mail sent from 2blog-settings SMTP e-mail sending test.');
                echo '测试邮件已发送';
                update_option('site_smtp_state', 1);
                die();
            }
            echo 'e-mail sending error'; //update_option('site_smtp_state',0);
            die();
        }
    }
    // 重写 WP 固定链接(初始化)
    if(!get_option('permalink_structure')){
        add_action( 'init', 'custom_permalink_rules' );
        function custom_permalink_rules() {
            global $wp_rewrite;
            $wp_rewrite->set_permalink_structure($wp_rewrite->root . '/%category%/%day%-%monthnum%-%year%_%postname%');
            $wp_rewrite->flush_rules();  // incase: 404 err occured
        }
    }
    // 移除 URL category 目录 // https://blog.wpjam.com/function_reference/trailingslashit/
    if(get_option('site_remove_category_switcher')){
        function remove_category($string, $type){
            if($type!='single' && $type=='category' && (strpos($string, 'category')!==false)){
                $url_without_category = str_replace(array("/category"), "", $string);
                return get_option('site_url_slash_sw') ? untrailingslashit($url_without_category) : trailingslashit($url_without_category); // use untrailingslashit to remove '/' at the end of url
                // return untrailingslashit($url_without_category);
            }
            return $string;
        }
        add_filter('user_trailingslashit', 'remove_category', 100, 2);
    }
    // 替换全局 gravatar 镜像源
    function replace_gravatar($avatar) {
    	$avatar = str_replace(array("//gravatar.com/avatar/", "//secure.gravatar.com/avatar/", "//www.gravatar.com/avatar/", "//0.gravatar.com/avatar/", 
    	"//1.gravatar.com/avatar/", "//2.gravatar.com/avatar/", "//cn.gravatar.com/avatar/"), get_option('site_avatar_mirror')."avatar/", $avatar);
    	return $avatar;
    }
    add_filter( 'get_avatar', 'replace_gravatar' );
    
    /*
     *--------------------------------------------------------------------------
     * 通用功能控制
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
    //通过邮箱匹配（gravatar/qq）头像（默认获取后台gravatar镜像源）
    function match_mail_avatar($user_mail){
        preg_match_all('/@qq.com/i', $user_mail, $qq_matches);
        preg_match_all('/(.*?)@/i', $user_mail, $mail_account);
        $avatar_mirror = get_option('site_avatar_mirror','//gravatar.com/');
        if($qq_matches[0]) $avatar_src='https://q.qlogo.cn/headimg_dl?dst_uin='.$mail_account[1][0].'&spec=640';else $avatar_src='https:'.$avatar_mirror.'avatar/'.md5($user_mail).'?s=100';
        return $avatar_src;
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
    // 搜索样式控制
    if(get_option('site_search_style_switcher')){
        // https://thomasgriffin.com/how-to-include-custom-post-types-in-wordpress-search-results/
        add_action( 'pre_get_posts', 'tg_include_custom_post_types_in_search_results' );
        function tg_include_custom_post_types_in_search_results($query){
            $res_array = explode(',',trim(get_option('site_search_includes','post')));  // NO "," Array
            $new_array = array();
            foreach ($res_array as $each){
                $arr = trim($each);  // NO WhiteSpace
                $arr ? array_push($new_array, $arr) : false;
            }
            if($query->is_main_query() && $query->is_search() && !is_admin()){
                $query->set('post_type', $new_array);  // implode(',', $new_array) array( 'post', 'page')
            }
        }
    }
    // 更新 sitemap 站点地图
    if(get_option('site_map_switcher')){
        function update_sitemap() {
            require_once(TEMPLATEPATH . '/plugin/sitemap.php');
        }
        add_action('publish_post','update_sitemap');
        // add_action('after_setup_theme', 'update_sitemap');
    }
    // RSS 输出分类  https://www.laobuluo.com/3863.html
    function rss_category($query) {
        $rss_array = explode(',',trim(get_option('site_rss_categories')));  // NO "," Array
        $new_array = array();
        $rss_array_count = count($rss_array);
        for($i=0;$i<$rss_array_count;$i++){
            $arr = trim($rss_array[$i]);  // NO WhiteSpace
            if($arr){
                $rss_category = get_category_by_slug($arr);
                $rss_category ? array_push($new_array, $rss_category->term_id) : false;
            }
        }
        if($query->is_feed){
            $query->set('cat', implode(',', $new_array));
        }
        return $query;
    }
    add_filter('pre_get_posts', 'rss_category');
    
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
                        unset($post);
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
     * WP Comment eamil/wechat notify, ajax/pagination etc
     *--------------------------------------------------------------------------
    */
    
    // 默认储存评论 COOKIE
    function coffin_set_cookies( $comment, $user, $cookies_consent){
    	$cookies_consent = true;
    	wp_set_comment_cookies($comment, $user, $cookies_consent);
    }
    add_action('set_comment_cookies','coffin_set_cookies',10,3);
    
    // 默认评论前置@（调用时插入文本）// 评论添加@（提交时写入数据库）https://www.ludou.org/wordpress-comment-reply-add-at.html
    function wp_comment_at($comment_text, $comment=''){
        $parent = $comment->comment_parent;
        if($parent>0) $comment_text = '<a href="#comment-' . $parent . '">@'. get_comment_author($parent) . '</a> , ' . $comment_text;
        return $comment_text;
    }
    add_filter('comment_text' , 'wp_comment_at', 20, 2);
    
    // 双数据页面类型（分类、页面）切换评论
    function dual_data_comments(){
        if(is_category()){
            if(get_option('site_third_comments')!='Valine'){
                echo '<div class="main"><span><h2> 评论留言 </h2></span><p>分类页面无法调用 WP 评论，<b> 开启移除 CATEGORY 后 </b>请前往页面指定当前页面父级，<small>亦可前往后台启用第三方评论。</small></p></div>';
            }else{
                include_once(TEMPLATEPATH . '/comments.php');
            }
        }else{
            comments_template();
        }
    }
    
    // wp评论邮件提醒（博主）手动开启
    if(get_option('site_wpmail_switcher')){
        function wp_notify_admin_mail( $comment_id, $comment_approved ) {
            global $img_cdn;
            $comment = get_comment( $comment_id );
            $admin_mail = get_option('site_smtp_mail', get_bloginfo('admin_email'));
            $user_mail = $comment->comment_author_email;
            $title = ' 「' . get_the_title($comment->comment_post_ID) . '」 收到一条来自 '.$comment->comment_author.' 的留言！';
            $body = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="'.$img_cdn.'/images/google.gif"><h2 class="head"><span class="title">「'. get_option("blogname") .'」上有一条新评论！</span><p><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank">点击查看</a></p></h2><div class="body"><p><strong>' . trim($comment->comment_author) . '：</strong></p><div class="content"><p><a class="at"href="#624a75eb1122b910ec549633">' . trim($comment->comment_content) . '</a></p></div></div></div>';
            $header = "\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
            // 当前用户不为博主时发送评论提醒邮件
            if($user_mail!=$admin_mail) wp_mail($admin_mail, $title, $body, $header);
            
        }
        add_action('comment_post', 'wp_notify_admin_mail', 10, 2);
    }
    
    // wp评论邮件提醒（访客）自动开启 // https://www.ziyouwu.com/archives/1615.html
    function wp_notify_guest_mail($comment_id) {
        $admin_mail = get_option('site_smtp_mail', get_bloginfo('admin_email'));
        $comment = get_comment($comment_id);
        $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
        if($parent_id!='' && $comment->comment_approved!='spam'){
            global $img_cdn;
            $tomail = trim(get_comment($parent_id)->comment_author_email);
            $title = '👉 叮咚！您在 「' . get_option("blogname") . '」 上有一条新回复！';
            $body = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="'.$img_cdn.'/images/google_flush.gif"><div class="head"><h2>'. trim(get_comment($parent_id)->comment_author) .'，</h2>有人回复了你在《' . get_the_title($comment->comment_post_ID) . '》上的评论！</div>&nbsp;&nbsp;&nbsp;你评论的：<div class="body"><div class="content"><p>' . trim(get_comment($parent_id)->comment_content) . '</p></div><p>被<strong> ' . trim($comment->comment_author) . ' </strong>回复：</p><div class="content"><p><a class="at" href="#">' . trim($comment->comment_content) . '</a></p></div><p style="margin:20px auto"><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank"rel="noopener">点击查看</a></p><p><center><b style="opacity:.5">此邮件由系统发送无需回复，</b>欢迎再来<a href="' . get_bloginfo('url') . '"target="_blank"rel="noopener"> '. get_option("blogname") .' </a>游玩！</center></p></div></div>';
            $headers = "From: \"" . get_option('blogname') . "\" <".$admin_mail.">\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
            // 博主收到评论回复时已收到评论邮件，无需重复通知（访客回复）邮件
            if($tomail!=$admin_mail) wp_mail($tomail, $title, $body, $headers);
        }
    }
    add_action('comment_post', 'wp_notify_guest_mail', 10, 2);
    
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
            unset($lazysrc,  $post);
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
    //关闭图片上传自动裁剪
    if(get_option('site_imgcrop_switcher')){
        // https://wordpress.stackexchange.com/questions/126718/disabling-auto-resizing-of-uploaded-images
        function remove_image_sizes( $sizes, $metadata ) {
            return [];
        }
        add_filter('intermediate_image_sizes_advanced', 'remove_image_sizes', 10, 2);
        // update_option('thumbnail_crop', '');
        // update_option('thumbnail_size_w', 0);
        // update_option('thumbnail_size_h', 0);
        // update_option('medium_size_w', 0);
        // update_option('medium_size_h', 0);
        // update_option('large_size_w', 0);
        // update_option('large_size_h', 0);
        // update_option('medium_large_size_w', 0);
    }
    
    //禁用远程管理文件 xmlrpc.php 防爆破
    if(get_option('site_xmlrpc_switcher')){
        add_filter('xmlrpc_enabled', '__return_false');
    }
    
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