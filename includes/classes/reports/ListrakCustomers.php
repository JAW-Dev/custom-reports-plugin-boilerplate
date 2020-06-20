<?php
/**
 * Listrak Customers.
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

if ( ! class_exists( __NAMESPACE__ . '\\ListrakCustomers' ) ) {

	/**
	 * Listrak Customers.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ListrakCustomers extends Listrak {

		/**
		 * Nonce
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $nonce = 'listrak-customers-report';

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Hooks
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'wp_ajax_listrak_customes_report', array( $this, 'listrak_customes_report' ) );
			add_action( 'wp_ajax_nopriv_listrak_customes_report', array( $this, 'listrak_customes_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function listrak_customes_report() {
			global $wpdb;
			$customers = get_users();

			if ( empty( $customers ) ) {
				// No customers, bail.
				return;
			}

			$data = array();

			foreach ( $customers as $c ) {
				$customer = new \WC_Customer( $c->ID );

				if ( empty( $customer->get_first_name() ) ) {
					continue;
				}

				$customers_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_orders WHERE customer_id = %d AND order_id IN (SELECT max(order_id) FROM wp_woocommerce_orders WHERE customer_id = %d)", $c->ID, $c->ID ) ); // phpcs:ignore

				foreach ( $customers_data as $customer_data ) {
					if ( (string) $c->ID === (string) $customer_data->customer_id ) {
						$data[] = array(
							'Email'     => $customer_data->billing_email,
							'FirstName' => $customer_data->billing_first_name,
							'LastName'  => $customer_data->billing_last_name,
							'Address 1' => $customer_data->shipping_address_1,
							'Address 2' => $customer_data->shipping_address_2,
							'City'      => $customer_data->shipping_city,
							'State'     => $customer_data->shipping_state,
							'Zip'       => $customer_data->shipping_postcode,
							'Country'   => $customer_data->shipping_country,
							'HomePhone' => $customer_data->billing_phone,
						);
						break;
					}
				}
			}

			if ( ! $_POST['test'] ) { // phpcs:ignore
				$this->upload( $data );
				echo 'sent';
				exit;
			} else {
				echo wp_json_encode( $data, true );
				exit;
			}
		}
	}
}
