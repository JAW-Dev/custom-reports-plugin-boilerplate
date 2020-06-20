<?php
/**
 *
 * Plugin Name: Custom Reports
 * Description: Customized CSV reports
 * Version:     1.0.0
 * Author:      Jason Witt
 * License:     GNU General Public License v2 or later
 * Text Domain: custom-reports
 * Domain Path: /languages
 *
 * @package    Custom_Reports
 * @author     Jason Witt
 * @copyright  Copyright (c) 2020, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace CustomReports;

use CustomReports\Includes\Classes as Classes;
use CustomReports\Includes\Classes\Reports as Reports;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
if ( file_exists( trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php' ) ) {
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';
}

if ( ! defined( 'CUSTOM_REPORTS_VERSION' ) ) {
	define( 'CUSTOM_REPORTS_VERSION', '1.0.0.' );
}

if ( ! defined( 'CUSTOM_REPORTS_DIR_URL' ) ) {
	define( 'CUSTOM_REPORTS_DIR_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( ! defined( 'CUSTOM_REPORTS_DIR_PATH' ) ) {
	define( 'CUSTOM_REPORTS_DIR_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'CUSTOM_REPORTS_PRFIX' ) ) {
	define( 'CUSTOM_REPORTS_PRFIX', 'custom-reports' );
}

if ( ! class_exists( __NAMESPACE__ . '\\CustomReports' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class CustomReports {

		/**
		 * Singleton instance of plugin.
		 *
		 * @var   static
		 * @since 1.0.0
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return static
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
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

			// Load translated strings for plugin.
			load_plugin_textdomain( 'custom-reports', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Enqueue Styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			// Enqueue Scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Include Classes.
			$this->include_classes();
		}

		/**
		 * Include Classes.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function include_classes() {
			new Classes\DashboardPage();
			new Reports\Orders();
			new Reports\ProductImages();
			new Reports\ListrakOrders();
			new Reports\ListrakOrderItems();
			new Reports\ListrakCustomers();
			new Reports\ListrakProducts();
		}

		/**
		 * Enqueue Styles
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $hook The page hook.
		 *
		 * @return void
		 */
		public function enqueue_styles( $hook ) {

			if ( 'toplevel_page_custom-reports' === $hook ) {
				$admin_file     = 'src/styles/admin.css';
				$admin_css      = trailingslashit( CUSTOM_REPORTS_DIR_PATH ) . $admin_file;
				$admin_filetime = file_exists( $admin_css ) ? filemtime( $admin_css ) : '1.0.0';
				$admin_handle   = 'custom-reports';

				wp_register_style( $admin_handle, trailingslashit( CUSTOM_REPORTS_DIR_URL ) . $admin_file, array(), $admin_filetime, 'all' );
				wp_enqueue_style( $admin_handle );

				wp_enqueue_style( 'jquery-ui-datepicker-style', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), '1.11.4', 'all' ); // phpcs:ignore
			}

			$menu_file     = 'src/styles/index.css';
			$menu_css      = trailingslashit( CUSTOM_REPORTS_DIR_PATH ) . $menu_file;
			$menu_filetime = file_exists( $menu_css ) ? filemtime( $menu_css ) : '1.0.0';
			$menu_handle   = 'custom-reports-menu';

			wp_register_style( $menu_handle, trailingslashit( CUSTOM_REPORTS_DIR_URL ) . $menu_file, array(), $menu_filetime, 'all' );
			wp_enqueue_style( $menu_handle );
		}

		/**
		 * Enqueue Scripts
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $hook The page hook.
		 *
		 * @return void
		 */
		public function enqueue_scripts( $hook ) {
			if ( 'toplevel_page_custom-reports' === $hook ) {
				$admin_file     = 'src/scripts/admin.js';
				$admin_css      = trailingslashit( CUSTOM_REPORTS_DIR_PATH ) . $admin_file;
				$admin_filetime = file_exists( $admin_css ) ? filemtime( $admin_css ) : '1.0.0';
				$admin_handle   = 'custom-reports';

				wp_register_script( $admin_handle, trailingslashit( CUSTOM_REPORTS_DIR_URL ) . $admin_file, array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), $admin_filetime, true );

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'custom-reports' );
			}
		}

		/**
		 * Activate the plugin.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function _activate() {
			flush_rewrite_rules();
		}

		/**
		 * Decativate the plugin.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function _deactivate() {}
	}
}

/**
 * Return an instance of the plugin class.
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @return CustomReports instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function custom_reports() {
	return CustomReports::get_instance();
}
add_action( 'plugins_loaded', array( custom_reports(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( custom_reports(), '_activate' ) );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, array( custom_reports(), '_deactivate' ) );
