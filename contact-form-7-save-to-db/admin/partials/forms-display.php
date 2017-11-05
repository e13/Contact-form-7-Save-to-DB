<div class="wrap forms">
	<h1><?php _e('Forms', $this->text_domain); ?></h1>

	<form method="post" class="mcf-service-list" action="<?php menu_page_url('mechanical-form-collector-forms') ?>">
		<table class="wp-list-table widefat fixed striped">
			<thead>
			<tr>
				<th><?php _e('Mailing service List', $this->text_domain); ?></th>
				<th><?php _e('Form', $this->text_domain); ?></th>
				<th><?php _e('Mail system', $this->text_domain); ?></th>
				<th><?php _e('Conditional subscription', $this->text_domain); ?></th>
				<th><?php _e('Condition', $this->text_domain); ?></th>
				<th><?php _e('Mail list mapping', $this->text_domain); ?></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ($forms as $form) : ?>
				<?php $current_name = $form['mail_system'] . '-' . $form['id']; ?>

				<tr>
					<td>
						<select class="service-list" name="forms[<?php echo $current_name; ?>][service_list]">

						<?php foreach ( $mailing_service_list as $service => $lists ) : ?>
							<option value=""><?php _e('All service list', $this->text_domain); ?></option>
							<optgroup label="<?php echo $service; ?>">

								<?php foreach ( $lists as $item ) : ?>
									<?php $current_option_name = $service . '-' . $item['id'];; ?>

									<?php $selected = !empty($saved_forms_list[$current_name]['service_list'])
										&& trim($saved_forms_list[$current_name]['service_list']) ==
										   $current_option_name; ?>

									<option value="<?php echo $current_option_name; ?>"
										<?php selected($selected, TRUE); ?>><?php echo $item['name']; ?></option>
								<?php endforeach; ?>

							</optgroup>
						<?php endforeach; ?>

						</select>
					</td>
					<td><?php echo $form['form_name']; ?></td>
					<td><?php echo $form['mail_system']; ?></td>
					<td>
						<?php $checked = !empty($saved_forms_list[$current_name]['condition_status']) &&
						                  $saved_forms_list[$current_name]['condition_status'] === 'on'; ?>

						<input class="mcf-switcher" name="forms[<?php echo $current_name; ?>][condition_status]"
						       type="checkbox" <?php echo checked($checked, TRUE) ; ?>/>
					</td>
					<td class="mcf-condition-wrap">
						<span class="mcf-permanent">permanent</span>
						<div class="mcf-condition">
							<?php $condition_field = !empty($saved_forms_list[$current_name]['condition_field'])
									? esc_attr($saved_forms_list[$current_name]['condition_field'])
									: ''; ?>
							<input type="text" name="forms[<?php echo $current_name; ?>][condition_field]"
								value="<?php echo $condition_field; ?>" />
							<?php $conditions = ['=', '<', '<=', '>', '>=']; ?>
							<select name="forms[<?php echo $current_name; ?>][condition_type]" id="">
								<?php foreach ($conditions as $value ) : ?>
									<?php $selected = !empty($saved_forms_list[$current_name]['condition_type'])
									                  && $saved_forms_list[$current_name]['condition_type'] == $value; ?>
									<option value="<?php echo $value; ?>"
										<?php selected($selected, TRUE); ?>><?php echo $value; ?></option>
								<?php endforeach; ?>
							</select>
							<?php $condition_value = !empty($saved_forms_list[$current_name]['condition_value'])
								? esc_attr($saved_forms_list[$current_name]['condition_value'])
								: ''; ?>
							<input type="text" name="forms[<?php echo $current_name; ?>][condition_value]"
							       value="<?php echo $condition_value; ?>"/>
						</div>
					</td>
					<td>
						<button class="get-form-settings" type="button"
						        data-form-id="<?php echo $form['id']; ?>"
						        data-form-system="<?php echo $form['mail_system']; ?>">
							<span class="dashicons dashicons-edit"></span>
						</button>
					</td>
				</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<input type="submit" name="submit" id="submit" class="button button-primary"
                      value="<?php _e('Save Changes', $this->text_domain); ?>">
			</div>
			<br class="clear">
		</div>

	</form>
</div>