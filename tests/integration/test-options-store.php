<?php

use WP_Hashids\Options_Store;

class Options_Store_Test extends WP_UnitTestCase {
	protected $wph_store;

	function setUp() {
		parent::setUp();

		$this->wph_store = new Options_Store;
	}

	function tearDown() {
		parent::tearDown();

		$this->wph_store = null;
	}

	/** @test */
	function it_can_add_an_option() {
		// Sanity check... Option should not exist yet.
		$this->assertFalse( get_option( 'test' ) );

		// Add and verify it was set properly.
		$this->wph_store->add( 'test', 'value' );
		$this->assertEquals( 'value', get_option( 'test' ) );

		// Option already exists - add() should not overwrite it.
		$this->wph_store->add( 'test', 'value 2' );
		$this->assertEquals( 'value', get_option( 'test' ) );
	}

	/** @test */
	function it_can_delete_an_option() {
		update_option( 'test', 'value' );

		$this->wph_store->delete( 'test' );

		$this->assertFalse( get_option( 'test' ) );
	}

	/** @test */
	function it_can_get_an_option() {
		update_option( 'test', 'value' );

		$this->assertEquals( 'value', $this->wph_store->get( 'test' ) );

		// Return value should be null instead of false if does not exist.
		$this->assertNull( $this->wph_store->get( 'not-real' ) );
	}

	/** @test */
	function it_can_set_an_option() {
		// Sanity check.
		$this->assertFalse( get_option( 'test' ) );

		// It can set a non-existent option.
		$this->wph_store->set( 'test', 'value' );
		$this->assertEquals( 'value', get_option( 'test' ) );

		// It can overwrite a pre-existing option.
		$this->wph_store->set( 'test', 'value 2' );
		$this->assertEquals( 'value 2', get_option( 'test' ) );
	}

	/** @test */
	function it_prepends_prefix_to_option_key_when_provided() {
		$store = new Options_Store( 'pfx' );

		$this->assertFalse( get_option( 'pfx_test' ) );

		// When using add.
		$store->add( 'test', 'value' );
		$this->assertEquals( 'value', get_option( 'pfx_test' ) );

		// When using get.
		$this->assertEquals( 'value', $store->get( 'test' ) );

		// When using set.
		$store->set( 'test', 'value 2' );
		$this->assertEquals( 'value 2', get_option( 'pfx_test' ) );

		// When using delete.
		$store->delete( 'test' );
		$this->assertFalse( get_option( 'pfx_test' ) );
	}
}
