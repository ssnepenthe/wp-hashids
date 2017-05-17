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
	 * Stack of providers with a boot step.
	 *
	 * @var ServiceProviderInterface[]
	 */
	protected $boot_queue = array();

	/**
	 * Stack of providers with a late boot step.
	 *
	 * @var ServiceProviderInterface[]
	 */
	protected $deferred_boot_queue = array();

	/**
	 * Call boot on all pending providers.
	 *
	 * @return void
	 */
	public function boot() {
		while ( count( $this->boot_queue ) ) {
			array_shift( $this->boot_queue )->boot( $this );
		}
	}

	/**
	 * Call deferred boot on all pending providers.
	 *
	 * @return void
	 */
	public function deferred_boot() {
		while ( count( $this->deferred_boot_queue ) ) {
			array_shift( $this->deferred_boot_queue )->deferred_boot( $this );
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

		if ( method_exists( $provider, 'boot' ) ) {
			$this->boot_queue[] = $provider;
		}

		if ( method_exists( $provider, 'deferred_boot' ) ) {
			$this->deferred_boot_queue[] = $provider;
		}

		return $this;
	}
}
