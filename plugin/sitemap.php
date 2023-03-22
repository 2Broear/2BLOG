<?php
    // if(get_option('site_map_switcher')){
        // equire_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');  // DO NOT Load WordPress Core!!! Caused E_COMPILE_ERROR
        $sitemap_opts = get_option('site_map_includes','');
        $async_array = explode(',', $sitemap_opts);
        $site_url = get_site_url();//get_option("home"); bloginfo('url')
        // https://www.xingkongweb.com/3320.html
        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">
            <url>
                <loc><?php echo $site_url; ?></loc>
                <priority>1</priority>
            </url>
<?php
        if(in_array('category', $async_array)){ // count($cat_match[0])>0 //strpos($sitemap_opts, 'category')!==false
            // 显示分类
            $terms = get_terms(array('hide_empty'=>0, 'order'=>'ASC' ,'orderby'=>'order_clause', 
                'meta_query'=>array(
                    'order_clause' => array(
                        'key' => 'seo_order',
                        'type' => 'NUMERIC'
                    )
                )
            ));
            if(count($terms)>0){
                foreach($terms as $term){
                    $term_link = get_term_link($term, $term->slug);
                    if($term_link!=$site_url){
?>
                        <url>
                            <loc><?php echo $term_link; ?></loc>
                            <priority>0.8</priority>
                        </url>
<?php 
                    }
                }
            }
        }
        if(in_array('page', $async_array)){ // count($page_match[0])>0 //strpos($sitemap_opts, 'page')!==false
            // 显示页面
            $mypages = get_pages();
            if(count($mypages)>0){
                foreach($mypages as $page){
                    $page_link = get_page_link($page->ID);
                    if($page_link!=$site_url){
?>
                        <url>
                            <loc><?php echo $page_link; ?></loc>
                            <lastmod><?php echo str_replace(" ","T",get_page($page->ID)->post_modified); ?>+00:00</lastmod>
                            <priority>0.8</priority>
                        </url>
<?php 
                    }
                }
            }
        }
        if(in_array('post', $async_array)){ // count($post_match[0])>0 //strpos($sitemap_opts, 'post')!==false
            // 显示文章$args
            query_posts(array(
                'showposts' => 1000
            ));
            while(have_posts()) : the_post();
?>
                <url>
                    <loc><?php the_permalink(); ?></loc>
                    <lastmod><?php the_time('c') ?></lastmod>
                    <priority>0.7</priority>
                </url>
<?php 
            endwhile;
            wp_reset_query();
        }
        if(in_array('tag', $async_array)){ // count($tag_match[0])>0 //strpos($sitemap_opts, 'tag')!==false
            // 显示便签
            $tags = get_terms("post_tag");
            foreach($tags as $key => $tag){
                $link = get_term_link( intval($tag->term_id), "post_tag" );
                if(is_wp_error($link)) return false;
                $tags[$key]->link = $link;
?>
                <url>
                    <loc><?php echo $link ?></loc>
                    <priority>0.4</priority>
                </url>
<?php 
            }
        }
?>
    </urlset>
<?php
        $content = ob_get_contents();
        ob_end_clean();
        file_put_contents(ABSPATH . '/sitemap.xml', $content);
    // file_put_contents(TEMPLATEPATH . '/sitemap.xml', $content);
    // }else{
    //     echo '<h1>Sitemap Disabled in 2blog-settings!</h1>';
    // }
?>