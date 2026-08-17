<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu class.
 *
 * Responsible for:
 * - Creating plugin top-level menu.
 * - Creating dashboard page.
 * - Loading admin styles.
 *
 * @since 1.0.0
 */
class SHIVLI_Admin_Menu {

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

		add_action(
			'admin_enqueue_scripts',
			array(
				$this,
				'enqueue_styles',
			)
		);
	}

	/**
	 * Register plugin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_menu() {

		add_menu_page(
			__( 'Login Insights', 'shivora-login-insights' ),
			__( 'Login Insights', 'shivora-login-insights' ),
			'list_users',
			'shivli-overview',
			array(
				$this,
				'overview_page',
			),
			'dashicons-clock',
			58
		);
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @since 1.1.0
	 *
	 * @param string $hook_suffix Current admin page.
	 *
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shivli' ) ) {
			return;
		}

		wp_enqueue_style(
			'shivli-admin',
			trailingslashit( SHIVLI_PLUGIN_URL ) . 'admin/css/shivli-admin.css',
			array(),
			SHIVLI_VERSION
		);
	}

	/**
	 * Dashboard page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function overview_page() {

		$total_users = count_users();

		$logged_today = SHIVLI_Helper::get_logged_in_today_count();

		$last_7_days = SHIVLI_Login_History::get_last_7_days_login_counts();

		$never_logged_in = SHIVLI_Helper::get_never_logged_in_count();

		$inactive_30 = SHIVLI_Helper::get_inactive_users_count( 30 );

		$inactive_60 = SHIVLI_Helper::get_inactive_users_count( 60 );

		$inactive_90 = SHIVLI_Helper::get_inactive_users_count( 90 );

		$total_logins = SHIVLI_Login_History::get_total_login_count();

		$today_logins = SHIVLI_Login_History::get_today_login_count();

		$unique_ips = SHIVLI_Login_History::get_unique_ip_count();

		$desktop_logins = SHIVLI_Login_History::get_device_login_count( 'Desktop' );

		$mobile_logins = SHIVLI_Login_History::get_device_login_count( 'Mobile' );

		$tablet_logins = SHIVLI_Login_History::get_device_login_count( 'Tablet' );
		?>

		<div class="wrap">

			<h1>
				<?php esc_html_e( 'Login Insights', 'shivora-login-insights' ); ?>
			</h1>

			<div class="shivli-dashboard-cards">

				<!-- Total Users -->
				<div class="card shivli-stat-card">

					<h2>
						<?php esc_html_e( 'Total Users', 'shivora-login-insights' ); ?>
					</h2>

					<p class="shivli-stat-value">
						<?php echo esc_html( $total_users['total_users'] ); ?>
					</p>

				</div>

				<!-- Total Logins -->
				<div class="card shivli-stat-card">

					<h2>
						<?php esc_html_e( 'Total Logins', 'shivora-login-insights' ); ?>
					</h2>

					<p class="shivli-stat-value">
						<?php echo esc_html( $total_logins ); ?>
					</p>

				</div>

				<!-- Today's Logins -->
				<div class="card shivli-stat-card">

					<h2>
						<?php esc_html_e( "Today's Logins", 'shivora-login-insights' ); ?>
					</h2>

					<p class="shivli-stat-value">
						<?php echo esc_html( $today_logins ); ?>
					</p>

				</div>

				<!-- Unique IPs -->
				<div class="card shivli-stat-card">

					<h2>
						<?php
						esc_html_e( 'Unique IP Addresses', 'shivora-login-insights' ); ?>
					</h2>

					<p class="shivli-stat-value">
						<?php echo esc_html( $unique_ips ); ?>
					</p>

				</div>

			</div>

			<div class="shivli-dashboard-section">

				<h2>
					<?php esc_html_e( 'Login Activity', 'shivora-login-insights' ); ?>
				</h2>

				<table class="widefat striped">

					<thead>
						<tr>

							<th>
								<?php esc_html_e( 'Metric', 'shivora-login-insights' ); ?>
							</th>

							<th>
								<?php esc_html_e( 'Count', 'shivora-login-insights' ); ?>
							</th>

						</tr>
					</thead>

					<tbody>

						<tr>
							<td>
								<?php esc_html_e( 'Logged In Today', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $logged_today ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php esc_html_e(	'Never Logged In', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $never_logged_in ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php
								esc_html_e( 'Inactive 30 Days', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $inactive_30 ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php esc_html_e( 'Inactive 60 Days', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $inactive_60 ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php esc_html_e( 'Inactive 90 Days', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $inactive_90 ); ?>
							</td>
						</tr>

					</tbody>

				</table>

			</div>

			<div class="shivli-dashboard-section">

				<h2>
					<?php esc_html_e( 'Login Activity - Last 7 Days', 'shivora-login-insights' ); ?>
				</h2>

				<div class="shivli-login-chart">

					<?php foreach ( $last_7_days as $day ) : ?>

						<?php $height = $day['count'] > 0 ? max( 20, min( 180, $day['count'] * 20 ) ) : 4;

						$label = wp_date(
							'M d',
							strtotime( $day['date'] )
						); ?>

						<div class="shivli-chart-column">

							<span class="shivli-chart-count">
								<?php echo esc_html( $day['count'] ); ?>
							</span>

							<div
								class="shivli-chart-bar"
								style="height: <?php echo esc_attr( $height ); ?>px;"
							></div>

							<span class="shivli-chart-label">
								<?php echo esc_html( $label ); ?>
							</span>

						</div>

					<?php endforeach; ?>

				</div>

			</div>

			<div class="shivli-dashboard-section">

				<h2>
					<?php esc_html_e( 'Device Activity', 'shivora-login-insights' ); ?>
				</h2>

				<table class="widefat striped">

					<thead>
						<tr>

							<th>
								<?php esc_html_e( 'Device', 'shivora-login-insights' ); ?>
							</th>

							<th>
								<?php esc_html_e( 'Login Count', 'shivora-login-insights' ); ?>
							</th>

						</tr>
					</thead>

					<tbody>

						<tr>
							<td>
								<?php esc_html_e( 'Desktop', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $desktop_logins ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php
								esc_html_e( 'Mobile', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php echo esc_html( $mobile_logins ); ?>
							</td>
						</tr>

						<tr>
							<td>
								<?php esc_html_e( 'Tablet', 'shivora-login-insights' ); ?>
							</td>

							<td>
								<?php
								echo esc_html( $tablet_logins ); ?>
							</td>
						</tr>

					</tbody>

				</table>

			</div>

		</div>

		<?php
	}
}