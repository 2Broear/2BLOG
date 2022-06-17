<?php
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));  // 一级分类slug文章模板
    if(!empty($cats)){
        foreach($cats as $the_cat){
            $the_cat_slug = $the_cat->slug;
            if(in_category($the_cat_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_cat_slug.'.php')) $the_cat_flag = $the_cat_slug;
            $catss = get_categories(meta_query_categories($the_cat->term_id, 'ASC', 'seo_order'));  // 二级分类slug文章模板
            if(!empty($catss)){
                foreach($catss as $the_cats){
                    $the_cats_slug = $the_cats->slug;
                    if(in_category($the_cats_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_cats_slug.'.php')) $the_cats_flag = $the_cats_slug;
                }
            }
        }
    }
    if($the_cat_flag){
        get_template_part('single-'.$the_cat_flag);
    }elseif($the_cats_flag){
        get_template_part('single-'.$the_cats_flag);
    }else{
        get_template_part('single-notes');  // default single-default.php
    }
?>
