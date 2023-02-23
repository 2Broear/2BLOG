<?php
/*
 * Template name: 友链模板（BaaS）
 * Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link type="text/css" rel="stylesheet" href="<?php custom_cdn_src(); ?>/style/2bfriends.css?v=<?php echo get_theme_info('Version'); ?>" />
    <?php get_head(); ?>
    <style>
        details{
            width: 100%;
            text-align: left;
            margin-bottom: 15px;
        }
        @keyframes spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}
        #loading{position:relative;padding:20px;display:block;height:80px;margin: 0 auto;}
        #loading:before{-webkit-box-sizing:border-box;box-sizing:border-box;content:"";position:absolute;display:inline-block;top:20px;left:50%;margin-left:-20px;width:40px;height:40px;border:6px double #a0a0a0;border-top-color:transparent;border-bottom-color:transparent;border-radius:50%;-webkit-animation:spin 1s infinite linear;animation:spin 1s infinite linear}
        .main{margin-bottom:auto}
        .friends-boxes .deals .inbox.girl em{background: url('<?php custom_cdn_src('img'); ?>/images/girl_symbols.png') no-repeat center center /contain;width: 80px;height: 100px;}
        .friends-boxes .inbox-clip h2:before{
            box-shadow: -78px 0 #27c93f, 78px 0 #ffbd2e;
        }
        .friends-boxes .inbox-clip h2:after{
            height: 18px!important;
            box-shadow: -88px 0 #ffbd2e, 88px 0 #27c93f;
        }
        .friends-boxes .deals.rcmd .inbox{
            max-width: calc(100%/5.8);
        }
        .friends-boxes .deals .inbox.standby .inbox-headside img{
            border-radius: 0;
        }
        .friends-boxes .deals .inbox.girl::after{
            content: "";
            width: 80px;height: 100px;
	        background: url('<?php custom_cdn_src('img'); ?>/images/girl_symbols.png') center center /contain no-repeat;
	        right: -12px;
        }
        .friends-boxes ul, .friends-boxes ol{margin-bottom: auto;}
        /*.friends-boxes .deals .inbox .inbox-headside img{*/
        /*    transition: none;*/
        /*}*/
        .friends-boxes .deals .inbox:hover{
            transform: scale(.85);
        }
        .friends-boxes .deals .inbox:hover > .inbox-headside img{
            border-radius: 0;
        }
        .friends-boxes .deals .inbox:hover > .inbox-headside{
            display: inherit;
            width: 100%;
            position: absolute;
            filter: opacity(.1) blur(15px) saturate(1.5);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
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
                    $baas = get_option('site_leancloud_switcher')&&strpos(get_option('site_leancloud_category'), basename(__FILE__))!==false;
                    if(!$baas){
        	            $rich_links = get_bookmarks(array(
        	                'orderby' => 'link_id',
        	                'order' => 'ASC',  // 最新排最后
        	                'category_name' => "standard",
        	                'hide_invisible' => 0
    	                ));
        	            $rcmd_links = get_bookmarks(array(
        	                'orderby' => 'rand',  // random orderby
        	                'order' => 'DESC',
        	                'category_name' => "special",
        	                'hide_invisible' => 0
    	                ));
        	            $lost_links = get_bookmarks(array(
        	                'orderby' => 'link_id',  //updated link_updated
        	                'order' => 'DESC',  // 最新排最前
        	                'category_name' => "missing",
        	                'hide_invisible' => 0,
        	               // 'show_updated' => 1
    	                ));
    	               // print_r($rcmd_links);
                        if(count($rich_links)>0){
                ?>
                            <div class="inbox-clip"><h2 id="exchanged"> 小伙伴们 </h2></div>
                            <div class="deals exchanged flexboxes"><?php site_links($rich_links, 'full'); ?></div>
				<?php 
                        }else{
                            echo '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.current_slug(true).' </h1></div>';
                        };
                        if(count($rcmd_links)>0){
                ?>
                            <div class="inbox-clip"><h2 id="rcmded"> 荐亦有见 </h2></div>
                            <div class="deals rcmd flexboxes"><?php site_links($rcmd_links, 'half'); ?></div>
				<?php 
                        };
                        if(count($lost_links)>0){
                ?>
                            <div class="deals oldest">
            					<div class="inboxSliderCard">
                                    <div class="slideBox flexboxes"><?php site_links($lost_links, 'none'); ?></div>
            					</div>
            				</div>
				<?php 
                        }
				    }else{
		        ?>
                        <div class="inbox-clip"><h2 id="exchanged"> 小伙伴们 </h2></div>
                        <div class="deals exchanged flexboxes"></div>
                        <!-- rcmd begain -->
                        <div class="inbox-clip"><h2 id="rcmded"> 荐亦有见 </h2></div>
                        <div class="deals rcmd flexboxes"></div>
                        <!-- lost begain <div class="inbox-clip"></div> -->
                        <div class="deals oldest">
        					<div class="inboxSliderCard">
                                <div class="slideBox flexboxes"></div>
        					</div>
        				</div>
		        <?php
				    }
				?>
                <!--<div class="inbox-clip">-->
                <!--    <h2 id="rcmded"> 友链说明 </h2>-->
                <!--</div>-->
                <br />
                <?php
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
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<!-- inHtmlJs -->
<!-- pluginJs !!! Cannot redefine property: applicationId (av-min must be same with valine.js cdn) !!! av-min.js must be load via dynamicLoad(use no raw function twice) to head js which allow init AV twice -->
<!--<script src="//cdn.jsdelivr.net/npm/leancloud-storage/dist/av-min.js"></script>-->
<!-- inHtmlJs -->
<?php
    // declear lazyLoad standby-avatar(seo-fix alt tips)
    if(get_option('site_lazyload_switcher')){
?>
        <script>
            const standbyimg = document.querySelectorAll(".friends-boxes .deals .inbox.standby img");
            if(standbyimg.length>=1){
                for(let i=0;i<standbyimg.length;i++){
                    standbyimg[i].removeAttribute('src');
                }
            }
        </script>
<?php
    };
    if($baas){
?>
    <script type="text/javascript">
        //request AV.Query
        const link_query_all = new AV.Query("link"),
              friends = document.querySelector(".friends-boxes .deals.exchanged"),
              special = document.querySelector(".friends-boxes .deals.rcmd"),
              missing = document.querySelector(".friends-boxes .deals.oldest .inboxSliderCard .slideBox"),
              loadlist = ["friends","special","missing"],
              fill = function() {
                var compare = {
                        "status": "missing",
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
                  if(status=="missing"){
                      templates = `<a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'nofollow'}">${name}</a>`
                  }else if(mark=="special"){
                      templates = `<div class="inbox flexboxes ${status} ${sex}"><a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'recommend'}"><span class="lowside-title"><h4>${name}</h4></span><span class="lowside-description"><p>${desc}</p></span></a></div>`
                  }else{
                      templates = `<div class="inbox flexboxes ${status} ${sex}"><div class="inbox-headside flexboxes"><a href="${link}" target="_blank" rel="${rel||'friends'}"><img class="lazy" data-src="${avatar}" src="${avatar}" alt="${name}" draggable="false" /><span class="ssl ${ssl}">${ssl}</span></a></div><a href="${link}" class="inbox-aside" target="_blank" rel="${rel||'friends'}"><span class="lowside-title"><h4>${name}</h4></span><span class="lowside-description"><p>${desc}</p></span></a></div>`
                  };
                  return templates;
              };
        for(let i=0;i<loadlist.length;i++){
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
                mark=="friends"&&status!="missing" ? marked++ : false;
                if(status=="missing"){
                    missing.innerHTML += template(name,desc,avatar,link,sex,ssl,status,'nofollow')
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
            for(let i=0;i<loads.length;i++){
                loads[i].remove()
            }
        })
    </script>
<?php
    }
?>
</body></html>