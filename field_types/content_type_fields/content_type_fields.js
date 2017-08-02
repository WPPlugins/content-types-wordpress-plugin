jQuery(function($){
	
	$('#field-builder tbody.field').each(function(i,e){		
		if($(e).find('tr.type-options td').size() == 1)
			$(e).find('tr.type-options').hide();
			
		if($(e).find('input.show-advanced').val() == '1'){
			$(e).find('a.advanced').attr('title','Hide advanced options.');
			$(e).find('a.advanced').html('&ndash;');
			$(e).find('tr.advanced').show();
			return true;
		}
		$(e).find('tr.advanced').hide();
	});
	$('#field-builder a').live('click', function(){
		var e = $(this);
		if(e.hasClass('dead')) return false;
		var p = e.parents('tbody');
		var m = e;
		if(e.hasClass('add-field'))
			p.before(field_row);
		else if(e.hasClass('delete') && confirm('Are you sure you want to delete this?'))
			p.remove();
		else if(e.hasClass('up')){
			m = p.prev();
			p.detach();
			m.before(p);
		}
		else if(e.hasClass('down')){
			m = p.next();
			p.detach();
			m.after(p);
		}
		else if(e.hasClass('advanced')){
			if($(e).text() == '+'){
				$(e).attr('title','Hide advanced options.');
				$(e).html('&ndash;');
				$(p).find('input.show-advanced').val('1');
				$(p).find('tr.advanced').show();
			}else{
				$(e).attr('title','Show advanced options.');
				$(e).text('+');
				$(p).find('input.show-advanced').val('');
				$(p).find('tr.advanced').hide();
			}
		}
		order_fields(true);
		return false;
	});
	$('#field-builder select.type').live('change', function(){
		var field = $(this).parents('tbody');
		field.find('tr.type-options').hide();
		for(x in field_type_edit)
			if(field_type_edit[x][0] == $(this).val())
				if(field_type_edit[x][2]){
					field.find('tr.type-options').show();
					if(field.find('tr.type-options td').size() == 1)
						field.find('tr.type-options').html(field_type_options.replace(/(fields\[)([0-9]*)/g, 'fields['+field.data('field-i')));
				}
	});
	function order_fields(reorder){
		$('#field-builder a').removeClass('dead');
		$('#field-builder tbody.field').each(function(i, e){
			$(e).data('field-i', i);
			if(i == 0) $(e).find('a.up').addClass('dead');
			if(i == $('#field-builder tbody.field').size()-1) $(e).find('a.down').addClass('dead');
			if(reorder)
				$(e).find('input, select, textarea').each(function(ii, e){
					i = $(e).parents('tbody').data('field-i');
					$(e).attr('name', $(e).attr('name').replace(/(fields\[)([0-9]*)/g, 'fields['+i));
				});						
		});
	}
	order_fields(false);
});