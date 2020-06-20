<?php
/**
 * Orders.
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

if ( ! class_exists( __NAMESPACE__ . '\\Orders' ) ) {

	/**
	 * Orders.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Orders {

		/**
		 * Nonce
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $nonce = 'orders-report';

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
		public function hooks() {
			add_action( 'wp_ajax_orders_report', array( $this, 'orders_report' ) );
			add_action( 'wp_ajax_nopriv_orders_report', array( $this, 'orders_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function orders_report() {

			// Bail if none check fails.
			if ( $this->nonce !== $_POST['nonce'] ) { // phpcs:ignore
				exit;
			}

			$from_date = isset( $_POST['from-date'] ) ? urldecode( sanitize_text_field( wp_unslash( $_POST['from-date'] ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month' ) ); // phpcs:ignore
			$to_date   = isset( $_POST['to-date'] ) ? urldecode( sanitize_text_field( wp_unslash( $_POST['to-date'] ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month' ) ); // phpcs:ignore

			$date_range = $from_date . '...' . $to_date;

			$args = array(
				'limit'        => -1,
				'date_created' => $date_range,
			);

			$query  = new \WC_Order_Query( $args );
			$orders = $query->get_orders();

			foreach ( $orders as $order ) {

				$order_id        = $order->get_id();
				$order_data      = $order->get_data();
				$order_number    = get_post_meta( $order_id, '_order_number', true );
				$user            = get_user_by( 'email', $order_data['billing']['email'] );
				$user_url        = $user ? get_edit_user_link( $user->ID ) : '';
				$order_items     = $order->get_items();
				$personalization = $this->has_addon( $order_items, 'Personalization' ) ? 'true' : '';
				$gift_wrapping   = $this->has_addon( $order_items, 'Gift Wrapping' ) ? 'true' : '';

				$data[] = (object) array(
					'order_id'        => $order_id,
					'first_name'      => $order_data['billing']['first_name'],
					'last_name'       => $order_data['billing']['last_name'],
					'personalization' => $personalization,
					'gift_wrapping'   => $gift_wrapping,
					'email'           => esc_html( $order_data['billing']['email'] ),
					'date'            => $order_data['date_created']->date( 'Y/m/d' ),
				);
			}

			echo wp_json_encode( $data, true );
			exit;
		}

		/**
		 * Has Addon
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array  $order_items The items iin the order.
		 * @param string $type        The type of addon to check for.
		 *
		 * @return boolean
		 */
		public function has_addon( $order_items, $type ) {
			foreach ( $order_items as $order_item ) {
				$item_meta_data = $order_item->get_meta_data();

				foreach ( $item_meta_data as $item_meta ) {
					$data = $item_meta->get_data();
					$key  = $data['key'];

					if ( $type === $key ) {
						return true;
					}
				}
			}
			return false;
		}
	}
}
