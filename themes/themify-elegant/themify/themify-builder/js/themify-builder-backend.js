(function($){

	'use strict';

	if( $('#page-builder.themify_write_panel').length === 0 ) return;

	var api = themifybuilderapp;

	api.mode = 'skeleton';
	// auto save metabox when save post
	api.isPostSave = false;
	api.render = function() {
		var data = new api.Collections.Rows(themifyBuilder.builder_data);

		api.Instances.Builder[0] = new api.Views.Builder({ el: '#themify_builder_row_wrapper', collection: data });
		api.Instances.Builder[0].render();

		var modulePanel = new api.Views.ModulePanel({ el: '.themify_builder_module_panel' }),
			navTool = new api.Views.Nav( {el: '.themify_builder_backend_options_menu' }),
			editorNav = new api.Views.EditorNavComponent({ el: '.themify_builder_undo_tools'});

		// Save Builder
		$('#themify_builder_main_save').on('click', function( event ){
			event.preventDefault();
			api.Utils.saveBuilder(true)
		});

		/* hook save to publish button */
		$('input#publish,input#save-post').on('click', function (e) {
			if (e.which) {
				$('input[name*="builder_switch_frontend"]').val(0);
			}

			if (!api.isPostSave) {
				api.Utils.saveBuilder(false).done(function(){
					// Clear undo history
					ThemifyBuilderCommon.undoManager.instance.clear();

					api.isPostSave = true;
					$(e.currentTarget).trigger('click');
				});
				e.preventDefault();
			}
		});

		// switch frontend
		$('#themify_builder_switch_frontend').on('click', this.switchFrontEnd);
		$('<a href="#" id="themify_builder_switch_frontend_button" class="button themify_builder_switch_frontend">' + themifyBuilder.switchToFrontendLabel + '</a>').on('click', this.switchFrontEnd).appendTo( '#postdivrich #wp-content-media-buttons' );

		// Switch to front builder after create post/page
		if( window.sessionStorage.getItem( 'frontendURL' ) ) {
			var postURL = window.sessionStorage.getItem( 'frontendURL' );
			window.sessionStorage.removeItem( 'frontendURL' );
			ThemifyBuilderCommon.showLoader('show');
			window.location.href = postURL;
		}

		$('input[name*="builder_switch_frontend"]').closest('.themify_field_row').hide(); // hide the switch to frontend field check

		api.Views.bindEvents();

		api.Forms.bindEvents();
	};

	api.switchFrontEnd = function (e) {
		e.preventDefault();
		var targetLink = themifyBuilder.permalink.replace(/\&amp;/g, '&') + '#builder_active';

		if ($('#themify_builder_row_wrapper').is(':visible')) {
			api.Utils.saveBuilder(true, function () {
				// Clear undo history
				ThemifyBuilderCommon.undoManager.instance.clear();
				api.createPostBuilderButton( targetLink );
			});
		} else {
			api.createPostBuilderButton( targetLink );
		}
	};

	api.createPostBuilderButton = function( link ) {
		var postId = $( '#post_ID' ).val();
		if( window.location.href.indexOf( postId ) < 0 ) {
			window.sessionStorage.setItem( 'frontendURL', link );
			$('#post').trigger( 'submit' );
		} else {
			window.location.href = link;
		}
	};

	$(function(){

		var _original_icl_copy_from_original;

		api.render();

		// WPML compat
		if (typeof window.icl_copy_from_original == 'function') {
			_original_icl_copy_from_original = window.icl_copy_from_original;
			// override "copy content" button action to load Builder modules as well
			window.icl_copy_from_original = function (lang, id) {
				_original_icl_copy_from_original(lang, id);
				jQuery.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action: 'themify_builder_icl_copy_from_original',
						source_page_id: id,
						source_page_lang: lang
					},
					success: function (data) {
						if (data != '-1') {
							jQuery('#page-builder .themify_builder.themify_builder_admin').empty().append(data).contents().unwrap();

							// redo module events
							//ThemifyPageBuilder.moduleEvents();
						}
					}
				});
			};
		}
		
	});

	// Run on WINDOW load
	$(window).load(function(){

		var $panel = $('#page-builder .themify_builder_module_panel'),
			$top = 0,
			$scrollTimer = null,
			$panel_top = 0,
			$wpadminbar = $('#wpadminbar'),
			$wpadminbarHeight = $wpadminbar.outerHeight(true);
		if ($panel.length > 0) {
			if ($panel.is(':visible')) {
				themify_sticky_pos();
			}
			else {
				$('#themify-meta-box-tabs a').one('click', function () {
					if ($(this).attr('id') == 'page-buildert') {
						themify_sticky_pos();
					}
				});
			}
		}

		function themify_sticky_pos() {
			$panel.width($panel.width());
			$top = $panel.offset().top;
			$panel_top = Math.round($('#page-builder').offset().top);
			$('#themify_builder_module_tmp').height($panel.outerHeight(true));
			$(window).scroll(function () {
				if ($scrollTimer) {
					clearTimeout($scrollTimer);
				}
				$scrollTimer = setTimeout(handleScroll, 15);

			}).resize(function () {
				$top = $panel.offset().top;
				$panel.width($('#page-builder .themify_builder_admin').width()).css('top', $wpadminbar.outerHeight(true));
				$('#themify_builder_module_tmp').height($panel.outerHeight(true));
			});
		}

		function handleScroll() {
			$scrollTimer = null;
			var $bottom = $panel_top + $('#page-builder').height(),
				$scroll = $(this).scrollTop();
			if ($scroll > $top && $scroll < $bottom) {
				$panel.addClass('themify_builder_module_panel_fixed').css('top', $wpadminbarHeight);
				$('#themify_builder_module_tmp').css('display', 'block');

			} else {
				$panel.removeClass('themify_builder_module_panel_fixed').css('top', 0);
				$('#themify_builder_module_tmp').css('display', 'none');
			}
		}

	});
})(jQuery);