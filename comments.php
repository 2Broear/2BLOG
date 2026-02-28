<?php
    global $post, $lazysrc, $src_cdn, $img_cdn;
    $post_ID = $post->ID;
    $third_cmt = get_option('site_third_comments');
    $comment_sw = $third_cmt=='Valine' ? true : false;//get_option('site_valine_switcher');
    $twikoo_sw = $third_cmt=='Twikoo' ? true : false;//get_option('site_twikoo_switcher');
    $wp_ajax_comment = get_option('site_ajax_comment_switcher');
    $wp_ajax_comment_paginate = get_option('site_ajax_comment_paginate');
    if (is_single()) {
        adsense_shortcode('adsense_list_context');
?>
    <div class="share" style="<?php if(!$comment_sw) echo 'margin-top:15px'; ?>">
        <a id="dislike" title="有点东西（Like）" href="javascript:;" data-action="like" data-id="<?php echo $pid=get_the_ID(); ?>" data-nonce="<?php echo wp_create_nonce($pid."_post_like_ajax_nonce"); ?>" class="<?php if(isset($_COOKIE['post_liked_'.$post_ID])) echo 'liked';?>" <?php if(!$comment_sw) echo 'onclick="postLike(this)"'; ?>><?php if($comment_sw) echo '<div class="user"><small></small><div id="list"></div></div>'; ?>
            <span id="like" class="count">
                <i id="counter"><?php $like=get_post_meta($post_ID,'post_liked',true);echo $like ? $like : '0'; ?></i>
                <em style="background:url(<?php echo $img_cdn; ?>/images/shareico.png) no-repeat -478px 4px"></em>
            </span>
        </a>
        <a id="qq" class="disabled" title="分享QQ" href="https://connect.qq.com/widget/shareqq/index.html?<?php echo $para_str = 'url='.get_permalink().'&p='.custom_excerpt(50, true).'&title='.get_the_title().'&summary='.custom_excerpt(100, true).'&pics='.get_postimg(); ?>" target="_blank"><span><em style="background:url(<?php echo $img_cdn; ?>/images/shareico.png) no-repeat -9px 4px"></em></span></a>
        <a id="qzone" title="分享空间（QZone）" href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?<?php echo $para_str; ?>" target="_blank"><span><em style="background:url(<?php echo $img_cdn; ?>/images/shareico.png) no-repeat -88px 4px"></em></span></a>
        <a id="Poster" title="图文海报（Poster）"><span id="recall" onclick="getPoster(this)"><em style="background:url(<?php echo $img_cdn; ?>/images/shareico.png) no-repeat -245px 4px"></em></span></a>
        <!--<img decoding="async" loading="lazy" data-src="<?php echo $img_cdn; ?>/images/bilibili_wink.webp" alt="bilibili_wink" style="margin: 0 auto;">-->
    </div>
    <script>
        function poster_sw() {
            const poster = document.querySelector(".poster");
            poster.classList && poster.classList.contains('active') ? poster.classList.remove('active') : poster.classList.add('active');
        };
        function getPoster(t) {
            if(document.querySelector("#capture")) {
                poster_sw();
                return;
            }
            t.parentNode.classList.add("disabled");  // incase multi click (first generating only)
            send_ajax_request("GET", "<?php echo $src_cdn.'/plugin/html2canvas.php'; ?>", 
                parse_ajax_parameter({
                    "title": "<?php echo urlencode(get_the_title()); ?>",
                    "content": "<?php echo urlencode(custom_excerpt(50, true)); ?>",
                    "tags": '<?php echo urlencode(get_the_tag_list('',' ','')); ?>',
                    "author": "<?php echo urlencode(get_option('site_nick')); ?>",
                    "date": "<?php the_time('d-m-Y'); ?>",
                    "image": "<?php echo urlencode(get_postimg(0,$pid,true)); ?>", //.'?fixed_cors_str'
                    "loading": "<?php custom_cdn_src('img'); ?>/images/loading_3_color_tp.png",
                }, true), function(res){
                    if(!res) throw new Error('signature error.'); //if(sign_.err) return;
					// generate poster QRCode (async)
					return new Promise(function(resolve,reject){
                        let _tp = t.parentNode,
                            div = document.createElement('DIV');
                        // _tp.classList.add("disabled");  // incase multi click (first generating only)
    					div.innerHTML += res;  //在valine环境直接追加到body会导致点赞元素层级错误（重绘性能问题）
    					document.body.appendChild(div);
                	    asyncLoad("<?php echo $src_cdn; ?>/js/qrcode/qrcode.min.js", function(){
                    		let url = location.href;
                    		var qrcode = new QRCode(document.getElementById("qrcode"), {
                    			text: url,
                    			width: 100,
                    			height: 100,
                    			colorDark : "#000000",
                    			colorLight : "#ffffff",
                    			correctLevel : QRCode.CorrectLevel.L
                    		});
                	        qrcode ? resolve(_tp) : reject('qrcode loading err.');
                	    });
					}).then(function(res){
                	    asyncLoad('<?php echo $src_cdn; ?>/js/html2canvas/html2canvas.min.js', function(){
                	       // console.log('now loading html2canvas..')
                    		html2canvas(document.querySelector('#capture'),{
                    		    useCORS: true,
                    		    allowTaint: true,
                    		    scrollX: 0,
                    		    scrollY: 0,
                    		    backgroundColor: null
                    	    }).then(canvas => {
                    	        const newImg = document.createElement("img");
                                canvas.toBlob(function(blob){
                        			newImg.src = URL.createObjectURL(blob);
                        			document.getElementById('poster').appendChild(newImg); //innerHTML += imgDom;
                                },"image/png",1);
                                res.classList.remove("disabled");  // remove click restrict
				                console.log('html2canvas done.');
                    		});
                	    });
					}).catch(function(err){
					    console.log(err);
					});
                }, function(err){
                    t.innerText = err+' occured';
                }
            );
        }
    </script>
<?php
    }
    if(comments_open() || is_category()&&$post->comment_status=="open"){ //$post->comment_status=="open"
        $user_name = array_key_exists("comment_author_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_" . COOKIEHASH] : false;
        $user_mail = array_key_exists("comment_author_email_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : false;
        $user_link = array_key_exists("comment_author_url_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_url_" . COOKIEHASH] : false;
        if (is_user_logged_in()) {
            $wp_user = get_currentuserinfo();// global $current_user;// print_r($wp_user);
            $user_name = $wp_user->user_nicename; // $_COOKIE["comment_author_" . COOKIEHASH];
            $user_mail = $wp_user->user_email; // $_COOKIE["comment_author_email_" . COOKIEHASH];
            $user_link = $wp_user->user_url; // $_COOKIE["comment_author_url_" . COOKIEHASH];
        }
        if ($comment_sw) {
            $welcome="既来之则留之~ 欢迎在下方留言评论，提交评论后还可以撤销或重新编辑。（Valine 会自动保存您的评论信息到浏览器）";
        } elseif ($twikoo_sw) {
            $welcome="既来之则留之~ 欢迎在下方留言评论";
        } else {
            $wp_login = is_user_logged_in() ? '<small> ( Logged as <a href="'.wp_login_url(get_permalink()).'" title="已登录为管理员，默认管理员信息评论，若需其他评论信息，请登出！">'.$user_name.'</a> ) </small>' : '';
            $welcome='欢迎您，'.$user_name.'！您可以在这里畅言您的的观点与见解！'.$wp_login;//<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked="checked"> <label for="wp-comment-cookies-consent">自动保存我的评论数据以便下次使用。</label>
        };
        echo '<div class="main"><span id="respond"><h2> 评论留言 </h2></span><p class="comment-form-cookies-consent">'.$welcome.'</p></div>';
        if (is_single()) {
?>
            <script type="text/javascript">
                function postLike(t){
                    if(t&&t.classList&&t.classList.contains('liked')){
                        alert("您已经点过赞了!");
                        return;
                    };
                    t.classList.add('liked');
                    send_ajax_request("get", "<?php echo admin_url('admin-ajax.php'); ?>", 
                        parse_ajax_parameter({
                            "action": "post_like",
                            "um_id": t.dataset.id,
                            _ajax_nonce: t.dataset.nonce,
                            // "um_action": t.dataset.action,
                        }, true), function(res){
                            document.querySelector('.count #counter').innerText = res;
                        }
                    );
                };
            </script>
<?php 
        };
        if ($comment_sw) {
            echo '<div id="vcomments" class="v"></div>';
        } elseif ($twikoo_sw) {
            echo '<div id="tcomment"></div>';
        } else {
            $avatar_src = match_mail_avatar($user_mail);
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            // $commenter = wp_get_current_commenter();
            $text_submit = '提交评论';
            $text_loadmore = '加载更多评论';
            $cf_turnstile_wordpress = get_cf_turnstile('Wordpress'); //get_option('site_cloudflare_turnstile') && 
?>
            <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/valine.css?v=<?php echo get_theme_info(); ?>" />
            <div id="vcomments" class="v">
            <?php 
                if ($cf_turnstile_wordpress) {
                    echo '<div id="widget-container"></div>';
                    the_cf_turnstile();
                }
            ?>
            <div class="vwrap">
                <form action="<?php echo esc_url(home_url('/')); ?>wp-comments-post.php" method="post" style="height: auto;border-radius: inherit;">
                    <div class="vheader item3">
                        <div class="avatar">
                            <span id="avatar">
                            <img class="user" alt="avatar" <?php echo $lazysrc.'="'.$avatar_src.'"';unset($lazysrc); ?> />
                            </span>
                        </div>
                        <input class="vnick vinput" type="text" id="author" name="author" placeholder="昵称" autocapitalize="off" autocomplete="off" autocorrect="off" value="<?php echo $user_name; ?>" />
                        <input class="vmail vinput" type="email" id="email" name="email" placeholder="邮箱" autocapitalize="off" autocomplete="off" autocorrect="off" value="<?php echo $user_mail; ?>" />
                        <input class="vurl vinput" type="url" id="url" name="url" placeholder="网址" autocapitalize="off" autocomplete="off" autocorrect="off" value="<?php echo $user_link; ?>" />
                    </div>
                    <div class="vedit txt-right">
                        <textarea name="comment" id="veditor" class="veditor vinput" placeholder="<?php $placeholder = '快来玩右下角的“涂鸦画板”！';$replytocom = array_key_exists('replytocom',$parameters) ? $parameters['replytocom'] : false;echo $replytocom ? '正在回复给：@'.get_comment_author($replytocom) : $placeholder; ?>"></textarea>
                        <?php
                            // if ($cf_turnstile_wordpress && !$wp_ajax_comment) echo '<input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response" value="0">';  // send turnstile token
                            echo get_comment_id_fields($post_ID);
                            do_action('comment_form', $post_ID);
                            // comment_form();
                        ?>
                        <div class="canvas_paint_board" style="display: none; font-size: 12px;">
                            <div class="paint_tools">
                                <input type="color" id="fill" class="canvas_tool" title="画笔颜色" style="background: whitesmoke;">&nbsp;&nbsp;粗细&nbsp;&nbsp;
                                <input type="number" id="bold" class="canvas_tool" title="画笔粗细" style="width: 50px; border: 1px solid whitesmoke;">&nbsp;&nbsp;
                                <button type="button" id="undraw" class="canvas_tool" title="上一步"> 撤销 </button>&nbsp;&nbsp;
                                <button type="button" id="redraw" class="canvas_tool" title="下一步"> 重做 </button>&nbsp;&nbsp;
                                <button type="button" id="eraser" class="canvas_tool" title="橡皮擦"> 擦除 </button>&nbsp;&nbsp;
                                <button type="button" id="clear" class="canvas_tool" title="全部清除"> 清屏 </button>&nbsp;
                            </div>
                            <canvas id="canvas" style="cursor: crosshair; margin: 15px auto; display: block; border-radius: 10px;">Your browser does not support the canvas element.</canvas>
                        </div>
                        <div class="vctrl">
                            <?php 
                                $reply_states = $replytocom ? 'inline-block' : 'none';
                                $reply_context = $wp_ajax_comment ? '取消回复' : get_cancel_comment_reply_link('取消回复');
                                echo '<span class="vsubmits vbtns" rel="nofollow" id="cancel-comment-reply-link" class="cancel-comment-reply-link" href="javascript:void(0);" style="display:' . $reply_states .';"> ' . $reply_context . '</span>';
                            ?>
                            <span class="painting-btn" title="CANVAS 画图面板">「 涂鸦画板 」</span>
                            <span class="vemoji-btn">关闭表情</span>
                            <span class="vpreview-btn"></span>
                            <span class="ESwitch-btn" title="bilibili 小电视" style="display:inherit">bilibili 小电视</span>
                        </div>
                        <div class="vcontrol">
                            <div class="col col-80 text-right">
                                <button type="button" id="repushBtn" class="vsubmit vbtn" style="display:none"> 重新提交 </button>
                                <button id="pushBtn" type="submit" class="submit_btn vsubmit vbtn" value="<?php echo $text_submit; ?>" data-pid="<?php echo $post_ID; ?>" data-cid="0" />回复</button>
                                <?php //cancel_comment_reply_link('取消回复'); ?>
                            </div>
                        </div>
                        <div class="info">
                            <div class="timeRecord" style="display:none;">编辑保存于：<b></b></div>
                            <!--<div class="power txt-right" style="display:none;">Powered By <a href="https://valine.js.org" target="_blank">Valine</a> v1.3.10</div>-->
                        </div>
                        <div class="vemojis" style="display:block">
                            <div class="emojis heo" style="display: block;">
                            <?php
                                global $lazysrc;
                                $emojis_counts = 64;
                                $emojis_src = custom_cdn_src('img', true) . '/images/emojis';
                                for ($i=0; $i<$emojis_counts; $i++) {
                                    echo '<img class="vemoji" ' . $lazysrc . '="' . $emojis_src . '/heo/' . $i . '.png" alt="h' . $i . '" crossorigin="Anonymous" />';
                                }
                            ?>
                            </div>
                            <div class="emojis bilibili" style="display: none;">
                            <?php
                                for ($i=0; $i<$emojis_counts; $i++) {
                                    if ($i < 10) $i = '0' . $i;
                                    echo '<img class="vemoji" ' . $lazysrc . '="' . $emojis_src . '/bilibili/b' . $i . '.png" alt="b' . $i . '" crossorigin="Anonymous" />';
                                }
                            ?>
                            </div>
                        </div>
                        <div class="vinput vpreview txt-center" style="display:none"></div>
                    </div>
                    <div style="display:none;" class="vmark"></div>
                </form>
            </div>
            <?php
                $per_page = get_option('comments_per_page', 15);
                $comments = get_comments(array(
                    'post_id' => $post_ID,
                    'number'  => $per_page,
                    'orderby' => 'comment_date', //comment_ID
                    'order'   => get_option('comment_order'),
                    // 'hierarchical' => true,
                    // 'status'  => 'approve', // approved only
                    'offset'  => 0,
                    'parent'  => 0  // top comments only
                ));
                $comments_all = get_comments(array(
                    'post_id' => $post_ID,
                    // 'status' => 'approve',
                    'count'  => true,
                    'parent'  => 0  // top comments only
                ));
                $comment_count = count($comments);
            ?>
            <div class="wp_comment_count">
                <?php echo '<strong id="count">'.$comments_all.'</strong> 条评论'; ?>
            </div>
            <div class="vlist" id="comments">
            <?php
                function custom_comment($comment, $args, $depth) {
                    global $lazysrc, $wp_ajax_comment;
                    $GLOBALS['comment'] = $comment; 
                    $approved = $comment->comment_approved;
                    // $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
            ?>
                    <div class="vcard" id="comment-<?php comment_ID(); ?>">
                        <a class="noslide" rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                            <?php 
                                if (get_option('show_avatars')) {
                                    $email = get_comment_author_email();
                                    echo '<img class="vimg" '.$lazysrc.'="'.match_mail_avatar($email).'" width=50 height=50 alt="user_avatar" />';
                                    unset($lazysrc);
                                }
                            ?>
                        </a>
                        <div class="vh" rootid="comment-<?php comment_ID(); ?>">
                            <div class="vhead">
                                <a class="vnick" rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                                    <em><?php comment_author(); ?></em>
                                </a>
                                <?php
                                    if (get_comment_author_email() == get_bloginfo('admin_email')) echo '<span class="vsys admin">admin</span>';
                                    $userAgent = get_userAgent_info($comment->comment_agent);
                                    echo '<span class="vsys">'.$userAgent['browser'].' / '.$userAgent['system'].'</span>';
                                    if($approved=="0") echo '<span class="vsys auditing">待审核</span>';
                                ?>
                            </div>
                            <div class="vmeta">
                                <span class="vtime"><?php echo get_comment_time('Y-m-d'); ?></span>
                                <span class="vedited"></span>
                                <?php 
                                    if ($approved=="1") {
                                        $comment_ID = $comment->comment_ID;
                                        if (get_option('site_ajax_comment_switcher')) {
                                            global $post;
                                            $comment_author = $comment->comment_author;
                                            echo '<a rel="nofollow" class="vat noslide comment-reply-link" href="javascript:void(0);" data-commentid="'.$comment_ID.'" data-postid="'.$post->ID.'" data-belowelement="comment-'.$comment_ID.'" data-respondelement="respond" data-replyto="'.$comment_author.'" aria-label="正在回复给：@'.$comment_author.'">回复</a>';
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
                                <?php
                                    $content = $comment->comment_content; //strip_tags($comment->comment_content);
                                    $parent = $comment->comment_parent;
                                    if($approved=='0') $content = '<small style="opacity:.5">[ 评论未审核，通过后显示 ]</small>';
                                    if($parent>0) $content = '<a href="#comment-'.$parent.'">@'. get_comment_author($parent) . '</a> , ' . $content;
                                    echo $content; //'<p>'.$content.'</p>'; //comment_text();
                                ?>
                            </div>
                        </div>
                    </div>
            <?php
                };
                if ($wp_ajax_comment && $wp_ajax_comment_paginate) {
                    // print_r($comments);
                    foreach ($comments as $each) {
                        if($each->comment_parent != 0){
                            return;
                        }
                        wp_comments_template($each);
                        // 遍历子评论列表 https://wp-kama.com/function/WP_Comment::get_children
                        $child_comment = $each->get_children(array(
                            'hierarchical' => 'threaded',
                            // 'status'       => 'approve',
                            'order'        => get_option('comment_order'), //'ASC',
                            // 'orderby'=>'order_clause',
                            // 'meta_query'=>array(
                            //   'order_clause' => 'comment_parent'
                            // )
                        ));
                        if (count($child_comment) >= 1) {
                            echo '<ul class="children" data-cpid="'.$each->comment_ID.'">'; //'<div class="vquote">'; //
                                wp_child_comments_loop($each);
                            echo '</ul>'; //'</div>'; //
                        }
                    }
                } else {
                    $wp_comment_args = array(
                    	'walker'            => null,
                    	'max_depth'         => '',
                    	'style'             => '',
                    	'callback'          => 'custom_comment',
                    	'end-callback'      => null,
                    	'type'              => 'all',
                    	'reply_text'        => 'Reply',
                    	'page'              => '',
                    	'per_page'          => $per_page,  //$per_page caused $w_comments err
                    	'avatar_size'       => 50,
                    	'reverse_top_level' => null,  //set null for panel settings
                    	'reverse_children'  => null
                    );
                    wp_list_comments($wp_comment_args);
                }
            ?>
            </div>
        <?php
            if (have_comments()) { //$comment_count>=1
        ?>
            <nav class="pageSwitcher dev">
                <?php 
                    if ($wp_ajax_comment_paginate) {
                        $load_class = 'loadmore';
                        if ($comment_count === $comments_all) {
                            $load_class = $load_class . ' disabled';
                            $text_loadmore = '没有更多评论';
                        }
                        echo '<a href="javascript:;" class="' . $load_class . ' noslide" data-click="0" data-load="'.$comment_count.'" data-counts="'.$comments_all.'" data-nonce="'.wp_create_nonce($post_ID."_comment_ajax_nonce").'">'.$text_loadmore.'</a>';
                    } else {
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
                            return '<a class="loadmore noslide wp" href="' . esc_url( get_comments_pagenum_link( $prevpage ) ) . '" ' . apply_filters( 'previous_comments_link_attributes', '' ) . '><i class="icom"></i>' . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
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
                            return '<a class="loadmore noslide wp" href="' . esc_url( get_comments_pagenum_link( $nextpage, $max_page ) ) . '" ' . apply_filters( 'next_comments_link_attributes', '' ) . '>' . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '<i class="icom left"></i></a>';
                        }
                        echo get_previous_comments_html("PREV COMMENTS");
                        echo get_next_comments_html("NEXT COMMENTS");
                    }
                ?>
            </nav>
        <?php
            }
        ?>
            </div>
            <script type="text/javascript">
                const admin_md5mail = "<?php echo md5(get_option('site_smtp_mail', get_bloginfo('admin_email'))); ?>"; //preset for wp_comment
                const comment_loads = <?php echo $per_page; ?>;
                const avatar_cdn = "<?php echo get_option('site_avatar_mirror'); ?>";
                const admin_ajax = "<?php echo admin_url('admin-ajax.php'); ?>";
                const placeholder = "<?php echo $placeholder; ?>";
        	    const vcomments = {
        	        dom: {
        	           // wrap: document.querySelector('.vwrap'),
        	            box: document.querySelector('#vcomments'), //.wp_comment_box form .userinfo
        	        },
                    init: function(dom) {
                        this.dom = dom;
        	           // this.ctrls = document.querySelector('.vedit');
        	            this.vwrap = document.querySelector('.vwrap');
        	            this.vinfo = this.vwrap?.querySelector('.vheader');
        	            this.vtext = this.vwrap?.querySelector('textarea');
                        this.vcount = document.querySelector(".wp_comment_count");
        	            this.vlist = document.querySelector('.vlist');
        	            this.vbtn = document.querySelector("#pushBtn");
        	            this.vcanvas = document.getElementById('canvas');
        	            this.vctx = this.vcanvas?.getContext('2d');
                    },
                };
                Object.setPrototypeOf(vcomments.init.prototype, {
                    reply_obj: {
                        last_reply: null,
                        context: {
                            reply: '回复',
                            loading: "加载中..",
                            comment_more: "加载更多评论",
                            comment_cancel: '取消回复',
                            comment_repeat: '检测到重复评论，您似乎已经提交过这条评论了！',
                            comment_limits: '您提交评论的速度太快了，请稍后再发表评论。',
                            comment_error: '抱歉，服务器错误，请稍后再试。',
                            comment_counter: '条评论',
                            comment_submit: '提交中..',
                            comment_loaded: '已加载全部评论',
                            turnstile_error: '重置 turnstile 发生错误！',
                            turnstile_waitting: '请先完成 turnstile 验证！',
                            invalid_comment: '评论填写有误！',
                            invalid_nickname: '昵称填写有误！',
                            invalid_email: '邮箱格式有误！',
                            class_err: 'err',
                            class_replying: 'replying',
                            class_no_reply: 'disabled_reply',
                            class_disabled: 'disabled',
                            class_loading: 'loading',
                            class_drawing: 'drawing',
                            emojis_bilibili: 'bilibili 小电视',
                            emojis_heo: 'HOE 表情包',
                            emojis_on: '打开表情',
                            emojis_off: '关闭表情',
                            canvas_on: '「 涂鸦画板 」',
                            canvas_off: '「 关闭画板 」',
                            canvas_eraser: '擦除',
                            canvas_erased: '取消擦除',
                        },
                        canvas: {
                            // vcanvas: document.getElementById('canvas'),
                            // vctx: this.vcanvas?.getContext('2d'),
                            // veditor: document.getElementById('veditor'),
                            eraser: document.getElementById('eraser'),
                            clear: document.getElementById('clear'),
                            number: document.getElementById('bold'),
                            color: document.getElementById('fill'),
                            vitual: new Image(),
                            width: 1102,
                            height: 322,
                            lineColor: "#000000",
                            lineBold: 5,
                            trigger: false,
                            drawCount: 0,
                            drawHistory: [],
                        },
                    },
                    
                    move: function(down_x, down_y, move_x, move_y) {
                        if (this.reply_obj.canvas.trigger == true) {
                            this.vctx.lineTo(down_x, down_y);
                            this.vctx.lineTo(move_x, move_y);
                            this.vctx.clearRect(move_x, move_y, this.reply_obj.canvas.number.value, this.reply_obj.canvas.number.value)
                        } else {
                            this.vctx.beginPath();
                            this.vctx.lineTo(down_x, down_y);
                            this.vctx.lineTo(move_x, move_y);
                            this.vctx.stroke();
                        }
                    },
                    draw: function() {
                        const that = this;
                        this.vcanvas.onmousedown = (omd) => {
                            that.dom.classList.add(that.reply_obj.context.class_drawing);
                            let down_x = omd.offsetX,
                                down_y = omd.offsetY;
                            // console.log(down_x, "," + down_y);
                            that.vcanvas.onmousemove = (omm) => {
                                // that.clearSelect('none');
                                let move_x = omm.offsetX,
                                    move_y = omm.offsetY;
                                that.move(down_x, down_y, move_x, move_y);
                                down_x = move_x;
                                down_y = move_y;
                                that.vcanvas.onmouseup = () => {
                                    that.exportCanvas();
                                    that.move(down_x, down_y, move_x, move_y);
                                }
                            }
                            that.vcanvas.onmouseup = () => that.exportCanvas();
                        };
                        this.vcanvas.ontouchstart = (ots) => {
                            ots.preventDefault();
                            let boundingTopStart = that.vcanvas.getBoundingClientRect().top,
                                boundingLeftStart = that.vcanvas.getBoundingClientRect().left,
                                down_x = ots.touches[0].clientX - boundingLeftStart,
                                down_y = ots.touches[0].clientY - boundingTopStart;
                            // console.log(down_x, "," + down_y);
                            that.vcanvas.ontouchmove = (otm) => {
                                otm.preventDefault();
                                // that.clearSelect('none');
                                let boundingTopMove = that.vcanvas.getBoundingClientRect().top,
                                    boundingLeftMove = that.vcanvas.getBoundingClientRect().left,
                                    move_x = otm.changedTouches[0].clientX - boundingLeftMove,
                                    move_y = otm.changedTouches[0].clientY - boundingTopMove;
                                // console.log(move_x + ',' + move_y);
                                that.move(down_x, down_y, move_x, move_y);
                                down_x = move_x;
                                down_y = move_y;
                                that.vcanvas.ontouchend = () => {
                                    that.exportCanvas();
                                    that.move(down_x, down_y, move_x, move_y);
                                }
                            }
                        }
                    },
                    redraw: function() {
                        this.reply_obj.canvas.drawCount < this.reply_obj.canvas.drawHistory.length ? this.reply_obj.canvas.drawCount++ : this.reply_obj.canvas.drawCount = this.reply_obj.canvas.drawHistory.length;
                        let stepfoward = this.reply_obj.canvas.drawHistory[this.reply_obj.canvas.drawCount - 1];
                            img = '<img id="draw" src="' + stepfoward + '" />';
                        stepfoward != undefined ? (this.reply_obj.canvas.vitual.src = stepfoward, this.vtext.value = img, this.vtext.focus()) : false;
                        this.clearCanvas();
                        this.reply_obj.canvas.vitual.onload = () => this.vctx.drawImage(this.reply_obj.canvas.vitual, 0, 0);
                    },
                    undraw: function() {
                        this.reply_obj.canvas.drawCount > 0 ? this.reply_obj.canvas.drawCount-- : this.reply_obj.canvas.drawCount = 0;
                        this.reply_obj.canvas.drawCount <= 1 ? (this.vtext.value = null, this.vtext.focus()) : false;
                        let stepback = this.reply_obj.canvas.drawHistory[this.reply_obj.canvas.drawCount - 1];
                            img = '<img id="draw" src="' + stepback + '" />';
                        stepback != undefined ? (this.reply_obj.canvas.vitual.src = stepback, this.vtext.value = img, this.vtext.focus()) : false;
                        this.clearCanvas();
                        this.reply_obj.canvas.vitual.onload = () => this.vctx.drawImage(this.reply_obj.canvas.vitual, 0, 0);
                    },
                    eraser: function(t) {
                    	this.reply_obj.canvas.trigger == false ? (this.reply_obj.canvas.trigger = true, t.innerText = this.reply_obj.context.canvas_erased) : (this.reply_obj.canvas.trigger = false, t.innerText = this.reply_obj.context.canvas_eraser);
                    },
                    clears: function(focus = false) {
                    	this.clearCanvas();
                    	this.vtext.value = null;
                    	if (focus) this.vtext.focus()
                    },
                    unbindCanvas: function() {
                        this.vcanvas.onmousedown = null;
                        this.vcanvas.onmousemove = null;
                        this.vcanvas.onmouseup = null;
                        // this.clearSelect();
                        this.vcanvas.ontouchstart = null;
                        this.vcanvas.ontouchmove = null;
                        this.vcanvas.ontouchend = null;
                    },
                    exportCanvas: function(filetype = 'image/png', quality = 0.92) {
                        // console.log('draw done.');
                        const that = this;
                        this.dom.classList.remove(this.reply_obj.context.class_drawing);
                        this.unbindCanvas();
                        this.draw();
                        this.vcanvas.toBlob(function(blob) {
                            let blobUrl = URL.createObjectURL(blob),
                                imgDom = `<img id="draw" src="${blobUrl}" />`;
                            that.vtext.value = imgDom; //+=
                            that.reply_obj.canvas.drawCount = that.reply_obj.canvas.drawHistory.length + 1;
                            that.reply_obj.canvas.drawHistory.push(blobUrl);
                        }, filetype, quality)
                    },
                    initCanvas: function(reinit = false) {
                        this.vcanvas.width = this.reply_obj.canvas.width;
                        this.vcanvas.height = this.reply_obj.canvas.height;
                        this.vcanvas.style = "cursor:crosshair;margin: 15px auto;display: block;border-radius:10px;";
                        this.vctx.lineCap = 'round';
                        this.reply_obj.canvas.number.style.border = "1px solid whitesmoke";
                        this.vctx.lineWidth = this.reply_obj.canvas.number.value = this.reply_obj.canvas.lineBold;
                        this.vctx.strokeStyle = this.reply_obj.canvas.color.value = this.reply_obj.canvas.lineColor;
                        // update canvas size only
                        if (reinit) return;
                        this.draw();
                    },
                    resizeCanvas: function(redraw = false) {
                        const canvas = this.vwrap.querySelector('.canvas_paint_board');
                        const canvasWidth = canvas.offsetWidth;
                        // update issue: ensure visible-canvas && size-changed before update canvas size
                        if (canvasWidth > 0 && canvasWidth !== this.reply_obj.canvas.width) {
                            this.reply_obj.canvas.width = canvasWidth;  // update realtime-canvas width
                            this.initCanvas(true);  // re-init canvas size
                            if (redraw) this.redraw();  // return to newest drawHistory
                        }
                    },
                    clearCanvas: function() {
                        this.vctx.clearRect(0, 0, this.reply_obj.canvas.width, this.reply_obj.canvas.height);
                    },
                    // clearSelect: function(state = '') {
                    //     document.body.style.userSelect = state;
                    // },
                    
                    filterComments: function(callback, comments) {
                        comments = comments || this.vtext.value;
                    <?php
                        if (get_option('site_comment_blockoutside')) {
                    ?>
                        const userLang = navigator.language || navigator.browserLanguage;
                        if (!userLang.match(/zh-CN|zh-HK|zh-MO|zh-TW/ig)) { ///china|hong kong|Taiwan/ig
                            callback?.(`Region Forbidden occured: ${comments}`, true);
                            return;
                        }
                    <?php
                        }
                        $comment_blacklist = get_option('site_comment_blacklists');
                        if ($comment_blacklist) {
                    ?>
                        const regBlack = new RegExp("<?php echo $comment_blacklist; ?>", 'g');
                        if (comments.match(regBlack)) {
                            callback?.(`Spam Comments contains: ${comments}`, true);
                            return;
                        }
                    <?php
                        }
                    ?>
                        // XSS filter
                        comments = comments.replace(/<script.*?>.*?<\/script>/gi,''); //comments || this.vtext.value;
                        // BLOB filter
                        var blobStrArr = comments.match(/blob.*?"/g);
                        if (blobStrArr) {
                            let that = this,
                                xhr = new XMLHttpRequest,
                                tempBaseArr = [];
                            xhr.responseType = 'blob';
                            for (let i = 0, blobLen = blobStrArr.length; i < blobLen; i++) {
                                xhr.onload = function() {
                                    var recoveredBlob = xhr.response;
                                    var reader = new FileReader;
                                    reader.onload = function() {
                                        tempBaseArr.push(reader.result);
                                        var map = that.arrReplacer(comments, blobStrArr, tempBaseArr);
                                        callback?.(map);
                                        return;
                                    };
                                    reader.readAsDataURL(recoveredBlob)
                                }
                                // xhr.onerror = function() {
                                //     callback?.(comments);
                                // }
                                xhr.open('GET', blobStrArr[i].replace('"', ''));
                                xhr.send();
                            }
                            return;
                        }
                        callback?.(comments);
                    },
                    
                    toggleSwitch: function(target = null, btn = null, _text = '', text_ = '', callback) {
                        if (!target || !btn) return;
                        if (btn.style.display === 'block') {
                            btn.style.display = 'none';
                            if (_text) target.textContent = target.title = _text;
                        } else {
                            btn.style.display = 'block';
                            if (text_) target.textContent = target.title = text_;
                        }
                        callback?.();
                    },
                    
                    arrReplacer: function(repStr, fromArr, toArr) {
                        if (!Array.isArray(fromArr) || !Array.isArray(toArr)) return repStr;
                        for (let i=0, j=fromArr.length; i<j; i++) {
                            repStr = repStr.split(fromArr[i].replace('"','')).join(toArr[i]);
                        }
                        return repStr
                    },
                    
                <?php 
                    if ($cf_turnstile_wordpress) {
                ?>
                    //*** Submit/reply comments logic !!global var:turnstile ***//
                    resetTurnstile: function() {
                        if (!turnstile) { //this.vbtn
                            console.warn(this.reply_obj.context.turnstile_error, this.verify, turnstile); //this.vbtn
                            return;
                        }
                        // this.vbtn.dataset.token = "";
                        turnstile.reset(this.verify?.dataset?.tid || 'cf-chl-widget-xxxxx'); //this.vbtn.dataset?.tid
                    },
                    
                    validTurnstileToken: function() { //this.vbtn.dataset.token
                        if (!this.verify?.value) {
                            alert(this.reply_obj.context.turnstile_waitting);
                            return false;
                        }
                        return true;
                    },
                <?php
                    }
                    if ($wp_ajax_comment) {
                ?>
                    childComments: function loop(childs) {
                        let output = "";
                        if (childs) {
                          //console.log(childs);  //Objects
                          childs = Object.values(childs);  //Objects to Array Object
                          //console.log(childs);  //Array Object
                          for(let i=0,childLen=childs.length;i<childLen;i++){
                              let child = childs[i],
                                  id = child.comment_ID,
                                  nick = child.comment_author,
                                  link = child.comment_author_url,
                                  email = child.comment_author_email,
                                  parent = child.comment_parent,
                                  approve = child.comment_approved,
                                  content = child.comment_content,//strip_tags(child.comment_content),
                                  user_agent = child._comment_agent,
                                  is_admin = email == admin_md5mail ? '<span class="admin">admin</span>' : '',
                                  is_approved = approve=="0" ? '<span class="auditing vsys">待审核</span>' : '',
                                  replytocom = approve=="1" ? `<a rel="nofollow" class="vat noslide comment-reply-link" href="javascript:void(0);" data-commentid="${id}" data-postid="<?php echo $post_ID; ?>" data-belowelement="comment-${id}" data-respondelement="respond" data-replyto="${nick}" aria-label="正在回复给：@${nick}">回复</a>` : "";
                              if(approve=="0") content = '<small style="opacity:.5">[ '+content+' ]</small>'; //${cururl}?replytocom=${id}#respond
                              // track-back (childCommentsLoop insert after output)
                              output += `<div class="vcard" id="comment-${id}"><a class="noslide" rel="nofollow" href="${link}" target="_blank"><img class="vimg" src="${avatar_cdn+'avatar/'+email}" width="50" height="50" alt="user_avatar"> </a><div class="vh" rootid="comment-${parent}"><div class="vhead"><a class="vnick" rel="nofollow" href="${link}" target="_blank"><em>${nick}</em></a><span class="vsys admin">admin</span><span class="vsys useragent">Safari / macOS</span></div><div class="vmeta"><span class="vtime">${child.comment_date}</span><span class="vedited"></span>${replytocom}</div><div class="vcontent"><p><a href="#comment-${parent}">@${nick}</a> , ${content}</p></div></div></div>` + loop(child._comment_childs);
                           }
                        }
                        return output;
                    },
                    
                    cancelReply: function(t, redraw = false) {
                        if (!t && this.reply_obj.last_reply) t = this.reply_obj.last_reply; // case of cancel from successed submit
                        // clear last_reply state
                        if (t) {
                            t.textContent = this.reply_obj.context.reply;
                            t.classList.remove(this.reply_obj.context.class_replying);
                        }
                        // adopt last_reply dom
                        let adopt_node = document.adoptNode(this.vwrap), //comment_box
                            adopt_area = adopt_node.querySelector("textarea[name=comment]"),
                            adopt_submit = adopt_node.querySelector("button[type=submit]"),
                            remains_node = document.querySelector(".vwrap"); //wp_comment_box
                        // detect if adopt_node remains before manual-load-comments (adoptNode cache)
                        if(remains_node) remains_node.remove();
                        this.dom.insertBefore(adopt_node, this.vcount);  // reverse adopt
                        adopt_area.placeholder = placeholder;
                        this.class_switcher(adopt_node.querySelectorAll(`.${this.reply_obj.context.class_err}`), this.reply_obj.context.class_err, false); // adopt_area.classList.remove(this.reply_obj.context.class_err);
                        adopt_submit.dataset.cid = 0;
                        adopt_submit.removeAttribute('data-replyto');
                        // update real-time canvas size&&returns newest drawHistory
                        this.resizeCanvas(redraw);
                    },
                    
                    class_switcher: function(els, cls, disabled = true) {
                        if (!els) return;
                        for (let i=0,eLen=els.length; eLen>i; i++) {
                            disabled ? els[i].classList.add(cls) : els[i].classList.remove(cls);
                        }
                    },
                    
                <?php
                    }
                ?>
                });
                
                Object.defineProperty(vcomments.init.prototype, 'dispatchEvents', {
                    value: function() {
                        const that = this;
                        // RealtimeAvatar
                        (function() {
                            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                  email = that.vinfo.querySelector('input[type=email]'),
                                  avatar = that.vinfo.querySelector('.avatar .user'); //img.avatar
                            if (!email || !avatar) {
                                // console.log(that.dom, email, avatar);
                                that.realtime_fields = that.vinfo;
                                throw new Error('email-field not exist, fallback to preset node..');
                            };
                            email.onchange = function(e) {
                                let mail = this.value;
                                if(!regex.test(mail)){
                                    console.log('invalid email.');
                                    return;
                                };
                                send_ajax_request("get", '<?php echo custom_cdn_src('default', true).'/plugin/gravatar.php' ?>?jump=0&email='+mail, false, (res)=>{
                                    try{
                                        let resed = JSON.parse(res);
                                        resed.code==200 ? avatar.setAttribute('src',resed.msg) : console.warn(resed.err);
                                    }catch(e){
                                        avatar.setAttribute('src',res);
                                    }
                                });
                            }
                        })();
                        
                        // Realtime Changes
                        bindEventClick(this.dom, '', function(t, e) {
                            if (!t.id) return;
                            switch (t.id) {
                                case 'fill':
                                    that.vctx.strokeStyle = that.reply_obj.canvas.lineColor = t.value;
                                    break;
                                case 'bold':
                                    t.value <= 1 ? (t.value = 1, that.vctx.lineWidth = 1) : that.vctx.lineWidth = that.reply_obj.canvas.lineBold = t.value;
                                    break;
                                default:
                                    // console.log(t.value)
                            }
                        }, 'oninput');
                        
                        // Realtime Clicks
                        bindEventClick(this.dom, '', function(t, e) {
                            if (t.id) {
                                switch (t.id) {
                                    case 'undraw':
                                        that.undraw();
                                        break;
                                    case 'redraw':
                                        that.redraw();
                                        break;
                                    case 'eraser':
                                        that.eraser(t);
                                        break;
                                    case 'clear':
                                        that.clears(true);
                                        break;
                                    default:
                                        // console.log(t)
                                }
                            }
                            
                            switch (true) {
                                // emojis
                                case t.classList.contains('vemoji'): //t.nodeName.toUpperCase() == 'IMG'
                                    let vtext_value = that.vtext.value,
                                        vtext_quote = vtext_value.match(<?php echo $third_cmt == 'Wordpress' ? '/^(.*?)(<blockquote class="vquotes">.*?<\/q><\/blockquote>)/s' :'/^([\s\S]*?)\n([\s\S]*)$/'; ?>);
                                    if (vtext_quote) {
                                        // console.log(vtext_quote);
                                        that.vtext.value = `${vtext_quote[1]}<img src="${t.src}" alt="emoji" />\n${vtext_quote[2]}`;
                                        let srange = that.vtext.value.indexOf('<?php echo $third_cmt ? '<blockquote class="vquotes">' : '\n';?>') - 1;
                                        that.vtext.setSelectionRange(srange, srange);
                                        that.vtext.focus();
                                    } else {
                                        that.vtext.value += `<img src="${t.src}" alt="emoji" />`;
                                        that.vtext.focus();
                                    }
                                    break;
                                // vedit ue tools
                                case t.classList.contains('ESwitch-btn'):
                                    const emojis = that.vwrap.querySelectorAll('.emojis');
                                    emojis.forEach((emoji)=> that.toggleSwitch(t, emoji, that.reply_obj.context.emojis_bilibili, that.reply_obj.context.emojis_heo));
                                    break;
                                case t.classList.contains('vemoji-btn'):
                                    const vemojis = that.vwrap.querySelector('.vemojis');
                                    that.toggleSwitch(t, vemojis, that.reply_obj.context.emojis_on, that.reply_obj.context.emojis_off);
                                    break;
                                case t.classList.contains('painting-btn'):
                                    const canvas = that.vwrap.querySelector('.canvas_paint_board');
                                    that.toggleSwitch(t, canvas, that.reply_obj.context.canvas_on, that.reply_obj.context.canvas_off, ()=> {
                                        that.resizeCanvas();
                                    });
                                    break;
                                default:
                                    console.debug(t);
                                    // code
                            }
                    <?php
                        if ($wp_ajax_comment) {
                    ?>
                            /**
                            * 
                            * load more
                            * logic
                            * 
                            **/
                            if (t.classList.contains("loadmore")) {
                                let years = t.dataset.year,
                                    loads = parseInt(t.dataset.load),
                                    counts = parseInt(t.dataset.counts),
                                    clicks = parseInt(t.dataset.click);
                                if (loads >= counts) {
                                    t.classList.add(that.reply_obj.context.class_disabled);
                                    t.innerText = that.reply_obj.context.comment_loaded;
                                    // return;
                                } else {
                                    clicks++;
                                    t.innerText = that.reply_obj.context.loading;
                                    t.classList.add(that.reply_obj.context.class_loading, that.reply_obj.context.class_disabled);
                                    t.setAttribute('data-click', clicks);
                                    send_ajax_request("post", admin_ajax, parse_ajax_parameter({
                                            "action": "ajaxLoadComments",
                                            "pid": <?php echo $post_ID; ?>, 
                                            "limit": comment_loads,
                                            "offset": comment_loads * clicks,
                                            // "parent": 0,
                                            _ajax_nonce: t.dataset.nonce,
                                        }, true), function(res){
                                            var posts_array = JSON.parse(res),
                                                posts_count = posts_array.length,
                                                loads_count = loads+posts_count;
                                            t.innerText = that.reply_obj.context.comment_more;
                                            t.classList.remove(that.reply_obj.context.class_loading, that.reply_obj.context.class_disabled);
                                            loads_count>=counts ? t.setAttribute('data-load', counts) :  t.setAttribute('data-load', loads_count);  // update current loaded(limit judge)
                                            for (let i=0; i<posts_count; i++) {
                                                let each_post = posts_array[i],
                                                    id = each_post.comment_ID,
                                                    nick = each_post.comment_author,
                                                    link = each_post.comment_author_url,
                                                    md5mail = each_post.comment_author_email,
                                                    childs = each_post._comment_childs,
                                                    user_agent = each_post._comment_agent,
                                                    content = each_post.comment_content,//strip_tags(each_post.comment_content),
                                                    approve = each_post.comment_approved,
                                                    if_child = childs ? '<ul class="children" data-cpid="'+id+'">'+that.childComments(childs)+'</ul>' : '',
                                                    is_admin = md5mail == admin_md5mail ? '<span class="vsys admin">admin</span>' : '',
                                                    is_approved = approve=='0' ? '<span class="vsys auditing">待审核</span>' : '',
                                                    replytocom = approve=='1' ? `<a rel="nofollow" class="vat noslide comment-reply-link" href="javascript:void(0);" data-commentid="${id}" data-postid="<?php echo $post_ID; ?>" data-belowelement="comment-${id}" data-respondelement="respond" data-replyto="${nick}" aria-label="正在回复给：@${nick}">回复</a>` : "";
                                                if(approve=="0") content = '<small style="opacity:.5">[ '+content+' ]</small>'; //${cururl}?replytocom=${id}#respond
                                                var appendList = document.createElement("div");
                                                // DO NOT use comment_list.innerHTML, innerHTML will refresh dom list (caused: binded event lose efficacy)
                                                appendList.id = "comments-"+id;
                                                appendList.classList.add("vcard"); //wp_comments
                                                appendList.innerHTML += `<a class="noslide" rel="nofollow" href="${link}" target="_blank"> <img class="vimg" src="${avatar_cdn+'avatar/'+md5mail}" width="50" height="50" alt="user_avatar"> </a> <div class="vh" rootid="${each_post.comment_parent}"> <div class="vhead"> <a class="vnick" rel="nofollow" href="${link}" target="_blank"> <em>${nick}</em> </a> ${is_admin}<span class="vsys useragent">${user_agent.browser+" / "+user_agent.system}</span> ${is_approved}</div> <div class="vmeta"> <span class="vtime">${each_post.comment_date}</span> <span class="vedited"></span> ${replytocom} </div> <div class="vcontent"> <p>${content}</p> </div> </div>`; //${if_child}
                                                that.vlist.appendChild(appendList);
                                                that.vlist.innerHTML += if_child;
                                            }
                                            // compare updated load counts
                                            if(parseInt(t.dataset.load)>=counts){
                                                t.innerText = that.reply_obj.context.comment_loaded;
                                                t.classList.add(that.reply_obj.context.class_disabled);
                                            }
                                        }
                                    );
                                }
                            }
                            /**
                            * comment reply handler
                            * logic
                            * 
                            **/
                            (function() {
                                // if (t.id && t.id == "cancel-comment-reply-link") 
                                if (t.classList.contains(that.reply_obj.context.class_replying)) {
                                    that.cancelReply(t, true);
                                    return;
                                };
                                if (t.classList.contains('comment-reply-link')) {
                                <?php 
                                    // return-reply(inside func) on turnstile-enabled with invalid TurnstileToken
                                    if ($cf_turnstile_wordpress) echo 'if (!that.validTurnstileToken()) return;'; 
                                ?>
                                    // clear states
                                    if (that.reply_obj.last_reply) {
                                        that.reply_obj.last_reply.textContent = that.reply_obj.context.reply;
                                        that.reply_obj.last_reply.classList.remove(that.reply_obj.context.class_replying);
                                    }
                                    that.reply_obj.last_reply = t; // record last reply dom
                                    t.textContent = that.reply_obj.context.comment_cancel;
                                    t.classList.add(that.reply_obj.context.class_replying);  //disable(current-reply) adopt
                                    let adopt_node = document.adoptNode(that.vwrap),  //comment_box
                                        adopt_area = adopt_node.querySelector("textarea[name=comment]"),
                                        adopt_submit = adopt_node.querySelector("button[type=submit]"),
                                        remains_node = document.querySelector(".vwrap"); //wp_comment_box
                                    // detect if adopt_node remains before manual-load-comments (adoptNode cache)
                                    if(remains_node) remains_node.remove();
                                    getParByCls(t, "vh").appendChild(adopt_node);  // append adopt node/vcontent
                                    adopt_area.placeholder = "正在回复给：@"+t.dataset.replyto;
                                    adopt_area.focus();
                                    that.class_switcher(adopt_node.querySelectorAll(`.${that.reply_obj.context.class_err}`), that.reply_obj.context.class_err, false); // adopt_area.classList.remove(that.reply_obj.context.class_err);
                                    adopt_submit.dataset.cid = t.dataset.commentid;
                                    adopt_submit.dataset.replyto = t.dataset.replyto;
                                    // that.cancelReply(t, true);  // comments return
                                    // update real-time canvas size&&returns newest drawHistory
                                    that.resizeCanvas(true);
                                };
                            })();
                            /**
                             * comment submit handler
                             * logic
                             * 
                             **/
                            if (t.classList.contains('submit_btn')) {
                                e.preventDefault();  // prevent form submit
                                const comment = that.vtext;
                                const author = that.vinfo.querySelector("input[name=author]");
                                const email = that.vinfo.querySelector("input[name=email]");
                                let a_val = author.value,
                                    e_val = email.value,
                                    c_val = comment.value,
                                    comment_pid = t.dataset.cid,
                                    comment_cid = t.dataset.pid;
                                <?php 
                                    if (get_option( 'require_name_email' )) {
                                ?>
                                    const e_reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/,
                                          t_fuc = function(str){
                                              let trim = str.replace(/^\s+|\s+$/g,"").replace( /^\s*/, '');
                                              return trim ? trim : false;
                                          },
                                          c_err = function(el,msg){
                                              el.setAttribute('placeholder', msg)
                                              el.focus();
                                              el.classList.add(that.reply_obj.context.class_err);
                                              el.oninput=function(){
                                                  this.value ? this.classList.remove(that.reply_obj.context.class_err) : this.oninput=null;
                                              };
                                          };
                                    if(!t_fuc(c_val)) c_err(comment, that.reply_obj.context.invalid_comment);
                                    if(!t_fuc(a_val)) c_err(author, that.reply_obj.context.invalid_nickname);
                                    if(!e_reg.test(e_val)) c_err(email, that.reply_obj.context.invalid_email);
                                    if(!t_fuc(c_val) || !t_fuc(a_val) || !e_reg.test(e_val)){
                                        return;
                                    }
                                <?php 
                                    }
                                ?>
                                (function() {
                                <?php if ($cf_turnstile_wordpress) echo 'if (!that.validTurnstileToken()) return;';  // return-reply(inside func) on turnstile-enabled with invalid TurnstileToken ?>
                                    t.value = that.reply_obj.context.comment_submit;
                                    // t.classList.add('busy');  //disable submit
                                    that.dom.classList.add(that.reply_obj.context.class_no_reply);  //disable reply
                                    that.filterComments((filter_c_val, filter_exit)=> {
                                        if (filter_exit) {
                                            alert(filter_c_val);
                                            return;
                                        }
                                        that.clears(); // clear canvas
                                        send_ajax_request("post", "<?php echo esc_url(home_url('')) . '/wp-comments-post.php'; //admin_url('admin-ajax.php');// ?>", 
                                            parse_ajax_parameter({
                                                "comment": encodeURIComponent(filter_c_val),
                                                "author": a_val,
                                                "email": e_val,
                                                "url": that.vinfo.querySelector("input[name=url]").value,
                                                "comment_post_ID": comment_cid,
                                                "comment_parent": comment_pid,
                                                "cf-turnstile-response": that.verify?.value, //t.dataset.token,
                                                // 'action': 'wp-comments-post',
                                                // _ajax_nonce: t.dataset.nonce,
                                            }, true), function(res) {
                                                const handleComment = function() {
                                                    let temp_date = new Date(),
                                                        temp_comment = document.createElement("div"),
                                                        comment_replyto = t.dataset.replyto ? '<a href="#comment-'+comment_pid+'">@'+t.dataset.replyto+'</a> , ' : '',
                                                        comment_info = '<span class="auditing vsys"> Auditing / Previews </span>';
                                                    if(a_val=="<?php echo $user_name; ?>"&&e_val=="<?php echo $user_mail; ?>"){
                                                        comment_info = admin_md5mail=="<?php echo md5($user_mail); ?>" ? '<span class="vsys admin">admin</span><span class="vsys useragent"> Comments Preview </span>' : '<span class="vsys useragent"> Comments / Preview </span>';
                                                    }
                                                    temp_comment.classList.add("vcard"); //wp_comments
                                                    temp_comment.innerHTML = `<a class="noslide" rel="nofollow" href="" target="_blank"> <img class="vimg" src="${that.vinfo.querySelector('.avatar img').src}" width="50" height="50" alt="user_avatar"> </a> <div class="vh" rootid=""> <div class="vhead"> <a class="vnick" rel="nofollow" href="" target="_blank"> <em>${a_val}</em> </a> ${comment_info}</div> <div class="vmeta"> <span class="vtime">${temp_date.toLocaleDateString()}</span> <span class="vedited"></span> </div> <div class="vcontent"> <p>${comment_replyto} ${filter_c_val}</p> </div> </div>`;
                                                    const inside_child_reply = getParByCls(t, 'children'),
                                                          check_child_reply = getParByCls(t, 'vcard'); //wp_comments
                                                    if (inside_child_reply) {
                                                        inside_child_reply.appendChild(temp_comment);  // inside loaded-children list reply
                                                    } else {
                                                        if(check_child_reply){
                                                            let outside_child_list = check_child_reply.nextElementSibling;
                                                            if(outside_child_list && outside_child_list.classList.contains('children')){
                                                                outside_child_list.appendChild(temp_comment);  // child-list exist (case: child-list next to wp_comments)
                                                            }else{
                                                                // create children list (case: new reply to parent comment)
                                                                let wrap_ul = document.createElement("ul");
                                                                wrap_ul.classList.add("children");
                                                                wrap_ul.appendChild(temp_comment);
                                                                that.vlist.insertBefore(wrap_ul, check_child_reply.nextElementSibling);  // (insert next to wp_comments)
                                                                wrap_ul = null;  //clear memory
                                                            }
                                                        }else{
                                                            // direct insert child-comment
                                                            that.vlist.insertBefore(temp_comment, that.vlist.firstElementChild);
                                                            //update comment_count at level-0 submit
                                                            that.vcount.innerHTML = `<strong id="count"> ${(parseInt(that.vcount.innerText)+1)}</strong> ${that.reply_obj.context.comment_counter}`;
                                                        }
                                                    }
                                                    temp_comment = null;  //clear memory
                                                    t.value = "<?php echo $text_submit; ?>";
                                                    comment.value = "";
                                                    // t.classList.remove('busy');  //enable submit
                                                    that.dom.classList.remove(that.reply_obj.context.class_no_reply);  //enable reply
                                                }
                                                try {
                                                    res = JSON.parse(res);
                                                    if (res?.success) {
                                                        handleComment();
                                                    } else if (res?.message) {
                                                        alert(res.message);
                                                        t.value = "<?php echo $text_submit; ?>";
                                                        that.dom.classList.remove(that.reply_obj.context.class_no_reply);  //enable reply
                                                    }
                                                } catch (e) {
                                                    console.debug(e);
                                                    handleComment();
                                                }
                                                // return reply
                                                that.cancelReply();
                                                // restore cf-verification(no mater success)
                                                <?php if ($cf_turnstile_wordpress) echo 'that.resetTurnstile();'; ?>
                                            }, function(err) {
                                                switch (err) {
                                                    case 409:
                                                        err = that.reply_obj.context.comment_repeat;
                                                        break;
                                                    case 429:
                                                        err = that.reply_obj.context.comment_limits;
                                                        break;
                                                    case 500:
                                                        err = that.reply_obj.context.comment_error;
                                                        break;
                                                    default:
                                                        // err;
                                                }
                                                alert(err);
                                                comment.focus();
                                                t.value = "<?php echo $text_submit; ?>";
                                                // t.classList.remove('busy');  //enable submit
                                                that.dom.classList.remove(that.reply_obj.context.class_no_reply);  //enable reply
                                                // return reply
                                                that.cancelReply();
                                                // restore cf-verification(no mater success)
                                                <?php if ($cf_turnstile_wordpress) echo 'that.resetTurnstile();'; ?>
                                            }
                                        );
                                    }, c_val);
                                })();
                            }
                    <?php
                        } else {
                    ?>
                            /**
                             * comment submit handler
                             * without ajax
                             **/
                            if (t.classList.contains('submit_btn')) {
                                e.preventDefault();  // prevent form submit..
                                // comment filter
                                that.filterComments((filter_comment, filter_exit)=> {
                                    if (filter_exit) {
                                        alert(filter_comment);
                                        return;
                                    }
                                    that.vtext.value = filter_comment;  // update filtered comment
                                    // exec submit manually
                                    that.vwrap.querySelector('form').submit();
                                });
                            }
                    <?php
                        }
                    ?>
                        }, 'onclick');
                    },
                    writable: false,
                    configurable: false,
                    enumerable: false,
                });
                
                Object.defineProperty(vcomments.init.prototype, 'verify', {
                    get() {
                        return this.dom.querySelector('#widget-container input[name=cf-turnstile-response]');
                    },
                    set(dom) {
                        this.dom = dom;
                    }
                });
                // Object.defineProperty(vcomments.init.prototype, 'box', {
                //     get() {
                //         return this.dom;
                //     },
                //     set(dom) {
                //         this.dom = dom;
                //     }
                // });
                
                const comments_init = new vcomments.init(vcomments.dom.box);
                // register events
                comments_init.dispatchEvents();
                // init canvas
                comments_init.initCanvas();
                console.log(comments_init);
            </script>
        <?php
        }
    }else{
        echo '<p class="disabled_comment">* 抱歉，由于某些原因已关闭页面评论</p>';
    }
    unset($post);
?>