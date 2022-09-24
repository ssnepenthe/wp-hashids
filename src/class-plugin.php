<?php

namespace WP_Hashids;

use Daedalus\Pimple\PimpleProvider;
use Daedalus\Plugin\Plugin as DaedalusPlugin;
use WP_Hashids\Events\Plugin_Deactivating;

class Plugin extends DaedalusPlugin
{
	public function configure(): void
	{
		$this
			->setFile( dirname( __DIR__ ) . '/wp-hashids.php' )
			->setName( 'WP Hashids' )
			->setPrefix( 'wp_hashids' );
	}

	public function getProviders(): array
	{
		return [
			new PimpleProvider(),
			new Plugin_Provider(),
		];
	}

	public function run(): void
	{
		parent::run();

		$this->getEventManager()->deactivate( $this->getFile(), [ $this, 'onDeactivation' ] );
	}

	public function onDeactivation()
	{
		$this->getEventDispatcher()->dispatch( new Plugin_Deactivating() );
	}
}
