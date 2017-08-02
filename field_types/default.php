<?php
/**
 * The default field file.
 *
 * This is the simplest field type. It is a single line of text.
 * This field will be loaded if no other field can be found.
 *
 * @title Single line of text
 * @slug default
 * @options false
 */

	$field_type_edit[] = array('default', 'single line of text');
	function field_type_edit_default($field){
	
		echo '<p><label class="screen-reader-text" for="'.$field['slug'].'">'.$field['name'].'</label><input type="text" name="'.$field['slug'].'" value="'.$field['value'].'" id="input-'.$field['slug'].'" style="width: 100%;" class="'.($field['required'] ? 'required-meta-field' : '').'" /></p>';
	
	}

?>