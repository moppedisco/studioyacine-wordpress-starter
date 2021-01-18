<?php
/* Custom Post Types GOES HERE*/

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'bones_flush_rewrite_rules' );

// Flush your rewrite rules
function bones_flush_rewrite_rules() {
	flush_rewrite_rules();
}

// let's create the function for the custom type
function post_work() {
	// creating (registering) the custom type
	register_post_type( 'work', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Work', 'bonestheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Work Post', 'bonestheme' ), /* This is the individual type */
			'all_items' => __( 'All Work', 'bonestheme' ), /* the all items menu item */
			'add_new' => __( 'Add New Work', 'bonestheme' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Work', 'bonestheme' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'bonestheme' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Work', 'bonestheme' ), /* Edit Display Title */
			'new_item' => __( 'New Work', 'bonestheme' ), /* New Display Title */
			'view_item' => __( 'View Work', 'bonestheme' ), /* View Display Title */
			'search_items' => __( 'Search Work', 'bonestheme' ), /* Search Custom Type Title */
			'not_found' =>  __( 'Nothing found in the Database.', 'bonestheme' ), /* This displays if there are no entries yet */
			'not_found_in_trash' => __( 'Nothing found in Trash', 'bonestheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'Work post', 'bonestheme' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => get_stylesheet_directory_uri() . '/library/images/custom-post-icon.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'portfolio', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'portfolio', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			'show_in_rest' => true,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions')
		) /* end of options */
	); /* end of register post type */
}

// now let's add custom categories (these act like categories)
register_taxonomy( 'work_category',
	array('work'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
	array('hierarchical' => false,     /* if this is true, it acts like categories */
		'labels' => array(
			'name' => __( 'Work Category', 'bonestheme' ), /* name of the custom taxonomy */
			'singular_name' => __( 'Work Category', 'bonestheme' ), /* single taxonomy name */
			'search_items' =>  __( 'Search Work Categories', 'bonestheme' ), /* search title for taxomony */
			'all_items' => __( 'All Work Categories', 'bonestheme' ), /* all title for taxonomies */
			'parent_item' => __( 'Parent Work Category', 'bonestheme' ), /* parent title for taxonomy */
			'parent_item_colon' => __( 'Parent Work Category:', 'bonestheme' ), /* parent taxonomy title */
			'edit_item' => __( 'Edit Work Category', 'bonestheme' ), /* edit custom taxonomy title */
			'update_item' => __( 'Update Work Category', 'bonestheme' ), /* update title for taxonomy */
			'add_new_item' => __( 'Add New Work Category', 'bonestheme' ), /* add new title for taxonomy */
			'new_item_name' => __( 'New Work Category Name', 'bonestheme' ) /* name title for taxonomy */
		),
		'show_admin_column' => true,
		'show_ui' => true,
		'query_var' => true
	)
);

// adding the function to the Wordpress init
// add_action( 'init', 'post_work');

?>
