<?php
/**
 * The constant-defined alphabet setting output.
 *
 * @package wp-hashids
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?>Alphabet set to <kbd><?php echo esc_html( $current ); ?> (<?php echo esc_html( $regex ); ?>)</kbd> via constant.

<input
	id="wp_hashids_alphabet_<?php echo esc_attr( $current ); ?>"
	name="wp_hashids_alphabet"
	type="hidden"
	value="<?php echo esc_attr( $current ); ?>"
>
