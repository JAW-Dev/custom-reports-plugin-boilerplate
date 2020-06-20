<?php
/**
 * Listrak Order Items.
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

if ( ! class_exists( __NAMESPACE__ . '\\ListrakOrderItems' ) ) {

	/**
	 * Listrak Order Items.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ListrakOrderItems extends Listrak {

		/**
		 * Nonce
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $nonce = 'listrak-order-items-report';

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
			add_action( 'wp_ajax_listrak_order_items_report', array( $this, 'listrak_order_items_report' ) );
			add_action( 'wp_ajax_nopriv_listrak_order_items_report', array( $this, 'listrak_order_items_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function listrak_order_items_report() {

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

			// Get All Orders Since Last Order.
			$new_orders = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE ID > %d AND post_type = 'shop_order'", $last_order ) ); // phpcs:ignore

			if ( empty( $new_orders ) ) {
				// No new orders, bail.
				return;
			}

			$data = array();

			foreach ( $new_orders as $order ) {
				$order = wc_get_order( $order->ID );

				$discount_amount      = 0;
				$discount_product_ids = array();

				if ( $order ) {

					// Get the product discount if applicable.
					if ( method_exists( $order, 'get_coupon_codes' ) ) {
						$order_coupons = $order->get_coupon_codes();

						if ( ! empty( $order_coupons ) ) {
							foreach ( $order_coupons as $order_coupon ) {
								$coupon_post_obj = get_page_by_title( $order_coupon, OBJECT, 'shop_coupon' );

								if ( $coupon_post_obj ) {
									$coupon_id   = $coupon_post_obj->ID;
									$coupons_obj = new \WC_Coupon( $coupon_id );

									if ( ! empty( $coupons_obj->get_product_ids ) ) {
										$discount_amount      = $coupons_obj->get_amount();
										$discount_product_ids = $coupons_obj->get_product_ids;
									}
								}
							}
						}
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

					foreach ( $order->get_items() as $item_id => $item ) {
						$discount = 0;

						// Get the discount applied to the product if applicable.
						if ( ! empty( $discount_product_ids ) ) {
							foreach ( $discount_product_ids as $discount_product_id ) {
								if ( $discount_product_id === $item_id ) {
									$discount = ( $product->get_price() - $discount_amount ) * $item->get_quantity();
								}
							}
						}
						$product = $item->get_product();

						if ( empty( $product ) || ! $product->get_sku() ) {
							continue;
						}

						$data[] = array(
							'OrderNumber'     => $order->get_order_number(),
							'SKU'             => $product->get_sku(),
							'Quantity'        => $item->get_quantity(),
							'Price'           => $product->get_price(),
							'Status'          => $status,
							'ShipDate'        => $shipped_date,
							'TrackingNumber'  => $tracking_number,
							'ShippingMethod'  => $shipping_method,
							'DiscountedPrice' => $discount,
							'ItemTotal'       => $item->get_total(),
						);

						// Remove duplicate rows.
						$data = array_map( 'unserialize', array_unique( array_map( 'serialize', $data ) ) );
					}
				}
			}

			// Merge multiple items on the same order.
			foreach ( $data as $current_key => $current_array ) {
				if ( ! isset( $current_array[0] ) ) {
					continue;
				}

				foreach ( $data as $search_key => $search_array ) {
					if ( ! isset( $search_array[0] ) ) {
						continue;
					}

					if ( $search_array[0] === $current_array[0] ) {
						if ( $search_key !== $current_key ) {

							if ( isset( $search_key[9] ) ) {
								$data[ $search_key ][9] = $search_array[9] + $current_array[9];
							}

							if ( isset( $search_key[2] ) ) {
								$data[ $search_key ][2] += $current_array[2];
							}

							if ( isset( $search_key[9] ) || isset( $search_key[2] ) ) {
								unset( $data[ $current_key ] );
							}
						}
					}
				}
			}

			array_values( $data );

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
