<?php
/**
 * Dashboard Page.
 *
 * @package    Custom_Reports
 * @subpackage Custom_Reports/Inlcudes/Classes
 * @author     Jason Witt
 * @copyright  Copyright (c) 2020, Jason Witt
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace CustomReports\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\DashboardPage' ) ) {

	/**
	 * Dashboard Page.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class DashboardPage {

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
			add_action( 'admin_menu', array( $this, 'init' ) );
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function init() {
			$icon = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj48cGF0aCBmaWxsPSJibGFjayIgZD0iTTM2MCAwSDI0QzEwLjcgMCAwIDEwLjcgMCAyNHY0NjRjMCAxMy4zIDEwLjcgMjQgMjQgMjRoMzM2YzEzLjMgMCAyNC0xMC43IDI0LTI0VjI0YzAtMTMuMy0xMC43LTI0LTI0LTI0ek0xMjggNDAwYzAgOC44LTcuMiAxNi0xNiAxNkg4MGMtOC44IDAtMTYtNy4yLTE2LTE2di0zMmMwLTguOCA3LjItMTYgMTYtMTZoMzJjOC44IDAgMTYgNy4yIDE2IDE2djMyem0wLTEyOGMwIDguOC03LjIgMTYtMTYgMTZIODBjLTguOCAwLTE2LTcuMi0xNi0xNnYtMzJjMC04LjggNy4yLTE2IDE2LTE2aDMyYzguOCAwIDE2IDcuMiAxNiAxNnYzMnptMC0xMjhjMCA4LjgtNy4yIDE2LTE2IDE2SDgwYy04LjggMC0xNi03LjItMTYtMTZ2LTMyYzAtOC44IDcuMi0xNiAxNi0xNmgzMmM4LjggMCAxNiA3LjIgMTYgMTZ2MzJ6bTE5MiAyNDhjMCA0LjQtMy42IDgtOCA4SDE2OGMtNC40IDAtOC0zLjYtOC04di0xNmMwLTQuNCAzLjYtOCA4LThoMTQ0YzQuNCAwIDggMy42IDggOHYxNnptMC0xMjhjMCA0LjQtMy42IDgtOCA4SDE2OGMtNC40IDAtOC0zLjYtOC04di0xNmMwLTQuNCAzLjYtOCA4LThoMTQ0YzQuNCAwIDggMy42IDggOHYxNnptMC0xMjhjMCA0LjQtMy42IDgtOCA4SDE2OGMtNC40IDAtOC0zLjYtOC04di0xNmMwLTQuNCAzLjYtOCA4LThoMTQ0YzQuNCAwIDggMy42IDggOHYxNnoiLz48L3N2Zz4=';

			add_menu_page(
				__( 'Custom Reports', 'custom-reports' ),
				__( 'Custom Reports', 'custom-reports' ),
				'manage_options',
				'custom-reports',
				array( $this, 'render' ),
				'data:image/svg+xml;base64,' . $icon
			);
		}

		/**
		 * Render
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function render() {
			?>
			<div class="wrap">
				<h1>Reports</h1>
				<div class="custom-reports">
					<?php
						$this->render_item( 'orders-report', 'Report for the distribution center', true );
						$this->render_item( 'product-images-report', 'Report to get the image URL\'s for the products' );
						$this->listrak( 'listrak-orders-report', 'Orders report to send to Listrak<br/>Uncheck <strong>Test</strong> to send the report to Listak' );
						$this->listrak( 'listrak-order-items-report', 'Order Items report to send to Listrak<br/>Uncheck <strong>Test</strong> to send the report to Listak' );
						$this->listrak( 'listrak-customers-report', 'Customers report to send to Listrak<br/>Uncheck <strong>Test</strong> to send the report to Listak' );
						$this->listrak( 'listrak-products-report', 'Products report to send to Listrak<br/>Uncheck <strong>Test</strong> to send the report to Listak' );
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Render Item
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string  $name        The report name.
		 * @param string  $description The report description.
		 * @param boolean $filter      If to add the date filter.
		 *
		 * @return void
		 */
		public function render_item( $name = '', $description = '', $filter = false ) {

			// Bail if name is not set.
			if ( empty( $name ) ) {
				return;
			}

			$name  = strtolower( str_replace( array( '_', ' ' ), '-', $name ) );
			$title = ucwords( str_replace( array( '_', '-' ), ' ', $name ) );
			?>
			<div class="custom-reports__item">
				<h2><?php echo esc_html( $title ); ?></h2>
				<?php if ( ! empty( $description ) ) { ?>
					<p><?php echo wp_kses_post( $description ); ?></p>
				<?php } ?>
				<form method="get" action="/">
					<?php if ( $filter ) { ?>
						<p class="custom-reports__filter">
							<label for="<?php echo esc_attr( $name ); ?>-date-from">From</label>
							<input type="text" id="<?php echo esc_attr( $name ); ?>-date-from" name="date_from" class="report-filter" placeholder="Date From" value="">
							<label for="<?php echo esc_attr( $name ); ?>-date-to">To</label>
							<input type="text" id="<?php echo esc_attr( $name ); ?>-date-to" name="date_to" class="report-filter" placeholder="Date To" value="">
						</p>
					<?php } ?>
					<button id="<?php echo esc_attr( str_replace( array( '_', ' ' ), '-', $name ) ); ?>" class="button" data-nonce="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $title ); ?></button>
				</form>
				<hr/>
			</div>
			<?php
		}

		/**
		 * Render Item
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $name        The report name.
		 * @param string $description The report description.
		 *
		 * @return void
		 */
		public function listrak( $name = '', $description = '' ) {

			// Bail if name is not set.
			if ( empty( $name ) ) {
				return;
			}

			$name  = strtolower( str_replace( array( '_', ' ' ), '-', $name ) );
			$title = ucwords( str_replace( array( '_', '-' ), ' ', $name ) );
			?>
			<div class="custom-reports__item">
				<h2><?php echo esc_html( $title ); ?></h2>
				<?php if ( ! empty( $description ) ) { ?>
					<p><?php echo wp_kses_post( $description ); ?></p>
				<?php } ?>
				<form method="get" action="/">
					<p class="custom-reports__filter">
						<label for="<?php echo esc_attr( $name ); ?>test">Test</label>
						<input type="checkbox" id="<?php echo esc_attr( $name ); ?>-test" name="test" class="report-filter" placeholder="Date From" checked="yes">
					</p>
					<button id="<?php echo esc_attr( str_replace( array( '_', ' ' ), '-', $name ) ); ?>" class="button" data-nonce="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $title ); ?></button>
				</form>
				<hr/>
			</div>
			<?php
		}
	}
}
