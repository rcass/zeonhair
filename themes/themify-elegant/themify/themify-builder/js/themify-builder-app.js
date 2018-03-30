window.themifybuilderapp = window.themifybuilderapp || {};
(function($){

	'use strict';

	// Serialize Object Function
	if ('undefined' === typeof $.fn.themifySerializeObject) {
		$.fn.themifySerializeObject = function() {
			var o = {};
			var a = this.serializeArray();
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};
	}

	var api = themifybuilderapp = {
		activeModel : null, 
		Models: {}, 
		Collections: {},
		Mixins: {},
		Views: { Modules: {}, Rows: {}, SubRows: {}, Columns : {}, Controls: {} },
		Forms: {},
		Utils: {},
		Instances: { Builder: {} }
	};

	api.editing = false;
	api.Models.Module = Backbone.Model.extend({
		defaults: {
			elType: 'module',
			mod_name: '',
			mod_settings: {}
		},
		initialize: function() {
			api.Models.Registry.register( this.cid, this );
		},
		toRenderData: function() {
			return {
				slug: this.get('mod_name'), 
				name: this.get('mod_name'),
				excerpt: this.getExcerpt()
			}
		},
		getExcerpt: function() {
			var excerpt = this.get('mod_settings').content_text || this.get('mod_settings').content_box || '';
			return this.limitString(excerpt, 100);
		},
		limitString: function (str, limit) {
			var new_str;
			str = this.stripHtml(str); // strip html tags

			if (str.toString().length > limit) {
				new_str = str.toString().substr(0, limit);
			}
			else {
				new_str = str.toString();
			}

			return new_str;
		},
		stripHtml: function(html) {
			var tmp = document.createElement("DIV");
			tmp.innerHTML = html;
			return tmp.textContent || tmp.innerText || "";
		},
		setData: function( data ) {
			this.set( data, {silent: true});
			this.trigger('custom:change', this);
		}
	});

	api.Models.SubRow = Backbone.Model.extend({
		defaults: {
			elType: 'subrow',
			row_order: 0,
			gutter: 'gutter-default',
			column_alignment: '',
			cols: {},
			styling: {},
                        'fullwidthvideo':'',
                        'mutevideo':'',
                        'unloopvideo':''
		},
		initialize: function() {
			api.Models.Registry.register( this.cid, this );
		},
		setData: function( data ) {
			this.set( data, {silent: true});
			this.trigger('custom:change', this);
		}
	});

	api.Models.Column = Backbone.Model.extend({
		defaults: {
			elType: 'column',
			column_order: '',
			grid_class: '',
			modules: {},
			styling: {},
			component_name: 'column',
                        'fullwidthvideo':'',
                        'mutevideo':'',
                        'unloopvideo':''
		},
		initialize: function() {
			api.Models.Registry.register( this.cid, this );
		},
		setData: function( data ) {
			this.set( data, {silent: true});
			this.trigger('custom:change', this);
		}
	});

	api.Models.Row = Backbone.Model.extend({
		defaults: {
			elType: 'row',
			row_order: 0,
			gutter: 'gutter-default',
			column_alignment: '',
			cols: {},
			styling: {},
                        'fullwidthvideo':'',
                        'mutevideo':'',
                        'unloopvideo':''
		},
		initialize: function() {
			api.Models.Registry.register( this.cid, this );
		},
		setData: function( data ) {
			this.set( data, {silent: true});
			this.trigger('custom:change', this);
		}
	});

	api.Collections.Rows = Backbone.Collection.extend({
		model: api.Models.Row
	});

	api.Models.Registry = {
		items: {},
		register: function (id, object) {
			this.items[id] = object;
		},
		lookup: function (id) {
			return this.items[id] || null;
		},
		remove: function (id) {
			delete this.items[id];
		},
		destroy: function() {
			_.each( this.items, function( model, cid ){
				model.destroy();
			});
			this.items = {};
			window.console && console.log('destroy registry');
		}
	};

	api.Models.setValue = function( cid, data, silent ) {
		silent = silent || false;
		var model = api.Models.Registry.lookup(cid);
		model.set( data, { silent: silent } );
	};

	api.vent = _.extend( {}, Backbone.Events );

	api.Views.register_module = function( type, args ) {

		if ( 'default' !== type )
			this.Modules[ type ] = this.Modules.default.extend( args );
	};

	api.Views.init_module = function( args, type ) {
		type = type || 'default';
		var model = args instanceof api.Models.Module ? args : new api.Models.Module( args ),
			callback = this.get_module( type ),
			view = new callback( { model: model, type: type });

		return {
			model: model,
			view: view
		};
	}

	api.Views.get_module = function( type ) {
		type = type || 'default';
		if ( this.module_exists( type ) )
			return this.Modules[ type ];

		return this.Modules.default;
	};

	api.Views.unregister_module = function( type ) {

		if ( 'default' !== type && this.module_exists( type ) )
			delete this.Modules[ type ];
	};

	api.Views.module_exists = function( type ) {

		return this.Modules.hasOwnProperty( type );
	};

	// column
	api.Views.register_column = function( type, args ) {

		if ( 'default' !== type )
			this.Columns[ type ] = this.Columns.default.extend( args );
	};

	api.Views.init_column = function( args, type ) {
		type = type || 'default';
		var model = args instanceof api.Models.Column ? args : new api.Models.Column( args ),
			callback = this.get_column( type ),
			view = new callback( { model: model, type: type });
		
		return {
			model: model,
			view: view
		};
	}

	api.Views.get_column = function( type ) {
		type = type || 'default';
		if ( this.column_exists( type ) )
			return this.Columns[ type ];

		return this.Columns.default;
	};

	api.Views.unregister_column = function( type ) {

		if ( 'default' !== type && this.column_exists( type ) )
			delete this.Columns[ type ];
	};

	api.Views.column_exists = function( type ) {

		return this.Columns.hasOwnProperty( type );
	};

	// sub-row
	api.Views.register_subrow = function( type, args ) {

		if ( 'default' !== type )
			this.SubRows[ type ] = this.SubRows.default.extend( args );
	};

	api.Views.init_subrow = function( args, type ) {
		type = type || 'default';
		var model = args instanceof api.Models.SubRow ? args : new api.Models.SubRow( args ),
			callback = this.get_subrow( type ),
			view = new callback( { model: model, type: type });
		
		return {
			model: model,
			view: view
		};
	}

	api.Views.get_subrow = function( type ) {
		type = type || 'default';
		if ( this.subrow_exists( type ) )
			return this.SubRows[ type ];

		return this.SubRows.default;
	};

	api.Views.unregister_subrow = function( type ) {

		if ( 'default' !== type && this.subrow_exists( type ) )
			delete this.SubRows[ type ];
	};

	api.Views.subrow_exists = function( type ) {

		return this.SubRows.hasOwnProperty( type );
	};

	// Row
	api.Views.register_row = function( type, args ) {

		if ( 'default' !== type )
			this.Rows[ type ] = this.Rows.default.extend( args );
	};

	api.Views.init_row = function( args, type ) {
		type = type || 'default';
		var model = args instanceof api.Models.Row ? args : new api.Models.Row( args ),
			callback = this.get_row( type ),
			view = new callback( { model: model, type: type });
		
		return {
			model: model,
			view: view
		};
	}

	api.Views.get_row = function( type ) {
		type = type || 'default';
		if ( this.row_exists( type ) )
			return this.Rows[ type ];

		return this.Rows.default;
	};

	api.Views.unregister_row = function( type ) {

		if ( 'default' !== type && this.row_exists( type ) )
			delete this.Rows[ type ];
	};

	api.Views.row_exists = function( type ) {

		return this.Rows.hasOwnProperty( type );
	};

	api.Views.BaseElement = Backbone.View.extend({
		type: 'default',
		menuTouched: [],
		events: {
			'click .themify_builder_copy_component' : 'copy',
			'click .themify_builder_paste_component' : 'paste',
			'click .themify_builder_import_component' : 'import',
			'click .themify_builder_export_component' : 'export',
			'hover .module_menu' : 'actionMenuHover',
			'hover .row_menu' : 'actionMenuHover'
		},
		initialize: function( options ) {
			_.extend(this, _.pick(options, 'type'));	

			this.listenTo( this.model, 'custom:change', this.modelChange );
			this.listenTo( this.model, 'destroy', this.remove );
			this.listenTo( this.model, 'custom:dom:update', this.domUpdate);
		},
		modelChange: function( model ) {
			this.$el.attr(_.extend({}, _.result(this, 'attributes')));
			this.render();
		},
		remove: function() {
			//api.Models.Registry.remove( this.model.cid );
			this.$el.remove();
		},
		domUpdate: function() {
			this.setElement( $('[data-cid="'+ this.model.cid +'"]') );
		},
		renderInlineData: function() {}, // will be overwrited by sub-view
		copy: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			window.console && console.log('copy');
			
			var $thisElem = $(event.currentTarget);
			var component = ThemifyBuilderCommon.detectBuilderComponent($thisElem);

			switch (component) {
				case 'row':
					var $selectedRow = $thisElem.closest('.themify_builder_row'),
						rowOrder = $selectedRow.index(),
						rowData = api.Utils._getRowSettings($selectedRow, rowOrder),
						rowDataInJson = JSON.stringify(rowData);

					ThemifyBuilderCommon.Clipboard.set('row', rowDataInJson);

					$selectedRow.find('.themify_builder_dropdown').hide();
					break;

				case 'sub-row':
					var $selectedSubRow = $thisElem.closest('.themify_builder_sub_row');

					var subRowOrder = $selectedSubRow.index();
					var subRowData = api.Utils._getSubRowSettings($selectedSubRow, subRowOrder);
					var subRowDataInJSON = JSON.stringify(subRowData);

					ThemifyBuilderCommon.Clipboard.set('sub-row', subRowDataInJSON);
					break;

				case 'module':
					var $selectedModule = $thisElem.closest('.active_module');

					var moduleName = $selectedModule.data('mod-name');
					var moduleData = JSON.parse($selectedModule.find('.themify_module_settings')
						.find('script[type="text/json"]').text());

					var moduleDataInJson = JSON.stringify({
						mod_name: moduleName,
						mod_settings: moduleData
					});

					ThemifyBuilderCommon.Clipboard.set('module', moduleDataInJson);
					break;

				case 'column':
				case 'sub-column':
					var $selectedColumn = $thisElem.closest('.themify_builder_col'),
						$selectedRow = 'sub-column' === component ? $thisElem.closest('.themify_builder_sub_row') : $thisElem.closest('.themify_builder_row'),
						rowOrder = $selectedRow.index(),
						rowData = 'sub-column' === component ? api.Utils._getSubRowSettings( $selectedRow, rowOrder ) : api.Utils._getRowSettings($selectedRow, rowOrder),
						columnOrder = $selectedColumn.index(),
						columnData = rowData.cols[ columnOrder ],
						columnDataInJson = JSON.stringify(columnData);

					ThemifyBuilderCommon.Clipboard.set(component, columnDataInJson);

					break;
			}
		},
		paste: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			window.console && console.log('paste');
			
			var $thisElem = $(event.currentTarget);
			var component = ThemifyBuilderCommon.detectBuilderComponent($thisElem);

			var dataInJSON = ThemifyBuilderCommon.Clipboard.get(component);

			if (dataInJSON === false) {
				ThemifyBuilderCommon.alertWrongPaste();
				return;
			}

			if (!ThemifyBuilderCommon.confirmDataPaste()) {
				return;
			}

			var $container = this.$el.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container, { cid: this.model.cid, value: this.model.toJSON() } );

			switch (component) {
				case 'column':
				case 'sub-column':
					var $selectedCol = $thisElem.closest('.themify_builder_col'),
						$selectedRow = 'column' === component ? $thisElem.closest('.themify_builder_row') : $thisElem.closest('.themify_builder_sub_row'),
						col_index = $selectedCol.index(),
						row_index = $selectedRow.index(),
						colDataPlainObject = JSON.parse(dataInJSON);

						colDataPlainObject['column_order'] = col_index;
						colDataPlainObject['grid_class'] = $selectedCol.prop('class').replace('themify_builder_col', '');

						if ( 'column' === component ) {
							colDataPlainObject['row_order'] = row_index;
						} else {
							colDataPlainObject['sub_row_order'] = row_index;
							colDataPlainObject['row_order'] = $selectedCol.closest('.themify_builder_row').index();
							colDataPlainObject['col_order'] = $selectedCol.parents('.themify_builder_col').index();
						}
						colDataPlainObject['component_name'] = component;

						this.model.setData( colDataPlainObject );
						
						api.vent.trigger('dom:builder:change');
					break;

				default: 
					var dataPlainObject = JSON.parse(dataInJSON);
					this.model.setData( dataPlainObject );
					
					api.vent.trigger('dom:builder:change');

				break;
			}

			if ( 'visual' !== this.type ) {
				api.vent.trigger('dom:observer:end', $container, { cid: this.model.cid, value: this.model.toJSON() } );
			}
		},
		import: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			window.console && console.log('import');
			
			var $thisElem = $(event.currentTarget);
			var component = ThemifyBuilderCommon.detectBuilderComponent($thisElem);

			var options = {
				data: {
					action: 'tfb_imp_component_data_lightbox_options'
				}
			};

			api.activeModel = api.Models.Registry.lookup( $thisElem.closest('[data-cid]').data('cid') );

			switch (component) {
				case 'row':
					var $selectedRow = $thisElem.closest('.themify_builder_row');
					options.data.component = 'row';

					ThemifyBuilderCommon.highlightRow($selectedRow);
					ThemifyBuilderCommon.Lightbox.open(options, null);
					break;

				case 'sub-row':
					var $selectedSubRow = $thisElem.closest('.themify_builder_sub_row');
					options.data.component = 'sub-row';

					ThemifyBuilderCommon.highlightSubRow($selectedSubRow);
					ThemifyBuilderCommon.Lightbox.open(options, null);
					break;

				case 'module':
					var $selectedModule = $thisElem.closest('.themify_builder_module');
					options.data.component = 'module';

					$('.themify_builder_module').removeClass('current_selected_module');
					$selectedModule.addClass('current_selected_module');

					ThemifyBuilderCommon.Lightbox.open(options, null);
					break;

				case 'column':
				case 'sub-column':
					var $selectedCol = $thisElem.closest('.themify_builder_col'),
						$selectedRow = 'column' === component ? $thisElem.closest('.themify_builder_row') : $thisElem.closest('.themify_builder_sub_row');
					options.data.component = component;
					options.data.indexData = { row: $selectedRow.index(), col: $selectedCol.index() };

					ThemifyBuilderCommon.highlightColumn($selectedCol);
					ThemifyBuilderCommon.Lightbox.open(options, null);
					break;
			}
		},
		export: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			window.console && console.log('export');
			
			var self = this,
				$thisElem = $(event.currentTarget),
				component = ThemifyBuilderCommon.detectBuilderComponent($thisElem);

			var options = {
				data: {
					action: 'tfb_exp_component_data_lightbox_options'
				}
			};

			switch (component) {
				case 'row':
					var $selectedRow = $thisElem.closest('.themify_builder_row');
					options.data.component = 'row';

					var rowCallback = function() {
						var rowOrder = $selectedRow.index();

						var rowData = api.Utils._getRowSettings($selectedRow, rowOrder);
						rowData['component_name'] = 'row';

						var rowDataInJson = JSON.stringify(rowData);

						var $rowDataTextField = $('#tfb_exp_row_data_field');
						$rowDataTextField.val(rowDataInJson);

						self._autoSelectInputField($rowDataTextField);
						$rowDataTextField.on('click', function() {
							self._autoSelectInputField($rowDataTextField)
						});
					};

					ThemifyBuilderCommon.Lightbox.open(options, rowCallback);
					break;

				case 'sub-row':
					var $selectedSubRow = $thisElem.closest('.themify_builder_sub_row');
					options.data.component = 'sub-row';

					var subRowCallback = function() {
						var subRowOrder = $selectedSubRow.index();

						var subRowData = api.Utils._getSubRowSettings($selectedSubRow, subRowOrder);
						subRowData['component_name'] = 'sub-row';

						var subRowDataInJSON = JSON.stringify(subRowData);

						var $subRowDataTextField = $('#tfb_exp_sub_row_data_field');
						$subRowDataTextField.val(subRowDataInJSON);

						self._autoSelectInputField($subRowDataTextField);
						$subRowDataTextField.on('click', function() {
							self._autoSelectInputField($subRowDataTextField)
						});
					};

					ThemifyBuilderCommon.Lightbox.open(options, subRowCallback);
					break;

				case 'module':
					var $selectedModule = $thisElem.closest('.active_module');
					options.data.component = 'module';

					var moduleCallback = function() {
						var moduleName = $selectedModule.data('mod-name');
						var moduleData = JSON.parse($selectedModule.find('.themify_module_settings')
							.find('script[type="text/json"]').text());

						var moduleDataInJson = JSON.stringify({
							mod_name: moduleName,
							mod_settings: moduleData,
							component_name: 'module'
						});

						var $moduleDataTextField = $('#tfb_exp_module_data_field');
						$moduleDataTextField.val(moduleDataInJson);

						self._autoSelectInputField($moduleDataTextField);
						$moduleDataTextField.on('click', function() {
							self._autoSelectInputField($moduleDataTextField)
						});
					};

					ThemifyBuilderCommon.Lightbox.open(options, moduleCallback);
					break;

				case 'column':
				case 'sub-column':
					var $selectedRow = 'column' === component ? $thisElem.closest('.themify_builder_row') : $thisElem.closest('.themify_builder_sub_row'),
						$selectedCol = $thisElem.closest('.themify_builder_col');
					options.data.component = component;

					var columnCallback = function() {

						var rowOrder = $selectedRow.index(),
						rowData = 'column' === component ? api.Utils._getRowSettings($selectedRow, rowOrder) : api.Utils._getSubRowSettings($selectedRow, rowOrder),
						columnOrder = $selectedCol.index(),
						columnData = rowData.cols[ columnOrder ];
						columnData['component_name'] = component;

						var columnDataInJson = JSON.stringify(columnData),
							$columnDataTextField = $('#tfb_exp_'+ component.replace('-', '_') +'_data_field');
						$columnDataTextField.val(columnDataInJson);

						self._autoSelectInputField($columnDataTextField);
						$columnDataTextField.on('click', function() {
							self._autoSelectInputField($columnDataTextField)
						});
					};

					ThemifyBuilderCommon.Lightbox.open(options, columnCallback);
					break;
			}
		},
		actionMenuHover: function( event ) {
			event.stopPropagation();
			var $this = $(event.currentTarget);

			if ('touchend' == event.type) {
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
					$this.find('.themify_builder_dropdown').stop(false, true).hide();
					$row.css('z-index', '');
					ThemifyPageBuilder.menuTouched = [];
				} else {
					var $builderCont = this.$el;
					$builderCont.find('.themify_builder_dropdown').stop(false, true).hide();
					$builderCont.find('.themify_builder_row').css('z-index', '');
					$this.find('.themify_builder_dropdown').stop(false, true).show();
					$row.css('z-index', '998');
					this.menuTouched = [];
					this.menuTouched[index] = true;
				}
			} else if (event.type == 'mouseenter') {
				$this.find('.themify_builder_dropdown').stop(false, true).show();
			} else if (event.type == 'mouseleave') {
				$this.find('.themify_builder_dropdown').stop(false, true).hide();
			}
		},
		getBuilderID: function() {
			return this.$el.closest('[data-postid]').data('postid');
		}
	});

	api.Views.BaseElement.extend = function(child) {
		var self = this,
			view = Backbone.View.extend.apply(this, arguments);
		view.prototype.events = _.extend({}, this.prototype.events, child.events);
		view.prototype.initialize = function() {
			if ( _.isFunction(self.prototype.initialize) ) self.prototype.initialize.apply(this, arguments);
			if ( _.isFunction(child.initialize) ) child.initialize.apply(this, arguments);
		}
		return view;
	};

	api.Views.Modules['default'] = api.Views.BaseElement.extend({
		tagName: 'div',
		attributes: function() {
			return {
				'class' : 'themify_builder_module module-' + this.model.get('mod_name') +' active_module',
				'data-mod-name' : this.model.get('mod_name'),
				'data-cid' : this.model.cid
			};
		},
		template: wp.template('builder_module_item'),
		events: {
			'dblclick' : 'edit',
			'click .themify_module_options' : 'edit',
			'click .js--themify_builder_module_styling' : 'edit',
			'click .themify_module_delete' : 'delete',
			'click .themify_module_duplicate': 'duplicate'
		},
		initialize: function() {
			this.listenTo(this, 'edit', this.edit);
		},
		render: function() {
			this.$el.html( this.template( this.model.toRenderData() ) );
			this.$el.find('script[type="text/json"]').text(JSON.stringify(this.model.get('mod_settings')));
			return this;
		},
		edit: function( event, isNewModule ) {
			if ( ! _.isUndefined( event ) ) { 
				event.preventDefault();

				if ( $(event.currentTarget).hasClass('themify_builder_module_styling') ) { 
					this.model.set('styleClicked', true );
				} else {
					this.model.set('styleClicked', false );
				}
			}

			isNewModule = isNewModule || false; // assume that if isNewModule:true = Add module, otherwise Edit Module
			api.activeModel = this.model;

			var self = this,
				el_settings = this.model.get('mod_settings');

			$('.module_menu .themify_builder_dropdown').hide();

			this.highlightModuleBack(this.$el);

			var callback = function (response) {
				if ( isNewModule ) {
					response.setAttribute('data-form-state', 'new');
				} else {
					response.setAttribute('data-form-state', 'edit');
				}

				if ( 'visual' === self.type ) {

					if ('desktop' !== api.Frontend.activeBreakPoint) {
						var styleFields = $('#themify_builder_options_styling .tfb_lb_option').map(function() {
							return $(this).attr('id');
						}).get();
						el_settings = _.omit(el_settings, styleFields);

						if (!_.isUndefined(el_settings['breakpoint_' + api.Frontend.activeBreakPoint]) && _.isObject(el_settings['breakpoint_' + api.Frontend.activeBreakPoint])) {
							el_settings = _.extend(el_settings, el_settings['breakpoint_' + api.Frontend.activeBreakPoint]);
						}
					}

					api.liveStylingInstance.init(self.$el.children('.module'), el_settings);
				}

				var inputs = response.getElementsByClassName('tfb_lb_option'), iterate,
					is_settings_exist = ! _.isEmpty( el_settings );
				for (iterate = 0; iterate < inputs.length; ++iterate) {
					var $this_option = $(inputs[iterate]),
						this_option_id = $this_option.attr('id'),
						$found_element = el_settings[this_option_id];

					if( $this_option.hasClass( 'themify-gradient' ) ) {
						api.Utils.createGradientPicker( $this_option, $found_element );
					} else if ( $this_option.hasClass( 'tf-radio-input-container' ) ) {
						//@todo move this
						$this_option.find( ':checked' ).trigger('change');
					}
					if ($found_element) {
						if ($this_option.hasClass('select_menu_field')) {
							if (!isNaN($found_element)) {
								$this_option.find("option[data-termid='" + $found_element + "']").attr('selected', 'selected');
							} else {
								$this_option.find("option[value='" + $found_element + "']").attr('selected', 'selected');
							}
						} else if ($this_option.is('select')) {
							$this_option.val($found_element).trigger('change');
						} else if ($this_option.hasClass('themify-builder-uploader-input')) {
							var img_field = $found_element,
									img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

							if (img_field != '') {
								$this_option.val(img_field);
								$this_option.parent().find('.img-placeholder').empty().html(img_thumb);
							}
							else {
								$this_option.parent().find('.thumb_preview').hide();
							}

						} else if ($this_option.hasClass('themify-option-query-cat')) {
							var parent = $this_option.parent(),
								multiple_cat = parent.find('.query_category_multiple'),
								elems = $found_element,
								value = elems.split('|'),
								cat_val = value[0];

							parent.find("option[value='" + cat_val + "']").attr('selected', 'selected');
							multiple_cat.val(cat_val);

						} else if ($this_option.hasClass('themify_builder_row_js_wrapper')) {
							var row_append = 0;
							if ($found_element.length > 0) {
								row_append = $found_element.length - 1;
							}

							// add new row
							for (var i = 0; i < row_append; i++) {
								$this_option.parent().find('.add_new a').first().trigger('click');
							}

							$this_option.find('.themify_builder_row').each(function (r) {
								$(this).find('.tfb_lb_option_child').each(function (i) {
										var $this_option_child = $(this),
											this_option_id_real = $this_option_child.attr('id'),
											this_option_id_child = $this_option_child.hasClass('tfb_lb_wp_editor') ? $this_option_child.attr('name') : $this_option_child.data('input-id');
											if(!this_option_id_child){
												this_option_id_child = this_option_id_real;
											}
											var $found_element_child = $found_element[r]['' + this_option_id_child + ''];

									if ($this_option_child.hasClass('themify-builder-uploader-input')) {
										var img_field = $found_element_child,
												img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

										if (img_field != '' && img_field != undefined) {
											$this_option_child.val(img_field);
											$this_option_child.parent().find('.img-placeholder').empty().html(img_thumb).parent().show();
										}
										else {
											$this_option_child.parent().find('.thumb_preview').hide();
										}

									}
									else if ($this_option_child.hasClass('tf-radio-choice')) {
										$this_option_child.find("input[value='" + $found_element_child + "']").attr('checked', 'checked').trigger( 'change' );
									} else if ($this_option_child.hasClass('themify-layout-icon')) {
										$this_option_child.find('#' + $found_element_child).addClass('selected');
									}
									else if ($this_option_child.hasClass('themify-checkbox')) {
										for(var $i in $found_element_child){
										   
											 $this_option_child.find("input[value='" + $found_element_child[$i] + "']").prop('checked', true);
										}
									}
									else if ($this_option_child.is('input, textarea, select')) {
										$this_option_child.val($found_element_child);
									}

									if ($this_option_child.hasClass('tfb_lb_wp_editor') && !$this_option_child.hasClass('clone')) {
										api.Views.init_control( 'wp_editor', { el: $this_option_child } );
									}

								});
							});

						} else if ($this_option.hasClass('tf-radio-input-container')) {
							$this_option.find("input[value='" + $found_element + "']").attr('checked', 'checked').trigger( 'change' );
							var selected_group = $this_option.find('input[name="' + this_option_id + '"]:checked').val();

							// has group element enable
							if ($this_option.hasClass('tf-option-checkbox-enable')) {
								$this_option.find('.tf-group-element').hide();
								$this_option.find('.tf-group-element-' + selected_group).show();
							}

						} else if ($this_option.is('input[type!="checkbox"][type!="radio"], textarea')) {
							$this_option.val($found_element).trigger( 'change' );
							if(!isNewModule && $this_option.is('textarea') && $this_option.hasClass('tf-thumbs-preview')){
							   self.getShortcodePreview($this_option,$found_element);
							}
						} else if ($this_option.hasClass('themify-checkbox')) {
							var cselected = $found_element;
							cselected = cselected.split('|');

							$this_option.find('.tf-checkbox').each(function () {
								if ($.inArray($(this).val(), cselected) > -1) {
									$(this).prop('checked', true);
								}
								else {
									$(this).prop('checked', false);
								}
							});

						} else if ($this_option.hasClass('themify-layout-icon')) {
							$this_option.find('#' + $found_element).addClass('selected');
						} else {
							$this_option.html($found_element);
						}
					}
					else {
						if ($this_option.hasClass('themify-layout-icon')) {
							$this_option.children().first().addClass('selected');
						}
						else if ($this_option.hasClass('themify-builder-uploader-input')) {
							$this_option.parent().find('.thumb_preview').hide();
						}
						else if ($this_option.hasClass('tf-radio-input-container')) {
							$this_option.find('input[type="radio"]').first().prop('checked');
							var selected_group = $this_option.find('input[name="' + this_option_id + '"]:checked').val();

							// has group element enable
							if ($this_option.hasClass('tf-option-checkbox-enable')) {
								$this_option.find('.tf-group-element').hide();
								$this_option.find('.tf-group-element-' + selected_group).show();
							}
						}
						else if ($this_option.hasClass('themify_builder_row_js_wrapper')) {
							$this_option.find('.themify_builder_row').each(function (r) {
								$(this).find('.tfb_lb_option_child').each(function (i) {
									var $this_option_child = $(this),
											this_option_id_real = $this_option_child.attr('id');

									if ($this_option_child.hasClass('tfb_lb_wp_editor')) {
										api.Views.init_control( 'wp_editor', { el: $this_option_child } );
									}

								});
							});
						}
						else if ($this_option.hasClass('themify-checkbox') && is_settings_exist) {
							$this_option.find('.tf-checkbox').each(function () {
								$(this).prop('checked', false);
							});
						}
						else if ($this_option.is('input[type!="checkbox"][type!="radio"], textarea') && is_settings_exist) {
							$this_option.val('');
						}
					}

					if ($this_option.hasClass('tfb_lb_wp_editor')) {
						api.Views.init_control( 'wp_editor', { el: $this_option } );
					}

				} // iterate

				// Trigger event
				$('body').trigger('editing_module_option', [el_settings]);
				$('.tf-option-checkbox-enable input:checked').trigger('change');

				// shortcut tabs
				if ( self.model.get('styleClicked') && $('a[href="#themify_builder_options_styling"]').length ) {
					$('a[href="#themify_builder_options_styling"]').trigger('click');
				}

				// add new wp editor
				api.Utils.addNewWPEditor();

				// colorpicker
				api.Utils.setColorPicker( response );

				// plupload init
				api.Utils.builderPlupload('normal');

				// option binding setup
				self.moduleOptionsBinding();

				// builder drag n drop init
				self.moduleOptionBuilder();

				// tabular options
				$('.themify_builder_tabs').tabs();

				if ( 'visual' === self.type ) {
					ThemifyBuilderCommon.Lightbox.rememberRow( self );
				}

				// "Apply all" // apply all init
				self.applyAll_init();
				ThemifyBuilderCommon.fontPreview($('#themify_builder_lightbox_container'),el_settings);
			};

			ThemifyBuilderCommon.highlightRow(this.$el.closest('.themify_builder_row'));

			ThemifyBuilderCommon.Lightbox.open( { loadMethod: 'inline', templateID: 'builder_form_module_' + this.model.get('mod_name') }, function( response ){
				setTimeout( function(){
					callback( response );
				}, 400);
			});
		},
		delete: function( event ) {
			event.preventDefault();

			if (confirm(themifyBuilder.moduleDeleteConfirm)) {
				var $container = this.$el.closest('[data-postid]');

				api.vent.trigger('dom:observer:start', $container);
				
				this.switchPlaceholdModule(this.$el);

				this.model.destroy();

				api.vent.trigger('dom:observer:end', $container);
				
				api.vent.trigger('dom:builder:change');
			}
		},
		duplicate: function( event ) {
			event.preventDefault();

			var moduleView = api.Views.init_module( this.model.toJSON(), this.type ),
				$container = this.$el.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container);

			moduleView.view.render().$el.insertAfter(this.$el);

			if ( 'visual' !== this.type ) {
				api.vent.trigger('dom:observer:end', $container);
			}

			moduleView.view.trigger('component:duplicate');
		}
	});

	api.Views.Columns['default'] = api.Views.BaseElement.extend({
		tagName: 'div',
		attributes: function() {
			return {
				'class' : 'themify_builder_col ' + this.model.get('grid_class'),
				'style' : 'width:' + this.model.get('grid_width') + '%',
				'data-cid' : this.model.cid,
                                'data-fullwidthvideo' : this.model.get('fullwidthvideo'),
                                'data-mutevideo':this.model.get('mutevideo'),
                                'data-unloopvideo':this.model.get('unloopvideo')
			};
		},
		template: wp.template('builder_column_item'),
		events: {
			'click .themify_builder_option_column' : 'edit'
		},
		initialize: function() {
			this.listenTo( this.model, 'change:styling', this.renderInlineData );
			this.listenTo( this.model, 'change:grid_class', this.renderAttrs );
		},
		renderAttrs: function() {
			this.$el.attr(_.extend({}, _.result(this, 'attributes')));
		},
		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.renderInlineData();

			// check if it has module
			if ( ! _.isEmpty( this.model.get('modules') ) ) {
				var container = document.createDocumentFragment();
				_.each( this.model.get('modules'), function( value, key ){

					if ( _.isNull( value ) ) return true;

					var moduleView = value && _.isUndefined( value.cols ) ? api.Views.init_module( value, this.type ) : api.Views.init_subrow( value, this.type );

					container.appendChild( moduleView.view.render().el );
				}, this);
				this.$el.find('.themify_module_holder').append( container );
			}
			return this;
		},
		renderInlineData: function() {
                        var styling = this.model.get('styling'),video='',mute='',loop='';;
                        if( _.isObject( styling )){
                            video = ! _.isEmpty( styling.background_video ) &&  styling.background_type==='video'? styling.background_video : '';
                            if(video && ! _.isEmpty(styling.background_video_options )){
                                mute = styling.background_video_options.indexOf('mute')!==-1?'mute':'';
                                loop = styling.background_video_options.indexOf('unloop')!==-1?'unloop':'';
                            }
                        }
                        this.$el.attr({'data-fullwidthvideo':video,'data-mutevideo':mute,'data-unloopvideo':loop});
			this.$el.children('.column-data-styling').attr('data-styling', JSON.stringify( styling ) );
		},
		edit: function( event ) {
			event.preventDefault();
			event.stopPropagation();

			api.activeModel = this.model;
			var self = this,
				$options = this.model.get('styling') || {};

			var callback = function () {

				if ( 'visual' === self.type ) {
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						var styleFields = $('#tfb_column_settings .tfb_lb_option').map(function() {
							return $(this).attr('id');
						}).get(), temp_background_type = $options.background_type;
						$options = _.omit($options, styleFields);

						if (!_.isUndefined($options['breakpoint_' + api.Frontend.activeBreakPoint]) && _.isObject($options['breakpoint_' + api.Frontend.activeBreakPoint])) {
							$options = _.extend($options, $options['breakpoint_' + api.Frontend.activeBreakPoint]);
						}
					}
				}

				if ('object' === typeof $options && $options != null) {
					if('undefined' !== typeof $options.background_slider){
						self.getShortcodePreview($('#background_slider'),$options.background_slider);
					}
					$.each($options, function (id, val) {
						$('#tfb_column_settings').find('#' + id).val(val);
					});

					$('#tfb_column_settings').find('.tfb_lb_option[type="radio"]').each(function () {
						var id = $(this).prop('name');
						if ('undefined' !== typeof $options[id]) {
							if ($(this).val() === $options[id]) {
								$(this).prop('checked', true);
							}
						}
					});
				}

				// image field
				$('#tfb_column_settings').find('.themify-builder-uploader-input').each(function () {
					var img_field = $(this).val(),
							img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

					if (img_field != '') {
						$(this).parent().find('.img-placeholder').empty().html(img_thumb);
					}
					else {
						$(this).parent().find('.thumb_preview').hide();
					}
				});
				
				$( '.themify-gradient ' ).each(function(){
					var $key = $(this).prop('name');
						$options = $.extend( {
						$key : '',
						}, $options );
						api.Utils.createGradientPicker( $( this ), $options[$key] );
				});

				// colorpicker
				api.Utils.setColorPicker();

				// @backward-compatibility
				if (jQuery('#background_video').val() !== '' && $('#background_type input:checked').length == 0) {
					$('#background_type_video').trigger('click');
				} else if ($('#background_type input:checked').length == 0) {
					$('#background_type_image').trigger('click');
				}

				$('.tf-option-checkbox-enable input:checked').trigger('click');

				// plupload init
				api.Utils.builderPlupload('normal');

				/* checkbox field type */
				$('.themify-checkbox').each(function () {
					var id = $(this).attr('id');
					if ($options && $options[id]) {
						$options[id] = typeof $options[id] == 'string' ? [$options[id]] : $options[id]; // cast the option value as array
						// First unchecked all to fixed checkbox has default value.
						$(this).find('.tf-checkbox').prop('checked', false);
						// Set the values
						$.each($options[id], function (i, v) {
							$('.tf-checkbox[value="' + v + '"]').prop('checked', true);
						});
					}
				});

				if ( 'visual' === self.type ) {
					// Hide non responsive fields
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						$('.responsive-na').hide();
						if( $.inArray( temp_background_type, ['video', 'slider'] ) > -1 ){
							$.each(['background_repeat', 'background_position', 'background_image'], function(i, v){
								if ( 'video' == temp_background_type && 'background_image' == v ) return true;
								$('#' + v).closest('.themify_builder_field').hide();
							});
						}
					}

					ThemifyBuilderCommon.Lightbox.rememberRow( self );
					api.liveStylingInstance.init(self.$el, $options);
				}

				$('body').trigger('editing_column_option', [$options]);

				// "Apply all" // apply all init
				self.applyAll_init();
				ThemifyBuilderCommon.fontPreview($('#themify_builder_lightbox_container'),$options);
			};

			ThemifyBuilderCommon.highlightColumn(this.$el);
			ThemifyBuilderCommon.highlightRow(this.$el.closest('.themify_builder_row'));

			ThemifyBuilderCommon.Lightbox.open({ loadMethod: 'inline', templateID: 'builder_form_column' }, callback);

		}
	});

	// SubRow view share same model as ModuleView
	api.Views.SubRows['default'] = api.Views.BaseElement.extend({
		tagName: 'div',
		attributes: function() {
			return {
				'class' : 'themify_builder_sub_row clearfix ' + this.model.get('gutter'),
				'data-column-alignment' : this.model.get('column_alignment'),
				'data-gutter' : this.model.get('gutter'),
				'data-cid' : this.model.cid,
                                'data-fullwidthvideo' : this.model.get('fullwidthvideo'),
                                'data-mutevideo':this.model.get('mutevideo'),
                                'data-unloopvideo':this.model.get('unloopvideo')
			};
		},
		template: wp.template('builder_sub_row_item'),
		events: {
			'click .themify_builder_style_subrow' : 'edit',
			'click .sub_row_delete' : 'delete',
			'click .sub_row_duplicate' : 'duplicate'
		},
		initialize: function() {
			this.listenTo( this.model, 'change:styling', this.renderInlineData );
		},
		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.renderInlineData();

			if ( ! _.isUndefined( this.model.get('cols') ) ) {
				var container = document.createDocumentFragment();
				_.each( this.model.get('cols'), function( value, key ) {
					value.component_name = 'sub-column';
					var columnView = api.Views.init_column( value, this.type );

					container.appendChild( columnView.view.render().el );
				}, this);
				this.$el.find('.themify_builder_sub_row_content').append( container );
			}
			return this;
		},
		renderInlineData: function() {
                        
                        var styling = this.model.get('styling'),video='',mute='',loop='';;
                        if( _.isObject( styling )){
                            video = ! _.isEmpty( styling.background_video ) &&  styling.background_type==='video'? styling.background_video : '';
                            if(video && ! _.isEmpty(styling.background_video_options )){
                                mute = styling.background_video_options.indexOf('mute')!==-1?'mute':'';
                                loop = styling.background_video_options.indexOf('unloop')!==-1?'unloop':'';
                            }
                        }
                        this.$el.attr({'data-fullwidthvideo':video,'data-mutevideo':mute,'data-unloopvideo':loop});
			this.$el.find('.gutter_select').val( this.model.get('gutter') );
			this.$el.find('[data-alignment="' + this.model.get('column_alignment') + '"]').parent().addClass('selected').siblings().removeClass('selected');
			this.$el.children('.subrow-data-styling').attr('data-styling', JSON.stringify( styling ) );
			
			if( this.$el.find('[data-alignment="' + this.model.get('column_alignment') + '"]') !== 'col_align_top' ) {
				setTimeout( function() {
					this.$el.find('[data-alignment="' + this.model.get('column_alignment') + '"]').trigger( 'click' );
				}.bind( this ), 400 );
			}
		},
		edit: function( event ) {
			event.preventDefault();
			event.stopPropagation();

			api.activeModel = this.model;
			var self = this,
				$options = this.model.get('styling') || {};

			var callback = function () {

				if ( 'visual' === self.type ) {
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						var styleFields = $('#tfb_subrow_settings .tfb_lb_option').map(function() {
							return $(this).attr('id');
						}).get(), temp_background_type = $options.background_type;
						$options = _.omit($options, styleFields);

						if (!_.isUndefined($options['breakpoint_' + api.Frontend.activeBreakPoint]) && _.isObject($options['breakpoint_' + api.Frontend.activeBreakPoint])) {
							$options = _.extend($options, $options['breakpoint_' + api.Frontend.activeBreakPoint]);
						}
					}
				}

				if ('object' === typeof $options && $options != null) {
					if('undefined' !== typeof $options.background_slider){
						self.getShortcodePreview($('#background_slider'),$options.background_slider);
					}
					$.each($options, function (id, val) {
						$('#tfb_subrow_settings').find('#' + id).val(val);
					});

					$('#tfb_subrow_settings').find('.tfb_lb_option[type="radio"]').each(function () {
						var id = $(this).prop('name');
						if ('undefined' !== typeof $options[id]) {
							if ($(this).val() === $options[id]) {
								$(this).prop('checked', true);
							}
						}
					});
				}

				// image field
				$('#tfb_subrow_settings').find('.themify-builder-uploader-input').each(function () {
					var img_field = $(this).val(),
							img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

					if (img_field != '') {
						$(this).parent().find('.img-placeholder').empty().html(img_thumb);
					}
					else {
						$(this).parent().find('.thumb_preview').hide();
					}
				});
				
				$( '.themify-gradient ' ).each(function(){
					var $key = $(this).prop('name');
						$options = $.extend( {
						$key : '',
						}, $options );
						api.Utils.createGradientPicker( $( this ), $options[$key] );
				});

				// colorpicker
				api.Utils.setColorPicker();

				// @backward-compatibility
				if (jQuery('#background_video').val() !== '' && $('#background_type input:checked').length == 0) {
					$('#background_type_video').trigger('click');
				} else if ($('#background_type input:checked').length == 0) {
					$('#background_type_image').trigger('click');
				}

				$('.tf-option-checkbox-enable input:checked').trigger('click');

				// plupload init
				api.Utils.builderPlupload('normal');

				/* checkbox field type */
				$('.themify-checkbox').each(function () {
					var id = $(this).attr('id');
					if ($options && $options[id]) {
						$options[id] = typeof $options[id] == 'string' ? [$options[id]] : $options[id]; // cast the option value as array
						// First unchecked all to fixed checkbox has default value.
						$(this).find('.tf-checkbox').prop('checked', false);
						// Set the values
						$.each($options[id], function (i, v) {
							$('.tf-checkbox[value="' + v + '"]').prop('checked', true);
						});
					}
				});

				if ( 'visual' === self.type ) {
					// Hide non responsive fields
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						$('.responsive-na').hide();
						if( $.inArray( temp_background_type, ['video', 'slider'] ) > -1 ){
							$.each(['background_repeat', 'background_position', 'background_image'], function(i, v){
								if ( 'video' == temp_background_type && 'background_image' == v ) return true;
								$('#' + v).closest('.themify_builder_field').hide();
							});
						}
					}

					ThemifyBuilderCommon.Lightbox.rememberRow( self );
					api.liveStylingInstance.init(self.$el, $options);
				}

				$('body').trigger('editing_subrow_option', [$options]);

				// "Apply all" // apply all init
				self.applyAll_init();
				ThemifyBuilderCommon.fontPreview($('#themify_builder_lightbox_container'),$options);
			};

			ThemifyBuilderCommon.highlightSubRow(this.$el);
			ThemifyBuilderCommon.highlightColumn(this.$el.closest('.themify_builder_column'));
			ThemifyBuilderCommon.highlightRow(this.$el.closest('.themify_builder_row'));

			ThemifyBuilderCommon.Lightbox.open({ loadMethod: 'inline', templateID: 'builder_form_sub_row' }, callback);

		},
		delete: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			
			if (confirm(themifyBuilder.subRowDeleteConfirm)) {

				var $container = this.$el.closest('[data-postid]');

				api.vent.trigger('dom:observer:start', $container);

				this.model.destroy();

				api.vent.trigger('dom:observer:end', $container);
				
				api.vent.trigger('dom:builder:change');
			}
		},
		duplicate: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			
			var subRowView = api.Views.init_subrow( api.Utils._getSubRowSettings(this.$el, this.$el.index()), this.type ),
				$container = this.$el.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container);

			subRowView.view.render().$el.insertAfter( this.$el );

			subRowView.view.trigger('component:duplicate');

			if ( 'visual' !== this.type ) {
				api.vent.trigger('dom:observer:end', $container);
			}
		}
	});

	api.Views.Rows['default'] = api.Views.BaseElement.extend({
		tagName: 'div',
		attributes: function() {
			return {
				'class' : 'themify_builder_row module_row clearfix '+this.model.get('gutter')+' module_row_' + this.model.cid,
				'data-column-alignment' : this.model.get('column_alignment'),
				'data-gutter' : this.model.get('gutter'),
				'data-cid' : this.model.cid,
                                'data-fullwidthvideo' : this.model.get('fullwidthvideo'),
                                'data-mutevideo':this.model.get('mutevideo'),
                                'data-unloopvideo':this.model.get('unloopvideo')
			};
		},
		template: wp.template('builder_row_item'),
		events: {
			'click .themify_builder_option_row' : 'edit',
			'click .themify_builder_style_row' : 'edit',
			'click .themify_builder_delete_row' : 'delete',
			'click .themify_builder_duplicate_row' : 'duplicate',
			'hover .grid_menu' : 'gridMenuHover',
			'click .themify_builder_grid_list li a' : '_gridMenuClicked',
			'click .themify_builder_column_alignment li a' : '_columnAlignmentMenuClicked',
			'change .gutter_select' : '_gutterChange',
			'click .toggle_row' : 'toggleRow',
			'click .themify_builder_toggle_row' : 'toggleRow'
		},
		initialize: function() {
			this.listenTo( this.model, 'change:styling', this.renderInlineData );
		},
		render: function() {
			//this.$el.html( this.defaultRowValues() );
			this.$el.html( this.template( this.model.toJSON() ) );
			this.renderInlineData();
			
			if ( ! _.isEmpty( this.model.get('cols') ) ) {
				var container = document.createDocumentFragment();
				_.each( this.model.get('cols'), function( value, key ) {
					value.component_name = 'column';
					var columnView = api.Views.init_column( value, this.type );

					container.appendChild( columnView.view.render().el );
				}, this);
				this.$el.find('.themify_builder_row_content').append( container );
			} else {
				// Add column
				api.Utils._addNewColumn({
					newclass: 'col-full', 
					component: 'column',
					type: this.type
				}, this.$el.find('.themify_builder_row_content'));
			}

			setTimeout( function() {
				api.Mixins.Builder._selectedGridMenu(this.$el);
			}.bind( this ), 100 )

			return this;
		},
		defaultRowValues: function() {
			var _this = this,
				$template = $( this.template( this.model.toJSON() ) );

			// Set default column alignment value
			if( _this.model.get( 'column_alignment' ) !== 'col_align_top' ) {
				$template.find( '.themify_builder_column_alignment > li' ).removeClass( 'selected' ).each( function() {
					if( $( this ).find( 'a' ).data( 'alignment' ) === _this.model.get( 'column_alignment' ) ) {
						$( this ).addClass( 'selected' );
					}
				} );
			}

			// Set default gutter value
			if( _this.model.get( 'gutter' ) !== 'gutter-default' ) {
				$template.find('.gutter_select option').each( function() {
					if( $( this ).val() === _this.model.get( 'gutter' ) ) {
						$( this ).attr( 'selected', '' );
						setTimeout( function() { $template.find('.gutter_select').trigger( 'change' ); } );
					}
				} );	
			}
			return $template[0];
		},
		renderInlineData: function() {
                        var anchorname  = '',video = '',mute='',loop='',styling = this.model.get('styling');
			this.$el.find('.row-data-styling').attr('data-styling', JSON.stringify( styling ) );    
                        if( _.isObject( styling )){
                            anchorname = ! _.isEmpty( styling.row_anchor )  ? '#' + styling.row_anchor : '';
                            video = ! _.isEmpty( styling.background_video ) &&  styling.background_type==='video'? styling.background_video : '';
                            if(video && ! _.isEmpty(styling.background_video_options )){
                                mute = styling.background_video_options.indexOf('mute')!==-1?'mute':'';
                                loop = styling.background_video_options.indexOf('unloop')!==-1?'unloop':'';
                            }
                        }
                        this.$el.attr({'data-fullwidthvideo':video,'data-mutevideo':mute,'data-unloopvideo':loop});
			this.$el.find('.row-anchor-name').first().text( anchorname );
			this.$el.find('.gutter_select').val( this.model.get('gutter') );
			this.$el.find('[data-alignment="' + this.model.get('column_alignment') + '"]').parent().addClass('selected').siblings().removeClass('selected');
		},
		edit: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			if ( $(event.currentTarget).hasClass('themify_builder_style_row') ) { 
				this.model.set('styleClicked', true );
			} else {
				this.model.set('styleClicked', false );
			}

			api.activeModel = this.model;

			var self = this,
				$options = this.model.get('styling') || {};

			var callback = function () {

				if ( 'visual' === self.type ) {
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						var styleFields = $('#themify_builder_row_fields_styling .tfb_lb_option').map(function() {
							return $(this).attr('id');
						}).get(), temp_background_type = $options.background_type;
						$options = _.omit($options, styleFields);

						if (!_.isUndefined($options['breakpoint_' + api.Frontend.activeBreakPoint]) && _.isObject($options['breakpoint_' + api.Frontend.activeBreakPoint])) {
							$options = _.extend($options, $options['breakpoint_' + api.Frontend.activeBreakPoint]);
						}
					}
				}

				if ('object' === typeof $options && $options != null) {
					if('undefined' !== typeof $options.background_slider){
						self.getShortcodePreview($('#background_slider'),$options.background_slider);
					}
					$.each($options, function (id, val) {
						$('#tfb_row_settings').find('#' + id).val(val);
					});

					$('#tfb_row_settings').find('.tfb_lb_option[type="radio"]').each(function () {
						var id = $(this).prop('name');
						if ('undefined' !== typeof $options[id]) {
							if ($(this).val() === $options[id]) {
								$(this).prop('checked', true);
							}
						}
					});
				}
				
				// image field
				$('#tfb_row_settings').find('.themify-builder-uploader-input').each(function () {
					var img_field = $(this).val(),
							img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

					if (img_field != '') {
						$(this).parent().find('.img-placeholder').empty().html(img_thumb);
					}
					else {
						$(this).parent().find('.thumb_preview').hide();
					}
				});
				
				$( '.themify-gradient ' ).each(function(){
					var $key = $(this).prop('name');
						$options = $.extend( {
							$key : '',
						}, $options );
					api.Utils.createGradientPicker( $( this ), $options[$key] );
				});

				// builder
				$('#tfb_row_settings').find('.themify_builder_row_js_wrapper').each(function () {
					var $this_option = $(this),
						this_option_id = $this_option.attr('id'),
						$found_element = $options ? $options[this_option_id] : false;

					if ($found_element) {
						var row_append = 0;
						if ($found_element.length > 0) {
							row_append = $found_element.length - 1;
						}

						// add new row
						for (var i = 0; i < row_append; i++) {
							$this_option.parent().find('.add_new a').first().trigger('click');
						}

						$this_option.find('.themify_builder_row').each(function (r) {
							$(this).find('.tfb_lb_option_child').each(function (i) {
									var $this_option_child = $(this),
										this_option_id_real = $this_option_child.attr('id'),
										this_option_id_child = $this_option_child.hasClass('tfb_lb_wp_editor') ? $this_option_child.attr('name') : $this_option_child.data('input-id');
										if(!this_option_id_child){
											this_option_id_child = this_option_id_real;
										}
										var $found_element_child = $found_element[r]['' + this_option_id_child + ''];

								if ($this_option_child.hasClass('themify-builder-uploader-input')) {
									var img_field = $found_element_child,
										img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

									if (img_field != '' && img_field != undefined) {
										$this_option_child.val(img_field);
										$this_option_child.parent().find('.img-placeholder').empty().html(img_thumb).parent().show();
									}
									else {
										$this_option_child.parent().find('.thumb_preview').hide();
									}
								}
								else if ($this_option_child.is('input, textarea, select')) {
									$this_option_child.val($found_element_child);
								}
							});
						});
					}
				});

				// colorpicker
				api.Utils.setColorPicker();

				// @backward-compatibility
				if (jQuery('#background_video').val() !== '' && $('#background_type input:checked').length == 0) {
					$('#background_type_video').trigger('click');
				} else if ($('#background_type input:checked').length == 0) {
					$('#background_type_image').trigger('click');
				}

				$('.tf-option-checkbox-enable input:checked').trigger('click');

				// plupload init
				api.Utils.builderPlupload('normal');

				/* checkbox field type */
				$('.themify-checkbox').each(function () {
					var id = $(this).attr('id');
					if ($options && $options[id]) {
						$options[id] = typeof $options[id] == 'string' ? [$options[id]] : $options[id]; // cast the option value as array
						// First unchecked all to fixed checkbox has default value.
						$(this).find('.tf-checkbox').prop('checked', false);
						// Set the values
						$.each($options[id], function (i, v) {
							$('.tf-checkbox[value="' + v + '"]').prop('checked', true);
						});
					}
				});

				$('body').trigger('editing_row_option', [$options]);

				// builder drag n drop init
				self.rowOptionBuilder();

				if ( 'visual' === self.type ) {

					// Hide non responsive fields
					if ('desktop' !== api.Frontend.activeBreakPoint) {
						$('.responsive-na').hide();
						if( $.inArray( temp_background_type, ['video', 'slider'] ) > -1 ){
							$.each(['background_repeat', 'background_position', 'background_image'], function(i, v){
								if ( 'video' == temp_background_type && 'background_image' == v ) return true;
								$('#' + v).closest('.themify_builder_field').hide();
							});
						}
					}

					ThemifyBuilderCommon.Lightbox.rememberRow( self );
					api.liveStylingInstance.init(self.$el, $options);
				}

				// "Apply all" // apply all init
				self.applyAll_init();

				if ( self.model.get('styleClicked') ) {
					$('a[href="#themify_builder_row_fields_styling"]').trigger('click');
				}

				ThemifyBuilderCommon.fontPreview($('#themify_builder_lightbox_container'),$options);
			};

			ThemifyBuilderCommon.highlightRow(this.$el);

			ThemifyBuilderCommon.Lightbox.open({ loadMethod: 'inline', templateID: 'builder_form_row' }, callback);
		},
		delete: function( event ) {
			event.preventDefault();
			event.stopPropagation();
			
			if (!confirm(themifyBuilder.rowDeleteConfirm)) {
				return;
			}

			var $container = this.$el.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container);

			this.model.destroy();

			api.vent.trigger('dom:observer:end', $container);

			api.vent.trigger('dom:builder:change');
		},
		duplicate: function( event ) {
			event.preventDefault();
			event.stopPropagation();

			var rowView = api.Views.init_row( api.Utils._getRowSettings(this.$el, this.$el.index()), this.type ),
				$container = this.$el.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container);

			rowView.view.render().$el.insertAfter( this.$el );

			rowView.view.trigger('component:duplicate');
			
			if ( 'visual' !== this.type ) {
				api.vent.trigger('dom:builder:change');
				api.vent.trigger('dom:observer:end', $container);
			}
		},
		gridMenuHover: function( event ) {
			event.stopPropagation();
			var $this = $(event.currentTarget);
			if (event.type == 'touchend') {
				var $column_menu = $this.find('.themify_builder_grid_list_wrapper');
				if ($column_menu.is(':hidden')) {
					$column_menu.show();
				} else {
					$column_menu.hide();
				}
			} else if (event.type == 'mouseenter') {
				$this.find('.themify_builder_grid_list_wrapper').stop(true, true).show();
			} else if (event.type == 'mouseleave' && (event.toElement || event.relatedTarget)) {
				$this.find('.themify_builder_grid_list_wrapper').stop(true, true).hide();
			}
		},
		_gridMenuClicked: function (event) {
			event.preventDefault();
			event.stopPropagation();

			var that = this,
				$this = $(event.currentTarget),
				set = $this.data('grid'),
				handle = $this.data('handle'), $base, is_sub_row = false,
				$container = $this.closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container);	

			$this.closest('.themify_builder_grid_list').find('.selected').removeClass('selected');
			$this.closest('li').addClass('selected');

			$base = $this.closest('.themify_builder_row').find('.themify_builder_row_content');

			switch (handle) {
				case 'module':
					if( set[0] !== '-full' ) {
						var subRowDataPlainObject = {
								cols: [ { grid_class: 'col-full'} ]
							},
							subRowView = api.Views.init_subrow( subRowDataPlainObject, this.type ),
							$mod_ori = $this.closest('.active_module'),
							$mod_clone = $mod_ori.clone();
						$mod_clone.insertAfter($mod_ori);
						$mod_ori.find('.grid_menu').remove();
						subRowView.view.render().$el
						$base = subRowView.view.render().$el
								.find('.themify_module_holder')
								.append($mod_ori)
							.end()
							.insertAfter($mod_clone)
								.find( '.' + $this.attr( 'class' ).replace( ' ', '.' ) )
								.closest( 'li' )
								.addClass( 'selected' )
							.end().end()
							.find('.themify_builder_sub_row_content');

						$mod_clone.remove();
					}
					break;

				case 'sub_row':
					is_sub_row = true;
					$base = $this.closest('.themify_builder_sub_row').find('.themify_builder_sub_row_content');
					break;
			}

			$.each(set, function (i, v) {
				if ($base.children('.themify_builder_col').eq(i).length > 0) {
					api.Models.setValue( $base.children('.themify_builder_col').eq(i).data('cid'), { grid_class: 'col' + v } );
				} else {
					// Add column
					api.Utils._addNewColumn({
						newclass: 'col' + v, 
						component: is_sub_row ? 'sub-column' : 'column',
						type: that.type
					}, $base);
				}
			});

			// remove unused column
			if (set.length < $base.children().length) {
				$base.children('.themify_builder_col').eq(set.length - 1).nextAll().each(function () {
					// relocate active_module
					var modules = $(this).find('.themify_module_holder').first();
					modules.find('.empty_holder_text').remove();
					modules.children().appendTo($(this).prev().find('.themify_module_holder').first());
					$(this).remove(); // finally remove it
				});
			}

			$base.children().removeClass('first last');
			$base.children().first().addClass('first');
			$base.children().last().addClass('last');
			var $move_modules = false;
			// remove sub_row when fullwidth column
			if (is_sub_row && set[0] == '-full') {
				$move_modules = $base.find('.active_module');
				$move_modules.insertAfter($this.closest('.themify_builder_sub_row'));
				$this.closest('.themify_builder_sub_row').remove();
			}

			setTimeout( function() {
				// hide column 'alignment', 'equal column height' and 'gutter' when fullwidth column
				var $grid = is_sub_row && $move_modules ? $move_modules.find('.themify_builder_grid_list') : $this.closest('.themify_builder_grid_list');
				if (set[0] == '-full') {
					$grid.find( 'a:first' ).parent().addClass( 'selected' );
					$grid.nextAll('.themify_builder_column_alignment').find('a:first').trigger('click');
					$grid.nextAll('.gutter_select').val('gutter-default').trigger('change');
					$grid.nextAll().hide();
				}
				else {
					$grid.nextAll().show();
				}
			}, 100 );

			ThemifyBuilderCommon.columnDrag($base,true);

			api.vent.trigger('dom:observer:end', $container);	

			api.vent.trigger('dom:builder:change');
		},
		_columnAlignmentMenuClicked: function(event) {
			event.preventDefault();

			var $this = $(event.currentTarget),
				handle = $this.data('handle'),
				alignment = $this.data('alignment'),
				$row = null;
			
			if (handle == 'module')
				return;

			$this.closest('.themify_builder_column_alignment').find('.selected').removeClass('selected');
			$this.closest('li').addClass('selected');

			if (handle == 'sub_row') {
				$row = $this.closest('.themify_builder_sub_row');
			} else {
				$row = $this.closest('.themify_builder_row');
			}

			$row.data('column-alignment', alignment);
			if ( 'visual' === this.type ) {
				$row.removeClass(themifyBuilder.columnAlignmentClass).addClass(alignment);
			}
		},
		_gutterChange: function (event) {
			event.stopPropagation();

			var $this = $(event.currentTarget),
				handle = $this.data('handle');
			if (handle == 'module')
				return;
			switch (handle) {
				case 'sub_row':
					$this.closest('.themify_builder_sub_row').data('gutter', $this.val()).removeClass(themifyBuilder.gutterClass).addClass($this.val());
					break;

				default:
					$this.closest('.themify_builder_row').data('gutter', $this.val()).removeClass(themifyBuilder.gutterClass).addClass($this.val());
			}
		},
		toggleRow: function(event) {
			event.preventDefault();
			$(event.currentTarget).parents('.themify_builder_row').toggleClass('collapsed').find('.themify_builder_row_content').slideToggle();
		},
	});

	api.Views.Builder = Backbone.View.extend({
		type: 'default',
		initialize: function( options ) {
			_.extend(this, _.pick(options, 'type'));	
			api.vent.on('dom:builder:change', this.tempEvents.bind(this));
		},
		render: function() {
			var container = document.createDocumentFragment();
			this.collection.each(function( row ) {
				var rowView = api.Views.init_row( row, this.type );
				container.appendChild(rowView.view.render().el);
			}, this);
			this.el.appendChild(container);
			this.addElementClasses();
			ThemifyBuilderCommon.columnDrag(null, false);

			api.vent.trigger('dom:builder:change');
			return this;
		},
		addElementClasses: function() {
			var builderID = this.$el.data('postid');
			this.$el.find('.themify_builder_row').addClass('themify_builder_'+ builderID +'_row');
			this.$el.find('.themify_builder_col:not(.sub_column)').addClass('tb_'+ builderID +'_column');
		},
		tempEvents: function() {
			this.deleteEmptyModule();
			this.newRowAvailable();
			this.moduleEvents();
		}
	});

	api.Mixins.Common = {
		_autoSelectInputField: function($inputField) {
			$inputField.trigger('focus').trigger('select');
		},
		highlightModuleBack: function ($module) {
			$('.active_module').removeClass('current_selected_module');
			$module.addClass('current_selected_module');
		},
		moduleOptionsBinding: function () {
			var doTheBinding = function( $self, binding, val ) {
                            if(binding){
				var logic = false;
                                    if (val == '' && binding['empty'] != undefined) {
                                            logic = binding['empty'];
                                    } else if (val != '' && binding[val] != undefined) {
                                            logic = binding[val];
                                    } else if (val != '' && binding['not_empty'] != undefined) {
                                            logic = binding['not_empty'];
                                    }

                                    if (logic) {
                                            if (logic['show'] != undefined) {
                                                    $.each(logic['show'], function (i, v) {
                                                            var optionRow = $self.closest('.themify_builder_row_content');
                                                            if( optionRow.length ) {
                                                                    optionRow.find('.' + v).removeClass( 'conditional-input' );
                                                                    optionRow.find('.' + v).children().show();
                                                            } else {
                                                                    $('.' + v).removeClass( 'conditional-input' );
                                                                    $('.' + v).children().show();
                                                            }
                                                    });
                                            }
                                            if (logic['hide'] != undefined) {
                                                    $.each(logic['hide'], function (i, v) {
                                                            var optionRow = $self.closest('.themify_builder_row_content');
                                                            if( optionRow.length ) {
                                                                    optionRow.find('.' + v).addClass( 'conditional-input' );
                                                                    optionRow.find('.' + v).children().hide();
                                                            } else {
                                                                    $('.' + v).addClass( 'conditional-input' );
                                                                    $('.' + v).children().hide();
                                                            }
                                                    });
                                            }
                                    }
                            }
                        }

			var form = $('#tfb_module_settings');
			$(form).on( 'change', 'input[data-binding], textarea[data-binding], select[data-binding]', function () {
				doTheBinding( $(this), $(this).data('binding'), $(this).val() );
			});
			$( 'input[data-binding], textarea[data-binding], select[data-binding]', form ).trigger( 'change' );

			$( form ).on( 'click', '.tfb_lb_option.themify-layout-icon[data-binding] a', function(){
				doTheBinding( $(this), $(this).parent().data('binding'), $(this).attr( 'id' ) );
			} );
			$( '.tfb_lb_option.themify-layout-icon a.selected', form ).trigger( 'click' );
		},
		

		moduleOptionBuilder: function () {

			// sortable accordion builder
			$(".themify_builder_module_opt_builder_wrap").sortable({
				items: '.themify_builder_row',
				handle: '.themify_builder_row_top',
				axis: 'y',
				placeholder: 'themify_builder_ui_state_highlight',
				start: function (event, ui) {
					if (typeof tinyMCE !== 'undefined') {
						$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
							var id = $(this).attr('id'),
									content = tinymce.get(id).getContent();
							$(this).data('content', content);
							tinyMCE.execCommand('mceRemoveEditor', false, id);
						});
					}
				},
				stop: function (event, ui) {
					if (typeof tinyMCE !== 'undefined') {
						$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
							var id = $(this).attr('id');
							tinyMCE.execCommand('mceAddEditor', false, id);
							tinymce.get(id).setContent($(this).data('content'));
						});
					}
				},
				sort: function (event, ui) {
					var placeholder_h = ui.item.height();
					$('.themify_builder_module_opt_builder_wrap .themify_builder_ui_state_highlight').height(placeholder_h);
				}
			});
		},

		// "Apply all" // apply all init
		applyAll_init: function() {
			var that = this;
			$('.style_apply_all').each(function() {
				var $val = $(this).val(),
					$fields = $(this).closest('.themify_builder_field').prevUntil('h4'),
					$last = $fields.last(),
					$inputs = $last.find('input[type="text"]').not('.colordisplay'),
					$selects = $last.find('select'),
					$fieldFilter = $val == 'border' ?
					'[name="border_top_color"], [name="border_top_width"], [name="border_top_style"], [name="border_right_color"], [name="border_right_width"], [name="border_right_style"], [name="border_bottom_color"], [name="border_bottom_width"], [name="border_bottom_style"], [name="border_left_color"], [name="border_left_width"], [name="border_left_style"]' :
					'[name="' + $val + '_top"], [name="' + $val + '_right"], [name="' + $val + '_bottom"], [name="' + $val + '_left"]',
					$preSelect = true,
					$callback = function(e) {
						if ($fields.first().next('.themify_builder_field').find('.style_apply_all').is(':checked')) {
							var $v = $(this).val(),
								$opt = false,
								$select = $(this).is('select');
							
							$fields.not(':last').each(function(){
								if ($select) {
									$opt = $(this).find('select option').prop('selected', false).filter('[value="' + $v + '"]');
									$opt.prop('selected', true);
									if($val!=='border'){
										$opt.trigger('change');
									}
									
								} else {
									$opt = $(this).find('input[type="text"].tfb_lb_option');
									$opt.val($v);
									if($val!=='border'){
										$opt.trigger('keyup');
									}
								}
							});
							if($opt && $val==='border'){
								if ( 'visual' === that.type ) {
									api.liveStylingInstance.setApplyBorder($select?$opt.closest('select').prop('name'):$opt.prop('name'),$v,$select?'style':'width');
								}
								if($select){
									$last.find('input[type="text"].colordisplay').trigger('blur');
								}
							}
						}
					};

				if ($(this).is(':checked')) {
					$fields.not(':last').hide();
					$last.children('.themify_builder_input').css('color', '#FFF');
				} else {
					// Pre-select
					$fields.find($fieldFilter).each(function() {
						if ($(this).val() != '') {
							$preSelect = false;
							return false;
						}
					});

					if ($preSelect) {
						$(this).prop('checked', true);
						$fields.not(':last').hide();
						$last.children('.themify_builder_input').css('color', '#FFF');
					}
				}

				// Events
				$inputs.on('keyup', _.debounce( $callback, 300 ) );
				$selects.on('change', $callback);
			});
		},

		switchPlaceholdModule: function (obj) {
			var check = obj.parents('.themify_module_holder');
			if (check.find('.themify_builder_module').length == 1) {
				check.find('.empty_holder_text').show();
			}
		},

		getShortcodePreview:function($input,$value){

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				data:
					{
						action: 'tfb_load_shortcode_preview',
						tfb_load_nonce: themifyBuilder.tfb_load_nonce,
						shortcode:$value
					},
				success: function (data) {
					if(data){
						$input.after(data);
					}
				}
			});
		},

		rowOptionBuilder: function () {
			$(".themify_builder_row_opt_builder_wrap").sortable({
				items: '.themify_builder_row',
				handle: '.themify_builder_row_top',
				axis: 'y',
				placeholder: 'themify_builder_ui_state_highlight',
				start: function (event, ui) {
					if (typeof tinyMCE !== 'undefined') {
						$('#tfb_row_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
							var id = $(this).attr('id'),
									content = tinymce.get(id).getContent();
							$(this).data('content', content);
							tinyMCE.execCommand('mceRemoveEditor', false, id);
						});
					}
				},
				stop: function (event, ui) {
					if (typeof tinyMCE !== 'undefined') {
						$('#tfb_row_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
							var id = $(this).attr('id');
							tinyMCE.execCommand('mceAddEditor', false, id);
							tinymce.get(id).setContent($(this).data('content'));
						});
					}
				},
				sort: function (event, ui) {
					var placeholder_h = ui.item.height();
					$('.themify_builder_row_opt_builder_wrap .themify_builder_ui_state_highlight').height(placeholder_h);
				}
			});
		},

	};

	api.Mixins.Builder = {
		builderContainer: document.querySelector('.themify_builder_editor_wrapper'),
		moduleEvents: function() {
			var self = this;

			this.$el.find('.row_menu .themify_builder_dropdown, .module_menu .themify_builder_dropdown').hide();
			this.$el.find('.themify_module_holder').each(function () {
				if ($(this).find('.active_module').length > 0) {
					$(this).find('.empty_holder_text').hide();
				} else {
					$(this).find('.empty_holder_text').show();
				}
			});

			var moduleHolderArgs = {
				placeholder: 'themify_builder_ui_state_highlight',
				items: '.active_module, .themify_builder_sub_row',
				connectWith: '.themify_module_holder',
				cursor: 'move',
				revert: 100,
				tolerance: 'pointer',
				cursorAt: {
					top: 20,
					left: 110
				},
				start: function( event, ui ) {
					api.vent.trigger('dom:observer:start', ui.item.closest('[data-postid]'));
				},
				sort: function(event, ui) {
					$('.themify_module_holder .themify_builder_ui_state_highlight').height(35);
					if ( 'visual' === self.type ) {
						$('.themify_module_holder .themify_builder_sortable_helper').height(40).width(220);
						if (!$('#themify_builder_module_panel').hasClass('slide_builder_module_state_down')) {
							$('#themify_builder_module_panel').addClass('slide_builder_module_state_down');
							api.Frontend.slidePanelOpen = false;
							$('#themify_builder_module_panel').find('.slide_builder_module_panel_wrapper').slideUp();
						}
					} else {
						ui.item.css('width', 220);
					}
				},
				receive: function (event, ui) {
					self.placeHoldDragger();
					$(this).parent().find('.empty_holder_text').hide();
				},
				stop: function (event, ui) {
					if (!ui.item.hasClass('active_module') && !ui.item.hasClass('themify_builder_sub_row')) {
						var moduleView = api.Views.init_module( { mod_name: ui.item.data('module-slug') }, self.type ),
							$newElems = moduleView.view.render().$el;

						$(this).parent().find(".empty_holder_text").hide();
						ui.item.replaceWith($newElems);
						moduleView.view.trigger('edit');							
					} else {
						// Make sub_row only can nested one level
						if (ui.item.hasClass('themify_builder_sub_row') && ui.item.parents('.themify_builder_sub_row').length) {
							var $clone_for_move = ui.item.find('.active_module').clone();
							$clone_for_move.insertAfter(ui.item);
							ui.item.remove();
						}

						if ( $('.themify_module_holder .themify_builder_sortable_helper').length ) 
								$('.themify_module_holder .themify_builder_sortable_helper').remove();

						api.vent.trigger('dom:builder:change');
						api.vent.trigger('dom:observer:end', ui.item.closest('[data-postid]'));

						if ( 'visual' === self.type ) {
							api.Frontend.showSlidingPanel();
						}
					}
				}
			};
			if ( 'visual' === self.type ) {
				moduleHolderArgs.helper = function() {
					return $('<div class="themify_builder_sortable_helper"/>');
				};
				moduleHolderArgs.create = function(event, ui) {
					$('body').css('overflow-x', 'inherit');
				}
			}
			this.$('.themify_module_holder').sortable(moduleHolderArgs);
			
			this.$el.sortable({
				items: '.themify_builder_row',
				handle: '.themify_builder_row_top',
				axis: 'y',
				placeholder: 'themify_builder_ui_state_highlight',
				start: function( event, ui ) {
					api.vent.trigger('dom:observer:start', ui.item.closest('[data-postid]'));
				},
				sort: function (event, ui) {
					if ( 'visual' === self.type ) {
						$('.themify_builder_ui_state_highlight').height(35);
					} else {
					var placeholder_h = ui.item.height();
						$('.themify_builder_row_panel .themify_builder_ui_state_highlight').height(placeholder_h);
					}
				},
				update: function(event, ui) {
					api.vent.trigger('dom:observer:end', ui.item.closest('[data-postid]'));		
				},
				create: function(event, ui) {
					if ( 'visual' === self.type ) {
						$('body').css('overflow-x', 'inherit');
					}
				}
			});

			// Column and Sub-Column sortable
			this.$el.find('.themify_builder_row_content, .themify_builder_sub_row_content').each(function(){
				var $wrapper = $(this);
				if ( $wrapper.children('.themify_builder_col').length > 1 ) {
					$wrapper.sortable({
						items: '> .themify_builder_col',
						handle: '> .themify_builder_column_action .themify_builder_column_dragger',
						axis: 'x',
						placeholder: 'themify_builder_ui_state_highlight',
						start: function( event, ui ) {
							api.vent.trigger('dom:observer:start', ui.item.closest('[data-postid]'));
						},
						sort: function(event, ui) {
							$('.themify_builder_ui_state_highlight').width( ui.item.width() );
						},
						stop: function( event, ui ){
							$wrapper.children().removeClass('first last');
							$wrapper.children().first().addClass('first');
							$wrapper.children().last().addClass('last');
							ThemifyBuilderCommon.columnDrag($wrapper,false);
							api.vent.trigger('dom:observer:end', ui.item.closest('[data-postid]'));	
						}
					});
				}
			});

			var grid_menu_tmpl = wp.template('builder_grid_menu'),
					grid_menu_render = grid_menu_tmpl({});
			this.$el.find('.themify_builder_row_content').each(function () {
				$(this).children().each(function () {
					var $holder = $(this).find('.themify_module_holder').first();
					$holder.children('.active_module').each(function () {
						if ($(this).find('.grid_menu').length == 0) {
							$(this).append($(grid_menu_render));
						}
					});
				});
			});
		},
		placeHoldDragger: function () {
			this.$el.find('.themify_module_holder').each(function () {
				if ($(this).find('.active_module').length == 0) {
					$(this).find('.empty_holder_text').show();
				}
			});
		},
		newRowAvailable: function () {
			var $parent = this.$el.children('.themify_builder_row');
			$parent.each(function () {
				if ($(this).find('.active_module').length != 0) {
					return;
				}

				var removeThis = true,
					column_data_styling = $(this).find('.column-data-styling'),
					data_styling = null;

				column_data_styling.each(function () {
					if (!removeThis) {
						return;
					}

					data_styling = $.parseJSON( $(this).attr('data-styling') );

					if ((typeof data_styling === 'array' && data_styling.length > 0) || !$.isEmptyObject(data_styling)) {
						removeThis = false;
					}
				});

				data_styling = $.parseJSON( $(this).find('.row-data-styling').attr('data-styling') );
				if (removeThis && (typeof data_styling === 'string' || $.isEmptyObject(data_styling)) && ( $parent.length > 1 && ( $(this).index() + 1 ) < $parent.length ) ) {
					var removeCids = $(this).find('[data-cid]').map(function() {
							return $(this).data('cid');
					}).get();
					removeCids.push( $(this).data('cid') );
					window.console && console.log(removeCids, 'removeCids');
					_.each( removeCids, function( cid, k ){
						var model = api.Models.Registry.lookup( cid );
						if ( model ) model.destroy();
					});
				}
			});

			if (this.$el.children('.themify_builder_row').last().find('.active_module').length > 0 || this.$el.children('.themify_builder_row').length == 0) {
				var rowDataPlainObject = {
						cols: [{ grid_class: 'col-full first last' }]
					},
					rowView = api.Views.init_row( rowDataPlainObject, this.type ),
					$template = rowView.view.render().$el;

				$template.appendTo(this.$el);
			}
		},
		_selectedGridMenu: function (context) {
			context = context || document;
			$('.grid_menu', context).each(function () {
				var handle = $(this).data('handle'),
					grid_base = [], $base;
				
				$base = $(this).closest('.themify_builder_row').find('.themify_builder_row_content');
				switch (handle) {
					case 'sub_row':
						$base = $(this).closest('.themify_builder_sub_row').find('.themify_builder_sub_row_content');
						break;
				}

				$base.children().each(function () {
					grid_base.push(api.Utils._getColClass($(this).prop('class').split(' ')));
				});

				var $selected = $(this).find('.grid-layout-' + grid_base.join('-'));
				$selected.closest('li').addClass('selected');

				// hide column 'alignment', 'equal column height' and 'gutter' when fullwidth column
				var $grid = $(this).find('.themify_builder_grid_list'),
					grid = $selected.data('grid');
				if (grid && grid[0] == '-full') {
					$grid.nextAll('.themify_builder_column_alignment').find('a:first').trigger('click');
					$grid.nextAll('.gutter_select').val('gutter-default').trigger('change');
					$grid.nextAll().hide();
				}
				else {
					$grid.nextAll().show();
				}
			});
		},
		makeEqual: function ($obj, target) {
			$obj.each(function () {
				var t = 0;
				$(this).find(target).children().each(function () {
					var $holder = $(this).find('.themify_module_holder').first();
					$holder.css('min-height', '');
					if ($holder.height() > t) {
						t = $holder.height();
					}
				});
				$(this).find(target).children().each(function () {
					$(this).find('.themify_module_holder').first().css('min-height', t + 'px');
				});
			});
		},
		deleteEmptyModule: function () {
			this.$el.find('.active_module').each(function () {
				if ($.trim($(this).find('.themify_module_settings').find('script[type="text/json"]').text()).length <= 2) {
					var model = api.Models.Registry.lookup( $(this).data('cid') );
					if ( model ) {
						model.destroy();
					}
				}
			});
		},
		toJSON: function () {
			var option_data = {};

			// rows
			this.$el.children('.themify_builder_row').each(function(r) {
				option_data[r] = api.Utils._getRowSettings($(this), r);
			});
			return option_data;
		},
	};

	api.Forms = {
		bindEvents: function() {
			var $body = $('body');

			if ( _.isNull( api.Utils.tfb_hidden_editor_object ) ) {
				api.Utils.tfb_hidden_editor_object = tinyMCEPreInit.mceInit['tfb_lb_hidden_editor'];
			}

			if($.fn.ThemifyGradient){
				$body.on( 'change', '.tf-image-gradient-field .tf-radio-input-container input', function(){
					if( $( this ).val() == 'image' ) {
						$( this ).closest( '.tf-image-gradient-field' ).find( '.themify-gradient-field' ).hide().end().find( '.themify-image-field' ).show();
					} else {
						$( this ).closest( '.tf-image-gradient-field' ).find( '.themify-gradient-field' ).show().end().find( '.themify-image-field' ).hide();
					}
				});
			}

			// Row field type
			$body.on('click', '#themify_builder_lightbox_container .themify_builder_duplicate_row', this.duplicateRowField)
				.on('click', '#themify_builder_lightbox_container .themify_builder_delete_row', this.deleteRowField)

			// used for both column and sub-column options
				.on('click', '#tfb_row_settings .add_new a', this.rowOptAddRow)

				/* save module option */
				.on('click', '#tfb_module_settings .add_new a', this.moduleOptAddRow)

				// layout icon selected
				.on('click', '.tfl-icon', function (e) {
					$(this).addClass('selected').siblings().removeClass('selected');
					e.preventDefault();
				})

				.on('click', '.js-builder-restore-revision-btn', ThemifyBuilderCommon.restoreRevision)
				.on('click', '.js-builder-delete-revision-btn', ThemifyBuilderCommon.deleteRevision)

				.on('click', '#builder_submit_import_form', this.builderImportSubmit)
				/* Layout Action */
				.on('click', '.layout_preview img', this.templateSelected)
				.on('click', '#builder_submit_layout_form', this.saveAsLayout)

				// Apply All checkbox
				.on('click', '.style_apply_all', this.applyAll_events)

				/* On component import form save */
				.on('click', '#builder_submit_import_component_form', this.importRowModBuilderFormSave)

				.on('click', '.builder-lightbox .toggle_row', api.Views.Rows.default.prototype.toggleRow)
				.on('hover', '.builder-lightbox .row_menu', api.Views.BaseElement.prototype.actionMenuHover);

			this.moduleActions();
		},
		moduleSave: function( event ) {
			event.preventDefault();

			var temp_appended_data = {},
				entire_appended_data = api.activeModel.get('mod_settings'),
				form_state = document.getElementById('tfb_module_settings').getAttribute('data-form-state') || 'edit',
				previewOnly = false, $container = $('.current_selected_module').closest('[data-postid]');

			if (ThemifyBuilderCommon.Lightbox.previewButtonClicked($(this))) {
				previewOnly = true;
			}

			$('#tfb_module_settings .tfb_lb_option').each(function () {
				var option_value, option_class,
						this_option_id = $(this).attr('id');

				option_class = this_option_id + ' tfb_module_setting';

				if ($(this).hasClass('tfb_lb_wp_editor') && !$(this).hasClass('builder-field')) {
					if (typeof tinyMCE !== 'undefined') {
						option_value = tinyMCE.get(this_option_id).hidden === false ? tinyMCE.get(this_option_id).getContent() : switchEditors.wpautop(tinymce.DOM.get(this_option_id).value);
					} else {
						option_value = $(this).val();
					}
				}
				else if ($(this).hasClass('themify-checkbox')) {
					var cselected = [];
					$(this).find('.tf-checkbox:checked').each(function (i) {
						cselected.push($(this).val());
					});
					if (cselected.length > 0) {
						option_value = cselected.join('|');
					} else {
						option_value = '|';
					}
				}
				else if ($(this).hasClass('themify-layout-icon')) {
					if ($(this).find('.selected').length > 0) {
						option_value = $(this).find('.selected').attr('id');
					}
					else {
						option_value = $(this).children().first().attr('id');
					}
				}
				else if ($(this).hasClass('themify-option-query-cat')) {
					var parent = $(this).parent(),
							single_cat = parent.find('.query_category_single'),
							multiple_cat = parent.find('.query_category_multiple');

					if (multiple_cat.val() != '') {
						option_value = multiple_cat.val() + '|multiple';
					} else {
						option_value = single_cat.val() + '|single';
					}
				}
				else if ($(this).hasClass('themify_builder_row_js_wrapper')) {
					var row_items = [];
					$(this).find('.themify_builder_row').each(function () {
						var temp_rows = {};
						$(this).find('.tfb_lb_option_child').each(function () {
							var option_value_child,
									this_option_id_child = $(this).data('input-id');
								if(!this_option_id_child){
									this_option_id_child = $(this).attr('id');
								}
								
							if ($(this).hasClass('tf-radio-choice')) {
								option_value_child = ($(this).find(':checked').length > 0) ? $(this).find(':checked').val() : '';
							} else if ($(this).hasClass('themify-layout-icon')) {
								if(!this_option_id_child){
									this_option_id_child = $(this).attr('id');
								}
								if ($(this).find('.selected').length > 0) {
									option_value_child = $(this).find('.selected').attr('id');
								}
								else {
									option_value_child = $(this).children().first().attr('id');
								}
							}
							else if($(this).hasClass('themify-checkbox')){
								 option_value_child = [];
								 $(this).find(':checked').each(function(i){
									 option_value_child[i] = $(this).val();
								 });
							}
							else if ($(this).hasClass('tfb_lb_wp_editor')) {
								var text_id = $(this).attr('id');
								this_option_id_child = $(this).attr('name');
								if (typeof tinyMCE !== 'undefined') {
									option_value_child = tinyMCE.get(text_id).hidden === false ? tinyMCE.get(text_id).getContent() : switchEditors.wpautop(tinymce.DOM.get(text_id).value);
								} else {
									option_value_child = $(this).val();
								}
							}
							else {
								option_value_child = $(this).val();
							}

							if (option_value_child) {
								temp_rows[this_option_id_child] = option_value_child;
							}
						});
						row_items.push(temp_rows);
					});
					option_value = row_items;
				}
				else if ($(this).hasClass('tf-radio-input-container')) {
					option_value = $(this).find('input[name="' + this_option_id + '"]:checked').val();
				}
				else if ($(this).hasClass('module-widget-form-container')) {
					option_value = $(this).find(':input').themifySerializeObject();
				}
				else if ($(this).is('select, input, textarea')) {
					option_value = $(this).val();
				}

				if (option_value) {
					temp_appended_data[this_option_id] = option_value;
				}
			});

			if ( 'visual' === api.mode ) {
				if ('desktop' !== api.Frontend.activeBreakPoint) {
					var styleFields = $('#themify_builder_options_styling .tfb_lb_option').map(function() {
						return $(this).attr('id');
					}).get();

					// get current styling data
					var temp_style_data = _.pick(temp_appended_data, styleFields);

					// revert desktop styling data
					temp_appended_data = _.omit(temp_appended_data, styleFields);
					temp_appended_data = _.extend(temp_appended_data, _.pick(entire_appended_data, styleFields));

					// append breakpoint data
					temp_appended_data['breakpoint_' + api.Frontend.activeBreakPoint] = temp_style_data;

					// Check for another breakpoint
					_.each(_.omit(themifyBuilder.breakpoints, api.Frontend.activeBreakPoint), function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				} else {
					// Check for another breakpoint
					_.each(themifyBuilder.breakpoints, function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				}
			} else {

				// Append responsive data styling, prevent lost responsive styling
				_.each(themifyBuilder.breakpoints, function(value, key) {
					if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
						temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
					}
				});
			}

			api.vent.trigger('dom:observer:start', $container, { cid: api.activeModel.cid, value: { mod_settings: api.activeModel.get('mod_settings') } });

			api.activeModel.set({previewOnly: previewOnly}, {silent: true});
			api.activeModel.setData({mod_settings: temp_appended_data});

			if (!previewOnly) {
				ThemifyBuilderCommon.Lightbox.close()
			}

			if ('visual' !== api.mode) {
				api.vent.trigger('dom:observer:end', $container, { cid: api.activeModel.cid, value: { mod_settings: api.activeModel.get('mod_settings') } });
			}

			// hack: hide tinymce inline toolbar
			if ( $('.mce-inline-toolbar-grp:visible').length ) {
				$('.mce-inline-toolbar-grp:visible').hide();
			}
		},

		rowSaving: function( event ) {
			event.preventDefault();

			var temp_appended_data = $('#tfb_row_settings .tfb_lb_option').themifySerializeObject(),
				entire_appended_data = api.activeModel.get('styling') || {},
				temp_style_data = {}, $container = $('.current_selected_row').closest('[data-postid]');

			$('#tfb_row_settings').find('.themify_builder_row_js_wrapper').each(function () {
				var this_option_id = $(this).attr('id'),
					row_items = [];
				
				$(this).find('.themify_builder_row').each(function () {
					var temp_rows = {};
					
					$(this).find('.tfb_lb_option_child').each(function () {
						var option_value_child,
							this_option_id_child = $(this).data('input-id');
							if(!this_option_id_child){
								this_option_id_child = $(this).attr('id');
							}
							
						option_value_child = $(this).val();

						if (option_value_child) {
							temp_rows[this_option_id_child] = option_value_child;
						}
					});

					row_items.push(temp_rows);
				});

				if (row_items) {
					temp_appended_data[this_option_id] = row_items;
				}
			});

			if ( 'visual' === api.mode ) {
				if ('desktop' !== api.Frontend.activeBreakPoint) {
					var styleFields = $('#themify_builder_row_fields_styling .tfb_lb_option').map(function() {
						return $(this).attr('id');
					}).get();

					// get current styling data
					temp_style_data = _.pick(temp_appended_data, styleFields);

					// revert desktop styling data
					temp_appended_data = _.omit(temp_appended_data, styleFields);
					temp_appended_data = _.extend(temp_appended_data, _.pick(entire_appended_data, styleFields));

					// append breakpoint data
					temp_appended_data['breakpoint_' + api.Frontend.activeBreakPoint] = temp_style_data;

					// Check for another breakpoint
					_.each(_.omit(themifyBuilder.breakpoints, api.Frontend.activeBreakPoint), function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				} else {
					// Check for another breakpoint
					_.each(themifyBuilder.breakpoints, function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				}
			} else {

				// Append responsive data styling, prevent lost responsive styling
				_.each(themifyBuilder.breakpoints, function(value, key) {
					if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
						temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
					}
				});
			}

			api.vent.trigger('dom:observer:start', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });

			api.activeModel.set('styling', temp_appended_data);

			ThemifyBuilderCommon.Lightbox.close();

			if ('visual' !== api.mode) {
				api.vent.trigger('dom:observer:end', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });
			}

		},

		subRowSaving: function( event ) {
			event.preventDefault();

			var entire_appended_data = api.activeModel.get('styling') || {},
				temp_appended_data = $('#tfb_subrow_settings .tfb_lb_option').themifySerializeObject(),
				temp_style_data = {}, $container = $('.current_selected_sub_row').closest('[data-postid]');
			if ( 'visual' === api.mode ) {
				if ('desktop' !== api.Frontend.activeBreakPoint) {
					var styleFields = $('#tfb_subrow_settings .tfb_lb_option').map(function() {
						return $(this).attr('id');
					}).get();

					// get current styling data
					temp_style_data = temp_appended_data;

					// revert desktop styling data
					temp_appended_data = _.omit(temp_appended_data, styleFields);
					temp_appended_data = _.extend(temp_appended_data, _.pick(entire_appended_data, styleFields));

					// append breakpoint data
					temp_appended_data['breakpoint_' + api.Frontend.activeBreakPoint] = temp_style_data;

					// Check for another breakpoint
					_.each(_.omit(themifyBuilder.breakpoints, api.Frontend.activeBreakPoint), function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				} else {
					// Check for another breakpoint
					_.each(themifyBuilder.breakpoints, function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				}
			} else {
				// Append responsive data styling, prevent lost responsive styling
				_.each(themifyBuilder.breakpoints, function(value, key) {
					if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
						temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
					}
				});
			}

			api.vent.trigger('dom:observer:start', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });
			
			api.activeModel.set('styling', temp_appended_data );

			ThemifyBuilderCommon.Lightbox.close();

			if ('visual' !== api.mode) {
				api.vent.trigger('dom:observer:end', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });
			}
		},

		columnSaving: function( event ) {
			event.preventDefault();

			var entire_appended_data = api.activeModel.get('styling') || {},
				temp_appended_data = $('#tfb_column_settings .tfb_lb_option').themifySerializeObject(),
				temp_style_data = {}, $container = $('.current_selected_column').closest('[data-postid]');

			if ( 'visual' === api.mode ) {
				if ('desktop' !== api.Frontend.activeBreakPoint) {
					var styleFields = $('#tfb_column_settings .tfb_lb_option').map(function() {
						return $(this).attr('id');
					}).get();

					// get current styling data
					temp_style_data = temp_appended_data;

					// revert desktop styling data
					temp_appended_data = _.omit(temp_appended_data, styleFields);
					temp_appended_data = _.extend(temp_appended_data, _.pick(entire_appended_data, styleFields));

					// append breakpoint data
					temp_appended_data['breakpoint_' + api.Frontend.activeBreakPoint] = temp_style_data;

					// Check for another breakpoint
					_.each(_.omit(themifyBuilder.breakpoints, api.Frontend.activeBreakPoint), function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				} else {
					// Check for another breakpoint
					_.each(themifyBuilder.breakpoints, function(value, key) {
						if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
							temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
						}
					});
				}
			} else {
				// Append responsive data styling, prevent lost responsive styling
				_.each(themifyBuilder.breakpoints, function(value, key) {
					if (!_.isUndefined(entire_appended_data['breakpoint_' + key])) {
						temp_appended_data['breakpoint_' + key] = entire_appended_data['breakpoint_' + key];
					}
				});
			}

			api.vent.trigger('dom:observer:start', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });
			
			api.activeModel.set('styling', temp_appended_data );

			ThemifyBuilderCommon.Lightbox.close();

			if ('visual' !== api.mode) {
				api.vent.trigger('dom:observer:end', $container, { cid: api.activeModel.cid, value: { styling: api.activeModel.get('styling') } });
			}
		},

		deleteRowField: function( e ) {
			e.preventDefault();

			if (!confirm(themifyBuilder.rowDeleteConfirm)) {
				return;
			}

			var $row = $(this).closest('.themify_builder_row'),
				row_length = $row.closest('.themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length;
			if (row_length > 1) {
				$row.remove();
			}
			else {
				$row.hide();
			}
		},
		duplicateRowField: function (e) {
			e.preventDefault();
			var oriElems = $(this).closest('.themify_builder_row'),
				newElems = $(this).closest('.themify_builder_row').clone(),
				row_count = $('#tfb_module_settings .themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length + 1,
				number = row_count + Math.floor(Math.random() * 9);

			// fix wpeditor empty textarea
			newElems.find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
				var this_option_id = $(this).attr('id'),
						element_val;

				if (typeof tinyMCE !== 'undefined') {
					element_val = tinyMCE.get(this_option_id).hidden === false ? tinyMCE.get(this_option_id).getContent() : switchEditors.wpautop(tinymce.DOM.get(this_option_id).value);
				} else {
					element_val = $('#' + this_option_id).val();
				}
				$(this).val(element_val);
				$(this).addClass('clone');
			});

			// fix textarea field clone
			newElems.find('textarea:not(.tfb_lb_wp_editor)').each(function (i) {
				var insertTo = oriElems.find('textarea').eq(i).val();
				if (insertTo != '') {
					$(this).val(insertTo);
				}
			});

			// fix radio button clone
			newElems.find('.themify-builder-radio-dnd').each(function (i) {
				var oriname = $(this).attr('name');
				$(this).attr('name', oriname + '_' + row_count);
				$(this).attr('id', oriname + '_' + row_count + '_' + i);
				$(this).next('label').attr('for', oriname + '_' + row_count + '_' + i);
			});

			newElems.find('.themify-builder-plupload-upload-uic').each(function (i) {
				$(this).attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-upload-ui');
				$(this).find('input[type="button"]').attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-browse-button');
				$(this).addClass('plupload-clone');
			});
			newElems.find('select').each(function (i) {
				var orival =  oriElems.find('select').eq(i).find('option:selected').val();
				$(this).find('option[value="'+orival+'"]').prop('selected',true);
			});
			newElems.insertAfter(oriElems).find('.themify_builder_dropdown').hide();

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child.clone').each(function (i) {
				var element = $(this),
						parent_child = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();

				var oriname = element.attr('name');
				element.attr('id', oriname + '_' + row_count + number + '_' + i);
				element.attr('class').replace('wp-editor-area', '');

				element.appendTo(parent_child).wrap('<div class="wp-editor-wrap"/>');

			});

			api.Utils.addNewWPEditor();
			api.Utils.builderPlupload('new_elemn');
			
			if(newElems.find('.builderColorSelect').length>0){
				newElems.find('.builderColorSelect').minicolors('destroy').removeAttr('maxlength');
				api.Utils.setColorPicker(newElems);
			}
		},

		rowOptAddRow: function (e) {
			var parent = $(this).parent().prev(),
				template = parent.find('.themify_builder_row').first().clone(),
				row_count = $('.themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length + 1,
				number = row_count + Math.floor(Math.random() * 9);

			// clear form data
			template.removeClass('collapsed').find('.themify_builder_row_content').show();
			template.find('.themify-builder-radio-dnd').each(function (i) {
				var oriname = $(this).attr('name');
				$(this).attr('name', oriname + '_' + row_count).not(':checked').prop('checked', false);
				$(this).attr('id', oriname + '_' + row_count + '_' + i);
				$(this).next('label').attr('for', oriname + '_' + row_count + '_' + i);
			});

			template.find('.themify-layout-icon a').removeClass('selected');

			template.find('.thumb_preview').each(function () {
				$(this).find('.img-placeholder').html('').parent().hide();
			});
			template.find('input[type="text"], textarea').each(function () {
				$(this).val('');
			});
			template.find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
				$(this).addClass('clone');
			});
			template.find('.themify-builder-plupload-upload-uic').each(function (i) {
				$(this).attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-upload-ui');
				$(this).find('input[type="button"]').attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-browse-button');
				$(this).addClass('plupload-clone');
			});

			// Fix color picker input
			template.find('.builderColorSelectInput').each(function () {
				var thiz = $(this),
						input = thiz.clone().val(''),
						parent = thiz.closest('.themify_builder_field');
				thiz.prev().minicolors('destroy').removeAttr('maxlength');
				parent.find('.colordisplay').wrap('<div class="themify_builder_input" />').before('<span class="builderColorSelect"><span></span></span>').after(input);
				api.Utils.setColorPicker(parent);
			});

			$(template).appendTo(parent).show();

			$('#tfb_row_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child.clone').each(function (i) {
				var element = $(this),
					parent_child = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();

				var oriname = element.attr('name');
				element.attr('id', oriname + '_' + row_count + number + '_' + i);
				element.attr('class').replace('wp-editor-area', '');

				element.appendTo(parent_child).wrap('<div class="wp-editor-wrap"/>');

			});

			if (e.which) {
				api.Utils.addNewWPEditor();
				api.Utils.builderPlupload('new_elemn');
			}

			e.preventDefault();
		},

		moduleOptAddRow: function (e) {
			var parent = $(this).parent().prev(),
				template = parent.find('.themify_builder_row').first().clone(),
				row_count = $('.themify_builder_row_js_wrapper').find('.themify_builder_row:visible').length + 1,
				number = row_count + Math.floor(Math.random() * 9);

			// clear form data
			template.removeClass('collapsed').find('.themify_builder_row_content').show();
			template.find('.themify-builder-radio-dnd').each(function (i) {
				var oriname = $(this).attr('name');
				$(this).attr('name', oriname + '_' + row_count).prop('checked', false);
				$(this).attr('id', oriname + '_' + row_count + '_' + i);
				$(this).next('label').attr('for', oriname + '_' + row_count + '_' + i);
				if( $(this).is('[data-checked]') ) {
					var $self = $(this);
					$(this).attr( 'checked', true );
					setTimeout( function() { $self.trigger( 'change' ) }, 100 );
				}
			});

			// Hide conditional inputs
			template.find( '[data-binding]' ).each( function() {
				var bindingData = $(this).data( 'binding' );
				try {
					var hideEl = '.' + bindingData.empty.hide.join( ', .' );
					template.find( hideEl ).children().hide();
				} catch(e) {}
			} );

			template.find('.themify-layout-icon a').removeClass('selected');

			template.find('.thumb_preview').each(function () {
				$(this).find('.img-placeholder').html('').parent().hide();
			});
			template.find('input[type=text], textarea').each(function () {
				$(this).val('');
			});
			template.find('.tfb_lb_wp_editor.tfb_lb_option_child').each(function () {
				$(this).addClass('clone');
			});
			template.find('.themify-builder-plupload-upload-uic').each(function (i) {
				$(this).attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-upload-ui');
				$(this).find('input[type=button]').attr('id', 'pluploader_' + row_count + number + i + 'themify-builder-plupload-browse-button');
				$(this).addClass('plupload-clone');
			});

			// Fix color picker input
			template.find('.builderColorSelectInput').each(function () {
				var thiz = $(this),
						input = thiz.clone().val(''),
						parent = thiz.closest('.themify_builder_field');
				thiz.prev().minicolors('destroy').removeAttr('maxlength');
				parent.find('.colordisplay').wrap('<div class="themify_builder_input" />').before('<span class="builderColorSelect"><span></span></span>').after(input);
				api.Utils.setColorPicker(parent);
			});

			$(template).appendTo(parent).show();

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.tfb_lb_option_child.clone').each(function (i) {
				var element = $(this),
						parent_child = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();

				var oriname = element.attr('name');
				element.attr('id', oriname + '_' + row_count + number + '_' + i);
				element.attr('class').replace('wp-editor-area', '');

				element.appendTo(parent_child).wrap('<div class="wp-editor-wrap"/>');

			});

			if (e.which) {
				api.Utils.addNewWPEditor();
				api.Utils.builderPlupload('new_elemn');
			}

			e.preventDefault();
		},

		builderImportSubmit: function (e) {
			e.preventDefault();

			var postData = $(this).closest('form').serialize();

			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action: 'builder_import_submit',
					nonce: themifyBuilder.tfb_load_nonce,
					data: postData,
					importTo: themifyBuilder.post_ID
				},
				success: function (data) {
					ThemifyBuilderCommon.Lightbox.close();
					window.location.reload();
				}
			});
		},

		templateSelected: function (event) {
			event.preventDefault();

			var $this = $(this).closest( '.layout_preview' );
			var options = {
				buttons: {
					no: {
						label: 'Replace Existing Layout'
					},
					yes: {
						label: 'Append Existing Layout'
					}
				}
			};

			ThemifyBuilderCommon.LiteLightbox.confirm( themifyBuilder.confirm_template_selected, function( response ){
				var action = '';
				if ( 'no' == response ) {
					action = 'tfb_set_layout';
				} else {
					action = 'tfb_append_layout';
				}
				var args = {
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'json',
					data: {
						action: action,
						nonce: themifyBuilder.tfb_load_nonce,
						layout_slug: $this.data('slug'),
						current_builder_id: themifyBuilder.post_ID,
						layout_group: $this.data('group')
					},
					success: function (data) {
						ThemifyBuilderCommon.Lightbox.close();
						if (data.status == 'success') {
							if ( 'visual' === api.mode ) window.location.hash = '#builder_active';
							window.location.reload()
						} else {
							alert(data.msg);
						}
					}
				};
				if( $this.data('group') == 'pre-designed' ) {
					ThemifyBuilderCommon.showLoader( 'show' );
					var file = 'https://themify.me/themify-layouts/' + $this.data( 'slug' ) + '.txt';
					jQuery.get( file, null, null, 'text' )
					.done( function( builder_data ){
						args.data.builder_data = builder_data;
						$.ajax( args );
					} )
					.fail(function( jqxhr, textStatus, error ){
						ThemifyBuilderCommon.LiteLightbox.alert( 'There was an error in loading layout, please try again later, or you can download this file: ('+ file +') and then import manually (http://themify.me/docs/builder#import-export).' );
					})
					.always( function(){
						ThemifyBuilderCommon.showLoader();
					} )
				} else {
					$.ajax( args );
				}
			}, options );
		},

		saveAsLayout: function (event) {
			event.preventDefault();

			var submit_data = $('#tfb_save_layout_form').serialize();
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data: {
					action: 'tfb_save_custom_layout',
					nonce: themifyBuilder.tfb_load_nonce,
					form_data: submit_data
				},
				success: function (data) {
					if (data.status == 'success') {
						ThemifyBuilderCommon.Lightbox.close();
					} else {
						alert(data.msg);
					}
				}
			});
		},

		// "Apply all" // apply all events
		applyAll_events: function($selector) {
			var $this = $(this),
				$fields = $this.closest('.themify_builder_field').prevUntil('h4');

			if ($this.prop('checked')) {
				var $fire = true;
				$fields.not(':last').slideUp(function(){
					if($fire){
						
						$fields.last().find('input[type="text"], select').each(function() {
								var ev = ($(this).prop('tagName') == 'SELECT') ? 'change' : 'keyup';
								$(this).trigger(ev);
						});
						$fire = false;
					}
				});
				$fields.last().children('.themify_builder_input').css('color', '#FFF');
			} else {
				$fields.slideDown();
				$fields.last().children('.themify_builder_input').css('color', '');
			}
		},

		importRowModBuilderFormSave: function(event) {
			event.preventDefault();

			var $form = $("#tfb_imp_component_form");
			var component = $form.find("input[name='component']").val();

			var $container = $('[data-cid="'+ api.activeModel.cid +'"]').closest('[data-postid]');

			api.vent.trigger('dom:observer:start', $container, { cid: api.activeModel.cid, value: api.activeModel.toJSON() } );

			switch (component) {
				case 'row':
					var $rowDataField = $form.find('#tfb_imp_row_data_field');
					var rowDataPlainObject = JSON.parse($rowDataField.val());

					if (!rowDataPlainObject.hasOwnProperty('component_name') ||
						rowDataPlainObject['component_name'] !== 'row') {
						ThemifyBuilderCommon.alertWrongPaste();
						return;
					}

					ThemifyBuilderCommon.Lightbox.close();

					api.activeModel.setData( rowDataPlainObject );
						
					api.vent.trigger('dom:builder:change');

					break;

				case 'sub-row':
					var $subRowDataField = $form.find('#tfb_imp_sub_row_data_field');
					var subRowDataPlainObject = JSON.parse($subRowDataField.val());

					if (!subRowDataPlainObject.hasOwnProperty('component_name') ||
						subRowDataPlainObject['component_name'] !== 'sub-row') {
						ThemifyBuilderCommon.alertWrongPaste();
						return;
					}

					ThemifyBuilderCommon.Lightbox.close();

					api.activeModel.setData( subRowDataPlainObject );
						
					api.vent.trigger('dom:builder:change');
					
					break;

				case 'module':
					var $modDataField = $form.find('#tfb_imp_module_data_field');
					var modDataPlainObject = JSON.parse($modDataField.val());

					if (!modDataPlainObject.hasOwnProperty('component_name') ||
						modDataPlainObject['component_name'] !== 'module') {
						ThemifyBuilderCommon.alertWrongPaste();
						return;
					}

					ThemifyBuilderCommon.Lightbox.close();

					api.activeModel.setData( modDataPlainObject );
						
					api.vent.trigger('dom:builder:change');
					
					break;

				case 'column':
				case 'sub-column':
					var $colDataField = $form.find('#tfb_imp_'+ component.replace('-', '_') +'_data_field');
					var colDataPlainObject = JSON.parse($colDataField.val());

					if (!colDataPlainObject.hasOwnProperty('component_name') ||
						colDataPlainObject['component_name'] !== component) {
						ThemifyBuilderCommon.alertWrongPaste();
						return;
					}

					var $column = $('.current_selected_column'),
						$row = 'column' === component ? $column.closest('.themify_builder_row') : $column.closest('.themify_builder_sub_row'),
						row_index = $row.index(),
						col_index = $column.index();

					colDataPlainObject['column_order'] = col_index;
					colDataPlainObject['grid_class'] = $column.prop('class').replace('themify_builder_col', '');

					if ( 'column' === component ) {
						colDataPlainObject['row_order'] = row_index;
					} else {
						colDataPlainObject['sub_row_order'] = row_index;
						colDataPlainObject['row_order'] = $column.closest('.themify_builder_row').index();
						colDataPlainObject['col_order'] = $column.parents('.themify_builder_col').index();
					}

					ThemifyBuilderCommon.Lightbox.close();

					api.activeModel.setData( colDataPlainObject );
						
					api.vent.trigger('dom:builder:change');

					break;
			}

			if ( 'visual' !== api.mode ) {
				api.vent.trigger('dom:observer:end', $container, { cid: api.activeModel.cid, value: api.activeModel.toJSON() } );
			}

		},

		moduleActions: function() {
			var $body = $('body');
			$body.on('change', '.module-widget-select-field', function() {
				var $seclass = $(this).val(),
					id_base = $(this).find(':selected').data('idbase');

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data: {
						action: 'module_widget_get_form',
						tfb_load_nonce: themifyBuilder.tfb_load_nonce,
						load_class: $seclass,
						id_base: id_base
					},
					success: function(data) {
						var $newElems = $(data);

						$('.module-widget-form-placeholder').html($newElems);
						$('#themify_builder_lightbox_container').each(function() {
							var $this = $(this).find('#instance_widget');
							$this.find('select').wrap('<div class="selectwrapper"></div>');
						});
						$('.selectwrapper').click(function() {
							$(this).toggleClass('clicked');
						});

					}
				});
			});

			$body.on('editing_module_option', function(e, settings) {
				var $field = $('#tfb_module_settings .tfb_lb_option.module-widget-select-field');
				if ($field.length == 0)
					return;

				var $seclass = $field.val(),
					id_base = $field.find(':selected').data('idbase'),
					$instance = settings.instance_widget;

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data: {
						action: 'module_widget_get_form',
						tfb_load_nonce: themifyBuilder.tfb_load_nonce,
						load_class: $seclass,
						id_base: id_base,
						widget_instance: $instance
					},
					success: function(data) {
						var $newElems = $(data);
						$('.module-widget-form-placeholder').html($newElems);
					}
				});
			});
		},

	};

	api.Views.ModulePanel = Backbone.View.extend({
		events: {
			'click .add_module' : 'addModule',
			'click .slide_builder_module_panel' : 'togglePanel'
		},
		initialize: function( options ) {
			this.$el.find('.themify_builder_module').not('.themify_is_premium_module').draggable({
				appendTo: "body",
				helper: "clone",
				revert: 'invalid',
				connectToSortable: '.themify_module_holder'
			});
		},
		addModule: function( event ) {
			event.preventDefault();

			var moduleView = api.Views.init_module( { mod_name: $(event.currentTarget).closest('.themify_builder_module').data('module-slug') }, api.Instances.Builder[0].type ),
				dest = api.Instances.Builder[0].$el.find('.themify_builder_row:visible').first().find('.themify_module_holder').first(),
				$newElems = moduleView.view.render().$el,
				position = $newElems.appendTo(dest);

			$('html,body').animate({scrollTop: position.offset().top - 300}, 500);
			moduleView.view.trigger('edit');
		},
		togglePanel: function( event ) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			api.Frontend.slidePanelOpen = $this.parent().hasClass('slide_builder_module_state_down');
			$this.parent().toggleClass('slide_builder_module_state_down');
			$this.next().slideToggle();
		}
	});

	api.Views.Nav = Backbone.View.extend({
		events: {
			'click .themify_builder_dup_link' : 'duplicate',
			'click .themify_builder_import_page' : 'importPage',
			'click .themify_builder_import_post' : 'importPost',
			'click .themify_builder_import_file' : 'importFile',
			'click .themify_builder_load_layout' : 'loadLayout',
			'click .themify_builder_save_layout' : 'saveLayout',
			'click .themify_builder_load_revision' : 'loadRevision',
			'click .themify_builder_save_revision' : 'saveRevision'
		},
		duplicate: function( event ) {
			event.preventDefault();

			var self = this,
				reply = confirm(themifyBuilder.confirm_on_duplicate_page);
			if (reply) {
				api.Utils.saveBuilder(true).done(function(){
					self.duplicatePageAjax();
				});
			} else {
				this.duplicatePageAjax();
			}
		},
		duplicatePageAjax: function () {
			$.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				dataType: 'json',
				data:
				{
					action: 'tfb_duplicate_page',
					tfb_load_nonce: themifyBuilder.tfb_load_nonce,
					tfb_post_id: themifyBuilder.post_ID,
					tfb_is_admin: 'visual' === api.mode ? 0 : 1
				},
				beforeSend: function (xhr) {
					ThemifyBuilderCommon.showLoader('show');
				},
				success: function (data) {
					ThemifyBuilderCommon.showLoader('hide');
					var new_url = data.new_url.replace(/\&amp;/g, '&');
					window.location.href = new_url;
				}
			});
		},
		importPage: function (event) {
			event.preventDefault();
			this.builderImport('page');
		},
		importPost: function (event) {
			event.preventDefault();
			this.builderImport('post');
		},
		builderImport: function (imType) {
			var options = {
				dataType: 'html',
				data: {
					action: 'builder_import',
					type: imType
				}
			};
			ThemifyBuilderCommon.Lightbox.open(options, null);
		},
		importFile: function (event) {
			event.preventDefault();
			var options = {
				dataType: 'html',
				data: {
					action: 'builder_import_file'
				}
			},
			callback = this.builderImportPlupload;

			if (confirm(themifyBuilder.importFileConfirm)) {
				ThemifyBuilderCommon.Lightbox.open(options, callback);
			}
		},
		builderImportPlupload: function () {
			var $builderPluploadUpload = $(".themify-builder-plupload-upload-uic");

			if ($builderPluploadUpload.length > 0) {
				var pconfig = false;
				$builderPluploadUpload.each(function () {
					var $this = $(this),
						id1 = $this.attr("id"),
						imgId = id1.replace("themify-builder-plupload-upload-ui", "");

					pconfig = JSON.parse(JSON.stringify(themify_builder_plupload_init));

					pconfig["browse_button"] = imgId + pconfig["browse_button"];
					pconfig["container"] = imgId + pconfig["container"];
					pconfig["drop_element"] = imgId + pconfig["drop_element"];
					pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
					pconfig["multipart_params"]["imgid"] = imgId;
					pconfig["multipart_params"]["_ajax_nonce"] = themifyBuilder.tfb_load_nonce;
					;
					pconfig["multipart_params"]['topost'] = themifyBuilder.post_ID;

					var uploader = new plupload.Uploader(pconfig);

					uploader.bind('Init', function (up) {
					});

					uploader.init();

					// a file was added in the queue
					uploader.bind('FilesAdded', function (up, files) {
						up.refresh();
						up.start();
						ThemifyBuilderCommon.showLoader('show');
					});

					uploader.bind('Error', function (up, error) {
						var $promptError = $('.prompt-box .show-error');
						$('.prompt-box .show-login').hide();
						$promptError.show();

						if ($promptError.length > 0) {
							$promptError.html('<p class="prompt-error">' + error.message + '</p>');
						}
						$(".overlay, .prompt-box").fadeIn(500);
					});

					// a file was uploaded
					uploader.bind('FileUploaded', function (up, file, response) {
						var json = JSON.parse(response['response']), status;

						if ('200' == response['status'] && !json.error) {
							status = 'done';
						} else {
							status = 'error';
						}

						$("#themify_builder_alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function () {
							$(this).removeClass(status);
						});

						if (json.error) {
							alert(json.error);
							return;
						}

						$('#themify_builder_alert').promise().done(function () {
							ThemifyBuilderCommon.Lightbox.close();
							window.location.reload();
						});

					});

				});
			}
		},
		loadLayout: function (event) {
			var api = this;
			event.preventDefault();
			var options = {
				dataType: 'html',
				data: {
					action: 'tfb_load_layout'
				}
			};

			ThemifyBuilderCommon.Lightbox.open(options, function(){
				var container = $('#themify_builder_tabs_pre-designed');
				var loader = $( '<div class="themify-builder-alert busy"></div>' ).appendTo( container );
				$.getJSON( 'https://themify.me/themify-layouts/index.json' )
				.done(function( data ){
					var template = wp.template( 'themify-builder-layout-item' ),
						categories = [];

					container.append( template( data ) );
					api.layoutLayoutsList();
					container.find( 'li.layout_preview_list' ).each(function(){
						var cat = $( this ).data( 'category' ).split( ',' );
						$.each( cat, function( i, v ){
							if( '' !== v && -1 == $.inArray( v, categories ) ) {
								categories.push( v );
								$( '#themify_builder_pre-designed-filter' ).append( '<li><a href="#">' + v + '</a></li>' );
							}
						} );
					});
					$( '#themify_builder_pre-designed-filter' ).show().find( 'a' ).click( function(e){
						e.preventDefault();
						if( ! $( this ).hasClass( 'selected' ) ) {
							if( $( this ).hasClass( 'all' ) ) {
								container.find( '.layout_preview_list' ).css( 'display', 'block' );
							} else {
								var cat = $( this ).text();
								container.find( '.layout_preview_list' ).css( 'display', 'none' ).filter( '[data-category*="' + cat + '"]' ).css( 'display', 'block' );
							}
							$( this ).addClass( 'selected' ).parent().siblings().find( 'a' ).removeClass( 'selected' );
							api.layoutLayoutsList();
						}
					} );
					$('#themify_builder_layout_search').on( 'keyup', function(){
						var s = $( this ).val();
						if( '' == s ) {
							container.find( '.layout_preview_list' ).css( 'display', 'block' );
						} else {
							if( ! $( '#themify_builder_pre-designed-filter a.all.selected' ).length ) {
								$( '#themify_builder_pre-designed-filter a.all' ).click();
							}
							container.find( '.layout_preview_list' ).hide();
							container.find( '.layout_title:contains('+ s +')' ).each(function(){
								$( this ).closest( '.layout_preview_list' ).show();
							});
						}
						api.layoutLayoutsList();
					} );

					// when switching between different tabs, redo the layout
					$( '#themify_builder_load_template_form .themify_builder_tabs' ).on('tabsactivate', function( e, ui ){
						api.layoutLayoutsList();
					});
				}).fail(function( jqxhr, textStatus, error ){
					ThemifyBuilderCommon.LiteLightbox.alert( $( '#themify_builder_load_layout_error' ).show().text() );
				}).always(function(){
					loader.fadeOut();
				});
			});
		},
		layoutLayoutsList: function(){
			$( '#themify_builder_load_template_form .themify_builder_layout_lists' ).each(function(){
				var el = $( this );
				if( ! el.find( '.grid-sizer' ).length ) {
					el.append( '<li class="grid-sizer"></li>' );
				}
				el.imagesLoaded(function(){
					el.masonry({
						itemSelector: '.layout_preview_list',
						columnWidth: '.grid-sizer',
						percentPosition: true,
						gutter: 10
					});
				});
			});
		},
		saveLayout: function (event) {
			event.preventDefault();
			var options = {
				data: {
					action: 'tfb_custom_layout_form',
					postid: themifyBuilder.post_ID
				}
			},
			callback = function () {
				// plupload init
				api.Utils.builderPlupload('normal');
			};
			ThemifyBuilderCommon.Lightbox.open(options, callback);
		},
		loadRevision: function(event) {
			event.preventDefault();
			event.stopPropagation();
			var options = {
				data: {
					action: 'tfb_load_revision_lists',
					postid: themifyBuilder.post_ID,
					tfb_load_nonce: themifyBuilder.tfb_load_nonce,
				}
			};
			ThemifyBuilderCommon.Lightbox.open(options, function() {
				$('.themify_builder_options_tab li:first-child').addClass('current');
			});
		},
		saveRevision: function(event) {
			event.preventDefault();
			event.stopPropagation();
			ThemifyBuilderCommon._saveRevision();
		},

	});

	api.Views.EditorNavComponent = Backbone.View.extend({
		events: {
			'click .themify-builder-front-save-title' : 'savePanel',
			'click .themify-builder-front-close' : 'panelClose',
			'click .themify-builder-revision-dropdown-panel' : 'toggleRevDropdown',
			'click .js-themify-builder-load-revision' : 'loadRevision',
			'click .js-themify-builder-save-revision' : 'saveRevision',
			'click .js--themify_builder_breakpoint_switcher' : 'breakPointSwitcher',
			'click .js-themify-builder-undo-btn' : 'actionUndo',
			'click .js-themify-builder-redo-btn' : 'actionRedo'
		},
		initialize: function() {
			// Listen to any changes of undo/redo
			if ( document.getElementsByClassName('themify_builder_undo_tools').length ) {
				ThemifyBuilderCommon.undoManager.instance.setCallback(this.undoManagerCallback.bind(this));
				this.updateUndoBtns();
				api.vent.on('dom:observer:start', function( $container, data ){
					data = data || {};
					ThemifyBuilderCommon.undoManager.setStartValue( $container[0].innerHTML );
					ThemifyBuilderCommon.undoManager.setStartData( data );
					window.console && console.log('dom:observer:start');
				})
				.on('dom:observer:end', function( $container, newData ){
					newData = newData || {};
					var startValue = ThemifyBuilderCommon.undoManager.getStartValue(),
						startData = ThemifyBuilderCommon.undoManager.getStartData(),
						newValue = $container[0].innerHTML;
					if (startValue !== newValue) {
						ThemifyBuilderCommon.undoManager.set($container, startValue, newValue, startData, newData);
						api.editing = true;
						window.console && console.log('dom:observer:end');
					}
				});
			}
		},
		savePanel: function( event ) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$this.parent().find('ul').removeClass('hover');
			if (!$this.hasClass('disabled')) {
				api.Utils.saveBuilder(true).fail(function() {
					alert(themifyBuilder.errorSaveBuilder);
				});
			}
		},
		panelClose: function( event ) {
			api.toggleFrontEdit( event );
		},
		toggleRevDropdown: function(event) {
			var $this = $(event.currentTarget);
			if (event.type == 'click' && $this.hasClass('themify-builder-revision-dropdown-panel')) {
				$this.find('ul').toggleClass('hover');
			}
			if (event.type == 'mouseenter' && $this.hasClass('themify-builder-front-save-title')) {
				$this.next().find('ul').removeClass('hover');
			}
			if (event.type == 'click' && $this.hasClass('themify-builder-front-save')) {
				$this.find('ul').removeClass('hover');
			}
		},
		loadRevision: function( event ) {
			api.Views.Nav.prototype.loadRevision( event );
		},
		saveRevision: function( event ) {
			api.Views.Nav.prototype.saveRevision( event );
		},
		breakPointSwitcher: function(event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				breakpoint = 'desktop',
				animateW = $(window).width(),
				prevBreakPoint = api.Frontend.activeBreakPoint;
			if ($this.hasClass('breakpoint-tablet')) {
				breakpoint = 'tablet';
			} else if ($this.hasClass('breakpoint-tablet-landscape')) {
				breakpoint = 'tablet_landscape';
			} else if ($this.hasClass('breakpoint-mobile')) {
				breakpoint = 'mobile';
			}
			api.Frontend.activeBreakPoint = breakpoint;
			$this.parent().addClass('selected').siblings().removeClass('selected');
			$('body').removeClass('builder-active-breakpoint--desktop builder-active-breakpoint--tablet builder-active-breakpoint--tablet_landscape builder-active-breakpoint--mobile')
			.addClass('builder-active-breakpoint--' + breakpoint );
			
			if ('desktop' == breakpoint) {
				$('.themify_builder_site_canvas').css('width', animateW);
				$('.themify_builder_workspace_container').hide();
				$('.themify_builder_front_panel').removeClass('builder-disable-module-draggable');
				$('.themify_builder_module_panel').removeClass('themify_builder_panel_state_inline');
				$('.themify_builder_module_panel .themify_builder_module').draggable('enable');
				document.body.style.height = ''; // reset the body height
			} else {
				if ( 'desktop' == prevBreakPoint ) {
					api.Frontend.responsiveFrame.sync();
				}
				var customBreak = $( '.themify_builder_site_canvas' ).data( 'preview-breakpoints' ),
					breakArr = themifyBuilder.breakpoints[breakpoint].toString().split('-');

				animateW = typeof customBreak[breakpoint] !== 'undefined' ? customBreak[breakpoint] : breakArr[breakArr.length - 1];
				$('.themify_builder_workspace_container').show();
				$('.themify_builder_site_canvas').removeAttr( 'data-preview-breakpoints' ).css('width', animateW);
				$('.themify_builder_front_panel').addClass('builder-disable-module-draggable');
				$('.themify_builder_module_panel .themify_builder_module').draggable('disable');
				$('.themify_builder_module_panel').addClass('themify_builder_panel_state_inline');
				var $padding_bottom = breakpoint==='mobile'? $('body').outerHeight(true) - api.Frontend.responsiveFrame.$el.outerHeight(true):-1;
				if($padding_bottom<0){
					$padding_bottom = 180;
				}
				$('body').css('padding-bottom',$padding_bottom);
				document.body.style.height = $(api.Frontend.responsiveFrame.contentWindow.document).height() + 'px'; // Set the same height as iframe content height
				api.Frontend.responsiveFrame.contentWindow.scrollTo(0, $(window).scrollTop());
			}
		},
		// Undo/Redo Functionality
		btnUndo: document.getElementsByClassName('js-themify-builder-undo-btn')[0],
		btnRedo: document.getElementsByClassName('js-themify-builder-redo-btn')[0],
		actionUndo: function(event) {
			event.preventDefault();
			if (event.currentTarget.classList.contains('disabled'))
				return;
			ThemifyBuilderCommon.undoManager.instance.undo();
			this.updateUndoBtns();
			this.undoUpdateCallback();
		},
		actionRedo: function(event) {
			event.preventDefault();
			if (event.currentTarget.classList.contains('disabled'))
				return;
			ThemifyBuilderCommon.undoManager.instance.redo();
			this.updateUndoBtns();
			this.undoUpdateCallback();
		},
		updateUndoBtns: function() {
			if (ThemifyBuilderCommon.undoManager.instance.hasUndo()) {
				this.btnUndo.classList.remove('disabled');
			} else {
				this.btnUndo.classList.add('disabled');
			}

			if (ThemifyBuilderCommon.undoManager.instance.hasRedo()) {
				this.btnRedo.classList.remove('disabled');
			} else {
				this.btnRedo.classList.add('disabled');
			}
		},
		undoManagerCallback: function() {
			window.console && console.log('undo callback');
			this.updateUndoBtns();
			ThemifyBuilderCommon.undoManager.startValue = null; // clear temp
		},
		undoUpdateCallback: function() {
			ThemifyBuilderCommon.columnDrag(null, false);
			$('.themify_builder_module_front_overlay').hide();
			$('.themify_builder_dropdown_front').removeAttr('style');
			$('.active_module').css({ width: '', position: '', left: '', top: '', zIndex: '' }); // reset ui dragger
			$('.themify_builder_col').css({height: '', position: '', zIndex: ''});

			if ( 'visual' === api.mode ) {
				api.Utils.loadContentJs();
			}

			api.vent.trigger('dom:builder:change');

			if ( 'visual' === api.mode ) {
				api.Frontend.responsiveFrame.doSync(); // sync responsive frame
			}
		}
	});

	api.Views.bindEvents = function() {
		ThemifyBuilderCommon.setupLoader();
		ThemifyBuilderCommon.Lightbox.setup();
		ThemifyBuilderCommon.LiteLightbox.modal.on('attach', function(){
			this.$el.addClass('themify_builder_lite_lightbox_modal');
		});

		api.Utils.setupTooltips();
		api.Utils.mediaUploader();
		api.Utils.openGallery();
	};

	api.Utils = {
		clearClass: 'col6-1 col5-1 col4-1 col4-2 col4-3 col3-1 col3-2 col2-1 col-full',
		gridClass: ['col-full', 'col4-1', 'col4-2', 'col4-3', 'col3-1', 'col3-2', 'col6-1', 'col5-1'],
		tfb_hidden_editor_object: null,
		_addNewColumn: function (params, $context) {
			var columnView = api.Views.init_column( { grid_class : params.newclass, component_name: params.component }, params.type );
			$context.append( columnView.view.render().$el );
		},
		filterClass: function (str) {
			var grid = this.gridClass.concat(['first', 'last']),
				n = str.split(' '),
				new_arr = [];

			for (var i = 0; i < n.length; i++) {
				if ($.inArray(n[i], grid) > -1) {
					new_arr.push(n[i]);
				}
			}

			return new_arr.join(' ');
		},
		_getRowSettings: function( $base, index ) {
			var self = this,
				option_data = {},
				cols = {};

			// cols
			$base.find('.themify_builder_row_content').children('.themify_builder_col').each(function (c) {
				var grid_class = self.filterClass($(this).attr('class')),
					modules = {};
				// mods
				$(this).find('.themify_module_holder').first().children().each(function (m) {
					if ($(this).hasClass('active_module')) {
						var mod_name = $(this).data('mod-name'),
							mod_elems = $(this).find('.themify_module_settings'),
							mod_settings = JSON.parse(mod_elems.find('script[type="text/json"]').text());
						modules[m] = {'mod_name': mod_name, 'mod_settings': mod_settings};
					}

					// Sub Rows
					if ($(this).hasClass('themify_builder_sub_row')) {
						modules[m] = self._getSubRowSettings($(this), m);
					}
				});

				cols[c] = {
					'column_order': c,
					'grid_class': grid_class,
					'grid_width':$(this).prop('style').width?parseFloat($(this).prop('style').width):false,
					'modules': modules
				};

				// get column styling
				if ($(this).children('.column-data-styling').length > 0) {
					var $data_styling = $.parseJSON( $(this).children('.column-data-styling').attr('data-styling') );
					if ('object' === typeof $data_styling)
						cols[ c ].styling = $data_styling;
				}
			});

			option_data = {
				row_order: index,
				gutter: $base.data('gutter'),
				column_alignment: $base.data('column-alignment'),
				cols: cols
			};

			// get row styling
			if ($base.find('.row-data-styling').length > 0) {
				var $data_styling = $.parseJSON( $base.find('.row-data-styling').attr('data-styling') );
				if ('object' === typeof $data_styling)
					option_data.styling = $data_styling;
			}
			return option_data;
		},
		_getSubRowSettings: function( $subRow, subRowOrder ) {
			var self = this,
				option_data = {},
				sub_cols = {};
			$subRow.find('.themify_builder_col').each(function (sub_col) {
				var sub_grid_class = self.filterClass($(this).attr('class')),
					sub_modules = {};

				$(this).find('.active_module').each(function (sub_m) {
					var sub_mod_name = $(this).data('mod-name'),
							sub_mod_elems = $(this).find('.themify_module_settings'),
							sub_mod_settings = JSON.parse(sub_mod_elems.find('script[type="text/json"]').text());
					sub_modules[sub_m] = {'mod_name': sub_mod_name, 'mod_settings': sub_mod_settings};
				});

				sub_cols[ sub_col ] = {
					column_order: sub_col,
					grid_class: sub_grid_class,
					grid_width:$(this).prop('style').width?parseFloat($(this).prop('style').width):false,
					modules: sub_modules
				};

				// get sub-column styling
				if ($(this).children('.column-data-styling').length > 0) {
					var $data_styling = $.parseJSON( $(this).children('.column-data-styling').attr('data-styling') );
					if ('object' === typeof $data_styling)
						sub_cols[ sub_col ].styling = $data_styling;
				}
			});
			option_data = {
				row_order: subRowOrder,
				gutter: $subRow.data('gutter'),
				column_alignment: $subRow.data('column-alignment'),
				cols: sub_cols
			};
			// get sub-row styling
			if ($subRow.find('.subrow-data-styling').length > 0) {
				var $data_styling = $.parseJSON( $subRow.find('.subrow-data-styling').attr('data-styling') );
				if ('object' === typeof $data_styling)
					option_data.styling = $data_styling;
			}
			return option_data;
		},
		builderPlupload: function (action_text) {
			var class_new = action_text == 'new_elemn' ? '.plupload-clone' : '',
					$builderPlupoadUpload = $(".themify-builder-plupload-upload-uic" + class_new);

			if ($builderPlupoadUpload.length > 0) {
				var pconfig = false;
				$builderPlupoadUpload.each(function () {
					var $this = $(this);
					var id1 = $this.attr("id");
					var imgId = id1.replace("themify-builder-plupload-upload-ui", "");

					pconfig = JSON.parse(JSON.stringify(themify_builder_plupload_init));
					pconfig["browse_button"] = imgId + pconfig["browse_button"];
					pconfig["container"] = imgId + pconfig["container"];
					pconfig["drop_element"] = imgId + pconfig["drop_element"];
					pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
					pconfig["multipart_params"]["imgid"] = imgId;
					//pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
					pconfig["multipart_params"]["_ajax_nonce"] = themifyBuilder.tfb_load_nonce;
					pconfig["multipart_params"]['topost'] = themifyBuilder.post_ID;
					if ($this.data('extensions')) {
						pconfig['filters'][0]['extensions'] = $this.data('extensions');
					}
					var uploader = new plupload.Uploader(pconfig);

					uploader.bind('Init', function (up) {
					});
					uploader.init();

					// a file was added in the queue
					uploader.bind('FilesAdded', function (up, files) {
						up.refresh();
						up.start();
						$('#themify_builder_alert').addClass('busy').show();
					});

					uploader.bind('Error', function (up, error) {
						var $promptError = $('.prompt-box .show-error');
						$('.prompt-box .show-login').hide();
						$promptError.show();

						if ($promptError.length > 0) {
							$promptError.html('<p class="prompt-error">' + error.message + '</p>');
						}
						$(".overlay, .prompt-box").fadeIn(500);
					});

					// a file was uploaded
					uploader.bind('FileUploaded', function (up, file, response) {
						var json = JSON.parse(response['response']), status;

						if ('200' == response['status'] && !json.error) {
							status = 'done';
						} else {
							status = 'error';
						}

						$("#themify_builder_alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function () {
							$(this).removeClass(status);
						});

						if (json.error) {
							alert(json.error);
							return;
						}

						var response_url = json.large_url ? json.large_url : json.url,
							 response_id = json.id,
							thumb_url = json.thumb;
							 
						$this.closest('.themify_builder_input').find('.themify-builder-uploader-input').val(response_url).trigger('change')
								.parent().find('.img-placeholder').empty()
								.html($('<img/>', {src: thumb_url, width: 50, height: 50}))
								.parent().show();
						// Attach image id to the input
						$this.closest('.themify_builder_input').find('.themify-builder-uploader-input-attach-id').val(response_id);
					});

					$this.removeClass('plupload-clone');
				});
			}
		},
		initNewEditor: function (editor_id) {
			if (typeof tinyMCEPreInit.mceInit[editor_id] !== "undefined") {
				return this.initMCEv4(editor_id, tinyMCEPreInit.mceInit[editor_id]);
			}
			var tfb_new_editor_object = this.tfb_hidden_editor_object;

			tfb_new_editor_object['elements'] = editor_id;
			tfb_new_editor_object['selector'] = '#' + editor_id;
			tfb_new_editor_object['wp_autoresize_on'] = false;
			tinyMCEPreInit.mceInit[editor_id] = tfb_new_editor_object;

			// v4 compatibility
			return this.initMCEv4(editor_id, tinyMCEPreInit.mceInit[editor_id]);
		},
		initMCEv4: function (editor_id, $settings) {
			// v4 compatibility
			if (parseInt(tinyMCE.majorVersion) > 3) {
				// Creates a new editor instance
				var ed = new tinyMCE.Editor(editor_id, $settings, tinyMCE.EditorManager);
				ed.render();
				return ed;
			}
		},
		initQuickTags: function (editor_id) {
			// add quicktags
			if (typeof (QTags) == 'function') {
				quicktags({id: editor_id});
				QTags._buttonsInit();
			}
		},
		addNewWPEditor: function () {
			var self = this;

			$('#tfb_module_settings').find('.tfb_lb_wp_editor.clone').each(function (i) {
				var element = $(this),
					element_val = element.val(),
					parent = element.closest('.themify_builder_input');

				$(this).closest('.wp-editor-wrap').remove();

				var oriname = element.attr('name'),
					this_option_id_temp = element.attr('id'),
					this_class = element.attr('class').replace('wp-editor-area', '').replace('clone', '');

				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					dataType: 'html',
					data:
					{
						action: 'tfb_add_wp_editor',
						tfb_load_nonce: themifyBuilder.tfb_load_nonce,
						txt_id: this_option_id_temp,
						txt_class: this_class,
						txt_name: oriname,
						txt_val: element_val
					},
					success: function (data) {
						var $newElems = $(data);

						$newElems.appendTo(parent);
						api.Views.init_control( 'wp_editor', { el: $newElems.find('.tfb_lb_wp_editor') } );
					}
				});

			});
		},
		setColorPicker: function (context) {
			$('.builderColorSelectInput', context).each(function(){
				api.Views.init_control( 'color', { el: $(this) } );
			});
		},
		_getColClass: function (classes) {
			var matches = this.clearClass.split(' '),
				spanClass = null;

			for (var i = 0; i < classes.length; i++) {
				if ($.inArray(classes[i], matches) > -1) {
					spanClass = classes[i].replace('col', '');
				}
			}
			return spanClass;
		},
		saveBuilder: function (loader, callback, saveto) {
			saveto = saveto || 'main';
			var ids = _.map( api.Instances.Builder, function( view, key ){
				var temp_id = view.$el.data('postid') || null;
				var temp_data = view.toJSON() || null;
				return {
					id: temp_id,
					data: temp_data
				};
			});
			window.console && console.log( ids, 'ids');

			return $.ajax({
				type: "POST",
				url: themifyBuilder.ajaxurl,
				data:
				{
					action: 'tfb_save_data',
					tfb_load_nonce: themifyBuilder.tfb_load_nonce,
					ids: JSON.stringify(ids),
					tfb_saveto: saveto
				},
				cache: false,
				beforeSend: function (xhr) {
					if (loader) {
						ThemifyBuilderCommon.showLoader('show');
					}
				},
				success: function (data) {
					if (loader) {
						ThemifyBuilderCommon.showLoader('hide');
					}

					// load callback
					if ($.isFunction(callback)) {
						callback.call(this, data);
					}
					api.editing = false;
					$('body').trigger('themify_builder_save_data');

				},
				error: function() {
					if (loader) {
						ThemifyBuilderCommon.showLoader('error');
					}
				}
			});
		},
		loadContentJs: function(el) {
			ThemifyBuilderModuleJs.loadOnAjax(el); // load module js ajax
			// hook
			$('body').trigger('builder_load_on_ajax');
		},
		setupTooltips: function() {
			var setupBottomTooltips = function() {
				$('body').on('mouseover', '[rel^="themify-tooltip-"]', function(e) {
					// append custom tooltip
					var $title = $(this).data('title') ? $(this).data('title') : $(this).prop('title');
					$(this).append('<span class="themify_tooltip">' + $title + '</span>');
				});

				$('body').on('mouseout', '[rel^="themify-tooltip-"]', function(e) {
					// remove custom tooltip
					$(this).children('.themify_tooltip').remove();
				});
			};

			setupBottomTooltips();
			ThemifyBuilderCommon.setUpTooltip();
		},
		mediaUploader: function() {

			// Uploading files
			var $body = $('body'); // Set this

			// Field Uploader
			$body.on('click', '.themify-builder-media-uploader', function(event) {
				var $el = $(this),
					$builderInput = $el.closest('.themify_builder_input'),
					isRowBgImage = $builderInput.children('#background_image').length == 1,
					isRowBgVideo = $builderInput.children('#background_video').length == 1;

				var file_frame = wp.media.frames.file_frame = wp.media({
					title: $el.data('uploader-title'),
					library: {
						type: $el.data('library-type') ? $el.data('library-type') : 'image'
					},
					button: {
						text: $el.data('uploader-button-text')
					},
					multiple: false // Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on('select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment = file_frame.state().get('selection').first().toJSON();

					// Do something with attachment.id and/or attachment.url here
					$el.closest('.themify_builder_input').find('.themify-builder-uploader-input').val(attachment.url).trigger('change')
						.parent().find('.img-placeholder').empty()
						.html($('<img/>', {
							src: attachment.url,
							width: 50,
							height: 50
						}))
						.parent().show();

					// Attached id to input
					$el.closest('.themify_builder_input').find('.themify-builder-uploader-input-attach-id').val(attachment.id);
				});

				// Hide ATTACHMENT DISPLAY SETTINGS
				if (isRowBgImage || isRowBgVideo) {
					if ($('#hide_attachment_display_settings').length == 0) {
						$('body').append('<style id="hide_attachment_display_settings">.media-modal .attachment-display-settings { display:none }</style>');
					}
	 
					file_frame.on('close', function (selection) {
						$('#hide_attachment_display_settings').remove();
					});
				}

				// Finally, open the modal
				file_frame.open();
				event.preventDefault();
			});

			// delete button
			$body.on('click', '.themify-builder-delete-thumb', function(e) {
				$(this).prev().empty().parent().hide();
				$(this).closest('.themify_builder_input').find('.themify-builder-uploader-input').val('').trigger('change');
				e.preventDefault();
			});

			// Media Buttons
			$body.on('click', '.insert-media', function(e) {
				window.wpActiveEditor = $(this).data('editor');
			});
		},
		openGallery: function() {

			var clone = wp.media.gallery.shortcode,
				$self = this,
				file_frame;

			$('body').on('click', '.tf-gallery-btn', function(event) {
				var shortcode_val = $(this).closest('.themify_builder_input').find('.tf-shortcode-input');

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					frame: 'post',
					state: 'gallery-edit',
					title: wp.media.view.l10n.editGalleryTitle,
					editing: true,
					multiple: true,
					selection: false
				});

				wp.media.gallery.shortcode = function(attachments) {
					var props = attachments.props.toJSON(),
						attrs = _.pick(props, 'orderby', 'order');

					if (attachments.gallery)
						_.extend(attrs, attachments.gallery.toJSON());

					attrs.ids = attachments.pluck('id');

					// Copy the `uploadedTo` post ID.
					if (props.uploadedTo)
						attrs.id = props.uploadedTo;

					// Check if the gallery is randomly ordered.
					if (attrs._orderbyRandom)
						attrs.orderby = 'rand';
					delete attrs._orderbyRandom;

					// If the `ids` attribute is set and `orderby` attribute
					// is the default value, clear it for cleaner output.
					if (attrs.ids && 'post__in' === attrs.orderby)
						delete attrs.orderby;

					// Remove default attributes from the shortcode.
					_.each(wp.media.gallery.defaults, function(value, key) {
						if (value === attrs[key])
							delete attrs[key];
					});

					var shortcode = new wp.shortcode({
						tag: 'gallery',
						attrs: attrs,
						type: 'single'
					});

					shortcode_val.val(shortcode.string()).trigger('change');

					wp.media.gallery.shortcode = clone;
					return shortcode;
				};

				// Hide GALLERY SETTINGS
				if ($('#hide_gallery_settings').length == 0) {
					$('body').append('<style id="hide_gallery_settings">.media-modal .gallery-settings { display:none }</style>');
				}

				file_frame.on('close', function (selection) {
					$('#hide_gallery_settings').remove();
				});

				file_frame.on('update', function(selection) {
					var shortcode = wp.media.gallery.shortcode(selection).string().slice(1, -1);
					shortcode_val.val('[' + shortcode + ']');
					$self.setShortcodePreview(selection.models, shortcode_val);
				});

				if ($.trim(shortcode_val.val()).length > 0) {
					file_frame = wp.media.gallery.edit($.trim(shortcode_val.val()));

					file_frame.on('close', function (selection) {
						$('#hide_gallery_settings').remove();
					});

					file_frame.state('gallery-edit').on('update', function(selection) {
						var shortcode = wp.media.gallery.shortcode(selection).string().slice(1, -1);
						shortcode_val.val('[' + shortcode + ']');
						$self.setShortcodePreview(selection.models, shortcode_val);
					});
				} else {
					file_frame.open();
					$('.media-menu').find('.media-menu-item').last().trigger('click');
				}
				event.preventDefault();
			});

		},
		setShortcodePreview: function($images, $input) {
			var $preview = $input.next('.themify_builder_shortcode_preview'),
				$html = '';
			if ($preview.length === 0) {
				$input.after('<div class="themify_builder_shortcode_preview"></div>');
				$preview = $input.next('.themify_builder_shortcode_preview');
			}
			for (var $i in $images) {
				var attachment = $images[$i].attributes,
					$url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
				$html += '<img src="' + $url + '" width="50" height="50" />';
			}
			$preview.html($html);
		},
		createGradientPicker : function( $input, value ) {
			if(typeof $.fn.ThemifyGradient==='undefined'){
				return;
			}
			var $field = $input.closest( '.themify-gradient-field' ),
				instance = null, // the ThemifyGradient object instance
				isTrigger = false,
				args = {
					onChange : function(stringGradient, cssGradient, asArray){ 
						$input.val( stringGradient );
						$field.find( '.themify-gradient-css' ).val( cssGradient );  
						if(isTrigger && 'visual' === api.mode){
							var $is_cover = $input.prop('name')==='cover_gradient-gradient',
							rgbaString = api.liveStylingInstance.bindBackgroundGradient($input.data('id'),cssGradient,$is_cover);
							if($is_cover && rgbaString){
								api.liveStylingInstance.addOrRemoveComponentOverlay(rgbaString,true);
							}
						}
					}
				};
			if( value ) {
				args.gradient = value;
			}
			else {
				if( $input.attr( 'data-default-gradient' ) ) {
					args.gradient = $input.attr( 'data-default-gradient' );
				} else {
					args.gradient = []; // empty gradient
				}
			}
			$input.prev().ThemifyGradient( args );
			instance = $input.prev().data( 'ThemifyGradient' );
			$field.find( '.themify-clear-gradient' ).on( 'click', function(e){
				e.preventDefault();
				instance.settings.gradient = '0% rgba(255,255,255, 1)|100% rgba(255,255,255,1)';
				instance.update();
				$input.add( $field.find( '.themify-gradient-css' ) ).val( '' ).trigger( 'change' );
				if($input.prop('name')==='cover_gradient-gradient' && 'visual' === api.mode){
					api.liveStylingInstance.addOrRemoveComponentOverlay('');
				}
			} );

			// $( 'body' ).on( 'themify_builder_lightbox_resize', function(){
				// instance.settings.width = $field.width();
				// instance.settings.gradient = $input.val();
				// instance.update();
			// } );
						// angle input
			var $angleInput = $field.find( '.themify-gradient-angle' );
						
			// Linear or Radial select field
			$field.find( '.themify-gradient-type' ).on( 'change', function(){
				instance.setType( $( this ).val() );
				var $angelparent = $angleInput.closest('.gradient-angle-knob'),
					$radial_circle = $field.find('.themify-radial-circle');
				if($( this ).val()==='radial'){
					$angelparent.hide();
					$angelparent.next('span').hide();
					$radial_circle.show();
				}
				else{
					$angelparent.show();
					$angelparent.next('span').show();
					$radial_circle.hide();
				}
			} )
			.trigger( 'change' ); // required: the option's value is set before the event handler is registered, trigger change manually to patch this
						
			$field.find('.themify-radial-circle input').on('change',function(){
				instance.setRadialCircle( $( this ).is(':checked'));
			}).trigger( 'change' );
			
			$angleInput.on( 'change', function(){
				var $val = parseInt($( this ).val());
				if(!$val){
					$val = 0;
				}
				instance.setAngle($val);
			} ).knob({
				change: function(v) {
					instance.setAngle( Math.round( v ) );
				}
			});
			$angleInput.trigger('change'); // required

			// angle input popup style
			$angleInput.removeAttr( 'style' )
				.focus(function(){
					$( this ).parent().find( 'canvas' ).show();
				})
				.parent().addClass( 'gradient-angle-knob' )
				.hover( function(){
					$( this ).addClass( 'gradient-angle-hover-state' );
				}, function(){
					$( this ).removeClass( 'gradient-angle-hover-state' );
				} )
				.find( 'canvas' )
					.insertAfter( $angleInput );
			$( document ).bind( 'click', function() {
				if ( ! $angleInput.parent().is('.gradient-angle-hover-state')) {
					$angleInput.parent().find('canvas').hide('fast');
				}
			});
			//for image_and_gradient
			setTimeout(function(){
				isTrigger = true;
				$field.closest('.tf-image-gradient-field').find('.tf-option-checkbox-js:checked').trigger('change');
			},900);
		},
		checkUnload: function() {
			/* unload event */
			if ($('body').hasClass('themify_builder_active')) {
				window.onbeforeunload = function() {
					return ThemifyBuilderCommon.undoManager.instance.hasUndo() && api.editing ? themifyBuilder.confirm_on_unload : null;
				};
			}
		},

	};

	_.extend( api.Views.BaseElement.prototype, api.Mixins.Common );
	_.extend( api.Views.Builder.prototype, api.Mixins.Builder );

	/**
	 * Form control views.
	 */

	api.Views.ControlRegistry = {
		items: {},
		register: function (id, object) {
			this.items[id] = object;
		},
		lookup: function (id) {
			return this.items[id] || null;
		},
		remove: function (id) {
			delete this.items[id];
		},
		destroy: function() {
			_.each( this.items, function( view, cid ){
				view.remove();
			});
			this.items = {};
			window.console && console.log('destroy controls');
		}
	};
	 
	api.Views.Controls[ 'default' ] = Backbone.View.extend({
		initialize: function() {
			api.Views.ControlRegistry.register( this.$el.attr('id'), this );
		}
	});

	api.Views.Controls.default.extend = function(child) {
		var self = this,
			view = Backbone.View.extend.apply(this, arguments);
		view.prototype.events = _.extend({}, this.prototype.events, child.events);
		view.prototype.initialize = function() {
			if ( _.isFunction(self.prototype.initialize) ) self.prototype.initialize.apply(this, arguments);
			if ( _.isFunction(child.initialize) ) child.initialize.apply(this, arguments);
		}
		return view;
	};

	api.Views.register_control = function( type, args ) {

		if ( 'default' !== type )
			this.Controls[ type ] = this.Controls.default.extend( args );
	};

	api.Views.get_control = function( type ) {

		if ( this.control_exists( type ) )
			return this.Controls[ type ];

		return this.Controls.default;
	};

	api.Views.control_exists = function( type ) {

		return this.Controls.hasOwnProperty( type );
	};

	api.Views.init_control = function( type, args ) {
		args = args || {};
		var exist = this.ControlRegistry.lookup( args.el.attr('id') );
		if ( exist ) {
			exist.setElement( args.el ).render();
			return exist;
		} else {
			var control = api.Views.get_control( type );
			return new control( args );
		}
	};

	api.vent.on('dom:observer:update', function( $container ) {
		window.console && console.log('dom:observer:update');
		$container.find('[data-cid]').each(function(){
			var cid = $(this).data('cid'),
				model = api.Models.Registry.lookup( cid );
			if ( model ) model.trigger('custom:dom:update');
		});
		$container.find('.themify_builder_ui_state_highlight').remove();
	});

	// Register core controls
	api.Views.register_control( 'wp_editor', {
		initialize: function() {
			this.render();
		},
		render: function(){
			var this_option_id = this.$el.attr('id');
			api.Utils.initQuickTags(this_option_id);
			if (typeof tinyMCE !== 'undefined') {
				var ed = api.Utils.initNewEditor(this_option_id);
				//ed.on('keyup', _.debounce( this.previewText, 300 ) );
				//ed.on('change', this.previewText);
			}
			return this;
		},
		previewText: function( event ) {
			window.console && console.log('previewText');
			$('.module-text').html( this.getContent() );
		}
	});

	api.Views.register_control( 'color', {
		is_typing: false,
		initialize: function(){
			this.render();
		},
		render: function(){
			var that = this,
				$minicolors = this.$el.parent().find('.builderColorSelect'),
				// Hidden field used to save the value
				$input = this.$el,
				// Visible field used to show the color only
				$colorDisplay = $minicolors.parent().parent().find('.colordisplay'),
				setColor = '',
				setOpacity = 1.0,
				sep = '_',
				$colorOpacity = $minicolors.parent().parent().find('.color_opacity');

			if ('' != $input.val()) {
				// Get saved value from hidden field
				var colorOpacity = $input.val();
				if (-1 != colorOpacity.indexOf(sep)) {
					// If it's a color + opacity, split and assign the elements
					colorOpacity = colorOpacity.split(sep);
					setColor = colorOpacity[0];
					setOpacity = colorOpacity[1] ? colorOpacity[1] : 1;
				} else {
					// If it's a simple color, assign solid to opacity
					setColor = colorOpacity;
					setOpacity = 1.0;
				}
				// If there was a color set, show in the dummy visible field
				$colorDisplay.val(setColor);
				$colorOpacity.val(setOpacity);
			}

			$minicolors.minicolors({
				opacity: 1,
				textfield: false,
				change: _.debounce(function(hex, opacity) {
					if ('' != hex) {
						if (opacity && '0.99' == opacity) {
							opacity = '1';
						}
						var value = hex.replace('#', '') + sep + opacity;

						var $cssRuleInput = $(this).parent().parent().find('.builderColorSelectInput');
						$cssRuleInput.val(value);

						if ( ! that.is_typing ) {
							$colorDisplay.val(hex.replace('#', ''));
							$colorOpacity.val(opacity);
						}

						// "Apply all" // verify is "apply all" is enabled to propagate the border color
						that.applyAll_verifyBorderColor($cssRuleInput, value, hex.replace('#', ''), hex.replace('#', ''),'change');

						$('body').trigger(
							'themify_builder_color_picker_change', [$cssRuleInput.attr('name'), $minicolors.minicolors('rgbaString')]
						);
					}
				}, 200)
			});
			// After initialization, set initial swatch, either defaults or saved ones
			$minicolors.minicolors('value', setColor);
			$minicolors.minicolors('opacity', setOpacity);

			$colorDisplay.on('blur keyup', function(e) {
				var $input = $(this),
					tempColor = '',
					$minicolors = $input.parent().find('.builderColorSelect'),
					$field = $input.parent().find('.builderColorSelectInput');
				if ('' != $input.val()) {
					tempColor = $input.val();
				}

				if ( 'keyup' === e.type ) {
					that.is_typing = true;
				} else {
					that.is_typing = false;
				}

				$input.val(tempColor.replace('#', ''));
				$field.val($input.val().replace(/[abcdef0123456789]{3,6}/i, tempColor.replace('#', '')));
				if ( 'keyup' === e.type ) {
					$minicolors.minicolors('value', tempColor);
				} else {
					$minicolors.minicolors('value', '').minicolors('value', tempColor); // fix change doesn't trigger
				}

				// "Apply all" // verify is "apply all" is enabled to propagate the border color
				that.applyAll_verifyBorderColor($field, $field.val(), $input.val(), tempColor,e.type);
			});

			$colorOpacity.on('blur keyup', function(e) {
				var $input = $(this),
					tempOpacity = '',
					$minicolors = $input.parent().find('.builderColorSelect'),
					$field = $input.parent().find('.builderColorSelectInput');
				if ('' != $input.val()) {
					tempOpacity = $input.val();
				}
				$input.val(tempOpacity);
				$minicolors.minicolors('opacity', tempOpacity);
				// "Apply all" // verify is "apply all" is enabled to propagate the border color
				//that.applyAll_verifyBorderColor($field, $field.val(), $input.val(), tempColor,e.type);
			});
		},
		// "Apply all" // apply all color change
		applyAll_verifyBorderColor: function(element, hiddenInputValue, colorDisplayInputValue, minicolorsObjValue, type) {
			var $checkbox = false,
				element = $(element);
			if(element.prop('name').indexOf('border_top_color')!==-1){
					var $fields = element.closest('.themify_builder_field').nextAll('.themify_builder_field');
					$fields.each(function(){
						$checkbox = $(this).find('.style_apply_all_border');
						if ( $checkbox.length>0) {
							return false;
						}
					});

				if ( $checkbox  && $checkbox.is(':checked')) {
					var minicolorsObj=null;
					if(type!=='keyup'){
						$('.builderColorSelectInput', $fields).each(function() {
							var $parent = $(this).closest('.themify_builder_input');
								minicolorsObj = $parent.find('.builderColorSelect');
							$(this).val(hiddenInputValue);
							$parent.children('.colordisplay').val(colorDisplayInputValue);
							minicolorsObj.minicolors('value', minicolorsObjValue);
						});	
					}
					else{
						minicolorsObj = element.closest('.themify_builder_input').find('.builderColorSelect');
					}

					if ( 'visual' === api.mode ) {
						api.liveStylingInstance.setApplyBorder(element.prop('name'),minicolorsObj.minicolors('rgbaString'),'color');
					}
				}
			}
		},
	});

})(jQuery);
