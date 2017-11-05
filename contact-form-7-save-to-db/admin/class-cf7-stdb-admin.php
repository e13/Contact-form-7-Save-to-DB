<?php

class CF7_STDB_Admin {

	private $plugin_name;
	private $version;
	private $text_domain;
	const SETTINGS_PAGE_SLUG = 'cf7-save-to-db';

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->text_domain = CF7_STDB_i18n::TEXT_DOMAIN;
	}

	public function add_forms_list_page(  ) {
		add_menu_page(
			__('CF7 save to DB', $this->text_domain),
			__('CF7 save to DB', $this->text_domain),
			'manage_options',
		    self::SETTINGS_PAGE_SLUG,
		    array($this, 'show_forms_list')
		);
	}

	public function show_forms_list(  ) {
		if (!(empty($_GET['form_name']))) {
			$form_name = sanitize_text_field($_GET['form_name']);
			$this->show_forms_by_form_name($form_name);
		} else {
			$this->show_forms_template();
		}
	}

	private function show_forms_template( ) {
		$form_page_url = menu_page_url(self::SETTINGS_PAGE_SLUG, false);
		$model = new CF7_STDB_Model_Admin();
		$forms = $model->get_used_form_template();

		require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/forms-list-display.php');
	}

	private function show_forms_by_form_name( $form_name ) {
		if (empty($form_name)) {
			return;
		}

		$filter_data = $this->get_forms_filter_data();
		$model = new CF7_STDB_Model_Admin();
		$forms = $model->get_form_by_name($form_name, $filter_data);
		$all_cols_names = $this->get_forms_titles($forms);
		$hidden_rows_title = $this->get_hidden_rows_title();
		$forms = $this->hide_cols($forms, $hidden_rows_title);
		$cols_names = $this->get_forms_titles($forms);
		$form_page = self::SETTINGS_PAGE_SLUG;
		$page_url = $this->prepare_forms_page_url($form_page, $form_name, $filter_data, $hidden_rows_title);
		$post_view_items = $model->get_post_view();
		$post_type_items = $model->get_post_type();

		require(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/forms-by-name-display.php');
	}

	private function get_hidden_rows_title(  ) {
		if (empty($_GET['hidden_rows'])) {
			return array();
		}

		return array_keys($_GET['hidden_rows']);
	}

	private function hide_cols( $forms, $hidden_rows_title ) {
		$ret = array();
		foreach ( $forms as $key => $form ) {
			foreach ( $form as $field_name => $field_value ) {
				if (in_array($field_name, $hidden_rows_title)) { continue; }
				$ret[$key][$field_name] = $field_value;
			}
		}

		return $ret;
	}

	private function prepare_forms_page_url( $form_page, $form_name, $filter_data, array $hidden_rows_title ) {
		$form_page_url = menu_page_url($form_page, false);
		$form_page_url = add_query_arg(
			array( 'form_name' => $form_name ),
			$form_page_url
		);
		if (!empty($filter_data['search'])) {
			$form_page_url = add_query_arg(
				array( 'search' => $filter_data['search'] ),
				$form_page_url
			);
		}
		if (!empty($filter_data['from'])) {
			$form_page_url = add_query_arg(
				array( 'from' => $filter_data['from'] ),
				$form_page_url
			);
		}
		if (!empty($filter_data['to'])) {
			$form_page_url = add_query_arg(
				array( 'to' => $filter_data['to'] ),
				$form_page_url
			);
		}
		if (!empty($filter_data['post_view_items'])) {
			$form_page_url = add_query_arg(
				array( 'post_view_items' => $filter_data['post_view_items'] ),
				$form_page_url
			);
		}
		if (!empty($filter_data['post_type_items'])) {
			$form_page_url = add_query_arg(
				array( 'post_type_items' => $filter_data['post_type_items'] ),
				$form_page_url
			);
		}

		foreach ( $hidden_rows_title as $row_title ) {
			$title = sanitize_text_field($row_title);
			$form_page_url = add_query_arg(
				array( "hidden_rows[{$title}]" => '' ),
				$form_page_url
			);
		}
		return esc_url($form_page_url);
	}

	public function download_forms_in_csv(  ) {
		if (!isset($_GET['csv'])) {
			return;
		}
		$form_name = sanitize_text_field($_GET['form_name']);

		$filter_data = $this->get_forms_filter_data();
		$model = new CF7_STDB_Model_Admin();
		$forms = $model->get_form_by_name($form_name, $filter_data);
		$hidden_rows_title = $this->get_hidden_rows_title();
		$forms = $this->hide_cols($forms, $hidden_rows_title);

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=data.csv' );

		// create a file pointer connected to the output stream
		$output = fopen( 'php://output', 'w' );
		// output the column headings
		fputcsv( $output, array_merge(array('id'), $this->get_forms_titles($forms)) );

		foreach ( $forms as $form ) {
			fputcsv( $output, $form );
		}
		exit;
	}

	private function get_forms_filter_data() {
		return array(
			'search' => !(empty($_GET['search'])) ? sanitize_text_field(trim($_GET['search'])) : '',
			'from' => !(empty($_GET['from'])) ? sanitize_text_field(trim($_GET['from'])) : '',
			'to' => !(empty($_GET['to'])) ? sanitize_text_field(trim($_GET['to'])) : '',
			'sort_by' => !(empty($_GET['sort_by'])) ? sanitize_text_field(trim($_GET['sort_by'])) : '',
			'sort' => !(empty($_GET['sort'])) ? sanitize_text_field(trim($_GET['sort'])) : '',
			'post_view_items' => !(empty($_GET['post_view_items'])) ? sanitize_text_field(trim($_GET['post_view_items'])) : '',
			'post_type_items' => !(empty($_GET['post_type_items'])) ? sanitize_text_field(trim($_GET['post_type_items'])) : ''
		);
	}

	private function get_forms_titles($forms) {
		if (empty($forms)) {
			return array();
		}
		$fields_name = array();
		foreach ( $forms as $form ) {
			foreach ( $form as $field_name => $value ) {
				if ($field_name === 'id' || in_array($field_name, $fields_name)) {
					continue;
				}
				$fields_name[] = $field_name;
			}
		}
		return $fields_name;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/mechanical-form-collector-admin.css',
			array(), $this->version, 'all'
		);

		wp_enqueue_style( 'jquery-ui-datepicker' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'mechanical-form-collector-admin.js',
			plugin_dir_url( __FILE__ ) . 'js/mechanical-form-collector-admin.js',
			array( 'jquery' ), $this->version, false
		);
		wp_localize_script(
			'mechanical-form-collector-admin.js',
			'ajax_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ))
		);

		wp_enqueue_script(
			'highlight.pack.js',
			'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), $this->version, false
		);
	}
}
