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

/**  
 * Custom Post type 'Books' 
 */

function init_generation_books_post_type() {

    $labels = [
        'name'                  =>  'Books',
        'singular_name'         =>  'Book',
        'menu_name'             =>  'Books',
        'name_admin_bar'        =>  'Book',
        'add_new'               =>  'Add New',
        'add_new_item'          =>  'Add New Book',
        'new_item'              =>  'New Book',
        'edit_item'             =>  'Edit Book',
        'view_item'             =>  'View Book',
        'all_items'             =>  'All Books',
        'search_items'          =>  'Search Books',
        'parent_item_colon'     =>  'Parent Books:',
        'not_found'             =>  'No books found.',
        'not_found_in_trash'    =>  'No books found in Trash.',
        'featured_image'        =>  'Book Cover Image',
        'set_featured_image'    =>  'Set cover image',
        'remove_featured_image' =>  'Remove cover image',
        'use_featured_image'    =>  'Use as cover image',
        'archives'              =>  'Book archives',
        'insert_into_item'      =>  'Insert into book',
        'uploaded_to_this_item' =>  'Uploaded to this book',
        'filter_items_list'     =>  'Filter books list',
        'items_list_navigation' =>  'Books list navigation',
        'items_list'            =>  'Books list',
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
 * Metabox setup. 
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

    //Global post-variabel för att kunna hämta data kring nuvarande post.
    global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'books_fields' );

	// Get the location data if it's already been entered
    $price  = get_post_meta( $post->ID, 'price', true );
    $author = get_post_meta( $post->ID, 'author', true );
    $genre  = get_post_meta( $post->ID, 'genre', true );
    $pages  = get_post_meta( $post->ID, 'pages', true ); 

    // Output the field
    ?>
    
    <label for="author"> Författare </label>
    <input type="text" name="author" value="<?php echo esc_textarea( $author ); ?>" class="widefat">

    <label for="genre"> Genre </label>
    <select name="genre" id="genre" class="widefat">

        <option value="<?php echo $genre ?>"> <?php echo $genre ?> </option>

        <option value="barn" <?php selected( 'genre', 'barn' ); ?>> Barn </option>
        <option value="thriller" <?php selected( 'genre', 'thriller' ); ?>> Thriller </option>
        <option value="äventyr" <?php selected( 'genre', 'äventyr' ); ?>> Äventyr </option>

    </select>
    
    <label for="price"> Pris </label> 
    <input type="number" name="price" value="<?php echo intval( $price ); ?>" class="widefat">

    <label for="pages"> Pages </label> 
    <input type="number" name="pages" value="<?php echo intval( $pages ); ?>" class="widefat">
    

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
    if ( ! isset( $_POST[ 'author' ] ) || ! isset( $_POST[ 'genre' ] ) || ! isset( $_POST[ 'pages' ] ) || ! isset( $_POST[ 'price' ] ) || ! wp_verify_nonce( $_POST[ 'books_fields' ], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
    // This sanitizes the data from the field and saves it into an array $books_meta.
    $author = isset( $_POST[ 'author' ] ) ? sanitize_text_field( $_POST[ 'author' ] ) : '';
    update_post_meta( $post->ID, 'author', $author );

    // Lätt att ändra injection i frontenden för select -> options 
    // Så sanitize_text_field räcker egentligen inte, skulle behöva bygga en egen sanitizefunktion med sanitize_meta()
    $genre = isset( $_POST[ 'genre' ] ) ? sanitize_text_field( $_POST[ 'genre' ] ) : '';
    update_post_meta( $post->ID, 'genre', $genre );

    $price = isset( $_POST[ 'price' ] ) ? intval( $_POST[ 'price' ] ) : '';
    update_post_meta( $post->ID, 'price', $price );

    $pages = isset( $_POST[ 'pages' ] ) ? intval( $_POST[ 'pages' ] ) : '';
    update_post_meta( $post->ID, 'pages', $pages );


    $books_meta[ 'author' ] = $_POST[ 'author' ];
    $books_meta[ 'genre' ]  = $_POST[ 'genre' ];
    $books_meta[ 'price' ]  = $_POST[ 'price' ];
    $books_meta[ 'pages' ]  = $_POST[ 'pages' ];
    

	// Cycle through the $books_meta array.
	foreach ( $books_meta as $key => $value ) :

        // Don't store custom data twice
        // revision är då en posttyp som är historik av tidigare poster. Versionhantering typ. 
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


/** 
 * Shortcode
*/
function generation_book_shortcode( $attr ) {


    $shortcode_args = shortcode_atts ( [
        'posts_per_page'    => 1,
        'book_id'           => 0,
        'offset'            => 0,
    ], $attr );

    $args = [
        'post_type' => 'books',
        'post_status' => 'published',
        'posts_per_page' => intval( $shortcode_args[ 'posts_per_page' ] ),
        'book_id'        => intval( $shortcode_args[ 'book_id' ] ),
        'offset'         => intval( $shortcode_args[ 'offset' ] ),
    ];

    $query = new WP_Query( $args ); 

    ob_start();

    echo '<div class="grid-container">';
   

    if ( $query->have_posts () ) :
        while ( $query->have_posts() ) :
      
            $query->the_post();

           echo '<div class="grid-item"> <h4> <a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</h4>' . get_the_post_thumbnail( $post_id, 'medium' ) . '</a> </div>';

        endwhile;
     endif;

     wp_reset_postdata();

    echo '</div>';

    $content = ob_get_clean();
    return $content;
  
 }
 
 add_shortcode( 'generation_books', 'generation_book_shortcode' );


?>