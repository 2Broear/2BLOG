<?php

    /***** global variables   ******/
    $upload_url = content_url() . '/uploads';
    $cdn_switch = get_option('site_cdn_switcher');
    $images_cdn = get_option('site_cdn_img');
    // !!! src_cdn & img_cdn MUST be declear after $cdn_switch & $images_cdn
    $src_cdn = custom_cdn_src('src', true);
    $img_cdn = custom_cdn_src('img', true);
    $lazysrc = 'src';
    $loadimg = $img_cdn . '/images/loading_3_color_tp.png';
    $videos_cdn_arr = explode(',',trim(get_option('site_cdn_vdo_includes')));
    $template_path = '/inc/templates/pages';
    
    /*
     *---------------------------------------------------------------------------------------------------------------------------------
     * extra_setup
     *---------------------------------------------------------------------------------------------------------------------------------
    */
    
    // 加载站点头部
    function get_head(){
        load_theme_partial('/inc/head.php', 'require');
    }
    // 加载站点尾部
    function get_foot(){
        load_theme_partial('/inc/foot.php', 'require');
    }
    // 获取主题信息
    function get_theme_info($type='Version'){ //Name
        $my_theme = wp_get_theme();
        return $my_theme->get($type);
    }
    // 返回站点 favicon
    function get_site_favico(){
        $site_favico = get_site_icon_url();
        if($site_favico){
            global $images_cdn, $upload_url;
            $site_favico = $images_cdn ? preg_replace('/('.preg_quote($upload_url,'/').')(.*?)/i', $images_cdn."\${2}", $site_favico) : $site_favico;
        }else{
            global $img_cdn;
            $site_favico = $img_cdn.'/images/favicon/favicon.ico';
        }
        return $site_favico;
    }
    // 返回站点标题（分类、文章、归档、搜索..）
    function get_site_title($surfix=true){
        $nick = get_option('site_nick');
        $name = !is_home() ? ' - '.get_bloginfo('name') : '';
        $surfix = $surfix ? ($nick ? " | " . get_option('site_nick') . $name : $nick) : "";
        $title = '';
        switch (true) {
            case is_search():
                $cid = check_request_param('cid');
                if($cid){
                    $year = check_request_param('year');
                    $cat_slug = get_category($cid)->slug;
                    $title = $year ? $cat_slug.' of '.$year.$surfix : $cat_slug.' in archives'.$surfix;
                }else{
                    $title = 'Search result for "' . esc_html(get_search_query()) .'"';
                }
                break;
            case is_category() || is_page():
                global $cat;
                $seo_title = get_term_meta($cat, 'seo_title', true); //single_cat_title();
                $title = $seo_title ? $seo_title . $surfix : get_the_title() . $surfix;
                break;
            case is_tag():
                $title = 'Posts of tag "'. single_tag_title('',false) .'"' . $surfix;
                break;
            case is_archive():
                global $wp_query;
                // $founds = $wp_query->found_posts;
                $dates = $wp_query->query;
                $date_mon = array_key_exists('monthnum',$dates) ? ' - '.$dates['monthnum'].' ' : '';
                $title = 'Archives of ' . $dates['year'] . $date_mon . $surfix; //$founds . 
                break;
            case is_home():
                $title .= get_bloginfo('name') . $surfix . " - " . get_bloginfo('description');
                break;
            case is_single():
                $the_title = get_the_title();
                // $title = in_category(array('notes','weblog')) ? $the_title . $surfix . " - " . get_the_category()[0]->name : $the_title;
                $title = $surfix ? $the_title . $surfix : $the_title; // get_between_string(0, " ", $the_title)
                break;
            default:
                $title = "NOTHING MATCHED" . $surfix;
                break;
        }
        return $title;
    }
    // 返回站点关键词（标签）
    function get_site_keywords(){
        $keywords = get_option('site_keywords', "no keywords yet");
        switch (true) {
            case is_category() || is_page():
                global $cat;
                $seo_keywords = get_term_meta($cat, 'seo_keywords', true);
                $keywords = $seo_keywords ? $seo_keywords : $keywords;
                break;
            case is_single():
                global $post;
                $site_title = get_site_title(false);
                $seo_tags = get_tag_list($post->ID, 99, ',', true);
                $keywords = $seo_tags ? $site_title . ',' . $seo_tags : $site_title;
                break;
            default:
                break;
        }
        return $keywords;
    }
    // 返回站点描述（摘要）
    function get_site_description($cat=false){
        $desc = get_option('site_description', "no descriptions yet");
        switch (true) {
            case is_category():
                if(get_term_meta($cat, 'seo_description', true)) {
                    if(!$cat) global $cat;
                    $desc = get_term_meta($cat, 'seo_description', true);
                }
                break;
            case is_single():
                $desc = get_ai_abstract();
                break;
            default:
                break;
        }
        return preg_replace('/\n/',"", $desc); //trim($desc);
    }
    
    function get_ai_abstract(){
        if(get_option('site_chatgpt_desc_sw') && in_chatgpt_cat()){
            $dir = get_option('site_chatgpt_dir') ? get_option('site_chatgpt_dir').'/' : '';
            include_once get_template_directory() . '/plugin/'.$dir.'gpt_data.php';  // 读取文件记录
            global $post;
            $pid = $post->ID;
            if(isset($cached_post['chat_pid_'.$pid]['error'])){
                $desc = $cached_post['chat_pid_'.$pid]['error']['message'];
            }else if(isset($cached_post['chat_pid_'.$pid]['choices'][0])){
                $desc = isset($cached_post['chat_pid_'.$pid]['choices'][0]['message']) ? $cached_post['chat_pid_'.$pid]['choices'][0]['message']['content'] : $cached_post['chat_pid_'.$pid]['choices'][0]['text'];
            }
            $desc = mb_substr($desc, 0, 666) . '...';
        }else{
            $desc = custom_excerpt(999, true); //get_the_excerpt();
        }
        return $desc;
    }
    
    // 返回指定文章标签
    function get_tag_list($pid=0, $max=3, $slice="、", $string=false){
        $res_list = false;
        if(!$pid) {
            global $post;
            $pid = $post->ID;
        }
        $tags_list = get_the_tags($pid);
        if(!$tags_list) {
            return $res_list;
        }
        $tag_count = count($tags_list);
        for($i=0;$i<$max;$i++){
            $tag_set = isset($tags_list[$i]) ? $tags_list[$i] : false; //array_key_exists($i,$tags_list)
            if(!$tag_set){
                return $res_list;
            }
            $tag_dots = $max<$tag_count ? ($i<$max-1 ? $slice : '') : ($i<$tag_count-1 ? $slice : '');
            $tag_name = $tag_set->name;
            $res_list .= $string ? $tag_name.$tag_dots : '<a href="'.get_bloginfo("url").'/tag/'.$tag_name.'" data-count="'.$tag_set->count.'" target="_blank" rel="tag">'.$tag_name.'</a>'.$tag_dots;
        }
        return $res_list;
    }
    
    // 自定义文章摘要
    function custom_excerpt($length=88, $var=false, $pid=0){
        // $res = in_chatgpt_cat()&&is_single() ? get_between_string('文章摘要chatGPT standby chatGPT responsing..', $length, get_the_excerpt()) : mb_substr(get_the_excerpt(), 0, $length);
        $excerpt = $pid ? get_the_excerpt($pid) : get_the_excerpt();
        $res = get_between_string('Standby API Responsing..', $length, $excerpt);  // chinese only
        $res = trim($res) . '...';
        if($var){
            return $res;
        }
        echo $res;
    }
    function wpdocs_custom_excerpt_length( $length ) {
        return 123;
    }
    function wpdocs_excerpt_more( $more ) {
        return '...';
    }
    add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );
    add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );
    
    //启用cdn加速(指定src/img)
    function custom_cdn_src($holder='src', $var=false){
        global $cdn_switch, $images_cdn;
        $default_src = get_bloginfo('template_directory');
        $cdn_src = get_option('site_cdn_src');
        $cdn_img = $images_cdn; //get_option('site_cdn_img');
        $cdn_api = get_option('site_cdn_api');
        if($cdn_switch&&$holder){ // set $holder as false for $default_src manually
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
    // 过滤文章内容 CDN 路径（新增 video 开关）
    if($cdn_switch){
        add_filter('the_content', 'replace_cdn_img', 9);
        function replace_cdn_img($content) {
            global $images_cdn, $upload_url, $videos_cdn_arr;
            // return str_replace('="'.$upload_url, '="'.$images_cdn, $content);
            // 控制全站视频加速开关（默认替换$images_cdn为$upload_url）
            $content = in_array('article', $videos_cdn_arr) ? preg_replace('/(<video.*src=.*)('.preg_quote($upload_url,'/').')(.*>)/i', "\${1}$images_cdn\${3}", $content) : preg_replace('/(<video.*src=.*)('.preg_quote($images_cdn,'/').')(.*>)/i', "\${1}$upload_url\${3}", $content);  // video filter works fine. //strpos($videos_cdn_page, 'article')!==false
            $res = preg_replace('/(<img.+src=\"?.+)('.preg_quote($upload_url,'/').')(.+\.*\"?.+>)/i', "\${1}".$images_cdn."\${3}", $content);
            // unset($images_cdn, $upload_url, $videos_cdn_arr);
            return $res;  //http://blog.iis7.com/article/53278.html
        }
        // 替换后台媒体库图片路径（目前无法自定义每个图像url）https://wordpress.stackexchange.com/questions/189704/is-it-possible-to-change-image-urls-by-hooks
        function wpse_change_featured_img_url(){
            global $images_cdn;
            return $images_cdn ? $images_cdn : $upload_url;  //get_option('site_cdn_img')
        }
        add_filter( 'pre_option_upload_url_path', 'wpse_change_featured_img_url' );
    }
    
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
            // unset($images_cdn, $upload_url, $videos_cdn_arr, $cat, $cdn_switch);
            return $url;
        }
    }
    // unset($videos_cdn_arr);
    
    //兼容gallery获取post内容指定图片（视频海报）
    function get_postimg($index=0, $pid=0, $default=false) {
        global $post, $images_cdn, $upload_url, $cdn_switch, $img_cdn;
        $_posts = $pid ? get_post($pid) : $post;
        $ret = array();
        if(has_post_thumbnail($_posts)){
            $ret = [get_the_post_thumbnail_url($_posts)];
        }else{
            $posts_content = $_posts->post_content;
            preg_match_all('/\<img.*src=("[^"]*")/i', $posts_content, $image);
            foreach($image[0] as $i => $v) {
                $ret[] = trim($image[1][$i],'"');
            };
            //未匹配到图片或调用值超出图片数量范围则输出（视频海报或）默认图
            if(count($ret)<=0 || count($ret)<=$index) {
                preg_match_all('/\<video.*poster=("[^"]*")/i', $posts_content, $video);
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
            $res = str_replace($upload_url, $images_cdn, $result);
        }
        unset($post, $images_cdn, $upload_url, $cdn_switch);
        return $res;
    }
    // 分类背景图/视频海报
    function get_meta_image($cid, $preset=false){
        global $img_cdn;
        $metaimg = get_term_meta($cid, 'seo_image', true);  //$page_cat->term_id
        $result = $metaimg ? $metaimg : ($preset ? $preset : $img_cdn.'/images/default.jpg');  //get_option('site_bgimg')
        global $images_cdn, $upload_url, $cdn_switch;
        $res = $result;
        if($cdn_switch){
            $res = preg_replace('/(<img.+src=\"?.+)('.preg_quote($upload_url,'/').')(.+\.*\"?.+>)/i', "\${1}".$images_cdn."\${3}", $result);
        }
        return $res;
    }
    // unset($images_cdn, $upload_url, $cdn_switch);
    
    // 获取当前分类、页面、文章slug
    function current_slug($upper=false, $cats=false, $posts=false){
        global $cat, $post;  //变量提升
        $cat = $cats ? $cats : $cat;
        $post = $posts ? $posts : $post;
        $slug = "NOT MATCHED";
        switch (true) {
            case is_archive():
                $slug = "ARCHIVE";
                break;
            case is_search():
                $slug = "SEARCH";
                break;
            case is_tag():
                $slug = "TAGS";
                break;
            case is_single(): //in_category(array('news','notes')):
                $slug = "ARTICLE";
                break;
            case is_home():
                $slug = "INDEX";
                break;
            case is_page():
                $slug = $upper ? strtoupper($post->post_name) : strtolower($post->post_name);
                break;
            case is_category():
                $slug = $upper ? strtoupper(get_category($cat)->slug) : strtolower(get_category($cat)->slug);
                break;
            default:
                break;
        };
        // unset($cat, $post);
        return $slug;
    }
    
    /******   wp_query handlers *****/
    
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
    //通过meta_query获取指定id自定义排序输出子级
    function meta_query_categories($cid=0, $order='ASC', $orderby='seo_order'){
        return array(
            'child_of' => $cid, 'parent' => $cid, 'hide_empty' => 0, 'order'=>$order , 'orderby' => 'order_clause',
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
        if($childs) {
            unset($query_array['parent']);
        }
        if($exclude) {
            $query_array['exclude'] = $current_cid;
        }
        return get_categories($query_array);
    }
    
    /*--------------------------------------------------------------------------
     *  $wpdb Queries
     *--------------------------------------------------------------------------
    */
    // 模糊匹配文章别名返回文章id
    function get_post_like_slug($post_slug) {
        global $wpdb;
        $post_slug = '%' . $post_slug . '%';
        $pid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name LIKE %s", $post_slug));
        // unset($wpdb);
        return get_post($pid);
    }
    
    // 返回年度分类统计数量
    function get_yearly_cat_count($year, $cid, $limit=999){
        $year_posts = get_posts(array(
            "year"        => $year,
            "category"    => $cid,
            "numberposts" => $limit,
        ));
        return count($year_posts);
    }
    // 返回年度文章id
    function get_wpdb_yearly_pids($year=false, $limit=99, $offset=0){
        global $wpdb;
        $year = $year ? $year : gmdate('Y', time() + 3600*8); //date('Y');
        $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = $year ORDER BY post_date DESC LIMIT $limit OFFSET $offset "); // !!!LIMIT & OFFSET must type of NUMBER!!!
        // unset($wpdb);
        return $res;
    }
    // 返回年度文章id
    function get_wpdb_yearly_pids_by_cid($cid=0, $year=0, $limit=99, $offset=0){
        global $wpdb;
        $year = $year ? $year : gmdate('Y', time() + 3600*8);
        $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts,wp_term_relationships WHERE ID = object_id AND post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = $year AND wp_term_relationships.term_taxonomy_id = $cid ORDER BY post_date DESC LIMIT $limit OFFSET $offset ");
        // unset($wpdb);
        return $res;
    }
    // 返回指定分类下文章id
    function get_wpdb_pids_by_cid($cid=0, $limit=99, $offset=0, $year=false){
        global $wpdb;
        if($year){
            $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts,wp_term_relationships WHERE ID = object_id AND post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = $year AND wp_term_relationships.term_taxonomy_id = $cid ORDER BY post_date DESC LIMIT $limit OFFSET $offset ");
        }else{
            $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts,wp_term_relationships WHERE ID = object_id AND post_type = 'post' AND post_status = 'publish' AND wp_term_relationships.term_taxonomy_id = $cid ORDER BY post_date DESC LIMIT $limit OFFSET $offset "); //(post_status = 'publish' OR post_status = 'private') //instance_type in ("m5.4xlarge","r5.large","r5.xlarge");
        }
        // unset($wpdb);
        return $res;
    }
    
    // 自定义 wpdb 查询函数
    function wpdb_postmeta_query($data, $key, $val){
        global $wpdb;
        $res = $wpdb->get_var("SELECT $data FROM $wpdb->postmeta WHERE $key = '$val'");
        // unset($wpdb);
        return $res;
    }
    // 获取自定义页面所属分类term_id
    function get_page_cat_id($slug){
        global $wpdb;
        $res = $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE slug = '$slug'");
        // unset($wpdb);
        return $res;
    }
    // 获取自定义页面内容
    function the_page_id($slug){
        global $wpdb;
        $res = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        // unset($wpdb);
        return $res;
    }
    // 输出指定文章别名内容
    function the_page_content($slug){
        global $wpdb;
        $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        // unset($wpdb);
        echo get_page($id)->post_content;// if(is_page()) echo get_page($id)->post_content;else echo '<p style="color:red">页面 '.current_slug().' 不存在，无法调用该页面内容。</p>';
    }
    
    /*--------------------------------------------------------------------------
     * Specified funcs.
     *--------------------------------------------------------------------------
    */
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
    // Disable SrcSet
    function remove_max_srcset_image_width( $max_width ) {
        return 1;
    }
    add_filter( 'max_srcset_image_width', 'remove_max_srcset_image_width' );
    /**
     * Kullanicinin kullandigi internet tarayici bilgisini alir.
     * 
     * @since 2.0
     */
    // 设置文章点赞
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
    
    /*
     *--------------------------------------------------------------------------
     * Wordpress ajax setup.
     *--------------------------------------------------------------------------
    */
    // 检查并返回 xhr 请求携带参数
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
                $res = isset($_COOKIE[$param]) ? $_COOKIE[$param] : false;
                break;
        }
        return esc_html($res);
    }
    // Ajax XHR request handler
    function ajaxGetPosts(){
        $cid = check_request_param('cid');
        $type = check_request_param('type');
        $limit = check_request_param('limit');
        $offset = check_request_param('offset');
        // $private = 'Accesing Private Content';
        $prefix = get_category($cid)->slug;
        if($type==='archive'){
            $prefix = $_GET['key'] ? $_GET['key'] : $_POST['key'];
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
                    case 'goods':
                    case 'acg':
                        $post_class->link = get_the_permalink($pid);
                        $post_class->poster = get_postimg(0, $pid, true);
                        $post_class->excerpt = custom_excerpt(66, true, $pid); // get_the_excerpt($pid)
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
    
    
    
    /*
     *---------------------------------------------------------------------------------------------------------------------------------
     * theme_setup
     *---------------------------------------------------------------------------------------------------------------------------------
    */
    
    
    
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
    //通过邮箱匹配（gravatar/qq）头像（默认获取后台gravatar镜像源）
    function match_mail_avatar($user_mail){
        preg_match_all('/@qq.com/i', $user_mail, $qq_matches);
        preg_match_all('/(.*?)@/i', $user_mail, $mail_account);
        $avatar_mirror = get_option('site_avatar_mirror','//gravatar.com/');
        if($qq_matches[0]) $avatar_src='https://q.qlogo.cn/headimg_dl?dst_uin='.$mail_account[1][0].'&spec=640';else $avatar_src='https:'.$avatar_mirror.'avatar/'.md5($user_mail).'?s=100';
        return $avatar_src;
    }
    
    // 更新 sitemap 站点地图
    if(get_option('site_map_switcher')){
        function update_sitemap() {
            load_theme_partial('/plugin/sitemap.php');
        }
        add_action('publish_post','update_sitemap');
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
    
    /*****   WP Comment email/wechat notify, ajax/pagination etc   *****/
    
    // 邮件 SMTP 初始化
    if(get_option('site_smtp_switcher')){
        add_action('phpmailer_init', 'mail_smtp');
        function mail_smtp( $phpmailer ) {
            $senderEmail = get_option('site_smtp_mail');
        	$phpmailer->FromName = get_bloginfo('name'); // 发件人昵称
        	$phpmailer->Host = get_option('site_smtp_host'); // 邮箱SMTP服务器
        	$phpmailer->Port = 465; // SMTP端口，不需要改
        	$phpmailer->Username = $senderEmail; // 邮箱账户
        	$phpmailer->Password = get_option('site_smtp_pswd'); // 此处填写邮箱生成的授权码 u5LZ4xWEuuoJdZJX
        	$phpmailer->From = $senderEmail; // 邮箱账户同上
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
// show wp_mail() errors
add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
function onMailError( $wp_error ) {
	echo "<pre>";
    print_r($wp_error);
    echo "</pre>";
} 
                wp_mail($_POST['toemail'],'ajax e-mail sent ok','this mail sent from 2blog-settings SMTP e-mail sending test.');
                echo '测试邮件已发送';
                update_option('site_smtp_state', 1);
                die();
            }
            echo 'e-mail sending error'; //update_option('site_smtp_state',0);
            die();
        }
    }
    
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
    
    // wp评论邮件提醒（博主）手动开启
    if(get_option('site_wpmail_switcher') && get_option('site_third_comments')=='Wordpress'){
        function wp_notify_admin_mail( $comment_id, $comment_approved ) {
            global $img_cdn;
            $comment = get_comment( $comment_id );
            $admin_mail = get_bloginfo('admin_email'); //get_option('site_smtp_mail', get_bloginfo('admin_email'));
            $user_mail = $comment->comment_author_email;
            $title = ' 「' . get_the_title($comment->comment_post_ID) . '」 收到一条来自 '.$comment->comment_author.' 的留言！';
            $body = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="'.$img_cdn.'/images/google.gif"><h2 class="head"><span class="title">「'. get_option("blogname") .'」上有一条新评论！</span><p><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank">点击查看</a></p></h2><div class="body"><p><strong>' . trim($comment->comment_author) . '：</strong></p><div class="content"><p><a class="at"href="#624a75eb1122b910ec549633">' . trim($comment->comment_content) . '</a></p></div></div></div>';
            $header = "\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
            // 当前用户不为博主时发送评论提醒邮件
            if($user_mail!=$admin_mail) wp_mail($admin_mail, $title, $body, $header);
            
        }
        add_action('comment_post', 'wp_notify_admin_mail', 10, 2);
    }
    
    // wp评论邮件提醒（访客）始终开启 // https://www.ziyouwu.com/archives/1615.html
    function wp_notify_guest_mail($comment_id) {
        $admin_mail = get_bloginfo('admin_email'); //get_option('site_smtp_mail', get_bloginfo('admin_email'));
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
    
    /*
     *---------------------------------------------------------------------------------------------------------------------------------
     * extend_setup
     *---------------------------------------------------------------------------------------------------------------------------------
    */
    
    // 加载主题部件（注：函数内部 include 文件内函数无法被外部作用域识别）
    function load_theme_partial($file='/', $method='include', $relative_path=true, $output_buffer=false){
        try {
            $file = $relative_path ? get_template_directory() . $file : $file;
            if($output_buffer) {
                ob_start();
                include $file;
                $content = ob_get_clean();
                print_r($content);
                return $content;
            }
            switch ($method) {
                case 'require':
                case 'require_once':
                    require_once($file);
                    break;
                case 'include':
                default:
                    include_once($file);
                    break;
            }
        } catch (Exception $err) {
            throw new Error($err->getMessage());
        }
    }
    // custom_template_path for custom page templates(disable filter will NOT able to specific template in page)
    add_filter('theme_page_templates', 'custom_template_path');
    function custom_template_path($templates) {
        global $template_path;
        $dir = get_template_directory() . $template_path;
        $templates = scan_templates_dir($templates, $dir);
        return $templates;
    }
    function scan_templates_dir($templates, $dir=false) {
        global $template_path;
        $dir = $dir ? $dir : get_template_directory() . $template_path;
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $templates = scan_templates_dir($templates, $path);
            } else {
                $file_name = basename($file);
                // MUST specific $template_slug for custom use
                $template_slug = str_replace(get_template_directory(), '', $path);
                $template_data = get_file_data($path, array('Template Name' => 'Template Name')); // 获取模板文件的头部信息
                if (!empty($template_data['Template Name'])) {
                    $template_name = $template_data['Template Name'];
                    // $template_slug = sanitize_title($template_name); // 使用模板名称作为数组键
                    $templates[$template_slug] = $template_name; // 将模板名称存储到数组中
                }else{
                    $templates[$template_slug] = $file_name;
                }
            }
        }
        asort($templates); //sort($templates); ksort($templates);
        return $templates;
    }
    
    // 通过分类模板名称获取绑定的分类别名
    function get_template_bind_cat($template=false){
        global $wpdb, $template_path;
        $template = $template_path . '/category/' . $template; //prefix for custom templates path
        $template_term_id = $wpdb->get_var("SELECT term_id FROM $wpdb->termmeta WHERE meta_value = '$template'");
        // return !get_category($template_term_id)->errors ? get_category($template_term_id) : get_category(1);
        return get_category($template_term_id);
    }
    // get bind category-template cat by specific binded-temp post_id
    function get_cat_by_template($temp='news', $parm=false){
        $cats = get_template_bind_cat('category-'.$temp.'.php');
        return !$cats->errors ? ($parm ? $cats->$parm : $cats) : false;
    }
    
    function get_between_string($begin, $end, $str){
        if(is_numeric($begin)){
            $b = $begin;
        }elseif(is_string($begin)){
            $b = strpos($str, $begin)!==false ? mb_strpos($str, $begin) + mb_strlen($begin) : 0;
        }
        $strlen = mb_strlen($str);
        $e = $strlen;
        if(is_numeric($end)){
            $e = $end;
        }elseif(is_string($end)){
            $e_pos = $end ? mb_strpos($str, $end) : $strlen;
            $e = $e_pos ? $e_pos - $b : $strlen;
        }
        // return $b.','.$e;
        return mb_substr($str, $b, $e);
    }
    
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
            '/windows nt 11/i'      =>  'Windows 11', // 添加对Windows 11的检测
            '/mac os x 10[\._\d]+/i' => 'macOS', // 添加对macOS的检测
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
            if(preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }
        foreach($browser_array as $regex => $value ) {
            if(preg_match($regex, $user_agent)) {
                $browser = $value;
                break;
            }
        }
        return ['browser' => $browser, 'system' => $os_platform];
    }
    // // 提取图片平均色值(耗时)
    // function extract_images_rgb($url){
    //     $im  =  imagecreatefromstring(file_get_contents($url));
    //     $rgb  =  imagecolorat ( $im ,  10 ,  15 );
    //     $r  = ( $rgb  >>  16 ) &  0xFF ;
    //     $g  = ( $rgb  >>  8 ) &  0xFF ;
    //     $b  =  $rgb  &  0xFF ;
    //     return "$r $g $b";
    //     // 加载图片
    //     // $image = imagecreatefrompng($url) or die('ext format err.');
    //     // // 获取图片中指定位置的颜色
    //     // $rgb = imagecolorat($image, 1, 2);
    //     // // 将rgb值转换为hex值
    //     // $hex = "#".str_pad(dechex($rgb), 6, "0", STR_PAD_LEFT); 
    //     // // 获取rgb
    //     // list($r, $g, $b) = array_map('hexdec', str_split($hex, 2));
    //     // return "$hex";
    // }
    
    // 检查远程url状态码（并发性能问题）
    function get_url_status_by_header($url){
        $headers = get_headers($url);
        if (!$headers) {
            return 0x0;
        }
        preg_match('/\d{3}/', $headers[0], $matches);
        return $matches[0];
    }
    function get_url_status_by_curl($url, $timeout=5){  //with timeout
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        // preg_match("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
        // return ($matches[1] == 200);
        preg_match('/\d{3}/', $data, $matches);
        return $matches ? $matches[0] : 0x0;
    }
?>