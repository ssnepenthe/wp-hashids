<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>

	<form action="options.php" method="POST">
		<?php settings_fields( $group ) ?>
		<?php do_settings_sections( $page ) ?>
		<?php submit_button() ?>
	</form>
</div>
