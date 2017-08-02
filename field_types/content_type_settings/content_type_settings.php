<?php
	
	wp_register_style('content_type_settings', plugins_url('/content_type_settings.css', __FILE__));
	wp_enqueue_style('content_type_settings');
	
    wp_enqueue_script('content_type_settings', plugins_url('/content_type_settings.js', __FILE__),array('jquery'));

	/* Do not alter this file in any way. */
	function field_type_edit_content_type_settings($field){
		
		global $_post;
		global $post;
		$field['value'] = maybe_unserialize($field['value']);
		$supports = array(
			array(1,'Title','title','Adds the title meta box when creating content for this custom post type'),
			array(1,'Editor','editor','Adds the content editor meta box when creating content for this custom post type'),
			array(0,'Excerpt','excerpt','Adds the excerpt meta box when creating content for this custom post type'),
			array(0,'Trackbacks','trackbacks','Adds the trackbacks meta box when creating content for this custom post type'),
			//array(0,'Custom Fields','custom-fields','Adds the custom fields meta box when creating content for this custom post type'),
			array(0,'Comments','comments','Adds the comments meta box when creating content for this custom post type'),
			array(0,'Revisions','revisions','Adds the revisions meta box when creating content for this custom post type'),
			array(0,'Featured Image','thumbnail','Adds the featured image meta box when creating content for this custom post type'),
			array(1,'Author','author','Adds the author meta box when creating content for this custom post type'),
			array(1,'Page Attributes','page-attributes','Adds the page attribute meta box when creating content for this custom post type')
		);
		$advanced = array(
			array(1,'Publicy Queriable','publicly_queryable','Whether this posts type is publicly queriable, shown in the Admin UI, and included in search.'),
			array(0,'Exclude from Search', 'exclude_from_search', 'Hide post type from search results.'),
			array(1,'Show in Admin UI', 'show_ui', 'Whether to generate a default UI for managing this post type.'),
			array(1,'Exportable', 'can_export', 'This post type be exported.'),			
			array(0,'Hierarchical', 'hierarchical', 'Whether the post type is hierarchical. Allows Parent to be specified.')			
		);
		if(!is_array($field['value'])){ $field['value'] = array();
			foreach($supports as $def_title_name_desc)
				$field['value']['supports-'.$def_title_name_desc[2]] = $def_title_name_desc[0];
			foreach($advanced as $def_title_name_desc)
				$field['value'][$def_title_name_desc[2]] = $def_title_name_desc[0];
		}
		$i = 3;
	?>
<table class="form-table" id="content-type-settings">
	<tbody>
		<tr>
			<th style="width: 150px;">Name</th>
			<td><input type="text" name="post_title" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $post->post_title ?>" style="width: 300px;" id="name">
				<a href="#" onclick="return false;" title="Name of Post Type" style="cursor: help;">?</a></td>
		</tr>
		<tr>
			<th>Sigular Name</th>
			<td><input type="text" name="settings[singular_name]" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $field['value']['singular_name']; ?>" style="width: 300px;" id="singular-name">
				<a href="#" onclick="return false;" title="Singular Name of Post Type" style="cursor: help;">?</a></td>
		</tr>
		<tr>
			<th>Supports</th>
			<td><?php foreach($supports as $def_title_name_desc){ ?>
				<input type="checkbox" name="settings[supports-<?php echo $def_title_name_desc[2]; ?>]" tabindex="<?php $i++; echo $i; ?>" value="1"<?php if($field['value']['supports-'.$def_title_name_desc[2]]) echo ' checked="checked"'; ?>/>
				<?php echo $def_title_name_desc[1]; ?> <a href="#" onclick="return false;" title="<?php echo $def_title_name_desc[3]; ?>" style="cursor: help;">?</a> <br>
				<?php } ?></td>
		</tr>
		<tr >
			<th><strong>Category</strong></th>
			<td><input type="checkbox" tabindex="<?php $i++; echo $i; ?>" name="settings[use_category]" id="use-category" value="1" <?php if($field['value']['use_category']) echo ' checked="checked"'; ?>/>
				Use Categories</td>
		</tr>
		<tr id="use-category1"<?php if(!$field['value']['use_category']) echo ' style="display:none;"'; ?>>
			<th class="sub">Name</th>
			<td><input type="text" name="settings[category_name]" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $field['value']['category_name']; ?>" style="width: 300px;" id="singular-name3" /></td>
		</tr>
		<tr id="use-category2"<?php if(!$field['value']['use_category']) echo ' style="display:none;"'; ?>>
			<th class="sub">Singular Name</th>
			<td><input type="text" name="settings[category_singular_name]" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $field['value']['category_singular_name']; ?>" style="width: 300px;" id="singular-name2" /></td>
		</tr>
		<tr>
			<th><strong>Tags</strong></th>
			<td><input type="checkbox" tabindex="<?php $i++; echo $i; ?>" name="settings[use_tags]" id="use-tags" value="1" <?php if($field['value']['use_tags']) echo ' checked="checked"'; ?> />
				Use Tags</td>
		</tr>
		<tr id="use-tags1"<?php if(!$field['value']['use_tags']) echo ' style="display:none;"'; ?>>
			<th class="sub">Name</th>
			<td><input type="text" name="settings[tags_name]" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $field['value']['tags_name']; ?>" style="width: 300px;" id="tags-name" /></td>
		</tr>
		<tr id="use-tags2"<?php if(!$field['value']['use_tags']) echo ' style="display:none;"'; ?>>
			<th class="sub">Singular Name</th>
			<td><input type="text" name="settings[tags_singular_name]" tabindex="<?php $i++; echo $i; ?>" value="<?php echo $field['value']['tags_singular_name']; ?>" style="width: 300px;" id="tags-singular-name" /></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tbody>
	<tbody>
		<tr id="advanced-setting-option">
			<td colspan="2"><a href="#">Hide Advanced Options</a>
				<input type="hidden" value="<?php echo $field['value']['advanced'] ? ($field['value']['advanced']) : 'no'; ?>" name="settings[advanced]" id="settings-advanced-show" /></td>
		</tr>
	</tbody>
	<tbody id="advanced-settings">
		<tr>
			<th>Advanced</th>
			<td><?php foreach($advanced as $def_title_name_desc){ ?>
				<input type="checkbox" name="settings[<?php echo $def_title_name_desc[2]; ?>]" tabindex="<?php $i++; echo $i; ?>" value="1"<?php if($field['value'][$def_title_name_desc[2]]) echo ' checked="checked"'; ?>/>
				<?php echo $def_title_name_desc[1]; ?> <a href="#" onclick="return false;" title="<?php echo $def_title_name_desc[3]; ?>" style="cursor: help;">?</a> <br>
				<?php } ?></td>
		</tr>
		<tr>
			<th>Capability Type</th>
			<td><input type="text" name="settings[capability_type]" tabindex="6" value="<?php echo $field['value']['capability_type'] ? $field['value']['capability_type'] : 'post' ?>">
				<a href="#" onclick="return false;" title="The post type to use for checking read, edit, and delete capabilities." style="cursor: help;">?</a></td>
		</tr>
		<tr>
			<th>Menu Position</th>
			<td><input type="text" name="settings[menu_position]" tabindex="11" size="5" value="<?php echo $field['value']['menu_position'] ? $field['value']['menu_position'] : '' ?>">
				<a href="#" onclick="return false;" title="The position in the menu order the post type should appear.
5 - below Posts
10 - below Media
20 - below Pages
60 - below first separator
100 - below second separator" style="cursor: help;">?</a></td>
		</tr>
	</tbody>
</table>
<?php
	}	
?>