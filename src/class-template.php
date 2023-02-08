<?php
/**
 * Template class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Template
{
    protected $directory;

    public function __construct( $directory ) {
        $this->directory = trailingslashit( $directory );
    }

    public function render( $name, $data = [] ) {
        $template = "{$this->directory}{$name}.php";

        ob_start();

        static::include_template( $template, $data );

        $rendered = ob_get_contents();
        ob_end_clean();

        return $rendered;
    }

    public static function include_template( $template, $data ) {
        extract( $data, EXTR_SKIP );

        include $template;
    }
}
