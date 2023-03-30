<form id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="text" class="search-field" name="s" placeholder="Searching" value="<?php echo get_search_query(); ?>">
    <!--<input type="hidden" name="post_type[]" value="post" />-->
    <!--<input type="hidden" name="post_type[]" value="page" />-->
    <!--<input type="submit" value="Search">-->
    <span class="slider"></span>
    <span class="m-search">
        <i class="BBFontIcons"></i>
    </span>
</form>