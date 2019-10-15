<?php
/**
 * Provides helper functions.
 *
 * @since	  {{VERSION}}
 *
 * @package	PDS_Send_Email_to_Basic_Student
 * @subpackage PDS_Send_Email_to_Basic_Student/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		{{VERSION}}
 *
 * @return		PDS_Send_Email_to_Basic_Student
 */
function PDSSENDEMAILTOBASICSTUDENT() {
	return PDS_Send_Email_to_Basic_Student::instance();
}