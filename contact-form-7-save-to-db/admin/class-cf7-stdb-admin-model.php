<?php

class CF7_STDB_Model_Admin {
	private $db;
	private $form_table;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->form_table = CF7_STDB::get_form_table_name();
		$this->form_statistic_table = CF7_STDB::get_form_statistic_table_name();
	}

	public function save_cf7_form($post_data, $post_fields) {
		$this->db->insert(
			$this->form_table,
			array(
				'name' => $post_data['name'],
				'title' => $post_data['title'],
				'form_id' => $post_data['form_id'],
				'post_id' => $post_data['post_id'],
				'post_view' => $post_data['post_view'],
				'post_type' => $post_data['post_type'],
				'date' => date("Y-m-d H:i:s"),
				'fields_name' => serialize(array_keys($post_fields)),
				'fields_value' => serialize(array_values($post_fields)),
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', )
		);
	}

	public function get_used_form_template() {
		$forms = $this->db->get_results("
			SELECT f.name, f.title, s.send_count, s.show_count
			FROM {$this->form_table} as f
			LEFT JOIN {$this->form_statistic_table} as s ON s.name = f.name
			GROUP BY name
		", ARRAY_A);

		return $forms;
	}

	public function get_form_by_name($form_name = '', $filter_data) {
		$where = 'WHERE name = %s';
		$placeholders = array($form_name);

		if (!empty($filter_data['search'])) {
			$where .= ' AND `fields_value` LIKE \'%%%s%%\'';
			array_push($placeholders, $filter_data['search']);
		}
		if (!empty($filter_data['from']) && $from = strtotime($filter_data['from'])) {
			$where .= ' AND `date` >= \'%s\'';
			array_push($placeholders, date('Y-m-d 00:00:00', $from));
		}
		if (!empty($filter_data['to']) && $to = strtotime($filter_data['to'])) {
			$where .= ' AND `date` <= \'%s\'';
			array_push($placeholders, date('Y-m-d 23:59:59', $to));
		}
		if (!empty($filter_data['post_view_items'])) {
			$where .= ' AND `post_view` = \'%s\'';
			array_push($placeholders, $filter_data['post_view_items']);
		}
		if (!empty($filter_data['post_type_items'])) {
			$where .= ' AND `post_type` = \'%s\'';
			array_push($placeholders, $filter_data['post_type_items']);
		}

		$order_by = sprintf(
			' ORDER BY `%s` %s',
			!empty($filter_data['sort_by']) ? $filter_data['sort_by'] : 'date',
			$filter_data['sort'] === 'asc' ? 'ASC' : 'DESC'
		);

		$query = $this->db->prepare("
			SELECT *
			FROM {$this->form_table} as f
			{$where}
			{$order_by}
			",
			$placeholders
		);
		$forms = $this->db->get_results($query, ARRAY_A);

		$forms = array_map(
			function($item) {
				$fields = array_combine(unserialize($item['fields_name']), unserialize($item['fields_value']));
				unset($item['fields_name']);
				unset($item['fields_value']);

				foreach ( $fields as $key => $field ) {
					$item[$key] = $field;
				}

				return $item;
			},
			$forms
		);

		return $forms;
	}

	public function get_post_view(  ) {
		return $this->db->get_col("
			SELECT DISTINCT post_view
			FROM {$this->form_table}
		" );
	}

	public function get_post_type(  ) {
		return $this->db->get_col("
			SELECT DISTINCT post_type
			FROM {$this->form_table}
		" );
	}

	public function increase_form_submit_count( $post_data ) {
		$form = $this->db->get_row("
			SELECT *
			FROM {$this->form_statistic_table}
			WHERE `name` = '{$post_data['name']}'
		", ARRAY_A);

		if (empty($form)) {
			$this->db->insert(
				$this->form_statistic_table,
				array(
					'form_id' => $post_data['form_id'],
					'name' => $post_data['name'],
					'show_count' => 1,
					'send_count' => 1,
				),
				array( '%s', '%s', '%s', '%s' )
			);
		} else {
			$this->db->update(
				$this->form_statistic_table,
				array(
					'send_count' => $form['send_count'] + 1,
				),
				array(
					'name' => $post_data['name']
				),
				array( '%d' ),
				array( '%s' )
			);
		}
	}

	public function increase_form_show_count( $post_data ) {
		$form = $this->db->get_row("
			SELECT *
			FROM {$this->form_statistic_table}
			WHERE `name` = '{$post_data['name']}'
		", ARRAY_A);

		if (empty($form)) {
			$this->db->insert(
				$this->form_statistic_table,
				array(
					'form_id' => $post_data['form_id'],
					'name' => $post_data['name'],
					'show_count' => 1,
					'send_count' => 1,
				),
				array( '%s', '%s', '%s', '%s' )
			);
		} else {
			$this->db->update(
				$this->form_statistic_table,
				array(
					'show_count' => $form['show_count'] + 1,
				),
				array(
					'name' => $post_data['name']
				),
				array( '%d' ),
				array( '%s' )
			);
		}
	}
}
