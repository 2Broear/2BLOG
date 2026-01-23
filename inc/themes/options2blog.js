jQuery(document).ready(function($){
    
    function throttle(fn, delay = 1000) {
        let runing = false;
        return function(...args) {
            if (runing) return;
            runing = true;
            let timer = setTimeout(function() {
                fn.apply(this, args);
                runing = false;
                timer = null;
                clearTimeout(timer);
            }, delay);
        };
    }
    
    function debouncer (fn, delay = 1000) { //this.#delay
        let timer;
        return function(...args) {
            if(timer) clearTimeout(timer);
            timer = setTimeout(()=> fn.apply(this, args), delay);
        };
    }

    function bindEvents(events = 'onclick', parent, ids, callback) {
        if (!parent) {
            console.warn('bindEvents failed', parent);
            return;
        }
        parent[events] = (e)=> {
            e = e || window.event;
            let t = e.target || e.srcElement;
            if(!t) return;
            while(t!=parent){
                if(!ids || ids==="") {
                    callback(t,e);
                    break;
                }
                if(t.id===ids || t.classList && t.classList.contains(ids) || t.nodeName.toUpperCase()===ids.toUpperCase()){
                    // callback?.();
                    if(callback&&typeof callback==='function') callback(t,e); //callback(t) || callback(t); // callback.apply(this, ...arguments);
                    break;
                }
                // console.log('origin', t);
                t = t.parentNode;
            }
        };
    }
    
    function get_api_salt(file, sucessCallback, errorCallback) {
        return fetch(`${window.location.origin}/wp-admin/admin-ajax.php?action=api_salt_handler&api_file=${file}`, {
            method: 'GET', // POST incase 200load from cache
        })
        .then(res=> {
            if (!res.ok) throw new Error('request failed.');
            try {
                return res.json();
            } catch (e) {
                console.warn(e);
                return res.text();
            }
        })
        .then(data=> {
            console.log('data(list) fullfilled.', data);
            sucessCallback?.(data);
            return data;
        })
        .catch(error => {
            console.error('Error fetching progress:', error);
            errorCallback?.(error);
            return error;
        });
    }
    
    function getParentElement(curEl, parCls){
      //!curEl.classList incase if dnode oes not have any classes (null occured)
      while(!curEl || !curEl.classList || !curEl.classList.contains(parCls)){
          if(!curEl) break;  //return undefined
          curEl = curEl.parentNode; //parentElement
      };
      return curEl;
    }
    
    function getParentNode(curPar,tarPar){
        while(curPar && curPar.nodeName.toLowerCase()!=tarPar){
            curPar = curPar.parentNode
        };
        return curPar
    }
    
    function setCookie(name,value,path='/',days=0) {
        let exp = new Date();
        let exp_time = typeof days=='number'&&days>=1 ? days*(24*60*60*1000) : 24*60*60*500;
        exp.setTime(exp.getTime() + exp_time);
        document.cookie = name+"="+escape(value)+";expires="+exp.toGMTString()+";path=/";
    }
    function getCookie(cname) {
        var name = cname+"=";
        var ca = document.cookie.split(';');
        for(var i=0,caLen=ca.length; i<caLen; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c=c.substring(1);
            if(c.indexOf(name)!=-1) return c.substring(name.length, c.length);
        }
        return "";
    }
    // 自动根据时段设置主题
    (function automode() {
        const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const targetNode = document.querySelector('.wrap'); //document.body
        function handleColorSchemeChange(e) {
            if (e.matches) {
                // 用户偏好深色模式优先 (dark)
                targetNode.classList.add('dark');
                setCookie('theme_mode_panel', 'dark');
                console.log('theme_mode_panel[auto] prefers-color-scheme: dark');
            } else {
                targetNode.classList.remove('dark');
                setCookie('theme_mode_panel', 'light');
                console.log('theme_mode_panel[auto] prefers-color-scheme: default');
            }
        }
        // 3. 初始执行一次，设置当前主题
        handleColorSchemeChange(colorSchemeQuery);
        // 4. 监听媒体查询的变化
        colorSchemeQuery.addEventListener('change', handleColorSchemeChange);
    })();
    
    var mediaUploader;
    // var _this;  // preset global this
    const upload_buttons = document.querySelectorAll(".upload_button"),
          ubsLen=upload_buttons.length;
    if(ubsLen>0){
        for(let i=0;i<ubsLen;i++){
            upload_buttons[i].onclick=function(e){
                // _this = this;
                this_parent = this.parentNode;
                var p_list = this_parent.querySelector('.upload_preview_list'),
                    d_type = Number(this.dataset.type), //1:images,2:videos,3:all;
                    u_multi = this.dataset.multi;
                // If the media frame already exists, reopen it (this will Cache mediaUploader frame arguments which's not applicable for required. tested: non-second requests without reopen)
                // e.preventDefault();
                // if(mediaUploader) {  // && !u_multi
                //     mediaUploader.open();
                //     return;
                // }
                //https://wordpress.stackexchange.com/questions/264115/show-only-images-and-videos-in-a-wp-media-window
                switch(d_type){
                    case 1:
                        d_type = 'image';
                        break;
                    case 2:
                        d_type = 'video';
                        break;
                    case 3:
                    default:
                        d_type = ['video','image'];
                        break;
                }
                console.log(`multiple: ${u_multi}, ${d_type} selection available.`);
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '',
                    button: {
                        text: '选择文件'
                    },
                    multiple: u_multi ? true : false,
                    library: {
                        type: d_type
                    },
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON(),
                        attachments = mediaUploader.state().get('selection').toJSON();
                    // console.log(mediaUploader.state());
                    let field = this_parent.querySelector('.upload_field'),//.parent().find('.upload_field')[0],
                        img = this_parent.querySelector('.upload_preview.img'),//.parent().find('.upload_preview.img'),
                        bg = this_parent.querySelector('.upload_preview.bg'),//.parent().find('.upload_preview.bg');
                        bgm = this_parent.querySelector('.upload_preview.bgm');
                    // preview loads
                    if(bgm){
                        bgm.setAttribute('src',attachment.url);
                        bgm.setAttribute('poster',attachment.url);
                    }else{
                        img ? img.setAttribute('src',attachment.url) : false;
                        bg ? bg.setAttribute('style','background:url('+attachment.url+') center center /cover;') : false;
                    };
                    if(p_list){
                        p_list.innerHTML = "";
                        field.value = "";
                        for(let i=0,atcLen=attachments.length;i<atcLen;i++){
                            let attachment = attachments[i];
                            if(attachment.url) {
                                p_list.innerHTML += attachment.type==='video' ? `<video class="upload_preview bgm" src="${attachment.url}" poster="${attachment.url}" preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>` : `<em class="upload_previews" style="background:url(${attachment.url}) center center /cover;"></em>`;
                                field.value += attachment.url+' , ';
                            }
                        }
                    }else{
                        field.value = attachment.url;
                    }
                });
                mediaUploader.open();
            };
        }
    }


// ***  ROW JAVASCRIPT FUNCTIONs (AFTER DOCUMENT LOADED) *** //
    
    bindEvents('onclick', document, '', (t, e)=> {
        // console.log(t)
        switch (true) {
            case t.classList && t.classList.contains('dynamic_dom'):
                e.stopPropagation();
                e.preventDefault();
                const dom = t.dataset.dom || 'IFRAME';
                const element = document.createElement(dom);
                if (!t.dataset.src) {
                    break;
                }
                element.src = t.dataset.src;
                if (dom == 'video' || dom == 'img') element.className = 'upload_preview bgm';
                if (dom == 'video') {
                    element.poster = t.dataset.src;
                    element.setAttribute('preload', 'preload');
                    element.setAttribute('autoplay', 'autoplay');
                    element.setAttribute('muted', 'muted');
                    element.setAttribute('loop', 'loop');
                    element.setAttribute('x5-video-player-type', 'h5');
                    element.setAttribute('controlslist', 'nofullscreen nodownload');
                }
                if (t.dataset.width) element.width = t.dataset.width;
                if (t.dataset.height) element.height = t.dataset.height;
                t.parentNode.appendChild(element);
                t.remove();
                console.log('dynamic loaded element', element);
                break;
            case t.id === 'updateSchedule':
                const updateScheduleInput = t.parentNode.querySelector('#updateSchedules');
                const updateScheduleValue = updateScheduleInput.value;
                if (confirm(`Updating Scheduled Tasks(scheduled_rss_feeds_updates->${updateScheduleValue})?`)) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', t.dataset.adminUrl, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                t.disabled = true;
                                alert('updating status: ' + response.data + ' Standby cronjob-redepoly..'); // + ', Locate to rss-feeds now..'
                                let counter = 3;
                                t.value = 'Standby ' + counter;
                                let countdown = setInterval(()=> {
                                    if (counter <= 1) {
                                        clearInterval(countdown);
                                        t.value = 'Schedules updated';
                                        updateScheduleInput.disabled = false;
                                        updateScheduleInput.focus();
                                        return;
                                    }
                                    counter--;
                                    t.value = 'Standby ' + counter;
                                }, 1000);
                                // location.replace(location.origin + location.pathname + "?page=" + t.dataset.page);
                                // location.reload(true);
                            } else {
                                console.error('Error:', response.data);
                            }
                        } else {
                            console.error('Request failed with status:', xhr.status);
                        }
                    };
                    xhr.send('action=update_cronjobs&nonce=' + t.dataset.nonce + '&interval=' + updateScheduleValue);
                }
                break;
            case t.id === 'updateDomain':
                const context = t.value;
                const options = t.dataset.options;
                if (!options) {
                    alert('未选择任何更新项！');
                    return;
                }
                let before = t.dataset.before;
                let after = t.dataset.after;
                before = before.substring(before.indexOf('//'), before.length);
                after = after ? after.substring(after.indexOf('//'), after.length) : before;
                let update_options = '';
                options.split(',').forEach((item)=> {
                    if (item) update_options += item.split('=')[0] + '/';
                });
                if (false === confirm(`确认更新（${update_options}）数据库内容：${before}->${after}？`)) {
                    console.log('user canceled.');
                    return;
                }
                t.value = '等待更新..';
                t.disabled = true;
                if (after === before) {
                    t.value = context;
                    t.disabled = false;
                    break;
                }
                // console.log(before, after);
                jQuery.post(t.dataset.url, {
                    action: 'update_db_data',
                    before: encodeURIComponent(before),
                    after: encodeURIComponent(after),
                    options: encodeURIComponent(options),
                    _ajax_nonce: t.dataset.nonce,
                }, function(response, status) {
                    console.log(response, status);
                    t.value = '已更新';
                    if (status !== 'success') {
                        return;
                    }
                    const memcachedUrl = window.location.origin + '/memcached.php';
                    fetch(memcachedUrl)
                        .then((res)=>{
                            t.value = context;
                            t.disabled = false;
                            if (res.status !== 200) {
                                console.log('no memcached flush needed', res);
                                return;
                            }
                            // mark opened window
                            const memcached = window.open(memcachedUrl, '', 'width=1200,height=800');
                            if (window.SharedWorker) {
                                // 连接 Shared Worker
                                const worker = new SharedWorker("/sworker.js", {
                                    name: "MySharedWorker", // 设置 Worker 名称
                                });
                                worker.port.start();
                                
                                // 接收更新
                                worker.port.onmessage = (e) => {
                                    console.log(e.data)
                                    if (e.data === 'stop') {
                                        console.warn('shared worker stoped, reflush done.');
                                        // close marked window
                                        let timer = setTimeout(()=> {
                                            window.focus();
                                            // 类 alert 阻塞主线程任务
                                            if (confirm('数据库已更新，刷新页面？')) window.location.reload();
                                            memcached.close();
                                            clearTimeout(timer);
                                            timer = null;
                                        }, 1500);
                                    }
                                };
                            }
                        })
                        .catch((err)=> {
                            console.warn(err)
                        });
                });
                break;
            default:
                // code
        }
    });
    
    const //switch_tab = document.querySelector(".switchTab"),
          switch_form = document.querySelector("form"),
          blog_settings = document.querySelector(".wrap.settings"),
          theme_root = document.querySelector(":root");
    if(blog_settings) {
        
        //onchange/onpropertychange only active when off-focus
        bindEvents('oninput', switch_form, '', throttle((t)=> {
            // console.log(t);
            if (!t.classList) return;
            switch (true) {
                case t.id == 'site_theme':
                    theme_root.style.setProperty("--panel-theme", t.value); //this.value
                    break;
                case t.classList.contains('live-update'):
                    const updateTarget = t.parentNode.querySelector('.update-target');
                    if (updateTarget) {
                        if (t.value === updateTarget.dataset.before) {
                            updateTarget.setAttribute('disabled', '');
                            break;
                        }
                        updateTarget.removeAttribute('disabled');
                        updateTarget.dataset.after = t.value;
                    }
                    break;
                default:
                    // console.warn(t);
            }
        }, 250));
        
        // 多选框同步逻辑
        const checkboxes = document.querySelectorAll('.checkbox');
        function checkboxClear(eachcheck, node, callback) {
            for(let j=0,echkLen=eachcheck.length;j<echkLen;j++) {
                const isButtonNode = node.type === 'button';
                let inputString = isButtonNode ? node.dataset.options : node.value;
                // decodeURIComponent for ',' fix
                inputString = decodeURIComponent(inputString);
                let outputTrim = inputString.replace(/\s*/g,""),  // clear all whitespace
                    outputEnds = outputTrim.substr(outputTrim.length-1, outputTrim.length);  // last chr
                if (outputEnds != ',' && outputTrim.length > 0) inputString += ',';  // support IE
                eachcheck[j].onchange = function() {
                    // decodeURIComponent for regexp match
                    this.value = decodeURIComponent(this.value);
                    node.dataset.options = decodeURIComponent(node.dataset.options);
                    let clearOutput = isButtonNode ? node.dataset.options.replace(/\s*/g,"") : node.value.replace(/\s*/g,""),  // clear all whitespace
                        clearString = clearOutput.match(this.value + ',') ? this.value + ',' : this.value;
                    if (this.checked) {
                        clearOutput += this.value + ',';
                    } else {
                        clearOutput = clearOutput.replace(clearString, '');
                    }
                    // console.log(clearOutput)
                    // re encodeURIComponent
                    // clearOutput = encodeURIComponent(clearOutput);
                    // final output
                    if (isButtonNode) {
                        node.dataset.options = clearOutput;
                    } else {
                        node.value = clearOutput;
                    }
                    callback?.(clearOutput);
                };
            }
        }
        for(let i=0,ckbLen=checkboxes.length;i<ckbLen;i++){
            let eachbox = checkboxes[i],
                eachcheck = eachbox.querySelectorAll('input[type=checkbox]'),
                outputText = eachbox.parentNode.querySelector('input[type=text]'),
                outputButton = eachbox.parentNode.querySelector('input[type=button]');
            if (outputButton)
                checkboxClear(eachcheck, outputButton);
            else 
                checkboxClear(eachcheck, outputText);
        }
        // 即时更新 select 图片
        const select_images = document.querySelectorAll(".select_images"),
              select_mirror = document.querySelectorAll(".select_mirror");
        for(let i=0,selImgLen=select_images.length;i<selImgLen;i++){
            select_images[i].onchange=function(e){
                let preview = this.options[this.selectedIndex].getAttribute("preview");
                this.parentNode.querySelector("img").src = preview ? preview : this.value;
            };
        }
        for(let i=0,selMirLen=select_mirror.length;i<selMirLen;i++){
            select_mirror[i].onchange=function(e){
                let image = this.parentNode.querySelector("img"),
                    value = this.getAttribute("parm");
                image.src = "";  // clear last-mirror cache https://img.2broear.com/images/loading_3.png
                image.src = value ? this.value+value : this.value;
            };
        }
        
        // 即时更新 select 选项
        const select_options = document.querySelectorAll(".select_options"),
              dynamic_comment = document.querySelector(".dynamic_comment"),
            //   dynamic_opts = document.querySelectorAll(".dynamic_opts"),
            //   dynamic_fn = function(t,c,e){
            //       for(let i=0,tLen=t.length;i<tLen;i++){
            //           t[i].classList.remove(c);
            //       }
            //       if(e && e!=''){
            //           dynamic_comment ? dynamic_comment.innerHTML = e : false;
            //           let dynamic_all = document.querySelectorAll('tr.'+e);
            //           for(let j=0,dynLen=dynamic_all.length;j<dynLen;j++){
            //               dynamic_all[j].classList.add(c);
            //           }
            //       }else{
            //           dynamic_comment ? dynamic_comment.innerHTML = 'BaaS' : false;
            //       }
            //   },
              dynamic_class = 'dynamic_optshow';
              
        for(let i=0,selOptLen=select_options.length;i<selOptLen;i++){
            select_options[i].onchange=function(e){
                let tar = getParentNode(this, 'tr');
                if(tar.classList && !tar.classList.contains('child_option')){
                    let opt = tar.nextElementSibling;
                    while(opt){
                        if(!opt.classList || !opt.classList.contains('child_option')) break;  //跳出循环
                            opt.classList.remove(dynamic_class);  // remove all optshow
                            let optsval = this.value;
                            if(optsval && optsval!=''){
                                dynamic_comment ? dynamic_comment.innerHTML = optsval : false;
                                let dynamic_lock = document.querySelectorAll('tr.'+optsval);
                                for(let j=0,dynLockLen=dynamic_lock.length;j<dynLockLen;j++){
                                    dynamic_lock[j].classList.add(dynamic_class);
                                }
                            }else{
                                dynamic_comment ? dynamic_comment.innerHTML = 'BaaS' : false;
                            }
                            // dynamic_fn(optshow, dynamic_class, optsval);
                            opt = opt.nextElementSibling;  // 继续查找，直到当前元素不含 child_option 类（即非该 checkbox 子项）
                    }
                }
            };
        }
        
        // 自动同步 checkbox 勾选框关联元素（被选框非 child_option，仅一级选框可用）
        const check_boxes = document.querySelectorAll("label input[type=checkbox]");  // inside label only;
        for(let i=0,chkBoxLen=check_boxes.length;i<chkBoxLen;i++){
            check_boxes[i].onchange=function(){
                let tar = getParentNode(this, 'tr');
                if(tar.classList && !tar.classList.contains('child_option')){
                    let opt = tar.nextElementSibling;
                    while(opt){
                        if(!opt.classList || !opt.classList.contains('child_option')) break;  //跳出循环
                            if(this.checked){
                                if(!opt.classList.contains(dynamic_class)) opt.classList.add(dynamic_class);
                            }else{
                                opt.classList.remove(dynamic_class);
                            }
                            opt = opt.nextElementSibling;  // 继续查找，直到当前元素不含 child_option 类（即非该 checkbox 子项）
                            // console.log(tar); //当前元素后一个
                    }
                }
            }
        }
        
        
        // send email to client
        $('.sendmail').on('click',function(){
            $("#loading").removeClass();
            $("#loading").addClass("responsing");
            var data = {
                action: 'mail_before_submit',
                toemail: $('#site_smtp_mail').val(), // change this to the email field on your form
                _ajax_nonce: $('#my_email_ajax_nonce').data('nonce'),
            };
            jQuery.post(window.location.origin + "/wp-admin/admin-ajax.php", data, function(response){
                if(response.match("测试邮件已发送")){
                    $("#loading").addClass("responsed ok");
                }else{
                    $("#loading").addClass("responsed err");
                }
                setTimeout(function() {
                    alert(response);
                    $("#loading").removeClass("responsing");
                }, 500);
            });
        });
        
        // same options sync(identify by input id)
        const sync_data = [
            'site_leancloud_appid',
            'site_leancloud_appkey',
            'site_leancloud_server',
        ];
        for(let i=0,syncLen=sync_data.length;i<syncLen;i++){
            let each_list = document.querySelectorAll("input#"+sync_data[i]);
            for(let j=0,listLen=each_list.length;j<listLen;j++){
                each_list[j].oninput=function(e){
                    let each_data = document.querySelectorAll("input#"+this.id);
                    for(let k=0,dataLen=each_data.length;k<dataLen;k++){
                        each_data[k].value = this.value;  //cur_data.classList.add("sync");
                    }
                };
            }
        }
    }
    // scroll function
    const submit_btn = document.querySelectorAll("p.submit")[0];
    var scroll_throttler = null,
        scroll_record = 0,
        scroll_delay = 200,
        scroll_func = function(e){
            const switch_offset = switch_form.offsetTop;
            // console.log(switch_form)
            return (function(){
                if(scroll_throttler==null){
                    scroll_throttler = setTimeout(function(){
                        console.debug('scroll_throttler');
                        var windowHeight = window.innerHeight,
                            clientHeight = document.body.clientHeight,
                            scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                        scroll_foward = window.pageYOffset;  //scroll Value
                        if(scroll_record-scroll_foward<0){  //down
                            if(scrollTop>=switch_offset){
                                document.body.classList.add("fixed");
                            }
                        }else{  //up
                            if(scrollTop<=switch_offset){ //*4
                                document.body.classList.remove("fixed");
                            }
                        }
                        scroll_record = scroll_foward;  // Update scrolled value
                        scroll_throttler = null;  //消除定时器
                    }, scroll_delay);
                }
            })();
        };
    if (switch_form) window.addEventListener('scroll', scroll_func, true);
    
    
    // switch tabs & active class
    const activecls = "active",
          switchcls = "show",
          switchtab = document.querySelectorAll(".switchTab li"),
          clearClass = function(els,cls){
              for(let i=0,elsLen=els.length;i<elsLen;i++){
                  els[i].classList.remove(cls);
              }
          },
          formtable = document.querySelectorAll("form .formtable")[0],
          pushParam = function(key,value){
              // THIS FUNCTION CONSTRUCTED VIA chatGPT 3.5 MODEL.
              const href = window.location.href;
              var newUrl = href;
              if(window.URLSearchParams){
                  const params = new URLSearchParams(window.location.search);  // Get the current URLSearchParams object
                  params.set(key, value);  // Update the value of a parameter
                  newUrl = `${window.location.pathname}?${params.toString()}`;  // Create a new URL with the updated parameters
              }else{
                  const url = new URL(href);  // Get the current URL as a URL object
                  url.searchParams.delete(key);  // Remove a parameter from the query string
                  url.searchParams.set(key, value);  // Add or update a parameter in the query string
                  newUrl = url.toString();  // Get the updated URL string
              }
              history.pushState(null, '', newUrl);  // Update the URL without refreshing the page
          },
          getQueryObject = function(url) {
              url = url == null ? window.location.href : url;
              var search = url.substring(url.lastIndexOf("?") + 1);
              var obj = {};
              var reg = /([^?&=]+)=([^?&=]*)/g;
              search.replace(reg, function (rs, $1, $2) {
                  var name = decodeURIComponent($1);
                  var val = decodeURIComponent($2);
                  val = String(val);
                  obj[name] = val;
                  return rs;
              });
              return obj;
          },
          parms = getQueryObject();
          
    // console.log(switchtab);
    if(parms && parms.tab) {
        document.querySelector("form ."+parms.tab).classList.add(switchcls);
        document.querySelector(".switchTab li#"+parms.tab).classList.add(activecls);  // clearClass then active
    } else {
        formtable ? formtable.classList.add(switchcls) : formtable;  // auto active first formtable
        if (switchtab[0]) switchtab[0].classList.add(activecls);  // clearClass then active
    }
    
    bindEvents('onclick', document.querySelector(".switchTab"), 'li', (t)=> {
        pushParam('tab', t.id);
        // location.search = '?page=2blog-settings&tab='+t.id;
        clearClass(switchtab, activecls);  // clear actived class
        t.classList.add(activecls);  // clearClass then active
        clearClass(document.querySelectorAll("form ."+switchcls),switchcls);  // upadted els while click
        document.querySelector("form ."+t.id).classList.add(switchcls);  // clearClass then show
    });
    
    // rss fetch feeds reloader
    const contents = document.querySelector('#contents');
    const reloader = 'reloadFeeds';
    if (contents) {
        // const reloadFeeds = contents.querySelector('.reloadFeeds');
        bindEvents('onclick', contents, '', (t)=> { //reloader
            if (t.id==='reloadCount') {
                const reloadFeeds = t.parentNode.querySelector('.reloadFeeds');
                t.onchange = t.onpropertychange = ()=> reloadFeeds.dataset.limit = t.value;
                return;
            }
            if (t.classList && t.classList.contains(reloader)) {
                const dataset = t.dataset;
                const api_url = dataset.api;
                const api_file = api_url.substring(api_url.lastIndexOf('/'), api_url.indexOf('?'));
                const container = contents.querySelector(`.formtable.${dataset.cat}.${switchcls}`);
                // console.log(`loading url: ${api_url}`);
                if (!confirm(`重新拉取 ${dataset.cat} 中所有 rss 数据（${dataset.limit}条）？`)) return;
                t.textContent = `fetching ${dataset.cat}...`;
                t.classList.remove(reloader);
                // async
                async function use_api_salt() {
                    const api_salt = await get_api_salt(api_file);
                    // get_api_salt(api_file, (api_salt)=> {
                        const new_url = api_url.substring(0, api_url.indexOf('?')) + `?s=${api_salt.s}&t=${api_salt.t}`;
                        // console.log(new_url);
                        var xhr = new XMLHttpRequest();
                        xhr.open('GET', `${new_url}&cat=${dataset.cat}&limit=${dataset.limit}&update=${dataset.update}&output=${dataset.output}&clear=${dataset.clear}`, true);
                        xhr.onprogress = function(event) {
                          if (event.lengthComputable) {
                            var percentComplete = event.loaded / event.total * 100;
                            console.log('Progress: ' + percentComplete + '%');
                          }
                        };
                        xhr.onload = function() {
                          if (xhr.status === 200) {
                            container.innerHTML = `<p style="text-align:right">site_rss_${dataset.cat}_cache Reloaded, <u>${dataset.cat} reloaded!</u></p> ${ xhr.responseText }`;
                            console.log('data fullfilled.');
                            alert(`${dataset.cat} rss data loaded.`);
                          } else {
                                t.classList.add(reloader);
                                t.textContent = `reload failed, reload ${dataset.cat}?`;
                          }
                        };
                        xhr.onerror = function(err) {
                            console.warn(err);
                            t.classList.add(reloader);
                            t.textContent = `reload failed, reload ${dataset.cat}?`;
                        }
                        xhr.send();
                    // });
                }
                use_api_salt();
            }
        });
        
        // dropdown_logs
        const rsslogs = contents.querySelector('.rsslogs');
        if (!rsslogs) {
            throw new Error('invalid rsslogs list/area/react provided!', rsslogs);
        }
        const dropdown_logs = rsslogs.querySelector('.logs-dropdown');
        const dropdown_area = rsslogs.querySelector('.logs-container');
        const selected_year = rsslogs.querySelector('.logs-year');
        const selected_month = rsslogs.querySelector('.logs-month');
        
        // dropdown list
        let cacheControl = {
                list: [],
                logs: []
            };
            
        // defaults defaults value
        if (rsslogs.dataset.defaults) {
            const selected_year_val = selected_year.selectedOptions[0].value;
            const selected_month_val = selected_month.selectedOptions[0].value;
            if (!selected_year_val || !selected_month_val) throw new Error('invalid selected_year or month with defaults on.');
            let valuePusher = (nodes, array, replace = false)=> {
                    for (let i=1,l=nodes.length; i<l; i++) {
                        const value = replace ? nodes[i].value.replace('https://', '/www/wwwroot/') : `${rsslogs.dataset.path}/${selected_year_val}/${nodes[i].value}`;
                        array.push(value);
                    }
                };
            cacheControl.list[selected_year_val] = [[]];
            valuePusher(selected_month.children, cacheControl.list[selected_year_val][0]);
            //..
            cacheControl.list[selected_year_val][selected_month_val] = [];
            valuePusher(dropdown_logs.children, cacheControl.list[selected_year_val][selected_month_val], true);
            // mark as cached.
            selected_year.selectedOptions[0].dataset.cached = selected_month.selectedOptions[0].dataset.cached = true;
        }
        
        function listUpdates(list, updateNode = null, cached = false) {
            if (!list || !Array.isArray(list)) {
                updateNode.innerHTML = `<option value=""> ${updateNode.dataset.context} </option>`;
                // throw new Error('invalid list array provided.');
                console.warn('invalid list array provided.', list);
                return;
            }
            if (!updateNode || !updateNode instanceof HTMLElement) throw new Error('invalid update node provided.');
            const year_value = selected_year.selectedOptions[0].value ? selected_year.selectedOptions[0].value : 0;
            const month_value = selected_month.selectedOptions[0].value ? selected_month.selectedOptions[0].value : 0;
            const is_edit_list = updateNode !== dropdown_logs;
            dropdown_logs.innerHTML = `<option value=""> ${dropdown_logs.dataset.context} </option>`;  // always update dropdown_logs
            updateNode.innerHTML = `<option value=""> ${updateNode.dataset.context} </option>`;
            // Performance enhancement
            let fragment = document.createDocumentFragment();
            list.forEach((item)=> {
                const itemLast = is_edit_list ? item.lastIndexOf('/') + 1 : item.lastIndexOf('/');
                const itemName = item.substr(itemLast, item.length);
                const itemLink = item.replace('/www/wwwroot/', 'https://');
                let option = document.createElement("Option");
                option.value = is_edit_list ? itemName : itemLink;
                if (cacheControl.logs[year_value] && cacheControl.logs[year_value][month_value] && cacheControl.logs[year_value][month_value][itemName]) {
                    console.log('find cached option while loading options.', itemLink);
                    // mark cached options(incase of logCaches loss effect)
                    option.dataset.cached = true;
                }
                option.textContent = itemName;
                fragment.appendChild(option);
            });
            updateNode.appendChild(fragment);
            // UE enhancement
            updateNode.focus();
        };
        
        bindEvents('onchange', rsslogs, '', (t)=> {
            if (!t.classList) return;
            const selectedOption = t.options[t.selectedIndex];
            let year_value = selected_year.selectedOptions[0].value ? selected_year.selectedOptions[0].value : 0;
            let month_value = selected_month.selectedOptions[0].value ? selected_month.selectedOptions[0].value : 0;
            if (t.classList.contains('dropdown-react')) {
                let directoryOnly = 0;
                let updateNode = dropdown_logs;
                // update month list(query directory only) if year selected
                const rootsSelected = selected_year.value == '';
                const yearsSelected = t.classList.contains('logs-year');
                const monthSelected = t.classList.contains('logs-month');
                if (yearsSelected || rootsSelected) {
                    selected_month.selectedIndex = dropdown_logs.selectedIndex = 0; // reset month&logs if year selected
                    if (yearsSelected) {
                        directoryOnly = 1;
                        updateNode = selected_month;
                        // reset month selection
                        month_value = 0;
                        selected_month.focus();
                    }
                    if (rootsSelected) {
                        directoryOnly = 0;
                        updateNode = dropdown_logs;
                        // reset all selections
                        year_value = month_value = 0;
                        selected_year.focus();
                        // quite if month selected while year not selected.
                        listUpdates(false, selected_month);
                        // if (monthSelected) return;
                    }
                }
                // load cached value
                if (selectedOption.dataset.cached) {
                    listUpdates(cacheControl.list[year_value][month_value], updateNode); // logs
                    console.log(`option(list) load from caches`, cacheControl.list);
                    return;
                }
                // fetching value
                let api_path = rsslogs.dataset.path;
                if (year_value) api_path = api_path + '/' + year_value;
                if (month_value) api_path = api_path + '/' + month_value;
                fetch(`${rsslogs.dataset.api}&path=${api_path}&dironly=${directoryOnly}&_ajax_nonce=${rsslogs.dataset.nonce}`, {
                    method: 'GET', // POST incase 200load from cache
                })
                .then(res=> {
                    if (!res.ok) throw new Error('request failed.');
                    return res.json(); //text()
                })
                .then(data=> {
                    listUpdates(data, updateNode);
                    // mark & save to caches
                    selectedOption.dataset.cached = true;
                    if (!cacheControl.list[year_value]) cacheControl.list[year_value] = [];
                    cacheControl.list[year_value][month_value] = data; //JSON.parse(data);
                    console.log('data(list) fullfilled.', data);
                })
                .catch(error => {
                    console.error('Error fetching progress:', error);
                });
                return;
            }
            
            // fetching logs
            if (t.classList.contains('logs-dropdown')) {
                // invalid options
                if (!selectedOption || !selectedOption.value) return;
                // load from cache
                const file_names = selectedOption.textContent; //.substr(1, selectedOption.textContent.lastIndexOf('.')-1)
                if (year_value == 1 || month_value == 1) {
                    console.warn('please selecte a year/month before selecting logs!');
                    // return;
                }
                if (selectedOption.dataset.cached) {
                    dropdown_area.value = cacheControl.logs[year_value][month_value][file_names];
                    console.log(`option(log) load from caches`, cacheControl.logs);
                    return;
                }
                // load new value
                fetch(`${selectedOption.value}?ts=${Date.now()}`, {
                    method: 'GET', // POST incase 200load from cache
                })
                .then(res=> {
                    if (!res.ok) throw new Error('request failed.');
                    return res.text(); //json()
                })
                .then(data=> {
                    dropdown_area.value = data;  // fullfill data
                    // mark & save to caches
                    selectedOption.dataset.cached = true;
                    if (!cacheControl.logs[year_value]) cacheControl.logs[year_value] = [];
                    if (!cacheControl.logs[year_value][month_value]) cacheControl.logs[year_value][month_value] = [];
                    cacheControl.logs[year_value][month_value][file_names] = data;
                    console.log('data(log) fullfilled.', data);
                })
                .catch(error => {
                    console.error('Error fetching progress:', error);
                });
            }
        });
    }
});