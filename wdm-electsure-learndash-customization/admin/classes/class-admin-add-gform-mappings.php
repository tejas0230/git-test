<?php

class WDM_Admin_Add_Gform_Mappings
{  
	public function __construct()
	{
		add_action( 'admin_init', array($this,'wdm_gform_register_settings') );
		add_action( 'admin_menu', array($this,'wdm_add_gform_settings_page') );

	}

	public function wdm_add_gform_settings_page()
	{
		add_menu_page(
			'Gform Mappings',
			'WDM GForms Mappings',
			'manage_options',
			'wdm-gform-mapping-settings',
			array($this,'gform_settings_page_content'),
			'dashicons-list-view',
            20
		);
	}

	public function wdm_gform_register_settings()
	{
		register_setting('wdm_gform_options_group','wdm_gform_settings');
	}

	public function gform_settings_page_content()
	{
		$options = get_option('wdm_gform_settings');

		global $wpdb;
    	$forms = $wpdb->get_results("SELECT id, title FROM wp_gf_form");

		?>
		<div class="wrap">
			<h1>Gravity Form Mappings</h1>
			<form action="options.php" method='post'>
				<?php settings_fields( 'wdm_gform_options_group' )?>
				<?php do_settings_sections( 'wdm-gform-mapping-settings')?>
				<table class='form-table'>
					<tr>
						<th scope="row">
							<label for="wdm_gform_login_form"><?php _e('Login Form','wdm-electsure-learndash-customization'); ?></label>
						</th>
						<td>
							<select id="wdm_gform_login_form" name="wdm_gform_settings[login_form]" class="regular-text" required>
								<?php foreach($forms as $form): ?>
									<option value="<?php echo esc_attr($form->id); ?>" <?php selected($options['login_form'], $form->id); ?>>
										<?php echo esc_html($form->title); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="wdm_gform_registration_form"><?php _e('Registration Form','wdm-electsure-learndash-customization'); ?></label>
						</th>
						<td>
							<select id="wdm_gform_registration_form" name="wdm_gform_settings[registration_form]" class="regular-text" required>
									<?php foreach($forms as $form): ?>
										<option value="<?php echo esc_attr($form->id); ?>" <?php selected($options['registration_form'], $form->id); ?>>
											<?php echo esc_html($form->title); ?>
										</option>
									<?php endforeach; ?>
								</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="wdm_gform_reset_pass_form"><?php _e('Reset Password Form','wdm-electsure-learndash-customization'); ?></label>
						</th>
						<td>
							<select id="wdm_gform_reset_pass_form" name="wdm_gform_settings[reset_pass_form]" class="regular-text" required>
									<?php foreach($forms as $form): ?>
										<option value="<?php echo esc_attr($form->id); ?>" <?php selected($options['reset_pass_form'], $form->id); ?>>
											<?php echo esc_html($form->title); ?>
										</option>
									<?php endforeach; ?>
								</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="wdm_gform_edit_profile_form"><?php _e('Edit Profile Form','wdm-electsure-learndash-customization'); ?></label>
						</th>
						<td>
							<select id="wdm_gform_edit_profile_form" name="wdm_gform_settings[edit_profile_form]" class="regular-text" required>
								<?php foreach($forms as $form): ?>
									<option value="<?php echo esc_attr($form->id); ?>" <?php selected($options['edit_profile_form'], $form->id); ?>>
										<?php echo esc_html($form->title); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="Save">
				</p>
			</form>
		</div>
		<?php
	}
}
$wdm_admin_add_gform_mappings = new WDM_Admin_Add_Gform_Mappings();