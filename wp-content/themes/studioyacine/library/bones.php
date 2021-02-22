<?php

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function bones_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // no php needed above it
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); // php is not closed in the last line
		
	// remove WP version from css
	add_filter( 'style_loader_src', 'bones_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'bones_remove_wp_ver_css_js', 9999 );

} /* end bones head cleanup */

// A better title
// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
	global $page, $paged;
  
	// Don't affect in feeds.
	if ( is_feed() ) return $title;
  
	// Add the blog's name
	if ( 'right' == $seplocation ) {
	  $title .= get_bloginfo( 'name' );
	} else {
	  $title = get_bloginfo( 'name' ) . $title;
	}
  
	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
  
	if ( $site_description && ( is_home() || is_front_page() ) ) {
	  $title .= " {$sep} {$site_description}";
	}
  
	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
	  $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
	}
  
	return $title;
  
}

// remove WP version from RSS
function bones_rss_version() { return ''; }

// remove WP version from scripts
function bones_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function bones_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function bones_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function bones_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}

/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function bones_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

  if (!is_admin()) {

		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/dist/assets/js/jquery.min.js', array(), null, true);

		$cache_buster = date("YmdHi", filemtime( get_stylesheet_directory() . '/library/css/style.css'));

		// register main stylesheet
		wp_register_style( 'bones-stylesheet', get_stylesheet_directory_uri() . '/dist/assets/css/main.min.css', array(), $cache_buster, 'all' );

		// comment reply script for threaded comments
		if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
			wp_enqueue_script( 'comment-reply' );
		}

		// wp_register_script( 'bones-plugins', get_stylesheet_directory_uri() . '/dist/js/plugins.min.js', '', '', true );

		//adding scripts file in the footer
		wp_register_script( 'bones-js', get_stylesheet_directory_uri() . '/dist/assets/js/all.min.js', array( 'jquery' ), '', true );

		// enqueue styles and scripts
		wp_enqueue_script( 'bones-modernizr' );
		wp_enqueue_style( 'bones-stylesheet' );

		wp_enqueue_script( 'jquery' );
		// wp_enqueue_script( 'bones-plugins' );
		wp_enqueue_script( 'bones-js' );
	}
}

function passData(){
	global $post;
	$category = null;

	if(is_404()){
		return array('');
	}

	if(is_category()) { 
		$category = single_cat_title('', false);
	} elseif(is_single()) {
		$category = get_the_category();
		$category = $category[0]->slug;			
	}

	return array(
		'home' => get_stylesheet_directory_uri(),
		'title' => $post->post_title,
		'postId' => $post->ID,
		'category' => $category
	);
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function bones_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	// set_post_thumbnail_size(125, 125, true);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// adding post format support
	// add_theme_support( 'post-formats',
	// 	array(
	// 		'aside',             // title less blurb
	// 		'gallery',           // gallery of images
	// 		'link',              // quick link to other site
	// 		'image',             // an image
	// 		'quote',             // a quick quote
	// 		'status',            // a Facebook like status update
	// 		'video',             // video
	// 		'audio',             // audio
	// 		'chat'               // chat transcript
	// 	)
	// );

	// wp menus
	add_theme_support( 'menus' );

	// -- Disable Gradients
	add_theme_support( 'disable-custom-colors' );	

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'bonestheme' ),
			'footer-links' => __( 'Footer Links', 'bonestheme' ) // secondary nav in footer
		)
	);


	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Blue', 'bonestheme' ),
			'slug'  => 'blue',
			'color'	=> '#19D2D7',
		),
		array(
			'name'  => __( 'Peach', 'bonestheme' ),
			'slug'  => 'peach',
			'color' => '#fbebdb',
		),
		array(
			'name'	=> __( 'Red', 'bonestheme' ),
			'slug'	=> 'red',
			'color'	=> '#FE412E',
		),
	) );

	add_theme_support( 'editor-font-sizes', array(
		array(
			'name'      => __( 'Label', 'bonestheme' ),
			'shortName' => __( 'label', 'bonestheme' ),
			'size'      => 12,
			'slug'      => 'label'
		),		
		array(
			'name'      => __( 'Small', 'bonestheme' ),
			'shortName' => __( 'S', 'bonestheme' ),
			'size'      => 14,
			'slug'      => 'small'
		),
		array(
			'name'      => __( 'Medium', 'bonestheme' ),
			'shortName' => __( 'M', 'bonestheme' ),
			'size'      => 28,
			'slug'      => 'medium'
		),
		array(
			'name'      => __( 'Large', 'bonestheme' ),
			'shortName' => __( 'L', 'bonestheme' ),
			'size'      => 40,
			'slug'      => 'large'
		),
		array(
			'name'      => __( 'XL', 'bonestheme' ),
			'shortName' => __( 'XL', 'bonestheme' ),
			'size'      => 60,
			'slug'      => 'xl'
		)
	));	

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array(
		'comment-list',
		'search-form',
		'comment-form'
	) );

} /* end bones theme support */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function bones_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bones_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function bones_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'bonestheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'bonestheme' ) .'</a>';
}



?>
