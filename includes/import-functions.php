<?php
/**
 * Import Functions
 *
 * @package     Easy Digital Downloads - CSV Import
 * @subpackage  Import Functions
 * @copyright   Copyright (c) 2012, Daniel Espinzoa
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/


/**
 * Show File Upload
 *
 * @access      public
 * @since       1.0
 * @return      void
*/

function edd_show_coupon_upload_form() {
	global $edd_import_field_mapping, $edd_import_coupon_file_columns, $edd_message;

	if ( $edd_message != '') { ?>
	<div id="" class="updated settings-error">
		<p><strong><?php echo $edd_message ?></strong></p>
	</div>
	<?php } ?>
	<h3><?php  _e('Import Coupons From A CSV File', 'edd') ?></h3>
	<p>
		<?php _e('To import coupons select a valid CSV file and press Import File.  Results  of the import will be displayed in the log area below.', 'edd') ?>
	</p>
	<p>
		<?php
		$link = '<a href="' . get_admin_url() . 'edit.php?post_type=download&page=edd-settings&tab=misc" >' . __('Coupon Import Mapping', 'edd') . '</a>';
		echo sprintf( __('The mapping of coupon fields to CSV columns can be adjusted in the %s section.','edd'), $link );
		?>
	</p>
	<form method="post" enctype="multipart/form-data">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Import File','edd') ?></th>
				<td><input type="file" id="import_file" name="import_file" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Validate File Only','edd') ?></th>
				<td><label for="check_file"><input type="checkbox" id="check_file" name="check_file" value="on" /> <?php esc_html_e( 'Check file, but do not save coupons to the database.', 'edd' ); ?></label></td>
			</tr>
		</tbody>
		</table>
		<?php
			$edd_generate_mapping_nonce = wp_create_nonce('edd_generate_mapping');
		?>
		<input type="hidden" name="_wpnonce" value="<?php echo $edd_generate_mapping_nonce  ?>" />
		<input type="hidden" name="edd-action" value="import_coupon_csv" />
		<p>
			<input type="submit" name="" id="" class="button-secondary action" value="<?php _e('Import File','edd'); ?>" />
		</p>
	</form>

	<?php
}


/**
 * Show Import Log
 *
 * @access      public
 * @global      string $edd_log_file The log file to display
 * @since       1.0
 * @return      void
*/

function edd_show_coupon_import_log() {
	global $edd_log_file;
	?>
	<strong>Output Log</strong><br/>
	<textarea style="width: 650px; height: 150px" ><?php echo $edd_log_file ?></textarea>
	<?php
}


/**
 * Import CSV File
 *
 * @access      public
 * @since       1.0
 * @return      void
*/
function edd_import_coupon_csv_file() {
	global $edd_options, $edd_log_file;

	$file_name = $_FILES['import_file']['name'];
	$edd_log_file = sprintf( __('Starting import of file: %s' , 'edd'), $file_name ) . "\n";

	if ( is_file($_FILES['import_file']['tmp_name'])) {
		if ( ($handle = fopen( $_FILES['import_file']['tmp_name'], "r")) !== FALSE ) {

			$row_ctr=0;
			while (($line = fgetcsv($handle, 1000,",")) !== FALSE ) {

				$row_ctr++;

				// skip header row
				if ( $row_ctr > 1 ) {

					// get the current post's data from the mapped column
					$edd_discount_name				= isset( $edd_options['edd_import_discount_name'] ) 			 ? $line[ $edd_options['edd_import_discount_name'] ]         : $line[0];
					$edd_discount_code				= isset( $edd_options['edd_import_discount_code'] ) 			 ? $line[ $edd_options['edd_import_discount_code'] ]         : $line[1];
					$edd_discount_type				= isset( $edd_options['edd_import_discount_type'] ) 			 ? $line[ $edd_options['edd_import_discount_type'] ]         : $line[2];
					$edd_discount_start				= isset( $edd_options['edd_import_discount_start'] ) 			 ? $line[ $edd_options['edd_import_discount_start'] ]        : $line[3];
					$edd_discount_end				= isset( $edd_options['edd_import_discount_end'] ) 				 ? $line[ $edd_options['edd_import_discount_end'] ]          : $line[4];
					$edd_discount_status			= isset( $edd_options['edd_import_discount_status'] ) 			 ? $line[ $edd_options['edd_import_discount_status'] ]       : $line[5];
					$edd_discount_uses				= isset( $edd_options['edd_import_discount_uses'] ) 			 ? $line[ $edd_options['edd_import_discount_uses'] ]     	 : $line[6];
					$edd_discount_max_uses			= isset( $edd_options['edd_import_discount_max_uses'] ) 		 ? $line[ $edd_options['edd_import_discount_max_uses'] ]     : $line[7];
					$edd_discount_amount			= isset( $edd_options['edd_import_discount_amount'] ) 			 ? $line[ $edd_options['edd_import_discount_amount'] ] 		 : $line[8];
					$edd_discount_min_price			= isset( $edd_options['edd_import_discount_min_price']) 		 ? $line[ $edd_options['edd_import_discount_min_price'] ]    : $line[9];
					$edd_discount_product_reqs		= isset( $edd_options['edd_import_discount_product_reqs']) 		 ? $line[ $edd_options['edd_import_discount_product_reqs'] ]    : $line[10];
					$edd_discount_product_condition	= isset( $edd_options['edd_import_discount_product_condition'])  ? $line[ $edd_options['edd_import_discount_product_condition'] ]    : $line[11];
					$edd_discount_is_not_global		= isset( $edd_options['edd_import_discount_is_not_global']) 	 ? $line[ $edd_options['edd_import_discount_is_not_global'] ]    : $line[12];
					$edd_discount_is_single_use		= isset( $edd_options['edd_import_discount_is_single_use']) 	 ? $line[ $edd_options['edd_import_discount_is_single_use'] ]    : $line[13];
					$edd_discount_id				= isset( $edd_options['edd_import_discount_id']) 				 ? $line[ $edd_options['edd_import_discount_id'] ]    : $line[14];

					// check for valid data
					$validation_error = false;
					$validation_msg  = '';

					// discount name  blank
					if ( 0 == strlen( $edd_discount_name ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount Name is blank.', 'edd') . "; ";
					}

					// discount code  blank
					if ( 0 == strlen( $edd_discount_code ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount code is blank.', 'edd') . "; ";
					}

					// Verify only accepted characters.
					$sanitized_discount_code = preg_replace( '/[^a-zA-Z0-9-_]+/', '', $edd_discount_code );
					if ( strtoupper( $edd_discount_code ) !== strtoupper( $sanitized_discount_code ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount code is invalid.', 'edd') . "; ";
					}

					// discount type is blank
					if ( 0 == strlen( $edd_discount_type ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount type is blank.', 'edd') . "; ";
					}

					// discount type is invalid
					$discount_types = array( 'flat', 'percentage' );
					if ( ! in_array( $edd_discount_type, $discount_types, true ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount type is invalid.', 'edd') . "; ";
					}

					// discount code is blank
					if ( 0 == strlen( $edd_discount_code ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount code is blank.', 'edd') . "; ";
					}

					// start time validation
					if ( ! strtotime( $edd_discount_start ) ) {
						$validation_error = true;
						$validation_msg  .= sprintf(__('Date validation failed for string: %s', 'edd'), $edd_discount_start) . "; ";
					}

					// end time validation
					if ( ! strtotime( $edd_discount_end ) ) {
						$validation_error = true;
						$validation_msg  .= sprintf(__('Date validation failed for string: %s', 'edd'), $edd_discount_end) . "; ";
					}

					// check status
					$status = array( 'active', 'inactive', 'expired' );
					if ( ! in_array( $edd_discount_status, $status, true ) ) {
						$validation_error = true;
						$validation_msg  .= __('Status needs to be active, inactive, or pending', 'edd') . "; ";
					}

					// Check product condition.
					$product_conditions = array( 'all', 'any' );
					if ( ! in_array( $edd_discount_product_condition, $product_conditions, true ) ) {
						$validation_error = true;
						$validation_msg  .= __('Product condition is invalid.', 'edd') . "; ";
					}

					// maybe more validation later

					if ( $validation_error ) {
						// report error and loop
						$edd_log_file .= sprintf( __('Error importing row %d: ', 'edd'), $row_ctr)  . $validation_msg . "\n" ;
					} else {

						if ( isset($_POST['check_file'] ) && ( $_POST['check_file'] == 'on' ) ) {

							$edd_log_file .= sprintf(__('Successfully validated row: %d', 'edd'), $row_ctr ) . "\n";

						} else {

							if ( ! empty( $edd_discount_product_reqs ) ) {
								$edd_discount_product_reqs = wp_parse_id_list( explode( "|", $edd_discount_product_reqs ) );
							} else {
								$edd_discount_product_reqs = false;
							}

							// Prepare arguments for EDD 2.X.
							$args = array(
								'name'              => sanitize_text_field( $edd_discount_name ),
								'code'              => $edd_discount_code,
								'status'            => $edd_discount_status,
								'uses'              => absint( $edd_discount_uses ),
								'max'               => absint( $edd_discount_max_uses ),
								'amount'            => floatval( $edd_discount_amount ),
								'start'             => sanitize_text_field( $edd_discount_start ),
								'expiration'        => sanitize_text_field( $edd_discount_end ),
								'type'              => $edd_discount_type,
								'min_price'         => floatval( $edd_discount_min_price ),
								'products'          => $edd_discount_product_reqs,
								'product_condition' => $edd_discount_product_condition,
								'not_global'        => boolval( $edd_discount_is_not_global ),
								'use_once'          => boolval( $edd_discount_is_single_use ),
							);

							// Is it already made?
							$discount = edd_get_discount( $edd_discount_id );

							// Check if we are on EDD 3.0+.
							if ( function_exists( 'edd_add_adjustment' ) ) {
								// Convert legacy argument names.
								$args = EDD_Discount::convert_legacy_args( $args );

								// Dates are in local timezone.
								$start_date = edd_get_utc_equivalent_date( EDD()->utils->date( $args['start_date'], edd_get_timezone_id(), false ) );
								if ( $start_date ) {
									$args['start_date'] = $start_date->format( 'Y-m-d H:i:s' );
								}

								$end_date = edd_get_utc_equivalent_date( EDD()->utils->date( $args['end_date'], edd_get_timezone_id(), false ) );
								if ( $end_date ) {
									$args['end_date'] = $end_date->format( 'Y-m-d H:i:s' );
								}
							}

							// If discount exists, update it; otherwise, add a new one.
							if ( $discount ) {
								// Check if we are on EDD 3.0+.
								$discount_id = function_exists( 'edd_add_adjustment' ) ? edd_update_discount( $discount->id, $args ) : edd_store_discount( $args, $discount->id );

								$edd_log_file .= sprintf(__('Successfully updated row: %d', 'edd'), $row_ctr ) . "\n";

							} else {
								// Check if we are on EDD 3.0+.
								$discount_id = function_exists( 'edd_add_adjustment' ) ? edd_add_discount( $args ) : edd_store_discount( $args );

								$edd_log_file .= sprintf(__('Successfully imported row: %d', 'edd'), $row_ctr ) . "\n";
							}

						} // if ( just validating )

					} // if ( $validation_error )

				} // if ( $row_ctr )

			} // while()

		} else {

			$edd_log_file .= __('Error opening file uploaded.','edd');
		}

	} else {

		$edd_log_file .= __('Error opening file uploaded.', 'edd');
	}


}
