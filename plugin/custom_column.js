
(function($) {
    // SINGLE QUICK EDIT
    // https://dev.w.org.ibadboy.net/reference/hooks/quick_edit_custom_box/#Examples
    // we create a copy of the WP inline edit post function
    var $wp_inline_edit = inlineEditPost.edit;
    // and then we overwrite the function with our own code
    inlineEditPost.edit = function( id ) {
        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply( this, arguments );
        // now we take care of our business
        // get the post ID
        var $post_id = 0;
        if ( typeof( id ) == 'object' ) {
            $post_id = parseInt( this.getId( id ) );
        }
        // console.log($post_id);
        if ( $post_id > 0 ) {
            // define the edit row
            var $edit_row = $( '#edit-' + $post_id );
            var $post_row = $( '#post-' + $post_id );
            // get the data
            var $post_orderby = $( '.column-post_orderby', $post_row ).text();
            // var $inprint = !! $('.column-inprint>*', $post_row ).prop('checked');
            // populate the data
            let orderby = $( ':input[name="post_orderby"]', $edit_row );
            !$post_orderby ? orderby.val(1) : orderby.val( $post_orderby );
            // $( ':input[name="inprint"]', $edit_row ).prop('checked', $inprint );
        }
    };
    
    // BULK QUICK EDIT
    // https://dev.w.org.ibadboy.net/reference/hooks/bulk_edit_custom_box/
    $( document ).on( 'click', '#bulk_edit', function() {
        // define the bulk edit row
        var $bulk_row = $( '#bulk-edit' );
        // get the selected post ids that are being edited
        var $post_ids = new Array();
        
        // wordpress compatibility check
        const bulk_list = $bulk_row.find( '#bulk-titles #bulk-titles-list' );
        if(bulk_list.length>0){
            // preg_match_all('/^([^.]*).*$/', get_bloginfo('version'), $version);
            /*** Wordpress 6.0 after ***/
            bulk_list.children().each( function() {
                $post_ids.push($(this).find('button').attr('id').replace(/\_/g, ''));
            });
        }else{
            $bulk_row.find( '#bulk-titles' ).children().each( function() {
                $post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
            });
        }
        console.log($post_ids)
        //wp_version_check()
        // get the data
        var $post_orderby = $bulk_row.find( 'input[name="post_orderby"]' ).val();
        // var $inprint = $bulk_row.find( 'input[name="inprint"]' ).attr('checked') ? 1 : 0;
        // save the data
        $.ajax({
            url: ajaxurl, // this is a variable that WordPress has already defined for us
            type: 'POST',
            async: false,
            cache: false,
            data: {
                action: 'save_bulk_edit_book', // this is the name of our WP AJAX function that we'll set up next
                post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
                post_orderby: $post_orderby,
                // inprint: $inprint
            }
        });
    });
})(jQuery);