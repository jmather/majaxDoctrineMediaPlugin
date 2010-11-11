(function($) {
	var url_base = '/admin/majaxMediaAdminModule/list';
	var fetch_url_base = '/admin/majaxMediaAdminModule/lookup';
	$.widget('ui.majaxmediaselector', {
		version : '1.0.0',
		eventPrefix : 'majaxmediaselector',
		options : {
			defaultTab: 'all',
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
			select_value : function(id, value, name, type) {
				$('#'+id).val(value);
				$('#'+id+'_display').val(type+': '+name);
				$('#'+id).majaxmediaselector('close_dialog');
			},
			fetch_value : function(id, val) {
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
				$.post(fetch_url_base, { value: val }, tfCallback(id), 'json');
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
			$('#'+id+'_display').focus(function() { $(this).majaxmediaselector('open_dialog'); this.blur(); });
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

			this.options['grid_initialized'] = false;

			if (parseInt($(this.element).val(), 10) > 0)
			{
				this.options['fetch_value'](id, parseInt($(this.element).val(), 10));
			}

			return this;
		},
		open_dialog : function()
		{
			this.options['open_dialog'](this.options['id']);
			var id = this.options['id'];

			if (this.options['grid_initialized'])
			{
				$('#'+id+'_grid').jqGrid('setGridParam', { url: url_base, page: 1 }).trigger('reloadGrid');
				return this;
			}

			var grid_opts = {
				"url": url_base,
				"datatype":"xml",
				"colModel": [
					{
						"name":"ID",
						"index":"id",
						"editable":false,
						"sorttype":"number",
						"key":true,
						width: 10
					},
					{
						"name":"Name",
						"index":"name",
						"editable":false,
						sortable: false
					},
					{
						"name":"Type",
						"index":"Type",
						"editable":false,
						sortable: false,
						width: 50
					},
					{
						'name': 'CreatedOn',
						'index': 'created_at'
					},
					{
						'name': 'UpdatedOn',
						'index': 'updated_at'
					}
				],
				"rowNum":10,
				"autowidth":true,
				"rowList":[10,20,30],
				"pager":"#"+id+"_pager",
				"sortname": "ID",
				viewrecords: true,
				sortorder: "desc"
			};

			var tfDblClick = function(widget_id) {
				return function(id) {
					var name = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Name');
					var type = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Type');
					$('#'+widget_id).majaxmediaselector('select_value', id, name, type);
				}
			}

			var tfSearchClick = function(widget_id) {
				return function() {
					$('#'+widget_id).majaxmediaselector('open_search');
				}
			}

			grid_opts['ondblClickRow'] = tfDblClick(id);

			var pager_opts = { add: false, edit: false, del: false, search: false };


			$('#'+id+'_media_dialog_content').append('<p style="font-size: .8em; text-align: center;">Double-click to select media file.</p><table id="'+id+'_grid"></table><div id="'+id+'_pager"></div>');
			$('#'+id+'_grid').jqGrid(grid_opts).navGrid('#'+id+'_pager', pager_opts).navButtonAdd('#'+id+'_pager', {
				caption: 'Search',
				buttonicon: 'ui-icon-search',
				onClickButton: tfSearchClick(id),
				position: 'last'
			});
			

			this.options['grid_initialized'] = true;

			return this;
		},
		open_search : function()
		{
			var id = this.options['id'];
			var search_grid_opts = {
				multipleSearch: true
			};

			$('#'+id+'_grid').jqGrid().searchGrid( search_grid_opts );
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
