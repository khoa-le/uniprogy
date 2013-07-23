/**
 * jQuery UniProgy plugin file.
 *
 */

;(function($) {
	
$.fn.uWorklet = function() {
	var worklet = this;
	
	var plugin = {		
		load: function(options)
		{
			var defaultSettings = {
				url: false,
				position: 'replace',
				success: false,				
				showLoading: true
			}
			var settings = $.extend(defaultSettings,options);
			if(settings.showLoading)
				plugin.loading(true);
			$.ajax({
				url: settings.url,
				success: function(data) {
					var vars = {};
					vars[settings.position] = data;
					plugin.process({content:vars});
					plugin.loading(false);
					if($.isFunction(settings.success))
						settings.success(data);
				}
			});
			return worklet;
		},
		
		process: function(data)
		{
			if(data.redirect) { 
				if(data.redirectDelay)
					setTimeout('window.location = "'+data.redirect+'";', data.redirectDelay);
				else
					window.location = data.redirect;
				delete data.redirect;
			}
			if(data.info) { plugin.pushContent('.worklet-info:first', data.info); delete data.info; }
			if(data.content) { plugin.pushContent('.worklet-content:first', data.content); delete data.content; }
			if(data.load) {
				var target = data.load.target ? $(data.load.target) : worklet.find('.worklet-content:first');
				var url = data.load.url ? data.load.url : data.load;
				target.load(url);
				delete data.load;
			}
			
			for(var item in data) {
				if($.isPlainObject(data[item])) {
					var nWorklet = data[item].worklet ? $(data[item].worklet) : worklet;
					nWorklet.uWorklet().process(data[item]);
				}
			}
			return worklet;
		},
		
		pushContent: function(target, data)
		{
			target = worklet.find(target);
			if(!$.isPlainObject(data))
				target.html(data).show();
			else {			
				if(data.prependReplace) {
					target.find('.worklet-pushed-content.prepended').remove();
					data.prepend = data.prependReplace;
				}
				
				if(data.appendReplace) {
					target.find('.worklet-pushed-content.appended').remove();
					data.append = data.appendReplace;
				}
				
				var div = $('<div />');
				div.addClass('worklet-pushed-content');
				
				if(data.prepend)
					div.addClass('prepended').prependTo(target.show()).html(data.prepend);
				else if(data.append)
					div.addClass('appended').appendTo(target.show()).html(data.append);
				else if(data.replace)
					div.appendTo(target.html('').show()).html(data.replace);
					
				if(data.fade)
				{
					if(data.fade == 'target')
						target.animate({opacity: 1.0}, 3000).fadeOut("normal");
					else if(data.fade == 'content')
						div.animate({opacity: 1.0}, 3000).fadeOut("normal");
					else
						$(data.fade).animate({opacity: 1.0}, 3000).fadeOut("normal");
				}
				
				if(data.focus)
					$.scrollTo(target);
			}
			return worklet;
		},
		
		resetWorklet: function()
		{
			worklet.find('.worklet-info').hide();
			worklet.find('.worklet-content').show();
			plugin.resetContent();
			return worklet;
		},
		
		resetContent: function()
		{
			worklet.find('.worklet-pushed-content').remove();
			return worklet;
		},
		
		loading: function(on)
		{
			worklet.toggleClass('loading',on);
			return worklet;
		}
	};
	
	return plugin;
};

$.fn.uForm = function() {
	var form = $(this);
	var button;
	
	var plugin = {
		attach: function()
		{
			form.submit(function(e){
				e.preventDefault();
				form = $(this);
				plugin.submit();
			});
			form.find('input:submit').click(function(){
				button = $(this);
			});
			return form;
		},
	
		submit: function()
		{
			plugin.resetErrors();
			if(form.attr('enctype') == 'multipart/form-data')
				form.each(function(){
					this.submit();
				});
			else
			{
				if(typeof(CKEDITOR)!='undefined')
					for (instance in CKEDITOR.instances) 
					{
						var id = CKEDITOR.instances[instance].element.getId();
						if($('#'+id).length)
							CKEDITOR.instances[instance].updateElement();
					}
				var data = form.serialize();
				if(button)
					data+= '&'+button.attr('name')+'='+button.val();
				form.closest('.worklet').uWorklet().loading(true);			
				$.uniprogy.loadingButton(form.find('input[name="submit"]'),true);
				form.find(':input').attr('disabled',true);
				$.ajax({
					'type':'POST',
					'url':form.attr('action'),
					'cache':false,
					'data':data,
					'dataType':'json',
					'success': function(data) {
						if(!data.redirect && !data.keepDisabled)
							form.find(':input').removeAttr('disabled');
						form.closest('.worklet').uWorklet().loading(false);
						$.uniprogy.loadingButton(form.find('input[name="submit"]'),false);
						plugin.process(data);
					}
				});
			}
			return form;
		},
		
		process: function(data)
		{
			if(data.hideForm) form.hide();
			if(data.errors)	plugin.errorSummary(data.errors);
			form.closest('.worklet').uWorklet().process(data);
			return form;
		},
		
		errorSummary: function(data)
		{
			summary = form.find('.errorSummary');
			if(!summary)
				return;
	
			var content = '';
			for(var i=0;i<data.length;i++)
				content+= '<li>' + data[i].message + '</li>';
			summary.find('ul').html(content);
			summary.toggle(content!='');
			$.scrollTo(summary);
			return form;
		},
		
		resetErrors: function()
		{
			summary = form.find('.errorSummary');
			if(summary)
			{
				summary.find('ul').html('');
				summary.hide();
			}
			return form;
		},
		
		resetForm: function()
		{
			$.each(form,function(){
				this.reset();
			});
			plugin.resetErrors();
			form.show();
			form.parents('.worklet').uWorklet().resetWorklet();
			return form;
		}
	};
	return plugin;
}

$.uniprogy = {
	version : '1.1',
	dialogHidden: [],
	
	preloadImages: function(imgs)
	{
		$(imgs).each(function(){
			$('<img />')[0].src = this;
		});
	},
	
	loadingButton: function(button,on)
	{
//		if(on) {
//			var l = $('<div />').addClass('loading').css({display:'inline-block',width:'50px',height:'20px','vertical-align':'middle'});
//			$(button).after(l);
//		} else {
//			$(button).next('.loading').remove();
//		}
	},
	
	dialog: function(url)
	{
		$('#wlt-BaseDialog .content').load(url, function() {
			$('#wlt-BaseDialog .content').css({
					'max-height': $('#wlt-BaseDialog').dialog("option","maxHeight"),
					'overflow-y': 'auto'
			});
			var title = $('#wlt-BaseDialog .content .worklet-title');
			title.hide();
			$('#wlt-BaseDialog').dialog('option', 'title', title.html()); 
			$('#wlt-BaseDialog').dialog('open');
			$('#wlt-BaseDialog').bind( "dialogbeforeclose", function(event, ui) {
				for(var i=0;i<$.uniprogy.dialogHidden.length;i++)
					$($.uniprogy.dialogHidden[i]).show();
			});
			
			$('embed, iframe, object').each(function(){
				if($(this).is(':visible'))
				{
					$(this).hide();
					$.uniprogy.dialogHidden.push(this);
				}
			});
		});
	},
	
	dialogClose: function()
	{
		$('#wlt-BaseDialog').dialog('close');
	},
	
	ucfirst: function(str)
	{
		return str.substring(0, 1).toUpperCase() + str.substring(1).toLowerCase();
	},
	
	val: function(field,value)
	{
		if($(field).is(':radio'))
			$(field).val([value]);
		else
			$(field).val(value);
	}
};

})(jQuery);