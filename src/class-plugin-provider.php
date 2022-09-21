<?php
/**
 * Plugin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Daedalus\Pimple\Events\AddingContainerDefinitions;
use Daedalus\Plugin\Events\PluginBooting;
use Daedalus\Plugin\SubscriberProvider;
use Hashids\Hashids;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;
use RuntimeException;
use WP;
use WP_Hashids\Events\Plugin_Deactivating;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the plugin provider class.
 */
class Plugin_Provider extends SubscriberProvider {
	protected $container;

	public function get_container(): ContainerInterface {
		if ( ! $this->container instanceof ContainerInterface ) {
			throw new RuntimeException( '@todo' );
		}

		return $this->container;
	}

	public function getSubscribedEvents(): array {
		return [
			AddingContainerDefinitions::class => 'on_adding_container_definitions',
			Plugin_Deactivating::class => 'on_plugin_deactivating',
			PluginBooting::class => 'on_plugin_booting', // @todo Can we go as early as plugin locking?

			'admin_init' => 'on_admin_init',
			'admin_menu' => 'on_admin_menu',
			'init' => 'on_init',
			'parse_request' => 'on_parse_request',
			'post_type_link' => 'on_post_link',
			'pre_post_link' => 'on_post_link',
		];
	}

	public function on_init(): void {
		// Docs still recommend using the admin_init hook but then the options will
		// not be available from the REST API...
		$this->get_container()->get( 'options_manager' )->register_settings();
		$this->get_container()->get( 'rewrite_manager' )->register_rewrites();
	}

	public function on_parse_request( WP $wp ): void {
		$this->get_container()
			->get( 'request_parser' )
			->parse( $wp );
	}

	public function on_update_option( $old_value, $value ): void {
		$this->get_container()
			->get( 'options_manager' )
			->flush_rewrites_on_save( $old_value, $value );
	}

	public function on_post_link( $link, $post ): string {
		return $this->get_container()
			->get( 'hashid_injector' )
			->inject( $link, $post );
	}

	public function on_pre_option( $pre_option, $option ) {
		return $this->get_container()
			->get( 'options_manager' )
			->use_constants_when_defined( $pre_option, $option );
	}

	public function on_plugin_booting( PluginBooting $event ): void {
		$this->container = $event->getPlugin()->getContainer();

		$event_manager = $event->getPlugin()->getEventManager();

		$event_manager->add( 'update_option_wp_hashids_alphabet', [ $this, 'on_update_option' ] );

		foreach ( [ 'alphabet', 'min_length', 'salt' ] as $option ) {
			$event_manager->add( "pre_option_wp_hashids_{$option}", [ $this, 'on_pre_option' ] );
		}
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
			'hashid_injector' => function( ContainerInterface $c ) {
				return new Hashid_Injector( $c->get( 'options_manager' ), $c->get( 'hashids' ) );
			},
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
			'request_parser' => function( ContainerInterface $c ) {
				return new Request_Parser( $c->get( 'hashids' ) );
			},
			'rewrite_manager' => function( ContainerInterface $c ) {
				return new Rewrite_Manager( $c->get( 'options_manager' ) );
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
