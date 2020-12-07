<?php if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Plugin Name: Generation book-plugin
 * Description: Shortcode for displaying books
 * Version: 0.1.0
 * Author: The Generation AB
 * Text Domain: generation-book-plugin
 * Domain Path: /languages
 */

//Resultat Shortcode: [generation_blog_feed category_id=”5” posts_per_page=”7” offset=”4”]

function my_css() {
    wp_register_style( 'style', plugins_url( 'style.css', __FILE__ ) );
    wp_enqueue_style( 'style' );
}
add_action( 'wp_enqueue_scripts', 'my_css' );


function init_generation_books_post_type() {

    $labels = [
        'name'                  => _x( 'Books', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Book', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Books', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Book', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Book', 'textdomain' ),
        'new_item'              => __( 'New Book', 'textdomain' ),
        'edit_item'             => __( 'Edit Book', 'textdomain' ),
        'view_item'             => __( 'View Book', 'textdomain' ),
        'all_items'             => __( 'All Books', 'textdomain' ),
        'search_items'          => __( 'Search Books', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Books:', 'textdomain' ),
        'not_found'             => __( 'No books found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No books found in Trash.', 'textdomain' ),
        'featured_image'        => _x( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'archives'              => _x( 'Book archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
        'insert_into_item'      => _x( 'Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
        'filter_items_list'     => _x( 'Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
        'items_list_navigation' => _x( 'Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
        'items_list'            => _x( 'Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
    ];

    $args = [
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_rest'          => true,
        'query_var'             => true,
        'rewrite'               => [ 'slug' => 'book' ],
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => null,
        'supports'              => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ],
        'register_meta_box_cb'  => 'add_generation_books_metaboxes',  
    ];

    register_post_type( 'books', $args );

}

add_action( 'init', 'init_generation_books_post_type' );


/* Metabox */
function add_generation_books_metaboxes() {
    
    add_meta_box(
        'generation_books_metaboxes',
        'Book Options',
        'generation_books_metaboxes',
        'books',
        'side',
        'default'
    );
}

function generation_books_metaboxes(){

    global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'books_fields' );

	// Get the location data if it's already been entered
    $price = get_post_meta( $post->ID, 'price', true );
    $author = get_post_meta( $post->ID, 'author', true );

    // Output the field
    ?>
    <label for="price">Pris</label> 
    <input type="number" name="price" value=" <?php esc_textarea( $price ) ?>" class="widefat">
    <label for="genre">Genre</label>
    <select name="genre" id="genre" class="postbox">
        <option value="">Select something...</option>
        <option value="barn" <?php selected( $value, 'barn' ); ?>>Barn</option>
        <option value="thriller" <?php /* selected( $value, 'thriller' ); */ ?>>Thriller</option>
        <option value="äventyr" <?php /* selected( $value, 'äventyr' ); */ ?>>Äventyr</option>
    </select>
    <label for="author">Författare</label>
    <input type="text" name="author" value=" <?php esc_textarea( $author ) ?>" class="widefat">

    <?php 

}














/* function get_generation_blog_feed( $attr ) {

    $shortcode_args = shortcode_atts ( [
        'posts_per_page'    => 1,
        'category_id'       => 0,
        'offset'            => 0,
    ], $attr );

    //ob_start();
    $return_string = '<div class="grid-container">';
     
    query_posts( [
        'posts_per_page' => intval( $shortcode_args[ 'posts_per_page' ] ),
        'cat'            => intval( $shortcode_args[ 'category_id' ] ),
        'offset'         => intval( $shortcode_args[ 'offset' ] )
    ] ); */

    /*
    $query = new WP_Query( $args );

    while ( $query->have_posts() ) {
        $query->the_post();
    }
    */

    /* if ( have_posts () ) :
       while ( have_posts() ) : 
        the_post();

       //var_dump(get_the_post_thumbnail( $post_id, 'medium' ));

          $return_string .= '<div class="grid-item">
                                <p>' . esc_html( get_the_category()[0]->cat_name ) . '</p>
                                <h4> <a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</h4>' . get_the_post_thumbnail( $post_id, 'medium' ) . '</a>
                            </div>';
       endwhile;
    endif;
    
    $return_string .= '</div>';
 
    wp_reset_query();
   
    //ob_get_clean(); 
    return $return_string;
}

add_shortcode( 'generation_blog_feed', 'get_generation_blog_feed' );
 */


