<?php
/**
 * Product Images.
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

if ( ! class_exists( __NAMESPACE__ . '\\ProductImages' ) ) {

	/**
	 * Product Images.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ProductImages {

		/**
		 * Nonce
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $nonce = 'product-images-report';

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
			add_action( 'wp_ajax_product_images_report', array( $this, 'product_images_report' ) );
			add_action( 'wp_ajax_nopriv_product_images_report', array( $this, 'product_images_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function product_images_report() {

			// Bail if none check fails.
			if ( $this->nonce !== $_POST['nonce'] ) { // phpcs:ignore
				exit;
			}

			$data     = array();
			$products = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => -1,
				)
			);

			foreach ( $products as $product ) {
				if ( 'variable' === $product->get_type() ) {
					foreach ( $product->get_available_variations() as $variation ) {
						// Skip if product doesn't have a SKU.
						if ( ! $variation['sku'] ) {
							continue;
						}

						$variable_product = wc_get_product( $variation['variation_id'] );

						$images = array();

						if ( ! empty( $variation['jck_additional_images'] ) ) {
							foreach ( $variation['jck_additional_images'] as $image_ids ) {
								$images[] = $image_ids['url'];
							}
						}

						if ( ! empty( $product_data['image_id'] ) ) {
							$image_1 = wp_get_attachment_image_src( $variation['image_id'] )[0];
							$image_2 = isset( $images[0] ) ? $images[0] : '';
							$image_3 = isset( $images[1] ) ? $images[1] : '';
							$image_4 = isset( $images[2] ) ? $images[2] : '';
							$image_5 = isset( $images[3] ) ? $images[3] : '';
							$image_6 = isset( $images[4] ) ? $images[4] : '';
						} else {
							$image_1 = isset( $images[0] ) ? $images[0] : '';
							$image_2 = isset( $images[1] ) ? $images[1] : '';
							$image_3 = isset( $images[2] ) ? $images[2] : '';
							$image_4 = isset( $images[3] ) ? $images[3] : '';
							$image_5 = isset( $images[4] ) ? $images[4] : '';
							$image_6 = isset( $images[5] ) ? $images[5] : '';
						}

						$data[] = array(
							'A' => $variation['sku'],
							'B' => $image_1,
							'C' => $image_2,
							'D' => $image_3,
							'E' => $image_4,
							'F' => $image_5,
							'G' => $image_6,
						);
					}
				} elseif ( 'simple' === $product->get_type() ) {
					// Skip if product doesn't have SKU.
					if ( ! $product->get_sku() ) {
						continue;
					}

					$product_data = $product->get_data();
					$images       = array();

					if ( ! empty( $product_data['gallery_image_ids'] ) ) {
						foreach ( $product_data['gallery_image_ids'] as $image_ids ) {
							$images[] = wp_get_attachment_image_src( $image_ids )[0];
						}
					}

					if ( ! empty( $product_data['image_id'] ) ) {
						$image_1 = wp_get_attachment_image_src( $product_data['image_id'] )[0];
						$image_2 = isset( $images[0] ) ? $images[0] : '';
						$image_3 = isset( $images[1] ) ? $images[1] : '';
						$image_4 = isset( $images[2] ) ? $images[2] : '';
						$image_5 = isset( $images[3] ) ? $images[3] : '';
						$image_6 = isset( $images[4] ) ? $images[4] : '';
					} else {
						$image_1 = isset( $images[0] ) ? $images[0] : '';
						$image_2 = isset( $images[1] ) ? $images[1] : '';
						$image_3 = isset( $images[2] ) ? $images[2] : '';
						$image_4 = isset( $images[3] ) ? $images[3] : '';
						$image_5 = isset( $images[4] ) ? $images[4] : '';
						$image_6 = isset( $images[5] ) ? $images[5] : '';
					}

					$data[] = array(
						'A' => $product->get_sku(),
						'B' => $image_1,
						'C' => $image_2,
						'D' => $image_3,
						'E' => $image_4,
						'F' => $image_5,
						'G' => $image_6,
					);
				}
			}

			echo wp_json_encode( $data, true );
			exit;
		}
	}
}
