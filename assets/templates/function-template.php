<?php
/**
 * Template.
 *
 * @package    Custom_Reports
 * @subpackage Custom_Reports/Inlcudes/Functions/Example
 * @author     Jason Witt
 * @copyright  Copyright (c) 2020, Jason Witt
 * @license    GPL-2.0
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'custom-reports_template' ) ) {
	/**
	 * Template.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function custom-reports_template() {
		echo 'This is an example function template tag';
	}
}
