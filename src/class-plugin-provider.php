<?php
/**
 * Plugin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Daedalus\Pimple\Events\AddingContainerDefinitions;
use Daedalus\Plugin\Events\AddingSubscribers;
use Daedalus\Plugin\SubscriberProvider;
use Hashids\Hashids;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the plugin provider class.
 */
class Plugin_Provider extends SubscriberProvider {
	public function getSubscribedEvents(): array {
		return [
			AddingContainerDefinitions::class => 'on_adding_container_definitions',
			AddingSubscribers::class => 'on_adding_subscribers',
		];
	}

	public function on_adding_subscribers( AddingSubscribers $event ): void
	{
		$event->addSubscriber( new Plugin_Subscriber( $event->getPlugin()->getContainer() ) );
	}

	public function on_adding_container_definitions( AddingContainerDefinitions $event ): void {
		$event->addDefinitions( [
			'hashids' => function( ContainerInterface $c ) {
				$options = $c->get( 'options_manager' );

				return new Hashids(
					$options->salt(),
					$options->min_length(),
					$options->alphabet()
				);
			},
			'options_manager' => function( ContainerInterface $c ) {
				return new Options_Manager( $c->get( 'options_store' ) );
			},
			'options_page' => function( ContainerInterface $c ) {
				return new Options_Page( $c->get( 'options_manager' ), $c->get( 'plates' ) );
			},
			'options_store' => function( ContainerInterface $c ) {
				return new Options_Store( $c->get( 'plugin.prefix' ) );
			},
			'plates' => function( ContainerInterface $c ) {
				return new Engine( $c->get( 'plugin.dir' ) . '/templates' );
			},
			'rewrite_service' => function( ContainerInterface $c ) {
				return new Rewrite_Service( $c->get( 'options_manager' ), $c->get( 'hashids' ) );
			},
		] );
	}
}
