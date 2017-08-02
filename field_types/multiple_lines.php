<?php
/**
 * @title Multiple lines of text
 * @slug multiple_lines
 * @options false
 */
 
	function field_type_edit_multiple_lines($field){
		
		echo '<p><label class="screen-reader-text" for="'.$field['slug'].'">'.$field['name'].'</label><textarea rows="3" cols="40" name="'.$field['slug'].'" tabindex="6" id="textarea-'.$field['slug'].'" style="width: 100%;" class="'.($field['required'] ? 'required-meta-field' : '').'">'.$field['value'].'</textarea></p>';
	
	}

?>