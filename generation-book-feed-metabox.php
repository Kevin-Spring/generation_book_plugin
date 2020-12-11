<?php if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**  
 * Metabox setup. 
 */
function add_generation_books_metaboxes() {
    add_meta_box(
        'generation_books_metaboxes',
        'Book Info',
        'generation_books_metaboxes',
        'gen_book',
        'side',
        'default'
    );
}

/**
 * Output the HTML for the metabox.
 */
function generation_books_metaboxes(){

    // Global post-variabel för att kunna hämta data kring nuvarande post.
    global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'handle_book_meta-fields' );

	// Get the book data if it's already been entered
    $author = get_post_meta( $post->ID, '_gen_book_author', true );
    $genre  = get_post_meta( $post->ID, '_gen_book_genre', true );
    $price  = get_post_meta( $post->ID, '_gen_book_price', true );
    $pages  = get_post_meta( $post->ID, '_gen_book_pages', true ); 

    ?>
    <label for="author">Författare</label>
    <input type="text" name="_gen_book_author" value="<?php echo esc_textarea( $author ); ?>" class="widefat">
    <label for="genre">Genre</label>
    <select name="_gen_book_genre" id="genre" class="widefat">
        <option value="<?php echo esc_attr( $genre ); ?>"> <?php echo esc_html( $genre ); ?> </option>
        <option value="barn" <?php selected( 'genre', 'barn' ); ?>> Barn </option>
        <option value="thriller" <?php selected( 'genre', 'thriller' ); ?>> Thriller </option>
        <option value="äventyr" <?php selected( 'genre', 'äventyr' ); ?>> Äventyr </option>
    </select>
    <label for="price">Pris</label> 
    <input type="number" name="_gen_book_price" value="<?php echo esc_html( $price ); ?>" class="widefat">
    <label for="pages">Pages</label> 
    <input type="number" name="_gen_book_pages" value="<?php echo esc_html( $pages ); ?>" class="widefat">
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
    if (! isset( $_POST[ '_gen_book_author' ] ) || ! isset( $_POST[ '_gen_book_genre' ] ) || 
        ! isset( $_POST[ '_gen_book_pages' ] ) || ! isset( $_POST[ '_gen_book_price' ] ) || 
        ! wp_verify_nonce( $_POST[ 'handle_book_meta-fields' ], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
    // This sanitizes the data from the field and saves it into an array $books_meta.
    $author = isset( $_POST[ '_gen_book_author' ] ) ? sanitize_text_field( $_POST[ '_gen_book_author' ] ) : '';
    update_post_meta( $post->ID, '_gen_book_author', $author );

    // Lätt att ändra injection i frontenden för select -> options 
    $genre = isset( $_POST[ '_gen_book_genre' ] ) ? sanitize_text_field( $_POST[ '_gen_book_genre' ] ) : '';
    update_post_meta( $post->ID, '_gen_book_genre', $genre );

    // Om priset ska kunnas visas i decimaler kan du kolla upp floatval() & sprintf
    $price = isset( $_POST[ '_gen_book_price' ] ) ? intval( $_POST[ '_gen_book_price' ] ) : '';
    update_post_meta( $post->ID, '_gen_book_price', $price );

    $pages = isset( $_POST[ '_gen_book_pages' ] ) ? intval( $_POST[ '_gen_book_pages' ] ) : '';
    update_post_meta( $post->ID, '_gen_book_pages', $pages );

}
add_action( 'save_post', 'generation_books_save_events_meta', 1, 2 );