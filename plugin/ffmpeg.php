<?php
    $execmd = ['shell_exec','system','exec'];
    $shell = false;
    foreach($execmd as $cmd){
        if(function_exists($cmd)){
            $shell = $cmd;
        }
    }
    if($shell){
        define('WP_USE_THEMES', false);  // No need for the template engine
        require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // Load WordPress Core 
        $ffmpeg_sw = get_option('site_video_capture_switcher');
        $ffmpeg_sw_gif = get_option('site_video_capture_gif');
        // mkdir
        function mkdirs($dir, $mode=0777){
        	if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        	if (!mkdirs(dirname($dir), $mode)) return FALSE;
        	return @mkdir($dir, $mode);
        };
        function ratio($a, $b){
            $gcd = function($a, $b) use (&$gcd) {
                return ($a % $b) ? $gcd($b, $a % $b) : $b;
            };
            $g = $gcd($a, $b);
            return $a/$g . ':' . $b/$g;
        };
        $dirURI = getcwd(); // dirname(__FILE__);
        $fileURI = $dirURI.'/univ240p.mp4'; // test  univ240p  vbr2scale50x  vbr2scale240p  data_dance32x
        $basename = basename($fileURI);
        $fileName = preg_replace('/\..+/',"",$basename);
        mkdirs($fileName);
        $savePath = $dirURI.'/'.$fileName.'/'.$fileName;
        $fileList = glob($fileName.'/'.$fileName.'*.jpeg');
        $fileResolution = $shell('ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 '.$fileURI);
        $fileWidth = $shell("ffmpeg -i ".$fileURI." 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f1");
        $fileHeight = $shell("ffmpeg -i ".$fileURI." 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f2");
        // echo $fileWidth.'-'.$fileHeight;
        // https://blog.csdn.net/weixin_39734048/article/details/116184761
        $file_ratio = ratio($fileWidth,$fileHeight);
        $preset_ratio = '16:9';
        $calcH = $fileHeight; //高不变
        $calcW = $fileWidth; //宽不变
        if($file_ratio!=$preset_ratio){
            list($scaleW, $scaleH) = explode(':', $preset_ratio);
            if($fileHeight < $fileWidth){
                $calcH = $fileHeight; //高不变
                $calcW = round($fileHeight / $scaleH * $scaleW); //根据高计算比例宽
            }else{
                $calcW = $fileWidth; //宽不变
                $calcH = round($fileWidth / $scaleW * $scaleH); //根据宽计算比例高
            }
            echo 'origin: '.$fileResolution.'('.$file_ratio.') => fixed: '.$calcW.'x'.$calcH.'('.$preset_ratio.')<br/>';
        }else{
            echo 'origin already '.$file_ratio.'('.$fileResolution.') , no need to fix.<br/>';
        }
        // $fileDuration = $shell("ffmpeg -i ".$fileURI." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
        $fileSize = filesize($fileURI); //clearstatcache()
        print_r((round($fileSize / 1024 * 100) / 100).'kb'); //round($fileSize / 1048576 * 100) / 100.'mb'
        if(count($fileList)<=0){
            $shell("ffmpeg -i $fileURI -map 0 -map -0:a -c copy ".$savePath."_mute.mp4");
            // 生成16：9视频
            // $shell('ffmpeg -i '.$fileURI.' -vf "scale='.$fileHeight.':'.$fileWidth.',setdar=16:9" '.$savePath.'_ratio-fix.mp4');
            $shell('ffmpeg -i '.$fileURI.' -vf "scale='.$calcW.':'.$calcH.',setdar=16:9" -r 0.25 -f image2 "'.$savePath.'_%2d.jpeg"');
            // $shell('ffmpeg -i '.$fileURI.' -vf "scale=iw:-1,setdar=16:9" -r 0.25 -f image2 "'.$savePath.'_%2d.jpeg"'); //  scale=427x240
            $fileList = glob($fileName.'/'.$fileName.'*.jpeg');
            print_r('<p><b>'.$basename.'</b> capture successed as following array </p>'.'<pre>');
            print_r($fileList);
            // 注意 -i 后跟随 $savePath 文件路径（压缩宽度/2）
            $shell('ffmpeg -i '.$savePath.'_%2d.jpeg -filter_complex "scale=iw/2:-1,tile='.count($fileList).'x1" "'.$savePath.'.jpg"');
            // 生成 低帧率（慢速） gif（压缩宽度/2）
            $shell('ffmpeg -r 1 -f image2 -i '.$savePath.'_%2d.jpeg -vf "scale=iw/2:-1" '.$savePath.'.gif');
            // $shell('ffmpeg -y -i '.$fileURI.' -vf palettegen palette.png');
            // $shell('ffmpeg -y -i '.$fileURI.' -i palette.png -filter_complex paletteuse -r 10 -s 320x480 '.$savePath.'.gif');
            // $shell('ffmpeg -y -i '.$fileURI.' -filter_complex "fps=5,scale=480:-1:flags=lanczos,split[s0][s1];[s0]palettegen=max_colors=32[p];[s1][p]paletteuse=dither=bayer" '.$savePath.'.gif');
        }else{
            $preview_bg = str_replace("/www/wwwroot/", "https://", $savePath);
            // $preview_em = $ffmpeg_sw ? '<div class="preview_bg" data-preview="'.$preview_bg.'.jpg" data-previews="'.$preview_bg.'.gif"><span class="progress"><em></em></span></div>' : false;
    ?>
            <h2><?php echo $basename; ?> has already been captured. </h2>
            <small>to re-generate video captures, just delete <b>/<?php echo $fileName; ?></b> folder.</small>
            <p style="overflow: auto;"><img src="<?php echo $preview_bg; ?>.jpg" style="max-height:100px" /></p>
            <div class="preview_videos">
                <video src="<?php echo str_replace("/www/wwwroot/", "https://", $fileURI); ?>" poster="" controls="" preload="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>
                <div class="preview_bg"<?php echo ' data-preview="'.$preview_bg.'.jpg"';echo $ffmpeg_sw_gif ? ' data-previews="'.$preview_bg.'.gif"' : false; ?>>
                    <span class="progress"><em></em></span>
                </div>
            </div>
            <style>
                video{object-fit: initial;}
                .preview_videos.hide_preview:before,.preview_videos.hide_preview .preview_bg{content:"";display:none}
                .preview_videos.previews:before{content:'';width:100%;height:50%;backdrop-filter:blur(10px);position:absolute;top:0;left:0;z-index:1;background:-webkit-linear-gradient(90deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);background:linear-gradient(0deg,rgb(255 255 255 / 0%) 0%, rgb(0 0 0 / 25%) 100%);}
                .preview_videos{position:relative;overflow:hidden;display:inline-block;border-radius:10px}
                .preview_videos.previews .preview_bg{z-index:1;opacity:1;top:25%;pointer-events:none;}
                .preview_bg .progress{width:32%;height:3px;background:white;border:1px solid;border-radius:15px;position:absolute;bottom:10%;left:50%;transform:translate(-50%,-50%);overflow:hidden}
                .preview_bg .progress em.pause_move{transform:translateX(0%)!important}
                .preview_bg .progress em{width:100%;height:100%;background:red;position:inherit;top:0;left:0;transform:translateX(-100%);will-change:transform}
                .preview_bg{cursor:crosshair;position:absolute;left:50%;transform:translate(-50%,-50%);border-radius:10px;z-index:-1;opacity:0;transition:opacity .35s ease-in;transition:top 1s ease;width:90%;height:35%;top:20%;/*width:88%;height:58%;top:38%!important;*/}
            </style>
            <script>
                const videos = document.querySelectorAll('video');
                if(videos[0]){
                    for(let i=0;i<videos.length;i++){
                        let video = videos[i],
                            video_box = video.parentNode,
                            preview_bg = video_box.querySelector('.preview_bg'),
                            preview_pg = video_box.querySelector('.progress em'),
                            preview_src = preview_bg.dataset.preview,
                            preview_gif = preview_bg.dataset.previews,
                            timer_throttle = null,
                            calcOffsetXFromParent = function(e,originElement){
                                var el = e.target || e.srcElement,
                                    ep = el.parentNode,
                                    getStyleByValue = function(el, val){
                                       return (window.getComputedStyle(el) || el.currentStyle)[val];
                                    };
                                return el.getBoundingClientRect().left - ep.getBoundingClientRect().left 
                                       + e.offsetX + 
                                       parseFloat(getStyleByValue(el, 'borderLeftWidth')) - parseFloat(getStyleByValue(ep, 'borderLeftWidth'));
                            };
                        if(preview_bg){
                            preview_src ? preview_bg.setAttribute('style','background:url('+preview_src+') no-repeat 0% 0% /cover') : false;
                            video.addEventListener('canplay', function () {
                                video = video_box.querySelector('video'); // canplay 内需重新声明 video，否则修改后无法应用到dom
                                <?php 
                                    if($ffmpeg_sw_gif){
                                ?>
                                        let gifWidth = video.videoWidth/2,  //预置gif预览宽度 this.videoWidth/2
                                            boxWidth = video_box.offsetWidth;
                                        // 仅当预览gif宽度小于视频盒子宽度时设置视频宽高，防止 poster 缩小视频宽高
                                        if(gifWidth<boxWidth){
                                            video.width = boxWidth;//this.videoWidth;
                                            video.height = video_box.offsetHeight;//this.videoHeight;
                                        }
                                <?php
                                    }
                                ?>
                                video.onplaying=()=>{
                                    video_box.classList.add('hide_preview');
                                }
                                video.onpause=()=>{
                                    video_box.classList.remove('hide_preview');
                                }
                            });
                            video_box.onmousemove=function(e){
                                var _this = this,
                                    video_offset = e.offsetX, //video_offset = calcOffsetXFromParent(e,this),
                                    // video_width = video.videoWidth; // video_width = video.videoWidth*0.99, 
                                    video_width = video_box.offsetWidth; // video_width = video.videoWidth*0.99,  //设置有效移动路径范围为video_width的99%（偏移右侧像素）
                                // console.log(video_offset);
                                return (function(){
                                    if(timer_throttle==null){
                                        <?php echo $ffmpeg_sw_gif ? "!video.getAttribute('poster')&&preview_gif ? video.setAttribute('poster',preview_gif) : false;" : false; ?>
                                        _this.classList.add('previews');
                                        timer_throttle = setTimeout(function(){
                                            // e.stopPropagation(); //e.preventDefault(); 
                                            let percentage = (Math.round(video_offset/video_width*10000)/100).toFixed(0),
                                                progressOffset = -100+Number(percentage); //-100+Number(percentage)
                                            preview_bg.style.backgroundPosition = percentage+"% 0%";
                                            preview_pg.style.transform = 'translateX('+progressOffset+'%)';
                                            // console.log(percentage);
                                            Number(percentage)>=100 ? preview_pg.classList.add('pause_move') : preview_pg.classList.remove('pause_move');
                                            _this.onmouseleave = function(){
                                                this.classList.remove("previews");
                                                preview_pg.style.transform = "";
                                            }
                                            timer_throttle = null;  //消除定时器
                                        }, 10);
                                    }
                                })();
                            };
                        }
                    }
                }
            </script>
    <?php
        }
    }else{
        echo "function(shell/shell_exec/system) disabled";
    }
?>