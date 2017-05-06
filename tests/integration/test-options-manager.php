<?php

use WP_Hashids\Options_Store;
use WP_Hashids\Options_Manager;

// Possible to test that config can be overridden using constants?

class Options_Manager_Test extends WP_UnitTestCase {
	/** @test */
	function salt_is_automatically_generated_and_save_when_not_in_db() {
		$manager = new Options_Manager( new Options_Store( 'pfx' ) );

		$this->assertFalse( get_option( 'pfx_salt' ) );

		$salt = $manager->salt();

		$this->assertEquals( $salt, get_option( 'pfx_salt' ) );
	}

	/** @test */
	function it_registers_settings_with_wordpress() {
		global $new_whitelist_options;

		$plugin_options = [
			'wp_hashids_alphabet',
			'wp_hashids_min_length',
			'wp_hashids_salt',
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
