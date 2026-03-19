<?php
/**
 * MFB_Plugin — bootstrap loader. Instantiates sub-classes and owns the activation hook.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MFB_Plugin {

	public function __construct() {
		$settings = new MFB_Settings();
		new MFB_Admin( $settings );
		new MFB_Frontend( $settings );
	}

	/**
	 * Static activation callback — runs before the object is instantiated,
	 * so it uses MFB_Settings::get_defaults() directly.
	 */
	public static function activate() {
		if ( ! get_option( MFB_OPTION_NAME ) ) {
			add_option( MFB_OPTION_NAME, MFB_Settings::get_defaults() );
		}
	}
}
