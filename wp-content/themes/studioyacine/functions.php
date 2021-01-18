<?php
/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, etc.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

  //Allow editor style.
  add_editor_style( get_stylesheet_directory_uri() . '/library/css/editor-style.css' );

  // let's get language support going, if you need it
  load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

  // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
  require_once( 'library/custom-post-type.php' );

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  bones_theme_support();

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 680;
}

/************* THEME CUSTOMIZE *********************/

function bones_theme_customizer($wp_customize) {
  // $wp_customize calls go here.
  //
  // Uncomment the below lines to remove the default customize sections

  $wp_customize->remove_section('title_tagline');
  $wp_customize->remove_section('colors');
  $wp_customize->remove_section('background_image');
  $wp_customize->remove_section('static_front_page');
  $wp_customize->remove_section('nav');

  // Uncomment the below lines to remove the default controls
  $wp_customize->remove_control('blogdescription');

  // Uncomment the following to change the default section titles
  $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
  $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/*******************************************************************************
GUTENBERG
*******************************************************************************/

add_action( 'after_setup_theme', 'studioyacine_gutenberg_css' );
 
function studioyacine_gutenberg_css(){
 
	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
	add_editor_style( 'library/css/style-editor.css' ); // tries to include style-editor.css directly from your theme folder
 
}

// ENABLED DEFAULTS
function studioyacine_gutenberg_scripts() {
  wp_enqueue_script( 'studioyacine-editor', get_template_directory_uri() . '/library/js/editor.js', array( 'wp-blocks', 'wp-dom' ), filemtime( get_template_directory() . '/library/js/editor.js' ), true );
}
add_action( 'enqueue_block_editor_assets', 'studioyacine_gutenberg_scripts' );

// ALLOWED BLOCKS
add_filter( 'allowed_block_types', 'studioyacine_allowed_block_types');
function studioyacine_allowed_block_types( $allowed_blocks ) {
  return array(
    'core/image',
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/quote',
    'core/gallery',
    'core/media-text',
    'core/separator',
    // 'core-embed/youtube'
  );
}

function ea_disable_editor( $id = false ) {

	$excluded_templates = array(
    'page-frontpage.php',
    'page-about.php',
		// 'templates/contact.php'
	);

	$excluded_ids = array(
		// get_option( 'page_on_front' )
	);

	if( empty( $id ) )
		return false;

	$id = intval( $id );
	$template = get_page_template_slug( $id );

	return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
}


/**
 * Disable Gutenberg by template
 *
 */
function ea_disable_gutenberg( $can_edit, $post_type ) {

	if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
		return $can_edit;

	if( ea_disable_editor( $_GET['post'] ) )
		$can_edit = false;

	return $can_edit;

}
// add_filter( 'gutenberg_can_edit_post_type', 'ea_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'ea_disable_gutenberg', 10, 2 );

/*******************************************************************************
CUSTOM INCLUDE TEMPLATE WITH PARAMETER
*******************************************************************************/
function get_template_part_with_params( $slug, $name, $params ) {
  $templates = array();
    $name      = (string) $name;
  
    if ( '' !== $name ) {
      $templates[] = "{$slug}-{$name}.php";
    }
  
    $templates[] = "{$slug}.php";
  
    // Save params to globals
    $GLOBALS['my_template_params'] = $params;
  
    locate_template( $templates, true, false );
  
    // Empty params to prevent some possible bugs
    $GLOBALS['my_template_params'] = [];
  }
  
function get_template_param( $template_param ) {
  if ( isset( $GLOBALS['my_template_params'][ $template_param ] ) ) {
    return $GLOBALS['my_template_params'][ $template_param ];
  }

  return false;
}

/*******************************************************************************
ADD ACF GLOBAL OPTION 
*******************************************************************************/
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page( array(
      'page_title' 	=> 'Global',
      'menu_title' 	=> 'Global',
      'menu_slug' 	=> 'global',
      'capability' 	=> 'edit_posts',
      'icon_url' => 'dashicons-admin-site',
      'position' => 20
  ));
}

/*******************************************************************************
WYSIWYG
*******************************************************************************/
add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars ) {

  // Add a new toolbar called "studioyacine custom"
	// - this toolbar has only 1 row of buttons
	$toolbars['studioyacine custom' ] = array();
  $toolbars['studioyacine custom' ][1] = array('styleselect','formatselect', 'bold' , 'italic','underline','alignleft','aligncenter','alignright','bullist','numlist','link','removeformat');


	// Edit the "Full" toolbar and remove 'code'
	// - delet from array code from http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
	if( ($key = array_search('code' , $toolbars['Full' ][2])) !== false ){
	    unset( $toolbars['Full'][2][$key] );
	}

	// remove the 'Basic' toolbar completely
	unset( $toolbars['Basic' ] );

	// return $toolbars - IMPORTANT!
	return $toolbars;
}

/* DON'T DELETE THIS CLOSING TAG */ ?>
