<?php
/**
 * Google Calendar Events Main Class
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */


class Google_Calendar_Events {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	protected $version = '2.2.5';

	/**
	 * Unique identifier for the plugin.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'google-calendar-events';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	public $show_scripts = false;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {
		
		$this->includes();
		
		$old = get_option( 'gce_version' );
		
		if( version_compare( $old, $this->version, '<' ) ) {
			delete_option( 'gce_upgrade_has_run' );
		}
		
		if( false === get_option( 'gce_upgrade_has_run' ) ) {
			$this->upgrade();
		}
		
		$this->setup_constants();
		
		add_action( 'init', array( $this, 'enqueue_public_scripts' ) );
		add_action( 'init', array( $this, 'enqueue_public_styles' ) );
		
		
		// Load scripts when posts load so we know if we need to include them or not
		add_filter( 'the_posts', array( $this, 'load_scripts' ) );
		
		// Load plugin text domain
		$this->plugin_textdomain();
		
		add_action( 'wp_footer', array( $this, 'localize_main_script' ) );
	}
	
	public function localize_main_script() {
		
		if( $this->show_scripts ) {
			global $localize;

			wp_localize_script( GCE_PLUGIN_SLUG . '-public', 'gce_grid', $localize );

			wp_localize_script( GCE_PLUGIN_SLUG . '-public', 'gce', 
					array(
						'script_debug'  => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ),
						'ajaxurl'     => admin_url( 'admin-ajax.php' ),
						'loadingText' => __( 'Loading...', 'gce' ),
					) );
		}
	}
	
	public function load_scripts( $posts ) {
		
		global $gce_options;

		// Init enqueue flag.
		$do_enqueue = false;
		
		if ( isset( $gce_options['always_enqueue'] ) ) {

			$do_enqueue = true;

		} elseif ( ! empty( $posts ) ) {

			foreach ( $posts as $post ) {

				if ( ( strpos( $post->post_content, '[gcal' ) !== false ) || ( $post->post_type == 'gce_feed' ) ) {

					$do_enqueue = true;
					break;
				}
			}
		}

		if ( true == $do_enqueue ) {

			// Load CSS after checking to see if it is supposed to be disabled or not (based on settings)
			if( ! isset( $gce_options['disable_css'] ) ) {
				wp_enqueue_style( $this->plugin_slug . '-public' );
			}

			// Load JS
			wp_enqueue_script( $this->plugin_slug . '-public' );

			$this->show_scripts = true;
		}

		return $posts;
	}
	
	/**
	 * Load the upgrade file
	 * 
	 * @since 2.0.0
	 */
	public function upgrade() {
		include_once( 'includes/admin/upgrade.php' );
	}
	
	/**
	 * Setup public constants 
	 * 
	 * @since 2.0.0
	 */
	public function setup_constants() {
		if( ! defined( 'GCE_DIR' ) ) {
			define( 'GCE_DIR', dirname( __FILE__ ) );
		}
		
		if( ! defined( 'GCE_PLUGIN_SLUG' ) ) {
			define( 'GCE_PLUGIN_SLUG', $this->plugin_slug );
		}
	}
	
	/**
	 * Include all necessary files
	 * 
	 * @since 2.0.0
	 */
	public static function includes() {
		global $gce_options;
		
		// First include common files between admin and public
		include_once( 'includes/misc-functions.php' );
		include_once( 'includes/gce-feed-cpt.php' );
		include_once( 'includes/class-gce-display.php' );
		include_once( 'includes/class-gce-event.php' );
		include_once( 'includes/class-gce-feed.php' );
		include_once( 'includes/shortcodes.php' );
		include_once( 'views/widgets.php' );
		
		// Now include files specifically for public or admin
		if( is_admin() ) {
			// Admin includes
			include_once( 'includes/admin/admin-functions.php' );
		} else {
			// Public includes
		}
		
		// Setup our main settings options
		include_once( 'includes/register-settings.php' );
		
		$gce_options = gce_get_settings();
	}
	
	/**
	 * Load public facing scripts
	 * 
	 * @since 2.0.0
	 */
	public function enqueue_public_scripts() {
		// ImagesLoaded JS library recommended by qTip2.
		wp_register_script( $this->plugin_slug . '-images-loaded', plugins_url( 'js/imagesloaded.pkg.min.js', __FILE__ ), null, $this->version, true );
		wp_register_script( $this->plugin_slug . '-qtip', plugins_url( 'js/jquery.qtip.min.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-images-loaded' ), $this->version, true );
		wp_register_script( $this->plugin_slug . '-public', plugins_url( 'js/gce-script.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-qtip' ), $this->version, true );
	}
	
	/*
	 * Load public facing styles
	 * 
	 * @since 2.0.0
	 */
	public function enqueue_public_styles() {
		wp_register_style( $this->plugin_slug . '-qtip', plugins_url( 'css/jquery.qtip.min.css', __FILE__ ), array(), $this->version );
		wp_register_style( $this->plugin_slug . '-public', plugins_url( 'css/gce-style.css', __FILE__ ), array( $this->plugin_slug . '-qtip' ), $this->version );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    2.0.0
	 *
	 * @return    Plugin version variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}
	
	/**
	 * Return the plugin version.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_version() {
		return $this->version;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain(
			'gce',
			false,
			dirname( plugin_basename( GCE_MAIN_FILE ) ) . '/languages/'
		);
	}
}
