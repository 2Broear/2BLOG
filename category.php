<?php
    $cat = $cat ? $cat : get_page_cat_id(current_slug(false));  // if is_page() then rewrite cat to cid
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));
    if(!empty($cats)){
        foreach($cats as $the_cat){
            $the_cat_id = $the_cat->term_id;
            $the_cat_temp = TEMPLATEPATH . '/category-'.$the_cat->slug.'.php';  // 子分类继承父分类模板
            if(cat_is_ancestor_of($the_cat_id, $cat) && file_exists($the_cat_temp)) include_once($the_cat_temp);//else include_once(TEMPLATEPATH . '/category-default.php');  // 二级..
            $catss = get_categories(meta_query_categories($the_cat_id, 'ASC', 'seo_order'));
            if(!empty($catss)){
                foreach($catss as $the_cats){
                    $the_cats_id = $the_cats->term_id;
                    $the_cats_temp = TEMPLATEPATH . '/category-'.$the_cats->slug.'.php';
                    if(cat_is_ancestor_of($the_cats_id, $cat) && file_exists($the_cats_temp)) include_once($the_cats_temp);//else include_once(TEMPLATEPATH . '/category-default.php');
                }
            }
        }
    }
?>
