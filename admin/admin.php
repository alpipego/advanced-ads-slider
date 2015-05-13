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

	}
}
