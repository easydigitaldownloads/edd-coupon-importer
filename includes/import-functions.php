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
				<td><input type="checkbox" id="check_file" name="check_file" /> <?php _e('Check file, but do not save downloads to the database.','edd') ?></td>
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
 * @param 		edd_log_file - The log file to display
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
					
					// discount type is blank
					if ( 0 == strlen( $edd_discount_type ) ) {
						$validation_error = true;
						$validation_msg  .= __('Discount type is blank.', 'edd') . "; ";
					}
					
					// discount type is invaid
					if ( ( $edd_discount_type != 'flat' ) && ( $edd_discount_type != 'percentage' ) ) {
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
					
					// set start time to correct date type
					$edd_discount_start =  date( 'm/d/Y H:i:s', strtotime( $edd_discount_start ) );
					
					// set end time to the correct date type
					$edd_discount_end =  date( 'm/d/Y H:i:s',  strtotime(  date( 'm/d/Y', strtotime( $edd_discount_end ) ) . ' 23:59:59' )  );
				
					// check status 
					$status = array( 'active', 'inactive', 'expired' );
					if ( ! in_array( $edd_discount_status, $status ) ) {
						$validation_error = true;
						$validation_msg  .= __('Status needs to be active, inactive, or pending', 'edd') . "; ";
					}
					
					// maybe more validation later


					if ( $validation_error ) {
						// report error and loop
						$edd_log_file .= sprintf( __('Error importing row %d: ', 'edd'), $row_ctr)  . $validation_msg . "\n" ;
					} else {

						if ( isset($_POST['check_file'] ) && ( $_POST['check_file'] == 'on' ) ) {

							$edd_log_file .= sprintf(__('Successfully validated row: %d', 'edd'), $row_ctr ) . "\n";

						} else {
								
							$meta = array(
									'code'       		=> $edd_discount_code,
									'uses'       		=> $edd_discount_uses,
									'max_uses'   		=> $edd_discount_max_uses,
									'amount'     		=> $edd_discount_amount,
									'start'      		=> $edd_discount_start,
									'expiration' 		=> $edd_discount_end,
									'type'       		=> $edd_discount_type,
									'min_price'   		=> $edd_discount_min_price,
									'product_reqs'      => $edd_discount_min_price,
									'product_condition' => $edd_discount_product_reqs,
									'is_not_global'     => $edd_discount_is_not_global,
									'is_single_use'     => $edd_discount_is_single_use
							);
						
							// Is it already made?
							if ( ( 0 == strlen( $edd_discount_id ) ) && edd_discount_exists( $edd_discount_id ) ){
							
								wp_update_post( array(
									'ID'          => $edd_discount_id,
									'post_title'  => $edd_discount_name,
									'post_status' => $edd_discount_status
								) );

								foreach( $meta as $key => $value ) {
									update_post_meta( $edd_discount_id , '_edd_discount_' . $key, $value );
								}

								
							}
							else {
							
								// add post
								// setup post args
								$new_download_args = array(
									'post_type' 	 => 'edd_discount',
									'post_title' 	 => $edd_discount_name,
									'post_status' 	 => $edd_discount_status,
								);
								$new_download_id = wp_insert_post( $new_download_args , true );
							

								foreach ( $meta as $key => $value ) {
									update_post_meta( $new_download_id, '_edd_discount_' . $key, $value );
								}
							
							}

							if ( is_wp_error( $new_download_id ) ) {

								$edd_log_file .= sprintf( __('Error importing row %d: ', 'edd'), $row_ctr) . $new_download_id->get_error_message() ;

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