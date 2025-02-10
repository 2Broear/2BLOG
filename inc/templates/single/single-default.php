<?php
/*
    Template Name: 笔记模板
    Template Post Type: post, notes
*/
    global $src_cdn, $img_cdn;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/n.css?1v=<?php echo get_theme_info(); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/highlight/agate.m.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/fancybox.css" />
    <style>
	    figure .wp-block-gallery figcaption{max-width:66%;}
        figure{
            text-align: left;
            /*float:left;*/
            /*float: right;*/
            /*margin-left: 25px;*/
        }
	    .in_dex li.child{margin-left: 15px!important;}
	    .bg h1 a{
	        animation-duration: 1.5s;
	        /*animation-delay: .5s;*/
	    }
	    figure.wp-block-gallery > figure{
	        min-height: 222px;
	    }
	    figure.wp-block-gallery{
	        margin: 0;
	        /*max-width: 88%;*/
	        justify-content: start;
	    }
	    /***  extras  ***/
	    .win-content {
	        background: linear-gradient(90deg, var(--mirror-start) 0, var(--mirror-end));
            background: -webkit-linear-gradient(0deg, var(--mirror-start) 0, var(--mirror-end));
	        background: rgb(255 255 255 / 95%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 2px solid white;
	    }
	    body.dark .win-content {
	        border-color: transparent;
	    }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" style="background: url(<?php $pid=$post->ID;echo get_postimg(0,$pid,true); ?>) center center /cover;">
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
            <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
            <h1><a href="javascript:;" rel="nofollow"><?php the_title(); ?></a><!--<span></span>--></h1>
        </div>
        <div class="content-all-windows">
            <div class="win-nav-content">
                <div class="win-content">
                    <article class="news-article-container">
                        <div class="infos">
                            <span id="classify">
                                <?php echo get_tag_list($pid, 5); ?>
                            </span>
                            <span id="view"><?php setPostViews($pid);echo getPostViews($pid); ?>°C </span>
                            <span id="date"><i class="icom"></i> <?php the_time('d-m-Y'); ?> </span>
                            <?php if(get_option('site_not_ai_switcher')) global $img_cdn;echo '<span><a href="//notbyai.fyi" target="_blank" rel="nofollow"><img src="'.$img_cdn.'/images/svg/not-by-ai.svg" alt="notbyai" style="height: 15px;filter:invert(0.5);margin: auto 5px;border-radius: 0;"></a></span>'; ?>
                            <span id="slider"></span>
                        </div>
                        <sup>最近更新于：<?php echo $post->post_modified; ?></sup>
                        <div class="content">
                            <?php 
                                the_content();
                                $ps = get_post_meta($pid, "post_feeling", true);
                                if($ps){
                                    $weblog = get_cat_by_template('weblog');
                                    $download = get_cat_by_template('download');
                                    if(in_category($weblog->slug, $pid) || in_category($download->slug, $pid)) echo do_shortcode('[custom_title title="其他" statu]').'<p>'.$ps.'</p>';
                                }
                            ?>
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
    <?php get_foot(); ?>
    <!-- plugins -->
    <script>
        const codeblock = document.querySelectorAll("pre code");
        if(codeblock.length>=1){
			new Promise(function(resolve,reject){
        	    dynamicLoad('<?php echo $src_cdn; ?>/js/highlight/highlight.pack.js', function(){
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