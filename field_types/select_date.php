<?php
/**
 * @title Date
 * @slug select_date
 * @options false
 */

function field_type_edit_select_date($field){

	$dt = array();
	$field['value'] = (!$field['value']) ? time() : $field['value'];
	$month_select = '';
	foreach(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec') as $mon)
		$month_options .= '<option value="'.$mon.'" '.($mon == date('M', $field['value']) ? ' selected="selected"' : '').'>'.$mon.'</option>';
	?>	
	<label class="screen-reader-text" for="<?php echo $field['slug']; ?>"><?php echo $field['name']; ?></label><select name="<?php echo $field['slug']; ?>[m]" tabindex="4"><?php echo $month_options; ?></select>
    <input type="text" name="<?php echo $field['slug']; ?>[d]" value="<?php echo date('d', $field['value']); ?>" size="2" maxlength="2" style="width: 28px;">, <input type="text" name="<?php echo $field['slug']; ?>[y]" value="<?php echo date('Y', $field['value']); ?>" size="4" maxlength="4"  style="width: 45px;">
	<?php
}

function field_type_save_select_date($value){
	
	return strtotime($value['d'].' '.$value['m'].' '.$value['y']);
	
}

?>