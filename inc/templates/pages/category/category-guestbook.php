<?php
/*
    Template name: 留言模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <style>
        #reverse_rotate {direction: rtl;unicode-bidi: bidi-override;transform:rotate(180deg);display: inline-block;}
        .content-all-windows {display: block!important;}
        .win-top h5 {font-family: "Playfair Display",宋体 , serif!important;}
        #vcomments {padding:0!important;}
        .typed-cursor {color: white; background: transparent;}
    </style>
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <div class="win-top bg" attr-bg="">
            <em class="digital_mask" style="background: url(<?php echo $img_cdn; ?>/images/svg/digital_mask.svg)"></em>
			<header>
				<nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
				</nav>
			</header>
			<?php 
			    $video_src = replace_video_url(get_option('site_guestbook_video'));
			    $poster_src = $video_src ? $video_src : get_meta_image($cat, $img_cdn.'/images/guestbook.jpg');
			    echo do_shortcode('[custom_video src="' . $video_src . '" poster="' . $poster_src . '"]');
		    ?>
            <h5> 
            <?php 
                $typing_effects = get_option('site_animated_typing_switcher') && in_array(current_slug(), explode(',', get_option('site_animated_typing_includes')));
                $cat_desc = get_category($cat)->category_description;
                $cat_text = $cat_desc ? $cat_desc : '畅所<b style="font-family:sans-serif;">，</b>';
                if (!$typing_effects) $cat_text .= '<span>你の欲言~</span>';
                echo '<div class="typed">' . $cat_text . '<span id="typed"></span></div>';
            ?> 
            </h5>
        </div>
		<div class="content-all-windows" style="padding-top:0;">
            <?php 
                the_content();  // the_page_content(current_slug());
                dual_data_comments();  // include_once(TEMPLATEPATH. '/comments.php');
            ?>
		</div>
		<footer>
            <?php get_footer(); ?>
		</footer>
	</div>
<!-- siteJs -->
<?php get_foot(); ?>
<!-- pluginJs -->
</body></html>