<?php
    // define('WP_USE_THEMES', false);  // No need for the template engine
    // // require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
    // require_once( '../../../../wp-load.php' );  // incase api DOCUMENT_ROOT
    parse_str($_SERVER['QUERY_STRING'], $parameters);
?>
<style>
    body{position: relative;}
    :root{/*--preset-fa:#fafafa;--preset-e:#eee;--preset-d:#ddd;--preset-c:#ccc;--preset-9:#949494;--preset-8:#888;--preset-6:#666;--preset-4a:#4a4a4a;--preset-3a:#3a3a3a;--preset-2b:#2b2b2b;--radius:10px*/--padding-num:15px;}
    .captureBox{width:100%;height:100%;}#capture{max-width:300px;min-width:280px;color:var(--preset-2b);text-align:center;border-radius:var(--radius);background:var(--preset-fa);overflow:hidden;font-family:var(--font-ms);position:fixed;top:0;left:0;z-index:-99999;/*transform:scale(2);-webkit-transform:scale(2);*/}#capture header{width:auto;height:auto;margin:0 auto;padding:20px var(--padding-num) 0;background:var(--preset-e);border-top-left-radius:var(--radius);border-top-right-radius:var(--radius)}#capture header img{max-width:100%;min-height:168px;max-height:188px;width:100%;object-fit:cover;border-radius:inherit;background:currentColor;margin:0 auto;display:inherit}#capture aside{text-align:left;padding:25px var(--padding-num) var(--padding-num);box-sizing:border-box;position:relative}#capture aside h3{margin:0;max-width:58%;text-overflow:ellipsis;overflow:hidden;/*max-height:50px*/}#capture aside p{color:var(--preset-6);font-size:0.8rem;font-weight:300;line-height:23px;min-height:36px}#capture aside small{color:var(--preset-c);width:100%;display:inherit;text-align:right;font-size:12px;padding-top:10px}#capture aside small span{margin:auto 5px}#capture aside small span a{color:inherit;}#capture aside #qrcode{width:100px;height:100px;background:var(--preset-fa);padding:10px;box-sizing:inherit;position:absolute;top:-50px;right:30px;box-shadow:rgb(0 0 0 / 0.18) 0px 5px 20px 0px}#capture aside #qrcode img{width:100%;height:100%}#capture footer{color:var(--preset-c);font-size:12px;padding:15px 0;border-top:1px solid var(--preset-e)}#html2img::before{width:200%;height:2px}#html2img::after{width:2px;height:150%}#html2img{/*padding:var(--padding-num);border:2px solid red;*/box-sizing:border-box;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);z-index:99999}#mask,#html2img::before,#html2img::after{content:'';/*background:red;*/position:absolute;top:inherit;left:inherit;transform:inherit;-webkit-transform:inherit;z-index:-1}.poster{display:none}.poster.active{display:block}.poster.active #mask{width:100%;height:100%;background: rgb(0 0 0 / 36%);top:0;left:0;z-index: 9999;/*backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);*/}#html2canvas{max-width:100%;max-height:100%;/*transform:translate(0,-0.5px)*/}#html2canvas img#loading{height:auto;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%)}#html2canvas img{max-width:100%;border-radius:inherit;display:inherit}#html2canvas #loadbox{min-width:280px;max-width:300px;min-height:388px;max-height:412px;background:var(--preset-fa);box-shadow:rgb(0 0 0 / 0.18) 0px 5px 20px 0px;border-radius: var(--radius);position:relative}#loadbox h3{color:var(--preset-fa);font-size: 1rem;line-height:66px;margin:0 auto;width:100%;background:var(--preset-3a);background:linear-gradient(to right, var(--preset-2b), var(--preset-3a));border-top-left-radius:var(--radius);border-top-right-radius:var(--radius);position:absolute;top:0}#loadbox img#loading{top:58%}#loadbox #cancel:hover{transform:rotate(-90deg);-webkit-transform:rotate(90deg);background:var(--theme-color)}#loadbox #cancel{position: absolute;top: -22px;right: -22px;z-index: 1;background: var(--preset-3a);border: 4px solid var(--preset-e);border-radius: 50%;padding: 20px;cursor:pointer;transition:transform .35s ease}span#cancel:before{transform:translate(-50%,-50%) rotate(45deg)}span#cancel::after{transform:translate(-50%,-50%) rotate(-45deg)}span#cancel:before,span#cancel:after{content:"";width:52%;height:4px;background:var(--preset-fa);position:inherit;top:50%;left:50%}#loadbox #poster{border-radius:inherit;display:block;position:inherit}
    #capture header em{display: block;width: 100%;min-height:168px;max-height:188px;border-radius:inherit;}
</style>
<div id="capture">
    <header><em style="background:url(<?php echo $parameters['image']; ?>) center center /cover"></em></header>
    <aside>
        <h3><?php echo urldecode($parameters['title']); ?></h3>
        <p><?php echo urldecode($parameters['content']); ?></p>
        <small><span contenteditable="true"><?php $tags = urldecode($parameters['tags']);echo $tags ? $tags : '<b>'.urldecode($parameters['author']).'</b>&nbsp;'; ?></span><?php echo $parameters['date']; ?></small>
        <span id="qrcode"></span>
    </aside>
    <footer>
        <?php echo $tags ? '<b> SHARING VIA '.urldecode($parameters['author']).' </b>' : '<i> Poster shared in '.date('Y/m/d H:i',$_SERVER['REQUEST_TIME']).' </i>'; //$_SERVER['HTTP_ORIGIN'];?>
    </footer>
</div>
<div class="poster active">
    <div id="html2img">
        <div id="html2canvas">
            <div id="loadbox">
                <img id="loading" src="<?php echo $parameters['loading']; ?>" />
                <h3> 正在生成海报，请等待.. </h3>
                <span id="cancel" onclick="poster_sw()"></span>
                <span id="poster"></span>
            </div>
        </div>
    </div>
    <div id="mask"></div>
</div>