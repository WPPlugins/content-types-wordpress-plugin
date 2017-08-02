<?php

require_once('../../../../../wp-admin/admin.php');

$query = 'post_status=inherit&post_mime_type=image&post_type=attachment';
$query .= (isset($_GET['s'])) ? '&s='.$_GET['s'] : '';
$query .= (isset($_GET['paged'])) ? '&paged='.$_GET['paged'] : '';
query_posts($query);

wp_iframe( 'image_select_popup', array() );

function image_select_popup(){
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
	
	$_GET['paged'] = ($_GET['paged']) ? $_GET['paged'] : 1;
//echo '<pre>'; var_dump($wp_query->posts); exit;
	
?>
<div class="image-selector" style="width: 620px; margin: 20px auto 0 auto; float: none; overflow: none;">
<form id="filter" action="" method="get">
	<input type="hidden" name="slug" value="<?php echo $_GET['slug']; ?>" />
<p id="media-search" class="search-box">
	<label class="screen-reader-text" for="media-search-input"><?php _e('Search Media');?>:</label>
	<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Media' ); ?>" class="button" />
</p>

<?php
$all_images = new WP_Query();
$all_images->query('posts_per_page=-1&post_status=inherit&post_mime_type=image&post_type=attachment');
?>
<ul class="subsubsub">
<li>
<a href="#" class="current">Images <span class="count">(<span id="image-counter"><?php echo $all_images->post_count; ?></span>)</span></a>
</li>
</ul>
<div class="tablenav">

<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($wp_query->found_posts / 10),
	'current' => $_GET['paged']
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft actions">
<?php

$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
<select name='m'>
<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( isset($_GET['m']) && ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] ) )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='" . esc_attr( $arc_row->yyear . $arc_row->mmonth ) . "'>";
	echo esc_html( $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear" );
	echo "</option>\n";
}
?>
</select>
<?php } ?>

<input type="submit" id="post-query-submit" value="<?php echo esc_attr( __( 'Filter &#187;' ) ); ?>" class="button-secondary" />

</div>

<br class="clear" />
</div>
<input id="selected_images_value" type="hidden">
</form>
<div style="margin-top: 20px; float: left; border-bottom: #ccc solid 1px;">
<?php
if(!count($wp_query->posts))
echo '<p>No Results Found.</p>';
else
foreach($wp_query->posts as $p){
//echo '<pre>'; var_dump(wp_get_attachment_image_src($p->ID, array(32,32))); exit;
$image = wp_get_attachment_image_src($p->ID, array(100,100));
 ?>
<div class="media-item preloaded" style="width: 613px; border: #ccc solid 1px; border-bottom: none; float: left; clear: both; padding: 5px 0 5px 5px;">

<?php echo str_replace('>', ' style="vertical-align: middle; padding-right: 10px;">', wp_get_attachment_image($p->ID, array(40, 40))); ?>

<span class="title"><?php echo $p->post_title; ?></span>
	<a class="insert-select-image describe-toggle-on" href="#<?php echo $p->ID; ?>?<?php echo $image[0]; ?>" id="image-<?php echo $p->ID; ?>">Select</a>
</div>
<?php } ?>
</div>
</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function($){

var slug = '<?php echo $_GET['slug']; ?>';

	$('a.insert-select-image').click(function(){
	
		var id_src = $(this).attr('href').split('#')[1].split('?');

		if($(this).hasClass('remove')){		
			top.select_image_remove(id_src[0], slug);		
			$(this).removeClass('remove');
			$(this).text('Select');	
		}else{
			top.select_image_add(id_src[1], id_src[0], slug);
			$(this).addClass('remove');
			$(this).text('Remove');	
		}
		return false;
	
	});

	var val = $('#'+slug, top.document).val().split('|');
	
	$('a.insert-select-image').each(function(){
		
		var id_src = $(this).attr('href').split('#')[1].split('?');

		for(var i = 0; i < val.length; i++){
			if(val[i]!=id_src[0]) continue;			
			$(this).addClass('remove');
			$(this).text('Remove');
		}
	
	});
});
-->
</script>
<?php
}
exit;

?>