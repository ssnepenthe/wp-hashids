<?php

class Rewrite_Without_Front_Test extends WPH_Rewrite_Test_Case {
	protected $permalink_structure = '/%hashid%/';

	/** @test */
	function it_handles_post_permalinks() {
		foreach ( $this->posts['post'] as $post_arr ) {
			$this->assertEquals(
				home_url( "{$post_arr['hashid']}/"),
				get_permalink( $post_arr['post'] )
			);
		}
	}

	/** @test */
	function it_handles_cpt_permalinks() {
		foreach ( $this->posts['one'] as $post_arr ) {
			$this->assertEquals(
				home_url(
					"one/{$post_arr['hashid']}/{$post_arr['post']->post_name}/"
				),
				get_permalink( $post_arr['post'] )
			);
		}

		foreach ( $this->posts['two'] as $post_arr ) {
			$this->assertEquals(
				home_url( "two/{$post_arr['hashid']}/" ),
				get_permalink( $post_arr['post'] )
			);
		}
	}

	/** @test */
	function it_parses_post_requests_with_hashid() {
		foreach ( $this->posts['post'] as $post_arr ) {
			$this->go_to( "{$post_arr['hashid']}/" );

			$this->assertEquals( $post_arr['hashid'], get_query_var( 'hashid' ) );
			$this->assertSame( $post_arr['post']->ID, get_query_var( 'p' ) );
		}
	}

	/** @test */
	function it_parses_cpt_requests_with_hashid() {
		// Nothing crazy happening here - WP already has everything it needs from post name.
		foreach ( $this->posts['one'] as $post_arr ) {
			$this->go_to(
				"one/{$post_arr['hashid']}/{$post_arr['post']->post_name}/"
			);

			$this->assertEquals(
				$post_arr['post']->post_name,
				get_query_var( 'one' )
			);
			$this->assertEquals( 'one', get_query_var( 'post_type' ) );
			$this->assertEquals(
				$post_arr['post']->post_name,
				get_query_var( 'name' )
			);
			$this->assertEquals( $post_arr['hashid'], get_query_var( 'hashid' ) );
		}

		// Custom permastruct with %hashid% as only tag.
		foreach ( $this->posts['two'] as $post_arr ) {
			$this->go_to( "two/{$post_arr['hashid']}/" );

			$this->assertEquals(
				$post_arr['post']->post_name,
				get_query_var( 'two' )
			);
			$this->assertEquals( 'two', get_query_var( 'post_type' ) );
			$this->assertEquals(
				$post_arr['post']->post_name,
				get_query_var( 'name' )
			);
			$this->assertEquals( $post_arr['hashid'], get_query_var( 'hashid' ) );
		}
	}

	/** @test */
	function it_maps_hashid_to_pagename_if_invalid() {
		$this->go_to( "{$this->posts['page'][0]['post']->post_name}/" );

		$this->assertEquals(
			$this->posts['page'][0]['post']->post_name,
			get_query_var( 'pagename' )
		);
		$this->assertEmpty( get_query_var( 'hashid' ) );
	}
}
