    
    (function(){
        const styleTitle1 = `font-size: 2rem;font-weight: 900;`,
              styleTitle2 = `font-style: oblique;font-size:12px;color: rgb(155,155,155);font-weight: 400;`,
              styleContent = `color: rgb(100,100,100);line-height:18px`,
              styleLight = `color:#3a3a3a;background:rgb(235,235,235);padding:5px 0;`,
              styleDark = `color:white;background:#3a3a3a;padding:5px 0;margin-bottom:10px`,
              title2 = `A wordpress theme Design & Devoloped via 2BROEAR open source in 2022`;
        console.debug(`%c2️⃣ 2 B L O G 🅱 %c${title2} %c \n 💻2BROEAR %c Release https://github.com/2Broear/2BLOG %c `, styleTitle1, styleTitle2, styleLight, styleDark, styleContent);
    })();
    
    // if(!raf_available){
        raf_available = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
    // }
    //https://www.jb51.net/article/216692.htm
    function loadlazy(imgs,offset=0, scroll=true){
        const imglist = document.querySelectorAll(imgs),
              loadimg = "https://img.2broear.com/images/loading_3_color_tp.png";
        if(imglist.length<=0) return;
        var time_delay = 500,
            msgObject = Object.create(null),
            loadArray = Array.apply(null, imglist), //[...imglist]
            arrayChunk = function(array, process, context=null, args){
                setTimeout(function(){
                    var item = array.shift();
                    process.apply(context, [item, args]);
                    if (array.length > 0){
                        setTimeout(arguments.callee, 50);
                    }
                }, 100);
            },
            processImage = function(image, imgList=null){
                const updateList = Object.prototype.toString.call(imgList)=='[object Array]';
                let datasrc = image.dataset.src;
                image.src = loadimg; //pre-holder(datasrc only)
                // console.debug('processing..', image, loadArray);
                // !!!LONG TASK engaged!!!
                if(image.getBoundingClientRect().top >= window.innerHeight) return;
                // console.log('onsreen image: ',image);
                image.src = datasrc; // 即时更新 image（设置后即可监听图片 onload 事件）
                // 使用 onload 事件替代定时器或Promise，判断已设置真实 src 的图片加载完成后再执行后续操作
                image.onload=function(){
                    if(this.getAttribute('src')===datasrc){
                        if(updateList) imgList.splice(imgList.indexOf(this), 1);  // 移除已加载图片数组（已赋值真实 src 情况下）
                        this.onload = null;  //fix bug: can't modify img-src(this.src = datasrc;)
                    }else{
                        this.removeAttribute('data-src'); // disable loading
                        // bug: can't modify img-src
                        this.src = datasrc;  // this.src will auto-fix [http://] prefix
                        time_delay = 3*time_delay;  //increase delay (decrease request)
                        console.log(time_delay);
                    }
                };
                // handle loading-err images image.onerror=()=>this.src=loading;
                image.onerror=function(){
                    if(updateList) imgList.splice(imgList.indexOf(this), 1);  // 移除错误图片数组
                    this.removeAttribute('src');
                    this.removeAttribute('data-src'); // disable loading
                    if(!this.dataset.err){
                        this.setAttribute('alt','图片请求出现问题');
                        this.id = 'err';
                    }
                };
                // image = null;
            },
            autoLoad = function(imgLoadArr){
                if(Object.prototype.toString.call(imgLoadArr)=='[object Array]'){
                    let arrLen = imgLoadArr.length;
                    for(let i=0;i<arrLen;i++){
                        new Promise(function(resolve,reject){
                            resolve(imgLoadArr);
                        }).then(function(res){
                            processImage(res[i], res); //imgLoadArr[i]
                        }).catch(function(err){
                            console.log(err);
                        });
                    }
                    return;
                };
                // single image: updateList === loadArray but imgLoadArr(concated array)
                processImage(imgLoadArr, loadArray);  // imgLoadArr
            },
            scrollLoad = closure_throttle((e)=>{
                if(loadArray.length<=0){
                    console.log(Object.assign(msgObject, {msg:'lazyload done', code:200}));
                    window.removeEventListener('scroll', scrollForRemove, true);
                    return;
                };
                arrayChunk(loadArray.concat(), autoLoad); // autoLoad(loadArray);
            }, time_delay),
            scrollForRemove = function(event){
                let e = event || window.event,
                    t = e.target || e.srcElement;
                if(t!==document) return;
                raf_available ? window.requestAnimationFrame(scrollLoad) : scrollLoad();
            };
        arrayChunk(loadArray.concat(), autoLoad); // autoLoad(loadArray);
        // requestAnimationFrame support
        if(!scroll) return;
        window.addEventListener('scroll', scrollForRemove, true);
    }
    
    function setupVideoPoster(second,quality,base64){
        const videos = document.querySelectorAll('video');
        var msgJson = Object.create(null);
        if(!videos[0]){
            console.log(Object.assign(msgJson, {msg:'setupVideoPoster Not found', code:404}));
            return;
        }
        for(let i=0,vdoLen=videos.length;i<vdoLen;i++){
            let video = videos[i];
            // return new Promise(function (resolve, reject) {  // RETURN caused outside-loop array length calc-err
            new Promise(function(resolve, reject){
                if(video.autoplay){
                    reject(Object.assign(msgJson, {msg:'setupVideoPoster Abort on autoplay', code:'v'+i}));
                    return;
                }
                let vdo = document.createElement('video');
                quality = quality ? quality : 0.5;
                vdo.currentTime = second ? second : 1;  // 设置当前帧
                vdo.setAttribute('src', video.src);
                vdo.setAttribute('crossOrigin', 'Anonymous'); // 处理跨域
                vdo.setAttribute('autoplay', true);
                vdo.setAttribute('muted', true);
                vdo.setAttribute('preload', 'auto'); // auto|metadata|none
                vdo.addEventListener('loadeddata', function(){
                    const canvas = document.createElement('canvas'),
                          width = vdo.videoWidth, ///1.5width = vdo.width,
                          height = vdo.videoHeight; ///1.5height = vdo.height;
                    canvas.width = width;
                    canvas.height = height;
                    canvas.getContext('2d').drawImage(vdo, 0, 0, width, height); // 绘制 canvas
                    vdo.pause();
                    vdo.removeAttribute('preload');  // 阻止临时创建的视频在 network 中持续加载耗费网络资源
                    if(base64){
                        resolve([video, canvas.toDataURL('image/jpeg', quality)]);
                    }else{
                        canvas.toBlob(function(blob){
                            resolve([video, URL.createObjectURL(blob)]);
                        },"image/jpeg",quality);
                    }
                });
            }).then(function(res){
                let video = res[0],
                    check = video.src.match(/\.(?:avi|mp4|mov|mpg|mpeg|flv|swf|wmv|wma|rmvb|mkv)$/i);
                if(!video || !check){
                    console.log(Object.assign(msgJson, {msg:'setupVideoPoster Error', code:'v'+i}));
                    return;
                }
                video.setAttribute('poster', res[1]);
                console.log(Object.assign(msgJson, {msg:'setupVideoPoster Done', code:'v'+i}));
            }).catch(function(err){
                console.log(err);
            });
        }
    }
    
    function setVideoPoster(curTime,imgSize,imgType){
        (async()=>{
            const videos = document.querySelectorAll('video');
            if(!videos[0]) return;
            curTime = curTime ? curTime : 0;
            imgSize = imgSize ? imgSize : 0.5;  // 默认减半质量
            for(let i=0,vdoLen=videos.length;i<vdoLen;i++){
                let video = videos[i],
                    check = video.src.match(/\.(?:avi|mp4|mov|mpg|mpeg|flv|swf|wmv|wma|rmvb|mkv)$/i), //video.src.match(/^(.*)(\.)(.{1,8})$/)[3],
                    dataURL = await this.getVideoFrames(video.src,curTime,imgSize,imgType); // video的url
                if(check) {
                    video.setAttribute('poster', dataURL);
                    return;
                }
                console.log('video Extention err');
            }
        })();
    }
    
    
    //https://www.jianshu.com/p/1dc6909e9456
    function raf_animate(cb,time){
        let myReq;    // 记录requestAnimationFrame的返回值
        let i = 1;    // 记录requestAnimationFrame的执行次数（屏幕刷新次数）
        myReq = requestAnimationFrame(function fn(){    // 开启初始requestAnimationFrame
            // 计数器 % (60/一秒钟执行的次数)
            if(i%parseInt(60/(1000/time)) == 0){
                cb();    // 执行真正要做的事情
            }
            i++;    // 记录requestAnimationFrame执行的次数
            myReq = requestAnimationFrame(fn);    // 开启下次requestAnimationFrame
            window.myReq = myReq;    // 将requestAnimationFrame返回值暴露，方便清除
        });
    }
    //https://www.cnblogs.com/yu01/p/15493430.html
    function raf_enqueue(enqueue=false, callback=false, ms=100, i=1, init=0){
        const exec = (rid,init)=>{
                  callback&&typeof callback==='function' ? callback(init) : false;
                  cancelAnimationFrame(rid);
              };
        return (function raf_queue(){
            init++;  // exec at returns
            rafId = window.requestAnimationFrame(raf_queue);
            // console.log('i'+init+'>='+i*ms); //debugger
            if(enqueue){
                init>=i*ms ? exec(rafId,init) : false;
            }else{
                exec(rafId,init);
            }
        })();
    }
    // setTimeout enqueue tasks
    function sto_enqueue(list, enqueue=false, callback=false, ms=100){
        if(!list[0]) return;
        for(let i=0,listLen=list.length;i<listLen;i++){
            let called = callback&&typeof callback==='function';
            if(enqueue){
                var inOrder = setTimeout(()=>{
                    called ? callback(i) : false;
                    inOrder = null;
                    clearTimeout(inOrder);
                }, i*ms);
            }else{
                called ? callback(i) : false;
            }
        }
    }
    // async await Promise enqueue tasks
    async function async_enqueue(list, enqueue, callback, ms=100){
        if(!list[0]) return;
        for(let i=0,listLen=list.length;i<listLen;i++){
            if(callback&&typeof callback==='function') await setTimeout(()=>callback(i), i*ms);
        }
    }
    
    // const raf_available already defineded in footer
    function dataDancing(list,target,offset=0,interval=100,append=''){
        if(!list[0]) return;
        // sto_enqueue(list, true, function(i){
        //     list[i].querySelector(target).innerHTML = (i++)+append;
        // });
        const execDance = function(each,counter,limit){
            each.classList.remove('blink');
            let times = -limit-offset,
                init = 0,
                inOrder = function(){
                    clearInterval(noOrder);
                    init<=limit ? counter.innerHTML = (init++)+append : clearInterval(noOrder);
                        times>=0 ? (times=0,clearInterval(noOrder)) : times++;
                    noOrder = setInterval(inOrder, init+times);
                };
            var noOrder = setInterval(inOrder);
        };
        for(let i=0,listLen=list.length;i<listLen;i++){
            let each = list[i],
                counter = each.querySelector(target),
                limit = parseInt(each.dataset.count);
            counter.innerHTML = '0'+append; //init counts
            if(raf_available){
                // requestAnimationFrame
                raf_enqueue(true, function(){
                    execDance(each,counter,limit);
                }, interval, i);
            }else{
                execDance(each,counter,limit);
            }
        }
    }
    
    function easeCounter (from = 0, to = 1, speed = 0.1, delta = 1, callback = false, easingFn = false, max = 100, min = 1) {
        let timer;
        let progress = 0, 
            direction = 1; // 1 for increasing progress, -1 for decreasing
        const deltaSpeed = speed * delta;
        const totalSteps = Math.abs((to - from) / deltaSpeed);
        return (function runCounter() {
            if (from === to) {
                if (timer) clearTimeout(timer);
                return from;
            }
            // Update the value
            if (from < to) {
                from += deltaSpeed;
                if (from > to) from = to;
            } else {
                from -= deltaSpeed;
                if (from < to) from = to;
            }
            
            // Update progress
            progress += direction;
            if (progress >= totalSteps || progress <= 0) {
                direction *= -1; // Reverse direction
            }
            /* easing effects: 
             / slow-fast-slow: (x) => Math.sin(x * Math.PI);
             / slow-fast: (x) => Math.pow(1 - x, 2);
             / fast-slow: (x) => Math.pow(x, 2);
            */
            if (typeof easingFn !== 'function') easingFn = (x) => Math.sin(x * Math.PI);
            // Calculate delay using a sine wave to create a slow-fast-slow effect
            const normalizedProgress = progress / totalSteps;
            const delay = min + (max - min) * (1 - easingFn(normalizedProgress));
            // Set the next timer
            timer = setTimeout(runCounter, delay);
            callback?.(from, delay);
        })();
    }
    
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
    
    // function getParByCls(curEl, parCls){
    //     while(!curEl || !curEl.classList || !curEl.classList.contains(parCls)){
    //         if(!curEl) break;  //return undefined
    //         curEl = curEl.parentNode; //parentElement
    //     };
    //     return curEl;
    // };
    
    
    function parse_ajax_parameter(data, decode=true){
        let str = "&";
        for(let key in data){
            str += `${key}=${data[key]}&`;
        }
        str = str.substr(0,str.lastIndexOf("&"));
        return decode ? decodeURI(str) : str;
    }
    function send_ajax_request(method,url,data,callback=false,catchback=false){
        return new Promise(function(resolve,reject){
            var ajax = new XMLHttpRequest();
            if(!ajax){
                let image = new Image();
                image.src = url+='?'+data;
                return;
            }
            if(method.toLowerCase()=='get'){  // GET请求
                if(data){
                    url+='?';
                    url+=data
                }
                ajax.open(method,url);
            }else{  // 非GET请求
                if(method=='') method = 'post';
                ajax.open(method,url);
                ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");  // 设置请求报文
            }
            ajax.onreadystatechange=function(){
                if(this.readyState!=4) return;
                if(this.status==200){
                    callback&&typeof callback==='function' ? resolve(callback(this.responseText)) : resolve(this.responseText);
                }else{
                    reject(this.status);
                }
            };
            data ? ajax.send(data) : ajax.send();
        }).catch(function(err){
            console.log(err);
            if(catchback&&typeof catchback==='function') catchback(err); //catchback.apply(this, err);
        });
    }
    
    function setCookie(name,value,path='/',days=0){
        let exp = new Date();
        let exp_time = typeof days=='number'&&days>=1 ? days*(24*60*60*1000) : 24*60*60*500;
        exp.setTime(exp.getTime() + exp_time);
        document.cookie = name+"="+escape(value)+";expires="+exp.toGMTString()+";path=/";
    }
    function getCookie(cname){
        var name = cname+"=";
        var ca = document.cookie.split(';');
        for(var i=0,caLen=ca.length; i<caLen; i++) {
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
        setCookie('theme_manual',1);  // set cookie to manual (disable auto detect)
        getCookie('theme_mode')!="dark" ? setCookie('theme_mode','dark','/',1) : setCookie('theme_mode','light','/',1);
        document.body.className = getCookie('theme_mode');  //change apperance after cookie updated
        console.warn(`theme_mode has changed: ${getCookie('theme_mode')}`);
    }
    
    /*  
     *  
     *  
     *   DIY CUSTOM FUNCTIONS
     *  
     *  
     */
     
    const header = document.querySelector('.main-header-all'),
          headbar = document.querySelector('.top-bar-tips .tipsbox .tips'),
          inform = document.querySelector('.scroll-inform'),
          informs = inform ? inform.querySelectorAll('.scroll-inform div.scroll-block span') : false,
          article_tool = document.querySelector(".news-article-head-tools"),
          aindex = document.querySelector('.article_index'),
          share = document.querySelector('.share'),
          npost = document.querySelector('.tips-switch p#np'),
          sidebar_window = document.querySelector(".news-slidebar-window"),
          sidebar_float = sidebar_window ? sidebar_window.querySelector('.news-content-right-window-all') : false,
          sidebar_ads = sidebar_window ? sidebar_window.querySelector(".news-ppt") : false,
          footer = document.querySelector('.footer-all'),
          site_tool = document.querySelector(".functions-tool"),
          progress_ball = site_tool ? site_tool.querySelector(".inside-functions") : null,
          progress_wave = progress_ball ? progress_ball.querySelector(".pagePer i span") : null,
          progress_bar = document.querySelector(".top-bar-tips span#doc-progress-bar"),
    	  class_up = 'barSetUp',
          class_down = 'barSetDown',
          class_fixed = 'window-all-get-fixed',
          marginOffset = inform ? inform.offsetHeight+15 : 15,
          aindex_fn = function(offset=300){
              if(!aindex) return;
              var aindexOffset = [];
                //   Constructor = function(index, offsets){
                //       this.index = index;
                //       this.offset = offsets;
                //   };
              for(let i=0;i<aindex.dataset.index;i++){
                  const each_index = document.querySelector('#title-'+i),
                        each_offset = each_index ? each_index.offsetTop+offset : 0;
                  aindexOffset.push(each_offset); //new Constructor(i, each_offset)
              }
              return aindexOffset;
          },
        //   once_fn = function(fn,rt) {
        //     let called = false;
        //     return function(){
        //         if(called) return;
        //             called = true;
        //             if(rt){
        //                 return fn.call(this,...arguments);
        //             }else{
        //                 fn.call(this,...arguments);
        //             }
        //     };
        //   },
        //   aindex_once_data = once_fn(aindex_fn,true),
          class_switch = function(el,add,remove,clear){
                if(!el) return;
                if(clear){
                    el.classList.remove(add,remove);
                }else{
                    remove&&remove!="" ? el.classList.remove(remove) : false;
                    add&&add!="" ? el.classList.add(add) : false;
                }
          },
          declear = function(els,cls,idx){
                for(let i=0,elsLen=els.length;i<elsLen;i++){
                    els[i].classList.remove(cls)
                };
                idx!=undefined ? els[idx].classList.add(cls) : idx
           };
    
    
    const scrollToTop = () => {
        const c = document.documentElement.scrollTop || document.body.scrollTop 
        if (c > 0) {  
            window.requestAnimationFrame(scrollToTop) 
            window.scrollTo(0, c - c / 8) 
        }
    }
    // scrollTo原生api兼容ie处理 https://www.cnblogs.com/xieyongbin/p/11274959.html
    if(!window.scrollTo){
    	window.scrollTo = function (x, y) {
    		window.pageXOffset = x;
    		window.pageYOffset = y;
    	}
    }
    if(!document.body.scrollTo){
    	Element.prototype.scrollTo = function (x, y) {
    		this.scrollLeft = x;
    		this.scrollTop = y;
    	}
    }
    
    if (site_tool) {
        // scrollTo && article_tool
        site_tool.querySelector(".top").onclick=()=>window.scrollTo(0,0);
        site_tool.querySelector(".bottom").onclick=()=>window.scrollTo(0,99999);
        // site_tool.querySelector(".top").onclick=()=>window.requestAnimationFrame(function(){window.scrollTo(0,0);});
        // site_tool.querySelector(".bottom").onclick=()=>window.requestAnimationFrame(function(){window.scrollTo(0,99999);});
    }
    
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
        article_tool.querySelector("#font-plus em").onclick=(e)=>{
            switcher(e,article_container,"AfontPlus","A-","A+",false,false,'article_fontsize');
        }
        article_tool.querySelector("#full-view em").onclick=(e)=>{
            switcher(e,article_sidebar,"fv-switch","展开边栏","全屏阅读",function(){
                article_window.classList.add("fullview");
            },function(){
                article_window.classList.remove("fullview");
            },false); //"article_fullview"
        }
    }
    
    // inform scroll_func
    if(informs && informs.length>0){
        const cls_move = "move",
              cls_show = "show";
        informs[0].classList.add("showes");  //init first show(no trans)
        if(informs.length>1){
            (function(els,count,delay){
                setInterval(() => {
                    declear(els, cls_move, count)
                    els[count].className = cls_move;  //current
                    els[count+1] ? els[count+1].classList.add(cls_show) : els[0].classList.add(cls_show);
                    count<els.length-1 ? count++ : count=0;
                }, delay)
            })(informs, 0, 3000);
        }
    }
    
    // console.log(aindex_once_data());
    if(aindex){
        aindex.querySelector('p').onclick=(e)=>{
            if(aindex.classList && aindex.classList.contains('fold')){
                aindex.classList.remove('fold');
                setCookie('article_index', 1);  // disable fold
            }else{
                aindex.classList.add('fold');
                setCookie('article_index', 0);  // disable fold
            }
        };
    }
    
    var scroll_throttler = null,
        scroll_record = 0,
        scroll_delay = 300,
        scroll_func = function(){
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop,
                clientHeight = document.body.clientHeight,
                windowHeight = window.innerHeight,
    		    page_percent = Math.round((scrollTop/(clientHeight-windowHeight))*100),
                fixedSidebar = sidebar_window ? header.offsetHeight+(sidebar_ads ? sidebar_ads.offsetHeight : 0) : false, //sidebar_ads.offsetHeight+marginOffset
                headbar_oh = headbar.querySelector('p#np') ? 100 : headbar.offsetHeight,
                footerDetect = sidebar_window ? footer.querySelector(".footer-detector").offsetTop-(headbar_oh+sidebar_float.offsetHeight) : false;
            // https://stackoverflow.com/questions/31223341/detecting-scroll-direction
            scroll_foward = window.pageYOffset;  // Get scroll Value
            if(scroll_record-scroll_foward<0){
                // scroll_delay = scrollTop>=header.offsetHeight+window.innerHeight ? 1000 : 0;  //设置滚动节流延迟
                //下滚超过导航栏执行
                if(scrollTop>=header.offsetHeight){
                    class_switch(header,class_up,class_down);  //nav bar
                    class_switch(headbar,"slide-down",null);
                    class_switch(progress_ball,"pull-up",null);
                    if(npost && share && scrollTop>=share.offsetTop){
                        class_switch(headbar,"next-post",null);  //show next post
                    }
                }else{
                    class_switch(headbar,null,class_up);
                }
                //超过侧边栏执行
                if(sidebar_window){
                    if(scrollTop>=fixedSidebar-5){
                        class_switch(sidebar_float,class_fixed,null);
                        sidebar_float.style.width = sidebar_float.parentElement.offsetWidth+"px";
                    }
                    //到达底部检测栏执行
                    if(scrollTop>=footerDetect){
                        sidebar_float.style.height = sidebar_float.offsetHeight+"px";
                        class_switch(sidebar_float,"window-all-get-stoped",null);
                        sidebar_float.parentElement.style.height = "100%";  //fix google ads load bug
                    }
                    sidebar_float.style.transform = "";  //始终执行
                }
            }else{
                //上滚至导航栏执行
                if(scrollTop<=header.offsetHeight){
                    // class_switch(header,class_down,class_up,true);
                    class_switch(headbar,null,"slide-down");
                    class_switch(progress_ball,null,"pull-up");
                }
                class_switch(header,class_down,class_up);
                // else{
                //     class_switch(header,class_down,class_up);
                // }
                if(npost && share && scrollTop<=share.offsetTop){
                    class_switch(headbar,null,"next-post");  //show next post
                }
                if(sidebar_window){
                    //上滑至侧边栏执行
                    if(scrollTop<fixedSidebar){
                        class_switch(sidebar_float,null,class_fixed);
                        sidebar_float.style.width = "";
                    }
                    //上滑小于侧边栏，大于底部栏+导航高度之间执行
                    sidebar_float.style.transform =  scrollTop>fixedSidebar && scrollTop<footerDetect-header.offsetHeight ? `translateY(${header.offsetHeight}px)` : "";
                    //上滑过底部栏后执行
                    if(scrollTop<footerDetect){
                        sidebar_float.style.height = "";
                        class_switch(sidebar_float,null,"window-all-get-stoped");
                    }
                }
            }
            scroll_record = scroll_foward;  // Update scrolled value
            // Progress ball
            progress_ball.querySelector(".pagePer strong").setAttribute('data-percent',page_percent);
            //.dataset.percent = page_percent; // dataset can not Update attr immediately https://qa.1r1g.com/sf/ask/1962219521/
            // progress_ball.querySelector(".pagePer strong").innerText = page_percent+"%";
            progress_ball.querySelector(".pagePer i").style.transform = `translateY(${100-page_percent}%)`;
            progress_wave.classList.add("active");
            progress_bar.classList.add("active");
            progress_bar.style.opacity = 1;
            progress_bar.style.transform = `translateX(${page_percent-100}%)`;
            if(scrollTop==0 || scrollTop+windowHeight>=clientHeight){  // 到达顶部（底部）执行
                progress_wave.classList.remove("active");
                progress_bar.classList.remove("active");
            }
            // TOC extends
            if(!aindex) return;
            const aindex_li = aindex.querySelectorAll('li'),
                  aindex_cl = function(el,cl){
                      for(let i=0,elLen=el.length;i<elLen;i++){
                          el[i].classList.remove(cl);
                      }
                  };
            new Promise(function(resolve,reject){
                let aindexOffset = aindex_fn();
                aindexOffset.length>=1 ? resolve(aindexOffset) : reject(aindexOffset);  // always update(do not call aindex_once_data)
            }).then(function(res){
                if(scrollTop<=res[0] || scrollTop>=share.offsetTop){ //-100
                    aindex_cl(aindex_li,'current')
                }else{
                    res.forEach(function(offset,index){
                        if(scrollTop>=offset){
                            aindex_cl(aindex_li,'current');  // location.href='title-'+index;
                            document.querySelector('#t'+index).classList.add('current');
                        }
                    });
                }
            }).catch(function(err){
                console.log(err);
            });
        };
        
    // document.addEventListener('DOMMouseScroll', scroll_func, false);  //DOMMouseScroll  // scroll 滚动+拖拽滚动条代替 wheel 滚动函数
    if(sidebar_window){
        window.addEventListener('scroll', scroll_func, true);
    }else{
        const scrollLoad = closure_throttle((e)=>scroll_func(), scroll_delay),
              scrollForRemove = function(event){
                  let e = event || window.event,
                      t = e.target || e.srcElement;
                  if(t!==document) return;
                  // requestAnimationFrame support
                  raf_available ? window.requestAnimationFrame(scrollLoad) : scrollLoad();
              };
        window.addEventListener('scroll', scrollForRemove, true);
        // return (function(){
        //     if(scroll_throttler==null){
        //         scroll_throttler = setTimeout(function(){
        //             exec_scroll();
        //             scroll_throttler = null;  //消除定时器
        //         }, scroll_delay);
        //     }
        // })();
    }
    
    // moblie ux
    const mobile_nav = document.querySelector('header .nav-wrap'),
          slide_menu = mobile_nav.querySelector('.slider-menu'),
          menu_mask = document.querySelector('.windowmask'),
          mobile_func = function(e){
              e.cancelable ? e.preventDefault() : e.stopPropagation();  // prevent penetrate a link
              const cls = 'show';
              if(slide_menu.classList && slide_menu.classList.contains(cls)){
                  document.body.style.overflowY = '';
                  slide_menu.classList.remove(cls);
                  menu_mask.style.display = '';
              }else{
                  document.body.style.overflowY = 'hidden';
                  slide_menu.classList.add(cls);
                  menu_mask.style.display = 'block';
              }
          },
          search_func = function(el, sw=true){
              if(!el) return;
              const search_cls = "searching",
                    el_cls = el.classList;
              if(!sw || !el_cls || !el_cls.contains(search_cls)){
                  const search_input = el.querySelector('.tips form input');
                  el_cls.add(search_cls);
                  search_input.focus();
                  search_input.setSelectionRange(0, search_input.length);
              }else{
                  el_cls.remove(search_cls);
              }
          };
    // release event
    menu_mask.onmouseup = menu_mask.ontouchend=(e)=>mobile_func(e);
    // bind click event
    mobile_nav.onclick = function(e){ //=> this->window
        e = e || window.event;
        let t = e.target || e.srcElement;
        if(!t) return;
        while(t!=mobile_nav){
            if(t.classList&&t.classList.contains("search-pop")){
                search_func(this);
                break;
            }else if(t.classList&&["m-menu", "slider-close"].some(c => t.classList.contains(c))){ //t.classList.contains("m-menu", "slider-close") || t.classList.contains("slider-close")
                mobile_func(e);
                break;
            }else if(t.classList&&t.classList.contains("footer-tips")){
                mobile_func(e);
                search_func(this, false);
                break;
            }else{
                t = t.parentNode;
            }
        }
    }
