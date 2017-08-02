<?php
/**
 * @title Select
 * @slug select
 * @options true
 */

	function field_type_edit_select($field){
		
		$field['value_range'] = setup_value_range_array($field);
		
		// build select options
		$select_options	= '<option value="">Select a '.$field['name'].'</option>';
		foreach($field['value_range'] as $option_value=>$option_label)				
			$select_options	 .= '<option value="'.$option_value.'"'.(($field['value']==$option_value) ? ' selected="selected"' : "").'>'.$option_label.'</option>';
	
		echo '<p><label class="screen-reader-text" for="'.$field['slug'].'">'.$field['name'].'</label>'.
		'<select name="'.$field['slug'].'" id="select-'.$field['slug'].'" class="'.($field['required'] ? 'required-meta-field' : '').'">'.$select_options.
		'</select></p>';
				
	}

?>