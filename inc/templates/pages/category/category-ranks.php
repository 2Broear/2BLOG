<?php
/*
    Template name: 评论排行
    Template Post Type: page
*/
/**
 * 获取评论排行数据（按评论数降序，排除管理员）
 * 
 * @return array 对象数组，包含 name, mail, link, count
 */
function get_comment_ranks() {
    global $wpdb;
    $admin_email = get_option('admin_email');

    $results = $wpdb->get_results($wpdb->prepare("
        SELECT 
            MAX(comment_author) AS comment_author,
            comment_author_email,
            MAX(comment_author_url) AS comment_author_url,
            COUNT(*) AS cnt,
            MIN(comment_date) AS first_date,
            MAX(comment_date) AS last_date
        FROM $wpdb->comments
        WHERE comment_approved = '1'
          AND comment_author_email != ''
          AND comment_author_email != %s
        GROUP BY comment_author_email
        ORDER BY cnt DESC
    ", $admin_email));

    $comments_data = [];
    foreach ($results as $row) {
        $obj = new stdClass();
        $obj->name       = $row->comment_author;
        $obj->mail       = $row->comment_author_email;
        $obj->link       = $row->comment_author_url;
        $obj->count      = (int) $row->cnt;
        $obj->first_date = $row->first_date;
        $obj->last_date  = $row->last_date;
        $comments_data[] = $obj;
    }
    return $comments_data;
}

/**
 * 输出评论排行 HTML
 *
 * @param string $t1 第一组标题
 * @param string $c1 第一组描述
 * @param string $t2 第二组标题
 * @param string $c2 第二组描述
 * @param string $t3 第三组标题
 * @param string $c3 第三组描述
 */
function the_comment_ranks($t1 = '常客', $c1 = '近期访问较频繁的童鞋', $t2 = '稀客', $c2 = '近期偶尔来访的小伙伴', $t3 = '游客', $c3 = '') {
    $output = '';
    global $valine_sw;
    if ($valine_sw) {
        $output .= '<div class="fade-item"><h1>'.$t1.' </h1><p>'.$c1.'</p><ul id="rankest"><span id="loading"></span></ul></div><div class="fade-item"><h1> '.$t2.' </h1><p>'.$c2.'</p><ul id="ranks"><span id="loading"></ul></div><div class="fade-item"><h1>'.$t3.'</h1>'.$c3.'<ul id="ranked"><span id="loading"></span></ul></div>';
    } else {
        // 缓存处理（保留原主题逻辑）
        $output_sw = false;
        if (get_option('site_cache_switcher')) {
            $caches = get_option('site_cache_includes');
            $temp_slug = get_cat_by_template('ranks', 'slug');
            $output_sw = in_array($temp_slug, explode(',', $caches));
            $output = $output_sw ? get_option('site_rank_list_cache') : '';
        }
    
        if (!$output || !$output_sw) {
            global $lazysrc, $loadimg;
    
            $rankdata = get_comment_ranks();
            $datalen = count($rankdata);
    
            // ---------- 辅助函数 ----------
            $get_range_max = function($rankdata, $loopnum) {
                $range_max = 0;
                for ($i = 0; $i < $loopnum && isset($rankdata[$i]); $i++) {
                    $user = $rankdata[$i];
                    $name = $user->name ?: '匿名者';
                    if ($name != '匿名者' && $name != '2broear') {
                        $range_max += $user->count ?: 0;
                    }
                }
                return $range_max;
            };
    
            $get_range_percent = function($num, $max) {
                if (!is_numeric($num) || !is_numeric($max) || $max == 0) {
                    return 0;
                }
                return ($num / $max) * 100;
            };
    
            // ---------- 开始构建 HTML ----------
            $output .= '<div class="fade-item"><h1>' . esc_html($t1) . '</h1><p>' . esc_html($c1) . '</p><ul id="rankest">';
    
            $loopmax = 3; // t1 显示前 3 名
            $range_max = $get_range_max($rankdata, $loopmax);
    
            // 前 3 名（过滤掉匿名者和特定用户后显示）
            for ($i = 0; $i < $loopmax; $i++) {
                if (!isset($rankdata[$i])) break;
                $user = $rankdata[$i];
                $count = $user->count ?: 0;
                $link  = $user->link ?: '#';
                $name  = $user->name ?: '???';
    
                if ($name != '匿名者' && $name != '2broear') {
                    $avatar = get_option('site_avatar_mirror') . 'avatar/' . md5($user->mail) . '?d=retro&s=100';
                    $lazyhold = (isset($lazysrc) && $lazysrc != 'src') ? 'data-src="' . $avatar . '"' : '';
                    $img_src = ($lazyhold === '') ? $avatar : (isset($loadimg) ? $loadimg : '');
    
                    $percent = $get_range_percent($count, $range_max);
                    $title_text = sprintf('首次评论于 %s，最近评论 %s', $user->first_date, $user->last_date);
                    $output .= '<li><span id="avatar" data-t="' . $count . '"><a href="' . esc_url($link) . '" target="_blank"><img ' . $lazyhold . ' src="' . esc_url($img_src) . '" title="' . esc_attr($title_text) . '" alt="' . esc_attr($name) . '" /></a></span>';
                    $output .= '<span id="range" style=""><em style="height:' . $percent . '%"><span class="wave active"></span></em></span>';
                    $output .= '<a href="' . esc_url($link) . '" target="_self"><b title="' . esc_attr($name) . '">' . $name . '</b></a></li>';
                }
            }
            $output .= '</ul>';
    
            // t2 组：第 4 到第 13 名（索引 3~12）
            if ($datalen > 3) {
                $output .= '<h1>' . esc_html($t2) . '</h1><p>' . esc_html($c2) . '</p><ul id="ranks">';
                for ($i = 3; $i < 13; $i++) {
                    if (!isset($rankdata[$i])) break;
                    $user = $rankdata[$i];
                    $count = $user->count ?: 0;
                    $link  = $user->link ?: '#';
                    $name  = $user->name ?: '匿名者';
                    $avatar = get_option('site_avatar_mirror') . 'avatar/' . md5($user->mail) . '?d=retro&s=100';
                    $lazyhold = (isset($lazysrc) && $lazysrc != 'src') ? 'data-src="' . $avatar . '"' : '';
                    $img_src = ($lazyhold === '') ? $avatar : (isset($loadimg) ? $loadimg : '');
    
                    $title_text = sprintf('首次评论于 %s，最近评论 %s', substr($user->first_date, 0, 10), substr($user->last_date, 0, 10));
                    $output .= '<li title="' . esc_attr($title_text) . '"><span id="avatar"><a href="' . esc_url($link) . '" target="_blank"><img ' . $lazyhold . ' src="' . esc_url($img_src) . '" alt="' . esc_attr($name) . '"></a></span>';
                    $output .= '<a href="' . esc_url($link) . '" target="_blank"><b data-mail="' . esc_attr($user->mail) . '">' . $name . '</b><sup>' . $count . '+</sup></a></li>';
                }
                $output .= '</ul>';
            }
    
            // t3 组：索引 13 开始，最多到第 50 名（索引 49）
            if ($datalen > 13) {
                $output .= '<h1>' . esc_html($t3) . '</h1><p>' . esc_html($c3) . '</p><ul id="ranked">';
                $max_t3 = min($datalen, 100);
                for ($i = 13; $i < $max_t3; $i++) {
                    $user = $rankdata[$i];
                    $link  = $user->link ?: '#';
                    $name  = $user->name ?: '匿名者';
                    if ($name === '2BER') {
                        break;
                    }
                    $count = $user->count ?: 0;
                    $output .= '<li><p title="这家伙就留了 ' . $count . ' 条评论！"><a href="' . esc_url($link) . '" target="_blank">' . esc_html($name) . '</a></p></li>';
                }
                $output .= '</ul>';
            }
    
            $output .= '</div>';
    
            // 写入缓存
            if ($output_sw) update_option('site_rank_list_cache', wp_kses_post($output));
        }
    }
    
    echo wp_kses_post($output);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/ranking.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        .ranking ul li span#range {
            max-height: 150px;
        }
        .ranking ul li span#range em span.wave{
            position: relative;
            z-index: 1;
        }
        #ranks b{
            margin: 5px auto auto;
            font-size: small;
            max-width: 6em;
        }
        .ranking ul li span#avatar::before{
            width: auto;
            padding: 0 5px;
        }
        .ranking ul li span#range em span.wave{
            overflow: hidden;
        }
        .ranking #ranked {
            margin-bottom: 5%;
        }
        .ranking #ranked,
        .ranks .ranking p {
            color: inherit;
        }
        .ranking #ranks img {
            min-width: 52px;
        }
        .ranking #rankest img {
            min-width: 72px;
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
                    the_comment_ranks('常客','近期访问较频繁的童鞋', '稀客','近期偶尔来访的小伙伴', '游客',''); 
                ?>
            </div>
            <?php 
                the_content();  // the_page_content(current_slug());
                // ads..
                adsense_shortcode('adsense_list_context');
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
                function get_range_max() {
                    let range_max = 0;
                    for (let i=0; i<limit; i++) {
                        let name = temps[i].n,
                            times = temps[i].t;
                        if (i < max && name != "匿名者" && name != "2broear") {
                            range_max += times;
                        }
                    }
                    return range_max;
                }
                function get_range_percent(num, max) {
                    if (isNaN(num) || isNaN(max)) {
                        alert('NaN: ', num, max);
                        return 0;
                    }
                    return (num / max) * 100;
                }
                const range_max = get_range_max();
                // console.log(range_max)
                for(let i=0;i<limit;i++){
                    let name = temps[i].n,
                        mail = temps[i].m,//md5(temps[i].m),
                        link = temps[i].l,
                        times = temps[i].t;
                    if(name!="匿名者"&&name!="2broear"){
                        <?php
                            $avatar = get_option('site_avatar_mirror').'avatar/${mail}?d=retro&s=100';
                            if ($lazysrc != 'src') {
                                $lazyhold = 'data-src="'.$avatar.'"';
                            } else {
                                $lazyhold = '';
                                $loadimg = $avatar;
                            }
                        ?>
                        link = link ? link : 'javascript:;';
                        if(i<max){
                            avg = avg += times;
                            average = avg/max;
                            remove_load(rankest);
                            temp_rankest.innerHTML += `<li><span id="avatar" data-t="${times}"><a href="${link}" target="_blank"><img <?php echo $lazyhold; ?> src="<?php echo $loadimg; ?>" title="这家伙留了 ${times} 条评论！" alt="${name}" /></a></span><span id="range"><em style="height:${get_range_percent(times, range_max)}%"><span class="wave active"></span></em></span><a href="${link}" target="_self"><b>${name}</b></a></li>`; // style="height:${average}px"
                            fragment_rankest.appendChild(temp_rankest);
                        }
                        if(i>=max && i<maxes){
                            remove_load(ranks);
                            temp_ranks.innerHTML += `<li title="TA 在本站已有 ${times} 条评论"><span id="avatar" data-t="${times}"><img <?php echo $lazyhold; ?> src="<?php echo $loadimg; ?>" alt="${name}" /></span><a href="${link}"><b data-mail="${temps[i].m}">${name}</b></a></li>`; //<sup>${times}+</sup>
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