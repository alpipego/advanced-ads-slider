<?php

/**
 * Class Advanced_Ads_Slider
 */
class Advanced_Ads_Slider {
	/**
	 * Initialize the plugin and styles.
	 */
	public function __construct() {

		// add js file to header
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_filter( 'advanced-ads-group-output-ad-ids', array( $this, 'output_ad_ids' ), 10, 5 );

		add_filter( 'advanced-ads-group-output-array', array( $this, 'output_slider_markup' ), 10, 2 );

		// manipulate number of ads that should be displayed in a group
		add_filter( 'advanced-ads-group-ad-count', array( $this, 'adjust_ad_group_number' ), 10, 2 );

		// add slider markup to passive cache-busting.
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'add_slider_markup_passive' ), 10, 3 );
	}

	/**
	 * Enqueue JS in footer.
	 */
	public function register_scripts() {
		$js_src  = AAS_BASE_URL . 'public/assets/js/unslider.min.js';
		$css_src = AAS_BASE_URL . 'public/assets/css/unslider.css';
		// Using a CDN to prevend encoding issues in certain cases.
		if ( defined( 'ADVANCED_ADS_SLIDER_USE_CDN' ) && ADVANCED_ADS_SLIDER_USE_CDN ) {
			$js_src  = 'https://cdnjs.cloudflare.com/ajax/libs/unslider/2.0.3/js/unslider-min.js';
			$css_src = 'https://cdnjs.cloudflare.com/ajax/libs/unslider/2.0.3/css/unslider.css';
		}

		wp_enqueue_script( 'unslider-js', $js_src, array( 'jquery', ADVADS_SLUG . '-advanced-js' ), AAS_VERSION, true );
		wp_enqueue_style( 'unslider-css', $css_src, array(), AAS_VERSION );
		wp_enqueue_style( 'slider-css', AAS_BASE_URL . 'public/assets/css/slider.css', array(), AAS_VERSION );

		// scripts for swipe feature
		if ( ! defined( 'ADVANCED_ADS_NO_SWIPE' ) ) {
			wp_enqueue_script( 'unslider-move-js', AAS_BASE_URL . 'public/assets/js/jquery.event.move.js', array( 'jquery', 'unslider-js' ), AAS_VERSION, true );
			wp_enqueue_script( 'unslider-swipe-js', AAS_BASE_URL . 'public/assets/js/jquery.event.swipe.js', array( 'jquery', 'unslider-js' ), AAS_VERSION, true );
		}
	}
	
	/**
	 * get ids from ads in the order they should be displayed
	 *
	 * @param arr $ordered_ad_ids ad ids in the order from the main plugin
	 * @param str $type group type
	 * @param arr $ads array with ad objects
	 * @param arr $weights array with ad weights
	 * @param arr $group Advanced_Ads_Group Object
	 * @return arr $ad_ids
	 */
	public function output_ad_ids( $ordered_ad_ids, $type, $ads, $weights, Advanced_Ads_Group $group ){
	    // return order by weights if this is a slider
	    if( $type === 'slider' ){
			// shuffle if this was set or we are on AMP
			if( isset($group->options['slider']['random'] )
				|| ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) ) {
				return $group->shuffle_ads($ads, $weights);
			} else {
				return array_keys($weights);
			}
	    }

	    // return default
	    return $ordered_ad_ids;
	}

	/**
	 * adjust the ad group number if the ad type is a slider
	 *
	 * @param int $ad_count
	 * @param obj $group Advanced_Ads_Group
	 * @return int $ad_count
	 */
	public function adjust_ad_group_number( $ad_count = 0, $group ){

	    // show all ads for slider, but only, if this is not an AMP page
	    if( $group->type === 'slider' && 
		    ( ! function_exists( 'advads_is_amp' ) || ! advads_is_amp() ) ){
		    return 'all';
	    }

	    return $ad_count;
	}

	/**
	 * add extra output markup for slider group
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr $ad_content with extra markup
	 */
	public function output_slider_markup( array $ad_content, Advanced_Ads_Group $group ){

		// return if we are on AMP
		if( function_exists( 'advads_is_amp' ) && advads_is_amp() ){
		    return $ad_content;
		}
	    
		if( count( $ad_content ) <= 1 || 'slider' !== $group->type ) {
		    return $ad_content;
		}

		$markup = $this->get_slider_markup( $group );

		foreach ( $ad_content as $index => $content ) {
			// this ad is encoded
			if ( strpos( $content, 'data-tcf="waiting-for-consent"' ) ) {
				$ad_content[ $index ] = sprintf( $markup['encoded'], $content );

				continue;
			}
			$ad_content[ $index ] = sprintf( $markup['decoded'], $content );
		}

		array_unshift( $ad_content, $markup['before'] );
		array_push( $ad_content, $markup['after'] );

		return $ad_content;
	}

	/**
	 * Get markup to inject around each slide and around set of slides.
	 *
	 * @param Advanced_Ads_Group $group The ad group to use for slider.
	 *
	 * @return array
	 */
	public function get_slider_markup( Advanced_Ads_Group $group ) {
		static $count = 0;

		$slider_options = self::get_slider_options( $group );
		$script         = <<<'PRIVACY'
<script>
document.addEventListener('advanced_ads_privacy', function (event) {
	if (event.detail.previousState !== 'unknown') {
		return;
	}
	if (event.detail.state === 'accepted' || event.detail.state === 'not_needed') {
		document.querySelectorAll('.custom-slider li.encoded').forEach(function(el) {
			var waitingForConsent = el.querySelector('script[data-tcf="waiting-for-consent"]');
			if (waitingForConsent !== null) {
				advads.privacy.decode_ad(waitingForConsent);
			}
			el.classList.remove('encoded');
		});
	}
	
	var %1$s = jQuery('#%2$s'),
		%3$s = %1$s.find('%4$s');

	if (%3$s.length < 2) {
		return;
	}

	%1$s.on('unslider.ready', function() {
			%3$s.css('display', 'block');
		}).on('mouseover', function() {
			%1$s.unslider('stop');
		}).on('mouseout', function() {
			%1$s.unslider('start');
		});

	%1$s.unslider({%5$s, selectors: {container: "ul:first", slides: "%4$s"}});
});
</script>
PRIVACY;

		$slider_var = '$' . preg_replace( '/[^\da-z]/i', '', $slider_options['init_class'] );
		$slider_id  = $slider_options['slider_id'] . ( ++ $count > 1 ? '-' . $count : '' );
		$script     = sprintf(
			$script,
			$slider_var,
			$slider_id,
			$slider_var . '_slides',
			'li:not(.encoded)',
			$slider_options['settings']
		);

		return array(
			'before'  => sprintf(
				'<div id="%1$s" class="custom-slider %2$s %3$sslider"><ul>',
				$slider_id,
				$slider_options['init_class'],
				$slider_options['prefix']
			),
			'after'   => '</ul></div>' . $script,
			'decoded' => '<li>%s</li>',
			'encoded' => '<li class="encoded">%s</li>',
			'min_ads' => 2,
		);
	}

	/**
	 * Add slider markup to passive cache-busting.
	 *
	 * @param arr $group_data
	 * @param obj $group Advanced_Ads_Group
	 * @param string $element_id
	 */
	public function add_slider_markup_passive( $group_data, Advanced_Ads_Group $group, $element_id ) {
		if ( $element_id && 'slider' === $group->type  ) {
			$group_data['group_wrap'][] = $this->get_slider_markup( $group );
		}

		return $group_data;
	}

    /**
     * return slider options
     *
     * @param obj $group Advanced_Ads_Group
     * @return array that contains slider options
     */
    public static function get_slider_options( Advanced_Ads_Group $group ) {
        $settings = array();
        if ( isset( $group->options['slider']['delay'] ) ) {
            $settings['delay'] = absint( $group->options['slider']['delay'] );
            $settings['autoplay'] = 'true';
            $settings['nav'] = 'false';
            $settings['arrows'] = 'false';
            $settings['infinite'] = 'true';
        }

        $settings = apply_filters( 'advanced-ads-slider-settings', $settings );

        // merge option keys and values in preparation for the option string
	$setting_attributes = array_map( array( 'Advanced_Ads_Slider', 'map_settings' ), array_values($settings), array_keys($settings));

        $settings = implode( ', ', $setting_attributes );

        $prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
        $slider_id = $prefix . 'slider-' . $group->id;
        $slider_init_class = $prefix . 'slider-' . mt_rand();

        return array(
            'prefix' => $prefix,
            'slider_id' => $slider_id,
            'init_class' => $slider_init_class,
            'settings' => $settings // slider init options
        );
    }
    
    /**
     * helper function for array_map, see above
     * needed for php prior 5.3
     */
    public static function map_settings( $value, $key ){
	
	return $key.':'.$value.'';
	
    }

}
