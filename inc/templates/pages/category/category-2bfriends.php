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
                            // $link_cats = get_links_category();
                            // asort($link_cats);
                            // foreach ($link_cats as $link_cat) {
                            //     $each_cat = $link_cat->slug;
                            //     $output_object = new stdClass();
                            //     $output_object->$each_cat = get_site_bookmarks($each_cat);
                            // }
                            $rich_links = get_site_bookmarks('standard');
                            $output .= $rich_links ? '<div class="inbox-clip"><h2 id="exchanged"> '.$t1.' </h2></div><div class="deals exchanged flexboxes">'.get_site_links($rich_links, 'full', 'standard').'</div>' : '<div class="empty_card"><i class="icomoon icom icon-'.current_slug().'" data-t=" EMPTY "></i><h1> '.current_slug(true).' </h1></div>';
                            $tech_links = get_site_bookmarks('technical');  // $tech_links = get_filtered_bookmarks('technical', 'others');
                            if($tech_links) $output .= '<div class="inbox-clip"><h2 id="exchanged"> '.$t2.' </h2></div><div class="deals tech exchanged flexboxes">'.get_site_links($tech_links, 'full', 'technical').'</div>';
                            
                            $rcmd_links = get_site_bookmarks('special', 'rand', 'DESC');
                            if($rcmd_links) $output .= '<div class="inbox-clip"><h2 id="rcmded"> '.$t3.' </h2></div><div class="deals rcmd flexboxes">'.get_site_links($rcmd_links, 'half').'</div>';
                            
                            $other_links = get_site_bookmarks('others', 'link_id', 'DESC');
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
<script type="text/javascript">
    // auto slideBox
    class slideBox {
        constructor() {
            this.elements = {
                slideCard: document.documentElement,
                slideBox: document.body,
            }
            this.data = {
                debugMode: false,
                slideRandom: true,
                slideAnimate: null,
                slideReverse: false,
                slideRestart: 1000,
                slideDirection: 0, //1,-1,0
                slideOffsetsX: 0,
                slideOffsetsY: 0,
                slideClass: 'sliding',
                slideSpeed: .25,
                slideRound: -1,
                slideCount: 0,
                slideWidth: 0,
                slideHeight: 0,
            }
            this.status = {
                // method
                isObject: (obj)=> Object.prototype.toString.call(obj) === '[object Object]',
                isElement: (node)=> node && node instanceof HTMLElement && node.nodeType === 1,
                isFunction: (func)=> func && typeof func === 'function',
                isNumber: (num, int = false)=> {
                    let isNum = !isNaN(num) && typeof num === 'number';
                    if (int) return this.status.isFunction(Number.isInteger) ? isNum && Number.isInteger(num) : isNum && num % 1 === 0;
                    return isNum;
                },
                // static
                isEvenNumber: (num)=> num & 1 && num % 2 === 0,
                isScrollToEnabled: ()=> this.status.isFunction(this.elements.slideCard.scrollTo),
                // dynamic
                isScrollAvailable: ()=> {
                    const validSlideElements = this.status.isElement(this.elements.slideCard) && this.status.isElement(this.elements.slideBox);
                    return validSlideElements && (this.data.slideDirection ? this.elements.slideCard.scrollHeight >= this.elements.slideCard.offsetHeight : this.elements.slideCard.scrollWidth >= this.elements.slideCard.offsetWidth); // use >= incase default elements(html,body) provided
                },
                isScrollToStart: ()=> this.data.slideDirection ? this.data.slideOffsetsY <= 0 : this.data.slideOffsetsX <= 0,
                isScrollToEnd: ()=> this.data.slideDirection ? this.data.slideOffsetsY >= this.data.slideHeight : this.data.slideOffsetsX >= this.data.slideWidth,
            }
            this.mods = {
                randomNumber(from = 0, to = 1, fix = 2) {
                    const random = (Math.random() * (to - from) + from);
                    return parseFloat(random.toFixed(fix));
                },
                confRewriter: function _confRewriter(rewrite = {}, preset = {}, merge = true) {
                    const result = { ...preset };
                    for (const key in rewrite) {
                        if (rewrite.hasOwnProperty(key)) {
                            const validObjects = this.status.isObject(result[key]) && this.status.isObject(rewrite[key]); // const validObjects = Object.prototype.toString.call(result[key])==='[object Object]' && Object.prototype.toString.call(rewrite[key][key])==='[object Object]';
                            if (!merge) {
                                // 递归合并对象
                                result[key] = validObjects ? _confRewriter(rewrite[key], result[key] || {}, merge) : rewrite[key];
                                continue;
                            }
                            switch (true) {
                                // 合并数组
                                case Array.isArray(result[key]) && Array.isArray(rewrite[key]):
                                    result[key] = [...new Set([...result[key], ...rewrite[key]])];
                                    break;
                                // 覆盖元素
                                case this.status.isElement(rewrite[key]): // rewrite[key] instanceof HTMLElement
                                    result[key] = rewrite[key];
                                    break;
                                // 递归合并对象
                                default:
                                    result[key] = validObjects ? _confRewriter(rewrite[key], result[key] || {}, merge) : rewrite[key];
                            }
                        }
                    }
                    return result;
                }
            }
            this.events = {
                bind(element, event, callback) {
                    element[event] = callback;
                },
                unbind(element, event, callback = null) {
                    element[event] = callback;
                }
            }
        }
        
        abortAnimation (animateKey, callback, delay = 0) {
            cancelAnimationFrame(animateKey);
            if (this.status.isFunction(callback)) {
                const restartDelay = delay ? delay : this.data.slideRestart;
                if (restartDelay === 0 && delay === 0) {
                    callback();
                    return;
                }
                const timer = setTimeout(()=> {
                    callback();
                    clearTimeout(timer);
                }, restartDelay);
                console.log(`animation(${animateKey}) abort, restart in ${restartDelay} ms..`);
                return;
            }
            console.log(`animation(${animateKey}) stoped(without callback).`);
        }
        
        startAnimation () {
            // must clear animation frame(if animateKey exists) before startAnimation
            if (this.data.slideAnimate) cancelAnimationFrame(this.data.slideAnimate);
            // requestAnimationFrame 中的箭头函数会确保 this 指向 slideBox 实例，从而避免 undefined 的问题 //()=>this.startAnimation()
            this.data.slideAnimate = requestAnimationFrame(this.startAnimation.bind(this));
            
            // scrollBy direction&reversible
            if (this.data.slideDirection) {
                this.data.slideReverse ? this.data.slideOffsetsY -= this.data.slideSpeed : this.data.slideOffsetsY += this.data.slideSpeed;
            } else {
                this.data.slideReverse ? this.data.slideOffsetsX -= this.data.slideSpeed : this.data.slideOffsetsX += this.data.slideSpeed;
            }
            // dynamic(sync) adjust overflows(max&min)
            if (this.data.slideOffsetsX < 0 || this.data.slideOffsetsY < 0) this.data.slideOffsetsX = this.data.slideOffsetsY = 0;
            if (this.data.slideOffsetsX > this.data.slideWidth || this.data.slideOffsetsY > this.data.slideHeight) {
                this.data.slideOffsetsX = this.data.slideWidth;
                this.data.slideOffsetsY = this.data.slideHeight;
            }
            
            // animation debug
            if (this.data.debugMode) {
                if (this.data.slideDirection) {
                    // if (this.data.slideHeight !== this.elements.slideCard.offsetHeight) console.warn(`slideHeight(${this.data.slideHeight}) !== slideCard.offsetHeight(${this.elements.slideCard.offsetHeight})!`);
                    console.log(this.data.slideOffsetsY, this.data.slideHeight);
                } else {
                    // if (this.data.slideWidth !== this.elements.slideCard.offsetWidth) console.warn(`slideWidth(${this.data.slideWidth}) !== slideCard.offsetWidth(${this.elements.slideCard.offsetWidth})!`);
                    console.log(this.data.slideOffsetsX, this.data.slideWidth)
                }
            }
            
            // animation start
            this.status.isScrollToEnabled ? this.elements.slideCard.scrollTo(this.data.slideOffsetsX, this.data.slideOffsetsY) : this.elements.slideBox.style.transform = `translate(-${this.data.slideOffsetsX}px, ${this.data.slideOffsetsY}px)`;
            
            // animation abort
            const isScrollToStart = this.status.isScrollToStart.call(this);
            const isScrollToEnd = this.status.isScrollToEnd.call(this);
            if (isScrollToStart || isScrollToEnd) {
                // specific rounds
                if (this.status.isNumber(this.data.slideRound, true) && this.data.slideRound >= 0) {
                    if (this.data.slideRound === 0) {
                        this.abortAnimation(this.data.slideAnimate, ()=> {
                            console.log(`slideCount(${this.data.slideCount})`, this.data);
                        });
                        // unbind events
                        this.events.unbind(this.elements.slideCard, 'onpointermove');
                        return;
                    }
                    ++this.data.slideCount; // use ++i insted of i++ unbind events
                    // prefix odd rounds
                    // if (!this.status.isEvenNumber(this.data.slideRound)) this.data.slideCount += 2;
                    if (this.data.slideCount >= this.data.slideRound) this.data.slideRound = 0;
                }
                // Infinity loop
                this.abortAnimation(this.data.slideAnimate, ()=> {
                    this.data.slideReverse = isScrollToEnd;  // reverse only if isScrollToEnd
                    if (this.data.slideRandom) this.data.slideSpeed = this.mods.randomNumber(0.25);
                    this.startAnimation();
                });
            }
        }
        
        initAnimation(_conf = {}) {
            // console.log(this.elements.slideCard.scrollWidth , this.elements.slideCard.offsetWidth, this.elements.slideCard)
            // rewrite custom arguments(data/elements only, before rewrite-elements)
            if (_conf.data) this.data = this.mods.confRewriter.call(this, _conf.data, this.data);
            if (_conf.elements) this.elements = this.mods.confRewriter.call(this, _conf.elements, this.elements);
            if (!this.status.isScrollAvailable()) {
                console.warn('invalid elements/scrollWidth/scrollHeight or slideDirection provided, check', this);
                return;
            }
            
            // update slideWidth/slideHeight after scrollWidth/scrollHeight updated.
            this.elements.slideCard.style.scrollBehavior = 'auto';
            this.elements.slideCard.classList.add(this.data.slideClass);
            if (this.data.slideDirection) {
                this.data.slideHeight = this.elements.slideCard.scrollHeight - this.elements.slideCard.offsetHeight; 
                if (this.data.slideHeight === 0) this.data.slideHeight = this.elements.slideCard.scrollHeight;
            } else {
                this.data.slideWidth = this.elements.slideCard.scrollWidth - this.elements.slideCard.offsetWidth;
                if (this.data.slideWidth === 0) this.data.slideWidth = this.elements.slideCard.scrollWidth;
            }
            
            // update dynamic status to static(multi-call performance issue)
            this.status.isScrollToEnabled = this.status.isFunction(this.elements.slideCard.scrollTo);
            
            // start animation
            this.startAnimation();
            console.log('animation init.', this);
            
            // bind events
            const that = this;
            this.events.bind(this.elements.slideCard, 'onpointermove', ((interval = 200)=> {
                let running = true;
                return function() {
                    if (!running) return;
                    running = false;
                    setTimeout(()=> {
                        that.abortAnimation(that.data.slideAnimate, that.startAnimation.bind(that), 0);
                        running = true;
                    }, interval);
                }
            })(500));
            // this.elements.slideCard.onpointerenter = ((interval = 200)=> {
            //     if (!that.data.slideAnimate) {
            //         console.log('non pointer exists.');
            //         return;
            //     };
            //     let debouncer = null;
            //     return function() {
            //         if(debouncer) clearTimeout(debouncer);
            //         debouncer = setTimeout(()=> {
            //             // remember to 'bind' that-to-this points
            //             that.abortAnimation(that.data.slideAnimate, that.startAnimation.bind(that));
            //         }, interval);
            //     }
            // })(250);
        }
    }
    
    const slideBoxes = new slideBox();
    // slideBoxes.data.slideSpeed = slideBoxes.mods.randomNumber(0.5);
    slideBoxes.initAnimation({
        data: {
            // slideSpeed: slideBoxes.mods.randomNumber(0.5),
            // slideDirection: 1,
            // slideSpeed: 10,
            // slideRound: 2,
            // slideRandom: false,
            // debugMode: true,
        },
        elements: {
            slideCard: document.querySelector('.inboxSliderCard'),
            slideBox: document.querySelector('.slideBox'),
        }
    });
    
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