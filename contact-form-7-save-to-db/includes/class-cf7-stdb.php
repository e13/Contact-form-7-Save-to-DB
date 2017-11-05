<?php

class CF7_STDB {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {

		$this->plugin_name = 'cf7-save-to-db';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->load_forms_handler();

	}

	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-stdb-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-stdb-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cf7-stdb-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cf7-stdb-public.php';

		$this->loader = new CF7_STDB_Collector_Loader();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/form-handlers/class-cf7-stdb-cf7-handler.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cf7-stdb-admin-model.php';

	}

	private function set_locale() {

		$plugin_i18n = new CF7_STDB_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new CF7_STDB_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'download_forms_in_csv' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_forms_list_page', 20);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		if (is_admin()) {
			return;
		}

		$plugin_public = new CF7_STDB_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

	}

	private function load_forms_handler(  ) {
		$cf7 = new CF7_STDB_Handler();
		$cf7->init();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public static function get_form_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'cf7_stdb_loader';
	}

	public static function get_form_statistic_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'cf7_stdb_loader_statistic';
	}

}
