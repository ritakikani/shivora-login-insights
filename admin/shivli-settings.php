<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 *
 * Responsible for:
 * - Registering plugin settings.
 * - Registering settings submenu.
 * - Rendering settings page.
 *
 * @since 1.0.0
 */
class SHIVLI_Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function hooks() {
		add_action(
			'admin_init',
			array(
				$this,
				'register_settings',
			)
		);
		add_action(
			'admin_menu',
			array(
				$this,
				'register_submenu',
			),
			20
		);
	}

	/**
	 * Register settings submenu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_submenu() {
		add_submenu_page(
			'shivli-overview',
			__( 'Settings', 'shivora-login-insights' ),
			__( 'Settings', 'shivora-login-insights' ),
			'manage_options',
			'shivli-settings',
			array(
				$this,
				'render_page',
			)
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'shivli_settings_group',
			'shivli_settings',
			array(
				'sanitize_callback' => array(
					$this,
					'sanitize_settings',
				),
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Settings values.
	 *
	 * @return array
	 */
	public function sanitize_settings( $input ) {

		$sanitized = array();

		$sanitized['track_ip'] = ! empty( $input['track_ip'] ) ? 1 : 0;

		$sanitized['dashboard_widget'] = ! empty( $input['dashboard_widget'] ) ? 1 : 0;

		$allowed_retention = array(
			'30',
			'60',
			'90',
			'180',
			'365',
			'forever',
		);

		$sanitized['retention'] = isset( $input['retention'] )
			&& in_array(
				$input['retention'],
				$allowed_retention,
				true
			) ? $input['retention'] : 'forever';

		return $sanitized;
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_page() {
		$settings = SHIVLI_Helper::get_settings(); ?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Login Insights Settings', 'shivora-login-insights'); ?>
			</h1>

			<form method="post" action="options.php" >
				<?php settings_fields( 'shivli_settings_group' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Track Login IP', 'shivora-login-insights' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="shivli_settings[track_ip]" value="1" <?php checked($settings['track_ip'], 1); ?> />
								<?php esc_html_e('Store user login IP address.',	'shivora-login-insights'); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Dashboard Widget',	'shivora-login-insights' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="shivli_settings[dashboard_widget]" value="1" <?php checked($settings['dashboard_widget'], 1); ?> />
								<?php esc_html_e('Show dashboard widget.', 'shivora-login-insights' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="retention">
								<?php esc_html_e( 'Login History Retention', 'shivora-login-insights' ); ?>
							</label>
						</th>

						<td>
							<select name="shivli_settings[retention]" id="retention" >
								<option value="30" <?php selected( $settings['retention'], '30' ); ?> >
									<?php esc_html_e( '30 Days', 'shivora-login-insights' ); ?>
								</option>

								<option value="60" <?php selected( $settings['retention'], '60' ); ?> >
									<?php esc_html_e( '60 Days', 'shivora-login-insights' ); ?>
								</option>

								<option value="90" <?php selected( $settings['retention'], '90' ); ?> >
									<?php esc_html_e( '90 Days', 'shivora-login-insights' ); ?>
								</option>

								<option value="180" <?php selected( $settings['retention'], '180' ); ?>>
									<?php esc_html_e( '180 Days', 'shivora-login-insights' ); ?>
								</option>

								<option value="365" <?php selected( $settings['retention'], '365' ); ?> >
									<?php esc_html_e( '1 Year', 'shivora-login-insights' ); ?>
								</option>

								<option value="forever" <?php selected( $settings['retention'], 'forever' ); ?> >
									<?php esc_html_e( 'Keep Forever', 'shivora-login-insights' ); ?>
								</option>
							</select>

							<p class="description">
								<?php esc_html_e( 'Automatically delete login history older than the selected period.', 'shivora-login-insights' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}