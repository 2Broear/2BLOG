
<div class="footer-all">
    <div class="footer-detector" id="end-news-all">
        <span id="end-end">END</span>
        <span id="end-obj"><?php echo(current_slug(true, $cat, $post)); ?></span>
    </div>
    <div class="container">
      <div id="footer-support-board">
        <p id="supports-txt"><?php $blogdesc=get_bloginfo('description');echo '<q>'.get_option('site_support',$blogdesc).'</q><b>'.$blogdesc.'</b>'; ?></p>
      </div>
      <div id="footer-contact-way">
        <ul class="footer-ul">
          <div class="footer-contact-left">
            <div class="footer-left flexboxes">
              <ul class="footer-recommend">
                <h2>近期文章</h2>
                <div class="recently">
                    <?php
                        $post_per = get_option('site_per_posts', get_option('posts_per_page'));
                        $cat_id = get_option('site_bottom_recent_cid');
                        if($cat_id){
                            $query_array = array('cat' => $cat_id, 'meta_key' => 'post_orderby', 'posts_per_page' => $post_per,
                                'orderby' => array(
                                    'meta_value_num' => 'DESC',
                                    'date' => 'DESC',
                                    'modified' => 'DESC',
                                )
                            );
                        }else{
                            $query_array = array('cat' => $cat_id, 'posts_per_page' => $post_per, 'order' => 'DESC', 'orderby' => 'date');
                        }
                        $left_query = new WP_Query(array_filter($query_array));
                        while ($left_query->have_posts()):
                            $left_query->the_post();
                            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                    ?>
                            <li class="<?php if($post_orderby>1) echo 'topset'; ?>" title="<?php the_title() ?>">
                                <a href="<?php the_permalink() ?>" target="_blank">
                                    <em><?php the_title() ?></em>
                                    <?php 
                                        if($post->comment_count>=50){
                                            echo '<sup id="hot">Hot</sup>';
                                        }else{
                                            if($post_orderby>1) echo '<sup id="new">new</sup>';
                                        }
                                    ?>
                                </a>
                            </li>
                    <?php
                        endwhile;
                        wp_reset_query();
                    ?>
                </div>
              </ul>
              <ul class="footer-quickway" id="comments">
                <h2>最新评论</h2>
                <?php
                    $baas = get_option('site_leancloud_switcher');
                    $third_cmt = get_option('site_third_comments');
                    $valine_sw = $third_cmt=='Valine' ? true : false;//get_option('site_valine_switcher');
                    $twikoo_sw = $third_cmt=='Twikoo' ? true : false;//get_option('site_twikoo_switcher');
                    if($valine_sw){    // 全站加载
                ?>
                        <script src="<?php custom_cdn_src(); ?>/js/Valine/Valine.m.js?v=<?php echo get_theme_info('Version'); ?>"></script>
                <?php
                        if(!$baas){
                ?>
                            <script src="<?php custom_cdn_src(); ?>/js/leancloud/av-min.js?v=footcall"></script>
                <?php
                        }
                ?>
                        <script type="text/javascript">
                		    new Valine({
                            	el: '#vcomments',
                            	appId: '<?php echo get_option('site_leancloud_appid') ?>',
                            	appKey: '<?php echo get_option('site_leancloud_appkey') ?>',
                            	serverURLs: '<?php echo get_option('site_leancloud_server') ?>',
                            	pageSize: '<?php echo get_option('comments_per_page',15) ?>',
                            	listSize: '<?php echo get_option('site_per_posts', 5) ?>',
                            	notify: false,
                            	verify: false,
                            	visitor: false,
                            	recordIP: false,
                            	pushPlus: '<?php echo get_option('site_comment_pushplus') ?>',
                            	serverChan: '<?php echo get_option('site_comment_serverchan') ?>',
                            	<?php
                            	    echo get_option('site_lazyload_switcher') ? 'lazyLoad: true,' : 'lazyLoad: false,';
                            	    if(get_option('site_cdn_switcher')){
                            	        echo 'imgCdn: "'.get_option('site_cdn_img').'", srcCdn: "'.get_option('site_cdn_src').'",';
                            	    }
                        	        echo get_option("site_wpwx_notify_switcher") ? 'wxNotify: true,' : 'wxNotify: false,';
                            	?>
                            	posterImg: '<?php echo get_postimg(); ?>',
                            	rootPath: '<?php echo get_bloginfo('template_directory'); ?>',
                            	adminMd5: '<?php echo md5(get_bloginfo('admin_email')) ?>',
                            	avatarCdn: '<?php echo get_option("site_avatar_mirror") ?>avatar/',
                            	placeholder: '快来玩右下角的“涂鸦画板”！'
                            });
                            // reply at current floor
                            const vcomments = document.querySelector("#vcomments");
                            if(vcomments){
                                const vwraps = vcomments.querySelectorAll(".vwrap"),
                                      vats = vcomments.querySelectorAll(".vat"),
                                      origin_wrap = vwraps[0];
                                bindEventClick(vcomments, 'vat');
                            }
                        </script>
                <?php
                    }elseif($twikoo_sw){
                ?>
                        <script src="https://cdn.staticfile.org/twikoo/1.6.4/twikoo.all.min.js"></script>
                        <script>
                            twikoo.init({
                              envId: '<?php echo $twikoo_envid = get_option('site_twikoo_envid'); ?>',
                              el: '#tcomment',
                            });
                            const comment_count = document.querySelectorAll('.valine-comment-count'),
                                  comments_list = document.querySelector('#comments');
                            if(comment_count){
                                var count_array = [];
                                for(let i=0;i<comment_count.length;i++){
                                    count_array.push(comment_count[i].dataset.xid);//getAttribute('data-xid'));
                                }
                                twikoo.getCommentsCount({
                                        envId: '<?php echo get_option('site_twikoo_envid'); ?>', // 环境 ID
                                        urls: count_array,
                                        includeReply: false // 评论数是否包括回复，默认：false
                                    }).then(function (res) {
                                        for(let i=0;i<res.length;i++){
                                            comment_count[i].innerHTML = res[i].count;
                                        }
                                    }).catch(function (err) {
                                        console.error(err);
                                });
                            };
                            if(comments_list){
                                twikoo.getRecentComments({
                                    envId: '<?php echo $twikoo_envid; ?>', // 环境 ID
                                    pageSize: <?php echo $post_per; ?>, // 获取多少条，默认：5，最大：100
                                    includeReply: true // 是否包括最新回复，默认：true
                                }).then(function (res) {
                                    for(let i=0;i<res.length;i++){
                                        // console.log(res[i]);
                                        let each = res[i];
                                        comments_list.innerHTML += `<a href="${each.url}#${each.id}" target="_blank" rel="nofollow"><em title="${each.commentText}">${each.nick}：${each.commentText}</em></a>`;
                                    }
                                }).catch(function (err) {
                                    console.error(err);
                                });
                            }
                        </script>
                        <style>
                            body.dark .twikoo{color: var(--preset-9)}
                            .twikoo{text-align: left!important;color: var(--preset-3a);width: 100%;}
                            .twikoo a{color: inherit;opacity: .75}
                            .twikoo img{margin: auto!important;}
                            .twikoo span{/*width: auto!important;*/margin-top: 0!important;/*display:inline-block*/}
                            .twikoo textarea{min-height:125px!important;}
                            .tk-comments-container{min-height: auto!important;}
                            .tk-extras{font-size: 12px}
                        </style>
                <?php
                    }else{
                        $comments = get_comments(
                            array(
                                'number' => $post_per, //get_option('posts_per_page')
                                'orderby' => 'comment_date',
                                'order' => 'DESC',
                                'status' => 'approve'  // 仅输出已通过审核的评论数量
                            )
                        );
                        // https://www.boke8.net/wordpress-function-get-comments.html
                        foreach($comments as $each){
                            $id = $each->comment_ID;
                            $parent = $each->comment_parent;
                            $content = $each->comment_content;
                            if($parent>0) $content = '<span data-href="#comment-' . $parent . '">@'. get_comment_author($parent) . '</span> , ' . $content;
                            $content = strip_tags($content);
                ?>
                            <li>
                                <a href="<?php echo get_permalink($each->comment_post_ID)."#comment-".$id; ?>" target="_blank" rel="nofollow">
                                    <em title="<?php echo $content; ?>"><?php echo $each->comment_author .' : '. $content ?></em>
                                </a>
                            </li>
                <?php
                        }
                    }
                ?>
              </ul>
            </div>
            <div class="footer-right">
              <ul class="footer-contact">
                <h2>找到我</h2>
                <li class="contactBox">
                    <?php
                        $weibo = get_option('site_contact_weibo');
                        if($weibo){
                            echo '<a href="'.$weibo.'" target="_blank" rel="nofollow" aria-label="weibo"><span class="contact-icons" id="icon-weibo"><i class="icom"></i></span></a>';
                        }
                    ?>
                  
                  <a href="<?php echo get_option('site_contact_music') ?>" target="_blank" rel="nofollow" aria-label="music">
                    <span class="contact-icons" id="icon-netease">
                      <i class="BBFontIcons"></i>
                    </span>
                  </a>
                  <a href="javascript:void(0)" target="_self" rel="nofollow" aria-label="wechat">
                    <span class="contact-icons" id="icon-wechat">
                      <i class="icom"></i>
                    </span>
                    <span class="preview">
                        <?php
                            // $lazyload = get_option('site_lazyload_switcher') ? 'data-src' : 'src';
                            global $lazysrc;
                            echo '<img '.$lazysrc.'="'.get_option('site_contact_wechat').'" alt="wechat" />';
                        ?>
                    </span>
                  </a>
                  <a href="mailto:<?php echo get_option('site_contact_email') ?>" target="_blank" rel="nofollow" aria-label="email">
                    <span class="contact-icons" id="icon-mail">
                      <i class="icom"></i>
                    </span>
                  </a>
                  <a href="<?php echo get_option('site_contact_bilibili') ?>" target="_blank" rel="nofollow" aria-label="bilibili">
                    <span class="contact-icons" id="icon-bilibili">
                      <i class="BBFontIcons"></i>
                    </span>
                  </a>
                <?php
                    $github = get_option('site_contact_github');
                    $steam = get_option('site_contact_steam');
                    $twitter = get_option('site_contact_twitter');
                    if($github){ ?>
                      <a href="<?php echo $github ?>" target="_blank" rel="nofollow" aria-label="github">
                        <span class="contact-icons" id="icon-github">
                          <i class="icom"></i>
                        </span>
                      </a>
                <?php }if($twitter){ ?>
                      <a href="<?php echo $twitter ?>" target="_blank" rel="nofollow" aria-label="twitter">
                        <span class="contact-icons" id="icon-twitter">
                          <i class="icom"></i>
                        </span>
                      </a>
                <?php };if($steam){ ?>
                      <a href="<?php echo $steam ?>" target="_blank" rel="nofollow" aria-label="steam">
                        <span class="contact-icons" id="icon-steam">
                          <i class="icom"></i>
                        </span>
                      </a>
                <?php }; ?>
                </li>
                <li class="rcmdBrowser">
                  <p>最佳浏览体验
                    <br/>推荐浏览器：</p>
                  <b>
                    <a id="chrome" href="https://www.google.cn/chrome/" target="_blank" rel="nofollow" title="Chrome大法好！" aria-label="chrome">Chrome</a>/
                    <a id="edge" href="https://www.microsoft.com/zh-cn/edge" target="_blank" rel="nofollow" title="新版Edge也不错~" aria-label="edge">Edge</a></b>
                </li>
                <li class="PoweredBy2B">
                  <ins> XTyDesign </ins>
                  <?php echo '<img '.$lazysrc.'="'.custom_cdn_src('img',true).'/images/svg/XTy_.svg" style="max-width:66px" alt="XTY Design" />'; ?>
              </li>
              </ul>
              <ul class="friend_links">
                <h2>朋友圈</h2>
                <li class="friendsBox">
                    <?php 
                        if($baas && strpos(get_option('site_leancloud_category'), 'category-2bfriends.php')!==false){
                    ?>
                            <script type="text/javascript"> //addAscending createdAt
                                new AV.Query("link").addDescending("updatedAt").equalTo('sitelink', 'true').find().then(result=>{
                                    for (let i=0; i<result.length;i++) {
                                        let res = result[i],
                                            name = res.attributes.name,
                                            link = res.attributes.link;
                                        document.querySelector(".friend_links li.friendsBox").innerHTML += `<a href="${link}" class="inbox-aside" target="_blank" rel="sitelink">${name}</a>`;
                                    };
                                })
                            </script>
                    <?php
                        }else{
                            site_links(get_bookmarks(array(
            	                'orderby' => 'link_id',
            	                'order' => 'DESC', //ASC
            	                'category_name' => "sitelink",
            	                'hide_invisible' => 0
        	                )));
        	                $use_temp = get_template_bind_cat('category-2bfriends.php');
        	                $temp_link = !$use_temp->errors ? get_category_link($use_temp->term_id) : 'javascript:;';
    	                    echo '<a id="more" href="'.$temp_link.'" title="更多" target="_blank">  更多 </a>';
                        }
                    ?>
                </li>
              </ul>
            </div>
          </div>
        </ul>
      </div>
      <div id="footer-copyright">
        <span class="what_says">
          <ul style="text-align:left">
            <li id="copy"> ©<?php calc_copyright(); ?> </li>
            <?php $rights=get_option('site_copyright');if($rights) echo '<li id="cc"><a href="https://creativecommons.org/licenses/'.strtolower(substr($rights,strpos($rights,"-")+1)).'/4.0/" style="opacity:.88" target="_blank" rel="nofollow"> '.$rights.' </a></li>'; ?>
            <li id="rights"><?php echo get_option('site_nick', get_bloginfo('name')); ?> 版权所有</li>
            <?php if(get_option('site_beian_switcher')) echo '<li id="etc">'.get_option('site_beian').'</li>'; ?>
            <p id="supports">
                <?php 
                    if(get_option('site_monitor_switcher')) echo '<script type="text/javascript" src="'.get_option('site_monitor').'"></script>';
                    if(get_option('site_chat_switcher')) echo '<a href="'.get_option("site_chat").'" target="_blank" title="Chat Online" rel="nofollow"><img '.$lazysrc.'="'.custom_cdn_src('img',true).'/images/svg/tidio.svg" alt="tidio" style="height: 16px;opacity:.88;"></a>';
                    // if(get_option('site_foreverblog_switcher'))
                    echo '<a href="'.get_option('site_foreverblog').'" target="_blank" rel="nofollow"><img '.$lazysrc.'="'.custom_cdn_src('img',true).'/images/svg/foreverblog.svg" alt="foreverblog" style="height: 16px;"></a>';
                    // if($valine_sw || $baas) echo '<a href="https://leancloud.cn" target="_blank"><b style="color:#2b96e7" title="AVOS BAAS Support">LeanCloud</b></a>';
                    $server = get_option('site_server_side');
                    if($server) echo '<a href="javascript:void(0);" rel="nofollow"><img '.$lazysrc.'="'.$server.'" style="height: 12px;" alt="server"></a>'; //&&$server!="已关闭"
                    if(get_option('site_foreverblog_wormhole')){
                        $theme = array_key_exists('theme_mode',$_COOKIE) ? $_COOKIE['theme_mode'] : false;
                        // $warmhole_img = $theme ? custom_cdn_src('img',true).'/images/wormhole_2_tp.gif' : custom_cdn_src('img',true).'/images/wormhole_4_tp.gif';
                        echo '<a href="https://www.foreverblog.cn/go.html" target="_blank" rel="nofollow"><em class="warmhole" style="background:url('.custom_cdn_src('img',true).'/images/wormhole_4_tp_ez.gif) no-repeat center center /cover" title="穿梭虫洞-随机访问十年之约友链博客"></em></a>';
                    }
                ?>
            </p>
          </ul>
          <ul style="text-align:right">
              <li id="feed"><a href="<?php bloginfo('rss2_url'); ?>" target="_blank">RSS</a></li>
              <?php
                  $bottom_nav_array = explode(',',get_option('site_bottom_nav'));
                  for($i=0;$i<count($bottom_nav_array);$i++){
                      $cat_slug = trim($bottom_nav_array[$i]);
                      $cat_term = get_category_by_slug($cat_slug);
                      if($cat_slug&&$cat_term){
                          echo '<li id="'.$cat_slug.'"><a href="'.get_category_link($cat_term->term_id).'" target="_blank">'.$cat_term->name.'</a></li>';
                      }
                  };
                  if(get_option('site_map_switcher')) echo '<li id="sitemap"><a href="'.get_bloginfo('siteurl').'/sitemap.xml" target="_blank">站点地图</a></li>';
              ?>
              <p style="margin:auto;opacity:.75;font-size:12px;font-style:italic"> WP Theme <a href="https://github.com/2Broear/2BLOG" style="color:inherit;" target="_blank"><ins> <b>2BLOG</b> </ins></a> openSourced via 2broear </p>
          </ul>
        </span>
      </div>
    </div>
    <div class="functions-tool">
        <div class="inside-functions">
            <div class="box">
                <div class="top" title="返回顶部"><em>顶</em></div>
                <div class="dark" title="主题切换" onclick="darkmode()">
                    <i class="icom icon-moon"></i>
                </div>
                <div class="bottom" title="跳至顶部"><em>底</em></div>
                <div class="pagePer" title="双击自动根据时段设置主题" ondblclick="automode()">
                    <strong></strong>
                    <i style>
                        <span class="wave"></span>
                    </i>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php custom_cdn_src(); ?>/js/nprogress.js"></script>
<script type="text/javascript">
	NProgress.start();
	window.addEventListener('load', function(){
		NProgress.done();
    });
    <?php
        // lazyLoad images
        if(get_option('site_lazyload_switcher')){
            global $cat;
            $acgcid = get_cat_by_template('acg','term_id');
            if($acgcid==$cat || cat_is_ancestor_of($acgcid, $cat)){
    ?>
                var getAverageRGB = function(imgEl){var blockSize=5,defaultRGB={r:255,g:255,b:255},canvas=document.createElement('canvas'),context=canvas.getContext&&canvas.getContext('2d'),data,width,height,i=-4,length,rgb={r:0,g:0,b:0},count=0;if(!context){return defaultRGB}height=canvas.height=imgEl.naturalHeight||imgEl.offsetHeight||imgEl.height;width=canvas.width=imgEl.naturalWidth||imgEl.offsetWidth||imgEl.width;context.drawImage(imgEl,0,0);try{data=context.getImageData(0,0,width,height)}catch(e){return defaultRGB}length=data.data.length;while((i+=blockSize*4)<length){++count;rgb.r+=data.data[i];rgb.g+=data.data[i+1];rgb.b+=data.data[i+2]}rgb.r=~~(rgb.r/count);rgb.g=~~(rgb.g/count);rgb.b=~~(rgb.b/count);return rgb},
                    setupBlurColor = function(imgEl,tarEl,tarCls="inbox"){
                        if(!tarEl.classList.contains(tarCls)){
                            return;
                        }
                        let rgb = getAverageRGB(imgEl),
                            rgba = rgb['r']+' '+rgb['g']+' '+rgb['b']+' / 50%';
                        tarEl.setAttribute('style','background:rgb('+rgba+')');
                    };
    <?php
            }
    ?>
            const bodyimg = document.querySelectorAll("body img"),
                  loadimg = "<?php custom_cdn_src('img') ?>/images/loading_3_color_tp.png";
            if(bodyimg[0]){
                var timer_throttle = null,
                    loadArray = [],
                    msgObject = Object.create(null),
                    autoLoad = function(imgLoadArr, initDomArr=false){
                        let tempArray = initDomArr ? initDomArr : imgLoadArr;  //判断加载数组类型，默认加载 loadArray
                        for(let i=0;i<tempArray.length;i++){
                            let eachimg = tempArray[i],
                                datasrc = eachimg.dataset.src;
                            if(datasrc){
                                eachimg.src = loadimg; //pre-holder(datasrc only)
                                new Promise(function(resolve,reject){
                                    initDomArr ? imgLoadArr.push(eachimg) : false;  //判断首次加载（载入 lazyload 元素数组）
                                    resolve(imgLoadArr);
                                }).then(function(res){
                                    if(eachimg.getBoundingClientRect().top<window.innerHeight){
                                        eachimg.src = datasrc; // 即时更新 eachimg（设置后即可监听图片 onload 事件）
                                        // 使用 onload 事件替代定时器或Promise，判断已设置真实 src 的图片加载完成后再执行后续操作
                                        eachimg.onload=function(){
                                            if(this.getAttribute('src')==datasrc){
                                                res.splice(res.indexOf(this), 1);  // 移除已加载图片数组（已赋值真实 src 情况下）
                                            }else{
                                                this.removeAttribute('data-src'); // disable loadimg
                                                this.src = datasrc;  // this.src will auto-fix [http://] prefix
                                                // console.log('waitting..', this);
                                            }
                                            <?php
                                                if($acgcid==$cat || cat_is_ancestor_of($acgcid, $cat)){
                                                    echo 'setupBlurColor(eachimg, eachimg.parentNode.parentNode);';
                                                }
                                            ?>
                                        }
                                        // handle loading-err images eachimg.onerror=()=>this.src=loadimg;
                                        eachimg.onerror=function(){
                                            res.splice(res.indexOf(this), 1);  // 移除错误图片数组
                                            this.removeAttribute('src');
                                            this.removeAttribute('data-src'); // disable loadimg
                                            this.setAttribute('alt','图片请求出现问题'); // this.removeAttribute('src');
                                        }
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                });
                            }
                        }
                    },
                    scrollLoad = function(){
                        return (function(){
                            if(timer_throttle==null){
                                timer_throttle = setTimeout(function(){
                                    // console.log('loading..');
                                    if(loadArray.length<=0){
                                        console.log(Object.assign(msgObject, {status:'lazyload done', type:'all'}));
                                        window.removeEventListener('scroll', scrollLoad, true);
                                        return;
                                    };
                                    autoLoad(loadArray);
                                    // console.log('throttling..',loadArray);
                                    timer_throttle = null;  //消除定时器
                                }, 500, loadArray); //重新传入array（单次）循环
                            }
                        })();
                    };
                window.addEventListener('scroll', scrollLoad, true);
                autoLoad(loadArray, bodyimg);
            }
    <?php
        }
    ?>
    // 自动根据时段设置主题
    function automode(){
        getCookie('theme_manual') ? setCookie('theme_manual',0,0,1) : false;  // disable manual mode
        let date = new Date(),
            hour = date.getHours(),
            min = date.getMinutes(),
            sec = date.getSeconds(),
            start = <?php echo get_option('site_darkmode_start',17); ?>,
            end = <?php echo get_option('site_darkmode_end',9); ?>;
        hour>=end&&hour<start || hour==end&&min>=0&&sec>=0 ? setCookie('theme_mode','light',0,1) : setCookie('theme_mode','dark',0,1);
        document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
    };
</script>
<?php
    if(get_option('site_video_capture_switcher')){
        $ffmpeg_sw_gif = get_option('site_video_capture_gif');
?>
        <style>
            video{object-fit: initial;}
            .video_preview_hide:before,.video_preview_hide .preview_bg{content:"";display:none}
            .video_previews:before{content:'';width:100%!important;height:52%;backdrop-filter:blur(10px);position:absolute!important;top:0!important;left:0!important;z-index:1;background:-webkit-linear-gradient(90deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);background:linear-gradient(0deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);}
            .video_previews{cursor: e-resize;}
            .video_preview_hide{cursor: default;}
            .wp-block-video, .video_previews{position:relative;overflow:hidden;border-radius:10px;/*display:inline-block;*/}
            .video_previews .preview_bg{z-index:99!important;opacity:1!important;top:30%;pointer-events:none;}
            .preview_bg .progress{width:32%;height:4px;background:white;border:1px solid black;border-radius:15px;position:absolute!important;bottom:10%;left:50%;transform:translate(-50%,-50%);overflow:hidden}
            .preview_bg .progress em.pause_move{transform:translateX(0%)!important}
            .preview_bg .progress em{width:100%;height:100%;background:var(--theme-color);position:inherit!important;top:1px;left:0;transform:translateX(-100%);will-change:transform}
            .preview_bg{cursor:crosshair;position:absolute!important;left:50%;transform:translate(-50%,-50%);border-radius:10px!important;z-index:-1!important;opacity:0;transition:opacity .35s ease-in;width:90%;height:45%;top:20%;margin:auto!important;/*transition:top 1s ease;width:88%;height:58%;top:38%!important;*/}
        </style>
        <script>
            const videos = document.querySelectorAll('video');
            if(videos[0]){
                for(let i=0;i<videos.length;i++){
                    let video = videos[i];
                    if(!video.autoplay){
                        let video_src = video.src,
                            video_box = video.parentNode,
                            video_dir = video_src.lastIndexOf('/')+1,
                            video_url = video_src.substr(0, video_dir),
                            video_title = video_src.substr(video_dir, video_src.length),
                            video_name = video_title.substr(0, video_title.lastIndexOf('.')),
                            video_path = video_url+video_name+"/"+video_name,
                            // video_width = video_box.offsetWidth,
                            video_gif = video_path+'.gif',
                            video_timer = null;
                        video.addEventListener('canplay', function () {
                            video = video_box.querySelector('video'); // canplay 内需重新声明 video，否则修改后无法应用到dom
                            video.onplaying=()=>video_box.classList.add('video_preview_hide');
                            video.onpause=()=>video_box.classList.remove('video_preview_hide');
                            <?php 
                                // if($ffmpeg_sw_gif){
                            ?>
                                    // let gifWidth = video.videoWidth/2,  //预置gif预览宽度 this.videoWidth/2
                                    //     boxWidth = video_box.offsetWidth;
                                    // // 仅当预览gif宽度小于视频盒子宽度时设置视频宽高，防止 poster 缩小视频宽高
                                    // if(gifWidth<boxWidth){
                                    //     video.width = boxWidth;//this.videoWidth;
                                    //     video.height = video_box.offsetHeight;//this.videoHeight;
                                    // }
                            <?php
                                // }
                            ?>
                        });
                        video_box.innerHTML += `<div class="preview_bg"<?php echo $ffmpeg_sw_gif ? ' data-previews="${video_gif}"' : false; ?> style="background:url(${video_path}.jpg) no-repeat 0% 0% /cover"><span class="progress"><em></em></span></div>`;
                        const preview_bg = video_box.querySelector('.preview_bg'),
                              preview_gif = preview_bg.dataset.previews,
                              preview_pg = video_box.querySelector('.progress em');
                        video_box.onmousemove=function(e){
                            var _this = this,
                                video = _this.querySelector("video"),  //update video dom
                                video_offset = e.offsetX,
                                video_width = video_box.offsetWidth;  //always update videoBox width
                            return (function(){
                                if(video_timer==null){
                                    <?php echo $ffmpeg_sw_gif ? 'video.poster!=video_gif&&preview_gif ? video.poster=preview_gif : false;' : false; ?>
                                    _this.classList.add('video_previews');
                                    video_timer = setTimeout(function(){
                                        // e.stopPropagation(); //e.preventDefault(); 
                                        let percentage = (Math.round(video_offset/video_width*10000)/100).toFixed(0),
                                            progressOffset = -100+Number(percentage); //-100+Number(percentage)
                                        preview_bg.style.backgroundPosition = percentage+"% 0%";
                                        preview_pg.style.transform = 'translateX('+progressOffset+'%)';
                                        // console.log(percentage);
                                        Number(percentage)>=100 ? preview_pg.classList.add('pause_move') : preview_pg.classList.remove('pause_move');
                                        _this.onmouseleave = function(){
                                            this.classList.remove("video_previews");
                                            preview_pg.style.transform = "";
                                        }
                                        video_timer = null;  //消除定时器
                                    }, 10);
                                }
                            })();
                        }
                    }
                }
            }
        </script>
<?php
    }
?>
<?php 
    if(get_option('site_logo_switcher')){
?>
        <style>
            body.dark .mobile-vision .m-logo span,
            body.dark .logo-area span{
                background: url(<?php echo get_option('site_logos'); ?>) no-repeat center center /cover!important;
            }
        </style>
<?php
    };
    $cat = $cat ? $cat : get_page_cat_id(current_slug());  //rewrite cat to cid (var cat for require php)
    require_once(TEMPLATEPATH. '/foot.php');
?>