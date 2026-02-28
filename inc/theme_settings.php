<?php
    // https://laurahoughcreative.co.uk/using-the-wordpress-media-uploader-in-your-plugin-options-page/
    // https://rudrastyh.com/wordpress/customizable-media-uploader.html
    // 加载options后台js代码（wp自带jquery无需原生）
    function misha_include_js() {
    	// I recommend to add additional conditions just to not to load the scipts on each page
    	if(!did_action('wp_enqueue_media')){
    		wp_enqueue_media();
    	}
     	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/inc/themes/options2blog.js', array( 'jquery' ) );
    }
    add_action( 'admin_enqueue_scripts', 'misha_include_js' );
    /* ------------------------------------------------------------------------ *
     * 后台设置面板自定义菜单
     * ------------------------------------------------------------------------ */
     // 注册自定义文章形式  https://www.xuxiaoke.com/wpfunc/140.html
    /**
    * From https://www.wpdaxue.com/custom-single-post-template.html
    */
    //https://www.cnblogs.com/huangtailang/p/4265998.html
    //https://wordpress.stackexchange.com/questions/57647/how-to-create-a-metabox-of-html-content-with-instructions-for-editors-when-editi/59304#59304
    /* ------------------------------------------------------------------------ *
     * Custom Category fields (in&out)
     * https://wpcrumbs.com/how-to-add-custom-fields-to-categories/
     * ------------------------------------------------------------------------ */
    function wcr_category_fields($term) {
        // $templates = $GLOBALS['templates']; // global $templates
        $templates = wp_get_theme()->get_page_templates();
        if(count($templates)<=0){
            $templates = scan_templates_dir($templates);
        }
        if (current_filter() == 'category_edit_form_fields') {  //分类页详情（修改）
    ?>
            <style>input.upload_field{max-width:80%}input.upload_button{margin-left:5px}</style>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="term_fields[seo_image]"><?php _e('Category Background'); ?></label></th>
                <td>
                    <input type="text" size="40" value="<?php echo esc_attr(get_term_meta($term->term_id, 'seo_image', true)); ?>" id="term_fields[seo_image]" name="term_fields[seo_image]" class="upload_field">
                    <input id="upload_image_button" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />
                    <br/>
                    <span class="image"><?php _e('SEO Background Image Options, upload or edit it.'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="term_fields[seo_template]"><?php _e('Page Templates'); ?></label></th>
                <td>
                    <select name="term_fields[seo_template]" id="term_fields[seo_template]" class="page_templates">
                        <option value="default">默认模板</option>
                        <?php 
                            foreach ($templates as $temp => $index){
                                echo '<option value="'.$temp.'"';
                                    if(get_term_meta($term->term_id, 'seo_template', true)==$temp) echo('selected="selected"');
                                echo '>'.$index.'</option>';
                            }
                        ?>
                    </select>
                    <br/>
                    <span><?php _e('Page Template, Used in Page..'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="term_fields[seo_order]"><?php _e('Category Order'); ?></label></th>
                <td>
                    <input type="number" class="small" size="40" value="<?php 
                        $seo_order = get_term_meta($term->term_id, 'seo_order', true);
                        if(!$seo_order){
                            echo $term->term_id;
                            update_term_meta($term->term_id, 'seo_order', $term->term_id);
                        }else{
                            echo esc_attr($seo_order);
                        }
                    ?>" id="term_fields[seo_order]" name="term_fields[seo_order]">
                    <br/>
                    <span class="orderby" term_group="<?php echo $term->term_group; ?>"><?php _e('Set <b style="color:red">Lower Number</b> for <b style="color:green">Front Ranking</b><small>（auto orderby term_id: '.$term->term_id.'）download-category:1/2/3</small>'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="seo_title"><?php _e('Page Title'); ?></label></th>
                <td>
                    <input type="text" size="40" value="<?php echo esc_attr(get_term_meta($term->term_id, 'seo_title', true)); ?>" id="seo_title" name="seo_title"><br/>
                    <span class="title"><?php _e('SEO Title Options, edit or leave it. (note that this will override the whole "title")'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="seo_keywords"><?php _e('Page Keywords'); ?></label></th>
                <td>
                    <input type="text" size="40" value="<?php echo esc_attr(get_term_meta($term->term_id, 'seo_keywords', true)); ?>" id="seo_keywords" name="seo_keywords"><br/>
                    <span class="keywords"><?php _e('SEO Keywords Options, edit or leave it.'); ?></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row"><label for="seo_description"><?php _e('Page Description'); ?></label></th>
                <td>
                    <textarea class="large-text" cols="50" rows="5" id="seo_description" name="seo_description"><?php echo esc_textarea(get_term_meta($term->term_id, 'seo_description', true)); ?></textarea><br/>
                    <span class="description"><?php _e('SEO Desc Options, edit or leave it.'); ?></span>
                </td>
            </tr>
	<?php } elseif (current_filter() == 'category_add_form_fields') {  //分类页外部（新增）?>
            <h1>Page Sync Options</h1>
            <div class="form-field">
                <label for="term_fields[seo_image]"><?php _e('Background Images'); ?></label>
                <input type="text" size="40" value="" id="term_fields[seo_image]" name="term_fields[seo_image]" class="upload_field">
                <input id="upload_image_button" type="button" class="button-primary upload_button" data-type=1 value="选择图片" style="margin: 20px;float: right;" />
                <p class="description"><?php _e('SEO Images, Used in metaNav/pageBG somewhere.'); ?></p>
            </div>
            <!--<div class="form-field">-->
            <!--    <label for="term_fields[seo_icons]"><?php _e('Page Icons'); ?></label>-->
            <!--    <select name="term_fields[seo_icons]" id="term_fields[seo_icons]" class="page_icons">-->
            <!--        <option value="default">导航图标</option>-->
            <!--    </select>-->
            <!--    <p class="description"><?php _e('Page Icons, Used in Navigation Bar.'); ?></p>-->
            <!--</div>-->
            <div class="form-field">
                <label for="term_fields[seo_template]"><?php _e('Page Template'); ?></label>
                <select name="term_fields[seo_template]" id="term_fields[seo_template]" class="page_templates">
                    <option value="default">默认模板</option>
                    <?php 
                        foreach ($templates as $temp => $index){
                            echo '<option value="'.$temp.'"';
                                // if($value==$temp) echo('selected="selected"');
                            echo '>'.$index.'</option>';
                        }
                    ?>
                </select>
                <p class="description"><?php _e('Page Template, Used in Page.'); ?></p>
            </div>
            <div class="form-field">
                <label for="term_fields[seo_title]"><?php _e('Page Title'); ?></label>
                <input type="text" size="40" value="" id="term_fields[seo_title]" name="term_fields[seo_title]">
                <p class="description"><?php _e('SEO Title Options, edit or leave it.'); ?></p>
            </div>  
            <div class="form-field">
                <label for="term_fields[seo_keywords]"><?php _e('Page Keywords'); ?></label>
                <input type="text" size="40" value="" id="term_fields[seo_keywords]" name="term_fields[seo_keywords]">
                <p class="description"><?php _e('SEO Keywords Options, edit or leave it.'); ?></p>
            </div>  
            <div class="form-field">
                <label for="term_fields[seo_description]"><?php _e('Page Description'); ?></label>
                <textarea cols="40" rows="5" id="term_fields[seo_description]" name="term_fields[seo_description]"></textarea>
                <p class="description"><?php _e('SEO Desc Options, edit or leave it.'); ?></p>
            </div>
    <?php
        }
    };
    // Add the fields, using our callback function  
    // if you have other taxonomy name, replace category with the name of your taxonomy. ex: book_add_form_fields, book_edit_form_fields
    add_action('category_add_form_fields', 'wcr_category_fields', 10, 2);
    add_action('category_edit_form_fields', 'wcr_category_fields', 10, 2);
    function wcr_save_category_fields($term_id) {
        if(!get_term_meta($term_id, 'seo_order', true) && !isset($_POST['seo_order'])){
            update_term_meta($term_id, 'seo_order', $term_id);  // auto upadte seo_order while saving category_fields (use get_term_meta to check to not change the seo_order if already exists)
        }
        foreach ($_POST['term_fields'] as $key => $value) {
            update_term_meta($term_id, $key, sanitize_text_field($value));
        }
    }
    // Save the fields values, using our callback function
    // if you have other taxonomy name, replace category with the name of your taxonomy. ex: edited_book, create_book
    add_action('edited_category', 'wcr_save_category_fields', 10, 2);
    add_action('create_category', 'wcr_save_category_fields', 10, 2);
    
    
    /* ------------------------------------------------------------------------ *
     * 分类与页面同步更新通信
     * ------------------------------------------------------------------------ */
    load_theme_partial('/inc/theme_sync.php');
    /* ------------------------------------------------------------------------ *
     * 自定义文章排序 column（编辑、快速、批量编辑文章页）
     * ------------------------------------------------------------------------ */
    load_theme_partial('/inc/wp_column.php');
    /* ------------------------------------------------------------------------ *
     * WordPress Custom Post Type
     * Register the Product post type with a Dashicon.
     * https://developer.wordpress.org/resource/dashicons
     * @see register_post_type()
     * ------------------------------------------------------------------------  */
    function wpdocs_create_post_type() {
        register_post_type( 'inform',
            array(
                'labels' => array(
                    'name'          => __( '公告', 'textdomain' ),
                    'singular_name' => __( '新公告', 'textdomain' ),
                    'add_new' => '新公告',
                    'add_new_item' => '添加公告（仅显示标题）',
                    'edit_item' => '编辑公告',
                    'new_item' => '新公告',
                    'all_items' => __('所有公告'),
                    'view_item' => '查看公告',
                    'search_items' => '搜索公告',
                    'not_found' =>  '没有找到有关公告',
                    'not_found_in_trash' => '回收站里面没有相关公告',
                    'parent_item_colon' => '',
                    'menu_name' => '公告',
                ),
                'public'      => true,
                'has_archive' => true,
                'menu_icon'   => 'dashicons-controls-volumeon',
                // 'description'=> '自定义的内容类型',
                // 'public' => true,
                // 'publicly_queryable' => true,
                // 'show_ui' => true,
                // 'show_in_menu' => true,
                // 'query_var' => true,
                // 'rewrite' => true,
                // 'capability_type' => 'post',
                // 'has_archive' => true,
                // 'hierarchical' => false,
                'menu_position' => 5,
                // 'menu_icon' => 'dashicons-admin-post',
                // 'taxonomies'=> array('post_tag'),
                // 'supports' => array('title','editor','author','thumbnail','excerpt','comments')
            )
        );
    }
    add_action( 'init', 'wpdocs_create_post_type', 0 );
    
    //  新增（顶级）主菜单/子菜单/图标
    add_action('admin_menu','add_settings_menu', 1);
    function add_settings_menu() {
        // add_menu_page(__('自定义菜单标题'), __('测试菜单'), 'administrator',  __FILE__, 'my_function_menu', false, 100);
        // add_submenu_page(__FILE__,'子菜单1','测试子菜单1', 'administrator', 'your-admin-sub-menu1', 'my_function_submenu1');
        add_menu_page(__('2BLOG - 主题设置页面'), __('2BLOG 主题设置'), 'read', '2blog-settings', 'add_options_submenu');  // 创建新的顶级菜单
        add_action( 'admin_init', 'register_mysettings' );  // 调用注册设置函数
    }
    
    add_action('admin_menu','add_settings_menus', 0);
    $RSS_PAGE_NAME = 'rss-feeds';
    function add_settings_menus() {
        add_menu_page(__('2BLOG - RSS 订阅聚合'), __('RSS 友链订阅'), 'read', $GLOBALS['RSS_PAGE_NAME'], 'add_options_submenu_rss', 'dashicons-rss');  // 创建新的顶级菜单
    }
    function the_panel_theme() {
        if (!get_option('site_darkmode_switcher')) return;
        $fixed_theme = get_option('site_darkmode_fixed');
        if ($fixed_theme) {
            echo $fixed_theme;
            return;
        }
        echo isset($_COOKIE['theme_mode_panel']) ? $_COOKIE['theme_mode_panel'] : '';
    }
    function auto_panel_theme() {
    ?>
        <script type="text/javascript">
        //     function presetDarkmode() {
        //     	const head = document.getElementsByTagName('head')[0],
        //     		  style = document.createElement('style');
        // 	    style.textContent = `body { background: rgb(10, 20, 28); }`;
        // 		head.appendChild(style);
        //     }
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
            function automode() {
                const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
                const targetNode = document.querySelector('.wrap'); //document.body
                function handleColorSchemeChange(e) {
                    if (e.matches) {
                        // 用户偏好深色模式优先 (dark)
                        // presetDarkmode();
                        targetNode.classList.add('dark');
                        setCookie('theme_mode_panel', 'dark');
                        console.log('theme_panel[auto] prefers-color-scheme: dark');
                    } else {
                        targetNode.classList.remove('dark');
                        setCookie('theme_mode_panel', 'light');
                        console.log('theme_panel[auto] prefers-color-scheme: default');
                    }
                }
                // 3. 初始执行一次，设置当前主题
                handleColorSchemeChange(colorSchemeQuery);
                // 4. 监听媒体查询的变化
                colorSchemeQuery.addEventListener('change', handleColorSchemeChange);
            }
    <?php 
        $fixed_theme = get_option('site_darkmode_fixed');
        // if ($fixed_theme === 'dark') wp_enqueue_style('main-styles', get_template_directory_uri() . '/css/style.css', array(), filemtime(get_template_directory() . '/css/style.css'), false);
        echo $fixed_theme ? 'console.log(`theme_mode[static] fixed-color-scheme: ' . $fixed_theme . '..`);' : 'automode();';
    ?>
        </script>
    <?php
    }
    function add_options_submenu_rss() {
?>
        <div class="wrap <?php the_panel_theme(); ?>">
        <style>
            :root{
                --panel-theme: <?php echo get_option('site_theme','#eb6844'); ?>;
            }
            @media screen and (max-width:760px) {
                #wpcontent,
                .switchTab li {
                    padding: 5px 10px!important;
                    margin: 5px auto!important;
                }
                .fixed .switchTab{
                    top: 22px!important;
                    width: 82%!important;
                }
                .auto-fold #wpcontent {
                    margin:auto!important;
                }
            }
            @keyframes scale {
                0% {
                    transform: scale(.85);
                    transform-origin: top
                }
            
                to {
                    transform: none
                }
            }
            
            @supports (animation-timeline:view()) {
                .formtable > .feeds {
                    animation: scale 1s;
                    transform: none;
                    animation-timeline: view(block);
                    animation-range: cover 0 30%;
                    /*will-change: transform;*/
                }
            }
            /**
            ** ui/UE Enhancements 2026
            **/
            .fixed .switchTab:active {
                transform: scale(0.95) translateY(20px);
            }
            .switchTab:active {
                transition-duration: .35s;
                transform: scale(0.95) translateY(0);
            }
            /*.feeds:active,*/
            .feeds details summary:active,
            .feeds a:active {
                transform: scale(0.99);
            }
            .feeds details summary,
            .feeds,
            .feeds a {
                display: block;
                transition: transform .35s ease;
                /*transition: transform .35s cubic-bezier(0.68, -0.55, 0.27, 1.55)!important;*/
            }
            .wrap.dark {
                /*background-image: radial-gradient(rgb(23 34 46) 1px, rgb(10, 20, 28) 1px);*/
                background-color: rgb(10, 20, 28);
                background-image: radial-gradient(rgb(23 34 46) 1px, transparent 1px), radial-gradient(rgb(23 34 46) 1px, transparent 1px);
            }
            .wrap.dark h1 b {
                color: transparent;
                background: -webkit-linear-gradient(180deg, transparent 0%, #fafafa 50%);
                background: linear-gradient(270deg, transparent 0%, #fafafa 50%);
                -webkit-background-clip: text;
                background-clip: text;
            }
            .wrap.dark .switchTab {
                color: #ccc;
                background-image: radial-gradient(rgb(18 27 36) 2px, rgb(10, 20, 28, .5) 2px);
                border-color: rgb(28 41 52);
            }
            .wrap.dark .switchTab li.active,
            .wrap.dark .switchTab li:active {
                box-shadow: 0 0 0 4px rgb(28 41 52), 0 0 0 6px var(--panel-theme);
            }
            .wrap.dark h1,
            .wrap.dark .feeds .info b {
                color: #eee;
                text-shadow: none!important;
            }
            .wrap.dark .feeds {
                color: #999;
                border-color: rgb(28 41 52);
            }
            /*.wrap.dark select,*/
            .wrap.dark input,
            .wrap.dark textarea {
                color: #ccc;
                background: rgb(14 22 30);
                border-color: rgb(28 41 52);
            }
            .wrap.dark .switchTab li.active:before {
                box-shadow: -5px -5px 20px 15px currentColor;
            }
            /*
            ** prefers-color-scheme
            ** fix Flicker on page refresh
            */
            /*@media (prefers-color-scheme: dark) {*/
            /*    body {*/
            /*        background: rgb(10, 20, 28);*/
            /*    }*/
            /*}*/
            #wpbody-content {
                padding-bottom: 0;
            }
            .wrap {
                /*background-image: radial-gradient(#eee 1px, #fafafa 1px);*/
                /*background-size: 10px 10px;*/
                background-color: #fafafa;
                background-image: radial-gradient(#eee 1px, transparent 1px), radial-gradient(#eee 1px, transparent 1px);
                background-size: 12px 12px;
                background-position: 0 0, 6px 6px;
                padding-bottom: 5%;
                margin: auto;
                /* fix of background-flicker on darkmode */
                min-height: 100vh;
            }
            .feeds {padding: 20px 0;border-bottom:1px solid #eee;/*margin-bottom:15px;*/}
            .feeds a {font-size:medium}
            .feeds p a {font-size:small}
            .feeds .info {margin-bottom:15px}
            .feeds .info img,
            .feeds .info i,
            .feeds .info b {vertical-align:middle}
            .feeds .info a {display:inline-block;color: initial;text-decoration: none;font-size: small;}
            .feeds .info img {width:25px;height:25px;border-radius:50%}
            .feeds .info b {margin:auto 7px auto 5px;opacity:.75}
            .feeds .pub {opacity: .75;font-style:italic}
            .feeds .rest ol {margin-left:3rem}
            .feeds details summary:hover {text-decoration:underline}
            .feeds details summary {cursor:pointer;user-select:none;-webkit-user-select:none;}
            /*.switchTab li:active {*/
            /*    transform: scale(1.05)!important;*/
            /*}*/
            .logs-container {
                padding: 10px;
                border-radius: 10px;
            }
            /*.feeds a,*/
            .switchTab li {
                display: inline-block;
                transition: transform .35s ease;
                /*transition: inherit;*/
            }
            .formtable{display:none;}.formtable.show{display:block;}.fixed p.submit:first-child{transform:translateX(-150px);/*right:-80px*/}.switchTab.fixed{/*position: fixed;width: 100%;top: 32px;left:0;padding-left:160px;*/}.fixed .switchTab{transform:translateY(20px);/*width:max-content;top: 55px;width: 360px;transition-duration: .55s;padding: 5px;*/}.switchTab{overflow:hidden;border-radius: 50px;border-top-right-radius:0;width:100%;max-width:max-content;padding:10px 20px;transition:transform .55s cubic-bezier(0.68, -0.55, 0.27, 1.55);margin:0 auto;top:32px;position:sticky;z-index: 9;box-sizing:border-box;box-shadow:rgb(0 0 0 / 5%) 0px 20px 20px;border: 1px solid #eee;box-sizing: border-box;/*transition: top .35s ease;top: -32px;padding: 0;background: rgb(255 255 255 / 75%);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(20px);background: linear-gradient(0deg, rgb(245 247 249 / 66%) 0, rgb(255 255 255 / 88%));background: -webkit-linear-gradient(90deg, rgb(245 247 249 / 66%) 0, rgb(255 255 255 / 88%));*/background-image: radial-gradient(rgb(255 255 255 / 55%) 2px, #fff 2px);background-size: 4px 4px;backdrop-filter: saturate(150%) blur(5px);-webkit-backdrop-filter: saturate(150%) blur(5px);}.switchTab ul{margin:auto;padding:0;text-align:center;}.switchTab li:active{transform:scale(0.85);-webkit-transform:scale(0.85);will-change:transform;}.switchTab li.active:before{content: '';display: block;width: 100%;height: 100%;position: absolute;top: 0;left: 0;border-radius: inherit;background: currentColor;opacity: .15;}.switchTab li.active{color:var(--panel-theme);}.switchTab li:hover{color:var(--panel-theme);}.switchTab li:hover b{text-shadow:none}.switchTab li.active,.switchTab li:active{color: var(--panel-theme);box-shadow: 0 0 0 4px #fff, 0 0 0 6px var(--panel-theme);}.switchTab li{display:inline-block;padding:5px 15px;margin:10px 2px;cursor:pointer;font-size:initial;font-style:normal;font-weight:bold;border-radius:25px;user-select: none;-webkit-user-select: none;transition:transform .15s ease;position:relative;border-top-right-radius: 0;}h1 b{font-weight:900!important;font-style:italic;letter-spacing:normal;}#wpcontent{padding:0!important}
        </style>
        <!--<h1 style="text-align: center;font-size: 4rem!important;font-weight:100;letter-spacing:2px;padding: 15px 0!important;text-shadow:1px 1px 0 white;"><b>2BLOG</b> RSS <b>Feeds</b></h1>-->
        <h1 style="text-align: center;font-size: 3.6rem!important;font-weight:100;letter-spacing:2px;padding: 15px 0!important;text-shadow:1px 1px 0 white;"><b><span style="color:var(--panel-theme);text-shadow:20px -20px 100px currentColor;"> RSS </span>Feeds</b></h1><!--<p style="letter-spacing:normal;margin-bottom:auto;">导航分类为友情链接分类</p>-->
        <div class="switchTab">
            <ul>
                <?php 
                    $link_cats = get_links_category();
                    if (!empty($link_cats)) {
                        asort($link_cats);
                        foreach ($link_cats as $link_cat) {
                            echo '<li id="' . $link_cat->slug . '">' . $link_cat->name . '</li>';
                        }
                    }
                ?>
            </ul>
        </div>
        <form method id="contents" style="margin:50px auto;padding:0 5%">
            <?php
                // wp_cache_flush(); // bug: to clear wp_options caches
                // $output_retry = 2;
                $output_limit = get_option('site_rss_update_count', 3);
                $output_chunk = 10;  // bigger for better performance
                $output_sw = false;
                $caches_sw = get_option('site_cache_switcher');
                $caches_inc = get_option('site_cache_includes');
                if (!empty($link_cats)) {
                    foreach ($link_cats as $link_cat) {
                        $link_slug = $link_cat->slug;
                        $link_marks = get_site_bookmarks($link_slug);
                ?>
                        <div class="formtable <?php echo $link_slug; ?>">
                            <?php
                                // use of mysql caches
                                $output_json = '';
                                $caches_name = 'site_rss_' . $link_slug . '_cache';
                                if($caches_sw) {
                                    $output_sw = in_array('rssfeeds', explode(',', $caches_inc));
                                    $output_caches = get_option($caches_name);
                                    if ($output_sw && $output_caches) {
                                        $output_json = $output_caches;
                                        $output_data = json_decode($output_json);
                                        $output_date = isset($output_data[0]->lastUpdate) ? $output_data[0]->lastUpdate : '0000-00-00';
                                        $link_api = get_api_refrence('rss');  //, true with auth-param
                                        // $link_api = get_plugin_refrence('rss', true);
                                        date_default_timezone_set('Asia/Shanghai');
                                        // print_r('(' . date('Y-m-d H:i:s', strtotime('today 06:00 Asia/Shanghai')) . ') ' . strtotime('today 06:00 Asia/Shanghai'));
                                        // print_r(wp_get_schedules());
                                        // wp_clear_scheduled_hook('scheduled_rss_feeds_updates_hook');
                                        $scheduled_ts = wp_next_scheduled('scheduled_rss_feeds_updates_hook');
                                        if($scheduled_ts) {
                                            // wp_unschedule_event($scheduled_ts, 'scheduled_rss_feeds_updates_hook');
                                            $scheduled_ts = 'Scheduled updates: ' . date('Y-m-d H:i:s', $scheduled_ts) . ' (' . time() .' -> ' . $scheduled_ts . ')<br/>';
                                            print_r("<i style='float:left;opacity:.75;'>$scheduled_ts</i>");
                                        }
                                        $reload_limits = get_option('site_rss_update_count', 3);
                                        echo "<p style='text-align:right;margin-bottom:35px;'>$caches_name ($output_date) <a href='javascript:;' class='reloadFeeds' data-cat='$link_slug' data-limit=$reload_limits data-update=1 data-output=1 data-clear=0 data-api='$link_api'> reload $link_slug *</a>&nbsp;<input type='number' id='reloadCount' class='small-text' value=$reload_limits min=1 max=99 style='max-width: 3em;margin-left: 15px;border-radius: 50px;border-top-right-radius: 0;' /></p>"; //
                                    }
                                }
                                // $output_json length will be 0 if non-caches loaded
                                if(strlen($output_json)===0 || !$output_sw) {
                                    // $output_array = array();
                                    $subscribed_urls = array();
                                    foreach ($link_marks as $link_mark) {
                                        $rss_url = $link_mark->link_rss;
                                        if ($rss_url && $link_mark->link_visible==='Y') {
                                            array_push($subscribed_urls, $link_mark);
                                        }
                                    }
                                    
                                    // fetch_rss_feeds_via_url plus array_chunk limits
                                    $output_json = parse_rss_data($subscribed_urls, $output_limit, $output_chunk);
                                    
                                    if($output_json && $output_sw) {
                                        echo 'updating caches..';
                                        update_option($caches_name, wp_kses_post(preg_replace( "/\s(?=\s)/","\1", $output_json )));
                                        // update_option($caches_name, $output_json);
                                    } else {
                                        if($caches_sw) {
                                            // if (in_array('rssfeeds', explode(',', $caches_inc)))
                                            echo '<p style="text-align:center">No rss feeds/caches found on category ' . $link_slug . ' or cache disabled</p>';
                                        }
                                    }
                                }
                                $subscribed_urls = array();
                                foreach ($link_marks as $link_mark) {
                                    // $rss_url = $link_mark->link_rss;
                                    // if ($rss_url && $link_mark->link_visible==='Y') {
                                        array_push($subscribed_urls, $link_mark->link_url);
                                    // }
                                }
                                // print_r($output_json);
                                // print_r('<pre>');
                                // print_r($subscribed_urls);
                                // print_r('</pre>');
                                $output_data = json_decode($output_json);
                                the_rss_feeds($output_data);
                            ?>
                        </div>
                <?php
                    }
                }
            ?>
            <div class="rsslogs" data-api="<?php echo admin_url('admin-ajax.php') . '?action=dirScaner&deep=0&extends=.log'; ?>" data-path="<?php echo $logDir = WP_CONTENT_DIR . '/uploads/logs'; ?>" data-nonce="<?php echo wp_create_nonce(date('Y-m-d') . '_dirscaner_ajax_nonce'); ?>" data-defaults='1'>
                <p>📜 <b> 查阅日志记录 </b> 📑</p>
                <select id="" class="logs-year dropdown-react">
                    <option value=""><?php esc_attr( _e( '日志年份', 'logs-year' ) ); ?></option>
                    <?php
                        $logYears = dirScaner($logDir, false, true);
                        $curYear = date('Y');
                        foreach ($logYears as $logPath) {
                            // $logLink = str_replace('/www/wwwroot/', 'https://', $logPath);
                            $logName = str_replace($logDir . '/', '', $logPath);  // basename($logPath)
                            $selectedYear = $logName === $curYear ? ' selected' : '';
                            echo '<option value="' . esc_attr($logName) . '"' . $selectedYear . '>' . $logName . '</option>';
                        }
                    ?>
                </select>
                <select id="" class="logs-month dropdown-react" data-context="<?php echo $monthText = '日志月份'; ?>">
                    <option value=""><?php esc_attr( _e( $monthText, 'logs-month' ) ); ?></option>
                    <?php
                        $logMonth = dirScaner($logDir . '/' . $curYear, false, true);
                        $curMonth = date('m');
                        foreach ($logMonth as $logPath) {
                            // $logLink = str_replace('/www/wwwroot/', 'https://', $logPath);
                            $logName = basename($logPath);  // str_replace($logDir . '/', '', $logPath)
                            $selectedYear = $logName === $curMonth ? ' selected' : '';
                            echo '<option value="' . esc_attr($logName) . '"' . $selectedYear . '>' . $logName . '</option>';
                        }
                    ?>
                </select>
                <select id="" class="logs-dropdown" data-context="<?php echo $monthText = '选择日志'; ?>" data-defaults="<?php $curDir = $logDir . '/' . $curYear . '/' . $curMonth;$logFiles = dirScaner($curDir, true, false, '.log'); ?>">
                    <option value=""><?php esc_attr( _e( $monthText, 'logs-dropdown' ) ); ?></option>
                    <?php
                        // print_r($logFiles);
                        foreach ($logFiles as $filePath) {
                            $file_link = str_replace('/www/wwwroot/', 'https://', $filePath);
                            $file_name = str_replace($curDir . '/', '', $filePath);  // basename($filePath)
                            echo '<option value="' . esc_attr($file_link) . '">/' . $file_name . '</option>';
                        }
                    ?>
                </select>
                <p><textarea id="" class="logs-container" placeholder="点击上方选项卡以切换查询" style="width:100%;height:150px;" readonly=""></textarea></p>
            </div>
        </form>
        </div>
<?php
        // execute after wrap dom
        auto_panel_theme();
    }
    
    // 注册设置
    function register_mysettings() {
        register_setting( 'baw-settings-group', 'site_nick' );
        register_setting( 'baw-settings-group', 'site_avatar' );
        register_setting( 'baw-settings-group', 'site_bgimg' );
        register_setting( 'baw-settings-group', 'site_theme' );
        register_setting( 'baw-settings-group', 'site_theme_array' );
        register_setting( 'baw-settings-group', 'site_theme_rand_sw' );
        register_setting( 'baw-settings-group', 'site_logo_switcher' );
        // if(get_option('site_logo_switcher')){
            register_setting( 'baw-settings-group', 'site_logo' );
            register_setting( 'baw-settings-group', 'site_logos' );
            register_setting( 'baw-settings-group', 'site_logo_svg' );
        // }
        register_setting( 'baw-settings-group', 'site_single_switcher' );
        register_setting( 'baw-settings-group', 'site_single_includes' );
        register_setting( 'baw-settings-group', 'site_icon_switcher' );
        register_setting( 'baw-settings-group', 'site_nav_slider_switcher' );
        register_setting( 'baw-settings-group', 'site_progress_bar_switcher' );
        register_setting( 'baw-settings-group', 'site_keywords' );
        register_setting( 'baw-settings-group', 'site_description' );
        register_setting( 'baw-settings-group', 'site_support' );
        register_setting( 'baw-settings-group', 'site_experimental_switcher' );
        register_setting( 'baw-settings-group', 'site_inform_switcher' );
        register_setting( 'baw-settings-group', 'site_inform_num' );
        // if(get_option('site_inform_switcher')){
        //     register_setting( 'baw-settings-group', 'site_inform_cid' );
        // }
        register_setting( 'baw-settings-group', 'site_navicon_switcher' );
        
        register_setting( 'baw-settings-group', 'site_remove_category_switcher' );
        // if(get_option('site_remove_category_switcher')){
            register_setting( 'baw-settings-group', 'site_url_slash_sw' );
        // }
        // register_setting( 'baw-settings-group', 'site_sync_level_sw' );
        
        register_setting( 'baw-settings-group', 'site_search_style_switcher' );
        // if(get_option('site_search_style_switcher')){
            register_setting( 'baw-settings-group', 'site_search_includes' );
        // }
        register_setting( 'baw-settings-group', 'site_indexes_switcher' );
        // if(get_option('site_indexes_switcher')){
            register_setting( 'baw-settings-group', 'site_indexes_includes' );
        // }
        
        register_setting( 'baw-settings-group', 'site_breadcrumb_switcher' );
        register_setting( 'baw-settings-group', 'site_metanav_switcher' );
        // if(get_option('site_metanav_switcher')){
            register_setting( 'baw-settings-group', 'site_metanav_array' );
            register_setting( 'baw-settings-group', 'site_metanav_image' );
        // }
        register_setting( 'baw-settings-group', 'site_per_posts' );
        register_setting( 'baw-settings-group', 'site_catnav_deepth' );
        
        register_setting( 'baw-settings-group', 'site_rcmdside_cid' );
        register_setting( 'baw-settings-group', 'site_cardnav_includes' );
        // register_setting( 'baw-settings-group', 'site_cardnav_array' );
        // register_setting( 'baw-settings-group', 'site_list_bg' );
        register_setting( 'baw-settings-group', 'site_list_links_category' );
        register_setting( 'baw-settings-group', 'site_tagcloud_switcher' );
        // if(get_option('site_tagcloud_switcher')){
            register_setting( 'baw-settings-group', 'site_tagcloud_num' );
            register_setting( 'baw-settings-group', 'site_tagcloud_max' );
            register_setting( 'baw-settings-group', 'site_tagcloud_auto_caches' );
        // }
        register_setting( 'baw-settings-group', 'site_footprint_switcher' );
            register_setting( 'baw-settings-group', 'site_footprint_map' );
            register_setting( 'baw-settings-group', 'site_footprint_apikey' );
            register_setting( 'baw-settings-group', 'site_footprint_data' );
            register_setting( 'baw-settings-group', 'site_footprint_panorama_data' );
        register_setting( 'baw-settings-group', 'site_stream_switcher' );
        register_setting( 'baw-settings-group', 'site_links_rss_cards_sw' );
            register_setting( 'baw-settings-group', 'site_links_rss_cards_manual' );
        register_setting( 'baw-settings-group', 'site_links_auto_slider' );
        register_setting( 'baw-settings-group', 'site_links_code_state' );
            register_setting( 'baw-settings-group', 'site_links_code_state_cats' );
            register_setting( 'baw-settings-group', 'site_links_rss_alive_state' );
        register_setting( 'baw-settings-group', 'site_mbit_array' );
        register_setting( 'baw-settings-group', 'site_mbit_result_array' );
        register_setting( 'baw-settings-group', 'site_animated_scrolling_switcher' );
        register_setting( 'baw-settings-group', 'site_animated_counting_switcher' );
        
        register_setting( 'baw-settings-group', 'site_memos_switcher' );
            register_setting( 'baw-settings-group', 'site_memos_apikey' );
            register_setting( 'baw-settings-group', 'site_memos_proxy' );
            register_setting( 'baw-settings-group', 'site_memos_pattern' );
        register_setting( 'baw-settings-group', 'site_chatgpt_switcher' );
            register_setting( 'baw-settings-group', 'site_chatgpt_includes' );
            register_setting( 'baw-settings-group', 'site_chatgpt_temper' );
            register_setting( 'baw-settings-group', 'site_chatgpt_tokens' );
            register_setting( 'baw-settings-group', 'site_chatgpt_model' );
            register_setting( 'baw-settings-group', 'site_chatgpt_apis' );
            register_setting( 'baw-settings-group', 'site_chatgpt_merge_sw' );
            register_setting( 'baw-settings-group', 'site_chatgpt_merge_ingore' );
            register_setting( 'baw-settings-group', 'site_chatgpt_caches' );
            register_setting( 'baw-settings-group', 'site_chatgpt_apikey' );
            register_setting( 'baw-settings-group', 'site_chatgpt_proxy' );
            register_setting( 'baw-settings-group', 'site_chatgpt_auth' );
            register_setting( 'baw-settings-group', 'site_chatgpt_dir' );
            register_setting( 'baw-settings-group', 'site_chatgpt_type_sw' );
                register_setting( 'baw-settings-group', 'site_chatgpt_type_shuffle' );
            register_setting( 'baw-settings-group', 'site_chatgpt_feed_sw' );
            register_setting( 'baw-settings-group', 'site_chatgpt_desc_sw' );
            // register_setting( 'baw-settings-group', 'site_chatgpt_require' );
        register_setting( 'baw-settings-group', 'site_marker_switcher' );
            register_setting( 'baw-settings-group', 'site_marker_max' );
            // register_setting( 'baw-settings-group', 'site_marker_news' );
        register_setting( 'baw-settings-group', 'site_cache_switcher' );
            register_setting( 'baw-settings-group', 'site_cache_includes' );
        register_setting( 'baw-settings-group', 'site_async_switcher' );
            register_setting( 'baw-settings-group', 'site_async_includes' );
            register_setting( 'baw-settings-group', 'site_async_acg' );
            register_setting( 'baw-settings-group', 'site_async_weblog' );
            register_setting( 'baw-settings-group', 'site_async_archive' );
        register_setting( 'baw-settings-group', 'site_async_archive_stats' );
        register_setting( 'baw-settings-group', 'site_async_archive_contributions' );
        
        register_setting( 'baw-settings-group', 'site_countdown_switcher' );
            register_setting( 'baw-settings-group', 'site_countdown_date' );
            register_setting( 'baw-settings-group', 'site_countdown_title' );
            register_setting( 'baw-settings-group', 'site_countdown_bgimg' );
            
        register_setting( 'baw-settings-group', 'site_techside_switcher' );
        // if(get_option('site_techside_switcher')){
            register_setting( 'baw-settings-group', 'site_techside_cid' );
            // register_setting( 'baw-settings-group', 'site_techside_bg' );
        // }
        register_setting( 'baw-settings-group', 'site_acgnside_switcher' );
        // if(get_option('site_acgnside_switcher')){
            register_setting( 'baw-settings-group', 'site_acgnside_cid' );
            // register_setting( 'baw-settings-group', 'site_acgnside_num' );
        // }
        register_setting( 'baw-settings-group', 'site_default_postimg_switcher' );
        
        // register_setting( 'baw-settings-group', 'site_acgn_bg' );
        register_setting( 'baw-settings-group', 'site_acgn_video' );
        // register_setting( 'baw-settings-group', 'site_guestbook_bg' );
        register_setting( 'baw-settings-group', 'site_guestbook_video' );
        // register_setting( 'baw-settings-group', 'site_about_bg' );
        register_setting( 'baw-settings-group', 'site_about_video' );
        // register_setting( 'baw-settings-group', 'site_privacy_bg' );
        register_setting( 'baw-settings-group', 'site_privacy_video' );
        
        register_setting( 'baw-settings-group', 'site_lazyload_switcher' );
        register_setting( 'baw-settings-group', 'site_cdn_switcher' );
        // if(get_option('site_cdn_switcher')){
            register_setting( 'baw-settings-group', 'site_cdn_src' );
            register_setting( 'baw-settings-group', 'site_cdn_img' );
            register_setting( 'baw-settings-group', 'site_cdn_api' );
            // register_setting( 'baw-settings-group', 'site_cdn_vid' );
            // register_setting( 'baw-settings-group', 'site_cdn_vid_sw' );
            register_setting( 'baw-settings-group', 'site_cdn_vdo_includes' );
        // }
        register_setting( 'baw-settings-group', 'site_video_poster_switcher' );
        register_setting( 'baw-settings-group', 'site_video_capture_switcher' );
            register_setting( 'baw-settings-group', 'site_video_capture_gif' );
        register_setting( 'baw-settings-group', 'site_darkmode_switcher' );
        // if(get_option('site_darkmode_switcher')){
            register_setting( 'baw-settings-group', 'site_darkmode_start' );
            register_setting( 'baw-settings-group', 'site_darkmode_end' );
            register_setting( 'baw-settings-group', 'site_darkmode_fixed' );
        // }
        register_setting( 'baw-settings-group', 'site_avatar_mirror' );
        register_setting( 'baw-settings-group', 'site_pixiv_switcher' );
        // if(get_option('site_pixiv_switcher')){
            register_setting( 'baw-settings-group', 'site_bar_pixiv' );
        // }
        register_setting( 'baw-settings-group', 'site_scheduled_times' );
        register_setting( 'baw-settings-group', 'site_mostview_switcher' );
        // if(get_option('site_mostview_switcher')){
            // register_setting( 'baw-settings-group', 'site_mostview_cid' );
            register_setting( 'baw-settings-group', 'site_mostview_cat' );
        // }      
        register_setting( 'baw-settings-group', 'site_leancloud_switcher' );
        register_setting( 'baw-settings-group', 'site_third_comments' );
            register_setting( 'baw-settings-group', 'site_comment_blacklists' );
            register_setting( 'baw-settings-group', 'site_comment_blockoutside' );
            register_setting( 'baw-settings-group', 'site_forbidden_outsideborder' );
        // register_setting( 'baw-settings-group', 'site_valine_switcher' );
            register_setting( 'baw-settings-group', 'site_comment_serverchan' );
            register_setting( 'baw-settings-group', 'site_comment_pushplus' );
        register_setting( 'baw-settings-group', 'site_cloudflare_turnstile' );
            register_setting( 'baw-settings-group', 'site_cloudflare_turnstile_sitekey' );
            register_setting( 'baw-settings-group', 'site_cloudflare_turnstile_secretkey' );
            register_setting( 'baw-settings-group', 'site_cloudflare_turnstile_comments' );
            register_setting( 'baw-settings-group', 'site_cloudflare_turnstile_login' );
        // if(get_option('site_valine_switcher')){
        //     // register_setting( 'baw-settings-group', 'site_leancloud_sdk' );
        //     // register_setting( 'baw-settings-group', 'site_comment_qmsgchan' );
        // }else{
        //     // site_wpwx_notify_switcher
        // }
        // register_setting( 'baw-settings-group', 'site_twikoo_switcher' );
            register_setting( 'baw-settings-group', 'site_twikoo_envid' );
            register_setting( 'baw-settings-group', 'site_twikoo_version' );
            register_setting( 'baw-settings-group', 'site_ajax_comment_switcher');
            register_setting( 'baw-settings-group', 'site_ajax_comment_paginate');
        
        register_setting( 'baw-settings-group', 'site_wpwx_notify_switcher' );
        // if(get_option('site_wpwx_notify_switcher')){
            register_setting( 'baw-settings-group', 'site_wpwx_id' );
            register_setting( 'baw-settings-group', 'site_wpwx_agentid' );
            register_setting( 'baw-settings-group', 'site_wpwx_secret' );
            register_setting( 'baw-settings-group', 'site_wpwx_type' );
        // }
        // enable appid/key/server fields if any of avos actived(incase can not update if anyone of them disabled)
        // if(get_option('site_leancloud_switcher') || get_option('site_third_comments')=='Valine'){
            register_setting( 'baw-settings-group', 'site_leancloud_appid' );
            register_setting( 'baw-settings-group', 'site_leancloud_appkey' );
            register_setting( 'baw-settings-group', 'site_leancloud_server' );
            register_setting( 'baw-settings-group', 'site_leancloud_category' );
        // }
        
        register_setting( 'baw-settings-group', 'site_ads_switcher' );
        // if(get_option('site_ads_switcher')){
            // register_setting( 'baw-settings-group', 'site_ads_init' );
            register_setting( 'baw-settings-group', 'site_ads_article' );
            register_setting( 'baw-settings-group', 'site_ads_sidebar' );
        // }
        register_setting( 'baw-settings-group', 'site_smtp_switcher' );
        // if(get_option('site_smtp_switcher')){
            register_setting( 'baw-settings-group', 'site_smtp_mail' );
            register_setting( 'baw-settings-group', 'site_smtp_host' );
            register_setting( 'baw-settings-group', 'site_smtp_pswd' );
        // }
        register_setting( 'baw-settings-group', 'site_wpmail_switcher' );
        register_setting( 'baw-settings-group', 'site_xmlrpc_switcher' );
        register_setting( 'baw-settings-group', 'site_imgcrop_switcher' );
        
        register_setting( 'baw-settings-group', 'site_rss_categories' );
        register_setting( 'baw-settings-group', 'site_rss_update_interval' );
        register_setting( 'baw-settings-group', 'site_rss_update_count' );
        register_setting( 'baw-settings-group', 'site_map_switcher' );
        // if(get_option('site_map_switcher')){
            register_setting( 'baw-settings-group', 'site_map_includes' );
        // }
        
        register_setting( 'baw-settings-group', 'site_banner_array' );
        // register_setting( 'baw-settings-group', 'site_bottom_recent_cid' );
        register_setting( 'baw-settings-group', 'site_bottom_recent_cat' );
        register_setting( 'baw-settings-group', 'site_bottom_nav' );
        register_setting( 'baw-settings-group', 'site_construction_switcher' );
        register_setting( 'baw-settings-group', 'site_not_ai_switcher' );
        register_setting( 'baw-settings-group', 'site_monitor_switcher' );
        // if(get_option('site_monitor_switcher')){
            register_setting( 'baw-settings-group', 'site_monitor' );
        // }
        register_setting( 'baw-settings-group', 'site_chat_switcher' );
        // if(get_option('site_chat_switcher')){
            register_setting( 'baw-settings-group', 'site_chat' );
        // }
        register_setting( 'baw-settings-group', 'site_begain' );
        register_setting( 'baw-settings-group', 'site_copyright' );
        register_setting( 'baw-settings-group', 'site_beian_switcher' );
        // if(get_option('site_beian_switcher')){
            register_setting( 'baw-settings-group', 'site_beian' );
        // }
        register_setting( 'baw-settings-group', 'site_moe_beian_switcher' );
            register_setting( 'baw-settings-group', 'site_moe_beian_num' );
            register_setting( 'baw-settings-group', 'site_moe_beian_travel' );
        register_setting( 'baw-settings-group', 'site_server_side' );
        register_setting( 'baw-settings-group', 'site_foreverblog_switcher' );
        // if(get_option('site_foreverblog_switcher')){
            register_setting( 'baw-settings-group', 'site_foreverblog' );
            register_setting( 'baw-settings-group', 'site_foreverblog_wormhole' );
        // }
        register_setting( 'baw-settings-group', 'site_contact_email' );
        register_setting( 'baw-settings-group', 'site_contact_wechat' );
        register_setting( 'baw-settings-group', 'site_contact_weibo' );
        register_setting( 'baw-settings-group', 'site_contact_music' );
        register_setting( 'baw-settings-group', 'site_contact_bilibili' );
        register_setting( 'baw-settings-group', 'site_contact_github' );
        register_setting( 'baw-settings-group', 'site_contact_twitter' );
        register_setting( 'baw-settings-group', 'site_contact_steam' );
    }
    // $theme_blocks  = 'dodgerblue, crimson, orange, limegreen';
    $templates_info = array(
        'news' => get_cat_by_template('news'),
        'notes' => get_cat_by_template('notes'),
        'weblog' => get_cat_by_template('weblog'),
        'acg' => get_cat_by_template('acg'),
        'guestbook' => get_cat_by_template('guestbook'),
        'about' => get_cat_by_template('about'),
        '2bfriends' => get_cat_by_template('2bfriends'),
        'download' => get_cat_by_template('download'),
        'footprints' => get_cat_by_template('footprints'),
        'archive' => get_cat_by_template('archive'),
        'ranks' => get_cat_by_template('ranks'),
        'privacy' => get_cat_by_template('privacy'),
        'goods' => get_cat_by_template('goods'),
    );
    function category_options($value) {
        // global $cats;
        $cats = get_categories(meta_query_categories(0,'ASC','seo_order'));
        if(!empty($cats)){
            foreach($cats as $the_cat){
                $cats_id = $the_cat->term_id;
                echo '<option value="'.$cats_id.'"';
                    if($value==$cats_id) echo('selected="selected"');
                echo '>'.$the_cat->name.'</option>';
                $catss = get_categories(meta_query_categories($cats_id,'ASC','seo_order'));
                if(!empty($catss)){
                    foreach($catss as $the_cats){
                        $catss_id = $the_cats->term_id;
                        echo '<option value="'.$catss_id.'"';
                            if($value==$catss_id) echo('selected="selected"');
                        echo '>— '.$the_cats->name.'</option>';  //&nbsp;&nbsp;
                        $catsss = get_categories(meta_query_categories($catss_id,'ASC','seo_order'));
                        if(!empty($catsss)){
                            foreach($catsss as $the_catss){
                                $catsss_id = $the_catss->term_id;
                                echo '<option value="'.$catsss_id.'"';
                                    if($value==$catsss_id) echo('selected="selected"');
                                echo '>—— '.$the_catss->name.'</option>';  //&nbsp;&nbsp;&nbsp;&nbsp;
                                $catssss = get_categories(meta_query_categories($catsss_id,'ASC','seo_order'));
                                if(!empty($catssss)){
                                    foreach($catssss as $the_catsss){
                                        $catssss_id = $the_catsss->term_id;
                                        echo '<option value="'.$catssss_id.'"';
                                            if($value==$catssss_id) echo('selected="selected"');
                                        echo '>——— '.$the_catsss->name.'</option>';  //&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    function check_status($opt='') {
        if(!$opt) return;
        return get_option($opt) ? "checked" : "closed";
    }
    function output_article_opts($opt, $value) {
        global $templates_info;
        $article_opts = array($templates_info['news'], $templates_info['notes'], $templates_info['weblog']);
        $pre_array = explode(',',trim($value));  // NO "," Array
        // $pre_array_count = count($pre_array);
        foreach ($article_opts as $option){
            if ($option->error) continue;
            $opts_key = $option->name;
            $opts_val = $option->term_id;
            $checking = in_array($opts_val, $pre_array) ? 'checked' : '';
            echo '<input id="'.$opt.'_'.$opts_key.'" type="checkbox" value="'.$opts_val.'" '.$checking.' /><label for="'.$opt.'_'.$opts_key.'">'.strtoupper($opts_key).'</label>';
        }
    }
    function add_options_submenu() {
        global $templates_info, $cached_post;
        $cats = get_categories(meta_query_categories(0,'ASC','seo_order'));
        $img_cdn = custom_cdn_src('img', true);
        $cats_haschild = array();
        $cats_seclevel = array();
        foreach($cats as $the_cat){
            if(count(get_term_children($the_cat->term_id,$the_cat->taxonomy))>0) array_push($cats_seclevel, $the_cat);  // has-child category only
            // array_push($cats_toplevel, $the_cat);
            if($the_cat->count>=1) array_push($cats_haschild, $the_cat);  //push 1st category which has posts
        }
?>
    <div class="wrap settings <?php the_panel_theme(); ?>">
        <style>
            :root{
                --panel-theme: <?php echo get_option('site_theme','#eb6844'); ?>;
            }
            @media screen and (max-width:760px) {
                #wpcontent,
                .switchTab li {
                    padding: 5px 10px!important;
                    margin: 5px auto!important;
                }
                p.submit:first-child {top: 85px!important;}
                .fixed .switchTab{
                    top: 22px!important;
                    width: 82%!important;
                }
                .auto-fold #wpcontent {
                    margin:auto!important;
                }
            }
            @keyframes scale {
                0% {
                    transform: scale(.85);
                    transform-origin: top
                }
            
                to {
                    transform: none
                }
            }
            
            @supports (animation-timeline:view()) {
                table > tbody > tr {
                    animation: scale 1s;
                    transform: none;
                    animation-timeline: view(block);
                    animation-range: cover 0 30%;
                    /*will-change: transform;*/
                }
            }
        p.description code{font-size:small;font-family: monospace;border-radius: 5px;margin:auto 5px;padding:5px 0;}textarea.codeblock{height:233px}textarea{min-width:50%;min-height:88px;overscroll-behavior:contain;}.child_option th{text-indent:3em;opacity: .75;font-size:smaller!important}.child_option td{background:linear-gradient(90deg,rgba(255, 255, 255, 0) 0%, #fff 100%);background:-webkit-linear-gradient(0deg,rgba(255, 255, 255, 0) 0%, #fff 100%);border-right:1px solid #e9e9e9;}.child_option td b{font-size:12px;font-style:inherit;}.btn{border: 1px solid;padding: 2px 8px;border-radius: 25px;border-top-left-radius:0;font-size: smaller;font-weight:bold;background:white;font-weight:900;background:-webkit-linear-gradient(-90deg,rgba(255, 255, 255, 0) 55%, currentColor 255%);background:linear-gradient(180deg,rgba(255, 255, 255, 0) 25%, currentColor 255%);}label:hover input[type=checkbox]{box-shadow:0 0 15px #2271b1;}input[type=checkbox]{margin:-1px 3px 0 0;}input[type=checkbox] + b.closed{/*opacity:.75;*/}input[type=checkbox]{vertical-align:middle!important;}input[type=checkbox] + b.checked{opacity:1;}.submit{text-align:center!important;padding:0;margin-top:35px!important}.submit input{padding: 5px 35px!important;border-radius: 25px!important;border: none!important;box-shadow:0 0 0 5px rgba(34, 113, 177, 0.15)}b{font-weight:900!important;font-style:italic;letter-spacing:normal;}input[type=color]{width:220px;height:20px;cursor:pointer;box-shadow:0 0 20px var(--panel-theme);padding: 3px 6px!important;background:transparent;border-color:transparent;/*border-top-right-radius: 15px!important;*/}h1{padding:35px 0 15px!important;font-size:2rem!important;text-align:center;letter-spacing:2px}h1 p.en{margin: 5px auto auto;opacity: .5;font-size: 10px;letter-spacing:normal}h1 b.num{color: white;background: black;border:2px solid black;letter-spacing: normal;margin-right:10px;padding:0 10px 3px;box-shadow:-5px -5px 0 rgb(0 0 0 / 10%);border-radius: 20px;border-top-right-radius: 0;border-bottom-left-radius: 0;}p.description{font-size:small;}table{margin:0 auto!important;max-width:95%}.form-table tr.dynamic_opts{display:none}.form-table tr.dynamic_optshow{display:table-row!important}.form-table tr.disabled{opacity:.75;pointer-events:none}.form-table tr:hover > th{padding: 15px 0 15px 35px;color: var(--panel-theme)!important;}.form-table tr:hover > th sup{color:var(--panel-theme)}.form-table tr:hover > td{background:inherit;padding: 15px 25px 15px 0;}.form-table tr:hover{border-left-color:var(--panel-theme);box-sizing: border-box;background: linear-gradient(90deg, #f5f7f9 0, #fff);background: -webkit-linear-gradient(0deg, #f5f7f9 0, #fff);background:white;}.form-table tr{}.form-table tr:hover{box-shadow:0 0 20px rgb(0 0 0 / 5%);}.form-table tr{padding: 0 15px;border:2px solid transparent;/*border-bottom:1px solid #e9e9e9;*/border-left:3px solid transparent;border-radius: 18px;position: relative;z-index: 1;}.form-table th{padding:15px 25px;vertical-align:middle!important;transition:padding .15s ease;}.form-table th sup#tips{border: 0;padding: 0;text-decoration: overline;opacity: .75;}.form-table th sup{border: 1px solid;padding: 1px 5px 2px;margin-left: 7px;border-radius: 5px;font-size: 10px;cursor:help;}.form-table label{display:block;-webkit-user-select:none;cursor:pointer;}.form-table td{text-align:right;transition: padding .35s ease;border-radius:inherit;border-top-left-radius: 0;border-bottom-left-radius: 0;}/*.form-table tr:last-child{border-bottom:none}*/.form-table td input.array-text{box-shadow:0 0 15px #a0d5ff;margin:15px 0 0 auto;display:block;/*border:2px solid*/}.form-table td del{opacity:.5}.form-table td p{font-size:smaller;margin-top:0!important;margin-bottom:10px!important;/*font-weight:200;*/}p.submit:first-child{position:fixed;top:115px;right:-180px;transform:translateX(-50px);z-index:9;transition:transform .75s cubic-bezier(0.68, -0.55, 0.27, 1.35);}p.submit:first-child input:hover{box-shadow: rgb(0 0 0 / 10%) 0 0 20px;color:var(--panel-theme);border: 2px solid #fff!important;box-sizing: border-box;background: linear-gradient(90deg, rgb(245 247 249 / 100%) 0, rgb(255 255 255 / 100%));padding-left:25px!important;}p.submit:first-child input{font-weight:bold;padding-left:20px!important;transition:padding .35s ease;background:white;box-shadow:0px 20px 20px 0px rgb(0 0 0 / 15%);border:3px solid var(--panel-theme)!important;background:rgb(10, 20, 28);/*border-top-left-radius: 0 !important;*/}p.submit:first-child input:focus{color:white;background:var(--panel-theme);box-shadow:0 0 0 1px #fff, 0 0 0 3px transparent;/*border-color:black!important*/}.upload_preview.img{vertical-align: middle;width:55px;height:55px;margin: auto;}#upload_banner_button{margin:10px auto;/*margin:10px;*/}.upload_preview_list em{margin-left:10px!important}.upload_preview_list em,.upload_preview_list video{margin:auto auto 10px 10px;width:115px!important;height:55px!important;}.upload_preview.bgm{object-fit:cover;}.upload_preview.bgm,.upload_preview_list em,.upload_preview.bg{height:55px;width:100px;vertical-align:middle;border-radius:5px;display:inline-block;}
            .upload_button:focus,.upload_button:hover{background:var(--panel-theme)!important;box-shadow:0 0 0 2px #fff, 0 0 0 4px var(--panel-theme)!important;border-color:transparent!important;}.upload_button.multi{/*background:var(--panel-theme);border-color:transparent;*/}.upload_button{margin-left:10px!important;background:black;border-radius:50px;}
            label.upload:before{content: "点击更换";width: 100%;height: 100%;font-size: smaller;font-weight:bold;text-align: center;color: var(--panel-theme);background: rgb(0 0 0 / 5%);box-sizing:border-box;border-radius: inherit;position: absolute;top: 0;left: 0;opacity:0;line-height:55px;backdrop-filter:blur(10px);}label.upload:hover:before{opacity:1}label.upload{display:inline-block;margin: auto 15px;border-radius: 10px;position: relative;overflow: hidden;}label.upload.upload_preview_list{margin-right: 0}
            .formtable{display:none;}.formtable.show{display:block;}.fixed p.submit:first-child{transform:translateX(-150px);/*right:-80px*/}.switchTab.fixed{/*position: fixed;width: 100%;top: 32px;left:0;padding-left:160px;*/}.fixed .switchTab{/*width:max-content;top: 55px;width: 360px;*/transform:translateY(20px);transition-duration: .55s;/*padding: 5px;*/}.switchTab{overflow:hidden;border-radius: 50px;border-top-right-radius:0;width:100%;max-width:max-content;padding:10px 20px;transition:transform .55s cubic-bezier(0.68, -0.55, 0.27, 1.55);margin:0 auto;top:32px;position:sticky;z-index: 9;box-sizing:border-box;box-shadow:rgb(0 0 0 / 5%) 0px 20px 20px;border: 1px solid #eee;box-sizing: border-box;/*transition: top .35s ease;top: -32px;padding: 0;background: rgb(255 255 255 / 75%);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(20px);background: linear-gradient(0deg, rgb(245 247 249 / 66%) 0, rgb(255 255 255 / 88%));background: -webkit-linear-gradient(90deg, rgb(245 247 249 / 66%) 0, rgb(255 255 255 / 88%));*/background-image: radial-gradient(rgb(255 255 255 / 55%) 2px, rgb(255 255 255) 2px);background-size: 4px 4px;backdrop-filter: saturate(150%) blur(5px);-webkit-backdrop-filter: saturate(150%) blur(5px);}.switchTab ul{margin:auto;padding:0;text-align:center;}.switchTab li:active{transform:scale(0.85);-webkit-transform:scale(0.85);will-change:transform;}.switchTab li.active:before{content: '';display: block;width: 100%;height: 100%;position: absolute;top: 0;left: 0;border-radius: inherit;background: currentColor;opacity: .15;}.switchTab li.active{color:var(--panel-theme);}.switchTab li:hover{color:var(--panel-theme);}.switchTab li.active,.switchTab li:hover b{text-shadow:none}.switchTab li.active,.switchTab li:active{color: var(--panel-theme);box-shadow: 0 0 0 4px #fff, 0 0 0 6px var(--panel-theme);}.switchTab li{display:inline-block;padding:5px 15px;margin:10px 2px;cursor:pointer;font-size:initial;font-style:normal;font-weight:bold;border-radius:25px;user-select: none;-webkit-user-select: none;transition:transform .15s ease;position:relative;border-top-right-radius: 0;}
            .smtp{margin-left:10px;vertical-align:middle;}
            #loading.responsed{-webkit-animation-duration:.35s!important;animation-duration:.35s!important;}
            #loading.responsing{-webkit-animation:rotateloop .5s infinite linear;animation:rotateloop .5s infinite linear}
            #loading.responsing.ok:before{border-color:limegreen;}
            #loading.responsing.err:before{border-color:orangered;}
            #loading{position: relative;padding: 20px;display: inline-block;vertical-align:middle;}
            #loading:before{-webkit-box-sizing:border-box;box-sizing:border-box;content:"";position:absolute;display:inline-block;top:0px;left:50%;margin-left:-20px;width:40px;height:40px;border:6px double #a0a0a0;border-top-color:transparent!important;border-bottom-color:transparent!important;border-radius:50%;}
            @keyframes rotateloop{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg);}100%{-webkit-transform:rotate(360deg);transform:rotate(360deg);}
            }
            .form-table .checkboxes,
            .form-table .checkbox{display:inline-block;border-radius:5px;padding:5px 0 5px 15px;}
            .form-table .checkboxes input[type=checkbox],
            .form-table .checkbox input[type=checkbox]{margin:auto}
            .form-table .checkboxes label,
            .form-table .checkbox label{display:inline-block;padding:1px 15px 0 5px;font-weight:bold;font-size:smaller}
            .form-table .checkboxes label:last-of-type,
            .form-table .checkbox label:last-of-type{padding-right:5px;}
            #wpcontent{padding:0!important}
            .wrap.settings hr,.wrap.settings{margin:0}
            ul.cached_post_list{margin:15px auto auto;padding:0;position:relative}
            ul.cached_post_list li:hover{opacity:.75;/*border-color:transparent;text-decoration:underline;border-style:dashed;background:whitesmoke;*/}
            /*ul.cached_post_list li:hover::before{content:attr(data-title)}*/
            /*ul.cached_post_list li:hover::after{content:'×';width:15px;height:15px;position:absolute;top:5px;right:5px;border:1px solid;border-radius:50%;line-height:14px;background:whitesmoke}*/
            ul.cached_post_list li:hover::before{content:attr(title);content:'重新摘要';font-size:small;}
            ul.cached_post_list li:hover::after{display:block}
            ul.cached_post_list li:before{
                content:attr(data-id);
                max-width: 15em;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: block;
            }
            ul.cached_post_list li:after{
                content: attr(data-content);
                text-align: left;
                font-size: smaller;
                line-height: 1.85em;
                padding: 10px 15px;
                border-radius: 15px;
                border-top-right-radius: 0;
                background: #222;
                position: absolute;
                color: white;
                bottom: -25px;
                right: -10px;
                transform: translate(0%, 100%);
                display: none;
            }
            ul.cached_post_list li{list-style-type:none;display:inline-block;margin-left:5px;margin-bottom:2px;border:1px solid #ddd;padding:5px 10px;border-radius:8px;cursor:pointer;text-align:center;/*white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:38px;position:relative*/}
            .dynamic_dom {display: inline-block;margin-bottom: 15px;text-decoration: underline;position:relative;z-index:9;cursor: help;}
            .upload .dynamic_dom {margin: auto;padding: 10px 15px;background: #f0f0f1;}
            .update-target {padding: 5px 10px;margin: auto 5px; min-height: 29px;border-radius: 50px;cursor: help;box-shadow: none !important;}
            /**
            **  UI/UE
            **  Enhancements
            **  2025
            **/
            /*p.description {*/
            /*    opacity: .75;*/
            /*}*/
            .child_option td b,
            .form-table b {
                font-style: normal;
                font-size: smaller;
            }
            .wrap.dark {
                /*background-image: radial-gradient(rgb(23 34 46) 1px, rgb(10, 20, 28) 1px);*/
                background-color: rgb(10, 20, 28);
                background-image: radial-gradient(rgb(23 34 46) 1px, transparent 1px), radial-gradient(rgb(23 34 46) 1px, transparent 1px);
            }
            .wrap.dark .switchTab {
                color: #ccc;
                background-image: radial-gradient(rgb(18 27 36) 2px, rgb(10, 20, 28, .5) 2px);
                border-color: rgb(28 41 52);
            }
            .wrap.dark input[type=button] {
                box-shadow: 0 0 0 3px rgb(28 41 52), 0 0 0 4px #2271b1;
            }
            .wrap.dark .switchTab li.active,
            .wrap.dark .switchTab li:active {
                box-shadow: 0 0 0 4px rgb(28 41 52), 0 0 0 6px var(--panel-theme);
            }
            /*.wrap.dark input[type=button],*/
            /*.wrap.dark select,*/
            .wrap.dark input[type=text],
            .wrap.dark input[type=number],
            .wrap.dark input[type=checkbox],
            .wrap.dark textarea {
                color: #ccc;
                background: rgb(14 22 30);
                border-color: #666;
            }
            .wrap.dark input[type=text],
            .wrap.dark h1 b.num,
            .wrap.dark ul.cached_post_list li,
            .wrap.dark textarea {
                border-color: rgb(28 41 52);
            }
            .wrap.dark .checkbox > input[type=checkbox]:checked,
            .wrap.dark .form-table td input.array-text {
                background: rgb(14 22 30)!important;
            }
            .wrap.dark .form-table td input.array-text {
                color: #666;
                border-color: rgb(32 43 52);
            }
            .wrap.dark .upload .dynamic_dom {
                color: #ccc;
                background: rgb(23 34 46);
            }
            .wrap.dark ul.cached_post_list li:after,
            .wrap.dark ul.cached_post_list li,
            .wrap.dark .form-table tr:hover {
                background: rgb(23 34 46);
            }
            .wrap.dark .child_option td {
                background: linear-gradient(90deg, transparent 0%, rgb(23 34 46) 100%);
                background: -webkit-linear-gradient(0deg, transparent 0%, rgb(23 34 46) 100%);
            }
            .wrap.dark .form-table th,
            .wrap.dark .form-table .checkbox label,
            .wrap.dark h1,
            .wrap.dark {
                color: #eee;
                text-shadow: none!important;
            }
            .wrap.dark h1 b {
                color: transparent;
                background: -webkit-linear-gradient(0deg, transparent -10%, #fafafa 100%);
                background: linear-gradient(90deg, transparent 0%, #fafafa 100%);
                background-clip: text;
                -webkit-background-clip: text;
            }
            .wrap.dark .btn,
            .wrap.dark h1 b.num {
                filter: brightness(1.25) contrast(1.35);
            }
            .wrap.dark .btn {
                text-shadow: 0px -5px 15px currentColor;
            }
            .wrap.dark img[alt=wormhole],
            .wrap.dark img[alt=notbyai] {
                filter: invert(1);
            }
            .wrap.dark .switchTab li.active:before {
                box-shadow: -5px -5px 20px 15px currentColor;
            }
            /*
            ** prefers-color-scheme
            ** fix of background-flicker on darkmode
            */
            /*@media (prefers-color-scheme: dark) {*/
            /*    body {*/
                    /*background: #1d2327;*/
            /*        background: rgb(10, 20, 28);*/
            /*    }*/
            /*}*/
            #wpbody-content {
                padding-bottom: 0;
            }
            .wrap {
                /*background-image: radial-gradient(#eee 1px, #fafafa 1px);*/
                /*background-size: 10px 10px;*/
                background-color: #fafafa;
                background-image: radial-gradient(#eee 1px, transparent 1px), radial-gradient(#eee 1px, transparent 1px);
                background-size: 12px 12px;
                background-position: 0 0, 6px 6px;
                padding-bottom: 5%;
                /* fix of background-flicker on darkmode */
                min-height: 100vh;
            }
            .upload_preview.img[src=''] {
                visibility: hidden;
            }
            .fixed p.submit:first-child:active {
                transform: scale(0.95) translateX(-150px);
                transition-duration: .35s;
            }
            .fixed .switchTab:active {
                transform: scale(0.95) translateY(20px);
            }
            .switchTab:active {
                transition-duration: .35s;
                transform: scale(0.95) translateY(0);
            }
            .form-table tr:active {
                transform: scale(0.99);
            }
            /*.switchTab,*/
            /*.switchTab li,*/
            .form-table tr {
                transition: .15s ease;
                transition-property: transform, top;
                will-change: transform;
            }
            /*.switchTab li:active {*/
            /*    transform: scale(1.15);*/
            /*}*/
            select:active,
            label.upload:active,
            .cached_post_list li:active,
            input[type*=time]:active,
            input[type*=date]:active,
            input[type=color]:active,
            input[type=number]:active,
            input[type=button]:active {
                transform: scale(1.15);
            }
            select,
            label.upload,
            .cached_post_list li,
            input[type*=time],
            input[type*=date],
            input[type=color],
            input[type=number],
            input[type=button] {
                transition: transform .15s ease;
            }
            @keyframes sliderSwitch {
                0% {
                    opacity: 0;
                }
                99% {
                    opacity: .99;
                    display: none;
                }
                100% {
                    opacity: 1;
                    display: table-row;
                }
            }
            .form-table tr.dynamic_optshow {
                animation: sliderSwitch 1s 1 forwards ease;
            }
            .form-table tr.dynamic_opts {
                opacity: 0;
                /*height: 0;*/
                /*display: table-row;*/
            }
            input[type=button] {
                box-shadow: 0 0 0 3px #fff, 0 0 0 4px #2271b1;
            }
            input[type=number].small-text {
                max-width: 4em;
            }
            input[type=color]:focus {
                box-shadow: 0 0 15px var(--panel-theme);
            }
            .form-table td input.array-text {
                opacity: .75;
                box-shadow: none;
                border-color: lightgray;
                padding: 0 15px;
                font-size: smaller;
                /*pointer-events: none;*/
            }
            h1 b.num:before {
                content: '';
                display: block;
                width: 100%;
                height: 100%;
                background: -webkit-linear-gradient(315deg, currentColor 0%, transparent 80%);
                background: linear-gradient(135deg, currentColor 0%, transparent 80%);
                border-radius: inherit;
                position: absolute;
                top: -5px;
                left: -5px;
                opacity: .25;
            }
            h1 b.num {
                border-color: currentColor;
                background: white;
                border: 1px solid #fafafa;
                /*font-style: normal;*/
                text-shadow: -15px -15px 20px currentColor;
                box-shadow: -5px -5px 0 rgb(215 215 215 / 15%);
                position: relative;
            }
            label:hover input[type=checkbox] + * {
                opacity: 1 !important;
            }
            label:hover input[type=checkbox]:checked {
                box-shadow:0 0 15px var(--panel-theme);
            }
            select,
            textarea,
            input {
                border-radius: 15px !important;
                border-top-right-radius: 0 !important;
                padding-left: 10px !important;
            }
            textarea {
                padding-top: 10px;
            }
            input[type=checkbox],
            .form-table td input[type=checkbox] {
                float: right;
                margin: 2px 0px 0 10px;
                /*transition: all .35s cubic-bezier(1, 0.5, 0.5, 1);*/
                transition: all .35s ease;
                border-top-right-radius: 15px!important;
            }
            .wrap.dark input[type=checkbox]:checked,
            input[type=checkbox]:checked:focus {
                color: var(--panel-theme);
                border-color: currentColor;
                box-shadow: 0 0 15px currentColor;
            }
            input[type=checkbox] + * {
                opacity: .66!important;
                transition: opacity .35s ease;
            }
            .checkbox input[type=checkbox]:checked + *,
            input[type=checkbox]:checked + * {
                opacity: 1!important;
            }
            input[type=checkbox],
            input[type=radio] {
                padding: 5px !important;
                display: inline-block;
                width: 35px;
                border-radius: 50px;
                position: relative;
                box-shadow: none;
            }
            input[type=checkbox]:checked {
                background: var(--panel-theme)!important;
                border-color: var(--panel-theme)!important;
            }
            input[type=checkbox]::before {
                content: "" !important;
                width: 10px !important;
                height: 10px !important;
                border-radius: inherit;
                /*background: var(--panel-theme);*/
                background: #2271b1;
                transition: inherit;
                position: absolute;
                top: 50%;
                left: 3px;
                transform: translate(0, -50%);
            }
            .checkbox > input[type=checkbox]::before, /** fix checkbox slider offsets **/
            input[type=checkbox]:checked::before {
                background: white;
                left: 100%;
                margin: auto;
                margin-left: -12px;
            }
            /*.checkbox label {*/
            /*    opacity: .88;*/
            /*}*/
            /*.checkbox > input[type=checkbox]:checked + label {*/
            /*    opacity: 1;*/
            /*}*/
            .checkbox > input[type=checkbox] + * {
                opacity: .66!important;
            }
            .checkbox > input[type=checkbox]:checked {
                background: white !important;
                border-color: gray;
                /*padding: 0 !important;*/
            }
            .checkbox > input[type=checkbox]:checked::before {
                background: var(--panel-theme) !important;
            }
            .checkbox > input[type=checkbox]::before {
                background: transparent !important;
            }
            .checkbox > input[type=checkbox] {
                max-width: 0;
                float: none!important;
            }
            .theme_blocks input[type=color]:last-child {
                margin-right: 5px;
            }
            .theme_blocks input[type=color]:hover {
                opacity: 1;
                border-color: transparent;
            }
            .theme_blocks input[type=color] {
                display: inline-block;
                width: 38px;
                height: 14px;
                padding: 0 5px!important;
                border-radius: 15px!important;
                margin-right: 15px;
                border: 1px solid whitesmoke;
                background: currentColor;
                box-shadow: 10px 0px 20px currentColor;
                position: relative;
                /*overflow: hidden;*/
                cursor: pointer;
                opacity: .75;
            }
            /*.wrap.dark .theme_blocks input[type=color] {*/
            /*    box-shadow: 0 0 15px currentColor;*/
            /*}*/
        </style>
        <h1 style="text-align: center;font-size: 3rem!important;font-weight:100;letter-spacing:2px;padding: 35px 0!important;text-shadow:1px 1px 0 white;"><b>2BLOG</b> 主题预设 <b>&#60;<span style="color:var(--panel-theme);text-shadow: -10px 0 30px currentColor;">&#47;</span>&#62;</b><p style="letter-spacing:normal;margin-bottom:auto;opacity:.75;"> 第三方 LeanCloud 数据储存服务将于 2027 停服，相关设置将停用<sup> （此主题需<ins>开发经验</ins>） </sup> </p></h1>
        <!--<hr/>-->
        <div class="switchTab">
            <ul>
                <li id="basic" class="">基本</li>
                <li id="common">通用</li>
                <li id="index">页面</li>
                <li id="sidebar">边栏</li>
                <li id="footer">页尾</li>
                <!--<li id="contact"><b>联系方式</b></li>-->
            </ul>
        </div>
        <!--<hr/>-->
        <form method="post" action="options.php" style="--block-one: <?php $theme_array = get_theme_array(true); echo trim($theme_array[0]); ?>;--block-two: <?php echo trim($theme_array[1]);; ?>;--block-three: <?php echo trim($theme_array[2]);; ?>;--block-four: <?php echo trim($theme_array[3]); ?>;">
            <?php 
                submit_button('立即提交');
                settings_fields( 'baw-settings-group' ); // 设置字段 这个函数取代了 nonce magic, action field, and page_options
                do_settings_sections( 'baw-settings-group' ); // 这个函数取代了表单字段标记形式本身
                 //$theme_blocks; //explode(',', get_option('site_theme_array'));
            ?>
            <div class="formtable basic">
                <h1><b class="num" style="color:var(--panel-theme);">01</b>基本信息<p class="en">BASIC INFO</p></h1>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">主题颜色</th>
                        <td>
                            <?php
                                $opt = 'site_theme';
                                $value = get_option($opt);
                                $preset = "#eb6844";
                                if(!$value) update_option($opt, $preset);else $preset=$value;
                                echo '<label for="'.$opt.'"><p class="description" id="site_theme_label">此选项将重写网站主题色及后台设置高亮，支持颜色选择器自动取色（后台即时生效，预设默认值 #eb6844</p><input type="color" name="'.$opt.'" id="'.$opt.'" value="' . $preset . '"/></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">随机主题</th>
                        <td>
                            <?php
                                $opt = 'site_theme_rand_sw';
                                $status = check_status($opt);
                                $theme_blocks = get_theme_array(); //get_option('site_theme_array'); //
                                echo '<label for="'.$opt.'"><p class="description" id="site_inform_switcher_label">根据自定义颜色值，访问页面时将<b>随机</b>调用其中一项作为主色调</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">RandTheme</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="child_option">
                        <th scope="row">主题色块 <sup>Blocks</sup></th>
                        <td>
                            <p class="description" id="site_theme_array_label">自定义一组色值，使用逗号“,”间隔，支持颜色选择器自动取色（后台即时生效，此项同时应用于设置页面及首页卡片色块，预设默认值 + 主题色</p>
                            <?php
                                $opt = 'site_theme_array';
                                $value = get_option($opt);
                                $preset = $theme_blocks . ', ' . get_option('site_theme');
                                if(!$value) update_option($opt, $preset);else $preset=$value;
                            ?>
                            <input type="text" name="site_theme_array" id="site_theme_array" class="regular-text" placeholder="主题色数组" value="<?php echo $preset; ?>"/>
                            <ul class="theme_blocks">
                                <?php
                                    foreach ($theme_array as $arr) {
                                        $arr = trim($arr);
                                        echo '<input class="block_colors" type="color" style="color:'.$arr.'" value="'.$arr.'" data-last="'.$arr.'" />';
                                    }
                                ?>
                            </ul>
                        </td>
                    </tr>
                    <tr valign="top" class="dynamic_box logo">
                        <th scope="row">站点 LOGO</th>
                        <td>
                            <?php
                                $opt = 'site_logo_switcher';
                                $value = get_option($opt);
                                // $data = get_option( 'site_logo', '' );
                                $status = $value ? "checked" : "check";
                                //设置默认开启（仅适用存在默认值的checkbox）
                                // if(!$value&&!$data){
                                //     update_option($opt, "on_default");
                                //     $status="checked";
                                // }else{
                                //     $status = $value ? "checked" : "check";
                                // };
                                echo '<label for="'.$opt.'"><p class="description" id="site_logo_switcher_label">站点 logo 图片，开启后可指定深色显示LOGO（默认显示<del>文字类型的站点名称</del>SVG(XTy)，可自适应深色显示模式</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:steelblue;" class="btn">LOGO</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_logo_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $logo = get_option('site_logo_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— LOGO 图片链接（默认）</th>
                                <td>
                                    <?php 
                                        $opt = 'site_logo';
                                        $value = get_option($opt);
                                        // $preset = $img_cdn.'/images/svg/XTy_115x35.svg';
                                        // $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                        echo '<p class="description" id="site_logo_label">站点 LOGO 图片链接（应用于全站，留空默认SVG</p><label for="'.$opt.'" class="upload"><img src="'.$value.'" class="upload_preview img" style="width:80px;" /></label><input type="text" name="'.$opt.'" placeholder="默认使用 XTY 矢量图" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $logo; ?>">
                                <th scope="row">— LOGO 图片链接（深色）</th>
                                <td>
                                    <?php 
                                        $opt = 'site_logos';
                                        $value = get_option($opt);
                                        // $preset = get_option('site_logo', $img_cdn.'/images/svg/XTy_115x35_light.svg');
                                        // $value ? $preset=$value : update_option($opt, $preset);
                                        echo '<p class="description" id="site_logos_label">站点 LOGO（深色）图片链接（应用于深色模式，默认上方LOGO，留空默认SVG</p><label for="'.$opt.'" class="upload"><img src="'.$value.'" class="upload_preview img" style="width:80px;" /></label><input type="text" name="'.$opt.'" placeholder="默认使用上方 LOGO" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $logo; ?>">
                                <th scope="row">— LOGO 矢量代码<sup id="tips">SVG</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_logo_svg';
                                        $value = get_option($opt);
                                        $preset = '<svg height=35px style="enable-background:new 0 0 115 35" version=1.1 viewBox="0 0 115 35" width=115px xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink>
	<style>
		body.dark .st0 { fill: var(--preset-s); }
		.st0 { fill:var(--preset-5a); }
		.st1{ fill:var(--theme-color); }
	</style>
	<polyline class=st0 id=y_2_ points="109.8,0 79.2,35 72.4,35 103,0 " />
	<polygon class=st1 id=ty_2_ points="79.2,0 72.4,0 76.8,5.1 81.3,10.3 88.1,10.3 " />
	<rect class=st0 height=35 id=t_2_ width=5.1 x=54.6 y=0 />
	<polygon class=st1 id=xt_1_ points="33.7,10.3 26.9,10.3 31.3,5.1 35.8,0 42.7,0 " />
	<polyline class=st0 id=x_4_ points="5.2,0 35.8,35 42.6,35 12,0 " />
	<polygon class=st0 id=x_3_ points="12,35 5.2,35 9.7,29.9 14.2,24.8 21,24.8 " />
</svg>';
                                        if(!$value) update_option($opt, $preset);else $preset=$value;
                                        echo '<p class="description" id="site_logo_svg_label">站点 LOGO 矢量代码（注：开启任意图片LOGO后此项失效，可使用<code> body.dark </code>或<code> prefers-color-scheme </code>自适应深色样式</p><textarea name="'.$opt.'" id="'.$opt.'" rows="8" placeholder="此项始终存在默认值">'.$preset.'</textarea>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">博主昵称</th>
                        <td>
                            <p class="description" id="site_nick_label">网站标题、底部描述、文章作者、来源等信息均会使用到此信息（默认站点名称）</p>
                            <?php
                                $value = get_option( 'site_nick', '' );
                                $preset = get_bloginfo('name');
                                if(!$value) update_option( 'site_nick', $preset );else $preset=$value;
                            ?>
                            <input type="text" name="site_nick" id="site_nick" class="middle-text" value="<?php echo esc_attr($preset); ?>" placeholder="博主昵称">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">个人头像</th>
                        <td>
                            <?php 
                                $opt = 'site_avatar';
                                $value = get_option($opt);
                                $mail = 'wapuu@wordpress.example';//get_bloginfo('admin_email');
                                // !$mail ? $mail="wapuu@wordpress.example" : $mail;
                                $preset = 'https:' . get_option('site_avatar_mirror','//cravatar.cn/') . 'avatar/' . md5($mail) . '?s=300';
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if avatar unset
                                echo '<p class="description" id="site_avatar_label">个人头像，用于笔记栈、关于等页面（默认管理员邮箱 gravatar 头像</p><label for="'.$opt.'" class="upload"><img src="'.$preset.'" class="upload_preview img" style="border-radius: 100%;" /></label><input type="text" name="'.$opt.'" placeholder="默认使用 gravatar 头像" class="regular-text upload_field" value="' . $preset . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">背景图片</th>
                        <td>
                            <?php
                                $opt = 'site_bgimg';
                                $value = get_option($opt);
                                $preset = 'https://s0.wp.com/mshots/v1/' . get_bloginfo('url') . '?w=&h=&quality=100'; // $img_cdn.'/images/fox.jpg';
                                // $preset = 'https:'.get_option('site_avatar_mirror','//sdn.geekzu.org/').'/avatar/?d=identicon&s=300';
                                $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="">默认背景图，用于各页面调用（默认站点首页预览</p><label for="'.$opt.'" class="upload"><em class="upload_preview bg" style="background:url('.$preset.') center center /cover;"></em></label><input type="text" name="'.$opt.'" class="regular-text upload_field" value="' . $preset . '" placeholder="'.$preset.'"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">站点描述</th>
                        <td>
                            <textarea name="site_description" id="site_description" placeholder="站点描述"><?php echo esc_attr(get_option('site_description')); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">站点关键词</th>
                        <td>
                            <textarea name="site_keywords" id="site_keywords" placeholder="站点关键词"><?php echo esc_attr(get_option('site_keywords')); ?></textarea>
                            <!--<input type="text" name="site_keywords" id="site_keywords" class="large-text" value="<?php echo esc_attr(get_option('site_keywords')); ?>" placeholder="站点关键词">-->
                        </td>
                    </tr>
                </table>
            </div>
            <div class="formtable common">
                <h1><b class="num" style="color: var(--block-one);">02</b>通用控制<p class="en">COMMON CONTROLS</p></h1>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">试验性内容<sup class="dualdata">Exp</sup></th>
                        <td>
                            <?php
                                $opt = 'site_experimental_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_inform_switcher_label">试验性（全站）UI/UE 内容（开启后可体验最新页面样式内容</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Experimental</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">导航 Icon</th>
                        <td>
                            <?php
                                $opt = 'site_icon_switcher';
                                $value = get_option($opt);
                                $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="">站点导航字体图标，导航别名默认为图标css类（暂不支持创建时手动选择</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:steelblue;" class="btn">ICON</span></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">导航 Slider</th>
                        <td>
                            <?php
                                $opt = 'site_nav_slider_switcher';
                                $value = get_option($opt);
                                $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="">站点导航跟随模块，开启后透明主题色块可跟随鼠标移动标记链接（暂仅支持PC端</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:dodgerblue;" class="btn">Slider</span></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Progresser</th>
                        <td>
                            <?php
                                $opt = 'site_progress_bar_switcher';
                                $value = get_option($opt);
                                $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="">站点顶部加载条，开启后全站使用</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style=";" class="btn">NProgress</span></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Adsense</th>
                        <td>
                            <?php
                                $opt = 'site_ads_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_ads_switcher_label">谷歌广告（开启后前往 "/inc/wp-blocks.php" 修改全站初始化广告代码：方形侧栏/纵向侧栏/富文本列表/纯文本列表</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color: orangered;" class="btn">Adsense</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_ads_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $ads = get_option('site_ads_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 文章页广告</th>
                                <td>
                                    <?php
                                        $opt = 'site_ads_article';
                                        // $value = get_option($opt);
                                        // $data = get_option('site_ads_init', '' );
                                        // //设置默认开启（仅适用存在默认值的checkbox）
                                        // if(!$value&&!$data){
                                        //     update_option($opt, "on_default");
                                        //     $status="checked";
                                        // }else{
                                        //     $status = $value ? "checked" : "check";
                                        // };
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启文章内页 尾部广告位</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">文章页</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $ads; ?>">
                                <th scope="row">— 侧边栏广告</th>
                                <td>
                                    <?php
                                        $opt = 'site_ads_sidebar';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启文章列表 侧边广告位</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">侧边栏</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">图片懒加载</th>
                        <td>
                            <?php
                                $opt = 'site_lazyload_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_lazyload_switcher_label">开启文章/部分页面图片使用 Lazyload 懒加载（默认关闭 </p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">LazyLoad</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">深色模式</th>
                        <td>
                            <?php
                                $opt = 'site_darkmode_switcher';
                                $value = get_option($opt);
                                $start = get_option( 'site_darkmode_start', '' );
                                $end = get_option( 'site_darkmode_end', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$start&&!$end){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_darkmode_switcher_label">开启后将根据时段自动切换主题（默认晚17至早9<br />⚠️注意：主题默认支持根据系统配置自动设置主题深浅色（同时支持手动切换）。此项启用时，将按照此处<b>配置的深浅色时段</b>自动配置显示模式<br />（若当前系统为深色模式，此时切换系统为浅色将无法自动生效，因为此处权配置显示时段重高于系统默认配置</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">DarkMode</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_darkmode_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $darkmode = get_option('site_darkmode_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 开启时间</th>
                                <td>
                                    <?php
                                        $opt = 'site_darkmode_start';
                                        $value = get_option($opt);
                                        $preset = 17;  //默认开启（时）间
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_darkmode_start_label">开启时间（大于13点小于24点</p><input type="number" min="13" max="24" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $darkmode; ?>">
                                <th scope="row">— 关闭时间</th>
                                <td>
                                    <?php
                                        $opt = 'site_darkmode_end';
                                        $value = get_option($opt);
                                        $preset = 9;  //默认关闭（时）间
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_darkmode_end_label">关闭时间（大于1点小于12点</p><input type="number" min="1" max="12" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $darkmode; ?>">
                                <th scope="row">— 固定模式<sup>FixedTheme</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_darkmode_fixed';
                                        $arrobj = array(
                                            array('name'=>'浅色', 'value'=>'light'),
                                            array('name'=>'深色', 'value'=>'dark'),
                                        );
                                        echo '<label for="'.$opt.'"><p class="description" id="site_darkmode_fixed_label">开启后可全站（包括后台）保持指定主题显示模式（注：此项将忽略系统自动切换，并同时禁用手动切换主题功能</p><select name="'.$opt.'" id="'.$opt.'"><option value=""> 默认 </option>';
                                            foreach ($arrobj as $arr){
                                                $val = $arr['value'];
                                                echo '<option value="'.$val.'"';if(get_option($opt)==$val)echo('selected="selected"');echo '>'.$arr['name'].'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top" class="">
                        <th scope="row">分类导航层级</th>
                        <td>
                            <?php
                                $opt = 'site_catnav_deepth';
                                $value = get_option($opt);
                                $preset = 4;  //默认填充数据
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="">页面 Header 头部分类导航层级（默认最大4级</p><input type="number" max="" min="1" max="4" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">近期内容展示数量</th>
                        <td>
                            <?php
                                $opt = 'site_per_posts';
                                $value = get_option($opt);
                                $preset = 5;  //默认填充数据
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="">应用于近期评论、排行等<b> 无需分页 </b>内容展示数量，可在<a href="/wp-admin/options-reading.php" target="_blank"> 阅读 </a>查看默认至多显示数量（*此项不应用于 <code>posts_per_page</code> 查询参数，<b>可能造成分页 404</b></p><input type="number" max="" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">地址栏 Category 目录</th>
                        <td>
                            <?php
                                $opt = 'site_remove_category_switcher';
                                $value = get_option($opt);
                                $data = get_option('site_url_slash_sw', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后移除 url 中自带的 category 目录（默认开启，模拟相同 slug 链接 page 页面</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">移除目录</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_remove_category_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_remove_category_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">—  URL 尾部斜杠</th>
                                <td>
                                    <?php
                                        $opt = 'site_url_slash_sw';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启后移除站点 Permalink 超链接中的尾部"/"，URL地址中的“/”需在<a href="/wp-admin/options-permalink.php" target="_blank"> 固定链接 </a>中设置</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">移除斜杠</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                            <!--<tr valign="top" class="child_option dynamic_optshow">-->
                            <!--    <th scope="row">— 页面层级关系<sup title="实验性功能">EXP</sup></th>-->
                            <!--    <td>-->
                                    <?php
                                        // $opt = 'site_sync_level_sw';
                                        // $status = check_status($opt);
                                        // echo '<label for="'.$opt.'"><p class="description" id="">实验性功能默认关闭，开启可使用自定义关键字“slash”将分类别名重写为“/” 以达到隐藏当前层级，将子级作为同级输出的目的（启用后将自动同步分类层级到页面。启用此项请保证分类中不存在“/”别名分类，如访问错误请检查错误页面父级别名是否为“/”并修改</b></p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">同步页面层级</b></label>';
                                    ?>
                            <!--    </td>-->
                            <!--</tr>-->
                    <tr valign="top" class="">
                        <th scope="row">搜索/标签 页面样式</th>
                        <td>
                            <?php
                                $opt = 'site_search_style_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_search_includes', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_search_style_switcher_label">搜索结果及标签内容展示列表样式，开启后将使用各页面数据列表样式（默认使用笔记栈列表样式</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /><b>多样搜索</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_search_style_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_search_style_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 搜索类型包含（多选项）</th>
                                <td>
                                    <?php
                                        $opt = 'site_search_includes';  //unique str
                                        $value = get_option($opt);
                                        $options = array('post', 'page');
                                        $preset = $options[0].',';
                                        if(!$value){
                                            // $preset_str = implode(' , ',$options).',';
                                            update_option($opt, $preset);
                                            $value = $preset;
                                        }
                                        echo '<p class="description" id="site_search_includes_label">指定搜索包含内容，使用逗号“ , ”分隔（默认 post 类型，可选 page（页面）及自定义选填类型</p><div class="checkbox">';
                                        $pre_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($pre_array);
                                        foreach ($options as $option){
                                            $checking = in_array($option, $pre_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$option.'" type="checkbox" value="'.$option.'" '.$checking.' /><label for="'.$opt.'_'.$option.'">'.strtoupper($option).'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                    <?php 
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">多元导航分类</th>
                        <td>
                            <?php
                                $opt = 'site_metanav_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_metanav_switcher_label">多元化（样式）展示分类导航名称、描述及背景内容</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">MetaBox</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_metanav_switcher')){
                            // $cats = get_categories(meta_query_categories(0,'ASC','seo_order'));
                            $options = array();
                            foreach($cats as $the_cat){
                                if(count(get_term_children($the_cat->term_id,$the_cat->taxonomy))>0) array_push($options, $the_cat);  // has-child category only
                            }
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $metacat = get_option('site_metanav_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 基础元分类（多选项）</th>
                                <td>
                                    <?php
                                        $opt = 'site_metanav_array';  //unique str
                                        $value = get_option($opt);
                                        // $preset = $options[0]->slug.','; //'notes,acg,';
                                        // if(!$value){
                                        //     update_option($opt, $preset);
                                        //     $value = $preset;
                                        // }
                                        echo '<p class="description" id="site_metanav_array_label">需要应用元导航样式的分类别名，使用逗号“ , ”分隔（仅输出存在子分类的一级分类</p><div class="checkbox">';
                                        $pre_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($pre_array);
                                        foreach ($cats_seclevel as $option){
                                            $slug = $option->slug;
                                            $checking = in_array($slug, $pre_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$slug.'" type="checkbox" value="'.$slug.'" '.$checking.' /><label for="'.$opt.'_'.$slug.'">'.$option->name.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $metacat; ?>">
                                <th scope="row">— 图文元分类（多选项）</th>
                                <td>
                                    <?php
                                        $opt = 'site_metanav_image';  //unique str
                                        $value = get_option($opt);
                                        $enabled_array = explode(',',trim(get_option('site_metanav_array')));
                                        $enabled_array_count = count($enabled_array);
                                        echo '<p class="description" id="site_metanav_image_label">需要使用背景图片的元分类导航，使用逗号“ , ”分隔（仅可选上方“基础元分类”中已启用分类，注slash“/”需手动写入</p><div class="checkbox">';
                                        for($i=0;$i<$enabled_array_count;$i++){
                                            $slug = trim($enabled_array[$i]);  // NO WhiteSpace
                                            if($slug){
                                                $new_category = get_category_by_slug($slug);
                                                if($new_category){
                                                    $checking = strpos($value, $slug)!==false ? 'checked' : '';
                                                    echo '<input id="'.$opt.'_'.$slug.'" type="checkbox" value="'.$slug.'" '.$checking.' /><label for="'.$opt.'_'.$slug.'">'.$new_category->name.'</label>';
                                                }
                                            }
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">Gravatar 镜像源<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_avatar_mirror';
                                $value = get_option($opt);
                                $preset = '//cravatar.cn/';
                                $arrobj = array(
                                    array('name'=>'Gravatar', 'href'=>'//gravatar.com/'),
                                    array('name'=>'V2EX', 'href'=>'//cdn.v2ex.com/'),
                                    array('name'=>'Cravatar', 'href'=>'//cravatar.cn/'),
                                    array('name'=>'Geekzu', 'href'=>'//sdn.geekzu.org/'),
                                    array('name'=>'LOLI', 'href'=>'//gravatar.loli.net/'),
                                    array('name'=>'SEP', 'href'=>'//cdn.sep.cc/'),
                                    array('name'=>'2Bavatar', 'href'=>'//gravatar.2broear.com/'),
                                );
                                // $md5mail = md5("wapuu@wordpress.example"); //get_bloginfo('admin_email')
                                $mirror_parm = 'avatar/'.md5(get_bloginfo('admin_email', "wapuu@wordpress.example")).'?s=100';
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<label for="'.$opt.'"><p class="description" id="site_avatar_mirror_label">评论头像 Gravatar 国内镜像源（同时适用于 wordpress/valine 评论头像展示</p><img src="'.$preset.$mirror_parm.'" style="vertical-align: middle;max-width: 50px;margin:auto 15px;border-radius:100%;" alt="镜像已失效.." /><select name="'.$opt.'" id="'.$opt.'" class="select_mirror" parm="'.$mirror_parm.'">';
                                    foreach ($arrobj as $arr){
                                        echo '<option value="'.$arr['href'].'"';if($preset==$arr['href']) echo('selected="selected"');echo '>'.$arr['name'].'</option>';
                                    }
                                echo '</select></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">定时任务 - 执行时间</th>
                        <td>
                            <?php
                                $opt = 'site_scheduled_times';
                                $value = get_option($opt);
                                $preset = "06:00"; //date("06:00")
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="">站点每日定时任务执行时间，包含rss、页面缓存、友链状态等更新任务（默认每天早晨 06:00 执行</p><input type="time" name="'.$opt.'" id="'.$opt.'" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">RSS 订阅分类（多选）</th>
                        <td>
                            <?php
                                $opt = 'site_rss_categories';  //unique str
                                $value = get_option($opt);
                                // $cats = get_categories(meta_query_categories(0,'ASC','seo_order'));
                                $options = array();
                                foreach($cats as $the_cat){
                                    if($the_cat->count>=1) array_push($options, $the_cat);  // has-content category only
                                }
                                echo '<p class="description" id="site_rss_categories_label">指定输出站点 RSS 分类文章，使用逗号“ , ”分隔（feed将在任意文章更新后更新</p><div class="checkbox">';
                                $pre_array = explode(',',trim($value));  // NO "," Array
                                // $pre_array_count = count($pre_array);
                                foreach ($options as $option){
                                    $slug = $option->slug;
                                    $checking = in_array($slug, $pre_array) ? 'checked' : '';
                                    echo '<input id="'.$opt.'_'.$slug.'" type="checkbox" value="'.$slug.'" '.$checking.' /><label for="'.$opt.'_'.$slug.'">'.$option->name.'</label>';
                                }
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="默认所有分类" /></div>';;
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">RSS 拉取频率（小时）</th>
                        <td>
                            <?php
                                $opt = 'site_rss_update_interval';
                                $value = get_option($opt);
                                $preset = 12;  //默认开启（时）间
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="site_rss_feeds_timeout_label"><a href="' . admin_url('admin.php?page=' . $GLOBALS['RSS_PAGE_NAME']) . '" target="_self">RSS 友链订阅</a> 计划自动更新 feeds 频率（默认12小时/一天更新两次，修改后可刷新任务</p><input id="updateSchedule" class="update-target" style="font-size: 12px;" type="button" value="刷新定时任务" data-before="' . $value . '" data-api="' . get_api_refrence('rss', true) . '" data-page="' . $GLOBALS['RSS_PAGE_NAME'] . '" data-admin-url="' . admin_url('admin-ajax.php') . '" data-nonce="' . wp_create_nonce("update_cronjobs") . '" disabled />&nbsp;<input id="updateSchedules" class="small-text live-update" type="number" min="1" max="" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>'; //以解锁操作
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">RSS 更新数量（条目）</th>
                        <td>
                            <?php
                                $opt = 'site_rss_update_count';
                                $value = get_option($opt);
                                $preset = 3;
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="site_rss_feeds_timeout_label"><a href="' . admin_url('admin.php?page=' . $GLOBALS['RSS_PAGE_NAME']) . '" target="_self">RSS 友链订阅</a> 拉取数量（默认3条</p><input type="number" min="1" max="9" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">Sitemap 站点地图</th>
                        <td>
                            <?php
                                $opt = 'site_map_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_map_includes', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_map_switcher_label">生成全站站点地图（默认启用，开启后可指定生成类型</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:inherit;" class="btn">SITEMAP</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_map_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_map_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 生成类型（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_map_includes';  //unique str
                                        $value = get_option($opt);
                                        $options = array('post','category','tag');
                                        if(!$value){
                                            $preset_str = implode(',', $options).',';
                                            update_option($opt, $preset_str);
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="site_map_includes_label">指定 sitemap 生成内容，使用逗号“ , ”分隔（默认 post（文章）tag（标签）category（分类/<del>即 page 页面</del></p><div class="checkbox">';
                                        $pre_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($pre_array);
                                        foreach ($options as $option){
                                            $checking = in_array($option, $pre_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$option.'" type="checkbox" value="'.$option.'" '.$checking.' /><label for="'.$opt.'_'.$option.'">'.strtoupper($option).'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                    <?php 
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">站点 CDN 加速</th>
                        <td>
                            <?php
                                $opt = 'site_cdn_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_cdn_switcher_label">开启后可自定义cdn加速域名（需要配置 nginx 指定域名 </p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">CDN 配置</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_cdn_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $cdn = get_option('site_cdn_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 图片加速域名</th>
                                <td>
                                    <p class="description" id="site_cdn_img_label">媒体库图片文件（存放于 wp-content/uploads 路径，此项需在提交修改前 <i>更新数据库</i></p>
                                    <div class="checkbox">
                                        <?php
                                            $options = array(
                                                ['options', 'option_value'],     // wp_options
                                                ['termmeta', 'meta_value'],      // wp_termmeta
                                                ['postmeta', 'meta_value'],      // wp_postmeta
                                                ['posts', 'post_content'],       // wp_posts
                                                ['comments', 'comment_content'], // wp_comments
                                            );
                                            $option_str = '';
                                            foreach ($options as $option) {
                                                $option_table = $option[0];
                                                $option_row = $option[1];
                                                $option_val = $option_table . '=' . $option_row;
                                                $option_str .= $option_val . ','; //'"' . $option_table . '",';
                                                // $option_arr = urlencode(json_encode($option));
                                                echo '<input id="site_update_'.$option_table.'" class="" type="checkbox"  value="'.$option_val.'" checked /><label for="site_update_'.$option_table.'">'.$option_table.'</label>';
                                            }
                                        ?>
                                    </div>
                                    <?php 
                                        $site_cdn_img = get_option('site_cdn_img');
                                        $default_options = $option_str; //urlencode(json_encode($options));
                                        echo '<input id="updateDomain" class="update-target" type="button" value="更新数据库" disabled data-options="' . $default_options . '" data-before="' . $site_cdn_img . '" data-url=" ' . admin_url('admin-ajax.php') . '" data-nonce="' . wp_create_nonce('dbupdate_ajax_nonce') . '" title="提交修改后可在此单独更新修改前旧值为最新" />';
                                    ?>
                                    <input type="text" name="site_cdn_img" id="site_cdn_img" class="middle-text live-update" placeholder="site_cdn_img" value="<?php echo $site_cdn_img; ?>"/>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $cdn; ?>">
                                <th scope="row">— 文件加速域名</th>
                                <td>
                                    <p class="description" id="site_cdn_src_label">可选项，网站cdn（css、js）链接/标头（默认使用当前主题目录，可用于安全性考量</p>
                                    <input type="text" name="site_cdn_src" id="site_cdn_src" class="middle-text" placeholder="site_cdn_src" value="<?php echo get_option( 'site_cdn_src', '' ); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $cdn; ?>">
                                <th scope="row">— API 调用域名</th>
                                <td>
                                    <p class="description" id="">此域名用于调用 plugin 目录内插件（留空默认调用根目录，开启后可以在下方显示 CDN Auth Sign 调用鉴权密钥</p>
                                    <input type="text" name="site_cdn_api" id="site_cdn_api" class="middle-text" placeholder="site_cdn_api" value="<?php echo get_option( 'site_cdn_api', '' ) ?>"/>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $cdn&&get_option('site_cdn_api') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— API Auth Sign</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_auth';
                                        $value = get_option($opt);
                                        echo '<p class="description" id="site_bar_pixiv_label">腾讯云CDN鉴权密钥（如 api 调用域名出现访问403可能是由于CDN服务器之前开启了鉴权但此项鉴权密钥尚未填写（无法判断远程服务器是否开启鉴权</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="normal-text" placeholder="cdn authentication" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $cdn; ?>">
                                <th scope="row">— 页面视频加速（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_cdn_vdo_includes';  //unique str
                                        $value = get_option($opt);
                                        // $cats = get_categories(meta_query_categories(0,'ASC','seo_order'));
                                        $options = array($templates_info['about'], $templates_info['acg'], $templates_info['guestbook'], $templates_info['privacy'], 'Article', 'Sidebar');
                                        echo '<p class="description" id="site_map_includes_label">开启后使用已配置的<code> 图片加速域名 </code>加速站内指定位置视频，常用于超小型文件（特殊配置：article：文章视频，sidebar：侧栏视频</p><div class="checkbox">';
                                        $pre_array = explode(',',trim($value));  // NO "," Array
                                        // print_r($options);
                                        foreach ($options as $option) {
                                            if (is_object($option) && $option->error) continue;
                                            $slug = is_string($option) ? strtolower($option) : strtolower($option->slug);
                                            $name = is_string($option) ? strtolower($option) : strtolower($option->name);
                                            $checking = in_array($slug, $pre_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$slug.'" type="checkbox" value="'.$slug.'" '.$checking.' /><label for="'.$opt.'_'.$slug.'">'.$name.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择" /></div>';;
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">视频 Poster 默认预览</th>
                        <td>
                            <?php
                                $opt = 'site_video_poster_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_lazyload_switcher_label">开启后自动捕获当前页面所有<i>未设置</i><code> autoplay </code> 属性的视频生成并设置预览海报（仅部分页面启用，默认截取第一帧</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">视频海报</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">视频截图捕获（动态预览）</th>
                        <td>
                            <?php
                                $opt = 'site_video_capture_switcher';
                                $status = check_status($opt);
                                function funcStatus($func){
                                    return function_exists($func) ? "<b style='color:green'>$func (已开启)</b>" : "<u style='color:red'>$func (已关闭)</u>";
                                }
                                echo '<label for="'.$opt.'"><p class="description" id="site_lazyload_switcher_label"><b>上传视频</b>到媒体库时 自动在存放文件同目录下生成动态截图（此前上传的视频无效<br/>⚠后端环境：服务端须提前安装<b> ffmpeg </b> 扩展，并开启以下任一<b> php 函数</b>：'.funcStatus('exec').'、'.funcStatus('system').'、'.funcStatus('shell_exec').'（解除禁用后需重启nginx），测试 shell_exec 暂时无法解析大文件<br/>⚠前端应用：视频元素不存在<b> autoplay </b>自动播放属性</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">片段预览</b></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_video_capture_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 截图 Gif 预览</th>
                                <td>
                                    <?php
                                        $opt = 'site_video_capture_gif';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启后上传视频时生成 gif 动图作用于视频海报（开启视频截图捕获后默认自动生成gif预览，此处仅控制 poster 属性</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">GIF 预览</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">Leancloud<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_leancloud_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_leancloud_switcher_label">使用第三方云数据库（Serverless）接管日记、友链、公告等数据内容（需在 leancloud 中新建对应分类 slug 名称的同名CLASS类，必填项与第三方评论 valine 自动同步，开启后可单独控制 BaaS 页面数据开关</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:dodgerblue;" class="btn">LeanCloud</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_leancloud_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $leancloud = get_option('site_leancloud_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— LBMS</th>
                                <td>
                                    <p class="description" id="site_leancloud_appid_label">
                                        <b>LBMS 是基于 leancloud 开发的数据储存容器 <a href="<?php echo bloginfo('url') ?>/lbms" target="_blank">前往 LBMS 管理页面</a></b><br />
                                    </p>
                                    <p>需前往<a href="https://console.leancloud.cn/" target="_blank"> Leancloud 控制台 </a>设置对应 serverurl 并创建对应页面 slug 数据表（启用后将自动新建别名为“lbms”及“login”页面</del></p>
                                    <?php
                                        $request_page = new WP_REST_Request( 'POST', '/wp/v2/pages' );
                                        $init_pages = array(
                                            array(
                                                'title' => 'LBMS管理后台', 
                                                'slug' => 'lbms',
                                                'template' => 'inc/templates/pages/lbms.php'
                                            ),
                                            array(
                                                'title' => 'LBMS登陆页面', 
                                                'slug' => 'lbms-login',
                                                'template' => 'inc/templates/pages/lbms-login.php'
                                            ),
                                        );
                                        global $wpdb;
                                        foreach ($init_pages as $each_page){
                                            $slug = $each_page['slug'];
                                            $title = $each_page['title'];
                                            $check_page = $wpdb->get_var("SELECT * FROM $wpdb->posts WHERE post_name = '$slug' AND post_type = 'page'");
                                            // unset($wpdb);
                                            if(!$check_page){
                                                // https://developer.wordpress.org/reference/classes/wp_rest_request/set_query_params/
                                                $request_page->set_query_params(array(
                                                    'slug' => $slug,
                                                    'title' => $title,
                                                    'status' => 'private',
                                                    'template' => $each_page['template']
                                                ));
                                                $response = rest_do_request( $request_page );
                                            }
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $leancloud; ?>">
                                <th scope="row">— APP ID<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_appid';
                                        echo '<p class="description" id="site_leancloud_appid_label"></p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud App Id" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $leancloud; ?>">
                                <th scope="row">— APP KEY<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_appkey';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud App Key" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $leancloud; ?>">
                                <th scope="row">— SERVER URL<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_server';
                                        echo '<!--<p class="description" id="site_leancloud_switcher_label">国内版二级域名可能出现的CORS跨域问题？<a href="#">点我查看</a></p>--><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud Server Url" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top" class="child_option dynamic_opts <?php echo $leancloud; ?>">
                        <th scope="row">— BaaS Switcher</th>
                        <td>
                            <?php
                                $opt = 'site_leancloud_category';  //unique str
                                $value = get_option($opt);
                                $baasarray = array();
                                $inform = 'site_leancloud_inform';
                                $baastring = $inform.',';  //category-weblog.php
                                // global $templates;
                                $templates = wp_get_theme()->get_page_templates();
                                if(count($templates)<=0){
                                    $templates = scan_templates_dir($templates);
                                }
                                foreach ($templates as $temp => $index){
                                    if(strpos($index, 'BaaS')!==false){
                                        array_push($baasarray, array($index=>$temp));
                                        $baastring .= $temp.',';
                                    }
                                }
                                $baasarray_count = count($baasarray);
                                if(!$value){
                                    update_option($opt, $baastring);
                                    $value = $baastring;
                                }
                                $check = strpos($value, $inform)!==false ? 'checked' : '';
                                echo '<p class="description" id="">手动指定需要启用 BaaS 的分类页面，使用逗号“ , ”分隔（默认全部开启，开启后将接管全站支持 LBMS 页面的 BaaS 数据来源</p><div class="checkbox"><input id="'.$inform.'" type="checkbox" value="'.$inform.'" '.$check.'><label for="'.$inform.'">站点公告（LBMS）</label>';
                                for($i=0;$i<$baasarray_count;$i++){
                                    foreach ($baasarray[$i] as $option => $index){
                                        $checking = strpos($value, $index)!==false ? 'checked' : '';
                                        echo '<input id="'.$opt.'_'.$index.'" type="checkbox" value="'.$index.'" '.$checking.' /><label for="'.$opt.'_'.$index.'">'.$option.'</label>';
                                    }
                                }
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly style="" value="' . $value . '" placeholder="请选择"/></div>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">评论系统<sup class="dualdata dynamic_comment"> <?php $third_comment=get_option('site_third_comments');echo $third_comment ? $third_comment : 'WordPress';//if($third_comment=='Valine'){echo 'Valine';}elseif($third_comment=='Twikoo'){echo 'Twikoo';}else{echo 'BaaS';} ?></sup></th>
                        <td>
                            <?php
                                $opt = 'site_third_comments';
                                $value = get_option($opt);
                                $comments_options = ['Wordpress', 'Valine', 'Twikoo'];
                                if(!$value) update_option($opt, $comments_options[0]);else $preset=$value;  //auto update option to default if unset
                                echo '<label for="'.$opt.'"><p class="description" id="">可选第三方评论系统（开启后需填配置项</p><select name="'.$opt.'" id="'.$opt.'" class="select_options">'; //<option value="">WordPress</option>
                                    foreach ($comments_options as $arr){
                                        echo '<option value="'.$arr.'"';
                                        if($value==$arr) echo('selected="selected"');
                                        echo '>'.$arr.'</option>';
                                    }
                                echo '</select></label>';
                            ?>
                        </td>
                    </tr>
                            <!-- Wordpress -->
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wordpress_statu = $third_comment=='Wordpress' ? 'dynamic_optshow Wordpress' : 'dynamic_opts Wordpress' ?>">
                                <th scope="row">— Ajax 评论</th>
                                <td>
                                    <?php
                                        $opt = 'site_ajax_comment_switcher';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启后免刷新页面评论，实现无感评论及回复等操作</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">AJAX Comments</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wordpress_statu; ?>">
                                <th scope="row">— Ajax 翻页</th>
                                <td>
                                    <?php
                                        $opt = 'site_ajax_comment_paginate';
                                        $status = check_status($opt);
                                        $premise = get_option('site_ajax_comment_switcher');
                                        $tips = '开启免刷新加载评论，替代 PREV/NEXT 翻页按钮并支持评论标签（关闭上方Ajax 评论后此项将自动关闭';
                                        if (get_option($opt) && !$premise) {
                                            update_option('site_ajax_comment_paginate', '');
                                        } else {
                                            $tips .= '<p>⚠️注：可前往<a href="/wp-admin/options-discussion.php" target="_blank"> 讨论->评论分页 </a>中配置 默认显示评论排序，启用此项后建议默认显示<code> 尾页 </code>顶部显示<code> 新的 </code></p>';
                                        }
                                        $check = !$premise ? 'disabled' : '';
                                        echo '<label for="'.$opt.'"><p class="description" id="">'.$tips.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$check.' '.$status.' /> <b class="'.$status.'">AJAX Pagination</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <!-- Valine -->
                            <tr valign="top" class="child_option sync_data <?php echo $valine_statu = $third_comment=='Valine' ? 'dynamic_opts dynamic_optshow Valine' : 'dynamic_opts Valine'; ?>">
                                <th scope="row">— APP ID<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_appid';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud App Id" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option sync_data <?php echo $valine_statu; ?>">
                                <th scope="row">— APP KEY<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_appkey';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud App Key" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option sync_data <?php echo $valine_statu; ?>">
                                <th scope="row">— SERVER URL<sup id="tips">sync</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_leancloud_server';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Leancloud Server Url" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option <?php echo $valine_statu; ?>">
                                <th scope="row">— ServerChan</th>
                                <td>
                                    <?php
                                        $opt = 'site_comment_serverchan';
                                        echo '<p class="description" id="site_comment_serverchan_label">评论微信公众号提醒（server酱提供的评论微信提醒服务（每天 5 条）<a href="https://sct.ftqq.com" target="_blank">相关文档</a></p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="ServerChan SendKey" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option <?php echo $valine_statu; ?>">
                                <th scope="row">— PushPlus</th>
                                <td>
                                    <?php
                                        $opt = 'site_comment_pushplus';
                                        echo '<p class="description" id="site_comment_pushplus_label">评论微信（公众号）提醒（pushplus提供的公众号推送服务（每天 200 条）<a href="http://www.pushplus.plus/push1.html" target="_blank">相关文档</a></p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Pushplus Token" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <!-- Twikoo -->
                            <tr valign="top" class="child_option dynamic_opts <?php echo $twikoo_statu = $third_comment=='Twikoo' ? 'dynamic_optshow Twikoo' : 'dynamic_opts Twikoo' ?>">
                                <th scope="row">— 版本号</th>
                                <td>
                                    <?php
                                        $opt = 'site_twikoo_version';
                                        $value = get_option($opt);
                                        $preset = '1.6.4';  //默认
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        $status_code = 0;
                                        $url = 'https://cdn.staticfile.org/twikoo/' . $preset . '/twikoo.min.js';
                                        if($third_comment=='Twikoo'){
                                            $status_code = get_url_status_by_curl($url, 3); //get_url_status_by_header($url);
                                        }
                                        echo '<p class="description" id="site_comment_pushplus_label">twikoo.min.js 版本号（默认 1.6.4，当前文件（'.$url.'）状态：'.$status_code.'</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="small-text" placeholder="Twikoo Source" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $twikoo_statu; ?>">
                                <th scope="row">— envId</th>
                                <td>
                                    <?php
                                        $opt = 'site_twikoo_envid';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="您的环境id" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <!-- Common options -->
                            <tr valign="top" class="child_option dynamic_optshow">
                                <th scope="row">— 境外 IP 屏蔽<sup id="tips">common</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_comment_blockoutside';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">阻止所有非大陆、香港、台湾的境外IP发布评论（可临时用于刷评屏蔽</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">外网屏蔽</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_optshow">
                                <th scope="row">— 关键词屏蔽<sup id="tips">common</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_comment_blacklists';
                                        $value = get_option($opt);
                                        echo '<p class="description" id="site_comment_serverchan_label">屏蔽指定评论内容，使用 “|” 分隔关键词（非模糊匹配</p><textarea name="'.$opt.'" id="'.$opt.'" placeholder="Comment BlackList">'.$value.'</textarea>';  //<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Comment BlackList" value="' . $value . '"/>
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">Cloudflare Turnstile</th>
                        <td>
                            <?php
                                $opt = 'site_cloudflare_turnstile';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">启用 Cloudflare Turnstile 校验访客信息（开启后默认检测评论访客信息，支持 wordpress 及第三方 valine 评论系统，暂不支持 twikoo，后续支持可选页面检测</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b style="color:darkorange;" class="btn"> Turnstile </b></label>';
                            ?>
                        </td>
                    </tr>
                            <!-- Turnstile -->
                            <tr valign="top" class="child_option dynamic_opts <?php echo $turnstile = get_option('site_cloudflare_turnstile') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— Turnstile SiteKey</th>
                                <td>
                                    <?php
                                        $opt = 'site_cloudflare_turnstile_sitekey';
                                        echo '<p class="description" id="">Turnstile SiteKey</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Turnstile SiteKey" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $turnstile; ?>">
                                <th scope="row">— Turnstile SecretKey</th>
                                <td>
                                    <?php
                                        $opt = 'site_cloudflare_turnstile_secretkey';
                                        echo '<p class="description" id="">Turnstile SecretKey（留空将导致三方评论回调验证失败！</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Turnstile SecretKey" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $turnstile; ?>">
                                <th scope="row">— Turnstile Comments</th>
                                <td>
                                    <?php
                                        $opt = 'site_cloudflare_turnstile_comments';
                                        $value = get_option($opt);
                                        $comments_options = ['Wordpress', 'Valine'];
                                        echo '<p class="description" id="site_map_includes_label">可选开启评论审核系统（Twikoo 默认支持 trunstile 配置</p><div class="checkbox">';
                                        $pre_array = explode(',',trim($value));  // NO "," Array
                                        foreach ($comments_options as $option) {
                                            $checking = in_array($option, $pre_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$option.'" type="checkbox" value="'.$option.'" '.$checking.' /><label for="'.$opt.'_'.$option.'">'.$option.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $turnstile; ?>">
                                <th scope="row">— Turnstile Login</th>
                                <td>
                                    <?php
                                        $opt = 'site_cloudflare_turnstile_login';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启后登陆 WordPress 后台需要通过 turnstile 验证（可能造成移动端登录失败?</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">登录校验</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">评论微信提醒</th>
                        <td>
                            <?php
                                $opt = 'site_wpwx_notify_switcher';
                                $value = get_option($opt);
                                $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="site_wpwx_notify_switcher_label">基于企业微信应用开发的评论推送微信通知，需填写企业ID、企业应用AgentId、企业应用Secret（微信需关注该企业应用才能收到通知<a href="https://www.jishusongshu.com/network-tech/work-weixin-push-website-comment/" target="_blank"> 相关文档 </a> 状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/><b>微信提醒</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_wpwx_notify_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wpwx = get_option('site_wpwx_notify_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 企业 ID</th>
                                <td>
                                    <?php
                                        $opt = 'site_wpwx_id';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="企业微信 ID" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wpwx; ?>">
                                <th scope="row">— 应用 AgentId</th>
                                <td>
                                    <?php
                                        $opt = 'site_wpwx_agentid';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="企业应用 AgentId" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wpwx; ?>">
                                <th scope="row">— 应用 Secret</th>
                                <td>
                                    <?php
                                        $opt = 'site_wpwx_secret';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="企业应用 Secret" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $wpwx; ?>">
                                <th scope="row">— 推送消息类型</th>
                                <td>
                                    <?php
                                        $opt = 'site_wpwx_type';
                                        $value = get_option($opt);
                                        $preset = 'textcard';
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update
                                        $arrobj = array(
                                            array('name'=>'文本卡片', 'type'=>'textcard'),
                                            array('name'=>'图文卡片', 'type'=>'news'),
                                            array('name'=>'模板卡片', 'type'=>'template_card'),
                                        );
                                        echo '<label for="'.$opt.'"><p class="description" id="site_wpwx_type_label">文本卡片为纯文本描述，图文卡片会附一张文章或页面图片，模板则为更丰富的图文消息（注意模板卡片仅支持企业微信提醒，微信端不会收到任何推送信息</p><img src="'.$img_cdn.'/images/settings/'.$preset.'.png" style="vertical-align: middle;max-width: 88px;margin:auto 15px;" /><select name="'.$opt.'" id="'.$opt.'" class="select_images">';
                                            foreach ($arrobj as $arr){
                                                $type = $arr['type'];
                                                echo '<option value="'.$type.'" preview="'.$img_cdn.'/images/settings/'.$type.'.png"';if($preset==$type)echo('selected="selected"');echo '>'.$arr['name'].'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top" class="">
                        <th scope="row">评论邮件提醒</th>
                        <td>
                            <?php
                                $opt = 'site_wpmail_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_wpmail_switcher_label">WP自带评论审核提醒邮件（博主，此选项为定制模板邮件（两者均需上方 SMTP 配置测试通过后才能收到邮件提醒，状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/><b>邮件提醒</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">SMTP 发件服务配置</th>
                        <td>
                            <?php
                                $opt = 'site_smtp_switcher';
                                $value = get_option($opt);
                                // $state = get_option( 'site_smtp_state', '' );
                                $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="site_smtp_switcher_label">SMTP 发件服务配置（配置smtp时默认使用常规设置内的管理员邮箱（状态：'.$status;
                                // if($state) echo '<u style="color:forestgreen">发件测试已通过</u>';else echo '<u style="color:orangered">配置未通过测试</u>';
                                echo '，如已通过但未收到邮件请检查授权码及服务器是否全部配置正确</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/><b>邮件配置</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_smtp_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $smtp = get_option('site_smtp_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 发件邮箱</th>
                                <td>
                                    <?php
                                        $opt = 'site_smtp_mail';
                                        $value = get_option($opt);
                                        $preset = get_bloginfo('admin_email');
                                        if(!$value) update_option($opt, $preset);else $preset=$value;
                                        echo '<p class="description" id="site_smtp_mail_label">SMTP 发件邮箱（此邮箱应用于所有评论提醒发送邮箱，默认为管理员邮箱：'.get_bloginfo('admin_email').'</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . $preset . '" placeholder="发件邮箱地址"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $smtp; ?>">
                                <th scope="row">— 发件授权码</th>
                                <td>
                                    <?php
                                        $opt = 'site_smtp_pswd';
                                        echo '<p class="description" id="site_smtp_pswd_label">SMTP 邮箱授权码（务必匹配发件邮箱</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . get_option($opt) . '" placeholder="管理员邮箱授权码"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $smtp; ?>">
                                <th scope="row">— 发件服务器</th>
                                <td>
                                    <?php
                                        $opt = 'site_smtp_host';
                                        $arrobj = array(
                                            array('name'=>'腾讯QQ邮箱', 'href'=>'smtp.qq.com'),
                                            array('name'=>'腾讯企业邮', 'href'=>'smtp.exmail.qq.com'),
                                            array('name'=>'阿里云邮箱', 'href'=>'smtp.mxhichina.com'),
                                            array('name'=>'网易163邮箱', 'href'=>'smtp.163.com'),
                                            array('name'=>'网易企业邮（免费版）', 'href'=>'smtp.ym.163.com'),
                                        );
                                        echo '<label for="'.$opt.'"><p class="description" id="site_smtp_host_label">SMTP发件服务器（务必匹配发件邮箱</p><select name="'.$opt.'" id="'.$opt.'"><option value="">请选择</option>';
                                            foreach ($arrobj as $arr){
                                                $href = $arr['href'];
                                                echo '<option value="'.$href.'"';if(get_option($opt)==$href)echo('selected="selected"');echo '>'.$arr['name'].'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $smtp; ?>">
                                <th scope="row">— 邮箱发件测试</th>
                                <td>
                                    <span id="my_email_ajax_nonce" data-nonce="<?php echo wp_create_nonce("my_email_ajax_nonce"); ?>"></span>
                                    <p class="description">默认收/发件人均为管理员邮箱（发送后会更新当前配置状态是否成功</p>
                                    <span id="loading"></span>
                                    <input class="smtp sendmail update-target" type="button" value="发送测试邮件" />
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top" class="">
                        <th scope="row">禁用 XML-RPC 服务（防爆破）</th>
                        <td>
                            <?php
                                $opt = 'site_xmlrpc_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">防止攻击者绕过 wordpress 登录限制消耗系统资源（禁用后将无法使用 wp 官方APP及相关接口</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">XML-RPC</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">禁用图片上传自动裁剪</th>
                        <td>
                            <?php
                                $opt = 'site_imgcrop_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">一般图片上传裁剪规则可在<a href="/wp-admin/options-media.php" target="_blank"> 媒体 </a>中修改</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">禁用裁剪</b></label>';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="formtable index">
                <h1><b class="num" style="color: var(--block-two);">03</b>页面配置<p class="en">PAGES CONFS</p></h1>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">站点头部公告<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_inform_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_inform_switcher_label">部分页面头部公告显示内容（支持第三方数据储存</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">站点公告</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_inform_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_inform_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 公告数量</th>
                                <td>
                                    <?php
                                        $opt = 'site_inform_num';
                                        $value = get_option($opt);
                                        $preset = 3;  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_bar_pixiv_label">公告展示数量（默认展示 最新发布 的 3 条公告</p><input type="number" max="" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php 
                        // } 
                    ?>
                    <tr valign="top">
                        <th scope="row">面包屑导航</th>
                        <td>
                            <?php
                                $opt = 'site_breadcrumb_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_breadcrumb_switcher_label">页面当前位置（面包屑导航</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">快捷导航</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">文章列表预览图</th>
                        <td>
                            <?php
                                $opt = 'site_default_postimg_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">默认当文章存在自定义 thumbnail 特色图片时才显示列表预览图，开启后将始终显示（显示优先级：自定义特色图片>文章内图片>默认图片</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">默认预览</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">展示型文章分类</th>
                        <td>
                            <?php
                                $opt = 'site_single_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">非展示型分类文章默认使用相应单页模板（开启指定分类下的文章将<u> 默认不显示文章链接 </u>（只可在归档中查阅</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">展示分类</b></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_single_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 开启页面（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_single_includes';  //unique str
                                        $value = get_option($opt);
                                        $async_opts = array($templates_info['weblog'], $templates_info['acg'], $templates_info['download']);
                                        if(!$value){
                                            $preset_str = $async_opts[0]->slug.','.$async_opts[1]->slug.','.$async_opts[2]->slug.',';
                                            update_option($opt, $preset_str);
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="">指定开启展示单页分类，使用逗号“ , ”分隔（默认开启日志、漫游影视、资源下载页面</p><div class="checkbox">';
                                        $async_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($async_array);
                                        foreach ($async_opts as $option) {
                                            if ($option->error) continue;
                                            $opts_slug = $option->slug;
                                            $checking = in_array($opts_slug, $async_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$opts_slug.'" type="checkbox" value="'.$opts_slug.'" '.$checking.' /><label for="'.$opt.'_'.$opts_slug.'">'.$option->name.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top" class="">
                        <th scope="row">文章 TOC 目录</th>
                        <td>
                            <?php
                                $opt = 'site_indexes_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_indexes_includes', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="">文章页 table of content 目录索引，开启后在文章页可见（建议 notes 类型</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /><b>TOC 目录</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_indexes_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_indexes_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 索引目录分类（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_indexes_includes';  //unique str
                                        $value = get_option($opt);
                                        echo '<p class="description" id="">选定分类下文章模版将开启目录索引，使用逗号“ , ”分隔（默认 notes 类型</p><div class="checkbox">';
                                        $news_cat =  $templates_info['news'];
                                        $notes_cat =  $templates_info['notes'];
                                        $arrobj = array();
                                        if($notes_cat && $news_cat){
                                            array_push($arrobj, array('name' => $notes_cat->name, 'slug' => $notes_cat->slug));
                                            array_push($arrobj, array('name' => $news_cat->name, 'slug' => $news_cat->slug));
                                        }elseif($notes_cat){
                                            array_push($arrobj, array('name' => $notes_cat->name, 'slug' => $notes_cat->slug));
                                        }elseif($news_cat){
                                            array_push($arrobj, array('name' => $news_cat->name, 'slug' => $news_cat->slug));
                                        }
                                        if (empty($arrobj)) {
                                            echo '<b> Empty Index </b>';
                                        } else {
                                            $preset = $arrobj[0]['slug'].',';
                                            if(!$value){
                                                update_option($opt, $preset);
                                                $value = $preset;
                                            }
                                            $pre_array = explode(',',trim($value));  // NO "," Array
                                            // $pre_array_count = count($pre_array);
                                            foreach ($arrobj as $array){
                                                $slug = $array['slug'];
                                                $checking = in_array($slug, $pre_array) ? 'checked' : '';
                                                echo '<input id="'.$opt.'_'.$slug.'" type="checkbox" value="'.$slug.'" '.$checking.' /><label for="'.$opt.'_'.$slug.'">'.$array['name'].'</label>';
                                            }
                                            echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php 
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">首页 - banner</th>
                        <td>
                            <?php
                                $opt = 'site_banner_array';
                                $value = get_option($opt);
                                $preset = ''; //$img_cdn.'/images/fox.jpg,';
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update
                                $arr = explode(',', trim($preset));
                                $arr_count = count($arr);
                            ?>
                                <p class="description" id="site_banner_array_label">首页 banner 组图数组，使用逗号“ , ”分隔，图库中按住“CTRL”多选图片/视频（缺省默认使用预设全景预览</p>
                                    <label for="upload_banner_button" class="upload upload_preview_list">
                            <?php
                                        for($i=0;$i<$arr_count;$i++){
                                            if(isset($arr[$i]) && !empty($arr[$i])) {
                                                $media_src = $arr[$i];
                                                echo '<video class="upload_preview bgm" src="'.$media_src.'" poster="'.$media_src.'" preload="" autoplay="" muted="" loop="" x5-video-player-type="h5" controlslist="nofullscreen nodownload"></video>';
                                            }
                                        }
                            ?>
                                    </label>
                                <input type="text" name="<?php echo $opt ?>" class="large-text upload_field" value="<?php echo $preset; ?>" style="max-width:88%" placeholder="选择媒体文件" />
                                <input id="upload_banner_button" type="button" class="button-primary upload_button multi" data-multi=true data-type=0 value="选择媒体" />
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">首页 - 卡片文章</th>
                        <td>
                            <?php
                                $opt = 'site_rcmdside_cid';
                                // $preset = $cats[0]->term_id;//get_category_by_slug('news')->term_id;  // can not get cid by '/') 
                                $value = get_option($opt);
                                // if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if options unset
                                echo '<label for="'.$opt.'"><p class="description" id="site_rcmdside_cid_label">默认使用“news”分类（应用于首页右侧推荐分类文章卡片展示</p><select name="'.$opt.'" id="'.$opt.'"><option value="">请选择</option>';
                                    category_options($value);
                                echo '</select><label>';
                            ?>
                        </td>
                    </tr>
                    <!--<tr valign="top">-->
                    <!--    <th scope="row">首页 - 卡片导航</th>-->
                    <!--    <td>-->
                            <?php
                                // $opt = 'site_cardnav_array';
                                // $value = get_option($opt);
                                // $preset = 'news/文; notes/筆; weblog/記; links/友'; 
                                // if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                // echo '<p class="description" id="site_cardnav_array_label">展示在首页的导航卡片，使用分号“ ; ”分隔（使用斜杠“ / ”自定义名称（留空默认分类名称）如 news/文; notes/笔...</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $preset . '"/>';
                            ?>
                    <!--    </td>-->
                    <!--</tr>-->
                    <tr valign="top">
                        <th scope="row">首页 - 卡片导航 / 列表</th>
                        <td>
                            <?php
                                $opt = 'site_cardnav_includes';  //unique str
                                $value = get_option($opt);
                                $async_opts = array($templates_info['news'], $templates_info['notes'],  $templates_info['weblog'],  $templates_info['acg'],  $templates_info['2bfriends']);
                                if(!$value){
                                    $preset_str = $async_opts[0]->slug.','.$async_opts[1]->slug.','.$async_opts[2]->slug.','.$async_opts[3]->slug.',';
                                    update_option($opt, $preset_str);
                                    $value = $preset_str;
                                }
                                echo '<p class="description" id="">展示在首页的导航卡片及对应文章列表，可多选（默认选择前四个</p><div class="checkbox">';
                                $async_array = explode(',',trim($value));  // NO "," Array
                                // $pre_array_count = count($async_array);
                                foreach ($async_opts as $option) {
                                    if ($option->error) continue;
                                    $opts_slug = $option->slug;
                                    $checking = in_array($opts_slug, $async_array) ? 'checked' : '';
                                    echo '<input id="'.$opt.'_'.$opts_slug.'" type="checkbox" value="'.$opts_slug.'" '.$checking.' /><label for="'.$opt.'_'.$opts_slug.'">'.$option->name.'</label>';
                                }
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">首页 - 友链分类</th>
                        <td>
                            <?php
                                $opt = 'site_list_links_category';
                                $value = get_option($opt);
                                $lists = get_links_category();
                                if($lists && !$value) {
                                    $preset_ids = '';
                                    foreach ($lists as $list) {
                                        $preset_ids .= $list->term_id . ',';
                                    }
                                    update_option($opt, $preset_ids);
                                    $value = $preset_ids;
                                }
                                echo '<p class="description" id="">首页随机友链列表指定分类（默认所有类目</p><div class="checkbox">';
                                if ($lists) {
                                    $async_array = explode(',',trim($value));  // NO "," Array
                                    foreach ($lists as $list) {
                                        $opts_id = $list->term_id;
                                        $checking = in_array($opts_id, $async_array) ? 'checked' : '';
                                        echo '<input id="'.$opt.'_'.$opts_id.'" type="checkbox" value="'.$opts_id.'" '.$checking.' /><label for="'.$opt.'_'.$opts_id.'">'.$list->name.'</label>';
                                    }
                                }
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly value="' . $value . '" placeholder="默认全选"/></div>';
                            ?>
                        </td>
                    </tr>
                    <!--<tr valign="top">-->
                    <!--    <th scope="row">首页 - 列表背景</th>-->
                    <!--    <td>-->
                            <?php
                                // $opt = 'site_list_bg';
                                // $value = get_option( $opt, '' );
                                // echo '<p class="description" id="site_about_video_label">首页卡片导航下方左侧背景图（带动画</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $preset . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" placeholder="列表背景" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button multi" data-type="" value="选取文件">';
                            ?>
                    <!--    </td>-->
                    <!--</tr>-->
                    <tr valign="top">
                        <th scope="row">首页 - TECH 单栏<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_techside_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_techside_cid', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                // $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_techside_switcher_label">开启首页科技资讯栏目（默认开启，选择任意项后可手动关闭，支持多分类及baas数据</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">TECH</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_techside_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $logs = get_option('site_techside_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 日志日记分类</th>
                                <td>
                                    <?php
                                        $opt = 'site_techside_cid';
                                        // $preset = $cats_haschild[0]->term_id;//get_category_by_slug('weblog')->term_id;  //return cid for recent_posts_query
                                        $value = get_option($opt);
                                        // if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if options unset
                                        echo '<label for="'.$opt.'"><p class="description" id="site_techside_cid_label">图文资讯分类</p><select name="'.$opt.'" id="'.$opt.'"><option value="">请选择</option>';
                                            category_options($value);
                                        echo '</select><label>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">首页 - ACG 栏目<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_acgnside_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_acgnside_cid', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                // $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_acgnside_switcher_label">开启首页科技资讯栏目（默认开启，选择任意项后可手动关闭，支持多分类及baas数据</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">ACGN</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_acgnside_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $acgn = get_option('site_acgnside_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— ACGN分类</th>
                                <td>
                                    <?php
                                        $opt = 'site_acgnside_cid';
                                        // $preset = $cats_haschild[0]->term_id;//get_category_by_slug('acg')->term_id;  //return cid for recent_posts_query
                                        $value = get_option($opt);
                                        // if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if options unset
                                        echo '<label for="'.$opt.'"><p class="description" id="site_acgnside_cid_label">默认使用“acg”模板分类</p><select name="'.$opt.'" id="'.$opt.'"><option value="">请选择</option>';
                                            category_options($value);
                                        echo '</select><label>';
                                    ?>
                                </td>
                            </tr>
                            <!--<tr valign="top" class="child_option dynamic_optshow">-->
                            <!--    <th scope="row">— 分类展示数量</th>-->
                            <!--    <td>-->
                                    <?php
                                        // $opt = 'site_acgnside_num';
                                        // $value = get_option($opt);
                                        // $preset = 5;  //默认填充数据
                                        // if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        // echo '<p class="description" id="site_bar_pixiv_label">分类展示数量（默认展示显示5条</p><input type="number" max="" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                            <!--    </td>-->
                            <!--</tr>-->
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">首页 - 标签云</th>
                        <td>
                            <?php
                                $opt = 'site_tagcloud_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_tagcloud_num', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">首页随机标签云（自带主题色，若检测到无标签将默认展示随机动漫图；可在页面缓存中开启 tagclouds</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:cornflowerblue;" class="btn">标签の云</span></label>';
                            ?>
                        </td>
                    </tr>
                        <tr valign="top" class="child_option dynamic_opts <?php echo $tags = get_option('site_tagcloud_switcher') ? 'dynamic_optshow' : false; ?>">
                            <th scope="row">— 标签展示数量</th>
                            <td>
                                <?php
                                    $opt = 'site_tagcloud_num';
                                    $value = get_option($opt);
                                    $preset = 32;  //默认填充数据
                                    if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                    echo '<p class="description" id=""> 最多显示数量（默认显示 32 个</p><input type="number" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                ?>
                            </td>
                        </tr>
                        <tr valign="top" class="child_option dynamic_opts <?php echo $tags; ?>">
                            <th scope="row">— 标签最大字体</th>
                            <td>
                                <?php
                                    $opt = 'site_tagcloud_max';
                                    $value = get_option($opt);
                                    $preset = 30;  //默认填充数据
                                    if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                    echo '<p class="description" id=""> 最大显示字体（默认最大 30px，最小 10px</p><input type="number" min="11" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                ?>
                            </td>
                        </tr>
                        <tr valign="top" class="child_option dynamic_opts <?php echo $tags; ?>">
                            <th scope="row">— 始终更新缓存</th>
                            <td>
                                <?php
                                    $opt = 'site_tagcloud_auto_caches';
                                    $status = check_status($opt);
                                    echo '<label for="'.$opt.'"><p class="description" id="">自动更新标签云缓存（开启后访问标签云时将始终更新缓存为最新，默认每日自动更新</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">禁用缓存</b></label>';
                                ?>
                            </td>
                        </tr>
                    <tr valign="top">
                        <th scope="row"> 缓存索引 - 页面配置 </th>
                        <td>
                            <?php
                                $opt = 'site_cache_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_cache_includes', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">部分页面使用 db 索引缓存数据（默认开启，<del>开启此项可能影响 _ajax_nonce 校验</del> 校验已修复</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:sienna;" class="btn">页面缓存</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $async = get_option('site_cache_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 开启页面（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_cache_includes';  //unique str
                                        $value = get_option($opt);
                                        $rss_feeds = new stdClass();
                                        $tag_clouds = new stdClass();
                                        $rss_feeds->name = 'RSS 友链订阅';
                                        $rss_feeds->slug = 'rssfeeds';
                                        $tag_clouds->name = 'TAG 标签云';
                                        $tag_clouds->slug = 'tagclouds';
                                        $async_opts = array($templates_info['news'], $templates_info['notes'], $templates_info['weblog'], $templates_info['acg'], $templates_info['2bfriends'], $templates_info['download'], $templates_info['archive'], $templates_info['ranks'], $tag_clouds, $rss_feeds);
                                        // print_r($async_opts);
                                        if(!$value) {
                                            $preset_str = $rss_feeds->slug.','; //$async_opts[3]->slug.','.$async_opts[5]->slug.','.$async_opts[6]->slug.','.
                                            update_option($opt, $preset_str);
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="">指定开启缓存索引页面，使用逗号“ , ”分隔（默认开启归档、漫游影视及友链页面。注：rss订阅为wp后台应用缓存，tag云为组件缓存<br />缓存时效：在此处开启缓存后，<u>某些页面设定后可能无法立即生效！</u>需等待缓存更新或在此取消对应缓存</p><div class="checkbox">';
                                        $async_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($async_array);
                                        foreach ($async_opts as $option){
                                            if (!isset($option->slug) || !isset($option->name)) {
                                                continue;
                                            }
                                            $opts_slug = $option->slug;
                                            $checking = in_array($opts_slug, $async_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$opts_slug.'" type="checkbox" value="'.$opts_slug.'" '.$checking.' /><label for="'.$opt.'_'.$opts_slug.'">'.$option->name.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row"> 异步加载 - 页面配置 </th>
                        <td>
                            <?php
                                $opt = 'site_async_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_async_archive', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">部分页面使用 ajax 异步加载数据（默认开启（为缓解数据库请求压力，归档已启用数据库索引，若此项修改提交后无效 可通过<b> 更新/发布/删除 </b>文章重建缓存</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:slateblue;" class="btn">异步加载</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $async = get_option('site_async_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 开启页面（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_async_includes';  //unique str
                                        $value = get_option($opt);
                                        $async_opts = array($templates_info['archive'], $templates_info['acg'],  $templates_info['weblog']);
                                        if(!$value){
                                            $preset_str = $async_opts[0]->slug.','.$async_opts[1]->slug.',';
                                            update_option($opt, $preset_str);
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="">指定开启 ajax 异步页面，使用逗号“ , ”分隔（默认开启漫游影视、归档页面</p><div class="checkbox">';
                                        $async_array = explode(',',trim($value));  // NO "," Array
                                        // $pre_array_count = count($async_array);
                                        foreach ($async_opts as $option) {
                                            if ($option->error) continue;
                                            $opts_slug = $option->slug;
                                            $checking = in_array($opts_slug, $async_array) ? 'checked' : '';
                                            echo '<input id="'.$opt.'_'.$opts_slug.'" type="checkbox" value="'.$opts_slug.'" '.$checking.' /><label for="'.$opt.'_'.$opts_slug.'">'.$option->name.'</label>';
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                                    ?>
                                </td>
                            </tr>
                            <?php
                                $acg_cat = $async_opts[1];
                                if(isset($acg_cat->slug) && in_array($acg_cat->slug, $async_array)) {
                            ?>
                                    <tr valign="top" class="child_option dynamic_opts <?php echo $async; ?>">
                                        <?php echo '<th scope="row">— '.$acg_cat->name.' 数量</th>'; ?>
                                        <td>
                                            <?php
                                                $opt = 'site_async_acg';
                                                $value = get_option($opt);
                                                $preset = 9;  //默认填充数据
                                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                                echo '<p class="description" id="site_bar_pixiv_label">漫游影视默认/手动加载数量（默认 9</p><input type="number" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                            ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                                $weblog_cat = $async_opts[2];
                                if(isset($weblog_cat->slug) && in_array($weblog_cat->slug, $async_array)){
                            ?>
                                <tr valign="top" class="child_option dynamic_opts <?php echo $async; ?>">
                                    <?php echo '<th scope="row">— '.$weblog_cat->name.' 数量</th>'; ?>
                                    <td>
                                        <?php
                                            $opt = 'site_async_weblog';
                                            $value = get_option($opt);
                                            $preset = get_option('posts_per_page');  //默认填充数据
                                            if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                            echo '<p class="description" id="site_bar_pixiv_label">日志·记默认/手动加载数量（默认 '.get_option('posts_per_page').'</p><input type="number" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                }
                                $archive_cat = $async_opts[0];
                                if(isset($archive_cat->slug) && in_array($archive_cat->slug, $async_array)){
                            ?>
                                <tr valign="top" class="child_option dynamic_opts <?php echo $async; ?>">
                                    <?php echo '<th scope="row">— '.$archive_cat->name.' 数量</th>'; ?>
                                    <td>
                                        <?php
                                            $opt = 'site_async_archive';
                                            $value = get_option($opt);
                                            $preset = 8;  //默认填充数据
                                            if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                            echo '<p class="description" id="site_bar_pixiv_label">归档默认/手动加载数量（默认 8</p><input type="number" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                    <tr valign="top">
                        <th scope="row"> AI 文章摘要 <sup>AI</sup> </th>
                        <td>
                            <?php
                                $opt = 'site_chatgpt_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">指定文章类型中自动生成 AI 摘要，内建本地文件缓存机制，仅首次请求返回付费（目前支持 3 个模型 api（chatgpt / kimi / deepseek）请根据不同 model 选择合适的 max token</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:purple" class="btn">文章摘要</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt = get_option('site_chatgpt_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— API Key <sup title="兼容选项">OPENAI</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_apikey';
                                        $value = get_option($opt);
                                        echo '<p class="description" id="">API Kyes 账号密钥（兼容密钥</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="API Key" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— API Proxy <sup title="兼容选项">OPENAI</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_proxy';
                                        $value = get_option($opt);
                                        $preset = 'https://api.openai.com';  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="">API 反代链接（默认 https://api.openai.com，可选兼容接口 https://api.moonshot.cn、https://api.deepseek.com</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Proxy URL" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— API Lists <sup title="兼容选项">OPENAI</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_apis';
                                        $value = get_option($opt);
                                        $models = ['/v1/chat/completions', '/v1/completions', '/chat/completions'];
                                        if(!$value) update_option($opt, $models[0]);else $preset=$value;  //auto update option to default if unset
                                        echo '<label for="'.$opt.'"><p class="description" id="">API 接口列表，/v1/completions 接口会调用 prompt（默认 /v1/chat/completions</p><select name="'.$opt.'" id="'.$opt.'" class="select_options">';
                                            foreach ($models as $mod) {
                                                echo '<option value="'.$mod.'"';
                                                if($value==$mod) echo('selected="selected"');
                                                echo '>'.$mod.'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— API Model <sup title="兼容选项">OPENAI</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_model';
                                        $value = get_option($opt);
                                        $models = ['gpt-3.5-turbo','text-davinci-003','Curie', 'moonshot-v1-8k','moonshot-v1-32k','moonshot-v1-128k','deepseek-chat','deepseek-coder'];
                                        if(!$value) update_option($opt, $models[0]);else $preset=$value;  //auto update option to default if unset
                                        echo '<label for="'.$opt.'"><p class="description" id="">可选 AI 对话模型，默认使用 gpt-3.5-turbo，<a href="https://openai.com/pricing" target="_blank">价格参考</a>，可选 Moonshot 系列模型，<a href="https://platform.moonshot.cn/docs/pricing/chat" target="_blank">价格参考</a>，可选 Deepseek 系列模型，<a href="https://api-docs.deepseek.com/zh-cn/quick_start/pricing" target="_blank">价格参考</a></p><select name="'.$opt.'" id="'.$opt.'" class="select_options">';
                                            foreach ($models as $mod){
                                                echo '<option value="'.$mod.'"';
                                                if($value==$mod) echo('selected="selected"');
                                                echo '>'.$mod.'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— Max Token</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_tokens';
                                        $value = get_option($opt);
                                        $preset = 4096;  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_bar_pixiv_label">限制消耗 token 总数，openAI 限制中文请求字符 token*2：请求 prompt 最大限制 4096，默认实际可用 3700+ prompt_token，余下 392 字符为 completion_token 响应预设占位，估算可返回150中文字符左右。（默认4096，预留（减少）196</p><input type="number" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— Temperature</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_temper';
                                        $value = get_option($opt);
                                        $preset = 0.8;  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_bar_pixiv_label">返回内容随机程度（最小0.0，默认0.8</p><input type="number" min="0.0" max="" step="0.1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 合并分割请求</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_merge_sw';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">此项主要用于长篇文章场景（更新：DeepSeek API 不限制用户并发量），开启自动计算文章字符请求所需 token 若大于模型限制 token （<u>gpt-3.5 默认 4096，限制输入 3700+</u>）则取消全文请求并自动将文章分割为上下文两段分别请求摘要，请求完成后合并上下文摘要内容再请求全文综合摘要。开启此项后若遇到长文，至少会消耗 3 次请求（内容 token 小于规定内仅请求一次</p><p>chat 模型下免费账号<a href="https://platform.openai.com/account/rate-limits" target="_blank">每分钟限制请求（RPM）为3次</a>（若请求返回 context_length_exceeded 错误代码时可尝试开启下方<b> “始终合并请求” </b>选项，<u><i>为节省 token 此项默认关闭</i></u></p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Summarize summaries</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 始终合并请求</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_merge_ingore';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">此项主要用于合并分割请求失败（二次请求 token 大于 4096）时，忽略后续返回错误并追加生成文章尾段摘要，再合并<b>首次+末尾</b>摘要生成<b>综合摘要</b></p><p>开启此项同样会消耗至少3次请求（不与分割请求叠加，可能丢失部分文章中段内容，<u><i>但可始终保持文章首尾逻辑</i></u></p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Always Summarize</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 已缓存数据（更新）</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_caches';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">本地已缓存文章摘要数据，勾选后<ins> 提交保存 </ins>以显示记录（倒序，默认最近10条）<b>。点击文章ID可删除对应记录（不可逆）</b>，<ins>悬浮文章ID</ins> 可查看文章标题及摘要</p><p>删除文章摘要记录后，<u><i>重新访问文章以更新摘要</i></u></p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Cached Posts</b></label>';
                                        if (get_option($opt)) {
                                            // global $cached_post;
                                            if (!$cached_post) {
                                                include(get_template_directory() . '/plugin/'.get_option('site_chatgpt_dir').'/gpt_data.php');
                                            }
                                            $res_cls_obj = json_decode(json_encode(array_reverse($cached_post)));
                                            $echo_count = 0;
                                            $echo_limit = 10;
                                            echo '<ul class="cached_post_list">';
                                            foreach ($res_cls_obj as $cached_pid => $cached_post){
                                                $echo_count++;
                                                $text_res = api_get_resultText($cached_post);
                                                if(!$text_res){
                                                    $text_res = $cached_pid.' => NULL';
                                                }
                                                $cached_post_content = preg_replace('/.*\n/','', $text_res);
                                                $cached_post_pid = preg_replace('/[^0-9]/', '', $cached_pid);
                                                $cached_post_title = get_the_title($cached_post_pid);
                                                echo '<li data-id="'.$cached_post_pid.'" data-content="'.str_replace('"',"'",$cached_post_content).'" title="'.$cached_post_title.'"></li>';
                                                if($echo_count>=$echo_limit) break;
                                            }
                                            echo '</ul>';
                                    ?>
                                            <script>const cached_posts=document.querySelector('.cached_post_list');cached_posts.onclick=(e)=>{e=e||window.event;let t=e.target||e.srcElement;if(!t)return;while(t!=cached_posts){if(t.nodeName.toUpperCase()==='LI'){const cached_pid=t.dataset.id,cached_title=t.title;if(confirm('确认删除（更新）：'+cached_title+' 摘要内容？')){return new Promise(function(resolve,reject){var ajax=new XMLHttpRequest();ajax.open('get',"<?php echo get_stylesheet_directory_uri().'/plugin/'.get_option('site_chatgpt_dir').'/gpt.php?pid='; ?>"+cached_pid+"&del=1");ajax.onreadystatechange=function(){if(this.readyState!=4)return;if(this.status==200){resolve();t.remove();if(this.responseText==404) alert('此记录先前已被清除（可能刷新过快，尝试重新刷新）');}else{reject(this.status)}};ajax.withCredentials=true;ajax.send()}).catch(function(err){console.log(err)})}else{console.log(cached_pid+' canceled.')}break}else{t=t.parentNode}}}</script>
                                    <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 开启页面（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_includes';
                                        $value = get_option($opt);
                                        if(!$value){
                                            $preset_str = $templates_info['weblog']->term_id.',';
                                            update_option($opt, $preset_str );
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="site_bottom_nav_label">指定开启 chatGPT AI 摘要文章页面（使用逗号“ , ”分隔，可选多个分类</p><div class="checkbox">';
                                        output_article_opts($opt, $value);
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 同步文章 RSS 摘要</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_feed_sw';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">使用文章AI摘要填充 rss 页面 feed 内容（启用此项可减少页面缓存数据调用</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">AI RSS</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 同步文章 SEO 描述</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_desc_sw';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">使用文章AI摘要填充 文章页面 description 描述（引入本地缓存文件过大可能影响性能</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">AI SEO</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 打字机效果</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_type_sw';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">使用打字机效果填充摘要文本，可能的 CLS 影响</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Words Typer</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— 混淆打字文本</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_type_shuffle';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">一次性加载混淆文本，避免频繁更新页面造成 CLS 性能影响</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">Shuffle Words</b></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $chatgpt; ?>">
                                <th scope="row">— auth Directory</th>
                                <td>
                                    <?php
                                        $opt = 'site_chatgpt_dir';
                                        $value = get_option($opt);
                                        $preset = 'authentication';  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="">GPT 文件目录（留空默认 authentication</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="normal-text" placeholder="chatGPT auth directory" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row"> 划线标记 <sup>Beta</sup> </th>
                        <td>
                            <?php
                                $opt = 'site_marker_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">开启后<del>默认通用</del>文章模板页面<del>（可禁用文章模板）</del>使用划线标记功能（该功能目前仅限评论用户使用，用户信息自动与 <u>通用控制->评论系统</u> 同步获取</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:forestgreen" class="btn">划线标记</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $marker_sw = get_option('site_marker_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 最大标记数量</th>
                                <td>
                                    <?php
                                        $opt = 'site_marker_max';
                                        $value = get_option($opt);
                                        $preset = 3;  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_bar_pixiv_label">Marker 最大标记数量（最大展示10个，默认开启 3 个</p><input type="number" max="10" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row"> 流式传输 API <sup>SSE</sup> </th>
                        <td>
                            <?php
                                $opt = 'site_stream_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_acgnside_switcher_label">后端 api 数据流式传输至前端（EventStream 接收输出，支持marker、gpt..</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">EventStream</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"> 足迹地图 </th>
                        <td>
                            <?php
                                $opt = 'site_footprint_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">聚合点足迹地图（tip: 将坐标数据及全景数据留空可恢复默认值</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:cornflowerblue" class="btn">足迹地图</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $footprint = get_option('site_footprint_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— Map</th>
                                <td>
                                    <?php
                                        $opt = 'site_footprint_map';
                                        $value = get_option($opt);
                                        $maps = array(
                                            array('name'=>'高德地图', 'type'=>'amap'),
                                            array('name'=>'腾讯地图', 'type'=>'tmap'),
                                        );
                                        $preset = $maps[0]['type'];
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update
                                        echo '<label for="'.$opt.'"><p class="description" id="">显示地图类型，默认使用高德地图 <a href="https://lbs.amap.com/demo/javascript-api-v2/example/mass-markers/marker-indexcluster2" target="_blank">文档参考</a>，可选腾讯地图 <a href="https://lbs.qq.com/webDemoCenter/glAPI/glMarkerCluster/markerCluster" target="_blank">文档参考</a></p><select name="'.$opt.'" id="'.$opt.'" class="select_options">';
                                            foreach ($maps as $map){
                                                $type = $map['type'];
                                                echo '<option value="'.$type.'"';if($preset==$type)echo('selected="selected"');echo '>'.$map['name'].'</option>'; // preview="'.$img_cdn.'/images/settings/'.$type.'.png"
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $footprint; ?>">
                                <th scope="row">— API Key</th>
                                <td>
                                    <?php
                                        $opt = 'site_footprint_apikey';
                                        $value = get_option($opt);
                                        echo '<p class="description" id="">地图 API 调用 KEY</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="API Key" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $footprint; ?>">
                                <th scope="row">— 坐标数据</th>
                                <td>
                                    <?php
                                        $opt = 'site_footprint_data';
                                        $value = get_option($opt);
                                        $preset_ = '
// 配置贴图（maps 数据来源下方全景数据）
const defaultTexture = maps.map_01;
const returnsTexture = window.structuredClone ? window.structuredClone(defaultTexture) : JSON.parse(JSON.stringify(defaultTexture));
maps.map_01.entry.push(maps.map_02);
maps.map_02.entry.push(returnsTexture);
const defaultTextureString = JSON.stringify(defaultTexture);
const encodedTextureString = encodeURIComponent(defaultTextureString);
';
                                        $preset = $preset_ . '
markerData = {
    // 配置标记点
    points: [
        {
            latlng: "39.925077049391,116.506621867519",
            position: "39.925077049391,116.506621867519",
            thumbnail: "//img.2broear.com/2025/06/right.webp",
            content: `<h2>Content</h2><p>overwrite context to content</p><img src="//img.2broear.com/2025/06/right.webp" />`,
            context: "晨光家园",
            district: "朝阳区",
            city: "北京",
        },
        {
            latlng: "39.957363077042,116.412789588355",
            position: "39.957363077042,116.412789588355",
            thumbnail: "//img.2broear.com/2025/06/front.webp",
            content: `<iframe src="//node.2broear.com/?data=${encodedTextureString}" frameborder="no" width="500" height="200"></iframe>`,
            context: "东城家园",
            district: "东城区",
            city: "北京",
        }
    ],
    // 配置标记区
    district: {
        "北京": {
            "latlng" : "39.904989,116.405285",
        },
        "朝阳区": {
            "latlng" : "39.921489,116.486409",
        }
    }
};';
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="">手动填写数据源，自动插入上方坐标数据之上（示例数据已预设，points：标点，district：索引，content 参数支持嵌入 iframe 标签</p><p>注意：此项预设数据（留空）随地图类型改变而改变！</p><a class="dynamic_dom" data-dom="iframe" data-src="//lbs.qq.com/tool/getpoint/get-point.html" data-width="100%" data-height="500" title="点击加载坐标拾取器">坐标拾取器（TMap）</a><textarea name="'.$opt.'" id="'.$opt.'" rows="10" style="width: 100%;">'.$preset.'</textarea>'; //https://lbs.amap.com/tools/picker
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $footprint; ?>">
                                <th scope="row">— 全景数据列表（可选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_footprint_panorama_data';
                                        $value = get_option($opt);
                                        $preset = '
const maps = {
    map_01: {
        ctx: ["2BROEAR NEXT", "Heading"],
        // src: "//img.2broear.com/2025/06/minecraft30x.jpg",
        env: [],
        uvs: [0.8757278879540462, 0.903727826428567, 0.40605076349556524, 0.48124969014847924],
        point: {
            x: 379.73991320053943, y: -88.8495197768429, z: -414.5114041846273,
            px: -130, py: 30, pz: 0,
            rx: 0, ry: 0, rz: Math.PI * 0.5,
            width:80, height:320, deepth:5,
            cw: 320, ch: 320, cs: 256
        },
        entry: [],
    },
    map_02: {
        ctx: ["MINECRAFT", "v0.7.3"],
        // src: "//img.2broear.com/2025/05/Ayutthaya_SD60x.mp4", 
        env: [
            "//img.2broear.com/2025/06/1.16_panorama_2.webp",
            "//img.2broear.com/2025/06/1.16_panorama_0.webp",
            "//img.2broear.com/2025/06/1.16_panorama_4s.webp",
            "//img.2broear.com/2025/06/1.16_panorama_5s.webp", 
            "//img.2broear.com/2025/06/1.16_panorama_1.webp",
            "//img.2broear.com/2025/06/1.16_panorama_3.webp",
        ],
        uvs: [0.6754583295687697, 0.8029043481247053, 0.30890971887284235, 0.5107758060045948],
        point: {
            x: -20.15844995141832, y: -145.91485079560525, z: -472.9048238279224,
            px: 0, py: 100, pz: 200,
            width:100, height:400, deepth:10,
            rx: -Math.PI * 0.5, ry: 0, rz: -600,
            cw: 512, ch: 512, cs: 256
        },
        entry: [],
    },
};';
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="">手动填写全景地图数据（示例数据已预设：可通过设置 entry 参数以跳转其他 map（可设置回溯 map），可选设置环境地图（跳转同理</p><p><b>参数说明：</b></p><p>entry：点击进入下个场景入口（数组对象，可加入多个入口）；ctx：入口标题数组；env：环境贴图数组；uvs：可点击范围UV数组（需开启debug调试右击获取uv信息）；point：点击中心点（显示动画贴图区域，x,y,z信息需在debug调试右击事件获取；px,py,pz为自定义修饰偏移；rx,ry,rz为旋转动画贴图配置；width,height,deepth为动画贴图尺寸；cw,ch,cs为ctx入口标题画布尺寸，其中cs可调节画布/字体大小）</p><textarea name="'.$opt.'" id="'.$opt.'" rows="15" style="width: 100%;">'.$preset.'</textarea>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row"> Memos 备忘录 </th>
                        <td>
                            <?php
                                $opt = 'site_memos_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">开启 memos 页面备忘录</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:lightcoral" class="btn">Memos</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $memos = get_option('site_memos_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— Access Token<sup>必填</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_memos_apikey';
                                        $value = get_option($opt);
                                        echo '<p class="description" id="">API Access Token 密钥</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Memos Access Token" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $memos; ?>">
                                <th scope="row">— API Url</th>
                                <td>
                                    <?php
                                        $opt = 'site_memos_proxy';
                                        $value = get_option($opt);
                                        $preset = 'https://demo.usememos.com';  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="">API 调用服务部署地址，默认 https://demo.usememos.com</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="Memos Api url" value="' . $value . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $memos; ?>">
                                <th scope="row">— Query Pattern</th>
                                <td>
                                    <?php
                                        $opt = 'site_memos_pattern';
                                        $value = get_option($opt);
                                        $models = ['/','/all','/stats'];
                                        if(!$value) update_option($opt, $models[0]);else $preset=$value;  //auto update option to default if unset
                                        echo '<label for="'.$opt.'"><p class="description" id="">useMemos 查询类型，默认查询所有数据，可选 all 仅返回公开数据（除 ARCHIVED、PRIVATE.. 详细参考、<a href="https://github.com/orgs/usememos/discussions/1024" target="_blank">Memos API 非官方不完全说明</a>、<a href="https://learnku.com/articles/85218#d25ce0" target="_blank">memos 接口文档</a></p><select name="'.$opt.'" id="'.$opt.'" class="select_options">';
                                            foreach ($models as $mod){
                                                echo '<option value="'.$mod.'"';
                                                if($value==$mod) echo('selected="selected"');
                                                echo '>'.$mod.'</option>';
                                            }
                                        echo '</select></label>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">动画 - 滚动加载</th>
                        <td>
                            <?php
                                $opt = 'site_animated_scrolling_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">全站列表数据滚动渐进式加载动画</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:cadetblue" class="btn">滚动动画</span></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">动画 - 数字计数</th>
                        <td>
                            <?php
                                $opt = 'site_animated_counting_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">启用位于页面背景上的数字自动递增到目标值动画（目前支持归档及漫游影视页面，若此项修改提交后无效 可通过 更新/发布/删除 文章重建缓存</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:teal" class="btn">计数动画</span></label>';
                            ?>
                        </td>
                    </tr>
                    
                    <!-- Links options -->
                    <tr valign="top">
                        <th scope="row">友链 - RSS 卡片</th>
                        <td>
                            <?php
                                $opt = 'site_links_rss_cards_sw';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后默认加载RSS卡片信息流，可选开启前端手动加载卡片信息（</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:darkolivegreen" class="btn">RSS 卡片</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $link_states = get_option('site_links_rss_cards_sw') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 手动加载卡片信息 </th>
                                <td>
                                    <?php
                                        $opt = 'site_links_rss_cards_manual';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">开启后可在前端手动加载指定友链RSS内容（默认关闭</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b>按需加载</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">友链 - 滑块动画</th>
                        <td>
                            <?php
                                $opt = 'site_links_auto_slider';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后隶属“其他”分类的链接盒子会自动使用滑动动画，经过时暂停动画，指定时间内未操作将继续返回上次位置滑动</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:red" class="btn">SlideLink</span></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">友链 - 状态检测</th>
                        <td>
                            <?php
                                $opt = 'site_links_code_state';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后定期检测并显示友链状态，可选开启指定分类，若检测站点返回 400+ 错误将自动将该友链设置为 standby 不可访问状态（此项将消耗大量时间，默认每日6点更新</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:darkgreen" class="btn">友链状态</span></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $link_states = get_option('site_links_code_state') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 检测分类（多选）</th>
                                <td>
                                    <?php
                                        $opt = 'site_links_code_state_cats';  //unique str
                                        $value = get_option($opt);
                                        echo '<p class="description" id="">指定检测友链分类状态，使用逗号“ , ”分隔</p><div class="checkbox">';
                                        $exist_array = explode(',',trim($value));  // NO "," Array
                                        $links_array = get_links_category();
                                        if ($links_array) {
                                            foreach ($links_array as $link_category) {
                                                if (!$link_category) continue;
                                                $link_slug = $link_category->slug;
                                                $checking = in_array($link_slug, $exist_array) ? 'checked' : '';
                                                echo '<input id="'.$opt.'_'.$link_slug.'" type="checkbox" value="'.$link_slug.'" '.$checking.' /><label for="'.$opt.'_'.$link_slug.'">'.$link_category->name.'</label>';
                                            }
                                        }
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $link_states ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— RSS活性检测 <sup>Alpha</sup></th>
                                <td>
                                    <?php
                                        $opt = 'site_links_rss_alive_state';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">检测并显示友链RSS活性状态，超过 2 年未更新将被标记“待除草”状态（此项为 A 测，存在数据差异，不建议开启</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' disabled /> <b style="color:gray">ALIVE STATES</b></label>';
                                    ?>
                                </td>
                            </tr>
                    <!-- Archives options -->
                    <tr valign="top">
                        <th scope="row">归档 - 报表范围</th>
                        <td>
                            <?php
                                $opt = 'site_async_archive_contributions';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后显示<b>全年</b>（去年-今年当月）热度报表（默认显示当年/月份，若开启此项无效 可通过<b> 更新/发布/删除 </b>文章重建归档缓存索引，<u>或等待第二天自动刷新缓存</u></p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">年度报表</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">归档 - 分类统计</th>
                        <td>
                            <?php
                                $opt = 'site_async_archive_stats';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">开启后显示当年已发布文章分类统计（默认开启，已修复可能存在的性能问题</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">分类文章</b></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">关于 - MBTI数据</th>
                        <td>
                            <?php
                                $opt = 'site_mbit_array';
                                $value = get_option($opt);
                                $preset = 'after/64; before/67; after/69; after/71; before/53;'; 
                                if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                echo '<p class="description" id="">展示在关于页面的MBIT图表数据，使用分号“ ; ”分隔（使用斜杠“ / ”分隔类型和占比，如 before/64; after/67;...</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                        <tr valign="top" class="child_option dynamic_optshow">
                            <th scope="row">— MBIT测试结果</th>
                            <td>
                                <?php
                                    $opt = 'site_mbit_result_array';
                                    $value = get_option($opt);
                                    $preset = 'infp-a/mediator'; 
                                    if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                    echo '<p class="description" id="">MBIT测试人格类型，使用斜杠“ / ”分隔（规则同上</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . $preset . '"/>';
                                ?>
                            </td>
                        </tr>
                    <tr valign="top" class="">
                        <th scope="row">关于 - 背景视频</th>
                        <td>
                            <?php
                                $opt = 'site_about_video';
                                $value = get_option($opt);
                                $preset = '';
                                $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="site_about_video_label">关于我背景视频</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $preset . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" placeholder="for_empty_about_video" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=2 value="选择视频" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">留言板 - 背景视频</th>
                        <td>
                            <?php
                                $opt = 'site_guestbook_video';
                                $value = get_option($opt);
                                $preset = '';
                                $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="site_guestbook_video_label">留言板背景视频</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $preset . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" placeholder="for_empty_guestbook_video" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=2 value="选择视频" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">漫游影视 - 背景视频</th>
                        <td>
                            <?php
                                $opt = 'site_acgn_video';
                                $value = get_option($opt);
                                $preset = '';
                                $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="">漫游影视背景视频（开启后背景图片将作为视频的poster展示</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $preset . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" placeholder="for_empty_acgn_video" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=2 value="选择视频" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row">隐私政策 - 背景视频</th>
                        <td>
                            <?php
                                $opt = 'site_privacy_video';
                                $value = get_option($opt);
                                // $preset = $img_cdn.'/media/videos/data.mp4';
                                // $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="site_privacy_video_label">隐私政策背景视频</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $value . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" placeholder="for_empty_privacy_video" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=2 value="选择视频" />';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="formtable sidebar">
                <h1><b class="num" style="color: var(--block-three);">04</b>边栏设置<p class="en">SIDEBAR SETTINGS</p></h1>
                <table class="form-table sidebar">
                    <tr valign="top">
                        <th scope="row">Pixiv 排行（挂件）</th>
                        <td>
                            <?php
                                $opt = 'site_pixiv_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_bar_pixiv', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                echo '<label for="'.$opt.'"><p class="description" id="site_pixiv_switcher_label">p站挂件（可自定义至多展示50数量</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <span style="color:lightpink;" class="btn">PIXIV</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_pixiv_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $pixiv = get_option('site_pixiv_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— Pixiv 加载数量</th>
                                <td>
                                    <?php
                                        $opt = 'site_bar_pixiv';
                                        $value = get_option($opt);
                                        $preset = 10;  //默认填充数据
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_bar_pixiv_label">Pixiv 每日排名数量（最大展示50个，默认开启</p><input type="number" max="50" min="1" name="'.$opt.'" id="'.$opt.'" class="small-text" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">倒计时（挂件）</th>
                        <td>
                            <?php
                                $opt = 'site_countdown_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="">文章列表及内页侧边栏倒计时挂件（如需在其他页面自定义定时器，只需在调用 the_countdown_widget() 函数时新增下列三个子选项作为参数即可</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'"><span style="color:inherit;" class="btn">CountDown</span></b></label>';
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $countdown_sw = get_option('site_countdown_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 定时日期</th>
                                <td>
                                    <?php
                                        $opt = 'site_countdown_date';
                                        $value = get_option($opt);
                                        $preset = date("Y/m/d,H:i:s"); //gmdate('Y/m/d,H:i:s', time() + 3600*8);
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<p class="description" id="site_countdown_date_label">倒计时日期（日期格式为“YYYY/MM/DD,HH:MM:SS”，上午12点表示当日凌晨00:00</p><input type="datetime-local" name="'.$opt.'" id="'.$opt.'" value="' . $preset . '"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $countdown_sw ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 标题 / 结语</th>
                                <td>
                                    <p class="description" id="site_countdown_title_label">倒计时左上角显示名称，及倒计时结束标语（默认当年春节倒计时，使用“/”分隔</p>
                                    <?php
                                        $opt = 'site_countdown_title';
                                        $value = get_option($opt);
                                        $preset = gmdate('Y', time() + 3600*8).' 春节倒计时/新年快乐';
                                        if(!$value) update_option($opt, $preset);else $preset=$value;  //auto update option to default if unset
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="'.$preset.'" placeholder="'.$preset.'">';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $countdown_sw ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 背景图片/视频</th>
                                <td>
                                    <?php
                                        $opt = 'site_countdown_bgimg';
                                        $value = get_option($opt);
                                        $preset = $img_cdn.'/images/newyear.gif';
                                        $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                        echo '<p class="description" id="">倒计时背景图片/视频（默认新年 gif </p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="video" data-src="' . $preset . '" title="加载预览">加载视频</a></label><input type="text" name="'.$opt.'" class="regular-text upload_field" value="' . $preset . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button multi" data-type value="选取媒体" />';  //<em class="upload_preview bg" style="background:url('.$preset.') center center /cover;"></em>
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">侧边栏热门文章<sup class="dualdata" title="“多数据”">BaaS</sup></th>
                        <td>
                            <?php
                                $opt = 'site_mostview_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_mostview_cid', '' );
                                //设置默认开启（仅适用存在默认值的checkbox）
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                // $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="site_mostview_switcher_label">资讯、资讯文章分类页面侧边栏文章热度排行（支持第三方数据储存</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">热门文章</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_mostview_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_mostview_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 热门文章分类</th>
                                <td>
                                    <?php
                                        // $opt = 'site_mostview_cid';
                                        // $value = get_option($opt);
                                        // echo '<label for="'.$opt.'"><p class="description" id="site_mostview_cid_label">默认使用一级栏目首位“$cats_haschild[0]->slug”分类（亦可选用其他分类文章热度排行</p><select name="'.$opt.'" id="'.$opt.'"><option value="">请选择</option>';
                                        //     category_options($value);
                                        // echo '</select><label>';
                                        $opt = 'site_mostview_cat';
                                        $value = get_option($opt);
                                        if(!$value){
                                            $preset_str = $templates_info['news']->term_id.','.$templates_info['notes']->term_id.',';
                                            update_option($opt, $preset_str );
                                            $value = $preset_str;
                                        }
                                        echo '<p class="description" id="site_bottom_nav_label">页面底部最左侧资讯栏目分类（使用逗号“ , ”分隔，可选多个分类</p><div class="checkbox">';
                                        output_article_opts($opt, $value);
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                </table>
            </div>
            <div class="formtable footer">
                <h1><b class="num" style="color: var(--block-four);">05</b>页尾设置<p class="en">FOOTER SETTINGS</p></h1>
                <table class="form-table footer">
                    <tr valign="top">
                        <th scope="row">底部近期文章</th>
                        <td>
                            <?php
                                $opt = 'site_bottom_recent_cat';
                                $value = get_option($opt);
                                if(!$value){
                                    $preset_str = $templates_info['news']->term_id.',';
                                    update_option($opt, $preset_str );
                                    $value = $preset_str;
                                }
                                echo '<p class="description" id="site_bottom_nav_label">页面底部最左侧资讯栏目分类（使用逗号“ , ”分隔，可选多个分类</p><div class="checkbox">';
                                output_article_opts($opt, $value);
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">底部导航链接</th>
                        <td>
                            <?php
                                $opt = 'site_bottom_nav';  //unique str
                                $value = get_option($opt);
                                $options = array($templates_info['archive'], $templates_info['privacy'], $templates_info['footprints']); //array('privacy','archives');
                                if(!$value){
                                    $preset_str = $options[0]->slug.','.$options[1]->slug.',';
                                    update_option($opt, $preset_str );
                                    $value = $preset_str;
                                }
                                echo '<p class="description" id="site_bottom_nav_label">底部右下角导航链接（使用逗号“ , ”分隔，可选填其他分类 slug 别名</p><div class="checkbox">';
                                $pre_array = explode(',',trim($value));  // NO "," Array
                                // $pre_array_count = count($pre_array);
                                foreach ($options as $option) {
                                    if ($option->error) continue;
                                    $opts_slug = $option->slug;
                                    $checking = in_array($opts_slug, $pre_array) ? 'checked' : '';
                                    echo '<input id="'.$opt.'_'.$opts_slug.'" type="checkbox" value="'.$opts_slug.'" '.$checking.' /><label for="'.$opt.'_'.$opts_slug.'">'.$option->name.'</label>';
                                }
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text array-text" readonly value="' . $value . '" placeholder="请选择"/></div>';;
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">站点启动时间</th>
                        <td>
                            <?php
                                $opt = 'site_begain';
                                $value = get_option($opt);
                                $year = date('Y');
                                if(!$value) update_option($opt, $year);
                                $options = array();
                                for(;$year>1999;$year--){
                                    array_push($options,$year);
                                }
                                $options_count = count($options);
                                echo '<label for="'.$opt.'"><p class="description" id="site_begain_label">站点开启时间，单位年</p><select name="'.$opt.'" id="'.$opt.'">';
                                    for($i=0;$i<$options_count;$i++){
                                        $each = $options[$i];
                                        echo '<option value="'.$each.'"';if($value==$each)echo('selected="selected"');echo '>'.$each.'</option>';
                                    };
                                echo '</select></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">创作共用许可</th>
                        <td>
                            <?php
                                $opt = 'site_copyright';
                                $value = get_option($opt);
                                $options = ["CC-BY","CC-BY-SA","CC-BY-NC","CC-BY-ND","CC-BY-NC-SA","CC-BY-NC-ND","CC-SA","CC-NC","CC-ND","CC-NC-SA","CC-NC-ND"];
                                if(!$value) update_option($opt, $options[0]);
                                $options_count = count($options);
                                //output each options
                                echo '<label for="'.$opt.'"><p class="description" id="site_copyright_label">创作共用许可协议用于网站底部、文章署名等位置</p><select name="'.$opt.'" id="'.$opt.'">';
                                    for($i=0;$i<$options_count;$i++){
                                        $each = $options[$i];
                                        echo '<option value="'.$each.'"';if($value==$each)echo('selected="selected"');echo '>'.$each.'</option>';
                                    };
                                echo '</select><label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">服务器信息</th>
                        <td>
                            <?php
                                $opt = 'site_server_side';
                                $value = get_option($opt);
                                $arrobj = array(
                                    array('name'=>'阿里云', 'icon'=>$img_cdn.'/images/settings/alicloud.png'),
                                    array('name'=>'腾讯云', 'icon'=>$img_cdn.'/images/settings/tencentcloud.svg'),
                                    array('name'=>'华为云', 'icon'=>$img_cdn.'/images/settings/huaweiclouds.svg'),
                                );
                                echo '<label for="'.$opt.'"><p class="description" id="">网站应用服务器（页尾图标</p><img src="'.$value.'" style="vertical-align: middle;max-width: 66px;margin:auto 15px;" /><select name="'.$opt.'" id="'.$opt.'" class="select_images"><option value="">请选择</option>';
                                    foreach ($arrobj as $arr){
                                        $icon = $arr['icon'];
                                        $selected = $value==$icon ? 'selected="selected"' : false;
                                        echo '<option value="'.$icon.'"'.$selected.'>'.$arr['name'].'</option>';
                                    }
                                echo '</select></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">网站备案信息</th>
                        <td>
                            <?php
                                $opt = 'site_beian_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_beian_switcher_label">网站备案信息（国外服务器请无视此选项</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <b class="'.$status.'">备案号</b></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_beian_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_beian_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 备案号</th>
                                <td>
                                    <?php
                                        $opt = 'site_beian';
                                        echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . get_option($opt) . '" placeholder="网站备案号"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">十年之约</th>
                        <td>
                            <?php
                                $opt = 'site_foreverblog_switcher';
                                $value = get_option($opt);
                                $data = get_option( 'site_foreverblog', '' );
                                if(!$value&&!$data){
                                    update_option($opt, "on_default");
                                    $status="checked";
                                }else{
                                    $status = $value ? "checked" : "check";
                                };
                                // $status = $value ? "checked" : "check";
                                echo '<label for="'.$opt.'"><p class="description" id="site_foreverblog_switcher_label">页面底部展示“十年之约”图标（页尾图标</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /> <img src="'.$img_cdn.'/images/svg/foreverblog.svg" alt="wormhole" style="height: 15px; vertical-align:middle;"><!--<b class="'.$status.'">ForeverBlog 成员</b>--></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_foreverblog_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $foreverblog; ?>">
                                <th scope="row">— foreverblog 链接</th>
                                <td>
                                    <?php
                                        $opt = 'site_foreverblog';
                                        $value = get_option($opt);
                                        if(!$value) update_option($opt, "https://www.foreverblog.cn/blog/2096.html");
                                        echo '<p class="description" id="site_foreverblog_label">十年之约链接（foreverblog 图标</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $value . '" placeholder="foreverblog 链接"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $foreverblog = get_option('site_foreverblog_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— wormhole 虫洞</th>
                                <td>
                                    <?php
                                        $opt = 'site_foreverblog_wormhole';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="site_foreverblog_wormhole_label">随机访问十年之约友链博客（页尾图标</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /><!--<b class="'.$status.'">穿梭虫洞</b>--> <img src="'.$img_cdn.'/images/wormhole_4_tp_ez.gif" alt="wormhole" style="height: 22px; vertical-align:middle;"></label>'; 
                                    ?>
                                </td>
                            </tr>
                            <!--<tr></tr>-->
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">萌备博主</th>
                        <td>
                            <?php
                                $opt = 'site_moe_beian_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_foreverblog_wormhole_label">萌国 ICP 备案（页尾图标</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /><img src="https://icp.gov.moe/images/ico64.png" alt="moe_beian" style="height: 22px;vertical-align:middle;"></label>'; 
                            ?>
                        </td>
                    </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $moe_beian_statu = get_option('site_moe_beian_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 萌备案号</th>
                                <td>
                                    <?php
                                        $opt = 'site_moe_beian_num';
                                        echo '<p class="description" id="">萌国ICP备案号（数字</p><input type="number" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . get_option($opt) . '" placeholder="萌国ICP备案号"/>';
                                    ?>
                                </td>
                            </tr>
                            <tr valign="top" class="child_option dynamic_opts <?php echo $moe_beian_statu; ?>">
                                <th scope="row">— 异次元之旅</th>
                                <td>
                                    <?php
                                        $opt = 'site_moe_beian_travel';
                                        $status = check_status($opt);
                                        echo '<label for="'.$opt.'"><p class="description" id="">我们一起去萌站成员de星球旅行吧 ！</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'"'.$status.' /><img src="//moe.one/upload/attach/202307/89_8TEYVRKUCP79XHG.png" alt="moe_beian" style="height: 22px;vertical-align:middle;"></label>';
                                    ?>
                                </td>
                            </tr>
                    <tr valign="top">
                        <th scope="row">非AI撰写声明</th>
                        <td>
                            <?php
                                $opt = 'site_not_ai_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_monitor_switcher_label">非AI撰写声明（生成<a href="https://notbyai.fyi/" target="_blank"> not-by-ai </a>声明图标，状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/> <img src="'.$img_cdn.'/images/svg/not-by-ai.svg" alt="notbyai" style="height: 14px; vertical-align:middle;"></label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">站点施工警示</th>
                        <td>
                            <?php
                                $opt = 'site_construction_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_monitor_switcher_label">工程施工警示灯控制（开启显示🚨警示灯🚨动画，状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/><a href="javascript:void(0);" class="alarm_lamp" style="width:58px;height:12px;" title="站点正处施工中.."><span id="bar"></span><span id="spot"></span></a> <style>@keyframes alarmLamp_bar_before{0%{opacity:.15;}2%{opacity:1;}4%{opacity:.15;}6%{opacity:1;}8%{opacity:.15;}10%{opacity:1;}12%{opacity:.15;}14%{opacity:1;}16%{opacity:.15;}18%{opacity:1;}20%{opacity:.15;}22%{opacity:1;}24%{opacity:.15;}26%{opacity:1;}28%{opacity:.15;}50%{opacity:.15;}60%{opacity:1;}61%{opacity:.15;}62%{opacity:1;}70%{opacity:.15;}80%{opacity:1;}81%{opacity:.15;}82%{opacity:1;}90%{opacity:.15;}100%{opacity:1;}}@keyframes alarmLamp_bar_after{0%{opacity:.15;}28%{opacity:.15;}30%{opacity:1;}32%{opacity:.15;}34%{opacity:1;}36%{opacity:.15;}38%{opacity:1;}39%{opacity:.15;}40%{opacity:1;}42%{opacity:.15;}44%{opacity:1;}46%{opacity:.15;}48%{opacity:1;}50%{opacity:.15;}52%{opacity:1;}54%{opacity:.15;}56%{opacity:1;}58%{opacity:.15;}60%{opacity:.15;}70%{opacity:1;}71%{opacity:.15;}72%{opacity:1;}80%{opacity:.15;}90%{opacity:1;}91%{opacity:.15;}92%{opacity:1;}100%{opacity:.15;}}@keyframes alarmLamp_spotlight{0%{filter:blur(0px);}28%{filter:blur(0px);}50%{filter:blur(0px);}60%{background:red;filter:blur(15px);}62%{background:red;filter:blur(15px);}70%{background:blue;filter:blur(15px);}72%{background:blue;filter:blur(15px);}80%{background:red;filter:blur(15px);}82%{background:red;filter:blur(15px);}90%{background:blue;filter:blur(15px);}92%{background:blue;filter:blur(15px);}100%{filter:blur(0px);}}.alarm_lamp span#spot::before,.alarm_lamp span#spot::after{content:none;}.alarm_lamp span#spot,.alarm_lamp span#bar::before,.alarm_lamp span#bar::after{content:"";width:33%;height:78%;background:red;box-shadow:rgb(255 0 0 / 80%) 0 0 20px 0px;position:absolute;top:50%;left:50%;transform:translate(0%,-50%);-webkit-transform:translate(0%,-50%);animation-duration:3s;animation-delay:0s;animation-timing-function:step-end;animation-iteration-count:infinite;animation-direction:normal;}.alarm_lamp span#bar::before{left:0%;animation-name:alarmLamp_bar_before;-webkit-animation-name:alarmLamp_bar_before;}.alarm_lamp span#bar::after{left:auto;right:0%;background:blue;box-shadow:rgb(0 0 255 / 80%) 0 0 20px 0px;animation-name:alarmLamp_bar_after;-webkit-animation-name:alarmLamp_bar_after;}.alarm_lamp{display:inline-block;padding:0 2px!important;box-sizing:border-box;position:relative;vertical-align:middle;border:1px solid transparent;}.alarm_lamp span{height:100%;display:block;position:inherit;}.alarm_lamp span#bar{width:100%;}.alarm_lamp span#spot{max-width:32%;background:white;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);box-shadow:rgb(255 255 255 / 100%) 0 0 20px 0px;animation-name:alarmLamp_spotlight;-webkit-animation-name:alarmLamp_spotlight;}</style> </label>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">在线沟通插件</th>
                        <td>
                            <?php
                                $opt = 'site_chat_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_chat_switcher_label">在线沟通控制（生成 script 链接和底部图标，状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/> <span style="color:dodgerblue;" class="btn"> TIDIO </span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_chat_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_chat_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 沟通链接</th>
                                <td>
                                    <?php
                                        $opt = 'site_chat';
                                        echo '<p class="description" id="">沟通直链（单页</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="沟通（单页）直链" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">站点统计插件</th>
                        <td>
                            <?php
                                $opt = 'site_monitor_switcher';
                                $status = check_status($opt);
                                echo '<label for="'.$opt.'"><p class="description" id="site_monitor_switcher_label">站点统计控制（生成 script 链接，状态：'.$status.'</p><input type="checkbox" name="'.$opt.'" id="'.$opt.'" '.$status.'/> <span style="color:orangered;" class="btn">U.MENG</span></label>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        // if(get_option('site_monitor_switcher')){
                    ?>
                            <tr valign="top" class="child_option dynamic_opts <?php echo get_option('site_monitor_switcher') ? 'dynamic_optshow' : false; ?>">
                                <th scope="row">— 统计链接</th>
                                <td>
                                    <?php
                                        $opt = 'site_monitor';
                                        echo '<p class="description" id="">CNZZ 统计链接</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" placeholder="CNZZ 统计链接" value="' . get_option($opt) . '"/>';
                                    ?>
                                </td>
                            </tr>
                    <?php
                        // }
                    ?>
                    <tr valign="top">
                        <th scope="row">底部文本栏目</th>
                        <td>
                            <?php
                                $opt = 'site_support';
                                $value = get_option($opt);  //默认填充数据
                                $preset = 'Art Design | Coding | Documents | Social Media | Tech Support';  //默认填充数据
                                if(!$value) update_option($opt, $preset);else $preset=$value;
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="'.$preset.'">';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Email</th>
                        <td>
                            <?php
                                $opt = 'site_contact_email';
                                $value = get_option($opt);
                                $preset = get_bloginfo('admin_email');  //默认填充数据
                                if(!$value) update_option($opt, $preset);else $preset=$value;
                                echo '<p class="description" id="site_contact_email_label">底部（邮箱）联系方式（默认管理员邮箱</p><input type="text" name="'.$opt.'" id="'.$opt.'" class="middle-text" value="' . $preset . '"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Wechat</th>
                        <td>
                            <?php
                                $opt = 'site_contact_wechat';
                                $value = get_option( $opt, '' );
                                $preset = get_option('site_avatar');
                                $value ? $preset=$value : update_option($opt, $preset);  //auto update option to default if avatar unset
                                echo '<p class="description" id="site_contact_wechat_label">底部（微信）联系方式（图片链接</p><label for="'.$opt.'" class="upload"><a class="dynamic_dom" data-dom="img" data-src="' . $preset . '" title="加载预览">加载图片</a></label><input type="text" name="'.$opt.'" placeholder="微信二维码" class="regular-text upload_field" value="' . $value . '"/><input id="'.$opt.'" type="button" class="button-primary upload_button" data-type=1 value="选择图片" />';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Github</th>
                        <td>
                            <?php
                                $opt = 'site_contact_github';
                                $value = get_option($opt);
                                $holder = 'https://github.com/2Broear/';
                                if(!$value) update_option($opt, $holder);else $holder=$value;  //auto update option
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $holder . '" placeholder="底部（github）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bilibili</th>
                        <td>
                            <?php
                                $opt = 'site_contact_bilibili';
                                $value = get_option($opt);
                                $holder = 'https://space.bilibili.com/7971779';
                                if(!$value) update_option($value, $holder);else $holder=$value;
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $holder . '" placeholder="底部（bilibili）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Netease</th>
                        <td>
                            <?php
                                $opt = 'site_contact_music';
                                $value = get_option($opt);
                                $holder = 'https://music.163.com/#/user/home?id=77750916';
                                if(!$value) update_option($opt, $holder);else $holder=$value;
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $value . '" placeholder="底部（网易云）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Steam</th>
                        <td>
                            <?php
                                $opt = 'site_contact_steam';
                                $value = get_option($opt);
                                $holder = 'https://steamcommunity.com/profiles/76561198145631868/';
                                if(!$value) update_option($opt, $holder);else $value=$holder;
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . $value . '" placeholder="底部（steam）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Weibo</th>
                        <td>
                            <?php
                                $opt = 'site_contact_weibo';
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . get_option($opt) . '" placeholder="底部（微博）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Twitter</th>
                        <td>
                            <?php
                                $opt = 'site_contact_twitter';
                                echo '<input type="text" name="'.$opt.'" id="'.$opt.'" class="regular-text" value="' . get_option($opt) . '" placeholder="底部（twitter）联系方式"/>';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
    // execute after wrap dom
    auto_panel_theme();
    }
?>