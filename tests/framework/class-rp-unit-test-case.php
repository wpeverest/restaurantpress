<?php
/**
 * RP Unit Test Case
 *
 * Provides RestaurantPress-specific setup/tear down/assert methods, custom factories,
 * and helper functions.
 *
 * @since 1.7
 */
class RP_Unit_Test_Case extends WP_UnitTestCase {

	/** @var RP_Unit_Test_Factory instance */
	protected $factory;

	/**
	 * Setup test case.
	 *
	 * @since 1.7
	 */
	public function setUp() {

		parent::setUp();

		// Add custom factories
		$this->factory = new RP_Unit_Test_Factory();

		// Setup mock RP session handler
		add_filter( 'restaurantpress_session_handler', array( $this, 'set_mock_session_handler' ) );

		$this->setOutputCallback( array( $this, 'filter_output' ) );

		// Register post types before each test
		RP_Post_types::register_post_types();
		RP_Post_types::register_taxonomies();
	}

	/**
	 * Mock the RP session using the abstract class as cookies are not available.
	 * during tests.
	 *
	 * @since  1.7
	 * @return string
	 */
	public function set_mock_session_handler() {
		return 'RP_Mock_Session_Handler';
	}

	/**
	 * Strip newlines and tabs when using expectedOutputString() as otherwise.
	 * the most template-related tests will fail due to indentation/alignment in.
	 * the template not matching the sample strings set in the tests.
	 *
	 * @since 1.7
	 */
	public function filter_output( $output ) {

		$output = preg_replace( '/[\n]+/S', '', $output );
		$output = preg_replace( '/[\t]+/S', '', $output );

		return $output;
	}

	/**
	 * Asserts thing is not WP_Error.
	 *
	 * @since 1.7
	 * @param mixed  $actual
	 * @param string $message
	 */
	public function assertNotWPError( $actual, $message = '' ) {
		$this->assertNotInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Asserts thing is WP_Error.
	 *
	 * @param mixed  $actual
	 * @param string $message
	 */
	public function assertIsWPError( $actual, $message = '' ) {
		$this->assertInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Throws an exception with an optional message and code.
	 *
	 * Note: can't use `throwException` as that's reserved.
	 *
	 * @param string $message
	 * @param int    $code
	 * @throws \Exception
	 */
	public function throwAnException( $message = null, $code = null ) {
		$message = $message ? $message : "We're all doomed!";
		throw new Exception( $message, $code );
	}

	/**
	 * Backport assertNotFalse to PHPUnit 3.6.12 which only runs in PHP 5.2.
	 *
	 * @since  2.2
	 * @param  $condition
	 * @param  string $message
	 * @return mixed
	 */
	public static function assertNotFalse( $condition, $message = '' ) {

		if ( version_compare( phpversion(), '5.3', '<' ) ) {

			self::assertThat( $condition, self::logicalNot( self::isFalse() ), $message );

		} else {

			parent::assertNotFalse( $condition, $message );
		}
	}
}
