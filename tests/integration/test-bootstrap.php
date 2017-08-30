<?php

class Bootstrap_Test extends WP_UnitTestCase {
	/** @test */
	function it_always_returns_the_same_instance() {
		$this->assertSame( _wph_instance(), _wph_instance() );
	}

	/** @test */
	function it_provides_a_shortcut_for_accessing_container_entries() {
		$this->assertSame( _wph_instance()['hashids'], _wph_instance( 'hashids' ) );
	}
}
