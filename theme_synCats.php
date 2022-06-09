<?php
    // CREATE category sync-action to page // https://stackoverflow.com/questions/32314278/how-to-create-a-new-wordpress-page-programmatically
    // BUG：删除页面后自动递增trashed..(已解决，来自wp_update_category)
    add_action('create_category', 'create_category_sync_page', 11, 2);  
    function create_category_sync_page($term_id){
        // create logic
        $create_cat = get_term($term_id);
        $create_cat_id = $create_cat->term_id;
        $create_cat_slug = $create_cat->slug;
        $create_cat_title = $create_cat->name;
        // $create_cat_slug=='slash' ? $create_cat_slug="/" : $create_cat_slug;  // not working.
        $new_page = array(
            'post_name'    => $create_cat_slug,
            'post_title'    => $create_cat_title,
            'post_type' => 'page',
            'post_status'   => 'publish',
            'comment_status' => 'open',
            'page_template' => get_term_meta($term_id, 'seo_template', true),
            'post_content'  => '',
            // 'menu_order' => $create_cat_id
            // 'post_author'   => 1,
        );
        global $wpdb;
        $use_slash = $create_cat_slug==='slash' || strpos($create_cat_slug, '/')!==false;
        if($use_slash) $wpdb->update($wpdb->terms, array('slug' => '/'), array('term_id' => $term_id), array('%s'), array('%d'));  // sync 'slash' to category
        // wpdb insert row logic
        wp_insert_post($new_page);
        // USE post_title But post_name(slug) INCASE 'slash' slug occured twice.
        $page_cid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$create_cat_title' AND post_type = 'page'");  // same name(title) with category's name
        update_post_meta($page_cid, 'post_term_id', $term_id);  // bind category id to page(slash err)
        if($use_slash) $wpdb->update($wpdb->posts, array('post_name' => '/'), array('ID' => $page_cid), array('%s'), array('%d'));  // sync 'slash' to page
    }
    
    
    // !!! BUG : admin-ajax.php 500 error !!! (fixed, found in wp_update_term at save_post_{page} with infinite loop)
    // UPDATE category sync-data to page (before term cache cleaned)
    add_action('edited_category', 'update_category_sync_page', 11, 2);  // edit_category 有缓存
    // add_action( 'saved_category', 'action_function_name_6269', 10, 4 );  // saved_term
    function update_category_sync_page($term_id, $tt_id){
        $edit_cat = get_term($term_id);  // get_term
        $edit_cat_slug = $edit_cat->slug;
        global $wpdb;
        $use_slash = $edit_cat_slug==='slash' || strpos($edit_cat_slug, '/')!==false;
        if($use_slash) $wpdb->update($wpdb->terms, array('slug' => '/'), array('term_id' => $term_id), array('%s'), array('%d'));  // sync 'slash' to category
        $page_cid = wpdb_postmeta_query('post_id','meta_value', $term_id);
        // creat new page with the same name/slug category if page not exists.
        $post_data = array(
            'ID' => $page_cid,  // required page cat-id for update
            'post_name' => $edit_cat_slug,
            'post_title' => $edit_cat->name,
            'page_template' => get_term_meta($term_id, 'seo_template', true) //sync page_template to page
        );
        wp_update_post(wp_slash($post_data));
        if($use_slash) $wpdb->update($wpdb->posts, array('post_name' => '/'), array('ID' => $page_cid), array('%s'), array('%d'));  // sync 'slash' to page
    }
    // DELETE category sync-action to page  // https://wp-kama.com/hook/delete_category
    add_action('delete_category', 'delete_category_sync_page', 10, 4);
    function delete_category_sync_page( $term, $tt_id, $deleted_term, $object_ids){
        $page_cid = wpdb_postmeta_query('post_id','meta_value', $deleted_term->term_id);
        wp_delete_post($page_cid, $bypass_trash=true);  // Delete Category have no trash-bin, delete post immediately without trash.
    }
    
    
    // UPDATE post(page) sync-data to category
    // add_action('save_post_page', 'update_page_sync_category', 10, 3);
    // add_action('post_updated', 'update_page_sync_category', 10, 3);
    add_action('save_post', 'update_page_sync_category', 10, 3);
    function update_page_sync_category($post_id, $post_after, $post_before) {
        // DO NOT USE wp_insert_category to UPDATE category
        $cat_id = get_post_meta($post_id, "post_term_id", true);  // page bind category id
        $edit_post_slug = $post_after->post_name;
        if($cat_id){
            // wp_update_{type} causes an infinite loop inside save_post hook
        	if(!wp_is_post_revision($post_id)){
        		// remove this hook so that it does not create an infinite loop
        		remove_action( 'save_post', 'update_page_sync_category', 10, 3);
        		// update the post when the save_post hook is called again // wp_update_post( $my_args );
        		wp_update_term($cat_id, 'category', array(
                    'name' => $post_after->post_title,
                    'slug' => $edit_post_slug
                ));
                update_term_meta($cat_id, 'seo_template', $post_after->page_template);  // sync page_template to category
                global $wpdb;
                if($edit_post_slug==='slash'){// || $edit_post_slug==$edit_post_title
                    $wpdb->update($wpdb->posts, array('post_name' => '/'), array('ID' => $post_id), array('%s'), array('%d'));  // sync 'slash' to post
                    $wpdb->update($wpdb->terms, array('slug' => '/'), array('term_id' => $term_id), array('%s'), array('%d'));  // sync 'slash' to category
                }
        		// return hook back
        		add_action( 'save_post', 'update_page_sync_category', 10, 3);  //, __FUNCTION__ 
        	};
        }
    }
    // DELETE/trash post(page) sync-action to category
    // BUG: return a delete error-page even if the page&category has been deleted successfully
    add_action('before_delete_post', 'delete_page_sync_category', 10, 2);
    function delete_page_sync_category($post_id, $post_data){
        $cat_id = get_post_meta($post_id, "post_term_id", true);  // cached page-bind-cid
		wp_delete_term($cat_id, 'category'); // wp_delete_category($cat_id);
    }
    // USE FILTER BUT ACTION WHILE DELETE A POST
    // add_action('create_category', 'update_category_slash_slug', 11, 2);  
    // https://www.cnblogs.com/bushe/p/3951433.html
    // add_action('edited_category', 'update_category_slash_slug', 99, 2);
    // function update_category_slash_slug($term_id, $tt_id){
    //     global $wpdb;
    //     $edit_cat = get_term($term_id);  // get_term
    //     // update_term_meta($term_id, 'seo_slash', $edit_cat->name);  // record updated term name(check if '/' or empty)
    //     if($edit_cat->slug==='slash' || strpos($edit_cat->slug, '/')!==false || $edit_cat->slug===$edit_cat->name || $edit_cat->slug===get_term_meta($term_id, 'seo_slash', true)){
    //         $wpdb->update($wpdb->terms, array('slug' => '/'), array('term_id' => $term_id), array('%s'), array('%d'));
    //     }
    // }
    
    // add_action('save_post', 'update_post_slash_slug', 99, 2);
    // function update_post_slash_slug($post_id, $post_after) {
    //     global $wpdb;
    //     // update post(page) slash / slug
    //     if($post_after->post_name==='slash' || strpos($post_after->post_name, '/')!==false){
    //         $wpdb->update($wpdb->posts, array('post_name' => '/'), array('ID' => $post_id), array('%s'), array('%d'));
    //     }
    // }
?>