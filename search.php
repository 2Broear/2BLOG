<?php
/*
    Template name: 搜索页面模版
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/notes.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        span.mark:hover{background:#ff9632}
        span.mark{
            color: black;
            font-weight: bold;
            background: #f9f900;
            display: inline!important;
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
<div class="content-all">
<div class="win-top bg" style="background: url() top center /cover">
	<header>
		<nav id="tipson" class="ajaxloadon">
            <?php get_header(); ?>
		</nav>
	</header>
    <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
    <video src="" poster="<?php echo $img_cdn; ?>/images/search.jpg" preload autoplay muted loop x5-video-player-type="h5" controlsList="nofullscreen nodownload"></video>
	<h5 class="workRange wow fadeInUp" data-wow-delay="0.2s">
	    <?php 
            global $wp_query, $page_flag;
            $res_num = $wp_query->found_posts;
            $searchString=esc_html(get_search_query());
            // $page_flag = strpos(get_option('site_search_includes'), 'page')!==false ? '/page' : '';
            $res_array = explode(',',trim(get_option('site_search_includes','post')));  // NO "," Array
            foreach ($res_array as $each){
                if(trim($each)=='page') $page_flag='/页面';
            }
            echo '<b> '.$res_num.' </b>篇有关“<span>'.$searchString.'</span>”の内容'.$page_flag;//printf(esc_html__('%d条关于“%s”的文章', ''),$res_num,'<span>'.esc_html(get_search_query()).'</span>');
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;">
                <?php 
                    the_posts_with_styles($searchString);
                    // print_r($post);
                    // print_r($wp_query->have_posts());
                    // print_r($wp_query)
                ?>
			</div>
		</div>
	</div>
</div>
<footer>
    <?php get_footer(); ?>
</footer>
</div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(0); ?>/js/main.js"></script>
<!-- inHtmlJs -->
<script type="text/javascript">
    // document.addEventListener('load', function(){
        // 创建一个 TreeWalker 对象，选择所有文本节点
        const text = "<?php echo trim($searchString); ?>";
        if(text && text.trim()!=''){
            const regex = new RegExp(text, "g"),
                  matches = [],
                  walker = document.createTreeWalker(
                      document.querySelector('.win-content'),
                      NodeFilter.SHOW_TEXT,
                      null,
                      false
                  );
            // 遍历文本节点
            while (walker.nextNode()) {
              const node = walker.currentNode;
              // 检查节点的文本内容是否包含要替换的文本
              if (node.nodeValue.includes(text)) {
                //   nodeValue.getRangeAt(0).surroundContents(document.createElement('span'))
                  node.textContent = node.nodeValue.replace(regex, "<span class='mark'>"+text+"</span>");
                  matches.push(node.parentNode);
                //   const mark = document.createElement('div');
                //   mark.innerHTML = node.nodeValue.replace(regex, "<span class='mark'>"+text+"</span>");
                //   node.parentNode.replaceChild(mark, node);
              }
            }
            // 重写 HTML 解析
            matches.forEach((i)=>i.innerHTML = i.innerText);
        }
    // }, true);
</script>
</body></html>