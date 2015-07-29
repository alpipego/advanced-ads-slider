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

		add_filter( 'advanced-ads-group-output-array', array( $this, 'output_slider_markup'), 10, 2 );
        }

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function register_scripts(){
		// include file only if js method is enabled
		wp_enqueue_script( 'unslider-js', AAS_BASE_URL . 'public/assets/js/unslider.min.js', array('jquery'), AAS_VERSION );
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

	/**
	 * add extra output markup for slider group
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr $ad_content with extra markup
	 */
	public function output_slider_markup( array $ad_content, Advanced_Ads_Group $group ){

		if( count( $ad_content ) <= 1 || 'slider' !== $group->type ) {
		    return $ad_content;
		}

		$settings = array();
		if( isset( $group->options['slider']['delay'] ) ) {
		    $settings[] = 'delay: ' . absint( $group->options['slider']['delay'] );
		}
		$settings = implode( ', ', $settings );

		foreach( $ad_content as $_key => $_content ){
		    $ad_content[$_key] = '<li>' . $_content . '</li>';
		}

		$slider_id = 'advads-slider-' . mt_rand();
		$css = "<style>.advads-slider { position: relative; width: 100% !important; overflow: hidden; } "
			. ".advads-slider ul, .advads-slider ul { list-style: none; margin: 0 !important; padding: 0 !important; } "
			. ".advads-slider ul li { width: 100%; float: left; }</style>";
		$script = "<script>jQuery(function() { jQuery('#$slider_id').unslider({ $settings }); });</script>";

		array_unshift( $ad_content, '<div id="'. $slider_id.'" class="advads-slider"><ul>' );
		array_push( $ad_content, '</ul></div>' );
		array_push( $ad_content, $css );
		array_push( $ad_content, $script );

		return $ad_content;
	}
}
