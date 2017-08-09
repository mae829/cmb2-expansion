'use strict';

jQuery(function($){
    // metabox tab navigation
    var $active,
        $content,
        $tabNav           = $('.nav-tab-wrapper'),
        $links            = $tabNav.find('a'),
        singleTabHeight   = $('.nav-tab').outerHeight();

    $active = $($links.filter(':visible')[0]).addClass('nav-tab-active');

    $content = $( $($active[0]).attr('href') );

    // hide all metaboxes that are hidden in nav-tab as well
    $links.not($active).each(function() {
        // use specific class instead of show/hide so metaboxes don't appear during checkbox toggle in "Screen Options"
        $(this.hash).addClass('cmb2-exp-hidden');
    });

    // screen options metabox functionality
    $('.metabox-prefs input[type="checkbox"]').each(function(){
        var $this = $(this),
            $tab  = '#'+$this.val();

        $this.on('click', function(e){
            //on click, toggle appearance in .nav-tab-wrapper
            $tabNav.find('[href="'+ $tab +'"]').toggleClass('hidden');
            responsiveTabs();
        });

    });

    // click actions for tab nav links
    $tabNav.on('click', 'a', function(e){
        $active.removeClass('nav-tab-active');
        $content.addClass('cmb2-exp-hidden');

        $active   = $(this);
        $content  = $(this.hash);

        $active.addClass('nav-tab-active');
        $content.removeClass('closed cmb2-exp-hidden');

        e.preventDefault();
        //return false;
    });

    //check if CMB2 admin-tabs are breaking into rows
    function responsiveTabs(){
        var tabsDisplay       = $tabNav.css('display'),
            tabsParentWidth   = $tabNav.parent().width(),
            $sidebar_width    = $('#side-sortables').css('width'),
            $metaBoxes        = $();

        // grab metaboxes based on class in the .nav-tab-wrapper
        $links.not('.hidden').each(function(){
            var $div    = $($(this).attr('href'));
            $metaBoxes  = $metaBoxes.add( $div );
        });

        // collapse sidebar metaboxes only when in mobile view (when post content width == sidebar width)
        var contentWidth = $('#post-body-content').width(),
                sideBarWidth = $('#postbox-container-1').width(),
                $sideMbxs = $('#side-sortables .postbox');

        if( contentWidth == sideBarWidth ){
            $sideMbxs.addClass('alt-meta-display closed');
        }else{
            $sideMbxs.removeClass('alt-meta-display closed');
        }

        // set width of .nav-tab-wrapper to its parent's width, THEN grab the height (flor fluid collapse of nav tabs)
        $tabNav.outerWidth(tabsParentWidth);
        var tabsHeight        = $tabNav.height();

        if(tabsDisplay == 'block' && tabsHeight > singleTabHeight){
            $metaBoxes
                .addClass('alt-meta-display closed')
                .on('click', function(){
                    $metaBoxes.not($(this)).addClass('closed');
                });
            $tabNav.addClass('alt-tabs-display');
        }else if(tabsHeight <= singleTabHeight){
            $tabNav.removeClass('alt-tabs-display');
            $metaBoxes.removeClass('alt-meta-display closed');
        }
    }
    responsiveTabs();
    $(window).on('resize', responsiveTabs);

});


(function($) {
    // Override the WP heartbeat ajax-admin function that saves the hidden metaboxes,
    // and instead use the unchecked boxes in "Screen Options"
    // Original code in file /wp-admin/js/postbox.js
    if( 'undefined' !== typeof postboxes ) {
        postboxes.save_state = function( page ) {
            var closed = $('.postbox').filter('.closed').map(function() { return this.id; }).get().join(','),
                hidden = $('.hide-postbox-tog').not(':checked').map(function() { return this.value; }).get().join(',');

            $.post(ajaxurl, {
                action: 'closed-postboxes',
                closed: closed,
                hidden: hidden,
                closedpostboxesnonce: jQuery('#closedpostboxesnonce').val(),
                page: page
            });
        }
    }

}(jQuery));
