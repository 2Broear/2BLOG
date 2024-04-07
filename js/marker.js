(function(){
    'use strict';
    const marker = {
        dom: {
            initiate: (marker)=> {
                const _conf = marker.init?._conf,
                      _mods = marker.mods,
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
                tool.innerHTML = `<div class="${_cls.toolInside}"><span class="${_cls.mark}" style="" title="划线${_static.ctxMark}">${_static.ctxMark}</span><i>&nbsp;|&nbsp;</i><span class="${_cls.quote}" title="评论${_static.ctxQuote}" onclick="marker.mods.quote(this)">${_static.ctxQuote}</span><i>&nbsp;|&nbsp;</i><span class="${_cls.copy}" title="${_static.ctxCopy}内容" onclick="marker.mods.copy(this)">${_static.ctxCopy}</span><span class="${_cls.close}" title="${_static.ctxCancel}"></span></div>`; // onclick="marker.mods.close(this, true)" onclick="marker.mods.down(this)" <img src="" alt="avatar" />
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
                    a.${_cls.line} .${_cls.tool} .${_cls.toolInside}{color:black;line-height:27px;font-size:11px;font-weight:normal;font-style:normal;white-space:nowrap;padding:0 5px;border:1px solid #fff;border-radius:5px;box-sizing:border-box;background:linear-gradient(0deg,rgb(245 247 249 / 88%) 0,rgb(255 255 255 / 100%));background:-webkit-linear-gradient(90deg,rgb(245 247 249 / 88%) 0,rgb(255 255 255 / 100%));box-shadow:rgba(0,0,0,0.12) 0 1px 18px;position:relative;user-select:none;-webkit-user-select:none;}
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
                                    let _cookie = _util._cookie;
                                    _cookie.set(_static.userNick, userinfo.nick);
                                    _cookie.set(_static.userMail, userinfo.mail);
                                    _cookie.set(_static.userMid, userinfo.mid);
                                    if(_util._etc.funValidator(cbk)) {
                                        cbk();
                                    }
                                };
                            if(typeof md5 === 'undefined') {
                                console.log('init md5..');
                                marker._utils._etc.dynamicLoad(_static.md5Url, ()=>execUpdate(userinfo, callback));
                            }else{
                                console.log('md5 initiated, updating records..');
                                execUpdate(userinfo, callback);
                            }
                        },
                        _outputMarkers = ()=> {
                            let dataList = marker.data.list,
                                localMarks = Object.keys(dataList);
                            if(_res_failure) {
                                console.log('Abort on _outputMarkers:', res.msg);
                                if(localMarks.length > 0) {
                                    // clear all local-data
                                    Object.keys(dataList).forEach(mark=> {
                                        console.log(`a non-updated local-marker(${mark}: ${_util._cookie.get(mark)}) was found, deleting.. (this mark should not be exists, perhaps caused by deletion failure)`);
                                        _util._cookie.del(mark, marker.data.path);
                                    });
                                }
                                return;
                            }
                            let curUserMid = marker.data.user.mid; // get curUserMid after marker user init.
                            // 输出所有服务端标记（未检验）
                            Object.keys(res).forEach(key=> {
                                let each_val = res[key];
                                if(each_val==null) {
                                    return;
                                }
                                // update currentUserCounts from remote
                                if(curUserMid === key){
                                    let remote_counts = res[key].length;
                                    _static.dataCounts = remote_counts;
                                    // 冻结 _conf 对象 static 成员
                                    Object.freeze(_static); //for dataCounts edit limitation
                                    marker.data = {counts: _static.dataCounts};
                                }
                                each_val.forEach(item=> {
                                    // console.log(key, item);
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
                                        }
                                        mark_paragraph = effectsArea_childs[mark_index];
                                        console.log('search done! found(firstIndexOf) on mark_uid('+mark_index+')');
                                    }
                                    tool_avatar.alt = 'avatar';
                                    tool_avatar.src = `${_static.avatar}avatar/${key}?d=mp&s=100&v=1.3.10`;
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
                            let curUserMarks = res[curUserMid],
                                dataPrefix = _static.dataPrefix;
                            // 返回本地记录中不存在于远程记录的元素（始终检验）
                            if(localMarks.length > 0) {
                                const existNonDeletedMarks = localMarks.filter(local => {
                                    // localNotInRemote: delete local marks which is non-existent from remote
                                    let localNotInRemote = !curUserMarks.some(remote => {
                                            return local === dataPrefix + remote.rid;
                                        });
                                    // console.log('localNotInRemote:', localNotInRemote);
                                    return localNotInRemote;
                                });
                                if(existNonDeletedMarks.length > 0) {
                                    existNonDeletedMarks.forEach(mark=> {
                                        console.log(`a local marker was found on non-existent remoteMarks(perhaps server delays), del cookie(${mark}: ${_util._cookie.get(mark)}) from local..`, '(existNonDeletedMarks: slow-down the frequency!)');
                                        // update(del) local-record
                                        _util._cookie.del(mark, marker.data.path);
                                        // no need for dom changes
                                    });
                                }else{
                                    console.log('remoteMarks: ALL MATCHED');
                                }
                            }
                            // 对比返回的远程用户标记与本地记录（仅存在记录检查）
                            if(curUserMarks) {
                                // 返回数据（已响应）——>对比本地记录（未匹配到本地记录）——>新增本地记录
                                if(curUserMarks.length > 0) {
                                    const existNonUpdatedMarks = curUserMarks.filter(remote => {
                                        let remote_mark = dataPrefix + remote.rid;
                                        // remoteNotInLocal: delete remote marks which is non-existent from local
                                        let remoteNotInLocal = !localMarks.some(local_mark => {
                                                return remote_mark === local_mark;
                                            });
                                        // console.log('remoteNotInLocal:', remoteNotInLocal);
                                        return remoteNotInLocal;
                                    });
                                    if(existNonUpdatedMarks.length > 0) {
                                        existNonUpdatedMarks.forEach(marks=> {
                                            const mark_rid = marks.rid,
                                                  mark_cname = dataPrefix + mark_rid,
                                                  ts_caches = JSON.parse(marker.data._caches),
                                                  cached_ts = ts_caches[mark_cname];
                                            // update localMarks only if localStorage exists(incase of any other user device get involved)
                                            if(cached_ts) {
                                                console.log(`a remote marker(${mark_cname}: ${cached_ts}) was found on non-existent localMarks(perhaps server delays), add cookie to local..`, '(existNonUpdatedMarks: slow-down the frequency!)');
                                                // update(add) local-data instantly
                                                _util._cookie.set(mark_cname, cached_ts, marker.data.path, 365);
                                                // marker.data = {counts: marker_num - 1}; // decrease counts
                                                // dom changes(no longer needed)
                                                // _mods.close(_els.effectsArea.querySelector('a[data-rid="'+mark_rid+'"]'));
                                            }else{
                                                console.log(`marker(${mark_rid}) belongs to another device(not found on localStorage!)`, ts_caches);
                                            }
                                        });
                                    }else{
                                        console.log('localMarks: ALL MATCHED');
                                    }
                                }
                            }
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
            _event: {
                get: (event)=> {
                    return event ? event : window.event;
                },
                add: function(element=null, type='', handler=false, cb=false) {
                    let init_func = function(element=null, type='', handler=false, callback=false){
                            if(!element || !type) return;
                            marker._utils._etc.assert(handler && typeof handler==='function', 'addEvent callback err.');
                            callback();
                            console.debug(type, 'event loaded.');
                        };
                    try {
                        if(element.addEventListener){
                            this.add = function(element=null, type='', handler=false, cb=false){
                                init_func(element, type, handler, ()=>{
                                    element.addEventListener(type, handler, cb);
                                });
                            };
                        }else if(element.attachEvent){
                            this.add = function(element=null, type='', handler=false){
                                init_func(element, type, handler, ()=>{
                                    element.attachEvent('on'+type, handler);
                                });
                            };
                        }else{
                            this.add = function(element=null, type='', handler=false){
                                init_func(element, type, handler, ()=>{
                                    element['on'+type] = handler;
                                });
                            };
                        }
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
            },
            _closure: {
                debouncer: (callback=false, delay=200)=> {
                    var timer = null;
                    return function(...args) {
                        if(timer) clearTimeout(timer);
                        timer = setTimeout(function(){
                            callback.apply(this, args);
                        }, delay);
                    };
                },
                throttler: (callback=false, delay=200)=> {
                    let closure_variable = true;  //default running
                    return function(...args) {
                        if(!closure_variable) return;  //now running..
                        closure_variable = false;  //stop running
                        setTimeout(()=>{
                            callback.apply(this, args); //arguments
                            closure_variable = true;  //reset running
                        }, delay);
                    };
                },
            },
            _cookie: {
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
            _dom: {
                valider: (node, textNode=true) => {
                    let valid_node = node && node instanceof HTMLElement;
                    return textNode ? valid_node && node.nodeType===1 : valid_node;
                },
                indexer: (node=null)=> {
                    return node ? Array.prototype.indexOf.call(node.parentElement.children, node) : 0;
                },
                finder: (element=null, className='', mod=0, tagName="")=> {
                    if (mod === '' || typeof mod !== 'number') {
                        console.warn('invalid mod, must be value 0 or 1', mod);
                        return null;
                    }
                    switch (mod) {
                        case 1:
                            let childElements = tagName ? element.getElementsByTagName(tagName) : element.getElementsByClassName(className);
                            return childElements.length>0 ? childElements : null;
                        case 0:
                        default:
                            let parent = element.parentElement;
                            while (parent) {
                                if(tagName) {
                                    if (parent.nodeName === tagName.toUpperCase()) {
                                        return parent;
                                    }
                                }else{
                                    if (parent.classList && parent.classList.contains(className)) {
                                        return parent;
                                    }
                                }
                                parent = parent.parentElement;
                            }
                            return null; // 如果未找到匹配的父级元素
                    }
                },
                clicker: function(parent, cls, callback, tag=null) {
                    const _util = marker._utils,
                          _event = _util._event; //this
                    _event.add(parent, 'click', function(e) {
                        let event = _event.get(e),
                            target = _event.getTarget(event);
                        target = tag ? target.closest(tag) : target.closest('.' + cls);
                        if (!_util._dom.valider(target)) {
                            return;
                        }
                        if(_util._etc.funValidator(callback)) {
                            callback(target, event);
                        }else{
                            console.log('invalid clicker callback', callback);
                        }
                    });
                },
            },
            _diy: {
                randomString: function(num=16) {
                    const rs = Math.random().toString(num);
                    return rs.substring(2, rs.length);
                },
                parseParameters: function(obj, post=false) {
                    // console.log(this);
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
            },
            _etc: {
                isObject: (obj)=> {
                    return Object.prototype.toString.call(obj)==='[object Object]';
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
                funValidator: function(fn, exec=false) {
                    if(!fn || typeof fn!=='function'){
                        return false;
                    }
                    return exec ? fn.apply(this.arguments) : true;
                },
                argsRewriter: function(args={}, presets={}, callback=false) {
                    try {
                        const _this = this._utils ? this : marker,
                              _util = _this._utils._etc;
                        _util.assert(Object.prototype.toString.call(args)==='[object Object]', 'invalid arguments provided!');
                        // rewrite conf
                        _this.init.prototype._singleton_conf._rewriter(args, presets);
                        if(!_util.funValidator(callback)){
                            return args;
                        }
                        // callback returns
                        callback(args);
                    } catch (error) {
                        console.log(error);
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
                            console[type](msg);
                            break;
                    }
                },
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
                    }
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
            isMarkerReachedMax: (server_verify = false)=> {
                const _static = marker.init._conf.static,
                      maxDataLength = parseInt(_static.dataMax);
                // auto-exec server_verify if un-editable server-counts reached_max
                if(server_verify) { //_static.dataCounts >= maxDataLength
                    return new Promise((resolve, reject) => {
                        marker.mods.fetch(marker.init._conf.static.apiUrl, {
                            'fetch': 1,
                            'count': 1,
                        }, (res) => {
                            if(res.code&&res.code==200) {
                                let res_counts = parseInt(res.msg),
                                    max_reached = res_counts >= maxDataLength;
                                if(max_reached) {
                                    marker.data = {counts: res_counts};
                                    console.log(`counts restored! the server responsed: ${res_counts} which is reached_max limits(${maxDataLength}).`);
                                }
                                resolve(max_reached);
                            }else{
                                reject(marker.data._counts >= maxDataLength);
                                console.warn('rejected of', res);
                            }
                        });
                    }).then(res=> {
                        return res;
                    }).catch(err=> {
                        return err;
                    });
                }
                // localCompare might includes same-user markers from another device!
                return marker.data.stat.counts >= maxDataLength || Object.keys(marker.data.list).length >= maxDataLength;
            },
            isMarkerSelectable: (node = null)=> {
                let notOnList = true,
                    _dom = marker._utils._dom;
                if(!_dom.valider(node) || !node.classList) {
                    console.warn('invalid nodes or classList', node);
                    return notOnList;
                }
                let _conf = marker.init._conf,
                    blackList = _conf.class.blackList,
                    blackTags = ['h1','h2','h3','h4','h5','h6','details','summary','code','mark','del'];
                for(let i=0;i<blackTags.length;i++){
                    let blackTag = blackTags[i].toUpperCase();
                    if(node.tagName===blackTag || _dom.finder(node, '', 0, blackTag)) {
                        console.warn('unSelectable content detected! (node/parentNode tagName: '+blackTag+')');
                        return;
                    }
                }
                blackList = blackList instanceof Array ? blackList : [];
                let blackLens = blackList.length;
                if(blackLens > 0) {
                    for(let i=0;i<blackLens;i++){
                        let blackClass = blackList[i];
                        if(node.classList.contains(blackClass) || _dom.finder(node, blackClass, 0)) {
                            notOnList = false;
                            console.warn('unSelectable content detected! (node/parentNode contains "'+blackClass+'")', node);
                            break;
                        }
                    }
                }
                return notOnList;
            },
            isNodeMarkAble: (node = null)=> {
                return node&&node.classList&&node.classList.contains(marker.init._conf.class.line);
            },
            isNodeMarkDone: (node = null)=> {
                return node&&node.classList&&node.classList.contains(marker.init._conf.class.done);
            },
            isNodeTextOnly: (node = null)=> {
                let node_child = node.children;
                switch(true){
                    case !marker._utils._dom.valider(node):
                        console.warn('invalid nodes wrapped.', node);
                        return false;
                        // break;
                    case node_child.length<=0:
                        console.debug('No childNodes wrapped in selections.', node_child);
                        return null;
                        // break;
                }
                let child_classes = node_child[0].classList;
                return child_classes&&child_classes.contains(marker.init._conf.class.tool);
            },
            isMultiSameChar: (paragraph, context, vars=false)=> {
                let uniqune_char = marker._utils._diy.contextIndexes(paragraph, context);
                return vars ? uniqune_char : uniqune_char.length > 1;
            },
            _adjustPending: (status=0, callback=false, delay=0)=> {
                if(callback){
                    // delay must under callback(always true on the outside)
                    delay = delay ? delay : marker.init._conf.static.dataDelay;
                    let timer = setTimeout(() => {
                            marker.data = {pending: status}; // adjusting pending statu.
                            if(marker._utils._etc.funValidator(callback)) {
                                callback();
                            }
                            clearTimeout(timer); // 在回调函数执行后清除 setTimeout
                        }, delay);
                }else{
                    marker.data = {pending: status}; // adjusting pending statu.
                }
            },
        },
        mods: {
            mark: function(){
                const _util = marker._utils,
                      _status = marker.status,
                      _conf = marker.init._conf,
                      _static = _conf.static,
                      _class = _conf.class,
                      _element = _conf.element;
                let selectedText = this.toString(),
                    selectedLen = selectedText.length,
                    selectedMin = parseInt(_static.dataMin);
                if (selectedLen < selectedMin || this.isCollapsed) {
                    console.debug(`Abort on context min-length(required ${selectedMin}+), selectedText length: ${selectedLen}`);
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
                    if(_status.isNodeMarkAble(contains_node) && _status.isNodeMarkDone(contains_node)) {
                        console.warn('selection contains marked-parent content, canceling..', contains_node);
                        return;
                    }
                    let marks = _element.line.cloneNode(true),
                        tool = _element.tool,
                        rid = _util._diy.randomString(); 
                    marks.dataset.rid = rid;
                    range.surroundContents(marks);
                    // check marker is selectable
                    const tool_mark = tool.querySelector('.'+_class.mark),
                          tool_disabled = tool_mark.classList.contains(_class.disabled);
                    if(_status.isMarkerReachedMax()){
                        // rewrite stored tool context only if tool_mark on enabled statu.(decreasing origin_mark dom edit)
                        if(!tool_disabled) {
                            tool_mark.classList.add(_class.disabled);
                            tool_mark.textContent = _static.ctxMarkMax;
                        }
                    }else{
                        if(tool_disabled) {
                            tool_mark.classList.remove(_class.disabled);
                            tool_mark.textContent = _static.ctxMark;
                        }
                    }
                    tool = tool.cloneNode(true);
                    marks.appendChild(tool);
                    this.removeRange(range); //this.removeAllRanges();
                    // close mark it-self if selections under markable-parent
                    const _dom = _util._dom,
                          marks_parents = _dom.finder(marks, _class.line);
                    if(marks_parents != null){
                        console.warn('markable-parent (deep-level) exists, unwrapping self marks', marks);
                        marker.mods.close(marks);  // marker.mods.close(marks_parents);
                        return;
                    }
                    const marks_children = marks.querySelectorAll('.' + _class.line); // marks.children;
                    if(marks_children.length <= 0){
                        return;
                    }
                    // console.log(marks_children);
                    marks_children.forEach((each_line)=>{
                        let dynamic_line = _element.effectsArea.querySelector('[data-rid="'+each_line.dataset.rid+'"]'),
                            line_child = _dom.finder(each_line, _class.line, 1);
                        // close inside wrapped child
                        if(line_child && line_child.length >= 1){
                            line_child = line_child[0];
                            console.warn('markable-child (deep level) exists, unwrapping line_child', line_child);
                            marker.mods.close(line_child);
                            return;
                        }
                        // close inside wrapped parent
                        if(_status.isNodeMarkDone(each_line)){
                            let line_parent = _dom.finder(each_line, _class.line);
                            if(line_parent != null){
                                console.warn('selection contains marked-parents content, unwrapping line_parent..', line_parent);
                                marker.mods.close(line_parent);
                            }else{
                                let dynamic_parent = _dom.finder(dynamic_line, _class.line);
                                console.warn('selection contains marked-children content, unwrapping dynamic_parent..', dynamic_parent);
                                marker.mods.close(dynamic_parent); // reject dynamic marks
                                // marker.mods.close(dynamic_line); // ckear-all-children
                            }
                            return;
                        }
                        // USE dynamic_line insted of each_line for close(null) confused innerHTML structure issue.
                        console.log('markable-child wrap exists, unwrapping dynamic_line..', dynamic_line);
                        marker.mods.close(dynamic_line);
                    });
                } catch (error) {
                    console.warn(error);
                }
            },
            down: function(node) {
                if(marker.data.stat.pending) {
                    console.warn('Abort on too-fast marking off! (wait a second then try to re-mark again.)');
                    return;
                }
                const _util = marker._utils,
                      _status = marker.status,
                      _static = marker.init._conf.static;
                if(!_util._dom.valider(node)) {
                    return node;
                }
                node.textContent = _static.ctxMarking;
                // compare local-counts(read only) for decreasing server_verify requests. (bug: read-only variables can not be updated instantly, always use server_verify)
                const ifServerReachedMax = _status.isMarkerReachedMax(true);
                ifServerReachedMax.then(res=>{
                    if(res) {
                        alert('Abort on reaching(server side) dataMax!');
                        this.close(node);
                        return;
                    }
                    const _class = marker.init._conf.class,
                          mark_node = _util._dom.finder(node, _class.line),
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
                    const mark_indexes = _util._dom.indexer(mark_paragraph) + '-' + paragraph_context.indexOf(mark_text);
                    this.update({
                        rid: mark_rid,
                        uid: mark_indexes,
                        text: mark_text,
                    }, (res)=> {
                        // local updates (dom changes)
                        mark_node.classList.add(_class.done);
                        node.innerHTML = `<small>${marker.init._conf.static.ctxMarked}（${mark_rid}）</small>`;
                        node.classList.add(_class.disabled);
                        mark_node.dataset.uid = mark_indexes;
                    });
                }).catch(err=>{
                    console.warn(err);
                });
            },
            quote: function(node) {
                if(!marker._utils._dom.valider(node)) return node;
                const _class = marker.init._conf.class,
                      mark_node = marker._utils._dom.finder(node, _class.line),
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
            copy: function(node) {
                if(!marker._utils._dom.valider(node)) return node;
                const _conf = marker.init._conf,
                      copy_mark = marker._utils._dom.finder(node, _conf.class.line),
                      range = document.createRange(),
                      selection = window.getSelection();
                range.selectNodeContents(copy_mark.firstChild);
                selection.removeAllRanges();
                selection.addRange(range);
                //exec copy..
                document.execCommand('copy');
                selection.removeAllRanges();
                node.textContent = _conf.static.ctxCopied;
            },
            close: function(node, update=false) {
                if(!marker._utils._dom.valider(node)) return node;
                // 执行 close() 操作后将打乱标记点父级（bug：无法再次找到已定义的子级元素，已通过动态选择each_line解决）
                const _util = marker._utils,
                      _dom = _util._dom,
                      _class = marker.init._conf.class,
                      _status = marker.status,
                      mark_node = _status.isNodeMarkAble(node) ? node : _dom.finder(node, _class.line),
                      mark_dataset = mark_node.dataset,
                      mark_rid = mark_dataset.rid,
                      mark_uid = mark_dataset.uid;
                // deletion auth.
                if(!_status.isMarkerAccessable()){
                    alert('marker deletion failure, anonymous not allowed..');
                    return;
                }
                let update_dom = ()=> {
                    let mark_tools = _dom.finder(mark_node, _class.tool, 1);
                    if(mark_tools.length >= 1) {
                        mark_tools[mark_tools.length-1].remove();  // mark_tools[0].remove();
                    }
                    let replace_content = _status.isNodeTextOnly(mark_node) ? mark_node.firstChild.textContent : mark_node.innerHTML;
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
                            update_dom(); // local updates (dom changes)
                        }, true);
                    }else{
                        mark_node.classList.remove(processing);
                    }
                }else{
                    update_dom();
                }
            },
            update: function(updObj={}, cbk=false, del=false) {
                if(!marker._utils._etc.isObject(updObj) || Object.keys(updObj).length<1) {
                    console.warn('remote updates failed, invalid updateObject.', updObj);
                    return;
                }
                const _util = marker._utils,
                      _static = marker.init._conf.static,
                      marker_rid = updObj.rid,
                      marker_uid = updObj.uid,
                      mark_node = updObj.node,
                      marker_num = marker.data.stat.counts,
                      mark_cname = _static.dataPrefix + marker_rid,
                      _valid_cbk = marker._utils._etc.funValidator(cbk),
                      _status = marker.status,
                      _apiUrl = _static.apiUrl;
                // start pending(exec immediately without callback)..
                _status._adjustPending(1);
                // deletion load ts from local
                if(del) {
                    let local_ts = marker.data.list[mark_cname];
                    // update(del) cookies Immediately(dual-check insurance)
                    _util._cookie.del(mark_cname, marker.data.path); // local updates
                    // update currentUserCounts(for no-refresh page max-mark limitation)
                    marker.data = {counts: marker_num - 1}; // decrease counts
                    this.fetch(_apiUrl, {
                        'del': 1,
                        'rid': marker_rid,
                        'ts': updObj.ts ? updObj.ts : local_ts,
                    }, (res)=> {
                        if(res.code && res.code!==200){
                            alert(`${res.msg}（err#${res.code}）`);
                            if(mark_node&&mark_node.classList) mark_node.classList.remove(updObj.cls);
                            _status._adjustPending(0);  // pending abort..
                            return;
                        }
                        console.log(`${mark_cname} deleted(ts: ${local_ts}) `, res.msg);
                        // pending stop..
                        _status._adjustPending(0, ()=> {
                            _valid_cbk ? cbk(res) : console.log('update(del) succesed(no calls)', res.msg);
                        });
                    });
                    return;
                }
                
                // addition load ts via real-time
                let realtime_ts = Date.now();
                // update local data Immediately no mater backend-saved or not. (add/del dual check supported)
                let _cnames = _static.dataCaches,
                    ts_caches = window.localStorage.getItem(_cnames);
                // record of localStorage(ts caches for del)
                ts_caches = ts_caches ? JSON.parse(ts_caches) : {};
                ts_caches[mark_cname] = realtime_ts;
                window.localStorage.setItem(_cnames, JSON.stringify(ts_caches));
                // update(add) cookies Immediately(dual-check insurance)
                _util._cookie.set(mark_cname, realtime_ts, marker.data.path, 365);
                // update currentUserCounts(for no-refresh page max-mark limitation)
                marker.data = {counts: marker_num + 1};  // increase counts 
                // exec backend-dom updates
                this.fetch(_apiUrl, {
                    'rid': marker_rid,
                    'uid': updObj.uid,
                    "text": updObj.text,
                    'ts': realtime_ts,
                }, (res)=> {
                    if(res.code && res.code!==200){
                        alert(`${res.msg}（err#${res.code}）`);
                        _status._adjustPending(0);  // pending abort..
                        return;
                    }
                    console.log(`${mark_cname} updated(ts: ${realtime_ts}) `, res.msg);
                    _status._adjustPending(0, ()=> {
                        _valid_cbk ? cbk(res) : console.log('update(add) succesed(no calls)', res.msg);
                    });
                });
            },
            fetch: (url='', _obj={}, cbk=false, cbks=false)=> {
                url = url || marker.init._conf.static.apiUrl;
                const _utils = marker._utils,
                      _util = _utils._etc,
                      _data = marker.data;
                _util.argsRewriter.call(marker, _obj, {
                    'pid': marker.init._conf.static.postId,
                    'fetch': 0,
                    'counts': 0,
                    'del': 0,
                    'ts': 0,
                    "nick": _data.user.nick,
                    "mail": _data.user.mail,
                }, (obj_)=> {
                    fetch(`${url}&${_utils._diy.parseParameters(obj_)}`, {}).then(response => {
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
                const _this = Object.getPrototypeOf(this)!==marker.init.prototype ? marker.init.prototype : this;
                try {
                    const _conf_res = _this._singleton_conf._rewriter.call(_this, user_conf);
                    // 冻结 _conf、_conf.static 对象成员（）
                    Object.freeze(_conf_res); // Object.freeze(_conf_res.static); //for dataMax limitation
                    // rewrite user-conf
                    marker.init._conf = _conf_res;
                    // 防止重写 _conf 对象
                    Object.defineProperty(marker.init, '_conf', {
                        value: _conf_res,
                        writable: false
                    });
                    // init&load dom..
                    marker.dom.initiate(marker);
                    // check marker status before initiate.(prevent mouseup events exec mark())
                    if(!marker.status.isMarkerAvailable()) {
                        throw new Error('marker unavailable, register init failed..');
                    }
                    // bind events
                    const _conf = marker.init._conf,
                          _dom = marker._utils._dom,
                          _closure = marker._utils._closure,
                          method = marker.mods,
                          effect = _conf.element.effectsArea;
                    marker._utils._event.add(effect, 'mouseup', _closure.debouncer(method.mark.bind(window.getSelection()), 100)); //marker.mods.mark.bind(window.getSelection())
                    _dom.clicker(effect, _conf.class.close, _closure.debouncer((t)=>method.close(t, true), 150));
                    _dom.clicker(effect, _conf.class.mark, _closure.debouncer((t)=>method.down(t), 200));
                    // _dom.clicker(effect, '', (t)=>console.log('h2 clicked.',t), 'h2');
                    console.log('marker initialized.', marker);
                } catch (error) {
                    console.log(error);
                }
            },
        },
        get data(){
            let _static = this.init._conf.static,
                _setter = this.init._conf.setter,
                _cookie = this._utils._cookie,
                regExp = new RegExp(`${_static.dataPrefix}(.*?)=(.*?);`, 'g'),
                stored = document.cookie.match(regExp) || [],
                result = {};
            if(_setter.list){
                result = _setter.list;
            }else{
                if(stored.length>=1){
                    stored.map(item => {
                        let pair = item.split("="),
                            key = pair[0],
                            val = pair[1].split(";")[0];
                        // return { [key]: val, }; // 修改返回的对象结构 { key, val }
                        result[key] = val; // 将键值对存入 result 对象中
                    });
                }
            }
            return {
                'user': {
                    nick: _cookie.get(_static.userNick) || _setter.nick,
                    mail: _cookie.get(_static.userMail) || _setter.mail,
                    mid: _cookie.get(_static.userMid) || _setter.mid,
                },
                'stat': {
                    counts: _setter.counts || 0,
                    pending: _setter.pending || 0,
                },
                'list': result,
                'path': window.location.pathname,
                '_caches': window.localStorage.getItem('markerCaches') || '{}',
                '_counts': _static.dataCounts, // freezed
            };
        },
        set data(obj){
            if(!this._utils._etc.isObject(obj)) {
                console.warn('set data error: typeof obj is not an Object!', obj);
                return;
            }
            let setter = this.init._conf.setter;
            Object.keys(obj).forEach(item=> {
                let set_val = obj[item];
                if(set_val || set_val===0) {
                    setter[item] = set_val; // setter[item] ??= set_val;
                }
            });
            // 冻结 setter 成员
            // Object.freeze(setter);
            // 防止重写对象
            // Object.defineProperty(this.init._conf, 'setter', {
            //     value: setter,
            //     writable: false
            // });
        },
    };
    // 冻结对象成员
    Object.freeze(marker);
    // 防止重写对象
    Object.defineProperty(window, 'marker', {
        value: marker,
        writable: false
    });
    // 扩展对象方法
    Object.defineProperties(marker.init.prototype, {
        _singleton_conf: {
            value: function(){
                let privatePresets = {
                        static: {
                            dataMin: 2,
                            dataMax: 3,
                            dataCounts: 0,
                            dataDelay: 1000,
                            dataPrefix: 'marker-',
                            dataCaches: 'markerCaches',
                            lineColor: 'red',
                            lineBold: 10,
                            lineBoldMax: 30,
                            lineAnimate: true,
                            ctxMark: '标记',
                            ctxMarking: '标记中...',
                            ctxMarked: '已标记',
                            ctxCopy: '复制',
                            ctxCopied: '已复制',
                            ctxQuote: '引用',
                            ctxMarkMax: '用户标记已满',
                            ctxCancel: '取消选中/删除',
                            // userinfo do NOT use the same prefix as dataPrefix
                            userNick: 'marker_userNick',
                            userMail: 'marker_userMail',
                            userMid: 'marker_userMid',
                            // request resources
                            md5Url: "/md5.js",
                            apiUrl: "/mark.php",
                            avatar: "//cravatar.com/",
                            postId: window.location.pathname,
                        },
                        class: {
                            line: 'markable',
                            tool: 'tools',
                            toolInside: 'tool-inside',
                            mark: 'mark',
                            copy: 'copy',
                            quote: 'quote',
                            update: 'update',
                            close: 'close',
                            done: 'done',
                            disabled: 'disabled',
                            aniUnderline: 'underline',
                            aniProcess: 'processing',
                            blackList: ['wp-block-quote','wp-block-code','wp-block-table','wp-element-caption'],
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
                return {
                    publicDefault: Object.create(null),
                    _rewriter: function fn(conf=this.publicDefault, opts=privatePresets) {
                        if(!marker._utils._etc.isObject(opts)) return;
                        for(const [key, val] of Object.entries(opts)){
                            // back-write (mark non-existent property)
                            let custom_conf = conf[key];
                            if(Array.isArray(val)){
                                if(Array.isArray(custom_conf)){
                                    conf[key] = custom_conf.concat(val);
                                }else{
                                    let _val = custom_conf ? custom_conf : val;
                                    conf[key] = custom_conf ? val.concat(_val.toString().split(",")) : _val;
                                }
                            }else{
                                conf[key] ??= val; //conf[key] ||= val;
                            }
                            // recursion-loop (use fn call-stack for recursion-func)
                            fn.apply(this, [custom_conf, val]);
                        }
                        // clear closure recycle-quotes
                        opts = privatePresets = null;
                        // 冻结 conf 对象 static 成员
                        // Object.freeze(conf.static);
                        return conf;
                    },
                };
            }(),
            configurable: false,
            writable: false
        },
    });
}).call(window);