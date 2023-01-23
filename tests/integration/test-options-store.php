<?php

use WP_Hashids\Options_Store;

class Options_Store_Test extends WP_UnitTestCase {
	protected $wph_store;

	function set_up() {
		parent::set_up();

		$this->wph_store = new Options_Store;
	}

	function tear_down() {
		$this->wph_store = null;

		parent::tear_down();
	}

	/** @test */
	function it_can_add_an_option() {
		$key = __METHOD__ . ':key';
		$value = __METHOD__ . ':value';

		// Sanity check... Option should not exist yet.
		$this->assertFalse( get_option( $key ) );

		// Add and verify it was set properly.
		$this->wph_store->add( $key, $value );
		$this->assertEquals( $value, get_option( $key ) );

		// Option already exists - add() should not overwrite it.
		$this->wph_store->add( $key, "{$value}:2" );
		$this->assertEquals( $value, get_option( $key ) );
	}

	/** @test */
	function it_can_delete_an_option() {
		$key = __METHOD__ . ':key';
		$value = __METHOD__ . ':value';

		update_option( $key, $value );

		$this->wph_store->delete( $key );

		$this->assertFalse( get_option( $key ) );
	}

	/** @test */
	function it_can_get_an_option() {
		$key = __METHOD__ . ':key';
		$value = __METHOD__ . ':value';

		update_option( $key, $value );

		$this->assertEquals( $value, $this->wph_store->get( $key ) );

		// Return value should be null instead of false if does not exist.
		$this->assertNull( $this->wph_store->get( 'not-real' ) );
	}

	/** @test */
	function it_can_set_an_option() {
		$key = __METHOD__ . ':key';
		$value = __METHOD__ . ':value';

		// Sanity check.
		$this->assertFalse( get_option( $key ) );

		// It can set a non-existent option.
		$this->wph_store->set( $key, $value );
		$this->assertEquals( $value, get_option( $key ) );

		// It can overwrite a pre-existing option.
		$this->wph_store->set( $key, "{$value}:2" );
		$this->assertEquals( "{$value}:2", get_option( $key ) );
	}

	/** @test */
	function it_prepends_prefix_to_option_key_when_provided() {
		$key = __METHOD__ . ':key';
		$value = __METHOD__ . ':value';

		$store = new Options_Store( 'pfx' );

		$this->assertFalse( get_option( "pfx_{$key}" ) );

		// When using add.
		$store->add( $key, $value );
		$this->assertEquals( $value, get_option( "pfx_{$key}" ) );

		// When using get.
		$this->assertEquals( $value, $store->get( $key ) );

		// When using set.
		$store->set( $key, "{$value}:2" );
		$this->assertEquals( "{$value}:2", get_option( "pfx_{$key}" ) );

		// When using delete.
		$store->delete( $key );
		$this->assertFalse( get_option( "pfx_{$key}" ) );
	}
}
