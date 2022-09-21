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
use WP_Hashids\Events\Plugin_Deactivating;

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
			Plugin_Deactivating::class => 'on_plugin_deactivating',
		];
	}

	public function on_adding_subscribers( AddingSubscribers $event ): void
	{
		$event->addSubscriber( new Plugin_Subscriber( $event->getPlugin()->getContainer() ) );
	}

	/**
	 * Remove the rewrite tag from site permalink structure on deactivation.
	 */
	public function on_plugin_deactivating( Plugin_Deactivating $event ): void {
		$container = $event->getPlugin()->getContainer();
		$wp_rewrite = $container->get( 'wp_rewrite' );
		$options_manager = $container->get( 'options_manager' );

		$wp_rewrite->set_permalink_structure( str_replace(
			$options_manager->rewrite_tag(),
			'%post_id%',
			$wp_rewrite->permalink_structure
		) );

		$wp_rewrite->flush_rules();
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
