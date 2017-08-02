jQuery(function($){
	
	var required_fields = $('.required-meta-field');
	
	var hndle_background = required_fields.parents('.postbox').find('h3.hndle').css('background');
	var postbox_border_color = required_fields.parents('.postbox').css('border-color');
	
	$('#post').submit(function(){
		
	 	// halt wp save styles
		$('#ajax-loading').hide();		
		$('#save-post').removeClass('button-disabled');
		$('#publish').removeClass('button-primary-disabled');
		
		// reset postbox style
		required_fields.parents('.postbox').find('h3.hndle').css('background',hndle_background);
		required_fields.parents('.postbox').css('border-color',postbox_border_color);
		
		if(required_fields.size() == 0) return true;
		var errors = false, error_count = 0, title = '', name = '', checked_names = [];
		required_fields.each(function(){
			name = $(this).attr('name');
			if($.inArray(name, checked_names) > -1) return true;
			checked_names[checked_names.length] = name;		
			
			if(name.substring(name.length-2, name.length) == '[]'){
				if($(this).attr('type') == 'checkbox' && $('input[name="'+name+'"]:checked').size() == 0)
					errors = true;
			} else
			if($(this).val().length == 0)
				errors = true;
				
			if(errors){
				error_count++;
				$(this).parents('.postbox').css('border-color','#d99');
				$(this).parents('.postbox').find('h3.hndle').css('background','#FFEBE8');
			}
			
			errors = false;
		});	
		if(error_count > 0) {
			$('#required-errors').remove();	
			$('#post').before(
				'<div id="required-errors" class="error below-h2" style="display: none;">'+
					'<p>Please fill in the required field'+(error_count > 1 ? 's' : '')+' below.</p>'
				+'</div>'
			);
			$('#required-errors').fadeIn();	
			return false; 
		}
		
		// UN halt wp save styles
		$('#ajax-loading').show();		
		$('#save-post').addClass('button-disabled');
		$('#publish').addClass('button-primary-disabled');
		
		return true;
	});

});