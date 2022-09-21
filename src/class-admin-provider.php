<?php
/**
 * Admin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Daedalus\Pimple\Events\AddingContainerDefinitions;
use Daedalus\Plugin\Events\PluginBooting;
use Daedalus\Plugin\SubscriberProvider;
use Psr\Container\ContainerInterface;
use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Admin_Provider extends SubscriberProvider {
	public function get_container(): ContainerInterface {
		if ( ! $this->container instanceof ContainerInterface ) {
			throw new RuntimeException( '@todo' );
		}

		return $this->container;
	}

	public function getSubscribedEvents(): array {
		return [
			AddingContainerDefinitions::class => 'on_adding_container_definitions',
			PluginBooting::class => 'on_plugin_booting',

			'admin_init' => 'on_admin_init',
			'admin_menu' => 'on_admin_menu',
		];
	}

	public function on_plugin_booting( PluginBooting $event ): void {
		$this->container = $event->getPlugin()->getContainer();
	}

	public function on_adding_container_definitions( AddingContainerDefinitions $event ): void {
		$event->addDefinitions( [
			'options_page' => function( ContainerInterface $c ) {
				return new Options_Page( $c->get( 'options_manager' ), $c->get( 'plates' ) );
			},
		] );
	}

	public function on_admin_init(): void {
		if ( ! $this->all_config_constants_are_defined() ) {
			$this->get_container()
				->get( 'options_page' )
				->register_sections_and_fields();
		}
	}

	public function on_admin_menu(): void {
		if ( ! $this->all_config_constants_are_defined() ) {
			$this->get_container()
				->get( 'options_page' )
				->register_page();
		}
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
