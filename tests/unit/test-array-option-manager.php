<?php

class Option_Manager_Test extends PHPUnit_Framework_TestCase {
	function setUp() {
		WP_Mock::setUp();
	}

	function tearDown() {
		WP_Mock::tearDown();
	}

	/** @test */
	function it_can_get_an_entry() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[
				'two' => 'bananas',
				'three' => function() {
					return 'cherries';
				},
			],
			[
				'one' => 'string',
				'two' => 'string',
				'three' => 'string',
				'four' => 'string',
			],
			Mockery::mock( WP_Hashids\Options_Store::class )
		);

		// It can get an explicitly set value.
		$manager->set( 'one', 'apples' );
		$this->assertEquals( 'apples', $manager->get( 'one' ) );

		// It gets default if none set.
		// @todo Test that default is set to $data.
		$this->assertEquals( 'bananas', $manager->get( 'two' ) );

		// Defaults can be callable.
		$this->assertEquals( 'cherries', $manager->get( 'three' ) );

		// It returns null if not set and no default.
		$this->assertNull( $manager->get( 'four' ) );
	}

	/** @test */
	function it_knows_if_a_value_is_set() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[ 'two' => 'bananas' ],
			[ 'one' => 'string', 'two' => 'string', 'three' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
		);

		$manager->set( 'one', 'apples' );

		// Explicitly set values.
		$this->assertTrue( $manager->has( 'one' ) );

		// Default values.
		$this->assertTrue( $manager->has( 'two' ) );

		// Non-existent.
		$this->assertFalse( $manager->has( 'three' ) );
	}

	/** @test */
	function it_can_be_initialized() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[],
			[ 'one' => 'string', 'two' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
				->shouldReceive( 'get' )
				->once()
				->andReturn( [ 'one' => 'apples', 'two' => 'bananas' ] )
				->mock()
		);
		$manager->init();

		$this->assertEquals( 'apples', $manager->get( 'one' ) );
		$this->assertEquals( 'bananas', $manager->get( 'two' ) );

		// $store->get() should not be called this second time.
		$manager->init();
	}

	/** @test */
	function it_knows_when_it_has_unsaved_changes() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[],
			[ 'one' => 'string', 'two' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
		);

		// User can decide whether to track changes.
		$manager->set( 'one', 'apples', false );
		$this->assertFalse( $manager->is_dirty() );

		// By default changes are tracked.
		$manager->set( 'two', 'bananas' );
		$this->assertTrue( $manager->is_dirty() );
	}

	/** @test */
	function it_can_set_data() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[],
			[ 'one' => 'string', 'two' => 'string', 'three' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
				->shouldReceive( 'get' )
				->once()
				->andReturn( [ 'one' => 'apples' ] )
				->mock()
		);
		$manager->init();

		// If key is not in schema, nothing is set.
		$manager->set( 'four', 'durian' );
		$this->assertNull( $manager->get( 'three' ) );

		// It doesn't mark as changed if new value is same as old.
		$manager->set( 'one', 'apples' );
		$this->assertFalse( $manager->is_dirty() );

		// It doesn't track changes if user specifies.
		$manager->set( 'two', 'bananas', false );
		$this->assertFalse( $manager->is_dirty() );

		// It tracks changes by default.
		$manager->set( 'three', 'cherries' );
		$this->assertTrue( $manager->is_dirty() );
	}

	/** @test */
	function it_can_set_defaults() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[],
			[ 'one' => 'string', 'two' => 'string', 'three' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
		);

		$manager->set_default( 'one', 'apples' );
		$manager->set_defaults( [ 'two' => 'bananas', 'three' => 'cherries' ] );

		$this->assertEquals( 'apples', $manager->get( 'one' ) );
		$this->assertEquals( 'bananas', $manager->get( 'two' ) );
		$this->assertEquals( 'cherries', $manager->get( 'three' ) );
	}

	/** @test */
	function it_can_sanitize_values() {
		WP_Mock::userFunction( 'absint', [
			'args' => [ WP_Mock\Functions::type( 'string' ) ],
			'return' => function( $value ) {
				return abs( intval( $value ) );
			},
			'times' => 1,
		] );
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[
				'four' => 1,
			],
			[
				'one' => 'string',
				'two' => 'int',
				'three' => 'bool',
				'four' => 'string',
				'five' => 'string',
			],
			Mockery::mock( WP_Hashids\Options_Store::class )
				->shouldReceive( 'get' )
				->once()
				->andReturn( [ 'five' => true ] )
				->mock()
		);
		$manager->init();

		$manager->set( 'one', 0 );
		$manager->set( 'two', 'apples' );
		$manager->set( 'three', 'bananas' );

		$this->assertSame( '0', $manager->get( 'one' ) );
		$this->assertSame( 0, $manager->get( 'two' ) );
		$this->assertTrue( $manager->get( 'three' ) );

		// Also applied to defaults.
		$this->assertSame( '1', $manager->get( 'four' ) );

		// Also applied to data from DB.
		$this->assertSame( '1', $manager->get( 'five' ) );
	}

	/** @test */
	function it_can_save_to_db() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[],
			[ 'one' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
				->shouldReceive( 'set' )
				->with( 'wph_test_options', [ 'one' => 'apples' ] )
				->once()
				->andReturn( true )
				->mock()
		);

		$manager->set( 'one', 'apples' );
		$this->assertEquals( 'apples', $manager->get( 'one' ) );
		$this->assertTrue( $manager->is_dirty() );

		$manager->save();
		$this->assertFalse( $manager->is_dirty() );
	}

	/** @test */
	function it_can_unset_data() {
		$manager = new WP_Hashids\Option_Manager(
			'wph_test_options',
			[ 'one' => 'apples' ],
			[ 'one' => 'string', 'two' => 'string' ],
			Mockery::mock( WP_Hashids\Options_Store::class )
		);

		$manager->set( 'two', 'bananas' );

		$this->assertEquals( 'apples', $manager->get( 'one' ) );
		$this->assertEquals( 'bananas', $manager->get( 'two' ) );

		// Can be removed from defaults.
		$manager->unset( 'one', true );

		// As well as from data.
		$manager->unset( 'two' );

		$this->assertNull( $manager->get( 'one' ) );
		$this->assertNull( $manager->get( 'two' ) );
	}
}
