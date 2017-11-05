<div class="wrap forms-listing">
	<h1><?php _e('Form name:', $this->text_domain); ?> <?php echo $form_name ?></h1>

	<div class="form-collector-filter">
		<form id="form-filter" action="">
			<div class="form-filter-field-names">
				<?php foreach ($all_cols_names as $name ) : ?>
					<?php $checked = in_array($name, $hidden_rows_title) ? 'checked' : '' ?>
					<label>
						<input type="checkbox" name="hidden_rows[<?php echo $name; ?>]"
						       <?php echo $checked; ?> value=""/>
						<span class="title-field-name"><?php echo $name; ?></span>
					</label>
				<?php endforeach; ?>
			</div>

			<label for="from">From</label>
			<input type="text" id="from" name="from" value="<?php echo esc_attr( $filter_data['from'] ); ?>">
			<label for="to">to</label>
			<input type="text" id="to" name="to" value="<?php echo esc_attr( $filter_data['to'] ); ?>">

			<input type="text" name="search" placeholder="<?php _e('Search', $this->text_domain); ?>"
			       value="<?php echo esc_attr( $filter_data['search'] ); ?>"/>
			<button>GO</button>

			<br />
			<label for="post_view">post_view</label>
			<select name="post_view_items" id="post_view">
				<option value=""><?php _e('Select post_view'); ?></option>
				<?php foreach ($post_view_items as $name ) : ?>
					<option value="<?php echo $name; ?>"  <?php selected($name, $filter_data['post_view_items']); ?>>
						<?php echo $name; ?>
					</option>
				<?php endforeach; ?>
			</select>
			<label for="post_type">post_type</label>
			<select name="post_type_items" id="post_type">
				<option value=""><?php _e('Select post_type'); ?></option>
				<?php foreach ($post_type_items as $name ) : ?>
					<option value="<?php echo $name; ?>" <?php selected($name, $filter_data['post_type_items']); ?> >
						<?php echo $name; ?>
					</option>
				<?php endforeach; ?>
			</select>

			<br />
			<input type="hidden" name="page" value="<?php echo $form_page; ?>"/>
			<input type="hidden" name="form_name" value="<?php echo $form_name ?>"/>

			<button type="button" id="reset" value="Reset">Reset</button>

			<input type="submit" name="csv" value="Export list as CSV file"/>
		</form>
	</div>

	<?php if (!$forms) :
		_e('No Forms found', $this->text_domain);
	else : ?>

		<table class="wp-list-table widefat fixed striped comments">
			<thead>
			<tr>
				<th><?php _e('#', $this->text_domain); ?></th>
				<?php foreach ($cols_names as $name ) : ?>
					<?php if ($name === 'date') : ?>
						<th class="sorted <?php echo ($filter_data['sort'] === 'desc') ? 'desc' : 'asc'; ?>">
							<a href="<?php echo $page_url . '&sort_by=date&sort=' .
							                    (($filter_data['sort'] === 'desc') ? 'asc' : 'desc' ); ?>">
								<span><?php echo $name; ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php else : ?>
						<th><?php echo $name; ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody>

			<?php foreach ($forms as $key => $form ) : ?>
				<tr>
					<td><?php echo $key + 1; ?></td>
					<?php foreach ($cols_names as $field_name ) : ?>
						<?php if ($field_name === 'id') { continue; } ?>
						<td>
							<?php echo esc_attr(
								!empty($form[$field_name]) ? $form[$field_name] : ''
							); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>

	<?php endif; ?>
</div>