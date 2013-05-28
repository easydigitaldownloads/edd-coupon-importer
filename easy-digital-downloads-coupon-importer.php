<?php
/*
Plugin Name: Easy Digital Downloads - Coupon Importer
Plugin URI: http://easydigitaldownloads.com/chris-is-amazing
Description: Adds the ability to import coupons via a CSV file.
Author: Chris Christoff
Author URI: http://www.chriscct7.com
Version: 1.0
*/

if ( class_exists( 'Easy_Digital_Downloads' ) ) {
    
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
    function edd_ci_updater() {    
    define( 'EDD_CI_STORE_URL', 'https://easydigitaldownloads.com' );
    define( 'EDD_CI', 'Coupon Importer' );
    define( 'EDD_CI_VERSION', '1.0' );
	define( 'EDD_CI_STORE_API_URL', 'https://easydigitaldownloads.com' ); 
	define( 'EDD_CI_PRODUCT_NAME', 'Coupon Importer' ); 
    
    if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
        include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
    }
    
    // retrieve our license key from the DB
    $license_key = trim( get_option( 'edd_ci_license_key' ) );
    

        // setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( EDD_CI_STORE_URL, __FILE__, array(
             'version' => EDD_CI_VERSION, // current version number
            'license' => $license_key, // license key (used get_option above to retrieve from DB)
            'item_name' => EDD_CI, // name of this plugin
            'author' => 'Chris Christoff' // author of this plugin
        ) );
	add_filter( 'edd_settings_misc' , 'add_ci_misc_settings' );
    }
    
    if ( is_admin() ) {
        
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
    }
    
    include_once( EDD_COUPON_IMPORT_DIR . 'includes/register-settings.php' );
    include_once( EDD_COUPON_IMPORT_DIR . 'includes/admin-actions.php' );
    include_once( EDD_COUPON_IMPORT_DIR . 'includes/admin-page.php' );
    include_once( EDD_COUPON_IMPORT_DIR . 'includes/import-functions.php' );
}
	function add_misc_ci_settings( $settings ) {

		$settings[] = array(
					'id'   => 'edd_ci_settings',
					'name' => __( '<strong>Amazon S3 Settings</strong>', 'edd' ),
					'desc' => '',
					'type' => 'header'
		);

		$settings[] = array(
					'id' => 'edd_ci_settings_license_key',
					'name' => __('License Key', 'edd_et'),
					'desc' => __('Enter your license for Amazon S3 to receive automatic upgrades', 'edd_sl'),
					'type' => 'text',
					'size' => 'regular'
		);

		return $settings;
	}

	function activate_license() {
		global $edd_options;
		if( ! isset( $_POST['edd_settings_misc'] ) )
			return;
		if( ! isset( $_POST['edd_settings_misc']['edd_ci_settings_license_key'] ) )
			return;

		if( get_option( 'edd_ci_settings_license_active' ) == 'active' )
			return;

		$license = sanitize_text_field( $_POST['edd_settings_misc']['edd_ci_settings_license_key'] );

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( EDD_CI_PRODUCT_NAME ) // the name of our product in EDD
		);
		
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_API_URL ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'edd_amazon_s3_license_active', $license_data->license );

	}