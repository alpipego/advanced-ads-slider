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
}
