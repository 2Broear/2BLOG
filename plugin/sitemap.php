<?php
$sitemap_opts = get_option('site_map_includes', '');
$async_array  = explode(',', $sitemap_opts);
$site_url     = get_site_url();

ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">
    <url>
        <loc><?php echo esc_url($site_url); ?></loc>
        <priority>1</priority>
    </url>

<?php
// ---------- 分类 ----------
if (in_array('category', $async_array)) {
    $terms = get_terms(array(
        'hide_empty' => 0,
        'order'      => 'ASC',
        'orderby'    => 'order_clause',
        'meta_query' => array(
            'order_clause' => array(
                'key'  => 'seo_order',
                'type' => 'NUMERIC'
            )
        )
    ));
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $term_link = get_term_link($term);
            if ($term_link !== $site_url && !is_wp_error($term_link)) {
?>
    <url>
        <loc><?php echo esc_url($term_link); ?></loc>
        <priority>0.8</priority>
    </url>
<?php
            }
        }
    }
}

// ---------- 页面 ----------
if (in_array('page', $async_array)) {
    $mypages = get_pages();
    if (!empty($mypages)) {
        foreach ($mypages as $page) {
            $page_link = get_page_link($page->ID);
            if ($page_link !== $site_url && !is_wp_error($page_link)) {
                $lastmod = get_post_modified_time('c', true, $page);
?>
    <url>
        <loc><?php echo esc_url($page_link); ?></loc>
        <lastmod><?php echo $lastmod; ?></lastmod>
        <priority>0.8</priority>
    </url>
<?php
            }
        }
    }
}

// ---------- 文章 (使用 WP_Query 替代 query_posts) ----------
if (in_array('post', $async_array)) {
    $post_query = new WP_Query(array(
        'posts_per_page' => 1000,
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,           // 提高性能，不计算总数
        'update_post_meta_cache' => false,  // 不更新文章元数据缓存
        'update_post_term_cache' => false,  // 不更新分类目录缓存
    ));
    while ($post_query->have_posts()) {
        $post_query->the_post();
?>
    <url>
        <loc><?php the_permalink(); ?></loc>
        <lastmod><?php the_time('c'); ?></lastmod>
        <priority>0.7</priority>
    </url>
<?php
    }
    wp_reset_postdata(); // 重置 $post 全局变量
}

// ---------- 标签 ----------
if (in_array('tag', $async_array)) {
    $tags = get_terms('post_tag');
    if (!empty($tags) && !is_wp_error($tags)) {
        foreach ($tags as $tag) {
            $link = get_term_link($tag);
            if (!is_wp_error($link)) {
?>
    <url>
        <loc><?php echo esc_url($link); ?></loc>
        <priority>0.4</priority>
    </url>
<?php
            }
        }
    }
}
?>
</urlset>
<?php
$content = ob_get_clean();
file_put_contents(ABSPATH . '/sitemap.xml', $content);