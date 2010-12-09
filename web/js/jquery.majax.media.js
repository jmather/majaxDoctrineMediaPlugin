(function($) {
	$.widget('ui.majaxmediaselector', {
		version : '1.0.0',
		eventPrefix : 'majaxmediaselector',
		options : {
			defaultTab: 'all',
			timeout: null,
			fetch_url: false,
			lookup_url: false,
			dialog_options : {
				autoOpen: false,
				buttons: {
					"Close": function() {
						$(this).dialog('close');
					}
				},
				closeOnEscape: true,
				modal: true,
				width: 700
			},
			grid_options: {
				"datatype":"xml",
				"colModel": [
					{ "name":"ID", "index":"id", "editable":false, "sorttype":"number", "key":true, width: 10 },
					{ "name":"Name", "index":"name", "editable":false, sortable: false },
					{ "name":"Type", "index":"Type", "editable":false, sortable: false, width: 50 },
					{ 'name': 'CreatedOn', 'index': 'created_at' },
					{ 'name': 'UpdatedOn', 'index': 'updated_at' }
				],
				"rowNum":10,
				"autowidth":true,
				"rowList":[10,20,30],
				"sortname": "ID",
				viewrecords: true,
				sortorder: "desc"
			},
			pager_options : {
				add: false,
				edit: false,
				del: false,
				search: false
			},
			open_dialog : function(id) {
				$('#'+id+'_media_dialog').dialog('open');
			},
			close_dialog : function(id) {
				$('#'+id+'_media_dialog').dialog('close');
			},
			clear_value : function(id) {
				$('#'+id).val('');
				$('#'+id+'_display').val('');
			},
			update_grid_data : function(id, data) {
				$('#'+id).majaxmediaselector('update_grid_data', data);
			},
			select_value : function(id, value, name, type) {
				$('#'+id).val(value);
				$('#'+id+'_display').val(type+': '+name);
				$('#'+id).majaxmediaselector('close_dialog');
			},
			fetch_value : function(id, url, val) {
				var tfCallback = function(id) {
					return function(resp) {
						if (resp['status'] == 'valid')
						{
							$('#'+id).majaxmediaselector('select_value', resp['id'], resp['name'], resp['type']);
						} else {
							$('#'+id).majaxmediaselector('clear_value');
						}
					}
				}
				$.post(url, { value: val }, tfCallback(id), 'json');
			}
		},
		_create: function() {
			var id = $(this.element).attr('id');
			this.options['id'] = id;
			var mor = '<input type="text" id="'+id+'_display" size="50" />';
			mor += '<button aria-disabled="false" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="'+id+'_select_button"><span class="ui-button-text">Select</span></button>';
			mor += '<button aria-disabled="false" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="'+id+'_clear_button"><span class="ui-button-text">Clear</span></button>';

			$(this.element).after(mor);
			$(this.element).css('display', 'none');
			$('body').prepend('<div style="display: none" id="'+id+'_media_dialog" title="Media Selector"><div id="'+id+'_media_dialog_content"></div></div>');
			$('#'+id+'_media_dialog').dialog(this.options['dialog_options']);
			var tfOpenDialog = function(id) {
				return function() {
					$('#'+id).majaxmediaselector('open_dialog');
					this.blur();
				}
			}
			$('#'+id+'_display').focus(tfOpenDialog(id));
			var tfOpenDialog = function(id) {
				return function() {
					$('#'+id).majaxmediaselector('open_dialog');
					return false;
				}
			}
			$('#'+id+'_select_button').click(tfOpenDialog(id));
			var tfClearValue = function(id) {
				return function() {
					$('#'+id).majaxmediaselector('clear_value');
					return false;
				}
			}
			$('#'+id+'_clear_button').click(tfClearValue(id));

			if (parseInt($(this.element).val(), 10) > 0)
			{
				this.options['fetch_value'](id, this.options.lookup_url, parseInt($(this.element).val(), 10));
			}

			return this;
		},
		open_dialog : function()
		{
			this.options['open_dialog'](this.options['id']);
			var id = this.options['id'];

			var tfDblClick = function(widget_id) {
				return function(id) {
					var name = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Name');
					var type = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Type');
					$('#'+widget_id).majaxmediaselector('select_value', id, name, type);
				}
			}

			var cont = $('#'+id+'_media_dialog_content');
			cont.html('');
                        //cont.append('<p style="font-size: .8em; text-align: center;">Double-click to select media file.</p>');

			var table = '<table width="100%"><thead><th style="text-align: left;">Filter: <input type="text" id="'+id+'_media_filter" /></th>';
			table += '<th style="text-align: right;">Type: <select id="'+id+'_media_type">';
			table += '<option value="all">All</option>';
			table += '<option value="Video">Video</option>';
			table += '<option value="Photo">Photo</option>';
			table += '<option value="Audio">Audio</option>';
			table += '<option value="Gallery">Gallery</option>';
			table += '</select></th></tr></thead></table>';
			//table += '<table width="100%"><thead><tr><th>Name</th><th>Type</th><th>Updated At</th><th>Select</th></tr></thead>';
			//table += '<tbody id="'+id+'_results"><td colspan="4">Loading...</td></tbody></table>';
			table += '<table id="'+id+'_grid" style="width: 100%;"></div>';
			table += '<div id="'+id+'_grid_pager"></div>';
			cont.append(table);


			var tfDblClick = function(widget_id) {
				return function(id) {
					var name = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Name');
					var type = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Type');
					$('#'+widget_id).majaxmediaselector('select_value', id, name, type);
				}
			}


			var grid_opts = this.options.grid_options;
			grid_opts['url'] = this.options.fetch_url;
			grid_opts['pager'] = id+'_grid_pager';
			grid_opts['ondblClickRow'] = tfDblClick(id);

			var pager_opts = this.options.pager_options;

			$('#'+id+'_grid').jqGrid(grid_opts).navGrid('#'+id+'_grid_pager', pager_opts);

			var tFunc = function(obj) {
				return function() {
					obj.update_grid_filters();
				}
			}

			$('#'+id+'_media_type').change(tFunc(this));
			$('#'+id+'_media_filter').keydown(tFunc(this));

			return this;
		},
		update_grid_filters: function()
		{
			var tFunc = function(obj) {
				return function() {
					obj.update_grid();
				}
			}

			if (this.options.timeout)
				clearTimeout(this.options.timeout);
			this.options.timeout = setTimeout(tFunc(this), 500);
		},
		update_grid: function()
		{
			var type = $('#'+this.options.id+'_media_type').val();
			var filter = $('#'+this.options.id+'_media_filter').val();

			var url = this.options['fetch_url']+'?type='+type+'&filter='+filter;
			$('#'+this.options.id+'_grid').jqGrid('setGridParam', { url: url, page: 1}).trigger('reloadGrid');
		},
		close_dialog : function()
		{
			this.options['close_dialog'](this.options['id']);
		},
		clear_value : function()
		{
			this.options['clear_value'](this.options['id']);
		},
		select_value : function(id, name, type)
		{
			this.options['select_value'](this.options['id'], id, name, type);
		},
		destroy: function() {
			var id = this.options['id'];
			$('#'+id+'_media_dialog').remove();
			$('#'+id+'_display').remove();
			$('#'+id+'_select_button').remove();
			$('#'+id+'_clear_button').remove();
			$(this.element).css('display', null);
			$.Widget.prototype.destroy.call( this );
			return this;
		}
	});

})(jQuery);
