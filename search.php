<?php
/*
    Template name: 搜索页面模版
    Template Post Type: pages
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/notes.css?v=<?php echo get_theme_info(); ?>" />
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
            $cid = check_request_param('cid');
            $year = check_request_param('year'); //gmdate('Y', time() + 3600*8)
            if($cid){
                $post_per_page = get_option('posts_per_page'); //get_option('site_per_posts');
                $real_current_page = max(1, get_query_var('paged'));
                // print_r('before rewrite current_page: '.$real_current_page.' ,offset: '.($real_current_page-1) * $post_per_page);
                $query_array = array(
                    'cat' => $cid, 
                    'meta_key' => 'post_orderby', 
                    'posts_per_page' => $post_per_page, 
                    'orderby' => array(
                        'date' => 'DESC',
                        'meta_value_num' => 'DESC',
                        'modified' => 'DESC',
                    ),
                    'page' => $real_current_page,  // note: MUST specific $current_page
                    'offset' => ($real_current_page-1) * $post_per_page,  // update $current_page offset
                );
                if($year){
                    $date_query = array(
                        'date_query' => array(
                            array(
                                'year' => $year,
                            ),
                        )
                    );
                    $query_array = array_merge($query_array, $date_query);
                }
                // rewrite $wp_query
                $wp_query = new WP_Query(array_filter($query_array));
            };
            $res_num = $wp_query->found_posts;
            $queryString = "";
            if(is_search()){
                $queryString = esc_html(get_search_query());
                if(trim($queryString)!=""){
                    // $page_flag = strpos(get_option('site_search_includes'), 'page')!==false ? '/page' : '';
                    $res_array = explode(',',trim(get_option('site_search_includes','post')));  // NO "," Array
                    foreach ($res_array as $each){
                        if(trim($each)=='page') $page_flag='/页面';
                    }
                    echo '<b> '.$res_num.' </b>篇有关“<span>'.$queryString.'</span>”の内容'.$page_flag;
                }else{
                    echo '正在浏览 <b> '.$res_num.' </b>篇<b> 全站内容.. </b>';
                }
            }else{
                $year = $year ? ' '.$year : 'bound.';
                echo '<b> '.$res_num.' </b>'.get_category($cid)->slug.' in<b>'.$year.'</b>';
            }
        ?>
    </h5>
</div>
<div class="content-all-windows">
	<div class="win-nav-content">
		<div class="win-content main">
			<div class="notes notes_default" style="max-width: 100%;min-height: 360px;">
                <?php 
                    the_posts_with_styles($queryString);
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
<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/main.js"></script>
<!-- inHtmlJs -->
<script type="text/javascript">
    // document.addEventListener('load', function(){
        // 创建一个 TreeWalker 对象，选择所有文本节点
        const text = "<?php echo trim($queryString); ?>";
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