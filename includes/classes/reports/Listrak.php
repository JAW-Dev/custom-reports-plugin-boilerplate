<?php
/**
 * Listrak.
 *
 * @package    Custom_Reports
 * @subpackage Custom_Reports/Inlcudes/Classes/Reports
 * @author     Jason Witt
 * @copyright  Copyright (c) 2020, Jason Witt
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace CustomReports\Includes\Classes\Reports;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Listrak.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
abstract class Listrak {

	/**
	 * Nonce
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $nonce = 'listrak-orders-report';

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function hooks() {}

	/**
	 * Upload
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $data The data.
	 *
	 * @return void
	 */
	public function upload( $data ) {
		die;
		// phpcs:disable
		// $ftp_server    = 'ftp.listrakbi.com';
		// $ftp_user_name = 'FAUser_MooreGiles';
		// $ftp_user_pass = 'P64T339n9V46JAZ';

		// Create a temporary file.
		$temp_file = tmpfile();

		fwrite( $temp_file, $data ); // phpcs:ignore

		$temp_file_path = stream_get_meta_data( $temp_file )['uri'];

		// Set up basic connection.
		$conn_id = ftp_connect( $ftp_server );

		// login with username and password.
		ftp_login( $conn_id, $ftp_user_name, $ftp_user_pass );

		// Set passive mode.
		ftp_pasv( $conn_id, true );

		// upload a file.
		if ( ftp_put( $conn_id, $remote_filename, $temp_file_path, FTP_ASCII ) ) {
			// it worked, do nothing.
			error_log( "Listrak Integration: SUCCEEDED uploading {$remote_filename}." ); // phpcs:ignore
		} else {
			error_log( "Listrak Integration: Failed to upload {$remote_filename}." ); // phpcs:ignore
		}

		// Close the FTP connection.
		ftp_close( $conn_id );

		update_option( 'mg_listrak_last_order', $new_last_order );
	}
}
