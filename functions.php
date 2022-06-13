<?php 
    //禁用 Gutenberg 编辑器
    // add_filter('use_block_editor_for_post', '__return_false');
    // remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
    /**
     * This function modifies the main WordPress query to include an array of 
     * post types instead of the default 'post' post type.
     *
     * @param object $query The main WordPress query.
     */
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
    
    
    // get bind category-template cat by specific binded-temp post_id
    function get_template_bind_cat($template=false){
        global $wpdb;
        // $template_page_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$template'");
        // $template_term_id = get_post_meta($template_page_id, "post_term_id", true); //SELECT *
        $template_term_id = $wpdb->get_var("SELECT term_id FROM $wpdb->termmeta WHERE meta_value = '$template'");
        return get_category($template_term_id);
    }
    /* ------------------------------------------------------------------------ *
     * Plugin Name: Link Manager
     * Description: Enables the Link Manager that existed in WordPress until version 3.5.
     * Author: WordPress
     * Version: 0.1-beta
     * See http://core.trac.wordpress.org/ticket/21307
     * ------------------------------------------------------------------------ */
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
    // 启用 wordpress 特色图片（缩略图）功能
    if(function_exists('add_theme_support')) {
        add_theme_support('post-thumbnails');
    };
    if(function_exists('add_image_size')){
        add_image_size('customized-post-thumb',300,300);
    };
    // include_once(TEMPLATEPATH . '/plugin/nocategory.php');  
    // https://blog.wpjam.com/function_reference/trailingslashit/
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
    /* ------------------------------------------------------------------------ *
     * WordPress Comments Setup etc
     * ------------------------------------------------------------------------  */
    function my_fields($fields) {
    	$fields =  array(
        	'author' => ($req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" placeholder="昵称" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' />',
        	
        	'email'  => ($req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" placeholder="邮箱" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' />',
        	
        	'url'    => '<input id="url" name="url" type="text" placeholder="网址" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />',
    	);
    	return $fields;
    }
    add_filter('comment_form_default_fields','my_fields');
    
    /* Auto set comment user cookies */
    function coffin_set_cookies( $comment, $user, $cookies_consent){
    	$cookies_consent = true;
    	wp_set_comment_cookies($comment, $user, $cookies_consent);
    }
    add_action('set_comment_cookies','coffin_set_cookies',10,3);
    
    
    //通过meta_query获取指定id自定义排序输出子级
    function meta_order_categories($cid,$order,$orderby,$num=99){
        return array('child_of'=>$cid, 'parent'=>$cid, 'hide_empty'=>0, 'number' => $num,
            'order'=>$order ,'orderby'=>'order_clause', 
            'meta_query'=>array(
                'order_clause' => array(
                    'key' => $orderby,
                    'type' => 'NUMERIC'
                )
            )
        );
    }
    
    
    /* ------------------------------------------------------------------------ *
     * 自定义后台面板选项
     * https://themes.artbees.net/blog/custom-setting-page-in-wordpress/
     * ------------------------------------------------------------------------ */
     
    include_once(TEMPLATEPATH . '/theme_settings.php');
    // https://laurahoughcreative.co.uk/using-the-wordpress-media-uploader-in-your-plugin-options-page/
    // https://rudrastyh.com/wordpress/customizable-media-uploader.html
    // 加载options后台js代码（wp自带jquery无需原生）
    function misha_include_js() {
    	// I recommend to add additional conditions just to not to load the scipts on each page
    	if(!did_action('wp_enqueue_media')){
    		wp_enqueue_media();
    	}
     	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/plugin/options2blog.js', array( 'jquery' ) );
    }
    add_action( 'admin_enqueue_scripts', 'misha_include_js' );
    
    /* ------------------------------------------------------------------------ *
     * WP Mail SMTP Setup & Comment eamil/wechat notify etc
     * ------------------------------------------------------------------------ *
    */
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
    
    // wp评论邮件提醒（博主）手动开启
    if(get_option('site_wpmail_switcher')){
        function wp_notify_admin_mail( $comment_id, $comment_approved ) {
            $comment = get_comment( $comment_id );
            $admin_mail = get_option('site_smtp_mail', get_bloginfo('admin_email'));
            $user_mail = $comment->comment_author_email;
            $title = ' 「' . get_the_title($comment->comment_post_ID) . '」 收到一条来自 '.$comment->comment_author.' 的留言！';
            $body = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="https://img.2broear.com/google/google_s.gif"><h2 class="head"><span class="title">「'. get_option("blogname") .'」上有一条新评论！</span><p><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank">点击查看</a></p></h2><div class="body"><p><strong>' . trim($comment->comment_author) . '：</strong></p><div class="content"><p><a class="at"href="#624a75eb1122b910ec549633">' . trim($comment->comment_content) . '</a></p></div></div></div>';
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
            $tomail = trim(get_comment($parent_id)->comment_author_email);
            $title = '👉 叮咚！您在 「' . get_option("blogname") . '」 上有一条新回复！';
            $body = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="https://img.2broear.com/google/google_flush.gif"><div class="head"><h2>'. trim(get_comment($parent_id)->comment_author) .'，</h2>有人回复了你在《' . get_the_title($comment->comment_post_ID) . '》上的评论！</div>&nbsp;&nbsp;&nbsp;你评论的：<div class="body"><div class="content"><p>' . trim(get_comment($parent_id)->comment_content) . '</p></div><p>被<strong> ' . trim($comment->comment_author) . ' </strong>回复：</p><div class="content"><p><a class="at" href="#">' . trim($comment->comment_content) . '</a></p></div><p style="margin:20px auto"><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank"rel="noopener">点击查看</a></p><p><center><b style="opacity:.5">此邮件由系统发送无需回复，</b>欢迎再来<a href="' . get_bloginfo('url') . '"target="_blank"rel="noopener"> '. get_option("blogname") .' </a>游玩！</center></p></div></div>';
            $headers = "From: \"" . get_option('blogname') . "\" <".$admin_mail.">\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
            // 博主收到评论回复时已收到评论邮件，无需重复通知（访客回复）邮件
            if($tomail!=$admin_mail) wp_mail($tomail, $title, $body, $headers);
        }
    }
    add_action('comment_post', 'wp_notify_guest_mail', 10, 2);
    
    // 评论企业微信应用通知
    if(get_option('site_wpwx_notify_switcher')){  //微信推送消息
        function push_weixin($comment_id){
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
                            'title' => '《' . get_the_title($post_id) . '》 上有新评论啦！',
                            'url' => get_bloginfo('url')."/?p=$post_id#comments",
                            'image' => get_postimg(0,$post_id),
                            // 'corpid' => get_option('site_wpwx_id'),  // id
                            // 'corpsecret' => get_option('site_wpwx_secret'),  // secret
                            // 'msgtype' => get_option('site_wpwx_type'),  //type
                            // 'agentid' => get_option('site_wpwx_agentid'),  //aid
                        )
                    )
                )
            );
            // 评论邮件不为博主邮件时，返回 notify 接口（$postdata）
            if($mail!=$admin_mail) return file_get_contents(get_bloginfo('template_directory') . '/plugin/wpwx-notify.php',false,stream_context_create($options));else return false;
        }
        // 挂载 WordPress 评论提交的接口
        add_action('comment_post', 'push_weixin', 19, 2);
    }
    
    // 评论添加@（提交时写入数据库）https://www.ludou.org/wordpress-comment-reply-add-at.html
    // function ludou_comment_add_at( $commentdata ) {
    //   if( $commentdata['comment_parent'] > 0) {
    //     $commentdata['comment_content'] = '@<a href="#comment-' . $commentdata['comment_parent'] . '">'.get_comment_author( $commentdata['comment_parent'] ) . '</a> , ' . $commentdata['comment_content'];
    //   }
    //   return $commentdata;
    // }
    // add_action( 'preprocess_comment' , 'ludou_comment_add_at', 20);
    // 评论添加@（调用时插入文本/get_comments()需另行配置）
    function wp_comment_at($comment_text, $comment=''){
        $parent = $comment->comment_parent;
        if($parent>0) $comment_text = '<a href="#comment-' . $parent . '">@'. get_comment_author($parent) . '</a> , ' . $comment_text;
        return $comment_text;
    }
    add_filter('comment_text' , 'wp_comment_at', 20, 2);
    
    
    /* ------------------------------------------------------------------------ *
     * 自定义功能函数
     * ------------------------------------------------------------------------ */
    // 分类背景图/视频海报
    function cat_metabg($cid, $preset=false){
        $metaimg = get_term_meta($cid, 'seo_image', true);  //$page_cat->term_id
        return $metaimg ? $metaimg : ($preset ? $preset : get_option('site_bgimg'));
    }
    // 更新 sitemap 站点地图
    if(get_option('site_map_switcher')){
        function update_sitemap() {
            require_once(TEMPLATEPATH . '/plugin/sitemap.php');
        }
        add_action('publish_post','update_sitemap');
    }
    // 站点头部
    function get_head(){
        require_once(TEMPLATEPATH. '/head.php');
    }
    // WP评论统计排行 https://www.seo628.com/2685.html
    function get_comments_ranking(){
        global $wpdb;
        $comments_data = array();
        $comments_mail = $wpdb->get_results("SELECT DISTINCT comment_author_email FROM $wpdb->comments WHERE 1 ");
        foreach($comments_mail as $email){
            $each_mail = $email->comment_author_email;
            $mail_data = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_author_email = '$each_mail' ");
            $comments_obj = new stdClass();
            $comments_obj->name = $mail_data[0]->comment_author;
            $comments_obj->mail = $each_mail;
            $comments_obj->link = $mail_data[0]->comment_author_url;
            $comments_obj->count = count($mail_data);
            array_push($comments_data, $comments_obj);
        }
        usort($comments_data,function($first,$second){
            return $first->count < $second->count;
        });
        return $comments_data;
    }
    // 双数据页面类型（分类、页面）切换评论
    function dual_data_comments(){
        if(is_category()){
            if(!get_option('site_comment_switcher')){
                echo '<div class="main"><span><h2> 评论留言 </h2></span><p>分类（category）页面无法调用 Wordpress 评论，可前往后台启用第三方评论。<small>若已开启WPML插件需关闭后刷新固定链接</small></p></div>';
            }else{
                include_once(TEMPLATEPATH . '/comments.php');
            }
        }else{
            comments_template();
        }
    }
    // 站点logo
    function site_logo($src=false){
        if(get_option('site_logo_switcher')){
            // echo $_COOKIE['theme_mode']=='dark' ? '<span style="background: url('.get_option('site_logos').') no-repeat center center /cover;"></span>' : '<span style="background: url('.get_option('site_logo').') no-repeat center center /cover;"></span>';
            echo '<span style="background: url('.get_option('site_logo').') no-repeat center center /cover;"></span>';
        }else{
            echo '<span>'.get_bloginfo('name').'</span>';
        }
    }
    // 近期公告
    function get_inform(){
        if(get_option('site_inform_switcher')){
            echo '<div class="scroll-inform"><p><b>近期公告&nbsp;</b><i class="icom inform"></i>:&nbsp;</p><div class="scroll-block" id="informBox">';
            if(get_option('site_leancloud_switcher')){
    ?>
                <script type="text/javascript">
                    new AV.Query("inform").addAscending("createdAt").limit(5).find().then(result=>{
                        for (let i=0; i<result.length;i++) {
                            let res = result[i],
                                title = res.attributes.title,
                                content = res.attributes.content;
                            document.querySelector("#informBox").innerHTML += `<span>${title}</span>`;
                        }
                        const informs = document.querySelectorAll('.scroll-inform div.scroll-block span');
                        informs[0].classList.add("showes");  //init first show(no trans)
                        informs.length>1 ? flusher(informs,0,3000) : false;  //scroll inform
                    });
                </script>
    <?php
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
                    'posts_per_page' => get_option('posts_per_page'),  //use left_query counts
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
    // 自定义 wpdb 查询函数
    // function get_wpdb_query($data,$key,$val,$type){
    //     global $wpdb;
    //     return $wpdb->get_var("SELECT $data FROM $wpdb->posts WHERE $key = '$val' AND post_type = '$type'");
    // }
    function wpdb_postmeta_query($data,$key,$val){
        global $wpdb;
        return $wpdb->get_var("SELECT $data FROM $wpdb->postmeta WHERE $key = '$val'");
    }
    // 获取自定义页面所属分类term_id
    function get_page_cat_id($slug){
        global $wpdb;
        return $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE slug = '$slug'");
    }
    // 获取自定义页面内容
    function the_page_id($slug){
        global $wpdb;
        return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
    }
    function the_page_content($slug){
        global $wpdb;
        $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        echo get_page($id)->post_content;// if(is_page()) echo get_page($id)->post_content;else echo '<p style="color:red">页面 '.current_slug().' 不存在，无法调用该页面内容。</p>';
    }
    
    //自定义当前滚动提示
    function current_tips($nick){
        if(!is_single()) echo "<b>".$nick."</b> の ";
        switch (true) {
            case is_home():
                echo bloginfo('name');
                break;
            case is_category():
                echo single_cat_title();  // get_cat_title();
                break;
            case is_page() || is_single():  // in_category($single):
                echo the_title();
                break;
            case is_search():
                echo 'Searching..';
                break;
            case is_tag():
                echo single_tag_title('',false).' Tags';
                break;
            default:
                echo "NOT MATCHED";
                break;
        }
    }
    // 获取当前分类、页面、文章slug
    function current_slug($upper=false){
        global $cat, $post;  //变量提升
        switch (true) {
            case is_home():
                $slug = "INDEX";
                break;
            case is_search():
                $slug = "SEARCH";
                break;
            case is_tag():
                $slug = "TAGS";
                break;
            case is_page():
                $upper ? $slug=strtoupper($post->post_name) : $slug=$post->post_name;
                break;
            case is_category():
                $upper ? $slug=strtoupper(get_category($cat)->slug) : $slug=get_category($cat)->slug;
                break;
            case is_single(): //in_category(array('news','notes')):
                $slug = "ARTICLE";
                break;
            default:
                $slug = "NOT MATCHED";
                break;
        };
        return $slug;
    }
    // 自动匹配首页、分类、文章、页面标题
    function custom_title(){
        global $cat, $post;
        $nick = get_option('site_nick');
        $surfix = $nick ? " | " . get_option('site_nick') : $nick;
        switch (true) {
            case is_home():
                echo bloginfo('name');
                echo $surfix . " - ";
                echo bloginfo('description');
                break;
            case is_category():
                echo single_cat_title() . $surfix;
                break;
            case is_search():
                echo 'Search for "' . esc_html(get_search_query()) . '" in '.trim(get_option('site_search_includes')) . $surfix;
            case is_tag():
                echo 'Tags for '. single_tag_title('',false) . $surfix;
                break;
            case is_page() || is_single()://in_category(array('news')):
                echo the_title() . $surfix;
                break;
            case in_category(array('notes','weblog')):
                echo the_title() . $surfix . " - " . get_the_category()[0]->name;
                break;
            default:
                echo "NO TITLE MATCHED" . $surfix;
                break;
        }
    }
    
    // 自动主题模式
    function theme_mode(){
        if(get_option('site_darkmode_switcher')) echo $_COOKIE['theme_mode'];
    }
    
    // 指定分类输出RSS feed  https://www.laobuluo.com/3863.html
    function rss_category($query) {
        $rss_array = explode(',',trim(get_option('site_rss_categories')));  // NO "," Array
        $new_array = array();
        for($i=0;$i<count($rss_array);$i++){
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
    
    // 初始化 wordpress 执行函数
    function custom_theme_setup(){
        $expire = time() + 1209600;  // 自定义 cookie 函数 darkmode cookie set
        if(!isset($_COOKIE['theme_manual'])){  //auto set manual 0 (reactive with javascript manually)
            setcookie('theme_manual', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        if(!$_COOKIE['theme_manual']){  //if theme_manual actived
            $hour = current_time('G');
            $start = get_option('site_darkmode_start');
            $end = get_option('site_darkmode_end');
            $hour>=$end&&$hour<$start || $hour==$end&&current_time('i')>=0&&current_time('s')>=0 ? setcookie('theme_mode', 'light', $expire, COOKIEPATH, COOKIE_DOMAIN, false) : setcookie('theme_mode', 'dark', $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        // ARTICLE FULL-VIEW SET
        if(!isset($_COOKIE['article_fullview'])){
            setcookie('article_fullview', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        // ARTICLE FONT-PLUS SET
        if(!isset($_COOKIE['article_fontsize'])){
            setcookie('article_fontsize', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
    };
    add_action('after_setup_theme', 'custom_theme_setup');
    
    //通过邮箱匹配（gravatar/qq）头像（默认获取后台gravatar镜像源）
    function match_mail_avatar($user_mail){
        preg_match_all('/@qq.com/i', $user_mail, $qq_matches);
        preg_match_all('/(.*?)@/i', $user_mail, $mail_account);
        $avatar_mirror = get_option('site_avatar_mirror','//gravatar.com/');
        if($qq_matches[0]) $avatar_src='https://q.qlogo.cn/headimg_dl?dst_uin='.$mail_account[1][0].'&spec=640';else $avatar_src='https:'.$avatar_mirror.'avatar/'.md5($user_mail).'?s=100';
        return $avatar_src;
    }
    //通过meta_query获取指定id自定义排序输出子级
    function meta_query_categories($cid,$order,$orderby){
        return array(
            'child_of' => $cid, 'parent' => $cid, 'hide_empty' => 0, 'order'=>$order , 'orderby' => 'order_clause',
            // 'orderby' => array(
            //     'order_clause' => $order,
            //     'modified' => 'DESC',
            // ), 
            'meta_query' => array(
                'order_clause' => array(
                    'key' => $orderby,
                    'type' => 'NUMERIC'
                )
            )
        );
    }
    function echo_postrank($max){
        $args = array('cat'=>$cat, 'posts_per_page'=>$max, 'caller_get_posts'=>1, 'orderby' => getPostViews(get_the_ID()), 'order' => 'DESC');
        if(!$post_show) $post_show=5;  //default $show 5
        if(count(query_posts($args))>=1) {
            query_posts($args);
            while(have_posts()) : the_post();
                echo '<li data-view="'.getPostViews(get_the_ID()).'" data-comment="'.get_comment_count($cat).'">';
                echo '<a href="'.the_permalink().'" target="_blank" title="'.the_title().'">';
                echo the_title();
                echo '</a>';
                echo '</li>';
            endwhile;
            wp_reset_query();  // 重置 wp 查询
        }
    }
    //友情链接函数
    function site_links($links,$iframe=false){
        // if(!$orderby) $orderby='id';  //默认id排序
    	//$links = get_bookmarks(array('orderby'=>'date','order'=>'DESC','category_name'=>$category,'hide_invisible'=>0));
        foreach ($links as $link){
            $target = $link->link_target;
            if(!$target) $target="_blank";
            $link->link_rating>=1 ? $sex="girl" : $sex="boy";
            $avatar = !$link->link_image ? 'https:' . get_option('site_avatar_mirror') . 'avatar/' . md5(mt_rand().'@rand.avatar') . '?s=300' : $avatar = $link->link_image;
            switch ($iframe) {
                case 'rich':
                    if($link->link_visible==="Y") echo '<div class="inbox flexboxes standard '.$sex.'"><div class="inbox-headside flexboxes"><a href="'.$link->link_url.'" target="'.$target.'" rel="'.$link->link_rel.'"><img class="lazy" data-original="" src="'.$avatar.'" alt="'.$link->link_name.'" draggable="false"><span class="ssl https">https</span></a></div><a href="'.$link->link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$link->link_rel.'"><span class="lowside-title"><h4>'.$link->link_name.'</h4></span><span class="lowside-description"><p>'.$link->link_description.'</p></span></a></div>';
                    break;
                case 'poor':
                    if($link->link_visible==="Y") echo '<div class="inbox flexboxes standard '.$sex.'"><a href="'.$link->link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$link->link_rel.'"><span class="lowside-title"><h4>'.$link->link_name.'</h4></span><span class="lowside-description"><p>'.$link->link_description.'</p></span></a></div>';
                    break;
                default:
                    echo '<a href="'.$link->link_url.'" title="'.$link->link_name.'" target="'.$target.'" rel="'.$link->link_rel.'">'.$link->link_name.'</a>';  //$link->link_visible=="Y"
                    break;
            }
        }
    }
    //面包屑导航（site_breadcrumb_switcher开启并传参true时启用）
    function breadcrumb_switch($switch,$frame){
        if(get_option('site_breadcrumb_switcher')&&$switch){
            if($frame){
                echo '<div class="news-cur-position wow fadeInUp"><ul>';
                    echo(the_breadcrumb());
                echo '</ul></div>';
            }else echo(the_breadcrumb());
        }
    };
    //谷歌 Adsense 广告（默认加载link传参true则加载sidebar广告块）
    function google_ads_switch($bar){  //$ink,
        // $disabled = '<h2 style="opacity:.5">Google 广告已关闭</h2>';
        if(get_option('site_ads_switcher')){
            // if($ink) echo(get_option('site_ads_link'));
            if($bar) echo(get_option('site_bar_ads'));else echo '<h2 style="opacity:.5">已手动关闭广告。</h2>';
            // if($ink&&!$bar) echo '<h2 style="opacity:.5">已停用 Google 广告</h2>';
        }else{
            echo '<h2 style="opacity:.75">未启用广告插件！</h2>';
        }
    };
    //分类 post metabox 信息
    function get_cat_title(){
        $cat_desc = strip_tags(trim(category_description()),"");
        $cat_meta = get_term_meta($cat, 'seo_title', true);
        if($cat_meta) echo($cat_meta);else echo($cat_desc);
    };
    //启用cdn加速(指定src/img)
    function custom_cdn_src($which=false,$var=false){
        $default_src = get_bloginfo('template_directory');
        $cdn_img = get_option('site_cdn_img');
        $cdn_src = get_option('site_cdn_src');
        if(get_option("site_cdn_switcher")){
            switch ($which) {
                case 'img':
                    $cdn_img ? $which=$cdn_img : $which=$default_src;
                    break;
                default:
                    $cdn_src ? $which=$cdn_src : $which=$default_src;
                    break;
            };
        }else{
            $which = $default_src;
        }
        if($var) return $which;else echo $which;
    };
    //兼容gallery获取post内容指定图片（视频海报）
    function get_postimg($index=0,$postid=false) {
        global $post, $posts;
        $postid ? $post = get_post($postid) : $post;
        preg_match_all('/\<img.*src=("[^"]*")/i', $post->post_content, $image);
        preg_match_all('/\<video.*poster=("[^"]*")/i', $post->post_content, $video);
        $video_poster = trim($video[1][0],'"');
        $ret = array();
        foreach($image[0] as $i => $v) {
            $ret[] = trim($image[1][$i],'"');
        };
        //未匹配到图片或调用值超出图片数量范围则输出（视频海报或）默认图
        if(count($ret)<=0 || count($ret)<=$index) {
            if($video_poster){
                $ret = [$video_poster];
            }else{
                if(has_post_thumbnail()) $ret = [get_the_post_thumbnail_url()];else $ret = [get_bloginfo('template_directory') . '/images/default.jpg']; //elseif($avatar) $ret = [get_option('site_avatar')];
            }
            $index = 0;
        }
        return has_post_thumbnail() ? get_the_post_thumbnail_url() : $ret[$index];
    };
    // 自定义文章摘要
    function custom_excerpt($excerpt_length) {
        global $post;
        $content = $post->post_content;
        $text = strip_shortcodes( $content );
        $text = str_replace(']]>', ']]>', $text);
        $text = strip_tags($text);
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if(count($words)>$excerpt_length){
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        }else{
            $text = implode(' ', $words);
        }
        echo $text . '...';
    }
    //计算版权时间，直接在footer使用会引发没有内容的notes子分类无法显示
    function calc_copyright(){
        $year = date('Y');
        $begain = get_option('site_begain');
        if($begain&&$begain<$year) echo $begain."-";
        echo $year;
    }
    // leancloud avos（标准li结构）查询
    function avos_posts_query($cid,$els){
        $slug = get_category($cid)->slug;
?>
        <script type="text/javascript">
            new AV.Query("<?php echo $slug; ?>").addDescending("createdAt").limit(<?php echo get_option('posts_per_page'); ?>).find().then(result=>{
                for (let i=0; i<result.length;i++) {
                    let res = result[i],
                        title = res.attributes.title,
                        content = res.attributes.content.replace(/</g,"&lt;").replace(/>/g,"&gt;");
                    document.querySelector("<?php echo $els ?>").innerHTML += `<li title="${content}"><a href="/<?php echo $slug ?>#${title}" target="_self" rel="nofollow">${title}</a></i>`;
                };
            })
        </script>
<?php
    }
    // wp自定义（含置顶无分页）查询函数
    function recent_posts_query($cid,$link=true){
        if($cid){
            $query_array = array('cat' => $cid, 'meta_key' => 'post_orderby', 'posts_per_page' => get_option('site_recent_num'),
                'orderby' => array(
                    'meta_value_num' => 'DESC',
                    'date' => 'DESC',
                    'modified' => 'DESC',
                )
            );
        }else{
            $query_array = array('cat' => $cid, 'posts_per_page' => get_option('site_recent_num'), 'order' => 'DESC', 'orderby' => 'data');
        }
        $left_query = new WP_Query(array_filter($query_array));
        while ($left_query->have_posts()):
            $left_query->the_post();
            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
            $topset = $post_orderby>1 ? 'topset' : false;
            $title = trim(get_the_title());
            $pre_link = $link ? '<a href="'.get_the_permalink().'" title="'.$title.'" target="_blank">' : '<a href="/'.get_category($cid)->slug.'" target="_self">';
            echo '<li class="'.$topset.'">'.$pre_link . $title . '</a></li>';
        endwhile;
        wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
    };
    
    // acg post query
    function acg_posts_query($the_cat, $pre_cat=false){
        global $post;
        $sub_cat = current_slug()!=$pre_cat ? 'subcat' : '';
        $cat_slug = $the_cat->slug;
        echo '<div class="inbox-clip wow fadeInUp '.$sub_cat.'"><h2 id="'.$cat_slug.'">'.$the_cat->name.'<sup> '.$cat_slug.' </sup></h2></div><div class="info flexboxes">';
        // start acg query
        $acg_query = new WP_Query(array_filter(array(
            'cat' => $the_cat->term_id,  //$acg_cat
            'meta_key' => 'post_orderby',
            'orderby' => array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC',
                'modified' => 'DESC'
            ),
            'posts_per_page' => get_option('site_techside_num', 5),
        )));
        while ($acg_query->have_posts()):
            $acg_query->the_post();
            $post_feeling = get_post_meta($post->ID, "post_feeling", true);
            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
            $post_source = get_post_meta($post->ID, "post_source", true);
?>
            <div class="inbox flexboxes">
                <div class="inbox-headside flexboxes">
                    <span class="author"><?php echo $post_feeling; ?></span>
                    <img class="bg" src="<?php echo get_postimg(); ?>">
                    <img src="<?php echo get_postimg(); ?>">
                </div>
                <div class="inbox-aside">
                    <span class="lowside-title">
                        <h4><a href="<?php echo $post_source; ?>" target="_blank"><?php the_title(); ?></a></h4>
                    </span>
                    <span class="lowside-description">
                        <p><?php the_content(); ?></p>
                    </span>
                </div>
            </div>
<?php
        endwhile;
        wp_reset_query();  // reset wp query incase following code occured query err
        echo '<div class="inbox more flexboxes"><div class="inbox-more flexboxes"><a href="mailto:'.get_bloginfo("admin_email").'" title="发送邮件，荐你所见"></a></div></div></div>';
    };
    
    // wp自定义（含置顶无分页）查询函数
    function download_posts_query($cats, $order, $single=false){
        for($i=0;$i<count($cats);$i++){
            $term_order = get_term_meta($cats[$i]->term_id, 'seo_order', true);
            // print_r($term_order);
            if($term_order==$order){
                $each_cat = $cats[$i];
                $cat_name = $each_cat->name;
                $cat_slug = $each_cat->slug;
                $cat_id = $each_cat->term_id;
                $meta_image = get_term_meta($cat_id, 'seo_image', true );
                if(!$meta_image) $meta_image = get_option('site_bgimg');
?>
				<div class="dld_box <?php echo $cat_slug.' '.$single ?>">
					<div class="dld_box_wrap">
						<div class="box_up preCover">
							<span style="background:url(<?php echo $meta_image; ?>) center center /cover">
								<a href="javascript:;"><h3> <?php echo $cat_name; ?> </h3><i> <?php echo strtoupper($cat_slug) ?></i><em></em></a>
						  	</span>
						</div>
						<div class="box_down">
						    <ul>
						        <?php 
                                    $left_query = new WP_Query(array_filter(array(
                                        'cat' => $cat_id,
                                        'meta_key' => 'post_orderby',
                                        'orderby' => array(
                                            'meta_value_num' => 'DESC',
                                            'date' => 'DESC',
                                            'modified' => 'DESC'
                                        ),
                                        // 'posts_per_page' => get_option('posts_per_page'),  //use left_query counts
                                    )));
                                    while ($left_query->have_posts()):
                                        $left_query->the_post();
                            ?>
                                        <li class="<?php if(get_post_meta($post->ID, "post_orderby", true)>1) echo 'topset'; ?>">
                                            <div class="details">
                                                <span style="background:url(<?php if(has_post_thumbnail()) the_post_thumbnail_url();else echo get_option('site_bgimg'); ?>) center center no-repeat"></span>
                                                <div><?php the_title() ?><i>
                                                    <a href="<?php echo get_post_meta($post->ID, "post_feeling", true); ?>" target="_blank">下载</a>
                                                    <a href="<?php echo get_post_meta($post->ID, "post_source", true); ?>" target="_blank">查看</a>
                                                    </i>
                                                </div>
                                            </div>
                                        </li>
                            <?php
                                    endwhile;
                                    wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
						        ?>
						    </ul>
						</div>
					</div>
				</div>
<?php
            }
        }
    };
    
    // search/tag page posts with styles
    function the_posts_with_styles($queryString){
        $post_styles = get_option('site_search_style_switcher');
        if($post_styles){
    ?>
        	<link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/news.css?v=2" />
            <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/weblog.css" />
            <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/acg.css" />
    <?php
        }
    ?>
    	<style>
    	    .win-content.main,
    	    .news-inside-content .news-core_area p,
    	    .empty_card{margin:0 auto;}
    	    .news-inside-content .news-core_area p{padding:0}
        	.win-content{width:100%;padding:0;display:initial}
            .win-top h5:before{content:none}
            .win-top h5{font-size:3rem;color:var(--preset-e)}
            .win-top h5 span:before{content:'';display:inherit;width:88%;height:36%;background-color:var(--theme-color);position:absolute;left:15px;bottom:1px;z-index:-1}
            .win-top h5 span{position:relative;background:inherit;color:white;font-weight:bolder}
            .win-top h5 b{font-family:var(--font-ms);font-weight:bolder;color:var(--preset-f);/*padding:0 10px;vertical-align:text-top;*/}
            .win-content article{max-width:88%;margin-top:auto}
            .win-content article.news-window{padding:0;border:1px solid rgb(100 100 100 / 10%);margin-bottom:25px}
            .win-content article .info span{margin-left:10px}
            .win-content article .info span#slider{margin:auto}
    	    .news-window-img{max-width:16%}
    	    /*.news-window-img img{padding:10px}*/
    	    .rcmd-boxes{width:21%;display:inline-block}
    	    .rcmd-boxes .info .inbox{max-width:none}
    	    /*.win-top h5:first-letter{
    	        font-size: 8rem;
                font-weight: bold;
                margin: var(--pixel-pd);
                margin-bottom: auto;
                float: left;
                opacity: var(--opacity-hi);
    	    }*/
    	    .main h2{font-weight: 600};
            #core-info p{padding:0}
            @media screen and (max-width:760px){
                .win-content article{
                    width: 100%;
                }
                .rcmd-boxes{width:49%!important}
            }
    	</style>
    <?php
        if(have_posts()) {
            while (have_posts()): the_post();
                if(!$post_styles){
    ?>
                    <article class="cat-<?php the_ID(); ?>">
                        <h1>
                            <a href="<?php the_permalink() ?>" target="_blank"><?php the_title() ?></a>
                            <?php $postmeta=get_post_meta($post->ID, "post_rights", true); echo $postmeta&&$postmeta!="请选择" ? '<sup>'.$postmeta.'</sup>' : false; ?>
                        </h1>
                        <p><?php the_excerpt() ?></p>
                        <div class="info">
                            <span class="classify" id="<?php $cpar = get_the_category()[1]->parent==0 ? get_the_category()[1] : get_the_category()[0];echo $cpar->slug; ?>">
                                <i class="icom"></i><?php echo $cpar->name; ?>
                            </span>
                            <span class="valine-comment-count" data-xid="<?php the_permalink() ?>"><?php echo $post->comment_count; ?></span>
                            <span class="date"><?php the_time('d-m-Y'); ?></span>
                            <span id="slider"></span>
                        </div>
                    </article>
        <?php
                }else{
                    if(in_category(get_template_bind_cat('category-news.php')->slug)){
        ?>
                    	<article class="news-window wow" data-wow-delay="0.1s">
                            <div class="news-window-inside">
                                <span class="news-window-img">
                                    <a href="<?php the_permalink() ?>" target="_blank">
                                        <img class="lazy" src="<?php echo get_postimg(); ?>" />
                                    </a>
                                </span>
                                <div class="news-inside-content">
                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title() ?>"><?php the_title() ?></a>
                                    </h2>
                                    <span class="news-core_area entry-content"><?php the_excerpt(); ?></span>
                                    <?php
                                        $postmeta = get_post_meta($post->ID, "post_feeling", true);
                                        if($postmeta) echo '<span class="news-personal_stand" unselectable="on"><dd>'.$postmeta.'</dd></span>';
                                    ?>
                                    <div id="news-tail_info">
                                        <ul class="post-info">
                                            <li class="tags author"><?php $tag = get_the_tag_list();if($tag) echo($tag);else echo '<a href="javascript:;" target="_blank" rel="nofollow">'.get_option('site_nick').'</a>'; ?></li>
                                            <li title="评论人数"><?php if(!get_option('site_comment_switcher')) $count=$post->comment_count;else $count=0; echo '<span class="valine-comment-count" data-xid="'.get_the_permalink().'">'.$count.'</span>'; ?></li>
                                            <li id="post-date" class="updated" title="发布日期">
                                                <i class="icom"></i><?php the_time('d-m-Y'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </article>
        <?php
                    }elseif(in_category(get_template_bind_cat('category-weblog.php')->slug)){
        ?>
                        <article class="weblog-tree-core-record i<?php the_ID() ?>">
                            <div class="weblog-tree-core-l">
                                <span id="weblog-timeline"><?php the_time('d-m-Y'); ?></span>
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
                                            <p class="excerpt"><?php custom_excerpt(100) ?></p>
                                        </span>
                                        <span id="other-info">
                                            <h4> Ps. </h4>
                                            <p class="feeling"><?php echo get_post_meta($post->ID, "post_feeling", true); ?></p>
                                            <p id="sub"><?php the_time('Y-n-j'); ?></p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
        <?php  
                    }elseif(in_category(get_template_bind_cat('category-acg.php')->slug)){
        ?>
                        <div class="rcmd-boxes flexboxes">
                            <div class="info anime flexboxes">
                                <div class="inbox flexboxes">
                                    <div class="inbox-headside flexboxes">
                                        <span class="author"><?php echo get_post_meta($post->ID, "post_feeling", true); ?></span>
                                        <img class="bg" src="<?php echo get_postimg(); ?>">
                                        <img src="<?php echo get_postimg(); ?>">
                                    </div>
                                    <div class="inbox-aside">
                                        <span class="lowside-title">
                                            <h4><a href="<?php echo get_post_meta($post->ID, "post_source", true); ?>" target="_blank"><?php the_title(); ?></a></h4>
                                        </span>
                                        <span class="lowside-description">
                                            <p><?php the_content(); ?></p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
        <?php
                    }
                }
            endwhile;
                $pages = paginate_links(array(
                    'prev_text' => __('上一页'),
                    'next_text' => __('下一页'),
                    'type' => 'plaintext',
                    'screen_reader_text' => null,
                    'total' => $wp_query -> max_num_pages,  //总页数
                    'current' => max(1, get_query_var('paged')), //当前页数
                ));
                if($pages) echo '<div class="pageSwitcher" style="width:100%;display:inline-block;user-select: none;">'.$pages.'</div>';
        }else{
            echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.$queryString.' </h1></div>';  //<b>'.current_slug(true).'</b> 
        }
    }
    
    /* ------------------------------------------------------------------------ *
     * 其他功能函数
     * ------------------------------------------------------------------------ */
    function get_previous_comments_html( $label = '' ) {
        if ( ! is_singular() ) {
            return;
        }
        $page = get_query_var( 'cpage' );
        if ( (int) $page <= 1 ) {
            return;
        }
        $prevpage = (int) $page - 1;
        if ( empty( $label ) ) {
            $label = __( '&laquo; Older Comments' );
        }
        return '<a href="' . esc_url( get_comments_pagenum_link( $prevpage ) ) . '" ' . apply_filters( 'previous_comments_link_attributes', '' ) . '><i class="icom"></i>' . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
    }
    function get_next_comments_html( $label = '', $max_page = 0 ) {
        global $wp_query;
        if ( ! is_singular() ) {
            return;
        }
        $page = get_query_var( 'cpage' );
        if ( ! $page ) {
            $page = 1;
        }
        $nextpage = (int) $page + 1;
        if ( empty( $max_page ) ) {
            $max_page = $wp_query->max_num_comment_pages;
        }
        if ( empty( $max_page ) ) {
            $max_page = get_comment_pages_count();
        }
        if ( $nextpage > $max_page ) {
            return;
        }
        if ( empty( $label ) ) {
            $label = __( 'Newer Comments &raquo;' );
        }
        return '<a href="' . esc_url( get_comments_pagenum_link( $nextpage, $max_page ) ) . '" ' . apply_filters( 'next_comments_link_attributes', '' ) . '>' . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '<i class="icom left"></i></a>';
    }
    // https://journalxtra.com/php/browser-os-detection-php/
    // 浏览器user-agent信息（浏览器/版本号、系统/版本号）
    // https://gist.github.com/Balamir/4a19b3b0a4074ff113a08a92908302e2
    function get_userAgent_info($user_agent) {
    	$os_array =   array(
    		'/windows nt 10/i'      =>  'Windows 10',
    		'/windows nt 6.3/i'     =>  'Windows 8.1',
    		'/windows nt 6.2/i'     =>  'Windows 8',
    		'/windows nt 6.1/i'     =>  'Windows 7',
    		'/windows nt 6.0/i'     =>  'Windows Vista',
    		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
    		'/windows nt 5.1/i'     =>  'Windows XP',
    		'/windows xp/i'         =>  'Windows XP',
    		'/windows nt 5.0/i'     =>  'Windows 2000',
    		'/windows me/i'         =>  'Windows ME',
    		'/win98/i'              =>  'Windows 98',
    		'/win95/i'              =>  'Windows 95',
    		'/win16/i'              =>  'Windows 3.11',
    		'/macintosh|mac os x/i' =>  'Mac OS X',
    		'/mac_powerpc/i'        =>  'Mac OS 9',
    		'/linux/i'              =>  'Linux',
    		'/ubuntu/i'             =>  'Ubuntu',
    		'/iphone/i'             =>  'iPhone',
    		'/ipod/i'               =>  'iPod',
    		'/ipad/i'               =>  'iPad',
    		'/android/i'            =>  'Android',
    		'/blackberry/i'         =>  'BlackBerry',
    		'/webos/i'              =>  'Mobile'
    	);
    	$browser_array  = array(
    		'/msie/i'       =>  'Internet Explorer',
    		'/firefox/i'    =>  'Firefox',
    		'/safari/i'     =>  'Safari',
    		'/chrome/i'     =>  'Chrome',
    		'/edge/i'       =>  'Edge',
    		'/opera/i'      =>  'Opera',
    		'/netscape/i'   =>  'Netscape',
    		'/maxthon/i'    =>  'Maxthon',
    		'/konqueror/i'  =>  'Konqueror',
    		'/mobile/i'     =>  'Handheld Browser'
    	);
    	$os_platform = "Unknown";
    	$browser = "Unknown";
    	foreach($os_array as $regex => $value){ 
    		if(preg_match($regex, $user_agent)) $os_platform = $value;
    	}
    	foreach($browser_array as $regex => $value ) {
    		if(preg_match( $regex, $user_agent)) $browser = $value;
    	}
        return ['browser'=>$browser,'system'=>$os_platform];
    }
    /**
     * Kullanicinin kullandigi internet tarayici bilgisini alir.
     * 
     * @since 2.0
     */
    // 文章点赞
    add_action('wp_ajax_nopriv_post_like', 'post_like');
    add_action('wp_ajax_post_like', 'post_like');
    function post_like(){
        global $wpdb,$post;
        $id = $_POST["um_id"];
        $action = $_POST["um_action"];
        if($action=='like'){
            $post_liked = get_post_meta($id,'post_liked',true);
            $expire = time() + 99999999;
            $domain = ($_SERVER['HTTP_HOST']!='localhost') ? $_SERVER['HTTP_HOST'] : false;
            setcookie('post_liked_'.$id,$id,$expire,'/',$domain,false);
            if (!$post_liked || !is_numeric($post_liked)) update_post_meta($id, 'post_liked', 1);else update_post_meta($id, 'post_liked', ($post_liked + 1));
            echo get_post_meta($id,'post_liked',true);
        }
        die;
    };
    // 文章浏览量
    function getPostViews($postID){
        $count_key = 'post_views';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return "0";
        }
        return $count.'';
    };
    function setPostViews($postID) {
        $count_key = 'post_views';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    };
    // 面包屑导航 https://www.thatweblook.co.uk/tutorial-wordpress-breadcrumb-function/
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
                global $post;
                $page_for_posts_id = get_option('page_for_posts');
                if ( $page_for_posts_id ) { 
                    $post = get_page($page_for_posts_id);
                    setup_postdata($post);
                    the_title();
                    rewind_posts();
                }
            }
            echo '</div>';
        }
    };
?>
