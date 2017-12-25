<?php
/**
 * RestaurantPress Unit Tests Bootstrap
 *
 * @since 1.7
 */
class RP_Unit_Tests_Bootstrap {

	/** @var RP_Unit_Tests_Bootstrap instance */
	protected static $instance = null;

	/** @var string directory where wordpress-tests-lib is installed */
	public $wp_tests_dir;

	/** @var string testing directory */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;

	/**
	 * Setup the unit testing environment.
	 *
	 * @since 1.7
	 */
	public function __construct() {

		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );

		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}

		$this->tests_dir    = dirname( __FILE__ );
		$this->plugin_dir   = dirname( $this->tests_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

		// Load test function so tests_add_filter() is available.
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// Load RP.
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_rp' ) );

		// Install RP.
		tests_add_filter( 'setup_theme', array( $this, 'install_rp' ) );

		// Load the WP testing environment.
		require_once $this->wp_tests_dir . '/includes/bootstrap.php';

		// Load RP testing framework.
		$this->includes();
	}

	/**
	 * Load RestaurantPress.
	 *
	 * @since 1.7
	 */
	public function load_rp() {
		require_once( $this->plugin_dir . '/restaurantpress.php' );
	}

	/**
	 * Install RestaurantPress after the test environment and RP have been loaded.
	 *
	 * @since 1.7
	 */
	public function install_rp() {

		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'RP_REMOVE_ALL_DATA', true );
		include( $this->plugin_dir . '/uninstall.php' );

		RP_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
		if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
			$GLOBALS['wp_roles']->reinit();
		} else {
			$GLOBALS['wp_roles'] = null;
			wp_roles();
		}

		echo 'Installing RestaurantPress...' . PHP_EOL;
	}

	/**
	 * Load RP-specific test cases and factories.
	 *
	 * @since 1.7
	 */
	public function includes() {}

	/**
	 * Get the single class instance.
	 *
	 * @since  1.7
	 * @return RP_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

RP_Unit_Tests_Bootstrap::instance();
