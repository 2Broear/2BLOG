<?php 
    /*--------------------------------------------------------------------------
     * ====== COMMON / EXTRA SETUPS ======
     * CAUTION!!! do NOT including via load_theme_partial (lose funcs effects)
     * -------------------------------------------------------------------------
    */
    include_once(get_template_directory() . '/inc/common_setup.php');
    include_once(get_template_directory() . '/inc/extra_setup.php');
    /*--------------------------------------------------------------------------
     * ====== THEME PANEL CONTROLS ======
     * https://themes.artbees.net/blog/custom-setting-page-in-wordpress/
     * -------------------------------------------------------------------------
    */
    if(is_admin()) {
        // load_theme_partial('/inc/theme_settings.php');
        include_once(get_template_directory() . '/inc/theme_settings.php');
        // print_r(get_cat_by_template('weblog'));
    }
?>