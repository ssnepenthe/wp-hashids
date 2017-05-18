<?php

class Salt_Generator_Test extends WP_UnitTestCase {
	/** @test */
	function it_can_generate_a_salt_of_requested_length() {
		$salt_gen = new WP_Hashids\Salt_Generator;

		// Returns a string, default length is 64.
		$salt = $salt_gen->generate();
		$this->assertEquals( 'string', gettype( $salt ) );
		$this->assertSame( 64, strlen( $salt ) );

		// Minimum length of 10 characters.
		$this->assertSame( 10, strlen( $salt_gen->generate( 5 ) ) );

		// Maximum length of 64 characters.
		$this->assertSame( 64, strlen( $salt_gen->generate( 80 ) ) );
	}
}
