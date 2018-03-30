/*! Themify Builder - Asynchronous Script and Styles Loader */
var tbLoaderVars, themifyBuilder;

(function($, window, document, undefined){

	'use strict';

	function updateQueryString(a,b,c){
		c||(c=window.location.href);var d=RegExp("([?|&])"+a+"=.*?(&|#|$)(.*)","gi");if(d.test(c))return b!==void 0&&null!==b?c.replace(d,"$1"+a+"="+b+"$2$3"):c.replace(d,"$1$3").replace(/(&|\?)$/,"");if(b!==void 0&&null!==b){var e=-1!==c.indexOf("?")?"&":"?",f=c.split("#");return c=f[0]+e+a+"="+b,f[1]&&(c+="#"+f[1]),c}return c;
	}

	$(document).ready(function(){

		var $tbContent = $('.themify_builder_content');
		var in_customizer = false;
		var builderLoader = $('<div/>', {
			id: 'themify_builder_alert',
			class: 'themify-builder-alert busy'
		});

		// check for wp.customize return boolean
		if ( typeof wp !== 'undefined' ) {
			in_customizer =  typeof wp.customize !== 'undefined' ? true : false;
		}

		if ( $tbContent.length > 0 && ! in_customizer ) {
			$tbContent.after( '<a class="themify_builder_turn_on js-turn-on-builder" href="#"><span class="dashicons dashicons-edit"></span>' + tbLoaderVars.turnOnBuilder + '</a>' );
		}

		// #wp-link-backdrop, #wp-link-wrap
		$('#wp--wrap').remove();

		$('body').on('click.tbloader', '.toggle_tf_builder a:first, a.js-turn-on-builder', function(e){

			e.preventDefault();

			if( $( '.themify-builder-alert' ).length == 0 ) {
				$( 'body' ).append( builderLoader );
			}

			// Change text to indicate it's loading
			$('.themify_builder_front_icon').parent().append($(tbLoaderVars.progress));

			// Fire the ajax request.
			var jqxhr = $.post( tbLoaderVars.ajaxurl, {
				action: 'themify_builder_loader',
				scripts: tbLoaderVars.assets.scripts,
				styles: tbLoaderVars.assets.styles
			});

			// Allow refreshes to occur again if an error is triggered.
			jqxhr.fail( function() {
				$("#themify_builder_alert").removeClass("busy").fadeOut(800);
				window.console && console.log( 'AJAX failed' );
			});

			// Success handler
			jqxhr.done( function( response ) {
	
				try {
					response = $.parseJSON( response );
				} catch(e) {
					window.console && console.log( response );
					return;
				}

				if ( ! response ) {
					return;
				}
				
				
				// Count styles and scripts
				var countStyles = 0, countScripts = 0;
				
				$.ajax({
					url:tbLoaderVars.ajaxurl,
					type:'POST',
					data:{'action':'themify_builder_loader_tpl'},
					success:function(resp){
						
						if (resp) {
							// Append template script to DOM in requested location
							document.getElementsByTagName( 'body' )[0].insertAdjacentHTML("beforeend", resp);
						}

						// Load scripts
						if ( response.scripts ) {
							countScripts = response.scripts.length - 1;

							$( response.scripts ).each( function() {
								var elementToAppendTo = this.footer ? 'body' : 'head';

								// Add script handle to list of those already parsed
								tbLoaderVars.assets.scripts.push( this.handle );

								// Output extra data, if present
								if ( this.jsVars ) {
									var data = document.createElement('script'),
										dataContent = document.createTextNode( "//<![CDATA[ \n" + this.jsVars + "\n//]]>" );

									data.type = 'text/javascript';
									data.appendChild( dataContent );

									document.getElementsByTagName( elementToAppendTo )[0].appendChild(data);
								}

								// Build script object
								var script = document.createElement('script');
								script.type = 'text/javascript';
								script.src = this.src;
								script.id = this.handle;
								script.async = false;
								script.onload = function(){
									if ( 0 === countScripts ) {
										// Write themifyBuilder.post_ID
										if ( themifyBuilder ) {
											themifyBuilder.post_ID = tbLoaderVars.post_ID;
											themifyBuilder.isRevisionEnabled = tbLoaderVars.isRevisionEnabled;
										}

										// Remove click event
										$('body').off('click.tbloader');

										// Initialize Builder
										// Event replaces $(document).ready() and $(window).load()
										// Functions hooked to those events must be hooked to this instead
										$('body').trigger('builderscriptsloaded.themify');
									}
									countScripts--;
								};
								script.onerror = function() {
									countScripts--;
								};

								// Append script to DOM in requested location
								document.getElementsByTagName( elementToAppendTo )[0].appendChild(script);

							} );
						}

						// responsive iframe
						var responsiveSrc = updateQueryString('builder_grid_activate', 1, window.location.href );
						$('#themify_builder_site_canvas_iframe').attr('src', responsiveSrc).on('load', function(){
							$('body').trigger('builderiframeloaded.themify');
						});
						
					}
				});
				
				// Load styles
				if ( response.styles ) {
					countStyles = response.styles.length - 1;
					$( response.styles ).each( function() {
						// Add stylesheet handle to list of those already parsed
						tbLoaderVars.assets.styles.push( this.handle );

						// Build link tag
						var style = document.createElement('link');
						style.rel = 'stylesheet';
						style.href = this.src;
						style.id = this.handle + '-css';
						style.async = false;
						style.onload = function(){
							if ( 0 === countStyles ) {
								// Event replaces $(document).ready() and $(window).load()
								// Functions hooked to those events must be hooked to this instead
								$('body').trigger('builderstylesloaded.themify');
							}
							countStyles--;
						};
						style.onerror = function() {
							countStyles--;
						};

						// Append link tag if necessary
						if ( style ) {
							document.getElementsByTagName('head')[0].appendChild(style);
						}
					} );
				}
				
			});

		});

		// Grab hash url #builder_active then activate frontend edit
		if ( window.location.hash === "#builder_active" ) {
			if ( $('.toggle_tf_builder a:first').length > 0 ) {
				$('.toggle_tf_builder a:first').trigger('click');
			} else if ( $('.js-turn-on-builder').length > 0 ) {
				$('.js-turn-on-builder').first().trigger( 'click' );
			}
		}

	});

})(jQuery, window, document);