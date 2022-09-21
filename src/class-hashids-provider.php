<?php
/**
 * Hashids_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Daedalus\Pimple\Events\AddingContainerDefinitions;
use Daedalus\Plugin\SubscriberProvider;
use Hashids\Hashids;
use Psr\Container\ContainerInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Hashids_Provider extends SubscriberProvider {
	public function getSubscribedEvents(): array
	{
		return [
			AddingContainerDefinitions::class => 'on_adding_container_definitions',
		];
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
		] );
	}
}
