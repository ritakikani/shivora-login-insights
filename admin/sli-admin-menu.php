<?php
/**
 * Admin Menu.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu class.
 *
 * Responsible for:
 * - Creating plugin top-level menu.
 * - Creating dashboard page.
 *
 * @since 1.0.0
 */
class SLI_Admin_Menu {

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
			'admin_menu',
			array(
				$this,
				'register_menu',
			)
		);
	}

	/**
	 * Register plugin menu.
	 *
	 * Creates:
	 *
	 * Last Login Tracker
	 * ├── Dashboard
	 * ├── Inactive Users
	 * ├── Export
	 * └── Settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_menu() {

		add_menu_page(
			__( 'Last Login Tracker', 'last-login-tracker' ),
			__( 'Last Login Tracker', 'last-login-tracker' ),
			'list_users',
			'sli-overview',
			array(
				$this,
				'overview_page',
			),
			'dashicons-clock',
			58
		);
	}

	/**
	 * Dashboard page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function overview_page() {

		$total_users = count_users();

		$logged_today = SLI_Helper::get_logged_in_today_count();

		$never_logged_in = SLI_Helper::get_never_logged_in_count();

		$inactive_30 = SLI_Helper::get_inactive_users_count( 30 );

		$inactive_60 = SLI_Helper::get_inactive_users_count( 60 );

		$inactive_90 = SLI_Helper::get_inactive_users_count( 90 );

		?>

		<div class="wrap">

			<h1>

				<?php esc_html_e(
					'Last Login Tracker',
					'last-login-tracker'
				); ?>

			</h1>

			<table class="widefat striped">

				<tbody>

					<tr>

						<th>

							<?php esc_html_e(
								'Total Users',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$total_users['total_users']
							); ?>

						</td>

					</tr>

					<tr>

						<th>

							<?php esc_html_e(
								'Logged In Today',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$logged_today
							); ?>

						</td>

					</tr>

					<tr>

						<th>

							<?php esc_html_e(
								'Never Logged In',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$never_logged_in
							); ?>

						</td>

					</tr>

					<tr>

						<th>

							<?php esc_html_e(
								'Inactive 30 Days',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$inactive_30
							); ?>

						</td>

					</tr>

					<tr>

						<th>

							<?php esc_html_e(
								'Inactive 60 Days',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$inactive_60
							); ?>

						</td>

					</tr>

					<tr>

						<th>

							<?php esc_html_e(
								'Inactive 90 Days',
								'last-login-tracker'
							); ?>

						</th>

						<td>

							<?php echo esc_html(
								$inactive_90
							); ?>

						</td>

					</tr>

				</tbody>

			</table>

		</div>

		<?php
	}
}