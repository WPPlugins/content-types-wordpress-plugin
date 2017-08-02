<?php
/**
 * @title Select an image
 * @slug select_image
 * @options false
 */
 
wp_enqueue_style('thickbox');
wp_enqueue_script('jquery');
wp_enqueue_script('thickbox');

$field_type_edit_custom[] = array('select_image', 'select an image');
function field_type_edit_select_image($field){

	if($field['type']=='select_image')
		$field['value_range'] = 'post_type=attachment&post_mime_type=image/';

	$field['value_range'] = setup_value_range_array($field);

	$selected_images = "";
	
	$selected_image_layout = '<div style="height: 75px; width: 75px; padding: 3px; border: #ccc solid 1px; margin: 5px 5px 10px 5px; position: relative; clear: none; float: left;" id="image-#slug#-#id#"><table cellpadding="0" cellspacing="0" style="width: 75px; height: 75px; vertical-align: middle; text-align: center;"><tr><td><img src="#src#" alt="" style="max-height: 75px; max-width: 75px;" /></td></tr></table><a href="#" onclick="select_image_remove(\'#id#\',\'#slug#\');return false;" style="position: absolute; top: 0; right: 0; display: block; text-decoration: none; text-align: right; color: #000; font-size: 13px; line-height: 7px; width: 10px; height: 10px; background: #ccc; padding: 2px; border-left: #fff solid 1px; border-bottom: #fff solid 1px;" title="Remove this Image">&times;</a></div>';
	#slug#,#id#,#src#
	
	if($field['value'])
	foreach(explode('|', $field['value']) as $id){
		$src = current(wp_get_attachment_image_src($id, array(150,150), false));
		$selected_images .= 
		str_replace(array('#slug#','#id#','#src#'), array($field['slug'],$id,$src), $selected_image_layout);
	}
	echo '<p><label class="screen-reader-text" for="'.$field['slug'].'">'.$field['name'].'</label>
	<input class="select_image_value'.($field['required'] ? ' required-meta-field' : '').'" type="hidden" id="'.$field['slug'].'" name="'.$field['slug'].'" value="'.$field['value'].'" /></p>'.
'<div id="selected_images_'.$field['slug'].'" style="float: left; clear: both; width: 100%; margin: 0; padding: 0 0 10px 0;">'.$selected_images.'</div>'.

'<p style="clear: both;">
<a class="thickbox button" href="../wp-content/plugins/content-types-wordpress-plugin/field_types/select_image/popup.php?slug='.$field['slug'].'&TB_iframe=true" title="Add an Image">Add an Image</a></p>';

	if(!$_GET['select_image_shown'])
		echo "
		<script type=\"text/javascript\">
		function select_image_add(src,id,slug){
			var val = jQuery('#'+slug).val();
			val += (val.length > 0) ? '|'+id : id;
			jQuery('#'+slug).val(val);	
			jQuery('#selected_images_'+slug).append(('".str_replace("'","\'", $selected_image_layout)."').replace(/#slug#/g,slug).replace(/#id#/g,id).replace(/#src#/g,src));
		
		}
		function select_image_remove(id,slug){
		
			var val = jQuery('#'+slug).val();
			val = val.replace('|'+id, '').replace(id+'|','').replace(id,'');
			jQuery('#'+slug).val(val);
			jQuery('#image-'+slug+'-'+id).remove();
		
		}
		</script>";
	$_GET['select_image_shown'] = true;	
}
?>