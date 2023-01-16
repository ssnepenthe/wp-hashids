<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use WP_Hashids\Options_Store;

class Options_Store_Test extends PHPUnit_Framework_TestCase {
	protected $store;

	function setUp() {
		parent::setUp();
		Monkey\setUp();

		$this->store = new Options_Store;
	}

	function tearDown() {
		$this->store = null;

		Monkey\tearDown();
		parent::tearDown();
	}

	/** @test */
	function add_delegates_to_add_option() {
		Functions\expect( 'add_option' )
			->once()
			->with( 'test', 'value' )
			->andReturn( true );

		$this->assertTrue( $this->store->add( 'test', 'value' ) );
	}

	/** @test */
	function delete_delegates_to_delete_option() {
		Functions\expect( 'delete_option' )
			->once()
			->with( 'test' )
			->andReturn( true );

		$this->assertTrue( $this->store->delete( 'test' ) );
	}

	/** @test */
	function get_delegates_to_get_option() {
		Functions\expect( 'get_option' )
			->once()
			->with( 'test' )
			->andReturn( 'value' );

		$this->assertEquals( 'value', $this->store->get( 'test' ) );
	}

	/** @test */
	function set_delegates_to_update_option() {
		Functions\expect( 'update_option' )
			->once()
			->with( 'test', 'value' )
			->andReturn( true );

		$this->assertTrue( $this->store->set( 'test', 'value' ) );
	}

	/** @test */
	function it_prepends_prefix_to_option_key_if_applicable() {
		Functions\expect( 'add_option' )
			->once()
			->with( 'pfx_test', 'value' )
			->andReturn( true );
		Functions\expect( 'delete_option' )
			->once()
			->with( 'pfx_test' )
			->andReturn( true );
		Functions\expect( 'get_option' )
			->once()
			->with( 'pfx_test' )
			->andReturn( 'value' );
		Functions\expect( 'update_option' )
			->once()
			->with( 'pfx_test', 'value' )
			->andReturn( true );

		$prefixed_store = new Options_Store( 'pfx' );

		$this->assertTrue( $prefixed_store->add( 'test', 'value' ) );
		$this->assertTrue( $prefixed_store->delete( 'test' ) );
		$this->assertEquals( 'value', $prefixed_store->get( 'test' ) );
		$this->assertTrue( $prefixed_store->set( 'test', 'value' ) );
	}
}
