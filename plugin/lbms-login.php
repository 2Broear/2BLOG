<?php
/*
 * Template name: LBMS（登录页面）
 * Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title> LBMS Login - Signup & Etc. </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="<?php custom_cdn_src('img'); ?>/images/favicon/lbms.ico"/>
    <style>
    .logedin,.welcome,
    html,body{
        height: 100%;
    }
body{background: whitesmoke;margin: 0;overflow: overlay;text-align: center;}
.show{display: block!important;opacity: 1;visibility: visible;}
.centre{height:auto;position: absolute;top: 50%;left: 50%;transform: translate(-50%,-50%);-webkit-transform: translate(-50%,-50%);}
@keyframes spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}
.signup #loading::before {background: black;}
.signup #loading::after {border-color: white;border-top-color: transparent;border-bottom-color: transparent;}
#loading:before {content: "";width: 100%;height: 100%;background: white;opacity: 0.66;display: block;border-radius: 10px;}
#loading{position: inherit;top: inherit;left: inherit;transform: inherit;display: block;width: 100%;height: 100%;}
#loading:after{-webkit-box-sizing:border-box;box-sizing:border-box;content:"";position:absolute;display:inline-block;top: 42%;left: 50%;margin-left:-20px;width:40px;height:40px;border: 6px double #2b2b2b;border-top-color:transparent;border-bottom-color:transparent;border-radius:50%;-webkit-animation:spin .75s infinite linear;animation:spin .75s infinite linear;z-index: 1;}
.signlog,.logedin{display: none;}
.logedin .welcome,.logedin .welcome #info{background: white;border-radius: 10px;}
.logedin .welcome iframe{border: 0;width: 100%;height: 100%;}
/* .logedin .welcome #info button{max-width: 36%;} */
.logedin .welcome #info{
    padding: 5%;
    display: block;
}
.logedin .welcomed #info::before{
    content: "×";
    position: absolute;
    top: 0;
    left: 15px;
    font-size: 36px;
    opacity: .5;
    cursor: pointer;
}
.logedin .welcomed #info.fold:hover::before{
    opacity: .88;
}
.logedin .welcomed #info.fold::before{
    content: "+";
    font-size: 45px;
    opacity: .66;
}
.logedin .welcomed #info.fold:hover{
    margin: 30px;
}
.logedin .welcomed #info.fold{
    cursor: pointer;
    bottom: -320px;
    right: -255px;
    border-top-left-radius: 50px;
}
.logedin .welcomed #info{
    width: 100%;
    padding: 55px 0;
    margin: 25px;
    max-width: 280px;
    border: 1px solid whitesmoke;
    border-radius: 10px;
    box-shadow: rgb(0 0 0 / 18%) 0px 0 18px;
    transition: all .35s ease;
    position: fixed;
    bottom: 0;
    right: 0;
}
.logedin .welcomed #info p small{
    display:block;
    margin-top:10px;
    opacity: .58;
}
.logedin .welcomed #info img{
    max-width: 30%;
    border-radius: 50px;
}
.logedin .welcomed #info button:focus{
    color: white;
    background: black;
}
.logedin .welcomed #info button{
    color: black;
    background: transparent;
    border: 2px solid currentColor;
}
.box.signup{
    background: #2b2b2b;
}
.box.forgot{
    box-shadow: none;
    background: transparent;
}
.box.forgot input{
    background: #eaeaea;
}
.box{
    width: 88%;
    max-width: 300px;
    padding: 20px 15px;
    margin: 0 auto;
    background: white;
    box-shadow: rgb(0 0 0 / 5%) 0px 0 10px;
    border-radius: 10px;
    box-sizing: border-box;
    display: none;
}
.box.signup h2,.box.signup p {color: whitesmoke;}
.box h2,.box p {
    margin: 0 auto;
    font-size: 27px;
    letter-spacing: 1;
}

.box input, button {
    display: block;
    width: 100%;
    line-height: 38px;
    border-radius: 50px;
    border: none;
}

.box .label {
    display: block;
    margin: 25px auto 15px auto;
}

.box input:focus{
    background: white;
    box-shadow: 0 0 0 5px rgb(0 0 0 / 10%)!important;
    border-color: black!important;
}
.box.signup input:focus{
    background: black;
    box-shadow: 0 0 0 5px rgb(255 255 255 / 5%)!important;
    border-color: white!important;
}
.box input.required {
    background: white;
    border-color: red;
    box-shadow: 0 0 0 5px rgb(255 0 0 / 15%);
}
.box input {
    margin: 15px auto;
    padding: 0 20px;
    background: whitesmoke;
    transition: all .15s ease;
    border: 1px solid transparent;
    outline: none;
    line-height: normal!important;
    padding: 15px;
    box-sizing: border-box;
}
.box.signup input {
    color: whitesmoke;
    background: #3a3a3a;
}

.box.login button:focus{
    box-shadow: 0 0 0 5px rgb(0 0 0 / 15%)!important;
}
.box.signup button:focus{
    box-shadow: 0 0 0 5px rgb(255 255 255 / 15%)!important;
}
button:hover {
    box-shadow: none!important;
    background: black;
    max-width: 52%;
}
button {
    display: inline-block;
    max-width: 58%;
    background: #2b2b2b;
    color: white;
    line-height: 42px;
    font-size: 16px;
    font-weight: bold;
    letter-spacing: 1px;
    margin: 15px auto 10px auto;
    box-shadow: 0 0 0 5px rgb(0 0 0 / 10%);
    cursor: pointer;
    transition: all .15s ease;
}
.box.signup button{
    color: #2b2b2b;
    background: whitesmoke;
    box-shadow: 0 0 0 5px rgb(255 255 255 / 10%);
}
.box p#desc:before {
    /* content: ""; */
    width: 100%;
    height: 1px;
    background: black;
    display: block;
    opacity: .12;
    position: absolute;
    top: 10px;
}
p#desc{
    font-weight: bold;
    opacity: .52;
}
.box p {
    font-size: .8rem;
    margin: 10px auto 15px auto;
    position: relative;
}


.box p a:hover{
    opacity: 1;
    font-weight: bold;
    text-decoration: underline;
}
.box p a {
    color: inherit;
    opacity: .66;
    text-decoration: none;
    border: none;
}
    </style>
</head>
<body>
<script src="<?php custom_cdn_src(false); ?>/js/leancloud/av-min.js"></script>
<div class="signlog">
    <div class="login box centre show">
        <h2>LBMS</h2>
        <p id="desc"><b> 即刻登入 LBMS 管理系统 </b></p>
        <div class="label">
            <!-- <form> -->
                <input id="name" type="text" placeholder="用户名或邮箱" />
                <input id="pswd" type="password" placeholder="账号密码" />
                <button id="loging"> 登 录 </button>
            <!-- </form> -->
        </div>
        <p>
            <a href="javascript:;" id="forgot"> 忘记密码 </a> &nbsp;|&nbsp;<a href="javascript:;" id="signup"> 注册账号 </a>
        </p>
    </div>
    <div class="signup box centre">
        <h2>LBMS</h2>
        <p id="desc"> 即时注册 LBMS 邮箱账号 </p>
        <div class="label">
            <input id="_name" type="text" placeholder="注册邮箱" />
            <input id="_pswd" type="password" placeholder="注册密码" />
            <button id="signing"> 注 册 </button>
        </div>
        <p>
            <a href="javascript:;" id="login"> 账号登录 </a> &nbsp;|&nbsp; <a href="javascript:;" id="forgot"> 忘记密码 </a>
        </p>
    </div>
    <div class="forgot box centre">
        <h2>L.BMS</h2>
        <p id="desc"> 通过邮箱找回 L.BMS 密码 </p>
        <div class="label">
            <input id="name_" type="text" placeholder="找回账号邮箱" />
            <!-- <input id="pswd_" type="text" placeholder="当前账号密码" /> -->
            <button id="forgetting"> 提 交 </button>
        </div>
        <p>
            <a href="javascript:;" id="login"> 账号登录 </a>  &nbsp;|&nbsp; <a href="javascript:;" id="signup"> 注册账号 </a>
        </p>
    </div>
</div>
<div class="logedin">
    <div class="welcome centre"><span id="info"></span></div>
</div>
<script>
    AV.init({
        appId: "<?php echo get_option('site_leancloud_appid') ?>",
        appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
		serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
    });
    const USER = new AV.User,
          ACL = new AV.ACL(),
        signlog = document.querySelector(".signlog"),
          boxes = signlog.querySelectorAll(".box"),
          inputs = signlog.querySelectorAll("input"),
          logedin = document.querySelector(".logedin"),
          welcome = logedin.querySelector(".welcome"),
        logbox = signlog.querySelector(".login"),
          name = logbox.querySelector("#name"),
          pswd = logbox.querySelector("#pswd"),
          loging = logbox.querySelector("#loging"),
        signbox = signlog.querySelector(".signup"),
          _name = signbox.querySelector("#_name"),
          _pswd = signbox.querySelector("#_pswd"),
          signing = signbox.querySelector("#signing"),
        forbox = signlog.querySelector(".forgot"),
          name_ = forbox.querySelector("#name_"),
        //   pswd_ = forbox.querySelector("#pswd_"),
          forgetting = forbox.querySelector("#forgetting"),
        signup = logbox.querySelector("#signup"),
        login = signbox.querySelector("#login"),
        emailReg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,
        dynamicLoad = function(url,fn){
			var _doc = document.getElementsByTagName('head')[0],
				script = document.createElement('script');
				script.setAttribute('type','text/javascript');
				script.setAttribute('async',true);
				script.setAttribute('src',url);
				_doc.appendChild(script);
			script.onload = script.onreadystatechange = function(){
				if(!this.readyState || this.readyState=='loaded' || this.readyState=='complete'){
					fn();
				}
				script.onload = script.onreadystatechange = null;
			}
		},
        loged=(user)=>{
            currentUser = user;  //currentUser for lbms first login verify
            signlog.classList.remove("show");
            logedin.classList.add("show");
            var uid = user.id,
                uname = user.attributes.username || "anonymous",
                umail = user.attributes.email,
                uverify = user.attributes.emailVerified,
                iframe = document.createElement("iframe");
            iframe.id = "postMessage";
            iframe.setAttribute("loading","lazy");  //lazyload iframe if support
            iframe.setAttribute("src","/lbms?v=3.0");  //https://lbms.2broear.com/
            if(uverify){
                console.log('VERIFIED')
                dynamicLoad('https://src.2broear.com/js/md5.min.js',function(){
                    welcome.querySelector("#info").innerHTML=`<img src="https://sdn.geekzu.org/avatar/${md5(umail)}?d=retro&s=100" /><p> <b>${uname}</b> <small>(${uid})</small></p><button id="logout"> 登 出 </button>`;
                });
                
                history.pushState(null,null,"/lbms");  //push back fake(sub-domain only)
                welcome.appendChild(iframe);  // iframe.innerHTML=xmlhttp.responseText;
                
                welcome.classList.remove("centre");
                welcome.classList.add("welcomed");
                tiemr = setTimeout(() => {
                    welcome.querySelector("#info").classList.add("fold");
                    clearTimeout(tiemr);
                }, 3000);
            }else{
                welcome.classList.remove("welcomed");
                welcome.querySelector("#info").innerHTML=`<h2 style="color:red"> 账户尚未验证 </h2><p>如已激活账号（${umail}）请退出登录后重试！</p><button id="logout"> 登出 </button>`;
                // alert(`emailVerified: ${uverify}`)
            }
        },
        resign=()=>{
            history.pushState(null,null,"/lbms-login");  //push forward
            signlog.classList.add("show");
            logedin.classList.remove("show")
        },
        loadbox = (box)=>{
            let loading = document.createElement("span");
                loading.id="loading";
            box.appendChild(loading);
        },
        isVaild = (i)=>{
            i.classList.remove("required");
            if(i.value.length<1||i.value==" "){
                let timer = setTimeout(() => {
                    i.classList.add("required");
                    clearTimeout(timer)
                }, 100);
            }
        },
        ifVaild = (ip,fn)=>{
            var fs = 0;
            for(let i=0;i<ip.length;i++){
                let ipv = ip[i];
                isVaild(ip[i]);
                ipv.value=="" ? fs++ : false
            };
            fs<1 ? fn() : false
        },
        loginAct = (t)=>{
            let tip = t.parentNode.parentNode.querySelectorAll("input");
            ifVaild(tip,function(){
                loadbox(logbox);
                const loading = logbox.querySelector("#loading");
                AV.User.logIn(name.value,pswd.value).then((user) => {
                    loading.remove();
                    loged(user);  // 登录成功跳转首页
                }, (err) => {
                    loading.remove();
                    alert("请检查账号密码是否拼写正确！");
                    console.warn(err);  // 登录失败（可能是密码错误）
                })
            })
        },
        signupAct = (t)=>{
            let tip = t.parentNode.parentNode.querySelectorAll("input");
            ifVaild(tip,function(){
                if(emailReg.test(_name.value)){
                    loadbox(signbox);
                    const loading = signbox.querySelector("#loading");
                    USER.setUsername(_name.value);
                    USER.setPassword(_pswd.value);
                    USER.setEmail(_name.value);  // USER.set("gender","secret");
                    USER.signUp().then((user) => {
                        alert(`用户 ${user.attributes.username} 注册成功！已发送账号激活链接至相应邮箱`);
                        // console.log(`SignUp Successful! (UserId: ${user.id})`);  // 注册成功
                        loged(user);  // 注册成功跳转首页
                        loading.remove();
                        AV.User.requestEmailVerify(user.attributes.email);  //验证邮箱
                    }, (err) => {
                        loading.remove();
                        alert("该用户名可能已被占用（检查域名是否加入 leancloud 白名单？）");
                        console.warn(err);  // 注册失败（通常是因为用户名已被使用）
                    })
                }else{
                    _name.focus();  // _name.select();
                    alert("错误的邮箱格式！")
                }
            })
        },
        forgotAct = (t)=>{
            let tp = t.parentNode.querySelectorAll("input");
            ifVaild(tp,function(){
                if(emailReg.test(name_.value)){
                    loadbox(forbox);
                    const loading = forbox.querySelector("#loading");
                    // AV.User.logIn(name_.value,pswd_.value).then((user) => {
                        loading.remove();
                        //account verify successed  // loged(user);  // 注册成功跳转首页
                        AV.User.requestPasswordReset(name_.value).then(() => {
                            loading.remove();
                            switchAct(login); //redirect to login
                        }, (err) => {
                            loading.remove();
                            console.warn(err)
                        });
                        alert(`重置密码链接已发送至 ${name_.value} 邮箱！`);
                    // }, (err) => {
                    //     loading.remove();
                    //     alert("验证失败！可能是账号或密码拼写错误")
                    //     //account verify failed
                    // })
                }else{
                    name_.focus();  // _name.select();
                    alert("错误的邮箱格式！")
                }
            })
        },
        switchAct = (t)=>{
            for(let i=0;i<boxes.length;i++){
                boxes[i].classList.remove("show")
            };
            signlog.querySelector(`.${t.id}`).classList.add("show")
        },
        switchRule = (t,i)=>{
            switch (i) {
                case "loging":
                    loginAct(t);
                    break;
                case "signing":
                    signupAct(t);
                    break;
                case "forgetting":
                    forgotAct(t);
                    break;
            }
        };
    var currentUser = AV.User.current();  //iframe never reads if use const.
    window.onload=function(){
        console.log(currentUser)
        console.log(JSON.stringify(currentUser))
        //send currentUser data to parent page via postMessage
        let pmsg = document.getElementById("postMessage");
        pmsg ? pmsg.contentWindow.postMessage(JSON.stringify(currentUser),"/") : console.warn('no postMessage');
    };
    for(let i=0;i<inputs.length;i++){
        let input = inputs[i];
        input.onblur=()=>{
            isVaild(input)
        }
    }
    signlog.onkeyup=(e)=>{
        var t = e.target || e.srcElement,
            tp = t.parentNode.parentNode.querySelector("button");
        while (t!=signlog) {
            if(t.nodeName.toLowerCase()=="input"){
                switch(e.which || e.keyCode){
                    case 13:
                        switchRule(t,tp.id);
                        break;
                };
                break;
            }else{
                t=t.parentNode
            }
        }
    }
    signlog.onclick=(e)=>{
        var t = e.target || e.srcElement;
        while (t!=signlog) {
            if(t.nodeName.toLowerCase()=="button"){
                switchRule(t,t.id);
                break;
            }else if(t.nodeName.toLowerCase()=="a"){
                switchAct(t);
                break;
            }else{
                t=t.parentNode
            }
        }
    }
    console.log(`currentUser: ${currentUser}`)
    if (currentUser) {
        loged(currentUser);  // 跳到首页
    } else {
        resign();  // 显示注册或登录页面
    }
    logedin.onclick=function(e){
        var t = e.target || e.srcElement;
        while (t!=logedin) {
            if(t.nodeName.toLowerCase()=="button"){
                let currentUser = AV.User.current(),
                    logoutcheck = confirm(`即将登出账户（${currentUser.attributes.username}），是否确定？`);
                if(logoutcheck){
                    window.document.title = "LBMS Login - Signup & Etc.";
                    AV.User.logOut();  // currentUser 变为 null
                    currentUser = AV.User.current();  //const
                    welcome.querySelector("#info").innerHTML="";
                    resign();
                };
                break;
            }else{
                switch(t.id){
                    case "info":
                        t.classList[0] ? t.classList.remove("fold") : t.classList.add("fold");
                        break;
                };
                t=t.parentNode;
            }
        }
    }
</script>
</body></html>