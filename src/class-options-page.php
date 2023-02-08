<?php
/**
 * Options_Page class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the options page class.
 */
class Options_Page {
	/**
	 * Options manager instance.
	 *
	 * @var Options_Manager
	 */
	protected $options;

	/**
	 * Template instance.
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager $options  Options manager instance.
	 * @param Template        $template Template instance.
	 */
	public function __construct( Options_Manager $options, Template $template ) {
		$this->options = $options;
		$this->template = $template;
	}

	/**
	 * Register the options page with WordPress.
	 *
	 * @return void
	 */
	public function register_page() {
		add_options_page(
			'WP Hashids Settings',
			'WP Hashids',
			'manage_options',
			'wp-hashids',
			function() {
				echo $this->template->render( 'option-page', [ // WPCS: XSS OK.
					'group' => 'wp_hashids_group',
					'page' => 'wp-hashids',
				] );
			}
		);
	}

	/**
	 * Register the settings sections and fields for the options page.
	 *
	 * @return void
	 */
	public function register_sections_and_fields() {
		add_settings_section(
			'wp_hashids',
			'Configure WP Hashids',
			function() {
				echo $this->template->render( 'option-section' ); // WPCS: XSS OK.
			},
			'wp-hashids'
		);

		add_settings_field(
			Options_Manager::ALPHABET_OPTION_KEY,
			'Alphabet',
			function() {
				$current = $this->options->alphabet();

				if ( defined( 'WP_HASHIDS_ALPHABET' ) ) {
					$alphabet = array_filter(
						Options_Manager::ALPHABET_MAP,
						function( $alpha ) use ( $current ) {
							return $current === $alpha['alphabet'];
						}
					);

					$values = reset( $alphabet );
					$key = key( $alphabet );

					echo $this->template->render( 'option-alphabet-disabled', [ // WPCS: XSS OK.
						'current' => $key,
						'regex' => $values['regex'],
					] );
				} else {
					echo $this->template->render( 'option-alphabet', [ // WPCS: XSS OK.
						'current' => $current,
						'options' => Options_Manager::ALPHABET_MAP,
					] );
				}
			},
			'wp-hashids',
			'wp_hashids'
		);

		add_settings_field(
			Options_Manager::MIN_LENGTH_OPTION_KEY,
			'Minimum Length',
			function() {
				$template = 'option-min-length';

				if ( defined( 'WP_HASHIDS_MIN_LENGTH' ) ) {
					$template .= '-disabled';
				}

				echo $this->template->render( $template, [ // WPCS: XSS OK.
					'value' => $this->options->min_length(),
				] );
			},
			'wp-hashids',
			'wp_hashids'
		);

		add_settings_field(
			Options_Manager::SALT_OPTION_KEY,
			'Hashids Salt',
			function() {
				$template = 'option-salt';

				if ( defined( 'WP_HASHIDS_SALT' ) ) {
					$template .= '-disabled';
				}

				echo $this->template->render( $template, [ // WPCS: XSS OK.
					'value' => $this->options->salt(),
				] );
			},
			'wp-hashids',
			'wp_hashids'
		);
	}
}
