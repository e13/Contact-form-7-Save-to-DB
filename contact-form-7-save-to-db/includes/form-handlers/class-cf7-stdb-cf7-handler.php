<?php

class CF7_STDB_Handler {
	private $loader;
	private $model;
	const POST_TYPE = 'wpcf7_contact_form';

	const POST_DATA_PREFIX = '_mfc_';

	public function __construct( ) {
		$this->loader = new CF7_STDB_Collector_Loader();
		$this->model = new CF7_STDB_Model_Admin();
	}

	public function init(  ) {
		$this->define_collect_hooks();
		$this->define_statistics_hooks();
		
		$this->loader->add_filter( 'mcf_admin_forms_list', $this, 'add_form_list' );
		$this->loader->add_action( 'wpcf7_enqueue_scripts', $this, 'enqueue_scripts' );

		$this->loader->run();
	}

	public function define_collect_hooks(  ) {
		$this->loader->add_action( 'wpcf7_form_hidden_fields', $this, 'add_post_data_to_cf7_form' );
		$this->loader->add_action( 'wpcf7_mail_sent', $this, 'save_submit_form' );
	}

	public function define_statistics_hooks(  ) {
		$this->loader->add_action( 'wp_ajax_count_show_form', $this, 'count_show_form' );
		$this->loader->add_action( 'wp_ajax_nopriv_count_show_form', $this, 'count_show_form' );
		$this->loader->add_action( 'wpcf7_mail_sent', $this, 'count_form_submit' );
	}

	public function save_submit_form($cf7) {
		if ( !$form = WPCF7_Submission::get_instance() ) {
			return;
		}

		$post_fields = $this->get_post_fields($form->get_posted_data());
		$post_data = $this->get_post_data($form->get_posted_data());
		$post_data['form_id'] = $cf7->id();
		$post_data['name'] = $cf7->name();
		$post_data['title'] = $cf7->title();

		$this->model->save_cf7_form($post_data, $post_fields);
		do_action( 'mfc-save-submit-form', $post_data['form_id'], 'cf7', $post_fields );
	}

	private function get_post_data( $fields ) {
		$post_data = array();
		foreach ( $fields as $key => $field ) {
			if ( strpos( $key, self::POST_DATA_PREFIX ) !== 0 ) { continue; }
			$post_data[ str_replace(self::POST_DATA_PREFIX, '', $key) ] = $field;
		}
		return $post_data;
	}

	private function get_post_fields( $fields ) {
		$post_fields = array();
		foreach ( $fields as $key => $field ) {
			if (strpos($key, '_wpcf7') === 0 || strpos($key, self::POST_DATA_PREFIX) === 0) { continue; }
			$post_fields[$key] = is_array($field) ? implode('|', $field) : $field;
		}
		return $post_fields;
	}

	public function count_form_submit( $cf7 ) {
		$this->model->increase_form_submit_count(array(
			'form_id' => $cf7->id(),
			'name' => $cf7->name(),
		));
	}

	public function count_show_form(  ) {
		$forms = $_POST['forms'];
		foreach ( $forms as $form_id ) {
			$cf7 = wpcf7_contact_form( $form_id );
			$this->model->increase_form_show_count(array(
				'form_id' => $cf7->id(),
				'name' => $cf7->name(),
			));
		}

		wp_die();
	}

	public function add_form_list() {
		global $wpdb;
		$post_type = self::POST_TYPE;
		$query = "
			SELECT ID as id, post_title as 'form_name', post_type
			FROM {$wpdb->posts}
			WHERE post_type = '{$post_type}'
		";
		$forms = $wpdb->get_results( $query, ARRAY_A);
		array_walk(
			$forms,
			function(&$item) {
				$item['mail_system'] = 'cf7';
			}
		);

		return $forms;
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'mfc-cf7-public.js',
			plugin_dir_url( __FILE__ ) . 'js/mfc-cf7-public.js',
			array( 'jquery' ), '0.0.1', false
		);

		wp_localize_script(
			'mfc-cf7-public.js',
			'formCollectorJSData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'pageData' => $this->get_page_data(),
			)
		);
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

	public function get_form_fields_by_id( $form_id ) {
		if ( !$form = WPCF7_Submission::get_instance(wpcf7_contact_form( $form_id )) ) {
			return array();
		}
		$fields = array_diff(array_keys($form->get_posted_data() ), array('data', 'action'));
		return $fields;
	}
}
