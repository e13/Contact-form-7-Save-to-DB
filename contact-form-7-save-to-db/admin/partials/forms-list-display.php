<div class="wrap forms-listing">
	<h1><?php _e('Forms list', $this->text_domain); ?></h1>

	<?php if (!$forms) :
		_e('No Forms found', $this->text_domain);
	else : ?>

		<table class="wp-list-table widefat fixed striped comments">
			<thead>
			<tr>
				<th><?php _e('#', $this->text_domain); ?></th>
				<th><?php _e('Name', $this->text_domain); ?></th>
				<th><?php _e('Title', $this->text_domain); ?></th>
				<th><?php _e('Show', $this->text_domain); ?></th>
				<th><?php _e('Submit', $this->text_domain); ?></th>
				<th></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ($forms as $key => $form ) : ?>
				<tr>
					<td><?php echo $key + 1; ?></td>
					<td><?php echo esc_attr( $form['name'] ); ?></td>
					<td><?php echo esc_attr( $form['title'] ); ?></td>
					<td><?php echo esc_attr( $form['show_count'] ); ?></td>
					<td><?php echo esc_attr( $form['send_count'] ); ?></td>
					<td>
						<?php printf(
							'<a href="%s">%s</a>',
							$form_page_url . '&form_name=' . $form['name'],
							__('View', $this->text_domain)
						); ?>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>

	<?php endif; ?>
</div>