(function ($) {
    'use strict';
    $(document).ready(function () {

        // Mobile Menu Customizer)
        $( 'body' ).on( 'click', '#customize-control-start_mobile_menu_acc_ctrl', function ( e ) {
            if( $( 'a.themify-suba-toggle' ).is( e.target ) ) {
                var menuPreview = jQuery('#customize-preview > iframe')[0].contentWindow;
                var stage = $(this).hasClass('topen') ? 'show' : 'hide';
                if( $( '.preview-desktop' ).hasClass( 'active' ) ) {
                    $('.preview-mobile').trigger( 'click' );
                } else {
                    $( '.preview-desktop' ).trigger( 'click' );
                }
            }
        } )
		
    });
}(jQuery));