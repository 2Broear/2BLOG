<?php
    global $post, $lazysrc, $src_cdn, $img_cdn;
    $post_ID = $post->ID;
    $third_cmt = get_option('site_third_comments');
    $comment_sw = $third_cmt=='Valine' ? true : false;//get_option('site_valine_switcher');
    $twikoo_sw = $third_cmt=='Twikoo' ? true : false;//get_option('site_twikoo_switcher');
    $wp_ajax_comment = get_option('site_ajax_comment_switcher');
    $wp_ajax_comment_paginate = get_option('site_ajax_comment_paginate');
    if (is_single()) {
        adscene_shortcode('adscene_list_context');
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
        if(is_user_logged_in()){
            $wp_user = get_currentuserinfo();// global $current_user;// print_r($wp_user);
            $user_name = $wp_user->user_nicename; // $_COOKIE["comment_author_" . COOKIEHASH];
            $user_mail = $wp_user->user_email; // $_COOKIE["comment_author_email_" . COOKIEHASH];
            $user_link = $wp_user->user_url; // $_COOKIE["comment_author_url_" . COOKIEHASH];
        }else{
            $user_name = array_key_exists("comment_author_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_" . COOKIEHASH] : false;
            $user_mail = array_key_exists("comment_author_email_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : false;
            $user_link = array_key_exists("comment_author_url_".COOKIEHASH, $_COOKIE) ? $_COOKIE["comment_author_url_" . COOKIEHASH] : false;
        };
        if($comment_sw){
            $welcome="既来之则留之~ 欢迎在下方留言评论，提交评论后还可以撤销或重新编辑。（Valine 会自动保存您的评论信息到浏览器）";
        }elseif($twikoo_sw){
            $welcome="既来之则留之~ 欢迎在下方留言评论";
        }else{
            $wp_login = is_user_logged_in() ? '<small> ( Logged as <a href="'.wp_login_url(get_permalink()).'" title="登出？">'.$user_name.'</a> ) </small>' : '';
            $welcome='欢迎您，'.$user_name.'！您可以在这里畅言您的的观点与见解！'.$wp_login;//
        };
        echo '<div class="main"><span id="respond"><h2> 评论留言 </h2></span><p>'.$welcome.'</p></div>';
        if(is_single()){
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
            $req = get_option( 'require_name_email' );
            $text_submit = '提交评论';
            $text_loadmore = '加载更多评论';
            $cf_turnstile = get_option('site_cloudflare_turnstile');
?>
            <div class="wp_comment_box">
                <form action="<?php echo esc_url(home_url('/')); ?>wp-comments-post.php" method="post">
                    
                	<textarea name="comment" cols="30" rows="10" placeholder="<?php $replytocom = array_key_exists('replytocom',$parameters) ? $parameters['replytocom'] : false;echo $replytocom ? '正在回复给：@'.get_comment_author($replytocom) : '畅所，你的欲言..'; ?>"></textarea>
                    <div class="userinfo">
                        <img class="avatar user" <?php echo $lazysrc.'="'.$avatar_src.'"';unset($lazysrc); ?> />
                    	<input type="text" name="nick" placeholder="昵称" value="<?php echo $user_name; ?>" />
                    	<input type="email" name="mail" placeholder="邮箱" value="<?php echo $user_mail; ?>" />
                    	<input type="url" name="url" placeholder="网址" value="<?php echo $user_link; ?>" />
                    	<div class="submit">
                    	    <input id="pushBtn" type="submit" class="submit_btn" value="<?php echo $text_submit; ?>" data-pid="<?php echo $post_ID; ?>" data-cid="0" /> <!-- onclick="return false;"-->
                            <?php echo '<a rel="nofollow" id="cancel-comment-reply-link" href="javascript:void(0);" style="display:none;">取消回复</a>';//cancel_comment_reply_link('取消回复'); ?>
                    	</div>
                    </div>
                	<?php
            	        if ($cf_turnstile) echo '<div class="cf-turnstile" data-sitekey="' . get_option('site_cloudflare_turnstile_sitekey') . '" data-language="cn" data-theme="' . theme_mode(true) . '" data-size="flexible" data-callback="onTurnstileSuccess" data-error-callback="onTurnstileError" data-expired-callback="onTurnstileExpired"></div>';
                		echo get_comment_id_fields($post_ID);
                		do_action('comment_form', $post_ID);
                	?>
                </form>
            </div>
            <?php
                $per_page = get_option('comments_per_page', 15);//15;//
                $comments = get_comments(array(
                    'post_id' => $post_ID,
                    'number'  => $per_page,
                    'orderby' => 'comment_date', //comment_ID
                    'order'   => 'DESC',
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
            <div class="wp_comments_list">
            <?php
                // if(have_comments()){ //$comment_count>=1
                // }
                // else{
                //     $prefix = is_page() ? '分类页面' : (is_single() ? '文章' : '未知');
                //     echo '<p class="wp_comment_tip">'.$prefix.' “'.get_the_title().'” <i>上暂无评论。</i></p>';
                // }
                function custom_comment($comment, $args, $depth){
                    global $lazysrc, $wp_ajax_comment;
                    $GLOBALS['comment'] = $comment; 
                    $approved = $comment->comment_approved;
            ?>
                    <div class="wp_comments" id="comment-<?php comment_ID(); ?>">
                        <div class="vh" rootid="<?php comment_ID(); ?>">
                            <div class="vhead">
                                <a rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                                    <?php 
                                        if(get_option('show_avatars')){
                                            $email = get_comment_author_email();
                                            echo '<img class="avatar" '.$lazysrc.'="'.match_mail_avatar($email).'" width=50 height=50 />';
                                            unset($lazysrc);
                                        }
                                    ?>
                                </a>
                            </div>
                            <div class="vcontent">
                                <div class="vinfo">
                                    <a rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                                        <?php
                                            comment_author();
                                            if(get_comment_author_email()==get_bloginfo('admin_email')) echo '<span class="admin">admin</span>';
                                            $userAgent = get_userAgent_info($comment->comment_agent);
                                            echo '<span class="useragent">'.$userAgent['browser'].' / '.$userAgent['system'].'</span>';
                                            if($approved=="0") echo '<span class="auditing">待审核</span>';
                                        ?>
                                    </a>
                                    <div class="vtime"><?php echo get_comment_time('Y-m-d');//date('Y-m-d', strtotime($comment->comment_date));; ?></div>
                                    <?php 
                                        if ($approved=="1") {
                                            if(get_option('site_ajax_comment_switcher')) {
                                                global $post;
                                                $comment_ID = $comment->comment_ID;
                                                $comment_author = $comment->comment_author;
                                                echo '<a rel="nofollow" class="comment-reply-link" href="javascript:void(0);" data-commentid="'.$comment_ID.'" data-postid="'.$post->ID.'" data-belowelement="comment-'.$comment_ID.'" data-respondelement="respond" data-replyto="'.$comment_author.'" aria-label="正在回复给：@'.$comment_author.'">回复</a>';
                                                unset($post);
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
                                <?php 
                                    $content = strip_tags($comment->comment_content);
                                    $parent = $comment->comment_parent;
                                    if($approved=='0') $content = '<small style="opacity:.5">[ 评论未审核，通过后显示 ]</small>';
                                    if($parent>0) $content = '<a href="#comment-'.$parent.'">@'. get_comment_author($parent) . '</a> , ' . $content;
                                    echo '<p>'.$content.'</p>'; //comment_text();
                                ?>
                            </div>
                        </div>
                    </div>
            <?php 
                };
                $wp_comment_args = array(
                	'walker'            => null,
                	'max_depth'         => '',
                	'style'             => '',
                	'callback'          => 'custom_comment',
                	'end-callback'      => null,
                	'type'              => 'all',
                	'reply_text'        => 'Reply',
                	'page'              => '',
                	'per_page'          => $per_page,
                	'avatar_size'       => 50,
                	'reverse_top_level' => null,  //set null for panel settings
                	'reverse_children'  => null
                );
                if($wp_ajax_comment){
                    if($wp_ajax_comment_paginate){
                        // print_r($comments);
                        foreach($comments as $each){
                            if($each->comment_parent!=0){
                                return;
                            }
                            wp_comments_template($each);
                            // 遍历子评论列表 https://wp-kama.com/function/WP_Comment::get_children
                            $child_comment = $each->get_children(array(
                                'hierarchical' => 'threaded',
                                // 'status'       => 'approve',
                                'order'        => 'ASC',
                                // 'orderby'=>'order_clause',
                                // 'meta_query'=>array(
                                //   'order_clause' => 'comment_parent'
                                // )
                            ));
                            if(count($child_comment)>=1){
                                echo '<ul class="children" data-cpid="'.$each->comment_ID.'">';
                                wp_child_comments_loop($each);
                                echo '</ul>';
                            }
                            // echo '</div>';
                        }
                    }else{
                        wp_list_comments($wp_comment_args);
                    }
                }else{
                    wp_list_comments($wp_comment_args);
                }
            ?>
            </div>
            <?php
                if ($wp_ajax_comment) {
            ?>
                <script>
            	    const comments = {
                        info: document.querySelector('.wp_comment_box form .userinfo'),
                        init: function(fields){
                            this.fields = fields;
                        },
                        // test: ()=>console.log(this)
                    };
                    Object.defineProperty(comments.init.prototype, 'realtime_avatar', {
                        value: function() {
                            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                  email = this.fields.querySelector('input[type=email]'),
                                  avatar = this.fields.querySelector('img.avatar');
                            if(!email || !avatar){
                                this.realtime_fields = comments.info;
                                throw new Error('email-field not exist, fallback to preset node..');
                            };
                            email.onchange = function(e){
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
                        },
                        writable: false,
                        configurable: false,
                        enumerable: false,
                    });
                    Object.defineProperty(comments.init.prototype, 'realtime_fields', {
                        get(){
                            return this.fields;
                        },
                        set(field){
                            this.fields = field;
                        }
                    });
                    const comments_init = new comments.init(comments.info);
                    comments_init.realtime_avatar();
                    //..
                    const comment_box = document.querySelector(".wp_comment_box"),
                          comment_form = comment_box.querySelector("form"),
                          placeholder = comment_box.querySelector("textarea").placeholder,
                          comment = comment_form.querySelector("textarea[name=comment]"),
                          author = comment_form.querySelector("input[name=nick]"),
                          email = comment_form.querySelector("input[name=mail]"),
                          admin_md5mail = "<?php echo md5(get_option('site_smtp_mail', get_bloginfo('admin_email'))); ?>", //preset for wp_comment
                          url = comment_form.querySelector("input[name=url]"),
                          comment_count = document.querySelector(".wp_comment_count"),
                          comment_list = document.querySelector(".wp_comments_list"),
                          comment_parnode = comment_list.parentNode,
                          required_fields = <?php echo $req; ?>,
                          //cururl = window.location.origin+window.location.pathname,
                          loadDone = "已加载全部评论",
                          focusCls = 'err',
                          disRepCls = 'disabled_reply',
                          class_switcher = function(els, cls, disabled=true){
                              if(!els) return;
                              for(let i=0,eLen=els.length;eLen>i;i++){
                                  disabled ? els[i].classList.add(cls) : els[i].classList.remove(cls);
                              }
                          };
                    //*** Submit comments logic ***//
                    bindEventClick(comment_box, 'submit_btn', function(t, e){
                        e.preventDefault();  // prevent form submit
                        let a_val = author.value,
                            e_val = email.value,
                            c_val = comment.value,
                            comment_pid = t.dataset.cid,
                            comment_cid = t.dataset.pid;
                        if(required_fields){
                            const e_reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/,
                                  t_fuc = function(str){
                                      let trim = str.replace(/^\s+|\s+$/g,"").replace( /^\s*/, '');
                                      return trim ? trim : false;
                                  },
                                  c_err = function(el,msg){
                                      el.setAttribute('placeholder', msg)
                                      el.focus();
                                      el.classList.add(focusCls);
                                      el.oninput=function(){
                                          this.value ? this.classList.remove(focusCls) : this.oninput=null;
                                      };
                                  };
                            if(!t_fuc(c_val)) c_err(comment, '评论不能为空！');
                            if(!t_fuc(a_val)) c_err(author, '昵称填写有误！');
                            if(!e_reg.test(e_val)) c_err(email, '邮箱格式有误！');
                            if(!t_fuc(c_val) || !t_fuc(a_val) || !e_reg.test(e_val)){
                                return;
                            }
                        }
                        t.value = "提交中..";
                        // t.classList.add('busy');  //disable submit
                        comment_parnode.classList.add(disRepCls);  //disable reply
                        send_ajax_request("post", "<?php echo esc_url(home_url('/')); ?>wp-comments-post.php", 
                            parse_ajax_parameter({
                                "comment": c_val,
                                "author": a_val,
                                "email": e_val,
                                "url": url.value,
                                "comment_post_ID": comment_cid,
                                "comment_parent": comment_pid,
                                "cf-turnstile-response": t.dataset.token,
                                // _ajax_nonce: t.dataset.nonce,
                            }, true), function(res) {
                                const handleComment = function() {
                                    let temp_date = new Date(),
                                        temp_comment = document.createElement("div"),
                                        comment_replyto = t.dataset.replyto ? '<a href="#comment-'+comment_pid+'">@'+t.dataset.replyto+'</a> , ' : '',
                                        comment_info = '<span class="auditing"> Auditing / Previews </span>';
                                    if(a_val=="<?php echo $user_name; ?>"&&e_val=="<?php echo $user_mail; ?>"){
                                        comment_info = admin_md5mail=="<?php echo md5($user_mail); ?>" ? '<span class="admin">admin</span><span class="useragent"> Comments Preview </span>' : '<span class="useragent"> Comments / Preview </span>';
                                    }
                                    temp_comment.classList.add("wp_comments");
                                    temp_comment.innerHTML = `<div class="vh" rootid=""><div class="vhead"><a rel="nofollow" href="" target="_blank"><img class="avatar" width="50" height="50" src="${comment_box.querySelector('img.avatar').src}"></a></div><div class="vcontent" style="margin-left:5px"><div class="vinfo"><a rel="nofollow" href="" target="_blank">${a_val}</a>${comment_info}<div class="vtime">${temp_date.toLocaleDateString()}</div></div><p>${comment_replyto} ${c_val}</p></div></div>`; //<small style="opacity:.5">[ 评论未审核，通过后显示 ]</small>   ${temp_date.toLocaleTimeString()}
                                    const inside_child_reply = getParByCls(t, 'children'),
                                          check_child_reply = getParByCls(t, 'wp_comments');
                                    if(inside_child_reply){
                                        inside_child_reply.appendChild(temp_comment);  // inside loaded-children list reply
                                    }else{
                                        if(check_child_reply){
                                            let outside_child_list = check_child_reply.nextElementSibling;
                                            if(outside_child_list && outside_child_list.classList.contains('children')){
                                                outside_child_list.appendChild(temp_comment);  // child-list exist (case: child-list next to wp_comments)
                                            }else{
                                                // create children list (case: new reply to parent comment)
                                                let wrap_ul = document.createElement("ul");
                                                wrap_ul.classList.add("children");
                                                wrap_ul.appendChild(temp_comment);
                                                comment_list.insertBefore(wrap_ul, check_child_reply.nextElementSibling);  // (insert next to wp_comments)
                                                wrap_ul = null;  //clear memory
                                            }
                                        }else{
                                            // direct insert child-comment
                                            comment_list.insertBefore(temp_comment, comment_list.firstElementChild);
                                            //update comment_count at level-0 submit
                                            comment_count.innerHTML = '<strong id="count">'+(parseInt(comment_count.innerText)+1)+'</strong> 条评论';
                                        }
                                    }
                                    temp_comment = null;  //clear memory
                                    t.value = "<?php echo $text_submit; ?>";
                                    comment.value = "";
                                    // t.classList.remove('busy');  //enable submit
                                    comment_parnode.classList.remove(disRepCls);  //enable reply
                                }
                                try {
                                    res = JSON.parse(res);
                                    if (res?.success) {
                                        handleComment();
                                    } else if (res?.message) {
                                        alert(res.message);
                                        t.value = "<?php echo $text_submit; ?>";
                                        comment_parnode.classList.remove(disRepCls);  //enable reply
                                    }
                                } catch (e) {
                                    handleComment();
                                }
                            }, function(err){
                                switch (err) {
                                    case 409:
                                        err = "检测到重复评论，您似乎已经提交过这条评论了！";
                                        break;
                                    case 429:
                                        err = "您提交评论的速度太快了，请稍后再发表评论。";
                                        break;
                                    case 500:
                                        err = "服务器错误。";
                                        break;
                                    default:
                                        // err;
                                }
                                alert(err);
                                comment.focus();
                                t.value = "<?php echo $text_submit; ?>";
                                // t.classList.remove('busy');  //enable submit
                                comment_parnode.classList.remove(disRepCls);  //enable reply
                            });
                    });
                    //*** Reply comments logic ***//
                    comment_list.onclick=(e)=>{
                        e = e || window.event;
                        let t = e.target || e.srcElement;
                        if(!t) return;
                        while(t!=comment_list){
                            if(t.classList && t.classList.contains("comment-reply-link")){
                                if(t.classList.contains('replying')) return;
                                    class_switcher(comment_list.querySelectorAll(".comment-reply-link"), 'replying', false);  //enable(all-reply) adopt 
                                    t.classList.add('replying');  //disable(current-reply) adopt
                                    let adopt_node = document.adoptNode(comment_box),
                                        adopt_area = adopt_node.querySelector("textarea[name=comment]"),
                                        adopt_submit = adopt_node.querySelector("input[type=submit]"),
                                        remains_node = document.querySelector(".wp_comment_box");
                                    // detect if adopt_node remains before manual-load-comments (adoptNode cache)
                                    if(remains_node) remains_node.remove();
                                    getParByCls(t, "vcontent").appendChild(adopt_node);  // append adopt node
                                    adopt_area.placeholder = "正在回复给：@"+t.dataset.replyto;
                                    adopt_area.focus();
                                    class_switcher(adopt_node.querySelectorAll('.'+focusCls), focusCls, false); // adopt_area.classList.remove(focusCls);
                                    adopt_submit.dataset.cid = t.dataset.commentid;
                                    adopt_submit.dataset.replyto = t.dataset.replyto;
                                break;
                            }else if(t.id && t.id=="cancel-comment-reply-link"){
                                class_switcher(comment_list.querySelectorAll(".comment-reply-link"), 'replying', false);  //enable(all-reply) adopt 
                                let adopt_node = document.adoptNode(comment_box),
                                    adopt_area = adopt_node.querySelector("textarea[name=comment]"),
                                    adopt_submit = adopt_node.querySelector("input[type=submit]"),
                                    remains_node = document.querySelector(".wp_comment_box");
                                // detect if adopt_node remains before manual-load-comments (adoptNode cache)
                                if(remains_node) remains_node.remove();
                                comment_list.parentNode.insertBefore(adopt_node, comment_count);  // reverse adopt
                                adopt_area.placeholder = placeholder;
                                // adopt_area.focus();
                                class_switcher(adopt_node.querySelectorAll('.'+focusCls), focusCls, false); // adopt_area.classList.remove(focusCls);
                                adopt_submit.dataset.cid = 0;
                                adopt_submit.removeAttribute('data-replyto');
                                break;
                            }else{
                                t = t.parentNode;
                            }
                        }
                    }
                </script>
            <?php
                }
                if (have_comments()) { //$comment_count>=1
            ?>
                    <nav class="pageSwitcher dev">
                        <?php 
                            if($wp_ajax_comment_paginate) {
                                $load_class = 'loadmore';
                                if($comment_count===$comments_all){
                                    $load_class = 'loadmore disabled';
                                    $text_loadmore = '没有更多评论';
                                }
                                echo '<a href="javascript:;" class="'.$load_class.'" data-click="0" data-load="'.$comment_count.'" data-counts="'.$comments_all.'" data-nonce="'.wp_create_nonce($post_ID."_comment_ajax_nonce").'">'.$text_loadmore.'</a>';
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
                                echo get_previous_comments_html("PREV COMMENTS");
                                echo get_next_comments_html("NEXT COMMENTS");
                            }
                        ?>
                    </nav>
                    <script>
                        const avatar_cdn = "<?php echo get_option('site_avatar_mirror'); ?>",
                              strip_tags = function(str){ 
                                  return str.replace(/<\/?[^>]*>/g,'');
                              },
                              childCommentsLoop = function(childs){
                                  let output = "";
                                  if(childs){
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
                                              is_admin = email==admin_md5mail ? '<span class="admin">admin</span>' : '',
                                              is_approved = approve=="0" ? '<span class="auditing">待审核</span>' : '',
                                              replytocom = approve=="1" ? `<a rel="nofollow" class="comment-reply-link" href="javascript:void(0);" data-commentid="${id}" data-postid="<?php echo $post_ID; ?>" data-belowelement="comment-${id}" data-respondelement="respond" data-replyto="${nick}" aria-label="正在回复给：@${nick}">回复</a>` : "";
                                          if(approve=="0") content = '<small style="opacity:.5">[ '+content+' ]</small>'; //${cururl}?replytocom=${id}#respond
                                          // track-back (childCommentsLoop insert after output)
                                          output += `<div class="wp_comments"id="comment-${id}"><div class="vh"rootid="${parent}"><div class="vhead"><a rel="nofollow"href="${link}"target="_blank"><img class="avatar"src="${avatar_cdn+'avatar/'+email}?s=50" width=50 height=50/></a></div><div class="vcontent"><div class="vinfo"><a rel="nofollow"href="${link}"target="_blank">${nick}</a>${is_admin}<span class="useragent">${user_agent.browser+" / "+user_agent.system}</span>${is_approved}<div class="vtime">${child.comment_date}</div>${replytocom}</div><p><a href="#comment-${parent}">@${child._comment_reply}</a> , ${content}</p></div></div></div>` + childCommentsLoop(child._comment_childs);
                                       }
                                  }
                                  return output;
                              };
                        //*** Load comments logic ***//
                        const page_switcher = document.querySelector(".pageSwitcher"),
                              comment_loads = <?php echo $per_page; ?>;
                        bindEventClick(page_switcher, 'loadmore', function(t){
                            let years = t.dataset.year,
                                loads = parseInt(t.dataset.load),
                                counts = parseInt(t.dataset.counts),
                                clicks = parseInt(t.dataset.click);
                            if(loads>=counts){
                                t.classList.add("disabled");
                                t.innerText = loadDone;
                                return;
                            }
                            clicks++;
                            t.innerText="加载中..";
                            t.classList.add('loading','disabled');
                            t.setAttribute('data-click', clicks);
                            send_ajax_request("post", "<?php echo admin_url('admin-ajax.php'); ?>", parse_ajax_parameter({
                                    "action": "ajaxLoadComments",
                                    "pid": <?php echo $post_ID; ?>, 
                                    "limit": comment_loads,
                                    "offset": comment_loads*clicks,
                                    _ajax_nonce: t.dataset.nonce,
                                }, true), function(res){
                                    var posts_array = JSON.parse(res),
                                        posts_count = posts_array.length,
                                        loads_count = loads+posts_count;
                                    t.innerText = "加载更多评论";
                                    t.classList.remove('disabled','loading');
                                    loads_count>=counts ? t.setAttribute('data-load', counts) :  t.setAttribute('data-load', loads_count);  // update current loaded(limit judge)
                                    for(let i=0;i<posts_count;i++){
                                        let each_post = posts_array[i],
                                            id = each_post.comment_ID,
                                            nick = each_post.comment_author,
                                            link = each_post.comment_author_url,
                                            md5mail = each_post.comment_author_email,
                                            childs = each_post._comment_childs,
                                            user_agent = each_post._comment_agent,
                                            content = each_post.comment_content,//strip_tags(each_post.comment_content),
                                            approve = each_post.comment_approved,
                                            if_child = childs ? '<ul class="children" data-cpid="'+id+'">'+childCommentsLoop(childs)+'</ul>' : '',
                                            is_admin = md5mail==admin_md5mail ? '<span class="admin">admin</span>' : '',
                                            is_approved = approve=='0' ? '<span class="auditing">待审核</span>' : '',
                                            replytocom = approve=='1' ? `<a rel="nofollow" class="comment-reply-link" href="javascript:void(0);" data-commentid="${id}" data-postid="<?php echo $post_ID; ?>" data-belowelement="comment-${id}" data-respondelement="respond" data-replyto="${nick}" aria-label="正在回复给：@${nick}">回复</a>` : "";
                                        if(approve=="0") content = '<small style="opacity:.5">[ '+content+' ]</small>'; //${cururl}?replytocom=${id}#respond
                                        var appendList = document.createElement("div");
                                        // DO NOT use comment_list.innerHTML, innerHTML will refresh dom list (caused: binded event lose efficacy)
                                        appendList.id = "comment-"+id;
                                        appendList.classList.add("wp_comments");
                                        appendList.innerHTML += `<div class="vh" rootid="${each_post.comment_parent}"><div class="vhead"><a rel="nofollow" href="${link}" target="_blank"><img class="avatar" src="${avatar_cdn+'avatar/'+md5mail}?s=50" width=50 height=50 /></a></div><div class="vcontent"><div class="vinfo"><a rel="nofollow" href="${link}" target="_blank">${nick}${is_admin}</a><span class="useragent">${user_agent.browser+" / "+user_agent.system}</span>${is_approved}<div class="vtime">${each_post.comment_date}</div>${replytocom}</div><p>${content}</p></div></div>` + if_child; //<div class="wp_comments" id="comment-${id}"></div>
                                        // const comment_list;
                                        document.querySelector(".wp_comments_list").appendChild(appendList);
                                    }
                                    // compare updated load counts
                                    if(parseInt(t.dataset.load)>=counts){
                                        t.innerText = loadDone;
                                        t.classList.add("disabled");
                                    }
                                }
                            );
                        });
                    </script>
            <?php
                }
        }
    }else{
        echo '<p class="disabled_comment">* 抱歉，由于某些原因已关闭页面评论</p>';
    }
    unset($post);
?>
