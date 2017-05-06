<?php

use WP_Hashids\Options_Store;
use WP_Hashids\Options_Manager;
use WP_Hashids\Options_Manager_Interface;

class Options_Manager_Test extends PHPUnit_Framework_TestCase {
	function setUp() {
		WP_Mock::setUp();
	}

	function tearDown() {
		WP_Mock::tearDown();
	}

	/** @test */
	function it_can_get_the_hashid_alphabet() {
		$store = Mockery::mock( Options_Store::class )
			->shouldReceive( 'get' )
			->once()
			->andReturn( null )
			->shouldReceive( 'get' )
			->once()
			->andReturn( 'lowerupper' )
			->mock();
		$manager = new Options_Manager( $store );

		// Not in store so falls back to default.
		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['all']['alphabet'],
			$manager->alphabet()
		);

		// In store - maps to actual alphabet.
		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['lowerupper']['alphabet'],
			$manager->alphabet()
		);
	}

	/** @test */
	function it_can_get_the_hashid_min_length() {
		WP_Mock::userFunction( 'absint', [
			'times' => 3,
			'return' => function( $value ) {
				return abs( intval( $value ) );
			}
		] );
		$store = Mockery::mock( Options_Store::class )
			->shouldReceive( 'get' )
			->once()
			->andReturn( null )
			->shouldReceive( 'get' )
			->once()
			->andReturn( '6' )
			->shouldReceive( 'get' )
			->once()
			->andReturn( 8 )
			->mock();
		$manager = new Options_Manager( $store );

		// Test that it is run through absint.
		$this->assertSame( 0, $manager->min_length() );
		$this->assertSame( 6, $manager->min_length() );

		// Normal.
		$this->assertSame( 8, $manager->min_length() );
	}

	/** @test */
	function it_can_get_the_rewrite_regex() {
		$store = Mockery::mock( Options_Store::class )
			->shouldReceive( 'get' )
			->once()
			->andReturn( null )
			->shouldReceive( 'get' )
			->once()
			->andReturn( 'lowerupper' )
			->mock();
		$manager = new Options_Manager( $store );

		// Not in store so falls back to default.
		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['all']['regex'],
			$manager->regex()
		);

		// In store - maps to actual regex.
		$this->assertEquals(
			Options_Manager::ALPHABET_MAP['lowerupper']['regex'],
			$manager->regex()
		);
	}

	/** @test */
	function it_can_get_the_rewrite_tag() {
		$manager = new Options_Manager( Mockery::mock( Options_Store::class ) );

		$this->assertEquals( '%hashid%', $manager->rewrite_tag() );
	}

	/** @test */
	function it_can_get_the_salt() {
		$store = Mockery::mock( Options_Store::class )
			->shouldReceive( 'get' )
			->once()
			->andReturn( null )
			->shouldReceive( 'set' )
			->once()
			->andReturn( true )
			->shouldReceive( 'get' )
			->once()
			->andReturn( 'test-salt' )
			->mock();
		$manager = new Options_Manager( $store );

		// Not in store so falls back to default and automatically saves.
		// @todo As written it depends on Salt_Generator which cannot be mocked.
		$salt = $manager->salt();
		$this->assertTrue( is_string( $salt ) );
		$this->assertSame( 64, strlen( $salt ) );

		// In store - maps to actual regex.
		$this->assertEquals( 'test-salt', $manager->salt() );
	}

	/** @test */
	function it_can_sanitize_alphabet() {
		$manager = new Options_Manager( Mockery::mock( Options_Store::class ) );

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

	/** @test */
	function it_can_sanitize_salt() {
		$manager = new Options_Manager( Mockery::mock( Options_Store::class ) );

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
}
