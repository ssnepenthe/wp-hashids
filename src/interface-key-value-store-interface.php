<?php

namespace WP_Hashids;

interface Key_Value_Store_Interface {
	public function add( string $key, $value ) : bool;
	public function delete( string $key ) : bool;
	public function get( string $key );
	public function set( string $key, $value ) : bool;
}
