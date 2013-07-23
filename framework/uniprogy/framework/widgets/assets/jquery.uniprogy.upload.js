(function($) {
$.fn.uUploadField = function() {
	var field = this;
	
	var plugin = {
		pushContent: function(data)
		{
			plugin.contentHandler().html(data).find('.controls a').click(function(e){			
				e.preventDefault();
				plugin.contentHandler().remove();
				$.ajax({
					url: $(this).attr('href'),
					type: 'POST',
					data: {
						'bin': field.val()
					},
					success: function()
					{
						field.val(0);
					}
				});
			});
			return field;
		},
		
		contentHandler: function()
		{
			var divId = field.attr('id') + '-content';
			var div = $('#'+divId);
			if(div.length == 0)
				div = $('<div />').attr({'id':divId}).insertBefore(field);
			return div;
		}
	};
	return plugin;
}
})(jQuery);