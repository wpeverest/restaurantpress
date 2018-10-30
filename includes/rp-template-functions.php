<?php
/**
 * RestaurantPress Template
 *
 * Functions for the templating system.
 *
 * @package RestaurantPress/Functions
 * @version 1.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove adjacent_posts_rel_link_wp_head - pointless for food.
 *
 * @since 1.5.0
 */
function rp_prevent_adjacent_posts_rel_link_wp_head() {
	if ( is_singular( 'food_menu' ) ) {
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}
}
add_action( 'template_redirect', 'rp_prevent_adjacent_posts_rel_link_wp_head' );

/**
 * Show the gallery if JS is disabled.
 *
 * @since 1.4.0
 */
function rp_gallery_noscript() {
	?>
	<noscript><style>.restaurantpress-food-gallery{ opacity: 1 !important; }</style></noscript>
	<?php
}
add_action( 'wp_head', 'rp_gallery_noscript' );

/**
 * When the_post is called, put food data into a global.
 *
 * @param  mixed $post Post Object.
 * @return RP_Food
 */
function rp_setup_food_data( $post ) {
	unset( $GLOBALS['food'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'food_menu' ) ) ) {
		return;
	}

	$GLOBALS['food'] = rp_get_food( $post );

	return $GLOBALS['food'];
}
add_action( 'the_post', 'rp_setup_food_data' );

/**
 * Sets up the restaurantpress_loop global from the passed args or from the main query.
 *
 * @since 1.6.0
 * @param array $args Args to pass into the global.
 */
function rp_setup_loop( $args = array() ) {
	$default_args = array(
		'loop'         => 0,
		'columns'      => 1,
		'name'         => '',
		'is_shortcode' => false,
		'is_paginated' => true,
		'is_search'    => false,
		'total'        => 0,
		'total_pages'  => 0,
		'per_page'     => 0,
		'current_page' => 1,
	);

	// If this is a main RP query, use global args as defaults.
	if ( $GLOBALS['wp_query']->get( 'rp_query' ) ) {
		$default_args = array_merge( $default_args, array(
			'is_search'    => $GLOBALS['wp_query']->is_search(),
			'total'        => $GLOBALS['wp_query']->found_posts,
			'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
			'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
			'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
		) );
	}

	// Merge any existing values.
	if ( isset( $GLOBALS['restaurantpress_loop'] ) ) {
		$default_args = array_merge( $default_args, $GLOBALS['restaurantpress_loop'] );
	}

	$GLOBALS['restaurantpress_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'restaurantpress_before_menu_loop', 'rp_setup_loop' );

/**
 * Resets the restaurantpress_loop global.
 *
 * @since 1.6.0
 */
function rp_reset_loop() {
	unset( $GLOBALS['restaurantpress_loop'] );
}
add_action( 'restaurantpress_after_menu_loop', 'rp_reset_loop', 999 );

/**
 * Gets a property from the restaurantpress_loop global.
 *
 * @since  1.6.0
 * @param  string $prop Prop to get.
 * @param  string $default Default if the prop does not exist.
 * @return mixed
 */
function rp_get_loop_prop( $prop, $default = '' ) {
	rp_setup_loop(); // Ensure shop loop is setup.

	return isset( $GLOBALS['restaurantpress_loop'], $GLOBALS['restaurantpress_loop'][ $prop ] ) ? $GLOBALS['restaurantpress_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the restaurantpress_loop global.
 *
 * @since 1.6.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function rp_set_loop_prop( $prop, $value = '' ) {
	if ( ! isset( $GLOBALS['restaurantpress_loop'] ) ) {
		rp_setup_loop();
	}
	$GLOBALS['restaurantpress_loop'][ $prop ] = $value;
}

/**
 * Output generator tag to aid debugging.
 *
 * @param string $gen  Generator.
 * @param string $type Type.
 *
 * @return string
 */
function rp_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="RestaurantPress ' . esc_attr( RP_VERSION ) . '" />';
			break;
	}
	return $gen;
}

/**
 * Add body classes for RP pages.
 *
 * @param  array $classes Body Classes.
 * @return array
 */
function rp_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_restaurantpress() ) {

		$classes[] = 'restaurantpress';
		$classes[] = 'restaurantpress-page';

	} elseif ( is_group_menu_page() ) {

		$classes[] = 'restaurantpress';
		$classes[] = 'restaurantpress-group';

	}

	return array_unique( $classes );
}

/**
 * Get classname for loops based on $restaurantpress_loop global.
 *
 * @since 1.6.0
 * @return string
 */
function rp_get_loop_class() {
	$loop_index = rp_get_loop_prop( 'loop', 0 );
	$per_page   = absint( max( 1, rp_get_loop_prop( 'per_page', apply_filters( 'loop_menu_per_page', 10 ) ) ) );

	$loop_index ++;
	rp_set_loop_prop( 'loop', $loop_index );

	if ( 1 === $loop_index ) {
		return 'first';
	} elseif ( 0 === $loop_index % $per_page ) {
		return 'last';
	} else {
		return '';
	}
}

/**
 * Get the classes for the product cat div.
 *
 * @since 1.6.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param object       $category object Optional.
 *
 * @return array
 */
function rp_get_food_cat_class( $class = '', $category = null ) {
	$classes   = is_array( $class ) ? $class : array_map( 'trim', explode( ' ', $class ) );
	$classes[] = 'food-category';
	$classes[] = 'food';
	$classes[] = rp_get_loop_class();
	$classes   = apply_filters( 'food_cat_class', $classes, $class, $category );

	return array_unique( array_filter( $classes ) );
}

/**
 * Adds extra post classes for foods.
 *
 * @since 1.6.0
 * @param array        $classes Current classes.
 * @param string|array $class Additional class.
 * @param int          $post_id Post ID.
 * @return array
 */
function rp_food_post_class( $classes, $class = '', $post_id = '' ) {
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'food_menu' ) ) ) {
		return $classes;
	}

	$food = rp_get_food( $post_id );

	if ( $food ) {
		$classes[] = 'food';
		$classes[] = rp_get_loop_class();
	}

	$key = array_search( 'hentry', $classes );
	if ( false !== $key ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}

/**
 * Disable zoom in group page.
 *
 * @param bool $status Image zoom status.
 */
function rp_group_zoom_disable( $status ) {
	return is_post_type_archive( 'food_menu' ) || is_food_menu_taxonomy() || is_group_menu_page() ? false : $status;
}
add_filter( 'restaurantpress_single_food_zoom_enabled', 'rp_group_zoom_disable' );

/**
 * Get a slug identifying the current theme.
 *
 * @since  1.6.0
 * @return string
 */
function rp_get_theme_slug_for_templates() {
	return apply_filters( 'restaurantpress_theme_slug_for_templates', get_option( 'template' ) );
}

/**
 * Global
 */

if ( ! function_exists( 'restaurantpress_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function restaurantpress_output_content_wrapper() {
		rp_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'restaurantpress_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function restaurantpress_output_content_wrapper_end() {
		rp_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'restaurantpress_get_sidebar' ) ) {

	/**
	 * Get the food sidebar template.
	 */
	function restaurantpress_get_sidebar() {
		rp_get_template( 'global/sidebar.php' );
	}
}

/**
 * Loop
 */

if ( ! function_exists( 'restaurantpress_page_title' ) ) {

	/**
	 * Page Title function.
	 *
	 * @param  bool $echo Should echo title.
	 * @return string
	 */
	function restaurantpress_page_title( $echo = true ) {

		if ( is_search() ) {
			/* translators: %s: search query */
			$page_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'restaurantpress' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: %s: page number */
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'restaurantpress' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {
			$page_title = single_term_title( '', false );
		} else {
			$page_title = __( 'Foods', 'restaurantpress' );
		}

		$page_title = apply_filters( 'restaurantpress_page_title', $page_title );

		if ( $echo ) {
			echo $page_title; // WPCS: XSS ok.
		} else {
			return $page_title;
		}
	}
}

if ( ! function_exists( 'restaurantpress_food_loop_start' ) ) {

	/**
	 * Output the start of a food loop. By default this is a UL.
	 *
	 * @param  bool $echo Should echo?.
	 * @return string
	 */
	function restaurantpress_food_loop_start( $echo = true ) {
		ob_start();

		rp_set_loop_prop( 'loop', 0 );

		rp_get_template( 'loop/loop-start.php' );

		$loop_start = apply_filters( 'restaurantpress_food_loop_start', ob_get_clean() );

		if ( $echo ) {
			echo $loop_start; // WPCS: XSS ok.
		} else {
			return $loop_start;
		}
	}
}
if ( ! function_exists( 'restaurantpress_food_loop_end' ) ) {

	/**
	 * Output the end of a food loop. By default this is a UL.
	 *
	 * @param  bool $echo Should echo?.
	 * @return string
	 */
	function restaurantpress_food_loop_end( $echo = true ) {
		ob_start();

		rp_get_template( 'loop/loop-end.php' );

		$loop_end = apply_filters( 'restaurantpress_food_loop_end', ob_get_clean() );

		if ( $echo ) {
			echo $loop_end; // WPCS: XSS ok.
		} else {
			return $loop_end;
		}
	}
}

if ( ! function_exists( 'restaurantpress_template_loop_food_title' ) ) {

	/**
	 * Show the food title in the food loop. By default this is an H2.
	 */
	function restaurantpress_template_loop_food_title() {
		echo '<h4 class="restaurantpress-loop-food__title"><a href="' . esc_url( get_the_permalink() ) . '" class="restaurantpress-LoopFood-link restaurantpress-loop-food__link">' . get_the_title() . '</a></h4>';
	}
}

if ( ! function_exists( 'restaurantpress_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives.
	 */
	function restaurantpress_taxonomy_archive_description() {
		if ( is_food_menu_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
			$term = get_queried_object();

			if ( $term && ! empty( $term->description ) ) {
				echo '<div class="term-description">' . rp_format_content( $term->description ) . '</div>'; // WPCS: XSS ok.
			}
		}
	}
}

if ( ! function_exists( 'restaurantpress_template_loop_food_thumbnail' ) ) {

	/**
	 * Get the food thumbnail for the loop.
	 */
	function restaurantpress_template_loop_food_thumbnail() {
		rp_get_template( 'loop/food-image.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_loop_price' ) ) {

	/**
	 * Get the food price for the loop.
	 */
	function restaurantpress_template_loop_price() {
		rp_get_template( 'loop/price.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_loop_excerpt' ) ) {

	/**
	 * Output the food short description (excerpt) for the loop.
	 */
	function restaurantpress_template_loop_excerpt() {
		rp_get_template( 'loop/short-description.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_loop_chef_badge' ) ) {

	/**
	 * Get the chef badge for the loop.
	 *
	 * @subpackage Loop
	 */
	function restaurantpress_show_food_loop_chef_badge() {
		rp_get_template( 'loop/chef-badge.php' );
	}
}

if ( ! function_exists( 'restaurantpress_get_food_thumbnail' ) ) {

	/**
	 * Get the food thumbnail, or the placeholder if not set.
	 *
	 * @subpackage Loop
	 * @param  string $size (default: 'food_thumbnail').
	 * @param  array  $attr Attributes array.
	 * @return string
	 */
	function restaurantpress_get_food_thumbnail( $size = 'food_thumbnail', $attr = array() ) {
		global $food;

		$image_size = apply_filters( 'single_food_archive_thumbnail_size', $size );

		return $food ? $food->get_image( $image_size, $attr ) : '';
	}
}

if ( ! function_exists( 'restaurantpress_pagination' ) ) {

	/**
	 * Output the pagination.
	 */
	function restaurantpress_pagination() {
		if ( ! rp_get_loop_prop( 'is_paginated' ) ) {
			return;
		}

		$args = array(
			'total'   => rp_get_loop_prop( 'total_pages' ),
			'current' => rp_get_loop_prop( 'current_page' ),
		);

		if ( rp_get_loop_prop( 'is_shortcode' ) ) {
			$args['base']   = esc_url_raw( add_query_arg( 'food-page', '%#%', false ) );
			$args['format'] = '?food-page = %#%';
		} else {
			$args['base']   = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
			$args['format'] = '';
		}

		rp_get_template( 'loop/pagination.php', $args );
	}
}

/**
 * Single Food
 */

if ( ! function_exists( 'restaurantpress_show_food_chef_badge' ) ) {

	/**
	 * Output the food chef badge.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_chef_badge() {
		rp_get_template( 'single-food/chef-badge.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_images' ) ) {

	/**
	 * Output the food image before the single food summary.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_images() {
		rp_get_template( 'single-food/food-image.php' );
	}
}
if ( ! function_exists( 'restaurantpress_show_food_thumbnails' ) ) {

	/**
	 * Output the food thumbnails.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_show_food_thumbnails() {
		rp_get_template( 'single-food/food-thumbnails.php' );
	}
}

if ( ! function_exists( 'restaurantpress_photoswipe' ) ) {

	/**
	 * Get the photoswipe template.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_photoswipe() {
		if ( 'yes' === get_option( 'restaurantpress_enable_gallery_lightbox' ) ) {
			rp_get_template( 'single-food/photoswipe.php' );
		}
	}
}
if ( ! function_exists( 'restaurantpress_template_single_title' ) ) {

	/**
	 * Output the food title.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_title() {
		rp_get_template( 'single-food/title.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_price' ) ) {

	/**
	 * Output the food price.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_price() {
		rp_get_template( 'single-food/price.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_excerpt' ) ) {

	/**
	 * Output the food short description (excerpt).
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_excerpt() {
		rp_get_template( 'single-food/short-description.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_contact' ) ) {

	/**
	 * Output the food contact.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_contact() {
		rp_get_template( 'single-food/contact.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_meta' ) ) {

	/**
	 * Output the food meta.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_meta() {
		rp_get_template( 'single-food/meta.php' );
	}
}
if ( ! function_exists( 'restaurantpress_template_single_sharing' ) ) {

	/**
	 * Output the food sharing.
	 *
	 * @subpackage Food
	 */
	function restaurantpress_template_single_sharing() {
		rp_get_template( 'single-food/share.php' );
	}
}
if ( ! function_exists( 'restaurantpress_output_food_data_tabs' ) ) {

	/**
	 * Output the food tabs.
	 *
	 * @subpackage Food/Tabs
	 */
	function restaurantpress_output_food_data_tabs() {
		rp_get_template( 'single-food/tabs/tabs.php' );
	}
}
if ( ! function_exists( 'restaurantpress_food_description_tab' ) ) {

	/**
	 * Output the description tab content.
	 *
	 * @subpackage Food/Tabs
	 */
	function restaurantpress_food_description_tab() {
		rp_get_template( 'single-food/tabs/description.php' );
	}
}

if ( ! function_exists( 'restaurantpress_default_food_tabs' ) ) {

	/**
	 * Add default food tabs to food pages.
	 *
	 * @param  array $tabs Array of tabs.
	 * @return array
	 */
	function restaurantpress_default_food_tabs( $tabs = array() ) {
		global $post;

		// Description tab - shows food content.
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'restaurantpress' ),
				'priority' => 10,
				'callback' => 'restaurantpress_food_description_tab',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'restaurantpress_sort_food_tabs' ) ) {

	/**
	 * Sort tabs by priority.
	 *
	 * @param  array $tabs Array of tabs.
	 * @return array
	 */
	function restaurantpress_sort_food_tabs( $tabs = array() ) {

		// Make sure the $tabs parameter is an array.
		if ( ! is_array( $tabs ) ) {
			trigger_error( 'Function restaurantpress_sort_food_tabs() expects an array as the first parameter. Defaulting to empty array.' );
			$tabs = array();
		}

		// Re-order tabs by priority.
		if ( ! function_exists( '_sort_priority_callback' ) ) {

			/**
			 * Sort Priority Callback Function
			 *
			 * @param array $a Comparison A.
			 * @param array $b Comparison B.
			 * @return bool
			 */
			function _sort_priority_callback( $a, $b ) {
				if ( ! isset( $a['priority'], $b['priority'] ) || $a['priority'] === $b['priority'] ) {
					return 0;
				}
				return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
			}
		}

		uasort( $tabs, '_sort_priority_callback' );

		return $tabs;
	}
}

/* Forms */

if ( ! function_exists( 'restaurantpress_form_field' ) ) {

	/**
	 * Outputs a reservaion/details form field.
	 *
	 * @subpackage Forms
	 *
	 * @param string $key   Key.
	 * @param mixed  $args  Arguments.
	 * @param string $value (default: null).
	 *
	 * @return string
	 */
	function restaurantpress_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'restaurantpress_form_field', $args, $key, $value );

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'restaurantpress' ) . '">*</abbr>';
		} else {
			$required = '';
		}

		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}

		if ( is_null( $value ) ) {
			$value = $args['default'];
		}

		// Custom attribute handling.
		$custom_attributes         = array();
		$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'] );

		if ( $args['maxlength'] ) {
			$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
		}

		if ( ! empty( $args['autocomplete'] ) ) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if ( true === $args['autofocus'] ) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach ( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$field           = '';
		$label_id        = $args['id'];
		$sort            = $args['priority'] ? $args['priority'] : '';
		$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

		switch ( $args['type'] ) {
			case 'textarea':
				$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'checkbox':
				$field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '><input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';
				break;
			case 'password':
			case 'text':
			case 'email':
			case 'tel':
			case 'number':
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
				break;
			case 'select':
				$options = '';

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						if ( '' === $option_key ) {
							// If we have a blank option, select2 needs a placeholder.
							if ( empty( $args['placeholder'] ) ) {
								$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'restaurantpress' );
							}
							$custom_attributes[] = 'data-allow_clear="true"';
						}
						$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
					}

					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">' . $options . '</select>';
				}
				break;
			case 'radio':
				$label_id = current( array_keys( $args['options'] ) );

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
						$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label>';
					}
				}
				break;
		}

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args['label'] && 'checkbox' != $args['type'] ) {
				$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
			}

			$field_html .= $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
			}

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id    = esc_attr( $args['id'] ) . '_field';
			$field           = sprintf( $field_container, $container_class, $container_id, $field_html );
		}

		$field = apply_filters( 'restaurantpress_form_field_' . $args['type'], $field, $key, $args, $value );

		if ( $args['return'] ) {
			return $field;
		} else {
			echo $field; // WPCS: XSS ok.
		}
	}
}

if ( ! function_exists( 'rp_no_foods_found' ) ) {

	/**
	 * Show no foods found message.
	 */
	function rp_no_foods_found() {
		rp_get_template( 'loop/no-foods-found.php' );
	}
}
