<?php

use WP_Hashids\Options_Store;

class Options_Store_Test extends PHPUnit_Framework_TestCase {
	protected $store;

	function setUp() {
		WP_Mock::setUp();

		$this->store = new Options_Store;
	}

	function tearDown() {
		WP_Mock::tearDown();

		$this->store = null;
	}

	/** @test */
	function add_delegates_to_add_option() {
		WP_Mock::userFunction( 'add_option', [
			'args' => [ 'test', 'value' ],
			'times' => 1,
			'return' => true,
		] );

		$this->assertTrue( $this->store->add( 'test', 'value' ) );
	}

	/** @test */
	function delete_delegates_to_delete_option() {
		WP_Mock::userFunction( 'delete_option', [
			'args' => 'test',
			'times' => 1,
			'return' => true,
		] );

		$this->assertTrue( $this->store->delete( 'test' ) );
	}

	/** @test */
	function get_delegates_to_get_option() {
		WP_Mock::userFunction( 'get_option', [
			'args' => 'test',
			'times' => 1,
			'return' => 'value',
		] );

		$this->assertEquals( 'value', $this->store->get( 'test' ) );
	}

	/** @test */
	function set_delegates_to_update_option() {
		WP_Mock::userFunction( 'update_option', [
			'args' => [ 'test', 'value' ],
			'times' => 1,
			'return' => true,
		] );

		$this->assertTrue( $this->store->set( 'test', 'value' ) );
	}

	/** @test */
	function it_prepends_prefix_to_option_key_if_applicable() {
		WP_Mock::userFunction( 'add_option', [
			'args' => [ 'pfx_test', 'value' ],
			'times' => 1,
			'return' => true,
		] );
		WP_Mock::userFunction( 'delete_option', [
			'args' => 'pfx_test',
			'times' => 1,
			'return' => true,
		] );
		WP_Mock::userFunction( 'get_option', [
			'args' => 'pfx_test',
			'times' => 1,
			'return' => 'value',
		] );
		WP_Mock::userFunction( 'update_option', [
			'args' => [ 'pfx_test', 'value' ],
			'times' => 1,
			'return' => true,
		] );

		$prefixed_store = new Options_Store( 'pfx' );

		$this->assertTrue( $prefixed_store->add( 'test', 'value' ) );
		$this->assertTrue( $prefixed_store->delete( 'test' ) );
		$this->assertEquals( 'value', $prefixed_store->get( 'test' ) );
		$this->assertTrue( $prefixed_store->set( 'test', 'value' ) );
	}
}
