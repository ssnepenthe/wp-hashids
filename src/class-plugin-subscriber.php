<?php

namespace WP_Hashids;

use Psr\Container\ContainerInterface;
use ToyWpEventManagement\SubscriberInterface;
use WP;

class Plugin_Subscriber implements SubscriberInterface
{
	protected $container;

	public function __construct( ContainerInterface $container )
	{
		$this->container = $container;
	}

	public function getSubscribedEvents(): array
	{
		return [
			'admin_init' => 'on_admin_init',
			'admin_menu' => 'on_admin_menu',
			'init' => 'on_init',
			'parse_request' => 'on_parse_request',
			'post_type_link' => 'on_post_link',
			'pre_option_wp_hashids_alphabet' => 'on_pre_option',
			'pre_option_wp_hashids_min_length' => 'on_pre_option',
			'pre_option_wp_hashids_salt' => 'on_pre_option',
			'pre_post_link' => 'on_post_link',
			'update_option_wp_hashids_alphabet' => 'on_update_option',
		];
	}

	public function on_admin_init(): void {
		if ( ! $this->all_config_constants_are_defined() ) {
			$this->container
				->get( 'options_page' )
				->register_sections_and_fields();
		}
	}

	public function on_admin_menu(): void {
		if ( ! $this->all_config_constants_are_defined() ) {
			$this->container
				->get( 'options_page' )
				->register_page();
		}
	}

	public function on_init(): void {
		// Docs still recommend using the admin_init hook but then the options will
		// not be available from the REST API...
		$this->container->get( 'options_manager' )->register_settings();
		$this->container->get( 'rewrite_service' )->register_rewrite_tag();
	}

	public function on_parse_request( WP $wp ): void {
		$this->container
			->get( 'rewrite_service' )
			->parse_request( $wp );
	}

	public function on_post_link( $link, $post ): string {
		return $this->container
			->get( 'rewrite_service' )
			->replace_hashid_rewrite_tag( $link, $post );
	}

	public function on_pre_option( $pre_option, $option ) {
		return $this->container
			->get( 'options_manager' )
			->use_constants_when_defined( $pre_option, $option );
	}

	public function on_update_option( $old_value, $value ): void {
		$this->container
			->get( 'options_manager' )
			->flush_rewrites_on_save( $old_value, $value );
	}

	/**
	 * Check if all three plugin settings have been configured via constants.
	 */
	private function all_config_constants_are_defined(): bool {
		return defined( 'WP_HASHIDS_ALPHABET' )
			&& defined( 'WP_HASHIDS_MIN_LENGTH' )
			&& defined( 'WP_HASHIDS_SALT' );
	}
}
