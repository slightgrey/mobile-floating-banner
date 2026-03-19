<?php
/**
 * Admin settings page partial.
 * Included only from mfb_render_settings_page() — never accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// $opts is set by MFB_Admin::render_settings_page() before this file is included.
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( 'mfb_options_group' ); ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'mfb_options_group' );
		do_settings_sections( 'mobile-floating-banner' );
		submit_button( __( 'Save Settings', 'mobile-floating-banner' ) );
		?>
	</form>

	<hr>

	<div class="mfb-preview-section">
		<h2><?php esc_html_e( 'Preview', 'mobile-floating-banner' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Reflects saved settings. Save the form above to update this preview.', 'mobile-floating-banner' ); ?></p>
		<?php
		$align_map = array( 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' );
		$justify   = $align_map[ $opts['alignment'] ] ?? 'flex-start';
		?>
		<div class="mfb-admin-preview-row" style="justify-content: <?php echo esc_attr( $justify ); ?>;">
			<a class="mfb-admin-preview-pill" style="background-color: <?php echo esc_attr( $opts['bg_color'] ); ?>; color: <?php echo esc_attr( $opts['text_color'] ); ?>;" href="#" onclick="return false;">
				<span class="mfb-preview-icon-wrap" aria-hidden="true" style="background-color: <?php echo esc_attr( $opts['icon_color'] ); ?>; color: <?php echo esc_attr( $opts['icon_text_color'] ); ?>;">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26">
						<path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.47 11.47 0 0 0 3.59.57 1 1 0 0 1 1 1V21a1 1 0 0 1-1 1A17 17 0 0 1 3 5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.59a1 1 0 0 1-.25 1.01l-2.2 2.2z"/>
					</svg>
				</span>
				<span class="mfb-preview-number">
					<?php echo $opts['phone_number'] ? esc_html( $opts['phone_number'] ) : esc_html__( '(enter a phone number above)', 'mobile-floating-banner' ); ?>
				</span>
			</a>
		</div>
		<p class="description" style="margin-top: 8px;">
			<?php
			$showing = array();
			if ( '1' === $opts['show_on_mobile'] )  $showing[] = __( 'mobile', 'mobile-floating-banner' );
			if ( '1' === $opts['show_on_tablet'] )  $showing[] = __( 'tablet', 'mobile-floating-banner' );
			if ( '1' === $opts['show_on_desktop'] ) $showing[] = __( 'desktop', 'mobile-floating-banner' );

			if ( $showing ) {
				/* translators: %s: comma-separated list of device types */
				printf( esc_html__( 'Banner will be shown on: %s.', 'mobile-floating-banner' ), esc_html( implode( ', ', $showing ) ) );
			} else {
				esc_html_e( 'Banner is currently hidden on all devices.', 'mobile-floating-banner' );
			}
			?>
		</p>
	</div>
</div>
