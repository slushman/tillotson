<?php

/**
 * Sanitize anything
 *
 * @since 		1.0.0
 * @package 	Tillotson
 */

class Tillotson_Sanitize {

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * Cleans the data
	 *
	 * @access 	public
	 * @since 	0.1
	 * @param 		mixed 		$data 		The data to sanitize.
	 * @param 		string  	$type 		The data type.
	 * @return  	mixed         			The sanitized data
	 */
	public function clean( $data, $type ) {

		$check = '';

		if ( empty( $type ) ) {

			$check = new WP_Error( 'forgot_type', __( 'Specify the data type to sanitize.', 'tillotson' ) );

		}

		if ( is_wp_error( $check ) ) {

			wp_die( $check->get_error_message(), __( 'Forgot data type', 'tillotson' ) );

		}

		$sanitized = '';

		/**
		 * Add additional santization before the default sanitization
		 */
		do_action( 'tillotson_pre_sanitize', $sanitized );

		switch ( $type ) {

			case 'radio'			:
			case 'select'			: $sanitized = $this->sanitize_random( $data ); break;

			case 'date'				:
			case 'datetime'			:
			case 'datetime-local'	:
			case 'time'				:
			case 'week'				: $sanitized = strtotime( $data ); break;

			case 'number'			:
			case 'range'			: $sanitized = intval( $data ); break;

			case 'hidden'			:
			case 'month'			:
			case 'text'				: $sanitized = sanitize_text_field( $data ); break;

			case 'checkbox'			: $sanitized = ( isset( $data ) ? 1 : 0 ); break;
			case 'color' 			: $sanitized = $this->sanitize_hex_color( $data ); break;
			case 'editor' 			: $sanitized = wp_kses_post( $data ); break;
			case 'email'			: $sanitized = sanitize_email( $data ); break;
			case 'file'				: $sanitized = sanitize_file_name( $data ); break;
			case 'tel'				: $sanitized = $this->sanitize_phone( $data ); break;
			case 'textarea'			: $sanitized = esc_textarea( $data ); break;
			case 'url'				: $sanitized = esc_url_raw( $data ); break;

		} // switch

		/**
		 * Add additional santization after the default .
		 */
		do_action( 'tillotson_post_sanitize', $sanitized );

		return $sanitized;

	} // clean()

	/**
	 * Checks a date against a format to ensure its validity
	 *
	 * @link 		http://www.php.net/manual/en/function.checkdate.php
	 * @param  		string 		$date   		The date as collected from the form field
	 * @param  		string 		$format 		The format to check the date against
	 * @return 		string 		A validated, formatted date
	 */
	private function validate_date( $date, $format = 'Y-m-d H:i:s' ) {

		$version = explode( '.', phpversion() );

		if ( ( (int) $version[0] >= 5 && (int) $version[1] >= 2 && (int) $version[2] > 17 ) ) {

			$d = DateTime::createFromFormat( $format, $date );

		} else {

			$d = new DateTime( date( $format, strtotime( $date ) ) );

		}

		return $d && $d->format( $format ) == $date;

	} // validate_date()

	/**
	 * Validates the input is a hex color.
	 *
	 * @exits 		If $color is empty.
	 * @param 		string 		$color 			The hex color string
	 * @return 		string 						The sanitized hex color string
	 */
	private function sanitize_hex_color( $color ) {

		if ( empty( $color ) ) { return FALSE; }

		$return = '';
		$color 	= trim( $color );
		$color 	= ltrim( $color, '#' );

		if ( preg_match( '/([A-Fa-f0-9]{3}){1,2}$/', $color ) ) {

			$return = $color;

		}

		return $return;

	} // sanitize_hex_color()

	/**
	 * Validates a phone number
	 *
	 * @exits 		If $phone is empty.
	 * @access 		private
	 * @since		0.1
	 * @link		http://jrtashjian.com/2009/03/code-snippet-validate-a-phone-number/
	 * @param 		string 			$phone				A phone number string
	 * @return		string|bool		$phone|FALSE		Returns the valid phone number, FALSE if not
	 */
	private function sanitize_phone( $phone ) {

		if ( empty( $phone ) ) { return FALSE; }

		if ( preg_match( '/^[+]?([0-9]?)[(|s|-|.]?([0-9]{3})[)|s|-|.]*([0-9]{3})[s|-|.]*([0-9]{4})$/', $phone ) ) {

			return trim( $phone );

		} // $phone validation

		return FALSE;

	} // sanitize_phone()

	/**
	 * Performs general cleaning functions on data
	 *
	 * @exits 		If $input is empty.
	 * @param 		mixed 	$input 		Data to be cleaned
	 * @return 		mixed 	$return 	The cleaned data
	 */
	private function sanitize_random( $input ) {

		if ( empty( $input ) ) { return ''; }

		$one	= trim( $input );
		$two	= stripslashes( $one );
		$return	= htmlspecialchars( $two );

		return $return;

	} // sanitize_random()

} // class
