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
        .head-inside video{
            /*height: auto;*/
        }
        .about_blocks li.intro_right .mbit .mbit_intro{
            margin-left: 25px;
        }
        .about_blocks li.intro_right .mbit .mbit_intro a{
            box-shadow: 0px 0 0px 3px rgb(51 164 116 / 12%);
            background: rgb(51 164 116 / 6%);
        }
        .about_blocks li.intro_right .mbit:before{
            right: 2%;
        }
        .about_blocks{
            margin-top: 15px;
        }
        .In-core-head #head-nickname{
            font-size: 1.35rem;
        }
        
        @keyframes loader{
            0%{transform:translateX(-100%);}
            100%{transform:translateX(360%);}
        }
        @keyframes loader-reverse{
            0%{transform:translateX(100%);}
            100%{transform:translateX(-360%);}
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li.after span em:after,*/
        .about_blocks li.intro_right .mbit .mbit_range li.after span em:before{
            background: -webkit-linear-gradient(left,var(--preset-fa) 0%,transparent 100%);
            -webkit-background: -webkit-linear-gradient(left,var(--preset-fa) 0%,transparent 100%);
            animation: loader-reverse ease-out 3.6s 1.2s infinite;
            -webkit-animation: loader-reverse ease-out 3.6s 1.2s infinite;
            left: auto;
            right: 0;
            transform: translateX(100%);
        }
        /*.about_blocks li.intro_right .mbit .mbit_range li span em:after,*/
        .about_blocks li.intro_right .mbit .mbit_range li span em:before{
            content: '';
            width: 66%;
            height: 100%;
            background-color: currentColor;
            position: absolute;
            top: 0;
            left: 0;
            transform: translateX(-100%);
            background: -webkit-linear-gradient(left,transparent 0%,var(--preset-fa) 100%);
            -webkit-background: -webkit-linear-gradient(left,transparent 0%,var(--preset-fa) 100%);
            animation: loader ease-out 3.6s 1.2s infinite;
            -webkit-animation: loader ease-out 3.6s 1.2s infinite;
            opacity: .75;
        }
        .about_blocks li.intro_right .mbit .mbit_range li span em{
            width: 0%;
            transition: width 1s ease;
            will-change: width;
            overflow: hidden;
        }
        .In-core-head .head-inside::before{
            /*background: url(https://img.2broear.com/images/svg/digital_mask.svg);*/
            background-size: 3px 3px!important;
            max-height: 100%;
        }
        
        .cs-tree{
            margin: 35px auto auto;
            text-align: left;
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
                                            <img src="<?php echo get_option('site_avatar'); ?>" style="" />
                                        </span>
                                        <div class="intro_info">
                                            <span id="head-nickname"><strong><?php echo get_option('site_nick'); ?></strong></span>
                                            <span id="head-sign"> <?php bloginfo('description'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="head-inside wow fadeInUp" data-wow-delay="0.15s" style="background:url(<?php echo $catbg=cat_metabg($cat, get_option('site_bgimg')); ?>) center center /cover;">
                                    <video src="<?php $video=get_option('site_about_video');echo $video; ?>" poster="<?php echo $catbg; ?>" <?php echo $video ? 'controls=""' : false; ?> preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>
                                </div>
                            </li>
                            <li class="intro_right">
                                <div class="mbit" data-mbit="<?php $mbit_result_array = explode(';', get_option('site_mbit_result_array'));$mbit_array_result = explode('/', $mbit_result_array[0]);echo strtoupper($mbit_array_result[1]); ?>">
                                    <div class="mbit_intro">
                                        <p> MBIT 16 Personalities result<!--<sup> (Oct 21, 2022) </sup>--> </p>
                                        <a href="https://www.16personalities.com/<?php $mbit_abbr=$mbit_array_result[0];echo strpos($mbit_abbr,'-')!==false ? substr($mbit_abbr,0,4) : $mbit_abbr; ?>-personality" style="color:#33a474;" target="_blank" title="Check more details for <?php echo $res_type=strtoupper($mbit_abbr); ?>"><b><?php echo $res_type; ?></b></a>
                                    </div>
                                    <ol class="mbit_range">
                                        <?php
                                            $mbit_array = explode(';', get_option('site_mbit_array'));
                                            $mbit_inits = array(
                                                array('before' => 'EXTRAVERTED', 'after' => 'INTROVERTED'),
                                                array('before' => 'INTUITIVE', 'after' => 'OBSERVANT'),
                                                array('before' => 'THINKING', 'after' => 'FEELING'),
                                                array('before' => 'JUDGING', 'after' => 'PROSPECTING'),
                                                array('before' => 'ASSERTIVE', 'after' => 'TURBULENT'),
                                            );
                                            for($i=0;$i<count($mbit_array);$i++){
                                                $each_data = explode('/', $mbit_array[$i]);
                                                array_push($each_data, $mbit_inits[$i]);
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
                                                    $init_before = trim($each_data[2]['before']);
                                                    $init_after = trim($each_data[2]['after']);
                                                    if($data_type){  //incase end with ";"
                                                        echo '<li class="'.$data_type.'" data-res="'.$data_percent.'"><span id="data-range" data-before="'.$data_before.'" data-after="'.$data_after.'"><em style="width:0%;"></em></span><span id="data-type" data-before="'.$init_before.'" data-after="'.$init_after.'"></span></li>'; //'.$data_percent.'%;
                                                    }
                                                }
                                            }
                                        ?>
                                    </ol>
                                    <!--<div class="mbit_extra">-->
                                    <!--    <p>Lastest result: <a href="javascript:;">Oct 21, 2022</a></p>-->
                                    <!--</div>-->
                                </div>
                            </li>
                        </ul>
                        <!--<div class="cs-tree"></div>-->
                    </div>
                </div>
                <div class="In-core-body">
                    <div class="body-basically wow fadeInUp" data-wow-delay="0.1s">
                        <div class="Introduce">
                            <?php 
                                the_content();  // the_page_content(current_slug());
                                dual_data_comments();  // include_once(TEMPLATEPATH. '/comments.php');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <?php get_footer(); ?>
    </footer>
</div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<script>
    // const video = document.querySelector('video'),
    //       user_info = document.querySelector('.user_info');
    // video.onplaying=()=>{user_info.classList.remove('pause');user_info.classList.add('playing');}
    // video.onpause=()=>{user_info.classList.remove('playing');user_info.classList.add('pause')}
    const counterList = document.querySelectorAll('.about_blocks li.intro_right .mbit .mbit_range li');
    function countAnimation(list,order){
        for(let i=0;i<list.length;i++){
            let count = parseInt(list[i].getAttribute('data-res')),
                counter = list[i].querySelector('em');
            // counter.style.width = count+"%";
            if(order){
                var inOrder = setTimeout(function(){
                        counter.style.width = count+"%";
                        inOrder = null;
                        clearTimeout(inOrder);
                    }, i*100);
            }else{
                counter.style.width = count+"%";
            }
        }
    };
    countAnimation(counterList,true)
</script>
</body></html>