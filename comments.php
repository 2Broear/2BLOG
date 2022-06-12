
<?php
    $comment_sw = get_option('site_comment_switcher');
    if(is_single()){
?>
        <div class="share" style="<?php if(!$comment_sw) echo 'margin-top:15px'; ?>">
            <a id="dislike" title="有点东西（Like）" href="javascript:;" data-action="like" data-id="<?php the_ID(); ?>" class="<?php if(isset($_COOKIE['post_liked_'.$post->ID])) echo 'liked';?>">
                <span id="like" class="count">
                    <em id="counter"><?php $like=get_post_meta($post->ID,'post_liked',true);if($like) echo $like;else echo '0'; ?></em>
                </span>
                <?php if($comment_sw) echo '<div class="user"><small></small><div id="list"></div></div>'; ?>
            </a>
            <a id="qq" title="分享QQ"><span></span></a>
            <a id="qzone" title="分享空间（QZone）"><span></span></a>
            <a id="Poster" title="生成海报（Poster）"><span id="recall"></span></a>
        </div>
        <script type="text/javascript" src="<?php custom_cdn_src("src"); ?>/js/jquery-1.9.1.min.js"></script>
        <script>
            var api = {
        			"qq": "https://connect.qq.com/widget/shareqq/index.html",
        			"qzone": "https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey",
        			"weibo": "https://service.weibo.com/share/share.php"
        		},
        		d = document,
        		eID = (e,d)=>{
        			let el = d.getElementById(e);
        			return el
        		},
        		eTG = (e,d)=>{
        			let el = d.getElementsByTagName(e)[0];
        			return el
        		},
        		eCS = (e,d)=>{
        			let el = document.getElementsByClassName(e)[0];
        			return el
        		},
        		eHide = (e)=>{e.style.display="none"},
        		eShow = (e)=>{e.style.display="block"},
        		sQQ=(u,p,t,s,i)=>{
        			i.match("http") ? i=i : i="https:"+i;
        			return api.qq+"?url="+u+"?p="+p+"&title="+t+"&summary="+s+"&pics="+i
        		},
        		sQZone=(u,p,t,s,i)=>{
        			i.match("http") ? i=i : i="https:"+i;
        			return api.qzone+"?url="+u+"?p="+p+"&title="+t+"&summary="+s+"&pics="+i
        		},
        		sWeibo=(u,p,t,i)=>{
        			i.match("http") ? i=i : i="https:"+i;
        			return api.weibo+"?url="+u+"?p="+p+"&title="+t+"&pic="+i+"&searchPic=true"
        		},
        		vaildImgAsync = (imgurl)=>{
        			return new Promise(function(resolve, reject){
        				var img = new Image();
        				img.src = imgurl;
        				img.onload = function(res){
        					resolve(res);
        				};
        				img.onerror = function(err){
        					reject(err)
        				}
        			})
        		},
        		vaildImgCheck = (imgurl)=>{
        			vaildImgAsync(imgurl).then(()=>{
        				return true
        			}).catch(()=>{
        				return false
        			})
        		},
        		ifLazy = (img,imgs,set)=>{
        			img==undefined?set=imgs:false;
        			imgs==undefined?set=img:false;
        		};
        	if($(".share").is(":visible")){
        		var url = location.href+"/",
        			title = '<?php echo get_the_title(); ?>', //document.title.replace(" | 2BROEAR","").replace(" 笔记栈",""),
        			content = $(".news-article-container p").first().text(),
        			img_false = "<?php custom_cdn_src('img'); ?>/images/default.jpg",
        			img_true;
        		if($(".win-top").is(":visible")){
        			var img = $(".win-top").attr("style"),
        				img2bg = img ? img.split("(")[1].split(")")[0].replaceAll("'","") : img_false,
        				img_true = img2bg;
        		}else{
        			var img_news = $("img").first().attr("data-original"),
        				img_news_ = $("img").first().attr("src"),
        				img_true = img_news;
        		}
        		ifLazy(img_news,img_news_,img_true)
        		img_true==undefined ? img_true = img_false : false;
        		img_true.match("<?php custom_cdn_src(); ?>/emojis") ? img_true=img_false : false;
        		content.length>32?content=content.slice(0,32)+'...':false;
        		var shareQQ = sQQ(url,"parameter",title,content,img_true||img_false),
        			shareQZone = sQZone(url,"parameter",title,content,img_true||img_false),
        			shareWeibo = sWeibo(url,"parameter",title,img_true||img_false),
        			sharelist = {
        				"#qq": shareQQ,
        				"#qzone": shareQZone,
        				"#weibo": shareWeibo 
        			};
        		for(key in sharelist){
        			let url = sharelist[key];
        			$(key).on("click",function(){
        				window.open(url, 'sharelist', 'height=600, width=800');
        			})
        		}
        	};
            function callAjax(){
        		if(!$('#capture').is(':visible')){
        			$.ajax({
        				url: "<?php custom_cdn_src(); ?>/plugin/html2canvas.php",
        				type:'get',
        				async: 'false',
        				dataType: '',
        				success: function(result){
        					$('body').append(result);
        				},error: function(xhr){
        					alert("Error: "+xhr.status+", "+xhr.statusText);
        				},complete: function(){
        					var div = document.createElement("DIV"),
        						title = eTG('h1',d).innerText,
        						container = eCS('news-article-container',d),
        						content = eTG('p',container).innerText,
        						img_false = "<?php custom_cdn_src('img'); ?>/images/default.jpg",
        						img_true;
        					if(eCS('win-top',d)!=undefined)
        						var img = eCS('win-top',d).getAttribute('style'),
        							date = eID('date',d).innerText,
        							tag = eID('classify',d).innerText,
        							img2bg = img ? img.split("(")[1].split(")")[0].replaceAll("'","") : img_false,
        							img_true = img2bg;
        					else
        						var img_news = eTG('img',d).getAttribute("data-original"),
        							img_news_ = eTG('img',d).getAttribute("src"),
        							date_news = eID("post-date",d).innerText,
        							img_true = img_news;
        					img_true==undefined?img_true=img_news_:false;
        					img_true.match("<?php custom_cdn_src(); ?>/emojis") ? img_true=img_false : false;
        					content.length>32?content=content.slice(0,32)+'...':false;
                            //${img_true||img_false} ${content}
        					div.innerHTML=`<div id="capture"><header><em style="background:url(<?php echo get_postimg(); ?>) center center /cover"></em></header><aside><h3>${title}</h3><?php the_excerpt(); ?><small><span contenteditable="true">${tag||"Posted in"}</span>${date||date_news}</small><span id="qrcode"></span></aside><footer><b> SHARING VIA <?php echo get_option('site_nick'); ?> </b></footer></div><div id="html2img"><div id="html2canvas"><div id="loadbox"><img id="loading" src="<?php custom_cdn_src('img'); ?>/images/loading_3_color_tp.png" /><h3> 正在生成海报，请等待.. </h3><span id="cancel" onclick="hide()"></span><span id="poster"></span></div></div></div><div id="mask"></div>`;
        					document.body.appendChild(div);
        					var html2img = eID("html2img",d),
        						mask = eID("mask",d);
        					eHide(html2img);  //hide blured// clear load-flash-0
        					var delay = setTimeout(function(){
        						eShow(html2img);  //show clear// clear load-flash-1
        						let capture=eID("capture",d),
        							loadbox=eID("loadbox",d),
        							clostBtn = eID('cancel',d),
        							openBtn = eID('recall',d);
        						loadbox.setAttribute("style","width:"+capture.clientWidth+"px;height:"+capture.clientHeight+"px");
        						openBtn.onclick=()=>{eShow(html2img);eShow(mask)};
        						clostBtn.onclick=()=>{eHide(html2img);eHide(mask)};
        						clearTimeout(delay)
        					},100);
        				}
        			})
        		}
        	};
        	$(".content-all").on("click",".share span#recall",function(){
        		callAjax()
        	})
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
        }else{
            $wp_login = is_user_logged_in() ? '<small> ( Logged as <a href="'.wp_login_url(get_permalink()).'" title="登出？">'.$user_name.'</a> ) </small>' : '';
            $welcome='欢迎您，'.$user_name.'！您可以在这里畅言您的的观点与见解！'.$wp_login;//
        }
        echo '<div class="main"><span><h2> 评论留言 </h2></span><p>'.$welcome.'</p></div>';
        if($comment_sw){
?>
            <div id="vcomments" class="v"></div>
<?php
        }else{
            if(is_single()){
?>
            <script type="text/javascript">
                $.fn.postLike = function() {
                    if ($(this).hasClass('liked')) {
                        alert("您已经点过赞了!");
                        return false;
                    }else{
                        $(this).addClass('liked');
                        var id = $(this).data("id"),
                            action = $(this).data('action'),
                            rateHolder = $(this).find('.count em');
                        var ajax_data = {
                                action: "post_like",
                                um_id: id,
                                um_action: action
                            };
                        $.post("/wp-admin/admin-ajax.php", ajax_data, function(data) {
                            $(rateHolder).text(data);
                        });
                        return false;
                    }
                };
                $(document).on("click", "#dislike", function() {
                    $(this).postLike();
                });
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
                        $admin_mail = get_option('site_smtp_mail', get_bloginfo('admin_email'));
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
