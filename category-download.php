<?php
/*
 * Template name: 下载模板（BaaS）
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/download.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        #loading:before{top:40px}
        .main{padding: 0 15px;}
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
                $preset = get_cat_by_template(str_replace('.php',"",substr($basename,9)));//get_template_bind_cat($basename)->slug;//'download';
                $preslug = $preset->slug;
                $curslug = current_slug();
                $baas = get_option('site_leancloud_switcher')&&strpos(get_option('site_leancloud_category'), $basename)!==false;
                $cats = get_categories(meta_query_categories($preset->term_id, 'ASC', 'seo_order'));
                if(!$baas){
                    // !empty($cats) && $curslug==$preslug ? download_posts_query($cats, 1) : download_posts_query(array(get_category($cat)), 1, 'single');
                    if(!empty($cats) && $curslug==$preslug){
                        echo '<div class="dld_boxes">';
                        download_posts_query($cats, 1);
                        echo '</div>';
                    }else{
                        echo '<div class="dld_boxes single">';
                        download_posts_query(array(get_category($cat)), 1);
                        echo '</div>';
                    }
                    // print_r($cats);
                    // print_r(array(get_category($cat)));
                }else{
            ?>
			        <div class="dld_boxes">
        				<div class="dld_box adobe">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> Adobe 2020/2021 </h3><i>ADOBE</i><em></em></a>
        						  	</span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
        				<div class="dld_box soft">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 桌面软件 </h3><i>SOFT</i><em></em></a>
        						  	</span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
			        </div>
            <?php
                };
                if(!$baas){
                    if(!empty($cats) && $curslug==$preslug){
                        echo '<div class="dld_boxes">';
                        download_posts_query($cats, 2);
                        echo '</div>';
                    }else{
                        echo '<div class="dld_boxes single">';
                        download_posts_query(array(get_category($cat)), 2);
                        echo '</div>';
                    }
                }else{
            ?>
    		        <div class="dld_boxes">
        				<div class="dld_box p2p">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 下载软件 </h3><i>P2P</i><em></em></a>
        							</span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
        				<div class="dld_box tool">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 实用工具 </h3><i>TOOLS</i><em></em></a>
        							</span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
        				<div class="dld_box media">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 视频媒体 </h3><i>MEDIA</i><em></em></a>
        							</span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
    		        </div>
            <?php
                };
                if(!$baas){
                    if(!empty($cats) && $curslug==$preslug){
                        echo '<div class="dld_boxes">';
                        download_posts_query($cats, 3);
                        echo '</div>';
                    }else{
                        echo '<div class="dld_boxes single">';
                        download_posts_query(array(get_category($cat)), 3);
                        echo '</div>';
                    }
                }else{
            ?>
    		        <div class="dld_boxes">
        				<div class="dld_box tools">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 辅助工具 </h3><i>SORDUM</i><em></em></a>
        						    </span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
        				<div class="dld_box crack">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 破解工具 </h3><i>CRACK</i><em></em></a>
        						    </span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
        				<div class="dld_box vpn">
        					<div class="dld_box_wrap">
        						<div class="box_up preCover">
        							<span>
        								<a href="javascript:;"><h3> 加速器 </h3><i>٧ρ∩</i><em></em></a>
        						    </span>
        						</div>
        						<div class="box_down">
        						    <ul><span id="loading"></span></ul>
        						</div>
        					</div>
        				</div>
    		        </div>
            <?php
                }
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
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
                            curdom.querySelector('.box_down ul').innerHTML = '<li><div class="details"><span style="background:url('+img+') center center no-repeat"></span><div> '+title+' <i><a href="'+file+'" target="_blank">下载</a><a href="'+src+'" target="_blank">查看</a></i></div></div></li>';
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