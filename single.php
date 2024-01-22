<?php
    function get_between($begin, $end, $str){
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }
    $single_dir = '/inc/templates/single/';
    $default_temp = "notes";
    function recursive_single_includes($cats){
        if(empty($cats)) return;
        global $post, $single_dir, $default_temp;
        foreach($cats as $cat){
            $cid = $cat->term_id;
            $cname = get_between('-', '.php', get_term_meta($cid, 'seo_template', true));
            if(in_category($cat->slug, $post) && file_exists(TEMPLATEPATH . $single_dir.'single-'.$cname.'.php')){
                // return $cname; // get_template_part($single_dir.'single-'.$cname);
                $default_temp = $cname;
            }else{
                // return "notes";
            }
            $cats = get_categories(meta_query_categories($cid, 'ASC', 'seo_order'));  // 二级分类slug文章模板
            if(!empty($cats)) recursive_single_includes($cats);
        }
    }
    $cats = get_categories(meta_query_categories(0, 'ASC', 'seo_order'));  // 一级分类slug文章模板
    recursive_single_includes($cats); //$temp = recursive_single_includes($cats);
    get_template_part($single_dir.'single-'.$default_temp); //get_template_part($single_dir.'single-'.$temp);
    unset($single_dir, $default_temp);
?>
