<?php
    global $src_cdn;
?>
<script src="<?php echo $src_cdn; //custom_cdn_src('',true) ?>/js/main.js?v=<?php echo get_theme_info('Version'); ?>"></script>
<script type="text/javascript">
    <?php
        global $cat;
        $vdo_poster_sw = get_option('site_video_poster_switcher');
        $datadance = get_option('site_animated_counting_switcher');
        $news_temp_id = get_cat_by_template('news','term_id');
        $note_temp_id = get_cat_by_template('notes','term_id');
        $acg_temp_id = get_cat_by_template('acg','term_id');
        if(is_single()){
            if(in_category($news_temp_id) || in_category($note_temp_id)){
                if($vdo_poster_sw) echo 'setupVideoPoster(3);'; // 截取设置当前页面所有视频 poster
    ?>
                //dynamicLoad
                asyncLoad('<?php echo $src_cdn; ?>/js/fancybox.umd.js', function(){
                    console.log('fancybox init.');
                    // gallery js initiate 'bodyimg' already exists in footer lazyload, use contimg insted.
                    fancyImages(document.querySelectorAll(".news-article-container .content img"));
                });
    <?php
            }
        }
        if($cat){
            switch ($cat) {
                case get_cat_by_template('privacy','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(1);'; // 截取设置当前页面所有视频 poster
                    break;
                case $acg_temp_id:
                case cat_is_ancestor_of($acg_temp_id, $cat):
                    if($datadance) echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h2", -15, 5, "<sup>+</sup>");';
                    break;
                case get_cat_by_template('archive','term_id'):
                    if($datadance) echo 'dataDancing(document.querySelectorAll(".win-top .counter div"), "h1", 200, 25);';
                    break;
                case get_cat_by_template('about','term_id'):
                    if($vdo_poster_sw) echo 'setupVideoPoster(2);';  // 截取设置当前页面所有视频 poster 
    ?>
                    const list = document.querySelectorAll('.mbit .mbit_range li');
                    // if(raf_available){
                    //     if(list[0]){
                    //         for(let i=0,listLen=list.length;i<listLen;i++){
                    //             raf_enqueue(true, function(init){
                    //                 list[i].classList.add('active');
                    //             }, 25, i);
                    //         }
                    //     }
                    // }else{
                        async_enqueue(list, true, function(i){ //sto_enqueue
                            list[i].classList.add('active');
                        }, 200);
                    // }
    <?php
                    break;
                // case get_cat_by_template('weblog','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('guestbook','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('2bfriends','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('download','term_id'):
                //     // code...
                //     break;
                // case get_cat_by_template('ranks','term_id'):
                //     // code...
                //     break;
                // default:
                //     // code...
                //     break;
            }
        }
    ?>
</script>