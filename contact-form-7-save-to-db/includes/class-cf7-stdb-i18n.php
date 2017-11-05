<?php
/**
	Contact Form 7 Save To DB i18n functions
*/
class CF7_STDB_i18n {

	const TEXT_DOMAIN = 'cf7-save-to-db';

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			self::TEXT_DOMAIN,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
