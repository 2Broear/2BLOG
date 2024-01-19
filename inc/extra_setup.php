<?php
    /*
     *--------------------------------------------------------------------------
     * global variables.
     *--------------------------------------------------------------------------
    */
    $upload_url = content_url().'/uploads';
    $cdn_switch = get_option('site_cdn_switcher');
    $images_cdn = get_option('site_cdn_img');
    // !!! src_cdn & img_cdn MUST be declear after $cdn_switch & $images_cdn
    $src_cdn = custom_cdn_src('src', true);
    $img_cdn = custom_cdn_src('img', true);
    $lazysrc = 'src';
    $loadimg = $img_cdn.'/images/loading_3_color_tp.png';
    $videos_cdn_page = get_option('site_cdn_vdo_includes');
    $videos_cdn_arr = explode(',',trim($videos_cdn_page));
    /*
     *--------------------------------------------------------------------------
     * Common functions.
     *--------------------------------------------------------------------------
    */
    // 获取主题信息
    function get_theme_info($type='Version'){ //Name
        $my_theme = wp_get_theme();
        return $my_theme->get($type);
    }
    // 加载站点头部
    function get_head(){
        require_once(TEMPLATEPATH. '/inc/head.php');
    }
    // 加载站点尾部
    function get_foot(){
        require_once(TEMPLATEPATH. '/inc/foot.php');
    }
    // 返回站点 favicon
    function get_site_favico(){
        global $img_cdn;
        $site_favico = get_site_icon_url();
        if($site_favico){
            global $images_cdn, $upload_url;
            $site_favico = $images_cdn ? preg_replace('/('.preg_quote($upload_url,'/').')(.*?)/i', $images_cdn."\${2}", $site_favico) : $site_favico;
        }else{
            $site_favico = $img_cdn.'/images/favicon/favicon.ico';
        }
        return $site_favico;
    }
    // 返回站点描述（摘要）
    function get_site_description($cat=false){
        $desc = "";
        switch (true) {
            case is_category():
                if(!$cat) global $cat;
                $desc = get_term_meta($cat, 'seo_description', true);
                break;
            case is_single():
                if(in_chatgpt_cat()){
                    $dir = get_option('site_chatgpt_dir') ? get_option('site_chatgpt_dir').'/' : '';
                    include_once TEMPLATEPATH.'/plugin/'.$dir.'chat_data.php';  // 读取文件记录
                    global $post;
                    $pid = $post->ID;
                    if(isset($cached_post['chat_pid_'.$pid]['error'])){
                        $desc = $cached_post['chat_pid_'.$pid]['error']['message'];
                    }else if(isset($cached_post['chat_pid_'.$pid]['choices'][0])){
                        $desc = isset($cached_post['chat_pid_'.$pid]['choices'][0]['message']) ? $cached_post['chat_pid_'.$pid]['choices'][0]['message']['content'] : $cached_post['chat_pid_'.$pid]['choices'][0]['text'];
                    }
                }else{
                    $desc = custom_excerpt();
                }
                break;
            default:
                $desc = get_option('site_description');
                break;
        }
        return trim($desc);
    }
    // 自定义文章摘要
    function custom_excerpt($length=88, $var=false){
        // $res = wp_trim_words(get_the_excerpt(), $length);
        $res = mb_substr(get_the_excerpt(), 0, $length).'...';  // chinese only
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
    
    /*
     *--------------------------------------------------------------------------
     * Custom funcs.
     *--------------------------------------------------------------------------
    */
    
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
            unset($images_cdn, $upload_url, $videos_cdn_arr);
            return $res;  //http://blog.iis7.com/article/53278.html
        }
        // 替换后台媒体库图片路径（目前无法自定义每个图像url）https://wordpress.stackexchange.com/questions/189704/is-it-possible-to-change-image-urls-by-hooks
        function wpse_change_featured_img_url(){
            return $images_cdn;  //'http://www.example.com/media/uploads';
        }
        add_filter( 'pre_option_upload_url_path', 'wpse_change_featured_img_url' );
    }
    
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
        unset($cat, $post);
        return $slug;
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
            unset($images_cdn, $upload_url, $videos_cdn_arr, $cat, $cdn_switch);
            return $url;
        }
    }
    
    //兼容gallery获取post内容指定图片（视频海报）
    function get_postimg($index=0, $postid=false, $default=false) {
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
        unset($images_cdn, $upload_url, $cdn_switch);
        return $res;
    }
    
    /*
     *--------------------------------------------------------------------------
     * wp_query handlers.
     *--------------------------------------------------------------------------
    */
    
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
        if($childs) unset($query_array['parent']);
        if($exclude) $query_array['exclude'] = $current_cid;
        return get_categories($query_array);
    }
    
    /*
     *--------------------------------------------------------------------------
     * $wpdb Queries.
     *--------------------------------------------------------------------------
    */
    
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
    
    // 模糊匹配文章别名返回文章id
    function get_post_like_slug($post_slug) {
        global $wpdb;
        $post_slug = '%' . $post_slug . '%';
        $pid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name LIKE %s", $post_slug));
        unset($wpdb);
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
        unset($wpdb);
        return $res;
    }
    // 返回年度文章id
    function get_wpdb_yearly_pids_by_cid($cid=0, $year=0, $limit=99, $offset=0){
        global $wpdb;
        $year = $year ? $year : gmdate('Y', time() + 3600*8);
        $res = $wpdb->get_results("SELECT DISTINCT ID FROM wp_posts,wp_term_relationships WHERE ID = object_id AND post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = $year AND wp_term_relationships.term_taxonomy_id = $cid ORDER BY post_date DESC LIMIT $limit OFFSET $offset ");
        unset($wpdb);
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
        unset($wpdb);
        return $res;
    }
    
    // 自定义 wpdb 查询函数
    function wpdb_postmeta_query($data, $key, $val){
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
    // 输出指定文章别名内容
    function the_page_content($slug){
        global $wpdb;
        $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$slug'");
        unset($wpdb);
        echo get_page($id)->post_content;// if(is_page()) echo get_page($id)->post_content;else echo '<p style="color:red">页面 '.current_slug().' 不存在，无法调用该页面内容。</p>';
    }
    
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
        if(isset($formart->error)) return $formart->error->message;
        $choices = $formart->choices[0];
        return isset($choices->message) ? $choices->message->content : $choices->text;
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
                isset($_COOKIE[$param]) ? $res = $_COOKIE[$param] : false;
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
    
    /*
     *--------------------------------------------------------------------------
     * Specified funcs.
     *--------------------------------------------------------------------------
    */
    
    // 返回指定文章标签
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
                $tas_list .= '<a href="'.get_bloginfo("url").'/tag/'.$tag_name.'" data-count="'.$tag->count.'" target="_blank" rel="tag">'.$tag_name.'</a>'.$dots;
            }
        }
        return $tas_list;
    }
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
        unset($lazysrc, $loadimg);
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
                unset($post, $lazysrc, $loadimg, $wp_query);
            }else{
                echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.$queryString.' </h1></div>';  //<b>'.current_slug(true).'</b> 
            }
        }
    }
?>