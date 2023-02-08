<?php
/**
 * The plugin uninstall script.
 *
 * @package wp-hashids
 */

use WP_Hashids\Options_Manager;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * The plugin uninstaller.
 *
 * @return void
 */
function _wph_uninstall() {
	delete_option( Options_Manager::ALPHABET_OPTION_KEY );
	delete_option( Options_Manager::MIN_LENGTH_OPTION_KEY );
	delete_option( Options_Manager::SALT_OPTION_KEY );
}

_wph_uninstall();
