<?php
/**
 * Options_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the options manager class.
 */
class Options_Manager {
	const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const LOWER = 'abcdefghijklmnopqrstuvwxyz';
	const NUMBERS = '0123456789';

	const ALPHABET_MAP = [
		'lower' => [
			'alphabet' => self::LOWER,
			'label' => 'Lowercase',
			'regex' => 'a-z',
		],
		'upper' => [
			'alphabet' => self::UPPER,
			'label' => 'Uppercase',
			'regex' => 'A-Z',
		],
		'lowerupper' => [
			'alphabet' => self::LOWER . self::UPPER,
			'label' => 'Lowercase and uppercase',
			'regex' => 'a-zA-Z',
		],
		'lowernumber' => [
			'alphabet' => self::LOWER . self::NUMBERS,
			'label' => 'Lowercase and numbers',
			'regex' => 'a-z0-9',
		],
		'uppernumber' => [
			'alphabet' => self::UPPER . self::NUMBERS,
			'label' => 'Uppercase and numbers',
			'regex' => 'A-Z0-9',
		],
		'all' => [
			'alphabet' => self::LOWER . self::UPPER . self::NUMBERS,
			'label' => 'Lowercase, uppercase and numbers',
			'regex' => 'a-zA-Z0-9',
		],
	];

	/**
	 * Options store instance.
	 *
	 * @var Options_Store
	 */
	protected $store;

	/**
	 * Class constructor.
	 *
	 * @param Options_Store $store Options store instance.
	 */
	public function __construct( Options_Store $store ) {
		$this->store = $store;
	}

	/**
	 * Get the configured alphabet - Looks in constants first, then DB.
	 *
	 * @return string
	 */
	public function alphabet() {
		// If not set, will be "all" as set in ->register_settings().
		$alphabet = $this->sanitize_alphabet( $this->store->get( 'alphabet' ) );

		return self::ALPHABET_MAP[ $alphabet ]['alphabet'];
	}

	/**
	 * Flush rewrite rules when the wp_hashids_alphabet option is updated.
	 *
	 * @param  string $old_value Previous alphabet option value.
	 * @param  string $value     New alphabet option value.
	 *
	 * @return void
	 */
	public function flush_rewrites_on_save( $old_value, $value ) {
		if ( $old_value === $value ) {
			return;
		}

		delete_option( 'rewrite_rules' );
	}

	/**
	 * Get the configured minimum length - Looks in constants first then DB.
	 *
	 * @return integer
	 */
	public function min_length() {
		// If not set, will be 6 as set in ->register_settings().
		return absint( $this->store->get( 'min_length' ) );
	}

	/**
	 * Get the regular expression matching the currently configured alphabet.
	 *
	 * @return string
	 */
	public function regex() {
		// If not set, will be "all" as set in ->register_settings().
		$alphabet = $this->sanitize_alphabet( $this->store->get( 'alphabet' ) );

		return self::ALPHABET_MAP[ $alphabet ]['regex'];
	}

	/**
	 * Register plugin settings with WordPress.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'wp_hashids_group', 'wp_hashids_alphabet', [
			'default' => 'all',
			'sanitize_callback' => [ $this, 'sanitize_alphabet' ],
			'show_in_rest' => true,
		] );

		register_setting( 'wp_hashids_group', 'wp_hashids_min_length', [
			'default' => 6,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
			'type' => 'integer',
		] );

		register_setting( 'wp_hashids_group', 'wp_hashids_salt', [
			'sanitize_callback' => [ $this, 'sanitize_salt' ],
			'show_in_rest' => true,
		] );
	}

	/**
	 * Get the hashids rewrite tag.
	 *
	 * @return string
	 */
	public function rewrite_tag() {
		return '%hashid%';
	}

	/**
	 * Get the currently configured salt - Looks in constants first then DB. If none
	 * found, a new salt is generated and automatically saved to the DB.
	 *
	 * @return string
	 */
	public function salt() {
		$needs_save = false;

		// If not set, will be null - there is no default.
		$salt = $this->store->get( 'salt' );

		// First run it will be null (non-existent in DB).
		if ( is_null( $salt ) ) {
			$needs_save = true;
		}

		$salt = $this->sanitize_salt( $salt );

		if ( $needs_save ) {
			$this->store->set( 'salt', $salt );
		}

		return $salt;
	}

	/**
	 * Sanitize the alphabet setting.
	 *
	 * @param  mixed $alphabet Alphabet setting.
	 *
	 * @return string
	 */
	public function sanitize_alphabet( $alphabet ) {
		if ( ! $this->is_valid_alphabet( $alphabet ) ) {
			$alphabet = 'all';
		}

		return $alphabet;
	}

	/**
	 * Sanitize the salt setting or generate a new salt if null given.
	 *
	 * @param  mixed $salt Salt setting.
	 *
	 * @return string
	 */
	public function sanitize_salt( $salt ) {
		if ( is_null( $salt ) ) {
			// @todo
			$salt = ( new Salt_Generator() )->generate();
		}

		return (string) $salt;
	}

	/**
	 * Grab plugin settings from constants when they are defined.
	 *
	 * @param  mixed  $pre_option Option value before checking DB.
	 * @param  string $option     The option key.
	 *
	 * @return mixed
	 */
	public function use_constants_when_defined( $pre_option, $option ) {
		if ( ! is_string( $option ) ) {
			return $pre_option;
		}

		$constant = strtoupper( $option );

		if ( ! defined( $constant ) ) {
			return $pre_option;
		}

		$value = constant( $constant );

		// If null we could get caught constantly regenerating the salt.
		if ( false === $value || is_null( $value ) ) {
			return $pre_option;
		}

		return $value;
	}

	/**
	 * Check if a given alphabet setting is valid.
	 *
	 * @param  mixed $alphabet Alphabet setting.
	 *
	 * @return boolean
	 */
	protected function is_valid_alphabet( $alphabet ) {
		return in_array( $alphabet, array_keys( self::ALPHABET_MAP ), true );
	}
}
