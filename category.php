<?php
    $cat = $cat ? $cat : get_page_cat_id(current_slug(false));  // rewrite cat to cid if is_page()
    $cat_template = get_term_meta($cat, 'seo_template', true);
    $TEMPLATEPATH = get_template_directory();
    if($cat_template){ // load category templates from specificed folder
        $cat_template==='default' ? include($TEMPLATEPATH . '/index.php') : include($TEMPLATEPATH . '/'.$cat_template);
    }else{ // load category templates via recursive
        function recursive_category_inheritance($cats){
            if(!empty($cats)){
                global $cat, $TEMPLATEPATH;
                foreach($cats as $the_cat){
                    $the_cat_id = $the_cat->term_id;
                    $the_cat_template = $TEMPLATEPATH . '/inc/templates/pages/category/' . get_term_meta($the_cat_id, 'seo_template', true);  // 子分类继承父分类模板（获取分类绑定模板）
                    if(file_exists($the_cat_template) && cat_is_ancestor_of($the_cat_id, $cat)){
                        include_once($the_cat_template);
                        return;
                    }
                    // recursive loops
                    $cats = get_categories(meta_query_categories($the_cat_id));
                    if(!empty($cats)) recursive_category_inheritance($cats);
                }
            }
        }
        $cats = get_categories(meta_query_categories());
        recursive_category_inheritance($cats);
    }
    unset($cat_template, $TEMPLATEPATH);
?>
