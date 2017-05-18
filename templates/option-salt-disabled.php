<?php
/**
 * The constant-defined salt option output.
 *
 * @package wp-hashids
 */

?>Salt set to <kbd><?php echo esc_html( $value ) ?></kbd> via constant.

<input
	id="wp_hashids_salt"
	name="wp_hashids_salt"
	type="hidden"
	value="<?php echo esc_attr( $value ) ?>"
>
