<?php
/*
Plugin Name: Easy Digital Downloads - Coupon Import
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
    
    define( 'EDD_CI_STORE_URL', 'https://easydigitaldownloads.com' );
    define( 'EDD_CI', 'Coupon Importer' );
    define( 'EDD_CI_VERSION', '1.0' );
    
    if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
        include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
    }
    
    // retrieve our license key from the DB
    $license_key = trim( get_option( 'edd_ci_license_key' ) );
    
    function edd_ci_updater() {
        // setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( EDD_CI_STORE_URL, __FILE__, array(
             'version' => EDD_CI_VERSION, // current version number
            'license' => $license_key, // license key (used get_option above to retrieve from DB)
            'item_name' => EDD_CI, // name of this plugin
            'author' => 'Chris Christoff' // author of this plugin
        ) );
    }
    add_action( 'admin_init', 'edd_ci_updater' );
    add_action( 'admin_menu', 'edd_ci_license_menu' );
    add_action( 'admin_init', 'edd_ci_register_option' );
    add_action( 'admin_init', 'edd_ci_deactivate_license' );
    add_action( 'admin_init', 'edd_ci_activate_license' );
    
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