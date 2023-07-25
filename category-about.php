<?php
/*
    Template name: 关于模板
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/about.css?v=<?php echo get_theme_info('Version'); ?>" />
    <style>
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
            font-style: italic;
            right: 3%;
            /*letter-spacing: 3px;*/
        }
        iframe.netease_embed{
            margin: 0 auto;
            display: block;
        }
        .has-text-align-center{
            text-align: center;
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
    <div class="content-all-windows">
        <div class="Introduce-window" style="width: 100%;">
            <div class="Introduce-core">
                <div class="In-core-head">
                    <div class="about_blocks">
                        <ul>
                            <li class="intro_left">
                                <div class="profile">
                                    <div class="user_info">
                                        <span id="head-photo">
                                            <?php
                                                // global $lazysrc, $loadimg; //
                                                $lazyhold = "";
                                                $avatar = get_option('site_avatar');
                                                if($lazysrc!='src'){
                                                    $lazyhold = 'data-src="'.$avatar.'"';
                                                    $avatar = $loadimg;
                                                }
                                                echo '<img '.$lazyhold.' src="'.$avatar.'" alt="avatar" />';
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
                                <div class="head-inside wow fadeInUp" data-wow-delay="0.15s" style="background:url(<?php echo !$video ? cat_metabg($cat, get_option('site_bgimg')) : false; ?>) center center /cover;">
                                    <?php
                                        if($video){
                                            echo '<video src="'.$video.'" poster="'.cat_metabg($cat, get_option('site_bgimg')).'" preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>';
                                        }
                                    ?>
                                </div>
                            </li>
                            <li class="intro_right">
                                <div class="mbit" data-mbit="<?php $mbit_array_result = explode('/', get_option('site_mbit_result_array'));echo strtoupper($mbit_array_result[1]); ?>">
                                    <div class="mbit_intro">
                                        <p> MBIT 16 Personalities overview<!--<sup> (Oct 21, 2022) </sup>--> </p>
                                        <a href="https://www.16personalities.com/<?php $mbit_abbr=$mbit_array_result[0];echo strpos($mbit_abbr,'-')!==false ? substr($mbit_abbr,0,4) : $mbit_abbr; ?>-personality" style="color:#33a474;" target="_blank" title="more details for <?php echo $res_type=strtoupper($mbit_abbr); ?>"><b><?php echo $res_type; ?></b></a>
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
<?php require_once(TEMPLATEPATH. '/foot.php'); ?>
</body></html>