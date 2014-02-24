<?php
/**
 * Admin Actions
 *
 * @package     Easy Digital Downloads - Coupon Import
 * @subpackage  Admin Actions
 * @copyright   Copyright (c) 2013, Chris Christoff
 * @since       1.0
*/


/**
 * Import Coupon File
 *
 * @access      public
 * @since       1.0
 * @return      void
*/
function edd_import_process_coupon_file() {
	global $edd_message;
	$edd_message = '';

	if ( isset( $_POST['edd-action'] ) && ( $_POST['edd-action'] == 'import_coupon_csv' ) ) {

		// check for file

		if ( empty( $_FILES ) || $_FILES['import_file']['size'] == 0 ){
			$edd_message = 'Please choose a file to import.';
			return;
		}

		edd_import_coupon_csv_file();

		$edd_message = "Import completed.  Results in Output Log below.";
	}

}
add_action( 'init', 'edd_import_process_coupon_file', 10 );



function edd_ci_license_menu() {
	add_plugins_page( 'EDD CI', 'EDD CI', 'manage_options', 'edd-ci-license', 'edd_ci_license_page' );
}
		
function edd_ci_license_page() {
	$license 	= get_option( 'edd_ci_license_key' );
	$status 	= get_option( 'edd_ci_license_status' );
	?>
			<div class="wrap">
				<h2><?php _e('EDD CI License Options'); ?></h2>
				<form method="post" action="options.php">
				
				<?php settings_fields('edd_ci_license'); ?>
			
				<table class="form-table">
					<tbody>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('License Key'); ?>
							</th>
							<td>
								<input id="edd_ci_license_key" name="edd_ci_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
								<label class="description" for="edd_ci_license_key"><?php _e('Enter your license key'); ?></label>
							</td>
						</tr>
						<?php if( false !== $license ) { ?>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'edd_ci_nonce', 'edd_ci_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_ci_nonce', 'edd_ci_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
				</table>	
				<?php submit_button(); ?>
		
			</form>
		<?php
		}

		function edd_ci_register_option() {
			// creates our settings in the options table
			register_setting('edd_ci_license', 'edd_ci_license_key', 'edd_ci_sanitize_license' );
		}

		function edd_ci_sanitize_license( $new ) {
			$old = get_option( 'edd_ci_license_key' );
			if( $old && $old != $new ) {
				delete_option( 'edd_ci_license_status' ); // new license has been entered, so must reactivate
			}
		return $new;
		}

		function edd_ci_activate_license() {
	
			// listen for our activate button to be clicked
			if( isset( $_POST['edd_license_activate'] ) ) {

			// run a quick security check 
			if( ! check_admin_referer( 'edd_ci_nonce', 'edd_ci_nonce' ) ) 	
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( 'edd_ci_license_key' ) );
			

			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( EDD_CI ) // the name of our product in EDD
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
			// $license_data->license will be either "active" or "inactive"

			update_option( 'edd_ci_license_status', $license_data->license );

			}
		}

		function edd_ci_deactivate_license() {

			// listen for our activate button to be clicked
			if( isset( $_POST['edd_license_deactivate'] ) ) {

				// run a quick security check 
				if( ! check_admin_referer( 'edd_ci_nonce', 'edd_ci_nonce' ) ) 	
					return; // get out if we didn't click the Activate button

					// retrieve the license from the database
					$license = trim( get_option( 'edd_ci_license_key' ) );
			

					// data to send in our API request
				$api_params = array( 
					'edd_action'=> 'deactivate_license', 
					'license' 	=> $license, 
					'item_name' => urlencode( EDD_CI ) // the name of our product in EDD
				);
		
				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' )
					delete_option( 'edd_ci_license_status' );

			}
		}


		function edd_ci_check_license() {

			global $wp_version;

			$license = trim( get_option( 'edd_ci_license_key' ) );
		
			$api_params = array( 
				'edd_action' => 'check_license', 
				'license' => $license, 
				'item_name' => urlencode( EDD_CI ) 
			);

			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, EDD_CI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );


			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( $license_data->license == 'valid' ) {
				return true;
				// this license is still valid
			} else {
				return false;
				// this license is no longer valid
			}
		}
