<?php
    // global $cat;
    $cat = $cat ? $cat : get_page_cat_id(current_slug(false));  // if is_page() then rewrite cat to cid
    $cur_template = get_term_meta($cat, 'seo_template', true);
    !$cur_template || $cur_template=='default' ? include_once(TEMPLATEPATH . '/index.php') : include_once(TEMPLATEPATH . '/'.$cur_template);//use category as folder
    function recursive_category($cats){
        if(!empty($cats)){
            global $cat;
            foreach($cats as $the_cat){
                $the_cat_id = $the_cat->term_id;
                $the_cat_temp = TEMPLATEPATH . '/'.get_term_meta($the_cat_id, 'seo_template', true);  // 子分类继承父分类模板（获取分类绑定模板）
                if(cat_is_ancestor_of($the_cat_id, $cat) && file_exists($the_cat_temp)) include_once($the_cat_temp);
                // recursive loops
                $cats = get_categories(meta_query_categories($the_cat_id));
                if(!empty($cats)) recursive_category($cats);
            }
        }
    }
    $cats = get_categories(meta_query_categories(0));
    recursive_category($cats);
?>
