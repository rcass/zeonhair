// tfsmartresize helper
!function(a,b){var c=function(a,b,c){var d;return function(){function g(){c||a.apply(e,f),d=null}var e=this,f=arguments;d?clearTimeout(d):c&&a.apply(e,f),d=setTimeout(g,b||100)}};jQuery.fn[b]=function(a){return a?this.bind("resize",c(a)):this.trigger(b)}}(jQuery,"tfsmartresize");

var Themify, ThemifyGallery;
(function ($, window, document, undefined) {
	'use strict';

	/* window load fires only once in IE */
	window.addEventListener( "load", function () {
		window.loaded = true;
		Themify.triggerEvent( window, 'resize' );
		$( 'body' ).addClass( 'page-loaded' );
	});

	Themify = {
		fonts:[],
		cssLazy:[],
		triggerEvent: function (a, b) {
			var c;
			document.createEvent ? (c = document.createEvent("HTMLEvents"), c.initEvent(b, !0, !0)) : document.createEventObject && (c = document.createEventObject(), c.eventType = b), c.eventName = b, a.dispatchEvent ? a.dispatchEvent(c) : a.fireEvent && htmlEvents["on" + b] ? a.fireEvent("on" + c.eventType, c) : a[b] ? a[b]() : a["on" + b] && a["on" + b]()
		},
		Init: function () {
			if (typeof tbLocalScript !== 'undefined' && tbLocalScript) {
				var $self = Themify;
				$(document).ready(function () {
                                    tbLocalScript.isTouch = $('body').hasClass('touch');
                                    if( $( '.themify_builder_content div:not(.js-turn-on-builder)' ).length ) {
                                            $self.LoadAsync(tbLocalScript.builder_url + '/js/themify.builder.script.js', function(){
                                                    $( 'body' ).trigger( 'themifyBuilderScriptLoaded' );
                                            }); // this script should be always loaded even there is no builder content because it's also requires for themify_shortcode for exp: animation js
                                    }
                                    else{
                                            $self.bindEvents();
					}
				});
				$('body').on('builderscriptsloaded.themify', function () {
					$self.LoadAsync(tbLocalScript.builder_url + '/js/themify.builder.script.js');
				});
			}
			else {
				this.bindEvents();
			}
		},
		bindEvents: function () {
			var $self = Themify;
			if (window.loaded) {
				$self.domready();
				$self.windowload();
			}
			else {
				$(window).load( $self.windowload );
				$(document).ready( $self.domready );
			}
			$('body').on('builder_load_module_partial builder_toggle_frontend', $self.InitMap)
                                .on( 'builder_load_module_partial builder_toggle_frontend', $self.LazyLoad);
		},
		domready : function() {
			Themify.LazyLoad();
			Themify.InitCarousel();
			Themify.InitMap();
		},
		windowload : function() {
			$('.shortcode.slider, .shortcode.post-slider, .slideshow-wrap').css({'height': 'auto', 'visibility': 'visible'});
			Themify.InitGallery();
		},
		LazyLoad: function() {
			var editMode = $('body').hasClass('themify_builder_active'),
                            self = Themify,
                            isLoaded = function( css ) { return $.inArray( css, self.cssLazy ) !== -1; },
                            is_fontawesome = editMode || $('.fa').length>0,
                            is_themify_icons = editMode || $( 'span[class*="ti-"], i[class*="ti-"], .module-menu[data-menu-breakpoint]').length>0;
                            if(!editMode){
                                    if(!is_fontawesome){
                                            is_fontawesome = self.checkFont('FontAwesome');
                                    }
                                    if(!is_themify_icons){
                                            is_themify_icons = self.checkFont('Themify');
                                    }
                            }
                            else if(typeof tbLocalScript !== 'undefined' && tbLocalScript && $('link#builder-styles').length===0 && ! isLoaded( 'builder-styles' ) ){
                            
                                self.LoadCss(  tbLocalScript.builder_url + '/css/themify-builder-style.css', themify_vars.version );
				self.cssLazy.push( 'builder-styles' );
                            }
			
                        /*Load addons css/js,we don't need to wait the loading of builder*/
                        if(typeof tbLocalScript !== 'undefined' && tbLocalScript && Object.keys(tbLocalScript.addons).length>0){
                            for(var i in tbLocalScript.addons){
                                if(!isLoaded( i )  && $(tbLocalScript.addons[i].selector).length>0){
                                    if(tbLocalScript.addons[i].css){
                                        self.LoadCss(tbLocalScript.addons[i].css, tbLocalScript.addons[i].ver );
                                    }
                                    
                                    if(tbLocalScript.addons[i].js){
                                        if(tbLocalScript.addons[i].external){
                                            var s = document.createElement('script');
                                            s.type = 'text/javascript';
                                            s.text = tbLocalScript.addons[i].external;
                                            var t = document.getElementsByTagName('script')[0];
                                            t.parentNode.insertBefore(s, t);
                                        }
                                        self.LoadAsync(tbLocalScript.addons[i].js,null, tbLocalScript.addons[i].ver );
                                    }
                                    self.cssLazy.push( i );
                                }
                            }
                        }
			if( (editMode  || is_fontawesome) && ! isLoaded( 'font-awesome' ) ) {
				self.LoadCss( themify_vars.url + '/fontawesome/css/font-awesome.min.css', themify_vars.version );
				self.cssLazy.push( 'font-awesome' );
			}
			if( (editMode  || is_themify_icons) && !isLoaded( 'themify-icons' )) {
				self.LoadCss(themify_vars.url + '/themify-icons/themify-icons.css', themify_vars.version);
				self.cssLazy.push( 'themify-icons' );
			}
			if( $( 'i[class*="icon-"]' ).length>0 && typeof themify_vars.fontello_path == 'string' && !isLoaded( 'themify-fontello' )) {
				self.LoadCss( themify_vars.fontello_path );
				self.cssLazy.push( 'themify-fontello' );
			}
			if(!isLoaded( 'themify-framework' ) && $( '.shortcode' ).length>0) {
				self.LoadCss( themify_vars.url + '/css/themify.framework.css', null, $( '#themify-framework-css' )[0] );
				self.cssLazy.push( 'themify-framework' );
			}  
		},
		InitCarousel: function () {
			if ($('.slides[data-slider]').length > 0) {
				var $self = this;
				$self.LoadAsync(themify_vars.includesURL + 'js/imagesloaded.min.js', function () {
						if('undefined' === typeof $.fn.carouFredSel){
							$self.LoadAsync(themify_vars.url + '/js/carousel.js', $self.carouselCalback, null, null, function () {
								return ('undefined' !== typeof $.fn.carouFredSel);
							});
						}
						else{
							$self.carouselCalback();
						}
				}, null, null, function () {
					return ('undefined' !== typeof $.fn.imagesLoaded);
				});
			}
		},
		carouselCalback: function () {

			$('.slides[data-slider]').each(function () {
				$(this).find("> br, > p").remove();
				var $this = $(this),
						$data = JSON.parse(window.atob($(this).data('slider'))),
						height = (typeof $data.height === 'undefined') ? 'auto' : $data.height,
						$numsldr = $data.numsldr,
						$slideContainer = 'undefined' !== typeof $data.custom_numsldr ? '#' + $data.custom_numsldr : '#slider-' + $numsldr,
						$speed = $data.speed >= 1000 ? $data.speed : 1000 * $data.speed,
						$args = {
							responsive: true,
							swipe: true,
							circular: $data.wrapvar,
							infinite: $data.wrapvar,
							auto: {
								play: $data.auto == 0 ? false : true,
								timeoutDuration: $data.auto >= 1000 ? $data.auto : 1000 * $data.auto,
								duration: $speed,
								pauseOnHover: $data.pause_hover
							},
							scroll: {
								items: parseInt($data.scroll),
								duration: $speed,
								fx: $data.effect
							},
							items: {
								visible: {
									min: 1,
									max: parseInt($data.visible)
								},
								width: 120,
								height: height
							},
							onCreate: function (items) {
								$this.closest('.caroufredsel_wrapper').outerHeight($this.outerHeight(true));
								$($slideContainer).css({'visibility': 'visible', 'height': 'auto'});
							}
						};
				if ($data.slider_nav) {
					$args.prev = $slideContainer + ' .carousel-prev';
					$args.next = $slideContainer + ' .carousel-next';
				}
				if ($data.pager) {
					$args.pagination = $slideContainer + ' .carousel-pager';
				}
				$this.imagesLoaded().always(function () {
					$this.carouFredSel($args);
				});
			});

			var tscpsDidResize = false;
			$(window).on("resize", function () {
				tscpsDidResize = true;
			});
			setInterval(function () {
				if (tscpsDidResize) {
					tscpsDidResize = false;
					$(".slides[data-slider]").each(function () {
						var heights = [],
								newHeight,
								$self = $(this);
						$self.find("li").each(function () {
							heights.push($(this).outerHeight(true));
						});
						newHeight = Math.max.apply(Math, heights);
						$self.outerHeight(newHeight);
						$self.parent().outerHeight(newHeight);
					});
				}
			}, 500);

		},
		InitMap: function () {
			var $self = Themify;
			if ($('.themify_map').length > 0) {
				if (typeof google !== 'object' || typeof google.maps !== 'object') {
					if(!themify_vars.map_key){
						themify_vars.map_key = '';
					}
					$self.LoadAsync('//maps.googleapis.com/maps/api/js?v=3.exp&callback=Themify.MapCallback&key='+themify_vars.map_key, false, true, true);
				} else {
					if( themify_vars.isCached && themify_vars.isCached === 'enable' ) {
						google.maps = { __gjsload__: function() { return; } };
						$self.LoadAsync('//maps.googleapis.com/maps/api/js?v=3.exp&callback=Themify.MapCallback&key='+themify_vars.map_key, false, true, true);
					} else {
						$self.MapCallback();
					}
				}
			}
		},
		MapCallback: function () {
			var $maps = $('.themify_map');
			$maps.each(function ($i) {
				var $data = JSON.parse(window.atob($(this).data('map'))),
						address = $data.address,
						zoom = parseInt($data.zoom),
						type = $data.type,
						scroll = $data.scroll,
						drag = $data.drag,
						node = this;
				var delay = $i * 1000;
				setTimeout(function () {
					var geo = new google.maps.Geocoder(),
						latlng = new google.maps.LatLng(-34.397, 150.644),
						mapOptions = {
							zoom: zoom,
							center: latlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP,
							scrollwheel: scroll,
							draggable: drag
						};
					switch (type.toUpperCase()) {
						case 'ROADMAP':
							mapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
							break;
						case 'SATELLITE':
							mapOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
							break;
						case 'HYBRID':
							mapOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
							break;
						case 'TERRAIN':
							mapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
							break;
					}

					var map = new google.maps.Map(node, mapOptions),
							revGeocoding = $(node).data('reverse-geocoding') ? true : false;

					google.maps.event.addListenerOnce( map, 'idle', function(){
						$( 'body' ).trigger( 'themify_map_loaded', [ $(node), map ] );
					});

					/* store a copy of the map object in the dom node, for future reference */
					$(node).data( 'gmap_object', map );

					if (revGeocoding) {
						var latlngStr = address.split(',', 2),
								lat = parseFloat(latlngStr[0]),
								lng = parseFloat(latlngStr[1]),
								geolatlng = new google.maps.LatLng(lat, lng),
								geoParams = {'latLng': geolatlng};
					} else {
						var geoParams = {'address': address};
					}

					geo.geocode(geoParams, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							var position = revGeocoding ? geolatlng : results[0].geometry.location;
							map.setCenter(position);
							var marker = new google.maps.Marker({
								map: map,
								position: position
							}),
							info = $(node).data('info-window');
							if (undefined !== info) {
								var contentString = '<div class="themify_builder_map_info_window">' + info + '</div>',
									infowindow = new google.maps.InfoWindow({
										content: contentString
									});

								google.maps.event.addListener(marker, 'click', function () {
									infowindow.open(map, marker);
								});
							}
						}
					});
				}, delay);
			});
		},
		LoadAsync: function (src, callback, version, defer, test) {
                    function hash(str){
                        var hash = 0;
                        for (var i = 0; i < str.length; i++) {
                            var char = str.charCodeAt(i);
                            hash = ((hash<<5)-hash)+char;
                            hash = hash & hash; // Convert to 32bit integer
                        }
                        return hash;
                    }
                    var id = hash(src), // Make script path as ID
                            existElemens = document.getElementById(id);
			if (existElemens) {
				if (callback) {
					if (test) {
                                            var callbackTimer = setInterval(function () {
                                                    var call = false;
                                                    try {
                                                            call = test.call();
                                                    } catch (e) {
                                                    }

                                                    if (call) {
                                                        clearInterval(callbackTimer);
                                                        callback.call();
                                                    }
                                            }, 100);
					} else {
						setTimeout(callback, 110);
					}
				}
				return;
			}
                        else if(test){
                            try {
                                if(test.call()){
                                    if(callback){
                                        callback.call();
                                    }
                                    return;
                                }
                            } catch (e) {
                            }
                        }
			var s, r, t;
			r = false;
			s = document.createElement('script');
			s.type = 'text/javascript';
			s.id = id;
			s.src = !version && 'undefined' !== typeof tbLocalScript ? src + '?version=' + tbLocalScript.version : src;
			if (!defer) {
				s.async = true;
			}
			else {
				s.defer = true;
			}
			s.onload = s.onreadystatechange = function () {
				if (!r && (!this.readyState || this.readyState === 'complete'))
				{
					r = true;
					if (callback) {
						callback();
					}
				}
			};
			t = document.getElementsByTagName('script')[0];
			t.parentNode.insertBefore(s, t);
		},
		LoadCss: function (href, version, before, media, callback) {
			var fullHref = version ? href + '?version=' + version : href;
			if ($("link[href='" + fullHref + "']").length > 0) {
				return;
			}
			var doc = window.document;
			var ss = doc.createElement("link");
			var ref;
			if (before) {
				ref = before;
			}
			else {
				var refs = (doc.body || doc.getElementsByTagName("head")[ 0 ]).childNodes;
				ref = refs[ refs.length - 1];
			}

			var sheets = doc.styleSheets;
			ss.rel = "stylesheet";
			ss.href = fullHref;
			// temporarily set media to something inapplicable to ensure it'll fetch without blocking render
			ss.media = "only x";
			ss.async = 'async';

			// Inject link
			// Note: `insertBefore` is used instead of `appendChild`, for safety re: http://www.paulirish.com/2011/surefire-dom-element-insertion/
			ref.parentNode.insertBefore(ss, (before ? ref : ref.nextSibling));
			// A method (exposed on return object for external use) that mimics onload by polling document.styleSheets until it includes the new sheet.
			var onloadcssdefined = function (cb) {
				var resolvedHref = ss.href;
				var i = sheets.length;
				while (i--) {
					if (sheets[ i ].href === resolvedHref) {
						if( callback ) {
							callback();
						}
						return cb();
					}
				}
				setTimeout(function () {
					onloadcssdefined(cb);
				});
			};

			// once loaded, set link's media back to `all` so that the stylesheet applies once it loads
			ss.onloadcssdefined = onloadcssdefined;
			onloadcssdefined(function () {
				ss.media = media || "all";
			});
			return ss;
		},
		checkFont:function(font) {
			// Maakt een lijst met de css van alle @font-face items.
			if($.inArray(font,this.fonts)){
				return true;
			}
			if(this.fonts.length===0){
				var o = [],
				sheets = document.styleSheets,
				rules = null,
				i = sheets.length, j;
				while( 0 <= --i ){
					rules = sheets[i].cssRules || sheets[i].rules || [];
					j = rules.length;

					while( 0 <= --j ){
						if(rules[j].style) {
							var fontFamily = '';
							if(rules[j].style.fontFamily){
								fontFamily = rules[j].style.fontFamily;
							}
							else{
								fontFamily = rules[j].style.cssText.match(/font-family\s*:\s*([^;\}]*)\s*[;}]/i);
								if(fontFamily){
									fontFamily = fontFamily[1];
								}
							}
							if(fontFamily===font){
								return true;
							}
							if(fontFamily){
                                                            o.push(fontFamily);
							}
						}
					}
				}
				this.fonts = $.unique( o );
			}
			return $.inArray(font,this.fonts);
		},
		video: function () {
			if ($('.themify_video_desktop a').length > 0) {
				if (typeof flowplayer === 'undefined') {
					this.LoadAsync(themify_vars.url + '/js/flowplayer-3.2.4.min.js', this.videoCalback);
				}
				else {
					this.videoCalback();
				}
			}
		},
		videoCalback: function () {
			$('.themify_video_desktop a').each(function () {
				flowplayer(
					$(this).attr('id'),
					themify_vars.url + "/js/flowplayer-3.2.5.swf",
					{
						clip: {autoPlay: false}
					}
				);
			});
		},
		lightboxCallback: function ($el, $args) {
			this.LoadAsync(themify_vars.url + '/js/themify.gallery.js', function () {
				Themify.GalleryCallBack($el, $args);
			}, null, null, function () {
				return ('undefined' !== typeof ThemifyGallery);
			});
		},
		InitGallery: function ($el, $args) {
			var lightboxConditions = typeof themifyScript === 'object' && ((themifyScript.lightbox.lightboxContentImages && $(themifyScript.lightbox.contentImagesAreas).length>0) || $(themifyScript.lightbox.lightboxSelector).length > 0);
                            if(!lightboxConditions){
                                lightboxConditions = typeof themifyScript === 'object' && themifyScript.lightbox.lightboxGalleryOn && ($(themifyScript.lightbox.lightboxContentImagesSelector).length > 0 || (typeof themifyScript.lightbox.gallerySelector!=='undefined' && $(themifyScript.lightbox.gallerySelector).length > 0));
                            }
			var self = this;
			if( lightboxConditions ) {
				this.LoadCss(themify_vars.url + '/css/lightbox.css', null);
				this.LoadAsync(themify_vars.url + '/js/lightbox.js', function () {
					Themify.lightboxCallback($el, $args);
				}, null, null, function () {
					return ('undefined' !== typeof $.fn.magnificPopup);
				});
			}
			if ( $('.module.module-gallery,.module.module-image').length > 0) {
				
				if( $( '.gallery-masonry' ).length > 0) {
					this.LoadAsync(themify_vars.includesURL + 'js/imagesloaded.min.js', function () {
						self.LoadAsync(themify_vars.includesURL + 'js/masonry.min.js', function () {
							$( '.gallery-masonry' ).imagesLoaded(function() {
								$( '.gallery-masonry' ).masonry({
									itemSelector: '.gallery-item',
									columnWidth: 1,
									originLeft : ! $( 'body' ).hasClass( 'rtl' )
								}); 
                                                                if(!lightboxConditions){
                                                                    $('body').addClass('themify_lightbox_loaded').removeClass('themify_lightboxed_images');
                                                                }
							});
						}, null, null, function () {
							return ('undefined' !== typeof $.fn.masonry);
						});
					}, null, null, function () {
						return ('undefined' !== typeof $.fn.imagesLoaded);
					});
				}
                                else if(!lightboxConditions){
                                        $('body').addClass('themify_lightbox_loaded').removeClass('themify_lightboxed_images');
                                    }
			} else if(!lightboxConditions){
				$('body').addClass('themify_lightbox_loaded').removeClass('themify_lightboxed_images');
			}
                        
		},
		GalleryCallBack: function ($el, $args) {
			if (!$el) {
				$el = $(themifyScript.lightboxContext);
			}
			$args = !$args && themifyScript.extraLightboxArgs ? themifyScript.extraLightboxArgs : {};
			ThemifyGallery.init({'context': $el, 'extraLightboxArgs': $args});
			$('body').addClass('themify_lightbox_loaded').removeClass('themify_lightboxed_images');
		},
		isPageHasBuilderContent: function () {
			var check_builder = $('.themify_builder_content').filter(function () {
				return $.trim($(this).html().toString()).length > 0;
			});
			return check_builder.length;
		}
	};

	Themify.Init();

}(jQuery, window, document));