<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use WP_Hashids\Options_Manager;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Options_Manager_Test extends TestCase {
	use MockeryPHPUnitIntegration;

	function set_up() {
		parent::set_up();
		Monkey\setUp();
	}

	function tear_down() {
		Monkey\tearDown();
		parent::tear_down();
	}

	/** @test */
	function it_can_get_the_hashid_alphabet_default() {
		Functions\when( 'get_option' )->justReturn( false );

		$manager = new Options_Manager();

		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['all']['alphabet'],
			$manager->alphabet()
		);
	}

	/** @test */
	function it_can_get_configured_hashid_alphabet() {
		Functions\when( 'get_option' )->justReturn( 'lowerupper' );

		$manager = new Options_Manager();

		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['lowerupper']['alphabet'],
			$manager->alphabet()
		);
	}

	/** @test */
	function it_can_get_the_hashid_min_length_default() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		Functions\when( 'get_option' )->justReturn( false );

		$manager = new Options_Manager();

		$this->assertSame( 0, $manager->min_length() );
	}

	/** @test */
	function it_can_get_the_configured_hashid_min_length() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		Functions\when( 'get_option' )->justReturn( 8 );

		$manager = new Options_Manager();

		$this->assertSame( 8, $manager->min_length() );
	}

	/** @test */
	function it_can_sanitize_get_the_hashid_min_length() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		Functions\when( 'get_option' )->justReturn( '6' );

		$manager = new Options_Manager();

		$this->assertSame( 6, $manager->min_length() );
	}

	/** @test */
	function it_can_get_the_rewrite_regex_default() {
		Functions\when( 'get_option' )->justReturn( false );

		$manager = new Options_Manager();

		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['all']['regex'],
			$manager->regex()
		);

	}

	/** @test */
	function it_can_get_the_configured_rewrite_regex() {
		Functions\when( 'get_option' )->justReturn( 'lowerupper' );

		$manager = new Options_Manager();

		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['lowerupper']['regex'],
			$manager->regex()
		);
	}

	/** @test */
	function it_can_get_the_rewrite_tag() {
		$manager = new Options_Manager();

		$this->assertEquals( '%hashid%', $manager->rewrite_tag() );
	}

	/** @test */
	function it_can_sanitize_alphabet() {
		$manager = new Options_Manager();

		// Unrecognized alphabets are reset to all.
		$this->assertEquals( 'all', $manager->sanitize_alphabet( 'test' ) );

		// Recognized alphabets are returned as passed.
		$this->assertEquals( 'lower', $manager->sanitize_alphabet( 'lower' ) );
		$this->assertEquals( 'upper', $manager->sanitize_alphabet( 'upper' ) );
		$this->assertEquals(
			'lowerupper',
			$manager->sanitize_alphabet( 'lowerupper' )
		);
		$this->assertEquals(
			'lowernumber',
			$manager->sanitize_alphabet( 'lowernumber' )
		);
		$this->assertEquals(
			'uppernumber',
			$manager->sanitize_alphabet( 'uppernumber' )
		);
		$this->assertEquals( 'all', $manager->sanitize_alphabet( 'all' ) );
	}
}
