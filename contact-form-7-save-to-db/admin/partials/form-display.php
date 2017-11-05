<div class="wrap forms-listing">
	<h1><?php _e('Form', $text_domain); ?></h1>

	<?php if (!$form) :
		_e('No data found', $text_domain);
	endif; ?>

	<table class="wp-list-table widefat fixed striped comments">
		<thead>
			<tr>
				<th><?php _e('Field', $text_domain); ?></th>
				<th><?php _e('Value', $text_domain); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e('Name', $text_domain); ?></td>
				<td><?php echo $form->name; ?></td>
			</tr>
			<tr>
				<td><?php _e('Title', $text_domain); ?></td>
				<td><?php echo $form->title; ?></td>
			</tr>
			<tr>
				<td><?php _e('Date', $text_domain); ?></td>
				<td><?php echo $form->date; ?></td>
			</tr>
			<tr>
				<td colspan="2" style="font-weight: bold; font-style: 14px;"><?php _e('Fields', $text_domain); ?></td>
			</tr>

			<?php foreach ($form->fields as $name => $field ) : ?>
				<tr>
					<td><?php echo $name ?></td>
					<td><?php echo $field ?></td>
				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>
</div>