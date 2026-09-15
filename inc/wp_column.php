<?php
    if (get_option('site_chatgpt_switcher') && get_option('site_chatgpt_ai_summary')) {
        /**
        * 在文章列表添加 "AI摘要" 列
        */
        add_filter('manage_posts_columns', function($columns) {
            $columns['gpt_summary'] = 'AI 摘要';
            return $columns;
        }, 10, 2);
        
        /**
        * 输出列内容
        */
        add_action('manage_posts_custom_column', function($column, $post_id) {
            if ($column !== 'gpt_summary') return;
        
            $post = get_post($post_id);
            if (!in_chatgpt_cat($post)) {
                echo '<span style="color:#999;">—</span>';
                return;
            }
        
            $cache_key = 'gpt_summary_' . $post_id;
            $cached    = get_option($cache_key, null);
            $summary   = '';
        
            if ($cached !== null) {
                $data = json_decode($cached, true);
                $summary = gpt_extract_result_text($data);
            }
        
            $has_cache = !empty($summary);
        
            echo '<div class="gpt-summary-cell" data-post-id="' . $post_id . '" style="max-width:420px;">';
        
            // 摘要内容展示
            if ($has_cache) {
                echo '<div class="gpt-summary-text" title="' . esc_attr($summary) . '" style="font-size:12px;margin-bottom:4px;max-height: 5em;overflow: hidden;">' . esc_html($summary) . '</div>';
            } else {
                // echo '<div class="gpt-summary-text" style="color:#999;">暂无摘要</div>';
            }
        
            // 状态 + 按钮
            echo '<a href="javascript:;" class="gpt-regen button button-small" data-post-id="' . $post_id . '">'
                . ($has_cache ? '重新生成' : '生成摘要')
                . '</a>';
            echo '<span class="gpt-spinner spinner" style="float:none;margin-left:4px;"></span>';
            echo '</div>';
        }, 10, 2);
        /**
         * 在 edit.php 加载列交互脚本
         */
        add_action('admin_footer-edit.php', function() {
            $screen = get_current_screen();
            if (!$screen || $screen->post_type !== 'post') return;
    ?>
            <script>
            jQuery(function($) {
                $('<style>.gpt-summary-cell .spinner{visibility:hidden;}.gpt-summary-cell.loading .spinner{visibility:visible;}</style>').appendTo('head');
        
                $(document).on('click', '.gpt-regen', function(e) {
                    if(!confirm('确认生成AI摘要吗？')) return;
                    e.preventDefault();
                    var $btn = $(this);
                    var postId = $btn.data('post-id');
                    var $cell = $btn.closest('.gpt-summary-cell');
                    var $text = $cell.find('.gpt-summary-text');
        
                    if ($btn.hasClass('disabled')) return;
                    $btn.addClass('disabled').text('生成中…');
                    $cell.addClass('loading');
        
                    $.ajax({
                        url: '/wp-json/gpt-summary/v1/summary/' + postId + '?force=1',
                        method: 'GET',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                        }
                    }).done(function(res) {
                        if (res && res.summary) {
                            $text.text(res.summary).css('color', '#444').attr('title', res.summary);
                            $btn.text('重新生成').removeClass('disabled');
                        } else {
                            $text.text('生成失败').css('color', '#dc3232');
                            $btn.text('重试').removeClass('disabled');
                        }
                    }).fail(function(xhr) {
                        var msg = '请求失败';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        $text.text(msg).css('color', '#dc3232');
                        $btn.text('重试').removeClass('disabled');
                    }).always(function() {
                        $cell.removeClass('loading');
                    });
                });
            });
            </script>
    <?php
        });
    }

    /*
     https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     load script/style to set exists column_value in column_input
    */
    if ( ! function_exists('wp_my_admin_enqueue_scripts') ){
        function wp_my_admin_enqueue_scripts( $hook ) {
            if ( 'edit.php' === $hook) {
     	        wp_enqueue_script( 'my_custom_script', get_stylesheet_directory_uri() . '/inc/themes/custom_column.js', false, null, true );
            }elseif('edit-comments.php'===$hook){
                wp_enqueue_style( 'custom_wp_admin_css', get_template_directory_uri() . '/inc/themes/custom_style.css', false, '1.0.0' );
                // wp_enqueue_style( 'custom_wp_admin_css' );
            }
        }
    }
    add_action( 'admin_enqueue_scripts', 'wp_my_admin_enqueue_scripts' );
    
    
    // 配合 custom_column.js 获取已显示列表数值填充到快速编辑栏目预览
    parse_str($_SERVER['QUERY_STRING'], $cur_edit_queries);
    $acg_cat = get_cat_by_template('acg','term_id');
    $cur_cat = array_key_exists('cat',$cur_edit_queries) ? $cur_edit_queries['cat'] : false;
    $edit_acg_posts = $cur_cat==$acg_cat || cat_is_ancestor_of($acg_cat, $cur_cat);
    // $inform_post = array_key_exists('post_type', $cur_edit_queries) ? $cur_edit_queries['post_type'] : false;
    // display custom_column for custom_column.js preset input-value
    add_filter('manage_posts_columns', 'wpse_3531_add_seo_columns', 10, 2);
    function wpse_3531_add_seo_columns($posts_columns, $post_type){
        if($post_type!='inform') $posts_columns['post_orderby'] = '排序';
        // $posts_columns['post_rating'] = '评分';
        global $edit_acg_posts;
        if($edit_acg_posts){
            $posts_columns['post_rcmd'] = '推荐';
            $posts_columns['post_rating'] = '评分';
        }
        return $posts_columns;
    }
    // preview custom_column-value
    add_action('manage_posts_custom_column', 'wpse_3531_display_seo_columns', 10, 2);
    function wpse_3531_display_seo_columns($column_name, $post_id) {
        $post_meta = get_post_meta($post_id);
        if ('post_orderby' == $column_name) {
            $post_orderby = get_post_meta($post_id, 'post_orderby', true);
            echo $post_meta ? ($post_orderby<1 || $post_orderby>1 ? "<b style='color:red';>$post_orderby</b>" : $post_orderby) : '0（edit required）'; //（置顶）
        }
        global $edit_acg_posts;
        if ($edit_acg_posts) {
            if('post_rcmd' == $column_name){
                $check = '';
                if($post_meta) $check = get_post_meta($post_id, 'post_rcmd', true) ? 'checked' : '';
                echo $check;
            }
            if('post_rating' == $column_name){
                echo $post_meta ? get_post_meta($post_id, 'post_rating', true) : '';
            }
        }
    }
    /**
     * 3. 让“排序”列可点击排序
     */
    add_filter( 'manage_edit-post_sortable_columns', 'wpse_3531_sortable_columns' );
    function wpse_3531_sortable_columns( $columns ) {
        $columns['post_orderby'] = 'post_orderby';
        return $columns;
    }
    
    /**
     * 4. 处理排序逻辑：把 orderby=post_orderby 转成 meta 值排序
     */
    add_action( 'pre_get_posts', 'wpse_3531_orderby_meta' );
    function wpse_3531_orderby_meta( $query ) {
        // 只在后台文章列表主查询生效
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }
    
        // 仅在 post 列表页
        global $pagenow;
        if ( 'edit.php' !== $pagenow ) {
            return;
        }
    
        // 如果用户点击了“排序”列
        if ( 'post_orderby' === $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', 'post_orderby' );
            $query->set( 'orderby', 'meta_value_num' ); // 按数值排序
            // order 参数由 WordPress 自动从 URL 的 order=asc/desc 读取，无需手动设置
            return;
        }
    
        // 默认情况：让没有 post_orderby 的文章也显示出来（避免 meta_key 过滤掉它们）
        // 如果你希望默认列表也按 post_orderby 排序，可以在这里加默认排序
        // 否则保持默认（日期排序），不加任何设置即可
    }

    // Add our text to the quick edit box
    add_action('quick_edit_custom_box', 'on_quick_edit_custom_box', 10, 2);
    function on_quick_edit_custom_box($column_name, $post_type){
        global $edit_acg_posts;
        // $status = get_post_meta($post->ID)['post_rcmd'][0] ? "checked" : "check";
        if ('post_orderby' == $column_name) {
    ?>
            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col">
                    <label>
    					<span class="title">排序（列表）</span>
    				    <input type="number" name="post_orderby" class="small-text" min="">
    				</label>
                </div>
            </fieldset>
    <?php
        }
        if ('post_rcmd' == $column_name && $edit_acg_posts) {
    ?>
            <fieldset class="inline-edit-col-center">
                <div class="inline-edit-col">
                    <label>
    					<span class="title">推荐（列表）</span>
    				    <input type="checkbox" name="post_rcmd">
    				</label>
                </div>
            </fieldset>
    <?php
        }
        if ('post_rating' == $column_name) {
    ?>
            <fieldset class="inline-edit-col-right">
                <div class="inline-edit-col">
                    <label>
    					<span class="title">评分（列表）</span>
    				    <input type="number" name="post_rating" class="small-text" min="0" max="10">
    				</label>
                </div>
            </fieldset>
    <?php
        }
    }
    
    // add to BULK-EDIT
    add_action('bulk_edit_custom_box', 'on_bulk_edit_custom_box', 10, 2);
    function on_bulk_edit_custom_box($column_name, $post_type){
        if ('post_orderby' == $column_name) {
    ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
    				<span class="title">排序</span>
    			    <input type="number" name="post_orderby" class="small-text" value="1" min="0">
    			</label>
            </div>
        </fieldset>
    <?php
        }
    }
    // save bulk-edit 
    add_action( 'wp_ajax_save_bulk_edit_book', 'save_bulk_edit_book' );
    function save_bulk_edit_book() {
        // TODO perform nonce checking
        // get our variables
        $post_ids = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
        $post_orderby  = ( ! empty( $_POST[ 'post_orderby' ] ) ) ? $_POST[ 'post_orderby' ] : 1;
        // $post_rcmd  = ( ! empty( $_POST[ 'post_rcmd' ] ) ) ? 'checked' : 'check';
        // $post_rating  = ( ! empty( $_POST[ 'post_rating' ] ) ) ? $_POST[ 'post_rating' ] : 1;
        // if everything is in order
        if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
            foreach( $post_ids as $post_id ) {
                update_post_meta( $post_id, 'post_orderby', $post_orderby );
                // update_post_meta( $post_id, 'post_rcmd', $post_rcmd );
                // update_post_meta( $post_id, 'post_rating', $post_rating );
            }
        }
        die();
    }
    
    
    //https://wordpress.stackexchange.com/questions/8736/add-custom-field-to-category
    /* ------------------------------------------------------------------------ *
     * https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
     * https://wordpress.stackexchange.com/questions/3531/how-can-i-add-columns-to-the-post-edit-listing-to-show-my-custom-post-data
     * Custom Post MetaBox (float dragble)
     * https://tryvary.com/wordpress-add-meta-box-to-custom-post-type-and-page/
     * ------------------------------------------------------------------------ */
     
    //Register POST Meta box
    add_action('add_meta_boxes',function (){
        add_meta_box(
             'post-field',
             '附加选项',
             'post_custom_fields_html',
             ['post'],
             'side'
        );
    });
    function postmeta_json($posts=false){
        global $post, $pagenow; //!!!in_category lose efficacy if $post unreachable!!!
        $posts = $posts ? $posts : $post; // $pid = $pid ? $pid : $post->ID;
        $acg_slug =  get_cat_by_template('acg','slug');
        $news_slug =  get_cat_by_template('news','slug');
        $note_slug =  get_cat_by_template('notes','slug');
        $creating_post = in_array($pagenow, array('post-new.php'));
        $pid = $posts->ID;
        $preset_arr = array(
            array('title'=>'额外内容', 'for'=>'post_feeling', 'type'=>'text', 'method'=>'textarea', 'options'=>false),
            array('title'=>'文章排序', 'for'=>'post_orderby', 'type'=>'number', 'method'=>false, 'options'=>false),
        );
        if(in_category($acg_slug, $posts) || $creating_post) {
            array_push($preset_arr, array('title'=>'推荐内容', 'for'=>'post_rcmd', 'type'=>'checkbox', 'method'=>'checkbox', 'options'=>false), array('title'=>'评测得分', 'for'=>'post_rating', 'type'=>'number', 'method'=>false, 'options'=>false));
        };
        if(in_category($news_slug, $posts)||in_category($note_slug, $posts) || $creating_post){
            array_push($preset_arr, array('title'=>'文章版权', 'for'=>'post_rights', 'type'=>'', 'method'=>'select', 'options'=>["原创","转载","其他"]));
            $post_meta = get_post_meta($pid);
            $post_rights = $post_meta&&array_key_exists('post_rights',$post_meta) ? $post_meta['post_rights'][0] : false;
            if($post_rights&&$post_rights!='原创') array_push($preset_arr, array('title'=>'文章来源', 'for'=>'post_source', 'type'=>'text', 'method'=>false, 'options'=>false));
        };
        return $preset_arr;
    }
    function postMetas($meta,$json){
        $for = $json['for'];
        $title = $json['title'];
        $type = $json['type'];
        // $select = $json['select'];
        // $textarea = $json['textarea'];
        $method = $json['method'];
        // default value
        $value = "";
        // if(isset($meta[$for])) $value=$meta[$for][0];elseif($for=='post_orderby'||$for=='post_rating') $value=1;else $value="";
        if(isset($meta[$for])){
            $value = $meta[$for][0];
        }elseif($for=='post_orderby'){
            $value = 1;
            global $post;
            update_post_meta($post->ID, $for, $value);
        }
        switch ($method) {
            case 'textarea':
                echo '<tr><th><label for="'.$for.'">'.$title.'</label></th><td><textarea name="'.$for.'" id="'.$for.'" placeholder="文章副标题、文章感想、文章额外内容等信息.." style="width:88%;height:88px">'.$value.'</textarea></td></tr>';
                break;
            case 'select':
                $selects = $json['options'];
                //output each selects
                echo '<tr><th><label for="'.$for.'">'.$title.'</label></th><td><select name="'.$for.'" id="'.$for.'">';
                $selects_count = count($selects);
                for($i=0;$i<$selects_count;$i++){
                    $each = $selects[$i];
                    $selected = $value==$each ? ' selected="selected"' : '';
                    echo '<option value="'.$each.'"'.$selected.'>'.$each.'</option>';
                };
                echo '</select></td></tr>';
                break;
            case 'checkbox':
                $status = $value ? "checked" : "check";
                echo '<tr><th><label for="'.$for.'">'.$title.'</label></th><td><input type="checkbox" name="'.$for.'" id="'.$for.'" '.$status.'></td></tr>';
                break;
            default:
                $min = 0;
                $max = 99999;
                $class = 'regular';
                if($type=='number'){
                    $class = 'small';
                    if($for=='post_rating'){
                        $min = 1;
                        $max = 10;
                    }
                }
                echo '<tr><th><label for="'.$for.'">'.$title.'</label></th><td><input type="'.$type.'" name="'.$for.'" id="'.$for.'" class="'.$class.'-text" min="'.$min.'" max="'.$max.'" value="'.$value.'" placeholder="'.$title.'"></td></tr>';
                break;
        }
    };
    //Meta callback function
    function post_custom_fields_html($post){
?>
        <table class="form-table">
<?php 
            $cs_meta_val = get_post_meta($post->ID);
            $meta_json = postmeta_json($post); //inside loop
            foreach ($meta_json as $arr){
                postMetas($cs_meta_val, $arr); //echo postMetas($cs_meta_val, $arr);
            }
?>
        </table>
<?php     
    }
    
    //save meta value with save post hook
    add_action('save_post', 'post_save_custom_field_value');
    function post_save_custom_field_value($post_id){
        $post = get_post($post_id);
        $meta_json = postmeta_json($post); //outside loop
        // print_r($meta_json);
        foreach ($meta_json as $arr){
            // update_post_meta($post_id, 'post_rcmd', true);
            $post_for = $arr['for'];
            // https://wordpress.stackexchange.com/questions/41517/custom-post-type-how-to-get-checkbox-to-update-meta-field-to-null
            if($arr['type']=='checkbox'){
                update_post_meta($post_id, $post_for, isset($_POST[$post_for]));
            }else{
                if(isset($_POST[$post_for])) update_post_meta($post_id, $post_for, sanitize_text_field($_POST[$post_for]));
            }
        }
    };
    // post_save_custom_field_value(4914);  //test meta_key array
    // Just set the last parameter ($prev) the update_post_meta to false, this enable you to insert new values to the meta_key. You can also check if the value is not already in the array before updating
    // https://wordpress.stackexchange.com/questions/305205/updating-post-meta-for-checkbox
    
    
    function pagemeta_json(){
        return array(
            // array('title'=>'展示元数据分类', 'input'=>'page_metanav', 'type'=>'', 'option'=>true),
            array('title'=>'展示元数据分类', 'for'=>'page_metanav', 'type'=>'', 'method'=>'select', 'options'=>["disabled","text","image"])
        );
    }
    //Register PAGE Meta box
    add_action('add_meta_boxes',function (){
        add_meta_box(
             'page-field',
             '页面附加选项',
             'page_custom_fields_html',
             ['page'],
             'side'
        );
    });
    //Meta callback function
    function page_custom_fields_html($post){
?>
        <table class="form-table">
<?php 
            $cs_meta_val = get_post_meta($post->ID);
            $meta_json = pagemeta_json();
            foreach ($meta_json as $arr){
                postMetas($cs_meta_val, $arr); //echo postMetas($cs_meta_val, $arr);
            }
?>
        </table>
<?php     
    }
    //save meta value with save post hook
    add_action('save_post', 'page_save_custom_field_value');
    function page_save_custom_field_value($post_id){
        $post = get_post($post_id);
        $meta_json = pagemeta_json($post); //outside loop
        // print_r($meta_json);
        foreach ($meta_json as $arr){
            $post_for = $arr['for'];
            if($arr['method']=='checkbox'){
                update_post_meta($post_id, $post_for, isset($_POST[$post_for]));
            }else{
                if(isset($_POST[$post_for])) update_post_meta($post_id, $post_for, sanitize_text_field($_POST[$post_for]));
            }
        }
    };
    // page_save_custom_field_value(19);
?>