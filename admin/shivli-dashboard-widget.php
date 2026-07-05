<?php
// Exit if accessed directly, outside of the WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard widget class.
 *
 * Responsible for:
 * - Registering dashboard widget.
 * - Displaying login activity summary.
 *
 * @since 1.0.0
 */
class SHIVLI_Dashboard_Widget {

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
			'wp_dashboard_setup',
			array( $this, 'register_widget' )
		);
	}

	/**
	 * Register dashboard widget.
	 *
	 * Widget visibility is controlled
	 * through plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_widget() {
		$settings = SHIVLI_Helper::get_settings();

		// Widget is disabled in plugin settings.
		if ( empty( $settings['dashboard_widget'] ) ) {
			return;
		}
		wp_add_dashboard_widget(
			'shivli_dashboard_widget',
			__( 'Login Insights', 'shivora-login-insights' ),
			array( $this, 'render_widget' )
		);
	}

	/**
	 * Render dashboard widget.
	 *
	 * Displays user activity statistics
	 * and recent login activity.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_widget() {
		$total_users     = count_users();
		$logged_today    = SHIVLI_Helper::get_logged_in_today_count();
		$never_logged_in = SHIVLI_Helper::get_never_logged_in_count();
		$inactive_30     = SHIVLI_Helper::get_inactive_users_count( 30 );

		$recent_users = get_users(
			array(
				'meta_key' => 'shivora_login_insights',
				'orderby'  => 'meta_value_num',
				'order'    => 'DESC',
				'number'   => 5,
			)
		); ?>

		<table class="widefat striped">
			<tbody>
				<tr>
					<th>
						<?php esc_html_e( 'Total Users', 'shivora-login-insights' ); ?>
					</th>
					<td>
						<?php echo esc_html( $total_users['total_users'] ); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Logged In Today', 'shivora-login-insights' ); ?>
					</th>
					<td>
						<?php echo esc_html( $logged_today ); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Never Logged In', 'shivora-login-insights' ); ?>
					</th>
					<td>
						<?php echo esc_html( $never_logged_in ); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Inactive 30 Days', 'shivora-login-insights' ); ?>
					</th>
					<td>
						<?php echo esc_html( $inactive_30 ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php if ( ! empty( $recent_users ) ) : ?>
			<h4>
				<?php esc_html_e( 'Recent Logins', 'shivora-login-insights' ); ?>
			</h4>

			<table class="widefat striped">
				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'User', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Last Login', 'shivora-login-insights' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $recent_users as $user ) : ?>
						<tr>
							<td>
								<?php echo esc_html( $user->display_name ); ?>
							</td>
							<td>
								<?php
								echo esc_html(
									SHIVLI_Helper::format_login_date(
										SHIVLI_Helper::get_last_login(
											$user->ID
										)
									)
								);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<p>
			<a href="<?php echo esc_url( admin_url( 'users.php?page=shivli-overview' ) ); ?>">
				<?php esc_html_e( 'View Full Report', 'shivora-login-insights' ); ?>
			</a>
		</p>
		<?php
	}
}