<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class    RP_Post_Types
 * @version  1.0.0
 * @package  RestaurantPress/Classes/Menu Items
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Post_Types Class.
 */
class RP_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
		add_action( 'restaurantpress_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( ! is_blog_installed() || taxonomy_exists( 'food_menu_cat' ) ) {
			return;
		}

		do_action( 'restaurantpress_register_taxonomy' );

		register_taxonomy( 'food_menu_cat',
			apply_filters( 'restaurantpress_taxonomy_objects_food_menu_cat', array( 'food_menu' ) ),
			apply_filters( 'restaurantpress_taxonomy_args_food_menu_cat', array(
				'hierarchical' => true,
				'label'        => __( 'Categories', 'restaurantpress' ),
				'labels'       => array(
						'name'              => __( 'Food categories', 'restaurantpress' ),
						'singular_name'     => __( 'Category', 'restaurantpress' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'restaurantpress' ),
						'search_items'      => __( 'Search categories', 'restaurantpress' ),
						'all_items'         => __( 'All categories', 'restaurantpress' ),
						'parent_item'       => __( 'Parent category', 'restaurantpress' ),
						'parent_item_colon' => __( 'Parent category:', 'restaurantpress' ),
						'edit_item'         => __( 'Edit category', 'restaurantpress' ),
						'update_item'       => __( 'Update category', 'restaurantpress' ),
						'add_new_item'      => __( 'Add new category', 'restaurantpress' ),
						'new_item_name'     => __( 'New category name', 'restaurantpress' ),
						'not_found'         => __( 'No categories found', 'restaurantpress' ),
					),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_food_menu_terms',
					'edit_terms'   => 'edit_food_menu_terms',
					'delete_terms' => 'delete_food_menu_terms',
					'assign_terms' => 'assign_food_menu_terms',
				),
				'rewrite'      => array(
					'slug'         => _x( 'food-category', 'slug', 'restaurantpress' ),
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'food_menu_tag',
			apply_filters( 'restaurantpress_taxonomy_objects_food_menu_tag', array( 'food_menu' ) ),
			apply_filters( 'restaurantpress_taxonomy_args_food_menu_tag', array(
				'hierarchical' => false,
				'label'        => __( 'Tags', 'restaurantpress' ),
				'labels'       => array(
						'name'                       => __( 'Food tags', 'restaurantpress' ),
						'singular_name'              => __( 'Tag', 'restaurantpress' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'restaurantpress' ),
						'search_items'               => __( 'Search tags', 'restaurantpress' ),
						'all_items'                  => __( 'All tags', 'restaurantpress' ),
						'edit_item'                  => __( 'Edit tag', 'restaurantpress' ),
						'update_item'                => __( 'Update tag', 'restaurantpress' ),
						'add_new_item'               => __( 'Add new tag', 'restaurantpress' ),
						'new_item_name'              => __( 'New tag name', 'restaurantpress' ),
						'popular_items'              => __( 'Popular tags', 'restaurantpress' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'restaurantpress' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'restaurantpress' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'restaurantpress' ),
						'not_found'                  => __( 'No tags found', 'restaurantpress' ),
					),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_food_menu_terms',
					'edit_terms'   => 'edit_food_menu_terms',
					'delete_terms' => 'delete_food_menu_terms',
					'assign_terms' => 'assign_food_menu_terms',
				),
				'rewrite'               => array(
					'slug'       => _x( 'food-tag', 'slug', 'restaurantpress' ),
					'with_front' => false,
				),
			) )
		);

		do_action( 'restaurantpress_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'food_menu' ) ) {
			return;
		}

		do_action( 'restaurantpress_register_post_type' );

		$supports = array( 'title', 'editor', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', 'publicize', 'wpcom-markdown' );

		if ( 'yes' === get_option( 'restaurantpress_enable_reviews', 'yes' ) ) {
			$supports[] = 'comments';
		}

		register_post_type( 'food_menu',
			apply_filters( 'restaurantpress_register_post_type_food_menu',
				array(
					'labels'              => array(
							'name'                  => __( 'Menu Items', 'restaurantpress' ),
							'singular_name'         => __( 'Menu Item', 'restaurantpress' ),
							'menu_name'             => _x( 'Menu Items', 'Admin menu name', 'restaurantpress' ),
							'all_items'             => __( 'All Menu Items', 'restaurantpress' ),
							'add_new'               => __( 'Add Menu Item', 'restaurantpress' ),
							'add_new_item'          => __( 'Add New Menu Item', 'restaurantpress' ),
							'edit'                  => __( 'Edit', 'restaurantpress' ),
							'edit_item'             => __( 'Edit Menu Item', 'restaurantpress' ),
							'new_item'              => __( 'New Menu Item', 'restaurantpress' ),
							'view'                  => __( 'View Menu Item', 'restaurantpress' ),
							'view_item'             => __( 'View Menu Item', 'restaurantpress' ),
							'search_items'          => __( 'Search Menu Items', 'restaurantpress' ),
							'not_found'             => __( 'No Menu Items found', 'restaurantpress' ),
							'not_found_in_trash'    => __( 'No Menu Items found in trash', 'restaurantpress' ),
							'parent'                => __( 'Parent Menu Item', 'restaurantpress' ),
							'featured_image'        => __( 'Food image', 'restaurantpress' ),
							'set_featured_image'    => __( 'Set menu image', 'restaurantpress' ),
							'remove_featured_image' => __( 'Remove menu image', 'restaurantpress' ),
							'use_featured_image'    => __( 'Use as menu image', 'restaurantpress' ),
							'insert_into_item'      => __( 'Insert into menu item', 'restaurantpress' ),
							'uploaded_to_this_item' => __( 'Uploaded to this menu item', 'restaurantpress' ),
							'filter_items_list'     => __( 'Filter menu items', 'restaurantpress' ),
							'items_list_navigation' => __( 'Menu items navigation', 'restaurantpress' ),
							'items_list'            => __( 'Menu items list', 'restaurantpress' ),
						),
					'description'         => __( 'This is where you can add new foods to your restaurant.', 'restaurantpress' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'food_menu',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'query_var'           => true,
					'rewrite'             => array( 'slug' => 'food', 'with_front' => false, 'feeds' => true ),
					'supports'            => $supports,
					'has_archive'         => true,
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
				)
			)
		);

		register_post_type( 'food_group',
			apply_filters( 'restaurantpress_register_post_type_food_group',
				array(
					'labels'              => array(
							'name'                  => __( 'Groups', 'restaurantpress' ),
							'singular_name'         => __( 'Group', 'restaurantpress' ),
							'menu_name'             => _x( 'Groups', 'Admin menu name', 'restaurantpress' ),
							'add_new'               => __( 'Add Group', 'restaurantpress' ),
							'add_new_item'          => __( 'Add New Group', 'restaurantpress' ),
							'edit'                  => __( 'Edit', 'restaurantpress' ),
							'edit_item'             => __( 'Edit Group', 'restaurantpress' ),
							'new_item'              => __( 'New Group', 'restaurantpress' ),
							'view'                  => __( 'View Groups', 'restaurantpress' ),
							'view_item'             => __( 'View Group', 'restaurantpress' ),
							'search_items'          => __( 'Search Groups', 'restaurantpress' ),
							'not_found'             => __( 'No Groups found', 'restaurantpress' ),
							'not_found_in_trash'    => __( 'No Groups found in trash', 'restaurantpress' ),
							'parent'                => __( 'Parent Group', 'restaurantpress' ),
							'filter_items_list'     => __( 'Filter Groups', 'restaurantpress' ),
							'items_list_navigation' => __( 'Groups navigation', 'restaurantpress' ),
							'items_list'            => __( 'Groups list', 'restaurantpress' ),
						),
					'description'         => __( 'This is where you can add new group for your food menu.', 'restaurantpress' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'food_group',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_restaurantpress' ) ? 'restaurantpress' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
				)
			)
		);
	}

	/**
	 * Add Menu Items Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'food_menu' );
		}
	}

	/**
	 * Added Menu Items for Jetpack related posts.
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'food_menu';

		return $post_types;
	}

	/**
	 * Flush rewrite rules.
	 */
	public static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}
}

RP_Post_Types::init();
