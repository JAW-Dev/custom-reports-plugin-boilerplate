<?php
/**
 * Listrak Products.
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

if ( ! class_exists( __NAMESPACE__ . '\\ListrakProducts' ) ) {

	/**
	 * Listrak Products.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ListrakProducts extends Listrak {

		/**
		 * Nonce
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $nonce = 'listrak-products-report';

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
			add_action( 'wp_ajax_listrak_products_report', array( $this, 'listrak_products_report' ) );
			add_action( 'wp_ajax_nopriv_listrak_products_report', array( $this, 'listrak_products_report' ) );
		}

		/**
		 * Orders Report
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function listrak_products_report() {
			global $wpdb;

			// Get All Orders Since Last Order.
			$products = wc_get_products( array( 'post_status' => 'publish' ) );

			if ( empty( $products ) ) {
				// No products, bail.
				return;
			}

			$excluded_categories = array(
				'alligator',          // Alligator.
				'corporate-gifts',    // Corporate Gifts.
				'gifts-3',            // Gifts.
				'gifts',              // Gift Guide.
				'gifts-under-350',    // Gifts under $350.
				'housewarming-gifts', // Housewarming Gifts.
				'leather-gifts',      // Leather Gifts.
				'mothers-day',        // Mother's Day.
				'new-items',          // New Items.
				'new-arrivals',       // New Arrivals.
				'secret-stash',       // Secret Stash.
				'last-chance',        // Last Chance.
				'richard-haines',     // Richard Haines.
				'best-sellers',       // Best Sellers.
			);

			$data = array();

			$products = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => -1,
				)
			);

			foreach ( $products as $product ) {

				$category            = '';
				$subcategory         = '';
				$categories          = wp_get_object_terms( $product->get_id(), 'product_cat' );
				$possible_categories = array();

				// Get a list of possible categories.
				foreach ( $categories as $cat ) {
					if ( 0 !== $cat->parent ) {
						continue;
					}
					if ( in_array( $cat->slug, $excluded_categories, true ) ) {
						continue;
					}

					$possible_categories[] = $cat;
				}

				$found = false;

				foreach ( $possible_categories as $pc ) {
					if ( $found ) {
						break;
					}

					$subcategories = get_terms( 'product_cat', array( 'parent' => $pc->term_id ) );

					$category = $subcategory = $pc->name;

					if ( ! empty( $subcategories ) ) {
						// Get subcategory of item.
						foreach ( $categories as $cat ) {
							if ( $cat->parent === $pc->term_id ) {
								$found       = true;
								$subcategory = $cat->name;
								break;
							}
						}
					}
				}

				$category    = html_entity_decode( $category );
				$subcategory = html_entity_decode( $subcategory );
				$description = preg_replace( "/\n\s+/", "\n", rtrim( html_entity_decode( strip_tags( $product->get_description() ) ) ) ); // phpcs:ignore
				$description = iconv( mb_detect_encoding( $description ), 'UTF-8//IGNORE', $description );

				if ( 'variable' === $product->get_type() ) {
					foreach ( $product->get_available_variations() as $variation ) {

						// Skip if product doesn't have a SKU.
						if ( ! $variation['sku'] ) {
							continue;
						}

						$variable_product = wc_get_product( $variation['variation_id'] );
						$parent_id        = $variable_product->get_parent_id() ? $variable_product->get_parent_id() : '';
						$parent_product   = $parent_id ? wc_get_product( $parent_id ) : '';
						$parent_sku       = $parent_product ? $parent_product->get_sku() : '';

						$raw_attributes = wc_get_formatted_variation( $variable_product, true );
						$raw_attributes = explode( ', ', $raw_attributes );
						$attributes     = array();

						foreach ( $raw_attributes as $ra ) {
							$x = explode( ': ', $ra );

							if ( isset( $attributes[ $x[0] ] ) ) {
								$attributes[ $x[0] ] = $x[1];
							}
						}

						if ( 'Antique Douglas Leather Belt' === $variable_product->get_name() ) {
							error_log( print_r( $raw_attributes, true ) ); // phpcs:ignore
						}

						$data[] = array(
							'SKU'          => $variation['sku'],
							'Title'        => $variable_product->get_name(),
							'ImageURL'     => $variation['image']['url'],
							'LinkURL'      => $product->get_permalink(),
							'Description'  => preg_replace( '!\s+!', ' ', $description ),
							'Price'        => $variation['display_regular_price'],
							'Brand'        => '',
							'Category'     => $category,
							'SubCategory'  => $subcategory,
							'SalePrice'    => $variation['display_price'] < $variation['display_regular_price'] ? $variation['display_price'] : '',
							'OnSale'       => $variation['display_price'] < $variation['display_regular_price'] ? 'True' : 'False',
							'QOH'          => $variation['max_qty'] ? $variation['max_qty'] : '0',
							'InStock'      => $variation['is_in_stock'] ? 'True' : 'False',
							'MasterSKU'    => '',
							'ReviewURL'    => $product->get_permalink() . '#respond',
							'Discontinued' => 'False',
							'Size'         => ! empty( $attributes['Size'] ) ? $attributes['Size'] : '',
							'Color'        => ! empty( $attributes['Color'] ) ? $attributes['Color'] : '',
							'MSRP'         => $variation['display_regular_price'],
							'FirstName'    => $product->get_name(),
							'MiddleName'   => '',
							'LastName'     => ! empty( $variation['attributes']['attribute_colors'] ) ? $variation['attributes']['attribute_colors'] : '',
						);

						$data[] = array(
							'SKU'          => $parent_product->get_sku(),
							'Title'        => $parent_product->get_name(),
							'ImageURL'     => get_the_post_thumbnail_url( $parent_product->get_id() ),
							'LinkURL'      => $product->get_permalink(),
							'Description'  => preg_replace( '!\s+!', ' ', $description ),
							'Price'        => $parent_product->get_regular_price(),
							'Brand'        => '',
							'Category'     => $category,
							'SubCategory'  => $subcategory,
							'SalePrice'    => $parent_product->get_price(),
							'OnSale'       => $parent_product->get_price() < $parent_product->get_regular_price() ? 'True' : 'False',
							'QOH'          => $parent_product->get_stock_quantity() > 0 ? $parent_product->get_stock_quantity() : '0',
							'InStock'      => $parent_product->get_stock_quantity() > 0 ? 'True' : 'False',
							'MasterSKU'    => $parent_product->get_sku(),
							'ReviewURL'    => $product->get_permalink() . '#respond',
							'Discontinued' => 'False',
							'Size'         => '',
							'Color'        => $parent_product->get_attribute( 'pa_color' ),
							'MSRP'         => $parent_product->get_regular_price(),
							'FirstName'    => $product->get_name(),
							'MiddleName'   => '',
							'LastName'     => '',
						);
					}
				} elseif ( 'simple' === $product->get_type() ) {

					// Skip if product doesn't have SKU.
					if ( ! $product->get_sku() ) {
						continue;
					}

					$data[] = array(
						'SKU'          => $product->get_sku(),
						'Title'        => $product->get_name(),
						'ImageURL'     => get_the_post_thumbnail_url( $product->get_id() ),
						'LinkURL'      => $product->get_permalink(),
						'Description'  => preg_replace( '!\s+!', ' ', $description ),
						'Price'        => $product->get_regular_price(),
						'Brand'        => '',
						'Category'     => $category,
						'SubCategory'  => $subcategory,
						'SalePrice'    => $product->get_price(),
						'OnSale'       => $product->get_price() < $product->get_regular_price() ? 'True' : 'False',
						'QOH'          => $product->get_stock_quantity() > 0 ? $product->get_stock_quantity() : '0',
						'InStock'      => $product->get_stock_quantity() > 0 ? 'True' : 'False',
						'MasterSKU'    => '',
						'ReviewURL'    => $product->get_permalink() . '#respond',
						'Discontinued' => 'False',
						'Size'         => '',
						'Color'        => $product->get_attribute( 'pa_color' ),
						'MSRP'         => $product->get_regular_price(),
						'FirstName'    => $product->get_name(),
						'MiddleName'   => '',
						'LastName'     => $product->get_attribute( 'pa_color' ),
					);
				}
			}

			$new_data = array();
			$temp     = array();

			foreach ( $data as $item ) {
				if ( ! empty( $item['SKU'] ) ) {
					if ( ! in_array( $item['SKU'], $temp, true ) ) {
						$temp[]     = $item['SKU'];
						$new_data[] = $item;
					}
				}
			}

			$data = $new_data;

			if ( ! $_POST['test'] ) { // phpcs:ignore
				$this->upload( $new_data );
				echo 'sent';
				exit;
			} else {
				echo wp_json_encode( $new_data, true );
				exit;
			}
		}
	}
}
