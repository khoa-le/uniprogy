(function($) {

$.fn.uLoc = function(data,selects) {
	var form = $(this);
	var data = data;
	
	form.find(':input[name$="[country]"]').change(function(){
		updateStates($(this).val(),1);
		return true;
	});
	
	form.find('select[name$="[state]"]').change(function(){
		updateCities(countryField().val(),$(this).val());
		return true;
	});
	
	var countryField = function()
	{
		return form.find(':input[name$="[country]"]');
	}
	
	var stateField = function()
	{
		return form.find('select[name$="[state]"]');
	}
	
	var cityField = function(type)
	{
		var cF = form.find(':input[name$="[city]"]');
			
		if(!cF.length)
			return false;
			
		if(type && (type.toUpperCase() != cF.get(0).tagName.toUpperCase()))
		{
			var id = cF.attr('id');
			var name = cF.attr('name');
			if(type == 'input')
				type+= ' type="text"';
			var newField = $('<'+type+' />').attr({'id': id, 'name': name});
			cF.after(newField).remove();
			return newField;
		}
		return cF;
	};
	
	var updateStates = function(country,uC)
	{
		var state = 0;
		if(data.states[country])
		{
			stateField().html('');
			var oneState = true;
			for(var i in data.states[country])
			{
				if(state===0)
					state = i;
				else
					oneState = false;
				$('<option />').attr('value',i).html(data.states[country][i]).appendTo(stateField());
			}
			if(oneState)
				stateField().parent().hide();
			else
				stateField().parent().show();
		}
		else
		{
			stateField().parent().hide();
		}
		if(uC)
			updateCities(country,state);
	}
	
	var updateCities = function(country,state)
	{
		var countryState = country+'_'+state;
		if(data.cities[countryState]===true || data.cities[country+'_*']===true)
		{
			var cF = cityField('input');
			if(cF)
				cF.parent().show();
		}
		else if(data.cities[countryState])
		{					
			var cF = cityField('select').html('');
			if(!cF)
				return;
			cF.parent().show().end();
			for(var i in data.cities[countryState])
			{
				$('<option />').attr('value',data.cities[countryState][i]).html(data.cities[countryState][i]).appendTo(cF);
			}
		}
		else
		{
			return cityField('input').parent().hide();
		}
	};
	
	if(!selects.country)
		updateStates(countryField().val(),1);
	else
	{
		countryField().val(selects.country);
		updateStates(selects.country,0);	
		stateField().val(selects.state);
		if(selects.state)
		{
			updateCities(selects.country,selects.state);
			cityField().val(selects.city);
		}
	}
	return this;
}

})(jQuery);