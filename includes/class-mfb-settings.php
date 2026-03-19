<?php
/**
 * MFB_Settings — options helper, Settings API registration, field callbacks, sanitizer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MFB_Settings {

	// -------------------------------------------------------------------------
	// Defaults
	// -------------------------------------------------------------------------

	public static function get_defaults() {
		return array(
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
	}

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	public function get_options() {
		$saved = get_option( MFB_OPTION_NAME, array() );
		return wp_parse_args( $saved, self::get_defaults() );
	}

	// -------------------------------------------------------------------------
	// Settings API
	// -------------------------------------------------------------------------

	public function register_settings() {
		register_setting(
			'mfb_options_group',
			MFB_OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
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
			array( $this, 'render_field_phone_number' ),
			'mobile-floating-banner',
			'mfb_section_contact'
		);

		add_settings_field(
			'mfb_phone_link',
			__( 'Phone Link (tel: URL)', 'mobile-floating-banner' ),
			array( $this, 'render_field_phone_link' ),
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
			array( $this, 'render_field_bg_color' ),
			'mobile-floating-banner',
			'mfb_section_appearance'
		);

		add_settings_field(
			'mfb_text_color',
			__( 'Pill Text Color', 'mobile-floating-banner' ),
			array( $this, 'render_field_text_color' ),
			'mobile-floating-banner',
			'mfb_section_appearance'
		);

		add_settings_field(
			'mfb_icon_color',
			__( 'Icon Circle Color', 'mobile-floating-banner' ),
			array( $this, 'render_field_icon_color' ),
			'mobile-floating-banner',
			'mfb_section_appearance'
		);

		add_settings_field(
			'mfb_icon_text_color',
			__( 'Icon Symbol Color', 'mobile-floating-banner' ),
			array( $this, 'render_field_icon_text_color' ),
			'mobile-floating-banner',
			'mfb_section_appearance'
		);

		add_settings_field(
			'mfb_alignment',
			__( 'Content Alignment', 'mobile-floating-banner' ),
			array( $this, 'render_field_alignment' ),
			'mobile-floating-banner',
			'mfb_section_appearance'
		);

		// Section: Display Rules
		add_settings_section(
			'mfb_section_display',
			__( 'Display Rules', 'mobile-floating-banner' ),
			array( $this, 'render_section_display_description' ),
			'mobile-floating-banner'
		);

		add_settings_field(
			'mfb_show_on_mobile',
			__( 'Show on Mobile', 'mobile-floating-banner' ),
			array( $this, 'render_field_show_on_mobile' ),
			'mobile-floating-banner',
			'mfb_section_display'
		);

		add_settings_field(
			'mfb_show_on_tablet',
			__( 'Show on Tablet', 'mobile-floating-banner' ),
			array( $this, 'render_field_show_on_tablet' ),
			'mobile-floating-banner',
			'mfb_section_display'
		);

		add_settings_field(
			'mfb_show_on_desktop',
			__( 'Show on Desktop', 'mobile-floating-banner' ),
			array( $this, 'render_field_show_on_desktop' ),
			'mobile-floating-banner',
			'mfb_section_display'
		);
	}

	// -------------------------------------------------------------------------
	// Sanitizer
	// -------------------------------------------------------------------------

	public function sanitize_options( $input ) {
		$clean = array();

		$clean['phone_number'] = isset( $input['phone_number'] )
			? sanitize_text_field( $input['phone_number'] )
			: '';

		$clean['phone_link'] = isset( $input['phone_link'] )
			? esc_url_raw( $input['phone_link'] )
			: '';

		$clean['bg_color'] = isset( $input['bg_color'] )
			? ( sanitize_hex_color( $input['bg_color'] ) ?: '#aee6b0' )
			: '#aee6b0';

		$clean['text_color'] = isset( $input['text_color'] )
			? ( sanitize_hex_color( $input['text_color'] ) ?: '#1a5c1e' )
			: '#1a5c1e';

		$clean['icon_color'] = isset( $input['icon_color'] )
			? ( sanitize_hex_color( $input['icon_color'] ) ?: '#25a244' )
			: '#25a244';

		$clean['icon_text_color'] = isset( $input['icon_text_color'] )
			? ( sanitize_hex_color( $input['icon_text_color'] ) ?: '#ffffff' )
			: '#ffffff';

		$allowed_alignments = array( 'left', 'center', 'right' );
		$clean['alignment'] = ( isset( $input['alignment'] ) && in_array( $input['alignment'], $allowed_alignments, true ) )
			? $input['alignment']
			: 'left';

		$clean['show_on_mobile']  = ! empty( $input['show_on_mobile'] )  ? '1' : '0';
		$clean['show_on_tablet']  = ! empty( $input['show_on_tablet'] )  ? '1' : '0';
		$clean['show_on_desktop'] = ! empty( $input['show_on_desktop'] ) ? '1' : '0';

		return $clean;
	}

	// -------------------------------------------------------------------------
	// Field render callbacks
	// -------------------------------------------------------------------------

	public function render_field_phone_number() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[phone_number]" id="mfb_phone_number" value="%2$s" class="regular-text" placeholder="%3$s">
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['phone_number'] ),
			esc_attr__( 'e.g. +1 800 555 0100', 'mobile-floating-banner' ),
			esc_html__( 'Text shown next to the phone icon in the bar.', 'mobile-floating-banner' )
		);
	}

	public function render_field_phone_link() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[phone_link]" id="mfb_phone_link" value="%2$s" class="regular-text" placeholder="%3$s">
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['phone_link'] ),
			esc_attr__( 'tel:+18005550100', 'mobile-floating-banner' ),
			esc_html__( 'Full tel: URL. Include the country code without spaces.', 'mobile-floating-banner' )
		);
	}

	public function render_field_bg_color() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[bg_color]" id="mfb_bg_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['bg_color'] ),
			esc_attr( '#aee6b0' )
		);
	}

	public function render_field_text_color() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[text_color]" id="mfb_text_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['text_color'] ),
			esc_attr( '#1a5c1e' )
		);
	}

	public function render_field_icon_color() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[icon_color]" id="mfb_icon_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['icon_color'] ),
			esc_attr( '#25a244' ),
			esc_html__( 'Background color of the circular icon on the left.', 'mobile-floating-banner' )
		);
	}

	public function render_field_icon_text_color() {
		$opts = $this->get_options();
		printf(
			'<input type="text" name="%1$s[icon_text_color]" id="mfb_icon_text_color" value="%2$s" class="mfb-color-picker" data-default-color="%3$s">
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			esc_attr( $opts['icon_text_color'] ),
			esc_attr( '#ffffff' ),
			esc_html__( 'Color of the phone symbol inside the icon circle.', 'mobile-floating-banner' )
		);
	}

	public function render_field_alignment() {
		$opts    = $this->get_options();
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

	public function render_section_display_description() {
		echo '<p>' . esc_html__( 'Control which device sizes show the banner. Mobile is enabled by default. Breakpoints: mobile < 768px, tablet 768–1024px, desktop > 1024px.', 'mobile-floating-banner' ) . '</p>';
	}

	public function render_field_show_on_mobile() {
		$opts = $this->get_options();
		printf(
			'<label><input type="checkbox" name="%1$s[show_on_mobile]" id="mfb_show_on_mobile" value="1"%2$s> %3$s</label>
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			checked( '1', $opts['show_on_mobile'], false ),
			esc_html__( 'Enabled', 'mobile-floating-banner' ),
			esc_html__( 'Viewports narrower than 768px.', 'mobile-floating-banner' )
		);
	}

	public function render_field_show_on_tablet() {
		$opts = $this->get_options();
		printf(
			'<label><input type="checkbox" name="%1$s[show_on_tablet]" id="mfb_show_on_tablet" value="1"%2$s> %3$s</label>
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			checked( '1', $opts['show_on_tablet'], false ),
			esc_html__( 'Enabled', 'mobile-floating-banner' ),
			esc_html__( 'Viewports between 768px and 1024px.', 'mobile-floating-banner' )
		);
	}

	public function render_field_show_on_desktop() {
		$opts = $this->get_options();
		printf(
			'<label><input type="checkbox" name="%1$s[show_on_desktop]" id="mfb_show_on_desktop" value="1"%2$s> %3$s</label>
			 <p class="description">%4$s</p>',
			esc_attr( MFB_OPTION_NAME ),
			checked( '1', $opts['show_on_desktop'], false ),
			esc_html__( 'Enabled', 'mobile-floating-banner' ),
			esc_html__( 'Viewports wider than 1024px.', 'mobile-floating-banner' )
		);
	}
}
