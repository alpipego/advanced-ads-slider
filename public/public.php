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

		// add group type
		add_filter( 'advanced-ads-group-types', array( $this, 'add_group' ) );
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
	 * add slider group type
	 *
	 * @param arr $group_types existing group types
	 * @return arr $group_types group types with the new slider group
	 */
	public function add_group( array $group_types ){

	    $group_types['slider'] = array(
		    'title' => __( 'Ad Slider', AAS_SLUG ),
		    'description' => __( 'Display all ads as a slider', AAS_SLUG ),
	    );
	    return $group_types;
	}
}
