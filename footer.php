<?php
    global $lazysrc, $cat, $src_cdn, $img_cdn;
    //计算版权时间，直接在footer使用会引发没有内容的notes子分类无法显示
    function get_copyright(){
        $year = gmdate('Y', time() + 3600*8);//date('Y');
        $begain = get_option('site_begain');
        if ($begain && $begain<$year) echo $begain . "-";
        return $year;
    }
?>
<div class="footer-all">
    <div class="footer-detector" id="end-news-all">
        <span id="end-end">END</span>
        <span id="end-obj" class="magnetics" data-magnet-scale="1" data-magnet-step=""><?php echo(current_slug(true, $cat, $post)); ?></span>
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
                    if ($third_cmt=='Valine') {    // 全站加载
                ?>
                        <script src="<?php echo $src_cdn;//custom_cdn_src(0,1);// ?>/js/Valine/Valine.m.js?v=<?php echo get_theme_info(); ?>"></script>
                <?php
                        if (!$baas) echo '<script src="' . $src_cdn . '/js/leancloud/av-min.js?v=footcall"></script>';
                	    $root_path = custom_cdn_src('default', true);
            	        $plugin_path = $root_path.'/plugin';
                	    $cf_turnstile_valine = get_cf_turnstile($third_cmt);
            	        if ($cf_turnstile_valine) the_cf_turnstile();
                ?>
                        <script type="text/javascript">
                            // asyncLoad('', function() {
                            // });
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
                            // 	recordIP: true,  // ad case
                            	placeholder: '快来玩右下角的“涂鸦画板”！',
                            	<?php
                        	        $comments_blackList = get_option("site_comment_blacklists");
                            	    echo $cf_turnstile_valine ? 'defender: `<div id="widget-container"></div>`,' . PHP_EOL : 'defender: "",' . PHP_EOL; // class="cf-turnstile" data-sitekey="'. get_option('site_cloudflare_turnstile_sitekey') . '" data-language="cn" data-theme="' . theme_mode(true) . '" data-size="flexible" data-callback="onTurnstileSuccess" data-error-callback="onTurnstileError" data-expired-callback="onTurnstileExpired"
                            	    echo get_option('site_cdn_switcher') ? 'imgCdn: "'.$img_cdn.'",' . PHP_EOL . ' srcCdn: "'.$src_cdn.'",' . PHP_EOL . ' apiCdn: "'.$plugin_path.'",' . PHP_EOL : 'rootPath: "'.$root_path.'",' . PHP_EOL;
                            	    echo get_option('site_lazyload_switcher') ? 'lazyLoad: true,' . PHP_EOL : 'lazyLoad: false,' . PHP_EOL;
                        	        echo get_option("site_wpwx_notify_switcher") ? 'wxNotify: true,' . PHP_EOL : 'wxNotify: false,' . PHP_EOL;
                        	        echo $comments_blackList ? 'blackList: "' . urlencode(trim($comments_blackList)) . '",' . PHP_EOL : 'blackList: "",' . PHP_EOL;
                        	        echo get_option("site_comment_blockoutside") ? 'ipForbidden: true,' . PHP_EOL : 'ipForbidden: false,' . PHP_EOL;
                            	?>
                            	pushPlus: <?php echo get_option('site_comment_pushplus') ? "'".get_option('site_comment_pushplus')."'" : 'false'; ?>,
                            	serverChan: <?php echo get_option('site_comment_serverchan') ? "'".get_option('site_comment_serverchan')."'" : 'false'; ?>,
                            	adminAjax: '<?php echo admin_url('admin-ajax.php'); ?>',
                            	adminMd5: '<?php echo md5(strtolower(get_bloginfo('admin_email'))); ?>',
                            	avatarCdn: '<?php echo get_option("site_avatar_mirror").'avatar/'; ?>',
                            	avatarApi: '<?php echo $plugin_path.'/gravatar.php?jump=0&email='; ?>',
                            	posterImg: '<?php echo get_postimg(0, $post->ID, true); ?>',
                            });
                            
                            // reply at current floor
                            const vcomments = document.querySelector("#vcomments");
                            if(vcomments) {
                                const vwraps = vcomments.querySelectorAll(".vwrap"),
                                      vsubmit = vcomments.querySelector(".vsubmit"),
                                      origin_wrap = vwraps[0]; // origin_bak = document.importNode(origin_wrap, true),
                                // origin_wrap.querySelector("textarea").setAttribute('autofocus',true);
                                bindEventClick(vcomments, 'vat', function(t) {
                                <?php
                                    if ($cf_turnstile_valine) {
                                ?>
                                    if (!vsubmit.dataset.token) {
                                        alert('等待 turnstile 验证...');
                                        // vcomments.querySelector("textarea").focus();
                                        return;
                                    }
                                <?php
                                    }
                                ?>
                                    // document.querySelector('.cf-turnstile').remove(); //classList.add('hide');
                                    const adopt_node = document.adoptNode(origin_wrap);
                                    if (!t.classList.contains('reply')) {
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
                                    } else {
                                        t.classList.remove('reply');
                                        t.innerText = "回复";
                                        vcomments.insertBefore(adopt_node, vcomments.querySelector(".vinfo"));  // reverse origin_bak
                                    }
                                });
                            }
                        </script>
                <?php
                    } elseif ($third_cmt=='Twikoo') {
                        $third_twikoo = get_option('site_twikoo_version');
                ?>
                        <script src="<?php echo $third_twikoo ? $third_twikoo : $src_cdn . '/js/Twikoo/twikoo.min.js'; ?>"></script>
                        <script>
                            twikoo.init({
                                envId: '<?php echo $twikoo_envid = get_option('site_twikoo_envid'); ?>',
                                el: '#tcomment',
                            });
                            const comment_count = document.querySelectorAll('.valine-comment-count'),
                                  comments_list = document.querySelector('#comments');
                            if (comment_count) {
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
                            if (comments_list) {
                                twikoo.getRecentComments({
                                    envId: "<?php echo $twikoo_envid; ?>", // 环境 ID
                                    pageSize: <?php echo $post_per; ?>, // 获取多少条，默认：5，最大：100
                                    includeReply: true // 是否包括最新回复，默认：true
                                })
                                .then(function (res) {
                                    for(let i=0,resLen=res.length;i<resLen;i++){
                                        // console.log(res[i]);
                                        let each = res[i];
                                        comments_list.innerHTML += `<a href="${each.url}#${each.id}" target="_blank" rel="nofollow"><em title="${each.commentText}">${each.nick}：${each.commentText}</em></a>`;
                                    }
                                })
                                .catch(function (err) {
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
                    } else {
                ?>
                        <!--<script src="<?php echo custom_cdn_src(0,1);//$src_cdn;// ?>/js/Valine/valine.js?v=<?php echo get_theme_info(); ?>"></script>-->
                <?php
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
                    <a href="javascript:void(0)" target="_self" rel="nofollow" aria-label="wechat">
                        <span class="contact-icons" id="icon-wechat">
                            <i class="icom"></i>
                        </span>
                        <span class="preview"><?php
                            echo '<img src="'.get_option('site_contact_wechat').'" alt="wechat" />';?>
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
                    <a href="<?php echo get_option('site_contact_music') ?>" target="_blank" rel="nofollow" aria-label="music">
                        <span class="contact-icons" id="icon-netease">
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
                  <?php echo '<img class="magnetic" src="'.$img_cdn.'/images/svg/XTy_.svg" style="max-width:66px" alt="XTY Design" />'; //'.$lazysrc.' ?>
              </li>
              </ul>
              <ul class="friend_links">
                <h2>博友圈</h2>
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
                if($moe_beian) echo '<li id="etc"><a href="//icp.gov.moe/?keyword='.$moe_beian_num.'" target="_blank" rel="nofollow"><img class="magnetic" data-magnet-scale="1" data-magnet-step="" src="//icp.gov.moe/images/ico64.png" alt="moe_beian" title="异次元之旅-跃迁" style="height: 16px;"> 萌ICP备'.$moe_beian_num.'号</a></li>';
            ?>
            <p id="supports">
                <?php 
                    if(get_option('site_monitor_switcher')) echo '<script async type="text/javascript" src="'.get_option('site_monitor').'"></script>';
                    if(get_option('site_chat_switcher')) echo '<a href="'.get_option("site_chat").'" target="_blank" title="Chat Online" rel="nofollow"><img src="'.$img_cdn.'/images/svg/tidio.svg" alt="tidio" style="height: 16px;opacity:.88;"></a>'; //'.$lazysrc.'
                    if(get_option('site_foreverblog_switcher')){
                        echo '<a href="'.get_option('site_foreverblog').'" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/foreverblog.svg" class="magnetic" data-magnet-scale="1" data-magnet-step="" alt="foreverblog" style="height: 16px;"></a>';
                        if(get_option('site_foreverblog_wormhole')){
                            // $theme = array_key_exists('theme_mode',$_COOKIE) ? $_COOKIE['theme_mode'] : false;
                            echo '<a href="//www.foreverblog.cn/go.html" target="_blank" rel="nofollow"><em class="warmhole magnetic" data-magnet-scale="1" data-magnet-step="" style="background:url('.$img_cdn.'/images/wormhole_4_tp_ez.gif) no-repeat center center /cover" title="穿梭虫洞-随机访问十年之约友链博客"></em></a>';
                        }
                    }
                    if($moe_beian && get_option('site_moe_beian_travel')) echo '<a href="//travel.moe/go.html" target="_blank" rel="nofollow"><img src="//moe.one/upload/attach/202307/89_8TEYVRKUCP79XHG.png" class="magnetic" data-magnet-scale="1" data-magnet-step="" alt="moe_beian" title="异次元之旅-跃迁" style=""></a>';
                    $server = get_option('site_server_side');
                    if($server) echo '<a href="javascript:;" rel="nofollow"><img src="'.$server.'" style="height: 12px;" alt="server"></a>'; //'.$lazysrc.'
                    // echo '<a href="javascript:;" target="" rel="nofollow"><img src="https://waf-ce.chaitin.cn/images/safeline.svg" alt="deepal-blue" style="height: 18px;opacity: 0.5;"></a>';
                    echo '<a href="javascript:;" target="" rel="nofollow"><img class="magnetic" data-magnet-scale="1" data-magnet-step="" src="'.$img_cdn.'/images/svg/deepal-blue.svg" alt="deepal-blue" style="height: 14px;filter:invert(0.5);"></a>';
                    // if(get_option('site_not_ai_switcher')) echo '<a href="//notbyai.fyi" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/not-by-ai.svg" alt="notbyai" style="height: 15px;filter:invert(0.5);"></a>';
                    if(get_option('site_construction_switcher')) echo '<style>@keyframes alarmLamp_bar_before{0%{opacity:.15;}2%{opacity:1;}4%{opacity:.15;}6%{opacity:1;}8%{opacity:.15;}10%{opacity:1;}12%{opacity:.15;}14%{opacity:1;}16%{opacity:.15;}18%{opacity:1;}20%{opacity:.15;}22%{opacity:1;}24%{opacity:.15;}26%{opacity:1;}28%{opacity:.15;}50%{opacity:.15;}60%{opacity:1;}61%{opacity:.15;}62%{opacity:1;}70%{opacity:.15;}80%{opacity:1;}81%{opacity:.15;}82%{opacity:1;}90%{opacity:.15;}100%{opacity:1;}}@keyframes alarmLamp_bar_after{0%{opacity:.15;}28%{opacity:.15;}30%{opacity:1;}32%{opacity:.15;}34%{opacity:1;}36%{opacity:.15;}38%{opacity:1;}39%{opacity:.15;}40%{opacity:1;}42%{opacity:.15;}44%{opacity:1;}46%{opacity:.15;}48%{opacity:1;}50%{opacity:.15;}52%{opacity:1;}54%{opacity:.15;}56%{opacity:1;}58%{opacity:.15;}60%{opacity:.15;}70%{opacity:1;}71%{opacity:.15;}72%{opacity:1;}80%{opacity:.15;}90%{opacity:1;}91%{opacity:.15;}92%{opacity:1;}100%{opacity:.15;}}@keyframes alarmLamp_spotlight{0%{filter:blur(0px);}28%{filter:blur(0px);}50%{filter:blur(0px);}60%{background:red;filter:blur(15px);}62%{background:red;filter:blur(15px);}70%{background:blue;filter:blur(15px);}72%{background:blue;filter:blur(15px);}80%{background:red;filter:blur(15px);}82%{background:red;filter:blur(15px);}90%{background:blue;filter:blur(15px);}92%{background:blue;filter:blur(15px);}100%{filter:blur(0px);}}.alarm_lamp span#spot::before,.alarm_lamp span#spot::after{content:none;}.alarm_lamp span#spot,.alarm_lamp span#bar::before,.alarm_lamp span#bar::after{content:"";width:33%;height:78%;background:red;box-shadow:rgb(255 0 0 / 80%) 0 0 20px 0px;position:absolute;top:50%;left:50%;transform:translate(0%,-50%);-webkit-transform:translate(0%,-50%);animation-duration:3s;animation-delay:0s;animation-timing-function:step-end;animation-iteration-count:infinite;animation-direction:normal;}.alarm_lamp span#bar::before{left:0%;animation-name:alarmLamp_bar_before;-webkit-animation-name:alarmLamp_bar_before;}.alarm_lamp span#bar::after{left:auto;right:0%;background:blue;box-shadow:rgb(0 0 255 / 80%) 0 0 20px 0px;animation-name:alarmLamp_bar_after;-webkit-animation-name:alarmLamp_bar_after;}.alarm_lamp{display:inline-block;padding:0 2px!important;box-sizing:border-box;position:relative;vertical-align:middle;border:1px solid transparent;}.alarm_lamp span{height:100%;display:block;position:inherit;}.alarm_lamp span#bar{width:100%;}.alarm_lamp span#spot{max-width:32%;background:white;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);box-shadow:rgb(255 255 255 / 100%) 0 0 20px 0px;animation-name:alarmLamp_spotlight;-webkit-animation-name:alarmLamp_spotlight;}</style><a href="javascript:void(0);" class="alarm_lamp" style="width:58px;height:12px;width: 20%;height: 5%;position: fixed;z-index: 999;top: 90%;left: 50%;transform: translate(-50%, -50%);opacity: .1;" title="站点正处施工中.."><span id="bar"></span><span id="spot"></span></a>';
                ?>
            </p>
          </ul>
          <ul style="text-align:right">
              <li id="feed"><a href="<?php bloginfo('rss2_url'); ?>" target="_blank" style="/*font-weight:bold;*/">RSS</a></li>
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
              <p style="margin:auto;opacity:.75;font-size:12px;font-style:italic"> WordPress Theme <a href="//github.com/2Broear/2BLOG" style="color:inherit;" target="_blank"><b>2BLOG</b></a> open sourced in 2022 </p>
          </ul>
        </span>
      </div>
    </div>
    <div class="functions-tool">
        <div class="inside-functions">
            <div class="box magnetics">
                <?php
                    $fixed_theme = get_option('site_darkmode_fixed');
                    $theme_class = '';
                    $theme_title = '主题切换';
                    $theme_titles = '点击根据时段自动设置主题';
                    $theme_event = 'darkmode()';
                    $theme_events = 'automode()';
                    if ($fixed_theme) {
                        $static_text = '已禁用（已启用 ' . $fixed_theme . ' 常驻）';
                        $theme_class = ' disabled';
                        $theme_title = $theme_title . $static_text;
                        $theme_titles = $theme_titles . $static_text;
                        $theme_event = $theme_events = '';
                    }
                ?>
                <div class="dark<?php echo $theme_class; ?> magnetic" title="<?php echo $theme_title; ?>" onclick="<?php echo $theme_event; ?>" data-magnet-scale="1.05" data-magnet-step="0.5">
                    <i class="icom icon-moon"></i>
                    <!--<svg id="scale"><circle cx="20" cy="20" r="20" /></svg>-->
                </div>
                <div class="top magnetic" title="返回顶部" data-magnet-scale="1.05" data-magnet-step="0.5"><em>顶</em></div>
                <div class="bottom magnetic" title="跳至顶部" data-magnet-scale="1.05" data-magnet-step="0.5"><em>底</em></div>
                <div class="pagePer<?php echo $theme_class; ?> magnetic" title="<?php echo $theme_titles; ?>" onclick="<?php echo $theme_events; ?>" data-magnet-scale="1.1" data-magnet-step="0.75" style="transform: translate(-50%,-50%);">
                    <strong data-percent="0"></strong>
                    <i style>
                        <span class="wave"></span>
                    </i>
                </div>
            </div>
        </div>
    </div>
</div>
<svg style="display: none;">
    <defs>
        <filter id="x" height="500%">
            <feTurbulence baseFrequency="0.01 0.02" numOctaves="2" result="t0"></feTurbulence>
            <feDisplacementMap in="SourceGraphic" in2="t0" result="d0" scale="5"></feDisplacementMap>
            <feComposite in="SourceGraphic" in2="d0" operator="atop" result="0"></feComposite>
            <feTurbulence baseFrequency="1" numOctaves="2" result="t1"></feTurbulence>
            <feDisplacementMap in="0" in2="t1" result="d1" scale="2"></feDisplacementMap>
            <feComposite in="0" in2="d1" operator="atop" result="1"></feComposite>
            <feOffset dx="-3" dy="-3" in="1"></feOffset>
        </filter>
        <!--<filter id="b2rac0c6h">-->
        <!--    <feGaussianBlur in="SourceGraphic" stdDeviation="1.2" result="blurred_source"></feGaussianBlur>-->
        <!--    <feImage href="data:image/png;base64,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" x="0" y="0" width="546" height="58" result="displacement_map"></feImage><feDisplacementMap in="blurred_source" in2="displacement_map" scale="71.52923222419567" xChannelSelector="R" yChannelSelector="G" result="displaced"></feDisplacementMap><feColorMatrix in="displaced" type="saturate" values="14" result="displaced_saturated"></feColorMatrix><feImage href="data:image/png;base64,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" x="0" y="0" width="546" height="58" result="specular_layer"></feImage><feComposite in="displaced_saturated" in2="specular_layer" operator="in" result="specular_saturated"></feComposite><feComponentTransfer in="specular_layer" result="specular_faded"><feFuncA type="linear" slope="0.2"></feFuncA></feComponentTransfer><feBlend in="specular_saturated" in2="displaced" mode="normal" result="withSaturation"></feBlend><feBlend in="specular_faded" in2="withSaturation" mode="normal"></feBlend>-->
        <!--</filter>-->
    </defs>
</svg>
<!--<div style="position: absolute;inset: 0px;z-index: -1;backdrop-filter: url(#b2rac0c6h);"></div>-->