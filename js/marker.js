(function(){
    'use strict';
    const marker = {
        dom: {
            initiate: (marker)=> {
                const {init: {_conf: {static: {ctxMark:s_ctxMark, ctxMarked:s_ctxMarked, ctxQuote:s_ctxQuote, ctxCopy:s_ctxCopy, ctxNote:s_ctxNote, ctxCancel:s_ctxCancel, ctxLike:s_ctxLike,ctxLiked:s_ctxLiked, lineAnimate:s_lineAnimate, lineKeepTop:s_lineKeepTop, lineColor:s_lineColor, lineColors:s_lineColors, lineBold:s_lineBold, lineBoldMax:s_lineBoldMax, lineDegrees:s_lineDegrees, userNick:s_userNick, userMail:s_userMail, userMid:s_userMid, md5Url:s_md5Url, dataAlive:s_dataAlive, dataPrefix:s_dataPrefix, dataStream:s_dataStream, avatar:s_avatar, useNote:s_useNote, useCopy:s_useCopy, useQuote:s_useQuote, likeMax:s_likeMax}, class: {line:c_line, tool:c_tool, toolIn:c_toolIn, avatars:c_avatars, mark:c_mark, done:c_done, note:c_note, quote:c_quote, copy:c_copy, close:c_close, like:c_like, liked:c_liked, underline:c_underline, processing:c_processing, disabled:c_disabled, }, element: {commentInfo: {userNick:e_userNick, userMail:e_userMail}, effectsArea:e_effectsArea}}}, data: {list:d_list, path:d_path, user: {mid:d_mid}, stat:{counts:d_counts}, _caches:d_caches,}, _utils: {_cookie: {get:getCookie, set:setCookie, del:delCookie}, _etc: {funValidator, dynamicLoad, isObject}, _dom: {finder, valider}}, status: {isMarkerUserUpdate, isMarkerAccessable}, mods: {fetch}} = marker;
                // changes required
                let _conf = marker.init._conf,
                    style = document.createElement('STYLE'),
                    marks = document.createElement("A"),
                    tools = document.createElement("DIV"),
                    toolsInside = document.createElement("DIV"),
                    toolsLoader = (container, cls, title, content)=> {
                        let toolsDivider = document.createElement("I"),
                            useContainer = document.createElement("SPAN");
                        toolsDivider.innerHTML = "&nbsp;|&nbsp;";
                        container.appendChild(toolsDivider);
                        useContainer.className = cls;
                        useContainer.title = title;
                        useContainer.innerHTML = content;
                        container.appendChild(useContainer);
                    };
                marks.className = c_line;
                marks.href = 'javascript:void(0);';
                marks.rel = 'nofollow';
                _conf.element.line = marks; //e_line
                tools.className = c_tool;
                toolsInside.className = c_toolIn;
                const tool_avatars = document.createElement('DIV');
                tool_avatars.className = c_avatars;
                toolsInside.appendChild(tool_avatars);
                // load basic mark
                toolsLoader(toolsInside, c_mark, `划线${s_ctxMark}`, s_ctxMark);
                // selectively load tools
                if(s_useNote) toolsLoader(toolsInside, c_note, `${s_ctxNote}内容`, `<label>${s_ctxNote}</label><input type="text" placeholder="输入注释.." max="50" />`);
                if(s_useCopy) toolsLoader(toolsInside, c_copy, `${s_ctxCopy}内容`, s_ctxCopy);
                if(s_useQuote) toolsLoader(toolsInside, c_quote, `评论${s_ctxQuote}`, s_ctxQuote);
                // always load closer(selectively remove)
                const tool_close = document.createElement('SPAN');
                tool_close.className = c_close;
                tool_close.title = s_ctxCancel;
                toolsInside.appendChild(tool_close);
                tools.appendChild(toolsInside);
                _conf.element.tool = tools; //e_tool
                if(s_lineAnimate) style.textContent = `@keyframes ${c_underline}{0%{background-size:0% ${s_lineBold}%;}100%{background-size:100% ${s_lineBold}%;}}@keyframes ${c_processing}{0%{transform:rotate(0deg)}100%{transform:rotate(360deg);}}`;
                style.textContent += `
                    a.${c_line}.${c_done}{/*animation:none;-webkit-animation:none;*/transition:none;}
                    a.${c_line}:hover,a.${c_line}.${c_done}{background-size:100% ${s_lineBoldMax}%;}
                    a.${c_line}:hover{color:inherit!important;z-index:1!important;}
                    a.${c_line}{color:inherit;text-decoration:none!important;background:-webkit-linear-gradient(${s_lineDegrees}deg, ${s_lineColor} 0%, ${s_lineColors} 100%) no-repeat left 100%/0 ${s_lineBold}%;background:linear-gradient(${s_lineDegrees}deg, ${s_lineColor} 0%, ${s_lineColors} 100%) no-repeat left 100%/0 ${s_lineBold}%;background-size:100% ${s_lineBold}%;transition:background-size .15s ease;animation:${c_underline} 1s 1 ease;-webkit-animation:${c_underline} 1s 1 ease;cursor:text;user-select:text;-webkit-user-drag:none;position:relative;}
                    a.${c_line}.${c_processing} .${c_tool},
                    a.${c_line}:hover .${c_tool}{padding:10px 0 50px;opacity:1;z-index:1;}
                    a.${c_line} .${c_tool}{padding-bottom:15px;position:absolute;top:0;left:0;transform:translate(0,-50%);opacity:0;z-index:-1;transition:all .15s ease;font-family:auto;}
                    a.${c_line} .${c_tool} .${c_toolIn}{color:black;line-height:27px;font-size:11px;font-weight:normal;font-style:normal;white-space:nowrap;padding:0 5px;border:1px solid #fff;border-radius:5px;box-sizing:border-box;background:linear-gradient(0deg,#f5f7f9 0,#ffffff);background:-webkit-linear-gradient(90deg,#f5f7f9 0,#ffffff);box-shadow:rgba(0,0,0,0.12) 0 1px 18px;position:relative;user-select:none;-webkit-user-select:none;}
                    a.${c_line}.${c_processing} .${c_tool} .${c_note},
                    a.${c_line}.${c_done}:hover .${c_tool} .${c_note}{margin:0 0 10px 10px;}
                    a.${c_line}.${c_done} .${c_tool} .${c_note}{position:absolute;bottom:100%;left:0;min-width:2em;max-width:100%;white-space:normal;padding: 5px 10px;color:gray;line-height:18px;font-weight:normal;margin:0px;transition:margin .15s linear;}
                    a.${c_line}.${c_done} .${c_tool} .${c_note}:after{content: "";width: 0;height: 0;border-style: solid;border-color: currentColor transparent transparent transparent;border-width: 7px 10px 0px 0px;position: inherit;left: 16px;bottom: -6px;z-index: 1;right:auto;margin:auto;}
                    a.${c_line}.${c_done} .${c_tool} .${c_note},
                    a.${c_line} .${c_tool} .${c_note} input,
                    a.${c_line} .${c_tool} .${c_note}:hover input{border-radius:50px;color:white;background:currentColor;box-shadow:inherit;/*border: 1px solid currentColor;background:inherit;*/}
                    a.${c_line} .${c_tool} .${c_note}:hover input{width: 100px;margin: auto 5px;padding: 2px 8px;color: inherit;border: 1px solid currentColor;background: transparent;}
                    a.${c_line} .${c_tool} .${c_note} input{width: 0px;padding:0px;font-size: 10px;box-sizing: border-box;transition:all .15s ease;border:none;}
                    a.${c_line}.${c_done} .${c_tool} .${c_note} label{color:black;/*font-style: italic;*/}
                    a.${c_line} .${c_tool} i:first-of-type,
                    a.${c_line}.${c_done} .${c_tool} .${c_note} input{border-color:currentColor!important;display:none;}
                    a.${c_line}.${c_done} .${c_tool} .${c_avatars}{margin:2px 5px 3px 10px;}
                    a.${c_line} .${c_tool} .${c_avatars}{margin: 3px 0px 2px 0px;display: inline-block;line-height: normal;vertical-align: middle;}
                    a.${c_line} .${c_tool} .${c_avatars} img:first-of-type{left:0;border:0;}
                    a.${c_line} .${c_tool} .${c_avatars} img{max-width: 23px;border-radius: 50%;display:block;margin:0;border: 2px solid white;box-sizing: content-box;margin-left:-10px!important;/*position:relative;left:-10px;*/}
                    a.${c_line} .${c_tool} i{font-style:normal;}
                    a.${c_line} .${c_tool} i, a.${c_line} .${c_tool} .${c_avatars} img,a.${c_line} .${c_tool} span{display: inline-block;vertical-align: middle;margin:auto;}
                    a.${c_line} .${c_tool} span:hover{font-weight:bold;}
                    a.${c_line}.${c_disabled} .${c_tool} span, a.${c_line} .${c_tool} i, a.${c_line} .${c_tool} span.${c_disabled}{opacity:.75;pointer-events:none;}
                    a.${c_line} .${c_tool} i{opacity:.35;}
                    a.${c_line} .${c_tool} span{cursor:pointer;}
                    a.${c_line} .${c_tool} span.${c_close}::before,a.${c_line} .${c_tool} span.${c_close}::after{content:'';width:68%;height:2px;display:block;background:currentColor;position:inherit;top:50%;left:50%;transform:translate(-50%,-50%) rotate(45deg);margin:inherit;border:none;}
                    a.${c_line} .${c_tool} span.${c_close}::after{transform:translate(-50%,-50%) rotate(-45deg);}
                    a.${c_line}.${c_processing} .${c_tool} span.${c_close}{animation:${c_processing} linear 1s infinite;-webkit-animation:${c_processing} linear 1s infinite;pointer-events:none;}
                    a.${c_line} .${c_tool} span.${c_like}:hover,
                    a.${c_line} .${c_tool} span.${c_close}:hover{transform:scale(1.25);-webkit-transform:scale(1.25)}
                    a.${c_line} .${c_tool} span.${c_like},
                    a.${c_line} .${c_tool} span.${c_close}{width:10px;height:10px;color:white;background:${s_lineColor};padding:1px;border:2px solid;border-radius:50%;position:absolute;top:-7px;right:-7px;}
                    a.${c_line} .${c_tool} span.${c_like}{width:auto;height:auto;font-size:10px;line-height:12px;padding:1px 5px;margin:-5px;border-radius:25px;background:limegreen;font-weight:bold;}
                    a.${c_line} .${c_tool} span.${c_liked}{background:orangered;}
                    @media (prefers-color-scheme: dark) {
                        a.${c_line} .${c_tool} .${c_avatars} img{opacity:1;border-color:#4a4a4a}
                        a.${c_line} .${c_tool} span.${c_like},a.${c_line} .${c_tool} span.${c_close},a.${c_line} .${c_tool} .${c_note}{color: #4a4a4a!important;}
                        a.${c_line} .${c_tool} .${c_note} label,a.${c_line} .${c_tool} .${c_note} input{color: lightgray!important;}
                        a.${c_line} .${c_tool} .${c_toolIn}{color: lightgray;border-color: #4a4a4a;background: -webkit-linear-gradient(90deg, #3a3a3a 0, #4a4a4a);background: linear-gradient(0deg, #3a3a3a 0, #4a4a4a);}
                    }
                    body.dark a.${c_line} .${c_tool} .${c_avatars} img{opacity:1;border-color:#3a3a3a}
                    body.dark a.${c_line} .${c_tool} span.${c_like},body.dark a.${c_line} .${c_tool} span.${c_close},body.dark a.${c_line} .${c_tool} .${c_note}{color: #4a4a4a!important;}
                    body.dark a.${c_line} .${c_tool} .${c_note} label,body.dark a.${c_line} .${c_tool} .${c_note} input{color: lightgray!important;}
                    body.dark a.${c_line} .${c_tool} .${c_toolIn}{color: lightgray;border-color: #4a4a4a;background: -webkit-linear-gradient(90deg, #3a3a3a 0, #4a4a4a);background: linear-gradient(0deg, #3a3a3a 0, #4a4a4a);}
                `;
                if(s_lineKeepTop) style.textContent += `a.${c_line} .${c_tool}{padding:10px 0 50px;opacity:1;z-index:1;}a.${c_line}.${c_done} .${c_tool} .${c_note}{margin:0 0 10px 10px;}`;
                document.head.appendChild(style);
                // fetch data.
                fetch("", {
                    'fetch': 1,
                    'sse': 1,
                }, (res)=> {
                    console.log('load marker from remote', res);
                    // user identification.. (MUST before output all keys for the first-time user-mid gets)
                    let _outputMarkers = ()=> {
                            // !!!BUG: stream 流多次执行 _outputMarkers() 无法即时更新 data 数据??
                            const _d_mid = d_mid ? d_mid : marker.data.user.mid;
                            // const _d_list = d_list ? d_list : marker.data.list;
                            const localMarks = Object.keys(d_list);
                            const {code, msg = 'no messages.'} = res;
                            if(code && code!==200) {
                                console.log('Abort on _outputMarkers:', msg);
                                if(localMarks.length > 0) {
                                    // clear all local-data
                                    localMarks.forEach(mark=> {
                                        console.log(`a non-updated local-marker(${mark}: ${getCookie(mark)}) was found, deleting.. (this mark should not be exists, perhaps caused by deletion failure)`);
                                        delCookie(mark, d_path);
                                    });
                                }
                                return;
                            }
                            let _eachMarks = (mark, user)=> {
                                let {nick, text, date, uid, rid, note} = mark,
                                    isOtherUserMark = user !== _d_mid;
                                if (!rid || !uid) {
                                    console.warn(`wrong rid|uid`, mark);
                                    return;
                                }
                                // URIError: URI malformed
                                try {
                                    if (nick) nick = decodeURIComponent(nick);
                                    if (text) text = decodeURIComponent(text);
                                    if (note) note = decodeURIComponent(note);
                                } catch(err) {
                                    console.log(err);
                                }
                                // console.log(user, mark);
                                let frag_mark = marks.cloneNode(true),
                                    frag_tool = tools.cloneNode(true), 
                                    tool_inside = finder(frag_tool, c_toolIn, 1),
                                    tool_mark = finder(frag_tool, c_mark, 1),
                                    tool_note = finder(frag_tool, c_note, 1),
                                    mark_indexes = uid.match('(\\d+)-(\\d+)'),
                                    mark_index = mark_indexes[1] > e_effectsArea.children.length-1 ? 0 : mark_indexes[1],
                                    mark_paragraph = e_effectsArea.children[mark_index];
                                // remove close button if marker does not belongs
                                if(isOtherUserMark) {
                                    const close_btn = finder(frag_tool, c_close, 1);
                                    close_btn.remove(); //if(valider(close_btn)) 
                                }
                                // traversal context nodes
                                if(!mark_paragraph.textContent.includes(text)){
                                    console.log(`mark_uid(${mark_index}) is diff with mark_paragraph record(perhaps content changed), traversal nodes on..`, e_effectsArea);
                                    const effectChildNodes = e_effectsArea.children;
                                    for (let i=0;i<effectChildNodes.length;i++) {
                                        if(effectChildNodes[i].textContent.includes(text)) {
                                            mark_index = i;
                                            break;
                                        }
                                    }
                                    mark_paragraph = effectChildNodes[mark_index];
                                    console.log(`traversal done. found(indexOf ${text}) on mark_uid:`, mark_index);
                                }
                                // load users avatar
                                const tool_avatars = finder(tool_inside, c_avatars, 1), // update append-avatars dom
                                      tool_avatar = new Image();
                                tool_avatar.alt = nick;
                                tool_avatar.src = `${s_avatar}avatar/${user}?d=mp&s=100&v=1.3.10`;
                                tool_avatars.appendChild(tool_avatar);
                                let multUserMarkContext = ` ${s_ctxMarked}`,
                                    likes = mark.like;
                                if(isObject(likes)) {
                                    console.warn(`confused(mixed) index of likes on mark#${rid}(should be typeof array)`, likes);
                                    likes = Object.values(likes); // 重新索引数组（避免后端 like 数组索引混乱
                                }
                                if(likes && likes.length>=1) {
                                    const multUserMarkExtra = likes.length>s_likeMax ? likes.length - s_likeMax : "";
                                    multUserMarkContext = ` 等${multUserMarkExtra}人${s_ctxMarked}`;
                                    const avatar_fragment = document.createDocumentFragment();
                                    for(let i=0;i<likes.length;i++) {
                                        if(i>=s_likeMax) break;
                                        const temp_avatar = new Image();
                                        temp_avatar.id = temp_avatar.alt = likes[i];
                                        temp_avatar.src = `${s_avatar}avatar/${likes[i]}?d=mp&s=100&v=1.3.10`;
                                        avatar_fragment.appendChild(temp_avatar);
                                    }
                                    tool_avatars.appendChild(avatar_fragment);
                                }
                                frag_mark.classList.add(c_done);
                                frag_mark.textContent = text;
                                frag_mark.dataset.uid = uid;
                                frag_mark.dataset.rid = rid;
                                frag_mark.title = `${nick} marked at ${date}`;
                                tool_mark.className = `${c_mark} ${c_disabled}`;
                                let markedContext = nick + multUserMarkContext;
                                if(note&&note.length >=1) {
                                    tool_mark.nextElementSibling.remove(); // "|"
                                    finder(tool_note, "", 1, "label").textContent = note;
                                }else{
                                    tool_note.previousElementSibling.remove(); // "|"
                                    tool_note.remove();
                                }
                                // additional like button ~~only if not(noted) others mark~~
                                if(isOtherUserMark) { // && isMarkerAccessable()
                                    const tool_like = document.createElement('SPAN');
                                    tool_like.className = c_like;
                                    tool_like.dataset.liked = '';
                                    tool_like.textContent = s_ctxLike;
                                    tool_like.title = `认同${s_ctxLike}👍`;
                                    if (likes && likes.includes(_d_mid)) {
                                        tool_like.dataset.liked = 1;
                                        tool_like.textContent = s_ctxLiked;
                                        tool_like.classList.add(c_liked); //c_disabled
                                        tool_like.title = `认同${s_ctxLiked}👎`;
                                    }
                                    tool_inside.appendChild(tool_like);
                                }
                                tool_mark.textContent = markedContext;
                                frag_mark.appendChild(frag_tool);
                                // write in
                                const specific_chars = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                                mark_paragraph.innerHTML = mark_paragraph.innerHTML.replace(specific_chars, frag_mark.outerHTML);
                            };
                            // 输出 所有用户标记
                            if(s_dataStream) {
                                // res.forEach(mark=> {
                                //     _eachMarks(mark, res.eventId);
                                // });
                                const user = res.eventId;
                                // console.warn(d_mid, marker.data.user);
                                if(_d_mid === user) {
                                    let local_counts = marker.data._counts + 1;
                                    _conf.static.dataCount = local_counts; //s_dataCount
                                    marker.data = {counts: local_counts}; //s_dataCount
                                    // Object.freeze(_conf.static); // 无法每次请求后冻结 _conf 对象 static 成员
                                }
                                _eachMarks(res, user);
                            }else{
                                Object.keys(res).forEach(user=> {
                                    let userMarks = Object.values(res[user]); // 重新索引数组对象（避免后端 mark_data 数组对象索引混乱
                                    // console.log(userMarks)
                                    if(!userMarks || userMarks==null) return;
                                    // compare curUserMid is curUser, then update currentUserCounts from remote
                                    if(_d_mid === user) {
                                        let remote_counts = userMarks.length;
                                        _conf.static.dataCount = remote_counts; //s_dataCount
                                        marker.data = {counts: remote_counts}; //s_dataCount
                                        // 冻结 _conf 对象 static 成员 for dataCount edit limits
                                        Object.freeze(_conf.static);
                                    }
                                    userMarks.forEach(mark=> {
                                        _eachMarks(mark, user);
                                    });
                                });
                            }
                            // 校验 当前用户标记
                            let curUserMarks = s_dataStream ? res : res[_d_mid],
                                isMarkerStream  = s_dataStream && isObject(curUserMarks),
                                isMarkerStreamUser = curUserMarks.eventId === _d_mid;
                            if(!curUserMarks) return;
                            if(!s_dataStream) curUserMarks = Object.values(curUserMarks); // 重新索引数组对象（避免手动删除 mark_data 索引混乱
                            // 返回本地记录中不存在于远程记录的元素（始终检验）
                            if(localMarks.length > 0) {
                                let existNonDeletedMarks = [];
                                if(isMarkerStream) {
                                    // match curUserMarks that exists on localMarks
                                    const localNotInRemote = !localMarks.some(local => {
                                        return local === s_dataPrefix + curUserMarks.rid;
                                    });
                                    if(localNotInRemote && isMarkerStreamUser) {
                                        existNonDeletedMarks.push(s_dataPrefix + curUserMarks.rid);
                                    }
                                }else{
                                    existNonDeletedMarks = localMarks.filter(local => {
                                        // localNotInRemote: delete local marks which is non-existent from remote
                                        const localNotInRemote = !curUserMarks.some(remote => {
                                                  return local === s_dataPrefix + remote.rid;
                                              });
                                        return localNotInRemote;
                                    });
                                }
                                if(existNonDeletedMarks.length > 0) {
                                    console.warn('existNonDeletedMarks', existNonDeletedMarks);
                                    existNonDeletedMarks.forEach(mark=> {
                                        console.log(`a local marker was found on non-existent remoteMarks(perhaps server delays), del cookie(${mark}: ${getCookie(mark)}) from local..`, '(existNonDeletedMarks: slow-down the frequency!)');
                                        // update(del) local-record
                                        delCookie(mark, d_path); // no need for dom changes
                                    });
                                }else{
                                    console.debug('remoteMarks: ALL MATCHED');
                                }
                            }
                            // 对比返回的远程用户标记与本地记录（仅存在记录检查）
                            // 返回数据（已响应）——>对比本地记录（未匹配到本地记录）——>新增本地记录
                            let existNonUpdatedMarks = [];
                            if(isMarkerStream) {
                                // remoteNotInLocal: push curUserMarks which is non-existent from local
                                const remoteNotInLocal = !localMarks.some(local => {
                                          return s_dataPrefix + curUserMarks.rid === local;
                                      });
                                if(remoteNotInLocal && isMarkerStreamUser) {
                                    existNonUpdatedMarks.push(curUserMarks);
                                }
                            }else if(Array.isArray(curUserMarks) && curUserMarks.length > 0){
                                existNonUpdatedMarks = curUserMarks.filter(remote => {
                                    // remoteNotInLocal: delete remote marks which is non-existent from local
                                    const remoteNotInLocal = !localMarks.some(local => {
                                              return s_dataPrefix + remote.rid === local;
                                          });
                                    return remoteNotInLocal;
                                });
                            }
                            if(existNonUpdatedMarks.length > 0) {
                                console.warn('existNonUpdatedMarks', existNonUpdatedMarks);
                                const _d_caches = d_caches ? d_caches : marker.data._caches;
                                existNonUpdatedMarks.forEach(marks=> {
                                    const mark_rid = marks.rid,
                                          mark_cname = s_dataPrefix + mark_rid,
                                          cached_ts = JSON.parse(_d_caches)[mark_cname];
                                    // update localMarks only if localStorage exists(incase of any other user device get involved)
                                    if(cached_ts) {
                                        console.log(`a remote marker(${mark_cname}: ${cached_ts}) was found on non-existent localMarks(perhaps server delays), add cookie to local..`, '(existNonUpdatedMarks: slow-down the frequency!)');
                                        // update(add) local-data instantly
                                        setCookie(mark_cname, cached_ts, d_path, s_dataAlive); // dom changes(no longer needed)
                                        // marker.data = {counts: remote_counts}; //s_dataCount
                                    }else{
                                        console.log(`marker(${mark_rid}) belongs to another device(not found in localStorage)`);
                                    }
                                });
                            }else{
                                console.debug('localMarks: ALL MATCHED');
                            }
                        },
                        _md5update = (callback)=> {
                            let userinfo = {
                                    nick: e_userNick.value,
                                    mail: e_userMail.value,
                                },
                                _execUpdate = (userinfo, cbk)=> {
                                    userinfo.mid = userinfo.mail ? md5(userinfo.mail) : "";
                			        // store userinfo(d_mid for currentUserCounts verification
            			            marker.data = userinfo;
                                    // store to local cookies
                                    setCookie(s_userNick, userinfo.nick);
                                    setCookie(s_userMail, userinfo.mail);
                                    setCookie(s_userMid, userinfo.mid);
                                    if(funValidator(cbk)) cbk();
                                };
                            if(typeof md5 === 'undefined') {
                                console.log('init md5..');
                                dynamicLoad(s_md5Url, ()=>_execUpdate(userinfo, callback));
                            }else{
                                console.log('md5 initiated, updating records..');
                                _execUpdate(userinfo, callback);
                            }
                        };
                    // re-update on userinfo->mail changed.
                    if(isMarkerUserUpdate()) {
                        _md5update(_outputMarkers);
                        console.log(`marker user updated: ${e_userMail.value} (counts: ${d_counts})`);
                    }else{
                        // abort on userinfo exists
                        if(!isMarkerAccessable() && e_userMail.value){
                            _md5update(_outputMarkers);
                            console.log(`marker user inited. (counts: ${d_counts})`);
                        }else{
                            _outputMarkers();
                            console.debug('default _outputMarkers');
                        }
                    }
                }, (err)=>console.warn(err));
            },
        },
        _utils: {
            _event: {
                get: (event)=> {
                    return event ? event : window.event;
                },
                add: function(element=null, type='', handler=false, cb=false) {
                    let {_utils: {_event: {add:addEvent}, _etc: {assert}, _dom: {valider}}} = marker,
                        init_func = function(element=null, type='', handler=false, callback=false){
                            if(!type) return;
                            assert(handler && typeof handler==='function', 'addEvent callback err.');
                            callback();
                            console.debug(type, 'event loaded.');
                        }; // _that = this&&this.add ? this : marker._utils._event;
                    try {
                        if (!valider(element)) throw new Error('invalid element provided', valider);
                        if(element.addEventListener){
                            addEvent = function(element=null, type='', handler=false, cb=false){
                                init_func(element, type, handler, ()=>{
                                    element.addEventListener(type, handler, cb);
                                });
                            };
                        }else if(element.attachEvent){
                            addEvent = function(element=null, type='', handler=false){
                                init_func(element, type, handler, ()=>{
                                    element.attachEvent('on'+type, handler);
                                });
                            };
                        }else{
                            addEvent = function(element=null, type='', handler=false){
                                init_func(element, type, handler, ()=>{
                                    element['on'+type] = handler;
                                });
                            };
                        }
                        addEvent(element, type, handler, cb);
                    } catch (error) {
                        console.warn(error);
                    }
                },
                getTarget: (event)=> {
                    return event.target || window.srcElement;
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
                    var cval = marker._utils._cookie.get(name); // that = this&&this.get ? this : marker._utils._cookie;
                    if(cval!=null){
                        document.cookie = name+"="+cval+";expires="+exp.toGMTString()+";path="+path;
                    }
                },
            },
            _storage: {
                set: (name="", data={})=> {
                    try {
                        if(marker._utils._etc.isObject(data)) data = JSON.stringify(data);
                        localStorage.setItem(name, data);
                    } catch (e) {
                        console.warn(e);
                        return null;
                    }
                },
                get: (name="", expires=0)=> {
                    try {
                        if(isNaN(expires) || typeof expires !== 'number') throw new Error('maxAge must be number of millseconds!');
                        const ts = localStorage.getItem(name),
                              ms = expires ? expires : parseInt(marker.init._conf.static.dataAlive*24*60*60);
                        if(parseInt(ts)+ms < Date.now()) {
                            localStorage.removeItem(name);
                            return null;
                        }
                        return ts;
                    } catch (e) {
                        console.warn(e);
                        return null;
                    }
                },
                _get: (name="")=> {
                    try {
                        return localStorage.getItem(name);
                    } catch (e) {
                        console.warn(e);
                        return null;
                    }
                },
            },
            _dom: {
                valider: (node, textNode=true) => {
                    const valid_node = node && node instanceof HTMLElement;
                    return textNode ? valid_node && node.nodeType===1 : valid_node;
                },
                indexer: (node=null)=> {
                    const valider = marker._utils._dom.valider;
                    if(!valider(node)) {
                        return 0;
                    }
                    const parentNode = node.parentElement;
                    return valider(parentNode) ? Array.prototype.indexOf.call(parentNode.children, node) : 0;
                },
                finder: (element=null, className='', mod=0, tagName="")=> {
                    const valider = marker._utils._dom.valider;
                    if(!valider(element)) return null;
                    if (mod === '' || typeof mod !== 'number') {
                        console.warn('invalid mod, must be value 0 or 1', mod);
                        return null;
                    }
                    switch (mod) {
                        case 1:
                            let childElements = tagName ? element.getElementsByTagName(tagName) : element.getElementsByClassName(className);
                            return childElements.length>0 ? childElements[0] : null;
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
                    const {_utils: {_event: {add:addEvent, get:getEvent, getTarget}, _dom: {valider}, _etc: {funValidator}}} = marker;
                    addEvent(parent, 'click', function(e) {
                        let event = getEvent(e),
                            target = getTarget(event);
                        target = tag ? target.closest(tag) : target.closest('.' + cls);
                        if (!valider(target)) return;
                        if(funValidator(callback)) {
                            callback(target, event);
                        }else{
                            console.log('invalid clicker callback', callback);
                        }
                    });
                },
            },
            _diy: {
                strGenerator: function(num=16, useCrypto=false) {
                    if(useCrypto && crypto && crypto instanceof Object){
                        const randomBytes = new Uint8Array(num);
                        crypto.getRandomValues(randomBytes);
                        return Array.from(randomBytes, byte => ('0' + byte.toString(16)).slice(-2)).join('');
                    }else{
                        const randomMix = Math.random() + parseFloat('0.' + Date.now()), // Math.random().toString(num)
                              randomStr = randomMix.toString(num);
                        return randomStr.substring(2, randomStr.length);
                    }
                },
                paramParser: function(obj, post=false, encode=true) {
                    if(post && marker._utils._etc.isObject(obj)) {
                        return obj;
                    }
                    let str = "";
                    for(let key in obj) {
                        let encoder = encode ? encodeURIComponent(obj[key]) : obj[key]; //decodeURIComponent
                        str += `${key}=${encoder}&`;
                    }
                    str = str.substr(0, str.lastIndexOf("&"));
                    return str;
                },
                ctxIndexer: (context, keyword)=> {
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
                    const script = document.createElement('SCRIPT');
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
                argsRewriter: function(_args={}, presets={}, callback=false) {
                    try {
                        const {_utils: {_etc: {assert, funValidator}}} = marker;
                        assert(Object.prototype.toString.call(_args)==='[object Object]', 'invalid arguments provided!');
                        // rewrite conf with merge on
                        const args_ = marker.init.prototype._singleton_conf._rewriter(_args, presets, true);
                        if(!funValidator(callback)){
                            return args_;
                        }
                        // callback returns
                        callback(args_);
                    } catch (error) {
                        console.log(error);
                    }
                },
                assert: function(conditions=true, message='', logType=false, report=false) {
                    if(conditions) return;
                    if(typeof logType==='string') {
                        marker._utils._etc.debugger(conditions, logType);
                    }
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
                            marker._utils._etc.assert(typeof console[type]==='function', 'invalid console type.');
                            console[type](msg);
                            break;
                    }
                },
            },
        },
        status: {
            isMarkerAvailable: (anonymous=false)=> {
                let valid_statu = true;
                if(!anonymous) {
                    const commentInfo = marker.init._conf.element.commentInfo,
                          userinfo = Object.entries(commentInfo);
                    for(let i=0;i<userinfo.length;i++){
                        let key = userinfo[i][0],
                            val = userinfo[i][1];
                        if(val==null) {
                            console.warn('Abort on '+key+': all commentInfo must be Specificed!', commentInfo);
                            valid_statu = false;
                        }else if(val.value=='') {
                            console.warn(key+' required to be FullFilled to use marker.', val);
                            valid_statu = false;
                        }
                    }
                }
                return valid_statu;
            },
            isMarkerAccessable: ()=> {
                const {mail, mid} = marker.data.user;
                return mail&&mail !== "" && mid&&mid !== "";
            },
            isMarkerUserUpdate: function() {
                const {init: {_conf: {element: {commentInfo: {userMail:e_userMail}}}}, data: {user: {mail:d_mail}}, status:{isMarkerAccessable}} = marker;
                const user_updated = decodeURIComponent(d_mail) !== e_userMail.value;
                return isMarkerAccessable() && user_updated;
            },
            isMarkerReachedMax: (server_verify = false)=> {
                const {init: {_conf: {static: {dataMax:s_dataMax, apiUrl:s_apiUrl}}}, data: {list:d_list, stat: {counts:d_counts_}, _counts:d_counts}, mods: {fetch}} = marker;
                const maxDataLength = parseInt(s_dataMax);
                // auto-exec server_verify if un-editable server-counts reached_max
                return new Promise((resolve, reject) => {
                    if(server_verify) { //dataCount >= maxDataLength
                        fetch(s_apiUrl, {
                            'fetch': 1,
                            'count': 1,
                        }, (res) => {
                            const {code, msg = 'no messages.'} = res;
                            if(code&&code==200) {
                                let res_counts = parseInt(msg),
                                    max_reached = res_counts >= maxDataLength;
                                if(max_reached) {
                                    marker.data = {counts: res_counts};
                                    console.log(`counts restored! the server responsed: ${res_counts} which is reached_max limits(${maxDataLength}).`);
                                }
                                resolve(max_reached);
                            }else{
                                reject(d_counts >= maxDataLength); //false
                                console.log('rejected of server_verify', res);
                            }
                        });
                    }else{
                        // localCompare might includes same-user markers from another device!
                        const reached_max = d_counts_ >= maxDataLength || Object.keys(d_list).length >= maxDataLength;
                        resolve(reached_max); // return reached_max;
                    }
                }).then(res=> {
                    return res;
                }).catch(err=> {
                    return err;
                });
            },
            isMarkerSelectable: (node = null)=> {
                const {init: {_conf: {class: {blackList:c_blackList}}}, _utils: {_dom: {valider, finder}}} = marker;
                if(!valider(node) || !node.classList) {
                    console.warn('invalid nodes or classList', node);
                    return false;
                }
                let blackTags = ['h1','h2','h3','h4','h5','h6','a','s','del','code','mark','details','summary', 'blockquote'],
                    blackList = c_blackList instanceof Array ? c_blackList : [];
                for(let i=0;i<blackTags.length;i++){
                    let blackTag = blackTags[i].toUpperCase();
                    if(node.tagName===blackTag || finder(node, '', 0, blackTag)) {
                        console.warn('unSelectable content detected! (node/parentNode tagName: '+blackTag+')');
                        return;
                    }
                }
                let notOnList = true,
                    blackLens = blackList.length;
                if(blackLens > 0) {
                    for(let i=0;i<blackLens;i++){
                        let blackClass = blackList[i];
                        if(node.classList.contains(blackClass) || finder(node, blackClass, 0)) {
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
                const {init: {_conf: {class: {tool:c_tool}}}, _utils: {_dom: {valider}}} = marker;
                const node_child = node.children;
                switch(true){
                    case !valider(node):
                        console.warn('invalid nodes wrapped.', node);
                        return false;
                        // break;
                    case node_child.length<=0:
                        console.debug('No childNodes wrapped in selections.', node_child);
                        return null;
                        // break;
                }
                let child_classes = node_child[0].classList;
                return child_classes&&child_classes.contains(c_tool);
            },
            isMultiSameChar: (paragraph, context, vars=false)=> {
                const uniqune_char = marker._utils._diy.ctxIndexer(paragraph, context);
                return vars ? uniqune_char : uniqune_char.length > 1;
            },
            _adjustPending: (status=0, callback=false, delay=0)=> {
                const res_status = {pending: status};
                if(callback){
                    const {init: {_conf: {static: {dataDelay:s_dataDelay}}}, _utils: {_etc: {funValidator}}} = marker;
                    // delay must under callback(always true on the outside)
                    delay = delay ? delay : s_dataDelay;
                    let timer = setTimeout(() => {
                            marker.data = res_status; // adjusting pending statu.
                            if(funValidator(callback)) {
                                callback();
                            }
                            clearTimeout(timer); // 在回调函数执行后清除 setTimeout
                        }, delay);
                }else{
                    marker.data = res_status; // adjusting pending statu.
                }
            },
        },
        mods: {
            mark: function(e){
                const {init: {_conf: {static: {dataMin:s_dataMin, ctxMark:s_ctxMark, ctxMarkMax:s_ctxMarkMax}, class: {line:c_line, mark:c_mark, disabled:c_disabled}, element: {line:e_line, tool:e_tool, effectsArea:e_effectsArea}}}, _utils: {_event: {get:getEvent, getTarget}, _dom: {finder}, _diy: {strGenerator}}, status: {isMarkerSelectable, isMarkerReachedMax, isNodeMarkAble, isNodeMarkDone}, mods: {close}} = marker;
                e = getEvent(e);
                e.preventDefault();
                let that = this.toString ? this : window.getSelection;
                const selectedText = that.toString(),
                      selectedLen = selectedText.length,
                      selectedMin = parseInt(s_dataMin);
                if (selectedLen < selectedMin || that.isCollapsed) {
                    console.debug(`Abort on context min-length(required ${selectedMin}+), selectedText length: ${selectedLen}`);
                    return;
                }
                try {
                    const range = that.getRangeAt(0),
                          anchor_parent = that.anchorNode.parentElement,
                          focus_parent = that.focusNode.parentElement;
                    /*** close switch for wrap-selects ***/
                    let contains_node = null; //anchor_parent || focus_parent
                    switch(true) {
                        case anchor_parent != range.commonAncestorContainer:
                            contains_node = anchor_parent;
                            break;
                        case focus_parent != range.commonAncestorContainer:
                            contains_node = focus_parent;
                            break;
                    }
                    if(!isMarkerSelectable(contains_node)) return;
                    /*** close switch for wrap-selects ***/
                    if(isNodeMarkAble(contains_node) && isNodeMarkDone(contains_node)) {
                        console.warn('selection contains marked-parent content, canceling..', contains_node);
                        return;
                    }
                    let marks = e_line.cloneNode(true),
                        tools = e_tool;
                    marks.dataset.rid = strGenerator();
                    range.surroundContents(marks);
                    // check marker is selectable
                    isMarkerReachedMax().then(res=> {
                        const tool_mark = finder(tools, c_mark, 1),
                              tool_disabled = tool_mark.classList.contains(c_disabled);
                        if(res) {
                            // rewrite stored tools context only if tool_mark on enabled statu.(decreasing origin_mark dom edit)
                            if(!tool_disabled) {
                                tool_mark.classList.add(c_disabled);
                                tool_mark.textContent = s_ctxMarkMax;
                            }
                        }else{
                            if(tool_disabled) {
                                tool_mark.classList.remove(c_disabled);
                                tool_mark.textContent = s_ctxMark;
                            }
                        }
                    }).catch(err=>console.warn(err));
                    tools = tools.cloneNode(true);
                    marks.appendChild(tools);
                    that.removeRange(range); //that.removeAllRanges();
                    // close mark it-self if selections under markable-parent
                    const marks_parents = finder(marks, c_line);
                    if(marks_parents != null){
                        console.warn('markable-parent (deep-level) exists, unwrapping self marks', marks);
                        close(marks);  // close(marks_parents);
                        return;
                    }
                    const marks_children = marks.querySelectorAll(`.${c_line}`); // marks.children;
                    if(marks_children.length <= 0) return;
                    // console.log(marks_children);
                    marks_children.forEach((each_line)=>{
                        let dynamic_line = e_effectsArea.querySelector(`[data-rid="${each_line.dataset.rid}"]`),
                            line_child = finder(each_line, c_line, 1);
                        // close inside wrapped child
                        if(line_child && line_child.length >= 1){
                            line_child = line_child[0];
                            console.warn('markable-child (deep level) exists, unwrapping line_child', line_child);
                            close(line_child);
                            return;
                        }
                        // close inside wrapped parent
                        if(isNodeMarkDone(each_line)){
                            let line_parent = finder(each_line, c_line);
                            if(line_parent != null){
                                console.warn('selection contains marked-parents content, unwrapping line_parent..', line_parent);
                                close(line_parent);
                            }else{
                                let dynamic_parent = finder(dynamic_line, c_line);
                                console.warn('selection contains marked-children content, unwrapping dynamic_parent..', dynamic_parent);
                                close(dynamic_parent); // reject dynamic marks
                                // close(dynamic_line); // ckear-all-children
                            }
                            return;
                        }
                        // USE dynamic_line insted of each_line for close(null) confused innerHTML structure issue.
                        console.log('markable-child wrap exists, unwrapping dynamic_line..', dynamic_line);
                        close(dynamic_line);
                    });
                } catch (error) {
                    console.warn(error);
                }
            },
            down: function(node, verify_updates=true) {  // verify_updates means standard mark-down(no like-down)
                const {init: {_conf: {static: {ctxMarking:s_ctxMarking, ctxMarked:s_ctxMarked, ctxMarkMax:s_ctxMarkMax, ctxLike:s_ctxLike, ctxLiked:s_ctxLiked, avatar:s_avatar}, class: {line:c_line, done:c_done, note:c_note, like:c_like,liked:c_liked, disabled:c_disabled, avatars:c_avatars}, element: {effectsArea:e_effectsArea}}}, data: {stat: {pending:d_pending}, user: {nick:d_nick, mid:d_mid}}, _utils: {_dom: {finder, valider, indexer}}, status: {isNodeMarkDone, isMultiSameChar, isMarkerReachedMax}, mods: {update}} = marker;
                if(d_pending) {
                    console.warn('Abort on too-fast marking off! (wait a second then try to re-mark again.)');
                    return;
                }
                if(!valider(node)) {
                    console.warn('invalid node.', node);
                    return;
                }
                node.textContent = s_ctxMarking;
                const mark_node = finder(node, c_line);
                // additional check for verify ingore(like action)
                if(isNodeMarkDone(mark_node) && verify_updates) {
                    alert('Abort on marked-done node!');
                    node.textContent = s_ctxMarked;
                    mark_node.classList.add(c_disabled);
                    return;
                }
                // loop on mark_nodes
                let mark_paragraph = mark_node;
                while(mark_paragraph.parentElement != e_effectsArea){
                    mark_paragraph = mark_paragraph.parentElement;
                }
                // check on same-chars
                let paragraph_context = mark_paragraph.textContent,
                    mark_text = mark_node.firstChild.nodeValue;
                if(isMultiSameChar(paragraph_context, mark_text)){
                    alert('Abort on multi Same-chars on current paragraph!' + isMultiSameChar(paragraph_context, mark_text, true));
                    return;
                }
                // compare local-counts(read only) for decreasing server_verify requests. (bug: read-only variables can not be updated instantly, always use server_verify)
                const checkServerReachedMax = isMarkerReachedMax(verify_updates); // server verification only if verify_updates: true
                checkServerReachedMax.then(res=> {
                    if(res && verify_updates) {
                        alert('Abort on reaching(server side) dataMax!');
                        node.textContent = s_ctxMarkMax;
                        node.classList.add(c_disabled);
                        // close(node);
                        return;
                    }
                    // update to remote.
                    const mark_rid = mark_node.dataset.rid,
                          mark_indexes = indexer(mark_paragraph) + '-' + paragraph_context.indexOf(mark_text),
                          mark_note = finder(mark_node, c_note, 1),
                          mark_input = finder(mark_note, "", 1, "input"),
                          mark_inputs = valider(mark_input) ? mark_input.value : "";
                    let disliked = node.dataset.liked,
                        updateObjs = {
                            'rid': mark_rid,
                            'uid': mark_indexes,
                            'text': mark_text,
                            'like': verify_updates ? 0 : d_mid,
                            'liked': verify_updates ? 0 : disliked,
                            'note': mark_inputs,
                            'node': node,
                        };
                    // updateObjs[node.dataset.action] = verify_updates ? 0 : d_mid;
                    update(updateObjs, (result)=> {
                        // local updates (dom changes)
                        mark_node.classList.add(c_done);
                        mark_node.dataset.uid = mark_indexes;
                        // mark "done"
                        const user_avatars = finder(mark_node, c_avatars, 1),
                              user_avatar = new Image();
                        if (disliked) {
                            document.getElementById(d_mid).remove();
                        } else {
                            user_avatar.id = d_mid;
                            user_avatar.alt = d_nick;
                            user_avatar.src = `${s_avatar}avatar/${d_mid}?d=mp&s=100&v=1.3.10`; // user_avatars.style.marginLeft = '-2px';
                            user_avatars.appendChild(user_avatar);
                        }
                        let markedContext = `${d_nick} ${s_ctxMarked}`;
                        if(mark_note) {
                            if (verify_updates) mark_note.nextElementSibling.remove(); // "|"
                            if (valider(mark_input) && mark_inputs.length>=1) {
                                finder(mark_note, "", 1, "label").textContent = mark_inputs;
                                mark_input.remove();
                                // markedContext = d_nick;
                            } else {
                                if (verify_updates) mark_note.remove();
                            }
                        }
                        // const isLikedUserMark = result.like && result.like.includes(d_mid); //marker.data.user.mid
                        if (verify_updates) {
                            console.log(markedContext)
                            node.className = c_disabled;
                            node.textContent = markedContext;
                        } else {
                            node.dataset.liked = disliked ? '' : 1;
                            node.className = disliked ? `${c_like}` : `${c_like} ${c_liked}`;
                            node.textContent = disliked ? s_ctxLike : s_ctxLiked; //s_ctxLike
                        }
                    });
                }).catch(err=>console.warn(err));
            },
            note: function(node) {
                const {init: {_conf: {static: {ctxNote:s_ctxNote, ctxNoted:s_ctxNoted}, class: {line:c_line}}}, _utils: {_dom: {valider, finder}}, status: {isNodeMarkDone}} = marker;
                if(!valider(node)) {
                    return node;
                }
                const mark_node = finder(node, c_line),
                      input_box = finder(mark_node, "", 1, "input");
                if(!valider(input_box)  || isNodeMarkDone(mark_node)){
                    return;
                }
                input_box.focus();
                if(input_box.oninput) {
                    console.debug('on-input has registered!');
                    return;
                }
                input_box.oninput = input_box.onpropertychange = function() {
                    finder(mark_node, "", 1, "label").textContent = this.value.length>=1 ? s_ctxNoted : s_ctxNote;
                };
            },
            quote: function(node) {
                const {init: {_conf: {static: {ctxQuote:s_ctxQuote, ctxQuoted:s_ctxQuoted}, class: {line:c_line, disabled:c_disabled}, element: {commentArea:e_commentArea}}}, _utils: {_dom: {valider, finder}}, status: {isNodeMarkDone}, mods: {close}} = marker;
                if(!valider(node)) {
                    return node;
                }
                const mark_node = finder(node, c_line),
                      comment_box = e_commentArea;
                if(!comment_box) {
                    console.warn('Quote abort on invalid commentArea!', comment_box);
                    return;
                }
                comment_box.value = `\n> ${mark_node.firstChild.nodeValue} ...`;
                comment_box.setSelectionRange(0,0);
                comment_box.focus();
                if(!isNodeMarkDone(mark_node)){
                    close(mark_node);
                    return;
                }
                node.classList.add(c_disabled);
                node.textContent = s_ctxQuoted;
                let timer = setTimeout(()=>{
                        node.classList.remove(c_disabled);
                        node.textContent = s_ctxQuote;
                        clearTimeout(timer);
                    }, 1500);
            },
            copy: function(node) {
                const {init: {_conf: {static: {ctxCopy:s_ctxCopy, ctxCopied:s_ctxCopied}, class: {line:c_line, disabled:c_disabled}}}, _utils: {_dom: {valider, finder}}, status: {isNodeMarkDone}, mods: {close}} = marker;
                if(!valider(node)) {
                    return node;
                }
                const range = document.createRange(),
                      selection = window.getSelection(),
                      mark_node = finder(node, c_line);
                range.selectNodeContents(mark_node.firstChild);
                selection.removeAllRanges();
                selection.addRange(range);
                //exec copy..
                document.execCommand('copy');
                selection.removeAllRanges();
                if(!isNodeMarkDone(mark_node)) {
                    node.textContent = s_ctxCopied;
                    // close(mark_node);
                    return;
                }
                node.classList.add(c_disabled);
                node.textContent = s_ctxCopied;
                let timer = setTimeout(()=>{
                        node.classList.remove(c_disabled);
                        node.textContent = s_ctxCopy;
                        clearTimeout(timer);
                    }, 1500);
            },
            close: function(node, execUpdate=false) {
                const {init: {_conf: {class: {line:c_line, tool:c_tool, processing:c_processing}}}, _utils: {_dom: {valider, finder}}, status: {isNodeMarkAble, isMarkerAccessable, isNodeTextOnly, isNodeMarkDone}, mods: {update}} = marker;
                if(!valider(node)) return node;
                // 执行 close() 操作后将打乱标记点父级（bug：无法再次找到已定义的子级元素，已通过动态选择each_line解决）
                const mark_node = isNodeMarkAble(node) ? node : finder(node, c_line);
                // deletion auth.
                if(!isMarkerAccessable()){
                    alert('marker deletion failure, anonymous not allowed..');
                    return;
                }
                let update_dom = ()=> {
                    let mark_tools = mark_node.querySelectorAll(`.${c_tool}`); //finder(mark_node, c_tool, 1);
                    if(mark_tools.length >= 1) mark_tools[mark_tools.length-1].remove();
                    let replace_content = isNodeTextOnly(mark_node) ? mark_node.firstChild.textContent : mark_node.innerHTML;
                    if(!mark_node.parentElement) {
                        console.log('mark parent NOT found while closing', mark_node);
                        return;
                    }
                    mark_node.parentElement.innerHTML = mark_node.parentElement.innerHTML.replace(mark_node.outerHTML, replace_content);
                };
                if(execUpdate && isNodeMarkDone(mark_node)) {
                    const {rid, uid} = mark_node.dataset;
                    if(confirm('deleting rid#' + rid + '?')) {
                        mark_node.classList.add(c_processing);
                        update({
                            rid: rid,
                            uid: uid,
                            node: mark_node,
                            cls: c_processing,
                        }, (res)=> update_dom(), true);
                    }else{
                        mark_node.classList.remove(c_processing);
                    }
                    return;
                }
                update_dom();
            },
            update: function(updObj={}, cbk=false, del=false) {
                const {init: {_conf: {static: {apiUrl:s_apiUrl, dataPrefix:s_dataPrefix, dataCaches:s_dataCaches, dataAlive:s_dataAlive, ctxMarked:s_ctxMarked}, class: {note:c_note}}}, data: {list:d_list, path:d_path}, _utils: {_cookie: {set:setCookie, del:delCookie}, _storage: {set:setStorage, get:getStorage}, _etc: {isObject, funValidator}, _dom: {finder}}, status: {_adjustPending}, mods: {fetch}} = marker;
                // changes required
                let {counts:d_counts} = marker.data.stat;
                if(!isObject(updObj) || Object.keys(updObj).length<1) {
                    console.warn('remote updates failed, invalid updateObject.', updObj);
                    return;
                }
                const {node, text, note, like, liked, rid, uid, cls, ts} = updObj,
                      mark_cname = s_dataPrefix + rid;
                // start pending(exec immediately without callback)..
                _adjustPending(1);
                // deletion load ts from local
                if(del) {
                    const stored_ts = d_list[mark_cname];
                    // update currentUserCounts Immediately no mater backend-saved or not. (add/del dual check supported)
                    if(d_counts>0) marker.data = {counts: d_counts - 1}; // decrease counts only if user already exists marker
                    fetch(s_apiUrl, {
                        'del': 1,
                        'rid': rid,
                        'ts': stored_ts, //ts ? ts : stored_ts,
                    }, (res)=> {
                        const {code, msg = 'no messages.'} = res;
                        if(code && code!=200){
                            alert(`${msg}（err#${code}）`);
                            if(code===403) console.warn("非法行为！！随意删除他人标记是不被允许的哦..");
                            if(node&&node.classList) node.classList.remove(cls);
                            marker.data = {counts: d_counts}; // restore counts on error
                            _adjustPending(0);  // pending abort..
                            return;
                        }
                        // update(del) cookies Immediately(dual-check insurance)
                        delCookie(mark_cname, d_path); // local updates
                        console.log(`${mark_cname} deleted(ts: ${stored_ts}) `, msg);
                        // pending stop..
                        _adjustPending(0, ()=> {
                            funValidator(cbk) ? cbk(res) : console.log('update(del) succesed(no calls)', msg);
                        });
                    });
                    return;
                }
                // addition load ts via real-time
                const realtime_ts = Date.now();
                // update currentUserCounts Immediately no mater backend-saved or not. (add/del dual check supported)
                marker.data = {counts: d_counts + 1};  // increase counts 
                // exec backend-dom updates
                fetch(s_apiUrl, {
                    'rid': rid,
                    'uid': uid,
                    "text": encodeURIComponent(text),
                    "note": encodeURIComponent(note),
                    "like": like,
                    "liked": liked,
                    'ts': realtime_ts,
                }, (res)=> {
                    const {code, msg = 'no messages.'} = res;
                    if(code && code!=200){
                        alert(`${msg}（err#${code}）`);
                        if(node) node.textContent = s_ctxMarked;
                        marker.data = {counts: d_counts}; // restore counts on error
                        _adjustPending(0);  // pending abort..
                        return;
                    }
                    if(like) {
                        console.log(msg);
                    }else{
                        try {
                            let ts_caches = getStorage(s_dataCaches, s_dataAlive);
                            ts_caches = ts_caches ? JSON.parse(ts_caches) : {};
                            ts_caches[mark_cname] = realtime_ts;
                            // record of localStorage(ts caches for del)
                            setStorage(s_dataCaches, JSON.stringify(ts_caches));
                            // update(add) cookies Immediately(dual-check insurance)
                            setCookie(mark_cname, realtime_ts, d_path, s_dataAlive);
                            console.log(`${mark_cname} updated(ts: ${realtime_ts}) `, msg);
                        } catch (e) {
                            console.warn(e);
                        }
                    }
                    _adjustPending(0, ()=> {
                        funValidator(cbk) ? cbk(res) : console.log('update(add) succesed(no calls)', msg);
                    });
                });
            },
            fetch: (url='', _obj={}, cbk=false, cbks=false)=> {
                const {init: {_conf: {static: {postId:s_postId, apiUrl:s_apiUrl, dataStream:s_dataStream}}}, data: {user: {nick:d_nick, mail:d_mail}, stat: {promised: d_promised}}, _utils: {_etc: {argsRewriter, funValidator}, _diy: {paramParser}}} = marker;
                argsRewriter.call(marker, _obj, {
                    'fetch': 0,
                    'count': 0,
                    'sse': 0,
                    'del': 0,
                    'ts': 0,
                    "nick": encodeURIComponent(d_nick),
                    "mail": decodeURIComponent(d_mail),
                    'pid': s_postId,
                }, (obj_)=> {
                    const params = '&'+paramParser(obj_);
                    url = url ? url : s_apiUrl;
                    if(url.indexOf('?') == -1) url = url + '?';
                    url = url + params;
                    // 检查 promise 缓存
                    let requestKey = JSON.stringify([url]),
                        requestFun = ()=> {
                            if (d_promised[requestKey]) {
                                console.log('Multi Same-fetch detected! Standby Promise..', d_promised[requestKey]);
                                return;
                            }
                            const fetchPromise = fetch(url, {
                                // method: type,
                                // data: JSON.stringify(data),
                                keepalive: true,
                            }).then(response => {
                                if(!response.ok) throw new Error('Network err');
                                return response.json();
                            }).then(data=> {
                                if(funValidator(cbk)) cbk(data);
                            }).catch(error=> {
                                if(funValidator(cbks)) cbks(error);
                            }).finally(() => {
                                // 删除 promise 缓存
                                delete d_promised[requestKey];
                            });
                            // 更新 promise 缓存
                            d_promised[requestKey] = fetchPromise;
                            marker.data = {promised: d_promised};
                        };
                    // use server-sent event only if fetch on sse:true(default fetch)
                    if(s_dataStream && obj_.sse) {
                        if(typeof(EventSource) === "undefined") {
                            console.warn("您的浏览器不支持SSE");
                            requestFun();
                            return;
                        }
                        const eventSource = new EventSource(url);
                        eventSource.addEventListener('open', function (event) {
                            console.debug('SSE Open Connected.');
                        });
                        eventSource.addEventListener('message', function (event) {
                            // if (!event.data) {
                            //     console.log('invalid eventData', event.data);
                            //     return;
                            // }
                            try {
                                const eventData = JSON.parse(event.data);
                                if (eventData == null) throw new Error('invalid eventData');
                                eventData.eventId = event.lastEventId;
                                console.debug('Reciving SSE Data..', event);
                                if(funValidator(cbk)) cbk(eventData); // `${event.lastEventId}:{${event.data}}`
                            } catch(err) {
                                console.log(err);
                            }
                        });
                        eventSource.addEventListener('error', function (event) {
                            console.debug(`SSE Error Closed. (statu: ${event.target.readyState})`, event);
                            eventSource.close();
                            // if(funValidator(cbks)) cbks(event);
                        });
                        return;
                    }
                    requestFun();
                });
            },
        },
        __proto__: {
            init: function(user_conf = {}){
                try {
                    const that = Object.getPrototypeOf(this)!==marker.init.prototype ? marker.init.prototype : this;
                    // console.log('publicDefault', that._singleton_conf.publicDefault)
                    const _conf_res = that._singleton_conf._rewriter(user_conf, that._singleton_conf.publicDefault);
                    // console.log('_rewriter', _conf_res);
                    // console.log('_rewriters', that._singleton_conf._rewriters(user_conf, that._singleton_conf.publicDefault, true));
                    // 冻结 _conf、_conf.static 对象成员（）
                    Object.freeze(_conf_res);
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
                    const {init: {_conf: {static: {useNote:s_useNote, useCopy:s_useCopy, useQuote:s_useQuote}, class: {close:c_close, mark:c_mark, note:c_note, copy:c_copy, quote:c_quote, like:c_like,liked:c_liked}, element: {effectsArea:e_effectsArea, commentArea:e_commentArea}}}, _utils: {_closure: {debouncer}, _dom: {clicker}, _event: {add:addEvent}}, status: {isMarkerAvailable}, mods: {mark, down, note, copy, quote, close}} = marker; // _event
                    if(s_useNote) clicker(e_effectsArea, c_note, debouncer((t)=>note(t)));
                    if(s_useCopy) clicker(e_effectsArea, c_copy, debouncer((t)=>copy(t)));
                    if(s_useQuote) clicker(e_effectsArea, c_quote, debouncer((t)=>quote(t)));
                    if(!isMarkerAvailable()) {
                        // extra tips for un-registerd mark user
                        let tips4unregister = (t)=> {
                            t.classList.add(c_liked);
                            t.textContent = 'Comments Required!';
                            alert('Unregistered user, you must comment(fullfill name/email) before marking-off!');
                            e_commentArea.focus();
                        };
                        clicker(e_effectsArea, c_mark, debouncer((t)=>tips4unregister(t), 300));
                        clicker(e_effectsArea, c_like, debouncer((t)=>tips4unregister(t), 300));
                        clicker(e_effectsArea, c_close, debouncer((t)=>tips4unregister(t), 300));
                        throw new Error('marker unavailable, register init failed..');
                    }
                    // bind events
                    const pointerupEvents = debouncer(mark.bind(window.getSelection()), 100);
                    addEvent(e_effectsArea, 'pointerup', pointerupEvents); // addEvent this enviroument changed!!
                    // const contextmenu_ = (e)=> {
                    //     e.preventDefault();
                    //     pointerupEvents();
                    // };
                    // addEvent(e_effectsArea, 'contextmenu', contextmenu_);  // moblie events
                    // addEvent(e_effectsArea, 'select', contextmenu_);  // moblie events
                    clicker(e_effectsArea, c_mark, debouncer((t)=>down(t)));
                    clicker(e_effectsArea, c_like, debouncer((t)=>down(t, false)));
                    clicker(e_effectsArea, c_close, debouncer((t)=>close(t, true), 150));
                    console.log('marker initialized.', marker);
                } catch (error) {
                    console.log(error);
                }
            },
        },
        get data() {
            const {init: {_conf: {static: {dataPrefix:s_dataPrefix, dataCaches:s_dataCaches, dataCount:s_dataCount, userNick:s_userNick, userMail:s_userMail, userMid:s_userMid}, setter: {nick, mail, counts, pending, promised}}}, _utils: {_cookie: {get:getCookie}, _storage: {get:getStorage}}} = this;
            const regExp = new RegExp(`${s_dataPrefix}(.*?)=(.*?);`, 'g'),
                  stored = document.cookie.match(regExp) || [];
            let result = {};
            if(stored.length>=1){
                stored.map(item => {
                    let pair = item.split("="),
                        key = pair[0],
                        val = pair[1].split(";")[0];
                    result[key] = val; // 将键值对存入 result 对象中
                });
            }
            return {
                'user': {
                    nick: getCookie(s_userNick) || nick,
                    mail: getCookie(s_userMail) || mail,
                    mid: getCookie(s_userMid),
                },
                'stat': {
                    counts: counts || 0,
                    pending: pending || 0,
                    promised: promised || {},
                },
                'list': result,
                'path': window.location.pathname,
                '_caches': getStorage(s_dataCaches) || '{}',
                '_counts': s_dataCount,
            };
        },
        set data(obj) {
            const {init: {_conf: {setter}}, _utils: {_etc: {isObject}}} = this;
            if(!isObject(obj)) {
                console.warn('set data error: typeof obj is not an Object!', obj);
                return;
            }
            Object.keys(obj).forEach(item=> {
                let set_val = obj[item];
                if(set_val || set_val===0) {
                    setter[item] = set_val; // ??= set_val;
                }
            });
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
                let presetConfs = {
                        static: {
                            likeMax: 2,
                            dataMin: 2,
                            dataMax: 3,
                            dataDelay: 500,
                            dataAlive: 365,
                            dataCount: 0,
                            dataStream: false,
                            dataPrefix: 'marker-',
                            dataCaches: 'markerCaches',
                            lineColor: 'orange',
                            lineColors: 'red',
                            lineDegrees: 0,
                            lineBold: 15,
                            lineBoldMax: 30,
                            lineAnimate: true,
                            lineKeepTop: false,
                            useNote: true,
                            useCopy: true,
                            useQuote: true,
                            ctxMark: '标记',
                            ctxMarking: '标记中..',
                            ctxMarked: '已标记',
                            ctxMarkMax: '用户标记已满',
                            ctxNote: '注释',
                            ctxNoted: '已注释',
                            ctxCopy: '复制',
                            ctxCopied: '已复制',
                            ctxQuote: '引用',
                            ctxQuoted: '已引用',
                            ctxCancel: '取消选中/删除',
                            ctxLike: '+1',
                            ctxLiked: '-1',
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
                            toolIn: 'toolInside',
                            mark: 'mark',
                            note: 'note',
                            copy: 'copy',
                            quote: 'quote',
                            like: 'like',
                            liked: 'liked',
                            update: 'update',
                            close: 'close',
                            done: 'done',
                            avatars: 'avatars',
                            disabled: 'disabled',
                            underline: 'underline',
                            processing: 'processing',
                            blackList: ['markable','wp-block-quote','wp-block-code','wp-block-table','wp-element-caption'],
                        },
                        element: {
                            effectsArea: document,
                            commentArea: null,
                            commentInfo: {
                                userNick: null,
                                userMail: null,
                            }
                        },
                        setter: {},
                    };
                return {
                    _rewriter: function mergeObjects(rewrite = {}, preset = presetConfs, merge = true) {
                        const result = { ...preset };
                        for (const key in rewrite) {
                            if (rewrite.hasOwnProperty(key)) {
                                const validObjects = marker._utils._etc.isObject(result[key]) && marker._utils._etc.isObject(rewrite[key]);
                                if (merge) { // && marker._utils._etc.isObject(result[key])
                                    if (Array.isArray(result[key]) && Array.isArray(rewrite[key])) {
                                        // 合并数组
                                        result[key] = [...new Set([...result[key], ...rewrite[key]])];
                                    } else if (marker._utils._dom.valider(rewrite[key])) {
                                        // 覆盖元素
                                        result[key] = rewrite[key];
                                    } else {
                                        // 递归合并对象
                                        if (validObjects) {
                                            result[key] = mergeObjects(rewrite[key], result[key] || {}, merge);
                                        } else {
                                            // 直接覆盖
                                            result[key] = rewrite[key];
                                        }
                                    }
                                } else {
                                    if (validObjects) {
                                        result[key] = mergeObjects(rewrite[key], result[key] || {}, merge);
                                    } else {
                                        // 直接覆盖
                                        result[key] = rewrite[key];
                                    }
                                }
                            }
                        }
                        return result;
                    },
                };
            }(),
            configurable: false,
            writable: false
        },
    });
}).call(window);