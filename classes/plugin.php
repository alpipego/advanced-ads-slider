<?php

/*
 * load common and WordPress based resources
 *
 * @since 1.0.0
 */

class Advanced_Ads_Slider_Plugin {

        /**
	 *
	 * @var Advanced_Ads_Slider_Plugin
	 */
	protected static $instance;

	/**
	 * plugin options
	 *
	 * @var     array (if loaded)
	 */
	protected $options = false;

        /**
         * name of options in db
         *
         * @car     string
         */
        public $options_slug;


	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
	}

	/**
	 *
	 * @return Advanced_Ads_Slider_Plugin
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
		self::$instance = new self;
		}

		return self::$instance;
	}

        /**
         * load actions and filters
         *
         * @todo include more of the hooks used in public and admin class
         */
	public function wp_plugins_loaded() {
            // stop, if main plugin doesn’t exist
            if ( ! class_exists( 'Advanced_Ads', false ) ) {
                return ;
            }

            $this->options_slug =  ADVADS_SLUG . '-slider';
	}

        /**
         * load advanced ads settings
         */
        public function options(){
            // don’t initiate if main plugin not loaded
            if(!class_exists('Advanced_Ads')) return array();

            return Advanced_Ads::get_instance()->options();
        }
}

