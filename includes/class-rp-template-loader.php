<?php
/**
 * Template Loader
 *
 * @class    RP_Template_Loader
 * @version  1.4.0
 * @package  RestaurantPress/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 * @param  string $template The path of the template to include.
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		if ( $default_file = self::get_template_loader_default_file() ) {
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
		$search_files   = apply_filters( 'restaurantpress_template_loader_files', array(), $default_file );
		$search_files[] = 'restaurantpress.php';

		if ( is_page_template() ) {
			$search_files[] = get_page_template_slug();
		}

		if ( is_food_menu_taxonomy() ) {
			$term   = get_queried_object();
			$search_files[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$search_files[] = RP()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$search_files[] = 'taxonomy-' . $term->taxonomy . '.php';
			$search_files[] = RP()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
		}

		$search_files[] = $default_file;
		$search_files[] = RP()->template_path() . $default_file;

		return array_unique( $search_files );
	}
}

RP_Template_Loader::init();
