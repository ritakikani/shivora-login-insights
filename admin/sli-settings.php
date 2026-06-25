<?php
/**
 * Settings.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

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
class SLI_Settings {

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
			'sli-overview',
			__( 'Settings', 'last-login-tracker' ),
			__( 'Settings', 'last-login-tracker' ),
			'manage_options',
			'sli-settings',
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
			'wpll_settings_group',
			'wpll_settings',
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

		$sanitized['track_ip'] = ! empty(
			$input['track_ip']
		) ? 1 : 0;

		$sanitized['dashboard_widget'] = ! empty(
			$input['dashboard_widget']
		) ? 1 : 0;

		$allowed_retention = array(
			'30',
			'60',
			'90',
			'forever',
		);

		$sanitized['retention'] = isset(
			$input['retention']
		) && in_array(
			$input['retention'],
			$allowed_retention,
			true
		)
			? $input['retention']
			: 'forever';

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

		$settings = SLI_Helper::get_settings();

		?>

		<div class="wrap">

			<h1>
				<?php esc_html_e(
					'Last Login Tracker Settings',
					'last-login-tracker'
				); ?>
			</h1>

			<form
				method="post"
				action="options.php"
			>

				<?php
				settings_fields(
					'wpll_settings_group'
				);
				?>

				<table class="form-table">

					<tr>

						<th scope="row">

							<?php esc_html_e(
								'Track Login IP',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<label>

								<input
									type="checkbox"
									name="wpll_settings[track_ip]"
									value="1"
									<?php checked(
										$settings['track_ip'],
										1
									); ?>
								/>

								<?php esc_html_e(
									'Store user login IP address.',
									'last-login-tracker'
								); ?>

							</label>

						</td>

					</tr>

					<tr>

						<th scope="row">

							<?php esc_html_e(
								'Dashboard Widget',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<label>

								<input
									type="checkbox"
									name="wpll_settings[dashboard_widget]"
									value="1"
									<?php checked(
										$settings['dashboard_widget'],
										1
									); ?>
								/>

								<?php esc_html_e(
									'Show dashboard widget.',
									'last-login-tracker'
								); ?>

							</label>

						</td>

					</tr>

					<tr>

						<th scope="row">

							<?php esc_html_e(
								'Retention Period',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<select
								name="wpll_settings[retention]"
							>

								<option value="30" <?php selected( $settings['retention'], '30' ); ?>>
									30 Days
								</option>

								<option value="60" <?php selected( $settings['retention'], '60' ); ?>>
									60 Days
								</option>

								<option value="90" <?php selected( $settings['retention'], '90' ); ?>>
									90 Days
								</option>

								<option value="forever" <?php selected( $settings['retention'], 'forever' ); ?>>
									Forever
								</option>

							</select>

						</td>

					</tr>

				</table>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php
	}
}