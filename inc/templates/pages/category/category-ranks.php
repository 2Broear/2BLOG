<?php
/*
    Template name: 评论排行
    Template Post Type: page
*/
// 返回WP评论排行统计 https://www.seo628.com/2685.html
function get_comment_ranks() {
    global $wpdb;
    $comments_data = array();
    $comments_mail = $wpdb->get_results("SELECT DISTINCT comment_author_email FROM $wpdb->comments WHERE 1 ");
    foreach($comments_mail as $email){
        $each_mail = $email->comment_author_email;
        $mail_data = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_author_email = '$each_mail' ");
        $comments_obj = new stdClass();
        $comments_obj->name = $mail_data[0]->comment_author;
        $comments_obj->mail = $each_mail;
        $comments_obj->link = $mail_data[0]->comment_author_url;
        $comments_obj->count = count($mail_data);
        array_push($comments_data, $comments_obj);
    }
    unset($wpdb);
    usort($comments_data,function($first,$second){
        return $first->count < $second->count;
    });
    return $comments_data;
}
function the_comment_ranks($t1='常客',$c1='访问较频繁的童鞋',$t2='稀客',$c2='偶尔来访的小伙伴',$t3='游客',$c3='') {
    global $valine_sw;
    $output = '';
    if(!$valine_sw){
        $output_sw = false;
        if(get_option('site_cache_switcher')){
            $caches = get_option('site_cache_includes');
            $temp_slug = get_cat_by_template('ranks','slug');
            $output_sw = in_array($temp_slug, explode(',', $caches));
            $output = $output_sw ? get_option('site_rank_list_cache') : '';
        }
        if(!$output || !$output_sw){
            global $lazysrc, $loadimg;
            $output .= '<div class="fade-item"><h1>'.$t1.' </h1><p>'.$c1.'</p><ul id="rankest">';
            $rankdata = get_comment_ranks();
            $datalen = count($rankdata);
            $databox = '';
            for($i=0;$i<3;$i++){
                if(array_key_exists($i,$rankdata)){
                    $user = $rankdata[$i];
                    $count = $user->count ? $user->count : 0;
                    $link = $user->link ? $user->link : '#';
                    $name = $user->name ? $user->name : '???';
                }
                $lazyhold = "";
                $avatar = get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100';
                if($lazysrc!='src'){
                    $lazyhold = 'data-src="'.$avatar.'"';
                    $avatar = $loadimg;
                }
                $counts = $count<50 ? $count*2 : $count;
                $databox .= '<li><span id="avatar" data-t="'.$count.'"><a href="'.$link.'" target="_blank"><img '.$lazyhold.' src="'.$avatar.'" title="这家伙留了 '.$count.' 条评论！" alt="'.$name.'" /></a></span><span id="range" style=""><em style="height:'.$counts.'%"><span class="wave active"></span></em></span><a href="'.$link.'" target="_self"><b title="'.$name.'">'.$name.'</b></a></li>';
            };
            $databox .= '</ul>';
            // top 10
            if($datalen>3){
                $databox .= '<h1>'.$t2.' </h1><p>'.$c2.'</p><ul id="ranks">';
                for($i=3;$i<10;$i++){
                    $user = array_key_exists($i,$rankdata) ? $rankdata[$i] : false;
                    if($user){
                        $count = $user->count;
                        $link = $user->link;
                        $lazyhold = "";
                        $avatar = get_option('site_avatar_mirror').'avatar/'.md5($user->mail).'?d=retro&s=100';
                        if($lazysrc!='src'){
                            $lazyhold = 'data-src="'.$avatar.'"';
                            $avatar = $loadimg;
                        }
                        $databox .= '<li title="TA 在本站已有 '.$count.' 条评论"><span id="avatar"><a href="'.$link.'" target="_blank"><img '.$lazyhold.' src="'.$avatar.'" title="这家伙留了 '.$count.' 条评论！" alt="'.$name.'"></a></span><a href="'.$link.'" target="_blank"><b data-mail="'.$user->mail.'">'.$user->name.'</b><sup>'.$count.'+</sup></a></li>';
                    }
                }
                $databox .= '</ul>';
            };
            // left 
            if($datalen>13){
                $databox .= '<h1>'.$t3.' </h1><p>'.$c3.'</p><ul id="ranked">';
                for($i=13;$i<50;$i++){
                    $user = array_key_exists($i,$rankdata) ? $rankdata[$i] : false;
                    if($user) $databox .= '<li><p>'.$user->name.'<sup>'.$user->count.'</sup></p></li>';
                }
                $databox .= '</ul>';
            };
            $output .= $databox . '</div>';
            if($output_sw) update_option('site_rank_list_cache', wp_kses_post($output));
            unset($lazysrc, $loadimg);
        }
    }else{
        $output .= '<div class="fade-item"><h1>'.$t1.' </h1><p>'.$c1.'</p><ul id="rankest"><span id="loading"></span></ul></div><div class="fade-item"><h1> '.$t2.' </h1><p>'.$c2.'</p><ul id="ranks"><span id="loading"></ul></div><div class="fade-item"><h1>'.$t3.'</h1>'.$c3.'<ul id="ranked"><span id="loading"></span></ul></div>';
    };
    echo wp_kses_post($output);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/ranking.css?v=<?php echo get_theme_info(); ?>" />
    <style>
        .ranking ul li span#range em span.wave{
            position: relative;
            z-index: 1;
        }
        #ranks b{
            margin: 5px auto;
            font-size: small;
            max-width: 6em;
        }
        .ranking ul li span#avatar::before{
            width: auto;
        }
        .ranking ul li span#range em span.wave{
            overflow: hidden;
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
        <div class="ranks">
            <div class="ranking">
                <?php 
                    $third_cmt = get_option('site_third_comments');
                    $valine_sw = $third_cmt==='Valine';
                    $twikoo_sw = $third_cmt==='Twikoo';
                    // 输出评论排行
                    the_comment_ranks('常客','访问较频繁的童鞋', '稀客','偶尔来访的小伙伴', '游客',''); 
                ?>
            </div>
            <?php 
                the_content();  // the_page_content(current_slug());
                // ads..
                adscene_shortcode('adscene_list_context');
            ?>
            <div id="comment_txt">
                <?php dual_data_comments(); ?>
            </div>
        </div>
        <footer>
            <?php get_footer(); ?>
        </footer>
    </div>
<!-- siteJs -->
<?php
    get_foot();
    if($valine_sw){
?>
        <!--<script type="text/javascript" src="<?php echo $src_cdn; ?>/js/md5.min.js"></script>-->
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
                },
                remove_load=(el)=>{
                    el.querySelector("#loading") ? el.querySelector("#loading").remove() : false;
                };
            // loading = document.createElement("span")
            // loading.id="loading";
            // rankest.insertBefore(loading,rankest.firstChild);
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
                var average = 0,
                    avg=0,
                    max=3,
                    maxes=10,
                    limit=52;
                var fragment_rankest = document.createDocumentFragment(),
                    fragment_ranks = document.createDocumentFragment(),
                    fragment_ranked = document.createDocumentFragment(),
                    temp_rankest = document.createElement("DIV"),
                    temp_ranks = document.createElement("DIV"),
                    temp_ranked = document.createElement("DIV");
                for(let i=0;i<limit;i++){
                    let name = temps[i].n,
                        mail = temps[i].m,//md5(temps[i].m),
                        link = temps[i].l,
                        times = temps[i].t;
                    avg = avg+=temps[i].t;
                    average = avg/max;
                    if(name!="匿名者"&&name!="2broear"){
                        <?php
                            $lazyhold = "";
                            $avatar = get_option('site_avatar_mirror').'avatar/${mail}?d=retro&s=100';
                            if($lazysrc!='src'){
                                $lazyhold = 'data-src="'.$avatar.'"';
                                $avatar = $loadimg;
                            }
                        ?>
                        link = link ? link : 'javascript:;';
                        if(i<max){
                            remove_load(rankest);
                            temp_rankest.innerHTML += `<li><span id="avatar" data-t="${times}"><a href="${link}" target="_blank"><img <?php echo $lazyhold; ?> src="<?php echo $avatar; ?>" title="这家伙留了 ${times} 条评论！" alt="${name}" /></a></span><span id="range" style="height:${average}px"><em style="height:${times*1}%"><span class="wave active"></span></em></span><a href="${link}" target="_self"><b>${name}</b></a></li>`;
                            fragment_rankest.appendChild(temp_rankest);
                        }
                        if(i>=max && i<maxes){
                            remove_load(ranks);
                            temp_ranks.innerHTML += `<li title="TA 在本站已有 ${times} 条评论"><span id="avatar" data-t="${times}"><img <?php echo $lazyhold; ?> src="<?php echo $avatar; ?>" alt="${name}" /></span><a href="${link}"><b data-mail="${temps[i].m}">${name}</b></a></li>`; //<sup>${times}+</sup>
                            fragment_ranks.appendChild(temp_ranks);
                        }
                        if(i>maxes){
                            remove_load(ranked);
                            temp_ranked.innerHTML += `<li><p>${name}<sup>${times}</sup></p></li>`;
                            fragment_ranked.appendChild(temp_ranked);
                        }
                        // note: re-call 'body img' caused frame drops, scrolling stuck at loop(specific images will be better)
                        // loadlazy("body img");
                    }
                };
                rankest.appendChild(fragment_rankest);
                ranks.appendChild(fragment_ranks);
                ranked.appendChild(fragment_ranked);
                // fine re-call with outside of loop
                loadlazy(".ranks .ranking img[data-src]");
            })
        </script>
<?php
    }
?>
</body></html>