<?php

class Advanced_Ads_Slider {

        /**
         * holds plugin base class
         *
         * @var Advanced_Ads_Slider_Plugin
         * @since 1.0.0
         */
        protected $plugin;

        /**
         * Initialize the plugin
         * and styles.
         *
         * @since     1.0.0
         */
        public function __construct() {

                $this->plugin = Advanced_Ads_Slider_Plugin::get_instance();

                // add js file to header
                add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
        }

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function register_scripts(){
		// include file only if js method is enabled
		wp_enqueue_script( 'advads-slider-js', AAR_BASE_URL . 'public/assets/js/script.js', array(), AAS_VERSION );
		wp_enqueue_style( 'advads-slider-css', AAR_BASE_URL . 'public/assets/css/styles.css', array(), AAS_VERSION );
	}
}
