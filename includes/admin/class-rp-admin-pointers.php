<?php
/**
 * Adds and controls pointers for contextual help/tutorials
 *
 * @class    RP_Admin_Pointers
 * @version  1.4.0
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Admin_Pointers Class.
 */
class RP_Admin_Pointers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_pointers_for_screen' ) );
	}

	/**
	 * Setup pointers for screen.
	 */
	public function setup_pointers_for_screen() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'food_menu' :
				$this->create_food_menu_tutorial();
			break;
		}
	}

	/**
	 * Pointers for creating a food menu.
	 */
	public function create_food_menu_tutorial() {
		if ( ! isset( $_GET['tutorial'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// These pointers will chain - they will not be shown at once.
		$pointers = array(
			'pointers' => array(
				'title' => array(
					'target'       => "#title",
					'next'         => 'content',
					'next_trigger' => array(
						'target' => '#title',
						'event'  => 'input',
					),
					'options'      => array(
						'content'  => '<h3>' . esc_html__( 'Menu item name', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( 'Give your new menu item a name here. This is a required field and will be what your users will see in your restaurant.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'top',
							'align' => 'left',
						),
					),
				),
				'content' => array(
					'target'       => "#wp-content-editor-container",
					'next'         => 'postexcerpt',
					'next_trigger' => array(),
					'options'      => array(
						'content'  => '<h3>' . esc_html__( 'Menu item description', 'woocommerce' ) . '</h3>' .
										'<p>' . esc_html__( 'This is your menu item main body of content. Here you should describe your menu item in detail.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'bottom',
							'align' => 'middle',
						),
					),
				),
				'postexcerpt' => array(
					'target'       => "#postexcerpt",
					'next'         => 'postimagediv',
					'next_trigger' => array(
						'target' => "#postexcerpt",
						'event'  => 'input',
					),
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Menu item short description', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( 'Add a quick summary for your menu item here. This will appear on the menu item page under the menu item name.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'bottom',
							'align' => 'middle',
						),
					),
				),
				'postimagediv' => array(
					'target'       => "#postimagediv",
					'next'         => 'food_menu_price',
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Menu item images', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( "Upload or assign an image to your menu item here. This image will be shown in your restaurant's catalog.", 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'right',
							'align' => 'middle',
						),
					),
				),
				'food_menu_price' => array(
					'target'       => "#restaurantpress-menu-data",
					'next'         => 'food_menu_catdiv',
					'next_trigger' => array(
						'target' => "#restaurantpress-menu-data",
						'event'  => 'input',
					),
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Menu item price', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( 'Next you need to give your menu item a price.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'right',
							'align' => 'middle',
						),
					),
				),
				'food_menu_catdiv' => array(
					'target'       => "#food_menu_catdiv",
					'next'         => 'submitdiv',
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Menu item categories', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( 'Optionally assign categories to your menu item to make them easier to browse through and find in your restaurant.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'right',
							'align' => 'middle',
						),
					),
				),
				'submitdiv' => array(
					'target'       => "#submitdiv",
					'next'         => '',
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Publish your menu item!', 'restaurantpress' ) . '</h3>' .
										'<p>' . esc_html__( 'When you are finished editing your menu item, hit the "Publish" button to publish your menu item to your restaurant.', 'restaurantpress' ) . '</p>',
						'position' => array(
							'edge'  => 'right',
							'align' => 'middle',
						),
					),
				),
			),
		);

		$this->enqueue_pointers( $pointers );
	}

	/**
	 * Enqueue pointers and add script to page.
	 * @param array $pointers
	 */
	public function enqueue_pointers( $pointers ) {
		$pointers = wp_json_encode( $pointers );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		rp_enqueue_js( "
			jQuery( function( $ ) {
				var rp_pointers = {$pointers};

				setTimeout( init_rp_pointers, 800 );

				function init_rp_pointers() {
					$.each( rp_pointers.pointers, function( i ) {
						show_rp_pointer( i );
						return false;
					});
				}

				function show_rp_pointer( id ) {
					var pointer = rp_pointers.pointers[ id ];
					var options = $.extend( pointer.options, {
						pointerClass: 'wp-pointer rp-pointer',
						close: function() {
							if ( pointer.next ) {
								show_rp_pointer( pointer.next );
							}
						},
						buttons: function( event, t ) {
							var close   = '" . esc_js( __( 'Dismiss', 'restaurantpress' ) ) . "',
								next    = '" . esc_js( __( 'Next', 'restaurantpress' ) ) . "',
								button  = $( '<a class=\"close\" href=\"#\">' + close + '</a>' ),
								button2 = $( '<a class=\"button button-primary\" href=\"#\">' + next + '</a>' ),
								wrapper = $( '<div class=\"rp-pointer-buttons\" />' );

							button.bind( 'click.pointer', function(e) {
								e.preventDefault();
								t.element.pointer('destroy');
							});

							button2.bind( 'click.pointer', function(e) {
								e.preventDefault();
								t.element.pointer('close');
							});

							wrapper.append( button );
							wrapper.append( button2 );

							return wrapper;
						},
					} );
					var this_pointer = $( pointer.target ).pointer( options );
					this_pointer.pointer( 'open' );

					if ( pointer.next_trigger ) {
						$( pointer.next_trigger.target ).on( pointer.next_trigger.event, function() {
							setTimeout( function() { this_pointer.pointer( 'close' ); }, 400 );
						});
					}
				}
			});
		" );
	}
}

new RP_Admin_Pointers();
