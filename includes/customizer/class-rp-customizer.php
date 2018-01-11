<?php
/**
 * Adds options to the customizer for RestaurantPress.
 *
 * @version 1.7.0
 * @package RestaurantPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Customizer class.
 */
class RP_Customizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'add_sections' ) );
		add_action( 'customize_save_after', array( $this, 'save_after' ) );
		add_action( 'wp_head', array( $this, 'header_output' ), 99999 );
		add_action( 'customize_controls_print_styles', array( $this, 'add_styles' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30 );
	}

	/**
	 * Add settings to the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_sections( $wp_customize ) {
		$wp_customize->add_panel( 'restaurantpress', array(
			'priority'       => 200,
			'capability'     => 'manage_restaurantpress',
			'theme_supports' => '',
			'title'          => __( 'RestaurantPress', 'restaurantpress' ),
		) );

		$this->add_colors_section( $wp_customize );
		$this->add_food_group_section( $wp_customize );
		$this->add_food_images_section( $wp_customize );
	}

	/**
	 * Compile the SCSS.
	 *
	 * @return string
	 */
	protected function compile_scss() {
		if ( ! class_exists( 'scssc' ) && ! class_exists( 'scss_formatter_nested' ) ) {
			include_once RP()->plugin_path() . '/includes/libraries/class-scss.php';
		}

		// Get options.
		$colors = get_option( 'restaurantpress_colors', array(
			'primary' => '#ff0033',
		) );

		ob_start();
		include 'views/scss.php';
		$scss = ob_get_clean();

		$compiler     = new scssc();
		$compiler->setFormatter( 'scss_formatter_compressed' );
		$compiled_css = $compiler->compile( trim( $scss ) );

		return $compiled_css;
	}

	/**
	 * Save the colors.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function save_after( $customize ) {
		if ( ! isset( $_REQUEST['customized'] ) ) {
			return;
		}

		$customized = json_decode( stripslashes( $_REQUEST['customized'] ), true );
		$save       = false;

		foreach ( $customized as $key => $value ) {
			if ( false !== strpos( $key, 'restaurantpress_colors' ) ) {
				$save = true;
				break;
			}
		}

		if ( $save ) {
			$css = $this->compile_scss();

			update_option( 'restaurantpress_colors_css', $css );
		}
	}

	/**
	 * Header output.
	 */
	public function header_output() {
		$css = get_option( 'restaurantpress_colors_css' );

		echo "<!-- RestaurantPress Colors -->\n";
		echo "<style type=\"text/css\">\n";
		echo $css;
		echo "\n</style>\n";
		echo "<!--/RestaurantPress Colors-->\n";
	}

	/**
	 * CSS styles to improve our form.
	 */
	public function add_styles() {
		?>
		<style type="text/css">
			.restaurantpress-cropping-control {
				margin: 0 40px 1em 0;
				padding: 0;
				display:inline-block;
				vertical-align: top;
			}

			.restaurantpress-cropping-control input[type=radio] {
				margin-top: 1px;
			}

			.restaurantpress-cropping-control span.restaurantpress-cropping-control-aspect-ratio {
				margin-top: .5em;
				display:block;
			}

			.restaurantpress-cropping-control span.restaurantpress-cropping-control-aspect-ratio input {
				width: auto;
				display: inline-block;
			}
		</style>
		<?php
	}

	/**
	 * Scripts to improve our form.
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( document.body ).on( 'change', '.restaurantpress-cropping-control input[type="radio"]', function() {
					var $wrapper = $( this ).closest( '.restaurantpress-cropping-control' ),
						value    = $wrapper.find( 'input:checked' ).val();

					if ( 'custom' === value ) {
						$wrapper.find( '.restaurantpress-cropping-control-aspect-ratio' ).slideDown( 200 );
					} else {
						$wrapper.find( '.restaurantpress-cropping-control-aspect-ratio' ).hide();
					}

					return false;
				} );

				wp.customize.bind( 'ready', function() { // Ready?
					$( '.restaurantpress-cropping-control' ).find( 'input:checked' ).change();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Should our settings show?
	 *
	 * @return boolean
	 */
	public function is_active() {
		return is_restaurantpress() || rp_post_content_has_shortcode( 'restaurantpress_menu' ) || ! current_theme_supports( 'restaurantpress' );
	}

	/**
	 * Should our settings show on group layout?
	 *
	 * @return boolean
	 */
	public function is_group_active() {
		return is_group_menu_page();
	}

	/**
	 * Should our settings show on food archives?
	 *
	 * @return boolean
	 */
	public function is_foods_archive() {
		return is_food_menu_taxonomy() || is_food_menu_category() || ! current_theme_supports( 'restaurantpress' );
	}

	/**
	 * Colors section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_colors_section( $wp_customize ) {
		$wp_customize->add_section(
			'restaurantpress_colors',
			array(
				'title'    => __( 'Colors', 'restaurantpress' ),
				'priority' => 10,
				'panel'    => 'restaurantpress',
			)
		);

		$wp_customize->add_setting(
			'restaurantpress_colors[primary]',
			array(
				'default'           => '#ff0033',
				'type'              => 'option',
				'transport'         => 'postMessage',
				'capability'        => 'manage_restaurantpress',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'restaurantpress_colors[primary]',
				array(
					'label'    => __( 'Primary Color', 'restaurantpress' ),
					'section'  => 'restaurantpress_colors',
					'settings' => 'restaurantpress_colors[primary]',
					'priority' => 1
				)
			)
		);
	}

	/**
	 * Food group section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_food_group_section( $wp_customize ) {
		$wp_customize->add_section(
			'restaurantpress_food_group',
			array(
				'title'           => __( 'Food Group', 'restaurantpress' ),
				'priority'        => 10,
				'active_callback' => array( $this, 'is_group_active' ),
				'panel'           => 'restaurantpress',
			)
		);

		$wp_customize->add_setting(
			'restaurantpress_food_single_page',
			array(
				'default'              => 'yes',
				'type'                 => 'option',
				'capability'           => 'manage_restaurantpress',
				'sanitize_callback'    => 'rp_bool_to_string',
				'sanitize_js_callback' => 'rp_string_to_bool',
			)
		);

		$wp_customize->add_control(
			'restaurantpress_food_single_page',
			array(
				'label'       => __( 'Enable single food page', 'restaurantpress' ),
				'section'     => 'restaurantpress_food_group',
				'settings'    => 'restaurantpress_food_single_page',
				'type'        => 'checkbox',
			)
		);
	}

	/**
	 * Food images section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_food_images_section( $wp_customize ) {
		$theme_support = get_theme_support( 'restaurantpress' );
		$theme_support = is_array( $theme_support ) ? $theme_support[0]: false;

		$wp_customize->add_section(
			'restaurantpress_food_images',
			array(
				'title'           => __( 'Food Images', 'restaurantpress' ),
				'priority'        => 20,
				'active_callback' => array( $this, 'is_active' ),
				'panel'           => 'restaurantpress',
			)
		);

		if ( ! isset( $theme_support['single_image_width'] ) ) {
			$wp_customize->add_setting(
				'single_image_width',
				array(
					'default'              => 600,
					'type'                 => 'option',
					'capability'           => 'manage_restaurantpress',
					'sanitize_callback'    => 'absint',
					'sanitize_js_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'single_image_width',
				array(
					'label'       => __( 'Main image width', 'restaurantpress' ),
					'description' => __( 'This is the width used by the main image on single food pages. These images will remain uncropped.', 'restaurantpress' ),
					'section'     => 'restaurantpress_food_images',
					'settings'    => 'single_image_width',
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => 0,
						'step' => 1,
					),
				)
			);
		}

		if ( ! isset( $theme_support['thumbnail_image_width'] ) ) {
			$wp_customize->add_setting(
				'thumbnail_image_width',
				array(
					'default'              => 300,
					'type'                 => 'option',
					'capability'           => 'manage_restaurantpress',
					'sanitize_callback'    => 'absint',
					'sanitize_js_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'thumbnail_image_width',
				array(
					'label'       => __( 'Thumbnail width', 'restaurantpress' ),
					'description' => __( 'This size is used for food archives and food listings.', 'restaurantpress' ),
					'section'     => 'restaurantpress_food_images',
					'settings'    => 'thumbnail_image_width',
					'type'        => 'number',
					'input_attrs' => array( 'min' => 0, 'step'  => 1 ),
				)
			);
		}

		include_once RP_ABSPATH . 'includes/customizer/class-rp-customizer-control-cropping.php';

		$wp_customize->add_setting(
			'restaurantpress_thumbnail_cropping',
			array(
				'default'              => '1:1',
				'type'                 => 'option',
				'capability'           => 'manage_restaurantpress',
				'sanitize_callback'    => 'rp_clean',
			)
		);

		$wp_customize->add_setting(
			'restaurantpress_thumbnail_cropping_custom_width',
			array(
				'default'              => '4',
				'type'                 => 'option',
				'capability'           => 'manage_restaurantpress',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_setting(
			'restaurantpress_thumbnail_cropping_custom_height',
			array(
				'default'              => '3',
				'type'                 => 'option',
				'capability'           => 'manage_restaurantpress',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new RP_Customizer_Control_Cropping(
				$wp_customize,
				'restaurantpress_thumbnail_cropping',
				array(
					'section'  => 'restaurantpress_food_images',
					'settings' => array(
						'cropping'      => 'restaurantpress_thumbnail_cropping',
						'custom_width'  => 'restaurantpress_thumbnail_cropping_custom_width',
						'custom_height' => 'restaurantpress_thumbnail_cropping_custom_height',
					),
					'label'    => __( 'Thumbnail cropping', 'restaurantpress' ),
					'choices'  => array(
						'1:1'             => array(
							'label'       => __( '1:1', 'restaurantpress' ),
							'description' => __( 'Images will be cropped into a square', 'restaurantpress' ),
						),
						'custom'          => array(
							'label'       => __( 'Custom', 'restaurantpress' ),
							'description' => __( 'Images will be cropped to a custom aspect ratio', 'restaurantpress' ),
						),
						'uncropped'       => array(
							'label'       => __( 'Uncropped', 'restaurantpress' ),
							'description' => __( 'Images will display using the aspect ratio in which they were uploaded', 'restaurantpress' ),
						),
					),
				)
			)
		);
	}
}

new RP_Customizer();
