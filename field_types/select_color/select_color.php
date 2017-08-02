<?php
/**
 * @title Select a color
 * @slug select_color
 * @options false
 */

wp_enqueue_script('content_type_settings', WP_PLUGIN_URL . '/content-types-wordpress-plugin/field_types/select_color/js/colorpicker.js', array('jquery'));
	
function field_type_edit_select_color($field){
		
	echo
	'<a href="#" style="display: block; height: 80px; width: 80px; border: #ccc solid 1px; margin: 15px 0 0 5px; background: #'.$field['value'].';" id="'.$field['slug'].'-select-color"></a><input type="hidden" id="'.$field['slug'].'-color-input" name="'.$field['slug'].'" value="'.$field['value'].'" class="'.($field['required'] ? 'required-meta-field' : '').'" />'.
	'<script type="text/javascript">'.
	"jQuery(function($){
		$('#".$field['slug']."-select-color').ColorPicker({
			//flat: true,
			onShow: function (colpkr) {
				$(colpkr).show();
				return false;
			},
			onSubmit: function(hsb, hex, rgb, el){
				$('#".$field['slug']."-color-input').val(hex);
				$('#".$field['slug']."-select-color').css({'background-color':'#'+hex});
				$('#".$field['slug']."-select-color').ColorPickerHide();
			}
		});/*
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});*/
	});".
	'</script>';
}

wp_register_style('add_colorpicker_style', WP_PLUGIN_URL .	'/content-types-wordpress-plugin/field_types/select_color/css/colorpicker.css');
wp_enqueue_style('add_colorpicker_style');

?>