<?php
/**
    Contact Form 7 Save To DB Activator class

	Defines which actions should take plase on plugin activation

*/
class CF7_STDB_Activator {

	public static function activate() {
		global $wpdb;

		// Creates two tables in WP database for data storage and statistics

		$db_name = CF7_STDB::get_form_table_name();
		if($wpdb->get_var("show tables like '$db_name'") != $db_name)
		{
			$sql = "CREATE TABLE " . $db_name . " (
				`id` mediumint(9) NOT NULL AUTO_INCREMENT,
				`form_id` int(12) NOT NULL default 0,
				`name` text NOT NULL default '',
				`title` text NOT NULL default '',
				`post_id` int(12) NOT NULL default 0,
				`post_view` Varchar(512) NOT NULL default '',
				`post_type` Varchar(512) NOT NULL default '',
				`date` datetime NOT NULL default '0000-00-00 00:00:00',
				`fields_name` longtext NOT NULL default '',
				`fields_value` longtext NOT NULL default '',
				PRIMARY KEY id (id)
				)
				CHARACTER SET utf8
				COLLATE utf8_general_ci
				;
			";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		$db_name = CF7_STDB::get_form_statistic_table_name();
		if($wpdb->get_var("show tables like '$db_name'") != $db_name)
		{
			$sql = "CREATE TABLE " . $db_name . " (
				`id` mediumint(9) NOT NULL AUTO_INCREMENT,
				`form_id` int(12) NOT NULL default 0,
				`name` text NOT NULL default '',
				`show_count` int(12) NOT NULL default 0,
				`send_count` int(12) NOT NULL default 0,
				PRIMARY KEY id (id)
				)
				CHARACTER SET utf8
				COLLATE utf8_general_ci
				;
			";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

}