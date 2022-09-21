<?php
/**
 * Plates_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Daedalus\Pimple\Events\AddingContainerDefinitions;
use Daedalus\Plugin\SubscriberProvider;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Plates_Provider extends SubscriberProvider {
	public function getSubscribedEvents(): array {
		return [
			AddingContainerDefinitions::class => 'on_adding_container_definitions',
		];
	}

	public function on_adding_container_definitions( AddingContainerDefinitions $event ): void {
		$event->addDefinitions( [
			'plates' => function( ContainerInterface $c ) {
				return new Engine( $c->get( 'plugin.dir' ) . '/templates' );
			},
		] );
	}
}
