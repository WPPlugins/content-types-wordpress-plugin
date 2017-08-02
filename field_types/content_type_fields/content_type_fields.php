<?php

	wp_register_style('content_type_fields', plugins_url('/content_type_fields.css', __FILE__));
	wp_enqueue_style('content_type_fields');

    wp_enqueue_script('content_type_fields', plugins_url('/content_type_fields.js', __FILE__),array('jquery'));

	/* Do not alter this file in any way. */
	function field_type_edit_content_type_fields($field){
		global $field_type_edit;
	
		$field_type_edit = array();
		$field_type_dir = "../wp-content/plugins/content-types-wordpress-plugin/field_types";
		$dir = opendir($field_type_dir);	
		while(false != ($file = readdir($dir))){
		
		if($file == "." or $file == ".." or substr_count($file, 'content_type_')) continue;
		if(substr_count($file, '.')) $field_file = file_get_contents("$field_type_dir/$file");
		else $field_file = file_get_contents("$field_type_dir/$file/$file.php");
					
		preg_match("/(@title )(.*?)(\s)(.*?)(@slug )(.*?)(\s)(.*?)(@options )(.*?)(\s)/", substr($field_file,3), $matches);
		$field_type = array();
		foreach($matches as $k=>$match){
			if($last_match == '@slug ')
				$field_type[0] = str_replace(array("\r", "\n"," "), '', $match);
			if($last_match == '@title ')
				$field_type[1] = str_replace(array("\r", "\n"), '', $match);
			if($last_match == '@options ')
				$field_type[2] = ($match == 'true') ? true : false;
			$last_match = $match;
		}
		ksort($field_type);
		if(count($field_type)!=3) continue;
		$field_type_edit[] = $field_type;
	}
		//echo '<pre>'.print_r(json_encode($field_type_edit), 1).'</pre>';
	
		$fields = maybe_unserialize($field['value']);
		$fields = is_array($fields) ? $fields : array(array());
		
		
		function field_row($i, $v) {
			global $field_type_edit;
			?>

<tbody class="field">
	<tr>
		<!-- td class="pad"></td -->
		<td colspan="4"><a href="#" class="add-field button">Add Field</a></td>
		<!-- td class="pad"></td -->
	</tr>
	<tr class="entry-row">
		<!-- td class="pad"></td -->
		<td class="name"><label for="fields-<?php echo $i; ?>-name">Name</label>
			<div class="input">
				<input type="text" name="fields[<?php echo $i; ?>][name]" class="name" id="fields-<?php echo $i; ?>-name" value="<?php echo $v['name']; ?>"/>
			</div></td>
		<td><label for="fields-<?php echo $i; ?>-type">Type</label>
			<div class="input">
				<select class="type" name="fields[<?php echo $i; ?>][type]" id="fields-<?php echo $i; ?>-type">
					<?php foreach($field_type_edit as $slug_title_option) {
									$req = ($v['type'] == $slug_title_option[0]) ? ' selected="selected"' : '';
									if($req and $slug_title_option[2]) { $show_options = true; }
									?>
					<option value="<?php echo $slug_title_option[0]; ?>"<?php echo $req; ?>><?php echo $slug_title_option[1] ?></option>
					<?php } ?>
				</select>
			</div></td>
		<td><label for="fields-<?php echo $i; ?>-required">Required</label>
			<div class="input">
				<input name="fields[<?php echo $i; ?>][required]" id="fields-<?php echo $i; ?>-required" value="1" type="checkbox"<?php echo $v['required'] ? ' checked="checked"' : ''; ?>/>
			</div>
			</div>
		<td class="actions"><label>&nbsp;</label>
			<div class="input"><a href="#" class="delete" title="Delete this field."> &#215;</a><a href="#" class="up" title="Move field up.">&#x25B2;</a><a href="#" class="down" title="Move field down.">&#x25BC;</a><a href="#" class="advanced" title="Show advanced options.">+</a></div></td>
		<!-- td class="pad"></td -->
	</tr>
	<tr class="entry-row type-options"<?php echo (array_key_exists('type_option', $v)) ?  '' : 'style="display: none;"'; ?>>
		<!-- td class="pad"></td -->
		<?php if($show_options) field_type_options($i, $v); else echo '<td colspan="4"></td>'; ?>
		<!-- td class="pad"></td -->
	</tr>
	<tr class="entry-row advanced">
		<!-- td class="pad"></td -->
		<td colspan="2"><input type="hidden" name="fields[<?php echo $i; ?>][show-advanced]" class="show-advanced" value="<?php echo $v['show-advanced'] ? $v['show-advanced'] : 0; ?>" />
			<label for="fields-<?php echo $i; ?>-description">Description</label>
			<div class="input">
				<input type="text" name="fields[<?php echo $i; ?>][description]" id="fields-<?php echo $i; ?>-description" class="description" value="<?php echo $v['description']; ?>"/>
			</div></td>
		<td colspan="2"><?php /*?>					<div class="label">Regex Verify <a href="#" onclick="return false;" title="Enter a regular expression for verifying entry.
	An expression which verifies email is as follows: \b([a-zA-Z0-9._%-]*)(@)([a-zA-Z0-9._%-]*)(\.)([a-zA-Z]{2,4})\b
						" style="cursor: help;">?</a></div>
						<div class="input">
							<input type="text" name="fields[<?php echo $i; ?>][verify]" class="verify" value="<?php echo $v['verify']; ?>"/>
						</div><?php /*/ ?></td>
		<!-- td class="pad"></td -->
	</tr>
</tbody>
<?php 

		}
		
		function field_type_options($i, $v){ ?>
<td><label>Select Method Options</label>
	<div class="input"> &nbsp;&nbsp;
		<label>
			<input type="radio" value="custom" name="fields[<?php echo $i ?>][type_option]"<?php
					 echo ($v['type_option'] != 'query') ? ' checked="checked"' : '' ?>/>
			Custom Choices <a href="#" onclick="return false;" title="Enter choices on each line." style="cursor: help;">?</a> </label>
		<br />
		&nbsp;&nbsp;
		<label>
			<input type="radio" value="query" name="fields[<?php echo $i ?>][type_option]"<?php
					 echo ($v['type_option'] == 'query') ? ' checked="checked"' : ''  ?>/>
			Query <a href="#" onclick="return false;" title="Enter get_posts parameters." style="cursor: help;">?</a> </label>
	</div></td>
<td colspan="3"><label for="fields-<?php echo $i ?>-type_option_data">Option Data</label>
	<div class="input">
		<textarea name="fields[<?php echo $i ?>][type_option_data]" id="fields-<?php echo $i ?>-type_option_data"><?php echo $v['type_option_data'] ?></textarea>
	</div></td>
<?php }
		
		ob_start();
			field_row(99, array('type'=>'default'));
			$field_row = ob_get_contents();
		ob_clean();
			field_type_options(99,array());
			$field_type_options = ob_get_contents();		
		ob_end_clean();
		
		?>
<script type="text/javascript">
	var field_type_options = '<?php echo str_replace(array("\r","\n","\t"), '', $field_type_options); ?>';
	var field_row = '<?php echo str_replace(array("\r","\n","\t"), '', $field_row); ?>';
	var field_type_edit = <?php echo json_encode($field_type_edit); ?>;
</script>
<table id="field-builder">
	<?php /*?>	<tbody>
		<tr><!-- td class="pad"></td -->
			<td colspan="4"><pre><?php echo print_r($fields, 1); ?></pre></td>
		<!-- td class="pad"></td --></tr>
	</tbody><?php */?>
	<?php $i = 0; foreach($fields as $v){ field_row($i, $v);  $i++; } ?>
	<tbody>
		<tr>
			<!-- td class="pad"></td -->
			<td colspan="4"><a href="#" class="add-field button">Add Field</a></td>
			<!-- td class="pad"></td -->
		</tr>
	</tbody>
</table>
<?php 
	}

?>
