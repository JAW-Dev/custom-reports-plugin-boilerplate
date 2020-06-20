<?php
/**
 * Listrak Orders.
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

if ( ! class_exists( __NAMESPACE__ . '\\ListrakOrders' ) ) {

	/**
	 * Listrak Orders.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ListrakOrders extends Listrak {

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
			add_action( 'wp_ajax_listrak_orders_report', array( $this, 'listrak_orders_report' ) );
			add_action( 'wp_ajax_nopriv_listrak_orders_report', array( $this, 'listrak_orders_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function listrak_orders_report() {

			// Bail if none check fails.
			if ( $this->nonce !== $_POST['nonce'] ) { // phpcs:ignore
				exit;
			}

			global $wpdb;

			// 459446
			$last_order = get_option( 'mg_listrak_last_order', true );

			if ( false === $last_order ) {
				$last_order = 0;
			}

			// Set timezone.
			date_default_timezone_set( 'GMT' ); // phpcs:ignore

			// Get All Orders Since Last Order.
			$new_orders = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE ID > %d AND post_type = 'shop_order'", $last_order ) ); // phpcs:ignore

			if ( empty( $new_orders ) ) {
				// No new orders, bail.
				return;
			}

			$data = array();

			foreach ( $new_orders as $order ) {
				$order = wc_get_order( $order->ID );

				if ( ! $order->get_billing_email() ) {
					continue;
				}

				if ( class_exists( '\\WC_Shipment_Tracking_Actions' ) ) {
					$WC_Shipment_Tracking_Actions = \WC_Shipment_Tracking_Actions::get_instance(); // phpcs:ignore
					$tracking_info                = $WC_Shipment_Tracking_Actions->get_tracking_items( $order->get_id() ); // phpcs:ignore
				}

				// Map shipped status to listrak value.
				$status = 4; // pending.

				if ( ! empty( $tracking_info ) ) {
					$status = 7; // shipped.
				}

				// Get shipped date if there.
				$shipped_date    = '';
				$tracking_number = '';
				$shipping_method = '';

				if ( ! empty( $tracking_info ) && ! empty( $tracking_info[0]['date_shipped'] ) ) {
					$shipped_date    = date( 'Y-m-d G:i:s', $tracking_info[0]['date_shipped'] ); // phpcs:ignore
					$tracking_number = $tracking_info[0]['tracking_number'];
					$shipping_method = $tracking_info[0]['tracking_provider'];
				}

				// Get Meta.
				$gift_note = $order->get_meta( 'gift_note', true ) ? $order->get_meta( 'gift_note', true ) : '';

				$data[] = array(
					'Email'          => $order->get_billing_email(),
					'OrderNumber'    => $order->get_order_number(),
					'DateEntered'    => date( 'Y-m-d G:i:s', strtotime( $order->get_date_created() ) ), // phpcs:ignore
					'OrderTotal'     => $order->get_total(),
					'ItemTotal'      => $order->get_subtotal(),
					'TaxTotal'       => $order->get_total_tax(),
					'ShippingTotal'  => $order->get_shipping_total(),
					'HandlingTotal'  => '0.00',
					'Status'         => $status,
					'ShipDate'       => $shipped_date,
					'TrackingNumber' => $tracking_number,
					'ShippingMethod' => $shipping_method,
					'CouponCode'     => '',
					'DiscountTotal'  => str_replace( '-', '', $order->get_discount_total() ),
					'Source'         => 'online',
					'Gift'           => $gift_note,
					'Meta1'          => $gift_note ? 'true' : 'false',
				);

				$new_last_order = $order->get_id();
			}

			// A final safety check.
			if ( count( $data ) < 2 ) {
				return;
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
