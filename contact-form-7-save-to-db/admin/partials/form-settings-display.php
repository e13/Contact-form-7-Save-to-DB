<div class="mfc-form-settings-wrap">
	<form id="mfc-form-settings" method="post" action="">
		<span class="dashicons dashicons-no mfc-form-settings-close"></span>
		<h2><?php _e('Mail list mapping', $this->text_domain); ?></h2>
		<?php foreach ($service_fields as $key => $service_field ) : ?>
			<div>
				<select name="setting[<?php echo $key; ?>][form]" id="">
					<option value=""><?php _e('Select field', $this->text_domain); ?></option>
				<?php foreach ($form_fields as $form_field ) : ?>
					<?php $selected = !empty($settings[$form_id][$service_field]) &&
					                  $settings[$form_id][$service_field]['form'] === $form_field; ?>
					<option value="<?php echo $form_field; ?>"
						<?php selected($selected, true); ?>><?php echo $form_field; ?></option>
				<?php endforeach; ?>
				</select>
				<input type="text" name="setting[<?php echo $key; ?>][service]" value="<?php echo $service_field; ?>" readonly/>
			</div>
		<?php endforeach; ?>
		<input type="hidden" name="form_id" value="<?php echo $form_id; ?>"/>
		<button type="submit"><?php _e('Save', $this->text_domain); ?></button>
	</form>
</div>