<?php

// create new content type on activation
add_action( 'init', 'promo_type' );
function promo_type() {
	  $labels = array(
		'name' => _x('Promos', 'post type general name'),
		'singular_name' => _x('Promo', 'post type singular name'),
		'add_new' => _x('Add New', 'promo'),
		'add_new_item' => __('Add New Promo'),
		'edit_item' => __('Edit Promo'),
		'new_item' => __('New Promo'),
		'all_items' => __('All Promos'),
		'view_item' => __('View Promo'),
		'search_items' => __('Search Promos'),
		'not_found' =>  __('No promos found'),
		'not_found_in_trash' => __('No promos found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Promos'
	
	  );
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor' )
	  ); 
	  register_post_type('promo',$args);
}

//add filter to ensure the text Promo, or promo, is displayed when user updates a promo 
add_filter( 'post_updated_messages', 'codex_promo_updated_messages' );
function codex_promo_updated_messages( $messages ) {
	  global $post, $post_ID;
	
	  $messages['promo'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Promo updated. <a href="%s">View promo</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Promo updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Promo restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Promo published. <a href="%s">View promo</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Promo saved.'),
		8 => sprintf( __('Promo submitted. <a target="_blank" href="%s">Preview promo</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Promo scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview promo</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Promo draft updated. <a target="_blank" href="%s">Preview promo</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );
	
	  return $messages;
}

add_action( 'init', 'promo_init' );
function promo_init() {
		$labels = array(
		'name' => _x('Promos', 'post type general name'),
		'singular_name' => _x('Promo', 'post type singular name'),
		'add_new' => _x('Add New', 'promo'),
		'add_new_item' => __('Add New Promo'),
		'edit_item' => __('Edit Promo'),
		'new_item' => __('New Promo'),
		'all_items' => __('All Promos'),
		'view_item' => __('View Promo'),
		'search_items' => __('Search Promos'),
		'not_found' =>  __('No promos found'),
		'not_found_in_trash' => __('No promos found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Promos'
	
	  );
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor' )
	  ); 
	  register_post_type('promo',$args);
}

function promo_rewrite_flush() 
{
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    promo_init();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'promo_rewrite_flush' );
