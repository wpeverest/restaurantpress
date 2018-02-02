<?php
/**
 * Template Loader
 *
 * @class    RP_Template_Loader
 * @package  RestaurantPress\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * RP_Template_Loader Class.
 */
class RP_Template_Loader {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. restaurantpress looks for theme.
	 * overrides in /theme/restaurantpress/ by default.
	 *
	 * For beginners, it also looks for a restaurantpress.php template first. If the user adds.
	 * this to the theme (containing a restaurantpress() inside) this will be used for all.
	 * restaurantpress templates.
	 *
	 * @param  string $template Template to load.
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		$default_file = self::get_template_loader_default_file();

		if ( $default_file ) {
			/**
			 * Filter hook to choose which files to find before RestaurantPress does it's own logic.
			 *
			 * @since 1.4.0
			 * @var   array
			 */
			$search_files = self::get_template_loader_files( $default_file );
			$template     = locate_template( $search_files );

			if ( ! $template || RP_TEMPLATE_DEBUG_MODE ) {
				$template = RP()->plugin_path() . '/templates/' . $default_file;
			}
		}

		return $template;
	}

	/**
	 * Get the default filename for a template.
	 *
	 * @since  1.4.0
	 * @return string
	 */
	private static function get_template_loader_default_file() {
		if ( is_singular( 'food_menu' ) ) {
			$default_file = 'single-food_menu.php';
		} elseif ( is_food_menu_taxonomy() ) {
			$term = get_queried_object();

			if ( is_tax( 'food_menu_cat' ) || is_tax( 'food_menu_tag' ) ) {
				$default_file = 'taxonomy-' . $term->taxonomy . '.php';
			} else {
				$default_file = 'archive-food.php';
			}
		} elseif ( is_post_type_archive( 'food_menu' ) ) {
			$default_file = 'archive-food.php';
		} else {
			$default_file = '';
		}
		return $default_file;
	}

	/**
	 * Get an array of filenames to search for a given template.
	 *
	 * @since  1.4.0
	 * @param  string $default_file The default file name.
	 * @return string[]
	 */
	private static function get_template_loader_files( $default_file ) {
		$templates   = apply_filters( 'restaurantpress_template_loader_files', array(), $default_file );
		$templates[] = 'restaurantpress.php';

		if ( is_page_template() ) {
			$templates[] = get_page_template_slug();
		}

		if ( is_singular( 'food_menu' ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name );
			if ( $name_decoded !== $object->post_name ) {
				$templates[] = "single-food-{$name_decoded}.php";
			}
			$templates[] = "single-product-{$object->post_name}.php";
		}

		if ( is_food_menu_taxonomy() ) {
			$object      = get_queried_object();
			$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = RP()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = 'taxonomy-' . $object->taxonomy . '.php';
			$templates[] = RP()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
		}

		$templates[] = $default_file;
		$templates[] = RP()->template_path() . $default_file;

		return array_unique( $templates );
	}
}

RP_Template_Loader::init();
