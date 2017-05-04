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
	function it_is_instantiable() {
		$manager = new Options_Manager( Mockery::mock( Options_Store::class ) );

		$this->assertInstanceOf( Options_Manager::class, $manager );
		$this->assertInstanceOf( Options_Manager_Interface::class, $manager );
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
			Options_Manager::LOWER . Options_Manager::UPPER . Options_Manager::NUMBERS,
			$manager->alphabet()
		);

		// In store - maps to actual alphabet.
		$this->assertEquals(
			Options_Manager::LOWER . Options_Manager::UPPER,
			$manager->alphabet()
		);
	}

	/** @test */
	function it_can_get_the_hashid_min_length() {
		WP_Mock::userFunction( 'absint', [
			'times' => 2,
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
			->andReturn( 8 )
			->mock();
		$manager = new Options_Manager( $store );

		$this->assertSame( 6, $manager->min_length() );
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
		$this->assertEquals( 'a-zA-Z0-9', $manager->regex() );

		// In store - maps to actual regex.
		$this->assertEquals( 'a-zA-Z', $manager->regex() );
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

		// Not in store so falls back to default.
		// @todo As written it depends on Salt_Generator which cannot be mocked.
		$salt = $manager->salt();
		$this->assertTrue( is_string( $salt ) );
		$this->assertSame( 64, strlen( $salt ) );

		// In store - maps to actual regex.
		$this->assertEquals( 'test-salt', $manager->salt() );
	}
}
