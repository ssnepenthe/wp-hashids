<?php
/**
 * Class Salt_Generator.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the salt generator class.
 */
class Salt_Generator {
	/**
	 * Attempt to generate a salt, fallback to the WP.org secret keys endpoint and
	 * fallback one more time to the wp_generate_password() function.
	 *
	 * Mostly borrowed from wp-admin/setup-config.php.
	 *
	 * @param  integer $length Desired salt string length.
	 *
	 * @return string
	 */
	public function generate( $length = 64 ) {
		// Arbitrary minimum length of 10 characters.
		$length = max( 10, (int) $length );

		// Maximum length of 64 characters to match WP.org API.
		$length = min( $length, 64 );

		$salt = '';

		try {
			$salt = $this->generate_salt( $length );
		} catch ( Exception $e ) {
			$salt = $this->fetch_salt( $length );
		}

		if ( ! $salt ) {
			$salt = wp_generate_password( $length, true, true );
		}

		return $salt;
	}

	/**
	 * Fetch a salt from the WP.org secret keys endpoint.
	 *
	 * @param  integer $length The desired salt length.
	 *
	 * @return string
	 */
	protected function fetch_salt( $length ) {
		$salt = '';
		$salts = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );

		if ( ! is_wp_error( $salts ) ) {
			$salts = array_filter( explode(
				"\n",
				wp_remote_retrieve_body( $salts )
			) );

			$salt = substr( $salts[ rand( 0, count( $salts ) - 1 ) ], 28, $length );
		}

		return $salt;
	}

	/**
	 * Generate a salt locally.
	 *
	 * @param  integer $length The desired salt length.
	 *
	 * @return string
	 */
	protected function generate_salt( $length ) {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
		$salt = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$salt .= substr(
				$alphabet,
				random_int( 0, strlen( $alphabet ) - 1 ),
				1
			);
		}

		return $salt;
	}
}
