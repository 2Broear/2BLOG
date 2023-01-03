<?php
/*
    Template name: 评论排行
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/ranking.css?v=<?php echo get_theme_info('Version'); ?>" />
</head>
<body class="<?php theme_mode(); ?>">
    <div class="content-all">
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
        <?php get_inform(); ?>
        <div class="ranks">
            <div class="ranking">
                <?php
                    echo '<h1>常客 <sup> &lt;3 </sup></h1><p>访问较频繁的老铁</p><ul id="rankest">';
                    $third_cmt = get_option('site_third_comments');
                    $valine_sw = $third_cmt=='Valine' ? true : false;//get_option('site_valine_switcher');
                    $twikoo_sw = $third_cmt=='Twikoo' ? true : false;//get_option('site_twikoo_switcher');
                    if(!$valine_sw){
                        $rankdata = get_comments_ranking();
                        $datalen = count($rankdata);
                        for($i=0;$i<3;$i++){
                            if(array_key_exists($i,$rankdata)){
                                $user = $rankdata[$i];
                                $count = $user->count ? $user->count : 0;
                                $link = $user->link;
                                $name = $user->name ? $user->name : '???';
                            }
                ?>
                            <li>
                                <span id="avatar" data-t="<?php echo $count ?>">
                                    <a href="<?php echo $link; ?>" target="_blank">
                                        <img src="<?php echo get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100' ?>" title="这家伙留了 <?php echo $count ?> 条评论！">
                                    </a>
                                </span>
                                <span id="range" style="">
                                    <em style="height:<?php echo $count<50?$count*2:$count ?>%"></em>
                                </span>
                                <a href="<?php echo $link; ?>" target="_self">
                                    <b title="<?php echo $name; ?>"><?php echo $name; ?></b>
                                </a>
                            </li>
                <?php
                        }
                    };
                    echo '</ul>';
                    // top 10
                    if(!$valine_sw){
                        if($datalen>3){
                            echo '<h1>稀客 <sup> &lt;10 </sup></h1><p>偶尔来访的兄弟</p><ul id="ranks">';
                            for($i=3;$i<13;$i++){
                                $user = array_key_exists($i,$rankdata) ? $rankdata[$i] : false;
                                if($user){
                                    $count = $user->count;
                                    $link = $user->link;
                ?>
                                    <li title="TA 在本站已有 <?php echo $count ?> 条评论">
                                        <span id="avatar">
                                            <a href="<?php echo $link; ?>" target="_blank">
                                            <img src="<?php echo get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100' ?>">
                                            </a>
                                        </span>
                                        <a href="<?php echo $link; ?>" target="_blank">
                                            <?php echo '<b data-mail="'.$user->mail.'">'.$user->name.'</b><sup>'.$count.'+</sup>' ?>
                                        </a>
                                    </li>
                <?php
                                }
                            }
                            echo '</ul>';
                        }
                    }else{
                ?>
                        <h1>稀客 <sup> &lt;10th </sup></h1>
                        <p>偶尔来访的兄弟</p>
                        <ul id="ranks"></ul>
                <?php
                    };
                    // left 
                    if(!$valine_sw){
                        if($datalen>13){
                            // if($datalen<13) echo '<p>这里暂时没有人哦～</p>';
                            echo '<h1>游客 <sup> &gt;10 </sup></h1><ul id="ranked">';
                            for($i=13;$i<50;$i++){
                                $user = $rankdata[$i];
                                if($user) echo '<li><p>'.$user->name.'<sup>'.$user->count.'</sup></p></li>';
                            }
                            echo '</ul>';
                        }
                    }else{
                ?>
                        <h1>游客 <sup> &gt;10th </sup></h1>
                        <ul id="ranked"></ul>
                <?php
                    };
                ?>
            </div>
            <?php the_content();  // the_page_content(current_slug()); ?>
            <div id="comment_txt">
                <?php dual_data_comments(); ?>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<?php
    if($valine_sw){
?>
        <!--<script type="text/javascript" src="<?php //custom_cdn_src(); ?>/js/md5.min.js"></script>-->
        <script>
            var query = new AV.Query("Comment"),
                rankest = document.getElementById("rankest"),
                ranks = document.getElementById("ranks"),
                ranked = document.getElementById("ranked"),
                max = 666,  //999
                comArr = [],
                compare = (reply) => {
                    return function(a,b){
                        var a = a[reply];
                        var b = b[reply];
                        return b - a;
                    }
                };
            query.addDescending("createdAt").limit(max).find().then(res => {
                var temp = [],
                    temps = [],
                    obj = {};
                for (let i=0;i<res.length;i++) {
                    let nick = res[i].attributes.nick,
                        mail = res[i].attributes.md5mail,  //use md5 insted of mail
                        link = res[i].attributes.link;
                    comArr.push({nick,mail,link});
                }
                for(k in comArr){
                    let name = comArr[k].nick,
                        mail = comArr[k].mail,
                        link = comArr[k].link;
                    temp.push({name,mail,link})
                }
                for(let i=0;i<temp.length;i++){
                    let _i = temp[i],
                        n = _i.name,
                        m = _i.mail,
                        l = _i.link;
                    // Object.assign(obj,{"l":l});
                    // obj[n+'['+m+']'+l] = obj[n+'['+m+']'+l]+1 || 1;
                    obj[n+'['+m+']'] = obj[n+'['+m+']']+1 || 1;
                }
                console.log(obj)
                for(k in obj){
                    let t = obj[k],
                        b = k.indexOf('['),
                        a = k.indexOf(']'),
                        n = k.substring(0,b),
                        m = k.substring(b+1,a),
                        l = k.substring(a+1,k.length);
                    n!="匿名者"&&n!="2broear" ? temps.push({t,n,m,l}) : console.log(n);
                    temps.sort(compare('t'));
                    // console.log(k);
                }
                // console.log(temps)
                // for(let i=0;i<temps.length;i++){
                //     let names=temps[i].n;
                //     names=="匿名者" ?  (temps.splice(i,1),console.log(i)) : false
                // }
                console.log(temps)
                var average = 0;
                for(var i=0,avg=0,max=3,maxes=10,limit=52;i<limit;i++){
                    let name = temps[i].n,
                        mail = temps[i].m,//md5(temps[i].m),
                        link = temps[i].l,
                        times = temps[i].t;
                    avg = avg+=temps[i].t;
                    average = avg/max;
                    if(name!="匿名者"&&name!="2broear"){
                        i<max ? rankest.innerHTML += `<li><span id="avatar" data-t="${times}"><a href="${link}" target="_blank"><img src="<?php echo get_option("site_avatar_mirror") ?>avatar/${mail||'none'}?d=retro&s=100" title="这家伙留了 ${times} 条评论！" /></a></span><span id="range" style="height:${average}px"><em style="height:${times*2}%"></em></span><a href="${link}" target="_self"><b>${name}</b></a></li>` : false;
                        i>=max && i<maxes ? ranks.innerHTML += `<li title="TA 在本站已有 ${times} 条评论"><span id="avatar"><img src="<?php echo get_option("site_avatar_mirror") ?>avatar/${mail||'none'}?d=retro&s=100" /></span><a href="${link}"><b data-mail="${temps[i].m}">${name}</b><sup>${times}+</sup></a></li>` : false;
                        i>maxes ? ranked.innerHTML += `<li><p>${name}<sup>${times}</sup></p></li>` : false;
                    }
                }
            })
        </script>
<?php
    }
?>
</body></html>