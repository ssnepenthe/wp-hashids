<?php
/**
 * The constant-defined minimum length setting output.
 *
 * @package wp-hashids
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?>Minimum length set to <kbd><?php echo esc_html( $value ) ?></kbd> via constant.

<input
	id="wp_hashids_min_length"
	name="wp_hashids_min_length"
	type="hidden"
	value="<?php echo esc_attr( $value ) ?>"
>
