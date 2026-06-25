<?php
/**
 * Inactive Users Page.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inactive users page class.
 *
 * Responsible for:
 * - Registering inactive users submenu.
 * - Listing inactive users.
 * - Filtering users by inactivity period.
 *
 * @since 1.0.0
 */
class SLI_Inactive_Users_Page {

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
				'register_submenu',
			),
			25
		);
	}

	/**
	 * Register submenu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_submenu() {

		add_submenu_page(
			'sli-overview',
			__( 'Inactive Users', 'last-login-tracker' ),
			__( 'Inactive Users', 'last-login-tracker' ),
			'list_users',
			'sli-inactive-users',
			array(
				$this,
				'render_page',
			)
		);
	}

	/**
	 * Render page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_page() {

		$days = isset( $_GET['days'] )
			? absint( $_GET['days'] )
			: 30;

		$timestamp = strtotime(
			sprintf(
				'-%d days',
				$days
			)
		);

		$users = get_users(
			array(
				'meta_key'     => 'wpll_last_login',
				'meta_value'   => $timestamp,
				'meta_compare' => '<',
				'orderby'      => 'meta_value_num',
				'order'        => 'ASC',
			)
		);

		?>

		<div class="wrap">

			<h1>
				<?php esc_html_e(
					'Inactive Users',
					'last-login-tracker'
				); ?>
			</h1>

			<form method="get">

				<input
					type="hidden"
					name="page"
					value="sli-inactive-users"
				/>

				<select name="days">

					<option value="30" <?php selected( $days, 30 ); ?>>
						30 Days
					</option>

					<option value="60" <?php selected( $days, 60 ); ?>>
						60 Days
					</option>

					<option value="90" <?php selected( $days, 90 ); ?>>
						90 Days
					</option>

					<option value="180" <?php selected( $days, 180 ); ?>>
						180 Days
					</option>

				</select>

				<?php submit_button(
					__( 'Filter', 'last-login-tracker' ),
					'secondary',
					'',
					false
				); ?>

			</form>

			<br>

			<table class="widefat striped">

				<thead>

					<tr>

						<th><?php esc_html_e( 'User', 'last-login-tracker' ); ?></th>

						<th><?php esc_html_e( 'Email', 'last-login-tracker' ); ?></th>

						<th><?php esc_html_e( 'Last Login', 'last-login-tracker' ); ?></th>

						<th><?php esc_html_e( 'Login IP', 'last-login-tracker' ); ?></th>

					</tr>

				</thead>

				<tbody>

					<?php if ( empty( $users ) ) : ?>

						<tr>

							<td colspan="4">

								<?php esc_html_e(
									'No inactive users found.',
									'last-login-tracker'
								); ?>

							</td>

						</tr>

					<?php else : ?>

						<?php foreach ( $users as $user ) : ?>

							<tr>

								<td>

									<a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>">

										<?php echo esc_html( $user->display_name ); ?>

									</a>

								</td>

								<td>

									<?php echo esc_html( $user->user_email ); ?>

								</td>

								<td>

									<?php echo esc_html(
										SLI_Helper::format_login_date(
											SLI_Helper::get_last_login(
												$user->ID
											)
										)
									); ?>

								</td>

								<td>

									<?php echo esc_html(
										SLI_Helper::get_last_login_ip(
											$user->ID
										)
									); ?>

								</td>

							</tr>

						<?php endforeach; ?>

					<?php endif; ?>

				</tbody>

			</table>

		</div>

		<?php
	}
}