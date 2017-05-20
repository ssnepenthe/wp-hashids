<?php
/**
 * Service_Proxy class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Pimple\Container;

/**
 * Defines the service proxy class.
 */
class Service_Proxy {
	/**
	 * Container instance.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Container entry key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Class constructor.
	 *
	 * @param Container $container Container instance.
	 * @param string    $key       Container entry key.
	 */
	public function __construct( Container $container, $key ) {
		$this->container = $container;
		$this->key = $key;
	}

	/**
	 * Proxy all method calls to the underlying container entry.
	 *
	 * @param  string $method Method name.
	 * @param  array  $args   Arguments passed to $method.
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		return call_user_func_array(
			[ $this->container[ $this->key ], $method ],
			$args
		);
	}
}
