<?php 
    function custom_title_shortcode($atts, $content = null) {
        $statu = isset($atts['statu']) ? $atts['statu'] : 'normal';
        $title = isset($atts['title']) ? $atts['title'] : 'Example';
        $tag = isset($atts['tag']) ? $atts['tag'] : 'h3';
        return "<span id='normal' class='$statu'><$tag>$title</$tag></span>";
    }
    function custom_imgbox_shortcode($atts, $content = null) {
        $img = isset($atts['img']) ? $atts['img'] : '';
        $title = isset($atts['title']) ? $atts['title'] : 'No Text';
        return '<div class="ibox"><div class="iboxes"><img decoding="async" alt="qr_code" src="'.$img.'" alt="'.$title.'"><mark>'.$title.'</mark></div></div>';
    }
    function custom_netease_shortcode($atts){
        $id = isset($atts['id']) ? $atts['id'] : 'id';
        $width = isset($atts['width']) ? $atts['width'] : '';
        $height = isset($atts['height']) ? $atts['height'] : '350';
        $class = isset($atts['class']) ? $atts['class'] : 'netease_embed';
        return '<iframe class="'.$class.'" src="//music.163.com/outchain/player?id='.$id.'&&type=0&auto=0" width="'.$width.'" height="'.$height.'" frameborder="no" marginwidth="0" marginheight="0" title="163"></iframe>';
    }
    function custom_bilibili_shortcode($atts){
        $vid = isset($atts['vid']) ? $atts['vid'] : 'vid';
        $class = isset($atts['class']) ? $atts['class'] : 'bilibili_embed';
        return '<iframe class="'.$class.'" src="//player.bilibili.com/player.html?bvid='.$vid.'" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>';
    }
    function custom_sidebar_ad_shortcode($atts){
        $sup = isset($atts['sup']) ? $atts['sup'] : '中意此款主题吗';
        $title = isset($atts['title']) ? $atts['title'] : '';
        $sub = isset($atts['sub']) ? $atts['sub'] : '现在体验<b> BETA </b>版';
        $src= isset($atts['src']) ? $atts['src'] : 'https://github.com/2Broear/2BLOG';
        $img = isset($atts['img']) ? $atts['img'] : 'https://img.2broear.com/2022/08/2BLOG-rainbow666.jpg';
        return '<div class="countdown-box" style="margin-bottom: 15px"><a href="'.$src.'" target="_blank" title="'.$title.'"><div id="countdown" class="countdowns" style="background-image:url('.$img.')"><p class="title">'.$sup.'</p><div class="time"><span class="timesup">'.$title.'</span></div><p class="today" style="text-decoration: underline;">'.$sub.'</p></div><sup id="ads">ads</sup></a></div>';
    }
    // 注册短代码
    add_shortcode('custom_title', 'custom_title_shortcode');
    add_shortcode('custom_imgbox', 'custom_imgbox_shortcode');
    add_shortcode('netease_embed', 'custom_netease_shortcode');
    add_shortcode('bilibili_embed', 'custom_bilibili_shortcode');
    add_shortcode('sidebar_ads', 'custom_sidebar_ad_shortcode');
    
    // 注册区块
    // function custom_bilibili_block_init() {
    //     register_block_type( 'bilibili-block', array(
    //         'attributes' => array(
    //             'code' => array(
    //                 'type' => 'string',
    //                 'default' => '',
    //             ),
    //         ),
    //         'editor_script' => get_stylesheet_directory_uri() . '/plugin/custom_blocks.js',
    //         'render_callback' => 'custom_bilibili_shortcode',
    //     ) );
    // }
    // add_action( 'init', 'custom_bilibili_block_init' );
    function enqueue_bilibili_block_script() {
      wp_enqueue_script(
        'bilibili-block-script',
        get_theme_file_uri('/plugin/custom_blocks.js'), // 替换为实际脚本文件的路径
        array('wp-blocks', 'wp-editor', 'wp-element'),
        filemtime(get_theme_file_path('/plugin/custom_blocks.js')) // 替换为实际脚本文件的路径
      );
    }
    add_action('enqueue_block_editor_assets', 'enqueue_bilibili_block_script');
    /**
     * is_edit_page 
     * function to check if the current page is a post edit page
     * 
     * @author Ohad Raz <admin@bainternet.info>
     * 
     * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
     * @return boolean
     */
    function is_edit_page($new_edit = null){
        global $pagenow;
        //make sure we are on the backend
        if (!is_admin()) return false;
        if($new_edit == "edit")
            return in_array( $pagenow, array( 'post.php',  ) );
        elseif($new_edit == "new") //check for new post page
            return in_array( $pagenow, array( 'post-new.php' ) );
        else //check for either new or edit
            return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
    }
    
    //禁用远程管理文件 xmlrpc.php 防爆破
    if(get_option('site_xmlrpc_switcher')){
        add_filter('xmlrpc_enabled', '__return_false');
    }
    //禁用 Gutenberg 编辑器
    // add_filter('use_block_editor_for_post', '__return_false');
    // remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
    /*
     * This function modifies the main WordPress query to include an array of 
     * post types instead of the default 'post' post type.
     *
     * @param object $query The main WordPress query.
    */
    // 重写 WP 固定链接(初始化)
    if(!get_option('permalink_structure')){
        add_action( 'init', 'custom_permalink_rules' );
        function custom_permalink_rules() {
            global $wp_rewrite;
            // $wp_rewrite->permalink_structure = $wp_rewrite->root . '/%category%/%day%-%monthnum%-%year%_%postname%.html';
            $wp_rewrite->set_permalink_structure($wp_rewrite->root . '/%category%/%day%-%monthnum%-%year%_%postname%');
            $wp_rewrite->flush_rules();  // incase: 404 err occured
        }
    }
    //限制上传文件的最大体积值 https://www.cnwper.com/wp-limit-uploads.html
    // function max_up_size() {
    //     return 500*1024*1024; //限制500kb
    // }
    // add_filter('upload_size_limit', 'max_up_size');
    // Rename image filename after uploaded.
    // https://wordpress.stackexchange.com/questions/59168/rename-files-on-upload
    // function wpa59168_rename_attachment( $post_ID ) {
    //     $post = get_post( $post_ID );
    //     $file = get_attached_file( $post_ID );
    //     $path = pathinfo( $file );
    //     $count = get_option( 'wpa59168_counter', 1 );
    //     // change to $new_name = $count; if you want just the count as filename
    //     $new_name = $path['filename'] . '_' . $count;
    //     $new_file = $path['dirname'] . '/' . $new_name . '.' . $path['extension'];
    //     rename( $file, $new_file );    
    //     update_attached_file( $post_ID, $new_file );
    //     update_option( 'wpa59168_counter', $count + 1 );
    // }
    // add_action( 'add_attachment', 'wpa59168_rename_attachment' );
    
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
    
    // 关闭 wordpress 自动压缩上传图片
    // add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
    // add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
     
    // function remove_width_attribute( $html ) {
    //   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
    //   return $html;
    // }
    // include_once(TEMPLATEPATH . '/plugin/nocategory.php');  
    
    // 自定义获取主题信息
    function get_theme_info($type='Name'){
        $my_theme = wp_get_theme();
        // [Name] => 2BLOG
        // [ThemeURI] => https://github.com/2Broear/2BLOG
        // [Description]
        // [Author] => 2BROEAR
        // [AuthorURI] => https://blog.2broear.com
        // [Version] => 1.3.3.4
        // [Template] => 
        // [Status] => 
        // [Tags] => article-topset
        // [TextDomain] => 
        // [DomainPath] => 
        // [RequiresWP] => 3.0
        // [RequiresPHP] => 5.3
        return $my_theme->get($type);
    }
    //替换所有后台镜像源
    function replace_gravatar($avatar) {
    	$avatar = str_replace(array("//gravatar.com/avatar/", "//secure.gravatar.com/avatar/", "//www.gravatar.com/avatar/", "//0.gravatar.com/avatar/", 
    	"//1.gravatar.com/avatar/", "//2.gravatar.com/avatar/", "//cn.gravatar.com/avatar/"), get_option('site_avatar_mirror')."avatar/", $avatar);
    	return $avatar;
    }
    add_filter( 'get_avatar', 'replace_gravatar' );
    
    
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
    
    // 评论添加@（提交时写入数据库）https://www.ludou.org/wordpress-comment-reply-add-at.html
    // function ludou_comment_add_at( $commentdata ) {
    //   if( $commentdata['comment_parent'] > 0) {
    //     $commentdata['comment_content'] = '@<a href="#comment-' . $commentdata['comment_parent'] . '">'.get_comment_author( $commentdata['comment_parent'] ) . '</a> , ' . $commentdata['comment_content'];
    //   }
    //   return $commentdata;
    // }
    // add_action( 'preprocess_comment' , 'ludou_comment_add_at', 20);
    
    // 评论前置@（调用时插入文本）
    function wp_comment_at($comment_text, $comment=''){
        $parent = $comment->comment_parent;
        if($parent>0) $comment_text = '<a href="#comment-' . $parent . '">@'. get_comment_author($parent) . '</a> , ' . $comment_text;
        return $comment_text;
    }
    add_filter('comment_text' , 'wp_comment_at', 20, 2);
    
    /* 评论 COOKIE 初始化 */
    function coffin_set_cookies( $comment, $user, $cookies_consent){
    	$cookies_consent = true;
    	wp_set_comment_cookies($comment, $user, $cookies_consent);
    }
    add_action('set_comment_cookies','coffin_set_cookies',10,3);
    
    /*** 邮件 SMTP 初始化 ***/
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
    
    
    /* ------------------------------------------------------------------------ *
     * 自定义后台面板选项 https://themes.artbees.net/blog/custom-setting-page-in-wordpress/
     * ------------------------------------------------------------------------ 
    */
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
     * WP Comment eamil/wechat notify etc
     * ------------------------------------------------------------------------  */
    
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
    
    
    /* ------------------------------------------------------------------------ *
     * WordPress AJAX Comments Setup etc (comment reply/paginate)
     * ------------------------------------------------------------------------  */
     
    //***  AJAX 回复评论  ***//
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
    
    //***  AJAX 加载评论  ***//
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
    
    
    /* ------------------------------------------------------------------------ *
     *  
     *  其他自定义功能函数
     *  
     * ------------------------------------------------------------------------ */
     
    //通过meta_query获取指定id自定义排序输出子级
    function meta_query_categories($cid=0, $order='ASC', $orderby='seo_order'){
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
    
    //获取同级分类（gpt）
    function get_sibling_categories($childs=false, $exclude=false){
        global $cat;
        $cat = $cat ? $cat : get_page_cat_id(current_slug());
        $current_category = get_category($cat); //get_queried_object();
        $current_cid = $current_category->term_id;
        $parent_cid = $current_category->parent;
        $current_categories =  get_categories(['parent' => $current_cid]);
        // if($parent_cid==0) return false;
        $query_cid =  $parent_cid==0||count($current_categories)>0 ? $current_cid : $parent_cid;
        $query_array = meta_query_categories($query_cid);
        if($childs) unset($query_array['parent']);
        if($exclude) $query_array['exclude'] = $current_cid;
        return get_categories($query_array);
    }
    
    $src_cdn = custom_cdn_src('src', true);
    $img_cdn = custom_cdn_src('img', true);
    $lazysrc = 'src';
    $loadimg = $img_cdn.'/images/loading_3_color_tp.png';
    // $upload_url = wp_get_upload_dir()['baseurl'];
    // $video_cdn_sw = get_option('site_cdn_vid_sw');
    $upload_url = content_url().'/uploads';
    $cdn_switch = get_option('site_cdn_switcher');
    $images_cdn = get_option('site_cdn_img');
    $videos_cdn_page = get_option('site_cdn_vdo_includes');
    $videos_cdn_arr = explode(',',trim($videos_cdn_page));
    
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
        if(isset($formart->error)) return $formart->error->message;
        $choices = $formart->choices[0];
        return isset($choices->message) ? $choices->message->content : $choices->text;
    }
    // API调用接口，接受三个参数：
    // 调用 api 文件名
    // api 代理访问（使用 api.php 文件中的 curl 携带鉴权参数二次请求（速度影响），适用前端异步调用
    // 返回请求api或返回sign签名（如开启cdn鉴权
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
    
    // 挂载文章 chatGPT AI 摘要 mount article chatgpt
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
    function article_ai_abstract($content) {
        global $src_cdn; //custom_cdn_src(0, true)
        $chatgpt_cat = in_chatgpt_cat();
        return $chatgpt_cat&&is_single() ? '<blockquote class="chatGPT" status="'.$chatgpt_cat.'"><p><b> 文章摘要 AI</b><span>chatGPT</span></p><p class="response load">standby chatGPT responsing..</p></blockquote><script type="module">const responser = document.querySelector(".chatGPT .response");try {import("'.$src_cdn.'/js/module.js").then((module)=>send_ajax_request("get", "'.get_api_refrence("gpt").'", false, (res)=>module.words_typer(responser, res, 25)));}catch(e){console.warn("dom responser not found, check backend.",e)}</script>'.$content : $content; //get_api_refrence("gpt", true)
    }
    add_filter( 'the_content', 'article_ai_abstract', 10);
    
    
    // 分类导航（PC/MOBILE）
    function category_navigation($mobile=false, $deepth=0){
        $deepth = $deepth ? $deepth : get_option('site_catnav_deepth', 9);  //default output 9-level nav-cats if catnav_lv unset
        global $cat;
        $use_icon = get_option('site_icon_switcher');
        $site_icon = $use_icon ? '<i class="icom icon-more"></i>' : '';
        $choosen = is_home() ? 'choosen' : '';
        echo '<li class="cat_0 top_level"><a href="/" class="'.$choosen.'">'.$site_icon.'首页</a></li>';
        $cat = $cat ? $cat : get_page_cat_id(current_slug());  // if is_page() then rewrite cat to cid // echo $cat;
        // print_r(get_category($cat));
        $cats = get_categories(meta_query_categories(0));
        if(!empty($cats)){
            global $img_cdn;
            $slash_href = 'javascript:void(0)';
            foreach($cats as $the_cat){
                $the_cat_id = $the_cat->term_id;
                $the_cat_slug = $the_cat->slug;  //use slug compare current category
                $the_cat_par = get_category($the_cat->category_parent);
                $catss = get_categories(meta_query_categories($the_cat_id));
                $slug_icon = $the_cat_slug!="/" ? $the_cat_slug : "more";
                $level = !empty($catss) ? "sec_level" : "top_level";
                $choosen = $the_cat_id==$cat&&!is_single() || cat_is_ancestor_of($the_cat_id, $cat) || in_category($the_cat_id)&&is_single() ? "choosen" : "";  // 当前选中栏目 || 当前选中栏目下子栏目 || 当前栏目下文章&&文章单页
                $cur_link = get_category_link($the_cat_id);
                $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? $slash_href : $cur_link;  // detect if use $slash_link
                // $slash_name = $slash_link===$slash_href
                $site_icon = $use_icon ? '<i class="icom icon-'.$slug_icon.'"></i>' : '';
                if($the_cat_slug!='uncategorized') echo '<li class="cat_'.$the_cat_id.' '.$level.'"><a href="'.$slash_link.'" class="'.$choosen.'" rel="nofollow">' . $site_icon . $the_cat->name.'</a>';  //liwrapper
                if(!empty($catss) && $deepth>=2){
                    $metanav_array = explode(',', get_option('site_metanav_array'));
                    if(get_option('site_metanav_switcher') && in_array($the_cat_slug, $metanav_array)){ //strpos(get_option('site_metanav_array'),$the_cat_slug)!==false
                        $metaimg_array = explode(',', get_option('site_metanav_image'));
                        $metaCls = in_array($the_cat_slug, $metaimg_array) ? "metaboxes" : "";  // must else for each-loop //strpos(get_option('site_metanav_image'), $the_cat_slug)!==false
                        //METABOX RICH INFO
                        echo $mobile ? '<ul class="links-mores '.$metaCls.'">' : '<div class="additional metabox '.$metaCls.'"><ol class="links-more">';
                        foreach($catss as $the_cats){
                            $the_cats_id = $the_cats->term_id;
                            $the_cats_par = $the_cats->category_parent;
                            $catsss = get_categories(meta_query_categories($the_cats_id));
                            $the_cats_name = !$mobile ? '<b>'.$the_cats->name.'</b>' : $the_cats->name;
                            $level = "sec_child";  // check level before sub-additionaln
                            if(!empty($catsss)){
                                $level = "trd_level";
                                $the_cats_name = '<b>'.$the_cats->name.'</b>';
                            }
                            $choosen = $the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single() ? "choosen 3rd" : "2nd";  // current choosen detect
                            if($metaCls&&!$mobile){
                                $meta_image = get_term_meta($the_cats_id, 'seo_image', true);
                                if($meta_image){
                                    if(get_option('site_cdn_switcher')){
                                        $upload_url = wp_get_upload_dir()['baseurl'];
                                        $meta_image = str_replace($upload_url, get_option('site_cdn_img',$upload_url), $meta_image);
                                    }
                                }else{
                                    $meta_image = $img_cdn.'/images/default.jpg';
                                }
                                echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'" style="background:url('.$meta_image.') center center /cover;">'.$the_cats_name.'</a>'; // style="--data-background:'.$meta_image.'" data-background="'.$meta_image.'" <style>.inside_of_block nav.main-nav .metaboxes li:hover > a{background-image: var(--data-background);}</style>
                            }else{
                                $cats_desc = $mobile ? '' : ($the_cats->description ? '<p>'.$the_cats->description.'</p>' : "<p>Category Description</p>");
                                echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats_par." ".$level.'"><a href="'.get_category_link($the_cats_id).'" class="'.$choosen.'">'.$the_cats_name.$cats_desc.'</a>';
                            }
                            if(!empty($catsss) && $deepth>=3){
                                echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional metabox"><ol class="links-more">';
                                foreach($catsss as $the_catss){
                                    $the_catss_id = $the_catss->term_id;
                                    $the_catss_name = $mobile ? $the_catss->name : '<b>'.$the_catss->name.'</b>';
                                    $catssss = get_categories(meta_query_categories($the_catss_id));
                                    $level = !empty($catssss) ? "th_level" : "trd_child";  // check level before sub-additionaln
                                    $choosen = $the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single() ? "choosen 3rd" : "3rd";  // current choosen detect
                                    echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'">'.$the_catss_name.'</a>';  //$catss_desc
                                };
                                echo $mobile ? "</ul>" : "</ol></div>";
                            }
                        }
                        echo $mobile ? "</ul>" : "</ol></div>";
                    }else{  //elseif($the_cat_slug!=$metaArray[$i]){
                        echo $mobile ? '<ul class="links-mores">' : '<div class="additional"><ol class="links-more">';
                        foreach($catss as $the_cats){
                            $the_cats_id = $the_cats->term_id;
                            $catsss = get_categories(meta_query_categories($the_cats_id));
                            $the_cats_name = $the_cats->name;
                            $level = "sec_child";  // check level before sub-additionaln
                            if(!empty($catsss)){
                                $level = "trd_level";
                                $the_cats_name = '<b>'.$the_cats_name.'</b>';
                            }
                            $choosen = $the_cats_id==$cat || cat_is_ancestor_of($the_cats_id, $cat) || in_category($the_cats_id)&&is_single() ? "choosen 2nd" : "2nd";  // current choosen detect
                            $cur_link = get_category_link($the_cats_id);
                            $slash_link = $cur_link==get_site_url()||$cur_link==get_site_url().'/category/'||$cur_link==get_site_url().'/category' ? $slash_href : $cur_link;  // detect if use $slash_link
                            echo '<li class="cat_'.$the_cats_id.' par_'.$the_cats->category_parent." ".$level.'"><a href="'.$slash_link.'" class="'.$choosen.'" rel="nofollow">'.$the_cats_name.'</a>';  //liwrapper
                            if(!empty($catsss) && $deepth>=3){
                                echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional"><ol class="links-more">';
                                foreach($catsss as $the_catss){
                                    $the_catss_id = $the_catss->term_id;
                                    $catssss = get_categories(meta_query_categories($the_catss_id));
                                    $the_catss_name = $the_catss->name;
                                    $level = "trd_child";  // check level before sub-additionaln
                                    if(!empty($catssss)){
                                        $level = "th_level";
                                        $the_catss_name = '<b>'.$the_catss_name.'</b>';
                                    }
                                    $choosen = $the_catss_id==$cat || cat_is_ancestor_of($the_catss_id, $cat) || in_category($the_catss_id)&&is_single() ? "choosen 3rd" : "3rd";  // current choosen detect
                                    echo '<li class="cat_'.$the_catss_id.' par_'.$the_catss->category_parent." ".$level.'"><a href="'.get_category_link($the_catss_id).'" class="'.$choosen.'">'.$the_catss_name.'</a>';  //liwrapper
                                    if(!empty($catssss) && $deepth>=4){
                                        echo $mobile ? '<ul class="links-moress">' : '<div class="sub-additional"><ol class="links-more">';
                                        foreach($catssss as $the_catsss){
                                            $the_catsss_id = $the_catsss->term_id;
                                            if($the_catsss_id==$cat || cat_is_ancestor_of($the_catsss_id, $cat) || in_category($the_catsss_id)&&is_single()) $choosen = "choosen 4th";else $choosen="4th";  // current choosen detect
                                            echo '<li class="cat_'.$the_catsss_id.' par_'.$the_catsss->category_parent.'"><a href="'.get_category_link($the_catsss_id).'" class="'.$choosen.'">'.$the_catsss->name.'</a></li>';  //no wrapper
                                        };
                                        echo $mobile ? "</ul>" : "</ol></div>";
                                    };
                                    echo "</li>";
                                };
                                echo $mobile ? "</ul>" : "</ol></div>";
                            };
                            echo "</li>";
                        };
                        echo $mobile ? "</ul>" : "</ol></div>";
                    }
                };
                echo "</li>";
            }
        }
        unset($cat);
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
    
    // 通过文章别名模糊匹配文章id
    function get_post_like_slug($post_slug) {
        global $wpdb;
        $post_slug = '%' . $post_slug . '%';
        $pid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name LIKE %s", $post_slug));
        unset($wpdb);
        return get_post($pid);
    }
    
    // 通过分类模板名称获取绑定的分类别名
    function get_template_bind_cat($template=false){
        global $wpdb;
        // $template_page_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$template'");
        // $template_term_id = get_post_meta($template_page_id, "post_term_id", true); //SELECT *
        $template_term_id = $wpdb->get_var("SELECT term_id FROM $wpdb->termmeta WHERE meta_value = '$template'");
        // return !get_category($template_term_id)->errors ? get_category($template_term_id) : get_category(1);
        unset($wpdb);
        return get_category($template_term_id);
    }
    // get bind category-template cat by specific binded-temp post_id
    function get_cat_by_template($temp='news', $parm=false){
        $cats = get_template_bind_cat('category-'.$temp.'.php');
        return !$cats->errors ? ($parm ? $cats->$parm : $cats) : false;
    }
    
    // 自动创建视频截图预览 
    // Automatic-Generate images captures(jpg/gif) while uploading a video file.(whether uploading inside the article)
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
    }
    
    
    function get_yearly_cat_count($year, $cid, $limit=99){
        $year_posts = get_posts(array(
            "year"        => $year,
            "category"    => $cid,
            "numberposts" => $limit,
        ));
        return count($year_posts);
    }
    function get_wpdb_yearly_pids($year=false, $limit=99, $offset=0){
        global $wpdb;
        $year = $year ? $year : gmdate('Y', time() + 3600*8); //date('Y');
        $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = $year ORDER BY post_date DESC LIMIT $limit OFFSET $offset "); // !!!LIMIT & OFFSET must type of NUMBER!!!
        unset($wpdb);
        return $res;
    }
    function get_wpdb_pids_by_cid($cid=0, $limit=99, $offset=0){
        global $wpdb;
        // https://www.likecs.com/show-306636263.html#sc=304
        $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts,wp_term_relationships WHERE ID = object_id AND post_type = 'post' AND post_status = 'publish' AND wp_term_relationships.term_taxonomy_id = $cid ORDER BY post_date DESC LIMIT $limit OFFSET $offset "); //(post_status = 'publish' OR post_status = 'private') //instance_type in ("m5.4xlarge","r5.large","r5.xlarge");
        unset($wpdb);
        return $res;
    }
    function check_request_param(string $param){
        $res = null;
        if(!isset($_REQUEST[$param])) return $res;
        // $req = filter_var($_REQUEST[$param], FILTER_SANITIZE_STRING);
        switch (true) {
            case isset($_GET[$param]):
                $res = $_GET[$param];
                break;
            case isset($_POST[$param]):
                $res = $_POST[$param];
                break;
            default:
                isset($_COOKIE[$param]) ? $res = $_COOKIE[$param] : false;
                break;
        }
        return $res;
    }
    // Ajax PostData calls
    function ajaxGetPosts(){
        $cid = check_request_param('cid');
        $type = check_request_param('type');
        $limit = check_request_param('limit');
        $offset = check_request_param('offset');
        // $private = 'Accesing Private Content';
        $prefix = get_category($cid)->slug;
        if($type==='archive'){
            $prefix = $_POST['key'];
            $cur_posts = get_wpdb_yearly_pids($prefix, $limit, $offset);
            $news_temp = get_cat_by_template('news','slug');
        }else{
            $cur_posts = get_wpdb_pids_by_cid($cid, $limit, $offset);
        }
        $res_array = array();
        // wp_verify_nonce($_POST['_ajax_nonce']);
        $ajax_referer = check_ajax_referer($prefix.'_posts_ajax_nonce');  // 检查 nonce [24h valid max]
        // https://developer.wordpress.org/reference/functions/check_ajax_referer/
        if(false===$ajax_referer){
            array_push($res_array, ['ajax_nonce verification failure']);
        }else{
            $cur_posts_count = count($cur_posts);
            for($i=0;$i<$cur_posts_count;$i++){
                $each_posts = $cur_posts[$i];
                $this_post = get_post($each_posts->ID);
                $pid = $this_post->ID;
                $post_class = new stdClass();
                // universal
                $post_class->id = $pid;
                $post_class->title = $this_post->post_title;
                $post_class->subtitle = get_post_meta($pid, "post_feeling", true);
                switch ($type) {
                    case 'acg':
                        $post_class->link = get_the_permalink($pid);
                        $post_class->poster = get_postimg(0, $pid, true);
                        $post_class->excerpt = get_the_excerpt($this_post);
                        $post_class->rcmd = get_post_meta($pid, "post_rcmd", true);
                        $post_class->rating = get_post_meta($pid, "post_rating", true);
                        break;
                    case 'weblog':
                        $post_class->tag = get_tag_list($pid);
                        $post_class->date = date('Y年n月j日', strtotime($this_post->post_date));
                        $post_class->content = $this_post->post_content;
                        break;
                    case 'archive':
                        $prev_posts = $i>0 ? $cur_posts[$i-1] : $cur_posts[$i];
                        $prev_post = get_post($prev_posts->ID);
                        $this_cats = get_the_category($this_post);
                        $cur_slug = $this_cats[0]->slug;
                        preg_match('/\d{2}-\d{2} /', $this_post->post_date, $this_date);
                        preg_match('/\d{2}-\d{2} /', $prev_post->post_date, $prev_date);
                        $unique_date = $this_date[0]!=$prev_date[0] || $each_posts->ID==$cur_posts[0]->ID ? '<div class="timeline">'.$this_date[0].'</div>' : '';
                        $cat_str = '';
                        foreach ($this_cats as $cat){
                            $cat_str .= '<span>'.$cat->name.'</span>';
                        };
                        $this_title = $this_post->post_title;
                        $post_class->title = $cur_slug==$news_temp ? '<b>'.$this_title.'</b>' : $this_title;
                        $post_class->mark = $cur_slug==$news_temp ? " article" : "";
                        $post_class->link = get_the_permalink($this_post);
                        $post_class->date = $unique_date;
                        $post_class->cat = $cat_str;
                        break;
                    default:
                        // code...
                        break;
                }
                // private
                // if($this_post->post_status==='private'){
                //     $post_class->title = $private;
                //     $post_class->content = $private;
                //     $post_class->subtitle = $private;
                // }
                array_push($res_array, $post_class);
            }
        }
        print_r(json_encode($res_array));
        die();
    }
    add_action('wp_ajax_ajaxGetPosts', 'ajaxGetPosts');
    add_action('wp_ajax_nopriv_ajaxGetPosts', 'ajaxGetPosts');
    
    
    
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
    
    
    // 文章 TOC 目录 https://www.ludou.org/wordpress-content-index-plugin.html/comment-page-3#comment-16566
    function article_index($content) {
        if(is_single() && preg_match_all('/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is', $content, $matches) && get_option('site_indexes_switcher')) {
            $match_h = $matches[1];
            $match_m = count($match_h);
            // set unique_title for the completely same h-tag
            // $match_str = ''; //$match_arr = [];
            // for($i=0;$i<$match_m;$i++){
            //     $match_str.=' ['.trim(strip_tags($matches[2][$i])).'] '; // array_push($match_arr, trim(strip_tags($matches[2][$i])));
            // }
            $ul_li = '';
            for($i=0;$i<$match_m;$i++){
                $value = $match_h[$i];
                $title = trim(strip_tags($matches[2][$i]));
                // if(substr_count($match_str, '['.$title.']')>=2){
                //     $title = trim(strip_tags($matches[2][substr($i,0)])).'_';
                //     // echo trim(strip_tags($matches[2][$i]));
                // }
                $content = str_replace($matches[0][$i], '<a href="javascript:;" id="title-'.$i.'" class="index_anchor" aria-label="anchor"></a><h'.$value.' id="title_'.$i.'">'.$title.'</h'.$value.'>', $content);
                // $content = preg_replace('/<h(\d)>(.+)(<\/h\d>)/i', "<a href='javascript:;' id='title-$i' class='index_anchor' aria-label='anchor'></a><h\${1} id='title_$i'>\${2}\${3}", $content);
                $value = $match_h[$i];
                $pre_val = array_key_exists($i-1,$match_h) ? $match_h[$i-1] : 9;
                $ul_li .= $value>$pre_val || $value>=3 ? '<li class="child" id="t'.$i.'"><a href="#title-'.$i.'" title="'.$title.'">'.$title.'</a></li>' : '<li id="t'.$i.'"><a href="#title-'.$i.'" title="'.$title.'">'.$title.'</a></li>';
                // $ul_li .= '<li><a href="#title-'.$i.'" title="'.$title.'">'.$title.'</a>'.$child.'</li>';
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
    
    
    // 文章归档查询
    function get_post_archives($type="yearly", $post_type="post", $limit=""){
        $archives = wp_get_archives(
            array(
                'type' => $type,
                'limit' => $limit,
                'echo' => false,
                'format' => 'custom',
                'before' => '', 
                'after' => ',',
                // 'before' => '<div><a href="" rel="nofollow"><h2>', 
                // 'after' => '</h2><p></p></a></div>',
                'post_type' => $post_type,
                'show_post_count' => true
            )
        );
        $archive_arr = explode(',', $archives);
        $archive_arr = array_filter($archive_arr, function($i) {
            return trim($i) !== '';  // Remove empty whitespace item from array
        });
        // print_r($archive_arr);
        $array = array();
        foreach($archive_arr as $year_item) {
            $data_row = trim($year_item);
            // print_r($data_row);
            preg_match('/href=["\']?([^"\'>]+)["\']>(.+)<\/a>(.*)/', $data_row, $data_vars);
            // print_r($data_vars);
            if (!empty($data_vars)) {
                preg_match('/\((\d+)\)/', $data_vars[3], $count);
                $array[] = array(
                    'title' => $data_vars[2], // Ex: January 2020
                    'link' => $data_vars[1], // Ex: http://demo.com/2020/01/
                    'count' => $count[1]
                );
            }
        }
        return $array;
    }
    
    
    // 文章归档统计
    function the_archive_stats(){
        $output = '';
        $output_sw = false;
        if(get_option('site_cache_switcher')){
            $caches = get_option('site_cache_includes');
            $temp_slug = get_cat_by_template('archive','slug');
            $output_sw = in_array($temp_slug, explode(',', $caches));
            $output = $output_sw ? get_option('site_archive_count_cache') : '';
        }
        if(!$output || !$output_sw){
            $archive_yearly = get_post_archives('yearly');
            $blink = get_option('site_animated_counting_switcher') ? ' blink' : false;
            foreach ($archive_yearly as $archive){
                $counts = $archive['count'];
                $output .= '<div class="'.$blink.'" data-count="'.$counts.'"><a href="'.$archive['link'].'" rel="nofollow"><b>'.$archive['title'].'</b><h1>'.$counts.'</h1><p>篇发布记录</p></a></div>';
            }
            if($output_sw) update_option('site_archive_count_cache', wp_kses_post($output));
            // unset($archive_yearly);
        }
        echo wp_kses_post($output);
    }
    
    //文章归档热度（每日更新一次热度表）
    function the_archive_contributions(){
        $output = '';
        $output_sw = false;
        if(get_option('site_cache_switcher')){
            $caches = get_option('site_cache_includes');
            $temp_slug = get_cat_by_template('archive','slug');
            $output_sw = in_array($temp_slug, explode(',', $caches));
            $output = $output_sw ? get_option('site_archive_contributions_cache') : '';
        }
        $GLOBALS['color_light'] = '#9be9a8';
        $GLOBALS['color_middle'] = '#40c463';
        $GLOBALS['color_heavy'] = '#30a14e';
        $GLOBALS['color_more'] = '#216e39';
        echo '<h5><strong> Contributions view </strong><ul class="cs_tips"><li></li><li style="color:'.$GLOBALS['color_light'].'"></li><li style="color:'.$GLOBALS['color_middle'].'"></li><li style="color:'.$GLOBALS['color_heavy'].'"></li><li style="color:'.$GLOBALS['color_more'].'"></li></ul></h5>';
        if(!$output || !$output_sw){  // no-cache or cache-disabled
            $GLOBALS['archive_daily'] = get_post_archives('daily','post',9999); //$archive_daily
            global $curYear; //$curYear = gmdate('Y', time() + 3600*8);
            $curday = gmdate('md', time() + 3600*8); //date('md'); //$today = date('d');
            $tomon = date('m');
            $lastYear = $curYear-1;
            // calculate number of days in a month // https://stackoverflow.com/questions/49612838/call-to-undefined-function-cal-days-in-month-error-while-running-from-server
            function days_in_month($month, $year){
                return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
            };
            function archive_contributions_output($days, $the_day, $compare_date, $year){
                $output = '<span class="'.$the_day.'" data-dates="'.$compare_date.'" data-date="'.$days.'"';
                foreach ($GLOBALS['archive_daily'] as $archive){
                    $archive_date = $archive['title'];
                    preg_match("/$year/", $archive_date, $res);  //output year
                    if(array_key_exists(0,$res) && $archive_date==$compare_date){
                        $counts = $archive['count'];
                        $output .= ' id="edit" data-count="'.$counts.'" style="color:';
                        if($counts>=4){
                            $color = $GLOBALS['color_more'];
                        }else{
                            switch ($counts) {
                                case 1:
                                    $color = $GLOBALS['color_light'];
                                    break;
                                case 2:
                                    $color = $GLOBALS['color_middle'];
                                    break;
                                case 3:
                                    $color = $GLOBALS['color_heavy'];
                                    break;
                                default:
                                    $color = $GLOBALS['color_more'];
                            };
                        };
                        $output .= $color.'"';
                    }
                }
                $output .= '></span>';
                return $output;
            }
            // 全年报表
            $async_fully_sw = get_option('site_async_archive_contributions');
            if($async_fully_sw){
                for($i=1;$i<13;$i++){
                    $m = days_in_month($i-$tomon, $lastYear);
                    for($j=1;$j<=$m;$j++){
                        $days = $j<10 ? $i.'0'.$j : $i.$j;
                        $the_day = $days==$curday ? 'today' : ($days>$curday ? 'dayto' : false);
                        $compare_date = $lastYear.'年'.$i.'月'.$j.'日';
                        if($lastYear<$curYear && $i>$tomon){// && $j>=$otday月份大于等于当前月份，天数大于今天
                            $output .= archive_contributions_output($days,$the_day,$compare_date,$lastYear);
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
                        $output .= archive_contributions_output($days,$the_day,$compare_date,$curYear);
                    }else if($i==$tomon){
                        $output .= archive_contributions_output($days,$the_day,$compare_date,$curYear);
                        // $j<=$today ? archive_contributions_output($days,$the_day,$compare_date,$curYear) : false;
                    }
                }
            }
            unset($GLOBALS['archive_daily']);
            if($output_sw) update_option('site_archive_contributions_cache', wp_kses_post($output));
        }
        unset($GLOBALS['color_light'],$GLOBALS['color_middle'],$GLOBALS['color_heavy'],$GLOBALS['color_more']);
        echo wp_kses_post($output);
    }
    
    // 文章归档列表（每日更新一次归档列表）
    function the_archive_lists(){
        $output = '';
        $output_sw = false;
        if(get_option('site_cache_switcher')){
            $caches = get_option('site_cache_includes');
            $temp_slug = get_cat_by_template('archive','slug');
            $output_sw = in_array($temp_slug, explode(',', $caches));
            $output = $output_sw ? get_option('site_archive_list_cache') : '';
        }
        global $wpdb;
        $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );
        if(!$output || !$output_sw){
            global $async_sw, $use_async, $async_loads, $curYear;
            $async_stats_sw = get_option('site_async_archive_stats');
            $news_temp = get_cat_by_template('news');
            $note_temp = get_cat_by_template('notes');
            $blog_temp = get_cat_by_template('weblog');
            $news_temp_id = $news_temp->term_id;
            $note_temp_id = $note_temp->term_id;
            $blog_temp_id = $blog_temp->term_id;
            $news_temp_name = $news_temp->name;
            $note_temp_name = $note_temp->name;
            $blog_temp_name = $blog_temp->name;
            $output_stats = "";
            // get years that have posts // https://wordpress.stackexchange.com/questions/46136/archive-by-year
            foreach ($years as $year) {
                $cur_year = $year->year;
                $cur_posts = get_wpdb_yearly_pids($cur_year, $async_loads, 0);
                $posts_count = count($cur_posts);
                $all_pids = get_wpdb_yearly_pids($cur_year, 999, 0);  //list 999+posts
                $pids_count = count($all_pids);
                if($async_stats_sw){
                    $news_count = get_yearly_cat_count($cur_year, $news_temp_id);
                    $note_count = get_yearly_cat_count($cur_year, $note_temp_id);
                    $blog_count = get_yearly_cat_count($cur_year, $blog_temp_id);
                    $rest_count = $pids_count - ($news_count+$note_count+$blog_count);
                    $output_stats = '<span class="stat_'.$cur_year.' stats">📈📉统计：<b>'.$news_temp_name.'</b> '.$news_count.'篇、 <b>'.$note_temp_name.'</b> '.$note_count.'篇、 <b>'.$blog_temp_name.'</b> '.$blog_count.'篇、 <b>其他类型</b> '.$rest_count.'篇。</span>';
                }
                // SAME COMPARE AS $found $limit
                $load_btns = $posts_count>=$async_loads ? '<sup class="call" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="'.wp_create_nonce($cur_year."_posts_ajax_nonce").'">加载更多</sup>' : '<sup class="call disabled" data-year="'.$cur_year.'" data-click="0" data-load="'.$posts_count.'" data-counts="'.$pids_count.'" data-nonce="disabled">已全部载入</sup>';
                $load_icon = $curYear==$cur_year ? ' 🚀 ' : ' 📁 ';
                $output .= $async_sw ? '<h2>' . $cur_year . ' 年度发布'.$load_icon.$load_btns.'</h2>'.$output_stats.'<ul class="list_'.$cur_year.'">' : '<h2>' . $cur_year . ' 年度发布</h2>'.$output_stats.'<ul class="list_'.$cur_year.'">';
                $output_each = '';
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
                    $output_each .= '<li>'.$unique_date.'<a class="link'.$this_article.'" href="'.get_the_permalink($this_post).'" target="_blank">'.$this_title.'<sup>';
                    $output_cat = '';
                    foreach ($this_cats as $this_cat){
                        $output_cat .= '<span id="'.$this_cat->term_id.'">'.$this_cat->name.'</span>';
                    }
                    $output_each .= $output_cat.'</sup></a></li>';
                };
                $output .= $output_each.'</ul>';
            }
            if($output_sw) update_option('site_archive_list_cache', wp_kses_post($output));
        }else{
            // always update wp-nonce if db-cached
            foreach ($years as $year) {
                $cur_year = $year->year;
                $cur_nonce = wp_create_nonce($cur_year."_posts_ajax_nonce");
                // 贪婪匹配(.*)有效（标识符连接处需?非贪婪匹配）
                $output = preg_replace('/<sup(.*)data-year=("'.$cur_year.'")(.*?)data-nonce=("[^"]*")(.*)<\/sup>/i', '<sup $1data-year=$2$3data-nonce="'.$cur_nonce.'"$5</sup>', $output);
            }
        }
        echo wp_kses_post($output);
    }
    
    
    /* ------------------------------------------------------------------------ *
     *  wp_schedule_event 定时任务
     * ------------------------------------------------------------------------ */
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
    function site_clear_db_caches() {
        // // 仅适用于不存在 wp_ajax_nopriv_my_ajax_action 请求验证的数据
        // remove_action('wp_ajax_my_ajax_action', 'my_ajax_callback');
        // remove_action('wp_ajax_nopriv_my_ajax_action', 'my_ajax_callback');
        // // 未解决BUG：data-nonce验证数据[24h有效，根据用户会话单独生成验证数据]被db缓存导致其他xhr请求会话返回403
        update_option('site_archive_count_cache', '');  //清除（重建）归档统计
        // update_option('site_archive_contributions_cache', ''); //解决bug：切换全年报表后无法判断db数据库中是否已存在全年记录
        update_option('site_archive_list_cache', '');  
        // //清除（重建）ACG 缓存
        // update_option('site_acg_stats_cache', '');
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
    function update_category_post_cache($pid, $temp, $cache) {
        $cat_temp = get_cat_by_template($temp);
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
    
    // 自定义文章标签
    function get_tag_list($pid, $max=3, $dot="、"){
        $tags_list = get_the_tags($pid);
        if(!$tags_list) return;
        $tas_list = '';
        $tags_count = count($tags_list);
        for($i=0;$i<$max;$i++){
            $tag = array_key_exists($i,$tags_list) ? $tags_list[$i] : false;
            $dots = $max<$tags_count ? ($i<$max-1 ? $dot : false) : ($i<$tags_count-1 ? $dot : false);
            if($tag){
                $tag_name = $tag->name;
                $tas_list .= '<a href="'.get_bloginfo("url").'/tag/'.$tag_name.'" data-count="'.$tag->count.'" rel="tag">'.$tag_name.'</a>'.$dots;
            }
        }
        return $tas_list;
    }
    
    // 自定义标签云
    function the_tag_clouds($html_tag="li"){
        $num = get_option('site_tagcloud_num');
        $tags = get_tags(array(
            'taxonomy' => 'post_tag',
            'orderby' => 'count', //name
            'hide_empty' => true // for development,
            // 'number' => $max_show
        ));
        $tag_count = count($tags);
        $max_show = $tag_count<=$num ? $tag_count : $num;
        $min_font = 10;
        $max_font = get_option('site_tagcloud_max');
        shuffle($tags);  // random tags
        if(get_option('site_tagcloud_switcher') && $tag_count>=1){
            global $bold_font;
            for($i=0;$i<$max_show;$i++){
                $tag = $tags[$i];
                $rand_font = mt_rand($min_font, $max_font);
                if($rand_font>=$max_font/1.25){
                    $rand_opt = mt_rand(5,10);  // highlight big_font
                    $bold_font = $rand_opt>9 || $rand_font==$max_font ? 'bold' : 'normal';  // max bold_font
                    $color_font = $rand_opt==10 && $rand_font==$max_font ? 'color:var(--theme-color)' : '';
                }else{
                    $rand_opt = mt_rand(2,10);
                    $color_font = $rand_opt<=5 && $rand_font<=$max_font/2 ? 'color:var(--theme-color)' : '';
                }
                $rand_opt = $rand_opt==10 ? $rand_opt=1 : '0.'.$rand_opt;  // use dot
                echo '<'.$html_tag.' data-count="'.$tag->count.'"><a href="'.get_tag_link($tag->term_id).'" target="_blank" style="font-size:'.$rand_font.'px;opacity:'.$rand_opt.';font-weight:'.$bold_font.';'.$color_font.'">'.$tag->name.'</a></'.$html_tag.'>'; //<sup>'.$tag->count.'</sup>
            }
            unset($bold_font);
        }else{
            echo '<span id="acg-content-area" style="background: url(//api.uuz.bid/random/?image) center /cover"></span><span id="acg-content-area-txt"><p id="hitokoto"> NO Tags Found.  </p></span>';
        }
    }
    
    // 分类背景图/视频海报
    function cat_metabg($cid, $preset=false){
        global $img_cdn;
        $metaimg = get_term_meta($cid, 'seo_image', true);  //$page_cat->term_id
        $result = $metaimg ? $metaimg : ($preset ? $preset : $img_cdn.'/images/default.jpg');  //get_option('site_bgimg')
        global $images_cdn, $upload_url, $cdn_switch;
        $res = $result;
        if($cdn_switch){
            $res = preg_replace('/(<img.+src=\"?.+)('.preg_quote($upload_url,'/').')(.+\.*\"?.+>)/i', "\${1}".$images_cdn."\${3}", $result);
        }
        unset($images_cdn, $upload_url, $cdn_switch);
        return $res;
    }
    
    // 更新 sitemap 站点地图
    if(get_option('site_map_switcher')){
        function update_sitemap() {
            require_once(TEMPLATEPATH . '/plugin/sitemap.php');
        }
        add_action('publish_post','update_sitemap');
        // add_action('after_setup_theme', 'update_sitemap');
    }
    
    // 站点头部
    function get_head($cat=false){
        // global $cat;
        require_once(TEMPLATEPATH. '/head.php');
    }
    
    
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
    
    // 近期公告
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
    // 自定义 wpdb 查询函数
    // function get_wpdb_query($data,$key,$val,$type){
    //     global $wpdb;
    //     return $wpdb->get_var("SELECT $data FROM $wpdb->posts WHERE $key = '$val' AND post_type = '$type'");
    // }
    function wpdb_postmeta_query($data,$key,$val){
        global $wpdb;
        $res = $wpdb->get_var("SELECT $data FROM $wpdb->postmeta WHERE $key = '$val'");
        unset($wpdb);
        return $res;
    }
    // 获取自定义页面所属分类term_id
    function get_page_cat_id($slug){
        global $wpdb;
        $res = $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE slug = '$slug'");
        unset($wpdb);
        return $res;
    }
    // 获取自定义页面内容
    function the_page_id($slug){
        global $wpdb;
        $res = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        unset($wpdb);
        return $res;
    }
    function the_page_content($slug){
        global $wpdb;
        $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        unset($wpdb);
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
                echo single_tag_title('',false) . ' Tag';
                break;
            case is_archive():
                echo 'Archives..';
                break;
            default:
                echo "NOT MATCHED";
                break;
        }
    }
    // 获取当前分类、页面、文章slug
    function current_slug($upper=false, $cats=false, $posts=false){
        global $cat, $post;  //变量提升
        $cats ? $cat=$cats: $cat;
        $posts ? $post=$posts: $post;
        switch (true) {
            case is_home():
                $slug = "INDEX";
                break;
            case is_page():
                $slug = $upper ? strtoupper($post->post_name) : strtolower($post->post_name);
                break;
            case is_category():
                $slug = $upper ? strtoupper(get_category($cat)->slug) : strtolower(get_category($cat)->slug);
                break;
            case is_single(): //in_category(array('news','notes')):
                $slug = "ARTICLE";
                break;
            case is_search():
                $slug = "SEARCH";
                break;
            case is_tag():
                $slug = "TAGS";
                break;
            case is_archive():
                $slug = "ARCHIVE";
                break;
            default:
                $slug = "NOT MATCHED";
                break;
        };
        unset($cat, $post);
        return $slug;
    }
    // 自动匹配首页、分类、文章、页面标题
    function custom_title(){
        global $wp_query;//$cat, $post;
        $nick = get_option('site_nick');
        $name = !is_home() ? ' - '.get_bloginfo('name') : '';
        $surfix = $nick ? " | " . get_option('site_nick') . $name : $nick; //
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
                echo $wp_query->found_posts . ' search results for "' . esc_html(get_search_query()) .'"'. $surfix;
                break;
            case is_tag():
                echo $wp_query->found_posts . ' post for tag "'. single_tag_title('',false) .'"'. $surfix;
                break;
            case is_archive():
                $dates = $wp_query->query;
                $date_mon = array_key_exists('monthnum',$dates) ? ' - '.$dates['monthnum'].' ' : '';
                echo $wp_query->found_posts . ' archives in ' . $dates['year'] . $date_mon . $surfix;
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
        unset($wp_query);//$cat, $post
    }
    
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
    
    // 指定分类输出RSS feed  https://www.laobuluo.com/3863.html
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
    
    
    function the_comment_ranks($t1='常客',$c1='访问较频繁的童鞋',$t2='稀客',$c2='偶尔来访的小伙伴',$t3='游客',$c3=''){
        global $valine_sw;
        $output = '';
        if(!$valine_sw){
            $output_sw = false;
            if(get_option('site_cache_switcher')){
                $caches = get_option('site_cache_includes');
                $temp_slug = get_cat_by_template('ranks','slug');
                $output_sw = in_array($temp_slug, explode(',', $caches));
                $output = $output_sw ? get_option('site_rank_list_cache') : '';
            }
            if(!$output || !$output_sw){
                $output .= '<h1>'.$t1.' </h1><p>'.$c1.'</p><ul id="rankest">';
                $rankdata = get_comment_ranks();
                $datalen = count($rankdata);
                $databox = '';
                for($i=0;$i<3;$i++){
                    if(array_key_exists($i,$rankdata)){
                        $user = $rankdata[$i];
                        $count = $user->count ? $user->count : 0;
                        $link = $user->link ? $user->link : '#';
                        $name = $user->name ? $user->name : '???';
                    }
                    $lazyhold = "";
                    $avatar = get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100';
                    if($lazysrc!='src'){
                        $lazyhold = 'data-src="'.$avatar.'"';
                        $avatar = $loadimg;
                    }
                    $counts = $count<50 ? $count*2 : $count;
                    $databox .= '<li><span id="avatar" data-t="'.$count.'"><a href="'.$link.'" target="_blank"><img '.$lazyhold.' src="'.$avatar.'" title="这家伙留了 '.$count.' 条评论！" alt="'.$name.'" /></a></span><span id="range" style=""><em style="height:'.$counts.'%"><span class="wave active"></span></em></span><a href="'.$link.'" target="_self"><b title="'.$name.'">'.$name.'</b></a></li>';
                };
                $databox .= '</ul>';
                // top 10
                if($datalen>3){
                    $databox .= '<h1>'.$t2.' </h1><p>'.$c2.'</p><ul id="ranks">';
                    for($i=3;$i<10;$i++){
                        $user = array_key_exists($i,$rankdata) ? $rankdata[$i] : false;
                        if($user){
                            $count = $user->count;
                            $link = $user->link;
                            $lazyhold = "";
                            $avatar = get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100';
                            if($lazysrc!='src'){
                                $lazyhold = 'data-src="'.$avatar.'"';
                                $avatar = $loadimg;
                            }
                            $databox .= '<li title="TA 在本站已有 '.$count.' 条评论"><span id="avatar"><a href="'.$link.'" target="_blank"><img '.$lazyhold.' src="'.$avatar.'" title="这家伙留了 '.$count.' 条评论！" alt="'.$name.'"></a></span><a href="'.$link.'" target="_blank"><b data-mail="'.$user->mail.'">'.$user->name.'</b><sup>'.$count.'+</sup></a></li>';
                        }
                    }
                    $databox .= '</ul>';
                };
                // left 
                if($datalen>13){
                    $databox .= '<h1>'.$t3.' </h1><p>'.$c3.'</p><ul id="ranked">';
                    for($i=13;$i<50;$i++){
                        $user = array_key_exists($i,$rankdata) ? $rankdata[$i] : false;
                        if($user) $databox .= '<li><p>'.$user->name.'<sup>'.$user->count.'</sup></p></li>';
                    }
                    $databox .= '</ul>';
                };
                $output .= $databox;
                if($output_sw) update_option('site_rank_list_cache', wp_kses_post($output));
            }
        }else{
            $output .= '<h1>'.$t1.' </h1><p>'.$c1.'</p><ul id="rankest"><span id="loading"></span></ul><h1> '.$t2.' </h1><p>'.$c2.'</p><ul id="ranks"><span id="loading"></ul><h1>'.$t3.'</h1>'.$c3.'<ul id="ranked"><span id="loading"></span></ul>';
        };
        echo wp_kses_post($output);
    }
    // WP评论统计排行 https://www.seo628.com/2685.html
    function get_comment_ranks(){
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
        unset($wpdb);
        usort($comments_data,function($first,$second){
            return $first->count < $second->count;
        });
        return $comments_data;
    }
    

    function get_site_bookmarks($category='standard', $orderby='link_id', $order='ASC'){
        $res = get_bookmarks(array(
            'orderby' => $orderby,
            'order' => $order,
            'category_name' => $category,
            // 'exclude' => 60,
            'hide_invisible' => 0
        ));
        return (count($res)>0 ? $res : false);
    }
    function the_site_links($t1='小伙伴们', $t2='', $t3=''){ //, $baas=false
        global $baas;
        $output = '';
        if(!$baas){
            $output_sw = false;
            if(get_option('site_cache_switcher')){
                $caches = get_option('site_cache_includes');
                $temp_slug = get_cat_by_template('2bfriends','slug');
                $output_sw = in_array($temp_slug, explode(',', $caches));
                $output = $output_sw ? get_option('site_link_list_cache') : '';
            }
            if(!$output || !$output_sw){
                $rich_links = get_site_bookmarks();
                $output .= $rich_links ? '<div class="inbox-clip"><h2 id="exchanged"> '.$t1.' </h2></div><div class="deals exchanged flexboxes">'.get_site_links($rich_links, 'full').'</div>' : '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.current_slug(true).' </h1></div>';
                if($t2){
                    $t2 = $t2 ? $t2 : '技术侧重';
                    $tech_links = get_site_bookmarks('technical');  // $tech_links = get_filtered_bookmarks('technical', 'others');
                    if($tech_links) $output .= '<div class="inbox-clip"><h2 id="exchanged"> '.$t2.' </h2></div><div class="deals tech exchanged flexboxes">'.get_site_links($tech_links, 'full').'</div>';
                }
                if($t3){
                    $t3 = $t3 ? $t3 : '荐亦有鉴';
                    $rcmd_links = get_site_bookmarks('special','rand','DESC');
                    if($rcmd_links) $output .= '<div class="inbox-clip"><h2 id="rcmded"> '.$t3.' </h2></div><div class="deals rcmd flexboxes">'.get_site_links($rcmd_links, 'half').'</div>';
                    $other_links = get_site_bookmarks('others','link_id','DESC');
                    if($other_links) $output .= '<div class="deals oldest"><div class="inboxSliderCard"><div class="slideBox flexboxes">'.get_site_links($other_links).'</div></div></div>';
                }
                if($output_sw) update_option('site_link_list_cache', wp_kses_post($output));
            }
        }else{
            $output .= '<div class="inbox-clip"><h2 id="exchanged"> '.$t1.' </h2></div><div class="deals exchanged flexboxes"></div><!-- rcmd begain --><div class="inbox-clip"><h2 id="rcmded"> '.$t3.' </h2></div><div class="deals rcmd flexboxes"></div><!-- lost begain --><div class="inbox-clip"></div><div class="deals oldest"><div class="inboxSliderCard"><div class="slideBox flexboxes"></div></div></div>';
        }
        echo wp_kses_post($output);
    }
    //友情链接函数
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
        unset($lazysrc, $loadimg);
        return $output;
    }
    
    //分类 post metabox 信息
    function get_cat_title(){
        $cat_meta = get_term_meta($cat, 'seo_title', true);
        echo $cat_meta ? $cat_meta : strip_tags(trim(category_description()),"");
    };
    
    // 过滤单页视频 cdn 路径
    function replace_video_url($url=false, $key=false){
        if($url){
            global $images_cdn, $upload_url, $videos_cdn_arr, $cat, $cdn_switch;
            if($cdn_switch){
                $key = $key ? $key : current_slug();
                $url = in_array($key, $videos_cdn_arr) ? str_replace($upload_url, $images_cdn, $url) : str_replace($images_cdn, $upload_url, $url);
            }else{
                $url = str_replace($images_cdn, $upload_url, $url);
            };
            unset($images_cdn, $upload_url, $videos_cdn_arr, $cat, $cdn_switch);
            return $url;
        }
    }
    
    // 过滤文章内容 CDN 路径（新增 video 开关）
    if($cdn_switch){
        add_filter('the_content', 'replace_cdn_path', 9);
        function replace_cdn_path($content) {
            global $images_cdn, $upload_url, $videos_cdn_arr;
            // return str_replace('="'.$upload_url, '="'.$images_cdn, $content);
            // 控制全站视频加速开关（默认替换$images_cdn为$upload_url）
            $content = in_array('article', $videos_cdn_arr) ? preg_replace('/(<video.*src=.*)('.preg_quote($upload_url,'/').')(.*>)/i', "\${1}$images_cdn\${3}", $content) : preg_replace('/(<video.*src=.*)('.preg_quote($images_cdn,'/').')(.*>)/i', "\${1}$upload_url\${3}", $content);  // video filter works fine. //strpos($videos_cdn_page, 'article')!==false
            $res = preg_replace('/(<img.+src=\"?.+)('.preg_quote($upload_url,'/').')(.+\.*\"?.+>)/i', "\${1}".$images_cdn."\${3}", $content);
            unset($images_cdn, $upload_url, $videos_cdn_arr);
            return $res;  //http://blog.iis7.com/article/53278.html
        }
        // 替换后台媒体库图片路径（目前无法自定义每个图像url）https://wordpress.stackexchange.com/questions/189704/is-it-possible-to-change-image-urls-by-hooks
        function wpse_change_featured_img_url(){
            return get_option('site_cdn_img');  //'http://www.example.com/media/uploads';
        }
        add_filter( 'pre_option_upload_url_path', 'wpse_change_featured_img_url' );
    }
    // Disable SrcSet
    function remove_max_srcset_image_width( $max_width ) {
        return 1;
    }
    add_filter( 'max_srcset_image_width', 'remove_max_srcset_image_width' );
    
    //启用cdn加速(指定src/img)
    function custom_cdn_src($holder='src', $var=false){
        $default_src = get_bloginfo('template_directory');
        $cdn_src = get_option('site_cdn_src');
        $cdn_img = get_option('site_cdn_img');
        $cdn_api = get_option('site_cdn_api');
        if(get_option("site_cdn_switcher")&&$holder){ // set $holder as false for $default_src manually
            switch ($holder) {
                case 'img':
                    $holder = $cdn_img ? $cdn_img : $default_src;
                    break;
                case 'api':
                    $holder = $cdn_api ? $cdn_api : ($cdn_src ? $cdn_src.'/plugin' : $default_src.'/plugin');
                    break;
                default:
                    $holder = $cdn_src ? $cdn_src : $default_src;
                    break;
            };
        }else{
            $holder = $default_src;
        }
        if($var) return $holder;else echo $holder;
    };
    //兼容gallery获取post内容指定图片（视频海报）
    function get_postimg($index=0,$postid=false,$default=false) {
        global $post, $images_cdn, $upload_url, $cdn_switch, $img_cdn;
        $postid ? $post = get_post($postid) : $post;
        $ret = array();
        if(has_post_thumbnail()){
            $ret = [get_the_post_thumbnail_url()];
        }else{
            preg_match_all('/\<img.*src=("[^"]*")/i', $post->post_content, $image);
            foreach($image[0] as $i => $v) {
                $ret[] = trim($image[1][$i],'"');
            };
            //未匹配到图片或调用值超出图片数量范围则输出（视频海报或）默认图
            if(count($ret)<=0 || count($ret)<=$index) {
                preg_match_all('/\<video.*poster=("[^"]*")/i', $post->post_content, $video);
                $video_poster = $video[1] ? trim($video[1][0],'"') : false;
                if($video_poster){
                    $ret = [$video_poster];
                }else{
                    $ret = get_option('site_default_postimg_switcher') || $default ? [$img_cdn . '/images/default.jpg'] : $ret;
                }
                $index = 0;
            }
            
        }
        $result = $ret ? $ret[$index] : false;
        $res = $result;
        if($cdn_switch){
            // return strpos($result, $images_cdn)!==false ? $result : str_replace($upload_url, $images_cdn, $result);
            $res = str_replace($upload_url, $images_cdn, $result);
            // return preg_replace('/(<img.+src=\"?.+)('.preg_quote($upload_url,'/').')(.+\.*\"?.+>)/i', "\${1}".$images_cdn."\${3}", $result);
        }
        unset($post, $images_cdn, $upload_url, $cdn_switch);
        return $res;
    };
    
    // 自定义文章摘要
    function wpdocs_custom_excerpt_length( $length ) {
        return 300;
    }
    add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );
    function wpdocs_excerpt_more( $more ) {
        return '...';
    }
    add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );
    function custom_excerpt($length=99, $var=false){
        // $res = wp_trim_words(get_the_excerpt(), $length);
        $res = mb_substr(get_the_excerpt(), 0, $length).'...';  // chinese only
        if($var){
            return $res;
        }else{
            echo $res;
        }
    }
    //计算版权时间，直接在footer使用会引发没有内容的notes子分类无法显示
    function calc_copyright(){
        $year = gmdate('Y', time() + 3600*8);//date('Y');
        $begain = get_option('site_begain');
        if($begain&&$begain<$year) echo $begain."-";
        echo $year;
    }
    // 提取图片平均色值(耗时)
    function extract_images_rgb($url){
        $im  =  imagecreatefromstring(file_get_contents($url));
        $rgb  =  imagecolorat ( $im ,  10 ,  15 );
        $r  = ( $rgb  >>  16 ) &  0xFF ;
        $g  = ( $rgb  >>  8 ) &  0xFF ;
        $b  =  $rgb  &  0xFF ;
        return "$r $g $b";
        // 加载图片
        // $image = imagecreatefrompng($url) or die('ext format err.');
        // // 获取图片中指定位置的颜色
        // $rgb = imagecolorat($image, 1, 2);
        // // 将rgb值转换为hex值
        // $hex = "#".str_pad(dechex($rgb), 6, "0", STR_PAD_LEFT); 
        // // 获取rgb
        // list($r, $g, $b) = array_map('hexdec', str_split($hex, 2));
        // return "$hex";
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
        $id = check_request_param('um_id'); //$_GET["um_id"];
        check_ajax_referer($id.'_post_like_ajax_nonce');  // 检查 nonce
        // if($_GET["um_action"]=='like'){
            $post_liked = get_post_meta($id,'post_liked',true);
            $expire = time() + 99999999;
            $domain = ($_SERVER['HTTP_HOST']!='localhost') ? $_SERVER['HTTP_HOST'] : false;
            setcookie('post_liked_'.$id,$id,$expire,'/',$domain,false);
            if (!$post_liked || !is_numeric($post_liked)) update_post_meta($id, 'post_liked', 1);else update_post_meta($id, 'post_liked', ($post_liked + 1));
            echo get_post_meta($id,'post_liked',true);
        // }
        die;
    };
    
    //谷歌 Adsense 广告（默认加载link传参true则加载sidebar广告块）
    // function google_ads_switch($bar){  //$ink,
    //     // $disabled = '<h2 style="opacity:.5">Google 广告已关闭</h2>';
    //     if(get_option('site_ads_switcher')){
    //         // if($ink) echo(get_option('site_ads_link'));
    //         if($bar) echo(get_option('site_ads_init'));else echo '<h2 style="opacity:.5">已手动关闭广告。</h2>';
    //         // if($ink&&!$bar) echo '<h2 style="opacity:.5">已停用 Google 广告</h2>';
    //     }else{
    //         echo '<h2 style="opacity:.75">未启用广告插件！</h2>';
    //     }
    // };
    
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
    
    // leancloud avos（标准li结构）查询
    function avos_posts_query($cid,$els){
        $slug = get_category($cid)->slug;
?>
        <script type="text/javascript">
            new AV.Query("<?php echo $slug; ?>").addDescending("createdAt").limit(<?php echo get_option('site_per_posts', get_option('posts_per_page')); ?>).find().then(result=>{
                for (let i=0,resLen=result.length; i<resLen;i++) {
                    let res = result[i],
                        title = res.attributes.title,
                        content = res.attributes.content.replace(/</g,"&lt;").replace(/>/g,"&gt;");
                    document.querySelector("<?php echo $els ?>").innerHTML += `<li title='${content}'><a href="/<?php echo $slug ?>#${res.id}" target="_self" rel="nofollow">${title}</a></i>`;
                };
            })
        </script>
<?php
    }
    // wp自定义（含置顶无分页）查询函数
    function recent_posts_query($cid=0, $specific_link=false, $detail=false, $limit=null, $random=false){
        global $post;
        $orderby = $random ? 'rand' : array(
            'date' => 'DESC',
            'meta_value_num' => 'DESC',
            'modified' => 'DESC',
        );
        $acg_single_sw = get_option('site_single_switcher');
        if($acg_single_sw){
            $includes = get_option('site_single_includes');
            $acg_slug = get_cat_by_template('acg','slug');
            $acg_single_sw = in_array($acg_slug, explode(',', $includes));
        }
        $limit = $limit ? $limit : get_option('site_per_posts');
        $query_array = $cid ? array('cat' => $cid, 'meta_key' => 'post_orderby', 'posts_per_page' => $limit, 'orderby' => $orderby) : array('cat' => $cid, 'posts_per_page' => $limit, 'order' => 'DESC', 'orderby' => $orderby);
        $left_query = new WP_Query(array_filter($query_array));
        while ($left_query->have_posts()):
            $left_query->the_post();
            $topset = get_post_meta($post->ID, "post_orderby", true)>1 ? 'topset' : false;
            $title = $detail ? trim(get_the_title()).' -（'.get_post_meta($post->ID, "post_feeling", true).'）<sup>'.$post->post_date.'</sup>' : trim(get_the_title());
            // print_r(get_category($cid)->parent);
            $cid = !get_category($cid)->errors ? $cid : 1; //php8
            $par_cid = get_category($cid)->parent;
            $par_slug = $par_cid!=0&&get_category($par_cid)->slug!='/' ? get_category($par_cid)->slug : get_category($cid)->slug;
            $post_cat = get_the_category($post->ID);
            $loc_id = $par_slug==get_cat_by_template('acg','slug') ? ($post_cat[0]->parent!=0 ? $post_cat[0]->slug : $post_cat[1]->slug) : 'pid_'.get_the_ID();
            $pre_link = $specific_link || !$acg_single_sw ? '<a href="'.get_the_permalink().'" title="'.$title.'" target="_blank">' : '<a href="'.get_category_link($cid).'#'.$loc_id.'" target="_self" rel="nofollow">';
            echo '<li class="'.$topset.'">'.$pre_link . $title . '</a></li>';
        endwhile;
        wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
        unset($post);
    };
    
    
    // acg post stats
    function the_acg_stats(){
        global $cat, $cats, $preset;
        $preslug = $preset->slug;
        $output = '';
        if(!empty($cats) && current_slug()==$preslug){
            $output_sw = false;
            if(get_option('site_cache_switcher')){
                $caches = get_option('site_cache_includes');
                $temp_slug = get_cat_by_template('acg','slug');
                $output_sw = in_array($temp_slug, explode(',', $caches));
                $output = $output_sw ? get_option('site_acg_stats_cache') : '';
            }
            if(!$output || !$output_sw){
                $datadance = get_option('site_animated_counting_switcher');
                foreach($cats as $the_cat){
                    $cat_slug = $the_cat->slug;
                    $cat_count = $the_cat->count;
                    $cat_num = $cat_count;
                    $dataCls = '';
                    if($datadance){
                        $dataCls = ' blink';
                        $cat_num = '0';
                    }
                    $output .= '<div class="'.$cat_slug.$dataCls.'" data-count="'.$cat_count.'"><a href="'.get_category_link($the_cat->term_id).'" rel="nofollow"><h2>'.$cat_num.'<sup>+</sup></h2><p>'.$the_cat->name.'/'.strtoupper($cat_slug).'</p></a></div>';
                }
                if($output_sw) update_option('site_acg_stats_cache', wp_kses_post($output));
            }
        }else{
            $the_cat = get_category($cat);
            $cat_count = $the_cat->count;
            $output .= '<div class="blink" data-count='.$cat_count.'><h2 class="single">'.$cat_count.'<sup>+</sup></h2><p>'.$the_cat->name.'/'.$the_cat->slug.'</p></div>';
        }
        echo wp_kses_post($output);
    }
    //acg post list(multi)
    function the_acg_posts(){
        global $cat, $cats, $preset, $async_loads;
        $preslug = $preset->slug;
        $output = '';
        if(!empty($cats) && current_slug()==$preslug){
            // cache db only if not-single sub-page
            $output_sw = false;
            if(get_option('site_cache_switcher')){
                $caches = get_option('site_cache_includes');
                $temp_slug = get_cat_by_template('acg','slug');
                $output_sw = in_array($temp_slug, explode(',', $caches));
                $output = $output_sw ? get_option('site_acg_post_cache') : '';
            }
            if(!$output || !$output_sw){
                foreach($cats as $the_cat) $output .= get_acg_posts($the_cat, $preslug, $async_loads);
                // wp_kses_post() filted javascript:; href
                if($output_sw) update_option('site_acg_post_cache', $output); //wp_kses_post($output)
            }else{
                // always update wp-nonce if db-cached
                foreach($cats as $the_cat){
                    $cat_slug = $the_cat->slug;
                    $cur_nonce = wp_create_nonce($cat_slug."_posts_ajax_nonce");
                    // (.*?) 会在匹配到 data-nonce 或 data-cat 属性后停止匹配
                    $output = preg_replace('/<a(.*)data-nonce=("[^"]*")(.*)data-cat=("'.strtoupper($cat_slug).'")(.*)<\/a>/i', '<a$1data-nonce="'.$cur_nonce.'"$3data-cat=$4$5</a>', $output);
                }
            }
        }else{
            $output .= get_acg_posts(get_category($cat), $preslug, $async_loads);
        }
        // wp_kses_post() caused setupBlurColor() unabled to setup
        echo $output; //wp_kses_post($output)
    }
    // acg post query(single)
    function get_acg_posts($the_cat, $pre_cat=false, $limit=99){
        $output = '';
        global $post, $lazysrc, $loadimg;
        $acg_slug = get_cat_by_template('acg','slug');
        $acg_single_sw = get_option('site_single_switcher');
        $target = "_blank";
        $rel = "";
        if($acg_single_sw){
            $includes = get_option('site_single_includes');
            $acg_single_sw = in_array($acg_slug, explode(',', $includes));
            if($acg_single_sw){
                $target = "_self";
                $rel = "nofollow";
            }
        }
        $sub_cat = current_slug()!=$pre_cat ? 'subcat' : '';
        $cat_slug = $the_cat->slug;
        // start acg query
        $acg_query = new WP_Query(array_filter(array(
            'cat' => $the_cat->term_id,  //$acg_cat
            'meta_key' => 'post_orderby',
            'orderby' => array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC',
                'modified' => 'DESC'
            ),
            'posts_per_page' => $limit,
        )));
        $output .= '<div class="inbox-clip wow fadeInUp '.$sub_cat.'"><h2 id="'.$cat_slug.'">'.$the_cat->name.'<sup> '.$cat_slug.' </sup></h2></div><div class="info loadbox flexboxes">';
        while ($acg_query->have_posts()):
            $acg_query->the_post();
            $post_feeling = get_post_meta($post->ID, "post_feeling", true);
            $post_source = get_post_meta($post->ID, "post_source", true);
            $post_rcmd = get_post_meta($post->ID, "post_rcmd", true);
            $post_rating = get_post_meta($post->ID, "post_rating", true);
            $postimg = get_postimg(0,$post->ID,true);
            if($lazysrc!='src'){
                $lazyhold = 'data-src="'.$postimg.'"';
                $postimg = $loadimg;
            }
            $href = $post_source ? $post_source : ($acg_single_sw ? "javascript:;" : get_the_permalink());
            $output .= '<div class="inbox flexboxes" id="pid_'.get_the_ID().'"><div class="inbox-headside flexboxes"><img '.$lazyhold.' src="'.$postimg.'" alt="'.$post_feeling.'" crossorigin="Anonymous" /><span class="author">'.$post_feeling.'</span></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="'.$href.'" target="'.$target.'" rel="'.$rel.'">'.get_the_title().'</a></h4></span><span class="lowside-description"><p>'.custom_excerpt(66,true).'</p></span>';
            if($post_rcmd){
                $rcmd_title = 'Personal Recommends';
                $rcmd_class = '';
                $rcmd_text = '荐';
                if($post_rating){
                    $rcmd_title = 'GOLD Recommendation';
                    $rcmd_class = ' both';
                    $rcmd_text = $post_rating;
                }
                $output .= '<div class="game-ratings gs'.$rcmd_class.'"><div class="gamespot" title="'.$rcmd_title.'"><div class="range Essential RSBIndex"><span id="before"></span><span id="after"></span></div><span id="spot"><h3>'.$rcmd_text.'</h3></span></div></div>';
            }else{
                if($post_rating) $output .=  '<div class="game-ratings ign"><div class="ign hexagon" title="IGN High Grades"><h3>'.$post_rating.'</h3></div></div>';
            }
            $output .= '</div></div>';
        endwhile;
        wp_reset_query();  // reset wp query incase following code occured query err
        unset($post, $lazysrc, $loadimg);
        // 单独判断当前查询文章数量
        if(get_option('site_async_switcher')){
            $async_array = explode(',', get_option('site_async_includes'));
            if(in_array($acg_slug, $async_array)){
                $cid = $the_cat->term_id;// $cat_name = current_slug(); //$acg_query->query['cat']
                $slug = $the_cat->slug;
                // preset all acg query
                $all_query = new WP_Query(array_filter(array(
                    'cat' => $the_cat->term_id,
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'no_found_rows' => true,
                )));
                $all_count = $all_query->post_count;
                $posts_count = $acg_query->post_count;  //count($acg_query->posts) //mailto:'.get_bloginfo("admin_email").' 发送邮件，荐你所见
                $disable_statu = $posts_count==$all_count ? ' disabled' : false; //>=
                $output .= '<div class="inbox more flexboxes"><div class="inbox-more flexboxes'.$disable_statu.'"><a class="load-more" href="javascript:;" data-counts="'.$all_count.'" data-load="'.$posts_count.'" data-click="0" data-cid="'.$cid.'" data-nonce="'.wp_create_nonce($slug."_posts_ajax_nonce").'" data-cat="'.strtoupper($slug).'" title="加载更多 '.$the_cat->name.'"></a></div></div>';
                unset($cid, $slug, $all_count, $posts_count, $disable_statu);
            }
        }
        $output .= '</div>';
        return $output;
    };
    
    // wp自定义（含置顶无分页）查询函数
    function get_download_posts($cats, $order=1){
        $output = '';
        $cats_count = count($cats);
        $dload_single_sw = get_option('site_single_switcher');
        if($dload_single_sw){
            $includes = get_option('site_single_includes');
            $dload_slug = get_cat_by_template('download','slug');
            $dload_single_sw = in_array($dload_slug, explode(',', $includes));
        }
        for($i=0;$i<$cats_count;$i++){
            $term_order = get_term_meta($cats[$i]->term_id, 'seo_order', true);
            // print_r($term_order);
            if($term_order==$order){
                $each_cat = $cats[$i];
                $cat_name = $each_cat->name;
                $cat_slug = $each_cat->slug;
                $cat_id = $each_cat->term_id;
                // 'category='.$cat_id.'&number=1&orderby'
                $cat_first_post = get_posts(array(
                    'cat' => $cat_id,
                    'meta_key' => 'post_orderby',
                    'orderby' => array(
                        'meta_value_num' => 'DESC',
                        'date' => 'DESC',
                        'modified' => 'DESC'
                    ),
                    'number' => 1,
                ));
                $cat_poster = get_term_meta($cat_id, 'seo_image', true );
                if(!$cat_poster) $cat_poster = get_postimg(0, $cat_first_post[0]->ID, true); //get_option('site_bgimg');
                $output .= '<div class="dld_box '.$cat_slug.'"><div class="dld_box_wrap"><div class="box_up preCover"><span style="background:url('.$cat_poster.') center center /cover"><a href="javascript:;"><h3> '.$cat_name.' </h3><i> '.strtoupper($cat_slug).'</i><em></em></a></span></div><div class="box_down"><ul>';
                    //setup query
                    global $post, $lazysrc, $loadimg;
                    $left_query = new WP_Query(array_filter(array(
                        'cat' => $cat_id,
                        'meta_key' => 'post_orderby',
                        'orderby' => array(
                            'meta_value_num' => 'DESC',
                            'date' => 'DESC',
                            'modified' => 'DESC'
                        ),
                        'posts_per_page' => 99 //get_option('posts_per_page'),  //use left_query counts
                    )));
                    while ($left_query->have_posts()):
                        $left_query->the_post();
                        $link = get_post_meta($post->ID, "post_feeling", true);
                        $postimg = get_postimg(0,$post->ID,true);
                        if($lazysrc!='src'){
                            $lazyhold = 'data-src="'.$postimg.'"';
                            $postimg = $loadimg;
                        }
                        $href = $link ? $link : 'javascript:void(0);';
                        $target = $link ? '_blank' : '_self';
                        $class_disabled  = !$link ? 'disabled ' : false;
                        $class_topset = get_post_meta($post->ID, 'post_orderby', true)>1 ? 'topset' : false;
                        $single_link = !$dload_single_sw ? '<a href="'.get_the_permalink().'" target="_blank" style="right:70px;">详情</a>' : '';
                        $output .= '<li class="'.$class_disabled.$class_topset.'"><div class="details"><a href="'.$href.'" target="'.$target.'" rel="nofollow" title="下载附件"><img '.$lazyhold.' src="'.$postimg.'" alt="poster" /></a><div class="desc">'.get_the_title().'<a href="'.$href.'" target="'.$target.'" rel="nofollow">下载附件</a>'.$single_link.'</div></div></li>';
                    endwhile;
                    wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
                    unset($post, $lazysrc, $loadimg);
                $output .= '</ul></div></div></div>';
            }
        };
        return $output;
    };
    
    // 倒计时挂件
    function the_countdown_widget($date=false, $title=false, $bgimg=false){
        if(get_option('site_countdown_switcher')){
            $date = $date ? $date : get_option('site_countdown_date');
            $title = $title ? $title : get_option('site_countdown_title');
            $bgimg = $bgimg ? $bgimg : replace_video_url(get_option('site_countdown_bgimg'), 'sidebar');
            $countDate = date('Y/m/d,H:i:s',strtotime($date));
            $countTitle = explode('/', $title);
    ?>
            <style>.news-ppt div,#countdown:before{border-radius:inherit}.countdown-box{width:100%;height:100%;min-height:160px;position:relative;}/* 新年侧边栏 */ #countdown {height:100%;padding: 1rem;box-sizing: border-box;position: absolute;top: 0;left: 0;width: 100%;background-size: cover;background-position: center;border-radius:var(--radius)}#countdown * {position: relative;color: white!important;/*line-height: 1.2;*/}#countdown p,#countdown div{position:relative;z-index:9;}#countdown p{text-align: left;margin: auto;font-size: small;}#countdown p.title{font-weight:bold;}#countdown p.today{opacity: .75;font-size: 12px;position: inherit;bottom: 15px;right: 15px;}#countdown .time {font-weight: bold;text-align: center;width:100%;position: inherit;top: 50%;left: 50%;transform: translate(-50%,-50%);}#countdown .time, #countdown .timesup {font-size: 3.5rem;display: block;/*margin: 1rem 0;*/}#countdown .day {font-size: 4rem;}@keyframes typing{0%{opacity:0;}50%{opacity:1;}100%{opacity:0;}}#countdown .day .unit {font-size: 1rem;display:inline;animation: typing ease .8s infinite;-webkit-animation: typing ease .8s infinite;opacity:0;}#countdown:before{content: "";position: inherit;left: 0;top: 0;height: 100%;width: 100%;background-color: rgba(0, 0, 0, .36);z-index:1;}.countdown-box video{width: 100%;height: 100%;position: absolute!important;top: 0;left: 0;object-fit: cover;border-radius:inherit;}</style>
            <div class="countdown-box" style="margin-bottom: 15px">
                <div id="countdown" style="background-image:url(<?php //echo $bgimg; ?>)">
                    <video src="<?php echo $bgimg; ?>" poster="<?php echo $bgimg; ?>" preload="" autoplay="" muted="muted" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>
                    <p class="title"><?php echo $countTitle[0]; ?></p>
                    <div class="time"></div>
                    <p class="today"></p>
                </div>
            </div>
            <script>
                const main = document.querySelector('#countdown'),
                      target = main.querySelector('.time'),
                      title = main.querySelector('.title'),
                      today = main.querySelector('.today'),
                      weeks = ['日','一','二','三','四','五','六'],
                      fillZero = function(i){
                          return i < 10 ? "0"+i : i;
                      },
                      endup = "<?php echo $countTitle[1]; ?>";
                var nowtime = new Date(),
                    endtime = new Date("<?php echo $countDate; ?>"),
                    result = parseInt((endtime.getTime() - nowtime.getTime()) / 1000),
                    day = parseInt(result / (24 * 60 * 60));
                today.innerHTML = `${nowtime.getMonth()+1}月${nowtime.getDate()}日 &nbsp;星期${weeks[nowtime.getDay()]}`;
                if(parseInt(day)<=0 && result>0){
                    (function countDown() {
                        // console.log('counting..')
                        let now = new Date(),
                            res = parseInt((endtime.getTime() - now.getTime()) / 1000),
                            hour = fillZero(parseInt(res / (60 * 60) % 24)),
                            min = fillZero(parseInt(res / 60 % 60)),
                            sec = fillZero(parseInt(res % 60)),
                            text = hour>0 ? hour+':'+min+':'+sec : (min>0 ? min+':'+sec : sec);
                        target.innerHTML = '<span class="day">'+text+'</span>';
                        if(res <= 0) {
                            title.innerHTML = "TIME'S UP!";
                            target.innerHTML = "<span class='timesup'>"+endup+"</span>";
                            clearTimeout(countDown);
                            countDown = null;
                            return;
                        }
                        setTimeout(countDown, 1000);
                    })();
                }else{
                    target.innerHTML = `<span class="day">${fillZero(day)}<span class="unit">天</span></span>`;
                    if(result <= 0) {
                        title.innerHTML = "TIME'S UP!";
                        target.innerHTML = "<span class='timesup'>"+endup+"</span>";
                    }
                }
            </script>
<?php
        }
    }
    
    // search/tag page posts with styles
    function the_posts_with_styles($queryString){
        global $post, $lazysrc, $loadimg, $wp_query, $src_cdn;
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
            .win-content article.news-window{padding:0;border:1px solid rgb(100 100 100 / 10%);margin-bottom:25px}
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
                        <article class="<?php if($post_orderby>1) echo 'topset'; ?> news-window icom wow" data-wow-delay="0.1s" post-orderby="<?php echo $post_orderby; ?>">
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
                                                    echo '<span class="valine-comment-count icom" data-xid="'.parse_url(get_the_permalink(), PHP_URL_PATH).'"> '.$count.'</span>';
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
            $pages = paginate_links(array(
                'prev_text' => __('上一页'),
                'next_text' => __('下一页'),
                'type' => 'plaintext',
                'screen_reader_text' => null,
                'total' => $wp_query -> max_num_pages,  //总页数
                'current' => max(1, get_query_var('paged')), //当前页数
            ));
            unset($post, $lazysrc, $loadimg, $wp_query);
            if($pages) echo '<div class="pageSwitcher">'.$pages.'</div>';
        }else{
            echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.$queryString.' </h1></div>';  //<b>'.current_slug(true).'</b> 
        }
    }
    
    /* ------------------------------------------------------------------------ *
     * 其他功能函数
     * ------------------------------------------------------------------------ */
     
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
    
    // 获取文章浏览量
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
    // 设置文章浏览量
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
    
    // 上一页评论
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
    // 下一页评论
    function get_next_comments_html( $label = '', $max_page = 0 ) {
        if ( ! is_singular() ) {
            return;
        }
        $page = get_query_var( 'cpage' );
        if ( ! $page ) {
            $page = 1;
        }
        $nextpage = (int) $page + 1;
        if ( empty( $max_page ) ) {
            global $wp_query;
            $max_page = $wp_query->max_num_comment_pages;
            unset($wp_query);
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
    
?>
