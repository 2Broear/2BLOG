    
    (function(){
        const styleTitle1 = `font-size: 2rem;font-weight: 900;`,
              styleTitle2 = `font-style: oblique;font-size:12px;color: rgb(155,155,155);font-weight: 400;`,
              styleContent = `color: rgb(100,100,100);line-height:18px`,
              styleLight = `color:#3a3a3a;background:rgb(235,235,235);padding:5px 0;`,
              styleDark = `color:white;background:#3a3a3a;padding:5px 0;margin-bottom:10px`,
              title2 = `
    A wordpress theme Design & Devoloped via 2BROEAR open source in 2022`,
              content = ``;
        console.log(`%c2️⃣ 2 B L O G 🅱 %c${title2} %c \n 💻2BROEAR %c Release https://github.com/2Broear/2BLOG %c ${content} `, styleTitle1, styleTitle2, styleLight, styleDark, styleContent);
    })();
    
    function dynamicLoad(jsUrl,fn){
    	var _doc = document.getElementsByTagName('head')[0],
    		script = document.createElement('script');
    		script.setAttribute('type','text/javascript');
    		script.setAttribute('async',true);
    		script.setAttribute('src',jsUrl);
    		_doc.appendChild(script);
    	script.onload = script.onreadystatechange = function(){
    		if(!this.readyState || this.readyState=='loaded' || this.readyState=='complete'){
    			fn ? fn() : false;
    		}
    		script.onload = script.onreadystatechange = null;
    	};
    }
    
    function send_ajax_request(method,url,data,callback){
        var ajax = new XMLHttpRequest();
        if(method=='get'){  // GET请求
            data ? (url+='?',url+=data) : false;
            ajax.open(method,url);
            ajax.send();
        }else{  // 非GET请求
            ajax.open(method,url);
            ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");  // 设置请求报文
            data ? ajax.send(data) : ajax.send();
        }
        ajax.onreadystatechange = function () {
            if(ajax.readyState==4 && ajax.status==200){
                callback ? callback(ajax.responseText) : false;
            }else{
                // error ? error(ajax.responseText) : false;
            }
        };
    }
    
    function parse_ajax_parameter(data,decode){
        let str = "";
        for(let key in data){
            str += `${key}=${data[key]}&`;
        }
        str = str.substr(0,str.lastIndexOf("&"));
        return decode ? decodeURI(str) : str;
    }
    //https://www.jb51.net/article/216692.htm
    function lazyload(imgs){
        const bodyimg = document.querySelectorAll(imgs);
        if(bodyimg.length>=1){
            for(let i=0;i<bodyimg.length;i++){
                let eachimg = bodyimg[i],
                    datasrc = eachimg.dataset.src;
                if(datasrc){
                    eachimg.getBoundingClientRect().top < window.innerHeight ? eachimg.src = datasrc : false;
                    window.addEventListener('scroll', function(){
                        if(eachimg.getBoundingClientRect().top < window.innerHeight){ // height-sheight<=wheight
                            eachimg.src = eachimg.dataset.src; // 即时更新 eachimg.dataset.src 替代 datasrc
                            eachimg.onerror=function(){ //!this.complete
                                let loadimg = "https://img.2broear.com/images/loading_3_color_tp.png";
                                this.src = loadimg;
                                this.dataset.src = loadimg;
                            }
                        }
                    });
                }
            }
        }
    };
    // lazyload("body img");
    
    function setCookie(name,value,path,days){
        let exp = new Date();
        days = !days ? 30 : days;
        path = !path ? ";path=/" : path;
        exp.setTime(exp.getTime() + days*24*60*60);
        document.cookie = name+"="+escape(value)+";expires="+exp.toGMTString()+path;
    }
    function getCookie(cname){
        var name = cname+"=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c=c.substring(1);
            if(c.indexOf(name)!=-1) return c.substring(name.length, c.length);
        }
        return "";
    }
    function delCookie(name){
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval=getCookie(name);
        cval!=null ? document.cookie = name+ "="+cval+";expires="+exp.toGMTString()+";path=/" : false;
    }
    function darkmode(){
        setCookie('theme_manual',1,false);  // set cookie to manual (disable auto detect)
        getCookie('theme_mode')!="dark" ? setCookie('theme_mode','dark',false) : setCookie('theme_mode','light',false);
        document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
        console.warn(`theme_mode has changed: ${getCookie('theme_mode')}`);
    }
    
    const site_tool = document.querySelector(".functions-tool"),
          tool_gotop = site_tool.querySelector(".top"),
          tool_gobottom = site_tool.querySelector(".bottom"),
          progress_ball = site_tool.querySelector(".inside-functions"),
          progress_ball_percent = progress_ball.querySelector(".pagePer strong"),
          progress_ball_wave = progress_ball.querySelector(".pagePer i"),
          progress_ball_waves = progress_ball.querySelector(".pagePer i span"),
          progress_bar = document.querySelector(".top-bar-tips span#doc-progress-bar"),
          article_tool = document.querySelector(".news-article-head-tools"),
          sidebar_only = document.querySelector(".news-slidebar-window");
    // scrollTo原生api兼容ie处理 https://www.cnblogs.com/xieyongbin/p/11274959.html
    if (!window.scrollTo) {
    	window.scrollTo = function (x, y) {
    		window.pageXOffset = x;
    		window.pageYOffset = y;
    	};
    }
    if (!document.body.scrollTo) {
    	Element.prototype.scrollTo = function (x, y) {
    		this.scrollLeft = x;
    		this.scrollTop = y;
    	};
    }
    tool_gotop.onclick=()=>{window.scrollTo(0,0)};
    tool_gobottom.onclick=()=>{window.scrollTo(0,99999)};
    if(article_tool){
        const tool_view = article_tool.querySelector("#full-view em"),
              tool_font = article_tool.querySelector("#font-plus em"),
              tool_lang = article_tool.querySelector("#s2t2s-switch em"),
              article_container = document.querySelector(".news-article-container"),
              article_sidebar = document.querySelector(".news-slidebar-window"),
              article_window = document.querySelector(".news-article-window");
        var switcher = (e,els,cls,txt_on,txt_off,cbk_on,cbk_off,cookie)=>{
            let _this = e.target;
            if(els.className.match(cls)){
                els.classList.remove(cls);
                _this.innerText = txt_off;
                cbk_off&&typeof(cbk_off)==="function" ? cbk_off() : false;
                cookie ? setCookie(cookie,0,false) : false;
            }else{
                els.classList.add(cls);
                _this.innerText = txt_on;
                cbk_on&&typeof(cbk_on)==="function" ? cbk_on() : false;
                cookie ? setCookie(cookie,1,false) : false;
            }
        };
        // tool_lang.onclick=(e)=>{switcher(e,article_container,"s2t_active","繁","简")};
        tool_font.onclick=(e)=>{switcher(e,article_container,"AfontPlus","A-","A+",false,false,'article_fontsize')};
        tool_view.onclick=(e)=>{
            switcher(e,article_sidebar,"fv-switch","Sideview","Overview",function(){
                article_window.classList.add("fullview");
            },
            function(){
                article_window.classList.remove("fullview");
            },"article_fullview");
        };
    }
    const header = document.querySelector('.main-header-all'),
          headbar = document.querySelector('.top-bar-tips .tipsbox .tips'),
          headbar_np = headbar.querySelector('p#np'),
          footer = document.querySelector('.footer-all'),
          detect = footer.querySelector(".footer-detector"),
          sidebar = document.querySelector('.news-content-right-window-all'),
          sideAds = document.querySelector(".news-ppt"),
          inform = document.querySelector('.scroll-inform'),
          aindex = document.querySelector('.article_index'),
          share = document.querySelector('.share'),
          npost = document.querySelector('.tips-switch p#np'),
          aindex_cl = function(el,cl){
            for(let i=0;i<el.length;i++){
                el[i].classList.remove(cl);
            }
          },
          aindex_fn = function(){
            if(aindex){
                var aindexOffset = [],
                    max = aindex.dataset.index, //getAttribute('data-index'),
                    Constructor = function(index,offset){
                    this.index = index;
                    this.offset = offset;
                };
                for(let i=0;i<max;i++){
                    const each_index = document.querySelector('#title-'+i),
                          each_offset = each_index.offsetTop+300;
                    // each_index.setAttribute('data-offset',each_index.offsetTop);
                    aindexOffset.push(new Constructor(i, each_offset));
                }
                // console.log(aindexOffset);
                return aindexOffset;
            }
          },
          once_fn = function(fn,rt) {
            let called = false;
            return function(){
                if(!called){
                    called = true;
                    if(rt){
                        return fn.call(this,...arguments);
                    }else{
                        fn.call(this,...arguments);
                    }
                }
            };
          },
          aindex_once_data = once_fn(aindex_fn,true);
    // console.log(aindex_once_data());
    if(aindex){
        const aindex_icon = aindex.querySelector('p');
        aindex_icon.onclick=(e)=>{
            let that = e.target;
            if(aindex.classList.contains('fold')){
                // that.setAttribute('title','折叠目录');
                aindex.classList.remove('fold');
                setCookie('article_index', 1);  // disable fold
            }else{
                // that.setAttribute('title','展开目录');
                aindex.classList.add('fold');
                setCookie('article_index', 0);  // disable fold
            }
        };
    }
    var throttler = function(fn,delay){
            var timer = null;
            return function(e){
                if(timer==null){
                    timer = setTimeout(function(){
                        fn(e);
                        timer = null;  //消除定时器表示激活
                    },delay);
                }
            };
        },
        scroll_class = (el,add,remove,clear)=>{
            if(el){
                if(clear){
                    el.classList.remove(add,remove);
                }else{
                    remove&&remove!="" ? el.classList.remove(remove) : false;
                    add&&add!="" ? el.classList.add(add) : false;
                }
            }
        },
        closure_timer = null,
        scroll_record = 0,
        marginOffset = inform ? inform.offsetHeight+15 : 15,
        scroll_func = function(e,st){  //st
            e = e || window.event;
            let class_up = 'barSetUp',
                class_down = 'barSetDown',
                class_fixed = 'window-all-get-fixed';
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop,
                fixedSidebar = sidebar_only ? header.offsetHeight+(sideAds ? sideAds.offsetHeight+marginOffset : 0) : false,
                headbar_oh = headbar_np ? 100 : headbar.offsetHeight,
                footerDetect = sidebar_only ? detect.offsetTop-(headbar_oh+sidebar.offsetHeight) : false,  //-marginOffset
                roll_up = function(){  //上滚操作
                    //上滑至导航栏执行
                    if(scrollTop<=header.offsetHeight*2){
                        scroll_class(header,class_down,class_up,true);
                        scroll_class(headbar,null,"slide-down");
                        scroll_class(progress_ball,null,"pull-up");
                    }else{
                        scroll_class(header,class_down,class_up);
                    };
                    if(npost && share && scrollTop<=share.offsetTop){
                        scroll_class(headbar,null,"next-post");  //show next post
                    }
                    //侧边栏事件
                    if(sidebar_only){
                        //上滑至侧边栏执行
                        if(scrollTop<fixedSidebar){
                            scroll_class(sidebar,null,class_fixed);
                            sidebar.style.width = "";
                        }
                        //上滑小于侧边栏，大于底部栏+导航高度之间执行
                        if(scrollTop>fixedSidebar && scrollTop<footerDetect-header.offsetHeight){
                            sidebar.style.transform = `translateY(${header.offsetHeight}px)`;
                        }else{
                            sidebar.style.transform = "";
                        }
                        //上滑过底部栏后执行
                        if(scrollTop<footerDetect){
                            sidebar.style.height = "";
                            scroll_class(sidebar,null,"window-all-get-stoped");
                        }
                    }
                },
                roll_down = function(){  //下滚操作
                    //超过导航栏执行
                    if(scrollTop>=header.offsetHeight){
                        scroll_class(header,class_up,class_down);  //nav bar
                        scroll_class(headbar,"slide-down",null);
                        scroll_class(progress_ball,"pull-up",null);
                        if(npost && share && scrollTop>=share.offsetTop){
                            scroll_class(headbar,"next-post",null);  //show next post
                        }
                    }else{
                        scroll_class(headbar,null,class_up);
                    }
                    //侧边栏事件
                    if(sidebar_only){
                        //超过侧边栏执行
                        if(scrollTop>=fixedSidebar-5){
                            scroll_class(sidebar,class_fixed,null);
                            sidebar.style.width = sidebar.parentElement.offsetWidth+"px";
                        }
                        //到达底部检测栏执行
                        if(scrollTop>=footerDetect){
                            sidebar.style.height = sidebar.offsetHeight+"px";
                            scroll_class(sidebar,"window-all-get-stoped",null);
                            sidebar.parentElement.style.height = "100%";  //fix google ads load bug
                        }
                        //下滚始终执行
                        sidebar.style.transform = "";
                    }
                };
            // console.log(scrollTop);
            var aindexOffset = aindex_fn();  // always update(do not call aindex_once_data)
            // console.log(aindexOffset);
            if(aindex && aindexOffset.length>=1){
                const aindex_li = aindex.querySelectorAll('li');
                if(scrollTop<=aindexOffset[0].offset || scrollTop>=share.offsetTop){ //-100
                    aindex_cl(aindex_li,'current')
                }else{
                    aindexOffset.forEach(function (item) {
                        // if(item===3){
                        //     return;
                        // }
                        if(scrollTop>=item.offset){
                            // location.href='title-'+item.index;
                            aindex_cl(aindex_li,'current');
                            document.querySelector('#t'+item.index).classList.add('current');
                        }
                    });
                }
            }
            // https://stackoverflow.com/questions/31223341/detecting-scroll-direction
            scroll_foward = window.pageYOffset;  // Get scroll Value
            scroll_record-scroll_foward<0 ? roll_down() : roll_up();  // Subtract two and conclude
            scroll_record = scroll_foward;  // Update scrolled value
        };
    // document.addEventListener('DOMMouseScroll', scroll_func, false);  //DOMMouseScroll  // scroll 滚动+拖拽滚动条代替 wheel 滚动函数
    window.onscroll = function(e){
        //实时获取窗口/文档高度（常量几率导致获取不准确）
        var windowHeight = window.innerHeight,
            clientHeight = document.body.clientHeight,
            scrollTop = document.documentElement.scrollTop || document.body.scrollTop,
		    page_percent = Math.round((((scrollTop)/(clientHeight-windowHeight))*100));  //.toFixed(2)
        progress_ball_percent.innerText = page_percent+"%";
        progress_ball_wave.style.transform = `translateY(${100-page_percent}%)`;
        progress_ball_waves.classList.add("active");
        progress_bar.classList.add("active");
        progress_bar.style.opacity = 1;
        progress_bar.style.transform = `translateX(${page_percent-100}%)`;
        // 到达顶部（底部）执行
        // console.log(scrollTop+windowHeight+'='+clientHeight). // 0.5 offset
        if(scrollTop==0 || scrollTop+windowHeight>=clientHeight){
            progress_ball_waves.classList.remove("active");
            progress_bar.classList.remove("active");
        }
        scroll_func(e,scrollTop);  //滚动函数
        // throttler(scroll_func,1000)  //scroll_func(e)无法传参
        // return function(){
        //     if(closure_timer==null){
        //         closure_timer = setTimeout(function(){
        //             scroll_func(e,scrollTop);  //滚动函数
        //             closure_timer = null;  //重置闭包定时器
        //         },150);  //150ms执行一次滚动操作（存在响应不及时）不适用即时更新场景
        //     }
        // }()
    };
    
    // inform scroll_func
    var declear = function(els,cls,idx){
            for(let i=0;i<els.length;i++){
                els[i].classList.remove(cls)
            };
            idx!=undefined ? els[idx].classList.add(cls) : idx
        },
        flusher = (els,count,delay) =>{
            setInterval(() => {
                declear(els,"move",count)
                els[count].className = "move";  //current
                els[count+1] ? els[count+1].classList.add("show") : els[0].classList.add("show");
                count<els.length-1 ? count++ : count=0;
            }, delay)
        },
        informs = document.querySelectorAll('.scroll-inform div.scroll-block span');
    if(informs.length>0){
        informs[0].classList.add("showes");  //init first show(no trans)
        informs.length>1 ? flusher(informs,0,3000) : false;  //scroll inform
    }
    
    // moblie ux
    const search_btn = document.querySelector('.mobile-vision .m-search'),
          menu_btn = document.querySelector('.mobile-vision .m-menu'),
          slide_menu = document.querySelector('.slider-menu'),
          close_menu = slide_menu.querySelector('.slider-close'),
          menu_mask = document.querySelector('.windowmask'),
          toggleMenu = function(){
            let show = 'show';
            if(slide_menu.classList.contains(show)){
                document.body.style.overflowY = '';
                slide_menu.classList.remove(show)
                menu_mask.style.display = '';
            }else{
                document.body.style.overflowY = 'hidden';
                slide_menu.classList.add(show)
                menu_mask.style.display = 'block';
            }
          };
    search_btn.onclick=function(){
        let cls = 'searching',
            search = this.parentNode;
        search.classList.contains(cls) ? search.classList.remove(cls) : search.classList.add(cls);
    }
    menu_btn.onclick = close_menu.onclick = menu_mask.onmouseup = menu_mask.ontouchend = function(e){  //menu_mask.onmouseup
        // console.log(e)
        e.cancelable ? e.preventDefault() : e.stopPropagation();  // prevent penetrate a link
        toggleMenu()
    }
    