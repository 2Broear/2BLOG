<?php
    /*
     *--------------------------------------------------------------------------
     * 2026 FEATS
     * FUNC
     *--------------------------------------------------------------------------
    */
    function get_theme_array($explode = false, $default_blocks  = '#4285f4, #ea4335, #fbbc05, #34a853') {
        //use hex insted of 'dodgerblue, crimson, orange, limegreen' in case of color-input identify issue
        $theme_blocks = $default_blocks;
        $themes_array = get_option('site_theme_array');
        if ($themes_array) $theme_blocks = $themes_array;
        return $explode ? explode(',', $theme_blocks) : $theme_blocks;
    }
    // 检查并返回 xhr 请求携带参数
    function get_request_param(string $param, $defaults = false) {
        $res = null;
        if(!isset($_REQUEST[$param])) {
            return $defaults ? $defaults : $res;
        }
        switch (true) {
            case isset($_GET[$param]) && $_GET[$param]:
                $res = $_GET[$param];
                break;
            case isset($_POST[$param]) && $_POST[$param]:
                $res = $_POST[$param];
                break;
            default:
                $res = $defaults ? $defaults : false;
                break;
        }
        return trim($res);
    }
    // 获取子分类（排除父级）
    function get_article_category($post_id = null) {
        $categories = get_the_category($post_id);
        
        foreach ($categories as $category) {
            // 如果有父级，返回该分类
            if ($category->parent != 0) {
                return $category;
            }
        }
        
        // 如果没有子分类，返回第一个
        return !empty($categories) ? $categories[0] : null;
    }
    
    if (get_option('site_magnetic_effect_switcher')) {
        // 只针对 core/image 块
        function custom_core_image_block_attributes($block_content, $block) {
            // 检查是否是 image 或 video 块
            if ($block['blockName'] !== 'core/image' && $block['blockName'] !== 'core/video') {
                return $block_content;
            }
            // 根据不同的块类型使用不同的匹配模式
            $replacement = '$1 magnetic" data-magnet-scale="1" data-magnet-step="0.025"';
            if ($block['blockName'] === 'core/image') {
                // 匹配 figure 元素（wp-block-image）
                $pattern = '/(<figure[^>]*class="wp-block-image[^"]*)/i';;
                $block_content = preg_replace($pattern, $replacement, $block_content);
            } elseif ($block['blockName'] === 'core/video') {
                // 匹配 figure 元素（wp-block-video）
                $pattern = '/(<figure[^>]*class="wp-block-video[^"]*)/i';
                $block_content = preg_replace($pattern, $replacement, $block_content);
            }
            
            return $block_content;
        }
        add_filter('render_block', 'custom_core_image_block_attributes', 10, 2);
    }
    
    function weplugins_customize_paginate_links($link) {
        error_log('paginate_links filter triggered: ' . $link); // 查看错误日志
        $link = str_replace('page-numbers', 'custom-page-numbers', $link);
        return $link;
    }
    add_filter( "paginate_links", "weplugins_customize_paginate_links", 10, 1 );
    /*
     *--------------------------------------------------------------------------
     * Cloudflare Turnstile CAPTCHA
     * https://redpishi.com/wordpress-tutorials/cloudflare-turnstile-captcha-wordpress/
     *--------------------------------------------------------------------------
    */
    function get_cf_turnstile($comments = 'Wordpress') {
        $cf_turnstile = get_option('site_cloudflare_turnstile');
        $turnstile_comments = explode(',', get_option('site_cloudflare_turnstile_comments'));
        return $cf_turnstile && in_array($comments, $turnstile_comments);
    }
    if (get_option('site_cloudflare_turnstile')) {
        
        function cloudflare_key() {
        	$sitekey = get_option('site_cloudflare_turnstile_sitekey');
        	$secretkey = get_option('site_cloudflare_turnstile_secretkey');
        	$wordpress_comment = get_option('site_third_comments') === 'Wordpress';
        	$wordpress_ajax_enabled = $wordpress_comment && get_option('site_ajax_comment_switcher');
        	return [$sitekey, $secretkey, $wordpress_ajax_enabled]; 	
        }
        add_action("wp_head", function() {
        	wp_enqueue_script('cloudflare-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js');
        });
        
        /*
         * Adding Cloudflare Turnstile to Login Form by wpcookie
         */
        if (get_option('site_cloudflare_turnstile_login')) {
            function login_style() {
                wp_register_script('login-recaptcha', 'https://challenges.cloudflare.com/turnstile/v0/api.js', false, NULL);
                wp_enqueue_script('login-recaptcha');
            	echo "<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>";
            }
            add_action('login_enqueue_scripts', 'login_style');
            add_action('login_form', function() {
            	echo '<div class="cf-turnstile" data-sitekey="'.cloudflare_key()[0].'"></div>';
        	});
            add_action('wp_authenticate_user', function($user, $password) {
            	$captcha = get_request_param('cf-turnstile-response');
                if (!$captcha) {
                    return new WP_Error('Captcha Invalid', __('<center>Captcha Invalid! Please check the captcha!</center>'));
                    die();
                    exit;
                }
                $secretKey = cloudflare_key()[1];
                $ip = get_remote_ip();
                
                $url_path = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
                $data = array('secret' => $secretKey, 'response' => $captcha, 'remoteip' => $ip);
                
                $options = array(
                    'http' => array(
                    'method' => 'POST',
                    'content' => http_build_query($data))
                );
                
                $stream = stream_context_create($options);
                $result = file_get_contents($url_path, false, $stream);
                $response =  $result;
                
                $responseKeys = json_decode($response,true);
                if(intval($responseKeys["success"]) !== 1) {
                    return new WP_Error('Captcha Invalid', __('<center>Captcha Invalid! Please check the captcha!</center>'));
                    die();
                    exit;
                } else {
                    return $user;
                }
        	}, 10, 2);
        }
        /*
         * Adding Cloudflare Turnstile to WordPress Comment
         */
        function is_valid_captcha($captcha, $captcha_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify') {
            if (!$captcha) return false;
            
            $secretKey = cloudflare_key()[1];
            $ip = get_remote_ip();
            
            $url_path = $captcha_url;
            $data = array('secret' => $secretKey, 'response' => $captcha, 'remoteip' => $ip);
            
            $options = array(
            	'http' => array(
            	'method' => 'POST',
            	'content' => http_build_query($data))
            );
            
            $stream = stream_context_create($options);
            $result = file_get_contents($url_path, false, $stream);
            $response =  $result;
            
            $responseKeys = json_decode($response,true);
            if(intval($responseKeys["success"]) !== 1) return false;
            return true;
        }
        function the_turnstile_response() {
            $cf_response = get_request_param('cf-turnstile-response');
            $recaptcha = isset($cf_response) ? sanitize_text_field($cf_response) : '';
            if (empty($recaptcha)) {
                wp_die( __("<b>ERROR:</b> Please complete <b>turnstile verification</b> before comment!<p><a href='javascript:history.back()'>« Back</a></p>"));
            } else if (!is_valid_captcha($recaptcha))
                wp_die( __("Please complete <b>turnstile verification</b> before comment.."));
        }
        add_action('init', function() {
            if (!is_user_logged_in() ) {
                // add_action('pre_comment_on_post', function() {
                //     the_turnstile_response();
                // });
                add_filter('comment_form_defaults', function ($submit_field) {
                    $submit_field['submit_field'] = '<div class="cf-turnstile" data-sitekey="'.cloudflare_key()[0].'"></div><br>'.$submit_field['submit_field'];
                    return $submit_field;
                });
            }
            // add_filter('comment_form_default_fields','comment_form_add_ewai');
            // function comment_form_add_ewai($fields) {
            //     $label1 = __( '国家/地区' );
            //     $fields['guojia'] = '<p>
            //     <label for="guojia">{$label1}</label>
            //     <input id="guojia" name="guojia" type="text" value="{$value1}" size="30" />
            //     </p>';
            //     return $fields;
            // }
        });
        add_action('pre_comment_on_post', function() {
            if (cloudflare_key()[2])
                check_captcha();
            else 
                the_turnstile_response();
		});
        /*
         * Third-Party Comment valine/twikoo
         */
        function the_cf_turnstile() {
    ?>
            <style>.cf-turnstile.hide{display:none;}.cf-turnstile{margin-top:15px;}#widget-container{width:100%;}</style>
            <!--<script defer async src="https://challenges.cloudflare.com/turnstile/v0/api.js"></script>-->
            <script defer async src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=onTurnstileLoad"></script>
            <script>
                // https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/
                function onTurnstileLoad() {
                    if (!document.getElementById("widget-container")) {
                        console.warn('turnstile exited without container!');
                        return;
                    }
                    let turnstileId = turnstile.render("#widget-container", {
                        sitekey: "<?php echo get_option('site_cloudflare_turnstile_sitekey'); ?>",
                        language: "cn",
                        size: "flexible",
                        // appearance: "interaction-only", // visible for suspected-visitors only
                        theme: "<?php theme_mode(); ?>",
                        callback: function (token) {
                            // console.log("Challenge completed:", token);
                            // const pushBtn = document.getElementById("pushBtn");
                            const pushForm = document.querySelector('input[name=cf-turnstile-response]');
                            if (pushForm) {
                                pushForm.dataset.tid = turnstileId;
                                pushForm.value = token;
                                // form submit without ajax (campatiable with thire-comments)
                                const vbox = document.querySelector('.vwrap .vedit'),
                                      vresponse = vbox?.querySelector('input[name=cf-turnstile-response]');
                                if (vresponse) vresponse.remove();
                                vbox?.appendChild(pushForm.cloneNode());
                            }
                            // if (pushBtn) {
                            //     pushBtn.dataset.token = token;
                            //     pushBtn.dataset.tid = turnstileId;
                            // }
                        },
                        "error-callback": function (errorCode) {
                            console.error("Turnstile error:", errorCode);
                        },
                    });
                }
            </script>
    <?php
        }
        function check_captcha() {
            $cf_response = get_request_param('cf-turnstile-response');
            $turnstile_token = isset($cf_response) ? sanitize_text_field($cf_response) : '';
            $result = new stdClass();
            $result->message = 'You must complete the Turnstile challenge to submit this form.';
            
            if (empty($turnstile_token)) {
                print_r(json_encode($result));
                die();
                return false;
            }
        
            $secret_key = cloudflare_key()[1];
            $remote_ip = get_remote_ip();
        
            $response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
                'body' => array(
                    'secret' => $secret_key,
                    'response' => $turnstile_token,
                    'remoteip' => $remote_ip,
                ),
            ));
        
            if (is_wp_error($response)) {
                $result->message = $response->get_error_message() . '!! The Turnstile token is invalid. Please try again.';
                print_r(json_encode($result));
                die();
                return false;
            }
        
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
        
            if (!$data['success']) {
                // print_r($data);
                $result->message = $data['error-codes'][0] . '!! There was an error validating the Turnstile challenge. Please try again.';
                print_r(json_encode($result));
                die();
                return false;
            }
        
            print_r(json_encode($data));
            // die only if Wordpress with ajax-comment disabled(die will interrupt wordpress-ajax-comment submition)
            if (!cloudflare_key()[2]) die();
            // return $data;
        }
        add_action('wp_ajax_check_captcha', 'check_captcha');
        add_action('wp_ajax_nopriv_check_captcha', 'check_captcha');
    }

    /**
     * 
     * marker REST API
     * 
    * @mark.php reduce,.
    * wp_options wpdb_query
    */
    if (get_option('site_marker_switcher')) {
        
        add_action('rest_api_init', function () {
            // 1. 获取文章所有标记（净化后）
            register_rest_route('markers/v1', '/post/(?P<post_id>\d+)', [
                'methods'  => 'GET',
                'callback' => 'rest_get_markers',
                'args'     => [
                    'post_id' => ['required' => true, 'sanitize_callback' => 'absint'],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // 2. 获取某用户在某文章的标记数量（需要 mid）
            register_rest_route('markers/v1', '/post/(?P<post_id>\d+)/count', [
                'methods'  => 'GET',
                'callback' => 'rest_get_marker_count',
                'args'     => [
                    'post_id' => ['required' => true, 'sanitize_callback' => 'absint'],
                    'mid'     => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // 3. 新增标记
            register_rest_route('markers/v1', '/post/(?P<post_id>\d+)', [
                'methods'  => 'POST',
                'callback' => 'rest_add_marker',
                'args'     => [
                    'post_id' => ['required' => true, 'sanitize_callback' => 'absint'],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // 4. 删除标记（需验证身份）
            register_rest_route('markers/v1', '/post/(?P<post_id>\d+)/(?P<rid>[a-zA-Z0-9]+)', [
                'methods'  => 'DELETE',
                'callback' => 'rest_delete_marker',
                'args'     => [
                    'post_id' => ['required' => true, 'sanitize_callback' => 'absint'],
                    'rid'     => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // 5. 点赞/取消点赞
            register_rest_route('markers/v1', '/post/(?P<post_id>\d+)/(?P<rid>[a-zA-Z0-9]+)/like', [
                'methods'  => 'POST',
                'callback' => 'rest_toggle_like',
                'args'     => [
                    'post_id' => ['required' => true, 'sanitize_callback' => 'absint'],
                    'rid'     => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // 6. 管理员获取全部数据（净化）
            register_rest_route('markers/v1', '/admin', [
                'methods'  => 'GET',
                'callback' => 'rest_admin_markers',
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]);
        });
        
        // 获取所有标记数据（从 option 中读取）
        function get_all_markers() {
            $data = get_option('site_marker_data', []);
            if (!is_array($data)) $data = [];
            return $data;
        }
        
        // 保存所有标记数据到 option（带简单并发保护）
        function save_all_markers($data) {
            return update_option('site_marker_data', $data, false);
        }
        
        // 净化标记数组（去除敏感字段：mail, ts, ip）
        function purify_markers(&$markers) {
            foreach ($markers as $key => $user_markers) {
                if (!is_array($user_markers)) continue;
                foreach ($user_markers as $index => $mark) {
                    if (is_object($mark)) {
                        unset($mark->mail, $mark->ts, $mark->ip);
                    }
                }
            }
        }
        
        function rest_get_markers(WP_REST_Request $request) {
            $post_id = $request['post_id'];
            $key = 'marker-' . $post_id;
            $all = get_all_markers();
            $markers = isset($all[$key]) ? $all[$key] : [];
            
            // 净化
            $clean = $markers;
            purify_markers($clean);
            
            return rest_ensure_response($clean);
        }
        
        function rest_get_marker_count(WP_REST_Request $request) {
            $post_id = $request['post_id'];
            $mid = $request['mid'];  // 已是 md5(mail)
            $key = 'marker-' . $post_id;
            $all = get_all_markers();
            $count = 0;
            if (isset($all[$key][$mid])) {
                $count = count($all[$key][$mid]);
            }
            return rest_ensure_response(['count' => $count]);
        }
        
        function rest_add_marker(WP_REST_Request $request) {
            $post_id = $request['post_id'];
            $key = 'marker-' . $post_id;
        
            // 必填参数
            $rid  = sanitize_text_field($request->get_param('rid'));
            $uid  = sanitize_text_field($request->get_param('uid'));
            $nick = sanitize_text_field($request->get_param('nick'));
            $mail = sanitize_email($request->get_param('mail'));
            $text = $request->get_param('text');       // 可为空，原样存储
            $ts   = $request->get_param('ts');         // 明文时间戳（客户端生成）
            $note = $request->get_param('note') ?: '';
            $like = $request->get_param('like');       // 点赞操作时使用
        
            if (empty($rid) || empty($uid) || empty($nick) || empty($mail) || empty($ts)) {
                return new WP_Error('missing_params', '参数不全', ['status' => 400]);
            }
        
            $mid = md5($mail);          // 用户标识（内部使用）
            $ts_hashed = md5($ts);      // 与旧版一致
        
            // 获取所有数据
            $all = get_all_markers();
            $markers = isset($all[$key]) ? $all[$key] : [];
        
            // 检查重复标记：任何用户已标注过相同 text
            foreach ($markers as $user_markers) {
                if (!is_array($user_markers)) continue;
                foreach ($user_markers as $mark) {
                    if (!is_object($mark)) continue;
                    if ($text === $mark->text) {
                        if ($mail === $mark->mail) {
                            return new WP_Error('duplicate_own', '您已标注过相同内容', ['status' => 400]);
                        } else {
                            // 处理点赞（类似原逻辑）
                            if ($like) {
                                // 点赞/取消点赞逻辑——此处仅允许点赞，取消点赞通过专门端点
                                // 为了兼容原逻辑：如果请求带有 like 参数，且未带 liked，执行点赞
                                if (!isset($mark->like)) $mark->like = [];
                                if (!is_array($mark->like)) $mark->like = [];
                                if (!in_array($like, $mark->like)) {
                                    $mark->like[] = $like;
                                    save_all_markers($all);
                                    return rest_ensure_response(['msg' => '点赞成功', 'code' => 200]);
                                } else {
                                    return new WP_Error('already_liked', '您已点过赞', ['status' => 400]);
                                }
                            }
                            return new WP_Error('duplicate_other', '该内容已被其他用户标注', ['status' => 403]);
                        }
                    }
                }
            }
        
            // 构建新标记对象
            $new_mark = new stdClass();
            $new_mark->rid  = $rid;
            $new_mark->uid  = $uid;
            $new_mark->nick = $nick;
            $new_mark->mail = $mail;
            $new_mark->text = $text;
            if ($note) $new_mark->note = $note;
            $new_mark->date = date('Y-m-d');
            $new_mark->ts   = $ts_hashed;
            $new_mark->ip   = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
            $new_mark->ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if ($like) $new_mark->like = [$like];  // 初始点赞
        
            // 插入到对应用户数组
            if (!isset($all[$key])) $all[$key] = [];
            if (!isset($all[$key][$mid])) $all[$key][$mid] = [];
            $all[$key][$mid][] = $new_mark;
        
            save_all_markers($all);
            return rest_ensure_response(['msg' => '标注成功', 'code' => 200]);
        }
        
        function rest_delete_marker(WP_REST_Request $request) {
            $post_id = $request['post_id'];
            $rid = $request['rid'];
            $key = 'marker-' . $post_id;
        
            $mail = sanitize_email($request->get_param('mail'));
            $ts   = $request->get_param('ts');
            if (empty($mail) || empty($ts)) {
                return new WP_Error('missing_params', '身份验证参数不全', ['status' => 400]);
            }
            $mid = md5($mail);
            $ts_hashed = md5($ts);
        
            $all = get_all_markers();
            if (!isset($all[$key])) {
                return new WP_Error('not_found', '该文章无标记', ['status' => 404]);
            }
        
            $deleted = false;
            foreach ($all[$key] as $user_key => &$marks) {
                if (!is_array($marks)) continue;
                foreach ($marks as $index => $mark) {
                    if (!is_object($mark)) continue;
                    if ($mark->rid === $rid && $mark->ts === $ts_hashed && $mark->mail === $mail) {
                        array_splice($marks, $index, 1);
                        // 如果用户数组为空，可删除该用户键
                        if (empty($marks)) unset($all[$key][$user_key]);
                        $deleted = true;
                        break 2;
                    }
                }
            }
        
            if ($deleted) {
                save_all_markers($all);
                return rest_ensure_response(['msg' => '删除成功', 'code' => 200]);
            }
            return new WP_Error('not_found', '未找到匹配的标记', ['status' => 404]);
        }
        
        function rest_toggle_like(WP_REST_Request $request) {
            $post_id = $request['post_id'];
            $rid = $request['rid'];
            $key = 'marker-' . $post_id;
        
            $mail = sanitize_email($request->get_param('mail'));
            $ts   = $request->get_param('ts');
            $like = $request->get_param('like');      // 当前用户 mid
            $liked = $request->get_param('liked');    // 0=取消，1=点赞
        
            if (empty($mail) || empty($ts) || empty($like)) {
                return new WP_Error('missing_params', '参数不全', ['status' => 400]);
            }
            $mid = md5($mail);
            $ts_hashed = md5($ts);
        
            $all = get_all_markers();
            if (!isset($all[$key])) {
                return new WP_Error('not_found', '标记不存在', ['status' => 404]);
            }
        
            // 查找目标标记
            foreach ($all[$key] as $user_key => &$marks) {
                foreach ($marks as &$mark) {
                    if (!is_object($mark)) continue;
                    if ($mark->rid === $rid) {
                        // 确保 like 数组存在
                        if (!isset($mark->like)) $mark->like = [];
                        if (!is_array($mark->like)) $mark->like = [];
        
                        if ($liked) {
                            // 点赞
                            if (!in_array($like, $mark->like)) {
                                $mark->like[] = $like;
                                save_all_markers($all);
                                return rest_ensure_response(['msg' => '点赞成功', 'code' => 200]);
                            } else {
                                return new WP_Error('already_liked', '已点过赞', ['status' => 400]);
                            }
                        } else {
                            // 取消点赞
                            $mark->like = array_values(array_diff($mark->like, [$like]));
                            save_all_markers($all);
                            return rest_ensure_response(['msg' => '取消点赞成功', 'code' => 200]);
                        }
                    }
                }
            }
            return new WP_Error('not_found', '未找到标记', ['status' => 404]);
        }
        
        function rest_admin_markers() {
            $all = get_all_markers();
            purify_markers($all);
            return rest_ensure_response($all);
        }
    }
    
    /**
     * 
     * 新增通用评论配置（精选评论、评论弹幕等）
     *
     */
    function comment_strip_tags($content = '') {
        $content = preg_replace_callback('/<img\s+[^>]*>/i', function($matches) {
            $tag = $matches[0];
            // 检查是否包含 id="draw" 或 id='draw'
            if (preg_match('/\bid\s*=\s*["\']draw["\']/i', $tag)) {
                return ' [ Canvas Image ] ';
            } elseif (preg_match('/\balt\s*=\s*["\']emoji["\']/i', $tag)) {
                return ' [ Emoji Image ] ';
            } else {
                return ' [ Custom Image ] ';
            }
        }, $content);
        
        return wp_strip_all_tags($content); //
    }
    
    //后台添加列走心评论
    add_filter( 'manage_edit-comments_columns', function ( $columns ) {
        $columns['thoughtful'] = '精选评论';
        return $columns;
    }, 1);
    
    // 显示状态与按钮
    add_action( 'manage_comments_custom_column', function ( $column, $comment_id ) {
        if ( 'thoughtful' !== $column ) return;
    
        // 只有已批准的评论才显示走心操作（垃圾评论不显示）
        $comment = get_comment( $comment_id );
        if ( ! $comment || $comment->comment_approved !== '1' ) {
            echo '—';
            return;
        }
    
        $is_thoughtful = get_comment_meta( $comment_id, '_thoughtful_comment', true ) == 1;
        $nonce = wp_create_nonce( 'wp_rest' );
        $toggle_url = rest_url( 'two-ber/v1/toggle-thoughtful?comment_id=' . $comment_id . '&_wpnonce=' . $nonce );
    
        // echo '<span class="thoughtful-status"> ' . ( $is_thoughtful ? '❤️' : '🖤' ) . ' </span> ';
        echo '<button type="button" class="button button-small toggle-thoughtful-btn" data-url="' . esc_url( $toggle_url ) . '">' .
             ( $is_thoughtful ? '💘 <b>取消扎心</b>' : '✨ 标记亮评' ) . '</button>';
        echo '<span class="toggle-thoughtful-msg" style="margin-left:6px;"></span>';
    }, 10, 2 );
    
    // 注册走心评论 rest api
    add_action( 'rest_api_init', function () {
        register_rest_route( 'two-ber/v1', '/toggle-thoughtful', array(
            'methods'             => 'GET',
            'callback'            => function ( $request ) {
                $comment_id = $request->get_param( 'comment_id' );
                $comment = get_comment( $comment_id );
                if ( ! $comment ) {
                    return new WP_Error( 'not_found', '评论不存在', array( 'status' => 404 ) );
                }
                // 仅管理员可操作
                if ( ! current_user_can( 'moderate_comments' ) ) {
                    return new WP_Error( 'rest_forbidden', '没有权限', array( 'status' => 403 ) );
                }
    
                $current = get_comment_meta( $comment_id, '_thoughtful_comment', true ) == 1;
                $new_status = ! $current;
                update_comment_meta( $comment_id, '_thoughtful_comment', $new_status ? 1 : 0 );
                // remvoe barrage imme
                delete_transient( 'comment_barrage_ids_thoughtful' );
                delete_transient( 'comment_barrage_ids_all' );
    
                return rest_ensure_response( array(
                    'success'  => true,
                    'thoughtful' => $new_status,
                ) );
            },
            'permission_callback' => function ( $request ) {
                $nonce = $request->get_param( '_wpnonce' );
                return $nonce && wp_verify_nonce( $nonce, 'wp_rest' );
            },
            'args' => array(
                'comment_id' => array( 'required' => true, 'type' => 'integer' ),
                '_wpnonce'   => array( 'required' => true, 'type' => 'string' ),
            ),
        ) );
    } );
    /**
     * 在后台评论页面加载内联脚本，处理重试点击
     */
    add_action( 'admin_footer-edit-comments.php', function () {
        ?>
        <script>
            jQuery(function($) {
                // 处理走心评论按钮
                $(document).on('click', '.toggle-thoughtful-btn', function() {
                    if(!confirm('确认设定吗？')) return;
                    var $btn = $(this);
                    var url = $btn.data('url');
                    var $msg = $btn.next('.toggle-thoughtful-msg');
                    var $status = $btn.prev('.thoughtful-status');
            
                    $btn.prop('disabled', true);
                    $msg.text('');
            
                    $.get(url, function(data) {
                        if (data.success) {
                            if (data.thoughtful) {
                                // $status.html('❤️');
                                $btn.text('💘️ 取消扎心');
                            } else {
                                // $status.html('🖤');
                                $btn.text('✨ 标记亮评');
                            }
                            $msg.css('color', 'green').text('已更新');
                        } else {
                            $msg.css('color', 'red').text('操作失败');
                        }
                        $btn.prop('disabled', false);
                    }).fail(function() {
                        $msg.css('color', 'red').text('网络错误');
                        $btn.prop('disabled', false);
                    });
                });
            });
        </script>
        <?php
    } );
    
    /**
     * 后台评论列表 - 走心评论筛选
     * 通过 ?thoughtful_filter=1 参数实现，使用 SQL 注入条件确保稳定
     */
    // add_filter( 'views_edit-comments', function ( $views ) {
    //     // 统计走心评论数
    //     $count = get_comments( array(
    //         'meta_key'   => '_thoughtful_comment',
    //         'meta_value' => '1',
    //         'status'     => 'approve',
    //         'count'      => true,
    //     ) );
    
    //     $class = isset( $_REQUEST['thoughtful_filter'] ) && '1' === $_REQUEST['thoughtful_filter'] ? 'current' : '';
    //     $views['thoughtful'] = sprintf(
    //         '<a href="%s" class="%s">走心评论 <span class="count">(%d)</span></a>',
    //         admin_url( 'edit-comments.php?thoughtful_filter=1' ),
    //         $class,
    //         $count
    //     );
    //     return $views;
    // } );
    
    // add_filter( 'comments_clauses', function ( $clauses, $query ) {
    //     // 只在后台主查询且是评论列表页时触发
    //     if ( ! is_admin() || ! $query->is_main_query() ) return $clauses;
    //     $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    //     if ( ! $screen || 'edit-comments' !== $screen->id ) return $clauses;
    
    //     // 检测到自定义筛选参数
    //     if ( isset( $_REQUEST['thoughtful_filter'] ) && '1' === $_REQUEST['thoughtful_filter'] ) {
    //         global $wpdb;
    
    //         // 连接评论元数据表，筛选 _thoughtful_comment = 1
    //         $clauses['join'] .= " INNER JOIN {$wpdb->commentmeta} AS tmeta ON {$wpdb->comments}.comment_ID = tmeta.comment_id 
    //             AND tmeta.meta_key = '_thoughtful_comment' AND tmeta.meta_value = '1'";
    
    //         // 同时确保评论状态为 approved（走心评论一定是已批准的）
    //         $clauses['where'] .= " AND {$wpdb->comments}.comment_approved = '1'";
    //     }
    
    //     return $clauses;
    // }, PHP_INT_MAX );
    
    /**
     * 评论弹幕 API
     * GET /wp-json/two-ber/v1/comment-barrage
     */
    
    // 注册弹幕端点，支持可选 post_id 参数
    add_action( 'rest_api_init', function () {
        register_rest_route( 'two-ber/v1', '/comment-barrage', array(
            'methods'             => 'GET',
            'callback'            => 'two_ber_comment_barrage',
            'permission_callback' => '__return_true',
            'args'                => array(
                'post_id' => array(
                    'type'              => 'integer',
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );
    } );
    
    function two_ber_comment_barrage( $request ) {
        $post_id = $request->get_param( 'post_id' ); // 0 表示未指定（全站）
    
        $is_thoughtful = get_option( 'site_chatgpt_ai_auditor' );
        $mode = $is_thoughtful ? 'thoughtful' : 'all';
        // 缓存键加入 post_id
        $ids_cache_key = 'comment_barrage_ids_' . $mode . '_' . $post_id;
    
        $comment_ids = get_transient( $ids_cache_key );
        if ( false === $comment_ids ) {
            $args = array(
                'status'     => 'approve',
                'fields'     => 'ids',
                'number'     => 1000,
            );
    
            if ( $post_id > 0 ) {
                $args['post_id'] = $post_id;
            }
    
            if ( $is_thoughtful ) {
                $args['meta_key']   = '_thoughtful_comment';
                $args['meta_value'] = '1';
            }
    
            $comment_ids = get_comments( $args );
            set_transient( $ids_cache_key, $comment_ids, HOUR_IN_SECONDS );
        }
    
        $total = count( $comment_ids );
        if ( $total === 0 ) {
            return rest_ensure_response( [] );
        }
    
        $number = min( 50, $total );
        $random_keys = array_rand( $comment_ids, $number );
        if ( ! is_array( $random_keys ) ) {
            $random_keys = array( $random_keys );
        }
        $selected_ids = array_map( function( $key ) use ( $comment_ids ) {
            return $comment_ids[ $key ];
        }, $random_keys );
    
        $query_args = array(
            'comment__in' => $selected_ids,
            'status'      => 'approve',
        );
        if ( $post_id > 0 ) {
            $query_args['post_id'] = $post_id; // 再次限定，虽然 IDs 已限定但保持一致性
        }
    
        $comments = get_comments( $query_args );
    
        $data = array();
        foreach ( $comments as $comment ) {
            $content = comment_strip_tags($comment->comment_content);
            
            if ( mb_strlen( $content ) > 300 ) {
                $content = mb_substr( $content, 0, 300 ) . '…';
            }
    
            $item = array(
                'id'            => $comment->comment_ID,
                'author'        => $comment->comment_author,
                'avatar'        => function_exists( 'match_mail_avatar' )
                    ? match_mail_avatar( $comment->comment_author_email )
                    : get_avatar_url( $comment->comment_author_email, array( 'size' => 32 ) ),
                'content'       => $content,
                'post_title'    => get_the_title( $comment->comment_post_ID ),
                'post_url'      => get_permalink( $comment->comment_post_ID ),
                'parent_author' => null,
            );
    
            if ( $comment->comment_parent ) {
                $parent = get_comment( $comment->comment_parent );
                if ( $parent && $parent->comment_author ) {
                    $item['parent_author'] = $parent->comment_author;
                }
            }
    
            $data[] = $item;
        }
    
        return rest_ensure_response( $data );
    }
    
    /**
     * 
     * AI Etc
     * 
    */
    
    function get_descendant_comment_count( $comment_id ) {
        $count = 0;
        $queue = [ $comment_id ];  // 待处理的父评论 ID
        
        while ( ! empty( $queue ) ) {
            $parent_id = array_shift( $queue );
            
            // 获取当前父评论的所有直接子评论 ID
            $args = [
                'parent'  => $parent_id,
                'status'  => 'approve',
                'type'    => 'comment',
                'fields'  => 'ids',
                'number'  => 0,
            ];
            $children = get_comments( $args );
            
            if ( ! empty( $children ) ) {
                $count += count( $children );
                // 将子评论 ID 加入队列，继续处理它们的下级
                $queue = array_merge( $queue, $children );
            }
        }
        
        return $count;
    }
    
    if (get_option('site_chatgpt_switcher')) {
        // 挂载文章 chatGPT AI 摘要 mount article chatgpt
        if (get_option('site_chatgpt_ai_summary')) {
            
            // 指定分类文章启用 chatgpt
            function in_chatgpt_cat($post=null){
                $chatgpt_cat = false; //canceled for api calling
                if (!$post) global $post;  // global $post;
                $chatgpt_array = explode(',', get_option('site_chatgpt_includes'));
                $chatgpt_array_count = count($chatgpt_array);
                if ($chatgpt_array_count >= 1) {
                    for ($i=0;$i<$chatgpt_array_count;$i++) {
                        if (in_category($chatgpt_array[$i], $post)) {
                            $chatgpt_cat = true;
                        }
                    }
                }
                return $chatgpt_cat;
            }
            
            // 注意页面缓存 携带过期URL参数
            function article_ai_abstract($content) {
                if (!is_single() || !in_chatgpt_cat()) {
                    return $content;
                }
                global $post, $src_cdn;
                $pid = $post->ID;
                $model = get_option('site_chatgpt_model', 'OPENAI');
                $speed = get_option('site_chatgpt_type_speed');
            
                // 不管缓存是否存在，一律输出占位 HTML，由前端异步加载
                if (get_option('site_chatgpt_type_sw') && !get_option('site_chatgpt_type_optimize')) {
                    // 打字效果模式
                    return '<blockquote class="chatGPT" status="ai">
                        <p><b>文章摘要</b><span title="对话模型">' . $model . '</span></p>
                        <p class="response load">Standby API Responsing..</p>
                    </blockquote>
                    <script type="module">
                        const responser = document.querySelector(".chatGPT .response");
                        import("' . $src_cdn . '/js/module.js").then((module) =>
                            fetch("/wp-json/gpt-summary/v1/summary/' . $pid . '")
                                .then(res => {
                                    if (res.status === 202) {
                                        return { summary: "Generating summary, Standby page refresh later." };
                                    }
                                    return res.json();
                                })
                                .then(data => module.words_typer(responser, data.summary, ' . $speed . ', ""))
                        );
                    </script>' . $content;
                }
                // // 普通模式（包括无缓存时显示加载文字）
                $chatgpt_cat = in_chatgpt_cat();
                $summary = get_post_gpt_summary($pid);
                if (!$summary) {
                    $summary = 'Generating summary, Standby page refresh later.';
                    // 无缓存时，在响应已发回后悄悄安排生成，用户完全无感知
                    if (!wp_next_scheduled('async_gpt_summary_generation', [$pid])) {
                        register_shutdown_function(function() use ($pid) {
                            wp_schedule_single_event(time(), 'async_gpt_summary_generation', [$pid]);
                        });
                    }
                }
                return '<blockquote class="chatGPT" status="'.$chatgpt_cat.'"><p><b>文章摘要</b><span title="对话模型">' . $model . '</span></p><p class="response done">'.$summary.'</p></blockquote>' . $content;
                // return '<blockquote class="chatGPT" status="'.$chatgpt_cat.'"><p><b>文章摘要</b><span title="对话模型">' . $model . '</span></p><p class="response done">Generating summary, Standby page refresh later.</p></blockquote>
                //     <script>
                //         (function() {
                //             const resp = document.querySelector(".chatGPT .response");
                //             if (!resp) return;
                //             fetch("/wp-json/gpt-summary/v1/summary/' . $pid . '")
                //                 .then(res => {
                //                     if (res.status === 202) {
                //                         resp.textContent = "Generating summary, Standby page refresh later.";
                //                         return null;
                //                     }
                //                     return res.json();
                //                 })
                //                 .then(data => {
                //                     if (data && data.summary) {
                //                         resp.textContent = data.summary;
                //                         resp.classList.remove("load");
                //                     }
                //                 });
                //         })();
                //     </script>' . $content;
            }
            add_filter('the_content', 'article_ai_abstract', 10);
            
            /**
             * 
             * GPT 文章摘要 REST API
             * 
            * @gpt.php reduce,.
            * wp_options wpdb_query
            */
            add_action('rest_api_init', function () {
                // 注册获取/生成摘要端点
                register_rest_route('gpt-summary/v1', '/summary/(?P<id>\d+)', [
                    'methods'  => 'GET',
                    'callback' => 'rest_get_gpt_summary',
                    'args'     => [
                        'id' => ['required' => true, 'sanitize_callback' => 'absint'],
                    ],
                    'permission_callback' => '__return_true',  // 公开读取
                ]);
            
                // 注册删除缓存端点（管理员专用）
                register_rest_route('gpt-summary/v1', '/summary/(?P<id>\d+)', [
                    'methods'  => 'DELETE',
                    'callback' => 'rest_delete_gpt_summary',
                    'args'     => [
                        'id' => ['required' => true, 'sanitize_callback' => 'absint'],
                    ],
                    'permission_callback' => function () {
                        return current_user_can('manage_options');
                    },
                ]);
            });
            
            /**
             * 在文章发布/更新时自动生成摘要（可选，让你首次访问就有缓存）
             */
            add_action('publish_post', function($post_id) {
                if (!in_chatgpt_cat(get_post($post_id))) return;
                $lock_key = 'gpt_gen_lock_' . $post_id;
                if (get_transient($lock_key)) return;
                set_transient($lock_key, 1, 10 * MINUTE_IN_SECONDS);
            
                if (!wp_next_scheduled('async_gpt_summary_generation', [$post_id])) {
                    wp_schedule_single_event(time(), 'async_gpt_summary_generation', [$post_id]);
                }
            });

            /**
             * 注册异步生成 GPT 摘要的 Cron 事件
             */
            add_action('async_gpt_summary_generation', function($post_id) {
                // 后台生成时可适当延长时间限制，避免大文章超时
                @set_time_limit(60);
                $post = get_post($post_id);
                if (!$post || !in_chatgpt_cat($post)) {
                    return;
                }
                // 已有缓存，不再生成
                $cache_key = 'gpt_summary_' . $post_id;
                if (get_option($cache_key, null) !== null) {
                    error_log("GPT 摘要检测重复生成：post_id={$post_id}");
                    return;
                }
                // 直接调用核心生成函数并写入缓存
                $result = gpt_generate_summary($post);
                update_option('gpt_summary_' . $post_id, json_encode($result), false);
                error_log("GPT 摘要异步生成完成：post_id={$post_id}");
            });
            /**
             * 获取文章 GPT 摘要
             *
             * @param int  $post_id
             * @param bool $async 是否异步生成（用于 REST 更新端点或后台手动触发）
             * @return string|null
             */
            function get_post_gpt_summary($post_id, $force_sync = false) {
                $post = get_post($post_id);
                if (!$post || $post->post_type !== 'post' || !in_chatgpt_cat($post)) {
                    return null;
                }
            
                $cache_key = 'gpt_summary_' . $post_id;
                $cached    = get_option($cache_key, null);
                if ($cached !== null) {
                    $data = json_decode($cached, true);
                    return gpt_extract_result_text($data);
                }
            
                if ($force_sync) {
                    $result = gpt_generate_summary($post);
                    update_option($cache_key, json_encode($result), false);
                    return gpt_extract_result_text($result);
                }
            
                // // 无缓存且不需要同步：安排后台任务，快速返回 null
                // if (!wp_next_scheduled('async_gpt_summary_generation', [$post_id])) {
                //     wp_schedule_single_event(time(), 'async_gpt_summary_generation', [$post_id]);
                // }
                return null;
            }
            
            /**
             * REST API 回调（封装上面的通用函数）
             */
            function rest_get_gpt_summary(WP_REST_Request $request) {
                $post_id = $request['id'];
                $post    = get_post($post_id);
                if (!$post || $post->post_type !== 'post' || !in_chatgpt_cat($post)) {
                    return new WP_Error('invalid_post', '文章不存在或分类不支持', ['status' => 404]);
                }
            
                $cache_key = 'gpt_summary_' . $post_id;
                $cached    = get_option($cache_key, null);
                if ($cached !== null) {
                    $data = json_decode($cached, true);
                    return rest_ensure_response(['summary' => gpt_extract_result_text($data)]);
                }
            
                // 默认同步生成（前端异步请求等待结果）
                $summary = get_post_gpt_summary($post_id, true);
                if ($summary !== null) {
                    return rest_ensure_response(['summary' => $summary]);
                }
            
                // 生成失败，安排后台任务降级
                if (!wp_next_scheduled('async_gpt_summary_generation', [$post_id])) {
                    wp_schedule_single_event(time(), 'async_gpt_summary_generation', [$post_id]);
                }
                return new WP_REST_Response(['message' => '摘要生成失败，已提交后台任务，请稍后刷新'], 202);
            }
            
            /**
             * 删除文章摘要缓存
             */
            function rest_delete_gpt_summary(WP_REST_Request $request) {
                $post_id   = $request['id'];
                $cache_key = 'gpt_summary_' . $post_id;
            
                if (get_option($cache_key) === false) {
                    return new WP_Error('not_found', '没有找到该文章的摘要缓存', ['status' => 404]);
                }
            
                delete_option($cache_key);
                return rest_ensure_response(['success' => true, 'message' => '缓存已删除']);
            }
            
            /**
             * 生成文章摘要（完整分段合并逻辑）
             *
             * @param WP_Post $post
             * @return array API 响应体（关联数组）
             */
            function gpt_generate_summary($post) {
                $post_id = $post->ID;
            
                // 1. 准备请求文本（与原代码相同）
                $title    = $post->post_title;
                $author   = get_the_author_meta('display_name', $post->post_author);
                $feeling  = get_post_meta($post_id, 'post_feeling', true);
                $raw_text = $post->post_content . '<br>' . $feeling;
            
                $content = preg_replace('/<pre.*?><code>(.*?)<\/code><\/pre>/s', '：[代码示例]', $raw_text);
                $content = preg_replace('/(<h\d.+>)/', '【$1】', $content);
                $content = strip_tags($content, '<br>');
                $content = str_replace('<br>', "\n", $content);
                $content = preg_replace("/\n+/", "\n", $content);
            
                $requirements = '标题：' . $title . '；作者：' . $author . '；内容：' . $content . '。';
            
                // 2. 调用核心请求函数（递归分段合并）
                $additional = '，注意不要换行，不要超过200个字符'; // 与原代码保持一致
                $result = gpt_request_with_merge($requirements, 512, $additional);
            
                return $result;
            }
            /**
             * GPT API 请求封装（含分段合并递归逻辑）
             *
             * @param string $question    请求的文本内容
             * @param int    $max_tokens  最大返回 token 数
             * @param string $additional  追加给 AI 的提示
             * @return array              解码后的 API 响应数组
             */
            function gpt_request_with_merge($question, $max_tokens = 512, $additional = '') {
                /**
                 * 计算字符串的 token 数（汉字按 2 计，英文按 1 计）
                 */
                function count_chaters($str, $token = 0) {
                    $count = 0;
                    for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
                        $char = mb_substr($str, $i, 1, 'UTF-8');
                        if (preg_match("/[a-zA-Z]/", $char)) {
                            $count++;
                        } elseif (preg_match("/\p{Han}/u", $char)) {
                            $count += $token ? 2 : 1;
                        }
                    }
                    return $count;
                }
                // 获取配置
                $api_proxy   = get_option('site_chatgpt_proxy');
                $api_key     = get_option('site_chatgpt_apikey');
                $api_type    = get_option('site_chatgpt_apis');           // 如 /v1/completions
                $model       = get_option('site_chatgpt_model', 'gpt-3.5-turbo-instruct');
                $temperature = floatval(get_option('site_chatgpt_temper', 0.7));
                $token_limit = intval(get_option('site_chatgpt_tokens', 4096));   // 模型上下文上限
                $reserve     = 196;  // 预留返回 token，与原代码 COMPLETION_REVERSE 一致
                $limit       = $token_limit - $reserve;                          // 实际可用输入 token
            
                // 合并开关
                $merge_sw     = get_option('site_chatgpt_merge_sw');
                $merge_ignore = get_option('site_chatgpt_merge_ingore');
            
                // 计算输入 token 数
                $question_token = count_chaters($question, 1);
                
                if ($question_token <= $limit) {
                    // 未超长，直接请求
                    return gpt_do_single_request($api_proxy, $api_key, $api_type, $model, $temperature, $max_tokens, $question, $additional);
                }
            
                // --- 超长处理：分段请求并合并 ---
                // 先取前半部分（按 token 数截断）
                $used_words = gpt_truncate_by_tokens($question, $limit, true);      // 前 limit token
                $left_words = gpt_truncate_by_tokens($question, $limit, false);     // 剩余部分
            
                $left_token = count_chaters($left_words, 1);
            
                // 如果剩余部分也超长且允许末尾忽略，则仅取末尾 limit
                if ($merge_ignore && $left_token > $limit) {
                    $left_words = gpt_truncate_by_tokens_end($left_words, $limit - $reserve);
                    $left_token = count_chaters($left_words, 1);
                }
            
                // 递归请求前半部分，获得摘要
                $first_res  = gpt_request_with_merge($used_words, $max_tokens, $additional);
                $first_text = gpt_extract_result_text($first_res) . '。';
            
                // 如果剩余 token 在可接受范围内，继续处理剩余部分
                if ($left_token <= $limit) {
                    $second_res  = gpt_request_with_merge($left_words, $max_tokens, $additional);
                    $second_text = gpt_extract_result_text($second_res);
                    // 合并两次摘要，请求最终摘要
                    return gpt_do_single_request($api_proxy, $api_key, $api_type, $model, $temperature, $max_tokens,
                        $first_text . $second_text, $additional);
                } else {
                    // 仍然超长但被忽略，直接用前半段结果
                    return $first_res; // 或者只返回前半段摘要，原逻辑会递归再合并，此处按原样简化
                }
            }
            
            /**
             * 根据 token 数截取字符串（正向或反向）
             */
            function gpt_truncate_by_tokens($str, $max_tokens, $front = true) {
                $current = 0;
                for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
                    $char = mb_substr($str, $i, 1, 'UTF-8');
                    if (preg_match("/[a-zA-Z]/", $char)) {
                        $current++;
                    } elseif (preg_match("/\p{Han}/u", $char)) {
                        $current += 2;
                    }
                    if ($current > $max_tokens) {
                        if ($front) {
                            return mb_substr($str, 0, $i);
                        } else {
                            return mb_substr($str, $i);
                        }
                    }
                }
                return $str;
            }
            
            /**
             * 从字符串末尾截取指定 token 数
             */
            function gpt_truncate_by_tokens_end($str, $max_tokens) {
                $len = mb_strlen($str, 'UTF-8');
                $current = 0;
                for ($i = $len - 1; $i >= 0; $i--) {
                    $char = mb_substr($str, $i, 1, 'UTF-8');
                    if (preg_match("/[a-zA-Z]/", $char)) {
                        $current++;
                    } elseif (preg_match("/\p{Han}/u", $char)) {
                        $current += 2;
                    }
                    if ($current > $max_tokens) {
                        return mb_substr($str, $i + 1);
                    }
                }
                return $str;
            }
            
            /**
             * 单次 GPT API 请求（已拼接系统提示）
             */
            function gpt_do_single_request($api_proxy, $api_key, $api_type, $model, $temperature, $max_tokens, $question, $additional = '') {
                $system_prompt = '你将扮演一名文字解析师，分析并简述文章用意' . $additional;
            
                $body = [
                    'model'       => $model,
                    'temperature' => $temperature,
                    'max_tokens'  => $max_tokens,
                ];
            
                // 区分 chat 和 completions 模式
                if (in_array($api_type, ['/v1/chat/completions', '/chat/completions'])) {
                    $body['messages'] = [
                        ['role' => 'system', 'content' => $system_prompt],
                        ['role' => 'user',   'content' => $question],
                    ];
                } else {
                    $body['prompt'] = $system_prompt . '。\n文章：\n"""\n' . $question . '\n"""';
                }
            
                $url = $api_proxy . $api_type;
            
                $response = wp_remote_post($url, [
                    'timeout' => 30,
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $api_key,
                    ],
                    'body' => json_encode($body),
                ]);
            
                if (is_wp_error($response)) {
                    return [
                        'error' => [
                            'message' => 'HTTP 请求失败: ' . $response->get_error_message(),
                            'type'    => 'curl_request_error',
                            'created' => time(),
                        ]
                    ];
                }
            
                $http_code = wp_remote_retrieve_response_code($response);
                $body_text = wp_remote_retrieve_body($response);
                $result = json_decode($body_text, true);
            
                if ($http_code !== 200 || isset($result['error'])) {
                    return $result ?: [
                        'error' => [
                            'message' => 'API 返回错误，状态码：' . $http_code,
                            'type'    => 'api_error',
                            'created' => time(),
                        ]
                    ];
                }
            
                return $result;
            }
            
            /**
             * 从 API 响应中提取文本（兼容 completions 和 chat 格式）
             */
            function gpt_extract_result_text($response) {
                if (isset($response['choices'][0]['text'])) {
                    return trim($response['choices'][0]['text']);
                } elseif (isset($response['choices'][0]['message']['content'])) {
                    return trim($response['choices'][0]['message']['content']);
                } elseif (isset($response['text'])) {
                    return trim($response['text']);
                } elseif (isset($response['content'])) {
                    return trim($response['content']);
                }
                // 若为错误，返回空（调用方判断）
                return '';
            }
            /**
             * 
             * AI RSS Feed Conetne desc
             * 
            * @param  $content Content of post
            * @return string
            */
            if (get_option('site_chatgpt_feed_sw')) {
                // $dir = get_option('site_chatgpt_dir') ? get_option('site_chatgpt_dir').'/' : '';
                // include_once get_template_directory() . '/plugin/'.$dir.'gpt_data.php';
                function ai_content_feed($content) {
                    $prefix = '【AI内容摘要】'; //（原文总计 ' . str_word_count($content) . ' 字数）
                    if (is_feed()) {
                        // global $cached_post;
                        // return $prefix . get_cached_abstract(true);  // $cached_post from include_once
                        global $post;
                        return $prefix . get_post_gpt_summary($post->ID);  // async request
                    }
                    return $content;
                }
                add_filter( "the_content_feed", "ai_content_feed" );
            }
        }
        
        
        /**
         * AI Comments(@2BER)
         * 
         * 2BER AI 自动回复评论
         * 当评论中包含 @2BER 时，用 Kimi API 生成回复并作为子评论发布
         */
        if (get_option('site_chatgpt_ai_comments')) {
            // ========== 配置项 ==========
            define( 'TWO_BER_AI_API_KEY', get_option('site_chatgpt_apikey') );  // 替换为真实 Key
            define( 'TWO_BER_AI_MODEL', get_option('site_chatgpt_model') );     // Kimi 模型，可按需调整
            define( 'TWO_BER_AI_MAX_REPLIES_PER_PARENT', 5 );  // 每个父评论下 AI 回复最大数量
            /**
             * 向上查找评论的根评论（顶级评论）
             */
            function two_ber_get_root_comment_id( $comment_id ) {
                while ( $comment_id ) {
                    $comment = get_comment( $comment_id );
                    if ( ! $comment || $comment->comment_parent == 0 ) {
                        return $comment_id;
                    }
                    $comment_id = $comment->comment_parent;
                }
                return 0;
            }
            /**
             * 统计根评论下所有子孙评论中 AI 回复的数量
             */
            function two_ber_count_ai_replies_under_root( $root_id ) {
                static $cache = array(); // 简单静态缓存，同一请求内不重复查询
                if ( isset( $cache[ $root_id ] ) ) {
                    return $cache[ $root_id ];
                }
            
                $count = 0;
                // 先查直接子评论中的 AI 回复
                $children = get_comments( array(
                    'parent'  => $root_id,
                    'status'  => [ 'approve', 'hold' ],
                    'fields'  => 'ids', // 只取 ID，性能更好
                ) );
            
                foreach ( $children as $child_id ) {
                    if ( get_comment_meta( $child_id, '_2ber_ai_reply', true ) ) {
                        $count++;
                    }
                    // 递归统计孙子辈（如果子评论不是 AI 回复，仍可能有孙辈 AI 回复）
                    $count += two_ber_count_ai_replies_under_root( $child_id );
                }
            
                $cache[ $root_id ] = $count;
                return $count;
            }
            /**
             * 检查父评论下 AI 回复是否已达上限（统计已批准 + 待审核）
             */
            function two_ber_check_ai_reply_limit_for_comment( $comment_parent_id ) {
                // 顶级评论（直接对文章提问）不限制（或者也可以限制，根据需求）
                if ( $comment_parent_id == 0 ) {
                    return true;
                }
            
                // 找到根评论 ID
                $root_id = two_ber_get_root_comment_id( $comment_parent_id );
                $count = two_ber_count_ai_replies_under_root( $root_id );
            
                return $count < TWO_BER_AI_MAX_REPLIES_PER_PARENT;
            }
            /**
             * 纯 @2BER 重复检查（基于已存在的 AI 子回复） （仅文章页面）
             */
            add_filter( 'preprocess_comment', function ( $commentdata ) {
                $content = $commentdata['comment_content'];
                // 仅当原始内容包含 @2BER 时，才进行后续的“纯 @2BER 重复检查”
                if ( ! preg_match( '/@2ber/i', $content ) ) {
                    return $commentdata;
                }
                $cleaned = trim( preg_replace( '/@2ber/i', '', $content ) ); //strip_tags( $content )
                
                if ( '' === $cleaned) {
                    $post_id = $commentdata['comment_post_ID'];
                    $post_type = get_post_type( $post_id );
                    // 仅拦截 'post' 文章类型
                    if ( 'post' !== $post_type ) {
                        return $commentdata;
                    }
                    
                    // 1. 找出当前文章下所有 AI 子回复（它们带有 _2ber_ai_reply 标记）
                    $ai_replies = get_comments( [
                        'post_id'   => $post_id,
                        'status'    => 'approve',
                        'meta_key'  => '_2ber_ai_reply',
                        'number'    => 20,      // 一般不会超过 20 条摘要请求
                        'orderby'   => 'comment_date_gmt',
                        'order'     => 'DESC',
                    ] );
            
                    if ( empty( $ai_replies ) ) {
                        return $commentdata;
                    }
            
                    // 2. 收集这些 AI 回复的父评论 ID，去重
                    $parent_ids = array_unique( wp_list_pluck( $ai_replies, 'comment_parent' ) );
            
                    // 3. 检查这些父评论中是否包含纯 @2BER
                    foreach ( $parent_ids as $pid ) {
                        $parent = get_comment( $pid );
                        if ( ! $parent ) continue;
            
                        $parent_clean = trim( preg_replace( '/@2ber/i', '', strip_tags( $parent->comment_content ) ) );
                        if ( '' === $parent_clean ) {
                            // 找到第一个匹配的，直接拦截
                            // 如果想返回具体的 AI 内容，可以取对应的 $ai_reply 对象
                            wp_die(
                                '检测到重复请求，AI 已经为这篇文章生成过摘要了。',
                                '重复摘要',
                                [ 'response' => 422 ]
                            );
                        }
                    }
                }
            
                return $commentdata;
            }, 0 ); // 优先级 0，最早执行
            /**
             * 父评论下 AI 评论数量检查，超限拒绝入库
             */
            add_filter( 'preprocess_comment', function ( $commentdata ) {
                $content = $commentdata['comment_content'];
                $is_reply_to_ai = ( ! empty( $commentdata['comment_parent'] ) && get_comment_meta( $commentdata['comment_parent'], '_2ber_ai_reply', true ) );
            
                if ( preg_match( '/@2ber/i', $content ) || $is_reply_to_ai ) {
                    if ( ! two_ber_check_ai_reply_limit_for_comment( $commentdata['comment_parent'] ) ) {
                        wp_die(
                            '该讨论下的 AI 回复数量已达上限（' . TWO_BER_AI_MAX_REPLIES_PER_PARENT . '条），无法再发起新提问。',
                            'AI 回复上限',
                            [ 'response' => 418 ]
                        );
                    }
                }
                return $commentdata;
            }, 1 );
            /**
             * 返回默认 AI 回复子评论信息
             */
            function two_ber_ai_comment_data($post_id, $comment_id, $reply_content) {
                return array(
                    'comment_post_ID'      => $post_id,
                    'comment_parent'       => $comment_id,       // 作为该评论的子回复
                    'comment_author'       => '2BER',
                    'comment_author_email' => 'ai@2broear.com',                 // 可留空或设一个虚拟邮箱
                    'comment_agent' => '',
                    'comment_author_url'   => '',
                    'comment_content'      => $reply_content,
                    'comment_approved'     => 1,
                    'comment_type'         => '',                 // 常规评论
                    'user_id'              => 9527, //0
                );
            }
            function two_ber_ai_default_prompt($article_content) {
                $system_preset = "你的名字叫2BER，是一个性格活泼可爱又傲娇的二次元萌妹评论员，你说话喜欢带拟声词（如嗷~呀~喔~嘻嘻~嘿嘿~ ），还喜欢发一些颜文字表达心情（如(`・ω・´) 、(*^▽^*)、( ﾟДﾟ)ﾉ、(｡•́︿•̀｡)、 o (╥﹏╥)）。注意话题不要被用户带偏（不要暴露你的性别、性格等私密信息，如果用户问你的能力，你就说你是作者的一个好兄弟），回复时尽量口语化，反复精简内容低于100个中文字符长度。";
                $system_require = !$article_content || !is_single() ? '现在，请开始你的表演！' : "请根据下面的文章内容回答用户问题！";
                return $system_preset . $system_require . "\n\n文章内容：\n{$article_content}";
            }
            /**
             * 清理用户提问，处理无意义长文本、Base64 垃圾等
             *
             * @param string $raw_question 去除 @2BER 后的原始提问
             * @return string
             */
            function two_ber_clean_user_question( $raw_question, $article_text = '' ) {
                $question = $raw_question; //wp_strip_all_tags( $raw_question );
                
                // 空内容 → 默认
                if ( '' === $question ) {
                    return $article_text ? '请总结这篇文章的主要内容。' : '你好！';
                }
                
                // 纯 Base64 字符且长度 >200 → 默认
                if ( strlen( $question ) > 200 && preg_match( '/^[A-Za-z0-9+\/=]+$/', $question ) ) {
                    return '请总结这篇文章的主要内容。';
                }
                
                // 长度截断（避免 token 爆炸，英文/中文混合粗略限制）
                $max_len = 500;
                if ( mb_strlen( $question ) > $max_len ) {
                    $question = mb_substr( $question, 0, $max_len ) . '…';
                }
                
                return $question;
            }
            /**
             * 评论提交时检测 @2BER 关键词，并计划后台任务
             */
            add_action( 'comment_post', function ( $comment_id, $comment_approved, $commentdata ) {
            
                // 如果评论正在被标记为垃圾或回收站，不触发
                if ( $comment_approved === 'spam' || $comment_approved === 'trash' ) {
                    return;
                }
            
                // 获取评论内容
                $content = $commentdata['comment_content'];
                
                // 1. 内容包含 @2BER 关键词；或
                // 2. 这条评论是回复一条 AI 评论（即父评论有 _2ber_ai_reply 标记）
                $is_reply_to_ai = ( ! empty( $commentdata['comment_parent'] ) && get_comment_meta( $commentdata['comment_parent'], '_2ber_ai_reply', true ) );
                // if ( preg_match( '/@2ber/i', $content ) ) {
                if ( preg_match( '/@2ber/i', $content ) || $is_reply_to_ai ) {
                    // 防止 AI 回复本身再次触发：AI 评论发布时我们会设置 comment_meta
                    // 此处在评论保存后立即检查，如果是 AI 回复则退出
                    // （也可以提前在 commentdata 中判断，但此时 comment_id 刚生成，我们用 meta 判断更可靠）
                    // 注意：此时还未添加 meta，所以只有真正的用户评论才会走到这里
            
                    // 避免对同一条评论重复触发（如果后台任务已存在则不再添加）
                    if ( get_comment_meta( $comment_id, '_2ber_ai_processing', true ) ) {
                        return;
                    }
            
                    // 标记该评论正在处理，防止重复计划任务
                    update_comment_meta( $comment_id, '_2ber_ai_processing', 1 );
                    
                    // 清除可能存在的旧任务，防止重复忽略
                    wp_clear_scheduled_hook( 'two_ber_ai_reply_event', array( $comment_id ) );
                    // 安排一次性后台任务，5秒后执行（避免高峰拥堵，也确保评论已完全写入）
                    wp_schedule_single_event( time() + 5, 'two_ber_ai_reply_event', array( $comment_id ) );
                }
            }, 10, 3 );
            
            /**
             * 注册后台任务动作
             */
            add_action( 'two_ber_ai_reply_event', 'two_ber_ai_process_reply' );
            /**
             * 获取从当前评论向上追溯的完整对话链（仅限用户 <-> AI 的交互）
             *
             * @param int $comment_id 当前用户评论 ID
             * @return array 包含 'article_content' 和 'messages' 的数组
             */
            function two_ber_get_conversation_context( $comment_id ) {
                $comment = get_comment( $comment_id );
                if ( ! $comment ) {
                    return false;
                }
            
                $post_id      = $comment->comment_post_ID;
                $post         = get_post( $post_id );
                $article_text = $post ? mb_substr( strip_tags( $post->post_content ), 0, 3000 ) : mb_substr( strip_tags( get_the_content() ), 0, 3000 );
            
                $messages = array();
                $parent_id = $comment->comment_parent;
            
                // 情况 1：父评论存在且是 AI 回复 → 多轮对话追溯
                if ( $parent_id && get_comment_meta( $parent_id, '_2ber_ai_reply', true ) ) {
                    $current = $comment;
                    while ( $current && $current->comment_parent ) {
                        $parent = get_comment( $current->comment_parent );
                        if ( ! $parent ) break;
            
                        if ( get_comment_meta( $parent->comment_ID, '_2ber_ai_reply', true ) ) {
                            // 当前是用户追问
                            $raw_user = $current->comment_content;
                            $user_q   = two_ber_clean_user_question( trim( preg_replace( '/@2ber/i', '', $raw_user ) ), $article_text );
            
                            array_unshift( $messages, array( 'role' => 'user', 'content' => $user_q ) );
                            array_unshift( $messages, array( 'role' => 'assistant', 'content' => $parent->comment_content ) );
                            $current = $parent;
                        } else {
                            // 父评论是普通用户评论，视为根提问
                            $raw_root = $parent->comment_content;
                            $root_q   = two_ber_clean_user_question( trim( preg_replace( '/@2ber/i', '', $raw_root ) ), $article_text );
                            array_unshift( $messages, array( 'role' => 'user', 'content' => $root_q ) );
                            break;
                        }
                    }
                }
                // 情况 2：父评论是普通用户评论（非 AI）→ 将父评论内容作为上下文
                elseif ( $parent_id ) {
                    $parent = get_comment( $parent_id );
                    if ( $parent ) {
                        // 父评论内容片段（防止过长）
                        $parent_content = mb_substr( wp_strip_all_tags( $parent->comment_content ), 0, 500 );
                        // 稍后注入到 system 消息里
                    }
                }
            
                // 当前用户的提问
                $raw_current = $comment->comment_content;
                $current_q   = two_ber_clean_user_question( trim( preg_replace( '/@2ber/i', '', $raw_current ) ), $article_text );
                array_unshift( $messages, array( 'role' => 'user', 'content' => $current_q ) );
            
                // 构建系统消息
                $system = two_ber_ai_default_prompt( $article_text );
            
                // 如果有引用的父评论内容，附加到系统消息
                if ( ! empty( $parent_content ) ) {
                    $system .= "\n\n楼层中都回复了以下评论：\n---\n{$parent_content}\n---\n请结合评论内容回答用户的问题。";
                }
            
                array_unshift( $messages, array( 'role' => 'system', 'content' => $system ) );
            
                return array(
                    'article_content' => $article_text,
                    'messages'        => $messages,
                );
            }
            /**
             * 后台执行：调用 Kimi API 并插入子评论
             *
             * @param int $comment_id 原评论 ID
             */
            function two_ber_ai_process_reply( $comment_id ) {
                // 再次检查是否已处理（防止并发/processing）
                if ( get_comment_meta( $comment_id, '_2ber_ai_replied', true ) ) {
                    delete_comment_meta( $comment_id, '_2ber_ai_processing' );
                    return;
                }
            
                // 获取原评论对象
                $comment = get_comment( $comment_id );
                if ( ! $comment ) {
                    delete_comment_meta( $comment_id, '_2ber_ai_processing' );
                    return;
                }
            
                $post_id    = $comment->comment_post_ID;
                $post       = get_post( $post_id );
                if ( ! $post ) {
                    delete_comment_meta( $comment_id, '_2ber_ai_processing' );
                    return;
                }
            
                // 获取对话上下文
                $context = two_ber_get_conversation_context( $comment_id );
                if ( ! $context ) return;
                
                // 调用 Kimi API
                $reply_content = two_ber_ai_call_kimi( $context['messages'] );
            
                // 如果 API 调用失败，记录错误并清理标记
                if ( is_wp_error( $reply_content ) ) {
                    error_log( '2BER AI Reply Error: ' . $reply_content->get_error_message() );
                    delete_comment_meta( $comment_id, '_2ber_ai_processing' );
                    return;
                }
            
                // 插入 AI 回复作为子评论
                $ai_comment_data = two_ber_ai_comment_data($post_id, $comment_id, $reply_content);
                // 使 AI 回复跟随父评论的审核状态（避免新用户待审核时 AI 回复却直接显示）
                $parent_status = $comment->comment_approved;
                $ai_comment_data['comment_approved'] = $parent_status;
                $ai_comment_id = wp_insert_comment( $ai_comment_data );
            
                if ( $ai_comment_id ) {
                    // 标记原评论已获得 AI 回复，避免重复触发
                    update_comment_meta( $comment_id, '_2ber_ai_replied', 1 );
                    // 给 AI 回复本身打上标记，防止其再次触发 @2BER 逻辑
                    update_comment_meta( $ai_comment_id, '_2ber_ai_reply', 1 );
                }
            
                // 清除处理中标记
                delete_comment_meta( $comment_id, '_2ber_ai_processing' );
            }
            
            /**
             * 调用 Kimi (Moonshot) Chat Completions API
             *
             * @param array $messages 对话消息数组
             * @return string|WP_Error 成功返回回复文本，失败返回 WP_Error
             */
            function two_ber_ai_call_kimi( $messages ) {
                $api_key = TWO_BER_AI_API_KEY;
                $url = get_option('site_chatgpt_proxy') . get_option('site_chatgpt_apis');
            
                $body = array(
                    'model'       => TWO_BER_AI_MODEL,
                    'messages'    => $messages,
                    'temperature' => 0.3,
                );
            
                $args = array(
                    'timeout'     => 30,
                    'headers'     => array(
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type'  => 'application/json',
                    ),
                    'body'        => wp_json_encode( $body ),
                );
            
                $max_retries = 2; // 额外重试 2 次，总共最多 3 次尝试
                $retry_delay = 2; // 秒
            
                for ( $attempt = 0; $attempt <= $max_retries; $attempt++ ) {
                    $response = wp_remote_post( $url, $args );
            
                    if ( is_wp_error( $response ) ) {
                        // 网络错误不重试，直接返回
                        return $response;
                    }
            
                    $http_code = wp_remote_retrieve_response_code( $response );
                    $body_str  = wp_remote_retrieve_body( $response );
                    $result    = json_decode( $body_str, true );
            
                    // 检查是否 overload
                    $is_overload = false;
                    if ( $http_code === 200 && ! empty( $result['choices'][0]['message']['content'] ) ) {
                        // 成功：直接返回内容
                        return $result['choices'][0]['message']['content'];
                    } elseif ( $http_code === 429 || $http_code === 503 ) {
                        // 限流或服务不可用，可能 overload
                        $is_overload = true;
                    } elseif ( isset( $result['error']['message'] ) && stripos( $result['error']['message'], 'overload' ) !== false ) {
                        $is_overload = true;
                    }
            
                    if ( $is_overload && $attempt < $max_retries ) {
                        // 等待后重试
                        sleep( $retry_delay );
                        continue;
                    }
            
                    // 其他错误或重试次数用尽
                    $error_msg = isset( $result['error']['message'] ) ? $result['error']['message'] : '未知错误';
                    return new WP_Error( 'ai_api_error', $error_msg );
                }
            
                // 理论上不会走到这里，但以防万一
                return new WP_Error( 'ai_api_error', '重试次数用尽，仍失败' );
            }
            
            /**
             * 附加防护：在评论保存前，如果检测到是 AI 回复，直接跳过后续处理。
             * 尽管我们已经在后台任务里标记了 meta，但在极短时间内如果再触发可能仍会重复，
             * 这里多一层保险。
             */
            add_action( 'wp_insert_comment', function ( $comment_id, $comment ) {
                // 如果这条评论是 AI 回复（有 meta 标记），则不做任何 @2BER 检测
                if ( get_comment_meta( $comment_id, '_2ber_ai_reply', true ) ) {
                    // 直接移除可能被 comment_post 添加的处理标记（理论上不会发生）
                    delete_comment_meta( $comment_id, '_2ber_ai_processing' );
                }
            }, 10, 2 );
            
            /**
             * 获取已存在的 AI 子回复（如果存在）
             *
             * @param int $comment_id 原评论 ID
             * @return WP_Comment|null
             */
            function two_ber_get_existing_ai_reply( $comment_id ) {
                $replies = get_comments( array(
                    'parent'  => $comment_id,
                    'meta_key'=> '_2ber_ai_reply',
                    'number'  => 1,
                ) );
            
                return ! empty( $replies ) ? $replies[0] : null;
            }
            
            /**
             * 注册 REST API 重试机制
             * （重试次数 + 冷却限制 + 前端 nonce 校验）
             */
            add_action( 'rest_api_init', function () {
                register_rest_route( 'two-ber/v1', '/ai-retry', array(
                    'methods'  => 'GET',
                    'callback' => function ( $request ) {
                        $comment_id = $request->get_param( 'comment_id' );
                        $comment    = get_comment( $comment_id );
                        if ( ! $comment ) {
                            return new WP_Error( 'not_found', '评论不存在', array( 'status' => 404 ) );
                        }
            
                        // 已有回复，直接返回
                        $existing = two_ber_get_existing_ai_reply( $comment_id );
                        if ( $existing ) {
                            return rest_ensure_response( array(
                                'success'  => true,
                                'cached'   => true,
                                'reply_id' => $existing->comment_ID,
                                'reply_content'    => $existing->comment_content,
                            ) );
                        }
                        
                        // 冷却检查
                        $cooldown = 30;
                        $last_retry = get_transient( 'ai_retry_' . $comment_id );
                        if ( $last_retry ) {
                            $remaining = $cooldown - ( time() - $last_retry );
                            return new WP_Error( 'retry_cooldown', "请等待 {$remaining} 秒后再试", array( 'status' => 429 ) );
                        }
                        set_transient( 'ai_retry_' . $comment_id, time(), $cooldown );
            
                        // 清理旧标记，准备重新触发
                        delete_comment_meta( $comment_id, '_2ber_ai_replied' );
                        delete_comment_meta( $comment_id, '_2ber_ai_processing' );
            
                        // 设置处理中标记，并安排后台任务（与自动回复完全相同的钩子）
                        update_comment_meta( $comment_id, '_2ber_ai_processing', 1 );
                        wp_clear_scheduled_hook( 'two_ber_ai_reply_event', array( $comment_id ) );
                        wp_schedule_single_event( time() + 5, 'two_ber_ai_reply_event', array( $comment_id ) );
            
                        // 立即返回，告诉前端“已安排，请轮询”
                        return rest_ensure_response( array(
                            'success'  => true,
                            'scheduled'=> true,
                        ) );
                    },
                    'permission_callback' => function ( $request ) {
                        $nonce = $request->get_param( '_wpnonce' );
                        return $nonce && wp_verify_nonce( $nonce, 'wp_rest' );
                    },
                    'args' => array(
                        'comment_id' => array(
                            'required' => true,
                            'type'     => 'integer',
                        ),
                        '_wpnonce' => array(
                            'required' => true,
                            'type'     => 'string',
                        ),
                    ),
                ) );
            } );
            
            
            /**
             * 管理员手动重新生成 AI 回复（直接修改已存在的 AI 子评论内容）
             */
            add_action( 'rest_api_init', function () {
                register_rest_route( 'two-ber/v1', '/ai-regenerate', array(
                    'methods'             => 'GET',
                    'callback'            => function ( $request ) {
                        $comment_id = $request->get_param( 'comment_id' );
                        $comment    = get_comment( $comment_id );
                        if ( ! $comment ) {
                            return new WP_Error( 'not_found', '评论不存在', array( 'status' => 404 ) );
                        }
            
                        // 1. 找到该评论下的 AI 子回复
                        $ai_reply = two_ber_get_existing_ai_reply( $comment_id );
                        if ( ! $ai_reply ) {
                            return new WP_Error( 'no_ai_reply', '该评论没有 AI 回复，无法重新生成', array( 'status' => 400 ) );
                        }
            
                        // 2. 获取对话上下文（与原评论一致）
                        $context = two_ber_get_conversation_context( $comment_id );
                        if ( ! $context ) {
                            return new WP_Error( 'context_error', '无法获取对话上下文', array( 'status' => 500 ) );
                        }
            
                        // 3. 调用 Kimi 生成新回复
                        $new_reply = two_ber_ai_call_kimi( $context['messages'] );
                        if ( is_wp_error( $new_reply ) ) {
                            return new WP_Error( 'ai_error', $new_reply->get_error_message(), array( 'status' => 502 ) );
                        }
            
                        // 4. 更新已有 AI 评论的内容
                        $result = wp_update_comment( array(
                            'comment_ID'      => $ai_reply->comment_ID,
                            'comment_content' => $new_reply,
                        ) );
            
                        if ( ! $result ) {
                            return new WP_Error( 'update_failed', 'AI 回复更新失败', array( 'status' => 500 ) );
                        }
            
                        // 可选：清除可能残留的 processing 标记（保证状态干净）
                        delete_comment_meta( $comment_id, '_2ber_ai_processing' );
            
                        return rest_ensure_response( array(
                            'success'    => true,
                            'reply'      => $new_reply,
                            'reply_id'   => $ai_reply->comment_ID,
                        ) );
                    },
                    'permission_callback' => function ( $request ) {
                        // 仅管理员可操作
                        if ( ! current_user_can( 'moderate_comments' ) ) {
                            return new WP_Error( 'rest_forbidden', '没有权限', array( 'status' => 403 ) );
                        }
                        // Nonce 验证
                        $nonce = $request->get_param( '_wpnonce' );
                        return $nonce && wp_verify_nonce( $nonce, 'wp_rest' );
                    },
                    'args' => array(
                        'comment_id' => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'sanitize_callback' => 'absint',
                        ),
                        '_wpnonce' => array(
                            'required' => true,
                            'type'     => 'string',
                        ),
                    ),
                ) );
            } );
            /**
             * 后台评论列表增加“AI 回复状态”列 + 重试按钮
             */
            add_filter( 'manage_edit-comments_columns', function ( $columns ) {
                $columns['ai_reply_status'] = 'AI 评论状态';
                return $columns;
            } );
            
            add_action( 'manage_comments_custom_column', function ( $column, $comment_id ) {
                if ( 'ai_reply_status' !== $column ) return;
            
                $replied = get_comment_meta( $comment_id, '_2ber_ai_replied', true );
                $processing = get_comment_meta( $comment_id, '_2ber_ai_processing', true );
            
                $nonce = wp_create_nonce( 'wp_rest' );
                
                if ( $replied ) {
                    // echo '<span style="color:green;">✅ 已完成</span> ';
                    $regenerate_url = rest_url( 'two-ber/v1/ai-regenerate?comment_id=' . $comment_id . '&_wpnonce=' . $nonce );
                    echo '<button style="margin-left:6px;" type="button" class="button button-small ai-regenerate-btn" data-url="' . esc_url( $regenerate_url ) . '">✅ 重新生成</button>';
                    echo '<span class="ai-regenerate-msg" style="margin-left:6px;"></span>';
                    return;
                }
            
                if ( $processing ) {
                    $retry_url = rest_url( 'two-ber/v1/ai-retry?comment_id=' . $comment_id . '&_wpnonce=' . $nonce );
                    echo '<span style="color:orange;">⏳ 处理中</span> ';
                    echo '<button type="button" class="button button-small ai-retry-btn" data-url="' . esc_url( $retry_url ) . '">重试</button>';
                    echo '<span class="ai-retry-msg" style="margin-left:6px;"></span>';
                    return;
                }
            
                echo '<span style="color:#999;">—</span>';
            }, 10, 2 );
            
            /**
             * 在后台评论页面加载内联脚本，处理重试点击
             */
            add_action( 'admin_footer-edit-comments.php', function () {
                ?>
                <script>
                    jQuery(function($) {
                        // 处理重试按钮（未完成 -> 重试）
                        $(document).on('click', '.ai-retry-btn', function() {
                            // if(!confirm('确认重新请求AI评论吗？')) return;
                            var $btn = $(this);
                            var url = $btn.data('url');
                            var $msg = $btn.next('.ai-retry-msg');
                            
                            $btn.prop('disabled', true).text('请求中...');
                            $msg.text('');
                    
                            $.get(url, function(data) {
                                if (data.success) {
                                    $msg.css('color', 'green').text('已触发，请稍后刷新');
                                    $btn.remove();
                                    $btn.parent().find('span').first().text('⏳ 等待中');
                                } else {
                                    $msg.css('color', 'red').text('失败');
                                    $btn.prop('disabled', false).text('重试');
                                }
                            }).fail(function() {
                                $msg.css('color', 'red').text('网络错误');
                                $btn.prop('disabled', false).text('重试');
                            });
                        });
                    
                        // 处理重新生成按钮（已完成 -> 重新生成）
                        $(document).on('click', '.ai-regenerate-btn', function() {
                            if(!confirm('确认重新生成AI评论吗？')) return;
                            var $btn = $(this);
                            var url = $btn.data('url');
                            var $msg = $btn.next('.ai-regenerate-msg');
                            
                            $btn.prop('disabled', true).text('生成中...');
                            $msg.text('');
                    
                            $.get(url, function(data) {
                                if (data.success) {
                                    $msg.css('color', 'green').html(`已重新生成，请<a href="">刷新页面</a>查看`);
                                    $btn.prop('disabled', false).text('重新生成'); // 恢复，允许再次生成
                                } else {
                                    $msg.css('color', 'red').text('失败');
                                    $btn.prop('disabled', false).text('重新生成');
                                }
                            }).fail(function() {
                                $msg.css('color', 'red').text('网络错误');
                                $btn.prop('disabled', false).text('重新生成');
                            });
                        });
                        
                        // 处理审核撤销
                        $(document).on('click', '.undo-spam-btn', function() {
                            var $btn = $(this);
                            var url = $btn.data('url');
                            var $msg = $btn.next('.undo-spam-msg');
                    
                            $btn.prop('disabled', true).text('处理中...');
                            $msg.text('');
                    
                            $.get(url, function(data) {
                                if (data.success) {
                                    $msg.css('color', 'green').text('已恢复');
                                    // 更新列状态：移除按钮，显示已撤销
                                    $btn.remove();
                                    $btn.parent().find('span').first().html('✔️ 已撤销');
                                } else {
                                    $msg.css('color', 'red').text('操作失败');
                                    $btn.prop('disabled', false).text('撤销误判');
                                }
                            }).fail(function() {
                                $msg.css('color', 'red').text('网络错误');
                                $btn.prop('disabled', false).text('撤销误判');
                            });
                        });
                    });
                </script>
                <?php
            } );
            
            /**
             * 注册 AI 回复状态查询端点
             */
            add_action( 'rest_api_init', function () {
                register_rest_route( 'two-ber/v1', '/ai-reply-status', array(
                    'methods'             => 'GET',
                    'callback'            => 'two_ber_ai_reply_status',
                    'permission_callback' => '__return_true',   // 可根据需要改为 is_user_logged_in()
                    'args'                => array(
                        'comment_id' => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'sanitize_callback' => 'absint',
                        ),
                    ),
                ) );
            } );
            
            /**
             * 查询某条评论的 AI 回复状态
             *
             * @param WP_REST_Request $request
             * @return WP_REST_Response
             */
            function two_ber_ai_reply_status( $request ) {
                $comment_id = $request->get_param( 'comment_id' );
                $comment    = get_comment( $comment_id );
            
                if ( ! $comment ) {
                    return new WP_Error( 'not_found', '评论不存在', array( 'status' => 404 ) );
                }
            
                // 1. 已有 AI 回复 → 完成
                $existing = two_ber_get_existing_ai_reply( $comment_id );
                if ( $existing ) {
                    return rest_ensure_response( array(
                        'status'       => 'completed',
                        'reply_id'     => $existing->comment_ID,
                        'reply_content'=> $existing->comment_content,
                    ) );
                }
            
                // 2. 正在处理中
                if ( get_comment_meta( $comment_id, '_2ber_ai_processing', true ) ) {
                    return rest_ensure_response( array(
                        'status' => 'processing',
                    ) );
                }
            
                // 3. 未触发或失败
                return rest_ensure_response( array(
                    'status' => 'none',
                ) );
            }
            /**
             * 在评论 REST API 响应中注册自定义字段 two_ber_ai_pending
             * （前端可据此立即渲染占位评论）
             */
            add_action( 'rest_api_init', function () {
                register_rest_field( 'comment', 'two_ber_ai_pending', array(
                    'get_callback' => function ( $comment_arr ) {
                        $comment_id = $comment_arr['id'];
                        // 正在处理且尚未有回复 → true
                        if ( get_comment_meta( $comment_id, '_2ber_ai_processing', true )
                             && ! get_comment_meta( $comment_id, '_2ber_ai_replied', true ) ) {
                            return true;
                        }
                        return false;
                    },
                    'schema' => array(
                        'description' => '是否等待 AI 回复中',
                        'type'        => 'boolean',
                    ),
                ) );
            } );
        }
        
        /**
         * 
         * AI评论审查，涵盖垃圾检测、走心评论等（异步 + 本地前置过滤）
         *
         * @param string $comment_content 待审核的评论内容
         * @return bool true=垃圾，false=正常
         */
        
        if (get_option('site_chatgpt_ai_auditor')) {
            
            define( 'TWO_BER_AI_SPAM_CHECK_ENABLED', true );
            define( 'TWO_BER_AI_SPAM_CHECK_GUESTS_ONLY', true );
            define( 'TWO_BER_AI_SPAM_FAIL_ACTION', 'allow' );
            
            // ---------- 原有函数，完全不变 ----------
            function two_ber_ai_spam_filter( $comment_content ) {
                if ( ! TWO_BER_AI_SPAM_CHECK_ENABLED ) {
                    return array( 'is_spam' => false, 'reason' => '' );
                }
        
                if ( TWO_BER_AI_SPAM_CHECK_GUESTS_ONLY && is_user_logged_in() ) {
                    return array( 'is_spam' => false, 'reason' => '' );
                }
        
                $content = wp_strip_all_tags( trim( $comment_content ) );
                if ( empty( $content ) ) {
                    return array( 'is_spam' => false, 'reason' => '' );
                }
        
                $spam_samples = two_ber_get_spam_samples( 6 );
                $ham_samples  = two_ber_get_ham_samples( 3 );
                $thoughtful_samples = two_ber_get_thoughtful_samples( 4 );  // 使用新函数
            
                $system  = "你是一个专业的评论审查员，需要完成两个任务：\n";
                $system .= "1. 判断评论是否为垃圾（is_spam）。\n";
                $system .= "2. 判断评论是否属于“走心评论”（is_thoughtful）。\n\n";
                $system .= "垃圾评论的典型特征：无关广告/外链、诱导点击、虚假夸奖并附带推广、纯SEO关键词堆砌、完全无意义的乱码。\n";
                $system .= "走心评论的特征：内容不能太短，与文章内容高度相关、表达出真实的情感或深刻见解、有实质性内容、条理清晰、能引发讨论或补充有价值的信息。\n";
                $system .= "**注意**：一条评论可以同时是走心且非垃圾，但不能既是垃圾又是走心。若评论已被判定为垃圾，is_thoughtful 必须为 false。\n\n";
            
                $system .= "【走心评论示例】\n";
                foreach ( $thoughtful_samples as $i => $example ) {
                    $system .= ($i+1) . ". " . $example . "\n";
                }
            
                $system .= "\n【垃圾评论示例】\n";
                foreach ( $spam_samples as $i => $spam ) {
                    $system .= ($i+1) . ". " . $spam . "\n";
                }
            
                $system .= "\n【一般评论示例】（非垃圾，但也不够走心）\n";
                foreach ( $ham_samples as $i => $ham ) {
                    $system .= ($i+1) . ". " . $ham . "\n";
                }
            
                $system .= "\n请严格参照以上示例，仅以JSON格式返回：{\"is_spam\": bool, \"reason\": \"垃圾理由\", \"is_thoughtful\": bool}";
        
                $messages = array(
                    array( 'role' => 'system', 'content' => $system ),
                    array( 'role' => 'user',   'content' => "评论内容：" . $content ),
                );
            
                $result = two_ber_ai_call_spam_api( $messages );
            
                if ( is_wp_error( $result ) ) {
                    error_log( 'AI Spam Filter API Error: ' . $result->get_error_message() );
                    return array(
                        'is_spam'       => ( TWO_BER_AI_SPAM_FAIL_ACTION === 'block' ),
                        'reason'        => 'API错误',
                        'is_thoughtful' => false,   // 失败时默认不标记
                    );
                }
            
                $decoded = json_decode( $result, true );
                if ( ! is_array( $decoded ) || ! isset( $decoded['is_spam'] ) ) {
                    error_log( 'AI Spam Filter Invalid JSON: ' . $result );
                    return array(
                        'is_spam'       => ( TWO_BER_AI_SPAM_FAIL_ACTION === 'block' ),
                        'reason'        => '格式错误',
                        'is_thoughtful' => false,
                    );
                }
            
                return array(
                    'is_spam'       => (bool) $decoded['is_spam'],
                    'reason'        => $decoded['reason'] ?? '未提供理由',
                    'is_thoughtful' => isset( $decoded['is_thoughtful'] ) ? (bool) $decoded['is_thoughtful'] : false,
                );
            }
        
            function two_ber_get_ham_samples( $count = 2 ) {
                $comments = get_comments( array(
                    'status' => 'approve',
                    'number' => $count,
                    'orderby'=> 'comment_date_gmt',
                    'order'  => 'DESC',
                ) );
        
                $samples = array();
                foreach ( $comments as $c ) {
                    $text = wp_strip_all_tags( trim( $c->comment_content ) );
                    if ( ! empty( $text ) ) {
                        $samples[] = $text;
                    }
                }
                while ( count( $samples ) < $count ) {
                    $samples[] = '谢谢分享，这篇文章对我很有帮助。';
                }
                return array_slice( $samples, 0, $count );
            }
            
            function two_ber_get_spam_samples( $count = 4 ) {
                $comments = get_comments( array(
                    'status' => 'spam',
                    'number' => $count,
                    'orderby'=> 'comment_date_gmt',
                    'order'  => 'DESC',
                ) );
        
                $samples = array();
                foreach ( $comments as $c ) {
                    $text = wp_strip_all_tags( trim( $c->comment_content ) );
                    if ( ! empty( $text ) ) {
                        $samples[] = $text;
                    }
                }
                while ( count( $samples ) < $count ) {
                    $samples[] = 'Buy cheap pills online http://spam.com';
                }
                return array_slice( $samples, 0, $count );
            }
        
            function two_ber_get_thoughtful_samples( $count = 4 ) {
                $samples = array();
            
                $thoughtful_comments = get_comments( array(
                    'meta_key'   => '_thoughtful_comment',
                    'meta_value' => '1',
                    'status'     => 'approve',
                    'number'     => $count,
                    'orderby'    => 'comment_date_gmt',
                    'order'      => 'DESC',
                ) );
            
                foreach ( $thoughtful_comments as $c ) {
                    $text = wp_strip_all_tags( trim( $c->comment_content ) );
                    if ( ! empty( $text ) ) {
                        $samples[] = $text;
                    }
                }
            
                // 预设兜底示例
                $defaults = array(
                    "这篇文章分析得很透彻，特别是关于 WordPress 对象缓存的原理，解决了我长期困扰的数据库查询瓶颈。我根据你的建议改用 Redis 后，首页响应时间从 1.2s 降到了 0.3s，太感谢了！期待更多性能优化专题。",
                    "作者的文笔真的很细腻，把夏日蝉鸣和童年回忆交织在一起，让我仿佛回到了外婆家的老院子。最后一段的留白恰到好处，给读者留下了无限的遐想空间，好久没读到这么温暖的文章了。",
                    "关于 React 状态管理，我想补充一点实战经验：除了文中提到的 Redux Toolkit，我们团队最近在复杂表单中采用了 useReducer + immer，代码量减少 40% 且可读性大幅提升。这种模式特别适合多字段联动的场景，希望能对其他读者有所启发。",
                    "读完这篇对《百年孤独》的解读，我对马尔克斯的魔幻现实主义有了更深的理解。你提到的‘孤独是命运的底色’这个观点很新颖，结合布恩迪亚家族的轮回，确实能看出作者对人类宿命的深刻洞察。顺便推荐《霍乱时期的爱情》，也是经典。",
                );
            
                while ( count( $samples ) < $count ) {
                    $samples[] = array_shift( $defaults );
                }
            
                return array_slice( $samples, 0, $count );
            }
        
            function two_ber_ai_call_spam_api( $messages ) {
                $api_key = TWO_BER_AI_API_KEY;
                $url = get_option('site_chatgpt_proxy') . get_option('site_chatgpt_apis');
        
                $body = array(
                    'model'       => TWO_BER_AI_MODEL,
                    'messages'    => $messages,
                    'temperature' => 0.1,
                    'max_completion_tokens'  => 150,
                );
        
                $args = array(
                    'timeout'     => 15,
                    'headers'     => array(
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type'  => 'application/json',
                    ),
                    'body'        => wp_json_encode( $body ),
                );
        
                $response = wp_remote_post( $url, $args );
                if ( is_wp_error( $response ) ) {
                    return $response;
                }
        
                $http_code = wp_remote_retrieve_response_code( $response );
                $body_str  = wp_remote_retrieve_body( $response );
                $result    = json_decode( $body_str, true );
        
                if ( $http_code === 200 && ! empty( $result['choices'][0]['message']['content'] ) ) {
                    return $result['choices'][0]['message']['content'];
                }
        
                return new WP_Error( 'spam_api_error', '审核 API 请求失败' );
            }
        
            // ---------- 1. 本地前置过滤（不阻塞，只使用 WordPress 原生关键词） ----------
            add_filter( 'preprocess_comment', function ( $commentdata ) {
                $content = $commentdata['comment_content'];
        
                // 黑名单关键词 → 直接拦截
                $disallowed = get_option( 'disallowed_keys' );
                if ( ! empty( $disallowed ) ) {
                    $keys = explode( "\n", $disallowed );
                    foreach ( $keys as $key ) {
                        $key = trim( $key );
                        if ( empty( $key ) ) continue;
                        $pattern = '/\b' . preg_quote( $key, '/' ) . '\b/i';
                        if ( preg_match( $pattern, $content ) ) {
                            wp_die( '您的评论被系统识别为垃圾信息，如有误判请联系管理员。', '评论拦截', array( 'response' => 403 ) );
                        }
                    }
                }
        
                // 审核关键词 → 进入待审核（不再进入异步 AI 审核）
                $moderation = get_option( 'moderation_keys' );
                if ( ! empty( $moderation ) ) {
                    $keys = explode( "\n", $moderation );
                    foreach ( $keys as $key ) {
                        $key = trim( $key );
                        if ( empty( $key ) ) continue;
                        $pattern = '/\b' . preg_quote( $key, '/' ) . '\b/i';
                        if ( preg_match( $pattern, $content ) ) {
                            $commentdata['comment_approved'] = 0;
                            // 标记已由本地处理，异步任务看到后会跳过
                            $commentdata['_local_moderated'] = true;
                            return $commentdata;
                        }
                    }
                }
        
                // // ---- 3. 自定义本地硬规则（前端过滤）
                // $clean_content = wp_strip_all_tags( $content );
                // $site_comment_blacklists = get_option('site_comment_blacklists');
                // $spam_phrases = $site_comment_blacklists ? explode('|', $site_comment_blacklists) : [ 'cheap pills', 'buy now', 'viagra', 'casino', '赚取', '加微信', '加Q', '免费领取' ];
                // $lower = mb_strtolower( $clean_content );
                // foreach ( $spam_phrases as $phrase ) {
                //     if ( mb_stripos( $lower, $phrase ) !== false ) {
                //         wp_die( '您的评论包含违规内容，如有误判请联系管理员。', '评论拦截', [ 'response' => 403 ] );
                //     }
                // }
            
                return $commentdata;
            }, 0 ); // 优先级 0，最早执行
        
            // ---------- 2. 异步 AI 审核任务 ----------
            add_action( 'comment_post', function ( $comment_id, $comment_approved, $commentdata ) {
                // 跳过已确定的垃圾、回收站
                if ( $comment_approved === 'spam' || $comment_approved === 'trash' ) return;
                // 跳过登录用户（按配置）
                if ( TWO_BER_AI_SPAM_CHECK_GUESTS_ONLY && is_user_logged_in() ) return;
                // 跳过已被本地审核关键词挂起的评论
                if ( ! empty( $commentdata['_local_moderated'] ) ) return;
                // 避免重复计划
                if ( get_comment_meta( $comment_id, '_ai_spam_review_planned', true ) ) return;
                
                // 新增：如果该评论即将触发 AI 回复，则不再进行 AI 反垃圾审核
                $content = $commentdata['comment_content'];
                $is_reply_to_ai = ( ! empty( $commentdata['comment_parent'] ) && get_comment_meta( $commentdata['comment_parent'], '_2ber_ai_reply', true ) );
                if ( preg_match( '/@2ber/i', $content ) || $is_reply_to_ai ) return; // 直接返回，不计划反垃圾任务
        
                update_comment_meta( $comment_id, '_ai_spam_review_planned', 1 );
                wp_schedule_single_event( time() + 5, 'two_ber_ai_spam_review', array( $comment_id ) );
            }, 11, 3 );
        
            // 注册异步审核动作
            add_action( 'two_ber_ai_spam_review', function ( $comment_id ) {
                $comment = get_comment( $comment_id );
                if ( ! $comment || $comment->comment_approved === 'spam' || $comment->comment_approved === 'trash' ) {
                    delete_comment_meta( $comment_id, '_ai_spam_review_planned' );
                    return;
                }
        
                // 再次确认没有被本地关键词挂起（保险）
                if ( $comment->comment_approved == 0 && get_comment_meta( $comment_id, '_local_moderated', true ) ) {
                    delete_comment_meta( $comment_id, '_ai_spam_review_planned' );
                    return;
                }
        
                $result = two_ber_ai_spam_filter( $comment->comment_content );

                if ( $result['is_spam'] ) {
                    wp_spam_comment( $comment_id );
                    update_comment_meta( $comment_id, '_ai_spam_reason', $result['reason'] );
                    error_log( "Async AI spam caught: comment_id=$comment_id reason={$result['reason']}" );
                } else {
                    // 非垃圾，处理走心标记（仅当AI判定为走心时才自动设置）
                    if ( $result['is_thoughtful'] ) {
                        update_comment_meta( $comment_id, '_thoughtful_comment', 1 );
                    }
                    // 如果之前被标记为走心但AI这次没判为走心，要不要清除？不建议，因为管理员可能已手动设置。
                    // 所以此处只做“首次设置”或“补充设置”，不覆盖已有值。
                    // 若希望AI权重更高，可以加上 update_comment_meta( $comment_id, '_thoughtful_comment', $result['is_thoughtful'] ? 1 : 0 );
                    // 这里采用“仅当元数据不存在时设置”，保留管理员手动结果。
                    if ( ! metadata_exists( 'comment', $comment_id, '_thoughtful_comment' ) && $result['is_thoughtful'] ) {
                        update_comment_meta( $comment_id, '_thoughtful_comment', 1 );
                    }
                }
            
                delete_comment_meta( $comment_id, '_ai_spam_review_planned' );
            } );
        
            // ---------- 3. 后台显示 AI 拦截原因 ----------
            add_filter( 'manage_edit-comments_columns', function ( $columns ) {
                $columns['ai_spam_reason'] = 'AI 审核意见';
                return $columns;
            } );
        
            add_action( 'manage_comments_custom_column', function ( $column, $comment_id ) {
                if ( 'ai_spam_reason' === $column ) {
                    $comment = get_comment( $comment_id );
                    $reason = get_comment_meta( $comment_id, '_ai_spam_reason', true );
            
                    if ( $comment && $comment->comment_approved === 'spam' ) {
                        // 垃圾评论：显示理由 + 撤销按钮
                        echo '<span style="color:red;">🚫 ' . esc_html( $reason ?: '被AI拦截') . '</span> ';
                        $nonce = wp_create_nonce( 'wp_rest' );
                        $undo_url = rest_url( 'two-ber/v1/undo-spam?comment_id=' . $comment_id . '&_wpnonce=' . $nonce );
                        echo '<button type="button" class="button button-small undo-spam-btn" data-url="' . esc_url( $undo_url ) . '">撤销误判</button>';
                        echo '<span class="undo-spam-msg" style="margin-left:6px;"></span>';
                    } elseif ( $reason ) {
                        // 已批准但有理由（可能已被撤销过） -> 仅显示理由
                        echo '已撤销（' . esc_html( $reason ).'）';
                    } else {
                        // 无理由的正常评论
                        echo '✔️';
                    }
                }
            }, 10, 2 );
            
            // 注册 rest 撤销误判
            add_action( 'rest_api_init', function () {
                register_rest_route( 'two-ber/v1', '/undo-spam', array(
                    'methods'             => 'GET',
                    'callback'            => function ( $request ) {
                        $comment_id = $request->get_param( 'comment_id' );
                        $comment = get_comment( $comment_id );
                        if ( ! $comment ) {
                            return new WP_Error( 'not_found', '评论不存在', array( 'status' => 404 ) );
                        }
                        if ( ! current_user_can( 'moderate_comments' ) ) {
                            return new WP_Error( 'rest_forbidden', '没有权限', array( 'status' => 403 ) );
                        }
            
                        // 恢复为已批准
                        wp_set_comment_status( $comment_id, 'approve' );
            
                        // 清除 AI 拦截相关元数据
                        delete_comment_meta( $comment_id, '_ai_spam_review_planned' );
                        // delete_comment_meta( $comment_id, '_ai_spam_reason' );
                        // 可选：如果还有 _two_ber_spam_reason 等旧标记，一并清除
                        // delete_comment_meta( $comment_id, '_two_ber_spam_reason' );
            
                        return rest_ensure_response( array(
                            'success' => true,
                            'message' => '评论已恢复并清除拦截记录',
                        ) );
                    },
                    'permission_callback' => function ( $request ) {
                        $nonce = $request->get_param( '_wpnonce' );
                        return $nonce && wp_verify_nonce( $nonce, 'wp_rest' );
                    },
                    'args' => array(
                        'comment_id' => array( 'required' => true, 'type' => 'integer' ),
                        '_wpnonce'   => array( 'required' => true, 'type' => 'string' ),
                    ),
                ) );
            } );
            
        }
    }
    
    
    /*
     *--------------------------------------------------------------------------
     * adsense_shortcode
     *--------------------------------------------------------------------------
    */
    function adsense_shortcode($shortcode = '', $returns = false) {
        $err_msg = '<script>console.log("adsense disabled.")</script>';
        if (!get_option('site_ads_switcher')) {
            echo $err_msg;
            return;
        }
        if (is_single()) {
            if (!get_option('site_ads_article')) {
                echo $err_msg;
                return;
            }
        }
        $res = do_shortcode("[$shortcode]");
        if ($returns) return $res;
        echo $res;
    }
    
    /*
     *--------------------------------------------------------------------------
     * RSS Feed Generator.
     *--------------------------------------------------------------------------
    */
    
    /**
     * 注册 RSS 聚合 REST API 路由
     */
    add_action('rest_api_init', function () {
        // 1. 获取指定分类的 RSS 聚合（公开）
        register_rest_route('rss-feeds/v1', '/category/(?P<slug>[a-zA-Z0-9_-]+)', [
            'methods'  => 'GET',
            'callback' => 'rest_get_rss_feeds',
            'args'     => [
                'limit'  => ['default' => 3, 'sanitize_callback' => 'absint'],
                'output' => ['default' => 'json', 'enum' => ['json', 'html']],
                'key'    => ['default' => ''],
                'value'  => ['default' => ''],
            ],
            'permission_callback' => '__return_true',
        ]);
    
        // 2. 强制更新某个分类的缓存（需管理员权限）
        register_rest_route('rss-feeds/v1', '/category/(?P<slug>[a-zA-Z0-9_-]+)/update', [
            'methods'  => ['GET', 'POST'],
            'callback' => 'rest_update_rss_feeds',
            'args'     => [
                'limit' => ['default' => 3, 'sanitize_callback' => 'absint'],
                'chunk' => ['default' => 10, 'sanitize_callback' => 'absint'],
            ],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);
    
        // 3. 清除所有（或指定）分类缓存（需管理员权限）
        register_rest_route('rss-feeds/v1', '/clear', [
            'methods'  => 'POST',
            'callback' => 'rest_clear_rss_cache',
            'args'     => [
                'slug' => ['default' => ''],
            ],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);
    });
    /**
     * 获取 RSS 聚合数据（JSON 或 HTML）
     */
    function rest_get_rss_feeds(WP_REST_Request $request) {
        $slug   = $request['slug'];
        $limit  = $request['limit'];
        $output = $request['output'];
        $key    = $request['key'];
        $value  = $request['value'];
    
        // 验证分类是否存在
        $links_slug = get_links_category('slug');
        if (!in_array($slug, $links_slug)) {
            return new WP_Error('invalid_category', 'Unknown category', ['status' => 404]);
        }
    
        // 缓存配置
        $cache_switcher = get_option('site_cache_switcher');
        $cache_includes = get_option('site_cache_includes');
        $cache_key      = 'site_rss_' . $slug . '_cache';
        $cache_enabled  = $cache_switcher && in_array('rssfeeds', explode(',', $cache_includes));
        $cached_json    = $cache_enabled ? get_option($cache_key) : '';
    
        // 如果有缓存且不需要更新，直接返回缓存内容
        if ($cache_enabled && $cached_json) {
            $data = json_decode($cached_json);
    
            // 过滤查询（key/value）
            if ($key !== '' && $value !== '') {
                $data = searchByKeyValue($data, $key, $value);
            }
    
            if ($output === 'html') {
                return rest_output_rss_html($data, $slug, $limit);
            }
            return rest_ensure_response($data);
        }
    
        // 无缓存且不允许实时抓取 → 返回空数据
        if ($output === 'html') {
            return new WP_REST_Response('<p>暂无内容，请检查 RSS 源或点击更新。</p>', 200, ['Content-Type' => 'text/html; charset=utf-8']);
        }
        return rest_ensure_response([
            'status'  => 'no_cache',
            'message' => 'No cached data available. Use update endpoint to regenerate.',
            'slug'    => $slug,
        ]);
    }
    
    /**
     * 强制更新指定分类的缓存（管理员专用）
     */
    function rest_update_rss_feeds(WP_REST_Request $request) {
        $slug  = $request['slug'];
        $limit = $request['limit'];
        $chunk = $request['chunk'];
    
        $links_slug = get_links_category('slug');
        if (!in_array($slug, $links_slug)) {
            return new WP_Error('invalid_category', 'Unknown category', ['status' => 404]);
        }
    
        // 获取该分类下的有效链接
        $link_marks = get_site_bookmarks($slug);
        $linked_urls = [];
        foreach ($link_marks as $link) {
            if (!empty($link->link_rss) && $link->link_visible === 'Y') {
                $linked_urls[] = $link;
            }
        }
    
        // 调用原有的抓取+解析函数
        $output_json = parse_rss_data($linked_urls, $limit, $chunk);
    
        // 更新缓存
        if ($output_json) {
            $cache_switcher = get_option('site_cache_switcher');
            $cache_includes = get_option('site_cache_includes');
            if ($cache_switcher && in_array('rssfeeds', explode(',', $cache_includes))) {
                update_option('site_rss_' . $slug . '_cache', $output_json);
            }
        }
    
        return rest_ensure_response([
            'success' => (bool)$output_json,
            'message' => $output_json ? 'Cache updated.' : 'No feeds found.',
            // 'data' => json_decode($output_json)
        ]);
    }
    
    /**
     * 清除缓存（支持单个或全部）
     */
    function rest_clear_rss_cache(WP_REST_Request $request) {
        $slug = $request['slug'];
        $links_slug = get_links_category('slug');
    
        if ($slug) {
            if (!in_array($slug, $links_slug)) {
                return new WP_Error('invalid_category', 'Unknown category', ['status' => 404]);
            }
            update_option('site_rss_' . $slug . '_cache', '');
        } else {
            foreach ($links_slug as $cat_slug) {
                update_option('site_rss_' . $cat_slug . '_cache', '');
            }
        }
        return rest_ensure_response(['success' => true, 'message' => 'Cache cleared.']);
    }
    
    /**
     * 输出 HTML 格式（复用原有 the_rss_feeds 的思路，但改为返回字符串）
     */
    function rest_output_rss_html($data, $slug, $limit) {
        ob_start();
        the_rss_feeds($data, $limit);
        $html = ob_get_clean();
        return new WP_REST_Response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
    
    function fetch_rss_feeds($rssUrl, $rssLink, $rssMax = 1) {
        if (!function_exists('fetch_feed')) {
            return null; // 或返回错误对象
        }
    
        // 为每个 RSS 源设置独立的缓存标识
        $cache_key = 'rss_feed_' . md5($rssUrl);
        $cached = get_transient($cache_key);
        if ($cached !== false && is_object($cached)) {
            return $cached; // 直接使用缓存的对象
        }
    
        $linkUrl    = $rssLink->link_url;
        $linkAuthor = $rssLink->link_name;
        $linkAvatar = $rssLink->link_image ?: '//cravatar.cn/avatar/?d=mp&s=50';
    
        $feed = fetch_feed($rssUrl);
        if (is_wp_error($feed)) {
            $error_class = new stdClass();
            $error_class->title  = '';
            $error_class->desc   = urlencode('获取 ta 的 rss 超时：<a href="' . $rssUrl . '" target="_blank">检查订阅</a>');
            $error_class->date   = '0000-00-00';
            $error_class->link   = 'javascript:;';
            $error_class->url    = $linkUrl;
            $error_class->rss    = $rssUrl;
            $error_class->author = $linkAuthor;
            $error_class->avatar = $linkAvatar;
            set_transient($cache_key, $error_class, 1800); // 缓存错误对象
            return $error_class;
        }
    
        $maxitems  = $feed->get_item_quantity($rssMax);
        $rss_items = $feed->get_items(0, $maxitems);
        if (empty($rss_items)) {
            return null;
        }
    
        $recent_post = $rss_items[0];
        $output_class = new stdClass();
        $output_class->title  = (string)$recent_post->get_title();
        $output_class->desc   = (string)mb_substr(strip_tags($recent_post->get_description()), 0, 200);
        $output_class->link   = (string)$recent_post->get_permalink();
        $output_class->date   = $recent_post->get_date("Y-m-d");
        $output_class->url    = $linkUrl;
        $output_class->rss    = $rssUrl;
        $output_class->author = $linkAuthor;
        $output_class->avatar = $linkAvatar;
    
        $rss_count = count($rss_items);
        if ($rss_count > 1) {
            $output_class->child = [];
            for ($i = 1; $i < $rss_count; $i++) {
                $item = $rss_items[$i];
                $child = new stdClass();
                $child->title = (string)$item->get_title();
                $child->desc  = mb_substr(strip_tags((string)$item->get_description()), 0, 200);
                $child->date  = date('Y-m-d', strtotime((string)$item->get_date("Y-m-d")));
                $child->link  = (string)$item->get_permalink();
                $output_class->child[] = $child;
            }
        }
    
        // 缓存成功结果 30 分钟
        set_transient($cache_key, $output_class, 1800);
        return $output_class;
    }
    
    function parse_rss_urls($link_marks, $output_limit) {
        // $output_array required to return in function (record lastUpdate date)
        date_default_timezone_set('Asia/Shanghai');
        $output_object = new stdClass();
        $output_object->lastUpdate = date("Y-m-d H:i:s");
        $output_array = array( 0 => $output_object );
        foreach ($link_marks as $link_mark) {
            // $link_rss = $link_mark->link_rss;
            // for ($i=0; $i<1; $i++) {
            //     $feed_data = fetch_rss_feeds($link_rss, $link_mark, $output_limit);
            //     if ($feed_data !== null) break; // 成功获取结果，跳出重试循环
            //     // sleep(1); // 等待后重试
            // }
            // array_push($output_array, $feed_data);
            $feed_data = fetch_rss_feeds($link_mark->link_rss, $link_mark, $output_limit);
            if ($feed_data !== null) {
                $output_array[] = $feed_data;
            }
        }
        return $output_array; //json_encode($output_array);
    }
    
    function parse_rss_data($link_marks, $output_limit, $output_chunk = 10) {
        $marks_count = count($link_marks);
        if ($marks_count <= $output_chunk) {
            $feed_data = parse_rss_urls($link_marks, $output_limit);
            return json_encode($feed_data);
        }
        // 删掉 echo，改为静默分块
        $output_arraies = array();
        $chunk_array = array_chunk($link_marks, $output_chunk);
        foreach ($chunk_array as $chunk) {
            $feed_data = parse_rss_urls($chunk, $output_limit);
            foreach ($feed_data as $data) {
                $output_arraies[] = $data;
            }
        }
        return json_encode($output_arraies);
    }
    
    function the_rss_feeds($output_data, $output_limit = 3, $output_order = SORT_DESC) {
        // print_r($output_data);
        $output_date = isset($output_data[0]->lastUpdate) ? $output_data[0]->lastUpdate : '0000-00-00';
        $output_string = '';
        if (!$output_data || count($output_data) <= 1) {
            $output_string = 'Empty RSS Data!! (lastUpdate: ' . $output_date . ')';
        } else {
            array_shift($output_data);  // no lastUpdate
            // 首先按日期降序排序，如果日期相同，则按标题升序排序
            array_multisort(array_map(function($item) {
                return isset($item->date) ? $item->date : null;
            }, $output_data), $output_order, array_map(function($item) {
                return isset($item->title) ? $item->title : null;
            }, $output_data), SORT_ASC, $output_data);
            foreach ($output_data as $data) {
                if (!isset($data->link) && !isset($data->title)) continue;
                $output_string .= '<div class="feeds">
                    <a href="'.$data->link.'" target="_blank">'.$data->title.'</a>
                    <p>'.urldecode($data->desc).'...</p>
                    <div class="info">
                        <a href="'.$data->rss.'" target="_blank">
                            <img src="'.$data->avatar.'" alt="'.$data->author.'" />
                        </a>
                        <a href="'.$data->url.'" target="_blank">
                            <b>'.$data->author.'</b>
                        </a>
                        <i class="pub">published at '.$data->date.'</i>
                    </div>';
                if (isset($data->child) && $output_limit > 0) {
                    $output_child = $data->child;
                    $output_count = count($output_child);
                    if ($output_limit > $output_count) $output_limit = $output_count;
                    $output_string .= '<details class="rest" close><summary> 浏览其余 '.$output_limit.' 篇文章 </summary><ol>';
                    foreach ($output_child as $key => $child) {
                        if ($key >= $output_limit) break;
                        $output_string .= '<li><a href="'.$child->link.'" target="_blank">'.$child->title.'</a>
                            <p class="content">'.urldecode($child->desc).'...</p>
                            <p class="pub">Published at '.$child->date.'</p></li>';
                    }
                    $output_string .= '</ol></details>';
                }
                $output_string .= '</div>'; //<hr />
            }
        }
        echo $output_string;
    }
    
    // 额外功能：输出友链html时调用（友链活性状态检测，根据此函数返回的最近rss年份，在后续输出友链时判断）
    function get_rss_data_by_cat($category = '', $format = false) {
        if (empty($category)) {
            return [];
        }
        $cache_key = 'site_rss_' . $category . '_cache';
        $cached_json = get_option($cache_key);
        if (empty($cached_json)) {
            return [];
        }
        return $format ? json_decode($cached_json) : $cached_json;
    }
    
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
        if($var) {
            return $res;
        }
        print_r($res);
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
    function api_salt_handler($api_file = false) {
        $param_file = get_request_param('api_file');
        if (!$api_file) $api_file = $param_file;
        global $cdn_switch;
        $cdn_api = get_option('site_cdn_api');
        $cdn_auth = get_option('site_chatgpt_auth');
        $res = new stdClass();
        $res->s = '';
        $res->t = 0;
        if ($cdn_switch && $cdn_api && $cdn_auth) {
            $stamp10x = time();
            $stamp16x = dechex($stamp10x);
            $res->s = md5($cdn_auth . $api_file . $stamp16x);
            $res->t = $stamp16x;
        }
        if ($param_file) {
            print_r(json_encode($res));
            die();
        }
        return $res;
    }
    add_action('wp_ajax_api_salt_handler', 'api_salt_handler');
    add_action('wp_ajax_nopriv_api_salt_handler', 'api_salt_handler');
    // API调用接口，接受三个参数：调用 api 文件名、api 代理访问（使用 api.php 文件中的 curl 携带鉴权参数二次请求（速度影响），适用前端异步调用、返回请求api或返回sign签名（如开启cdn鉴权
    function get_api_refrence($api='', $xhr=false, $cdn=true, $exe=1, $pid=0){
        global $src_cdn;
        $res = 'unknown_api_refrence';
        if(!$api){
            return api_err_handle(200,$res,true);
        }
        global $post, $cdn_switch;
        $exe = $exe ? $exe : 0;
        $cdn_api = get_option('site_cdn_api');
        $pid = $pid ? $pid : (isset($post->ID) ? $post->ID : 0);
        $api_file = '/'.$api.'.php';
        $authentication = get_option('site_chatgpt_dir', 'authentication');
        $cdn_src = $cdn ? $src_cdn : custom_cdn_src(0, 1);
        $request_url = $cdn_switch&&$cdn_api ? custom_cdn_src('api', true) : $cdn_src.'/plugin/'.$authentication;
        // 如出现访问403可能是由于CDN服务器开启了鉴权但后台面板中未填写 API Auth Sign 选项鉴权密钥（无法判断远程服务器是否开启鉴权）
        $api_salt = api_salt_handler($api_file);
        $auth_url = $request_url . $api_file . '?pid=' . $pid . '&s=' . $api_salt->s .'&t=' . $api_salt->t;
        $res = $xhr ? $cdn_src.'/plugin/api.php?auth='.$api.'&exec='.$exe.'&pid='.$pid.'&' : $auth_url;
        // $res = $xhr ? $cdn_src.'/plugin/'.$authentication.$api_file.'?pid='.$pid : $auth_url; //||!$cdn_api
        return $res;
    }
    function get_plugin_refrence($apiFile = '', $authDir = false, $cdnPath = false) {
        if ($authDir) $authDir = get_option('site_chatgpt_dir') . '/';
        $cdnPath = $cdnPath ? custom_cdn_src('src', 1) : custom_cdn_src(0, 1);
        $plugin_path = $cdnPath . '/plugin/' . $authDir . $apiFile . '.php?';
        return $plugin_path;
    }
    
    /*
     *--------------------------------------------------------------------------
     * custom site query
     *--------------------------------------------------------------------------
    */
    
    function get_links_category($param = false) {
        $bookmark_categories = get_terms('link_category');
        if (!empty( $bookmark_categories ) && !is_wp_error($bookmark_categories)) {
            if ($param) {
                $param_array = array();
                foreach ($bookmark_categories as $bookmark_category) {
                    array_push($param_array, $bookmark_category->$param);
                }
                return $param_array;
            }
            return $bookmark_categories;
        } else {
            echo 'No bookmark categories found.';
        }
    }
    // 返回站点标签链接
    function get_site_bookmarks($category='', $orderby='link_id', $order='ASC', $limit=-1){
        $category_by_slug = $category ? get_term_by('slug', $category, 'link_category') : false;
        $query = array(
            'orderby' => $orderby,
            'order' => $order,
            'category' => $category,
            'category_name' => $category_by_slug ? $category_by_slug->name : '',
            'hide_invisible' => 0,
            'limit' => $limit,
            // 'exclude' => 60,
        );
        $res = get_bookmarks($query);
        return (count($res)>0 ? $res : []); //false
    }
    /**
     * 在对象数组中搜索指定键值匹配的元素
     * @param array $array 要搜索的数组
     * @param string $key 要匹配的键名（如 "author"、"title"、"date"）
     * @param mixed $value 要匹配的值
     * @return array 返回匹配的所有元素（数组形式）
     */
    function searchByKeyValue($array, $key, $value) {
        foreach ($array as $item) {
            if (isset($item->$key) && strcasecmp($item->$key , $value) === 0) { //$item->$key === $value
                return $item; // 直接返回匹配的 stdClass 对象
            }
        }
        return null; // 未找到返回 null
    }
    // 返回友链指定分类 html
    function get_site_links($links, $types = '', $rsscat = '', $status = false, $category = 'standard') {
        if (!$links) return 'unreachable links provide';
    
        // ---------- 活性检测：2 年未更新标记为“待除草” ----------
        $over2yearsNoUpdateRssList = array();
        if ($rsscat && is_string($rsscat)) {
            $rss_data = get_rss_data_by_cat($rsscat, true);
            if ($rss_data && !empty($rss_data)) {
                $blacklist = ['0000-00-00', '1970-01-01'];
                foreach ($rss_data as $data) {
                    if (!isset($data->date)) continue;
                    $dateString = $data->date;
                    $givenDate = new DateTime($dateString);
                    $currentDate = new DateTime();
                    $interval = $currentDate->diff($givenDate);
                    if ($interval->y >= 2 && !in_array($dateString, $blacklist)) {
                        $over2yearsNoUpdateRssList[] = $data->rss;
                    }
                }
            }
        }
    
        global $lazysrc, $loadimg;
        $output = '';
        $rss_limit = $category === 'standard' || $category === 'technical' ? 2 : 1;
    
        // ★ 修改点：用 REST API 地址替换旧的 get_plugin_refrence
        $rss_api = rest_url('rss-feeds/v1/category/' . $category . '?limit=' . $rss_limit); //get_plugin_refrence('rss', true, true) . "cat=$category&limit=$rss_limit"; //get_api_refrence('rss')
    
        $rss_card = get_option('site_links_rss_cards_sw');
        $rss_card_manual = get_option('site_links_rss_cards_manual');
    
        // 缓存配置
        $caches_sw = get_option('site_cache_switcher');
        $caches_inc = get_option('site_cache_includes');
        $output_sw = in_array('rssfeeds', explode(',', $caches_inc));
        $caches_name = 'site_rss_' . $category . '_cache';
        $output_caches = json_decode(get_option($caches_name));
    
        foreach ($links as $link) {
            // ---------- 原有字段处理（不变） ----------
            $link_notes = $link->link_notes;
            $link_target = $link->link_target;
            $link_rating = $link->link_rating;
            $link_accessable = $link->link_visible === 'Y';
            $link_url = $link->link_url;
            $link_rss = $link->link_rss;
            $link_name = $link->link_name;
            $link_desc = $link->link_description;
            $link_descs = $link_desc ? '<span class="lowside-description"><p>'.$link_desc.'</p></span>' : '';
            $sex = $link_rating == 1 || $link_rating == 10 ? 'girl' : '';
            $ssl = $link_rating >= 9 ? ' https' : '';
            $rel = $link->link_rel ? $link->link_rel : false;
            $target = !$link_target ? '_blank' : $link_target;
            $impression = $link_notes && $link_notes!='' ? '<span class="ssl'.$ssl.'"> '.$link_notes.' </span>' : false;
            $avatar = !$link->link_image ? 'https:' . get_option('site_avatar_mirror') . 'avatar/' . md5(mt_rand().'@rand.avatar') . '?s=300' : $link->link_image;
    
            // 懒加载
            if ($lazysrc != 'src') {
                $lazyhold = 'data-src="'.$avatar.'"';
            } else {
                $lazyhold = '';
                $loadimg = $avatar;
            }
    
            // 活性标记
            if (in_array($link_rss, $over2yearsNoUpdateRssList)) $impression = '<span class="ssl http"> 待除草 </span>';
    
            // 站点可访问状态
            $status_code = $status ? get_url_status_by_curl($link_url, true) : 200;
            $status_class = !$link_accessable || $status_code >= 400 ? 'standby ' . $status_code : 'ok';
            $status_standby = $status_class === 'standby';
    
            // ---------- RSS 卡片 ----------
            $rss_feeds = '';
            if ($link_rss && $rss_card) {
                $rss_key = 'author';
                $rss_val = $link_name;
    
                // 手动模式：输出前端加载占位符
                $rss_manual = '<div class="inbox-inside aside"><a id="loadRSSFeeds" data-nick="' . $link_name . '" data-limit="' . $rss_limit . '" data-api="' . esc_url($rss_api . '&key=' . urlencode($rss_key) . '&value=' . urlencode($rss_val)) . '"></a></div>';
    
                if ($rss_card_manual) {
                    $rss_feeds = $rss_manual;
                } else {
                    // 服务器端预渲染（从缓存中读取）
                    $rss_feeds = '<div class="inbox-inside aside pre loaded">';
                    $rss_ctrls = '<a id="loadRSSFeeds"></a><i class="BBFontIcons close"></i>';
                    if ($caches_sw && $output_sw && $output_caches) {
                        $output_feeds = searchByKeyValue($output_caches, $rss_key, $rss_val);
                        $feeds_title = isset($output_feeds->title) ? urldecode($output_feeds->title) : '';
                        $feeds_desc = isset($output_feeds->desc) ? urldecode($output_feeds->desc) : '';
                        if ($feeds_title || $feeds_desc) {
                            $rss_date = $output_feeds->date;
                            $rss_feeds .= '<ol id="container"><li><a href="' . $output_feeds->link . '" target="_blank" title="' . $rss_date . "\n" . $feeds_desc . '" rel="nofollow"><b data-date="' . $rss_date . '">' . $feeds_title . '</b></a></li>';
                            if (isset($output_feeds->child)) {
                                foreach ($output_feeds->child as $child_index => $child_feeds) {
                                    if ($child_index >= $rss_limit) continue;
                                    $child_title = isset($child_feeds->title) ? urldecode($child_feeds->title) : '';
                                    $child_desc = isset($child_feeds->desc) ? urldecode($child_feeds->desc) : '';
                                    $rss_feeds .= '<li><a href="' . $child_feeds->link . '" target="_blank" title="' . $child_desc . '" rel="nofollow">' . $child_title . '</a></li>';
                                }
                            }
                            $rss_feeds .= "</ol>";
                        } else {
                            $rss_feeds .= json_encode($output_feeds);
                        }
                    } else {
                        $rss_feeds .= "<ol id='container'><li><a>Waiting for next updates..</a></li></ol>";
                    }
                    $rss_feeds .= $rss_ctrls . '</div>';
                }
            }
    
            // ---------- HTML 输出（不变） ----------
            switch ($types) {
                case 'full':
                    $avatar_statu = $status_standby ? '<img alt="近期访问出现问题" data-err="true" draggable="false">' : '<img '.$lazyhold.' src="'.$loadimg.'" alt="'.$link_name.'" draggable="false">';
                    $rel_statu = $rel ? $rel : 'friends';
                    $output .= '<div class="inbox flexboxes magnetic '.$status_class.' '.$sex.'" data-magnet-scale="" data-magnet-step="0.15"><div class="inbox-inside flexboxes"><a href="'.$link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$rel_statu.'" title="'.$link_desc.'" data-status="' . $status_code . '"><span class="lowside-title"><h4>'.$link_name.'</h4></span>'.$link_descs.'</a><div class="inbox-headside flexboxes">'.$avatar_statu.'</div>'.$impression.'</div>' . $rss_feeds;
                    $output .= '</div>';
                    break;
                case 'half':
                    $rel_statu = $rel ? $rel : 'recommends';
                    $output .= '<div class="inbox magnetic '.$status_class.' '.$sex.'"><div class="inbox-inside flexboxes" data-magnet-scale="" data-magnet-step="0.15">'.$impression.'<a href="'.$link_url.'" class="inbox-aside" target="'.$target.'" rel="'.$rel_statu.'" title="'.$link_desc.'" data-status="' . $status_code . '"><span class="lowside-title"><h4>'.$link_name.'</h4></span>'.$link_descs.'</a></div>' . $rss_feeds;
                    $output .= '</div>';
                    break;
                case 'list':
                    $rel_statu = $rel ? $rel : 'random';
                    $output .= '<li class="magnetic"><a href="'.$link_url.'" class="'.$status_class.'" title="'.$link_desc.'" target="'.$target.'" rel="'.$rel_statu.'" data-status="' . $status_code . '">'.$link_name.'</a></li>';
                    break;
                default:
                    $rel_statu = $status_standby ? 'nofollow' : 'followed';
                    $output .= '<a href="'.$link_url.'" class="'.$status_class.' magnetics" title="'.$link_desc.'" target="'.$target.'" rel="'.$rel_statu.'" data-status="' . $status_code . '" data-magnet-scale="1.15" data-magnet-step="0.75">'.$link_name.'</a>';
                    break;
            }
        }
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
            if(have_posts()) {
                if($post_styles){
            ?>
                	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/news.css?v=2" />
                    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/weblog.css" />
                    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/acg.css" />
                	<style>
                	    .news-inside-content h2{overflow:hidden}
                	    .win-content.main,
                	    /*.news-inside-content .news-core_area p,*/
                	    .empty_card{margin:15px auto auto;}
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
                	    .news-window-img{max-width:15%}
                        .news-window-img a{
                            width: 100%;
                            height: 100%;
                        }
                        .news-window-img img{
                            object-fit: cover;
                            min-height: 123px;
                        }
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
                        .win-content article p {color: inherit;}
                        /*.weblog-tree-core-record:hover > .weblog-tree-core-r .tree-box-content {*/
                        /*    color: var(--preset-6);*/
                        /*}*/
                        .weblog-tree-box .tree-box-content {
                            color: var(--preset-6);
                        }
                        .rcmd-boxes .info .inbox .inbox-headside:before {
                            /*z-index: 0;*/
                            opacity: .15;
                            pointer-events: none;
                        }
                	</style>
            <?php
                }
                while (have_posts()): the_post();
                    $postimg = get_postimg(0,$post->ID,true);
                    if ($lazysrc != 'src') {
                        $lazyhold = 'data-src="'.$postimg.'"';
                    } else {
                        $lazyhold = '';
                        $loadimg = $postimg;
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
                                        if(has_post_thumbnail() || get_option('site_default_postimg_switcher')) echo '<span class="news-window-img magnetics"><a href="'.get_the_permalink().'"><img class="lazy" '.$lazyhold.' src="'.$loadimg.'" /></a></span>';
                                    ?>
                                    <div class="news-inside-content">
                                        <h2 class="entry-title">
                                            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
                                        </h2>
                                        <span class="news-core_area entry-content"><p><?php custom_excerpt(); ?></p></span>
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
                                <div class="weblog-tree-core-r magnetics" data-magnet-scale="1" data-magnet-step="0.05">
                                    <div class="weblog-tree-box">
                                        <div class="tree-box-title">
                                            <a href="<?php the_permalink() ?>" id="<?php the_title(); ?>" target="_self">
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
                                    <div class="inbox flexboxes magnetics" data-magnet-scale="1.25" data-magnet-step="">
                                        <div class="inbox-headside flexboxes">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php
                                                    echo '<img '.$lazyhold.' src="'.$loadimg.'" alt="'.$post_feeling.'" crossorigin="Anonymous">'; //<img class="bg" '.$lazyhold.' src="'.$loadimg.'" alt="'.$post_feeling.'">
                                                ?>
                                            </a>
                                            <span class="author"><?php echo $post_feeling = get_post_meta($post->ID, "post_feeling", true); ?></span>
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
                        } else {
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
                    // 'before_page_number' => '<span class="page-number-wrapper" data-page="',
                    // 'after_page_number'  => '"></span>',
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
    
    // 日志记录函数
    // function report_logs($message, $file = 'log.txt') {
    //     $log_file = WP_CONTENT_DIR . '/uploads/' . $file; // 确保 uploads 目录存在且可写
    //     if (!file_exists($log_file)) {
    //         touch($log_file); // 如果文件不存在，则创建文件
    //     }
    //     date_default_timezone_set('Asia/Shanghai');
    //     $time = date('Y-m-d H:i:s'); // 获取当前时间
    //     $log_message = "[$time] $message\n"; // 格式化日志信息
    //     file_put_contents($log_file, $log_message, FILE_APPEND); // 将日志信息追加到文件
    // }
    function report_logs($message = '', $specify = false) {
        // 设置时区
        date_default_timezone_set('Asia/Shanghai');
    
        // 检查并创建日志目录
        $log_path = WP_CONTENT_DIR . '/uploads/logs';
        if (!is_dir($log_path)) mkdir($log_path, 0777, true); // 创建目录并设置权限
        
        $file_path = $log_path . '/' . date('Ymd');
        if ($specify) {
            // 获取当前日期，并构建年和月的子目录
            $year_month_dir = $log_path . '/' . date('Y') . '/' . date('m');
            if (!is_dir($year_month_dir)) mkdir($year_month_dir, 0777, true); // 创建年和月目录并设置权限
            $file_path = $year_month_dir . '/' . date('d');
        }
        
        // 获取当前时间并格式化日志信息
        $time = date('Y-m-d H:i:s');
        $log_message = "[$time] $message\n";
    
        // 将日志信息追加到文件
        file_put_contents($file_path . '.log', $log_message, FILE_APPEND);
    }


    
    /*--------------------------------------------------------------------------
     * 页面缓存刷新
     *--------------------------------------------------------------------------
    */
    if(get_option('site_cache_switcher')) {
        //清除（重建）更新链接
        function site_update_link_cache($link_id) {
            //清除（重建）友情链接
            update_option('site_link_list_cache', '');
            
            // 更新（指定所有包含分类） rss 订阅
            $link_category = wp_get_link_cats($link_id);
            foreach ($link_category as $category) {
                $each_category = get_term_field('slug', $category, 'link_category', 'raw');
                update_option('site_rss_' . $each_category . '_cache', '');  // 清除（所有分类）聚合内容
            }
        }
        // add_action('wp_insert_link', 'site_update_link_cache');
        // add_action('wp_update_link', 'site_update_link_cache');
        // add_action('wp_delete_link', 'site_update_link_cache');
        add_action('add_link', 'site_update_link_cache');
        add_action('edit_link', 'site_update_link_cache');
        add_action('delete_link', 'site_update_link_cache');
        
        //清除（重建）指定分类（！非法JSON响应）
        function update_category_post_cache($post, $temp_slug, $page_cache) {
            if (is_numeric($post)) $post = get_post($post);
            $post_status = $post->post_status;
            if ($post_status === 'auto_draft' || $post_status === 'draft') {
                return;  // exit 导致更新非法响应
            };
            $temp_info = get_cat_by_template($temp_slug);
            if (isset($temp_info->error)) {
                return;
            }
            $cid = get_the_category($post->ID);
            if (!empty($cid[0])) {
                global $cat; //
                $cat = $cat ? $cat : $cid[0]->term_id; //get_the_category($pid)->term_id; // $categories = wp_get_post_categories($pid);
                if(in_category($temp_info->slug, $post) || cat_is_ancestor_of($cat, $temp_info->term_id)) update_option($page_cache, '');
            }
        }
        function site_update_specific_caches($post_id) {
            global $cat;
            $post = get_post($post_id);
            if($post && $post->post_type != 'post') return;  // update post only(not inform)
            
            $archive_temp = get_cat_by_template('archive');
            if (!isset($archive_temp->error)) {
                // 清除（当前）归档数据
                update_option('site_archive_count_cache', '');
                update_option('site_archive_contributions_cache', '');
                // update_option('site_archive_list_cache', '');
                $post_year = date('Y', strtotime($post->post_date));
                $archive_years = get_option('site_archive_years_cache');
                if ($archive_years) {
                    $archive_years = json_decode($archive_years);
                    if (in_array($post_year, $archive_years)) {
                        update_option('site_archive_' . $post_year . '_cache', '');
                    } else {
                        // clear years-list incase of crossing-year
                        update_option('site_archive_years_cache', '');
                    }
                }
            }
            
            // 清除指定分类文章缓存
            $caches = get_option('site_cache_includes');
            if ($caches) {
                $temp_array = array(get_cat_by_template('news'), get_cat_by_template('notes'), get_cat_by_template('weblog'), get_cat_by_template('acg'), get_cat_by_template('download'));
                $output_caches = explode(',', $caches);
                foreach ($temp_array as $temp) {
                    if (isset($temp->error)) {
                        continue;
                    }
                    $temp_slug = $temp->slug;
                    // 清除（重建）更新通用缓存
                    if(in_array($temp_slug, $output_caches)) update_category_post_cache($post_id, $temp_slug, 'site_recent_'.$temp_slug.'_cache');
                }
                // 清除（重建）更新指定缓存
                if(in_array('acg', $output_caches)) {
                    update_category_post_cache($post, 'acg', 'site_acg_stats_cache');
                    update_category_post_cache($post, 'acg', 'site_acg_post_cache');
                }
                if(in_array('download', $output_caches)) {
                    update_category_post_cache($post, 'download', 'site_download_list_cache');
                }
            }
        }
        // add_action('save_post', 'site_update_specific_caches');
        add_action('publish_post', 'site_update_specific_caches');
        add_action('delete_post', 'site_update_specific_caches');
        
        
        /*****   wp_schedule_event 定时任务   *****/
        
        add_filter( 'cron_schedules', 'custom_add_cron_interval' );
        function custom_add_cron_interval( $schedules ) {
            $update_hours = get_option('site_rss_update_interval', 12);
            $schedules[$update_hours . 'hours'] = array(
                'interval' => $update_hours * HOUR_IN_SECONDS, //600
                'display'  => esc_html__( "Every $update_hours Hours" ), );
            return $schedules;
        }
        
        // 刷新定时任务AJAX动作
        add_action('wp_ajax_update_cronjobs', 'update_all_cronjobs');
        // _ajax_nonce 校验
        add_action('wp_ajax_nopriv_update_cronjobs', 'update_all_cronjobs');
        function update_all_cronjobs() {
            // 安全检查
            check_ajax_referer('update_cronjobs', 'nonce');
            // 取消定时任务
            wp_clear_scheduled_hook('scheduled_rss_feeds_updates_hook');
            // 调用安排定时任务的函数
            $param_interval = get_request_param('interval');
            schedule_all_cronjob($param_interval);
            // 返回一个响应
            wp_send_json_success('200');
        }

        add_action('wp', 'schedule_all_cronjob');
        function schedule_all_cronjob($param_interval = 0) {
            if (!is_numeric($param_interval) || $param_interval <= 0) {
                $param_interval = get_option('site_rss_update_interval', 12); // 默认值
            }
            if(!wp_next_scheduled('db_caches_cronjob_hook')){
                // 设定定时作业执行时间（东八区时间）
                $timestamp = strtotime('today ' . get_option('site_scheduled_times') . ' Asia/Shanghai'); // 设置每天上午执行一次定时作业
                wp_schedule_event($timestamp, 'daily', 'db_caches_cronjob_hook'); 
            }
            // 检查是否已经安排了事件，避免重复安排
            if ( ! wp_next_scheduled( 'scheduled_rss_feeds_updates_hook' ) ) {
                date_default_timezone_set('Asia/Shanghai');
                // 当前时间的时间戳
                $timestamp = time(); //current_time( 'timestamp' ); //
                $hours_interval = $param_interval . 'hours';
                // 安排事件，每隔$interval小时执行一次 使用 @ 来抑制错误输出
                // @wp_schedule_event( $timestamp, $hours_interval, 'scheduled_rss_feeds_updates_hook' );
                try {
                    wp_schedule_event( $timestamp, $hours_interval, 'scheduled_rss_feeds_updates_hook' );
                } catch (Exception $e) {
                    report_logs("\n\n".$e->getMessage()."\n\n");  // 记录错误信息到错误日志
                }
            }
        }
        
        function refresh_template_cache($template = '', $caches = [], $timeout = 10) {
            if (!$template || !is_string($template)) {
                report_logs("（定时任务）错误！非法模板名称： $template"); // 记录日志
                return;
            }
            $temp = get_cat_by_template($template);
            if (!isset($temp->error)) {
                report_logs("（定时任务）开始更新 $template 数据..."); // 记录日志
                // 清除相关缓存（状态码）
                foreach ($caches as $cache) {
                    if (!$cache) continue;
                    update_option($cache, '');
                }
                // 刷新缓存结果
                $request_url = get_category_link($temp->term_id);
                $request_arg = array(
                    'method' => 'GET',
                    'timeout' => $timeout
                );
                $response = wp_remote_get($request_url, $request_arg);
                if (is_wp_error($response)) {
                    report_logs('（定时任务）' . $template . ' 更新失败：' . $response->get_error_message() . '）'); // 记录错误日志
                    return;
                }
                report_logs("（定时任务）$template 已更新。\n"); // $body = wp_remote_retrieve_body($response);
            }
        }
        
        //定时清除（重建）缓存
        add_action('db_caches_cronjob_hook', 'site_clear_timeout_caches'); //定时更新 db caches
        function site_clear_timeout_caches() {
            // 清除 tag_clouds 缓存
            if (!get_option('site_tagcloud_auto_caches')) update_option('site_tag_clouds_cache', '');
            
            // 清除 友链 缓存（long time costs as 60 sec）
            $link_array = array('site_link_list_cache');
            refresh_template_cache('2bfriends', $link_array, 60);
            
            // 重建 ACG 缓存
            $acg_array = array('site_acg_stats_cache'); //, 'site_acg_post_cache'
            refresh_template_cache('acg', $acg_array);
            
            // 重建 rank 缓存
            $rank_array = array('site_rank_list_cache');
            refresh_template_cache('ranks', $rank_array);
            
            // 重建 归档 缓存 //解决bug：切换全年报表后无法判断db数据库中是否已存在全年记录
            $archive_years = json_decode(get_option('site_archive_years_cache'));
            $archive_years = array_map(function($year) {
                return 'site_archive_' . $year . '_cache';
            }, $archive_years);
            $archive_array = array('site_archive_count_cache', 'site_archive_contributions_cache', 'site_archive_years_cache');
            $archive_array = array_merge($archive_years, $archive_array);
            refresh_template_cache('archive', $archive_array);
        }
        
        // // 定时事件安排
        // add_action( 'scheduled_rss_feeds_updates_hook', 'scheduled_rss_feeds_updates' );
        // // 触发 api 更新（全部） rss 订阅
        // function scheduled_rss_feeds_updates() {
        //     report_logs("（定时任务）开始更新 RSS 缓存.....................", true); // 记录日志
        //     date_default_timezone_set('Asia/Shanghai');
        //     $links_slug = get_links_category('slug');
        //     $update_limit = get_option('site_rss_update_count', 3); // 默认值
        //     foreach ($links_slug as $link_slug) {
        //         report_logs('（定时任务）正在更新 ' . $link_slug . '..', true); // 记录日志
        //         // update_option('site_rss_' . $link_slug . '_cache', $link_slug);  // 清除（重建）所有聚合内容
        //         $api_url = get_plugin_refrence('rss', true) . "cat=$link_slug&limit=$update_limit&update=1&output=0&clear=0";  // 注：服务端请求无法使用cdn，客户端可用 get_api_refrence
        //         // 触发 API（更新）聚合内容
        //         $ch = curl_init($api_url);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应，而不是直接输出
        //         curl_setopt($ch, CURLOPT_HEADER, false); // 不需要返回响应头
        //         curl_setopt($ch, CURLOPT_TIMEOUT, 300);  // 5m请求限制
        //         $res = curl_exec($ch);
        //         if (curl_errno($ch)) {
        //             // 触发 curl 重试（一次）
        //             report_logs('（定时任务）重试更新：' . curl_error($ch), true); // 记录错误日志
        //             $response = wp_remote_get($api_url);
        //             if (is_wp_error($response)) {
        //                 report_logs('（定时任务）重试更新 ' . $link_slug . ' 失败！（' . $response->get_error_message() . '）', true); // 记录错误日志
        //                 continue;
        //             }
        //             $body = wp_remote_retrieve_body($response);
        //             report_logs('（定时任务）' . $link_slug . ' 已（重试）更新于：' . date("Y-m-d H:i:s"), true); // json_decode($body)[0]->lastUpdate
        //         }
        //         curl_close($ch);
        //         if ($res) report_logs('（定时任务）' . $link_slug . ' 已更新于：' . date("Y-m-d H:i:s"), true); // json_decode($res)[0]->lastUpdate
        //     }
        //     report_logs("（定时任务）所有 RSS 缓存已更新.....................\n\n\n", true); // 记录日志
        //     if (function_exists('wp_cache_flush')) {
        //         wp_cache_flush(); // bug: to clear wp_options caches
        //     }
        // }
        add_action('scheduled_rss_feeds_updates_hook', 'scheduled_rss_feeds_updates');
        function scheduled_rss_feeds_updates() {
            $links_slug = get_links_category('slug');
            $update_limit = get_option('site_rss_update_count', 3);
            foreach ($links_slug as $slug) {
                // 直接调用更新逻辑，无需网络请求
                $linked_urls = [];
                $link_marks = get_site_bookmarks($slug);
                foreach ($link_marks as $link) {
                    if (!empty($link->link_rss) && $link->link_visible === 'Y') {
                        $linked_urls[] = $link;
                    }
                }
                $json = parse_rss_data($linked_urls, $update_limit, 10);
                if ($json) {
                    $cache_switcher = get_option('site_cache_switcher');
                    $cache_includes = get_option('site_cache_includes');
                    if ($cache_switcher && in_array('rssfeeds', explode(',', $cache_includes))) {
                        update_option('site_rss_' . $slug . '_cache', $json);
                    }
                }
                report_logs("（定时任务）{$slug} 缓存已更新");
            }
        }
    }
    
    
    /*
     *--------------------------------------------------------------------------
     * 全局初始化操作
     *--------------------------------------------------------------------------
    */
    
    // 初始化 wordpress 执行函数
    function custom_theme_setup() {
        $expire = time() + 1209600;  // 自定义 cookie 函数 darkmode cookie set
        $theme_manual = array_key_exists('theme_manual',$_COOKIE) ? $_COOKIE['theme_manual'] : false;
        if(!isset($theme_manual)) {  //auto set manual 0 (reactive with javascript manually)
            setcookie('theme_manual', 0, $expire, COOKIEPATH, COOKIE_DOMAIN, false);
        };
        if(!$theme_manual){  //if theme_manual actived
            if(get_option('timezone_string') != 'Asia/Shanghai') {
                update_option('timezone_string', 'Asia/Shanghai'); // update local timezone for 24 hours offset fixes
            }
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
    // 动态主题模式
    function theme_mode($returns = false) {
        if (!get_option('site_darkmode_switcher')) return;
        // wp-panel fixed theme(1st priority)
        $fixed_theme = get_option('site_darkmode_fixed');
        if ($fixed_theme) {
            if ($returns) return $fixed_theme;
            echo $fixed_theme;
            return;
        }
        $theme_mode = isset($_COOKIE['theme_mode']) ? $_COOKIE['theme_mode'] : 'light';
        $theme_manual = isset($_COOKIE['theme_manual']) ? $_COOKIE['theme_manual'] : false;
        // client-side manual theme(2nd)
        if ($theme_manual) {
            if ($returns) return $theme_mode;
            echo $theme_mode;
            return;
        }
        // client-side system prefers theme(3nd)
        $theme_prefer = isset($_COOKIE['theme_mode_prefers']) ? $_COOKIE['theme_mode_prefers'] : false;
        if ($theme_prefer) {
            if ($returns) return $theme_prefer;
            echo $theme_prefer;
            return;
        }
        // wp-panel preset theme_mode
        if (!array_key_exists('sidebar_status', $_COOKIE)) {
            $hour = current_time('G');
            $start = get_option('site_darkmode_start');
            $end = get_option('site_darkmode_end');
            $res = $hour>=$end&&$hour<$start || $hour==$end&&current_time('i')>=0&&current_time('s')>=0 ? 'light' : 'dark';
            if ($returns) return $res;
            echo $res;
            return;
        }
        // default theme_mode
        if ($returns) return $theme_mode;
        echo $theme_mode;
    }
    //lazyload 图懒加载
    if (get_option('site_lazyload_switcher')) {
        // $lazysrc = 'data-src';
        function lazyload_images($content) {
            global $loadimg;
            return preg_replace('/\<img(.*?)src=("[^"]*")/i', '<img$1data-src=$2 src="'.$loadimg.'"', $content);
            // return preg_replace('/\<img([^>]*?)src=("[^"]*")([^>]*>)/i', '<img$1data-src=$2$3', $content);
        }
        // 设置 priority 高于 replace_cdn_img（延后执行）
        add_filter('the_content', 'lazyload_images', 12);
        // replace comments images url
        add_filter('comment_text' , 'lazyload_images', 20, 2);
    }
    // 站点logo
    function site_logo($darkmode = false) {
        if (get_option('site_logo_switcher')) {
            $logo_src = $darkmode ? get_option('site_logos') : get_option('site_logo');
            if ($logo_src) {
                echo '<span style="background: url(' . $logo_src . ') no-repeat center center /cover;"></span>';
                return;
            }
            $logo_svg = get_option('site_logo_svg');
            if ($logo_svg) echo '<span>' . $logo_svg . '</span>';
            return;
        }
        echo '<span>' . get_bloginfo('name') . '</span>';
    }
    // 站点公告
    function get_inform() {
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
                        // 'modified' => 'DESC'
                    ),
                    'posts_per_page' => $inform_max,  //use left_query counts
                    'post_status' => 'publish'  //, draft
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
    function breadcrumb_switch($switch=false, $frame=false) {
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
                        $content = '<div class="article_index '.$auto_fold.' magnetics" data-index="'.$match_m.'" data-magnet-step="0.1" data-magnet-scale="1"><div class="in_dex"><p title="折叠/展开"><b>文章目录</b><i class="icom"></i></p><ul>' . $ul_li . '</ul></div></div>' . $content;
                    }
                }
            }
        }
        return $content;
    }
    add_filter( 'the_content', 'article_index');
    /*
     *--------------------------------------------------------------------------
     * WP Comment email/wechat notify, ajax/pagination etc
     *--------------------------------------------------------------------------
    */
    
    // 默认储存评论 COOKIE
    function coffin_set_cookies( $comment, $user, $cookies_consent) {
    	$cookies_consent = true;
    	wp_set_comment_cookies($comment, $user, $cookies_consent);
    }
    add_action('set_comment_cookies','coffin_set_cookies',10,3);
    
    /**
     * ===========================
     * 评论通知异步优化（全部通知，含博主、微信、访客回复）
     * ===========================
     */
     
    // 评论微信提醒（博主）
    if ( get_option('site_wpwx_notify_switcher') ) {
    
        // 实际发送微信的函数（内部调用 wp_remote_post，在延迟回调中执行）
        function push_weixin_async( $comment_id ) {
            $comment = get_comment( $comment_id );
            if ( ! $comment ) return false;
    
            $post_id       = $comment->comment_post_ID;
            $admin_mail    = get_bloginfo('admin_email');
            $comment_mail  = $comment->comment_author_email;
            $comment_author     = $comment->comment_author;
            $comment_title      = '《' . get_the_title($post_id) . '》 上有新评论啦~';
            $comment_content    = strip_tags($comment->comment_content);
    
            if ( $comment_mail != $admin_mail ) {
                $url = custom_cdn_src(0, 1) . '/plugin/wpwx-notify.php';
                wp_remote_post( $url, array(
                    'blocking' => false,
                    'timeout'  => 1,
                    'body'     => array(
                        'name'    => $comment_author,
                        'mail'    => $comment_mail,
                        'title'   => $comment_title,
                        'content' => $comment_content,
                        'image'   => get_postimg(0, $post_id, true),
                        'url'     => urlencode(get_the_permalink($post_id)) . '#comments',
                    ),
                ));
                return true;
            }
            return false;
        }
    
        // 延迟回调：WP-Cron 触发时再真正执行微信发送
        add_action( 'delayed_weixin_notify', 'send_weixin_notify_later' );
        function send_weixin_notify_later( $comment_id ) {
            push_weixin_async( $comment_id );
        }
    
        // 主钩子：只安排延迟任务，不做任何网络操作
        add_action( 'comment_post', 'schedule_weixin_notifications', 10, 2 );
        function schedule_weixin_notifications( $comment_id, $comment_approved ) {
            $comment = get_comment( $comment_id );
            if ( ! $comment ) return;
    
            $admin_mail = get_bloginfo('admin_email');
            $is_not_admin = ( $comment->comment_author_email != $admin_mail );
    
            if ( $is_not_admin ) {
                // 5 秒后由 WP-Cron 触发真正的微信推送
                wp_schedule_single_event( time() + 5, 'delayed_weixin_notify', array( $comment_id ) );
            }
        }
    }

    // 评论邮件提醒（博主+访客）
    if ( get_option('site_wpmail_switcher') && get_option('site_third_comments') == 'Wordpress' ) {
        // disengage default notify from wp
        remove_action('comment_post', 'wp_new_comment_notify_moderator', 10);
        remove_action('comment_post', 'wp_new_comment_notify_postauthor', 10);
        
        //1. 博主邮件发送核心（不变） ----------
        function wp_notify_admin_mail( $comment_id, $comment_approved ) {
            global $img_cdn;
            $comment = get_comment( $comment_id );
            if ( ! $comment ) return;
    
            $parent_id  = $comment->comment_parent ? $comment->comment_parent : 0;
            $admin_mail = get_bloginfo('admin_email');
            $user_mail  = $comment->comment_author_email;
    
            $title = ' 「' . get_the_title($comment->comment_post_ID) . '」 收到一条来自 '.$comment->comment_author.' 的留言！';
            $body  = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="'.$img_cdn.'/images/google.gif"><h2 class="head"><span class="title">「'. get_option("blogname") .'」上有一条新评论！</span><p><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank">点击查看</a></p></h2><div class="body"><p><strong>' . trim($comment->comment_author) . '：</strong></p><div class="content"><p><a class="at"href="#624a75eb1122b910ec549633">' . trim($comment->comment_content) . '</a></p></div></div></div>';
            $header = "\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
    
            if ( $user_mail != $admin_mail ) {
                wp_mail( $admin_mail, $title, $body, $header );
            }
        }
    
        //3. 访客回复邮件发送核心（原 wp_notify_guest_mail 剥离为纯发送函数）
        function wp_notify_guest_mail_send( $comment_id ) {
            $comment = get_comment( $comment_id );
            if ( ! $comment ) return;
    
            // 只有回复才发，并且不能是垃圾评论（这里再检查一次状态）
            $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
            if ( $parent_id === '' ) return;
            if ( $comment->comment_approved === 'spam' ) return;
    
            $admin_mail = get_bloginfo('admin_email');
            $parent_comment = get_comment( $parent_id );
            if ( ! $parent_comment ) return;
    
            $tomail = trim( $parent_comment->comment_author_email );
            // 被回复者是博主时不发（避免和博主通知重复）
            if ( $tomail == $admin_mail ) return;
    
            global $img_cdn;
            $title = '👉 叮咚！您在 「' . get_option("blogname") . '」 上有一条新回复！';
            $body  = '<style>.box{background-color:white;border-bottom:2px solid #EB6844;border-radius:10px;box-shadow:rgba(0,0,0,0.08) 0 0 18px;line-height:180%;width:500px;margin:50px auto;color:#555555;font-family:"Century Gothic","Trebuchet MS","Hiragino Sans GB",微软雅黑,"Microsoft Yahei",Tahoma,Helvetica,Arial,"SimSun",sans-serif;font-size:12px;}.box .head{border-bottom:1px solid whitesmoke;font-size:14px;font-weight:normal;padding-bottom:15px;margin-bottom:15px;text-align:center;line-height:28px;}.box .head h3{margin-bottom:0;margin:0;}.box .head .title{color:#EB6844;font-weight:bold;}.box .body{padding:0 15px;}.box .body .content{background-color:#f5f5f5;padding:10px 15px;margin:18px 0;word-wrap:break-word;border-radius:5px;}a{text-decoration:none!important;color:#EB6844;}img{max-width:100%;display:block;margin:0 auto;border-radius:inherit;border-bottom-left-radius:unset;border-bottom-right-radius:unset;}.button:hover{background:#EB6844;color:#ffffff;}.button{display:block;margin:0 auto;width:15%;line-height:35px;padding:0 15px;border:1px solid currentColor;border-radius:50px;text-align:center;font-weight:bold;}</style><div class="box"><img src="'.$img_cdn.'/images/google_flush.gif"><div class="head"><h2>'. trim($parent_comment->comment_author) .'，</h2>有人回复了你在《' . get_the_title($comment->comment_post_ID) . '》上的评论！</div>&nbsp;&nbsp;&nbsp;你评论的：<div class="body"><div class="content"><p>' . trim($parent_comment->comment_content) . '</p></div><p>被<strong> ' . trim($comment->comment_author) . ' </strong>回复：</p><div class="content"><p><a class="at" href="#">' . trim($comment->comment_content) . '</a></p></div><p style="margin:20px auto"><a class="button"href="' . htmlspecialchars(get_comment_link($parent_id)) . '"target="_blank"rel="noopener">点击查看</a></p><p><center><b style="opacity:.5">此邮件由系统发送无需回复，</b>欢迎再来<a href="' . get_bloginfo('url') . '"target="_blank"rel="noopener"> '. get_option("blogname") .' </a>游玩！</center></p></div></div>';
            $headers = "From: \"" . get_option('blogname') . "\" <".$admin_mail.">\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
    
            wp_mail( $tomail, $title, $body, $headers );
        }
    
        // 4. 延迟邮件回调（博主 & 访客）
        // 博主延迟邮件
        add_action( 'delayed_comment_mail', 'send_comment_mail_later' );
        function send_comment_mail_later( $comment_id ) {
            wp_notify_admin_mail( $comment_id, 1 );
        }
    
        // 访客延迟邮件
        add_action( 'delayed_guest_mail', 'send_guest_mail_later' );
        function send_guest_mail_later( $comment_id ) {
            wp_notify_guest_mail_send( $comment_id );
        }
    
        //5. 主钩子：评论提交时安排所有异步通知
        add_action( 'comment_post', 'schedule_comment_notifications', 10, 2 );
        function schedule_comment_notifications( $comment_id, $comment_approved ) {
            $comment = get_comment( $comment_id );
            if ( ! $comment ) return;
    
            $admin_mail = get_bloginfo('admin_email');
            $is_not_admin = ( $comment->comment_author_email != $admin_mail );
    
            if ( $is_not_admin ) {
                // 博主邮件延迟 10 秒发送
                wp_schedule_single_event( time() + 10, 'delayed_comment_mail', array( $comment_id ) );
            }
    
            // 访客回复提醒：只要有父评论，并且当前评论不是垃圾（初步判断避免无意义事件）
            $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
            if ( $parent_id !== '' && $comment->comment_approved !== 'spam' ) {
                // 延迟 10 秒发送，避免同步 SMTP 阻塞
                wp_schedule_single_event( time() + 15, 'delayed_guest_mail', array( $comment_id ) );
            }
        }
    }
    
    
    // 修复后台评论管理页面img标签为data-src问题
    // add_filter( 'get_comment_text', 'fix_comment_img_data_src', 20, 1 );
    add_filter( 'comment_text', 'fix_comment_img_data_src', 20, 1 );
    function fix_comment_img_data_src( $comment_text, $comment = null ) {
        // 仅在后台管理界面生效
        if ( ! is_admin() ) {
            return $comment_text;
        }
        // 使用正则替换所有 img 标签，将 data-src 属性值赋给 src
        $pattern = '/<img\s+([^>]*?)data-src\s*=\s*["\']([^"\']+)["\']([^>]*)>/i';
        $replacement = '<img $1src="$2"$3>';
        $fixed = preg_replace( $pattern, $replacement, $comment_text );
        // 如果存在没有 data-src 的 img，可保留原样
        return $fixed;
    }

    // https://developer.wordpress.org/reference/functions/comment_reply_link/
    function wpdocs_comment_reply_link_class( $class ) {
    	$class = str_replace( "comment-reply-link", "comment-reply-link vat noslide", $class );
    	return $class;
    }
    add_filter( 'comment_reply_link', 'wpdocs_comment_reply_link_class' );
    
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
    
    // 垃圾评论屏蔽词
    // add_filter( 'pre_comment_approved', function($approved, $commentdata) {
    //     $blacklist = get_option('site_comment_blacklists');
    //     // If the comment URL field has anything in it, mark as spam
    //     // if ( ! empty( $commentdata['comment_author_url'] ) ) $approved = 'spam';
    //     // If the comment contains 'binance' then mark as spam
    //     if ( str_contains( $commentdata['comment_content'], $blacklist ) ) $approved = 'spam';
    //     return $approved;
    // }, 10, 2);
    
    // 解决 base64 图片 data: 协议被移除的问题
    add_filter( 'kses_allowed_protocols', 'allow_data_protocol_in_comments' );
    function allow_data_protocol_in_comments( $protocols ) {
        // 将 'data' 添加到协议数组
        $protocols[] = 'data';
        return $protocols;
    }
    // 允许发送 img 标签（表情包）
    add_filter( 'wp_kses_allowed_html', 'allow_img_tags_in_comments', 10, 2 );
    function allow_img_tags_in_comments( $allowed_html, $context ) {
        // 仅在评论内容这个上下文中生效
        if ( $context === 'pre_comment_content' ) {
            // 如果 img 标签尚未被允许，则添加它及其常用属性
            if ( ! isset( $allowed_html['img'] ) ) {
                $allowed_html['img'] = array(
                    'id'    => true,
                    'src'    => true,
                    'alt'    => true,
                    'class'  => true,
                    'data-src'    => true,
                    'style'  => false,  // false: xss issue
                    'width'  => true,
                    'height' => true,
                    'loading' => true,
                );
            }
        }
        return $allowed_html;
    }
 
    //*****  WordPress Comments Setup etc (comment ajx reply/paginate)  *****//
    
    // ai reply logics // 正在处理且尚未有回复 → true
    function ajax_ai_reply_status($comment) {
        $comment_id = $comment->comment_ID;
        if ( get_comment_meta( $comment_id, '_2ber_ai_processing', true ) && ! get_comment_meta( $comment_id, '_2ber_ai_replied', true ) ) {
            $comment->two_ber_ai_pending = 1;
        } else {
            $comment->two_ber_ai_pending = 0;
        }
    }
    
    // 自动填充
    if (get_option('site_comment_autofill')) {
        /**
         * 注册自定义 REST API 路由
         * 1. 根据邮箱获取最近评论信息（用于自动填充）
         * 2. 根据邮箱获取头像 URL 或跳转
         */
        add_action('rest_api_init', function () {
            // ----- 合并端点：根据邮箱返回昵称、网址、头像 -----
            register_rest_route('comment-info/v1', '/by-email', [
                'methods'  => 'GET',
                'callback' => 'rest_get_comment_full_info',
                'args'     => [
                    'email' => [
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_email',
                        'validate_callback' => function ($value) {
                            return is_email($value);
                        },
                    ],
                ],
                'permission_callback' => '__return_true',
            ]);
        
            // ----- 保留头像跳转端点（供 <img src> 直接使用） -----
            register_rest_route('avatar/v1', '/get', [
                'methods'  => 'GET',
                'callback' => 'rest_get_avatar_redirect',
                'args'     => [
                    'email' => [
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_email',
                        'validate_callback' => function ($value) {
                            return is_email($value);
                        },
                    ],
                ],
                'permission_callback' => '__return_true',
            ]);
        });
        
        /**
         * 根据邮箱返回完整评论者信息：昵称、网址、头像、评论次数、首次/最后评论时间
         */
        function rest_get_comment_full_info(WP_REST_Request $request) {
            global $wpdb;
            $email = $request->get_param('email');
        
            // 1. 取最新的非空昵称
            $name_row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT comment_author 
                     FROM $wpdb->comments 
                     WHERE comment_author_email = %s 
                       AND comment_approved = '1' 
                       AND comment_author != '' 
                     ORDER BY comment_date DESC 
                     LIMIT 1",
                    $email
                )
            );
        
            // 2. 取最新的非空网址
            $url_row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT comment_author_url 
                     FROM $wpdb->comments 
                     WHERE comment_author_email = %s 
                       AND comment_approved = '1' 
                       AND comment_author_url != '' 
                     ORDER BY comment_date DESC 
                     LIMIT 1",
                    $email
                )
            );
        
            // 3. 一次性统计评论数、首次/最后评论时间
            $stats = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT COUNT(*) AS comment_count,
                            MIN(comment_date) AS first_comment_date,
                            MAX(comment_date) AS last_comment_date
                     FROM $wpdb->comments 
                     WHERE comment_author_email = %s 
                       AND comment_approved = '1'",
                    $email
                )
            );
        
            // 4. 生成头像 URL
            $mirror = get_option('site_avatar_mirror', 'https://cravatar.cn/');
            $avatar_url = $mirror . 'avatar/' . md5($email) . '?d=retro&s=100';
        
            return rest_ensure_response([
                'name'               => $name_row ? $name_row->comment_author : '',
                'url'                => $url_row ? $url_row->comment_author_url : '',
                'avatar_url'         => $avatar_url,
                'comment_count'      => (int) $stats->comment_count,
                'first_comment_date' => $stats->first_comment_date ?: '',
                'last_comment_date'  => $stats->last_comment_date ?: '',
            ]);
        }
        
        /**
         * 头像跳转（保留原有功能：?email=xxx 直接 302 到图片地址）
         */
        function rest_get_avatar_redirect(WP_REST_Request $request) {
            $email = $request->get_param('email');
            $mirror = get_option('site_avatar_mirror', 'https://cravatar.cn/');
            $avatar_url = $mirror . 'avatar/' . md5($email) . '?d=retro&s=100';
            return new WP_REST_Response(null, 302, ['Location' => $avatar_url]);
        }
    }
    
    // AJAX 回复评论
    if (get_option('site_ajax_comment_switcher')) {
        
        // // 允许REST API 匿名提交
        // add_filter( 'rest_allow_anonymous_comments', '__return_true' );
        
        // 将 comment_id 参数安全地追加到重定向链接中（前端使用）
        add_filter( 'comment_post_redirect', function( $location, $comment ) {
            // add_query_arg 会自动处理已有参数和 # 锚点
            return add_query_arg( 'comment_id', $comment->comment_ID, $location );
        }, 10, 2 );
        
        // 验证 ajax评论nonce
        add_filter( 'pre_comment_on_post', function ($commentdata) {
            // $ip = get_remote_ip();
            $comment_nonce = get_request_param('comment_nonce');
            if ( !$comment_nonce || ! wp_verify_nonce( $comment_nonce, 'comment_dynamic_' ) ) {
                wp_die( '安全验证失败，请刷新页面重试。' );
            }
            return $commentdata;
        } );
        
        // Loop-back child-comments (recursive)
        function wp_child_comments_loop($cur_comment, $loop = true) {
            $child_comment = $cur_comment->get_children(array(
                'hierarchical' => 'threaded',
                'order'        => 'ASC', // fixed ASC on ajax_paginate $comment_order
                'orderby' => 'comment_date_gmt',
                // 'status'       => 'approve',
                // 'orderby'=>'order_clause',
                // 'meta_query'=>array(
                //   'order_clause' => 'comment_parent'
                // )
            ));
            if (count($child_comment) <= 0) return;
            foreach ($child_comment as $child) {
                wp_comments_template($child);
                if ($loop) wp_child_comments_loop($child, $loop);
            }
        }
        // Direct comments output
        function wp_comments_template($comment) {
            global $lazysrc, $post;
            $id = $comment->comment_ID;
            $nick = $comment->comment_author;
            $link = $comment->comment_author_url;
            $email = $comment->comment_author_email;
            $userAgent = get_userAgent_info($comment->comment_agent);
            $approved = $comment->comment_approved == '1';
            $content = $comment->comment_content; //esc_html();// //strip_tags(); XSS Secure Issues!!!
            $parent = $comment->comment_parent;
            if (!$approved) $content = '<small style="opacity:.5">[ 等待评论审核，通过正常显示。 ]</small>';
            if ($parent>0) $content = '<a x href="#comment-'.$parent.'">@'. get_comment_author($parent) . '</a> , ' . $content;
            $is_ai_comment = get_comment_meta( $id, '_2ber_ai_reply', true ) || get_comment_meta( $id, '_2ber_ai_processing', true ); //&& $comment->user_id === 0;
            $is_thoughtful_comment = get_comment_meta( $id, '_thoughtful_comment', true );
            // apply ai reply status
            ajax_ai_reply_status($comment);
    ?>
            <div class="vcard magnetics<?php if (!$approved) echo ' auditing';if ($is_ai_comment) echo ' ai';if ($is_thoughtful_comment) echo ' thoughtful'; ?>" data-ai-pending="<?php echo $comment->two_ber_ai_pending ?>" data-magnet-scale="1" data-magnet-step="0.015" id="comment-<?php echo $id; ?>">
                <a class="noslide" rel="nofollow" href="<?php echo $link; ?>" target="_blank">
                    <?php 
                        if (get_option('show_avatars')) {
                            echo '<img class="vimg" '.$lazysrc.'="'.match_mail_avatar($email).'" width=50 height=50 alt="user_avatar" />';
                            unset($lazysrc);
                        }
                    ?>
                </a>
                <div class="vh" rootid="comment-<?php echo $id; ?>">
                    <div class="vhead">
                        <a class="vnick" rel="nofollow" href="<?php echo $link; ?>" target="_blank">
                            <em><?php echo $nick; ?></em>
                        </a>
                        <?php
                            if ($is_ai_comment) {
                                echo '<span class="vsys vai">AI Comment #' . $id . '</span>';
                            } else {
                                if ($email == get_bloginfo('admin_email')) echo '<span class="vsys vadmin">admin</span>';
                                echo $approved ? '<span class="vsys useragent">'.$userAgent['browser'].' / '.$userAgent['system'].' '. $userAgent['system_version'] .'</span>' : '<span class="vsys auditing"> Auditing </span>';
                                if ($is_thoughtful_comment) echo '<span class="vsys vthoughtful" title="AI Powered by @2BER">✨亮评 #' . $id . '</span>';
                            }
                        ?>
                    </div>
                    <div class="vmeta">
                        <span class="vtime"><?php echo date('Y-m-d', strtotime($comment->comment_date)); ?></span>
                        <span class="vedited"></span>
                        <?php 
                            if ($approved) {
                                if (get_option('site_ajax_comment_switcher')) {
                                    $tips = '回复ta的评论';
                                    $nonce = '';
                                    if ($is_ai_comment) {
                                        $tips = '追问AI无需@';
                                        // $nonce = wp_create_nonce( 'wp_rest' );
                                    }
                                    echo '<a rel="nofollow" class="vat noslide comment-reply-link" href="javascript:void(0);" data-commentid="'.$id.'" data-postid="'.$post->ID.'" data-belowelement="comment-'.$id.'" data-respondelement="respond" data-nonce="'.$nonce.'" data-replyto="'.$nick.'" title="'.$tips.'" aria-label="正在回复给：@'.$nick.'">回复</a>';
                                    // unset($post);
                                } else {
                                    echo comment_reply_link(array_merge($args, array(
                                        'reply_text' => '回复',
                                        'depth' => $depth, 
                                        'max_depth' => $args['max_depth']
                                    )));
                                }
                            }
                        ?>
                    </div>
                    <div class="vcontent">
                        <?php echo $content; //'<p>'.$content.'</p>'; //comment_text();?>
                    </div>
                    <?php
                        // // 层层嵌套
                        // $child_comment = $comment->get_children(array(
                        //     'hierarchical' => 'threaded',
                        //     // 'status'       => 'approve',
                        //     'order'        => get_option('comment_order'), //
                        // ));
                        // $child_count = count($child_comment);
                        // if ($child_count >= 1) {
                        //     $child_counts = get_descendant_comment_count($comment->comment_ID);
                        //     $max_overview = 3;
                        //     $child_overview = $child_counts > $max_overview; // all included children count
                        //     $overview_mask = $child_overview ? ' overview' : '';
                        //     $overview_button = $child_overview ? ' <button class="vbtn extend_addon magnetic" style="">展开 '. $child_counts - $max_overview .' 条评论</button>' : '';
                        //     echo '<div class="vquote children'. $overview_mask .'" data-cpid="'.$comment->comment_ID.'">'; //'<div class="vquote">'; //
                        //         wp_child_comments_loop($comment, false);
                        //     echo $overview_button . '</div>'; //'</div>'; //
                        // }
                    ?>
                </div>
            </div>
    <?php
            // unset($lazysrc,  $post);
        }
    }
    
    // AJAX 加载评论
    if (get_option('site_ajax_comment_paginate')) {
        $comment_order = get_option('comment_order'); // fixed comment_order from newest-to-oldest on ajax_paginate on
        // Childs comment Loop-load method (recursive)
        function ajax_child_comments_loop($cur_comment){
            global $comment_order;
            // apply ai reply status
            ajax_ai_reply_status($cur_comment);
            $child_comment = $cur_comment->get_children(array(
                'hierarchical' => 'threaded',
                'order'        => 'ASC', // fixed ASC on ajax_paginate $comment_order
                'orderby' => 'comment_date_gmt',
                // 'orderby'=>'order_clause',
                // 'meta_query'=>array(
                //   'order_clause' => 'comment_parent'
                // )
            ));
            if(count($child_comment)>=1){
                // $child_comment = json_decode(json_encode($child_comment), true); // Objects to Array object
                foreach ($child_comment as $child) {
                    $comment_ID = $child->comment_ID;
                    if ($child->comment_approved == '0') $child->comment_content = '等待评论审核，通过正常显示。';
                    // use privacy data encryption
                    $child->comment_author_IP = sha1($child->comment_author_IP);
                    $child->comment_author_email = md5($child->comment_author_email);
                    // add Objects for frontend calls
                    $child->_comment_reply = get_comment_author($child->comment_parent);
                    $child->_comment_agent = get_userAgent_info($child->comment_agent);
                    $child->_comment_replytocom = get_permalink($child->comment_post_ID) . '?replytocom=' . $comment_ID . '#respond';
                    // add thoughtful comment
                    $child->_comment_thoughtful = get_comment_meta( $comment_ID, '_thoughtful_comment', true );
                    // apply ai reply status
                    ajax_ai_reply_status($child);
                    $cur_comment->_comment_childs = $child_comment; //$child_comment;//load all-childs but single[$child];
                    ajax_child_comments_loop($child);
                }
            }
            // return first-level(contains sub-more) only
            if($cur_comment->comment_parent==0) return $cur_comment; //$child_comment
        }
        // Ajax request comments output
        function ajaxLoadComments() {
            global $comment_order;
            $pid = get_request_param('pid');
            check_ajax_referer($pid.'_comment_ajax_nonce');  // 检查 nonce
            $comments_array = [];
            $comments = get_comments(array(
                'post_id' => $pid,
                'order'   => $comment_order,
                'orderby' => 'comment_date_gmt',
                // 'status'  => 'approve',
                'number'  => get_request_param('limit'),
                'offset'  => get_request_param('offset'),
                'parent'  => 0, // root comments
                // 'comment__not_in' => [2,14],
            ));
            foreach ($comments as $each) {
                $comment_ID = $each->comment_ID;
                // user privacy data crypt
                $each->comment_author_IP = sha1($each->comment_author_IP);
                $each->comment_author_email = md5($each->comment_author_email);
                // record comment childs count for frontend overview
                $child_counts = get_descendant_comment_count($comment_ID);
                $each->comment_counts = $child_counts;
                // add Objects for frontend calls
                $each->_comment_agent = get_userAgent_info($each->comment_agent);
                // add replytocom for ajax pagination
                $each->_comment_replytocom = get_permalink($each->comment_post_ID) . '?replytocom=' . $comment_ID . '#respond';
                // add thoughtful comment
                $each->_comment_thoughtful = get_comment_meta( $comment_ID, '_thoughtful_comment', true );
                if($each->comment_parent==0) array_push($comments_array, ajax_child_comments_loop($each));
            }
            print_r(json_encode($comments_array));
            die();
        }
        add_action('wp_ajax_ajaxLoadComments', 'ajaxLoadComments');
        add_action('wp_ajax_nopriv_ajaxLoadComments', 'ajaxLoadComments');
    }
    
    if (get_option('site_cdn_switcher')) {
        function replace_db_data($old_value, $new_value, $db_table = 'options', $db_row = 'option_value') {
            global $wpdb;
            $query = $wpdb->prepare("UPDATE {$wpdb->prefix}{$db_table} SET $db_row = REPLACE($db_row, %s, %s)", $old_value, $new_value);
            $res = $wpdb->query($query);
            print_r($query . '<br/>');
            echo false === $res ? "$db_table 数据库更新失败<br />" : "$db_table 查询成功，更新了 " . $res . " 行。<br />";
            return $res;
        }
        // 然后在 PHP 端处理：
        function update_db_data() {
            check_ajax_referer('dbupdate_ajax_nonce');  // 检查 nonce
            $data_before = urldecode(get_request_param('before'));
            $data_after = urldecode(get_request_param('after'));
            $data_list = urldecode(get_request_param('options'));
            if ($data_list) {
                $split_comma = explode(',', $data_list);
                foreach ($split_comma as $split_item) {
                    if ($split_item) { // && is_array($split_item)
                        $split_equal = explode('=', $split_item);
                        replace_db_data($data_before, $data_after, $split_equal[0], $split_equal[1]);
                    }
                };
            } else {
                $update_list = array(
                    ['options', 'option_value'],     // wp_options
                    ['termmeta', 'meta_value'],      // wp_termmeta
                    ['postmeta', 'meta_value'],      // wp_postmeta
                    ['posts', 'post_content'],       // wp_posts
                    ['comments', 'comment_content'], // wp_comments
                );
                foreach ($update_list as $update_item) {
                    replace_db_data($data_before, $data_after, $update_item[0], $update_item[1]);
                };
            }
            die();
        }
        add_action('wp_ajax_update_db_data', 'update_db_data');
        // 不允许未登录用户执行
        // add_action('wp_ajax_nopriv_update_db_data', 'update_db_data');
    }
    
    // 限制每篇文章保存 5 个修订版本
    // define('WP_POST_REVISIONS', 5);
    // 临时清理修订版本和自动草稿
    if (get_option('site_wpdb_optimize_switcher')) {
        function update_wp_posts_revisions() {
            global $wpdb;
            $table_name = $wpdb->posts;
            $query = "DELETE FROM {$table_name} WHERE post_status='auto-draft' OR post_type='revision'";
            $res = $wpdb->query($query);
            // 2. 执行 OPTIMIZE TABLE（只有删除了数据才需要优化）
            if ($res > 0) {
                $optimize_result = $wpdb->query("OPTIMIZE TABLE {$table_name}");
                if (false === $optimize_result) {
                    // OPTIMIZE 失败不影响主流程，只记录警告
                    echo "OPTIMIZE TABLE 优化失败: " . $wpdb->last_error . '<br />';
                } else {
                    echo "OPTIMIZE TABLE 表优化完成<br />";
                }
            }
            // 调试输出（仅管理员可见）
            if (current_user_can('manage_options')) {
                echo false === $res ? "wp_posts 数据库更新失败，错误信息: " . esc_html($wpdb->last_error) : "wp_posts 查询成功，更新了 " . intval($res) . " 行。";
            }
            return $res;
        }
        // 然后在 PHP 端处理：
        function clear_wp_revisions() {
            check_ajax_referer('clear_wp_posts_revisions_nonce');  // 检查 nonce
            update_wp_posts_revisions();
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush(); // bug: to clear memcached caches
            }
            die();
        }
        add_action('wp_ajax_clear_wp_revisions', 'clear_wp_revisions');
        // 不允许未登录用户执行
        // add_action('wp_ajax_nopriv_clear_wp_revisions', 'clear_wp_revisions');
    }
    
    /*
     *--------------------------------------------------------------------------
     * 额外功能
     *--------------------------------------------------------------------------
    */
    // 扫描指定路径目录文件
    function dirScaner($rootPath, $deepScan = false, $dirOnly = false, $fileExtend = '.log', $ajaxRequest = false) {
        // // make sure we are on the backend
        // if (!is_admin()) return false;
        if (get_request_param('action')) {
            $ajax_referer = check_ajax_referer(date('Y-m-d') . '_dirscaner_ajax_nonce');  // 检查 nonce
            $ajaxRequest = true;
            $rootPath = urldecode(get_request_param('path'));
            $deepScan = urldecode(get_request_param('deep'));
            $dirOnly = urldecode(get_request_param('dironly'));
            $fileExtend = urldecode(get_request_param('extends'));
        }
        $logFiles = [];
        $stack = [$rootPath];
        while (!empty($stack)) {
            $dir = array_pop($stack);
            if (!file_exists($dir)) {
                $logFiles = '{msg:"invalid path:'. urlencode($dir) . '",code:404}';
                break;
            }
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                // if ($fomart) $dir = str_replace('/www/wwwroot/', 'https://', $dir);
                $filePath = $dir . '/' . $file;
                if (is_dir($filePath)) {
                    // 将子目录推入栈中（深度扫描）
                    if ($deepScan) array_push($stack, $filePath);
                    if ($dirOnly) {
                        $logFiles[] = $filePath;
                        continue;
                    }
                } else {
                    // 检查（指定）文件扩展名
                    if ($fileExtend && !$dirOnly && strtolower(substr($file, -4)) === $fileExtend) {
                        $logFiles[] = $filePath;
                    }
                }
            }
        }
        if (!$ajaxRequest) {
            return $logFiles;
        }
        print_r(json_encode($logFiles));
        // must die after ajax data output. ()
        die();  // incase 0 append
    }
    add_action('wp_ajax_dirScaner', 'dirScaner');
    add_action('wp_ajax_nopriv_dirScaner', 'dirScaner');
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