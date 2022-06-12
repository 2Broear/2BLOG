<!DOCTYPE html>
<html lang="zh-CN">
<head>
<title>404 NOT FOUND | <?php $nick=get_option('site_nick', get_bloginfo('name'));echo $nick; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" content="404错误页面，丢失目标文件！ | <?php echo $nick; ?>">
<meta name="theme-color" content="#eb6844">
<style type="text/css" rel="stylesheet">
  body{
    background: #000;
    background-size: cover;
    margin: 0;;padding: 0;
    overflow: hidden;
    cursor: url(<?php custom_cdn_src('img'); ?>/images/404/flashlight.cur),default;
  }
  .items{
    width: 100%;height: auto;
    position: absolute;
  }
  .items .itemsInside span{ transition: all .05s ease;}
  .items .itemsInside{
    width: 1920px;height: 1024px;
    background: url('<?php custom_cdn_src('img'); ?>/images/404/1920-dark-wall.jpg') no-repeat;
  }
  .items .itemsInside #fof{
    display: block;
    width: 266px;height: 291px;
    margin: 0 auto;
    position: absolute;
    top: 191px;left: 366px;
    background: url('<?php custom_cdn_src('img'); ?>/images/404/960-glitch-ez.gif') no-repeat;
  }
  .items .itemsInside #dusts{
    display: block;
    position: absolute;
    top: 330px;left: 340px;
    width: 200px;height: 200px;
    z-index: 1;
    animation: dustsLoop 1.8s linear infinite alternate;
    background: url('<?php custom_cdn_src('img'); ?>/images/404/light-dusts.gif') no-repeat;
  }
  @keyframes dustsLoop {
    0%{opacity: 0;}
    100%{opacity: 0.66;}
  }
  .items .itemsInside #break{
    display: block;
    position: absolute;
    top: 160px;left: 160px;
    z-index: 3;
    width: 512px;height: 512px;
    background: url('<?php custom_cdn_src('img'); ?>/images/404/light-break.gif') no-repeat;
  }
  .items .itemsInside #mask{
    display: block;
    position: absolute;
    top: -350px;left: -680px;
    z-index: 2;
    width: 2515px;height: 2009px;
    background: url('<?php custom_cdn_src('img'); ?>/images/404/mask-dark-wall.png') no-repeat;
  }
  .positions{ opacity: 0;visibility: hidden;display: none;}
  .positions span{
    z-index: 999999;
  }
  .positions #central{
    display: block;
    position: absolute;
    width: 25px;height: 25px;
    border: 2px solid red;
    border-radius: 50%;
  }
  .positions #centralX{
    display: block;
    position: absolute;
    width: 100%;height: 1px;
    background: white;
  }
  .positions #centralY{
    display: block;
    position: absolute;
    width: 1px;height: 100%;
    background: white;
  }
</style>
</head>
<!--<div class="positions" style="opacity: 0;visibility: hidden;display: none;">
  <span id="central"></span>
  <span id="centralX"></span>
  <span id="centralY"></span>
</div>-->
<div class="items">
  <div class="itemsInside">
    <span id="mask"></span>
    <span id="dusts"></span>
    <span id="fof"></span>
    <span id="break"></span>
  </div>
</div>
<!-- siteJs -->
<script type="text/javascript" src="<?php custom_cdn_src(); ?>/js/jquery-1.9.1.min.js"></script>
<!-- pluginJs-->
<!-- inHtmlJs -->
<script type="text/javascript">
  $(function(){
      var WinHeight = $(window).height(),WinWidth = $(window).width(),
      DocHeight = $(document).height(),DocWidth = $(document).width(),
      cpl = WinWidth/2,cpt = WinHeight/2,
      halfCentralH = $('#central').outerHeight(true)/2,halfCentralW = $('#central').outerWidth(true)/2;
      console.log(WinHeight/2 + ' winW ' + WinWidth/2);
      $('.positions #central').offset({left:(WinWidth/2)-halfCentralW,top:(WinHeight/2)-halfCentralH});
      $('.positions #centralX').offset({left:'',top:WinHeight/2});
      $('.positions #centralY').offset({left:WinWidth/2,top:''});
      $(window).bind({
        mousemove:function(e){
          var thisx = e.pageX,thisy = e.pageY,
          lmX = -(cpl - thisx),lmY = -(cpt - thisy),
          lmXb = (cpl - thisx),lmYb = (cpt - thisy);
          //相对中心点，x左小右大，y上小下大。左上角既满足x左小也满足y上小，右下角相反。左下角满足x左小 y下大，右上角相反。
          //第一象限 (thisx > cpl && thisy < cpt)，第二象限 (thisx < cpl && thisy < cpt)
          //第三象限 (this < cpl && thisy > cpt)，第四象限 (thisx > cpl && thisy > cpt)
          //全局 thisx > cpl || thisx < cpl && thisy > cpt || thisy < cpt
          if(thisx > cpl || thisx < cpl && thisy > cpt || thisy < cpt){
            //排除二三四象限(||而不用&&)
            $('.items .itemsInside #mask').css({'transform':'translateX('+lmX/4+'px) translateY('+lmY/4+'px)'})
            $('.items .itemsInside #fof').css({'transform':'translateX('+lmXb/10+'px) translateY('+lmYb/10+'px)'})
            $('.items .itemsInside #dusts').css({'transform':'translateX('+lmX/8+'px) translateY('+lmY/8+'px)'})
            $('.items .itemsInside #break').css({'transform':'translateX('+lmX/6+'px) translateY('+lmY/6+'px)'})
          }else if(thisx > cpl || thisy < cpt){
            //针对第一象限(右上角) 大范围移动
          }
          //象限检测
          if(thisx > cpl && thisy < cpt){
            console.log('一');
          }else if (thisx < cpl && thisy < cpt) {
            console.log('二');
          }else if (thisx < cpl && thisy > cpt) {
            console.log('三');
          }else if (thisx > cpl && thisy > cpt) {
            console.log('四');
          }
        }
      })
  })
</script>
</body></html>
