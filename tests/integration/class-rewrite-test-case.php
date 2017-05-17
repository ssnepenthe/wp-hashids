<?php

use Hashids\HashidsInterface;

abstract class WPH_Rewrite_Test_Case extends WP_UnitTestCase {
	protected $posts;
	protected $post_types;
	protected $permalink_structure = '/blog/%hashid%/';

	function setUp() {
		// Order of operations in this method is important.
		parent::setUp();

		$this->add_query_vars();
		$this->set_permalink_structure( $this->permalink_structure );
		$this->register_post_types();

		// Already flushed in ->set_permalink_structure() - need it one more time.
		$GLOBALS['wp_rewrite']->flush_rules();

		$this->create_posts();
	}

	function tearDown() {
		parent::tearDown();

		$this->delete_posts();
		$this->unregister_post_types();
	}

	protected function add_query_vars() {
		// Definitely not right to be adding this manually for each test
		// However, public query vars are wiped out between tests
		// https://core.trac.wordpress.org/ticket/34346
		// https://core.trac.wordpress.org/ticket/37207
		// How else could this be handled?
		$GLOBALS['wp']->add_query_var( 'hashid' );
	}

	protected function register_post_types() {
		$this->post_types = [];

		$this->post_types['one'] = register_post_type( 'one', [
			'public' => true,
			'rewrite' => [
				'slug' => 'one/%hashid%',
			],
		] );

		$this->post_types['two'] = register_post_type( 'two', [
			'public' => true,
		] );

		add_permastruct( 'two', 'two/%hashid%', [
			'feed' => false,
			'with_front' => false,
		] );
	}

	protected function create_posts() {
		$post_one = $this->factory()->post->create_and_get();
		$post_two = $this->factory()->post->create_and_get();
		$one_one = $this->factory()->post->create_and_get( [
			'post_type' => 'one',
		] );
		$one_two = $this->factory()->post->create_and_get( [
			'post_type' => 'one',
		] );
		$two_one = $this->factory()->post->create_and_get( [
			'post_type' => 'two',
		] );
		$two_two = $this->factory()->post->create_and_get( [
			'post_type' => 'two',
		] );

		$this->posts = [
			'page' => [
				[
					'post' => $this->factory()->post->create_and_get(),
					'hashid' => '',
				],
			],
			'post' => [
				[
					'post' => $post_one,
					'hashid' => wph_instance()['hashids']
						->encode( $post_one->ID ),
				],
				[
					'post' => $post_two,
					'hashid' => wph_instance()['hashids']
						->encode( $post_two->ID ),
				],
			],
			'one' => [
				[
					'post' => $one_one,
					'hashid' => wph_instance()['hashids']
						->encode( $one_one->ID ),
				],
				[
					'post' => $one_two,
					'hashid' => wph_instance()['hashids']
						->encode( $one_two->ID ),
				],
			],
			'two' => [
				[
					'post' => $two_one,
					'hashid' => wph_instance()['hashids']
						->encode( $two_one->ID ),
				],
				[
					'post' => $two_two,
					'hashid' => wph_instance()['hashids']
						->encode( $two_two->ID ),
				],
			],
		];
	}

	protected function delete_posts() {
		foreach ( $this->posts as $post_type => $posts ) {
			foreach ( $posts as $post ) {
				wp_delete_post( $post['post']->ID );
			}
		}

		$this->posts = null;
	}

	protected function unregister_post_types() {
		foreach ( $this->post_types as $post_type ) {
			// @todo TEST!
			unregister_post_type( $post_type->name );
		}

		// @todo Do we need to do anything about the custom permastructs?

		$this->post_types = null;
	}
}
