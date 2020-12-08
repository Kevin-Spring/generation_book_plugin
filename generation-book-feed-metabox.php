<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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

	// Get the book data if it's already been entered
    $price  = get_post_meta( $post->ID, 'price', true );
    $author = get_post_meta( $post->ID, 'author', true );
    $genre  = get_post_meta( $post->ID, 'genre', true );
    $pages  = get_post_meta( $post->ID, 'pages', true ); 

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
    // Kanske spara $_POST värderna till variabler & lägga in i en array istället för att kolla A L L A?
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

?>