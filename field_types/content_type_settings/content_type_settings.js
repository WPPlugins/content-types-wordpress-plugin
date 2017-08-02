jQuery(function($){
	if($('#settings-advanced-show').val() == 'no'){
		$('#advanced-settings').hide();
		$('#advanced-setting-option a').text('Show Advanced Options');
	}
	$('#advanced-setting-option a').click(function(){
		if($(this).text() == 'Show Advanced Options'){
			$('#advanced-settings').show();
			$(this).text('Hide Advanced Options');
			$('#settings-advanced-show').val('yes');
		}else if($(this).text() == 'Hide Advanced Options'){
			$('#advanced-settings').hide();
			$(this).text('Show Advanced Options');
			$('#settings-advanced-show').val('no');
		}
		return false;
	});
	$('#name').keyup(function(){
		$('#singular-name').val(make_singular($('#name').val()));
	});
	$('#use-category').click(function(){
		$("#use-category1").toggle(this.checked);
		$("#use-category2").toggle(this.checked);
	});
	$("#use-category1 input").keyup(function(){
		$('#use-category2 input').val(make_singular($(this).val()));
	});
	$('#use-tags').click(function(){
		$("#use-tags1").toggle(this.checked);
		$("#use-tags2").toggle(this.checked);
	});
	$("#use-tags1 input").keyup(function(){
		$('#use-tags2 input').val(make_singular($(this).val()));
	});
});
function make_singular(word){
	var ends = 'os=o&ies=y&xes=x&oes=o&ies=y&ves=f&s= '.split('&');		
	for(i in ends){
		p_end = ends[i].split('=')[0];
		s_end = ends[i].split('=')[1];
		s_end = (s_end == ' ') ? '' : s_end;
		if(p_end != word.substring(word.length-p_end.length)) continue;
		return word.substring(0, word.length-p_end.length) + s_end; break;
	}
	return word;
}