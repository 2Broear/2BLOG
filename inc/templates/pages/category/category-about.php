<?php
/*
    Template name: 关于模板
    Template Post Type: page
*/
    $mbit_array_result = explode('/', get_option('site_mbit_result_array'));
    $mbit_abbr = isset($mbit_array_result[0]) ? strtoupper($mbit_array_result[0]) : 'MBTI';
    if (strpos($mbit_abbr, '-') !== false) {
        $mbit_split = explode('-', $mbit_abbr);
        $mbit_abbr = substr($mbit_abbr, 0, 4);
    } else {
        $mbit_split = [];
    }
    $mbit_avatar = 'https://www.16personalities.com/static/images/personality-types/avatars/email/large/' . $mbit_abbr . '_male.png?v=1';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/about.css?v=<?php echo get_theme_info(); ?>" />
    <style>
        .about_blocks li.intro_right .mbit {
            position: relative;
            overflow: hidden;
        }
        .about_blocks li.intro_right .mbit::after {
            content: '';
            width: 100%;
            height: 100%;
            display: block;
            background: url(<?php echo $mbit_avatar; ?>) no-repeat;/* center center /contain */
            background-size: contain;
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: .2;
            z-index: 0;
            mask: linear-gradient(top, var(--preset-f) 88%, transparent 100%);
            mask: -webkit-linear-gradient(top, var(--preset-f) 36%, transparent 100%);
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li:nth-child(1) span em,*/
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(1) span em::after{
            background: #4398AD;
        }
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(1).before span:before,
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(1).after span:after{
            color: #4398AD;
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li:nth-child(2) span em,*/
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(2) span em::after{
            background: #E6AE3F;
        }
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(2).before span:before,
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(2).after span:after{
            color: #E6AE3F;
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li:nth-child(3) span em,*/
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(3) span em::after{
            background: #2EA575;
        }
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(3).before span:before,
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(3).after span:after{
            color: #2EA575;
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li:nth-child(4) span em,*/
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(4) span em::after{
            background: #895D9E;
        }
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(4).before span:before,
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(4).after span:after{
            color: #895D9E;
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li:nth-child(5) span em,*/
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(5) span em::after{
            background: #EB6167;
        }
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(5).before span:before,
        .about_blocks li.intro_right .mbit .mbit_range li:nth-child(5).after span:after{
            color: #EB6167;
        }
        .about_blocks li.intro_right .mbit .mbit_intro p,
        .about_blocks li.intro_right .mbit:before{
            right: 3%;
            /*font-style: italic;*/
            /*letter-spacing: 3px;*/
        }
        body.dark .about_blocks li.intro_right .mbit:before {
            opacity: .36;
        }
        .about_blocks li.intro_right .mbit:before {
            opacity: .18;
        	/*text-transform: capitalize;*/
        	text-transform: uppercase;
        }
        iframe.netease_embed {
            /*margin: 0 auto;*/
            display: block;
        }
        .ibox mark,
        .has-text-align-center{
            text-align: center;
        }
        .ibox{
            text-align: inherit;
        }
        
        .wp-block-table {
            margin: 25px auto;
            text-align: left;
            border-radius: 0;
        }
        .wp-block-table table {
            width: auto;
            border-spacing: 10px;
            border-collapse: collapse;
        }
        .wp-block-table table tr {
            padding: 5px;
            border-radius: 1px 1px 0 0;
        }
        .wp-block-table table tr td:nth-child(2) {
            width: 20%;
        }
        .wp-block-table table tr td:last-child {
            /*border-right-color: inherit;*/
        }
        .wp-block-table table tr td:empty {
            /*display: none;*/
            border-right-color: transparent;
            background: transparent;
        }
        .wp-block-table table tr td {
            text-align: center;
            padding: 15px 35px;
            border: 1px solid rgb(200 200 200 / 35%);
        }
        .wp-block-table table tr td img {
            /*max-width: 36%;*/
            /*display: inline-block;*/
        }
        .about_blocks li.intro_right .mbit .mbit_intro {
            z-index: 1;
        }
        .about_blocks li.intro_right .mbit .mbit_intro a {
            border-width: 2px;
            box-shadow: 0px 0 0px 5px rgb(51 164 116 / 10%);
            /*box-shadow: none;*/
        }
        .about_blocks li.intro_right .mbit .mbit_intro a b {
            cursor: pointer;
        }
        .In-core-head .profile {
            margin-bottom: 15px;
        }
        .In-core-head .head-inside::before {z-index: -1}
        .In-core-head .head-inside {
            padding: 0;
            height: 200px;
        };
        body.dark .In-core-head .user_info{
            background: linear-gradient(90deg,var(--preset-3a) 0%,var(--preset-3b) 100%);
        }
        .body-basically .Introduce {
            color: var(--preset-4a);
        }
        .about_blocks li.intro_right .mbit .mbit_range li.active span em:before {
            /*opacity: 1;*/
            border-radius: inherit;
        }
        .about_blocks li.intro_right .mbit .mbit_range li span:before, 
        .about_blocks li.intro_right .mbit .mbit_range li span:after {
            opacity: .35;
        }
        blockquote p {
            margin: 10px auto;
        }
        .body-basically table td {
            color: var(--preset-6);
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
    <?php //get_inform(); ?>
    <!--<video type="video/webm" src="https://cdn.cdmmscl.com/20250428164.webm" poster="" preload="auto" autoplay="" muted="" loop="" x5-playsinline="true" playsinline="true" webkit-playsinline="true" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>-->
    <!--<video controls="" loop="" autoplay="autoplay" preload="true" crossorigin="anonymous" x5-playsinline="true" playsinline="true" webkit-playsinline="true" x5-video-player-type="h5"><source type="video/webm" src="https://cdn.cdmmscl.com/20250428164.webm"><p>your browser does not support videos, suggest to upgrade.</p></video>-->
    <div class="content-all-windows">
        <div class="Introduce-window" style="width: 100%;">
            <div class="Introduce-core">
                <div class="In-core-head">
                    <div class="about_blocks magnetic" data-magnet-scale="1.015" data-magnet-step="0.015">
                        <ul>
                            <li class="intro_left">
                                <div class="profile">
                                    <div class="user_info">
                                        <span id="head-photo">
                                            <?php
                                                // global $lazysrc, $loadimg;
                                                $avatar = get_option('site_avatar');
                                                if ($lazysrc!='src') {
                                                    $lazyhold = 'data-src="'.$avatar.'"';
                                                } else {
                                                    $lazyhold = '';
                                                    $loadimg = $avatar;
                                                }
                                                echo '<img '.$lazyhold.' src="'.$loadimg.'" alt="avatar" />';
                                                // unset($lazysrc, $loadimg);
                                            ?>
                                        </span>
                                        <div class="intro_info">
                                            <span id="head-nickname"><strong><?php echo get_option('site_nick'); ?></strong></span>
                                            <span id="head-sign"> <?php bloginfo('description'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $video = replace_video_url(get_option('site_about_video'));
                                ?>
                                <div class="head-inside wow fadeInUp" data-wow-delay="0.15s" style="background:url(<?php echo !$video ? get_meta_image($cat, get_option('site_bgimg')) : false; ?>) center center /cover;">
                                    <?php
                                        if ($video) {
                                            // echo '<video src="'.$video.'" poster="" preload="auto" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload" playsinline -webkit-playsinline></video>'; //'.get_meta_image($cat, get_option('site_bgimg')).'
                                            echo '<iframe id="panorama" frameborder="no" data-src="https://node.2broear.com/?texture&entry=mbti_enfp&model=/assets/3d/draco/mbti_enfp-textured-x200_compresseds.glb"></iframe>';
                                        }
                                    ?>
                                </div>
                            </li>
                            <li class="intro_right">
                                <div class="mbit" data-mbit="<?php echo isset($mbit_array_result[1]) ? $mbit_array_result[1] : 'MBTI';//strtoupper(); ?>">
                                    <div class="mbit_intro">
                                        <p> MBTI 16 Personalities<!--<sup> (Oct 21, 2022) </sup>--> </p>
                                        <a class="magnetic" href="https://www.16personalities.com/<?php echo $mbit_abbr; ?>" style="color:#33a474;" target="_blank" title="more about <?php echo $mbit_abbr; ?>"><b><?php echo $mbit_abbr . '-' . $mbit_split[1]; ?></b></a>
                                    </div>
                                    <ol class="mbit_range">
                                        <?php
                                            $mbit_array = explode(';', get_option('site_mbit_array'));
                                            $mbit_array_count = count($mbit_array);
                                            $mbit_inits = array(
                                                array('before' => 'Extraverted', 'after' => 'Introverted'),
                                                array('before' => 'Intuitive', 'after' => 'Observant'),
                                                array('before' => 'Thinking', 'after' => 'Feeling'),
                                                array('before' => 'Judging', 'after' => 'Prospecting'),
                                                array('before' => 'Assertive', 'after' => 'Turbulent'),
                                            );
                                            for($i=0;$i<$mbit_array_count;$i++){
                                                $each_data = explode('/', $mbit_array[$i]);
                                                array_key_exists($i,$mbit_inits) ? array_push($each_data, $mbit_inits[$i]) : false;
                                                //   print_r($each_data);
                                                if($each_data[0]){
                                                    $data_type = trim($each_data[0]);
                                                    $data_percent = trim($each_data[1]);
                                                    $data_calculate = 100-$data_percent;
                                                    if($data_type=='before' && $data_percent>50){
                                                        $data_before = $data_percent;
                                                        $data_after = $data_calculate;
                                                    }else{
                                                        $data_before = $data_calculate;
                                                        $data_after = $data_percent;
                                                    };
                                                    $init_before = strtoupper(trim($each_data[2]['before']));
                                                    $init_after = strtoupper(trim($each_data[2]['after']));
                                                    if($data_type){  //incase end with ";"
                                                        echo '<li class="'.$data_type.'" data-res="'.$data_percent.'"><span id="data-range" data-before="'.$data_before.'" data-after="'.$data_after.'"><em style="width:'.$data_percent.'%;"></em></span><span id="data-type" data-before="'.$init_before.'" data-after="'.$init_after.'"></span></li>'; //'.$data_percent.'%;
                                                    }
                                                }
                                            }
                                        ?>
                                    </ol>
                                </div>
                            </li>
                        </ul>
                        <!--<div class="cs-tree"></div>-->
                    </div>
                </div>
                <div class="In-core-body">
                    <div class="body-basically wow fadeInUp" data-wow-delay="0.1s">
                        <div class="Introduce">
                            <?php the_content();  // the_page_content(current_slug());?>
                        </div>
                    </div>
                </div>
            </div>
            <?php dual_data_comments();  // include_once(TEMPLATEPATH. '/comments.php');?>
        </div>
    </div>
    <footer>
        <?php get_footer(); ?>
    </footer>
</div>
<!-- siteJs -->
<?php get_foot(); ?>
</body></html>