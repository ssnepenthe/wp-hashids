<?php

namespace WP_Hashids;

use Metis\View\Template_Interface;

class Options_Page {
	protected $manager;
	protected $view;

	public function __construct(
		Options_Manager $manager,
		Template_Interface $view
	) {
		$this->manager = $manager;
		$this->view = $view;
	}

	public function register_page() {
		add_options_page(
			'WP Hashids Settings',
			'WP Hashids',
			'manage_options',
			'wp-hashids',
			function() {
				$this->view->output( 'option-page', [
					'group' => 'wp_hashids_group',
					'page' => 'wp-hashids',
				] );
			}
		);
	}

	public function register_sections_and_fields() {
		add_settings_section(
			'wp_hashids',
			'Configure WP Hashids',
			function() {
				$this->view->output( 'option-section' );
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
						'checked' => $value === $current,
						'label' => $details['label'],
						'regex' => $details['regex'],
						'value' => $value,
					];
				}

				$this->view->output( 'option-alphabet', compact( 'options' ) );
			},
			'wp-hashids',
			'wp_hashids'
		);

		add_settings_field(
			'wp_hashids_min_length',
			'Minimum Length',
			function() {
				$this->view->output( 'option-min-length', [
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
				$this->view->output( 'option-salt', [
					'value' => $this->manager->salt(),
				] );
			},
			'wp-hashids',
			'wp_hashids'
		);
	}
}
