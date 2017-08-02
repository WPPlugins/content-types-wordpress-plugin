<?php 
	wp_enqueue_script('jquery');

	$post = is_object($post) ? $post : (isset($_GET['post']) ? get_post($_GET['post']) : new StdClass);

	// creates post hierarchy :)
	function create_post_hierarchy($type, $parent = 0, $level = 0){
		
		global $wpdb;
		$return_posts = array();
		$theposts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM $wpdb->posts WHERE `post_parent` = $parent AND post_type = '$type' ORDER BY `menu_order` ASC"));
		if(!is_array($theposts)) return false;
		
		foreach($theposts as $p){
			$p->level = $level;
			$return_posts[] = $p;
			$children_posts = create_post_hierarchy($type, $p->ID, ($level+1));
			if($children_posts)
				$return_posts = array_merge($return_posts, $children_posts);
		}
		if(count($return_posts))
			return $return_posts;		
		return false;
	}

	$field_type_edit_custom = array();		
			
	//	Build custom field wrappers calling on input building function (meta_box_inside)
	function init_post_fields(){
		global $post_types;
		global $post;
		foreach($post_types as $type){
			if(($type['settings']['slug'] == $post->post_type or $_GET['post_type'] == $type['settings']['slug']) and is_array($type['fields'])){
				$meta_fields = array();
				$required_fields = false;
				foreach($type['fields'] as $key=>$field){
					$meta_fields[] = $field['slug'].'='.$field['type'];
					$field_title = $field['name'];
					if(array_key_exists('required',$field)) {
						$required_fields = true;
						$field_title = '<span title="This field is required."><span class="title">'.$field_title.'</span><span style="color: red;">*</span></span>';
					}
					if(count($type['fields']) == count($meta_fields)){
						$field['meta_fields'] = implode('&', $meta_fields);
						$field['required_fields'] = $required_fields;
					}
					
					add_meta_box($field['slug'].'-meta', $field_title, 'meta_box_inside', $type['settings']['slug'], "normal", "high", $field);
				}
			}  // end fields looop
		}
	}
	
	//	Input building function echoing actual html
	function meta_box_inside($post, $metabox){
	
		$field = $metabox['args'];
		$field_value = get_post_custom_values($field['slug'], $post->ID);
		$field['value'] = $field_value[0];		
		
		$test_function = 
			function_exists('field_type_edit_'.$field['type']) ? 'field_type_edit_'.$field['type'] : 'field_type_edit_default';
		
		$test_function($field);
		
		if($field['description'])
			echo '<p>'.$field['description'].'</p>';
		
		if($field['meta_fields'])
			echo '<input type="hidden" style="display: none;" name="all_meta_fields" value="'.$field['meta_fields'].'"  />';
			
		if($field['required_fields'])
			echo '<script type="text/javascript" src="'.plugins_url('/field-verification.js', __FILE__).'"></script>';
	
	}
	
	//	Get Value Range
	function setup_value_range_array($field){
		
			$options = array();
			if(!isset($field['type_option_data']))
				return $options;		
			
			if(substr_count($field['type_option_data'], "\r")){
				foreach(explode("\r", $field['type_option_data']) as $v)
					if(trim($v)) $options[sanitize_title_with_dashes($v)] = trim($v);
				return $options;
			}
			
			$arguments = $field['type_option_data'].((substr_count($field['type_option_data'],'numberposts')) ? "" : '&numberposts=-1');		
			$get_posts = get_posts($arguments);
			if(is_array($get_posts))
			foreach($get_posts as $_post)
				$options[$_post->ID] =  $_post->post_title;
			return $options;
	}

	/*
		Post type functions
	*/
	$field_type_edit = array();	
	
	$field_type_edit = array_merge($field_type_edit, (is_array($field_type_edit_custom) ? $field_type_edit_custom : array()));
	
	//	Adds messages for custom post types
	add_filter('post_updated_messages', 'custom_post_types_updated_messages');
	function custom_post_types_updated_messages( $messages ) {
		global $post_types;
		foreach($post_types as $type){
			$s = $type['settings'];
			$messages[$s['slug']] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => sprintf( __($s['singular_name'].' updated. <a href="%s">View '.$s['singular_name'].'</a>'), esc_url( get_permalink($post_ID) ) ),
				2 => __('Custom field updated.'),
				3 => __('Custom field deleted.'),
				4 => __($s['singular_name'].' updated.'), // translators: %s: date and time of the revision 
				5 => isset($_GET['revision']) ? sprintf( __($s['singular_name'].' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => sprintf( __($s['singular_name'].' published. <a href="%s">View '.$s['singular_name'].'</a>'), esc_url( get_permalink($post_ID) ) ),
				7 => __($s['singular_name'].' saved.'),
				8 => sprintf( __($s['singular_name'].' submitted. <a target="_blank" href="%s">Preview '.$s['singular_name'].'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
				9 => sprintf( __($s['singular_name'].' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.$s['singular_name'].'</a>'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
				10 => sprintf( __($s['singular_name'].' draft updated. <a target="_blank" href="%s">Preview '.$s['singular_name'].'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			);
		}
		return $messages;
	}
	
?>