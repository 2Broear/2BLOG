
<div class="footer-all">
    <div class="footer-detector" id="end-news-all">
        <span id="end-end">END</span>
        <span id="end-obj"><?php echo(current_slug(true, $cat, $post)); ?></span>
    </div>
    <div class="container">
      <div id="footer-support-board">
        <p id="supports-txt"><?php echo '<q>'.get_option('site_support','Art Design | Coding | Documents | Social Media | Tech Support | 2broear.com').'</q><b>'.get_option('site_nick').'</b>'; ?></p>
      </div>
      <div id="footer-contact-way">
        <ul class="footer-ul">
          <div class="footer-contact-left">
            <div class="footer-left flexboxes">
              <ul class="footer-recommend">
                <h2>近期文章</h2>
                <div class="recently">
                    <?php
                        $cat_id = get_option('site_bottom_recent_cid');
                        if($cat_id){
                            $query_array = array('cat' => $cat_id, 'meta_key' => 'post_orderby', 'posts_per_page' => get_option('site_per_posts', get_option('posts_per_page')),
                                'orderby' => array(
                                    'meta_value_num' => 'DESC',
                                    'date' => 'DESC',
                                    'modified' => 'DESC',
                                )
                            );
                        }else{
                            $query_array = array('cat' => $cat_id, 'posts_per_page' => get_option('site_per_posts', get_option('posts_per_page')), 'order' => 'DESC', 'orderby' => 'date');
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
                                        if($post_orderby>1) echo '<sup id="new">Top</sup>';
                                        if($post->comment_count>=10) echo '<sup id="hot">Hot</sup>';
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
                    $comment_sw = get_option('site_comment_switcher');
                    if($comment_sw){    // 全站加载
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
                            	// avosSdk: '<?php //echo get_option('site_leancloud_sdk') ?>',
                            	// avatar: '<?php //echo get_option('avatar_default','retro') ?>',
                            	listSize: '<?php echo get_option('site_per_posts', 5) ?>',
                            	notify: false,
                            	verify: false,
                            	visitor: false,
                            	recordIP: false,
                            	pushPlus: '<?php echo get_option('site_comment_pushplus') ?>',
                            	serverChan: '<?php echo get_option('site_comment_serverchan') ?>',
                            	// qmsgChan: '<?php //echo get_option('site_comment_qmsgchan') ?>',
                            	rootPath: '<?php echo get_bloginfo('template_directory') ?>',
                            	adminMd5: '<?php echo md5(get_bloginfo('admin_email')) ?>',
                            	avatarCdn: '<?php echo get_option("site_avatar_mirror") ?>avatar/',
                            	posterImg: '<?php echo get_postimg(); ?>',
                            	wxNotify: '<?php echo get_option("site_wpwx_notify_switcher") ?>',
                            	placeholder: '快来玩右下角的“涂鸦画板”！'
                            });
                            // window.onload=function(){
                            //     if(document.querySelector('#vcomments')){
                                    // $("#vcomments").on('click','span.vat',function(){
                                    //     let _t=$(this),
                                    //         rp=$("div.vwrap");
                                    //     _t.attr("id") ? (_t.text("回复").removeAttr("id"),$("div.vinfo").before(rp)) : ($("span.vat").not(_t).text("回复").removeAttr("id"),_t.text("取消回复").attr("id","cancel").parent('div.vmeta').next("div.vcontent").after(rp));
                                    //     $('textarea#veditor').focus()
                                    // })
                            //     }
                            // };
                        </script>
                <?php
                    }else{
                        $comments = get_comments(
                            array(
                                'number' => get_option('site_per_posts', get_option('posts_per_page')), //get_option('posts_per_page')
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
                  <a href="<?php echo get_option('site_contact_weibo') ?>" target="_blank" rel="nofollow">
                    <span class="contact-icons" id="icon-weibo">
                      <i class="icom"></i>
                    </span>
                  </a>
                  <a href="<?php echo get_option('site_contact_music') ?>" target="_blank" rel="nofollow">
                    <span class="contact-icons" id="icon-netease">
                      <i class="BBFontIcons"></i>
                    </span>
                  </a>
                  <a href="javascript:void(0)" target="_blank" rel="nofollow">
                    <span class="contact-icons" id="icon-wechat">
                      <i class="icom"></i>
                    </span>
                    <span class="preview">
                        <img src="<?php echo get_option('site_contact_wechat') ?>" />
                    </span>
                  </a>
                  <a href="mailto:<?php echo get_option('site_contact_email') ?>" target="_blank" rel="nofollow">
                    <span class="contact-icons" id="icon-mail">
                      <i class="icom"></i>
                    </span>
                  </a>
                  <a href="<?php echo get_option('site_contact_bilibili') ?>" target="_blank" rel="nofollow">
                    <span class="contact-icons" id="icon-bilibili">
                      <i class="BBFontIcons"></i>
                    </span>
                  </a>
                <?php
                    $github = get_option('site_contact_github');
                    $steam = get_option('site_contact_steam');
                    $twitter = get_option('site_contact_twitter');
                    if($github){ ?>
                      <a href="<?php echo $github ?>" target="_blank" rel="nofollow">
                        <span class="contact-icons" id="icon-github">
                          <i class="icom"></i>
                        </span>
                      </a>
                <?php }if($twitter){ ?>
                      <a href="<?php echo $twitter ?>" target="_blank" rel="nofollow">
                        <span class="contact-icons" id="icon-twitter">
                          <i class="icom"></i>
                        </span>
                      </a>
                <?php };if($steam){ ?>
                      <a href="<?php echo $steam ?>" target="_blank" rel="nofollow">
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
                    <a id="chrome" href="https://www.google.cn/chrome/" target="_blank" rel="nofollow" title="Chrome大法好！">Chrome</a>/
                    <a id="edge" href="https://www.microsoft.com/zh-cn/edge" target="_blank" rel="nofollow" title="新版Edge也不错~">Edge</a></b>
                </li>
                <li class="PoweredBy2B">
                  <ins> XTyDesign </ins>
                  <img src="<?php custom_cdn_src('img'); ?>/images/svg/XTy_.svg" alt="XTY Design" /></li>
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
        	                echo '<a id="more" href="/'.get_template_bind_cat('category-2bfriends.php')->slug.'" title="更多" target="_blank">  更多 </a>';
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
            <?php $rights=get_option('site_copyright');if($rights) echo '<li id="cc"><a href="https://creativecommons.org/" style="opacity:.88" > '.$rights.' </a></li>'; ?>
            <li id="rights"><?php echo get_option('site_nick', get_bloginfo('name')); ?> 版权所有</li>
            <?php if(get_option('site_beian_switcher')) echo '<li id="etc">'.get_option('site_beian').'</li>'; ?>
            <p id="supports">
                <?php 
                    if(get_option('site_monitor_switcher')) echo '<script type="text/javascript" src="'.get_option('site_monitor').'"></script>';
                    if(get_option('site_chat_switcher')) echo '<a href="'.get_option("site_chat").'" target="_blank" title="Chat Online"><img src="'.custom_cdn_src('img',true).'/images/svg/tidio.svg" alt="tidio" style="height: 16px;opacity:.88;"></a>';
                    $server = get_option('site_server');
                    if($server&&$server!="已关闭") echo '<a href="javascript:void(0);"><img src="'.$server.'" style="height: 12px;"></a>';
                    if(get_option('site_foreverblog_wormhole')){
                        $warmhole_img = $_COOKIE['theme_mode']=='dark' ? custom_cdn_src('img',true).'/images/wormhole_2_tp.gif' : custom_cdn_src('img',true).'/images/wormhole_4_tp.gif';
                        echo '<a href="https://www.foreverblog.cn/go.html" target="_blank"><em class="warmhole" style="background:url('.custom_cdn_src('img',true).'/images/wormhole_4_tp.gif) no-repeat center center /cover" title="穿梭虫洞-随机访问十年之约友链博客"></em></a>';
                    }
                    // if(get_option('site_foreverblog_switcher'))
                    echo '<a href="'.get_option('site_foreverblog').'" target="_blank"><img src="'.custom_cdn_src('img',true).'/images/svg/foreverblog.svg" alt="foreverblog" style="height: 16px;"></a>';
                    // if($comment_sw || $baas) echo '<a href="https://leancloud.cn" target="_blank"><b style="color:#2b96e7" title="AVOS BAAS Support">LeanCloud</b></a>';
                ?>
            </p>
          </ul>
          <ul style="text-align:right">
              <li id="feed"><a href="<?php bloginfo('rss2_url'); ?>" target="_blank">RSS</a></li>
              <?php
                  if(get_option('site_map_switcher')) echo '<li id="sitemap"><a href="'.get_bloginfo('siteurl').'/sitemap.xml" target="_blank">站点地图</a></li>';
                  $bottom_nav_array = explode(',',get_option('site_bottom_nav'));
                  for($i=0;$i<count($bottom_nav_array);$i++){
                      $cat_slug = trim($bottom_nav_array[$i]);
                      $cat_term = get_category_by_slug($cat_slug);
                      if($cat_slug&&$cat_term){
                          echo '<li id="'.$cat_slug.'"><a href="'.get_category_link($cat_term->term_id).'" target="_blank">'.$cat_term->name.'</a></li>';
                      }
                  }
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
	window.onload=function(){
		NProgress.done();
	};
    function automode(){
        getCookie('theme_manual') ? setCookie('theme_manual',0,false) : false;  // disable manual mode
        let date = new Date(),
            hour = date.getHours(),
            min = date.getMinutes(),
            sec = date.getSeconds(),
            start = <?php echo get_option('site_darkmode_start',17); ?>,
            end = <?php echo get_option('site_darkmode_end',9); ?>;
        hour>=end&&hour<start || hour==end&&min>=0&&sec>=0 ? setCookie('theme_mode','light') : setCookie('theme_mode','dark');
        document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
    }
</script>
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