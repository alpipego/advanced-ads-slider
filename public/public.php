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

		add_filter( 'advanced-ads-group-output-ad-ids', array( $this, 'output_ad_ids' ), 10, 4 );
        }

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function register_scripts(){
		// include file only if js method is enabled
		wp_enqueue_script( 'advads-slider-js', AAS_BASE_URL . 'public/assets/js/script.js', array(), AAS_VERSION );
		wp_enqueue_style( 'advads-slider-css', AAS_BASE_URL . 'public/assets/css/styles.css', array(), AAS_VERSION );
	}

	/**
	 * get ids from ads in the order they should be displayed
	 *
	 * @param arr $ordered_ad_ids ad ids in the order from the main plugin
	 * @param str $type group type
	 * @param arr $ads array with ad objects
	 * @param arr $weights array with ad weights
	 * @return arr $ad_ids
	 */
	public function output_ad_ids( $ordered_ad_ids, $type, $ads, $weights ){

	    // return order by weights if this is a slider
	    if( $type === 'slider' ){
		return array_keys($weights);
	    }

	    // return default
	    return $ordered_ad_ids;
	}
}
