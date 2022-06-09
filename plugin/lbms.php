<?php
/*
 * Template name: LBMS（管理页面）
 * Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title> Leancloud Blog (Content) Management System </title>
    <meta name="keywords" content="Weblog,Submit,2BROEAR" />
    <meta name="description" content="Weblog Submit System" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="<?php custom_cdn_src(); ?>/images/favicon/lbms.ico"/>
    <link type="text/css" rel="stylesheet" href="https://2broear.com/style/highlight/agate.m.css" />
    <link type="text/css" rel="stylesheet" href="https://2broear.com/style/lbms/editor.css" />
    <script src="<?php custom_cdn_src(); ?>/js/leancloud/av-min.js"></script>
    <script src="<?php custom_cdn_src(); ?>/js/Marked.js/marked.min.js"></script>
    <script src="<?php custom_cdn_src(); ?>/js/highlight/highlight.pack.js"></script>
</head>
<body>
<style>
    :root{--radius:10px}
    ::selection {background-color:red;color:white}
    ::-moz-selection {background-color:red;color:white}
    body {padding:0; margin:0;font-family: "Microsoft YaHei","微软雅黑","Sans-Serif";}
    a {color:inherit;}
    h1 {margin: 15px auto;}
    h6 {margin: 15px auto auto;}
    ol,
    ul {text-align:left;}
    ol li,
    ul li {line-height:25px;}
    table {border-collapse:collapse;}
    table th,table td {border: 1px solid lightgray;padding: 10px 15px;}
    .sublog {text-align:center;box-sizing:border-box;}
    .sublog .require {border:2px dashed red;}
    .sublog input:focus,
    .sublog select:focus,
    .sublog textarea:focus,
    .sublog .article .mdbox #tabledit .editare,
    .sublog button:focus{background:white;border:2px solid black!important;box-shadow:0 0 0 5px rgb(0 0 0 / 10%)!important;}
    .sublog input,
    .sublog select,
    .sublog textarea,
    .sublog button {border:2px solid transparent;border-radius:var(--radius);box-sizing:border-box;outline:none;}
    .sublog input,
    .sublog textarea,
    .sublog select {width:100%;height:45px;padding:15px;border-color:transparent;background:#fafafa;}
    .sublog select,
    .sublog input[type=file] {padding: 10px 15px;}
    .sublog textarea.disabled {cursor: not-allowed;resize: none;background:whitesmoke;border: 1px solid lightgray;}
    .sublog textarea.invisible {opacity: 0;visibility: hidden;display: none;}
    .sublog textarea {resize:vertical;min-height:88px;}
    .sublog button:hover {color:white;background:black;}
    .sublog button {line-height:45px;background:white;margin:0 15px;padding:0 35px;border-radius:50px;border:1px solid gray;letter-spacing:3px;cursor:pointer;user-select: none;}
    .sublog .sup {width:100%;display: inline-block;font-size:14px;background: white;box-shadow: rgb(0 0 0 / 8%) 0px 18px 18px;/*position: fixed;z-index:9;top: 0;left: 0;*/}
    .sup .supbox{max-width:1102px;margin: 0 auto;}
    .sup span:hover,
    .sup span.active {color:white;background:black;}
    .sup span#markedJs:hover,
    .sup span#markedJs.active{color:white;background:red;border-color:red;}
    .sup span#markedJs{color:red;border-color:currentColor;}
    .sup span {display:inline-block;line-height:35px;margin:15px 5px 20px;padding:0 15px;border:2px solid black;cursor:pointer;user-select: none;-webkit-user-drag: element;/*border-radius:var(--radius);*/}
    .sublog .sub,
    .sublog .log {width:55%;margin:25px auto;text-align:left;display:inline-block;}
    .sublog .sub input,
    .sublog .sub select,
    .sublog .sub textarea{
        border-radius: unset;
    }
    .sublog .log h1{letter-spacing: normal;}
    .sub .add {white-space:nowrap;}
    .sub .add.plus .additional {width:32.5%;}
    .add .additional.switch_off{display: none;}
    .add .additional {width:49%;display:inline-block;margin-right: 10px;vertical-align: middle;}
    .add .additional h5 {font-size:1rem;opacity:.88;}
    .sub .tagbox #box {margin:15px auto auto;font-size:.75rem;}
    .tagbox #box span:before,
    .tagbox #box span:after {content:"";width:10px;height:2px;background:currentColor;position:absolute;top:10px;right:10px;}
    .tagbox #box span:before {transform:rotate(45deg);}
    .tagbox #box span:after {transform:rotate(-45deg);}
    .tagbox #box span:hover {color:white;background:black;}
    .tagbox #box span {min-height:30px;line-height:30px;display:inline-block;padding:0 25px 0 15px;margin-right:10px;border-radius:50px;border:2px solid black;position:relative;cursor:pointer;-webkit-user-drag: element;}
    .sub ul {list-style-type:none;}
    .sub ul li {margin:15px auto;}
    .sub .btns {text-align:center;margin:35px auto 15px;}
    .sublog .log {width:36%;margin:35px 35px 35px auto;font-size:small;max-height:888px;overflow:auto;float:right;}
    .log ol,ul {list-style-type:square;}
    .log #list {margin:55px auto;}
    .log p {margin:0 auto;}
    .log b{display: inline-block;font-size: 15px;/*margin-top: 5px;*/}
    /* .log p.flup input{width: 50%;display: inline-block;} */
    .log ins {text-align:right;}
    .log span{ width: 100%;display: inline-block;white-space: nowrap;margin-bottom:15px;padding:0 15px 15px;box-sizing: border-box;border:1px solid #eee;}
    .log span .edit h5:hover,.log span.unlocked .edit h5{color: white;background: black;}
    .log span .edit h5:hover::before,.log span .edit h5:hover::after,.log span.unlocked .edit h5::after{display: inline;background: red;padding:0 2px}
    .log span .edit h5:hover::before{background: white;color:black;}
    .log span .edit h5::before, .log span .edit h5::after{display: none;}
    .log span .edit h5::before{content: "UPDATE";}
    .log span .edit h5{color: black;padding:5px;cursor: pointer;border:2px solid black;}
    .log span .edit h5,
    .log span .edit .switcher,
    .log span.unlocked .edit button{display: inline-block;}
    .log span.unlocked .edit{margin-bottom: 25px;}
    .log span.unlocked .edit .action{display: block;padding-top: 15px;border-top: 1px solid whitesmoke;}
    .log span .edit .action{display: none;}
    .log span .edit button{display:none;line-height: normal;border: 2px solid;margin-left:0;letter-spacing: normal;box-shadow:0 0 0 5px rgb(0 0 0 / 10%)!important;}
    .log span .edit .switcher.inactive::before{color: black;right:auto;left:2px}
    .log span .edit .switcher.inactive{color: white;}
    .log span .edit .switcher::before{content: "";width: 25px;height: 25px;color:white;background: currentColor;border-radius: 50%;display: block;position: absolute;top: 2px;right: 2px;}
    .log span .edit button,.log span .edit .switcher,.log span .edit del{padding: 6px 15px;border-radius: 25px;}
    .log span .edit .switcher{
        color: black;
        border: 2px solid black;
        background: currentColor;
        position: relative;
        cursor: pointer;
    }
    .log span .edit i.del::before{
        content: "";
    }
    .log span .edit del:hover{
        color: white;
        background: red;
    }
    .log span .edit del{
        color:red;
        font-style: normal;
        border: 2px solid red;
        cursor: pointer;
    }
    .log span .edit b{ margin:auto 15px; }
    .log span .edit{position: relative;white-space: normal;}
    .log span aside{overflow: hidden;}
    .log span aside .option select{
        background: transparent;
        border: 1px solid #ccc;
        margin: 15px auto 5px;
        box-shadow: none!important;
        display: none;
    }
    .log span.unlocked .option select.changed{
        color: black;
        font-weight: 900;
        border:2px solid currentColor!important;
    }
    .log span.unlocked textarea,
    .log span.unlocked .option select{
        display: block;
        border-radius: unset;
    }
    .log span.unlocked textarea{
        resize: vertical!important;
    }
    .log span textarea{
        border-radius: unset;
    }
    .log span aside .flup input[type=text]{
        cursor: not-allowed;
    }
    .log span aside .flup input{
        pointer-events: none;
        user-select: none;
        user-zoom: none;
        -webkit-user-drag: none;
        cursor: progress;
        /* opacity: .75; */
    }
    .log span.unlocked aside .option input,
    .log span.unlocked aside .option input.hide{
        display: none;
    }
    textarea#markdown{background: whitesmoke;}
    .log span aside input[type=file]{padding-bottom: 35px;}
    .log span.unlocked aside input, .log span.unlocked aside textarea{pointer-events:all;}
    .log span aside input, .log span aside textarea{pointer-events: none;}
    .log span aside textarea:focus{box-shadow:none!important;}
    .log span aside textarea{display: block;margin:15px auto;min-height: 66px;resize: none;}
    .log span aside input{max-height: 35px;padding:0;background: transparent;font-style: italic;}
    .log span.unlocked textarea#markdown{border: 1px solid #ccc;background: white;}
    .log span.unlocked{border: 2px solid black;/*border-width: 2px;*/}
    .log span.unlocked .edit h5::before{display: none;}
    .log span.unlocked .edit h5::after{content: "UNLOCK";color:black;background:limegreen;}
    .log span.unlocked aside input:focus{color:black;font-weight:bold;font-size:initial;padding-left:10px;box-shadow: none!important;border:none!important;border-bottom: 2px solid currentColor!important;/*border-top: 2px solid transparent!important;*/}
    .log span.unlocked aside input{display:block;border:none;border-bottom: 1px dashed lightgray;border-radius: unset;transition: padding .15s ease;}
    .log span.unlocked aside p:last-child input{border:none}
    body.MDFocus {position: fixed;}
    .sublog .article .mdbox.ondrag::before,
    .sublog .article .mdbox.ondrag::after {
        content: "";
        background: black;
        position: absolute;
        top: 32%;
        left: 50%;
        transform: translate(-50%,-50%);
        z-index: 1;
        pointer-events: none;
    }
    .sublog .article .mdbox.ondrag::before {
        width: 4px;
        height: 50px;
    }
    .sublog .article .mdbox {position: relative;}
    .sublog .article .mdbox.ondrag #markdown {
        opacity: .55;
    }
    .sublog .article .mdbox.ondrag::after {
        width: 50px;
        height: 4px;
    }
    body.MDFocus .sublog .article .mdbox {
        width: 100%;
        height: 100%;
        text-align: center;
        padding: 4% 5% 2%;
        box-sizing: border-box;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 999;
        overflow: auto;
    }
    .sublog .article .mdbox #markdown {min-height: 233px;}
    body.MDFocus .sublog .article .mdbox .mdoutput,
    body.MDFocus .sublog .article .mdbox #markdown{
        width: 100%;
        height: 100%;
        /* min-height: 38%; */
        display: block;
        text-align: left;
        margin-top:25px!important;
        position: relative;
    }
    .sublog .article .mdbox .mdoutput{
        margin-top:25px;
        display: none;
    }
    body.MDFocus .sublog .article .mdbox .mdoutput #preview,
    body.MDFocus .sublog .article .mdbox .mdoutput #htmldom {
        width:49%;
        height: 100%;
        overflow: auto;
        background: whitesmoke;
    }
    body.MDFocus .sublog .article .mdbox .mdoutput #htmldom ul {
        list-style-type: square;
    }
    body.MDFocus .sublog .article .mdbox .mdoutput #htmldom img {
        max-width: 100%;
    }
    body.MDFocus .sublog .article .mdbox .mdoutput #preview {
        float: right;
    }
    body.MDFocus .sublog .article .mdbox .mdoutput,
    body.MDFocus .sublog .article .mdbox #toolbar,
    body.MDFocus .sublog .article .mdbox #masker,
    body.MDFocus .sublog .article .mdbox #tabledit{
        display: inline-block
    }
    .sublog .article .mdbox #toolbar {
        display: none;
        padding: 8px;
        background: white;
        border-radius:var(--radius);
        box-sizing: border-box;
        text-align: center;
        box-shadow: 0 0 0 5px rgb(0 0 0 / 3%)!important;
        position: relative;
    }
    .sublog .article .mdbox #toolbar span:hover {
        color: black;
        background: linear-gradient(white, whitesmoke);
        border: 1px solid currentColor;
        box-shadow: 0 0 0 5px rgb(0 0 0 / 10%);
    }
    .sublog .article .mdbox #toolbar span {
        color: gray;
        /* font-size: 14px; */
        padding: 7px 14px;
        margin: 5px;
        border: 1px solid lightgray;
        display: inline-block;
        border-radius: inherit;
        cursor: pointer;
        position: relative;
        vertical-align: top;
    }
    .sublog .article .mdbox #toolbar span#imgfile{
        overflow: hidden;
    }
    .mdbox #toolbar span#imgfile input[type='file'] {
        opacity: 0;
        padding: 0;
        max-height: 100%;
        border-radius: inherit;
        position: absolute;
        top: 0;
        left: -100%;
        cursor: inherit;
        width: auto;
    }
    .sublog .article .mdbox #esctip.popup {
        bottom: 0;
    }
    .sublog .article .mdbox #esctip {
        padding: 15px 35px 15px;
        box-sizing: border-box;
        background: white;
        margin: 0 auto;
        position: fixed;
        bottom: -150px;
        /* right: 10%; */
        left: 50%;
        transform: translate(-50%, 0);
        text-align: center;
        box-shadow: rgb(0 0 0 / 12%) 0 0 18px;
        transition: bottom .35s ease;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .sublog .article .mdbox #esctip h2 {
        margin: 0 auto;
        font-size: 1.25rem;
    }
    .sublog .article .mdbox #esctip p {
        font-size: 14px;
        margin: 10px auto;
    }
    .sublog .article .mdbox #esctip small {
        color: gray;
    }
    .sublog .article .mdbox #masker {
        content: "";
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgb(0 0 0 / 12%);
        margin: 0;
        backdrop-filter: blur(15px);
        /* z-index: -99;
        opacity: .32; */
    }
    .sublog .article #htmldom::before {
        /* content: "Marked Html-dom Previews. ( only on focus )"; */
        text-align: center;
        font-size: small;
        display: block;
    }
    .sublog .article #htmldom {
        width: 100%;
        height: 100%;
        font-size: 14px;
        padding: 15px;
        /* background: whitesmoke; */
        box-sizing: border-box;
        border-radius:var(--radius);
        float: left;
    }
    .sublog .article #htmldom small {opacity: .66;}
    #upload_progress {content:"";width:100%;height:100%;background:rgb(0 0 0 / 18%);display:block;}
    #upload_progress {z-index:999;width:100%;height:100%;position:fixed;top:0;left:0;}
    #upload_progress .tips {width:30%;height:30%;background:white;position:inherit;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;border-radius:var(--radius);transition:box-shadow .35s ease;box-shadow:0 1px 1px rgba(0,0,0,0.3),0 8px 0 -3px whitesmoke,0 9px 1px -3px rgba(0,0,0,0.3),0 16px 0 -6px whitesmoke,0 17px 2px -6px rgba(0,0,0,0.3);}
    #upload_progress.error .close,
    #upload_progress.success .close {opacity: unset;}
    #upload_progress.success .tips {box-shadow:none;}
    #upload_progress.success .tips .progress #percent {height: 100%;}
    #upload_progress.success .loader.done{color: limegreen;}
    #upload_progress.error .loader.done{color: orangered;}
    .tips h3 {margin:35px auto;letter-spacing: 1px;}
    .tips p {font-size:14px;}
    .tips p small{color: gray;}
    .tips span {display:block;}
    .tips .progress {width: 100%;height:100%;overflow: hidden;;position:absolute;bottom:0;border-radius: inherit;}
    .tips .progress #percent {height:0%;width:100%;position:inherit;bottom:0;background:rgb(0 0 0 / 4%);transition:height .5s ease;}
    .loader.done,
    .loader.done:before,
    .loader.done:after{animation: unset;-webkit-animation: unset;}
    .loader,
    .loader:before,
    .loader:after {background:currentColor;-webkit-animation:loading 1s infinite ease-in-out;animation:loading 1s infinite ease-in-out;width:8px;height:8px;border-radius:var(--radius);}
    .loader:before,
    .loader:after {position:absolute;top:0;content:'';}
    .loader:before {left:-18px;}
    .loader {color:black;text-indent:-9999em;margin:12% auto;position:relative;font-size:10px;-webkit-animation-delay:0.16s;animation-delay:0.16s;}
    .loader:after {left:18px;-webkit-animation-delay:0.32s;animation-delay:0.32s;}
    @keyframes loading {0%,80%,100% {box-shadow:0 0 currentColor;height:25px;}40% {box-shadow:0 -2em currentColor;height:45px;}}
    @keyframes spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}
    #loadbar{position:absolute;right:10%;top:15%}
    #loadbar:before{-webkit-box-sizing:border-box;box-sizing:border-box;content:"";position:absolute;display:inline-block;top:-5px;left:0;width:40px;height:40px;border:6px double #ccc;border-top-color:transparent!important;border-bottom-color:transparent!important;border-radius:50%;-webkit-animation:spin 1s infinite linear;animation:spin 1s infinite linear;transition: border-color .5s ease;}
    #loadbar.success:before{border-color: limegreen;animation-duration: 0.5s;}
    #loadbar.failure:before{border-color: red;animation-duration: 3s;}
    
    .tips .close:before {transform:translate(-50%,-50%) rotate(45deg)}
    .tips .close::after {transform:translate(-50%,-50%) rotate(-45deg)}
    .tips .close:before,
    .tips .close:after {
        content:"";
        width:58%;
        height:2px;
        background:white;
        position:inherit;
        top:50%;
        left:50%
    }
    .tips .close:hover {transform:rotate(-90deg);-webkit-transform:rotate(90deg)}
    .tips .close {opacity: 0;position:absolute;top:-20px;right:-20px;z-index:1;background:black;border:4px solid whitesmoke;border-radius:50%;padding:20px;cursor:pointer;transition:transform .35s ease}

    /* rewrite article textarea */

    body.MDFocus .sublog .article .mdbox {
        padding: 5%;
    }
    body.MDFocus .sublog .article .mdbox #markdown {
        margin: 0!important;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    body.MDFocus .sublog .article .mdbox #markdown,
    body.MDFocus .sublog .article .mdbox .mdoutput {
        height: 45%;
        /* min-height: 45%; */
    }
    .sublog .article .mdbox #toolbar {
        width: 100%;
        text-align: left;
        box-shadow: none!important;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom: 1px solid whitesmoke;
    }
    .sublog .article .mdbox #toolbar span {
        padding: 8px 12px;
        border-radius: 5px;
        border-color: whitesmoke;
    }
    .sublog .article .mdbox #toolbar span.func {
        float: right;
        border-color: lightgray;
    }

    
    .sublog .article .mdbox #tabledit {
        display: none;
        width: 58%;
        height: 58%;
        padding: 0 25px;
        background: white;
        border-radius:var(--radius);
        box-sizing: border-box;
        box-shadow: rgb(0 0 0 / 12%) 0px 0px 18px;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
    }
    .sublog .article .mdbox #tabledit .editare {
        margin-bottom: 15px;
        resize: none;
    }
    .sublog .article .mdbox #tabledit .editare,
    .sublog .article .mdbox #tabledit .editpre {
        height: 36%;
        overflow: auto;
        /* min-height: 32%; */
    }
    .sublog .article .mdbox #tabledit .editpre table{
        width: 100%;
    }
    .sublog .article .mdbox #tabledit .editact {
        width: 100%;
        position: inherit;
        bottom: 0;
        left: 0;
        margin: 20px auto;
    }
    .sublog .article .mdbox #tabledit .editact button {
        letter-spacing: normal;
        display: inline-block;
        border: 2px solid lightgray;
    }
</style>
<div class="sublog">
    <h1 style="letter-spacing: 5px;"><a href="#"> l.BMS </a></h1>
    <h6> Leancloud Blog (Content) Mangement System. </h6>
    <div class="sup"></div>
    <div class="sub"></div>
    <div class="log"></div>
</div>
<script>
    const Categories = {
             "news" : ["markdown","preview","title","author","desc","feel","type_news","tag","source","origin","others","mixed","img","bg"],
            //  "notes" : ["markdown","preview","title","src","desc","index","type_notes","bg"],
             "weblog" : ["title","content","ps","type_weblog","index","dates","tag"],
             "acg" : ["title","subtitle","desc","img","src","type_acg","rating","gs","ign"],
             "link" :  ["name","link","avatar","online","offline","sitelink","sex","desc","ssl","status","mark"],
             "download" : ["title","src","img","file","type_download"],
            //  "markedJs" : ["markdown","preview"],
             "inform" : ["title"],
            //  test:[] 相同类名的 select 需要区分类名防止与 Selections 冲突
          },
          Category = Object.keys(Categories),
          Selections = {
            "sex" : ["boy","girl"],
            "ssl" : ["https","http"],
            "mark" : ["friends","special"],
            "status" : ["standard","standby","missing"],
            "sitelink" : ["false","true"],
            // "pan" : ["disabled","enabled"],
            "rating" : ["disabled","ign","gs"],
            "source" : ["origin","others","mixed"],
            "avatar" : ["online","offline"],
            "type_news" : ["daily","record","feel","events","tech","share","tutorial","design","hardware"],
            "type_notes" : ["sundries","tech","design","frontend","backend","diary"],
            "type_acg" : ["anime","comic","game","movie","tv"],
            "type_weblog" : ["weblog","dairy","feeling"],
            "type_download" : ["soft","p2p","tool","tools","vpn","crack","media","adobe"]
          },
          Switchers = ["rating","source","avatar"],
          MDToolbars = {
              "bold" : {
                  "before" : " __", 
                  "text" : "strong text",
                  "icon" : "icon-bold",
                  "after" : "__ "
              },
              "italic" : {
                  "before" : " _",
                  "text" : "italic text",
                  "icon" : "icon-italic",
                  "after" : "_ "
              },
              "delete" : {
                  "before" : " ~~",
                  "text" : "delete text",
                  "icon" : "icon-strikethrough",
                  "after" : "~~ "
              },
              "underline" : {
                  "before" : " <u>",
                  "text" : "underline text",
                  "icon" : "icon-underline",
                  "after" : "</u> "
              },
              "ol" : {
                  "before" : "\n1. ",
                  "text" : "List item",
                  "icon" : "icon-list-numbered",
                  "after" : "\n"
              },
              "ul" : {
                  "before" : "\n- ",
                  "text" : "List item",
                  "icon" : "icon-list2",
                  "after" : "\n"
              },
              "h1" : {
                  "before" : "\n# ",
                  "text" : "title/h1",
                  "icon" : "icon-h1",
                  "after" : "\n"
              },
              "h2" : {
                  "before" : "\n## ",
                  "text" : "title/h2",
                  "icon" : "icon-h2",
                  "after" : "\n"
              },
              "h3" : {
                  "before" : "\n### ",
                  "text" : "title/h3",
                  "icon" : "icon-h3",
                  "after" : "\n"
              },
              "code" : {
                  "before" : " `",
                  "text" : "code text",
                  "icon" : "icon-embed",
                  "after" : "` "
              },
              "codes" : {
                  "before" : "\n``` autolang\n",
                  "text" : "enter code here",
                  "icon" : "icon-embed2",
                  "after" : "\n```\n"
              },
              "blockquote" : {
                  "before" : "\n> ",
                  "text" : "Blockquote",
                  "icon" : "icon-quotes-right",
                  "after" : "\n"
              },
              "table" : {
                  "before" : `\n| th right | th center | th right |\n| :-- | :-: | --: |`,
                  "text" : "",
                  "icon" : "icon-table2",
                  "after" : `\n| td1 | td2 | td3 |\n| td4 | td5 | td6 |\n`
              },
              "hyperlink" : {
                  "before" : "",
                  "text" : "enter hyperlink description here",
                  "icon" : "icon-link",
                  "after" : ""
              },
              "imgsrc" : {
                  "before" : "",
                  "text" : "enter imgsrc description here",
                  "icon" : "icon-attachment func",
                  "after" : ""
              },
              "imgfile" : {
                  "before" : "",
                  "text" : "",
                  "icon" : "icon-file-picture func",
                  "after" : ""
              }
          },
          now = new Date(),
          day = ("0" + now.getDate()).slice(-2),
          month = ("0" + (now.getMonth() + 1)).slice(-2),
          year = now.getFullYear(),
          fulldate = year + "-" + month + "-" + day,
          today = {
              "d" : day,
              "m" : month,
              "y" : year
          },
          dateFormat=(fmt, date)=>{
            let ret;
            const opt = {
                    "Y+": date.getFullYear().toString(),        // 年
                    "m+": (date.getMonth() + 1).toString(),     // 月
                    "d+": date.getDate().toString(),            // 日
                    "H+": date.getHours().toString(),           // 时
                    "M+": date.getMinutes().toString(),         // 分
                    "S+": date.getSeconds().toString()          // 秒
                };
            for (let k in opt) {
                ret = new RegExp("(" + k + ")").exec(fmt);
                if (ret) {
                    fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
                };
            };
            return fmt;
        },
        forArr=(arr,exe)=>{
            if(arr!=null && arr!=undefined)
            for(let i=0;i<arr.length;i++){
                exe!=undefined ? exe(i) : false;
            }
        },
        replaceAll = function(repStr,fromArr,toArr){
            for(let i=0;i<fromArr.length;i++){
                repStr = repStr.split(fromArr[i]).join(toArr[i])
            };
            return repStr
        },
        selectSwitch=(selectArr,chooseArr)=>{
            forArr(chooseArr,function(i){
                let eachCho = chooseArr[i],  //"rating","from"
                    eachSel = selectArr[eachCho],  //arrary in "rating","from" from selectArr
                    select = document.querySelector("#"+chooseArr[i]),  //选项卡数组
                    sval = select ? select.value : false,  //选项卡默认值
                    spar = select ? select.parentNode.parentNode : false,  //选项卡 switch 父级
                    sinp = spar ? spar.getElementsByTagName('input') : false,  //父级 switch 内所有选项卡
                    cls = "switch_off",  //隐藏效果 class
                    initSel = ()=>{
                        forArr(eachSel,function(i){
                            let eachSels = eachSel[i];
                            sinp[eachSels] ? sinp[eachSels].parentNode.classList.add(cls) : false
                        })
                    };
                initSel();  //默认隐藏所有选项卡
                sinp[sval] ? sinp[sval].parentNode.classList.remove(cls) : false;  //默认显示当前选项卡
                //选项卡切换函数
                if(select){
                    select.onchange=(t)=>{
                        initSel();  //隐藏所有选项卡
                        let curval = t.target.value;  //改变后的值
                        sinp[curval].parentNode.classList.remove(cls)  //取消隐藏符合改变后的值的选项卡
                    }
                }
            })
        },
        tagCheck = function(curTab){
            curDom = document.querySelector(`.sub.${curTab} .${curTab}`);  //tagCheck() 时更新 curTab 索引
            var tag = curDom.querySelector("#tag"),
                box = curDom.querySelector("#box");
            if(tag && box){
                // eval("var tagArr_"+curTab+"=[],tagArr=tagArr_"+curTab)
                tagArr = [];  //设定为全局变量以调用提交
                autohold = "输入后按回车 enter 创建标签";
                var tspan = box.childNodes,
                    tagHold = function(arr){
                        arr.length<=0 ? tag.placeholder=autohold : tag.placeholder = " 当前标签 || "+arr[arr.length-1]+" || "+autohold
                    },
                    tagInit = function(tag,arr){
                        box.innerHTML = "";
                        forArr(arr,function(i){
                            let tagspan = document.createElement("span");
                            tagspan.innerText=arr[i];
                            box.appendChild(tagspan);
                            tagHold(tagArr);  //每次按下松开时判断 tag 数量并设置默认 placeholder
                        })
                    },
                    tagPush = function(){
                        if(tspan.length>=1){
                            tagArr = [];
                            forArr(tspan,function(i){
                                tagArr.push(tspan[i].innerText)
                            })
                        }
                    },
                    pushRes = tagPush(),
                    tagDel = function(){
                        //切换 tab 时检测是否存在 tag 并 push 到 tagArr，否则切换 tab 默认重置 tagArr
                        var deleteable;
                        return function(){
                            deleteable ? clearTimeout(deleteable) : deleteable;
                            deleteable = setTimeout(function(){
                                // console.log(tag.value.length)
                                if(tag.value==""){
                                    tagArr.pop();  //删除最后一个返回剩余数组
                                    tspan.length ? tspan[tspan.length-1].remove() : false;
                                    // tagHold(tagArr);  //松开时检测 tag 数量并设置对应的 placeholder
                                    tagDel()()
                                    // tagArr.length<=0 ? tag.placeholder=autohold : tag.placeholder = " 当前标签 || "+tagArr[tagArr.length-1]+" || "+autohold
                                }
                            },300)
                        }
                    },
                    delRes = tagDel();
                    
                tagSave = function(){
                    const max = 5;
                    if(tag.value!="" && tagArr.length<max){
                        pushRes;  //tagPush()
                        tagArr.push(tag.value);  //添加输入信息到数组
                        tag.value = "";  //清空 tag 输入信息
                        tagInit(tag,tagArr);
                    }else if(tagArr.length>=max){
                        alert(`最多仅可上传 ${max} 个标签！`);
                        tag.value = "";
                    }
                };

                // tag.addEventListener("keyup",function(){
                    //使用 addEventListener 事件处理器会导致重复绑定事件（触发多次执行时间内函数 bug）
                    //https://blog.csdn.net/weixin_38883338/article/details/107816537
                    //https://blog.csdn.net/lm278858445/article/details/81707921
                //});
                
                //使用 on 代替 addEventListener 事件处理器
                var tagCount=0;
                // tag.oninput = function(){
                //     tag.value=tag.value.replace(/\s+/g,"");  //过滤空格字符串（safari下输入拼音自动失去焦点bug！）
                // };
                tag.onkeyup = function(event){
                    tag.value.length<=0 ? tagCount-- : tagCount=0;  //当前仅剩一个标签时额外增加一次删除按下次数
                    switch(event.which || event.keyCode){
                        case 13:  //enter keycode
                            tag.value==""||tag.value.match(/^[ ]*$/) ? (alert("未输入任何标签！"),tag.value="") : tagSave();
                            break;
                        case 8:  //backspace keycode
                            tagCount<-1 ? tagDel()() : false;//tagCount<-1||tagArr.length>1 ? tagDel()() : false;  //判断仅剩标签删除逻辑
                            break;
                    }
                };
            }
        },
        fileCheck = function(_this,fileArr,max,fn){
            if(fileArr){
                forArr(fileArr,function(i){
                    let file = fileArr[i];
                    if(file){
                        let fname = file.name,
                            fextend = fname.substring(fname.lastIndexOf('.')),
                            fsize = ((file.size/1024)/1024).toFixed(2),
                            regimg = /jpe|jpg|jpeg|png|gif|svg|webp|apng|bmp|ico/
                            regzip = /zip|rar|7z|zipx|tar|gz|jar|apk|iso|img|bin/;
                        console.log(file.type+" ,"+/application\/\w+/.test(file.type))
                        if(regzip.test(fextend)||/image\/\w+/.test(file.type)){
                            let maxfile=30;
                            if(fsize>max&&regimg.test(fextend)){
                                alert(`最大支持 ${max}mb 大小图片上传！（当前图片大小：${fsize}mb）`);
                                _this ? _this.value = null : _this;
                            }else if(fsize>maxfile&&regzip.test(fextend)){
                                alert(`当前最大支持 ${maxfile}mb 大小文件上传！（文件大小：${fsize}mb）`);
                                _this ? _this.value = null : _this;
                            }else{
                                fn(i)  //传参 i 作为 fileArr 下标
                            }
                        }else{
                            alert(`非法文件！（当前不支持 ${fextend} 文件类型上传）`);
                            _this ? _this.value = null : _this;
                            return false;  
                        }
                    }
                })
            }
        },
        uploadCheck = function(curDom,curFile,tarea,callback){
            var inputs;  //alert("This input does not support the file that you have choosed! NOT,SUPPORT!!")
            forArr(tabArr,function(i){
                let each = tabArr[i];
                inputs = curDom.querySelector("#"+each);
                inputs && inputs.type=="file" ? fileVaild(inputs,1) : false;
            });
            function fileVaild(inputs,max){
                inputs.onchange = function(){
                    var that = this,
                        fileArr = [];
                    for(let i=0;i<this.files.length;i++){
                        fileArr.push(this.files[i])
                    };
                    console.log(fileArr);  //alert("checking file..")
                    fileCheck(this,fileArr,max,function(i){  //传入 i 遍历
                        // callback 传参 tarea,fileArr``(点击上传时使用 fileArr[i] 则无需在 processsFile 解析内判断（返回 Failed to execute 'readAsDataURL' on 'FileReader': parameter 1 is not of type 'Blob')
                        that.parentNode.id=="imgfile" ? (callback(tarea,fileArr[i]),that.value=null) : false;
                    })
                }
            };
            curFile ? fileVaild(curFile,3) : false;
        },
        choosed = Object.keys(Categories)[0],
        style = document.createElement("style"),
        sup = document.querySelector(".sup"),
        sub = document.querySelector(".sub"),
        log = document.querySelector(".log"),
        currentUser = parent.currentUser;

    sub.onclick=function(e){
        let t=e.target;
        while (t!=sub) {
            if(t.nodeName.toLowerCase()=="input"){
                if(t.id!="tag"){
                    t.onblur=function(){
                        t.value.length<1||t.value=="" ? t.classList.add("require") : t.classList.remove("require")
                    }
                };
                break
            }else if(t.parentNode.id=="box"){
                t.remove();
                let tagIndex = tagArr.indexOf(t.innerText);  //定位当前标签在数组内位置
                    tagArr.splice(tagIndex,1);  //移除当前标签数组
                tagHold(tagArr);  //点击判断 tag 数量并获取最后数组标签或设置默认 placeholder
                tag.focus();  //删除时聚焦
                break
            }else{
                t=t.parentNode
            }
        }
    };

window.addEventListener('message',function(event){
    console.log(event);
    let data = JSON.parse(event.data);
    currentData = data;
    // alert(`getMessage ${data} from ${event.origin}`)
});
if (currentUser) {
    parent.window.document.title = `Welcome ${currentUser.attributes.username} , LBMS is all Ready!`;// window.location.href=window.location.origin;  //redirect to lbms
    console.log(`${currentUser.attributes.username}(${currentUser.id}) has Loged in LBMS.`);
    for(key in Categories){
        sup.innerHTML += `<span id="${key}">${key}</span>`;
        sub.innerHTML += `<div class="${key}"></div>`;
        // if(key.match("global")){
        //     //console.log(key)
        //     for(k in Categories[key]){
        //         // console.log(k)
        //         forArr(Categories[key][k],function(i){
        //             //console.log(Categories[key][k][i])
        //         })
        //     }
        // };
        //动态添加内联样式
        style.innerText += '.'+key+'{display:none}';
        style.innerText += '.sub.'+key+' .'+key+'{display:block}';
    };
    document.head.appendChild(style);  //添加指定样式
    sub.innerHTML += `<div class="btns"><button class="submit"> 数据提交 </button></div>`;
    var news_box = document.querySelector(".news"),
          notes_box = document.querySelector(".notes"),
          acg_box = document.querySelector(".acg"),
          weblog_box = document.querySelector(".weblog"),
          link_box = document.querySelector(".link"),
          download_box = document.querySelector(".download"),
          markedJs_box = document.querySelector(".markedJs"),
          inform_box = document.querySelector(".inform"),
        update_s = function(dates){
            if(dates.length>=1){
                forArr(dates,function(i){
                    dates[i].value = fulldate;
                })
            }
        };
    news_box ? news_box.innerHTML += `<div class="article"><h3> News Article </h3><div class="mdbox"><span id="masker"></span><div id="toolbar"></div><textarea id="markdown"type="text"placeholder="News Article Markdown Resource"></textarea><div class="mdoutput"><textarea id="preview"class="disabled"disabled="true"type="text"placeholder="Markdown Documents Output Data"></textarea><span id="htmldom"><small> Marked Html Dom Elements Previews </small></span></div></div></div><div class="add"><span class="additional"><h5>Title</h5><input id="title"type="text"placeholder="标题"/></span><span class="additional"><h5>Author</h5><input id="author"type="text"placeholder="作者"/></span><span class="additional"></div><h3>Content</h3><ul><li><textarea id="desc"type="text"placeholder="简述"></textarea></li><li><input id="feel"type="text"placeholder="吐槽" /></li></ul><h3>Tags</h3><div class="tagbox"><input id="tag"type="text"placeholder="输入后按回车 enter 创建标签"/><div id="box"></div></div><div class="add switch"><span class="additional"><h5>Source</h5><select id="source"></select></span><span class="additional"><h5>Origin</h5><input id="origin"type="text"placeholder="原创链接"/></span><span class="additional"><h5>Others</h5><input id="others"type="text"placeholder="转载链接"/></span><span class="additional"><h5>Mixed</h5><input id="mixed"type="text"placeholder="二次创作"/></span></div><div class="add plus"><span class="additional"><h5>Type</h5><select id="type_news"></select></span><span class="additional"><h5>Image</h5><input id="img"type="file"accept="image/*"placeholder="图片"/></span><span class="additional"><h5>Background</h5><input id="bg"type="file"accept="image/*"placeholder="背景图"/></span></span></div>`:false;
    notes_box ? notes_box.innerHTML += `<div class="article"><h3>Notes Article</h3><div class="mdbox"><span id="masker"></span><div id="toolbar"></div><textarea id="markdown"type="text"placeholder="Notes Article Markdown Resource"></textarea><div class="mdoutput"><textarea id="preview"class="disabled"disabled="true"type="text"placeholder="Markdown Documents Output Data"></textarea><span id="htmldom"><small> Marked Html Dom Elements Previews </small></span></div></div></div><div class="add"><span class="additional"><h5>Title</h5><input id="title"type="text"placeholder="标题"/></span><span class="additional"><h5>Source</h5><input id="src"type="text"placeholder="链接"/></span></div><h3>Content</h3><textarea id="desc"type="text"placeholder="简述"></textarea><h3>Background</h3><input id="bg"type="file"accept="image/*"placeholder="背景图"/><div class="add"><span class="additional"><h5>Type</h5><select id="type_notes"></select></span><span class="additional"><h5>index</h5><input id="index"type="number"placeholder="排序"value="0"/></span><span class="additional"></div>`:false;
    acg_box ? acg_box.innerHTML += `<div class="add"><span class="additional"><h5>Title</h5><input id="title"type="text"placeholder="标题"/></span><span class="additional"><h5>Subtitle</h5><input id="subtitle"type="text"placeholder="副标题"/></span><span class="additional"></div><div class="add"><span class="additional"><h5>Src</h5><input id="src"type="text"placeholder="链接"/></span><span class="additional"><h5>Type</h5><select id="type_acg"></select></span></div><h3>Description</h3><textarea id="desc"type="text"placeholder="简述"></textarea><h3>Image</h3><input id="img"type="file"accept="image/*"placeholder="图片"/><div class="add switch"><span class="additional"><h5>Ratings</h5><select id="rating"></select></span><span class="additional"><h5>GameSpot</h5><input id="gs"type="number"placeholder="GameSpot 评分"/></span><span class="additional"><h5>IGN</h5><input id="ign"type="number"placeholder="IGN 评分"/></span><span class="additional"><h5>Rating Disabled</h5><input id="disabled"type="number"placeholder="disabled"disabled/></span></div>`:false;
    weblog_box ? weblog_box.innerHTML += `<h3>Title</h3><input id="title"type="text"placeholder="标题"/><h3>Content</h3><ul><li><textarea id="content"type="text"placeholder="主要内容"></textarea></li><li><textarea id="ps"type="text"placeholder="备注"></textarea></li></ul><h3>Tags</h3><div class="tagbox"><input id="tag"type="text"placeholder="输入后按回车 enter 创建标签"/><div id="box"></div></div><div class="add"><span class="additional"><h5>Type</h5><select id="type_weblog"></select></span><span class="additional"><h5>Index</h5><input id="index"type="number"value="0"/></span></div><h3>Date</h3><input id="dates"type="date"/>`:false;
    link_box ? link_box.innerHTML += `<div class="add"><span class="additional"><h5>Name</h5><input id="name"type="text"placeholder="昵称"/></span><span class="additional"><h5>Sex</h5><select id="sex"></select></span></div><div class="add"><span class="additional"><h5>Link</h5><input id="link"type="text"placeholder="链接"/></span><span class="additional"><h5>Sitelink</h5><select id="sitelink"></select></span></div><div class="add switch"><span class="additional"><h5>Avatar</h5><select id="avatar"></select></span><span class="additional"><h5>Online Src</h5><input id="online"type="text"placeholder="头像链接"/></span><span class="additional"><h5>Offline File</h5><input id="offline"type="file"accept="image/*"placeholder="头像图片"/></span></div><h3>Description</h3><textarea id="desc"type="text"placeholder="个人描述"></textarea><div class="add plus"><span class="additional"><h5>SSL(Https)</h5><select id="ssl"></select></span><span class="additional"><h5>Status</h5><select id="status"></select></span><span class="additional"><h5>Mark</h5><select id="mark"></select></span></div>`:false;
    download_box ? download_box.innerHTML += `
    <h3>Title</h3>
    <input id="title" type="text" placeholder="标题" />
<div class="add">
  <span class="additional">
    <h5>Source</h5>
    <input id="src" type="text" placeholder="链接" />
  </span>
  <span class="additional">
    <h5>Type</h5>
    <select id="type_download"></select>
  </span>
</div>
<div class="add">
  <span class="additional">
    <h5>Image</h5>
    <input id="img" type="file" accept="image/*" placeholder="图片" />
  </span>
  <span class="additional">
    <h5>File</h5>
    <input id="file" type="file" accept=".zip,.rar,.7z,.zipx,.tar,.gz,.jar,.apk,.iso,.img,.bin" placeholder="背景图" />
  </span>
</div>`:flase;
    markedJs_box ? markedJs_box.innerHTML += `<div class="article"><h3>Markdown</h3><div class="mdbox"><span id="masker"></span><div id="toolbar"></div><textarea id="markdown"type="text"placeholder="Notes Article Markdown Resource"></textarea><div class="mdoutput"><!--<h3>Htmldom</h3>--><textarea id="preview"class="disabled"disabled="true"type="text"placeholder="Markdown Documents Output Data"></textarea><span id="htmldom"><small> Marked Html Dom Elements Previews </small></span></div></div></div>`:false;
    inform_box ? inform_box.innerHTML += `<h3>Title</h3><input id="title" type="text" placeholder="标题" />`:false;
    for(key in MDToolbars){
        let text = MDToolbars[key].text,
            icon = MDToolbars[key].icon,
            eachBar= document.querySelectorAll(".article .mdbox #toolbar");
        forArr(eachBar,function(i){
            if(key=="imgfile"){
                eachBar[i].innerHTML += `<span id="${key}" class="${icon}" title="${key}"><input id="uploader" type="file" accept="image/*" multiple /></span>`;
            }else{
                eachBar[i].innerHTML += `<span id="${key}" class="${icon}" title="${key}"></span>`;
            }
        })
    };
    //..
    sub.classList.add(choosed);
    sup.firstChild.classList.add("active");
    //const 常量无法被赋值改变
    var curTab = choosed,  //默认选中 tab
        curDom = document.querySelector(`.sub.${curTab} .${curTab}`),  //默认选中 dom 索引
        tempTab = [],  //临时 tab 数组
        tabArr = Categories[choosed],  //默认 tab 数组
        initTab = (tabs)=>{
            console.warn("displaced fn: initTab")
            /*forArr(tabs,function(i){
                let each = tabs[i],
                    selected = document.querySelector(".sup .active").innerText,
                    inputs = curDom.querySelector(`#${each}`);
                if(inputs && inputs.id!="tag" && inputs.id!="feel"){
                    inputs.onblur=function(){
                        inputs.value.length<1 ? inputs.classList.add("require") : inputs.classList.remove("require")
                    }
                }else{
                    inputs.onblur=function(){
                        inputs.value.length>=1 ? tagSave() : false;
                    }
                }
            })*/
        };
    for(key in Selections){
        let keys = Selections[key];
        forArr(keys,function(i){
            // let each_key = document.getElementById(key);
            // each_key += `<option value="${keys[i]}">${keys[i]}</option>`;
            document.getElementById(key) ? document.getElementById(key).innerHTML += `<option value="${keys[i]}">${keys[i]}</option>` : false;
            // each_key ? each_key += `<option value="${keys[i]}">${keys[i]}</option>` : each_key;
        })
    };
    // uploadCheck(curDom);  //上传文件合法检测
    //渲染可选项（Selections）后执行选项卡切换函数（selectSwitch）
    selectSwitch(Selections,Switchers);
    forArr(Category,function(i){
        var queryTab = function(){
                var debounce;
                return function(){
                    debounce ? clearTimeout(debounce) : debounce;
                    debounce = setTimeout(function(){
                        QueryAll(curTab);  //查询切换后的 class 类
                        for(key in Categories){
                            if(key.match("global")){
                                // for(k in Categories[key]){
                                //     console.log(k)
                                //     forArr(Categories[key][k],function(i){
                                //         console.log(Categories[key][k][i])
                                //     })
                                // }
                            }else{
                                tempTab=[];  //切换为 curTab 前清空数组
                                forArr(Categories[key],function(i){
                                    tempTab.push(Categories[curTab][i]);  //载入匹配 curTab 的数组
                                    switch(curTab){
                                        case key:
                                            tabArr = tempTab
                                            break;
                                        default:
                                            tabArr = tabArr
                                            break;
                                    }
                                })
                            }
                        };
                        console.log(tabArr);
                        uploadCheck(curDom);  //更新 tabArr 后（切换 tab）遍历检测所有 type 为 file 的 input 的文件合法性
                        update_s(curDom.querySelectorAll("input[type=date]"));  // 更新当前选项卡页面内所有日期为今天
                        // initTab(tabArr);  //this fn no longer required.
                    },500)
                };
            },
            queryRes = queryTab();
        tabs = document.querySelectorAll(".sup span");
        tabs[i].onclick=()=>{
            selected = tabs[i].innerText;
            curTab = selected;  //切换到当前 class 类查询
            queryRes();
            forArr(Category,function(i){
                tabs[i].classList.remove('active');
            });
            tabs[i].classList.add('active');
            sub.setAttribute('class','sub '+ selected);
            //在queryRes()/sub设置完 class 后再执行以下操作, 否则 curDom 返回 null
            // curDom = document.querySelector(`.sub.${curTab} .${curTab}`);  //切换 tabArr 后更新 curDom 索引值
            // uploadCheck(curDom);  //切换 tab 后上传文件合法检测（此处遍历有延迟，无法即时获取最新 tabArr）
            tagCheck(curTab);  //检查当前选项卡内标签
            runMarked(curTab);  //打印 marked.js 预览（如果有）
        }
    });
    tagCheck(curTab);  //初始化 tab 时查询默认页面 tag
    //不能使用 choosed 初始化因为后期 push 点击函数获取的是 selected 的初始化值
    initTab(tabArr)
    console.log(tabArr)
    
    // //init app
    AV.init({
        appId: "<?php echo get_option('site_leancloud_appid') ?>",
        appKey: "<?php echo get_option('site_leancloud_appkey') ?>",
		serverURLs: "<?php echo get_option('site_leancloud_server') ?>"
    });

    //push AV.Query Fn
    function push(){
        selected = document.querySelector(".sup span.active").innerText;
        curTab = selected;  //每次提交更新当前已选 tab
        curTab = AV.Object.extend(curTab);  //查找或创建当前 tab 指定 class（此时 curTab 已不再是 string 类型，后续直接使用 selected）
        var demo = new curTab(),  //每次 push 新数据都新建 demo 防止提交新数据变为更新已提交数据
            file_id_arr = [],  //所有 type 为 file 的 input 数组合集
            empty = 0,  //emptyArr = [],
            inputs,
            each;
        forArr(tabArr,function(i){
            each = tabArr[i];
            inputs = curDom.querySelector("#"+each);
            //使用 offsetParent 判断 inputs 是否可见（选择可见且未输入的 id 不为 tag 的 inputs）
            if(inputs.offsetParent != null && inputs.id!="tag"){  // && inputs.id!="feel" && inputs.id!="file"
                inputs.classList.remove("require");
                if(inputs.value==""){
                    empty++;  //emptyArr.push(inputs);
                    inputs.classList.add("require")
                }else{
                    demo.set(each,inputs.value)
                }
            }
        });
        empty>=1 ? console.warn(`${empty} fields remaining to load.`) : console.log('fullloaded! ready to upload..');  //console.log(emptyArr)
        //仅储存一次不为空的 inputs 数据到云端
        if(empty<=0){
            window.onbeforeunload = function(){
                return `"Any string value here forces a dialog box to \n "appear before closing the window."`
            };
            demo.set('today',today);
            curDom.querySelector(".tagbox") ? demo.add('tag',tagArr) : false;  // 判断当前选项卡是否存在tag（tag标签无数据，重复写入 tagArr 值到云端）
            var fileRemain = 0,  //files count
                fileUpload = 1,  //reamin files 
                fileCount = 0,  //all files count
                fileCount = 0;  //article file count
            document.querySelector("button.submit").setAttribute("disabled","true");
            var demoClear = function(){
                    forArr(tabArr,function(i){
                        document.querySelector("button.submit").removeAttribute("disabled");
                        curTab = selected;
                        each = tabArr[i];
                        inputs = curDom.querySelector("#"+each);
                        //清空除下拉选项外的所有输入框
                        inputs.type!='select-one' && inputs.type!='number' ? inputs.value = "" : false;
                        curDom.querySelector(".article #htmldom") ? curDom.querySelector(".article #htmldom").innerHTML = "" : false;
                        //通过判断 inputs 的 id 清空标签会导致 input 返回错误的 false/true id
                        let tagbox = curDom.querySelector(".tagbox");
                        if(tagbox || tagArr.length>=1){
                            tagArr = [];
                            tagbox.querySelector("#tag").placeholder=autohold;
                            tagbox.querySelector("#box").innerHTML="";
                        }
                    })
                },
                throttle = function(callback,delay){
                    var timer = null;
                    return function(){
                        if(timer==null){
                            timer = setTimeout(function(){
                                callback();
                                timer = null
                            },delay)
                        }
                    }
                },
                demoSave = function(){
                    demo.save().then((res) => {
                        tipsSuccess();
                        QueryAll(selected);
                        // window.onbeforeunload = null;
                    }, (error) => {
                        alert("请检查网络后重试！（已保留当前输入框内容）");
                        tips.remove();  // demoClear();
                        document.querySelector("button.submit").removeAttribute("disabled");
                        console.error(error);
                    })
                },
                tipsRemove = function(timeout){
                    let timer;
                    timer ? clearTimeout(timer) : timer;
                    timer = setTimeout(function(){
                        tips ? tips.remove() : false;
                        demoClear()
                    },timeout);
                    tips_close.onclick=function(){
                        clearTimeout(timer);
                        tips.remove();
                        demoClear()
                    };
                },
                tipsReplace = function(status,title,text,delay){
                    tips.classList.add(status);
                    tips_loader.classList.add("done");
                    tips_title.innerText = title;
                    tips_text.innerHTML = text;
                    tipsRemove(delay);
                },
                tipsSuccess = function(){
                    tipsReplace("success","✅ 已成功上传数据 ✅","<p>当前页面的所有数据均已成功上传至服务器！</p><p><small>（3秒后自动关闭提示）</small></p>",3500)
                },
                tipsError = function(){
                    tipsReplace("error","❌ 数据上传失败 ❌","<p>可能是文件无法被读取，或上传过程中出现问题</p><p><small>（5秒后自动关闭提示）</small></p>",5000)
                },
                tips = document.createElement("div");
            tips.id = "upload_progress";
            tips.innerHTML = `<div class="tips"><h3>正在上传数据中，请等待</h3><span class="loader"></span><p>（上传进度响应中..）</p><div class="progress"><span id="percent"></span></div><span class="close"></span></div>`;
            document.body.appendChild(tips);
            const  tips_title = document.querySelector(".tips h3"),
                   tips_text = document.querySelector(".tips p"),
                   tips_loader = document.querySelector(".tips .loader"),
                   tips_percent = document.querySelector(".tips .progress #percent"),
                   tips_close = document.querySelector(".tips .close"),
                   markdown = curDom.querySelector("#markdown");
            forArr(tabArr,function(i){
                each = tabArr[i];
                inputs = curDom.querySelector("#"+each);
                if(inputs.id=="preview"){
                    let markblob = markdown.value.match(/\!\[.*?\(blob.*?\"\)/g),  //匹配已选中的 markblob 且包含 "blob" 值的图片链接//.match(/blob.*? /g),
                        articleData = inputs.value.match(/data.*?"/g); //data:image/png;base64
                        //filesName = inputs.value.match(/\[.*?\]/g);  //精准匹配 (/\[([\s\S]*?)\]/)[1]
                    if(articleData && articleData.length>=1){
                        forArr(articleData,function(i){
                            fileRemain++;  //count cur files
                            fileCount++;  //fileCount++;  //count article file
                            const mbi = markblob[i],
                                  fblob = mbi.match(/blob.*? /g)[0],  //（同时存在文本链接/图片外链/本地图片时过滤其他图片 fname 文件名）
                                  fname = mbi.match(/\[.*?\]/g)[0].replace('[','').replace(']',''),  //filesName[i].replace('[','').replace(']',''),  //Invalid key name ：
                                  fdata = articleData[i].replace('"',''),
                                  newfile = new AV.File(fname, {base64: fdata}),
                                  files = [];
                            console.log(fname)
                            newfile.metaData('file_blob',fblob);
                            //延迟循环发送异步请求，降低发送频率防止同一时间请求过多返回 429 too many requests 错误
                            setTimeout(() => {
                                newfile.save({
                                    keepFileName: true,
                                    onprogress: (progress) => {
                                        let percent = progress.percent,
                                            loaded = progress.loaded,
                                            total = progress.total,
                                            formartSize = (size)=>{
                                                return (size/1024).toFixed(2)
                                            };
                                        console.log(formartSize(loaded)+"kb / "+formartSize(total)+"kb");
                                        if(tips){
                                            tips_text.innerText = `${formartSize(loaded)}kb / ${formartSize(total)}kb（${fileUpload} / ${fileCount+fileCount}）`;
                                            tips_percent.setAttribute("style","height:"+percent+"%")
                                        }
                                    }
                                }).then((res) => {
                                    fileRemain--;  //count save successed files
                                    fileUpload++;  //record remain files for upload
                                    console.log(`上传文章图片数据，还剩 ${fileRemain} 个文件未上传！`);
                                    demo.add('attachments', res);  // attachments 是一个 Array 属性
                                    files.push(res);
                                    forArr(files,function(i){
                                        let eachFile = files[i].attributes,
                                            eachData = eachFile.metaData;
                                        markdown.value = markdown.value.replace(eachData.file_blob,eachFile.url);
                                        console.log(markdown.value)
                                        demo.set("markdown",markdown.value);  //在循环中即更新返回的图片url
                                    });
                                    fileRemain<=0 ? (demoSave(),console.log(`文章图片数据已全部上传！`)) : console.log(`正在上传文章图片数据，还剩 ${fileRemain} 个文件未上传！`)
                                }, (error) => {
                                    console.error(error)
                                    tipsError();
                                })
                            }, i*500)  //i跟随请求数量变化而变化
                        })
                    }
                };
                //选择当前页面所有可见的 file 上传框
                if(inputs.type=="file" && inputs.offsetParent!=null){
                    fileRemain++;  //count cur files
                    fileCount++;  //count all files
                    file_id_arr.push(inputs.id);  //获取 type 为 file 的 input 的 id 写入数组
                    if(inputs.files.length) {
                        const localFile = inputs.files[0],
                                fileName = localFile.name,
                                newfile = new AV.File(fileName, localFile),
                                files = [];
                        forArr(file_id_arr,function(i){
                            newfile.metaData('file_id',file_id_arr[i]);
                        });
                        setTimeout(function(){
                            newfile.save({
                                keepFileName: true,
                                onprogress: (progress) => {
                                    let percent = progress.percent,
                                        loaded = progress.loaded,
                                        total = progress.total,
                                        formartSize = (size)=>{
                                            return (size/1024).toFixed(2)
                                        };
                                    console.log(formartSize(loaded)+"kb / "+formartSize(total)+"kb");
                                    if(tips){
                                        tips_text.innerText = `${formartSize(loaded)}kb / ${formartSize(total)}kb（${fileUpload} / ${fileCount+fileCount}）`;
                                        tips_percent.setAttribute("style","height:"+percent+"%")
                                    }
                                }
                            }).then((res) => {
                                fileRemain--;  //count save successed files
                                fileUpload++;  //record remain files for upload
                                // inputs.value = "";  //清空 file 上传框
                                demo.addUnique('file_id_arr', file_id_arr);  //addUnique 添加唯一属性（过滤相同属性）
                                demo.add('attachments', res);  // attachments 是一个 Array 属性
                                files.push(res);
                                forArr(files,function(i){
                                    let eachFile = files[i].attributes,
                                        eachData = eachFile.metaData;
                                    demo.set(eachData.file_id,eachFile.url);
                                    console.log(eachFile.url)
                                });
                                fileRemain<=0 ? (demoSave(),console.log(`页面图片数据已全部上传！`)) : console.log(`正在上传页面图片数据，还剩 ${fileRemain} 个文件未上传！`)
                            }, (error) => {
                                document.querySelector("button.submit").removeAttribute("disabled");
                                console.error(error)
                                tipsError();
                            })
                        },i*100)
                    }
                }
            });
            //匹配当前页面是否存在文件上传
            fileRemain<=0 ? (demoSave(),console.log(`页面所有数据已全部上传完成！`)) : console.log(`正在上传所有页面数据，还剩 ${fileRemain} 个文件未上传！`)
        }
    };
    //闭包防抖连续点击时等待 1s 后再执行最后一次点击
    var debounce = function(){
        let timer;
        return function closure(){
            timer ? clearTimeout(timer) : timer;
            timer = setTimeout(function(){
                push();
            },1000)
        }
    },
    res = debounce();
    document.querySelector("button.submit").addEventListener('click',res);
    //request AV.Query
    function QueryAll(curTab){
        log.innerHTML = '';  //清空数据列表
        const query = new AV.Query(curTab);
        query.include('attachments').addDescending("index").addDescending("createdAt").limit(999).find().then(result=>{
            const demos = result[0];
            for (let i=0; i<result.length;i++){
                let res = result[i],
                    objId = res.id,
                    files = res.attributes.attachments,
                    files_id = res.attributes.file_id_arr,
                    box = document.createElement("span"),
                    eachVal;
                box.id = objId;
                box.innerHTML += `<div class="edit"><h5 title="UPDATE DATA"> ${objId} </h5><div class="action"><del class="del"> Delete </del><b> ACL </b><div class="switcher">visible</div><b> PUSH </b><button id="repush"> Submit </button></div></div><aside></aside>`;
                let hdata = box.querySelectorAll("h5"),
                    sdata = box.querySelectorAll(".switcher"),
                    ddata = box.querySelectorAll(".del"),
                    getNode = function(that){
                        return that.parentNode.parentNode.parentNode
                    },
                    popout = function(id,fn,that){
                        that.parentNode.previousSibling.removeAttribute("id");
                        let cfm = confirm(`是否更新 ${id} 内容至云端？`);
                        cfm ? (fn(),that.parentNode.previousSibling.removeAttribute("id")) : that.removeAttribute("disabled");
                    };
                forArr(ddata,function(i){
                    ddata[i].onclick=function(){
                        let tp = this.parentNode.parentNode.parentNode, //三级
                            upd = AV.Object.createWithoutData(curTab, tp.id),
                            cfm = confirm(`确认删除 ${tp.id} 吗？（注：此操作不可逆）`);
                        cfm ? (tp.remove(),upd.destroy()) : false;
                    }
                });
                forArr(hdata,function(i){
                    hdata[i].onclick=function(){
                        let tp = this.parentNode.parentNode;
                        if(this.id){
                            tp.classList.remove("unlocked");
                            this.removeAttribute("id");
                            this.setAttribute("title","UPDATE DATA")
                        }else{
                            tp.classList.add("unlocked");
                            this.id="on";
                            this.setAttribute("title","CANCEL UPDATA")
                        }
                    }
                });
                forArr(sdata,function(i){
                    sdata[i].onclick=function(){
                        var acl = new AV.ACL(),
                            tid = getNode(this).id,  //三级
                            upd = AV.Object.createWithoutData(curTab, tid),
                            that = this;
                        acl.setPublicWriteAccess(true);
                        if(this.id){
                            acl.setPublicReadAccess(true);
                            this.removeAttribute("id");
                            this.classList.remove("inactive");
                        }else{
                            this.id="on";
                            this.classList.add("inactive");
                            let delay = setTimeout(function(){
                                    if(confirm(`设置 ${tid} 可读性（ACL访问控制，仅管理员（${currentUser.attributes.username}），是否继续）？`)){
                                        // acl.setPublicReadAccess(currentUser,true);
                                        acl.setReadAccess(AV.User.current(), true);
                                        upd.setACL(acl);
                                        upd.save()
                                    }else{
                                        that.classList.remove("inactive");
                                        that.removeAttribute("id");
                                        acl.setPublicReadAccess(true);
                                        upd.setACL(acl);
                                        upd.save()
                                    };
                                    clearTimeout(delay)
                                },100);
                        };
                        // demos.setACL(acl);
                        upd.setACL(acl);
                        upd.save().then((res) => {
                            console.log("ACL Update Successful!")
                        }, (error) => {
                            console.log("ACL Update Failed!")
                        })
                    }
                });
                box.querySelector("#repush").onclick=function(){
                    let that = this,
                        tp = getNode(that),  //三级
                        tid = tp.id,
                        logUpd = AV.Object.createWithoutData(curTab, tid),
                        aside = tp.querySelector("aside").childNodes,
                        loadbar = document.createElement("em"),
                        cfm = confirm(`是否更新 ${tid} 内容至云端？`),
                        // flupOpts = tp.querySelectorAll(".flup select"),  //tp replaced box
                        flupFile = tp.querySelectorAll(".flup input[type=file]"),  //tp replaced box
                        flupText = tp.querySelectorAll(".flup input[type=text]"),  //tp replaced box
                        logSuccess = function(that){
                            loadbar.classList.add("success");
                            let success = setTimeout(function(){
                                tp.classList.remove("unlocked");
                                that.removeAttribute("disabled");
                                loadbar.remove();
                                clearTimeout(success)
                            }, 500)
                        },
                        logFailure = function(error){
                            console.error(error);
                            loadbar.classList.add("failure");
                            let failure = setTimeout(function(){
                                loadbar.remove();
                                clearTimeout(failure)
                            }, 3000)
                        },
                        logSaving = function(){
                            //每次更新 logUpd 值都需要 set 一遍当前值再 save
                            forArr(aside,function(i){
                                let key = aside[i].children[0].innerText,
                                    val = aside[i].children[1].value;
                                console.log(key+" : "+val);
                                key=="tag" ? val=val.split(",") : val=val;
                                // for(keys in Selections){
                                //     if(key!=keys){
                                //         logUpd.set(key,val)
                                //     }
                                // }
                                logUpd.set(key,val)
                            });
                            logUpd.save().then(() => {
                                console.log("Updating Success!");
                                logSuccess(that)
                            }, (error) => {
                                console.error("Updating Failure!");
                                logFailure(error)
                            })
                        };
                    loadbar.id="loadbar";
                    // popout(tid,update,this);
                    if(cfm){
                        this.parentNode.previousSibling.removeAttribute("id");  //仅确认上传后关闭面板
                        this.setAttribute("disabled","true");
                        this.parentNode.appendChild(loadbar);
                        if(flupFile[0]){  //flupFile 始终存在，但 length 为 0
                            for(let i=0;i<flupFile.length;i++){  //循环每个flupFile再执行 logSaving（循环执行）
                                let file = flupFile[i],
                                    text = flupText[i];
                                if(file.files.length) {
                                    console.log(`fileList: [${i}] detected, Uploading data fileList..`)
                                    const localFile = file.files[0],
                                          fileName = localFile.name,
                                          newFile = new AV.File(fileName,localFile);
                                    newFile.save().then((file) => {
                                        console.log("File uploaded, Processing reload..");
                                        text.value = file.attributes.url;
                                        logSaving()
                                    }, (error) => {
                                        console.warn(error);// 保存失败，可能是文件无法被读取，或者上传过程中出现问题
                                    })
                                }else{
                                    console.log(`fileList: [${i}] ${file.files[0]}, Uploading other data..`);  // console.log("No specific fileList updates required, Uploading data directly..");
                                    logSaving();
                                }
                            }
                        }else{
                            //默认（文本）通用储存执行逻辑
                            logSaving();
                            this.removeAttribute("disabled");
                            console.log("No fileList updates required, Uploading data directly..");
                        }
                    }
                };
                // console.log(res)
                let selectStr = JSON.stringify(Selections);
                forArr(tabArr,function(i){
                    let tabName = tabArr[i],
                        eachVal = eval('res.attributes.'+tabName);
                    let typeSwitch = function(tp,md,up,op,sw){
                        let ip = `<p id="${tabName}"><b>${tabName}</b> : <input type="${tp}" value='${eachVal}' /></p>`,
                            ta = `<p><b>${tabName}</b> : <textarea id="${tabName}">${eachVal}</textarea></p>`,
                            fl = `<div class="flup"><b><u>${tabName}</u></b> : <input type="text" value="${eachVal}" disabled /><input type="${tp}" title="更新当前文件链接" /></div>`,
                            sl = `<div class="option" id="${tabName}"><b>${tabName}</b> : <input type="text" value="${eachVal}" /><select id="${tabName}" selected="${eachVal}"></select></div>`;  //tabName用作logSave的key值不可随意更改，如[]
                        if(md){
                            box.lastChild.innerHTML += ta;  //textarea文本框
                        }else if(up){
                            box.lastChild.innerHTML += fl;  //文件上传框
                        }else if(op){
                            box.lastChild.innerHTML += sl;  //下拉选项框
                            sw ? box.lastChild.innerHTML+=`
                            <p><b>GameSpot</b> : <input id="gs" type="number" placeholder="GameSpot 评分"></p><p><b>GameSpot</b> : <input id="gs" type="number" placeholder="GameSpot 评分"></p>
                            <p><b>IGN</b> : <input id="ign" type="number" placeholder="IGN 评分"></p><p><b>IGN</b> : <input id="ign" type="number" placeholder="IGN 评分"></p>
                            ` : false;  //selectSwitch
                        }else{
                            box.lastChild.innerHTML += ip;  //默认文本框
                        }
                    };
                    if(eachVal!=undefined && eachVal!=""){
                        switch (tabArr[i]) {
                            case "dates":
                                typeSwitch("date")
                                break;
                            case "index":
                                typeSwitch("number")
                                break;
                            case "bg":case "img":case "file":
                                typeSwitch("file",false,true)
                                break;
                            // case "sex":case "ssl":
                            //     typeSwitch(false,false,false,true)
                            //     break;
                            case "markdown":case "desc":case "content":case "ps":
                                typeSwitch(false,true)
                                break;
                            default:
                                for(key in Selections){
                                    if(tabName==key){
                                        typeSwitch(false,false,false,true)
                                    }else if(tabName=="rating"){
                                        //typeSwitch(false,false,false,true,true)
                                    }
                                    // tabName==key ? typeSwitch(false,false,false,true) : false;
                                };
                                typeSwitch("text")  //nextSibling
                                break;
                        };
                        // if(tabName=="dates"){
                        //     typeSwitch("date")
                        // }else if(tabName=="index"){
                        //     typeSwitch("number")
                        // }else if(tabName=="markdown"){
                        //     typeSwitch(false,true)
                        // }else if(tabName=="img"||tabName=="bg"){
                        //     typeSwitch("file",false,true)
                        // }else if(selectStr.indexOf(tabName)!=-1){
                        //     //typeSwitch(false,false,false,true)
                        // }else{
                        //     typeSwitch("text");
                        //     // for(key in Selections){
                        //     //     tabName==key ? typeSwitch(false,false,false,true) : false;
                        //     // }
                        // };
                    };
                    log.appendChild(box);  // log.innerHTML = '';  //此处清空导致输出不全
                });
                //注册更新编辑事件
                let idata = box.querySelectorAll("input"),
                    tdata = box.querySelector("textarea#markdown");
                if(tdata){
                    var pasteText;
                    tdata.onmouseenter=function(){
                        this.select();
                        document.execCommand("Copy");  //pasteText=this.value; 
                    };
                    // tdata.onmouseup=function(){
                    //     MDOpen(pasteText);  // document.execCommand("Paste");
                    // }
                }else if(idata){
                    forArr(idata,function(i){
                        idata[i].onchange=function(){
                            console.log(getNode(this).id)
                        }
                    })
                }
            };
            //循环外执行，加载选项卡并为其注册事件
            const options = log.querySelectorAll(".option select"),
                  flupText = log.querySelectorAll(".flup input[type=text]");
            // if(flupText[0]){
            //     for(let i=0;i<flupText.length;i++){
            //         console.log(flupText[i])
            //         flupText[i].onclick=function(){
            //             console.log(this)
            //         }
            //     }
            // };
            if(options[0]){
                for(let i=0;i<options.length;i++){
                    let eachOpts = options[i],
                        optsId = eachOpts.id,
                        dataInput = eachOpts.parentNode.querySelector("input[type=text]"),
                        sameInput = eachOpts.parentNode.parentNode.querySelector("p#"+optsId),
                        selList = eval('Selections.'+optsId);
                    dataInput.classList.add("hide");
                    sameInput.remove();  //classList.add("hide");（删除暂时无法排除的selects的input选项）
                    eachOpts.onchange=function(){
                        dataInput.value=eachOpts.value;
                        this.value!=this.getAttribute("selected") ? this.classList.add("changed") : this.classList.remove("changed");
                    };
                    if(selList){
                        for(let j=0;j<selList.length;j++){
                            // eachOpts.innerHTML += `<option value="${selList[j]}">${selList[j]}</option>`;
                            if(selList[j]==eachOpts.getAttribute("selected")){
                                eachOpts.innerHTML += `<option value="${selList[j]}" selected="selected">${selList[j]}</option>`;
                            }else{
                                eachOpts.innerHTML += `<option value="${selList[j]}">${selList[j]}</option>`;
                            }
                        }
                    }
                }
            }
        })
    }QueryAll(curTab);

    //选项卡子级切换功能触发事件
    if(document.querySelector("select#type_acg")){
        const linkevent = new Event("change"),
            typelink = document.querySelector("select#type_acg"),
            linktype = document.querySelector("select#rating");
        typelink.onchange=function(){
            if(this.value=="game"){
                linktype[1].selected=true;
                linktype.dispatchEvent(linkevent);
            }else{
                linktype[0].selected=true;
                linktype.dispatchEvent(linkevent);
            }
        }
    };

    //lbms markdown editor logic..

    hljs.initHighlightingOnLoad();
    var rendererMD = new marked.Renderer();
    marked.setOptions({
      renderer: rendererMD,
      langPrefix: "hljs ",
      highlight: function (code) {
        return hljs.highlightAuto(code).value;
      },
      gfm: true,
      tables: true,
      breaks: false,
      pedantic: false,
      sanitize: false,
      smartLists: true,
      smartypants: false
    });
    const renderTitle = rendererMD.heading = function (text, level) {
          var escapedText = text.toLowerCase().replace(/[^\w]+/g, '-');
          return `<h${level}> ${text} </h${level}>`;
          },
          renderImage = rendererMD.image = function (href, title, text) {
              return `<a data-fancybox="gallery" href="${href}" name="${title}" target="blank">
    <img class="lazy" data-original src="${href}" alt="${title}" />
</a>`;
          },
          renderLink = rendererMD.link = function (href, title, text) {
              return `<a href="${href}" title="${text}" target="blank"> ${text} </a>`;
          };
    marked.use({ renderTitle,renderImage,renderLink });
    // Run marked
    var runMarked = function(curTab){
        const article = curDom.querySelector(".article");
        if(article){
            var masker = article.querySelector("#masker"),
                mdbox = article.querySelector(".mdbox"),
                mdoutput = mdbox.querySelector(".mdoutput"),
                toolbar = mdbox.querySelector("#toolbar"),
                imgfile = toolbar.querySelector("#imgfile #uploader"),
                markdown = mdbox.querySelector("#markdown"),
                htmldom = mdoutput.querySelector("#htmldom"),
                preview = mdoutput.querySelector("#preview"),
                listParent = function(cur,cls){
                    while(cur.parentNode.classList[0]!=cls){
                        cur = cur.parentNode
                    };
                    return cur
                },
                markedTip = function(){
                    var timer,
                        tip = document.createElement("div");
                    tip.id = "esctip";
                    tip.innerHTML = "<h2>MD 文档编辑</h2><p>[ 当前正处于 Markdown ↓ 文档编辑模式 ]</p><p><small>按下<b>“ESC”</b>键或点击空白处退出</small></p>";
                    document.getElementById(tip.id)==null ? mdbox.appendChild(tip) : false;
                    setTimeout(() => {
                        tip.classList.add("popup")
                    }, 500);
                    timer ? clearTimeout(timer) : timer;
                    timer = setTimeout(function(){
                        tip.classList.remove("popup")
                    }, 5000)
                },
                keysup = function(e){
                    switch(e.which || e.keyCode){
                        case 27:  //ESC
                            esctip.remove();
                            document.body.classList.remove("MDFocus");
                            window.removeEventListener('keyup',keysuped);
                            break;
                        case 9:  //TAB
                            markdown.focus();
                            markdown.value += "\n";  //"  "
                            break;
                    }
                },
                keysuped = function(e){
                    return keysup(e)  //返回带参闭包函数
                },
                focusArea = function(t){
                    t.focus();
                    t.scrollTop = t.scrollHeight
                },
                insertRepos = function(text,before,after){
                    let tval = text.value,
                        start = text.selectionStart,
                        end = text.selectionEnd,
                        sval = tval.substring(start,end);
                    return tval.substring(0,start)+before+sval+after+tval.substring(end,tval.length)
                },
                insertLink = function(tarea,text,linkinfo,linkval,desctext,type){
                    let linkpop = prompt(linkinfo,linkval),
                        link = linkpop ? linkpop : false;
                    if(link){  //canceled by default
                        let descpop = prompt(`${desctext}（${text}）`,text),
                            desc = descpop ? descpop : false;
                        if(desc){
                            switch(type){
                                case "hyperlink":
                                    tarea.value += `\n[${desc}](${link||'javascript:;'})\n`;
                                    break;
                                case "imgsrc":
                                    tarea.value += `\n![${desc}](${link} "${desc}")\n`;
                            }
                        }
                    }
                },
                insertTable = function(tarea,before,text,after){
                    const col = 3,
                          row = 3,
                          table = document.createElement("div");
                    table.id = "tabledit";
                    table.innerHTML = "<h2> Table Insert </h2><textarea class='editare' type='text'></textarea><div class='editpre'contenteditable='false'></div><div class='editact'><button id='col'> Insert COL </button><button id='row'> Insert ROW </button><button id='con'> Confirm </button><button id='can'> Cancel </button></div>";
                    !document.getElementById(table.id) ? tarea.parentNode.appendChild(table) : false;
                    const sarea = tarea.parentNode.querySelector(".editare"),
                          stext = tarea.parentNode.querySelector(".editpre"),
                          editact = tarea.parentNode.querySelector(".editact"),
                          colspan = editact.querySelector("#col"),
                          rowspan = editact.querySelector("#row"),
                          confirm = editact.querySelector("#con"),
                          cancel = editact.querySelector("#can"),
                          editpre = insertRepos(sarea,before+text,after),
                          random = Math.random().toString().substr(2,1),  //默认生成 3 位数避免 colspan 匹配到重复项;
                          initable = `| : |\n| :-: |\n`;
                    sarea.value = editpre;
                    sarea.focus();
                    stext.innerHTML = marked(editpre);
                    sarea.oninput = function(){
                        focusArea(this);
                        stext.innerHTML = marked(this.value)
                    };
                    colspan.onclick=function(){
                        let tableline = sarea.value.split("\n"),
                            rs = Math.random().toString().substr(2,3);  //随机生成 3 位数避免 colspan 匹配到重复项;
                        if(tableline[1]){
                            forArr(tableline,function(i){
                                if(tableline[i]!=""){
                                    let eachline = tableline[i];
                                    sarea.value = sarea.value.replace(eachline,eachline+" : |");  //.replace(tableline[2]," : ")
                                }
                            });
                        }else{
                            sarea.value += initable;  // alert('Please at least insert 1 row !')
                        }
                        stext.innerHTML = marked(sarea.value);
                        sarea.focus()
                    };
                    rowspan.onclick=function(){
                        var nl = "",
                            rs = Math.random().toString().substr(2,3);  //随机生成 3 位数避免 colspan 匹配到重复项;
                        if(sarea.value.split("\n")[1]){
                            let lc = sarea.value.split("\n")[1].match(/\|/g);
                            for(let i=0;i<lc.length-1;i++){
                                nl+="| "+i+"-"+rs;  // newline.replace(/\|\|/g,"|")
                            };
                            sarea.value += nl+" |\n";
                        }else{
                            sarea.value += initable;
                        }
                        stext.innerHTML = marked(sarea.value);
                        sarea.focus()
                    };
                    confirm.onclick = function(){
                        tarea.value += sarea.value+'\n';
                        tarea.dispatchEvent(oninput);
                        tarea.focus();
                        tabledit.remove();
                    };
                    cancel.onclick = function(){
                        editact.parentNode.remove();
                        tarea.focus()
                    }
                },
                BlobaseImg = function(blob,base){
                    this.blob = blob;
                    this.base = base;
                },
                ProcessFile = function(tarea,file){
                    bloBaseArr = [];  //闭包构造数组对象
                    return function(tarea,file){
                        console.log(file)
                        var freader = new FileReader(), 
                            fname = file.name;
                        // freader.readAsDataURL(file); 如果无法直接访问 blob 则尝试遍历下一级访问 blob 文件
                        try {
                            freader.readAsDataURL(file)
                        } catch (error) {
                            freader.readAsDataURL(file[0])
                        }
                        freader.onload = function(event) {
                            let base64 = freader.result,  //base64;
                                blobUrl = URL.createObjectURL(file),  //bloburl
                                blobStr = `\n![${fname}](${blobUrl} "${fname}")\n`;  //marked bloburl
                            bloBaseArr.push(new BlobaseImg(blobStr,base64));
                            tarea.value = insertRepos(tarea,blobStr,"");  // tarea.value += blobStr;
                            focusArea(tarea);
                            htmldom.innerHTML = marked(tarea.value);  //htmldom.innerHTML += marked(blobStr);
                            //preview.value = tarea.value;  //preview.value += base64;
                            blob2baseMap(tarea);
                            focusArea(preview);
                        }
                    }
                },
                ProcessFiles = ProcessFile(),
                //blob2baseMap 函数遍历当前输入框内所有图片链接的更新状态，请求变更后的 blob 链接的 base64 图片链接（blob/base64）
                blob2baseMap = function(tarea){
                    var blobStrArr = tarea.value.match(/blob.*? /g),  //blob url only
                        tempBaseArr = [];  //临时 blob 数组
                    forArr(blobStrArr,function(i){
                        var xhr = new XMLHttpRequest;
                        xhr.responseType = 'blob';
                        xhr.onload = function(){
                            var recoveredBlob = xhr.response;
                            var reader = new FileReader;
                            reader.onload = function(){
                                tempBaseArr.push(reader.result);  //所有 blob 链接
                                preview.value = replaceAll(tarea.value,blobStrArr,tempBaseArr)  //过滤所有 blob 图片元素并写入 preview
                                focusArea(preview);
                            };
                            reader.readAsDataURL(recoveredBlob);
                        };
                        xhr.open('GET', blobStrArr[i]);  //发送所有 blob 请求至 base64 链接
                        xhr.readyState!=404 ? xhr.send() : false;
                    })
                },
                MDSelection = function(tarea){
                    for(key in MDToolbars){
                        let before = MDToolbars[key].before,
                            text = MDToolbars[key].text,
                            after = MDToolbars[key].after,
                            eachTool = toolbar.querySelector(`#${key}`),
                            curSelect = window.getSelection(),
                            curStart = tarea.value.indexOf(curSelect[0]),
                            curEnd = tarea.value.lastIndexOf(curSelect[curSelect.length-1])+1;
                        //上传文件合法检测并使用函数 ProcessFiles 解析上传文件（uploadCheck 已传参 tarea,file,fname 给 ProcessFiles 所以此处无需传参）
                        uploadCheck(curDom,imgfile,tarea,ProcessFiles);
                        eachTool.onclick = function(i){
                            tarea.focus();  //提前 focus 获取 window.getSelection()
                            switch(eachTool.id){
                                case "hyperlink":
                                    insertLink(tarea,text,"文本链接（Enter Hyperlink）","https://","文本描述",eachTool.id);
                                    break;
                                case "imgsrc":
                                    insertLink(tarea,text,"图片链接（Enter ImgLink）","https://","图片描述",eachTool.id);
                                    break;
                                case "table":
                                    insertTable(tarea,before,text,after);
                                    break;
                                default:
                                    curSelect=="" ? tarea.value = insertRepos(tarea,before+text,after) : tarea.value = insertRepos(tarea,before,after);
                                    focusArea(tarea);  //插入完成后再次聚焦文本框
                                    break;
                            }
                            htmldom.innerHTML = marked(tarea.value);
                            preview.value = tarea.value;
                            blob2baseMap(tarea);
                        }
                    };
                    tarea.ondragenter = function(e){ this.parentNode.classList.add("ondrag") };
                    tarea.ondragleave = function(e){ this.parentNode.classList.remove("ondrag") };
                    // tarea.addEventListener('drop',function(e){});  //使用 ondrop 替代 drop（addEventListener 会导致重复执行）
                    tarea.ondrop = function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        this.parentNode.classList.remove("ondrag")
                        var res = e.dataTransfer,
                            dragArr = [];  //每次 drag 重置当前 dragArr
                        for(let i=0;i<res.items.length;i++){
                            let dragFiles = res.files[i];
                            dragArr.push(dragFiles)
                        };
                        fileCheck("this",dragArr,3,function(i){
                            ProcessFiles(tarea,dragArr[i])  //调用文件解析需传 tarea,dragArr(i) 参数
                        })
                    }
                };
            MDSelection(markdown)
            if(markdown && htmldom && preview){
                var oninput = new Event('input');
                markdown.addEventListener('input',function(e){
                    preview.value = this.value;
                    focusArea(preview);  // preview.focus()
                    blob2baseMap(this);
                    this.value=="" || this.value.length<=0 ? htmldom.innerHTML = "<small> Marked Html Tag Elements Preview. ( focus only ) </small>" : htmldom.innerHTML = marked(this.value);
                },false);
                markdown.dispatchEvent(oninput);
                markdown.onselect = ()=>{MDSelection(this)};
                MDOpen = function(pasteText){
                    window.addEventListener('keyup',keysuped);
                    document.body.classList.add("MDFocus");
                    markedTip();
                    // markdown.value = pasteText||"";  单开md编辑器
                    markdown.focus();
                };
                markdown.onmouseup = ()=>{MDOpen()};
                //onmouseup onclick 事件处理逻辑：鼠标按下后松开目标位置元素
                mdbox.onclick = function(event){
                    let et = event.target,
                        md = this.querySelector("textarea#markdown");
                    // console.log(md.value)
                    if(et==masker){
                        window.removeEventListener('keyup',keysuped);
                        document.body.classList.remove("MDFocus");
                        // markdown.value = md.value;  单开md编辑器
                        esctip.remove()
                    }
                }
                markdown.placeholder = `[preset]:https://www.link.com
                
# main title
paragraph with  __bold text__ and  _italic text_ plus  ___both___ 
 <u>underline tex</u> t with a [preset] link and [link](www.link.com) text

## sub title

for some items use ol:
1. ol item 1
1.  ol item 2

for ul likes:
* use
* this

for quote like:
> normal
> 
> on same line
>> inside
>>
>> inside online

and of course the  'code' and code line:

''' autolang
like this

'''

the table

|a|b|c|
|:-|:-:|-:|
|1|2|3|
|4|5|6|


more rich text for media elements:

![01test.jpg](blob:https://lbms.2broear.com/af8c42fa-62ae-4d42-8d4b-a884477836af "01test.jpg")

add mulit images like (exchangeable position)

![01测试.jpeg](blob:https://lbms.2broear.com/bd760c66-2903-45d2-9147-ab886d1af2f9 "01测试.jpeg")

attation   __NOTICE__ :

those img link is  __temporary !__  these link will be gone for ever <u>agfter page reload or closed.</u>which means if the browser menmory gets released, then you need to rebuild one new img blob url at that time.

but the base64 url in preview will be still works nomater whats changed.

`
            }

        }
    };runMarked(curTab)
} else {
    // document.body.innerHTML="";
    window.location.href="/lbms-login";  //redirect to login
    console.log(currentUser||"USER not login LBMS!");  // 检测 currentUser 登录状态（验证是否登录已验证账号）
}
</script>
</body></html>