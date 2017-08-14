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
	 * @param mixed $template
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		return $template;
	}
}

RP_Template_Loader::init();
