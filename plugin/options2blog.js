jQuery(document).ready(function($){
    var mediaUploader;
    var _this;  // preset global this
    var p_list;
    const upload_buttons = document.querySelectorAll(".upload_button");
    if(upload_buttons.length>0){
        for(let i=0;i<upload_buttons.length;i++){
            upload_buttons[i].onclick=function(e){
                _this = this;
                this_parent = this.parentNode;
                p_list = this_parent.querySelector('.upload_preview_list');//.parent().find('.upload_preview_list')[0];
                e.preventDefault();
                if(mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '',
                    button: {
                        text: '选择图片'
                    },
                    multiple: p_list ? true : false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON(),
                        attachments = mediaUploader.state().get('selection').toJSON();
                    let field = this_parent.querySelector('.upload_field');//.parent().find('.upload_field')[0],
                        img = this_parent.querySelector('.upload_preview.img');//.parent().find('.upload_preview.img'),
                        bg = this_parent.querySelector('.upload_preview.bg');//.parent().find('.upload_preview.bg');
                    // preview loads
                    img ? img.setAttribute('src',attachment.url) : false;
                    bg ? bg.setAttribute('style','background:url('+attachment.url+') center center /cover;') : false;
                    if(p_list){
                        p_list.innerHTML = "";
                        field.value = "";
                        for(let i=0;i<attachments.length;i++){
                            let each_url = attachments[i].url;
                            if(each_url){
                                p_list.innerHTML += '<em class="upload_previews" style="background:url('+each_url+') center center /cover;"></em>';
                                field.value += each_url+' , ';
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

    var switch_tab = document.querySelector(".switchTab"),
        blog_settings = document.querySelector(".wrap.settings"),
        scroll_record = 0;
    if(blog_settings){
        // 多选框同步逻辑
        const checkboxes = document.querySelectorAll('.checkbox');
        for(let i=0;i<checkboxes.length;i++){
            let eachbox = checkboxes[i],
                eachcheck = eachbox.querySelectorAll('input[type=checkbox]'),
                outputText = eachbox.parentNode.querySelector('input[type=text]');
            for(let j=0;j<eachcheck.length;j++){
                // let checkArray = [outputText.value];
                let outputTrim = outputText.value.replace(/\s*/g,""),  // clear all whitespace
                    outputEnds = outputTrim.substr(outputTrim.length-1, outputTrim.length);  // last chr
                outputEnds!=','&&outputTrim.length>0 ? outputText.value += ',' : false;  // support IE
                // !outputText.value.replace(/\s*/g,"").endsWith(',')&&outputText.value.length>0 ? outputText.value += ',' : false;  // check if endsWith ',' (not compatible ie)
                eachcheck[j].onchange=function(){
                    // checkArray.push(this.value+' , ');  console.log(checkArray);
                    let clearOutput = outputText.value.replace(/\s*/g,""),  // clear all whitespace
                        clearString = clearOutput.match(this.value+',') ? this.value+',' : this.value;
                    this.checked ? clearOutput += this.value+',' : clearOutput = clearOutput.replace(clearString, '');  // ',' replace logic
                    outputText.value = clearOutput;  // final output
                };
            }
        }
        // 即时更新 select 图片
        const select_images = document.querySelectorAll(".select_images"),
              select_mirror = document.querySelectorAll(".select_mirror");
        for(let i=0;i<select_images.length;i++){
            select_images[i].onchange=function(e){
                let image = this.parentNode.querySelector("img"),
                    preview = this.childNodes[this.selectedIndex].getAttribute("preview");
                preview ? image.src=preview : image.src=this.value;
            };
        }
        for(let i=0;i<select_mirror.length;i++){
            select_mirror[i].onchange=function(e){
                let image = this.parentNode.querySelector("img"),
                    value = this.getAttribute("parm");
                image.src = "";  // clear last-mirror cache https://img.2broear.com/images/loading_3.png
                value ? image.src=this.value+value : image.src=this.value;
            };
        }
        // 即时更新 select 选项
        const select_options = document.querySelectorAll(".select_options"),
              dynamic_opts = document.querySelectorAll(".dynamic_opts"),
              dynamic_comment = document.querySelector(".dynamic_comment"),
              dynamic_class = 'dynamic_optshow',
              dynamic_fn = function(t,c,e){
                for(let i=0;i<t.length;i++){
                    t[i].classList.remove(c);
                }
                if(e && e!=''){
                    dynamic_comment ? dynamic_comment.innerHTML = e : false;
                    let dynamic_all = document.querySelectorAll('tr.'+e);
                    for(let j=0;j<dynamic_all.length;j++){
                        dynamic_all[j].classList.add(c);
                    }
                }else{
                    dynamic_comment ? dynamic_comment.innerHTML = 'BaaS' : false;
                }
              };
        for(let i=0;i<select_options.length;i++){
            select_options[i].onchange=function(e){
                let tar = this.parentElement.parentElement.parentElement.nextElementSibling;
                while(tar){
                    if(!tar.classList.contains('child_option')){
                        // console.log(tar.previousElementSibling);
                        break;  //跳出循环
                    }else{
                        tar.classList.remove(dynamic_class);  // remove all optshow
                        // console.log(tar)
                        let optsval = this.value;
                        if(optsval && optsval!=''){
                            dynamic_comment ? dynamic_comment.innerHTML = optsval : false;
                            let dynamic_lock = document.querySelectorAll('tr.'+optsval);
                            for(let j=0;j<dynamic_lock.length;j++){
                                dynamic_lock[j].classList.add(dynamic_class);
                            }
                        }else{
                            dynamic_comment ? dynamic_comment.innerHTML = 'BaaS' : false;
                        }
                        // dynamic_fn(optshow, dynamic_class, optsval);
                        tar = tar.nextElementSibling;  // 继续查找，直到当前元素不含 child_option 类（即非该 checkbox 子项）
                    }
                }
            };
        }
        // 自动同步 checkbox 勾选框关联元素
        const check_boxes = document.querySelectorAll("label input[type=checkbox]"),  // inside label only
              dynamic_list = document.querySelectorAll(".dynamic_box.logo");
        for(let i=0;i<check_boxes.length;i++){
            check_boxes[i].onchange=function(e){
                let _checked = this.checked;
                // console.log(this.checked);
                let tar = this.parentElement.parentElement.parentElement.nextElementSibling;
                        // console.log(tar);
                while(tar){
                    if(!tar.classList.contains('child_option')){
                        // console.log(tar.previousElementSibling);
                        break;  //跳出循环
                    }else{
                        // console.log(tar); //当前元素
                        if(_checked){
                            !tar.classList.contains(dynamic_class) ? tar.classList.add(dynamic_class) : false;
                        }else{
                            tar.classList.remove(dynamic_class);
                        }
                        tar = tar.nextElementSibling;  // 继续查找，直到当前元素不含 child_option 类（即非该 checkbox 子项）
                        // console.log(tar); //当前元素后一个
                    }
                }
            };
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
        // scroll function
        window.onscroll = function(e){
            var windowHeight = window.innerHeight,
                clientHeight = document.body.clientHeight,
                scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            scroll_foward = window.pageYOffset;  //scroll Value
            if(scroll_record-scroll_foward<0){  //down
                if(scrollTop>=switch_tab.offsetTop){
                    switch_tab.classList.add("fixed");
                    document.querySelectorAll("p.submit")[0].style.right = "-80px";
                }
            }else{  //up
                if(scrollTop<=switch_tab.offsetTop*4){
                    switch_tab.classList.remove("fixed");
                    document.querySelectorAll("p.submit")[0].style.right = "";
                }
            }
            scroll_record = scroll_foward;  // Update scrolled value
        };
        
        // same options sync(identify by input id)
        const sync_data = [
            'site_leancloud_appid',
            'site_leancloud_appkey',
            'site_leancloud_server',
        ];
        for(let i=0;i<sync_data.length;i++){
            let each_list = document.querySelectorAll("input#"+sync_data[i]);
            for(let j=0;j<each_list.length;j++){
                each_list[j].oninput=function(e){
                    let each_data = document.querySelectorAll("input#"+this.id);
                    for(let k=0;k<each_data.length;k++){
                        each_data[k].value = this.value;  //cur_data.classList.add("sync");
                    }
                };
            }
        }
        // switch tabs & active class
        const activecls = "active",
              switchcls = "show",
              switchtab = document.querySelectorAll(".switchTab li"),
              clearClass = function(els,cls){
                  for(let i=0;i<els.length;i++){
                      els[i].classList.remove(cls);
                  }
              },
              formtable = document.querySelectorAll("form .formtable")[0];
        formtable ? formtable.classList.add(switchcls) : formtable;  // auto active first formtable
        for(let i=0;i<switchtab.length;i++){
            switchtab[i].onclick=function(){
                clearClass(switchtab,activecls);  // clear actived class
                this.classList.add(activecls);  // clearClass then active
                clearClass(document.querySelectorAll("form ."+switchcls),switchcls);  // upadted els while click
                document.querySelector("form ."+this.id).classList.add(switchcls);  // clearClass then show
            };
        }
    }
});
