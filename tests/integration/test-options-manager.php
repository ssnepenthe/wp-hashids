<?php

use WP_Hashids\Options_Manager;

// Possible to test that config can be overridden using constants?

class Options_Manager_Test extends WP_UnitTestCase {
	public function set_up() {
		parent::set_up();

		delete_option( Options_Manager::ALPHABET_OPTION_KEY );
		delete_option( Options_Manager::MIN_LENGTH_OPTION_KEY );
		delete_option( Options_Manager::SALT_OPTION_KEY );
	}

	public function tear_down() {
		delete_option( Options_Manager::ALPHABET_OPTION_KEY );
		delete_option( Options_Manager::MIN_LENGTH_OPTION_KEY );
		delete_option( Options_Manager::SALT_OPTION_KEY );
	}

	/** @test */
	function salt_is_automatically_generated_and_save_when_not_in_db() {
		$manager = new Options_Manager();

		$this->assertFalse( get_option( Options_Manager::SALT_OPTION_KEY ) );

		$salt = $manager->salt();

		$this->assertTrue( is_string( $salt ) );
		$this->assertSame( 64, strlen( $salt ) );
		$this->assertEquals( $salt, get_option( Options_Manager::SALT_OPTION_KEY ) );
	}

	/** @test */
	function it_can_sanitize_salt() {
		$manager = new Options_Manager();

		// Salt is cast to string.
		$this->assertSame( '0', $manager->sanitize_salt( 0 ) );
		$this->assertSame( '', $manager->sanitize_salt( false ) );

		// Except null value which triggers salt generator.
		$salt = $manager->sanitize_salt( null );
		$this->assertTrue( is_string( $salt ) );
		$this->assertSame( 64, strlen( $salt ) );

		// Otherwise returned as passed.
		$this->assertEquals( 'test', $manager->sanitize_salt( 'test' ) );
	}

	/** @test */
	function it_registers_settings_with_wordpress() {
		global $new_whitelist_options;

		$plugin_options = [
			Options_Manager::ALPHABET_OPTION_KEY,
			Options_Manager::MIN_LENGTH_OPTION_KEY,
			Options_Manager::SALT_OPTION_KEY,
		];

		$this->assertTrue(
			array_key_exists( 'wp_hashids_group', $new_whitelist_options )
		);
		$this->assertEqualSets(
			$new_whitelist_options['wp_hashids_group'],
			$plugin_options
		);

		foreach ( $plugin_options as $option ) {
			$this->assertTrue(
				array_key_exists( $option, get_registered_settings() )
			);
		}
	}
}
