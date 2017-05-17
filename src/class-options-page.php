<?php
/**
 * Options_Page class.
 *
 * @package wp-hashids
 *
 * @todo  Disable individual options inputs if their corresponding constant is defined.
 */

namespace WP_Hashids;

use League\Plates\Engine;

/**
 * Defines the options page class.
 */
class Options_Page {
	/**
	 * Options manager instance.
	 *
	 * @var Options_Manager
	 */
	protected $manager;

	/**
	 * Plates engine instance.
	 *
	 * @var Engine
	 */
	protected $template;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager $manager  Options manager instance.
	 * @param Engine          $template Plates engine instance.
	 */
	public function __construct( Options_Manager $manager, Engine $template ) {
		$this->manager = $manager;
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
				echo $this->template->render( 'option-page', [
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
				echo $this->template->render( 'option-section' );
			},
			'wp-hashids'
		);

		add_settings_field(
			'wp_hashids_alphabet',
			'Alphabet',
			function() {
				$options = [];
				$current = $this->manager->alphabet();

				foreach ( Options_Manager::ALPHABET_MAP as $value => $details ) {
					$options[] = [
						'checked' => $current === $details['alphabet'],
						'label' => $details['label'],
						'regex' => $details['regex'],
						'value' => $value,
					];
				}

				echo $this->template->render(
					'option-alphabet',
					compact( 'options' )
				);
			},
			'wp-hashids',
			'wp_hashids'
		);

		add_settings_field(
			'wp_hashids_min_length',
			'Minimum Length',
			function() {
				echo $this->template->render( 'option-min-length', [
					'value' => $this->manager->min_length(),
				] );
			},
			'wp-hashids',
			'wp_hashids'
		);

		add_settings_field(
			'wp_hashids_salt',
			'Hashids Salt',
			function() {
				echo $this->template->render( 'option-salt', [
					'value' => $this->manager->salt(),
				] );
			},
			'wp-hashids',
			'wp_hashids'
		);
	}
}
