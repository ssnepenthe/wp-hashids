<?php
/**
 * The alphabet radio inputs.
 *
 * @package wp-hashids
 */

?><fieldset>
	<legend class="screen-reader-text">
		<!-- @todo -->
	</legend>
	<?php foreach ( $options as $option ) : ?>
			<label>
				<input
					<?php checked( $option['checked'] ) ?>
					id="wp_hashids_alphabet_<?php echo esc_attr( $option['value'] ) ?>"
					name="wp_hashids_alphabet"
					type="radio"
					value="<?php echo esc_attr( $option['value'] ) ?>"
				>

				<?php echo esc_html( $option['label'] ) ?> (<?php echo esc_html( $option['regex'] ) ?>)
			</label>

			<br>
	<?php endforeach ?>
</fieldset>
