<?php
    $third_cmt = get_option('site_third_comments');
    $comment_sw = $third_cmt=='Valine' ? true : false;//get_option('site_valine_switcher');
    $twikoo_sw = $third_cmt=='Twikoo' ? true : false;//get_option('site_twikoo_switcher');
    if(is_single()){
?>
        <div class="share" style="<?php if(!$comment_sw) echo 'margin-top:15px'; ?>">
            <a id="dislike" title="有点东西（Like）" href="javascript:;" data-action="like" data-id="<?php the_ID(); ?>" class="<?php if(isset($_COOKIE['post_liked_'.$post->ID])) echo 'liked';?>" <?php if(!$comment_sw) echo 'onclick="postLike(this)"'; ?>>
                <?php if($comment_sw) echo '<div class="user"><small></small><div id="list"></div></div>'; ?>
                <span id="like" class="count">
                    <i id="counter"><?php $like=get_post_meta($post->ID,'post_liked',true);if($like) echo $like;else echo '0'; ?></i>
                    <em style="background:url(<?php custom_cdn_src('img'); ?>/images/shareico.png) no-repeat -478px 4px"></em>
                </span>
            </a>
            <a id="qq" title="分享QQ" href="https://connect.qq.com/widget/shareqq/index.html?<?php echo $para_str = 'url='.get_permalink().'&p='.custom_excerpt(50,true).'&title='.get_the_title().'&summary='.custom_excerpt(100,true).'&pics='.get_postimg(); ?>" target="_blank"><span><em style="background:url(<?php custom_cdn_src('img'); ?>/images/shareico.png) no-repeat -9px 4px"></em></span></a>
            <a id="qzone" title="分享空间（QZone）" href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?<?php echo $para_str; ?>" target="_blank"><span><em style="background:url(<?php custom_cdn_src('img'); ?>/images/shareico.png) no-repeat -88px 4px"></em></span></a>
            <a id="Poster" title="图文海报（Poster）"><span id="recall" onclick="ajaxPoster()"><em style="background:url(<?php custom_cdn_src('img'); ?>/images/shareico.png) no-repeat -245px 4px"></em></span></a>
        </div>
        <!--<script type="text/javascript" src="<?php //custom_cdn_src("src"); ?>/js/jquery-1.9.1.min.js"></script>-->
        <script>
            function send_ajax_request(method,url,data,callback){
                var ajax = new XMLHttpRequest();
                if(method=='get'){  // GET请求
                    data ? (url+='?',url+=data) : false;
                    ajax.open(method,url,true);
                    ajax.send();
                }else{  // 非GET请求
                    ajax.open(method,url,true);
                    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");  // https://www.cnblogs.com/dangdanghepingping/p/10167135.html
                    data ? ajax.send(data) : ajax.send();
                }
                ajax.onreadystatechange = function () {
                    if(ajax.readyState==4 && ajax.status==200){
                        callback ? callback(ajax.responseText) : false;
                    }else{
                        // error ? error(ajax.responseText) : false;
                    }
                };
            }
            function poster_sw(){
                const poster = document.querySelector(".poster");
                poster.classList.contains('active') ? poster.classList.remove('active') : poster.classList.add('active');
            }
            function ajaxPoster(){
                if(!document.querySelector("#capture")){
                    var div = document.createElement('DIV');
                    send_ajax_request("get", "<?php custom_cdn_src(false); ?>/plugin/html2canvas.php",
                        'pid=<?php echo $post->ID ?>', 
                        function(res){
        					div.innerHTML += res;  //在valine环境直接追加到body会导致点赞元素层级错误
        					document.body.appendChild(div);
        					// generate poster QRCode
                        	dynamicLoad('<?php custom_cdn_src(); ?>/js/qrcode/qrcode.min.js',function(){
                        		let url = location.href;
                        		var qrcode = new QRCode(document.getElementById("qrcode"), {
                        			text: url,
                        			width: 100,
                        			height: 100,
                        			colorDark : "#000000",
                        			colorLight : "#ffffff",
                        			correctLevel : QRCode.CorrectLevel.L
                        		});
                        		// html2canvas CAUSED too many requests.
                        		dynamicLoad('<?php custom_cdn_src(); ?>/js/html2canvas/html2canvas.min.js',function(){
                        		    // delay 300ms wait for QRCode generated incase qrcode not-fit
                        		    var delay_h2c = setTimeout(function(){
                                		html2canvas(document.querySelector('#capture'),{
                                		    useCORS: true,
                                		    allowTaint: true,
                                		    scrollX: 0,
                                		    scrollY: 0,
                                		    backgroundColor: null
                                	    }).then(canvas => {
                                			let baseUrl = canvas.toDataURL("image/png"),
                                				newImg = document.createElement("img"),
                                				imgDom = '<img src="'+baseUrl+'" />';
                                			newImg.src = baseUrl;
                                			document.getElementById('poster').innerHTML+=imgDom
                                		});
                                		clearTimeout(delay_h2c);
                                        delay_h2c = null;  //消除定时器表示激活
                                    }, 300);
                        		});
                        	});
                        }
                    )
                }else{
                    poster_sw();
                }
            };
        </script>
<?php
    }
    global $post,$posts;
    if($post->comment_status=="open" || is_category()){
        if(is_user_logged_in()){
            $wp_user = get_currentuserinfo();// global $current_user;// print_r($wp_user);
            $user_name = $wp_user->user_nicename; // $_COOKIE["comment_author_" . COOKIEHASH];
            $user_mail = $wp_user->user_email; // $_COOKIE["comment_author_email_" . COOKIEHASH];
            $user_link = $wp_user->user_url; // $_COOKIE["comment_author_url_" . COOKIEHASH];
        }else{
            $user_name = $_COOKIE["comment_author_" . COOKIEHASH];
            $user_mail = $_COOKIE["comment_author_email_" . COOKIEHASH];
            $user_link = $_COOKIE["comment_author_url_" . COOKIEHASH];
        };
        if($comment_sw){
            $welcome="既来之则留之，欢迎在下方留言评论。提交评论后还可以撤销或重新编辑，未发布的留言会被储存在本地以供下次继续编辑（Valine 会自动保存您的评论信息到浏览器）";
        }elseif($twikoo_sw){
            $welcome="既来之则留之，欢迎在下方留言评论";
        }else{
            $wp_login = is_user_logged_in() ? '<small> ( Logged as <a href="'.wp_login_url(get_permalink()).'" title="登出？">'.$user_name.'</a> ) </small>' : '';
            $welcome='欢迎您，'.$user_name.'！您可以在这里畅言您的的观点与见解！'.$wp_login;//
        }
        echo '<div class="main"><span><h2> 评论留言 </h2></span><p>'.$welcome.'</p></div>';
        if($comment_sw){
            echo '<div id="vcomments" class="v"></div>';
        }elseif($twikoo_sw){
            echo '<div id="tcomment"></div>';
        }else{
            if(is_single()){
?>
            <script type="text/javascript">
                function postLike(t){
                    let _this = t;
                    // console.log(_this);
                    if(_this.classList.contains('liked')){
                        alert("您已经点过赞了!");
                        return false;
                    }else{
                        _this.classList.add('liked');
                        var id = _this.getAttribute('data-id'),
                            action =_this.getAttribute('data-action'),
                            rateHolder = document.querySelector('.count #counter');
                        var ajax_data = {
                                action: "post_like",
                                um_id: parseInt(id),
                                um_action: action
                            };
                        console.log(ajax_data);
                        send_ajax_request("get", "/wp-admin/admin-ajax.php", "action=post_like&um_id="+id+"&um_action="+action, function(res){
                                console.log(res);
                                rateHolder.innerText = res;
                        })
                        // var form_data = 'action=post_like&um_id='+id+'&um_action='+action;
                        // send_ajax_request("post", "/wp-admin/admin-ajax.php", form_data, function(res){
                        //         console.log(res);
                        //         rateHolder.innerText = res;
                        //     }
                        // );
                        return false;
                    }
                };
            </script>
<?php 
            };
            $avatar_src = match_mail_avatar($user_mail);
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            $replytocom = $parameters['replytocom'];
?>
            <div class="wp_comment_box">
                <form action="<?php echo esc_url(home_url('/')); ?>wp-comments-post.php" method="post">
                    
                	<textarea name="comment" cols="30" rows="10" placeholder="<?php if($replytocom) echo '正在回复给：@'.get_comment_author($replytocom);else echo '畅所，你的欲言..'; ?>"></textarea>
                    <div class="userinfo">
                        <img class="avatar" src="<?php echo $avatar_src; ?>" />
                    	<input type="text" name="author" placeholder="昵称" value="<?php echo $user_name; ?>" />
                    	<input type="email" name="email" placeholder="邮箱" value="<?php echo $user_mail; ?>" />
                    	<input type="url" name="url" placeholder="网址" value="<?php echo $user_link; ?>" />
                    	<div class="submit">
                    	    <input type="submit" value="提交评论" />
                            <?php cancel_comment_reply_link('取消回复'); ?>
                    	</div>
                    </div>
                	<?php
                		echo get_comment_id_fields( $post->id );
                		do_action( 'comment_form', $post->id );
                	?>
                </form>
            </div>
    <?php
                $comments = get_comments(array(
                    'post_id' => $post->ID,
                    'orderby' => 'comment_date',
                    'order' => 'DESC',
                    'status' => 'approve'  //仅输出已通过审核的评论数量
                ));
            $comment_count = count($comments);
            if($comment_count>=1){
?>
                <div class="wp_comment_count">
                    <?php echo '<strong id="count">'.$comment_count.'</strong> 条评论'; ?>
                </div>            
                <div class="wp_comments_list">
                    <?php function custom_comment($comment, $args, $depth){
                        $GLOBALS['comment'] = $comment; 
                        $admin_mail = get_bloginfo('admin_email');
                        $approved = $comment->comment_approved;
                        // $user_agent = get_user_agent($comment->comment_agent);
                        $userAgent = get_userAgent_info($comment->comment_agent);
                    ?>
                        <div class="wp_comments" id="comment-<?php comment_ID(); ?>">
                            <div class="vh" rootid="<?php comment_author_email(); ?>">
                                <div class="vhead">
                                    <a rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                                        <?php 
                                            if(get_option('show_avatars')){
                                                $email = get_comment_author_email();
                                                echo '<img class="avatar" src="'.match_mail_avatar($email).'" width=50 height=50 />';
                                            }
                                        ?>
                                    </a>
                                </div>
                                <div class="vcontent">
                                    <div class="vinfo">
                                        <a rel="nofollow" href="<?php comment_author_url(); ?>" target="_blank">
                                            <b><?php comment_author(); ?></b>
                                        <?php
                                            if(get_comment_author_email()==$admin_mail) echo '<span class="admin">admin</span>';
                                            echo '<span class="useragent">'.$userAgent['browser'].' / '.$userAgent['system'].'</span>';
                                            if($approved=="0") echo '<span class="auditing">Reviewing</span>';
                                        ?>
                                        </a>
                                        <span class="vtime"><?php echo get_comment_time('Y-m-d'); //get_comment_time('Y-m-d H:i');  ?></span>
                                        <?php 
                                            comment_reply_link(
                                                array_merge(
                                                    $args, array(
                                                        'reply_text' => '回复',
                                                        'depth' => $depth, 
                                                        'max_depth' => $args['max_depth']
                                                    )
                                                )
                                            );
                                        ?>
                                    </div>
                                    <?php 
                                        if($approved=='0') echo '<p style="opacity:.5">[ 您的评论正在审核中，通过后即可显示！]</p>';
                                        comment_text();
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        };
                        $per_page = get_option('comments_per_page',15);
                        $comment_args = array(
                        	'walker'            => null,
                        	'max_depth'         => '',
                        	'style'             => '',
                        	'callback'          => 'custom_comment',
                        	'end-callback'      => null,
                        	'type'              => 'all',
                        	'reply_text'        => 'Reply',
                        	'page'              => '',
                        	'per_page'          => $per_page,  //
                        	'avatar_size'       => 50,
                        	'reverse_top_level' => null,  //set null for panel settings
                        	//'reverse_children'  => ''
                        );
                        wp_list_comments($comment_args);
                        if($comment_count>$per_page){
                    ?>
                            <nav class="pageSwitcher dev">
                                <?php echo get_previous_comments_html("PREV COMMENTS");//echo '<a href="'.get_previous_comments_url().'#comments"><i class="icom"></i>PREV COMMENTS</a>'; ?>
                                <?php echo get_next_comments_html("NEXT COMMENTS");//echo '<a href="'.get_next_comments_url().'">NEXT COMMENTS<i class="icom left"></i></a>'; ?>
                            </nav>
                    <?php
                        }
                    ?>
            </div>  <!-- 无论列表是否存在评论都需要将判断内html包裹（bug：嵌套顺序混乱） -->
<?php
            }else{
                if(is_page()) $prefix="分类页面";elseif(is_single()) $prefix="文章";else $prefix="未知";
                echo '<p class="wp_comment_tip">'.$prefix.' “'.get_the_title().'” <i>上暂无评论。</i></p>';
            }
        }
    }else{
        echo '<p class="disabled_comment">* 抱歉，由于某些原因已关闭页面评论</p>';
    }
?>
