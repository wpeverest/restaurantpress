<?php
/**
 * Email Controller
 *
 * RestaurantPress Emails class which handles the sending on notification emails and email templates. This class loads in available emails.
 *
 * @class    RP_Emails
 * @version  1.6.0
 * @package  RestaurantPress/Classes/Emails
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Emails Class.
 */
class RP_Emails {

	/**
	 * Email notification classes.
	 *
	 * @var array
	 */
	public $emails = array();

	/**
	 * The single instance of the class.
	 *
	 * @var RP_Emails|null
	 */
	protected static $instance = null;

	/**
	 * Background emailer class.
	 */
	protected static $background_emailer;

	/**
	 * Gets the main RP_Emails Instance.
	 *
	 * @static
	 * @since  1.6.0
	 * @return RP_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.6.0
	 */
	public function __clone() {
		rp_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restaurantpress' ), '1.6.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.6.0
	 */
	public function __wakeup() {
		rp_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restaurantpress' ), '1.6.0' );
	}

	/**
	 * Hook in all notificational emails.
	 */
	public static function init_notificational_emails() {
		$email_actions = apply_filters( 'restaurantpress_email_actions', array() );

		if ( apply_filters( 'restaurantpress_defer_notificational_emails', false ) ) {
			self::$background_emailer = new RP_Background_Emailer();

			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'queue_notificational_email' ), 10, 10 );
			}
		} else {
			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'send_notificational_email' ), 10, 10 );
			}
		}
	}

	/**
	 * Queues transactional email so it's not sent in current request if enabled,
	 * otherwise falls back to send now.
	 */
	public static function queue_transactional_email() {
		if ( is_a( self::$background_emailer, 'RP_Background_Emailer' ) ) {
			self::$background_emailer->push_to_queue( array(
				'filter' => current_filter(),
				'args'   => func_get_args(),
			) );
		} else {
			call_user_func_array( array( __CLASS__, 'send_notificational_email' ), func_get_args() );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param string $filter Filter name.
	 * @param array  $args   Email args (default: []).
	 */
	public static function send_queued_notificational_email( $filter = '', $args = array() ) {
		if ( apply_filters( 'restaurantpress_allow_send_queued_notificational_email', true, $filter, $args ) ) {
			self::instance(); // Init self so emails exist.
			do_action_ref_array( $filter . '_notification', $args );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param array $args Email args (default: []).
	 */
	public static function send_notificational_email( $args = array() ) {
		try {
			$args = func_get_args();
			self::instance(); // Init self so emails exist.
			do_action_ref_array( current_filter() . '_notification', $args );
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				trigger_error( 'Notificational email triggered fatal error for callback ' . current_filter(), E_USER_WARNING );
			}
		}
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'restaurantpress_email_header', array( $this, 'email_header' ) );
		add_action( 'restaurantpress_email_footer', array( $this, 'email_footer' ) );

		// Hook for replacing {site_title} in email-footer.
		add_filter( 'restaurantpress_email_footer_text' , array( $this, 'email_footer_replace_site_title' ) );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'restaurantpress_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once( dirname( __FILE__ ) . '/emails/class-rp-email.php' );

		$this->emails = apply_filters( 'restaurantpress_email_classes', $this->emails );

		// Include CSS inliner.
		if ( ! class_exists( 'Emogrifier' ) && class_exists( 'DOMDocument' ) ) {
			include_once( dirname( __FILE__ ) . '/libraries/class-emogrifier.php' );
		}
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return array
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( get_option( 'restaurantpress_email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( get_option( 'restaurantpress_email_from_address' ) );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading heading for the email.
	 */
	public function email_header( $email_heading ) {
		rp_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		rp_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Filter callback to replace {site_title} in email footer
	 *
	 * @param  string $string Email footer text.
	 * @return string         Email footer text with any replacements done.
	 */
	public function email_footer_replace_site_title( $string ) {
		return str_replace( '{site_title}', $this->get_blogname(), $string );
	}

	/**
	 * Wraps a message in the RestaurantPress mail template.
	 *
	 * @param string $email_heading Heading text.
	 * @param string $message       Email message.
	 * @param bool   $plain_text    Set true to send as plain text. Default to false.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'restaurantpress_email_header', $email_heading, null );

		echo wpautop( wptexturize( $message ) );

		do_action( 'restaurantpress_email_footer', null );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 *
	 * @param mixed $to
	 * @param mixed $subject
	 * @param mixed $message
	 * @param string $headers (default: "Content-Type: text/html\r\n")
	 * @param string $attachments (default: "")
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
		// Send.
		$email = new RP_Email();
		return $email->send( $to, $subject, $message, $headers, $attachments );
	}
}
