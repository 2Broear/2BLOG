<?php
    function get_between($begin, $end, $str){
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));  // 一级分类slug文章模板
    if(!empty($cats)){
        foreach($cats as $the_cat){
            $the_cat_slug = $the_cat->slug;
            $the_cat_temp = get_term_meta($the_cat->term_id, 'seo_template', true);
            $the_cat_temp_name = get_between('-','.php',$the_cat_temp);
            if(in_category($the_cat_slug) && file_exists(TEMPLATEPATH . '/templates/single-'.$the_cat_temp_name.'.php')) $the_cat_flag = $the_cat_temp_name;
            // $catss = get_categories(meta_query_categories($the_cat->term_id, 'ASC', 'seo_order'));  // 二级分类slug文章模板
            // if(!empty($catss)){
            //     foreach($catss as $the_cats){
            //         $the_cats_slug = $the_cats->slug;
            //         $the_cats_temp = get_term_meta($the_cats->term_id, 'seo_template', true);
            //         $the_cats_temp_name = get_between('-','.php',$the_cats_temp);
            //         if(in_category($the_cats_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_cats_temp_name.'.php')) $the_cats_flag = $the_cats_temp_name;
            //         $catsss = get_categories(meta_query_categories($the_cats->term_id, 'ASC', 'seo_order'));  // 二级分类slug文章模板
            //         if(!empty($catsss)){
            //             foreach($catsss as $the_catss){
            //                 $the_catss_slug = $the_catss->slug;
            //                 $the_catss_temp = get_term_meta($the_catss->term_id, 'seo_template', true);
            //                 $the_catss_temp_name = get_between('-','.php',$the_catss_temp);
            //                 if(in_category($the_catss_slug) && file_exists(TEMPLATEPATH . '/single-'.$the_catss_temp_name.'.php')) $the_catss_flag = $the_catss_temp_name;
            //             }
            //         }
            //     }
            // }
        }
    }
    if($the_cat_flag){
        get_template_part('/templates/single-'.$the_cat_flag);
    }
    // elseif($the_cats_flag){
    //     get_template_part('single-'.$the_cats_flag);
    // }elseif($the_catss_flag){
    //     get_template_part('single-'.$the_catss_flag);
    // }
    else{
        get_template_part('/templates/single-notes');  // default single-template
    }
?>
