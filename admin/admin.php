<?php
class Advanced_Ads_Slider_Admin {

	/**
	 * holds base class
	 *
	 * @var Advanced_Ads_Slider_Plugin
	 * @since 1.0.0
	 */
	protected $plugin;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin = Advanced_Ads_Slider_Plugin::get_instance();
		// add group options
		add_action( 'advanced-ads-group-form-options', array( $this, 'group_options' ) );
		add_action('advanced-ads-settings-init', array($this, 'settings_init'), 10, 1);
	}

	/**
	 * render group options for slider
	 *
	 * @param obj $group Advanced_Ads_Group
	 */
	public function group_options( Advanced_Ads_Group $group ){

		$delay = isset( $group->options['slider']['delay'] ) ? absint( $group->options['slider']['delay'] ) : 2000;

		include AAS_BASE_PATH . 'admin/views/group-options.php';
	}

	/**
	 * add settings to settings page
	 *
	 * @param string $hook settings page hook
	 * @since 1.0.3
	 */
	public function settings_init($hook) {

	    // don’t initiate if main plugin not loaded
	    if ( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
		    return;
	    }

	    // add license key field to license section
	    add_settings_field(
		'slider-license',
		__('Slider', AAT_SLUG),
		array($this, 'render_settings_license_callback'),
		'advanced-ads-settings-license-page',
		'advanced_ads_settings_license_section'
	    );
	}

	/**
	 * render license key section
	 *
	 * @since 1.0.3
	 */
	public function render_settings_license_callback(){
		$licenses = get_option(ADVADS_SLUG . '-licenses', array());
		$license_key = isset($licenses['slider']) ? $licenses['slider'] : '';
		$license_status = get_option($this->plugin->options_slug . '-license-status', false);
		$index = 'slider';
		$plugin_name = AAS_PLUGIN_NAME;
		$options_slug = $this->plugin->options_slug;

		// template in main plugin
		include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
	}
}
