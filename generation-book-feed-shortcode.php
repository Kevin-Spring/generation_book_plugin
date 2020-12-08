<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** 
 * Shortcode
*/
function generation_book_shortcode( $attr ) {


    $shortcode_args = shortcode_atts ( [
        'posts_per_page'    => 3,
        'book_title'        => '',
        'offset'            => 0,
    ], $attr );

    $args = [
        'post_type'         => 'books',
        'post_status'       => 'published',
        'posts_per_page'    => intval( $shortcode_args[ 'posts_per_page' ] ),
        'title'             => $shortcode_args[ 'book_title' ],
        'offset'            => intval( $shortcode_args[ 'offset' ] ),
    ];

    $query = new WP_Query( $args ); 

    ob_start();

    echo '<div class="grid-container">';
   

    if ( $query->have_posts () ) :
        while ( $query->have_posts() ) :
      
            $query->the_post();

            echo '<div class="grid-item">';
            echo '<h4> <a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</h4>' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a>'; 
            echo '<p> Författare: <b>' . esc_html( get_post_meta( get_the_ID(), 'author' )[0] ) . '</b></p>';
            echo '<p> Genre: <b>' . esc_html( get_post_meta( get_the_ID(), 'genre' )[0] ) . '</b></p>';
            echo '<p> Sidor: <b>' . esc_html( get_post_meta( get_the_ID(), 'pages' )[0] ) . '</b></p>';
            echo '<p> Pris: <b>' . esc_html( get_post_meta( get_the_ID(), 'price' )[0] ) . '</b></p>';
            echo '</div>';

        endwhile;
    endif;

    wp_reset_postdata();

    echo '</div>';

    $content = ob_get_clean();
    return $content;
  
 }
 
 add_shortcode( 'generation_books', 'generation_book_shortcode' );


?>