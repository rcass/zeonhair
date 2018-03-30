    var ThemifyLiveStyling;
(function($){

	'use strict';

	var api = themifybuilderapp;

	api.mode = 'visual';
	api.Frontend = {
		slidePanelOpen: true,
		activeBreakPoint: 'desktop',
		hideSlidingPanel: function() {
			if (this.slidePanelOpen) {
				this.slidePanelOpen = false;
				$('.slide_builder_module_panel').trigger('click');
			}
		},
		hideModulesControl: function() {
			$('.themify_builder_module_front_overlay').hide();
			$('.themify_builder_dropdown_front').hide();
		},
		hideColumnsBorder: function() {
			//$('.themify_builder_col').css('border', 'none');
		},
		showSlidingPanel: function() {
			if (!this.slidePanelOpen) {
				this.slidePanelOpen = true;
				$('.slide_builder_module_panel').trigger('click');
			}
		},
		showColumnsBorder: function() {
		   // $('.themify_builder_col').css('border', '');
		},
		responsiveFrame: {
			$el: null,
			contentWindow: null,
			init: function() {
				this.$el = $('#themify_builder_site_canvas_iframe').contents();
				this.contentWindow = document.getElementById('themify_builder_site_canvas_iframe').contentWindow;
				var $frame = this.$el,
					eventToUse = 'true' == themifyBuilder.isTouch ? 'touchend' : 'mouseenter mouseleave';

				if ($('#wpadminbar', $frame).length){
					$('#wpadminbar', $frame).remove();
				}
				$frame.find('body').addClass('themify_builder_active themify_builder_front themify_builder_responsive_frame_body');
				$('body').addClass('builder-active-breakpoint--' + api.Frontend.activeBreakPoint );

				$frame.on(eventToUse, '.themify_builder_module_front', function(event) {
						api.Views.Modules.visual.prototype.modHover(event);
					})
					.on('click', '.themify_builder_option_column,.js--themify_builder_module_styling,.themify_builder_style_row', function(event) {
						event.preventDefault();
						var cid = $(this).closest('[data-cid]').data('cid'),
							$selector = $('[data-cid="'+ cid +'"]');

						if ( $(this).hasClass('js--themify_builder_module_styling') ) {
							$selector.children('.module_menu_front').find('.js--themify_builder_module_styling').trigger('click');
						} else if ( $(this).hasClass('themify_builder_style_row') ) {
							$selector.children('.themify_builder_row_top').find('.themify_builder_style_row').trigger('click');
						} else if ( $(this).hasClass('themify_builder_option_column') ) {
							$selector.children('.themify_builder_column_action').find('.themify_builder_option_column').trigger('click');
						}
					})
					.on('dblclick', '.themify_builder .active_module', function(event){
						event.preventDefault();
						var cid = $(this).data('cid'),
							$selector = $('[data-cid="'+ cid +'"]');

						$selector.children('.module_menu_front').find('.js--themify_builder_module_styling').trigger('click');
					});

				$('body').on('builder_toggle_frontend', this.toggleBuilder);
				// Enable iframe scrolling
				this.doIframeScrolling();
			},
			syncTimeout: null,
			doSync: function() {
				if ('desktop' !== api.Frontend.activeBreakPoint) {
					clearTimeout(this.syncTimeout);
					this.syncTimeout = setTimeout(function(){
						api.Frontend.responsiveFrame.sync();
					}, 300);
				}
			},
			sync: function(reverse) {
				reverse = reverse || false;
				if (reverse) {
					api.Frontend.responsiveFrame.$el.find('.themify_builder_content').not('.not_editable_builder').each(function() {

					});
				} else {
					$('.themify_builder_content').not('.not_editable_builder').each(function(i) {
						api.Frontend.responsiveFrame.$el.find('.themify_builder_content').not('.not_editable_builder').get(i).innerHTML = this.innerHTML;
					});
				}
				api.Frontend.responsiveFrame.contentWindow.ThemifyBuilderModuleJs.isResponsiveFrame = true;
				api.Frontend.responsiveFrame.contentWindow.ThemifyBuilderModuleJs.bindEvents();
			},
			toggleBuilder: function(event, is_edit) {
				if (is_edit) {
					$('.themify_builder_module_panel').removeClass('themify_builder_panel_state_inline');
					$('.themify_builder_responsive_switcher_item .themify_builder_popup_menu_wrapper')
						.find('.breakpoint-desktop').parent().addClass('selected').siblings().removeClass('selected');
					$('.themify_builder_front_panel').removeClass('builder-disable-module-draggable');
					$('.themify_builder_module_panel .themify_builder_module').draggable('enable');
					api.Frontend.responsiveFrame.doIframeScrolling();
				} else {
					$('.themify_builder_site_canvas, .themify_builder_workspace_container').removeAttr('style');
					$(window).off('scroll.themifybuilderresponsive');
				}
			},
			doIframeScrolling: function() {
				// Sync iframe scrolling with parent window.
				var $window = $(window);
				$window.on('scroll.themifybuilderresponsive', function(){ 
					api.Frontend.responsiveFrame.contentWindow.scrollTo(0, $window.scrollTop());
				});
			}
		},
		importLayoutButton: function() {
			$('.themify_builder_content')
				.not('.not_editable_builder')
				.append( '<a href="#" class="tb-import-layout-button">' 
					+ themifyBuilderCommon.text_import_layout_button + '</a>' );

			$( 'body' ).on( 'click', '.tb-import-layout-button', function( e ) {
				e.preventDefault();
				api.Views.Nav.prototype.loadLayout(e);
			} );
		},
	};

	api.Views.register_row( 'visual', {
		template: wp.template('builder_visual_row_item'),
		initialize: function() {
			this.listenTo(this.model, 'create:element', this.createEl);
			this.listenTo(this.model, 'custom:change', this.duplicateCallback);
			this.listenTo(this, 'component:duplicate', this.duplicateCallback);
			this.listenTo(this.model, 'change:styling', this.updateEl);
			this.listenTo(this.model, 'custom:restorehtml', this.restoreHtml);
		},
		createEl: function( markup ) {
			var $rawHtml = $(markup).filter('.themify_builder_row');
			this.$el.children('.row_inner_wrapper').children('.row_inner')
				.prepend( $rawHtml.find('style') );
			$rawHtml.children('.builder_row_cover').insertAfter( this.$el.children('.themify_builder_row_top') );
			this.$el.attr('class', $rawHtml.attr('class')).addClass( this.attributes.class );
			if ( this.$el.children('.row-slider').length ) {
				this.$el.children('.row-slider').replaceWith( $rawHtml.children('.row-slider') );
			} else {
				this.$el.prepend($rawHtml.children('.row-slider'));
			}
		},
		duplicateCallback: function() {
			var that = this,
				$container = this.$el.closest('[data-postid]');
			api.render_visual.call(this).done(function(){
				api.Utils.loadContentJs();
				api.vent.trigger('dom:builder:change');

				if ( _.isEmpty( ThemifyBuilderCommon.undoManager.getStartData() ) ) {
					api.vent.trigger('dom:observer:end', $container);
				} else {
					api.vent.trigger('dom:observer:end', $container, { cid: that.model.cid, value: that.model.toJSON() } );
				}
			});
		},
		updateEl: function() {
			var that = this,
				builder_id = that.getBuilderID(),
				sendData = that.model.toJSON();

			sendData['row_order'] = that.model.cid;

			if ( _.has( sendData, 'cols' ) ) delete sendData.cols;

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data: {
					action: 'tfb_load_row_partial',
					post_id: builder_id,
					nonce: themifyBuilder.tfb_load_nonce,
					row: JSON.stringify(sendData)
				},
				beforeSend: function(xhr) {
					ThemifyBuilderCommon.showLoader('show');
					ThemifyBuilderCommon.Lightbox.close();
				},
				success: function(data) {
					var $rawElems = $(data.html).filter('.themify_builder_row'),
						$rowContent = that.$el.children('.row_inner_wrapper').children('.row_inner');

					$rowContent.children('style').remove();
					$rowContent.prepend( $rawElems.find('style') );
					that.$el.attr('class', $rawElems.attr('class'));
					that.$el.addClass(_.extend({}, _.result(that, 'attributes')).class);

					that.$el.children('.builder_row_cover').replaceWith($rawElems.children('.builder_row_cover'));
					
					api.Utils.loadContentJs(that.$el);

					// Load google font style
					if ('undefined' !== typeof WebFont && data.gfonts.length > 0) {
						WebFont.load({
							google: {
								families: data.gfonts
							}
						});
					}

					ThemifyBuilderCommon.columnDrag($('.current_selected_row'),false);
					ThemifyBuilderCommon.showLoader('hide');

					api.Frontend.responsiveFrame.doSync();
										
					// Hook
					$('body').trigger('builder_load_row_partial', that.$el);

					api.vent.trigger('dom:observer:end', that.$el.closest('[data-postid]'), { cid: that.model.cid, value: { styling: that.model.get('styling') } });

				}
			});
		},
		restoreHtml: function( rememberedEl ) {
			var $currentEl = $('.current_selected_row');
			if ( $currentEl.length ) {
				var $columns = $currentEl.children('.row_inner_wrapper').children('.row_inner').children('.themify_builder_row_content').detach(),
					$rememberedEl = $(rememberedEl);

				$rememberedEl.find('.themify_builder_row_content').remove(); // don't need this
				$currentEl[0].outerHTML = $rememberedEl[0].outerHTML;
				this.setElement( $('.current_selected_row') );
				$('.current_selected_row').children('.row_inner_wrapper').children('.row_inner').append($columns);
			}
		}
	});

	api.Views.register_subrow( 'visual', {
		template: wp.template('builder_visual_sub_row_item'),
		initialize: function() {
			this.listenTo(this.model, 'create:element', this.createEl);
			this.listenTo(this.model, 'custom:change', api.render_visual.bind(this));
			this.listenTo(this, 'component:duplicate', this.duplicateCallback);
			this.listenTo(this.model, 'change:styling', this.updateEl);
			this.listenTo(this.model, 'custom:restorehtml', this.restoreHtml);
		},
		createEl: function( markup ) {
			var $rawHtml = $(markup).filter('.themify_builder_sub_row');
			this.$el.prepend( $rawHtml.find('style') );
			$rawHtml.children('.builder_row_cover').insertAfter( this.$el.children('.themify_builder_sub_row_top') );
			this.$el.attr('class', $rawHtml.attr('class')).addClass( this.attributes.class );
			if ( this.$el.children('.sub_row-slider').length ) {
				this.$el.children('.sub_row-slider').replaceWith( $rawHtml.children('.sub_row-slider') );
			} else {
				this.$el.prepend($rawHtml.children('.sub_row-slider'));
			}
		},
		duplicateCallback: function() {
			var that = this,
				$container = this.$el.closest('[data-postid]');
			api.render_visual.call(this).done(function(){
				api.Utils.loadContentJs();
				api.vent.trigger('dom:builder:change');
				
				if ( _.isEmpty( ThemifyBuilderCommon.undoManager.getStartData() ) ) {
					api.vent.trigger('dom:observer:end', $container);
				} else {
					api.vent.trigger('dom:observer:end', $container, { cid: that.model.cid, value: that.model.toJSON() } );
				}
			});
		},
		updateEl: function() {
			var that = this,
				builder_id = that.getBuilderID(),
				sendData = that.model.toJSON();

			sendData['sub_row_order'] = that.model.cid;
			sendData['row_order'] = that.$el.closest('.themify_builder_row').data('cid');
			sendData['col_order'] = that.$el.parents('.themify_builder_col').data('cid');

			if ( _.has( sendData, 'cols' ) ) delete sendData.cols;

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data: {
					action: 'tfb_render_subrow',
					post_id: builder_id,
					nonce: themifyBuilder.tfb_load_nonce,
					sub_row_data: JSON.stringify(sendData)
				},
				beforeSend: function(xhr) {
					ThemifyBuilderCommon.showLoader('show');
					ThemifyBuilderCommon.Lightbox.close();
				},
				success: function(data) {
					var $rawElems = $(data.html).filter('.themify_builder_sub_row');

					that.$el.children('style').remove();
					that.$el.prepend( $rawElems.find('style') );
					that.$el.attr('class', $rawElems.attr('class'));
					that.$el.addClass(_.extend({}, _.result(that, 'attributes')).class);

					that.$el.children('.builder_row_cover').replaceWith($rawElems.children('.builder_row_cover'));
					
					api.Utils.loadContentJs(that.$el);


					//ThemifyBuilderCommon.columnDrag($('.current_selected_row'),false);
					ThemifyBuilderCommon.showLoader('hide');

					api.Frontend.responsiveFrame.doSync();

					api.vent.trigger('dom:observer:end', that.$el.closest('[data-postid]'), { cid: that.model.cid, value: { styling: that.model.get('styling') } });
				
				}
			});
		},
		restoreHtml: function( rememberedEl ) {
			var $currentEl = $('.current_selected_sub_row');
			if ( $currentEl.length ) {
				var $columns = $currentEl.children('.sub_row_inner_wrapper').children('.themify_builder_sub_row_content').detach(),
					$rememberedEl = $(rememberedEl);

				$rememberedEl.children('.sub_row_inner_wrapper').children('.themify_builder_sub_row_content').remove(); // don't need this
				$currentEl[0].outerHTML = $rememberedEl[0].outerHTML;
				this.setElement( $('.current_selected_sub_row') );
				$('.current_selected_sub_row').children('.sub_row_inner_wrapper').append($columns);
			}
		}
	});

	api.Views.register_column( 'visual', {
		template: wp.template('builder_visual_column_item'),
		attributes: function() {
			var classes = 'column' === this.model.get('component_name') ? 'module_column module_column_' + this.model.cid : 'sub_column sub_column_' + this.model.cid;
			return {
				'class' : 'themify_builder_col ' + classes + ' ' + this.model.get('grid_class'),
				'style' : 'width:' + this.model.get('grid_width') + '%',
				'data-cid' : this.model.cid
			};
		},
		initialize: function() {
			this.listenTo(this.model, 'create:element', this.createEl);
			this.listenTo(this.model, 'custom:change', this.duplicateCallback);
			this.listenTo(this.model, 'change:styling', this.updateEl);
			this.listenTo(this.model, 'custom:restorehtml', this.restoreHtml);
		},
		createEl: function( markup ) {
			var $rawHtml = $(markup);
			this.$el.children('.tb-column-inner').prepend( $rawHtml.find('.tb-column-inner').html().replace(/&nbsp;/gi,'') );
			this.$el.addClass( $rawHtml.attr('class').replace('empty-column', '') );
			if ( this.$el.children('.builder_row_cover').length ) {
				this.$el.children('.builder_row_cover').replaceWith( $rawHtml.children('.builder_row_cover') );
			} else {
				this.$el.prepend($rawHtml.children('.builder_row_cover'));
			}
			if ( this.$el.children('.col-slider').length ) {
				this.$el.children('.col-slider').replaceWith( $rawHtml.children('.col-slider') );
			} else {
				this.$el.prepend($rawHtml.children('.col-slider'));
			}
		},
		duplicateCallback: function() {
			var that = this,
				$container = this.$el.closest('[data-postid]');
			api.render_visual.call(this).done(function(){
				api.Utils.loadContentJs();
				api.vent.trigger('dom:builder:change');

				if ( _.isEmpty( ThemifyBuilderCommon.undoManager.getStartData() ) ) {
					api.vent.trigger('dom:observer:end', $container);
				} else {
					api.vent.trigger('dom:observer:end', $container, { cid: that.model.cid, value: that.model.toJSON() } );
				}
			});
		},
		updateEl: function() {
			var that = this,
				$base = that.$el.parent();

			var $selectedRow = 'column' === this.model.get('component_name') ? that.$el.closest('.themify_builder_row') : that.$el.closest('.themify_builder_sub_row'),
				colDataPlainObject = this.model.toJSON();

				if ( _.has( colDataPlainObject, 'modules') ) delete colDataPlainObject.modules

				colDataPlainObject['column_order'] = that.$el.data('cid');
				colDataPlainObject['grid_class'] = ThemifyBuilderCommon.getColClassName( that.$el );

			if ( 'column' === this.model.get('component_name') ) {
				
			} else {
				colDataPlainObject['sub_row_order'] = $selectedRow.add($selectedRow.siblings()).filter('.themify_builder_sub_row, .active_module').data('cid');
				colDataPlainObject['row_order'] = that.$el.closest('.themify_builder_row').data('cid');
				colDataPlainObject['col_order'] = that.$el.parents('.themify_builder_col').data('cid');
			}

			return $.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data: {
					action: 'tfb_render_column',
					cid: that.model.cid,
					nonce: themifyBuilder.tfb_load_nonce,
					column_data: JSON.stringify( colDataPlainObject )
				},
				beforeSend: function(xhr) {
					ThemifyBuilderCommon.showLoader('show');
					ThemifyBuilderCommon.Lightbox.close();
				},
				success: function( data ) {
					var $rawColumn = $(data.html),
						prepend = $rawColumn.find('.tb-column-inner').html().replace(/&nbsp;/gi,''),
						$modules = that.$el.children('.tb-column-inner').children('.themify_module_holder').detach();
					
					that.$el.children('.builder_row_cover').replaceWith($rawColumn.children('.builder_row_cover'));
					that.$el.children('.tb-column-inner').html( prepend ).append($modules);
					that.$el.attr('class', $rawColumn.attr('class').replace('empty-column', ''));
					that.$el.addClass(_.extend({}, _.result(that, 'attributes')).class);

					$base.children().removeClass('first last');
					$base.children().first().addClass('first');
					$base.children().last().addClass('last');
					ThemifyBuilderCommon.columnDrag($base,false);
                                        api.Utils.loadContentJs(that.$el);
					ThemifyBuilderCommon.showLoader('hide');
					api.Frontend.responsiveFrame.doSync();

					api.vent.trigger('dom:observer:end', that.$el.closest('[data-postid]'), { cid: that.model.cid, value: { styling: that.model.get('styling') } });
				}
			});
		},
		restoreHtml: function( rememberedEl ) {
			var $currentEl = $('.current_selected_column');
			if ( $currentEl.length ) {
				var $modules = $currentEl.children('.tb-column-inner').children('.themify_module_holder').detach(),
					$rememberedEl = $(rememberedEl);

				$rememberedEl.find('.themify_module_holder').remove(); // don't need this
				$currentEl[0].outerHTML = $rememberedEl[0].outerHTML;
				this.setElement( $('.current_selected_column') );
				$('.current_selected_column').children('.tb-column-inner').append($modules);
			}
		}
	});

	api.Views.register_module( 'visual', {
		draggedNotTapped : false,
		template: wp.template('builder_visual_module_item'),
		attributes: function() {
			return {
				'class' : 'themify_builder_module_front module-' + this.model.get('mod_name') +' active_module clearfix',
				'data-mod-name' : this.model.get('mod_name'),
				'data-cid' : this.model.cid
			};
		},
		events: {
			'hover' : 'modHover',
		},
		initialize: function() {
			this.listenTo(this.model, 'create:element', this.createEl);
			this.listenTo(this.model, 'custom:change', this.updateEl);
			this.listenTo(this, 'component:duplicate', this.duplicateCallback);
			this.listenTo(this.model, 'custom:restoredata', this.restoreData);
			this.listenTo(this.model, 'custom:restorehtml', this.restoreHtml);
		},
		createEl: function( markup ) {
			this.el.insertAdjacentHTML('beforeend', markup);
		},
		duplicateCallback: function() {
			var that = this,
				$container = this.$el.closest('[data-postid]');
			api.render_visual.call(this).done(function(){
				api.Utils.loadContentJs();
				api.vent.trigger('dom:builder:change');

				if ( _.isEmpty( ThemifyBuilderCommon.undoManager.getStartData() ) ) {
					api.vent.trigger('dom:observer:end', $container);
				} else {
					api.vent.trigger('dom:observer:end', $container, { cid: that.model.cid, value: that.model.toJSON() } );
				}
			});
		},
		updateEl: function() {
			var that = this;
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data: {
					action: 'tfb_load_module_partial',
					tfb_post_id: this.$el.closest('.themify_builder_content').data('postid'),
					tfb_cid: this.model.cid,
					tfb_module_slug: this.model.get('mod_name'),
					tfb_module_data: JSON.stringify(this.model.get('mod_settings')),
					tfb_load_nonce: themifyBuilder.tfb_load_nonce,
					rules: api.liveStylingInstance.isModuleExists(this.model.get('mod_name'))?0:1
				},
				beforeSend: function(xhr) {
					if ( that.model.get('previewOnly') ) {
						ThemifyBuilderCommon.showLoader('lightbox-preview');
					} else {
						ThemifyBuilderCommon.showLoader('show');

						if( $('.tb-import-layout-button').length ) {
							$('.tb-import-layout-button').remove();
						}
					}
				},
				success: function(data) {
					
					that.el.insertAdjacentHTML('beforeend', data.html);
					that.$el.find('.module').addClass('module_' + that.model.cid );
					
					if(data.rules){
					   api.liveStylingInstance.addStyleDate(that.model.get('mod_name'),data.rules);
					}

					// Load google font style
					if ('undefined' !== typeof WebFont && data.gfonts.length > 0) {
						WebFont.load({
							google: {
								families: data.gfonts
							}
						});
					}

					api.Utils.loadContentJs(that.$el);

					if ( that.model.get('previewOnly') ) {
						ThemifyBuilderCommon.showLoader('lightbox-preview-hide');
						that.$el.addClass('current_selected_module');
					} else {
						ThemifyBuilderCommon.showLoader('spinhide');
						api.Frontend.responsiveFrame.doSync();

						api.vent.trigger('dom:observer:end', that.$el.closest('[data-postid]'), { cid: that.model.cid, value: { mod_settings: that.model.get('mod_settings') } });
					}
					that.model.unset('previewOnly', {silent: true});

					// Hook
					$('body').trigger('builder_load_module_partial', that.$el);
				}
			});

			return this;
		},
		modHover: function(e) {
			var $this = $(e.currentTarget);
			if ('touchend' == e.type) {
				if (!this.draggedNotTapped) {
					this.draggedNotTapped = false;
					var $row = $this.closest('.themify_builder_row'),
						$col = $this.closest('.themify_builder_col'),
						$mod = $this.closest('.themify_builder_module'),
						index = 'row_' + $row.index();
					if ($col.length > 0) {
						index += '_col_' + $col.index();
					}
					if ($mod.length > 0) {
						index += '_mod_' + $mod.index();
					}
					if (this.menuTouched[index]) {
						$this.find('.themify_builder_dropdown_front').stop(false, true).css('z-index', '').hide();
						$this.find('.themify_builder_dropdown_front ul').stop(false, true).hide();
						$this.find('.themify_builder_dropdown').stop(false, true).hide();
						this.menuTouched = [];
					} else {
						var $builderCont = $('.themify_builder_content');
						$builderCont.find('.themify_builder_dropdown_front').stop(false, true).css('z-index', '').hide();
						$builderCont.find('.themify_builder_dropdown_front ul').stop(false, true).hide();
						$builderCont.find('.themify_builder_dropdown').stop(false, true).hide();
						$this.find('.themify_builder_dropdown_front').stop(false, true).css('z-index', '999').show();
						$this.find('.themify_builder_dropdown_front ul').stop(false, true).show();
						this.menuTouched = [];
						this.menuTouched[index] = true;
					}
				}
			} else if (e.type == 'mouseenter') {
				$this.find('.themify_builder_module_front_overlay').stop(false, true).show();
				$this.find('.themify_builder_dropdown_front').stop(false, true).show();
			} else if (e.type == 'mouseleave') {
				$this.find('.themify_builder_module_front_overlay').stop(false, true).hide();
				$this.find('.themify_builder_dropdown_front').stop(false, true).hide();
			}
		},
		tempData: null,
		restoreData: function() {
			this.tempData = this.model.get('mod_settings');
		},
		restoreHtml: function( rememberedEl ) {
			var $currentEl = $('.current_selected_module');
			if ( $currentEl.length ) {
				$currentEl[0].outerHTML = rememberedEl;
				this.setElement( $('.current_selected_module') );
				this.model.set({mod_settings: this.tempData }, {silent: true });
				this.tempData = null;
			}
		}
	});

	api.bootstrap = function(callback) {
		// collect all jobs
		var jobs = [];
		_.each( api.Models.Registry.items, function( model, cid ){
			var data = model.toJSON();
			if ( 'column' === data.elType && _.has( data, 'modules' ) ) delete data.modules;
			if ( 'row' === data.elType && _.has( data, 'cols' ) ) delete data.cols;
			if ( 'module' === data.elType && _.has( data, 'cols') ) delete data.cols;
			jobs.push({ jobID: cid, data: data });
		});

		this.batch_rendering(jobs, 0, 250, callback);
	};

	api.batch_rendering = function( jobs, current, size, callback ) {
		if(current >= jobs.length){
			// load callback
			if ($.isFunction(callback)) {
				callback.call(this);
			}
			return;
		}else{
			var smallerJobs = jobs.slice(current, current+size);
			this.render_element(smallerJobs).done(function(){
				api.batch_rendering(jobs, current += size, size, callback);
			});
		}
	};

	api.render_element = function( constructData ) {
		// send json data
		return $.ajax({
			type: "POST",
			url: themifyBuilder.ajaxurl,
			dataType: 'json',
			data: {
				action: 'tfb_render_element',
				batch: JSON.stringify(constructData),
				tfb_load_nonce: themifyBuilder.tfb_load_nonce
			},
			success: function(data) {
				_.each( data, function( obj, cid ) {
					var model = api.Models.Registry.lookup( cid );
					model.trigger('create:element', obj.markup);
				});
			}
		});
	};

	api.render_visual = function() {
		// collect all jobs
		var constructData = [],
			cid = this.$el.data('cid'),
			model = api.Models.Registry.lookup( cid );

		if ( model ) constructData.push({ jobID: cid, data: model.toJSON() });
		this.$el.find('[data-cid]').each(function(){
			cid = $(this).data('cid');
			model = api.Models.Registry.lookup( cid );
			if ( model ) constructData.push({ jobID: cid, data: model.toJSON() });
		});

		return api.render_element( constructData );
	};

	api.toggleFrontEdit = function( event ) {
		var self = this,
			is_edit = 0;

		// remove lightbox if any
		if ($('#themify_builder_lightbox_parent').is(':visible')) {
			$('.builder_cancel_lightbox').trigger('click');
		}

		var location_url = window.location.pathname + window.location.search;
		// remove hash
		if (window.history && window.history.replaceState) {
			window.history.replaceState('', '', location_url);
		} else {
			window.location.href = window.location.href.replace(/#.*$/, '#');
		}

		var bids = $('.themify_builder_content').not('.not_editable_builder').map(function() {
			return $(this).data('postid') || null;
		}).get();

		// add body class
		if (!$('body').hasClass('themify_builder_active')) {
			is_edit = 1;
			$('.toggle_tf_builder a:first').text(themifyBuilder.toggleOff);
			$('.themify_builder_front_panel').slideDown();
		} else {
			$('.themify_builder_front_panel').slideUp();
			$('.toggle_tf_builder a:first').text(themifyBuilder.toggleOn);
			is_edit = 0;
		}

		if (is_edit == 0 && self.editing) {
			// confirm
			var reply = confirm(themifyBuilder.confirm_on_turn_off);
			if (reply) {
				api.Utils.saveBuilder(true).done(function(){
					self.toggleFrontEditAjax(is_edit, bids);
				});
			} else {
				self.toggleFrontEditAjax(is_edit, bids);
			}
		} else {
			self.toggleFrontEditAjax(is_edit, bids);
			self.editing = false;
		}

		if ('undefined' !== typeof event) {
			event.preventDefault();
		}
	};

	api.toggleFrontEditAjax = function( is_edit, bids ) {
		var self = this;
		$.ajax({
			type: "POST",
			url: themifyBuilder.ajaxurl,
			dataType: 'json',
			data: {
				action: 'tfb_toggle_frontend',
				tfb_post_ids: bids,
				tfb_load_nonce: themifyBuilder.tfb_load_nonce,
				state: is_edit
			},
			beforeSend: function(xhr) {
				ThemifyBuilderCommon.showLoader('show');
			},
			success: function(data) {

				if (!is_edit) {
					// Clear undo history
					ThemifyBuilderCommon.undoManager.instance.clear();
					api.Models.Registry.destroy();
					api.Instances.Builder = {};
				}

				if (is_edit) {
					$('body').addClass('themify_builder_active themify_builder_front');
					
					if ( data.length ) {
						api.render(data);
					} else {
						ThemifyBuilderCommon.showLoader('spinhide');
					}

					if( $( '.module_row ' ).length < 2 ) {
						api.Frontend.importLayoutButton();
					}
					api.Utils.checkUnload();
				} else {
					$('body').removeClass('themify_builder_active themify_builder_front');
						
					if (data.length > 0) {
						$.each(data, function(i, v) {
							var $target = $('#themify_builder_content-' + data[i].builder_id).empty();
							$target.get(0).innerHTML = $(data[i].markup).unwrap().get(0).innerHTML;
						});
					}

					window.onbeforeunload = null;
					ThemifyBuilderModuleJs.init();

					ThemifyBuilderCommon.showLoader('spinhide');
					$('body').trigger('builder_toggle_frontend', is_edit);
				}
				ThemifyBuilderCommon.columnDrag(null,false);
			}
		});
	};

	api.render = function( data ) {
		if (data.length > 0) {
			$.each(data, function(i, v) {
				var el = '#themify_builder_content-' + data[i].builder_id,
					$el = $(el),
					dataJSON = new api.Collections.Rows(data[i].builder_data);
				$el.wrapInner('<div class="themify_builder_content_remove_wrapper" />');
				api.Instances.Builder[i] = new api.Views.Builder({ el: el, collection: dataJSON, type: 'visual' });
				api.Instances.Builder[i].render();
				$el.find('.themify_builder_content_remove_wrapper').nextAll().addClass('hide-builder-content');
			});

			ThemifyBuilderCommon.showLoader('show');
			api.bootstrap(function(){
				$.each(data, function(i, v){
					var $el = $( '#themify_builder_content-' + data[i].builder_id );
					$el.find('.themify_builder_content_remove_wrapper').remove().end()
					.find('.hide-builder-content').removeClass('hide-builder-content');
				});
				ThemifyBuilderCommon.showLoader('spinhide');
				$('body').trigger('builder_toggle_frontend', true);
				api.Utils.loadContentJs();
				api.vent.trigger('dom:builder:change');
			});
		}
	};

	api.initFrontend = function() {
		api.Views.bindEvents();

		api.Forms.bindEvents();

		var modulePanel = new api.Views.ModulePanel({ el: '.themify_builder_module_panel' }),
			navTool = new api.Views.Nav( {el: '#wp-admin-bar-themify_builder'});

		var editorNav = new api.Views.EditorNavComponent({ el: '.builder_save_front_panel'});

		/**
		 * New instance created on lightbox open, destroyed on lightbox close
		 * @type ThemifyLiveStyling
		 */
		this.liveStylingInstance = new ThemifyLiveStyling();

		// Ajax Start action
		$(document).on('ajaxSend', function(){
			document.getElementsByClassName('themify-builder-front-save')[0].classList.add('disabled');
		}).on('ajaxComplete', function(){
			document.getElementsByClassName('themify-builder-front-save')[0].classList.remove('disabled');
		});
	};

	// Initialize Builder
	$('body').on('builderscriptsloaded.themify', function(e) {
		api.initFrontend();
		api.toggleFrontEdit(e);
		$('.toggle_tf_builder a:first').on('click', api.toggleFrontEdit.bind(api));
	})
	.on('builderiframeloaded.themify', function(e) {
		api.Frontend.responsiveFrame.init();
	});

	ThemifyLiveStyling = (function($, jss) {

		function ThemifyLiveStyling() {
			this.$context = $('#themify_builder_lightbox_parent');
			this.elmtSelector = '#builder_live_styled_elmt';
			this.isInit = false;
			this.style_tab = '.themify_builder_style_tab';
			this.selectorQueue = [];
			this.data  = [];
			var self = this;
			$('body').on('builderiframeloaded.themify', function(e) {
				self.iframe = api.Frontend.responsiveFrame.contentWindow;
				self.data = self.iframe.themify_module_styles;
				self.frameJss = self.iframe.jss;
				self.bindLightboxForm();
				$(document).trigger('tfb.live_styling.after_create', self);
			});
		}

		ThemifyLiveStyling.prototype.init = function($liveStyledElmt, currentStyleObj) {
			this.remove(); // remove previous live styling, if any

			this.$liveStyledElmt = $liveStyledElmt;

			if (typeof currentStyleObj === 'object') {
				this.currentStyleObj = currentStyleObj;
			} else {
				this.currentStyleObj = {};
			}

			//this.setLiveStyleSelector( this.$liveStyledElmt );

			this.setLiveStyledElmtID();
			this.isInit = true;
			$(document).trigger('tfb.live_styling.after_init', this);
		};

		ThemifyLiveStyling.prototype.setLiveStyleSelector = function( $el ) {
			var cid = _.isUndefined( $el.data('cid') ) ? $el.closest('[data-cid]').data('cid') : $el.data('cid'),
				model = api.Models.Registry.lookup( cid ),
				type = model.get('elType'),
				selector = '';
			
			if ( 'row' == type ) {
				selector = '.module_row_' + cid + '-' + cid;
			} else if ( 'module' == type ) {
				selector = '.' + model.get('mod_name') + '-' + cid + '-' + cid;
			} else if ( 'column' == type ) {
				selector = '.module_column_' + cid + '-' + cid + '-' + cid
			}
			
			this.elmtSelector = '.themify_builder ' + selector;
		};

		ThemifyLiveStyling.prototype.setLiveStyledElmtID = function() {
			this.$liveStyledElmt.prop('id', this.elmtSelector.substring(1));
		};

		ThemifyLiveStyling.prototype.unsetLiveStyledElmtID = function() {
			this.$liveStyledElmt.prop('id', false);
			$('#'+this.elmtSelector.substring(1)).prop('id',false);
		};
		
		ThemifyLiveStyling.prototype.isModuleExists = function($module) {
			return this.data['module-inner'] && this.data['module-inner'][$module]?true:false;
		};
		
		ThemifyLiveStyling.prototype.addStyleDate = function($module,$rules) {
				   
			if(!this.isModuleExists[$module]){
				if ( ! this.data['module-inner'] ) this.data['module-inner'] = {}; 
				this.data['module-inner'][$module] = $rules;
			}
		};

		/**
		 * Apply CSS rules to the live styled element.
		 *
		 * @param {Object} newStyleObj Object containing CSS rules for the live styled element.
		 * @param {Array} selectors List of selectors to apply the newStyleObj to (e.g., ['', 'h1', 'h2']).
		 */
		ThemifyLiveStyling.prototype.setLiveStyle = function(newStyleObj, selectors) {
			var self = this;
			if(!selectors){
				selectors = [''];
			}
			selectors.forEach(function(selector) {
				var fullSelector = self.elmtSelector;
				if (selector && selector.length > 0) {
					fullSelector += selector;
				}
				self.selectorQueue.push(fullSelector);
				jss.set(fullSelector, newStyleObj);
				self.frameJss.set(fullSelector, newStyleObj);

				//logging(fullSelector);
			});
			api.Frontend.responsiveFrame.doSync();

			function logging(fullSelector) {
			}
		};

		ThemifyLiveStyling.prototype.bindColors = function() {
			var self = this;
			this.$context.on('change', 'input.colordisplay', function() {
				var hexString = $(this).val().length > 0 ? '#' + $(this).val() : '',
					colorType = $(this).parent().find('.builderColorSelectInput').prop('name');

				$('body').trigger('themify_builder_color_picker_change', [colorType, hexString]);
			});
			this.$context.on('change', 'input.color_opacity', function() {
				var opacity = $(this).val(),
					hexString = "#" + $(this).parent().find('.colordisplay').prop('value'),
					colorType = $(this).parent().find('.builderColorSelectInput').prop('name');
					var patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
					var matches = patt.exec(hexString);
					var rgbaString = "rgba(" + parseInt(matches[1], 16) + ", " + parseInt(matches[2], 16) + ", " + parseInt(matches[3], 16) + ", " + opacity + ")";
				$(this).parent().find('.builderColorSelectInput').prop('value', $(this).parent().find('.colordisplay').prop('value') + "_" + opacity);
				$('body').trigger('themify_builder_color_picker_change', [colorType, rgbaString]);
			});
			$('body').on('themify_builder_color_picker_change', function(e, colorType, rgbaString) {
				if (colorType === 'cover_color') {
					self.addOrRemoveComponentOverlay(rgbaString,false);
				} else if(self.isInit) {
					
					var $el = $('input[name="'+colorType+'"]');
					if($el.next('.style_border').length>0 && $el.closest('.themify_builder_field').nextAll('.themify_builder_field').last().find('.style_apply_all_border').is(':checked')){
						return false;
					}
					var $data = self.getValue(colorType),
						$selector = $data.selector,
						$prop = {};
					if ($data) {
						$prop[ $data.prop ] = rgbaString;
						self.setLiveStyle($prop, $selector);
					}
				}
			});
		};
				
		ThemifyLiveStyling.prototype.overlayType = function() {
			var self = this;
			this.$context.on('change', 'input[name="cover_color-type"]', function() {
				if($(this).val()==='color'){
					$('input[name="cover_color"]',this.$context).prev().trigger('change');
				}
				else{
					$('#'+$(this).val()+'-gradient-type').trigger('change');
				}
			});
		};

		ThemifyLiveStyling.prototype.getSpecialTextSelectors = function() {
			return [' h1', ' h2', ' h3:not(.module-title)', ' h4', ' h5', ' h6'];
		};

		ThemifyLiveStyling.prototype.addOrRemoveComponentOverlay = function(rgbaString,$gradient) {
			var $overlayElmt = ThemifyLiveStyling.getComponentBgOverlay(this.$liveStyledElmt);

			if (!rgbaString.length) {
				$overlayElmt.remove();
				return;
			}
			var $isset = $overlayElmt.length!==0;
			if (!$isset) {
				$overlayElmt = $('<div/>', {
						class: 'builder_row_cover'
				});
			}
			$overlayElmt.data('color', rgbaString);
			if(!$gradient){
				 $overlayElmt.css({'background-image': '','background':rgbaString});
			}
			else{
				$overlayElmt.css({'background': rgbaString,'background-color':''});
			}
			if($isset){
				return;
			}
						
			var $elmtToInsertBefore = ThemifyLiveStyling.getComponentBgSlider(this.$liveStyledElmt);

			if (!$elmtToInsertBefore.length) {
				var selector = '';
				var componentType = ThemifyBuilderCommon.getComponentType(this.$liveStyledElmt);

				if (componentType === 'row') {
					selector = '.row_inner_wrapper';
				} else if (componentType === 'col' || componentType === 'sub-col') {
					selector = '.tb-column-inner'
				}

				$elmtToInsertBefore = this.$liveStyledElmt.children(selector);
			}

			$overlayElmt.insertBefore( $elmtToInsertBefore );
		};

		ThemifyLiveStyling.prototype.bindTextInputs = function() {
			var self = this;

			$('body').delegate(self.style_tab + ' input[type="text"]', 'keyup', function() {
				var $id = $(this).prop('id'),
					$data = self.getValue($id),
					$prop = {};
				if ($data) {
					var $val = $.trim($(this).val());
					if ($('#' + $id + '_unit').length > 0) {
						$val += $('#' + $id + '_unit').val() ? $('#' + $id + '_unit').val() : 'px';
					} else if ($(this).hasClass('style_border')) {
						if($(this).closest('.themify_builder_field').nextAll('.themify_builder_field').last().find('.style_apply_all_border').is(':checked')){
							return false;
						}
						$val = parseFloat($val);
						if($val){
							$(this).closest('.themify_builder_field').find('select').trigger('change');
							$val = $val.toString();
						}
						else{
							$val = '0';
						}
						$val += 'px';
						
					}
					$prop[ $data.prop ] = $val;
					self.setLiveStyle($prop, $data.selector);
				}
			});
			$('body').delegate(self.style_tab + ' [id$="_unit"]', 'change', function() {
				var $id = $(this).prop('id').replace('_unit', '');
				$('#' + $id).trigger('keyup');
			});
			$(document).on('tfb.live_styling.after_init',function(){
				setTimeout(function(){
					var self = api.liveStylingInstance;
					$(self.style_tab +' .style_apply_all_border:checked').each(function(){
						var $field =$(this).closest('.themify_builder_field').prevUntil('h4').last(),
							$input = $field.find('.style_border');
						if(!$input.val()){
							self.setApplyBorder($input.prop('id'),0,'width');
						}
						var $select = $field.find('select');
						self.setApplyBorder($select.prop('id'),$select.val(),'style');
					});
				},900);
				
			});			
		};

		ThemifyLiveStyling.prototype.bindRowWidthHeight = function() {
			var self = this;

			this.$context.on('change', 'input[name="row_width"],input[name="row_height"]', function() {
				var rowVal = self.getStylingVal('row_width'),
					val = $(this).val();
				if (val.length > 0) {
					if (rowVal.length > 0) {
						self.$liveStyledElmt.removeClass(rowVal);
					}
					self.setStylingVal($(this).prop('name'), val);
					self.$liveStyledElmt.addClass(val);
				} else {
					self.$liveStyledElmt.removeClass(rowVal);
				}
			});
		};

		ThemifyLiveStyling.prototype.removeAnimations = function(animationEffect, $elmt) {
			$elmt.removeClass(animationEffect + ' wow animated').css('animation-name', '');
		};

		ThemifyLiveStyling.prototype.addAnimation = function(animationEffect, $elmt) {
			$elmt.addClass(animationEffect + ' animated');
		};

		ThemifyLiveStyling.prototype.bindAnimation = function() {
			var self = this;

			this.$context.on('change', '#animation_effect', function() {
				var animationEffect = self.getStylingVal('animation_effect');

				if ($(this).val().length) {
					if (animationEffect.length) {
						self.removeAnimations(animationEffect, self.$liveStyledElmt);
					}

					animationEffect = $(this).val();

					self.setStylingVal('animation_effect', animationEffect);
					self.addAnimation(animationEffect, self.$liveStyledElmt);
				} else {
					self.removeAnimations(animationEffect, self.$liveStyledElmt);
				}
			});
		};

		ThemifyLiveStyling.prototype.bindAdditionalCSSClass = function() {
			var self = this;

			this.$context.on('keyup', '#custom_css_row, #custom_css_column, #add_css_text', function() {
				var id = this.id,
					className = self.getStylingVal(id);

				self.$liveStyledElmt.removeClass(className);

				className = $(this).val();

				self.setStylingVal(id, className);
				self.$liveStyledElmt.addClass(className);
			});
		};

		ThemifyLiveStyling.prototype.bindRowAnchor = function() {
			var self = this;

			this.$context.on('keyup', '#row_anchor', function() {
				var rowAnchor = self.getStylingVal('row_anchor');

				self.$liveStyledElmt.removeClass(self.getRowAnchorClass(rowAnchor));

				rowAnchor = $(this).val();

				self.setStylingVal('row_anchor', rowAnchor);
				self.$liveStyledElmt.addClass(self.getRowAnchorClass(rowAnchor));
			});
		};

		ThemifyLiveStyling.prototype.getRowAnchorClass = function(rowAnchor) {
			return rowAnchor.length > 0 ? 'tb_section-' + rowAnchor : '';
		};

		ThemifyLiveStyling.prototype.getStylingVal = function(stylingKey) {
			return this.currentStyleObj.hasOwnProperty(stylingKey) ? this.currentStyleObj[stylingKey] : '';
		};

		ThemifyLiveStyling.prototype.setStylingVal = function(stylingKey, val) {
			this.currentStyleObj[stylingKey] = val;
		};

		ThemifyLiveStyling.prototype.bindBackgroundMode = function() {
			var self = this;
			this.$context.on('change', '#background_repeat', function() {
				var previousVal = self.getStylingVal('background_repeat');

				var val = $(this).val();

				if (val.length > 0) {
					if (previousVal.length > 0) {
						self.$liveStyledElmt.removeClass(previousVal);
					}

					self.setStylingVal('background_repeat', val);
					self.$liveStyledElmt.addClass(val);
				} else {
					self.$liveStyledElmt.removeClass(previousVal);
				}
			});

		};

		ThemifyLiveStyling.prototype.bindBackgroundPosition = function() {
			var self = this;
			this.$context.on('change', '#background_position', function() {
				var previousVal = self.getStylingVal('background_position');

				var val = $(this).val();

				if (val.length > 0) {
					if (previousVal.length > 0) {
						self.$liveStyledElmt.removeClass(previousVal);
					}
					val = 'bg-position-' + val;
					self.setStylingVal('background_position', val);
					self.$liveStyledElmt.addClass(val);
				} else {
					self.$liveStyledElmt.removeClass(previousVal);
				}
			});

		};

		ThemifyLiveStyling.prototype.isWebSafeFont = function(fontFamily) {
			/**
			 *  Array containing the web safe fonts from the backend themify_get_web_safe_font_list().
			 *
			 * @type {Array}
			 */
			var webSafeFonts = themifyBuilder.webSafeFonts;

			return webSafeFonts.indexOf(fontFamily) !== -1;
		};

		ThemifyLiveStyling.prototype.bindBackgroundSlider = function() {
			function getBackgroundSlider(options) {
				return $.post(
					themifyBuilder.ajaxurl, {
						nonce: themifyBuilder.tfb_load_nonce,
						action: 'tfb_slider_live_styling',
						tfb_background_slider_data: options
					}
				);
			}

			var getOptions, insertBackgroundSliderToHTML, initBackgroundSlider;

			getOptions = function() {
				return {
					shortcode: encodeURIComponent($('#background_slider').val()),
					mode: $('#background_slider_mode').val(),
					size: $('#background_slider_size').val(),
					order: ThemifyBuilderCommon.getComponentOrder(this.$liveStyledElmt),
					type: ThemifyBuilderCommon.getComponentType(this.$liveStyledElmt)
				};
			}.bind(this);

			insertBackgroundSliderToHTML = function($backgroundSlider) {
				var liveStyledElmtType = ThemifyBuilderCommon.getComponentType(this.$liveStyledElmt);
				var bgCover = ThemifyLiveStyling.getComponentBgOverlay(this.$liveStyledElmt);

				if (bgCover.length) {
					bgCover.after($backgroundSlider);
				} else {
					if (liveStyledElmtType == 'row') {
						this.$liveStyledElmt.children('.themify_builder_row_top').after($backgroundSlider);
					} else {
						this.$liveStyledElmt.prepend($backgroundSlider);
					}
				}
			}.bind(this);

			initBackgroundSlider = function($bgSlider) {
				ThemifyBuilderModuleJs.backgroundSlider($bgSlider);
			};

			var self = this;

			this.$context.on('change', '#background_slider, #background_slider_mode,#background_slider_size', function() {
				ThemifyLiveStyling.removeBgSlider(self.$liveStyledElmt);

				if (!$('#background_slider').val().length) {
					return;
				}

				getBackgroundSlider(getOptions()).done(function(backgroundSlider) {
					if (backgroundSlider.length < 10) {
						return;
					}

					var $bgSlider = $(backgroundSlider);

					insertBackgroundSliderToHTML($bgSlider);
					initBackgroundSlider($($bgSlider.get(0)));
				});
			});
		};
                ThemifyLiveStyling.prototype.VideoOptions = function() {
			var self = this;

			this.$context.on('change', 'input[name="background_video_options"]', function() {
				if (!self.isInit) {
					return;
				}
                                var video = self.$liveStyledElmt.children('.big-video-wrap').first(),
                                    el = '',
                                    type = '';
                                if(video.hasClass('themify_ytb_wrapper')){
                                    el = video.closest('[data-fullwidthvideo]');
                                    if(el.length>0){
                                        type = 'ytb';
                                    }
                                }
                                else if(video.hasClass('themify-video-vmieo')){
                                    el = $f(video.children('iframe')[0]);
                                    if(el){
                                        type = 'vimeo';
                                    }
                                }
                                else{
                                    
                                    el = video.closest('[data-fullwidthvideo]').data('video');
                                    if(el){
                                        el = el.getPlayer();
                                        type = 'local';
                                    }
                                }
                              
                                if($(this).val()==='mute'){
                                    if($(this).is(':checked')){
                                        if(type==='ytb'){
                                           el.ThemifyYTBMute()
                                        }
                                        else if(type === 'vimeo'){
                                            el.api('setVolume', 0);
                                        }
                                        else if(type === 'local'){
                                            el.muted(true);
                                        }
                                    }
                                    else{
                                        if(type==='ytb'){
                                            el.ThemifyYTBUnmute();
                                        }
                                        else if(type === 'vimeo'){
                                            el.api('setVolume', 1);
                                        }
                                        else if(type === 'local'){
                                            el.muted(false);
                                        }
                                    }
                                }
                                else if($(this).val()==='unloop'){
                                    if($(this).is(':checked')){
                                        if(type === 'vimeo'){
                                            el.api('setLoop', 0);
                                        }
                                        else if(type === 'local'){
                                            el.loop(false);
                                        }
                                    }
                                    else{
                                        if(type === 'vimeo'){
                                            el.api('setLoop', 1);
                                        }
                                        else if(type === 'local'){
                                            el.loop(true);
                                        }
                                    }
                                }
			});

		};
		ThemifyLiveStyling.prototype.bindBackgroundTypeRadio = function() {
			var self = this;

			this.$context.on('change', 'input[name="background_type"]', function() {
				if (!self.isInit) {
					return;
				}

				var bgType = ThemifyBuilderCommon.getCheckedRadioInGroup($(this), self.$context).val();
				if (bgType === 'image' || bgType === 'gradient') {
					ThemifyLiveStyling.removeBgSlider(self.$liveStyledElmt);
					ThemifyLiveStyling.removeBgVideo(self.$liveStyledElmt);
					if(bgType === 'image'){
						self.setLiveStyle({
								'background-image': 'none'
						}, ['']);
					}
					else{
						bgType+= '-gradient-angle';
					}
				} 
				else if (bgType === 'video') {
					ThemifyLiveStyling.removeBgSlider(self.$liveStyledElmt);
				} else {
					ThemifyLiveStyling.removeBgVideo(self.$liveStyledElmt);
					// remove bg image
					self.setLiveStyle({
						'background-image': 'none'
					}, ['']);
				}
				self.$context.find('#background_' + bgType).trigger('change');
			});

		};
				
		ThemifyLiveStyling.prototype.bindBackgroundGradient = function($id,$val,$return) {
			var self = this;
				if (self.isInit) {
					var $data = self.getValue($id),
						$prop = {};
					if ($data) {
						$val = $val.replace(/background-image:/ig,'').split(";");
						var $vendors = {'moz':0,'webkit':4,'o':2,'ms':3},
							$pref = getVendorPrefix(),
							$val = $vendors[$pref] && $val[$vendors[$pref]]?$val[$vendors[$pref]]:$val[4];
							if(!$return){
								$prop[ $data.prop ] = $val;
								self.setLiveStyle($prop, $data.selector);
							}
							else{
								return $val;
							}
					}
				}
				
				function getVendorPrefix(){
					if ( typeof getVendorPrefix.pre === 'undefined' ) {
						
						var styles = window.getComputedStyle(document.documentElement, '');
						
						getVendorPrefix.pre = (Array.prototype.slice
						  .call(styles)
						  .join('')
						  .match(/-(moz|webkit|ms)-/) || (styles.OLink === '' && ['', 'o'])
						  )[1];
						
					}
					return getVendorPrefix.pre;
				}

		};
		/**
		 * Binds module layout + styling options to produce live styling on change.
		 */
		ThemifyLiveStyling.prototype.bindModuleLayout = function() {
			var self = this;

			var layoutStylingOptions = {
				'layout_accordion': '> ul.ui.module-accordion',
				'layout_callout': '',
				'layout_feature': '',
				'style_image': '',
				'layout_menu': 'ul.ui.nav:first',
				'layout_post': '> .builder-posts-wrap',
				'layout_tab': ''
			};

			// TODO: Optimize for speed by having one .on('click') handler
			Object.keys(layoutStylingOptions).forEach(function(layoutSelectorKey) {
				self.$context.on('click', '#' + layoutSelectorKey + ' > a', function() {
					var selectedLayout = $(this).attr('id');

					var $elmtToApplyTo = self.$liveStyledElmt;

					if (layoutStylingOptions[layoutSelectorKey] !== '') {
						$elmtToApplyTo = self.$liveStyledElmt.find(layoutStylingOptions[layoutSelectorKey]);
					}

					var prevLayout = self.getStylingVal(layoutSelectorKey);

					if (layoutSelectorKey === 'layout_feature') {
						selectedLayout = 'layout-' + selectedLayout;
						prevLayout = 'layout-' + prevLayout;
					}

					$elmtToApplyTo
						.removeClass(prevLayout)
						.addClass(selectedLayout);

					if (layoutSelectorKey === 'layout_feature') {
						selectedLayout = selectedLayout.substr(7);
					}

					self.setStylingVal(layoutSelectorKey, selectedLayout);
				});
			});

		};

		/**
		 * Binds module radio buttons to produce live styling on change.
		 */
		ThemifyLiveStyling.prototype.bindModuleRadio = function() {
			var self = this;

			var RadioStylingOptions = {
				'buttons_style': '> .module-buttons',
				'buttons_size': '> .module-buttons',
				'icon_style': '> .module-icon',
				'icon_size': '> .module-icon',
				'icon_arrangement': '> .module-icon'
			};

			Object.keys(RadioStylingOptions).forEach(function(radioSelectorKey) {
				self.$context.on('change', '#' + radioSelectorKey + ' input[type="radio"]', function() {
									if(self.$liveStyledElmt){
					var selectedRadio = $(this).val(),
						$elmtToApplyTo = RadioStylingOptions[radioSelectorKey] !== '' ?
						self.$liveStyledElmt.find(RadioStylingOptions[radioSelectorKey]) : self.$liveStyledElmt,
						prevLayout = self.getStylingVal(radioSelectorKey);

					$elmtToApplyTo.removeClass(prevLayout).addClass(selectedRadio);
					self.setStylingVal(radioSelectorKey, selectedRadio);
									}
				});
			});

		};

		ThemifyLiveStyling.prototype.bindModuleColor = function() {
			var self = this;

			/**
			 * A key-value pair.
			 * Key represents the ID of the element which should be listened to.
			 * Value represents the selector to the element which the live styling should be applied to.
			 */
			var colorStylingOptions = {
				'color_accordion': '> ul.ui.module-accordion',
				'color_box': '> .module-box-content.ui',
				'color_button': '> .ui.builder_button',
				'color_callout': '',
				'color_menu': 'ul.ui.nav:first',
				'mod_color_pricing_table': ['', '> .module-pricing-table-header', '.module-pricing-table-button'],
				'color_tab': '',
				'icon_color_bg': '> .module-icon i',
				'button_color_bg': '> .module-buttons a'
			};

			var colorStylingSelector = Object.keys(colorStylingOptions).reduce(function(selectors, selector, index) {
				var result = selectors;

				if (index !== 0) {
					result += ',';
				}

				result += '#' + selector + ' > a';

				return result;
			}, '');

			self.$context.on('click', colorStylingSelector, function() {
				var $this = $(this),
					colorSelectorKey = $this.parent().attr('id'),
					selectedColor = $(this).attr('id'),
					$builder = $this.closest('.themify_builder_row_js_wrapper');

				var elmtToApplyToSelector = colorStylingOptions[colorSelectorKey];

				if (!Array.isArray(elmtToApplyToSelector)) {
					elmtToApplyToSelector = [elmtToApplyToSelector];
				}

				var $elmtsToApplyTo = $([]);

				elmtToApplyToSelector.forEach(function(selector) {
					if (selector === '') {
						$elmtsToApplyTo = $elmtsToApplyTo.add(self.$liveStyledElmt);
					} else {
						$elmtsToApplyTo = $elmtsToApplyTo.add(
							self.$liveStyledElmt.find(selector)
						);
					}

				});

				if ($builder.length > 0) {
					var $index = $this.closest('.themify_builder_row').index(),
						realKey = colorSelectorKey;
					colorSelectorKey += '_' + $index;
					$elmtsToApplyTo = $($elmtsToApplyTo[$index]);
				}

				var prevColor = self.getStylingVal(colorSelectorKey);
				if (!prevColor && $builder.length > 0) {
					var $rows = self.getStylingVal($builder.attr('id'));
					if ($rows[$index] && $rows[$index][realKey]) {
						prevColor = $rows[$index][realKey];
					}
				}

				$elmtsToApplyTo
					.removeClass(prevColor)
					.addClass(selectedColor);

				self.setStylingVal(colorSelectorKey, selectedColor);
			});
		};		
		ThemifyLiveStyling.prototype.setApplyBorder = function(id,value,type) {
			var $data = this.getValue(id),
				$prop = {};
			if ($data) {
				$prop[ 'border-'+type ] = type==='width'?(value>0?value+'px':'0'):value;
				this.setLiveStyle($prop, $data.selector);
			}
		};
		
		ThemifyLiveStyling.prototype.bindChangesEvent = function() {
			var self = this;
 
			$('body').delegate(self.style_tab + ' select,' + self.style_tab + ' input[type="radio"],' + self.style_tab + ' .themify-builder-uploader-input', 'change', function() {
				if(!self.isInit){
					return;
				}
			   
				var $id = $(this).is('select') || $(this).hasClass('themify-builder-uploader-input') ? $(this).prop('id') : $(this).parent('.tfb_lb_option').prop('id'),
					$data = self.getValue($id),
					$prop = {};
				if ($data) {
					var $val = $(this).val(),
						$selector = $data.selector;
					if( ! $val ) {
						return;
					}
					if ($(this).hasClass('font-family-select')) {
						if ($val !== 'default' && !self.isWebSafeFont($val)) {
							ThemifyBuilderCommon.loadGoogleFonts([$val.split(' ').join('+') + ':400,700:latin,latin-ext'], self.iframe);
						} else if ($val === 'default') {
							$val = '';
						}
					} else if ($(this).hasClass('themify-builder-uploader-input')) {
						if ($(this).closest('.themify_builder_input').find('.thumb_preview').length > 0) {
							$val = $val ? 'url(' + $val + ')' : 'none';
						} else {
							self.$liveStyledElmt.data('fullwidthvideo', $val);
							if ($val.length > 0) {
								ThemifyBuilderModuleJs.fullwidthVideo(self.$liveStyledElmt);
							} else {
								ThemifyLiveStyling.removeBgVideo(self.$liveStyledElmt);
							}
							return false;
						}
					}
					else if($(this).is('select') && $(this).find('[value="dashed"]').length>0){
						if(($val!=='none' && !parseInt($(this).closest('.themify_builder_input').find('.style_border').val())) || $(this).closest('.themify_builder_field').nextAll('.themify_builder_field').last().find('.style_apply_all_border').is(':checked')){
							return false;
						}
						
					}
					$prop[ $data.prop ] = $val;
					self.setLiveStyle($prop, $selector);
				}
			});
		};
		
		ThemifyLiveStyling.prototype.getValue = function($id) {
			if (this.isInit) {
				var type = ThemifyBuilderCommon.getComponentType(this.$liveStyledElmt);
				if (!this.data[type]) {
					if (type === 'module-inner') {
						return false;
					}
					type = 'raw';
				}
				if (type === 'module-inner') {
					var $module = $(this.style_tab).data('module');
					return $module && this.data[type][$module] && this.data[type][$module][$id] ? this.data[type][$module][$id] : false;
				} else {
					return this.data[type] && this.data[type][$id] ? this.data[type][$id] : false; //raw, column
				}
			}
			return false;
		};

		ThemifyLiveStyling.prototype.bindModuleApppearance = function() {
			var self = this;

			var getSelectedAppearances = function(appearanceSelector) {
				var selectedAppearances = self.$context.find('#' + appearanceSelector + ' > input:checked')
					.map(function(i, checkbox) {
						return $(checkbox).val();
					})
					.toArray();

				return selectedAppearances.join(' ');
			};

			var appearanceStylingOptions = {
				'accordion_appearance_accordion': '> ul.ui.module-accordion',
				'appearance_box': '> .module-box-content.ui',
				'appearance_button': '> .ui.builder_button',
				'appearance_callout': '',
				'appearance_image': '',
				'according_style_menu': 'ul.ui.nav:first',
				'mod_appearance_pricing_table': ['', '> .module-pricing-table-header', '.module-pricing-table-button'],
				'tab_appearance_tab': ''
			};

			var appearanceStylingSelector = Object.keys(appearanceStylingOptions).reduce(
				function(selectors, selector, index) {
					var result = selectors;

					if (index !== 0) {
						result += ',';
					}

					result += '#' + selector + ' > input';

					return result;
				},
				'');

			self.$context.on('change', appearanceStylingSelector, function() {
				var $this = $(this);
				var appearanceSelectorKey = $this.parent().attr('id');

				var elmtToApplyToSelector = appearanceStylingOptions[appearanceSelectorKey];

				if (!Array.isArray(elmtToApplyToSelector)) {
					elmtToApplyToSelector = [elmtToApplyToSelector];
				}

				var $elmtsToApplyTo = $([]);

				elmtToApplyToSelector.forEach(function(selector) {
					if (selector === '') {
						$elmtsToApplyTo = $elmtsToApplyTo.add(self.$liveStyledElmt);
					} else {
						$elmtsToApplyTo = $elmtsToApplyTo.add(
							self.$liveStyledElmt.find(selector)
						);
					}

				});

				var prevAppearances = self.getStylingVal(appearanceSelectorKey)
					.split('|')
					.join(' ');

				var selectedAppearances = getSelectedAppearances(appearanceSelectorKey);

				if (appearanceSelectorKey === 'mod_appearance_pricing_table') {
					prevAppearances += ' ' + prevAppearances.split(' ').join('|');
					selectedAppearances += ' ' + selectedAppearances.split(' ').join('|');
				}

				$elmtsToApplyTo
					.removeClass(prevAppearances)
					.addClass(selectedAppearances);

				self.setStylingVal(
					appearanceSelectorKey,
					getSelectedAppearances(appearanceSelectorKey).split(' ').join('|')
				);
			});
		};

		ThemifyLiveStyling.prototype.bindLightboxForm = function() {

			// "Styling" tab live styling
			this.bindChangesEvent();
			this.bindColors();
			this.bindTextInputs();
			this.bindBackgroundMode();
			this.bindBackgroundPosition();
			this.bindAnimation();
			this.bindBackgroundSlider();
			this.bindBackgroundTypeRadio();
                        this.VideoOptions();
			this.overlayType();

			// "Module options tab" live styling
			this.bindModuleLayout();
			this.bindModuleColor();
			this.bindModuleApppearance();
			this.bindModuleRadio();
			this.bindRowAnchor();
			this.bindAdditionalCSSClass();
			this.bindRowWidthHeight();
		};

		/**
		 * Resets or removes all styling (both live and from server).
		 */
		ThemifyLiveStyling.prototype.resetStyling = function() {

			var selectorsWithTriggerRequired = [
				'#background_repeat',
				'#row_anchor',
				'#custom_css_row',
				'#custom_css_column',
				'#add_css_text',
				'#animation_effect',
				'input[name=row_height]',
				'input[name=row_width]'
			];

			$(selectorsWithTriggerRequired.join(','), this.$context).trigger('change');

			var $styleTag = ThemifyLiveStyling.getComponentStyleTag(this.$liveStyledElmt);
			$styleTag.remove();

			// Removes row overlay.
			this.addOrRemoveComponentOverlay('');

			this._removeAllLiveStyles();

			// TODO: removing bg slider needs more testing.
		};

		/**
		 * Returns component's background cover element wrapped in jQuery.
		 *
		 * @param {jQuery} $component
		 * @returns {jQuery}
		 */
		ThemifyLiveStyling.getComponentBgOverlay = function($component) {
			return $component.children('.builder_row_cover');
		};

		/**
		 * Returns component's background slider element wrapped in jQuery.
		 *
		 * @param {jQuery} $component
		 * @returns {jQuery}
		 */
		ThemifyLiveStyling.getComponentBgSlider = function($component) {
			return $component.children('.row-slider, .col-slider, .sub-col-slider, .sub_row-slider');
		};

		/**
		 * Returns component's background video element wrapped in jQuery.
		 *
		 * @param {jQuery} $component
		 * @returns {jQuery}
		 */
		ThemifyLiveStyling.getComponentBgVideo = function($component) {
			return $component.children('.big-video-wrap');
		};

		/**
		 * Returns component's <style> tag.
		 *
		 * @param {jQuery} $component
		 * @returns {jQuery|null}
		 */
		ThemifyLiveStyling.getComponentStyleTag = function($component) {
			var type = ThemifyBuilderCommon.getComponentType($component);

			var $styleTag = null;

			if (type === 'row') {
				$styleTag = $component.find('.row_inner').children('style');
			} else if (type === 'col') {
				$styleTag = $component.find('.tb-column-inner').children('style');
			} else if (type === 'subrow') {
				$styleTag = $component.children('style');
			} else if (type === 'sub-col') {
				$styleTag = $component.children('style');
			} else if (type === 'module-inner') {
				$styleTag = $component.siblings('style');
			}

			return $styleTag;
		};

		/**
		 * Removes background slider if there is any in $component.
		 *
		 * @param {jQuery} $component
		 */
		ThemifyLiveStyling.removeBgSlider = function($component) {
			ThemifyLiveStyling.getComponentBgSlider($component)
				.add($component.children('.backstretch'))
				.remove();

			$component.css({
				'position': '',
				'background': '',
				'z-index': ''
			});
		};

		/**
		 * Removes background video if there is any in $component.
		 *
		 * @param {jQuery} $component
		 */
		ThemifyLiveStyling.removeBgVideo = function($component) {
			ThemifyLiveStyling.getComponentBgVideo($component).remove();
		};

		/**
		 * Removes live styling from the live styled element.
		 *
		 * @private
		 */
		ThemifyLiveStyling.prototype._removeAllLiveStyles = function() {
			var self = this;

			var selectors = this.getSpecialTextSelectors().concat(' a');

			selectors.forEach(function(selector) {
				// Remove styles for special selectors.
				jss.remove(self.elmtSelector + selector);
				if ( self.frameJss ) self.frameJss.remove(self.elmtSelector + selector);
			});

			self.selectorQueue.forEach(function(selector) {
				// Remove styles for special selectors.
				jss.remove(selector);
				if ( self.frameJss ) self.frameJss.remove(selector);
			});
			self.selectorQueue = []; // reset the selectors

			// Remove styles for the selector.
			jss.remove(this.elmtSelector);
			if ( self.frameJss ) self.frameJss.remove(this.elmtSelector);
			api.Frontend.responsiveFrame.doSync();
		};

		ThemifyLiveStyling.prototype.remove = function() {
			if (!this.isInit) {
				return;
			}

			this._removeAllLiveStyles();

			this.unsetLiveStyledElmtID();

			this.$liveStyledElmt = null;
			this.currentStyleObj = null;
			this.isInit = false;
		};

		return ThemifyLiveStyling;
	})(jQuery, jss);
	
})(jQuery);