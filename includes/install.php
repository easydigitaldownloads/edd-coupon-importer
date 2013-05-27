<?php
/**
 * Install Function
 *
 * @package     Easy Digital Downloads - Coupon Import
 * @subpackage  Install Function
 * @copyright   Copyright (c) 2013, Chris Christoff
 * @since       1.0
*/


/**
 * CSV Import Install
 *
 * Runs on plugin install.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/

function edd_csv_import_csv_install() {
	global $wpdb, $edd_options;


}
register_activation_hook( EDD_COUPON_IMPORT_FILE, 'edd_csv_import_csv_install' );
