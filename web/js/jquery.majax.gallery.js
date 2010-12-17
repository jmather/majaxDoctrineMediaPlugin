(function($) {
	$.widget('ui.majaxgalleryselector', {
		version : '1.0.0',
		eventPrefix : 'majaxgalleryselector',
		options : {
			name: null,
			defaultTab: 'all',
			timeout: null,
			fetch_url: false,
			lookup_url: false,
			exclude: false,
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
				"colModel":
				[
					{ "name":"ID", "index":"id", "key":true, "hidden": true},
					{ "name":"Name", "index":"name" },
					{ 'name': 'CreatedOn', 'index': 'created_at', hidden: true },
					{ 'name': 'UpdatedOn', 'index': 'updated_at', width: 80}
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
				$('#'+id+'_gallery_dialog').dialog('open');
			},
			close_dialog : function(id) {
				$('#'+id+'_gallery_dialog').dialog('close');
			},
			clear_value : function(id) {
				$('#'+id).val('');
				$('#'+id+'_display').val('');
			},
			select_value : function(id, value, name, type) {
				$('#'+id).val(value);
				$('#'+id+'_display').val(type+': '+name);
				$('#'+id).majaxgalleryselector('close_dialog');
			}
		},
		_create: function() {
			if (this.options['name'] == null)
			{
				alert('No name was passed to GallerySelector. Aborting.');
				this.destroy();
				return;
			}
			var id = $(this.element).attr('id');
			this.options['id'] = id;
			var display = '<span id="'+id+'_galleries"></span><span id="'+id+'_controls">';
			display += '<button aria-disabled="false" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="'+id+'_select_button"><span class="ui-button-text">Add Gallery</span></button>';
			$(this.element).html(display);
			$('body').prepend('<div style="display: none" id="'+id+'_gallery_dialog" title="Gallery Selector"><div id="'+id+'_gallery_dialog_content"></div></div>');

			$('#'+id+'_gallery_dialog').dialog(this.options['dialog_options']);
			var tfOpenDialog = function(id) {
				return function() {
					$('#'+id).majaxgalleryselector('open_dialog');
					return false;
				}
			}
			$('#'+id+'_select_button').click(tfOpenDialog(id));
			this.options['grid_initialized'] = false;
			this._rebuild_values();

			var tfInitTags = function(id) {
				return function(data) {
					$('#'+id).majaxgalleryselector('init_galleries', data);
				}
			}
			var d = {};
			for(var i = 0; i < this.values.length; i++)
				d['values['+i+']'] = this.values[i];
			$.post(this.options.lookup_url, d, tfInitTags(id), 'json');

			return this;
		},
		init_galleries : function(data)
		{
			if (data['status'] == 'invalid')
				return;
			for(var i in data['results'])
			{
				var res = data['results'][i];
				this._add_button(res['id'], res['name']);
			}
			this._rebuild_values();
		},
		_add_button : function(gallery_id, gallery_name)
		{
			var id = $(this.element).attr('id');
			var name = $(this.element).attr('name');
			if ($('#'+id+'_'+gallery_id, '#'+id+'_values').length == 0)
			{
				var inp = '<input type="hidden" name="'+this.options['name']+'['+gallery_id+']" ';
				inp += 'id="'+id+'_'+gallery_id+'" value="'+gallery_id+'" />';
				$('#'+id+'_values').append(inp);
			}
			var button = '<div id="'+id+'_display_'+gallery_id+'">'+gallery_name+'</div>';
			$('#'+id+'_galleries').append(button);
			$('#'+id+'_display_'+gallery_id).button();
			var tfMouseOver = function(id, gallery_id, obj) {
				return function() {
					var a = $('<a title="Remove Gallery" href="#">X</a>');
					tfRemove = function(id, gallery_id, obj) {
						return function() {
							$('#'+id+'_display_'+gallery_id).remove();
							$('#'+id+'_'+gallery_id).remove();
							obj._rebuild_values();
							return false;
						}
					}
					a.click(tfRemove(id, gallery_id, obj));
					var b = ' | ';
					$('.ui-button-text', this).append(b);
					$('.ui-button-text', this).append(a);
				}
			}
			var tfMouseOut = function() {
				var tc = $('.ui-button-text', this).html().split(" |", 2);
				$('.ui-button-text', this).html(tc[0]);
			}
			$('#'+id+'_display_'+gallery_id).mouseenter(tfMouseOver(id, gallery_id, this));
			$('#'+id+'_display_'+gallery_id).mouseleave(tfMouseOut);
		},
		open_dialog : function()
		{
			this.options['open_dialog'](this.options['id']);
			var id = this.options['id'];

			var postdata = { 'exclude': this.values, 'also_exclude': this.options['exclude'] };

			if (this.options['grid_initialized'])
			{
				$('#'+id+'_grid').jqGrid('setGridParam', { postData: postdata, url: url_base, page: 1 }).trigger('reloadGrid');
				return this;
			}

			var grid_opts = this.options.grid_options;
			grid_opts['url'] = this.options.fetch_url;
			grid_opts['postData'] = postdata;
			grid_opts['pager'] = '#'+id+'_pager';

			var tfDblClick = function(widget_id) {
				return function(id) {
					var name = $('#'+widget_id+'_grid').jqGrid().getCell(id, 'Name');
					$('#'+widget_id).majaxgalleryselector('select_value', id, name);
				}
			}

			var tfSearchClick = function(widget_id) {
				return function() {
					$('#'+widget_id).majaxgalleryselector('open_search');
				}
			}

			grid_opts['ondblClickRow'] = tfDblClick(id);

			var pager_opts = this.options.pager_options;


			$('#'+id+'_gallery_dialog_content').append('<p style="font-size: .8em; text-align: center;">Double-click to select a gallery.</p>');

			var table = '<table width="100%"><thead><tr>';
			table += '<th style="text-align: left;">Filter: <input type="text" id="'+id+'_media_filter" /></th>';
			table += '</tr></thead></table>';
			table += '<table id="'+id+'_grid"></table><div id="'+id+'_pager"></div>';

			$('#'+id+'_gallery_dialog_content').append(table);

			$('#'+id+'_grid').jqGrid(grid_opts).navGrid('#'+id+'_pager', pager_opts);

			var tFunc = function(obj) {
				return function() {
					obj.update_grid_filters();
				}
			}

			$('#'+id+'_media_filter').keydown(tFunc(this));

			this.options['grid_initialized'] = true;

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
			var filter = $('#'+this.options.id+'_media_filter').val();

			var url = this.options['fetch_url']+'?filter='+filter;
			$('#'+this.options.id+'_grid').jqGrid('setGridParam', { url: url, page: 1}).trigger('reloadGrid');
		},
		close_dialog : function()
		{
			this.options['close_dialog'](this.options['id']);
		},
		select_value : function(gallery_id, name)
		{
			if ($('#'+this.options['id']+'_'+gallery_id, '#'+this.options['id']+'_values').length == 1)
				return;
			this._add_button(gallery_id, name);
			this._rebuild_values();
			this.close_dialog();
		},
		destroy: function() {
			var id = this.options['id'];
			$('#'+id+'_gallery_dialog').remove();
			$('#'+id+'_display').remove();
			$('#'+id+'_select_button').remove();
			$('#'+id+'_galleries').remove();
			$('#'+id+'_controls').remove();
			$.Widget.prototype.destroy.call( this );
			return this;
		},
		_rebuild_values : function()
		{
			this.values = new Array();
			var vals = $('#'+this.options['id']+'_values input');
			for(var i = 0; i < vals.length; i++)
				this.values[this.values.length] = $(vals[i]).val();
		}
	});

})(jQuery);
