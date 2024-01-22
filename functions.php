<?php 
    /*--------------------------------------------------------------------------
     * COMMON SETUP
     * -------------------------------------------------------------------------
    */
    include_once(get_template_directory() . '/inc/common_setup.php');  // Common setup
    /*--------------------------------------------------------------------------
     * EXTRA THEME FUNCTIONS
     *--------------------------------------------------------------------------
    */
    include_once(get_template_directory() . '/inc/extra_setup.php');  // Common setup
    /*--------------------------------------------------------------------------
     * THEME PANEL CONTROLS https://themes.artbees.net/blog/custom-setting-page-in-wordpress/
     * -------------------------------------------------------------------------
    */
    if(is_admin()) include_once(get_template_directory() . '/inc/theme_settings.php');
?>