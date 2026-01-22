<?php
/*
    Template name: 足迹地图
    Template Post Type: page
*/
    // $default_key = get_option('site_footprint_apikey');
    // $default_map = get_option('site_footprint_map');
    // $default_coords_data = urlencode(get_option('site_footprint_data'));
    // $default_panorama_data = urlencode(get_option('site_footprint_panorama_data'));
    $default_theme = theme_mode(true) ;//get_request_param('theme', theme_mode(true));
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <style>
        .win-top:after {
            background: transparent!important;
        }
        .content-all, .win-top {
            height: 100%;
        }
        html, body,
        iframe#map {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="<?php echo $default_theme; ?>">
<div class="content-all">
    <div class="win-top bg">
        <?php
            if (get_option('site_footprint_switcher')) {
                $default_api = get_api_refrence('map');
                echo "<iframe id='map' src='$default_api&map=&key=&path&coords=map_data&zoom=4.8&center=&theme=$default_theme' frameborder='no'></iframe>"; //&data=&panorama=
            } else {
                echo '<div class="empty_card"><i class="icomoon icom icon-' . current_slug() . '" data-t=" EMPTY "></i><h1> ' . $default_key ? current_slug(1) : 'API KEY' . ' </h1></div></div>';
            }
        ?>
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
    </div>
    <footer>
        <?php //get_footer(); ?>
    </footer>
</div>
<!-- siteJs -->
<?php get_foot(); ?>
</body></html>