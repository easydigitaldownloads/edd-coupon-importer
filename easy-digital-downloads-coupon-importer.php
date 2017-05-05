<?php
/*
Plugin Name: Easy Digital Downloads - Coupon Importer
Plugin URI: https://easydigitaldownloads.com/downloads/csv-coupon-importer/
Description: Adds the ability to import coupons via a CSV file.
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
Version: 1.1.1
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

		if ( ! self::$instance ) {
			self::$instance = new EDD_CI();
		}

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
		define( 'EDD_CI_VERSION', '1.1.1' );
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
		$this->db_upgrades();
		$this->includes();
		$this->init();

	}

	/**
	 * Perform any updates needed to settings and version numbers
	 *
	 * @since 1.1.2
	 * @return void
	 */
	private function db_upgrades() {
		$current_version = get_option( 'edd_ci_version' );
		if ( empty( $current_version ) ) {
			// Perform the first set of updates needed
			$edd_ci_license_key = edd_get_option( 'edd_ci_settings_license_key', '' );
			edd_update_option( 'edd_coupon_importer_license_key', $edd_ci_license_key );
			update_option( 'edd_ci_version', EDD_CI_VERSION );
		}
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

		if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
			return; // EDD not present
		}

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

		// auto updater

		if( is_admin() ) {
			// retrieve our license key from the DB
			$license = new EDD_License( __FILE__, EDD_CI_PRODUCT_NAME, EDD_CI_VERSION, 'EDD Team' );
		}
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
				'id' 		=> 'edd_import_discount_status',
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
				'desc'		=> __('Products that must be in cart for coupon to be used. Valid input: If one product, just the id. If multiple, seperate by pipes: 20|32|108', 'edd'),
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