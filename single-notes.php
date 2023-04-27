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
	    .bg h1 a{
	        /*animation-delay: .5s;*/
	        animation-duration: 1.5s;
	    }
	    .in_dex li.child{margin-left: 15px!important;}
	    p.response.load:after{
	        animation-duration: .35s!important;
	        -webkit-animation-duration: .35s!important;
	    }
	    p.response.load:after,
	    p.response.done:after{
            animation: footerHot 1s step-end infinite normal;
            -webkit-animation: footerHot 1s step-end infinite normal;
	    }
	    p.response:after{
	        content: '';
            width: 3px;
            height: 20px;
            display: inline-block;
            background: currentColor;
            vertical-align: middle;
            margin: 0 0 2px 5px;
	        /*content: '|';*/
	    }
	    blockquote.chatGPT{
	        padding: 15px 15px 10px;
	        margin: 20px;
	        border-width: 3px;
            border-top-right-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
	        background: rgb(100 100 100 / 5%);
	        background: -webkit-linear-gradient(180deg,rgba(255, 255, 255, 0) -10%,rgb(100 100 100 / 5%) 100%);
	        background: linear-gradient(-90deg,rgba(255, 255, 255, 0) -10%,rgb(100 100 100 / 5%) 100%);
            /*box-shadow: rgb(0 0 0 / 5%) -20px 20px 20px;*/
	    }
	    blockquote.chatGPT p{
	        color: var(--preset-8)!important;
	    }
	    blockquote.chatGPT p.response{
	        font-size: var(--min-size);
	    }
	    blockquote.chatGPT p:first-child{
	        color: var(--preset-6)!important;
	    }
	    blockquote.chatGPT p:first-child span{
            border: 1px solid rgb(100 100 100 / 50%);
            padding: 0 4px;
            border-radius: 5px;
            font-size: 12px;
            vertical-align: top;
            margin-left: 3px;
            opacity: .75;
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
            <em class="digital_mask" style="background: url(<?php custom_cdn_src('img'); ?>/images/svg/digital_mask.svg)"></em>
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
                            <span id="view"><?php $cat=get_the_ID();setPostViews($cat);echo getPostViews($cat); ?>°C </span>
                            <span id="date"><i class="icom"></i> <?php the_time('d-m-Y'); ?> </span>
                            <span id="slider"></span>
                        </div>
                        <sup>最近更新于：<?php echo $post->post_modified; ?></sup>
                        <div class="content">
                            <?php
                                $chatgpt_sw = get_option('site_chatgpt_switcher');
                                $chatgpt_cat = false;
                                if($chatgpt_sw){
                                    $chatgpt_array = explode(',', get_option('site_chatgpt_includes'));
                                    $chatgpt_array_count = count($chatgpt_array);
                                    if($chatgpt_array_count>=1){
                                        for($i=0;$i<$chatgpt_array_count;$i++){
                                            if(in_category($chatgpt_array[$i]) || $pid===5291) $chatgpt_cat=true;
                                        }
                                    }
                                    if($chatgpt_cat) echo '<blockquote class="chatGPT"><p><b> 文章摘要 </b><span>chatGPT</span></p><p class="response load">standby chatGPT responsing..</p></blockquote>';
                                }
                                the_content();//print_r(get_post_parent($pid));
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
        /*
         *
         * chatGPT AI ARTICLE-DESCRIPTION SHORT-CUTS
         *
         */
        <?php
            if($chatgpt_sw&&$chatgpt_cat){
        ?>
                function words_typer(el, str, speed=100){
                    try{
                        if(!str||typeof(str)!='string'||str.replace(/^\s+|\s+$/g,"").replace( /^\s*/, '')=="") throw new Error("invalid string");
                        new Promise(function(resolve,reject){
                            setTimeout(() => {
                                el.classList.remove('load');
                                for(let i=0,textLen=el.innerText.length;i<textLen;i++){
                                    // real-time data stream
                                    let elText = el.innerText,
                                        elLen = elText.length-1;
                                    setTimeout(() => {
                                        el.innerText = elText.slice(0, elLen-i); // console.log(i+'-'+elLen);
                                        if(i===elLen) resolve(el);
                                    }, i*5);
                                }
                            }, 700);
                        }).then((res)=>{
                            setTimeout(() => {
                                res.classList.remove('load');
                                for(let i=0,strLen=str.length;i<strLen;i++){
                                    setTimeout(() => {
                                        res.innerText += str[i]; // console.log(str[i]);
                                        if(i+1===strLen) res.classList.add('done');
                                    }, i*speed);
                                }
                            }, 300);
                        }).catch(function(err){
                            console.log(err)
                        });
                    }catch(err){
                        console.log(err);
                    }
                };
                send_ajax_request("get", "<?php echo get_api_refrence('gpt');//echo custom_cdn_src('src',true).'/plugin/api.php?auth=gpt&pid='.$pid.'&exec=1';//$auth_url;//; ?>", false, (res)=>words_typer(document.querySelector('.chatGPT .response'), res, 25));
        <?php
            }
        ?>
    </script>
</body></html>