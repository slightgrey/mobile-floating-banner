<?php
/**
 * MFB_Admin — admin menu, asset enqueueing, plugin action links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MFB_Admin {

	private $settings;

	public function __construct( MFB_Settings $settings ) {
		$this->settings = $settings;

		add_action( 'admin_menu',            array( $this, 'add_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter(
			'plugin_action_links_' . plugin_basename( MFB_PLUGIN_FILE ),
			array( $this, 'add_action_links' )
		);
	}

	public function add_settings_page() {
		add_options_page(
			__( 'Mobile Floating Banner Settings', 'mobile-floating-banner' ),
			__( 'Floating Banner', 'mobile-floating-banner' ),
			'manage_options',
			'mobile-floating-banner',
			array( $this, 'render_settings_page' )
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$opts = $this->settings->get_options();
		require_once MFB_PLUGIN_DIR . 'admin/settings-page.php';
	}

	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_mobile-floating-banner' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'mfb-admin',
			MFB_PLUGIN_URL . 'assets/admin.css',
			array(),
			MFB_VERSION
		);

		wp_add_inline_script(
			'wp-color-picker',
			'jQuery(function($){ $(".mfb-color-picker").wpColorPicker(); });'
		);
	}

	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=mobile-floating-banner' ) ),
			esc_html__( 'Settings', 'mobile-floating-banner' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}
}
