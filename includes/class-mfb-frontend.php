<?php
/**
 * MFB_Frontend — frontend asset enqueueing and floating bar rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MFB_Frontend {

	private $settings;

	public function __construct( MFB_Settings $settings ) {
		$this->settings = $settings;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer',          array( $this, 'render_floating_bar' ), 100 );
	}

	public function enqueue_assets() {
		$opts = $this->settings->get_options();

		if ( empty( $opts['phone_link'] ) ) {
			return;
		}

		if ( '0' === $opts['show_on_mobile']
		  && '0' === $opts['show_on_tablet']
		  && '0' === $opts['show_on_desktop'] ) {
			return;
		}

		wp_enqueue_style(
			'mfb-frontend',
			MFB_PLUGIN_URL . 'assets/frontend.css',
			array(),
			MFB_VERSION
		);

		$inline_css = sprintf(
			'#mfb-floating-bar a.mfb-call-link { background-color: %1$s; color: %2$s; }
			 #mfb-floating-bar .mfb-icon-wrap { background-color: %3$s; color: %4$s; }
			 #mfb-floating-bar .mfb-icon { color: %4$s; }',
			esc_attr( $opts['bg_color'] ),
			esc_attr( $opts['text_color'] ),
			esc_attr( $opts['icon_color'] ),
			esc_attr( $opts['icon_text_color'] )
		);
		wp_add_inline_style( 'mfb-frontend', $inline_css );
	}

	public function render_floating_bar() {
		$opts = $this->settings->get_options();

		if ( empty( $opts['phone_link'] ) ) {
			return;
		}

		$classes   = array( 'mfb-bar' );
		$classes[] = 'mfb-align-' . $opts['alignment'];

		if ( '1' === $opts['show_on_mobile'] )  $classes[] = 'mfb-show-mobile';
		if ( '1' === $opts['show_on_tablet'] )  $classes[] = 'mfb-show-tablet';
		if ( '1' === $opts['show_on_desktop'] ) $classes[] = 'mfb-show-desktop';

		// If only the base + alignment classes exist, no device is enabled.
		if ( count( $classes ) === 2 ) {
			return;
		}

		$class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		?>
		<div id="mfb-floating-bar" class="<?php echo esc_attr( $class_attr ); ?>" role="complementary" aria-label="<?php esc_attr_e( 'Call us', 'mobile-floating-banner' ); ?>">
			<a href="<?php echo esc_url( $opts['phone_link'] ); ?>" class="mfb-call-link">
				<span class="mfb-icon-wrap" aria-hidden="true">
					<svg class="mfb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26">
						<path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.47 11.47 0 0 0 3.59.57 1 1 0 0 1 1 1V21a1 1 0 0 1-1 1A17 17 0 0 1 3 5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.59a1 1 0 0 1-.25 1.01l-2.2 2.2z"/>
					</svg>
				</span>
				<span class="mfb-number"><?php echo esc_html( $opts['phone_number'] ); ?></span>
			</a>
		</div>
		<?php
	}
}
