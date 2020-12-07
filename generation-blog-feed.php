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
        'name'                  => __( 'Books', 'Post type general name'),
        'singular_name'         => __( 'Book', 'Post type singular name'),
        'menu_name'             => __( 'Books', 'Admin Menu text'),
        'name_admin_bar'        => __( 'Book', 'Add New on Toolbar'),
        'add_new'               => __( 'Add New'),
        'add_new_item'          => __( 'Add New Book'),
        'new_item'              => __( 'New Book'),
        'edit_item'             => __( 'Edit Book'),
        'view_item'             => __( 'View Book'),
        'all_items'             => __( 'All Books'),
        'search_items'          => __( 'Search Books'),
        'parent_item_colon'     => __( 'Parent Books:'),
        'not_found'             => __( 'No books found.'),
        'not_found_in_trash'    => __( 'No books found in Trash.'),
        'featured_image'        => __( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type.'),
        'set_featured_image'    => __( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type.'),
        'remove_featured_image' => __( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.'),
        'use_featured_image'    => __( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.'),
        'archives'              => __( 'Book archives', 'The post type archive label used in nav menus. Default “Post Archives”.'),
        'insert_into_item'      => __( 'Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).'),
        'uploaded_to_this_item' => __( 'Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).'),
        'filter_items_list'     => __( 'Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”.'),
        'items_list_navigation' => __( 'Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”.'),
        'items_list'            => __( 'Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”.'),
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


/**  
 * Metabox 
 */
function add_generation_books_metaboxes() {
    
    add_meta_box(
        'generation_books_metaboxes',
        'Book Info',
        'generation_books_metaboxes',
        'books',
        'side',
        'default'
    );
}

/**
 * Output the HTML for the metabox.
 */
function generation_books_metaboxes(){

    global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'books_fields' );

	// Get the location data if it's already been entered
    $price = get_post_meta( $post->ID, 'price', true );
    $author = get_post_meta( $post->ID, 'author', true );
    $genre = get_post_meta( $post->ID, 'genre', true );

    // Output the field
    ?>
    
    <label for="author">Författare</label>
    <input type="text" name="author" value="<?php echo $author ?>" class="widefat">

    <label for="genre">Genre</label>
    <select name="genre" id="genre" class="widefat">

        <option value="<?php echo $genre ?>"> <?php echo $genre ?> </option>

        <option value="barn" <?php selected( 'genre', 'barn' ); ?>>Barn</option>
        <option value="thriller" <?php selected( 'genre', 'thriller' ); ?>>Thriller</option>
        <option value="äventyr" <?php selected( 'genre', 'äventyr' ); ?>>Äventyr</option>

    </select>
    
    <label for="price">Pris</label> 
    <input type="number" name="price" value="<?php echo $price ?>" class="widefat">
    

    <?php 

}

/**
 * Save the metabox data
 */
function generation_books_save_events_meta( $post_id, $post ) {

	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
	}

	// Verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times.
    if ( ! isset( $_POST['author'] )  || ! isset( $_POST['price'] ) || ! isset( $_POST['genre'] )  || ! wp_verify_nonce( $_POST['books_fields'], basename(__FILE__) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $events_meta.
    $books_meta['author'] = esc_textarea( $_POST['author'] );
    $books_meta['price'] = $_POST['price'];
    $books_meta['genre'] = $_POST['genre'];

	// Cycle through the $books_meta array.
	foreach ( $books_meta as $key => $value ) :

		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}

		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}

	endforeach;

}
add_action( 'save_post', 'generation_books_save_events_meta', 1, 2 );














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


