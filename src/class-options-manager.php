<?php
/**
 * Options_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the options manager class.
 */
class Options_Manager implements Options_Manager_Interface {
	const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const LOWER = 'abcdefghijklmnopqrstuvwxyz';
	const NUMBERS = '0123456789';

	protected $alphabet_map = [
		'lower' => self::LOWER,
		'upper' => self::UPPER,
		'lowerupper' => self::LOWER . self::UPPER,
		'lowernumber' => self::LOWER . self::NUMBERS,
		'uppernumber' => self::UPPER . self::NUMBERS,
		'all' => self::LOWER . self::UPPER . self::NUMBERS,
	];
	protected $regex_map = [
		'lower' => 'a-z',
		'upper' => 'A-Z',
		'lowerupper' => 'a-zA-Z',
		'lowernumber' => 'a-z0-9',
		'uppernumber' => 'A-Z0-9',
		'all' => 'a-zA-Z0-9',
	];

	public function __construct( Key_Value_Store_Interface $store ) {
		$this->store = $store;
	}

	public function alphabet() : string {
		$alphabet = defined( 'WP_HASHIDS_ALPHABET' )
			? WP_HASHIDS_ALPHABET
			: $this->store->get( 'alphabet' );


		return $this->get_alphabet( $alphabet );
	}

	public function min_length() : int {
		$min_length = defined( 'WP_HASHIDS_MIN_LENGTH' )
			? WP_HASHIDS_MIN_LENGTH
			: $this->store->get( 'min_length' );

		return $this->get_min_length( $min_length );
	}

	public function regex() : string {
		$alphabet = defined( 'WP_HASHIDS_ALPHABET' )
			? WP_HASHIDS_ALPHABET
			: $this->store->get( 'alphabet' );

		return $this->get_regex( $alphabet );
	}

	public function rewrite_tag() : string {
		return '%hashid%';
	}

	public function salt() : string {
		$salt = defined( 'WP_HASHIDS_SALT' )
			? WP_HASHIDS_SALT
			: $this->store->get( 'salt' );

		return $this->get_salt( $salt );
	}

	protected function get_alphabet( $alphabet ) : string {
		if ( ! $this->is_valid_alphabet( $alphabet ) ) {
			$alphabet = 'all';
		}

		return $this->alphabet_map[ $alphabet ];
	}

	protected function get_min_length( $min_length ) : int {
		if ( is_null( $min_length ) ) {
			$min_length = 6;
		}

		return absint( $min_length );
	}

	protected function get_regex( $alphabet ) : string {
		if ( ! $this->is_valid_alphabet( $alphabet ) ) {
			$alphabet = 'all';
		}

		return $this->regex_map[ $alphabet ];
	}

	protected function get_salt( $salt ) : string {
		if ( ! $salt ) {
			// @todo
			$salt = ( new Salt_Generator )->generate();
			$this->store->set( 'salt', $salt );
		}

		return (string) $salt;
	}

	protected function is_valid_alphabet( $alphabet ) : bool {
		return in_array( $alphabet, array_keys( $this->alphabet_map ), true );
	}
}
