<?php
/**
 * The minimum length input template.
 *
 * @package wp-hashids
 *
 * @todo Placeholder?
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?><input
	class="small-text"
	id="wp_hashids_min_length"
	min="0"
	name="wp_hashids_min_length"
	step="1"
	type="number"
	value="<?php echo esc_attr( $value ); ?>"
> characters
