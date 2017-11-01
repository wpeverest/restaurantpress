<?php
/**
 * RestaurantPress Email Settings
 *
 * @class    RP_Settings_Emails
 * @version  1.5.1
 * @package  RestaurantPress/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RP_Settings_Emails', false ) ) :

/**
 * RP_Settings_Emails Class.
 */
class RP_Settings_Emails extends RP_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = __( 'Emails', 'restaurantpress' );

		add_action( 'restaurantpress_admin_field_email_notification', array( $this, 'email_notification_setting' ) );
		parent::__construct();
	}

	/**
	 * Add this page to settings.
	 *
	 * @param  array $pages Existing pages.
	 * @return array|mixed
	 */
	public function add_settings_page( $pages ) {
		return rp_mailer_enabled() ? parent::add_settings_page( $pages ) : $pages;
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'Email options', 'restaurantpress' ),
		);
		return apply_filters( 'restaurantpress_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'restaurantpress_email_settings', array(

			array( 'title' => __( 'Email notifications', 'restaurantpress' ),  'desc' => __( 'Email notifications sent from RestaurantPress are listed below. Click on an email to configure it.', 'restaurantpress' ), 'type' => 'title', 'id' => 'email_notification_settings' ),

			array( 'type' => 'email_notification' ),

			array( 'type' => 'sectionend', 'id' => 'email_notification_settings' ),

			array( 'type' => 'sectionend', 'id' => 'email_recipient_options' ),

			array( 'title' => __( 'Email sender options', 'restaurantpress' ), 'type' => 'title', 'desc' => '', 'id' => 'email_options' ),

			array(
				'title'    => __( '"From" name', 'restaurantpress' ),
				'desc'     => __( 'How the sender name appears in outgoing RestaurantPress emails.', 'restaurantpress' ),
				'id'       => 'restaurantpress_email_from_name',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => esc_attr( get_bloginfo( 'name', 'display' ) ),
				'autoload' => false,
				'desc_tip' => true,
			),

			array(
				'title'             => __( '"From" address', 'restaurantpress' ),
				'desc'              => __( 'How the sender email appears in outgoing RestaurantPress emails.', 'restaurantpress' ),
				'id'                => 'restaurantpress_email_from_address',
				'type'              => 'email',
				'custom_attributes' => array(
					'multiple' => 'multiple',
				),
				'css'               => 'min-width:300px;',
				'default'           => get_option( 'admin_email' ),
				'autoload'          => false,
				'desc_tip'          => true,
			),

			array( 'type' => 'sectionend', 'id' => 'email_options' ),

			array( 'title' => __( 'Email template', 'restaurantpress' ), 'type' => 'title', 'desc' => sprintf( __( 'This section lets you customize the RestaurantPress emails. <a href="%s" target="_blank">Click here to preview your email template</a>.', 'restaurantpress' ), wp_nonce_url( admin_url( '?preview_restaurantpress_mail=true' ), 'preview-mail' ) ), 'id' => 'email_template_options' ),

			array(
				'title'       => __( 'Header image', 'restaurantpress' ),
				'desc'        => __( 'URL to an image you want to show in the email header. Upload images using the media uploader (Admin > Media).', 'restaurantpress' ),
				'id'          => 'restaurantpress_email_header_image',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'placeholder' => __( 'N/A', 'restaurantpress' ),
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true,
			),

			array(
				'title'       => __( 'Footer text', 'restaurantpress' ),
				'desc'        => sprintf( __( 'The text to appear in the footer of RestaurantPress emails. Available placeholders: %s', 'restaurantpress' ), '{site_title}' ),
				'id'          => 'restaurantpress_email_footer_text',
				'css'         => 'width:300px; height: 75px;',
				'placeholder' => __( 'N/A', 'restaurantpress' ),
				'type'        => 'textarea',
				/* translators: %s: site name */
				'default'     => '{site_title}',
				'autoload'    => false,
				'desc_tip'    => true,
			),

			array(
				'title'    => __( 'Base color', 'restaurantpress' ),
				/* translators: %s: default color */
				'desc'     => sprintf( __( 'The base color for RestaurantPress email templates. Default %s.', 'restaurantpress' ), '<code>#d54e21</code>' ),
				'id'       => 'restaurantpress_email_base_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#d54e21',
				'autoload' => false,
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Background color', 'restaurantpress' ),
				/* translators: %s: default color */
				'desc'     => sprintf( __( 'The background color for RestaurantPress email templates. Default %s.', 'restaurantpress' ), '<code>#f7f7f7</code>' ),
				'id'       => 'restaurantpress_email_background_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#f7f7f7',
				'autoload' => false,
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Body background color', 'restaurantpress' ),
				/* translators: %s: default color */
				'desc'     => sprintf( __( 'The main body background color. Default %s.', 'restaurantpress' ), '<code>#ffffff</code>' ),
				'id'       => 'restaurantpress_email_body_background_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#ffffff',
				'autoload' => false,
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Body text color', 'restaurantpress' ),
				/* translators: %s: default color */
				'desc'     => sprintf( __( 'The main body text color. Default %s.', 'restaurantpress' ), '<code>#3c3c3c</code>' ),
				'id'       => 'restaurantpress_email_text_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#3c3c3c',
				'autoload' => false,
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

		) );

		return apply_filters( 'restaurantpress_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		// Define emails that can be customised here.
		$mailer          = RP()->mailer();
		$email_templates = $mailer->get_emails();

		if ( $current_section ) {
			foreach ( $email_templates as $email_key => $email ) {
				if ( strtolower( $email_key ) == $current_section ) {
					$email->admin_options();
					break;
				}
			}
		} else {
			$settings = $this->get_settings();
			RP_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {
			RP_Admin_Settings::save_fields( $this->get_settings() );

		} else {
			$rp_emails = RP_Emails::instance();

			if ( in_array( $current_section, array_map( 'sanitize_title', array_keys( $rp_emails->get_emails() ) ) ) ) {
				foreach ( $rp_emails->get_emails() as $email_id => $email ) {
					if ( sanitize_title( $email_id ) === $current_section ) {
						do_action( 'restaurantpress_update_options_' . $this->id . '_' . $email->id );
					}
				}
			} else {
				do_action( 'restaurantpress_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}

	/**
	 * Output email notification settings.
	 */
	public function email_notification_setting() {
		// Define emails that can be customised here.
		$mailer          = RP()->mailer();
		$email_templates = $mailer->get_emails();
		?>
		<tr valign="top">
		    <td class="rp_emails_wrapper" colspan="2">
				<table class="rp_emails widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = apply_filters( 'restaurantpress_email_setting_columns', array(
									'status'     => '',
									'name'       => __( 'Email', 'restaurantpress' ),
									'email_type' => __( 'Content type', 'restaurantpress' ),
									'recipient'  => __( 'Recipient(s)', 'restaurantpress' ),
									'actions'    => '',
								) );
								foreach ( $columns as $key => $column ) {
									echo '<th class="rp-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
							if ( ! empty( $email_templates ) ) {
								foreach ( $email_templates as $email_key => $email ) {
									echo '<tr>';

									foreach ( $columns as $key => $column ) {

										switch ( $key ) {
											case 'name' :
												echo '<td class="rp-email-settings-table-' . esc_attr( $key ) . '">
													<a href="' . admin_url( 'admin.php?page=rp-settings&tab=email&section=' . strtolower( $email_key ) ) . '">' . $email->get_title() . '</a>
													' . rp_help_tip( $email->get_description() ) . '
												</td>';
											break;
											case 'recipient' :
												echo '<td class="rp-email-settings-table-' . esc_attr( $key ) . '">
													' . esc_html( $email->is_customer_email() ? __( 'Customer', 'restaurantpress' ) : $email->get_recipient() ) . '
												</td>';
											break;
											case 'status' :
												echo '<td class="rp-email-settings-table-' . esc_attr( $key ) . '">';

												if ( $email->is_manual() ) {
													echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manually sent', 'restaurantpress' ) . '">' . esc_html__( 'Manual', 'restaurantpress' ) . '</span>';
												} elseif ( $email->is_enabled() ) {
													echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'restaurantpress' ) . '">' . esc_html__( 'Yes', 'restaurantpress' ) . '</span>';
												} else {
													echo '<span class="status-disabled tips" data-tip="' . esc_attr__( 'Disabled', 'restaurantpress' ) . '">-</span>';
												}

												echo '</td>';
											break;
											case 'email_type' :
												echo '<td class="rp-email-settings-table-' . esc_attr( $key ) . '">
													' . esc_html( $email->get_content_type() ) . '
												</td>';
											break;
											case 'actions' :
												echo '<td class="rp-email-settings-table-' . esc_attr( $key ) . '">
													<a class="button alignright tips" data-tip="' . esc_attr__( 'Configure', 'restaurantpress' ) . '" href="' . admin_url( 'admin.php?page=rp-settings&tab=email&section=' . strtolower( $email_key ) ) . '">' . esc_html__( 'Configure', 'restaurantpress' ) . '</a>
												</td>';
											break;
											default :
												do_action( 'restaurantpress_email_setting_column_' . $key, $email );
											break;
										}
									}

									echo '</tr>';
								}
							} else { ?>
								<td class="rp-email-blank-state" colspan="5"><?php _e( 'No notificational emai has been configured.', 'restaurantpress' ); ?></td>
							<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}

endif;

return new RP_Settings_Emails();
