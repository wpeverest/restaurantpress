<?php
/**
 * Custom control for radio buttons with nested options.
 *
 * Used for our image cropping settings.
 *
 * @version 1.7.0
 * @package RestaurantPress
 * @author  WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Customizer_Control_Cropping class.
 */
class RP_Customizer_Control_Cropping extends WP_Customize_Control {

	/**
	 * Declare the control type.
	 *
	 * @var string
	 */
	public $type = 'restaurantpress-cropping-control';

	/**
	 * Render control.
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$value         = $this->value( 'cropping' );
		$custom_width  = $this->value( 'custom_width' );
		$custom_height = $this->value( 'custom_height' );
		?>

		<span class="customize-control-title">
			<?php echo esc_html( $this->label ); ?>
		</span>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<ul id="input_<?php echo esc_attr( $this->id ); ?>" class="restaurantpress-cropping-control">
			<?php foreach ( $this->choices as $key => $radio ) : ?>
				<li>
					<input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $this->id . $key ); ?>" <?php $this->link( 'cropping' ); ?> <?php checked( $value, $key ); ?> />
					<label for="<?php echo esc_attr( $this->id . $key ); ?>"><?php echo esc_html( $radio['label'] ); ?><br/><span class="description"><?php echo esc_html( $radio['description'] ); ?></span></label>

					<?php if ( 'custom' === $key ) : ?>
						<span class="restaurantpress-cropping-control-aspect-ratio">
							<input type="text" pattern="\d*" size="3" value="<?php echo esc_attr( $custom_width ); ?>" <?php $this->link( 'custom_width' ); ?> /> : <input type="text" pattern="\d*" size="3" value="<?php echo esc_attr( $custom_height ); ?>" <?php $this->link( 'custom_height' ); ?> />
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
