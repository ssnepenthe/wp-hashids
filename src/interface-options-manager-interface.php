<?php

namespace WP_Hashids;

interface Options_Manager_Interface {
	public function alphabet() : string;
	public function min_length() : int;
	public function regex() : string;
	public function rewrite_tag() : string;
	public function salt() : string;
}
