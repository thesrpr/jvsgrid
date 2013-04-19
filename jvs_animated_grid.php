<?php
/*
Plugin Name: JVS Animated Grid Plugin
Description: Creates animated grid on home page
Version: 1.0
License: GPL
Author: Corey Ellis
Author URI: http://theseniorpartners.com
*/

// login rewrite
register_activation_hook( __FILE__, 'plugin_activate' );
function plugin_activate() {
    flush_rewrite_rules();
}
 
register_deactivation_hook( __FILE__, 'plugin_deactivate' );
function plugin_deactivate() {
    flush_rewrite_rules();
}
// Plug In Updater

if (is_admin()) {
	include_once('includes/updater.php'); // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'jvs-animated-grid', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/thesrpr/jvsgrid', // the github API url of your github repo
        'raw_url' => 'https://raw.github.com/thesrpr/jvsgrid/master', // the github raw url of your github repo
        'github_url' => 'https://github.com/thesrpr/jvsgrid', // the github url of your github repo
        'zip_url' => 'https://github.com/thesrpr/jvsgrid/zipball/master', // the zip url of the github repo
        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '3.0', // which version of WordPress does your plugin require?
        'tested' => '3.5.1', // which version of WordPress is your plugin tested up to?
        'readme' => 'README.md' // which file to use as the readme for the version number
    );
    new WP_GitHub_Updater($config);
}

// add scripts and styles
function thesrpr_jvs_includes()
  {
    if (!is_admin()) {
      
  wp_enqueue_style( 'grid-styles', plugins_url('/' , __FILE__).'includes/gridstyles.css', '', '','all' );

  if ( ! jQuery ) { wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'); }

  wp_enqueue_script('jquery-timing', plugins_url('/' , __FILE__).'includes/jquery-timing.min.js', array( 'jquery' ), '', true);
  wp_enqueue_script('grid', plugins_url('/' , __FILE__).'includes/grid.js', array( 'jquery', 'jquery-timing' ), '', true);

  
  }   
}
  
add_action('init', 'thesrpr_jvs_includes');


// add thumbnails
add_theme_support( 'post-thumbnails', array( 'grid_post' ) );


add_action( 'init', 'create_post_type' );
function create_post_type() {

//Animated Grid cpt
$labels = array( 
        'name' => _x( 'grid posts', 'grid_post' ),
        'singular_name' => _x( 'grid post', 'grid_post' ),
        'add_new' => _x( 'Add New', 'grid_post' ),
        'add_new_item' => _x( 'Add New grid post', 'grid_post' ),
        'edit_item' => _x( 'Edit grid post', 'grid_post' ),
        'new_item' => _x( 'New grid post', 'grid_post' ),
        'view_item' => _x( 'View grid post', 'grid_post' ),
        'search_items' => _x( 'Search grid posts', 'grid_post' ),
        'not_found' => _x( 'No grid posts found', 'grid_post' ),
        'not_found_in_trash' => _x( 'No grid posts found in Trash', 'grid_post' ),
        'parent_item_colon' => _x( 'Parent grid post:', 'grid_post' ),
        'menu_name' => _x( 'grid posts', 'grid_post' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom post type for JVS animated grid',
        'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => plugins_url('/' , __FILE__).'includes/grid.png',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
        );

    register_post_type( 'grid_post', $args );


}
function rewrite_flush() 
        {
 
            create_post_type();

            flush_rewrite_rules();
        }
register_activation_hook( __FILE__, 'rewrite_flush' );

function the_content_by_id($post_id) {
     $page_data = get_page($post_id);
     if ( $page_data )
          return apply_filters('the_content',$page_data->post_content);
     return false;
}

add_shortcode( jvsgrid, thesrpr_jvs_grid );


function thesrpr_jvs_grid() {
    global $post;

    $args = array('post_type' => 'grid_post', 'posts_per_page' => 8, 'orderby' => 'menu_order', 'order' =>  'ASC'); 
    $posts = get_posts($args);                

    $output = '<ul id="animated-grid">';

    foreach ( $posts as $post ) {

        $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        $content = the_content_by_id($post->ID);
        $cite = get_the_title( $post->ID);

        $output .= '<li>';

        $output .= '<a href=""><img src="'.$img_url.'"/></a>';


        $output .= '<div class="quote">'.$content.'</div>';

        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output; 

}