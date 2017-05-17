<?php
/**
 * Plugin class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the plugin class.
 */
class Plugin extends Container {
	/**
	 * Counter for tracking the number of calls to ->boot().
	 *
	 * @var integer
	 */
	protected $boot_calls = 0;

	/**
	 * List of registered providers.
	 *
	 * @var ServiceProviderInterface[]
	 */
	protected $providers = array();

	/**
	 * Call boot on all pending providers.
	 *
	 * @return void
	 */
	public function boot() {
		if ( 1 < $this->boot_calls ) {
			return;
		}

		$boot_method = $this->boot_calls++ ? 'deferred_boot' : 'boot';

		foreach ( $this->providers as $provider ) {
			if ( method_exists( $provider, $boot_method ) ) {
				$provider->{$boot_method}( $this );
			}
		}
	}

	/**
	 * Call deactivate on all pending preoviders.
	 *
	 * @return void
	 */
	public function deactivate() {
		foreach ( $this->providers as $provider ) {
			if ( method_exists( $provider, 'deactivate' ) ) {
				$provider->deactivate( $this );
			}
		}
	}

	/**
	 * Register a service provider with the container.
	 *
	 * @param  ServiceProviderInterface $provider ServiceProviderInterface instance.
	 * @param  array                    $values   Values to customize the provider.
	 *
	 * @return static
	 */
	public function register(
		ServiceProviderInterface $provider,
		array $values = array()
	) {
		parent::register( $provider, $values );

		$this->providers[] = $provider;

		return $this;
	}
}
