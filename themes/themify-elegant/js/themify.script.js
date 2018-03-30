/*!
* FitText.js 1.1
* @link https://github.com/petewarman/FitText.js/
*/
!function(a){a.fn.fitText=function(b){var c,d=a.extend({minFontSize:Number.NEGATIVE_INFINITY,maxFontSize:Number.POSITIVE_INFINITY,lineCount:1,scale:100},b);return this.each(function(){var b=a(this);b.css({"white-space":"nowrap",position:"absolute",width:"auto"}),c=parseFloat(b.width())/parseFloat(b.css("font-size")),b.css({position:"",width:"","white-space":""});var e=function(){b.css("font-size",Math.max(Math.min(d.scale/100*d.lineCount*b.width()/c-d.lineCount,parseFloat(d.maxFontSize)),parseFloat(d.minFontSize)) - 1)};e(),a(window).on("resize.fittext orientationchange.fittext",e)})}}(jQuery);

;// Themify Theme Scripts - http://themify.me/

// Initialize object literals
var FixedHeader = {},
	LayoutAndFilter = {};

/////////////////////////////////////////////
// jQuery functions					
/////////////////////////////////////////////
(function($){

// Initialize carousels //////////////////////////////
function createCarousel(obj) {
	obj.each(function() {
		var $this = $(this);
		$this.carouFredSel({
			responsive : true,
			prev : '#' + $this.data('id') + ' .carousel-prev',
			next : '#' + $this.data('id') + ' .carousel-next',
			pagination : {
				container : '#' + $this.data('id') + ' .carousel-pager'
			},
			circular : true,
			infinite : true,
			swipe: true,
			scroll : {
				items : 1,
				fx : $this.data('effect'),
				duration : parseInt($this.data('speed'))
			},
			auto : {
				play : !!('off' != $this.data('autoplay')),
				timeoutDuration : 'off' != $this.data('autoplay') ? parseInt($this.data('autoplay')) : 0
			},
			items : {
				visible : {
					min : 1,
					max : 1
				},
				width : 222
			},
			onCreate : function() {
				$this.closest('.slideshow-wrap').css({
					'visibility' : 'visible',
					'height' : 'auto'
				});
				var $testimonialSlider = $this.closest('.testimonial.slider');
				if( $testimonialSlider.length > 0 ) {
					$testimonialSlider.css({
						'visibility' : 'visible',
						'height' : 'auto'
					});
				}
				$(window).resize();
			}
		});
	});
}

// Test if touch event exists //////////////////////////////
function is_touch_device() {
	return $('body').hasClass('touch');
}

// Scroll to Element //////////////////////////////
function themeScrollTo(offset) {
	$('body,html').animate({ scrollTop: offset }, 800);
}

// Heading resize
function apply_fittext( el ) {
	el.contents().wrapAll( '<span style="display: block; line-height: 100%; word-wrap: normal !important;" />' );
	el.find( '> span' )
		.fitText({ maxFontSize : el.css( 'font-size' ) })
		.fitText({ maxFontSize : el.css( 'font-size' ) }); // applying it twice fixes the issue of text breaking with some fonts.
	el.css( 'visibility', 'visible' );
}

// Fixed Header /////////////////////////
FixedHeader = {

	lastHeight: 0,
	headerHeight: 0,

	init: function() {
		if( '' != themifyScript.fixedHeader ) {
			this.activate();
			$(window).on('scroll touchstart.touchScroll touchmove.touchScroll', this.activate);
		}
	},
	activate: function() {
		var $window = $(window),
			scrollTop = $window.scrollTop(),
			fixedHeaderDefaultHeight = is_touch_device() ? 10 : 71,
			$headerwrap = $('#headerwrap');

		if ( FixedHeader.lastHeight != 0 ) {
			FixedHeader.headerHeight = FixedHeader.lastHeight;
		} else {
			FixedHeader.headerHeight = $headerwrap.height() - fixedHeaderDefaultHeight;
		}

		if( scrollTop > FixedHeader.headerHeight ) {
			if ( ! $headerwrap.hasClass('fixed-header') ) {
				FixedHeader.lastHeight = Math.floor( FixedHeader.headerHeight );
				$('#pagewrap').css('paddingTop', FixedHeader.lastHeight);
				$headerwrap.addClass('fixed-header');
				$('#header').addClass('header-on-scroll');
				$('body').addClass('fixed-header-on');
			}
		} else {
			if ( $headerwrap.hasClass('fixed-header') ) {
				FixedHeader.lastHeight = 0;
				$('#pagewrap').css('paddingTop', '');
				$('#headerwrap').removeClass('fixed-header');
				$('#header').removeClass('header-on-scroll');
				$('body').removeClass('fixed-header-on');
			}
		}
	}
};

// Entry Filter /////////////////////////
LayoutAndFilter = {
	init: function() {
		if ( $('body').hasClass('masonry-enabled') ) {
			themifyScript.disableMasonry = '';
		} else {
			themifyScript.disableMasonry = 'disable-masonry';
		}
		if ( 'disable-masonry' != themifyScript.disableMasonry ) {
			$('#loops-wrapper.grid4,#loops-wrapper.grid3,#loops-wrapper.grid2,.portfolio.loops-wrapper').prepend('<div class="grid-sizer">').prepend('<div class="gutter-sizer">');
			this.filter();
		}
	},
	filter: function(){
		var $filter = $('.post-filter');
		if ( $filter.find('a').length > 0 && 'undefined' !== typeof $.fn.isotope ){
			$filter.find('li').each(function(){
				var $li = $(this),
					$entries = $li.parent().next(),
					cat = $li.attr('class').replace( /(current-cat)|(cat-item)|(-)|(active)/g, '' ).replace( ' ', '' );
				if ( $entries.find('.portfolio-post.cat-' + cat).length <= 0 ) {
					$li.remove();
				}
			});

			$filter.show().on('click', 'a', function(e) {
				e.preventDefault();
				var $li = $(this).parent(),
					$entries = $li.parent().next();
				if ( $li.hasClass('active') ) {
					$li.removeClass('active');
					$entries.isotope( {
						filter: '.portfolio-post',
						isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
					} );
				} else {
					$li.siblings('.active').removeClass('active');
					$li.addClass('active');
					$entries.isotope( {
						filter: '.cat-' + $li.attr('class').replace( /(current-cat)|(cat-item)|(-)|(active)/g, '' ).replace( ' ', '' ),
						isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
					} );
				}
			} );
		}
	},
	layout: function(){
		if ( 'disable-masonry' != themifyScript.disableMasonry ) {
			$('.loops-wrapper.portfolio').isotope({
				masonry: {
					columnWidth: '.grid-sizer',
					gutter: '.gutter-sizer'
				},
				transformsEnabled: false,
				itemSelector : '.portfolio-post',
				isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
			}).addClass('masonry-done');
			
			$('.masonry').isotope({
				itemSelector: '.item',
				isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
			});
		
			$('#loops-wrapper.grid4,#loops-wrapper.grid3,#loops-wrapper.grid2').not('.portfolio-taxonomy,.portfolio').isotope({
				masonry: {
					columnWidth: '.grid-sizer',
					gutter: '.gutter-sizer'
				},
				itemSelector: '#loops-wrapper > article',
				isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
			}).addClass('masonry-done');
		}
	}
};

// DOCUMENT READY
$(document).ready(function() {

	var $body = $('body'), $window = $(window);
		
		  //add cuszomtize styles in widgettitle:after
		$('.widgettitle').each(function(){
			var $align = $(this).css('text-align');
			$align = $align==='center'?'left: 50%;margin-left: -.8em;':($align==='right'?'left: auto;right:0;':'');
			$('<style type="text/css">.widgettitle:after{border-color:'+$(this).css('color')+';'+$align+';}</style>').appendTo('head');
		});
		
	// Initialize color animation
	if ( 'undefined' !== typeof $.fn.animatedBG ) {
		themifyScript.colorAnimationSet = themifyScript.colorAnimationSet.split(',');
		themifyScript.colorAnimationSpeed = parseInt( themifyScript.colorAnimationSpeed, 10 );
		$('.animated-bg').animatedBG({
			colorSet: themifyScript.colorAnimationSet,
			speed: themifyScript.colorAnimationSpeed
		});
	}

	/////////////////////////////////////////////
	// Scroll to row when a menu item is clicked.
	/////////////////////////////////////////////
	if ( 'undefined' !== typeof $.fn.themifyScrollHighlight ) {
		$body.themifyScrollHighlight();
	}

	/////////////////////////////////////////////
	// Fixed header
	/////////////////////////////////////////////
	if ( '' != themifyScript.fixedHeader ) {
		FixedHeader.init();
	}

	/////////////////////////////////////////////
	// Initialize Layout
	/////////////////////////////////////////////
	LayoutAndFilter.init();

	/////////////////////////////////////////////
	// Scroll to top
	/////////////////////////////////////////////
	$('.back-top a').on('click', function(e){
		e.preventDefault();
		themeScrollTo(0);
	});

	/////////////////////////////////////////////
	// Toggle main nav on mobile
	/////////////////////////////////////////////
	if( $( 'body' ).hasClass( 'touch' ) && typeof jQuery.fn.themifyDropdown != 'function' ) {
		Themify.LoadAsync(themify_vars.url + '/js/themify.dropdown.js', function(){
			$( '#main-nav' ).themifyDropdown();
		});
	}

	var $overlay = $( '<div class="body-overlay">' );
		$body.append( $overlay );
	// Release spacing taken by mobile menu
	$window.on('debouncedresize', function(){
		$.UIkit.offcanvas.hide();
	}).on('hide.uk.offcanvas', function(){
		$overlay.removeClass( 'body-overlay-on' );
	});
		
	$('#menu-icon-close').on('click', function(e){
		e.preventDefault();
		$.UIkit.offcanvas.hide();
		$overlay.removeClass( 'body-overlay-on' );
	});
		$body.delegate('#menu-icon','click',function(){
			$overlay.addClass( 'body-overlay-on' );
		}).on( 'click.themify touchend.themify', '.body-overlay', function () {
			$.UIkit.offcanvas.hide();
			$overlay.removeClass( 'body-overlay-on' );
		} );

		// Hack the Builder row scrollTo anchor issue with off-canvas menu
		$('#main-nav').find( 'a[href*="#"]' ).not( 'a[href="#"]').on( 'click', function (e) {
			if ( $body.hasClass('uk-offcanvas-page') ) {
				$('html').css('marginTop', 0);
			}
		}); 
	/////////////////////////////////////////////
	// Reset slide nav width
	/////////////////////////////////////////////
	if ( $(window).width() < 780 ) {
		$('#main-nav').addClass('scroll-nav');
	}
	$(window).resize(function(){
		if ( $(window).width() > 780 ) {
			$('body').removeAttr('style');
			$('#main-nav').removeClass('scroll-nav');
		} else {
			$('#main-nav').addClass('scroll-nav');
		}
	});
	

});

// WINDOW LOAD
$(window).load(function() {

	/////////////////////////////////////////////
	// Carousel initialization
	/////////////////////////////////////////////
		if($('.slideshow').length>0){
			if(!$.fn.carouFredSel){
				Themify.LoadAsync(themify_vars.url+'/js/carousel.js',function(){
					createCarousel($('.slideshow'));
				});
			}
			else{
			   createCarousel($('.slideshow'));
			}
		}
	/////////////////////////////////////////////
	// Entry Filter Layout
	/////////////////////////////////////////////
	LayoutAndFilter.layout();

	/////////////////////////////////////////////
	// Heading Resize
	/////////////////////////////////////////////
	$( themifyScript.fittext_selector ).each(function(){
		var thiz = $(this);
		if( undefined == thiz.attr( 'class' ) ) {
			WebFont.load({
				google: {
					families: [themifyScript[thiz.prop('tagName').toLowerCase() + "_font"]]
				},
				active : function(){
					apply_fittext( thiz );
				},
				inactive : function(){ // fail-safe: in case font fails to load, use the fallback font and apply the effect.
					apply_fittext( thiz );
				}
			});
		}
	});
	
	// EDGE MENU //
	jQuery(function ($) {
		$("#main-nav li").on('mouseenter mouseleave dropdown_open', function (e) {
			if ($('ul', this).length) {
				var elm = $('ul:first', this);
				var off = elm.offset();
				var l = off.left;
				var w = elm.width();
				var docW = $(window).width();
				var isEntirelyVisible = (l + w <= docW);

				if (!isEntirelyVisible) {
					$(this).addClass('edge');
				} else {
					$(this).removeClass('edge');
				}

			}
		});
	});
	
});
	
})(jQuery);