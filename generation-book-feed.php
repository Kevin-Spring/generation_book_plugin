<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Plugin Name: Generation book-plugin
 * Description: Shortcode for displaying books
 * Version: 0.1.0
 * Author: The Generation AB
 * Text Domain: generation-book-plugin
 * Domain Path: /languages
 */

function my_css() {
    wp_register_style( 'style', plugins_url( 'style.css', __FILE__ ) );
    wp_enqueue_style( 'style' );
}
add_action( 'wp_enqueue_scripts', 'my_css' );

include 'generation-book-feed-post-type.php'; 

include 'generation-book-feed-metabox.php';

include 'generation-book-feed-shortcode.php';


?>