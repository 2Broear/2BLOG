<?php
/*
    Template Name: 笔记模板
    Template Post Type: post, notes
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/n.css?v=<?php echo get_theme_info('Version'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/highlight/agate.m.css" />
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/fancybox.css" />
    <style>
        .win-top em.digital_mask{
            background-size: 2px 2px!important;
        }
        .bg h1{
            background: none;
        }
        .bg h1 a{
            background: linear-gradient(var(--theme-color), var(--theme-color)) no-repeat left 97%/0 30%;
            background-size: 100% 30%;
            color: inherit;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background: url(<?php echo get_postimg(0,$post->ID,true); ?>) center center /cover;">
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
            <h1><a href="javascript:;" rel="nofollow"><?php the_title(); ?></a><!--<span></span>--></h1>
        </div>
        <div class="content-all-windows">
            <div class="win-nav-content">
                <div class="win-content">
                    <article class="news-article-container">
                        <div class="infos">
                            <span id="classify">
                                <?php echo get_tag_list($post->ID, 5); ?>
                            </span>
                            <span id="view"><?php $cat=get_the_ID();setPostViews($cat);echo getPostViews($cat); ?>°C </span>
                            <span id="date"><i class="icom"></i> <?php the_time('d-m-Y'); ?> </span>
                            <span id="slider"></span>
                        </div>
                        <sup>最近更新于：<?php echo $post->post_modified; ?></sup>
                        <div class="content">
                            <?php the_content();//print_r(get_post_parent($post->ID)); ?>
                        </div>
                        <br />
                        <?php dual_data_comments();  //DO NOT INCLUDE AFTER CALLING comments_template, cause fatal error,called twice?>
                    </article>
                </div>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
    <?php require_once(TEMPLATEPATH. '/foot.php'); ?>
    <!-- plugins -->
    <script>
        const codeblock = document.querySelectorAll("pre code");
        if(codeblock.length>=1){
			new Promise(function(resolve,reject){
        	    dynamicLoad('<?php custom_cdn_src(); ?>/js/highlight/highlight.pack.js', function(){
        	        hljs ? resolve(hljs) : reject('highlight err.');
        	    });
			}).then(function(res){
                // initilize highlight.js
                res.initHighlightingOnLoad();
                // code copy support
                const content = document.querySelector('.content'),
                      text = document.createElement('textarea'),
                      codeLoop = function(callback){
                          for(let i=0,codeLen=codeblock.length;i<codeLen;i++){
                              if(callback&&typeof callback=='function') callback(i); //callback.apply(this, arguments);
                          }
                      },
                      copied = 'copied';
                codeLoop(function(i){
                    const each_code = codeblock[i];
                    each_code.innerHTML = "<ul><li>" +each_code.innerHTML.replace(/\n/g,"\n</li><li>") +"\n</li></ul><span class='copy_btn' title='复制当前代码块'></span>"; //each_code.innerHTML.replace(/\n/g,"\n") +"\n<span class='copy_btn'></span>"
                });
                text.classList.add('copy_area');
                document.body.appendChild(text);
                bindEventClick(content, 'copy_btn', function(t){
                    const tp = t.parentNode;
                    if(tp.classList.contains(copied)) return;
                    codeLoop(function(i){
                        codeblock[i].classList.remove(copied);
                    });
                    tp.classList.add(copied);
                    text.value = tp.querySelector('ul').innerText.replace(/\n\n/g,"\n");
                    text.select(); //.setSelectionRange(0,text.value.length);
                    document.execCommand('copy');
                    // console.log(text.value);
                });
			}).catch(function(err){
			    console.log(err);
			});
        }
    </script>
</body></html>