<?php
    global $lazysrc, $cat, $src_cdn, $img_cdn;
    //计算版权时间，直接在footer使用会引发没有内容的notes子分类无法显示
    function get_copyright(){
        $year = gmdate('Y', time() + 3600*8);//date('Y');
        $begain = get_option('site_begain');
        if($begain&&$begain<$year) echo $begain."-";
        return $year;
    }
?>
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
        <ol class="footer-ul">
          <div class="footer-contact-left">
            <div class="footer-left flexboxes">
              <ul class="footer-recommend">
                <h2>近期更新</h2>
                <div class="recently">
                    <?php
                        $post_per = get_option('site_per_posts', get_option('posts_per_page'));
                        $query_array = array('cat' => [get_option('site_bottom_recent_cat')], 'posts_per_page' => $post_per, 'order' => 'DESC', 'orderby' => 'date');
                        $current_month = date('Ym');
                        $left_query = new WP_Query(array_filter($query_array));
                        $left_count = 0;
                        while ($left_query->have_posts()):
                            $left_query->the_post();
                            $post_orderby = get_post_meta($post->ID, "post_orderby", true);
                            $left_count++;
                    ?>
                            <li class="<?php if($post_orderby>1) echo 'topset'; ?>" title="<?php the_title(); ?>">
                                <a href="<?php the_permalink(); ?>" target="_blank">
                                    <em><?php the_title(); ?></em>
                                    <?php 
                                        if($post->comment_count>=25){
                                            echo '<sup id="hot">Hot</sup>';
                                        }elseif($left_count<=3){
                                            if(date('Ym',strtotime($post->post_date))==$current_month) echo '<sup id="new">new</sup>';
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
                    if($third_cmt=='Valine'){    // 全站加载
                ?>
                        <script src="<?php echo $src_cdn; ?>/js/Valine/Valine.m.js?v=<?php echo get_theme_info(); ?>"></script>
                <?php
                        if(!$baas){
                ?>
                            <script src="<?php echo $src_cdn; ?>/js/leancloud/av-min.js?v=footcall"></script>
                <?php
                        }
                	    $root_path = custom_cdn_src('default', true);
            	        $plugin_path = $root_path.'/plugin';
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
                            	placeholder: '快来玩右下角的“涂鸦画板”！',
                            	<?php
                            	    if(get_option('site_cdn_switcher')){
                            	        echo 'imgCdn: "'.$img_cdn.'",' . PHP_EOL . ' srcCdn: "'.$src_cdn.'",' . PHP_EOL . ' apiCdn: "'.$plugin_path.'",' . PHP_EOL;
                            	    }else{
                            	        echo 'rootPath: "'.$root_path.'",' . PHP_EOL;
                            	    }
                            	    echo get_option('site_lazyload_switcher') ? 'lazyLoad: true,' . PHP_EOL : 'lazyLoad: false,' . PHP_EOL;
                        	        echo get_option("site_wpwx_notify_switcher") ? 'wxNotify: true,' . PHP_EOL : 'wxNotify: false,' . PHP_EOL;
                            	?>
                            	pushPlus: <?php echo get_option('site_comment_pushplus') ? "'".get_option('site_comment_pushplus')."'" : 'false'; ?>,
                            	serverChan: <?php echo get_option('site_comment_serverchan') ? "'".get_option('site_comment_serverchan')."'" : 'false'; ?>,
                            	adminMd5: '<?php echo md5(get_bloginfo('admin_email')) ?>',
                            	avatarCdn: '<?php echo get_option("site_avatar_mirror").'avatar/'; ?>',
                            	avatarApi: '<?php echo $plugin_path.'/gravatar.php?jump=0&email='; ?>',
                            	posterImg: '<?php echo get_postimg(0, $post->ID, true); ?>',
                            });
                            // reply at current floor
                            const vcomments = document.querySelector("#vcomments");
                            if(vcomments){
                                const vwraps = vcomments.querySelectorAll(".vwrap"),
                                      origin_wrap = vwraps[0]; // origin_bak = document.importNode(origin_wrap, true),
                                // origin_wrap.querySelector("textarea").setAttribute('autofocus',true);
                                bindEventClick(vcomments, 'vat', function(t){
                                    const adopt_node = document.adoptNode(origin_wrap);
                                    if(!t.classList.contains('reply')){
                                        const vats = vcomments.querySelectorAll(".vat"),
                                              vpar = getParByCls(t, 'vh');
                                        for(let i=0,vatsLen=vats.length;i<vatsLen;i++){
                                            vats[i].classList.remove('reply');
                                            vats[i].innerText = "回复";
                                        }
                                        t.classList.add('reply');
                                        t.innerText = "取消回复";
                                        vpar.appendChild(adopt_node);
                                        //.. vpar.querySelector("textarea")
                                        var delay_focus = setTimeout(()=>{
                                            adopt_node.querySelector("textarea").focus();
                                            clearTimeout(delay_focus);
                                            delay_focus = null;
                                        }, 100);
                                    }else{
                                        t.classList.remove('reply');
                                        t.innerText = "回复";
                                        vcomments.insertBefore(adopt_node, vcomments.querySelector(".vinfo"));  // reverse origin_bak
                                    }
                                });
                            }
                        </script>
                <?php
                    }elseif($third_cmt=='Twikoo'){
                ?>
                        <script src="<?php echo 'https://cdn.staticfile.org/twikoo/' . get_option('site_twikoo_version'). '/twikoo.all.min.js'; ?>"></script>
                        <script>
                            twikoo.init({
                                envId: '<?php echo $twikoo_envid = get_option('site_twikoo_envid'); ?>',
                                el: '#tcomment',
                            });
                            const comment_count = document.querySelectorAll('.valine-comment-count'),
                                  comments_list = document.querySelector('#comments');
                            if(comment_count){
                                var count_array = [];
                                for(let i=0,ccLen=comment_count.length;i<ccLen;i++){
                                    count_array.push(comment_count[i].dataset.xid);//getAttribute('data-xid'));
                                }
                                twikoo.getCommentsCount({
                                        envId: '<?php echo get_option('site_twikoo_envid'); ?>', // 环境 ID
                                        urls: count_array,
                                        includeReply: false // 评论数是否包括回复，默认：false
                                    }).then(function (res) {
                                        for(let i=0,resLen=res.length;i<resLen;i++){
                                            comment_count[i].innerHTML = `${res[i].count}`;
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
                                    for(let i=0,resLen=res.length;i<resLen;i++){
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
                            echo '<img src="'.get_option('site_contact_wechat').'" alt="wechat" />'; //'.$lazysrc.'
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
                  <p>最佳浏览体验<br/>推荐浏览器：</p>
                  <b>
                    <a id="chrome" href="//www.google.cn/chrome/" target="_blank" rel="nofollow" title="Chrome大法好！" aria-label="chrome">Chrome</a> / <a id="edge" href="//www.microsoft.com/zh-cn/edge" target="_blank" rel="nofollow" title="新版Edge也不错~" aria-label="edge">Edge</a></b>
                </li>
                <li class="PoweredBy2B">
                  <ins> XTyDesign </ins>
                  <?php echo '<img src="'.$img_cdn.'/images/svg/XTy_.svg" style="max-width:66px" alt="XTY Design" />'; //'.$lazysrc.' ?>
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
                                    for (let i=0,resLen=result.length; i<resLen;i++) {
                                        document.querySelector(".friend_links li.friendsBox").innerHTML += `<a href="${result[i].attributes.link}" class="inbox-aside" target="_blank" rel="sitelink">${result[i].attributes.name}</a>`;
                                    };
                                })
                            </script>
                    <?php
                        }else{
                            $sitelink = get_site_bookmarks('sitelink', 'rand', 'DESC');
                            $sitelinks = get_site_links($sitelink);
                            if (empty($sitelink)) {
    	                        echo '<a id="more" href="javascript:;">' . $sitelinks . '</a>';
                            } else {
                                echo get_site_links($sitelink);
            	                $use_temp = get_template_bind_cat('category-2bfriends.php');
            	                $temp_link = !$use_temp->errors ? get_category_link($use_temp->term_id) : 'javascript:;';
        	                    echo '<a id="more" href="'.$temp_link.'" title="更多" target="_blank">  更多 </a>';
                            }
                        }
                    ?>
                </li>
              </ul>
            </div>
          </div>
        </ol>
      </div>
      <div id="footer-copyright">
        <span class="what_says">
          <ul style="text-align:left">
            <li id="copy"> ©<?php echo get_copyright(); ?> </li>
            <?php $rights=get_option('site_copyright');if($rights) echo '<li id="cc"><a href="//creativecommons.org/licenses/'.strtolower(substr($rights,strpos($rights,"-")+1)).'/4.0/" target="_blank" rel="nofollow"> '.$rights.' </a></li>'; ?>
            <li id="rights"><?php echo get_option('site_nick', get_bloginfo('name')); ?> 版权所有</li>
            <?php 
                if(get_option('site_beian_switcher')) echo '<li id="etc"><a href="//beian.miit.gov.cn/" target="_blank" rel="nofollow">'.get_option('site_beian').'</a></li>';
                $moe_beian = get_option('site_moe_beian_switcher');
                $moe_beian_num = get_option('site_moe_beian_num');
                if($moe_beian) echo '<li id="etc"><a href="//icp.gov.moe/?keyword='.$moe_beian_num.'" target="_blank" rel="nofollow"><img src="//icp.gov.moe/images/ico64.png" alt="moe_beian" title="异次元之旅-跃迁" style="height: 16px;"> 萌ICP备'.$moe_beian_num.'号</a></li>';
            ?>
            <p id="supports">
                <?php 
                    if(get_option('site_monitor_switcher')) echo '<script async type="text/javascript" src="'.get_option('site_monitor').'"></script>';
                    if(get_option('site_chat_switcher')) echo '<a href="'.get_option("site_chat").'" target="_blank" title="Chat Online" rel="nofollow"><img src="'.$img_cdn.'/images/svg/tidio.svg" alt="tidio" style="height: 16px;opacity:.88;"></a>'; //'.$lazysrc.'
                    if(get_option('site_foreverblog_switcher')){
                        echo '<a href="'.get_option('site_foreverblog').'" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/foreverblog.svg" alt="foreverblog" style="height: 16px;"></a>';
                        if(get_option('site_foreverblog_wormhole')){
                            // $theme = array_key_exists('theme_mode',$_COOKIE) ? $_COOKIE['theme_mode'] : false;
                            echo '<a href="//www.foreverblog.cn/go.html" target="_blank" rel="nofollow"><em class="warmhole" style="background:url('.$img_cdn.'/images/wormhole_4_tp_ez.gif) no-repeat center center /cover" title="穿梭虫洞-随机访问十年之约友链博客"></em></a>';
                        }
                    }
                    if($moe_beian && get_option('site_moe_beian_travel')) echo '<a href="//travel.moe/go.html" target="_blank" rel="nofollow"><img src="//moe.one/upload/attach/202307/89_8TEYVRKUCP79XHG.png" alt="moe_beian" title="异次元之旅-跃迁" style=""></a>';
                    $server = get_option('site_server_side');
                    if($server) echo '<a href="javascript:;" rel="nofollow"><img src="'.$server.'" style="height: 12px;" alt="server"></a>'; //'.$lazysrc.'
                    echo '<a href="javascript:;" target="" rel="nofollow"><img src="'.$img_cdn.'/images/svg/deepal-blue.svg" alt="deepal-blue" style="height: 14px;filter:invert(0.5);"></a>';
                    // if(get_option('site_not_ai_switcher')) echo '<a href="//notbyai.fyi" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/not-by-ai.svg" alt="notbyai" style="height: 15px;filter:invert(0.5);"></a>';
                    if(get_option('site_construction_switcher')) echo '<style>@keyframes alarmLamp_bar_before{0%{opacity:.15;}2%{opacity:1;}4%{opacity:.15;}6%{opacity:1;}8%{opacity:.15;}10%{opacity:1;}12%{opacity:.15;}14%{opacity:1;}16%{opacity:.15;}18%{opacity:1;}20%{opacity:.15;}22%{opacity:1;}24%{opacity:.15;}26%{opacity:1;}28%{opacity:.15;}50%{opacity:.15;}60%{opacity:1;}61%{opacity:.15;}62%{opacity:1;}70%{opacity:.15;}80%{opacity:1;}81%{opacity:.15;}82%{opacity:1;}90%{opacity:.15;}100%{opacity:1;}}@keyframes alarmLamp_bar_after{0%{opacity:.15;}28%{opacity:.15;}30%{opacity:1;}32%{opacity:.15;}34%{opacity:1;}36%{opacity:.15;}38%{opacity:1;}39%{opacity:.15;}40%{opacity:1;}42%{opacity:.15;}44%{opacity:1;}46%{opacity:.15;}48%{opacity:1;}50%{opacity:.15;}52%{opacity:1;}54%{opacity:.15;}56%{opacity:1;}58%{opacity:.15;}60%{opacity:.15;}70%{opacity:1;}71%{opacity:.15;}72%{opacity:1;}80%{opacity:.15;}90%{opacity:1;}91%{opacity:.15;}92%{opacity:1;}100%{opacity:.15;}}@keyframes alarmLamp_spotlight{0%{filter:blur(0px);}28%{filter:blur(0px);}50%{filter:blur(0px);}60%{background:red;filter:blur(15px);}62%{background:red;filter:blur(15px);}70%{background:blue;filter:blur(15px);}72%{background:blue;filter:blur(15px);}80%{background:red;filter:blur(15px);}82%{background:red;filter:blur(15px);}90%{background:blue;filter:blur(15px);}92%{background:blue;filter:blur(15px);}100%{filter:blur(0px);}}.alarm_lamp span#spot::before,.alarm_lamp span#spot::after{content:none;}.alarm_lamp span#spot,.alarm_lamp span#bar::before,.alarm_lamp span#bar::after{content:"";width:33%;height:78%;background:red;box-shadow:rgb(255 0 0 / 80%) 0 0 20px 0px;position:absolute;top:50%;left:50%;transform:translate(0%,-50%);-webkit-transform:translate(0%,-50%);animation-duration:3s;animation-delay:0s;animation-timing-function:step-end;animation-iteration-count:infinite;animation-direction:normal;}.alarm_lamp span#bar::before{left:0%;animation-name:alarmLamp_bar_before;-webkit-animation-name:alarmLamp_bar_before;}.alarm_lamp span#bar::after{left:auto;right:0%;background:blue;box-shadow:rgb(0 0 255 / 80%) 0 0 20px 0px;animation-name:alarmLamp_bar_after;-webkit-animation-name:alarmLamp_bar_after;}.alarm_lamp{display:inline-block;padding:0 2px!important;box-sizing:border-box;position:relative;vertical-align:middle;border:1px solid transparent;}.alarm_lamp span{height:100%;display:block;position:inherit;}.alarm_lamp span#bar{width:100%;}.alarm_lamp span#spot{max-width:32%;background:white;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);box-shadow:rgb(255 255 255 / 100%) 0 0 20px 0px;animation-name:alarmLamp_spotlight;-webkit-animation-name:alarmLamp_spotlight;}</style><a href="javascript:void(0);" class="alarm_lamp" style="width:58px;height:12px;width: 20%;height: 5%;position: fixed;z-index: 999;top: 50%;left: 50%;transform: translate(-50%, -50%);opacity: .1;" title="站点正处施工中.."><span id="bar"></span><span id="spot"></span></a>';
                ?>
            </p>
          </ul>
          <ul style="text-align:right">
              <li id="feed"><a href="<?php bloginfo('rss2_url'); ?>" target="_blank">RSS</a></li>
              <?php
                  $bottom_nav_array = explode(',',get_option('site_bottom_nav'));
                  $bottom_nav_array_count = count($bottom_nav_array);
                  for($i=0;$i<$bottom_nav_array_count;$i++){
                      $cat_slug = trim($bottom_nav_array[$i]);
                      $cat_term = get_category_by_slug($cat_slug);
                      if($cat_slug&&$cat_term){
                          echo '<li id="'.$cat_slug.'"><a href="'.get_category_link($cat_term->term_id).'" target="_blank">'.$cat_term->name.'</a></li>';
                      }
                  };
                  if(get_option('site_map_switcher')) echo '<li id="sitemap"><a href="'.get_bloginfo('siteurl').'/sitemap.xml" target="_blank">站点地图</a></li>';
              ?>
              <p style="margin:auto;opacity:.75;font-size:12px;font-style:italic"> WP Theme <a href="//github.com/2Broear/2BLOG" style="color:inherit;" target="_blank"><b>2BLOG</b></a>  open source via 2broear </p>
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
                    <strong data-percent="0"></strong>
                    <i style>
                        <span class="wave"></span>
                    </i>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    console.info("<?php echo get_num_queries().'次查询，耗时'.timer_stop(0).'秒。'; ?>");
    asyncLoad("<?php echo $src_cdn; ?>/js/nprogress.js", function(){
	    NProgress.start();
	    const NProgressLoaded = function(){
    		NProgress.done();
    	    window.removeEventListener('load', NProgressLoaded, true);
	    }
    	window.addEventListener('load', NProgressLoaded, true);
    });
    // 闭包节流器
    function closure_throttle(callback=false, delay=200){
        let closure_variable = true;  //default running
        return function(){
            if(!closure_variable) return;  //now running..
            closure_variable = false;  //stop running
            setTimeout(()=>{
                callback.apply(this, arguments);
                closure_variable = true;  //reset running
            }, delay);
        };
    }
    // 自动根据时段设置主题
    function automode(){
        getCookie('theme_manual') ? setCookie('theme_manual',0) : false;  // disable manual mode
        let date = new Date(),
            hour = date.getHours(),
            min = date.getMinutes(),
            sec = date.getSeconds(),
            start = <?php echo get_option('site_darkmode_start',17); ?>,
            end = <?php echo get_option('site_darkmode_end',9); ?>;
        hour>=end&&hour<start || hour==end&&min>=0&&sec>=0 ? setCookie('theme_mode','light') : setCookie('theme_mode','dark');
        document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
    };
    <?php
        // lazyLoad images
        if(get_option('site_lazyload_switcher')) {
            $acgcid = get_cat_by_template('acg','term_id');
            if($acgcid==$cat || cat_is_ancestor_of($acgcid, $cat)) {
    ?>
                const worker = new Worker('<?php echo custom_cdn_src(0,1); ?>/js/worker.js');
                const getAverageRGB = function(imgEl){var blockSize=5,defaultRGB={r:255,g:255,b:255},canvas=document.createElement('canvas'),context=canvas.getContext&&canvas.getContext('2d'),data,width,height,i=-4,length,rgb={r:0,g:0,b:0},count=0;if(!context){return defaultRGB}height=canvas.height=imgEl.naturalHeight||imgEl.offsetHeight||imgEl.height;width=canvas.width=imgEl.naturalWidth||imgEl.offsetWidth||imgEl.width;context.drawImage(imgEl,0,0);try{data=context.getImageData(0,0,width,height)}catch(e){return defaultRGB}length=data.data.length;while((i+=blockSize*4)<length){++count;rgb.r+=data.data[i];rgb.g+=data.data[i+1];rgb.b+=data.data[i+2]}rgb.r=~~(rgb.r/count);rgb.g=~~(rgb.g/count);rgb.b=~~(rgb.b/count);return rgb},
                    setupBlurColor = function(img, tarEl) {
                        if (!tarEl) return;
                        if (img instanceof HTMLImageElement) {
                            const rgb = getAverageRGB(img);
                            const rgba = rgb['r']+' '+rgb['g']+' '+rgb['b']+' / 50%';
                            tarEl.setAttribute('style','background:rgb('+rgba+')');
                            return;
                        }
                        worker.postMessage({
                            img: img,  // HTMLImageElement object could not be cloned on 'Worker'
                        });
                        worker.onmessage = function(event) {
                            const { url: objectURL } = event.data;
                            let tempimg = new Image();
                            tempimg.src = objectURL;
                            tempimg.setAttribute('crossorigin', 'Anonymous');
                            const rgb = getAverageRGB(tempimg);
                            const rgba = rgb['r']+' '+rgb['g']+' '+rgb['b']+' / 50%';
                            tarEl.setAttribute('style','background:rgb('+rgba+')');
                            // URL.revokeObjectURL(objectURL);
                        }
                        worker.onerror = function(event){
                            console.warn("ERROR: " + event.filename + " (" + event.lineno + "): " +
                                        event.message);
                        }
                    }
    <?php
            }
    ?>
            const lazyimg = document.querySelectorAll("body img[data-src]"),
                  loading = "<?php echo $img_cdn; ?>/images/loading_3_color_tp.png";
            var raf_available = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
            if(lazyimg[0]){
                var time_delay = 500,
                    msgObject = Object.create(null),
                    loadArray = Array.apply(null, lazyimg),  // [...lazyimg]
                    arrayChunk = function(array, process, context=null, args){
                        setTimeout(function(){
                            var item = array.shift();
                            // process.call(context, item);
                            process.apply(context, [item, args]);
                            // console.log(process)
                            if (array.length > 0){
                                setTimeout(arguments.callee, 10);
                            }
                        }, 100);
                    },
                    processImage = function(image, initial, imgList=null){
                        const updateList = Object.prototype.toString.call(imgList)=='[object Array]';
                        let datasrc = image.dataset.src;
                        initial ? image.src = loading : false; //pre-holder (datasrc only)
                        // console.debug('processing..', image, loadArray);
                        // !!!LONG TASK engaged!!!
                        if(image.getBoundingClientRect().top >= window.innerHeight || image.offsetParent==null || !image.src){
                            return;
                        }
                        // console.log('onsreen image: ',image);
                        image.src = datasrc; // 即时更新 image（设置后即可监听图片 onload 事件）
                        // 使用 onload 事件替代定时器或Promise，判断已设置真实 src 的图片加载完成后再执行后续操作
                        image.onload=function(){
                            if(this.getAttribute('src')===datasrc){
                                if(updateList) imgList.splice(imgList.indexOf(this), 1);  // 移除已加载图片数组（已赋值真实 src 情况下）
                                this.onload = null;  //fix bug: can't modify img-src(this.src = datasrc;)
                            }else{
                                this.removeAttribute('data-src'); // disable loading
                                // bug: can't modify img-src
                                this.src = datasrc;  // this.src will auto-fix [http://] prefix
                                time_delay = 3*time_delay;  //increase delay (decrease request)
                                console.log(time_delay);
                            }
                            <?php
                                if($acgcid==$cat || cat_is_ancestor_of($acgcid, $cat)){
                                    echo 'setupBlurColor(this, getParByCls(this, "inbox"));'; //this.parentElement.parentElement
                                }
                            ?>
                        }
                        // handle loading-err images image.onerror=()=>this.src=loading;
                        image.onerror=function(){
                            if(updateList) imgList.splice(imgList.indexOf(this), 1);  // 移除错误图片数组
                            this.removeAttribute('src');
                            this.removeAttribute('data-src'); // disable loading
                            if(!this.dataset.err){
                                this.setAttribute('alt','图片请求出现问题');
                                this.id = 'err';
                            }
                        }
                    },
                    autoLoad = function(imgLoadArr, initial=false){
                        if(Object.prototype.toString.call(imgLoadArr)=='[object Array]'){
                            let arrLen = imgLoadArr.length;
                            for(let i=0;i<arrLen;i++){
                                new Promise(function(resolve,reject){
                                    resolve(imgLoadArr);
                                }).then(function(res){
                                    processImage(res[i], initial, res); //imgLoadArr[i]
                                }).catch(function(err){
                                    console.log(err);
                                });
                            }
                            return;
                        };
                        // single image: updateList === loadArray but imgLoadArr(concated array)
                        processImage(imgLoadArr, initial, loadArray);  // imgLoadArr
                    },
                    scrollLoad = closure_throttle((e)=>{
                        if(loadArray.length<=0){
                            console.log(Object.assign(msgObject, {msg:'lazyload done', code:200}), loadArray);
                            window.removeEventListener('scroll', scrollForRemove, true);
                            return;
                        };
                        // autoLoad(loadArray);
                        arrayChunk(loadArray.concat(), autoLoad);
                        // console.log('throttling..',loadArray);
                    }, time_delay),
                    scrollForRemove = function(event){
                        let e = event || window.event,
                            t = e.target || e.srcElement;
                        if(t!==document) return;
                        // requestAnimationFrame support
                        raf_available ? window.requestAnimationFrame(scrollLoad) : scrollLoad();
                    };
                // autoLoad(loadArray, true);
                arrayChunk(loadArray.concat(), autoLoad, this, true); // will NOT change origin chunk array(lose effect)
                window.addEventListener('scroll', scrollForRemove, true);
            }
    <?php
        };
        if(get_option('site_async_switcher') && !is_single()){
    ?>
            function load_ajax_posts(t,type,limit,callback,action=false,url=false,params=false){
                const type_acg = "acg",
                      dis_class = "disabled",
                      load_done = type===type_acg ? "" : "已加载全部";
                let tp = t.parentElement,
                    tpp = tp.parentElement,
                    cid = parseInt(t.dataset.cid),
                    years = parseInt(t.dataset.year), // add-opts for archive
                    loads = parseInt(t.dataset.load),
                    counts = parseInt(t.dataset.counts) || 0,
                    clicks = parseInt(t.dataset.click) || 0;
                // console.log(loads, counts);
                if(loads >= counts){
                    t.innerText = load_done;
                    t.classList.add(dis_class);  // add-opts for weblog
                    tpp.classList.add(dis_class);
                    return;
                }
                clicks++;
                t.innerText = type===type_acg ? "Loading.." : "加载中..";
                t.classList.add('loading', dis_class);  // add-opts archive (disable click)
                t.setAttribute('data-click', clicks);
                url = url ? url : "<?php echo admin_url('admin-ajax.php'); ?>";
                params = params ? params : parse_ajax_parameter({
                    "action": action || "ajaxGetPosts",
                    "key": years, // add-opts for archive params
                    "cid": cid || 0,
                    "limit": limit,
                    "offset": limit*clicks,
                    "type": type,
                    _ajax_nonce: t.dataset.nonce,
                }, true);
                send_ajax_request("GET", url, params, function(res){
                        t.innerText = type===type_acg ? "" : "加载更多";
                        t.classList.remove('loading', dis_class);  // add-opts for archive (enable click)
                        var posts_array = JSON.parse(res),
                            posts_count = posts_array.length,
                            loads_count = loads + posts_count,
                            load_box = tp, //t.parentElement.parentElement.parentElement;
                            last_offset = 0;
                        // console.log(loads, posts_count)
                        switch(type){
                            case type_acg:
                                load_box = getParByCls(t, 'loadbox');
                                break;
                            case 'archive':
                                load_box = archive_tree.querySelector('.list_'+years);
                                last_offset = load_box.lastChild.offsetTop;
                                break;
                            default:
                                break;
                        }
                        loads_count>=counts ? t.setAttribute('data-load', counts) :  t.setAttribute('data-load', loads_count);  // update current loaded(limit judge)
                        if(callback&&typeof callback === 'function') {
                            if(posts_count>=1) callback(posts_array, load_box, last_offset);
                        }
                        // compare updated loads
                        if(parseInt(t.dataset.load) >= counts){
                            t.classList.add(dis_class); // for archive
                            tpp.classList.add(dis_class); // for weblog/acg
                            t.innerText = load_done;
                        }
                    }, function(err){
                        t.innerText = err+' err occured';
                    }
                );
            }
    <?php
        }
    ?>
</script>
<?php
    if(get_option('site_video_capture_switcher')){
        $ffmpeg_sw_gif = get_option('site_video_capture_gif');
?>
        <style>
            video{object-fit: initial;}
            .video_preview_hide:before,.video_preview_hide .preview_bg{content:"";display:none}
            .video_previews:before{content:'';width:100%!important;height:52%;backdrop-filter:blur(15px);position:absolute!important;top:0!important;left:0!important;z-index:1;background:-webkit-linear-gradient(90deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);background:linear-gradient(0deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);}
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
                for(let i=0,vdosLen=videos.length;i<vdosLen;i++){
                    let video = videos[i];
                    if(video.autoplay) break;
                    let video_src = video.src,
                        video_box = video.parentElement,
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
                                    let percentage = Number((Math.round(video_offset/video_width*10000)/100).toFixed(0)),
                                        progressOffset = -100+percentage;
                                    preview_bg.style.backgroundPosition = percentage+"% 0%";
                                    preview_pg.style.transform = 'translateX('+progressOffset+'%)';
                                    // console.log(percentage);
                                    percentage>=100 ? preview_pg.classList.add('pause_move') : preview_pg.classList.remove('pause_move');
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
            };
        </script>
<?php
    }
    // $cat = $cat ? $cat : get_page_cat_id(current_slug());  //rewrite cat to cid (var cat for require php)
    unset($lazysrc, $cat);  //release current file.php global variables
    // require_once(TEMPLATEPATH. '/foot.php');
?>