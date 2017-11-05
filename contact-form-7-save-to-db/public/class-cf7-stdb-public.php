<?php

class CF7_STDB_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function save_submit_form($cf7) {
		if (!$this->is_enabled_collect_data()) {
			return;
		}

		$form = WPCF7_Submission::get_instance();
		if ( !$form ) {
			return;
		}
        $mfcma = new Mechanical_Form_Collector_Model_Admin();
		$mfcma->save_cf7_form($cf7, $form);
	}

	private function is_enabled_collect_data(  ) {
        $mfcs = new Mechanical_Form_Collector_Settings();
		$option = $mfcs->get_cf7_collect_data_option();

		return $option === 'on';
	}

	public function count_form_submit( $cf7 ) {
		if (!$this->is_enabled_statistic()) {
			return;
		}

		$model = new Mechanical_Form_Collector_Model_Admin();
		$model->increase_form_submit_count($cf7);
	}

	public function count_show_form(  ) {
		if (!$this->is_enabled_statistic() || empty($_POST['forms'])) {
			return;
		}

		$forms = $_POST['forms'];
		foreach ( $forms as $form_id ) {
			$model = new Mechanical_Form_Collector_Model_Admin();
			$model->increase_form_show_count(wpcf7_contact_form( $form_id ));
		}

		wp_die();
	}

	private function is_enabled_statistic(  ) {
        $mfcs = new Mechanical_Form_Collector_Settings();
		$option = $mfcs->get_cf7_statistic();

		return $option === 'on';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mechanical-form-collector-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'form-collector-public.js',
			plugin_dir_url( __FILE__ ) . 'js/mechanical-form-collector-public.js',
			array( 'jquery' ), $this->version, false
		);

//		$cf7_js_scripts = (new Mechanical_Form_Collector_Settings())->get_cf7_code_option();
//
//		wp_localize_script(
//			'form-collector-public.js',
//			'formCollectorJSData',
//			[
//				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
//				'eventScripts' => $this->prepare_js_scripts($cf7_js_scripts),
//				'pageData' => $this->get_page_data(),
//			]
//		);
	}

	private function prepare_js_scripts( $cf7_js_scripts ) {
		$ret = '';
		foreach ( $cf7_js_scripts as $key => $item ) {
			$item = $item !== NULL ? $item : '';
			$ret .= "function {$key}(){{$item}}";
		}

		return sprintf('<script>%s</script>', ($ret));
	}

	public function add_post_data_to_cf7_form( $hidden_fields ) {
		return array_merge($hidden_fields, $this->get_page_data());
	}

	private function get_page_data(  ) {
		$page_data = array();
		if ( is_singular() ) {
			$post_id = get_the_ID();

			$page_data['_mfc_post_id'] = $post_id;
			$page_data['_mfc_post_view'] = 'post';
			$page_data['_mfc_post_type'] = get_post_type($post_id);
		} elseif (is_tax() || is_category() || is_tag()) {
			$term = get_queried_object();

			$page_data['_mfc_post_id'] = $term->term_id;
			$page_data['_mfc_post_view'] = 'term';
			$page_data['_mfc_post_type'] = $term->taxonomy;
		}

		return $page_data;
	}

}
