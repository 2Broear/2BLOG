<?php
/*
 * Template name: （BaaS）友链模板
 * Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php echo $src_cdn; ?>/style/2bfriends.css?v=<?php echo get_theme_info(); ?>" />
    <?php get_head(); ?>
    <style>
        .friends-boxes .deals .inbox.girl em{background: url('<?php echo $img_cdn; ?>/images/girl_symbols.png') no-repeat center center /contain;}
        .friends-boxes .deals .inbox.girl::after{background: url('<?php echo $img_cdn; ?>/images/girl_symbols.png') center center /contain no-repeat;}
        .main,
        #vcomments{
            margin: auto!important;
        }
        .friends-boxes .deals.tech .inbox {
            max-width: calc(100% / 6.35);
        }
        .friends-boxes .deals.tech .inbox .inbox-aside span.lowside-title h4{
            max-width: 4em;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .friends-boxes .deals.tech .inbox:hover .inbox-aside span.lowside-title h4{
            max-width: 100%;
        }
        .friends-boxes .deals .inboxSliderCard {
            white-space: nowrap;
        }
        .friends-boxes .deals .inboxSliderCard.sliding {
            scroll-behavior: auto!important;
        }
        .friends-boxes .deals .inboxSliderCard.sliding .slideBox {
            display: table;
            display: inline-block;
            white-space: nowrap;
            transition: transform .35s ease;
        }
        .friends-boxes .deals .inboxSliderCard.sliding .slideBox a {
            /*padding: 0 15px 0 0;*/
            letter-spacing: 3px;
            writing-mode: tb-rl;
            -webkit-writing-mode: vertical-rl;
            will-change: transform;
        }
        .friends-boxes .deals .inboxSliderCard .slideBox a:last-child {
            padding: 0;
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
        <div class="content-all-windows" style="padding-top:0;">
            <div class="friends-boxes flexboxes">
                <?php 
                    $baas = get_option('site_leancloud_switcher') && strpos(get_option('site_leancloud_category'), basename(__FILE__))!==false; //in_array(basename(__FILE__), explode(',', get_option('site_leancloud_category')))
                    // 输出站点链接
                    function the_site_links($t1='小伙伴', $t2='技术の', $t3='荐见鉴') { //, $baas=false
                        global $baas;
                        if($baas) {
                            echo '<div class="inbox-clip"><h2 id="exchanged"> '.$t1.' </h2></div><div class="deals exchanged flexboxes"></div><!-- rcmd begain --><div class="inbox-clip"><h2 id="rcmded"> '.$t3.' </h2></div><div class="deals rcmd flexboxes"></div><!-- lost begain --><div class="inbox-clip"></div><div class="deals oldest"><div class="inboxSliderCard"><div class="slideBox flexboxes"></div></div></div>';
                            return;
                        };
                        $output = '';
                        $output_sw = false;
                        if(get_option('site_cache_switcher')) {
                            $caches = get_option('site_cache_includes');
                            $temp_slug = get_cat_by_template('2bfriends','slug');
                            $output_sw = in_array($temp_slug, explode(',', $caches));
                            $output = $output_sw ? get_option('site_link_list_cache') : '';
                        }
                        if(!$output || !$output_sw) {
                            $rich_links = get_site_bookmarks('standard');
                            $tech_links = get_site_bookmarks('technical');  // $tech_links = get_filtered_bookmarks('technical', 'others');
                            $rcmd_links = get_site_bookmarks('special', 'rand', 'DESC');
                            $other_links = get_site_bookmarks('others', 'link_id', 'DESC');
                            $stete_rich = $stete_tech = $stete_rcmd = '';
                            if(get_option('site_links_code_state')) {
                                $state_includes = get_option('site_links_code_state_cats');
                                $stete_rich = in_array('standard', explode(',', $state_includes));
                                $stete_tech = in_array('special', explode(',', $state_includes));
                                $stete_rcmd = in_array('special', explode(',', $state_includes));
                            }
                            $output .= $rich_links ? '<div class="inbox-clip"><h2 id="exchanged"> '.$t1.' </h2></div><div class="deals exchanged flexboxes">'.get_site_links($rich_links, 'full', '', $stete_rich).'</div>' : '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.current_slug(true).' </h1></div>';
                            if($tech_links) $output .= '<div class="inbox-clip"><h2 id="exchanged"> '.$t2.' </h2></div><div class="deals tech exchanged flexboxes">'.get_site_links($tech_links, 'full', '', $stete_tech).'</div>';
                            if($rcmd_links) $output .= '<div class="inbox-clip"><h2 id="rcmded"> '.$t3.' </h2></div><div class="deals rcmd flexboxes">'.get_site_links($rcmd_links, 'half', '', $stete_rcmd).'</div>';
                            if($other_links) $output .= '<div class="deals oldest"><div class="inboxSliderCard"><div class="slideBox flexboxes">'.get_site_links($other_links).'</div></div></div>';
                            if($output_sw) update_option('site_link_list_cache', wp_kses_post($output));
                        }
                        echo wp_kses_post($output);
                    }
                    the_site_links();
                    echo '<br />';
                    the_content();  // the_page_content(current_slug());
                    dual_data_comments();
                ?>
            </div>
        </div>
		<footer>
            <?php get_footer(); ?>
		</footer>
    </div>
<!-- siteJs -->
<script type="module">
    try {
        import("<?php echo $src_cdn; ?>/js/slidebox.js").then((res)=> {
            const { AutoSlideBox, Utils } = res;
            const slideBox = new AutoSlideBox({
                slideSpeed: Utils.BASIC.randomNumber(0.5),
                // slideDirection: 1,
                // slideSpeed: 10,
                // slideRound: 2,
                // slideRandom: false,
                // slideDebug: true,
                slideElements: {
                    slideFrame: document.querySelector('.inboxSliderCard'),
                    slideBox: document.querySelector('.slideBox'),
                }
            });
            // slideBox.#config.slideSpeed = slideBox.UTILS.randomNumber(0.5); // false
            // slideBox.setConfig('slideSpeed', slideBox.UTILS.randomNumber(0.5)); // true
            slideBox.initAnimation();
        });
    } catch(e) {
        console.warn("import err.", e)
    }
</script>
<!-- inHtmlJs -->
<!-- pluginJs !!! Cannot redefine property: applicationId (av-min must be same with valine.js cdn) !!! av-min.js must be load via dynamicLoad(use no raw function twice) to head js which allow init AV twice -->
<!--<script src="//cdn.jsdelivr.net/npm/leancloud-storage/dist/av-min.js"></script>-->
<!-- inHtmlJs -->
<?php
    get_foot();
    // declear lazyLoad standby-avatar(seo-fix alt tips)
    if($baas){
?>
    <script type="text/javascript">
        //request AV.Query
        const link_query_all = new AV.Query("link"),
              friends = document.querySelector(".friends-boxes .deals.exchanged"),
              special = document.querySelector(".friends-boxes .deals.rcmd"),
              others = document.querySelector(".friends-boxes .deals.oldest .inboxSliderCard .slideBox"),
              loadlist = ["friends", "special", "others"],
              fill = function() {
                var compare = {
                        "status": "others",
                        "mark": "special"
                    };
                for(key in compare){
                    if(key==compare[key]){
                        let el = eval(compare[key]);
                        el.innerHTML += template(name,desc,avatar,link,sex,ssl,status)
                        el.querySelector("#loading").remove()
                    }
                }
              },
              template = (name,desc,avatar,link,sex,ssl,status,rel,mark)=>{
                  var templates;
                  if(status=="others"){
                      templates = `<a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'nofollow'}">${name}</a>`
                  }else if(mark=="special"){
                      templates = `<div class="inbox flexboxes ${status} ${sex}"><a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'recommend'}"><span class="lowside-title"><h4>${name}</h4></span><span class="lowside-description"><p>${desc}</p></span></a></div>`
                  }else{
                      templates = `<div class="inbox flexboxes ${status} ${sex}"><div class="inbox-headside flexboxes"><a href="${link}" target="_blank" rel="${rel||'friends'}"><img class="lazy" data-src="${avatar}" src="${avatar}" alt="${name}" draggable="false" /><span class="ssl ${ssl}">${ssl}</span></a></div><a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'friends'}"><span class="lowside-title"><h4>${name}</h4></span><span class="lowside-description"><p>${desc}</p></span></a></div>`
                  };
                  return templates;
              };
        for(let i=0,listLen=loadlist.length;i<listLen;i++){
            let eachload = loadlist[i],
                loading = document.createElement("span"),
                evalload = eval(eachload);
            loading.id="loading";
            evalload.appendChild(loading);
        };
        link_query_all.addAscending("createdAt").limit(99).find().then(result=>{
            let marked = 0,
                dom=document.getElementById("left");
            for (let i=0; i<result.length;i++) {
                let res = result[i],
                    name = res.attributes.name,
                    desc = res.attributes.desc,
                    avatar = res.attributes.offline||res.attributes.online,
                    link = res.attributes.link,
                    sex = res.attributes.sex,
                    ssl = res.attributes.ssl,
                    mark = res.attributes.mark,
                    status = res.attributes.status,
                    sitelink = res.attributes.sitelink;
                mark=="friends"&&status!="others" ? marked++ : false;
                if(status=="others"){
                    others.innerHTML += template(name,desc,avatar,link,sex,ssl,status,'nofollow')
                }else if(mark=="special"){
                    special.innerHTML += template(name,desc,avatar,link,sex,ssl,status,'recommend',mark)
                }else{
                    friends.innerHTML += template(name,desc,avatar,link,sex,ssl,status,'friends')
                }
            };
            if(dom){
                marked<50 ? dom.innerHTML="友链数量即将达到 50 限制（当前：<b>"+marked+"</b>）在这之后" : dom.innerHTML="友链数量已达到限制<b> 50（<b>"+marked+"）</b>，";
            }
            // loading.remove();
            const loads = document.querySelectorAll("#loading");
            for(let i=0,loadLen=loads.length;i<loadLen;i++){
                loads[i].remove()
            }
        })
    </script>
<?php
    }
?>
</body></html>