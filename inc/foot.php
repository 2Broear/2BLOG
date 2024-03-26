<?php
    global $src_cdn;
?>
<script src="<?php echo $src_cdn;//custom_cdn_src('',true) ?>/js/main.js?v=<?php echo get_theme_info(); ?>"></script>
<script type="text/javascript">
    <?php
        global $cat;
        $vdo_poster_sw = get_option('site_video_poster_switcher');
        $datadance = get_option('site_animated_counting_switcher');
        $news_temp_id = get_cat_by_template('news','term_id');
        $note_temp_id = get_cat_by_template('notes','term_id');
        $acg_temp_id = get_cat_by_template('acg','term_id');
        if(is_single()){
            if(in_category($news_temp_id) || in_category($note_temp_id)){
                if($vdo_poster_sw) echo 'setupVideoPoster(3);'; // 截取设置当前页面所有视频 poster
    ?>
                //dynamicLoad
                asyncLoad('<?php echo $src_cdn; ?>/js/fancybox.umd.js', function(){
                    console.log('fancybox init.');
                    // gallery js initiate 'bodyimg' already exists in footer lazyload, use contimg insted.
                    let fancyImages = function(imgs){
                        if(imgs.length<=0) return;
                        for(let i=0,imgsLen=imgs.length;i<imgsLen;i++){
                            let eachimg = imgs[i],
                                eachpar = eachimg.parentNode,
                                fancybox = document.createElement("a");
                            fancybox.setAttribute("data-fancybox","gallery");
                            fancybox.setAttribute("aria-label", "gallery_images");
                            eachimg.src ? fancybox.setAttribute("href", eachimg.src) : fancybox.setAttribute("href", eachimg.dataset.src);
                            fancybox.appendChild(eachimg);
                            eachpar.insertBefore(fancybox, eachpar.firstChild);
                        }
                    }
                    fancyImages(document.querySelectorAll(".news-article-container .content img"));
                });
    <?php
            }
            // marker
            if(get_option('site_marker_switcher')){
    ?>
                (function(){
                    'use strict';
                    this.marker = {
                        dom: {
                            initiate: ()=> {
                                const _conf = marker.init?._conf,
                                      _mods = marker.methods,
                                      _static = _conf.static,
                                      _util = marker._utils,
                                      _els = _conf.element,
                                      _cls = _conf.class,
                                      mark = document.createElement("a"),
                                      tool = document.createElement("div"),
                                      sty = document.createElement('style');
                                mark.className = _cls.line;
                                mark.href = 'javascript:;';
                                mark.rel = 'nofollow';
                                tool.className = _cls.tool;
                                // tool.setAttribute('onselectstart','return false;');
                                tool.innerHTML = `<div class="${_cls.toolInside}"><span class="${_cls.mark}" style="" title="划线${_static.ctxMark}">${_static.ctxMark}</span><i>&nbsp;|&nbsp;</i><span class="${_cls.quote}" title="评论${_static.ctxQuote}" onclick="marker.methods.quote(this)">${_static.ctxQuote}</span><span class="${_cls.close}" title="${_static.ctxCancel}"></span></div>`; // onclick="marker.methods.close(this, true)" onclick="marker.methods.down(this)" <img src="" alt="avatar" />
                                if(_static.lineAnimate) {
                                    sty.textContent = `@keyframes ${_cls.aniUnderline}{0%{background-size:0% ${_static.lineBold}%;}100%{background-size:100% ${_static.lineBold}%;}}@keyframes ${_cls.aniProcess}{0%{transform:rotate(0deg)}100%{transform:rotate(360deg);}}`;
                                }
                                sty.textContent += `
                                    a.${_cls.line}.${_cls.done}{animation:none;-webkit-animation:none;transition:none;}
                                    a.${_cls.line}:hover,a.${_cls.line}.${_cls.done}{background-size:100% ${_static.lineBoldMax}%;}
                                    a.${_cls.line}:hover{color:inherit!important;}
                                    a.${_cls.line}{color:inherit;text-decoration:none!important;background:linear-gradient(${_static.lineColor},${_static.lineColor}) no-repeat left 100%/0 ${_static.lineBold}%;background-size:100% ${_static.lineBold}%;transition:background-size .15s ease;animation:${_cls.aniUnderline} 1s 1 ease;-webkit-animation:${_cls.aniUnderline} 1s 1 ease;cursor:text;user-select:text;-webkit-user-drag:none;position:relative;}
                                    a.${_cls.line}.${_cls.aniProcess} .${_cls.tool},
                                    a.${_cls.line}:hover .${_cls.tool}{padding-bottom:35px;opacity:1;}
                                    a.${_cls.line} .${_cls.tool}{padding-bottom:15px;position:absolute;top:0%;left:50%;transform:translate(-50%,-50%);opacity:0;transition:all .15s ease;font-family:auto;}
                                    a.${_cls.line} .${_cls.tool} .${_cls.toolInside}{color:black;line-height:27px;font-size:11px;font-weight:normal;white-space:nowrap;padding:0 5px;border:1px solid #fff;border-radius:5px;box-sizing:border-box;background:linear-gradient(0deg,rgb(245 247 249 / 88%) 0,rgb(255 255 255 / 100%));background:-webkit-linear-gradient(90deg,rgb(245 247 249 / 88%) 0,rgb(255 255 255 / 100%));box-shadow:rgba(0,0,0,0.12) 0 1px 18px;position:relative;user-select:none;-webkit-user-select:none;}
                                    a.${_cls.line} .${_cls.tool} img{max-width: 23px;border-radius: 50%;margin: 5px 5px 5px 0!important;}
                                    a.${_cls.line} .${_cls.tool} i{font-style:normal;}
                                    a.${_cls.line} .${_cls.tool} i,
                                    a.${_cls.line} .${_cls.tool} img,
                                    a.${_cls.line} .${_cls.tool} span{display: inline-block;vertical-align: middle;margin:auto;}
                                    a.${_cls.line} .${_cls.tool} span:hover{font-weight:bold;}
                                    a.${_cls.line} .${_cls.tool} i,
                                    a.${_cls.line}.${_cls.disabled} .${_cls.tool} span,
                                    a.${_cls.line} .${_cls.tool} span.${_cls.disabled}{opacity:.75;pointer-events:none;}
                                    a.${_cls.line} .${_cls.tool} span{cursor:pointer;}
                                    a.${_cls.line} .${_cls.tool} span.${_cls.close}::before,a.${_cls.line} .${_cls.tool} span.${_cls.close}::after{content:'';width:68%;height:12%;display:block;background:currentColor;position:inherit;top:50%;left:50%;transform:translate(-50%,-50%) rotate(45deg);margin:inherit;border:none;}
                                    a.${_cls.line} .${_cls.tool} span.${_cls.close}::after{transform:translate(-50%,-50%) rotate(-45deg);}
                                    a.${_cls.line} .${_cls.tool} span.${_cls.close}:hover::before,a.${_cls.line} .${_cls.tool} span.${_cls.close}:hover::after{height:18%;}
                                    a.${_cls.line}.${_cls.aniProcess} .${_cls.tool} span.${_cls.close}{animation:${_cls.aniProcess} linear 1s infinite;-webkit-animation:${_cls.aniProcess} linear 1s infinite;pointer-events:none;}
                                    /*a.${_cls.line}.${_cls.aniProcess} .${_cls.tool} span.${_cls.close},*/
                                    a.${_cls.line} .${_cls.tool} span.${_cls.close}:hover{transform:scale(1.25);-webkit-transform:scale(1.25)}
                                    a.${_cls.line} .${_cls.tool} span.${_cls.close}{width:10px;height:10px;color:white;background:${_static.lineColor};padding:1px;border-radius:50%;position:absolute;top:-5px;right:-5px;}
                                `;
                                document.head.appendChild(sty);
                                _els.tool = tool;
                                _els.line = mark;
                                // fetch data.
                                _mods.fetch("", {
                                    'fetch': 1,
                                }, (res)=> {
                                    console.log('load marker from remote', res);
                                    // user identification.. (MUST before output all keys for the first-time user-mid gets)
                                    let _res_failure = res.code && res.code!==200,
                                        _status = marker.status,
                                        commentInfo = _els.commentInfo,
                                        _md5update = (callback)=> {
                                            let userinfo = {
                                                    nick: commentInfo.userNick.value,
                                                    mail: commentInfo.userMail.value,
                                                },
                                                execUpdate = (userinfo, cbk)=> {
                                                    userinfo.mid = md5(userinfo.mail);
                                			        // store userinfo(marker.data.user.mid for currentUserCounts verification
                            			            marker.data = userinfo;
                                                    // store to local cookies
                                                    let _cookies = _util._cookies;
                                                    _cookies.set(_static.userNick, userinfo.nick);
                                                    _cookies.set(_static.userMail, userinfo.mail);
                                                    _cookies.set(_static.userMid, userinfo.mid);
                                                    if(_util.funValidator(cbk)) {
                                                        cbk();
                                                    }
                                                };
                                            if(typeof md5 === 'undefined') {
                                                console.log('init md5..');
                                                marker._utils.dynamicLoad(_static.md5src, ()=>execUpdate(userinfo, callback));
                                            }else{
                                                console.log('md5 initiated, updating records..');
                                                execUpdate(userinfo, callback);
                                            };
                                        },
                                        _outputMarkers = ()=> {
                                            if(_res_failure) {
                                                console.log('canceled on _outputMarkers:', res.msg);
                                                return;
                                            }
                                            let remoteMarks = Object.keys(res),
                                                currentUserMid = marker.data.user.mid, // get currentUserMid after marker user init.
                                                currentUserMarks = res[currentUserMid];
                                            if(currentUserMarks) {
                                                let dataList = marker.data.list,
                                                    dataPrefix = _static.dataPrefix,
                                                    localMarks = Object.keys(dataList);
                                                // localNotInRemote: delete local marks which is non-existent from remote
                                                const existNonUpdatedMarks = localMarks.filter(local => !currentUserMarks.some(remote => dataPrefix+remote.rid === local)); // 返回本地记录中不存在于远程记录的元素
                                                if(existNonUpdatedMarks.length >= 1) {
                                                    existNonUpdatedMarks.forEach(marks=> {
                                                        console.log('local marker('+marks+') not update to remote, try to Re-mark after refresh.. (existNonUpdatedMarks: slow-down the frequency of marking-off)', marks);
                                                        delete dataList[marks];
                                                        marker.data = {list: dataList}; // update data instantly
                                                        _util._cookies.del(marks, marker.data.path); // update local cookies
                                                    });
                                                }
                                                // remoteNotInLocal: delete remote marks which is non-existent from local
                                                const existNonDeletedMarks = currentUserMarks.filter(remote => !localMarks.some(local => local === dataPrefix+remote.rid)); // 返回远程记录中不存在于本地记录的元素
                                                if(existNonDeletedMarks.length >= 1) {
                                                    existNonDeletedMarks.forEach(marks=> {
                                                        const mark_rid = marks.rid,
                                                              ts_caches = JSON.parse(marker.data.caches),
                                                              cached_ts = ts_caches[dataPrefix + mark_rid];
                                                        // console.log(ts_caches, cached_ts);
                                                        if(cached_ts) {
                                                            console.log('remote marker matched non-existent local user record, updating remote from localStorage.. (existNonDeletedMarks: slow-down the frequency of marking-off)', marks);
                                                            // dom changes
                                                            _status.adjustPending(1, ()=> {
                                                                let exec_node = _els.effectsArea.querySelector('a[data-rid="'+mark_rid+'"]');
                                                                _mods.close(exec_node);
                                                                _status.adjustPending(0);
                                                            }, 1);
                                                            // request del update via localStorage timestamp
                                                            _mods.update({
                                                                ts: cached_ts,
                                                                rid: mark_rid,
                                                            }, (res)=> {
                                                                console.log(`remote marker ${res.cname}(ts: ${cached_ts}) deleted..`, res);
                                                            }, true);
                                                        }else{
                                                            console.log(`marker(${mark_rid}) belongs to another device(not found on localStorage!)`, ts_caches);
                                                        }
                                                    });
                                                }
                                            }
                                            // load available remote marks
                                            remoteMarks.forEach(key=> {
                                                let each_val = res[key];
                                                if(each_val==null) {
                                                    return;
                                                }
                                                // update currentUserCounts
                                                if(currentUserMid === key){
                                                    marker.data = {counts: res[key].length};
                                                }
                                                // console.log(key, currentUserMid, res[key].length, marker.data.stat.counts);
                                                each_val.forEach(item=> {
                                                    // console.log(key, item);
                                                    // console.log(dataPrefix+item.rid, localMarks)
                                                    let frag_mark = mark.cloneNode(true),
                                                        frag_tool = tool.cloneNode(true),
                                                        tool_inside = frag_tool.querySelector('.'+_cls.toolInside),
                                                        tool_mark = frag_tool.querySelector('.'+_cls.mark),
                                                        tool_avatar = new Image(), //document.createElement('img'),
                                                        mark_nick = item.nick,
                                                        mark_text = item.text,
                                                        mark_uid = item.uid,
                                                        mark_indexes = mark_uid.match('(\\d+)-(\\d+)'),
                                                        mark_index = mark_indexes[1],
                                                        mark_paragraph = _els.effectsArea.children[mark_index];
                                                    if(!mark_paragraph.textContent.includes(mark_text)){
                                                        console.log('mark_uid('+mark_index+') with diff records(perhaps content changed), traversal all nodes..');
                                                        const effectsArea_childs = _els.effectsArea.children;
                                                        for (let i=0;i<effectsArea_childs.length;i++) {
                                                            if(effectsArea_childs[i].textContent.includes(mark_text)) {
                                                                mark_index = i;
                                                                break;
                                                            }
                                                        };
                                                        mark_paragraph = effectsArea_childs[mark_index];
                                                        console.log('search done! found(firstIndexOf) on mark_uid('+mark_index+')');
                                                    }
                                                    tool_avatar.alt = 'marker avatar';
                                                    tool_avatar.src = `<?php echo get_option("site_avatar_mirror").'avatar/'; ?>${key}?d=mp&s=100&v=1.3.10`;
                                                    tool_inside.insertBefore(tool_avatar, tool_inside.firstChild);
                                                    frag_mark.classList.add(_cls.done);
                                                    frag_mark.textContent = mark_text;
                                                    frag_mark.dataset.uid = mark_uid;
                                                    frag_mark.dataset.rid = item.rid;
                                                    frag_mark.title = `marked at ${item.date}`;
                                                    tool_mark.className = `${_cls.mark} ${_cls.disabled}`;
                                                    tool_mark.textContent = `${mark_nick} ${_static.ctxMarked}`;
                                                    frag_mark.appendChild(frag_tool);
                                                    let specific_chars = mark_text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                                                    mark_paragraph.innerHTML = mark_paragraph.innerHTML.replace(specific_chars, frag_mark.outerHTML);
                                                });
                                            });
                                        };
                                    // re-update on userinfo->mail changed.
                                    if(_status.isMarkerUserUpdate()) {
                                        _md5update(_outputMarkers);
                                        console.log(`marker user updated: ${commentInfo.userMail.value} (counts: ${marker.data.stat.counts})`);
                                    }else{
                                        // abort on userinfo exists
                                        if(!_status.isMarkerAccessable() && commentInfo.userMail.value){
                                            _md5update(_outputMarkers);
                                            console.log(`marker user inited. (counts: ${marker.data.stat.counts})`);
                                        }else{
                                            _outputMarkers();
                                        }
                                    }
                                }, (err)=> {
                                    console.warn(err); // load data from local cookies
                                });
                            },
                        },
                        _utils: {
                            _events: {
                                get: (event)=> {
                                    return event ? event : window.event;
                                },
                                add: function(element=null, type='', handler=false, cb=false) {
                                    let init_func = function(element=null, type='', handler=false, callback=false){
                                            if(!element || !type) return;
                                            marker._utils.assert(handler && typeof handler==='function', 'addEvent callback err.');
                                            callback();
                                            console.debug(type, 'event loaded.');
                                        };
                                    try {
                                        if(element.addEventListener){
                                            this.add = function(element=null, type='', handler=false, cb=false){
                                                init_func(element, type, handler, ()=>{
                                                    element.addEventListener(type, handler, cb);
                                                })
                                            }
                                        }else if(element.attachEvent){
                                            this.add = function(element=null, type='', handler=false){
                                                init_func(element, type, handler, ()=>{
                                                    element.attachEvent('on'+type, handler);
                                                })
                                            }
                                        }else{
                                            this.add = function(element=null, type='', handler=false){
                                                init_func(element, type, handler, ()=>{
                                                    element['on'+type] = handler;
                                                })
                                            }
                                        };
                                        // console.log('lazy function: ',this.add);
                                        this.add(element, type, handler, cb);
                                    } catch (error) {}
                                },
                                getTarget: (event)=> {
                                    return event.target || window.srcElement;
                                },
                                preventDefault: (event)=> {
                                    if(event.preventDefault){
                                        event.preventDefault();
                                    }else{
                                        event.returnValue = false;
                                    }
                                },
                                bindClassClick: function(parent, cls, callback) {
                                    let that = this;
                                    that.add(parent, 'click', function(e) {
                                        let event = that.get(e),
                                            target = that.getTarget(event).closest('.' + cls);
                                        if (target) {
                                            callback(target, event); //marker._utils.funValidator(callback)
                                        }
                                    });
                                }
                            },
                            _closure: {
                                debouncer: (callback=false, delay=200)=>{
                                    var timer = null;
                                    return function(...args){
                                        if(timer) clearTimeout(timer);
                                        timer = setTimeout(function(){
                                            callback.apply(this, args);
                                        }, delay);
                                    }
                                },
                                throttler: (callback=false, delay=200)=>{
                                    let closure_variable = true;  //default running
                                    return function(){
                                        if(!closure_variable) return;  //now running..
                                        closure_variable = false;  //stop running
                                        setTimeout(()=>{
                                            callback.apply(this, arguments);
                                            closure_variable = true;  //reset running
                                        }, delay);
                                    };
                                },
                            },
                            _cookies: {
                                set: (name, value, path='/', days=30)=> {
                                    let exp = new Date();
                                    exp.setTime(exp.getTime() + days*(24*60*60*1000));
                                    document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + ';path=' + path; //escape
                                },
                                get: (cname)=> {
                                    var name = cname + "=";
                                    var ca = document.cookie.split(';');
                                    for(var i=0,caLen=ca.length; i<caLen; i++) {
                                        var c = ca[i];
                                        while (c.charAt(0)==' ') c=c.substring(1);
                                        if(c.indexOf(name) != -1) {
                                            return c.substring(name.length, c.length);
                                        }
                                    }
                                    return "";
                                },
                                del: function(name, path='/') {
                                    var exp = new Date();
                                    exp.setTime(exp.getTime() - 1);
                                    var cval = this.get(name);
                                    if(cval!=null){
                                        document.cookie = name+"="+cval+";expires="+exp.toGMTString()+";path="+path;
                                    }
                                },
                            },
                            debugger: function(msg='', type='log') {
                                switch (true) {
                                    case typeof opera==='object':
                                        opera.postError(msg);
                                        break;
                                    case typeof java==='object' && java.lang==='object':
                                        java.lang.System.out.printIn(msg);
                                        break;
                                    case typeof console==='object':
                                    default:
                                        this.assert(typeof console[type]==='function', 'invalid console type.');
                                        console[type](msg)
                                        break;
                                }
                            },
                            assert: function(conditions=true, message='', logType=false, report=false) {
                                if(conditions) return;
                                if(typeof logType==='string') this.debugger(conditions, logType);
                                if(typeof message==='string'){
                                    let err = new Error(message);
                                    if(report){
                                        let img = new Image();
                                        img.src = report+'.php?err='+err+'&msg='+message;
                                    }
                                    throw err;
                                }
                            },
                            dynamicLoad: (jsUrl, fn)=> {
                                const script = document.createElement('script');
                                script.type = 'text/javascript';
                                script.async = true;
                                script.src = jsUrl;
                                document.getElementsByTagName('head')[0].appendChild(script);
                                script.onload = script.onreadystatechange = () => {
                                    if (!script.readyState || script.readyState === 'loaded' || script.readyState === 'complete') {
                                        if (fn) fn();
                                    }
                                    script.onload = script.onreadystatechange = null;
                                };
                            },
                            isObject: (obj)=> {
                                return Object.prototype.toString.call(obj)==='[object Object]';
                            },
                            funValidator: function(fn, exec=false) {
                                if(!fn || typeof fn!=='function'){
                                    return false;
                                }
                                return exec ? fn.apply(this.arguments) : true;
                            },
                            argsRewriter: function(args={}, presets={}, callback=false) {
                                try {
                                    const _this = this._utils ? this : marker,
                                          _util = _this._utils;
                                    _util.assert(Object.prototype.toString.call(args)==='[object Object]', 'invalid arguments provided!');
                                    // rewrite conf
                                    _this.init.prototype._singleton_conf._rewriter(args, presets);
                                    if(!_util.funValidator(callback)){
                                        return args;
                                    }
                                    // callback returns
                                    callback(args);
                                } catch (error) {
                                    console.log(error)
                                }
                            },
                            randomString: function(num=16) {
                                const rs = Math.random().toString(num);
                                return rs.substring(2, rs.length);
                            },
                            elementIndexer: (node=null)=> {
                                return node ? Array.prototype.indexOf.call(node.parentElement.children, node) : 0;
                            },
                            elementFinder: (element=null, className='', mod=0)=> {
                                if (mod === '' || typeof mod !== 'number') {
                                    console.warn('invalid mod, must be value 0 or 1', mod);
                                    return null;
                                }
                                switch (mod) {
                                    case 1:
                                        let childElements = element.getElementsByClassName(className);
                                        return childElements.length>0 ? childElements : null;
                                    case 0:
                                    default:
                                        let parentElement = element.parentNode;
                                        while (parentElement&&parentElement.classList) {
                                            if (parentElement.classList.contains(className)) {
                                                return parentElement;
                                            }
                                            parentElement = parentElement.parentNode;
                                        }
                                        return null; // 如果未找到匹配的父级元素
                                }
                            },
                            contextIndexes: (context, keyword)=> {
                                if(!context || !keyword) return;
                                let indexes = [],
                                    index = context.indexOf(keyword);
                                while (index !== -1) {
                                    indexes.push(index);
                                    index = context.indexOf(keyword, index + 1);
                                }
                                return indexes;
                            },
                            parseParameters: function(obj, post=false) {
                                if(post && this.isObject(obj)) {
                                    return obj;
                                }
                                let str = "";
                                for(let key in obj){
                                    str += `${key}=${obj[key]}&`;
                                }
                                str = str.substr(0,str.lastIndexOf("&"));
                                return decodeURIComponent(str);
                            },
                        },
                        status: {
                            isMarkerAvailable: (anonymous=false)=> {
                                let valid_status = true;
                                if(!anonymous) {
                                    const commentInfo = marker.init._conf.element.commentInfo,
                                          userinfo = Object.entries(commentInfo),
                                          userKeys = Object.keys(commentInfo),
                                          userVals = Object.values(commentInfo);
                                    for(let i=0;i<userinfo.length;i++){
                                        let key = userinfo[i][0],
                                            val = userinfo[i][1];
                                        if(val==null){
                                            console.warn('Abort on '+key+': all commentInfo must be Specificed!', commentInfo);
                                            valid_status = false;
                                        }else if(val.value==''){
                                            console.warn(key+' required to be FullFilled to use marker.', val);
                                            valid_status = false;
                                        }
                                    };
                                }
                                return valid_status;
                            },
                            isMarkerAccessable: ()=> {
                                return marker.data.user.mail&&marker.data.user.mail!=="";
                            },
                            isMarkerUserUpdate: function() {
                                const user_updated = decodeURIComponent(marker.data.user.mail)!==marker.init._conf.element.commentInfo.userMail.value;
                                return this.isMarkerAccessable() && user_updated;
                            },
                            isMarkerReachedMax: ()=> {
                                let maxDataLen = marker.init._conf.static.dataMax,
                                    userMarkedCounts = marker.data.stat.counts,
                                    localCompare = Object.keys(marker.data.list).length < maxDataLen,
                                    remoteCompare = userMarkedCounts < maxDataLen;
                                return localCompare && remoteCompare;
                            },
                            isMarkerSelectable: (node)=> {
                                if(!node instanceof HTMLElement || !node.classList) {
                                    console.warn('invalid nodes', node);
                                    return notOnList;
                                }
                                if(node.tagName=='H1' || node.tagName=='H2' || node.tagName=='H3') {
                                    console.warn('unSelectable nodes(title of h1/h2/h3) detected..', node);
                                    return;
                                }
                                let notOnList = true,
                                    blackList = marker.init._conf.class.blackList;
                                blackList = blackList instanceof Array ? blackList : [];
                                let blackLens = blackList.length;
                                if(blackLens > 0) {
                                    for(let i=0;i<blackLens;i++){
                                        let blackClass = blackList[i];
                                        if(node.classList.contains(blackClass) || marker._utils.elementFinder(node, blackClass, 0)) {
                                            notOnList = false;
                                            console.warn('unSelectable nodes(node/parentNodes on "'+blackClass+'") detected..', node);
                                            break;
                                        }
                                    }
                                }
                                return notOnList;
                            },
                            isNodeMarkable: (node)=> {
                                return node&&node.classList&&node.classList.contains(marker.init._conf.class.line);
                            },
                            isNodeMarkDone: (node)=> {
                                return node&&node.classList&&node.classList.contains(marker.init._conf.class.done);
                            },
                            isTextWrapOnly: (node)=> {
                                let node_child = node.children;
                                switch(true){
                                    case !node:
                                    case !node instanceof HTMLElement:
                                        console.warn('invalid nodes wrapped.', node);
                                        return false;
                                        break;
                                    case node_child.length<=0:
                                        console.debug('No childNodes wrapped in selections.', node_child);
                                        return null;
                                        break;
                                }
                                let child_classes = node_child[0].classList;
                                return child_classes&&child_classes.contains(marker.init._conf.class.tool);
                            },
                            isMultiSameChar: (paragraph, context, vars=false)=> {
                                let uniqune_char = marker._utils.contextIndexes(paragraph, context);
                                return vars ? uniqune_char : uniqune_char.length > 1;
                            },
                            adjustPending: (status=0, callback=false, delay=0)=> {
                                if(callback){
                                    delay = delay ? delay : marker.init._conf.static.dataDelay;
                                    let timer = setTimeout(() => {
                                        marker.data = {pending: status}; // adjusting pending statu.
                                        if(marker._utils.funValidator(callback)) {
                                            callback();
                                        }
                                        clearTimeout(timer); // 在回调函数执行后清除 setTimeout
                                    }, delay);
                                }else{
                                    marker.data = {pending: status}; // adjusting pending statu.
                                }
                            },
                        },
                        methods: {
                            mark: function(){
                                // console.log(e)
                                const _util = marker._utils,
                                      _status = marker.status,
                                      _conf = marker.init._conf,
                                      _static = _conf.static,
                                      _class = _conf.class,
                                      _element = _conf.element;
                                let selectedText = this.toString();
                                if (selectedText.length <= 0 || this.isCollapsed) {
                                    console.debug('empty selection', selectedText);
                                    return;
                                }
                                try {
                                    let range = this.getRangeAt(0);
                                    const anchor_parent = this.anchorNode.parentElement,
                                          focus_parent = this.focusNode.parentElement;
                                    let contains_node = null;
                                    switch(true) {
                                        case anchor_parent != range.commonAncestorContainer:
                                            contains_node = anchor_parent;
                                            break;
                                        case focus_parent != range.commonAncestorContainer:
                                            contains_node = focus_parent;
                                            break;
                                    }
                                    if(!_status.isMarkerSelectable(contains_node)) {
                                        return;
                                    }
                                    if(_status.isNodeMarkable(contains_node) && _status.isNodeMarkDone(contains_node)) {
                                        console.warn('selection contains marked-parent content, canceling..', contains_node);
                                        return;
                                    }
                                    let marks = _element.line.cloneNode(true),
                                        tool = _element.tool,
                                        rid = _util.randomString(); 
                                    marks.dataset.rid = rid;
                                    range.surroundContents(marks);
                                    // check marker is selectable
                                    const tool_mark = tool.querySelector('.'+_class.mark),
                                          tool_disabled = tool_mark.classList.contains(_class.disabled);
                                    if(_status.isMarkerReachedMax()){
                                        if(tool_disabled) {
                                            tool_mark.classList.remove(_class.disabled);
                                            tool_mark.textContent = _static.ctxMark;
                                        }
                                    }else{
                                        // rewrite stored tool context only if tool_mark on enabled statu.(decreasing origin_mark dom edit)
                                        if(!tool_disabled) {
                                            tool_mark.classList.add(_class.disabled);
                                            tool_mark.textContent = _static.ctxMarkMax;
                                        }
                                    }
                                    tool = tool.cloneNode(true);
                                    marks.appendChild(tool);
                                    this.removeRange(range); //this.removeAllRanges();
                                    // close mark it-self if selections under markable-parent
                                    const marks_parents = _util.elementFinder(marks, _class.line, 0);
                                    if(marks_parents != null){
                                        console.warn('markable-parent (deep-level) exists, unwrapping self marks', marks);
                                        marker.methods.close(marks);  // marker.methods.close(marks_parents);
                                        return;
                                    }
                                    const marks_children = marks.querySelectorAll('.' + _class.line); // marks.children;
                                    if(marks_children.length <= 0){
                                        return;
                                    }
                                    // console.log(marks_children);
                                    marks_children.forEach((each_line)=>{
                                        let dynamic_line = _element.effectsArea.querySelector('[data-rid="'+each_line.dataset.rid+'"]'),
                                            line_child = _util.elementFinder(each_line, _class.line, 1);
                                        // close inside wrapped child
                                        if(line_child && line_child.length >= 1){
                                            let line_child = line_child[0];
                                            console.warn('markable-child (deep level) exists, unwrapping line_child', line_child);
                                            marker.methods.close(line_child);
                                            return;
                                        }
                                        // close inside wrapped parent
                                        if(_status.isNodeMarkDone(each_line)){
                                            let line_parent = _util.elementFinder(each_line, _class.line, 0);
                                            if(line_parent != null){
                                                console.warn('selection contains marked-parents content, unwrapping line_parent..', line_parent);
                                                marker.methods.close(line_parent);
                                            }else{
                                                let dynamic_parent = _util.elementFinder(dynamic_line, _class.line, 0);
                                                console.warn('selection contains marked-children content, unwrapping dynamic_parent..', dynamic_parent);
                                                marker.methods.close(dynamic_parent); // reject dynamic marks
                                                // marker.methods.close(dynamic_line); // ckear-all-children
                                            }
                                            return;
                                        }
                                        // USE dynamic_line insted of each_line for close(null) confused innerHTML structure issue.
                                        console.log('markable-child wrap exists, unwrapping dynamic_line..', dynamic_line);
                                        marker.methods.close(dynamic_line);
                                    });
                                } catch (error) {
                                    console.warn(error);
                                }
                            },
                            down: function(node) {
                                const pending_statu = marker.data.stat.pending;
                                console.log('pending_statu: '+pending_statu)
                                if(pending_statu) {
                                    console.warn('Abort on too-fast marking off! (please wait for a few seconds then re-mark again.)');
                                    return;
                                }
                                const _util = marker._utils,
                                      _status = marker.status,
                                      _static = marker.init._conf.static;
                                // _util.assert(marker.data.list.length < _static.dataMax, 'Reaching maximum data length.');
                                if(!_status.isMarkerReachedMax()){
                                    alert('Abort on reaching maximum data-list length!');
                                    this.close(node);
                                    return;
                                }
                                const _class = marker.init._conf.class,
                                      mark_node = _util.elementFinder(node, _class.line),
                                      mark_text = mark_node.firstChild.nodeValue,
                                      mark_rid = mark_node.dataset.rid;
                                if(_status.isNodeMarkDone(mark_node)) {
                                    console.warn('Abort on marked-done node!', mark_node);
                                    mark_node.classList.add(_class.disabled);
                                    return;
                                }
                                let mark_paragraph = mark_node;
                                while(mark_paragraph.parentElement != marker.init._conf.element.effectsArea){
                                    mark_paragraph = mark_paragraph.parentElement;
                                }
                                let paragraph_context = mark_paragraph.textContent;
                                if(_status.isMultiSameChar(paragraph_context, mark_text)){
                                    alert('Abort on multi Same-chars on current paragraph!'+_status.isMultiSameChar(paragraph_context, mark_text, true));
                                    return;
                                }
                                // update to remote.
                                const mark_indexes = _util.elementIndexer(mark_paragraph) + '-' + paragraph_context.indexOf(mark_text);
                                node.textContent = _static.ctxMarking;
                                this.update({
                                    rid: mark_rid,
                                    uid: mark_indexes,
                                    text: mark_text,
                                }, (res)=> {
                                    // local updates
                                    let _ts = res.ts,
                                        _cname = res.cname,
                                        _cnames = _static.dataCaches,
                                        ts_caches = window.localStorage.getItem(_cnames);
                                    // record ts caches to localStorage(for ever del)
                                    ts_caches = ts_caches ? JSON.parse(ts_caches) : {};
                                    ts_caches[_cname] = _ts;
                                    console.log(ts_caches)
                                    window.localStorage.setItem(_cnames, JSON.stringify(ts_caches));
                                    // record cookies
                                    _util._cookies.set(_cname, _ts, marker.data.path, 365); 
                                    // update currentUserCounts(for no-refresh page max-mark limitation)
                                    marker.data = {counts: res.counts + 1};  // increase counts
                                    console.log(`${_cname} updated(ts: ${_ts}) `, res.msg);
                                    // dom changes
                                    mark_node.classList.add(_class.done);
                                    node.innerHTML = `<small>${marker.init._conf.static.ctxMarked}（${mark_rid}）</small>`;
                                    node.classList.add(_class.disabled);
                                    mark_node.dataset.uid = mark_indexes;
                                });
                            },
                            quote: function(node) {
                                const _class = marker.init._conf.class,
                                      mark_node = marker._utils.elementFinder(node, _class.line),
                                      comment_box = marker.init._conf.element.commentArea;
                                if(!comment_box) {
                                    console.warn('Quote abort on invalid commentArea!', comment_box);
                                    return;
                                }
                                comment_box.value = `\n> ${mark_node.firstChild.nodeValue} ...`;
                                comment_box.setSelectionRange(0,0);
                                comment_box.focus();
                                if(!marker.status.isNodeMarkDone(mark_node)){
                                    this.close(mark_node);
                                }
                            },
                            close: function(node, update=false) {
                                if(!node || !node instanceof HTMLElement) return;
                                // 执行 close() 操作后将打乱标记点父级（bug：无法再次找到已定义的子级元素，已通过动态选择each_line解决）
                                const _util = marker._utils,
                                      _class = marker.init._conf.class,
                                      _status = marker.status,
                                      mark_node = _status.isNodeMarkable(node) ? node : _util.elementFinder(node, _class.line),
                                      mark_dataset = mark_node.dataset,
                                      mark_rid = mark_dataset.rid,
                                      mark_uid = mark_dataset.uid;
                                // deletion auth.
                                if(!_status.isMarkerAccessable()){
                                    alert('marker deletion failure, anonymous not allowed..');
                                    return;
                                }
                                let update_dom = ()=> {
                                    let mark_tools = _util.elementFinder(mark_node, _class.tool, 1);
                                    if(mark_tools.length >= 1) {
                                        mark_tools[mark_tools.length-1].remove();  // mark_tools[0].remove();
                                    }
                                    let replace_content = _status.isTextWrapOnly(mark_node) ? mark_node.firstChild.textContent : mark_node.innerHTML;
                                    if(!mark_node.parentElement) {
                                        console.log('mark parent NOT found while closing', mark_node);
                                        return;
                                    }
                                    mark_node.parentElement.innerHTML = mark_node.parentElement.innerHTML.replace(mark_node.outerHTML, replace_content);
                                };
                                if(update && _status.isNodeMarkDone(mark_node)){
                                    const processing = _class.aniProcess;
                                    if(confirm('deleting rid#' + mark_rid + '?')) {
                                        mark_node.classList.add(processing);
                                        // delete from remote.
                                        this.update({
                                            rid: mark_rid,
                                            uid: mark_uid,
                                            node: mark_node,
                                            cls: processing,
                                        }, (res)=> {
                                            _util._cookies.del(res.cname, marker.data.path); // local updates
                                            // update currentUserCounts(for no-refresh page max-mark limitation)
                                            marker.data = {counts: res.counts - 1}; // decrease counts
                                            console.log(`${res.cname} deleted(ts: ${res.ts}) `, res.msg);
                                            // dom changes
                                            update_dom();
                                        }, true);
                                    }else{
                                        mark_node.classList.remove(processing);
                                    }
                                }else{
                                    update_dom();
                                }
                            },
                            update: function(updObj={}, cbk=false, del=false) {
                                if(!marker._utils.isObject(updObj) || Object.keys(updObj).length<1) {
                                    console.warn('remote updates failed, invalid updateObject.', updObj);
                                    return;
                                }
                                const marker_rid = updObj.rid,
                                      marker_uid = updObj.uid,
                                      marker_num = marker.data.stat.counts,
                                      mark_cname = marker.init._conf.static.dataPrefix + marker_rid,
                                      _valid_cbk = marker._utils.funValidator(cbk),
                                      _status = marker.status;
                                // start pending..
                                _status.adjustPending(1);
                                // deletion load ts from local
                                if(del) {
                                    let local_ts = marker.data.list[mark_cname];
                                    this.fetch("<?php echo $mark_url = get_api_refrence('mark', true); ?>", {
                                        'del': 1,
                                        // 'uid': marker_uid,
                                        'rid': marker_rid,
                                        'ts': updObj.ts ? updObj.ts : local_ts,
                                    }, (res)=> {
                                        if(res.code && res.code!==200){
                                            alert(`${res.msg}（err#${res.code}）`);
                                            if(updObj.node&&updObj.node.classList) updObj.node.classList.remove(updObj.cls);
                                            return;
                                        }
                                        _status.adjustPending(0, ()=> {
                                            // delete from local.
                                            res.ts = local_ts;
                                            res.cname = mark_cname;
                                            res.counts = marker_num;
                                            _valid_cbk ? cbk(res) : console.log('update(del) succesed(no calls)', res.msg);
                                        }); //, 1000
                                    });
                                    return;
                                }
                                // addition load ts via real-time
                                let realtime_ts = Date.now();
                                this.fetch("<?php echo $mark_url; ?>", {
                                    'rid': marker_rid,
                                    'uid': updObj.uid,
                                    "text": updObj.text,
                                    'ts': realtime_ts,
                                }, (res)=> {
                                    if(res.code && res.code!==200){
                                        alert(`${res.msg}（err#${res.code}）`);
                                        return;
                                    }
                                    // _status.adjustPending(0);
                                    _status.adjustPending(0, ()=> {
                                        // 储存在本地的 ts 验证必须与发送 update 请求验证保持一致（当前生成将延迟于服务端记录）
                                        res.ts = realtime_ts; // update realtime_ts
                                        res.cname = mark_cname;
                                        res.counts = marker_num;
                                        _valid_cbk ? cbk(res) : console.log('update(add) succesed(no calls)', res.msg);
                                    });
                                });
                            },
                            fetch: (url='', _obj={}, cbk=false, cbks=false)=> {
                                url = url || "<?php echo get_api_refrence('mark'); ?>";
                                const _util = marker._utils,
                                      _data = marker.data;
                                _util.argsRewriter.call(marker, _obj, {
                                    <?php parse_str($mark_url, $mark_params);if(!isset($mark_params['pid'])) echo "'pid': $pid,"; ?>
                                    'fetch': 0,
                                    'del': 0,
                                    'ts': 0,
                                    "nick": _data.user.nick,
                                    "mail": _data.user.mail,
                                }, (obj_)=> {
                                    fetch(`${url}&${_util.parseParameters(obj_)}`, {}).then(response => {
                                        if(!response.ok) throw new Error('Network err');
                                        return response.json();
                                    }).then(data => {
                                        if(_util.funValidator(cbk)) cbk(data);
                                    }).catch(error => {
                                        console.warn('fetch '+error);
                                        if(_util.funValidator(cbks)) cbks(error);
                                    });
                                });
                            },
                        },
                        __proto__: {
                            init: function(user_conf = {}){
                                let _this = Object.getPrototypeOf(this)!==marker.init.prototype ? marker.init.prototype : this;
                                try {
                                    // rewrite user-conf.
                                    marker.init._conf = _this._singleton_conf._rewriter.call(_this, user_conf);
                                    // init&load dom..
                                    marker.dom.initiate();
                                    // check marker status before initiate.(prevent mouseup events exec mark())
                                    if(!marker.status.isMarkerAvailable()) {
                                        throw new Error('marker unavailable, register init failed..');
                                    }
                                    // bind events
                                    const _conf = marker.init._conf,
                                          _event = marker._utils._events,
                                          _closure = marker._utils._closure,
                                          method = marker.methods,
                                          effects = _conf.element.effectsArea;
                                    _event.add(effects, 'mouseup', _closure.debouncer(method.mark.bind(window.getSelection()), 100)); //marker.methods.mark.bind(window.getSelection())
                                    _event.bindClassClick(effects, _conf.class.close, _closure.debouncer((t)=>method.close(t, true), 150));
                                    _event.bindClassClick(effects, _conf.class.mark, _closure.debouncer((t)=>method.down(t), 200));
                                    console.log('marker initialized.', marker);
                                } catch (error) {
                                    console.log(error)
                                }
                            },
                        },
                        get data(){
                            let _static = marker.init._conf.static,
                                _cookies = marker._utils._cookies,
                                regExp = new RegExp(`${_static.dataPrefix}(.*?)=(.*?);`, 'g'),
                                stored = document.cookie.match(regExp) || [],
                                setter = this.init._conf.setter,
                                result = {};
                            if(stored.length>=1){
                                stored.map(item => { //stored = 
                                    let pair = item.split("="),
                                        key = pair[0],
                                        val = pair[1].split(";")[0];
                                    // return { [key]: val, }; // 修改返回的对象结构 { key, val }
                                    result[key] = val; // 将键值对存入 result 对象中
                                });
                            }
                            return {
                                'user': {
                                    nick: _cookies.get(_static.userNick) || setter.nick,
                                    mail: _cookies.get(_static.userMail) || setter.mail,
                                    mid: _cookies.get(_static.userMid) || setter.mid,
                                },
                                'stat': {
                                    counts: setter.counts || 0,
                                    pending: setter.pending || 0,
                                },
                                'list': result,
                                'caches': window.localStorage.getItem('markerCaches') || '{}', //JSON.parse()
                                'path': window.location.pathname,
                            };
                        },
                        set data(obj){
                            if(!marker._utils.isObject(obj)) {
                                return;
                            }
                            let setter = this.init._conf.setter;
                            setter.nick = obj.nick;
                            setter.mail = obj.mail;
                            setter.mid = obj.mid;
                            setter.counts = obj.counts;
                            setter.pending = obj.pending;
                        },
                    };
                    
                    Object.defineProperties(marker.init.prototype, {
                        _singleton_conf: {
                            value: function(){
                                let privatePresets = {
                                        static: {
                                            dataMax: 3,
                                            dataDelay: 1500,
                                            dataPrefix: 'marker-',
                                            dataCaches: 'markerCaches',
                                            lineBold: 10,
                                            lineColor: 'var(--theme-color)',
                                            lineBoldMax: 30,
                                            lineAnimate: true,
                                            ctxMark: '标记',
                                            ctxQuote: '引用',
                                            ctxMarked: '已标记',
                                            ctxMarking: '标记中...',
                                            ctxMarkMax: '用户标记已满',
                                            ctxCancel: '取消选中/删除',
                                            // userinfo do NOT use the same prefix as dataPrefix
                                            userNick: 'marker_userNick',
                                            userMail: 'marker_userMail',
                                            userMid: 'marker_userMid',
                                            md5src: "<?php echo $src_cdn; ?>/js/md5.min.js",
                                        },
                                        class: {
                                            line: 'markable',
                                            tool: 'tools',
                                            toolInside: 'tool-inside',
                                            mark: 'mark',
                                            quote: 'quote',
                                            update: 'update',
                                            close: 'close',
                                            done: 'done',
                                            disabled: 'disabled',
                                            aniUnderline: 'underline',
                                            aniProcess: 'processing',
                                            blackList: ['chatGPT', 'article_index', 'wp-block-code'],
                                        },
                                        element: {
                                            effectsArea: document,
                                            commentArea: document.querySelector('textarea'),
                                            commentInfo: {
                                                userNick: null,
                                                userMail: null,
                                            }
                                        },
                                        setter: {},
                                    };
                                Object.freeze(privatePresets);
                                return {
                                    publicDefault: Object.create(null),
                                    _rewriter: function fn(conf=this.publicDefault, opts=privatePresets) {
                                        if(!marker._utils.isObject(opts)) return;
                                        for(const [key, val] of Object.entries(opts)){
                                            // back-write (mark non-existent property)
                                            conf[key] ??= val; //conf[key] ||= val;
                                            // recursion-loop (use fn call-stack for recursion-func)
                                            fn.apply(this, [conf[key], val]);
                                        }
                                        // clear closure recycle-quotes
                                        opts = privatePresets = null;
                                        return conf;
                                    },
                                }
                            }(),
                            configurable: false,
                        },
                    });
                }).call(window);
                // use keyword "new" to point to init method.
                new marker.init({
                    static: {
                        lineBold: 10,
                        // dataDelay: 1000,
                    },
                    element: {
                        effectsArea: document.querySelector('.content'),
                        commentArea: document.querySelector('#vcomments textarea') || document.querySelector('#twikoo textarea') || document.querySelector('.wp_comment_box textarea'),
                        commentInfo: {
                            userNick: document.querySelector('input[name=nick]'),
                            userMail: document.querySelector('input[name=mail]'),
                        }
                    },
                });
    <?php
            }
        }
        if($cat){
            switch ($cat) {
                case get_cat_by_template('privacy','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(1);'; // 截取设置当前页面所有视频 poster
                    break;
                case $acg_temp_id:
                case cat_is_ancestor_of($acg_temp_id, $cat):
                    if($datadance) echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h2", -15, 5, "<sup>+</sup>");';
                    break;
                case get_cat_by_template('archive','term_id'):
                    if($datadance) echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h1", 200, 25);';
                    break;
                case get_cat_by_template('about','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(2);';  // 截取设置当前页面所有视频 poster 
    ?>
                    const list = document.querySelectorAll('.mbit .mbit_range li');
                    // if(raf_available){
                    //     if(list[0]){
                    //         for(let i=0,listLen=list.length;i<listLen;i++){
                    //             raf_enqueue(true, function(init){
                    //                 list[i].classList.add('active');
                    //             }, 25, i);
                    //         }
                    //     }
                    // }else{
                        async_enqueue(list, true, function(i){ //sto_enqueue
                            list[i].classList.add('active');
                        }, 200);
                    // }
    <?php
                    break;
                // case get_cat_by_template('weblog','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('guestbook','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('2bfriends','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('download','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('ranks','term_id'):
                //     // code...
                //     break;
                // default:
                //     // code...
                //     break;
            }
        }
    ?>
</script>