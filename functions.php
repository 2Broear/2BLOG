<?php 
    /*--------------------------------------------------------------------------
     * EXTENDS SETTING
     * -------------------------------------------------------------------------
    */
    include_once(TEMPLATEPATH . '/inc/extend_setup.php');  // Extends setup
    // EDIT PAGE EDITOR // if(is_edit_page() || is_single()) 
    include_once(TEMPLATEPATH . '/inc/wp_blocks.php');  // Gutenberg editor
    
    /*--------------------------------------------------------------------------
     * EXTRA THEME FUNC
     *--------------------------------------------------------------------------
    */
    include_once(TEMPLATEPATH . '/inc/extra_setup.php');  // Common setup
    
    /*--------------------------------------------------------------------------
     * THEME SETUP (!!!fatal error 500 occured: require_once(TEMPLATEPATH . './plugin/sitemap.php');)
     *--------------------------------------------------------------------------
    */
    include_once(TEMPLATEPATH . '/inc/theme_setup.php');  // Theme setup
    
    /*--------------------------------------------------------------------------
     * PANEL CONTROLS https://themes.artbees.net/blog/custom-setting-page-in-wordpress/
     * -------------------------------------------------------------------------
    */
    if(is_admin()) include_once(TEMPLATEPATH . '/inc/theme_settings.php');
?>