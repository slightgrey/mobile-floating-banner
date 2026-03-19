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

// ---------------------------------------------------------------------------
// Activation
// ---------------------------------------------------------------------------

register_activation_hook( __FILE__, 'mfb_activate' );

function mfb_activate() {
	$defaults = array(
		'phone_number'    => '',
		'phone_link'      => '',
		'bg_color'        => '#aee6b0',
		'text_color'      => '#1a5c1e',
		'icon_color'      => '#25a244',
		'icon_text_color' => '#ffffff',
		'alignment'       => 'left',
		'show_on_mobile'  => '1',
		'show_on_tablet'  => '0',
		'show_on_desktop' => '0',
	);
	if ( ! get_option( MFB_OPTION_NAME ) ) {
		add_option( MFB_OPTION_NAME, $defaults );
	}
}

// ---------------------------------------------------------------------------
// Hooks
// ---------------------------------------------------------------------------

add_action( 'admin_menu',            'mfb_add_settings_page' );
add_action( 'admin_init',            'mfb_register_settings' );
add_action( 'admin_enqueue_scripts', 'mfb_enqueue_admin_assets' );
add_action( 'wp_enqueue_scripts',    'mfb_enqueue_frontend_assets' );
add_action( 'wp_footer',             'mfb_render_floating_bar', 100 );
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	'mfb_plugin_action_links'
);

// ---------------------------------------------------------------------------
// Helper: get options merged with defaults
// ---------------------------------------------------------------------------

function mfb_get_options() {
	$defaults = array(
		'phone_number'    => '',
		'phone_link'      => '',
		'bg_color'        => '#aee6b0',
		'text_color'      => '#1a5c1e',
		'icon_color'      => '#25a244',
		'icon_text_color' => '#ffffff',
		'alignment'       => 'left',
		'show_on_mobile'  => '1',
		'show_on_tablet'  => '0',
		'show_on_desktop' => '0',
	);
	$saved = get_option( MFB_OPTION_NAME, array() );
	return wp_parse_args( $saved, $defaults );
}

// ---------------------------------------------------------------------------
// Admin: settings page registration
// ---------------------------------------------------------------------------

function mfb_add_settings_page() {
	add_options_page(
		__( 'Mobile Floating Banner Settings', 'mobile-floating-banner' ),
		__( 'Floating Banner', 'mobile-floating-banner' ),
		'manage_options',
		'mobile-floating-banner',
		'mfb_render_settings_page'
	);
}

function mfb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	require_once MFB_PLUGIN_DIR . 'admin/settings-page.php';
}

// ---------------------------------------------------------------------------
// Admin: Settings API — sections, fields, sanitize
// ---------------------------------------------------------------------------

function mfb_register_settings() {
	register_setting(
		'mfb_options_group',
		MFB_OPTION_NAME,
		array(
			'sanitize_callback' => 'mfb_sanitize_options',
			'default'           => array(),
		)
	);

	// Section: Contact
	add_settings_section(
		'mfb_section_contact',
		__( 'Phone Contact', 'mobile-floating-banner' ),
		'__return_false',
		'mobile-floating-banner'
	);

	add_settings_field(
		'mfb_phone_number',
		__( 'Display Text', 'mobile-floating-banner' ),
		'mfb_field_phone_number',
		'mobile-floating-banner',
		'mfb_section_contact'
	);

	add_settings_field(
		'mfb_phone_link',
		__( 'Phone Link (tel: URL)', 'mobile-floating-banner' ),
		'mfb_field_phone_link',
		'mobile-floating-banner',
		'mfb_section_contact'
	);

	// Section: Appearance
	add_settings_section(
		'mfb_section_appearance',
		__( 'Appearance', 'mobile-floating-banner' ),
		'__return_false',
		'mobile-floating-banner'
	);

	add_settings_field(
		'mfb_bg_color',
		__( 'Background Color', 'mobile-floating-banner' ),
		'mfb_field_bg_color',
		'mobile-floating-banner',
		'mfb_section_appearance'
	);

	add_settings_field(
		'mfb_text_color',
		__( 'Pill Text Color', 'mobile-floating-banner' ),
		'mfb_field_text_color',
		'mobile-floating-banner',
		'mfb_section_appearance'
	);

	add_settings_field(
		'mfb_icon_color',
		__( 'Icon Circle Color', 'mobile-floating-banner' ),
		'mfb_field_icon_color',
		'mobile-floating-banner',
		'mfb_section_appearance'
	);

	add_settings_field(
		'mfb_icon_text_color',
		__( 'Icon Symbol Color', 'mobile-floating-banner' ),
		'mfb_field_icon_text_color',
		'mobile-floating-banner',
		'mfb_section_appearance'
	);

	add_settings_field(
		'mfb_alignment',
		__( 'Content Alignment', 'mobile-floating-banner' ),
		'mfb_field_alignment',
		'mobile-floating-banner',
		'mfb_section_appearance'
	);

	// Section: Display Rules
	add_settings_section(
		'mfb_section_display',
		__( 'Display Rules', 'mobile-floating-banner' ),
		'mfb_section_display_description',
		'mobile-floating-banner'
	);

	add_settings_field(
		'mfb_show_on_mobile',
		__( 'Show on Mobile', 'mobile-floating-banner' ),
		'mfb_field_show_on_mobile',
		'mobile-floating-banner',
		'mfb_section_display'
	);

	add_settings_field(
		'mfb_show_on_tablet',
		__( 'Show on Tablet', 'mobile-floating-banner' ),
		'mfb_field_show_on_tablet',
		'mobile-floating-banner',
		'mfb_section_display'
	);

	add_settings_field(
		'mfb_show_on_desktop',
		__( 'Show on Desktop', 'mobile-floating-banner' ),
		'mfb_field_show_on_desktop',
		'mobile-floating-banner',
		'mfb_section_display'
	);
}

function mfb_sanitize_options( $input ) {
	$clean = array();

	$clean['phone_number'] = isset( $input['phone_number'] )
		? sanitize_text_field( $input['phone_number'] )
		: '';

	// esc_url_raw preserves tel: scheme correctly
	$clean['phone_link'] = isset( $input['phone_link'] )
		? esc_url_raw( $input['phone_link'] )
		: '';

	$clean['bg_color'] = isset( $input['bg_color'] )
		? ( sanitize_hex_color( $input['bg_color'] ) ?: '#222222' )
		: '#222222';

	$clean['text_color'] = isset( $input['text_color'] )
		? ( sanitize_hex_color( $input['text_color'] ) ?: '#1a5c1e' )
		: '#1a5c1e';

	$clean['icon_color'] = isset( $input['icon_color'] )
		? ( sanitize_hex_color( $input['icon_color'] ) ?: '#25a244' )
		: '#25a244';

	$clean['icon_text_color'] = isset( $input['icon_text_color'] )
		? ( sanitize_hex_color( $input['icon_text_color'] ) ?: '#ffffff' )
		: '#ffffff';

	$allowed_alignments  = array( 'left', 'center', 'right' );
	$clean['alignment']  = ( isset( $input['alignment'] ) && in_array( $input['alignment'], $allowed_alignments, true ) )
		? $input['alignment']
		: 'center';

	$clean['show_on_mobile']  = ! empty( $input['show_on_mobile'] )  ? '1' : '0';
	$clean['show_on_tablet']  = ! empty( $input['show_on_tablet'] )  ? '1' : '0';
	$clean['show_on_desktop'] = ! empty( $input['show_on_desktop'] ) ? '1' : '0';

	return $clean;
}

// ---------------------------------------------------------------------------
// Field render callbacks
// ---------------------------------------------------------------------------

function mfb_field_phone_number() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[phone_number]" id="mfb_phone_number" value="%2$s" class="regular-text" placeholder="%3$s">
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['phone_number'] ),
		esc_attr__( 'e.g. +1 800 555 0100', 'mobile-floating-banner' ),
		esc_html__( 'Text shown next to the phone icon in the bar.', 'mobile-floating-banner' )
	);
}

function mfb_field_phone_link() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[phone_link]" id="mfb_phone_link" value="%2$s" class="regular-text" placeholder="%3$s">
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['phone_link'] ),
		esc_attr__( 'tel:+18005550100', 'mobile-floating-banner' ),
		esc_html__( 'Full tel: URL. Include the country code without spaces.', 'mobile-floating-banner' )
	);
}

function mfb_field_bg_color() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[bg_color]" id="mfb_bg_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['bg_color'] ),
		esc_attr( '#222222' )
	);
}

function mfb_field_text_color() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[text_color]" id="mfb_text_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['text_color'] ),
		esc_attr( '#ffffff' )
	);
}

function mfb_field_icon_color() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[icon_color]" id="mfb_icon_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['icon_color'] ),
		esc_attr( '#25a244' ),
		esc_html__( 'Background color of the circular icon on the left.', 'mobile-floating-banner' )
	);
}

function mfb_field_icon_text_color() {
	$opts = mfb_get_options();
	printf(
		'<input type="text" name="%1$s[icon_text_color]" id="mfb_icon_text_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		esc_attr( $opts['icon_text_color'] ),
		esc_attr( '#ffffff' ),
		esc_html__( 'Color of the phone symbol inside the icon circle.', 'mobile-floating-banner' )
	);
}

function mfb_field_alignment() {
	$opts    = mfb_get_options();
	$current = $opts['alignment'];
	$choices = array(
		'left'   => __( 'Left', 'mobile-floating-banner' ),
		'center' => __( 'Center', 'mobile-floating-banner' ),
		'right'  => __( 'Right', 'mobile-floating-banner' ),
	);
	echo '<select name="' . esc_attr( MFB_OPTION_NAME ) . '[alignment]" id="mfb_alignment">';
	foreach ( $choices as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
}

function mfb_section_display_description() {
	echo '<p>' . esc_html__( 'Control which device sizes show the banner. Mobile is enabled by default. Breakpoints: mobile < 768px, tablet 768–1024px, desktop > 1024px.', 'mobile-floating-banner' ) . '</p>';
}

function mfb_field_show_on_mobile() {
	$opts = mfb_get_options();
	printf(
		'<label><input type="checkbox" name="%1$s[show_on_mobile]" id="mfb_show_on_mobile" value="1"%2$s> %3$s</label>
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		checked( '1', $opts['show_on_mobile'], false ),
		esc_html__( 'Enabled', 'mobile-floating-banner' ),
		esc_html__( 'Viewports narrower than 768px.', 'mobile-floating-banner' )
	);
}

function mfb_field_show_on_tablet() {
	$opts = mfb_get_options();
	printf(
		'<label><input type="checkbox" name="%1$s[show_on_tablet]" id="mfb_show_on_tablet" value="1"%2$s> %3$s</label>
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		checked( '1', $opts['show_on_tablet'], false ),
		esc_html__( 'Enabled', 'mobile-floating-banner' ),
		esc_html__( 'Viewports between 768px and 1024px.', 'mobile-floating-banner' )
	);
}

function mfb_field_show_on_desktop() {
	$opts = mfb_get_options();
	printf(
		'<label><input type="checkbox" name="%1$s[show_on_desktop]" id="mfb_show_on_desktop" value="1"%2$s> %3$s</label>
		 <p class="description">%4$s</p>',
		esc_attr( MFB_OPTION_NAME ),
		checked( '1', $opts['show_on_desktop'], false ),
		esc_html__( 'Enabled', 'mobile-floating-banner' ),
		esc_html__( 'Viewports wider than 1024px.', 'mobile-floating-banner' )
	);
}

// ---------------------------------------------------------------------------
// Admin: enqueue assets (only on plugin settings page)
// ---------------------------------------------------------------------------

function mfb_enqueue_admin_assets( $hook_suffix ) {
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

	// Initialise color pickers — inline to avoid a separate JS file
	wp_add_inline_script(
		'wp-color-picker',
		'jQuery(function($){ $(".mfb-color-picker").wpColorPicker(); });'
	);
}

// ---------------------------------------------------------------------------
// Frontend: enqueue CSS and inject inline dynamic colors
// ---------------------------------------------------------------------------

function mfb_enqueue_frontend_assets() {
	$opts = mfb_get_options();

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

// ---------------------------------------------------------------------------
// Frontend: render the floating bar in wp_footer
// ---------------------------------------------------------------------------

function mfb_render_floating_bar() {
	$opts = mfb_get_options();

	if ( empty( $opts['phone_link'] ) ) {
		return;
	}

	$classes   = array( 'mfb-bar' );
	$classes[] = 'mfb-align-' . $opts['alignment'];

	if ( '1' === $opts['show_on_mobile'] ) {
		$classes[] = 'mfb-show-mobile';
	}
	if ( '1' === $opts['show_on_tablet'] ) {
		$classes[] = 'mfb-show-tablet';
	}
	if ( '1' === $opts['show_on_desktop'] ) {
		$classes[] = 'mfb-show-desktop';
	}

	// If no device class was added there's nothing to show
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

// ---------------------------------------------------------------------------
// Plugin action links: add Settings shortcut in Plugins list
// ---------------------------------------------------------------------------

function mfb_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=mobile-floating-banner' ) ),
		esc_html__( 'Settings', 'mobile-floating-banner' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
