<?php
/**
 * Admin Page
 *
 * @package     Easy Digital Downloads - Coupon Import
 * @subpackage  Admin Page
 * @copyright   Copyright (c) 2013, Chris Christoff
 * @since       1.0
*/


/**
 * Add Options Link
 *
 * Creates the admin submenu page for CSV Coupon Import.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/

function edd_csv_coupon_import_menu() {
	global $edd_csv_coupon_import_page;
	if (edd_ci_check_license()){
		$edd_csv_coupon_import_page = add_submenu_page( 'edit.php?post_type=download', __('Easy Digital Download CSV Coupon Import', 'edd'), __('Import Coupons', 'edd'), 'manage_options', 'edd-csv-coupon-import', 'edd_csv_coupon_import_page' );
	}
}
add_action( 'admin_menu', 'edd_csv_coupon_import_menu', 10 );


/**
 * Coupon Import Page
 *
 * Shows the import settings and import button.
 *
 * @access      public
 * @since       1.0
 * @return      void
*/
function edd_csv_coupon_import_page() {
	global $edd_options;

	?>
	<div class="wrap">
		<h2><?php _e('CSV Coupon Import', 'edd'); ?></h2>
		<?php

			do_action('edd_csv_import_coupons_top');

			// The upload form
			edd_show_coupon_upload_form();

			edd_show_coupon_import_log();

			do_action('edd_coupon_import_bottom');
		?>
	</div><!--end wrap-->
	<?php
}
