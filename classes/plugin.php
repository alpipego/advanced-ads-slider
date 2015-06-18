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
		// add group type
		add_filter( 'advanced-ads-group-types', array( $this, 'add_group_type' ) );


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

	    // register plugin for auto updates
	    if( is_admin() ){
		    add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
	    }
	}

        /**
         * load advanced ads settings
         */
        public function options(){
            // don’t initiate if main plugin not loaded
            if(!class_exists('Advanced_Ads')) return array();

            return Advanced_Ads::get_instance()->options();
        }

	/**
	 * add slider group type
	 *
	 * @param arr $group_types existing group types
	 * @return arr $group_types group types with the new slider group
	 */
	public function add_group_type( array $group_types ){

	    $group_types['slider'] = array(
		    'title' => __( 'Ad Slider', AAS_SLUG ),
		    'description' => __( 'Display all ads as a slider', AAS_SLUG ),
	    );
	    return $group_types;
	}

	/**
	 * register plugin for the auto updater in the base plugin
	 *
	 * @param arr $plugins plugin that are already registered for auto updates
	 * @return arr $plugins
	 */
	public function register_auto_updater( array $plugins = array() ){

		$plugins['slider'] = array(
			'name' => AAS_PLUGIN_NAME,
			'version' => AAS_VERSION,
			'path' => AAS_BASE_PATH . 'slider.php',
			'options_slug' => $this->options_slug,
		);
		return $plugins;
	}
}

