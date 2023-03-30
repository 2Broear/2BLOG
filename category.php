<?php
    // global $cat;
    $cat = $cat ? $cat : get_page_cat_id(current_slug(false));  // if is_page() then rewrite cat to cid
    $cur_template = get_term_meta($cat, 'seo_template', true);
    !$cur_template || $cur_template=='default' ? include_once(TEMPLATEPATH . '/index.php') : include_once(TEMPLATEPATH . '/'.$cur_template);//use category as folder
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));
    if(!empty($cats)){
        foreach($cats as $the_cat){
            $the_cat_id = $the_cat->term_id;
            $the_cat_temp = TEMPLATEPATH . '/'.get_term_meta($the_cat_id, 'seo_template', true);  // 子分类继承父分类模板（获取分类绑定模板）
            if(cat_is_ancestor_of($the_cat_id, $cat) && file_exists($the_cat_temp)) include_once($the_cat_temp);//else include_once(TEMPLATEPATH . '/category-default.php');  // 二级..
            $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
            if(!empty($catss)){
                foreach($catss as $the_cats){
                    $the_cats_id = $the_cats->term_id;
                    $the_cats_temp = TEMPLATEPATH . '/'.get_term_meta($the_cats_id, 'seo_template', true);
                    if(cat_is_ancestor_of($the_cats_id, $cat) && file_exists($the_cats_temp)) include_once($the_cats_temp);
                    $catsss = get_categories(meta_query_categories($the_cats_id, 'ASC', 'seo_order'));
                    if(!empty($catsss)){
                        foreach($catsss as $the_catss){
                            $the_catss_id = $the_catss->term_id;
                            $the_catss_temp = TEMPLATEPATH . '/'.get_term_meta($the_catss_id, 'seo_template', true);
                            if(cat_is_ancestor_of($the_catss_id, $cat) && file_exists($the_catss_temp)) include_once($the_catss_temp);
                        }
                    }
                }
            }
        }
    }
?>
