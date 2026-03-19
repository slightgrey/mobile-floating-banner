<?php
/**
 * Plugin Name:       Mobile Floating Banner
 * Description:       Displays a fixed floating call bar at the bottom of the page with a phone icon, number, and configurable colors, alignment, and device visibility.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            VinZoy
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mobile-floating-banner
 */

if ( ! function_exists( 'add_action' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define( 'MFB_VERSION',     '1.0.0' );
define( 'MFB_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'MFB_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'MFB_OPTION_NAME', 'mobile_floating_banner_options' );
define( 'MFB_PLUGIN_FILE', __FILE__ );

// ---------------------------------------------------------------------------
// Load classes
// ---------------------------------------------------------------------------

require_once MFB_PLUGIN_DIR . 'includes/class-mfb-settings.php';
require_once MFB_PLUGIN_DIR . 'includes/class-mfb-admin.php';
require_once MFB_PLUGIN_DIR . 'includes/class-mfb-frontend.php';
require_once MFB_PLUGIN_DIR . 'includes/class-mfb-plugin.php';

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------

register_activation_hook( MFB_PLUGIN_FILE, array( 'MFB_Plugin', 'activate' ) );
add_action( 'plugins_loaded', function () {
	new MFB_Plugin();
} );
