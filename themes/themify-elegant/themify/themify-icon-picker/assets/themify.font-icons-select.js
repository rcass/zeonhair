/* Routines to manage font icons in theme settings and custom panel. */

;var Themify_Icons = {};

(function($){

	'use strict';

	Themify_Icons = {

		target: '',

		init: function() {

			var $body = $('body');

			$body.on('click', '.themify_fa_toggle', function(e){
				e.preventDefault();
				var $self = $( this );
				if( $self.attr('data-target') ) {
					Themify_Icons.target = $( $self.attr('data-target') );
				} else {
					Themify_Icons.target = $self.prev();
				}
				Themify_Icons.showLightbox( Themify_Icons.target.val() );
			});

			$body.on('click', '#themify_lightbox_fa .lightbox_container a', function(e){
				e.preventDefault();
				Themify_Icons.setIcon( $( this ).data( 'icon' ) );
			});

			$body.on('click', '#themify_lightbox_overlay, #themify_lightbox_fa .close_lightbox', function(e){
				e.preventDefault();
				Themify_Icons.closeLightbox();
			});
			$body.on('change', '.tf-icon-group-select input', function(e){
				var group = $(this).val();
				$( '#themify_lightbox_fa .tf-font-group' ).hide().filter( '[data-group="' + group + '"]' ).show();
			});
			Themify_Icons.Category();
			Themify_Icons.Search();

		},

		getDocHeight: function() {
			var D = document;
			return Math.max(
				Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
				Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
				Math.max(D.body.clientHeight, D.documentElement.clientHeight)
			);
		},

		showLightbox: function( selected ) {
			Themify_Icons.loadIconsList( function(){
				var top = $(document).scrollTop() + 80,
					$lightbox = $("#themify_lightbox_fa"),
					$lightboxOverlay = $('#themify_lightbox_overlay'),
					$body = $('body');

				$lightboxOverlay.show();
				$lightbox
				.show()
				.css('top', Themify_Icons.getDocHeight())
				.animate({
					'top': top
				}, 800 );
				if( selected ) {
					$('a', $lightbox)
					.removeClass('selected')
					.find('.' + selected)
					.closest('a')
						.addClass('selected');
				}

				// set active font group
				if( $lightbox.find( 'a.selected' ).length ) {
					var group = $lightbox.find( 'a.selected' ).closest( '.tf-font-group' ).data( 'group' );
					$( '#themify_lightbox_fa .tf-icon-group-select input[value="' + group + '"]' ).click();
				} else {
					$( '#themify_lightbox_fa .tf-icon-group-select input:first' ).click();
				}

				// Position lightbox correctly in Builder
				if ( $body.hasClass('themify_builder_active') && $body.hasClass('frontend') ) {
					var $tbOverlay = $('#themify_builder_overlay');
					if ( $tbOverlay.length > 0 ) {
						$lightboxOverlay.insertAfter($tbOverlay);
						$lightbox.insertAfter($tbOverlay);
					}
				}
				$('#themify-search-icon-input').val('').trigger('keyup');
				$('#themify_lightbox_fa .themify-lightbox-icon').find('.selected').trigger('click');
			});
		},

		loadIconsList : function( callback ){
			if( $( '#themify_lightbox_fa' ).length ) {
				callback();
			} else {
				$.ajax({
					url : ajaxurl,
					data : { action : 'tf_icon_picker' },
					type : 'POST',
					success : function( data ){
						$( 'body' ).append( data );
						callback();
					}
				});
			}
		},

		setIcon: function(iconName) {
			var $target = $(Themify_Icons.target);
			$target.val( iconName );

			// icon preview
			if ( $('.fa:not(.icon-close)', $target.parent().parent()).length > 0 ) {
				$.ajax({
					url : ajaxurl,
					data : { action: 'tf_get_icon', tf_icon : iconName },
					success: function( data ){
						$('.fa:not(.icon-close)', $target.parent().parent()).removeClass().addClass( data );
					}
				});
			}
			Themify_Icons.closeLightbox();
		},

		closeLightbox: function() {
			$('#themify_lightbox_fa').animate({
				'top': Themify_Icons.getDocHeight()
			}, 800, function() {
				$('#themify_lightbox_overlay').hide();
				$('#themify_lightbox_fa').hide();
			});
		},
		Category:function(){
		   
			$('body').delegate('.themify-lightbox-icon li','click',function(e){
				e.preventDefault();
				var $selected =  $(this).closest('.themify-lightbox-icon').find('.selected');
				if(!$(this).hasClass('selected')){
				   $selected.removeClass('selected');
					var $id = $(this).data('id');
					if($('#'+$id).length>0){
						$(this).closest('.lightbox_container').find('section').not('#'+$id).fadeOut('fast',function(){
							$('#'+$id).fadeIn('normal');
						});
						$(this).addClass('selected');
					}
				}
				else {
					$selected.removeClass('selected');
					$(this).closest('.lightbox_container').find('section').fadeIn('fast',function(){
						$('#themify-search-icon-input').trigger('keyup');
					});
				}
			});
		},
		Search:function(){
			$.expr[":"].contains = $.expr.createPseudo(function(arg) {
				return function( elem ) {
					return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
				};
			});
			$('body').delegate('#themify-search-icon-input','keyup',function(){
				var $text = $.trim($(this).val()),
					$container = $('#themify_lightbox_fa').find('.tf-font-group a'),
					$sections  = $('#themify_lightbox_fa').find('section');
				if($text){
					$container.hide();
					$sections.hide();

					$container.filter(':contains(' + $text.toUpperCase()  + ')').show().each( function(){
						$( this ).closest( 'section' ).show();
					} );
				}
				else{
				   
					$sections.show();
					$container.show();
				}
			});
		}
	};
	if( typeof tbLoaderVars !== 'undefined' ) {
		//is frontend
		$( 'body' ).on( 'builderscriptsloaded.themify', Themify_Icons.init );
	} else {
		$( document ).ready( Themify_Icons.init );
	}
})(jQuery);