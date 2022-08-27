<?php
    function get_between($begin, $end, $str){
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));  // 一级分类slug文章模板
    $the_cat_flag = null;  // php 8
    $the_cats_flag = null;  // php 8
    if(!empty($cats)){
        foreach($cats as $the_cat){
            $the_cat_slug = $the_cat->slug;
            $the_cat_temp = get_term_meta($the_cat->term_id, 'seo_template', true);
            $the_cat_temp_name = get_between('-','.php',$the_cat_temp);
            if(in_category($the_cat_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_cat_temp_name.'.php')) $the_cat_flag = $the_cat_temp_name;
            $catss = get_categories(meta_query_categories($the_cat->term_id, 'ASC', 'seo_order'));  // 二级分类slug文章模板
            if(!empty($catss)){
                foreach($catss as $the_cats){
                    $the_cats_slug = $the_cats->slug;
                    $the_cats_temp = get_term_meta($the_cats->term_id, 'seo_template', true);
                    $the_cats_temp_name = get_between('-','.php',$the_cats_temp);
                    if(in_category($the_cats_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_cats_temp_name.'.php')) $the_cats_flag = $the_cats_temp_name;
                }
            }
        }
    }
    if($the_cat_flag){
        get_template_part('single-'.$the_cat_flag);
    }elseif($the_cats_flag){
        get_template_part('single-'.$the_cats_flag);
    }else{
        get_template_part('single-notes');  // single-default.php
    }
?>
