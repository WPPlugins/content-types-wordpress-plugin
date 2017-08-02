<?php
/**
 * @title Checkbox(es)
 * @slug checkbox
 * @options true
 */
 
	function field_type_edit_checkbox($field){
	
		$field['value_range'] = setup_value_range_array($field);
		
		// make value and array
		$field['value'] = maybe_unserialize($field['value']);
		$field['value'] = (!is_array($field['value'])) ? array() : $field['value'];
		
		// build checkbox(es)
		$checkboxes	= ""; $checkbox_count = 0;
		foreach($field['value_range'] as $checkbox_value=>$checkbox_label){
			$checkbox_count++;
			$checkboxes	.= '<label for="'.$field['slug'].'-'.$checkbox_count.'">'.
			'<input name="'.$field['slug'].'[]" type="checkbox" id="checkbox-'.$field['slug'].'-'.$checkbox_count.'" value="'.$checkbox_value.'"'.((in_array($checkbox_value, $field['value'])) ? ' checked="checked"' : "").' class="'.($field['required'] ? 'required-meta-field' : '').'"> '.$checkbox_label.'</label><br />';
		}			
		
		echo '<p class="meta-options">'.$checkboxes.'</p>';
	}	

?>