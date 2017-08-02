<?php

/* 

Plugin Name: Content Types

Plugin URI: http://www.scottreeddesign.com 

Description: Content Types is a WordPress plugin that helps you create custom content types in WordPress. It allows for custom categories, custom tags, and custom input fields.

Author: Brian S. Reed 

Version: 1.6.6

Author URI: http://www.scottreeddesign.com

*/



$post = is_object($post) ? $post : (isset($_GET['post']) ? get_post($_GET['post']) : new StdClass);



// define base post_type

$post_types = array(array('post_data'=> array('post_title' => 'Content Types'),'settings'=>array('show_ui'=>1,'singular_name'=>'Content Type','rewrite' => false, 'menu_position' => 62),'fields' => array(array('name'=>'Settings', 'type'=>'content_type_settings'),array('name'=>'Fields', 'type'=>'content_type_fields'))));



$raw_post_types = get_posts('post_type=content-type&numberposts=-1');

foreach($raw_post_types as $type)

	$post_types[] = array_merge(array('post_data' => (array) $type), get_all_post_meta($type->ID));



$content_type_slug_array = array();

foreach($post_types as $key=>$type){

	$post_types[$key]['settings']['slug'] = sanitize_title_with_dashes($type['settings']['singular_name']);

	$content_type_slug_array[] = $post_types[$key]['settings']['slug'];

	$post_types[$key]['settings']['name'] = $type['post_data']['post_title'];

	unset($post_types[$key]['post_data']);

	

	if(is_array($type['fields']))

	foreach($type['fields'] as $fields_key=>$field){

		if(!is_array($field) || !$field['name']) 

			unset($post_types[$key]['fields'][$fields_key]);

		else

			$post_types[$key]['fields'][$fields_key]['slug'] = str_replace('-','_',sanitize_title_with_dashes($field['name']));

	}



	if(in_array($post_types[$key]['settings']['slug'], array($post->post_type, $_GET['post_type'],$_POST['post_type'])))

		$current_type = $post_types[$key];

}

//echo '<pre>'.print_r($current_type, 1).'</pre>'; exit;



$build_fields = ((substr_count($_SERVER['SCRIPT_NAME'], '/wp-admin/post.php') or 

	substr_count($_SERVER['SCRIPT_NAME'], '/wp-admin/post-new.php')) and 

	(in_array($post->post_type, $content_type_slug_array) or 

	in_array(($_GET['post_type']+""), $content_type_slug_array)));

	

if($build_fields && $current_type)

	foreach($current_type['fields'] as $field){

		$field_types_dir = '../wp-content/plugins/content-types-wordpress-plugin/field_types/';

		if(file_exists($field_types_dir.$field['type'].'.php')){

			require_once($field_types_dir.$field['type'].'.php');

		}				

		if(file_exists($field_types_dir.$field['type'].'/'.$field['type'].'.php')){

			require_once($field_types_dir.$field['type'].'/'.$field['type'].'.php');

		}

	}		



//	Register custom post types & call custom fields function (init_post_fields)

add_action('init', 'init_custom_post_types');

function init_custom_post_types(){



	global $post;

	global $post_types;

	global $build_fields;

	$post = is_object($post) ? $post : (isset($_GET['post']) ? get_post($_GET['post']) : new StdClass);



	foreach($post_types as $k=>$type){

		$s = $type['settings'];

		// register post types {		

			$args = array(

				'labels' => array(

					'name' => _x($s['name'], 'post type general name'),

					'singular_name' => _x($s['singular_name'], 'post type singular name'),

					'add_new' => _x('Add New', strtolower($s['singular_name'])),

					'add_new_item' => __('Add New '.$s['singular_name']),

					'edit_item' => __('Edit '.$s['singular_name']),

					'new_item' => __('New '.$s['singular_name']),

					'view_item' => __('View '.$s['singular_name']),

					'search_items' => __('Search '.$s['name']),

					'not_found' =>  __('No '.strtolower($s['name']).' found'),

					'not_found_in_trash' => __('No '.strtolower($s['name']).' found in Trash'), 

					'parent_item_colon' => ''

				)

			);

			if(!isset($s['rewrite']) and !$s['rewrite'])

				$args['rewrite'] = array('slug'=>$type['settings']['slug'],'with_front'=>false);

				

			foreach(array('publicly_queryable'=>true,'show_ui'=>true,'hierarchical'=>false,'public'=>true,'can_export'=>true,'capability_type'=>'post','menu_position'=>60) as $key=>$value)

				$args[$key] = array_key_exists($key, $s) ? $s[$key] : $value;

				

			$args['menu_position'] = (int) $s['menu_position'];

			$args['show_ui'] = (bool) $s['show_ui'];

			

			$args['supports'] = array(''); //this needs to have an array of something otherwise it adds the default support

			$supports = array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes');

			foreach($supports as $support)

				if(array_key_exists('supports-'.$support, $s)) $args['supports'][] = $support;



			//$register_post_type = 

			register_post_type($s['slug'], $args);

		// }	

		// add category & tag support {

			if($s['use_category'] and $s['category_name'] and $s['category_singular_name']){		

			list($s_cat, $p_cat) = array($s['category_singular_name'], $s['category_name']);

			if(strtolower($s_cat) != 'type')	

			register_taxonomy(sanitize_title_with_dashes($s_cat), $s['slug'], array(

				'hierarchical' => true,

				'labels' =>  array(

						'name' => _x( $p_cat, 'taxonomy general name' ),

						'singular_name' => _x( "$s_cat", 'taxonomy singular name' ),

						'search_items' =>  __( "Search $p_cat" ),

						'all_items' => __( "All $p_cat" ),

						'parent_item' => __( "Parent $s_cat" ),

						'parent_item_colon' => __( "Parent $s_cat:" ),

						'edit_item' => __( "Edit $s_cat" ),

						'update_item' => __( "Update $s_cat" ),

						'add_new_item' => __( "Add New $s_cat" ),

						'new_item_name' => __( "New $s_cat Name" ),

					),

				'show_ui' => true,

				'query_var' => true,

				'public' => true,

				'rewrite' => array( 'slug' => sanitize_title_with_dashes($s_cat) ),

			));	

		}

		

			if($s['use_tags'] and $s['tags_name'] and $s['tags_singular_name']){		

				list($s_tag, $p_tag) = array($s['tags_singular_name'], $s['tags_name']);

				if(strtolower($s_tag) != 'type')	

				register_taxonomy(sanitize_title_with_dashes($s_tag), $s['slug'], array(

					'hierarchical' => false,

					'labels' =>  array(

							'name' => _x( $p_tag, 'taxonomy general name' ),

							'singular_name' => _x( "$s_tag", 'taxonomy singular name' ),

							'search_items' =>  __( "Search $p_tag" ),

							'all_items' => __( "All $p_tag" ),

							'parent_item' => __( "Parent $s_tag" ),

							'parent_item_colon' => __( "Parent $s_tag:" ),

							'edit_item' => __( "Edit $s_tag" ),

							'update_item' => __( "Update $s_tag" ),

							'add_new_item' => __( "Add New $s_tag" ),

							'new_item_name' => __( "New $s_tag Name" ),

						),

					'show_ui' => true,

					'query_var' => true,

					'public' => true,

					'rewrite' => array( 'slug' => sanitize_title_with_dashes($s_tag) ),

				));	

			}

		// }

	}



	if($build_fields){

		require_once("build-fields.php");

		add_action("admin_init", "init_post_fields");

	}

	

	



	flush_rewrite_rules();

}



	// save post {



	add_action('save_post', 'save_fields');	

	function save_fields(){	

		global $post;

		foreach(explode('&', $_POST['all_meta_fields']) as $name_type){

			list($name, $type) = explode('=',$name_type);

			$field_type_save_function = 'field_type_save_'.$type;

			if(function_exists($field_type_save_function) and !array_key_exists($name.'_save_posted', $_POST)){

				$_POST[$name.'_save_posted'] = true;

				$_POST[$name] = $field_type_save_function($_POST[$name]);

			}

			update_post_meta($post->ID, $name, $_POST[$name]);

		}

	}

	// }





function get_all_post_meta($post_id){

	global $wpdb;

	$metas = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE `post_id` = '$post_id' AND SUBSTRING(`meta_key`, 1, 1) <> '_'",'ARRAY_A');

	$am = array();

	foreach($metas as $m)

		$am[$m['meta_key']] = maybe_unserialize($m['meta_value']);

	return $am;

}



?>