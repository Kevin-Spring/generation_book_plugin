<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**  
 * Custom Post type 'books' 
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

?>