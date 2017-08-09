jQuery(function($) {
    // close CMB2 sub group boxes if more than one is available
    $('.cmb-repeatable-group').each(function() {
        var $this = $(this),
            $boxes = $this.find('.postbox');

        if ( $boxes.length > 1 ) { $boxes.addClass('closed'); }
    } );
    $('.cmb2-metabox').each(function() {
        var $this = $(this),
            $boxes = $this.find('.cmb-repeat-group-wrap'),
            $tags  = $this.find('.cmb-repeatable-grouping');

        if ( $boxes.length > 1 ) { $tags.addClass('closed'); }
    } );

} );
