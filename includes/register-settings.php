<?php
/**
 * Register Settings
 *
 * @package     Easy Digital Downloads - Coupon Import
 * @subpackage  Register Settings
 * @copyright   Copyright (c) 2013, Chris Christoff
 * @since       1.0
*/

/**
 * Register Settings
 *
 * Registers the required settings for the plugin and adds them to the 'CSV Import' tab
 *
 * @access      private
 * @since       1.0
 * @return      void
*/

function edd_csv_coupon_import_register_settings( $settings ) {
	global $edd_import_coupon_file_columns;

	$settings[] = array(
					'id' => 'edd_csv_discount_import',
					'name' => '<strong>' . __('CSV Coupon Import Mapping', 'edd') . '</strong>',
					'desc' => __('', 'edd'),
					'type' => 'header',
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_name',
					'name'		=> __('Coupon Name','edd'),
					'desc'		=> __('The name of the coupon.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_code',
					'name'		=> __('Coupon Code','edd'),
					'desc'		=> __('The code users will enter to recieve the discount.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_type',
					'name'		=> __('Type of Discount','edd'),
					'desc'		=> __('Possible values: \'flat\' or \'percentage\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_start',
					'name'		=> __('Date and Time when the discount becomes valid','edd'),
					'desc'		=> __('Format: \'YYYY-MM-DD HH:MM:SS\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_end',
					'name'		=> __('Date and Time when the discount becomes invalid','edd'),
					'desc'		=> __('Format: \'YYYY-MM-DD HH:MM:SS\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_type',
					'name'		=> __('Status:','edd'),
					'desc'		=> __('Possible values: \'active\',\'inactive\',or \'expired\'', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_coupon_uses',
					'name'		=> __('Current number of uses:','edd'),
					'desc'		=> __('The number of times the coupon has already been used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_max_uses',
					'name'		=> __('Max uses:','edd'),
					'desc'		=> __('The number of times the coupon may be used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_amount',
					'name'		=> __('Amount of Discount:','edd'),
					'desc'		=> __('The amount of discount.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_min_price',
					'name'		=> __('Min Price:','edd'),
					'desc'		=> __('The amount that must be in cart before the discount may be used.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_product_reqs',
					'name'		=> __('Product Requirements:','edd'),
					'desc'		=> __('Products that must be in cart for coupon to be used. Valid input: Array of product ids.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_product_condition',
					'name'		=> __('Product Condition:','edd'),
					'desc'		=> __('Require \'all\' or \'any\' of the products from the product requirements for discount to be valid. ', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_is_not_global',
					'name'		=> __('Is Global:','edd'),
					'desc'		=> __('Apply discount only to products from the product requirements. Either 1 for yes, or 0 for no.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_is_single_use',
					'name'		=> __('Is  Single Use:','edd'),
					'desc'		=> __('Limit this discount to a single-use per customer? Either 1 for yes, or 0 for no.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	$settings[] = array(
					'id' 		=> 'edd_import_discount_id',
					'name'		=> __('Discount ID:','edd'),
					'desc'		=> __('If discount already exists, the ID of it. Else empty string.', 'edd'),
					'type' 		=> 'select',
					'options'	=> $edd_import_coupon_file_columns
				);
	return $settings;

}

add_filter('edd_settings_misc', 'edd_csv_coupon_import_register_settings', 10, 1);
