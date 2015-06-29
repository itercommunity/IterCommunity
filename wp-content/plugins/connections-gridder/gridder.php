<?php
/**
 * A gridlike layout template for the Connections Business Directory plugin.
 *
 * @package   Connections Gridder
 * @category  Template
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      http://connections-pro.com
 * @copyright 2014 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Gridder - Template
 * Plugin URI:        http://connections-pro.com
 * Description:       Template for the Connections Business Directory
 * Version:           1.0.1
 * Author:            Steven A. Zahm
 * Author URI:        http://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cnt_gridder
 * Domain Path:       /languages
 */


if ( ! class_exists( 'CNT_Gridder' ) ) {

	class CNT_Gridder {

		const VERSION = '1.0.1';

		/**
		 * Stores a copy of the shortcode $atts for use throughout the class.
		 *
		 * @access private
		 * @since 1.0
		 * @var (array)
		 */
		private static $atts;

		/**
		 * Stores an initialized instance of cnTemplate.
		 *
		 * @access private
		 * @since 1.0
		 * @var (object)
		 */
		private static $template;

		/**
		 * Setup the template.
		 *
		 * @access public
		 * @since 1.0
		 * @param (object) $template An initialized instance of the cnTemplate class.
		 * @return (void)
		 */
		public function __construct( $template ) {

			self::$template = $template;

			// Enqueue the required JS file.
			add_filter( 'cn_template_required_js-' . $template->getSlug(), array( __CLASS__, 'requiredJS' ) );
			// add_action( 'cn_template_enqueue_js-' . $template->getSlug(), array( __CLASS__, 'enqueueJS' ) );

			// Update the permitted shortcode attribute the user may use and override the template defaults as needed.
			add_filter( 'cn_list_atts_permitted-' . $template->getSlug(), array( __CLASS__, 'initShortcodeAtts') );
			add_filter( 'cn_list_atts-' . $template->getSlug(), array( __CLASS__, 'initTemplateOptions') );
		}

		public static function register() {

			self::defineConstants();

			$atts = array(
				'class'       => 'CNT_Gridder',
				'name'        => 'Gridder',
				'type'        => 'all',
				'version'     => self::VERSION,
				'author'      => 'Steven A. Zahm',
				'authorURL'   => 'connections-pro.com',
				'description' => __( 'A grid style template.', 'cnt_gridder'),
				'path'        => CNT_GRIDDER_BASE_PATH,
				'url'         => CNT_GRIDDER_BASE_URL,
				'thumbnail'   => ''
				);

			cnTemplateFactory::register( $atts );

			// License and Updater.
			if ( class_exists( 'cnLicense' ) ) {

				new cnLicense( __FILE__, 'Gridder', self::VERSION, 'Steven A. Zahm' );
			}

			add_action( 'init', array( __CLASS__ , 'loadTextdomain' ) );

			// Register the CSS and JS files.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'registerScripts' ) );
		}

		/**
		 * Define the constants.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private static function defineConstants() {

			define( 'CNT_GRIDDER_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'CNT_GRIDDER_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'CNT_GRIDDER_BASE_PATH', plugin_dir_path( __FILE__ ) );
			define( 'CNT_GRIDDER_BASE_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 *
		 * @return (void)
		 */
		public static function loadTextdomain() {

			// Plugin's unique textdomain string.
			$textdomain = 'cnt_gridder';

			// Filter for the plugin languages folder.
			$languagesDirectory = apply_filters( 'cnt_gridder_lang_dir', CNT_GRIDDER_DIR_NAME . '/languages/' );

			// The 'plugin_locale' filter is also used by default in load_plugin_textdomain().
			$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

			// Filter for WordPress languages directory.
			$wpLanguagesDirectory = apply_filters(
				'cnt_gridder_wp_lang_dir',
				WP_LANG_DIR . '/connections-gridder/' . sprintf( '%1$s-%2$s.mo', $textdomain, $locale )
			);

			// Translations: First, look in WordPress' "languages" folder = custom & update-safe!
			load_textdomain( $textdomain, $wpLanguagesDirectory );

			// Translations: Secondly, look in plugin's "languages" folder = default.
			load_plugin_textdomain( $textdomain, FALSE, $languagesDirectory );
		}

		public static function registerScripts() {

			// wp_register_script( 'jquery-gridder', CNT_GRIDDER_BASE_URL . 'vendor/gridder/js/gridder.js', array( 'jquery' ), '1.1' );
			wp_enqueue_style( 'dashicons' );
		}

		/**
		 *
		 *
		 * @access private
		 * @since  1.0
		 * @return array
		 */
		public static function requiredJS( $required ) {

			$required[] = 'jquery-chosen';
			$required[] = 'jquery-gomap';

			return $required;
		}

		public static function enqueueJS() {

			wp_enqueue_script( "cnt-{self::$template->getSlug()}", CNT_GRIDDER_BASE_URL . 'assets/js/gridder.js', array( 'jquery-gridder' ), self::$template->getVersion(), TRUE );
		}


		/**
		 * Initiate the permitted template shortcode options and load the default values.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (array)  $permittedAtts The shortcode $atts array.
		 * @return (array)
		 */
		public static function initShortcodeAtts( $permittedAtts = array() ) {

			// Grab an instance of the Connections object.
			$instance = Connections_Directory();

			$addressLabel = $instance->options->getDefaultAddressValues();
			$phoneLabel   = $instance->options->getDefaultPhoneNumberValues();
			$emailLabel   = $instance->options->getDefaultEmailValues();

			$permittedAtts['enable_search']                   = FALSE;

			$permittedAtts['enable_pagination']               = FALSE;
			$permittedAtts['page_limit']                      = 20;
			$permittedAtts['pagination_position']             = 'after';

			$permittedAtts['enable_category_select']          = FALSE;
			$permittedAtts['show_empty_categories']           = FALSE;
			$permittedAtts['show_category_count']             = FALSE;
			$permittedAtts['enable_category_by_root_parent']  = FALSE;
			$permittedAtts['enable_category_multi_select']    = FALSE;
			$permittedAtts['enable_category_group_by_parent'] = FALSE;

			$permittedAtts['enable_map']                      = TRUE;
			$permittedAtts['enable_bio']                      = TRUE;
			$permittedAtts['enable_bio_head']                 = TRUE;
			$permittedAtts['enable_note']                     = TRUE;
			$permittedAtts['enable_note_head']                = TRUE;

			$permittedAtts['show_title']                      = TRUE;
			$permittedAtts['show_org']                        = TRUE;
			$permittedAtts['show_contact_name']               = TRUE;
			$permittedAtts['show_family']                     = TRUE;
			$permittedAtts['show_addresses']                  = TRUE;
			$permittedAtts['show_phone_numbers']              = TRUE;
			$permittedAtts['show_email']                      = TRUE;
			$permittedAtts['show_im']                         = TRUE;
			$permittedAtts['show_social_media']               = TRUE;
			$permittedAtts['show_dates']                      = TRUE;
			$permittedAtts['show_links']                      = TRUE;
			$permittedAtts['show_profile_link']               = TRUE;

			$permittedAtts['address_types']                   = NULL;
			$permittedAtts['phone_types']                     = NULL;
			$permittedAtts['email_types']                     = NULL;
			$permittedAtts['date_types']                      = NULL;
			$permittedAtts['link_types']                      = NULL;

			$permittedAtts['image']                           = 'photo';
			$permittedAtts['image_width']                     = 300;
			$permittedAtts['image_height']                    = 300;
			$permittedAtts['image_opacity']                   = NULL;

			$permittedAtts['image_single']                    = 'photo';
			$permittedAtts['image_single_width']              = cnSettingsAPI::get( 'connections', 'image_large', 'width' );
			$permittedAtts['image_single_height']             = cnSettingsAPI::get( 'connections', 'image_large', 'height' );
			$permittedAtts['image_single_fallback']           = 'block';

			// $permittedAtts['map_type']                        = 'm';
			$permittedAtts['map_zoom']                        = 13;
			$permittedAtts['map_frame_height']                = 400;

			$permittedAtts['str_select']                      = __( 'Select Category', 'cnt_gridder' );
			$permittedAtts['str_select_all']                  = __( 'Show All Categories', 'cnt_gridder' );
			$permittedAtts['str_image_single']                = __( 'No Photo Available', 'cnt_gridder' );
			$permittedAtts['str_bio_head']                    = __( 'Biography', 'cnt_gridder' );
			$permittedAtts['str_note_head']                   = __( 'Notes', 'cnt_gridder' );
			$permittedAtts['str_contact']                     = __( 'Contact', 'cnt_gridder' );
			$permittedAtts['str_home_addr']                   = $addressLabel['home'];
			$permittedAtts['str_work_addr']                   = $addressLabel['work'];
			$permittedAtts['str_school_addr']                 = $addressLabel['school'];
			$permittedAtts['str_other_addr']                  = $addressLabel['other'];
			$permittedAtts['str_home_phone']                  = $phoneLabel['homephone'];
			$permittedAtts['str_home_fax']                    = $phoneLabel['homefax'];
			$permittedAtts['str_cell_phone']                  = $phoneLabel['cellphone'];
			$permittedAtts['str_work_phone']                  = $phoneLabel['workphone'];
			$permittedAtts['str_work_fax']                    = $phoneLabel['workfax'];
			$permittedAtts['str_personal_email']              = $emailLabel['personal'];
			$permittedAtts['str_work_email']                  = $emailLabel['work'];

			$permittedAtts['name_format']                     = '';
			$permittedAtts['contact_name_format']             = '';
			$permittedAtts['addr_format']                     = '';
			$permittedAtts['email_format']                    = '';
			$permittedAtts['phone_format']                    = '';
			$permittedAtts['link_format']                     = '';
			$permittedAtts['date_format']                     = '';

			$permittedAtts['overlay']                         = 'static'; // Valid Options: hover, static, none

			$permittedAtts['color']                           = '#000';
			$permittedAtts['background_color']                = '#f3f3f3';

			$permittedAtts['excerpt_length']                  = 55;
			$permittedAtts['excerpt_more']                    = '&hellip;';

			return $permittedAtts;
		}

		/**
		 * Initiate the template options using the user supplied shortcode option values.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (array)  $atts The shortcode $atts array.
		 * @return (array)
		 */
		public static function initTemplateOptions( $atts ) {
			global $connectionsROT13;

			remove_filter( 'cn_output_email_addresses', array( $connectionsROT13, 'outputROT13' ) );

			// Because the shortcode option values are treated as strings some of the values have to converted to boolean.
			cnFormatting::toBoolean( $atts['enable_search'] );
			cnFormatting::toBoolean( $atts['enable_pagination'] );
			cnFormatting::toBoolean( $atts['enable_category_select'] );
			cnFormatting::toBoolean( $atts['show_empty_categories'] );
			cnFormatting::toBoolean( $atts['show_category_count'] );
			cnFormatting::toBoolean( $atts['enable_category_by_root_parent'] );
			cnFormatting::toBoolean( $atts['enable_category_multi_select'] );
			cnFormatting::toBoolean( $atts['enable_category_group_by_parent'] );
			cnFormatting::toBoolean( $atts['enable_map'] );
			cnFormatting::toBoolean( $atts['enable_bio'] );
			cnFormatting::toBoolean( $atts['enable_bio_head']);
			cnFormatting::toBoolean( $atts['enable_note'] );
			cnFormatting::toBoolean( $atts['enable_note_head'] );

			cnFormatting::toBoolean( $atts['show_title'] );
			cnFormatting::toBoolean( $atts['show_org'] );
			cnFormatting::toBoolean( $atts['show_contact_name'] );
			cnFormatting::toBoolean( $atts['show_family'] );
			cnFormatting::toBoolean( $atts['show_addresses'] );
			cnFormatting::toBoolean( $atts['show_phone_numbers'] );
			cnFormatting::toBoolean( $atts['show_email'] );
			cnFormatting::toBoolean( $atts['show_im'] );
			cnFormatting::toBoolean( $atts['show_social_media'] );
			cnFormatting::toBoolean( $atts['show_dates'] );
			cnFormatting::toBoolean( $atts['show_links'] );
			cnFormatting::toBoolean( $atts['show_profile_link'] );

			cnFormatting::toBoolean( $atts['background_gradient'] );

			// Set the entry card width and map iframe width defaults
			if ( empty( $atts['width'] ) ) {
				$atts['map_frame_width'] = NULL;
			} else {
				$width = get_query_var( 'cn-entry-slug' ) ? 16 : 50;
				$atts['map_frame_width'] = $atts['width'] - $width;
			}

			// If displaying a single entry, no need to display category select, search and pagination.
			if ( get_query_var( 'cn-entry-slug' ) ) {
				$atts['enable_search']          = FALSE;
				$atts['enable_pagination']      = FALSE;
				$atts['enable_category_select'] = FALSE;
			}

			add_filter( 'cn_phone_number' , array( __CLASS__, 'phoneLabels') );
			add_filter( 'cn_email_address' , array( __CLASS__, 'emailLabels') );
			add_filter( 'cn_address' , array( __CLASS__, 'addressLabels') );

			// Start the form.
			add_action( 'cn_action_list_before-' . self::$template->getSlug() , array( __CLASS__, 'formOpen'), -1 );

			// Close the form
			add_action( 'cn_action_list_before-' . self::$template->getSlug() , array( __CLASS__, 'formClose'), 99999 );

			// If search is enabled, add the appropiate filters.
			if ( $atts['enable_search'] ) {
				add_filter( 'cn_list_retrieve_atts-' . self::$template->getSlug() , array( __CLASS__, 'limitList'), 10 );
				add_action( 'cn_action_list_before-' . self::$template->getSlug() , array( __CLASS__, 'searchForm') , 1 );
			}

			// If pagination is enabled add the appropiate filters.
			if ( $atts['enable_pagination'] ) {
				add_filter( 'cn_list_retrieve_atts-' . self::$template->getSlug() , array( __CLASS__, 'limitList'), 10 );
				add_action( 'cn_action_list_' . $atts['pagination_position'] . '-' . self::$template->getSlug() , array( __CLASS__, 'listPages') );
			}

			// If the category select/filter feature is enabled, add the appropiate filters.
			if ( $atts['enable_category_select'] ) {
				add_filter( 'cn_list_retrieve_atts-' . self::$template->getSlug() , array( __CLASS__, 'setCategory') );
				add_action( 'cn_action_list_before' . '-' . self::$template->getSlug() , array( __CLASS__, 'categorySelect') , 5 );
			}

			// These filters should not be applied when viewing a single entry.
			if ( ! get_query_var( 'cn-entry-slug' ) ) {

				add_filter( 'cn_list_row_class-' . self::$template->getSlug(), array( __CLASS__, 'addListRowClass' ) );

			} else {

				add_filter( 'cn_list_body_class-' . self::$template->getSlug(), array( __CLASS__, 'addListBodyClass' ) );
			}

			// Ensure valid $atts['overlay'] value is set.
			$atts['overlay'] = strtolower( $atts['overlay'] );

			if ( ! in_array( $atts['overlay'], array( 'hover', 'static', 'none' ) ) ) $atts['overlay'] = 'hover';

			// Ensure valid hashed HEX color.
			$atts['color']            = cnFormatting::maybeHashHEXColor( $atts['color'] );
			$atts['background_color'] = cnFormatting::maybeHashHEXColor( $atts['background_color'] );

			// Ensure valid opacity.
			if ( filter_var( (float) $atts['image_opacity'], FILTER_VALIDATE_FLOAT ) === FALSE ) {

				$atts['image_opacity'] = NULL;
			}

			// For these off so Gridder markup is not broken.
			$atts['show_alphahead']    = FALSE;
			$atts['repeat_alphaindex'] = FALSE;

			// Store a copy of the shortcode $atts to be used in other class methods.
			self::$atts = $atts;

			return $atts;
		}

		public static function addListBodyClass( $class ) {

			$class[] = 'cn-entry-single';

			return $class;
		}

		public static function addListRowClass( $class ) {

			$class[] = 'cn-gridder-item';

			return $class;
		}

		/**
		 * Alter the Address Labels.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (object) $data
		 * @return (object)
		 */
		public static function addressLabels( $data ) {

			switch ( $data->type ) {
				case 'home':
					$data->name = self::$atts['str_home_addr'];
					break;
				case 'work':
					$data->name = self::$atts['str_work_addr'];
					break;
				case 'school':
					$data->name = self::$atts['str_school_addr'];
					break;
				case 'other':
					$data->name = self::$atts['str_other_addr'];
					break;
			}

			return $data;
		}

		/**
		 * Alter the Phone Labels.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (object) $data
		 * @return (object)
		 */
		public static function phoneLabels( $data ) {

			switch ( $data->type ) {
				case 'homephone':
					$data->name = self::$atts['str_home_phone'];
					break;
				case 'homefax':
					$data->name = self::$atts['str_home_fax'];
					break;
				case 'cellphone':
					$data->name = self::$atts['str_cell_phone'];
					break;
				case 'workphone':
					$data->name = self::$atts['str_work_phone'];
					break;
				case 'workfax':
					$data->name = self::$atts['str_work_fax'];
					break;
			}

			return $data;
		}

		/**
		 * Alter the Email Labels.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (object) $data
		 * @return (object)
		 */
		public static function emailLabels( $data ) {

			switch ( $data->type ) {
				case 'personal':
					$data->name = self::$atts['str_personal_email'];
					break;
				case 'work':
					$data->name = self::$atts['str_work_email'];
					break;

				default:
					$data->name = 'Email';
				break;
			}

			return $data;
		}

		/**
		 * Limit the returned results.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (array) $atts The shortcode $atts array.
		 * @return (array)
		 */
		public static function limitList( $atts ) {

			// $atts['limit'] = $this->pageLimit; // Page Limit
			$atts['limit'] = empty( $atts['limit'] ) ? $atts['page_limit'] : $atts['limit'];

			return $atts;
		}

		/**
		 * Echo the form beginning.
		 *
		 * @access private
		 * @since  1.0
		 * @return (void)
		 */
		public static function formOpen( $atts ) {

		    cnTemplatePart::formOpen( $atts );
		}

		/**
		 * Echo the form ending.
		 *
		 * @access private
		 * @since  1.0
		 * @return (void)
		 */
		public static function formClose() {

		    cnTemplatePart::formClose();
		}

		/**
		 * Output the search input fields.
		 *
		 * @access private
		 * @since  1.0
		 * @return (void)
		 */
		public static function searchForm() {

			cnTemplatePart::search();
		}

		/**
		 * Output the pagination control.
		 *
		 * @access private
		 * @since  1.0
		 * @return (void)
		 */
		public static function listPages() {

			cnTemplatePart::pagination( array( 'limit' => self::$atts['page_limit'] ) );

		}

		/**
		 * Outputs the category select list.
		 *
		 * @access private
		 * @since  1.0
		 * @return (void)
		 */
		public static function categorySelect() {

			$atts = array(
				'default'    => self::$atts['str_select'],
				'select_all' => self::$atts['str_select_all'],
				'type'       => self::$atts['enable_category_multi_select'] ? 'multiselect' : 'select',
				'group'      => self::$atts['enable_category_group_by_parent'],
				'show_count' => self::$atts['show_category_count'],
				'show_empty' => self::$atts['show_empty_categories'],
				'parent_id'  => self::$atts['enable_category_by_root_parent'] ? self::$atts['category'] : array(),
				'exclude'    => self::$atts['exclude_category'],
				);

			cnTemplatePart::category( $atts );
		}

		/**
		 * Alters the shortcode attribute values before the query is processed.
		 *
		 * @access private
		 * @since  1.0
		 * @param  (array)  $atts The shortcode $atts array.
		 * @return (array)
		 */
		public static function setCategory( $atts ) {

			if ( $atts['enable_category_multi_select'] ) {

				if ( get_query_var('cn-cat') ) $atts['category_in'] = get_query_var('cn-cat');
				remove_query_arg( 'cn-cat' );

			}

			return $atts;
		}

		public static function excerptLength( $length ) {

			return absint( self::$atts['excerpt_length'] );
		}

	}

	// Register the template.
	add_action( 'cn_register_template', array( 'CNT_Gridder', 'register' ) );
}
