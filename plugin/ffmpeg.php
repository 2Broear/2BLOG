<?php
/**
 * 视频帧捕获工具 - 适配 PHP 8.0 + 空 PATH 环境
 * 直接使用 ffmpeg 绝对路径，用 PHP 解析输出，不依赖 grep/cut
 */

// 直接设置 ffmpeg 绝对路径（绕过 open_basedir 和 PATH 问题）
$ffmpegBin = '/usr/bin/ffmpeg';

// 原图片取色（保持不变）
$im_url = "https://blog.2broear.com/wp-content/themes/2BLOG-main/images/fox.jpg";
$im = imagecreatefromstring(file_get_contents($im_url));
$rgb = imagecolorat($im, 10, 15);
$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
echo "<span style='background:rgb($r $g $b)'>$r $g $b</span>";

function getJpgImgColor($img_path) {
    $imgccc = imagecreatefromjpeg($img_path);
    $total = 0;
    $rTotal = 0;
    $gTotal = 0;
    $bTotal = 0;
    for ($x = 0; $x < imagesx($imgccc); $x++) {
        for ($y = 0; $y < imagesy($imgccc); $y++) {
            $rgb = imagecolorat($imgccc, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $rTotal += $r;
            $gTotal += $g;
            $bTotal += $b;
            $total++;
        }
    }
    $color = array();
    $color['r'] = round($rTotal / $total);
    $color['g'] = round($gTotal / $total);
    $color['b'] = round($bTotal / $total);
    return $color;
    imagedestroy($imgccc);
}
$raba = getJpgImgColor($im_url);
echo "<span style='background:rgb($raba[r] $raba[g] $raba[b])'>$raba[r] $raba[g] $raba[b]</span>";

// 命令执行函数选择
$execmd = ['shell_exec','system','exec'];
$shell = false;
foreach($execmd as $cmd){
    if(function_exists($cmd)){
        $shell = $cmd;
    }
}
if (!$shell) {
    echo "function(shell/shell_exec/system) disabled";
    exit;
}

define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
$ffmpeg_sw = get_option('site_video_capture_switcher');
$ffmpeg_sw_gif = get_option('site_video_capture_gif');
if (!$ffmpeg_sw) {
    echo 'ffmpeg disabled.';
    exit;
}

function mkdirs($dir, $mode=0777){
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

// PHP 8.0 修复：类型转换 + 除零保护
function ratio($a, $b){
    $a = (int) trim($a);
    $b = (int) trim($b);
    if ($b == 0) return '0:0';
    $gcd = function($a, $b) use (&$gcd) {
        return ($a % $b) ? $gcd($b, $a % $b) : $b;
    };
    $g = $gcd($a, $b);
    return $a/$g . ':' . $b/$g;
}

$dirURI = getcwd();
$dirURI = substr($dirURI, 0, strpos($dirURI, '/plugin'));
$fileURI = $dirURI.'/media/videos/video.mp4';
if (!file_exists($fileURI)) {
    echo '404 file not found: ' . $fileURI;
    exit;
}

$basename = basename($fileURI);
$fileName = preg_replace('/\..+/',"",$basename);
mkdirs($fileName);
$savePath = $dirURI.'/'.$fileName.'/'.$fileName;
$fileList = glob($fileName.'/'.$fileName.'*.jpeg');

// ========== 核心修复：用 PHP 正则直接提取分辨率，不依赖 grep/cut ==========
$ffmpegCmd = "$ffmpegBin -i " . escapeshellarg($fileURI) . " 2>&1";
if ($shell === 'exec') {
    $output = [];
    exec($ffmpegCmd, $output);
    $ffmpegOutput = implode("\n", $output);
} else {
    $ffmpegOutput = $shell($ffmpegCmd);
}

$fileWidth = 0;
$fileHeight = 0;
if (preg_match('/(\d{3,5})x(\d{3,5})/', $ffmpegOutput, $matches)) {
    $fileWidth = (int) $matches[1];
    $fileHeight = (int) $matches[2];
}

if ($fileWidth <= 0 || $fileHeight <= 0) {
    // 可选调试信息，若仍然失败可查看 ffmpeg 原始输出
    echo "Could not detect video resolution. (width=$fileWidth, height=$fileHeight)<br>";
    echo "FFmpeg output:<br><pre>" . htmlspecialchars($ffmpegOutput) . "</pre>";
    exit;
}

$fileResolution = $fileWidth . 'x' . $fileHeight; // 不使用 ffprobe 也能显示

$file_ratio = ratio($fileWidth,$fileHeight);
$preset_ratio = '16:9';
$calcH = $fileHeight;
$calcW = $fileWidth;

if($file_ratio!=$preset_ratio){
    list($scaleW, $scaleH) = explode(':', $preset_ratio);
    if($fileHeight < $fileWidth){
        $calcH = $fileHeight;
        $calcW = round($fileHeight / $scaleH * $scaleW);
    }else{
        $calcW = $fileWidth;
        $calcH = round($fileWidth / $scaleW * $scaleH);
    }
    echo 'origin: '.$fileResolution.'('.$file_ratio.') => fixed: '.$calcW.'x'.$calcH.'('.$preset_ratio.')<br/>';
}else{
    echo 'origin already '.$file_ratio.'('.$fileResolution.') , no need to fix.<br/>';
}

$fileSize = filesize($fileURI);
print_r((round($fileSize / 1024 * 100) / 100).'kb');

if(count($fileList)<=0){
    // 所有 ffmpeg 命令都改用绝对路径
    $shell("$ffmpegBin -i ".escapeshellarg($fileURI)." -map 0 -map -0:a -c copy ".escapeshellarg($savePath."_mute.mp4"));
    $shell("$ffmpegBin -i ".escapeshellarg($fileURI)." -vf \"scale={$calcW}:{$calcH},setdar=16:9\" -r 0.25 -f image2 ".escapeshellarg($savePath."_%2d.jpeg"));
    $fileList = glob($fileName.'/'.$fileName.'*.jpeg');
    print_r('<p><b>'.$basename.'</b> capture successed as following array </p>'.'<pre>');
    print_r($fileList);
    $shell("$ffmpegBin -i ".escapeshellarg($savePath.'_%2d.jpeg')." -filter_complex \"scale=iw/2:-1,tile=".count($fileList)."x1\" ".escapeshellarg($savePath.'.jpg'));
    $shell("$ffmpegBin -r 1 -f image2 -i ".escapeshellarg($savePath.'_%2d.jpeg')." -vf \"scale=iw/2:-1\" ".escapeshellarg($savePath.'.gif'));
} else {
    $preview_bg = str_replace("/www/wwwroot/", "https://", $savePath);
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
                for(let i=0,vdoLen=videos.length;i<vdoLen;i++){
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
                            video.width = <?php echo $calcW; ?>;//this.videoWidth;
                            video.height = <?php echo $calcH; ?>;//this.videoHeight;
                            video = video_box.querySelector('video');
                            <?php 
                                if($ffmpeg_sw_gif){
                            ?>
                                    let gifWidth = video.videoWidth/2,
                                        boxWidth = video_box.offsetWidth;
                                    if(gifWidth<boxWidth){
                                        video.width = boxWidth;
                                        video.height = video_box.offsetHeight;
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
                                video_offset = e.offsetX,
                                video_width = video_box.offsetWidth;
                            return (function(){
                                if(timer_throttle==null){
                                    <?php echo $ffmpeg_sw_gif ? "!video.getAttribute('poster')&&preview_gif ? video.setAttribute('poster',preview_gif) : false;" : false; ?>
                                    _this.classList.add('previews');
                                    timer_throttle = setTimeout(function(){
                                        let percentage = (Math.round(video_offset/video_width*10000)/100).toFixed(0),
                                            progressOffset = -100+Number(percentage);
                                        preview_bg.style.backgroundPosition = percentage+"% 0%";
                                        preview_pg.style.transform = 'translateX('+progressOffset+'%)';
                                        Number(percentage)>=100 ? preview_pg.classList.add('pause_move') : preview_pg.classList.remove('pause_move');
                                        _this.onmouseleave = function(){
                                            this.classList.remove("previews");
                                            preview_pg.style.transform = "";
                                        }
                                        timer_throttle = null;
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