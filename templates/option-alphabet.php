<?php
/**
 * The alphabet radio inputs.
 *
 * @package wp-hashids
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?><fieldset>
	<legend class="screen-reader-text">
		Hashids Alphabet
	</legend>
	<?php foreach ( $options as $value => $details ) : ?>
		<label>
			<input
				<?php checked( $current === $details['alphabet'] ) ?>
				id="wp_hashids_alphabet_<?php echo esc_attr( $value ) ?>"
				name="wp_hashids_alphabet"
				type="radio"
				value="<?php echo esc_attr( $value ) ?>"
			>

			<?php echo esc_html( $details['label'] ) ?> (<?php echo esc_html( $details['regex'] ) ?>)
		</label>

		<br>
	<?php endforeach ?>
</fieldset>
