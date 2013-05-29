<?php
/*
Plugin Name: Easy Digital Downloads - Coupon Importer
Plugin URI: http://easydigitaldownloads.com/chris-is-amazing
Description: Adds the ability to import coupons via a CSV file.
Author: Chris Christoff
Author URI: http://www.chriscct7.com
Version: 1.0
*/

class EDD_CI {

	private static $instance;


	/**
	 * Get active object instance
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @static
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new EDD_CI();

		return self::$instance;
	}

	/**
	 * Class constructor.  Includes constants, includes and init method.
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		define( 'EDD_CI_STORE_API_URL', 'https://easydigitaldownloads.com' );
		define( 'EDD_CI_PRODUCT_NAME', 'Coupon Importer' );
		define( 'EDD_CI_VERSION', '1.0' );
		// plugin folder url
		if ( !defined( 'EDD_COUPON_IMPORT_URL' ) ) {
			define( 'EDD_COUPON_IMPORT_URL', plugin_dir_url( __FILE__ ) );
		}
		// plugin folder path
		if ( !defined( 'EDD_COUPON_IMPORT_DIR' ) ) {
			define( 'EDD_COUPON_IMPORT_DIR', plugin_dir_path( __FILE__ ) );
		}
		// plugin root file
		if ( !defined( 'EDD_COUPON_IMPORT_FILE' ) ) {
			define( 'EDD_COUPON_IMPORT_FILE', __FILE__ );
		}
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater
			include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
		}

		$this->includes();
		$this->init();

	}


	/**
	 * Include our extra files
	 *
	 * @since 1.0
	 *
	 * @access private
	 * @return void
	 */
	private function includes() {

		if( is_admin() ) {
			include dirname( __FILE__ ) . '/includes/admin-actions.php';
			include dirname( __FILE__ ) . '/includes/admin-page.php';
			include dirname( __FILE__ ) . '/includes/import-functions.php';
		}

	}


	/**
	 * Run action and filter hooks.
	 *
	 * @since 1.0
	 *
	 * @access private
	 * @return void
	 */
	private function init() {

		if( ! class_exists( 'Easy_Digital_Downloads' ) )
			return; // EDD not present

		global $edd_options;

		// internationalization
		add_action( 'init', array( $this, 'textdomain' ) );

		// Register the global
		global $edd_import_coupon_file_columns;
        $edd_import_coupon_file_columns = array(
             "0" => "A",
            "1" => "B",
            "2" => "C",
            "3" => "D",
            "4" => "E",
            "5" => "F",
            "6" => "G",
            "7" => "H",
            "8" => "I",
            "9" => "J",
            "10" => "K",
            "11" => "L",
            "12" => "M",
            "13" => "N",
            "14" => "O",
            "15" => "P",
            "16" => "Q",
            "17" => "R",
            "18" => "S",
            "19" => "T",
            "20" => "U",
            "21" => "V",
            "22" => "W",
            "23" => "X",
            "24" => "Y",
            "25" => "Z",
            "26" => "AA",
            "27" => "BB",
            "28" => "CC",
            "29" => "DD",
            "30" => "EE",
            "31" => "FF",
            "32" => "GG",
            "33" => "HH",
            "34" => "II",
            "35" => "JJ",
            "36" => "KK",
            "37" => "LL",
            "38" => "MM",
            "39" => "NN",
            "40" => "OO",
            "41" => "PP",
            "42" => "QQ",
            "43" => "RR",
            "44" => "SS",
            "45" => "TT",
            "46" => "UU",
            "47" => "VV",
            "48" => "WW",
            "49" => "XX",
            "50" => "YY",
            "51" => "ZZ" 
        );

		// register our license key settings
		add_filter( 'edd_settings_misc', array( $this, 'settings' ), 1 );

		// activate license key on settings save
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );

		// auto updater

		// retrieve our license key from the DB
		$edd_ci_license_key = isset( $edd_options['edd_ci_license_key'] ) ? trim( $edd_options['edd_ci_license_key'] ) : '';

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( EDD_CI_STORE_API_URL, __FILE__, array(
				'version' 	=> EDD_CI_VERSION, 		// current version number
				'license' 	=> $edd_ci_license_key, // license key (used get_option above to retrieve from DB)
				'item_name' => EDD_CI_PRODUCT_NAME, // name of this plugin
				'author' 	=> 'Chris Christoff'  // author of this plugin
			)
		);

	}


	/**
	 * Load plugin text domain
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @return void
	 */
	public static function textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'edd_ci_lang_directory', $lang_dir );

		// Load the translations
		load_plugin_textdomain( 'edd-ci', false, $lang_dir );

	}


	/**
	 * Add our extension settings
	 *
	 * @since 1.0
	 * @todo STDS for the columns
	 * @access public
	 * @return array
	 */
	public function settings( $settings ) {
	global $edd_import_coupon_file_columns;
	$ci_settings = array(
	array(
					'id'   => 'edd_ci_settings',
					'name' => '<strong>' . __( 'Coupon Importer', 'edd' ) . '</strong>',
					'desc' => '',
					'type' => 'header',
					'size' => 'regular'
		),

	array(
					'id'   => 'edd_ci_settings_license_key',
					'name' => __('License Key', 'edd'),
					'desc' => __('Enter your license for EDD Coupon Importer to receive automatic upgrades', 'edd'),
					'type' => 'license_key',
					'size' => 'regular'
					'options' => array( 'is_valid_license_option' => 'edd_ci_license_active' )
		),
	array(
					'id' => 'edd_csv_discount_import',
					'name' => '<strong>' . __('CSV Coupon Import Mapping', 'edd') . '</strong>',
					'desc' => __('', 'edd'),
					'type' => 'header',
				),
	array(
					'id' 		=> 'edd_import_discount_name',
					'name'		=> __('Coupon Name','edd'),
					'desc'		=> __('The name of the coupon.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_code',
					'name'		=> __('Coupon Code','edd'),
					'desc'		=> __('The code users will enter to recieve the discount.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_type',
					'name'		=> __('Type of Discount','edd'),
					'desc'		=> __('Possible values: \'flat\' or \'percentage\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_start',
					'name'		=> __('Date and Time when the discount becomes valid','edd'),
					'desc'		=> __('Format: \'YYYY-MM-DD HH:MM:SS\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_end',
					'name'		=> __('Date and Time when the discount becomes invalid','edd'),
					'desc'		=> __('Format: \'YYYY-MM-DD HH:MM:SS\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_type',
					'name'		=> __('Status:','edd'),
					'desc'		=> __('Possible values: \'active\',\'inactive\',or \'expired\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_coupon_uses',
					'name'		=> __('Current number of uses:','edd'),
					'desc'		=> __('The number of times the coupon has already been used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_max_uses',
					'name'		=> __('Max uses:','edd'),
					'desc'		=> __('The number of times the coupon may be used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_amount',
					'name'		=> __('Amount of Discount:','edd'),
					'desc'		=> __('The amount of discount.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_min_price',
					'name'		=> __('Min Price:','edd'),
					'desc'		=> __('The amount that must be in cart before the discount may be used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_product_reqs',
					'name'		=> __('Product Requirements:','edd'),
					'desc'		=> __('Products that must be in cart for coupon to be used. Valid input: Array of product ids.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_product_condition',
					'name'		=> __('Product Condition:','edd'),
					'desc'		=> __('Require \'all\' or \'any\' of the products from the product requirements for discount to be valid. ', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_is_not_global',
					'name'		=> __('Is Global:','edd'),
					'desc'		=> __('Apply discount only to products from the product requirements. Either 1 for yes, or 0 for no.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_is_single_use',
					'name'		=> __('Is  Single Use:','edd'),
					'desc'		=> __('Limit this discount to a single-use per customer? Either 1 for yes, or 0 for no.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				),
	array(
					'id' 		=> 'edd_import_discount_id',
					'name'		=> __('Discount ID:','edd'),
					'desc'		=> __('If discount already exists, the ID of it. Else empty string.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				)
				);
	return array_merge( $settings, $ci_settings );
}


	/**
	 * Activate a license key
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @return void
	 */
	public function activate_license() {

		global $edd_options;

		if( ! isset( $_POST['edd_settings_misc'] ) )
			return;
		if( ! isset( $_POST['edd_settings_misc']['edd_ci_license_key'] ) )
			return;

		if( get_option( 'edd_ci_license_active' ) == 'valid' )
			return;

		$license = sanitize_text_field( $_POST['edd_settings_misc']['edd_ci_license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_CI_PRODUCT_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_API_URL ), array( 'timeout' => 15, 'body' => $api_params, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'edd_ci_license_active', $license_data->license );

	}


	/**
	 * Deactivate a license key
	 *
	 * @since  1.1.1
	 * @return void
	 */

	public function deactivate_license() {
		global $edd_options;

		if ( ! isset( $_POST['edd_settings_misc'] ) )
			return;

		if ( ! isset( $_POST['edd_settings_misc']['edd_ci_license_key'] ) )
			return;

		// listen for our activate button to be clicked
		if( isset( $_POST['edd_ci_license_key_deactivate'] ) ) {

		    // run a quick security check
		    if( ! check_admin_referer( 'edd_ci_license_key_nonce', 'edd_ci_license_key_nonce' ) )
		      return; // get out if we didn't click the Activate button

		    // retrieve the license from the database
		    $license = trim( $edd_options['edd_ci_license_key'] );

		    // data to send in our API request
		    $api_params = array(
		      'edd_action'=> 'deactivate_license',
		      'license'   => $license,
		      'item_name' => urlencode( EDD_CI_PRODUCT_NAME ) // the name of our product in EDD
		    );

		    // Call the custom API.
		    $response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		    // make sure the response came back okay
		    if ( is_wp_error( $response ) )
		    	return false;

		    // decode the license data
		    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

		    // $license_data->license will be either "deactivated" or "failed"
		    if( $license_data->license == 'deactivated' )
		    	delete_option( 'edd_ci_license_active' );

		}
	}

}


/**
 * Get everything running
 *
 * @since 1.0
 *
 * @access private
 * @return void
 */

function edd_ci_load() {
	$discounts = new EDD_CI();
}
add_action( 'plugins_loaded', 'edd_ci_load' );



/**
 * Registers the new license field type
 *
 * @access      private
 * @since       10
 * @return      void
*/

if( ! function_exists( 'edd_license_key_callback' ) ) {
	function edd_license_key_callback( $args ) {
		global $edd_options;

		if( isset( $edd_options[ $args['id'] ] ) ) { $value = $edd_options[ $args['id'] ]; } else { $value = isset( $args['std'] ) ? $args['std'] : ''; }
		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $args['size'] . '-text" id="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

		if( 'valid' == get_option( $args['options']['is_valid_license_option'] ) ) {
			$html .= wp_nonce_field( $args['id'] . '_nonce', $args['id'] . '_nonce', false );
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'edd-recurring' ) . '"/>';
		}
		$html .= '<label for="edd_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}
}