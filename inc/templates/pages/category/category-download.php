<?php
/*
 * Template name: （BaaS）下载模板
   Template Post Type: page
*/
// wp自定义（含置顶无分页）查询函数
function get_download_posts($cats, $order=1){
    $output = '';
    $cats_count = count($cats);
    $dload_single_sw = get_option('site_single_switcher');
    if($dload_single_sw){
        $includes = get_option('site_single_includes');
        $dload_slug = get_cat_by_template('download','slug');
        $dload_single_sw = in_array($dload_slug, explode(',', $includes));
    }
    for($i=0;$i<$cats_count;$i++){
        $term_order = get_term_meta($cats[$i]->term_id, 'seo_order', true);
        // print_r($term_order);
        if($term_order==$order){
            $each_cat = $cats[$i];
            $cat_name = $each_cat->name;
            $cat_slug = $each_cat->slug;
            $cat_id = $each_cat->term_id;
            // 'category='.$cat_id.'&number=1&orderby'
            $cat_first_post = get_posts(array(
                'cat' => $cat_id,
                'meta_key' => 'post_orderby',
                'orderby' => array(
                    'meta_value_num' => 'DESC',
                    'date' => 'DESC',
                    'modified' => 'DESC'
                ),
                'number' => 1,
            ));
            $cat_poster = get_term_meta($cat_id, 'seo_image', true );
            if(!$cat_poster && !empty($cat_first_post)) $cat_poster = get_postimg(0, $cat_first_post[0]->ID, true); //get_option('site_bgimg');
            $output .= '<div class="dld_box fade-item '.$cat_slug.'"><div class="dld_box_wrap"><div class="box_up preCover"><span style="background:url('.$cat_poster.') center center /cover"><a href="javascript:;"><h3> '.$cat_name.' </h3><i> '.strtoupper($cat_slug).'</i><em></em></a></span></div><div class="box_down"><ul>';
                //setup query
                global $post, $lazysrc, $loadimg;
                $left_query = new WP_Query(array_filter(array(
                    'cat' => $cat_id,
                    'meta_key' => 'post_orderby',
                    'orderby' => array(
                        'meta_value_num' => 'DESC',
                        'date' => 'DESC',
                        'modified' => 'DESC'
                    ),
                    'posts_per_page' => 99 //get_option('posts_per_page'),  //use left_query counts
                )));
                while ($left_query->have_posts()):
                    $left_query->the_post();
                    $link = get_post_meta($post->ID, "post_feeling", true);
                    $postimg = get_postimg(0,$post->ID,true);
                    $lazyhold = '';
                    if($lazysrc!='src'){
                        $lazyhold = 'data-src="'.$postimg.'"';
                        $postimg = $loadimg;
                    }
                    $href = $link ? $link : 'javascript:void(0);';
                    $target = $link ? '_blank' : '_self';
                    $class_disabled  = !$link ? 'disabled ' : false;
                    $class_topset = get_post_meta($post->ID, 'post_orderby', true)>1 ? 'topset' : false;
                    $single_link = !$dload_single_sw ? '<a href="'.get_the_permalink().'" target="_blank" style="right:70px;">详情</a>' : '';
                    $output .= '<li class="'.$class_disabled.$class_topset.'"><div class="details"><a href="'.$href.'" target="'.$target.'" rel="nofollow" title="下载附件"><img '.$lazyhold.' src="'.$postimg.'" alt="poster" /></a><div class="desc">'.get_the_title().'<a href="'.$href.'" target="'.$target.'" rel="nofollow">下载附件</a>'.$single_link.'</div></div></li>';
                endwhile;
                wp_reset_query();  // 重置 wp 查询（每次查询后都需重置，否则将影响后续代码查询逻辑）
                unset($post, $lazysrc, $loadimg);
            $output .= '</ul></div></div>' . adscene_shortcode('adscene_sidebar_square', true) . '</div>';
        }
    };
    return $output;
};
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/download.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        #loading:before{top:40px}
        .main{
            padding: 0 15px;
            box-sizing: border-box;
        }
        .scroll-inform {
            padding: 0 15px;
        }
        .download_boxes .dld_boxes{
            /*width: calc(100%/3.03)!important;*/
        }
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
        <?php get_inform(); ?>
        <div class="download_boxes">
		    <?php 
		        $basename = basename(__FILE__);
                $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9))); //get_template_bind_cat($basename)->slug;//'download';
                $preslug = $preset->slug;
                $output = '';
                $baas = get_option('site_leancloud_switcher') && strpos(get_option('site_leancloud_category'), $basename)!==false; //in_array($basename, explode(',', get_option('site_leancloud_category')))
                if(!$baas){
                    $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                    $output_sw = false;
                    if(get_option('site_cache_switcher')){
                        $caches = get_option('site_cache_includes');
                        $output_sw = in_array($preslug, explode(',', $caches));
                        $output = $output_sw ? get_option('site_download_list_cache') : '';
                    }
                    if(!empty($cats) && current_slug()==$preslug){
                        if(!$output || !$output_sw){
                            for($i=1;$i<4;$i++) $output .= '<div class="dld_boxes">'.get_download_posts($cats, $i).'</div>';
                            if($output_sw) update_option('site_download_list_cache', $output); //wp_kses_post($output)
                        }
                    }else{
                        $term_order = get_term_meta($cat, 'seo_order', true);
                        // echo $cat;
                        $output = '<div class="dld_boxes single">'.get_download_posts(array(get_category($cat)), $term_order).'</div>';
                    }
                }else{
                    $output = '<div class="dld_boxes"><div class="dld_box adobe"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>Adobe 2020/2021</h3><i>ADOBE</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div><div class="dld_box soft"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>桌面软件</h3><i>SOFT</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div></div><div class="dld_boxes"><div class="dld_box p2p"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>下载软件</h3><i>P2P</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div><div class="dld_box tool"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>实用工具</h3><i>TOOLS</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div><div class="dld_box media"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>视频媒体</h3><i>MEDIA</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div></div><div class="dld_boxes"><div class="dld_box tools"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>辅助工具</h3><i>SORDUM</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div><div class="dld_box crack"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>破解工具</h3><i>CRACK</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div><div class="dld_box vpn"><div class="dld_box_wrap"><div class="box_up preCover"><span><a href="javascript:;"><h3>加速器</h3><i>٧ρ∩</i><em></em></a></span></div><div class="box_down"><ul><span id="loading"></span></ul></div></div></div></div>';
                };
                echo $output; //wp_kses_post($output);
		    ?>
		</div>
		<div style="max-width:1102px;margin:0 auto">
            <?php 
                the_content();// the_page_content(current_slug());  //
                dual_data_comments();
            ?>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
<!-- siteJs -->
<?php
    get_foot();
    if($baas){
?>
        <script type="text/javascript">
            AV.init({
                appId: "<?php echo get_option('site_leancloud_appid') ?>",
                appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
    	        serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
            });
            //request AV.Query
            const query_new = new AV.Query("<?php echo $preslug; ?>"),
                  query_tab = ["soft","p2p","tool","tools","vpn","crack","media","adobe"];
            query_new.addDescending("createdAt").find().then(result=>{ //.equalTo('type_download',loadType)
                for (let i=0; i<result.length;i++) {
                    let res = result[i],
                        title = res.attributes.title,
                        file = res.attributes.file,
                        src = res.attributes.src,
                        type = res.attributes.type_download,
                        img = res.attributes.img;
                    for(let j=0,qtLen=query_tab.length;j<qtLen;j++){
                        let each_load = query_tab[j];
                        if(type == each_load){
                            let curdom = document.querySelector('.'+each_load);
                            curdom.querySelector('.box_down ul').innerHTML = '<li class=""><div class="details"><a href="'+src+'" target="_blank" rel="nofollow" title="下载附件"><img src="'+img+'" alt="poster" /></a><div class="desc">'+title+'<a href="'+src+'" target="_blank" rel="nofollow">下载附件</a>'+file+'</div></div></li>';
                            curdom.querySelector('.box_up span').setAttribute("style",'background:url('+img+') center center /cover');
                        }
                    }
                }
            })
        </script>
<?php
    }
?>
</body></html>