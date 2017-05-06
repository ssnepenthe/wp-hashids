<?php
/**
 * The salt option input template.
 *
 * @package wp-hashids
 *
 * @todo Placeholder?
 *       Button to regenerate random salt?
 */

?><input
	class="large-text"
	id="wp_hashids_salt"
	name="wp_hashids_salt"
	placeholder="Unique salt..."
	type="text"
	value="<?php echo esc_attr( $value ) ?>"
>
