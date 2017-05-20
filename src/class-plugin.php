<?php
/**
 * Plugin class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the plugin class.
 */
class Plugin extends Container {
	/**
	 * List of registered providers.
	 *
	 * @var ServiceProviderInterface[]
	 */
	protected $providers = array();

	/**
	 * List of cached proxy objects.
	 *
	 * @var Service_Proxy[]
	 */
	protected $proxies = array();

	/**
	 * Call boot on all pending providers.
	 *
	 * @return void
	 */
	public function boot() {
		foreach ( $this->providers as $provider ) {
			if ( method_exists( $provider, 'boot' ) ) {
				$provider->boot( $this );
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
	 * Get a proxy wrapper for a given container entry.
	 *
	 * @param  string $key Container key.
	 *
	 * @return Service_Proxy
	 */
	public function proxy( $key ) {
		if ( isset( $this->proxies[ $key ] ) ) {
			return $this->proxies[ $key ];
		}

		$this->proxies[ $key ] = new Service_Proxy( $this, $key );

		return $this->proxies[ $key ];
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
