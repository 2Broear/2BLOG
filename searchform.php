<form id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="text" class="search-field" name="s" placeholder="Search" autocomplete="off" value="<?php echo get_search_query(); ?>">
    <span class="slider"></span>
    <span class="m-search">
        <i class="BBFontIcons"></i>
    </span>
</form>