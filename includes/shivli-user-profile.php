<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User profile integration.
 *
 * @since 1.0.0
 */
class SHIVLI_User_Profile {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action(
			'show_user_profile',
			array( $this, 'render_login_insights' )
		);

		add_action(
			'edit_user_profile',
			array( $this, 'render_login_insights' )
		);
	}

	/**
	 * Render login insights on user profile.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user User object.
	 *
	 * @return void
	 */
	public function render_login_insights( $user ) {

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$last_login = get_user_meta( $user->ID, 'shivora_login_insights', true );

		$last_ip = get_user_meta( $user->ID, 'shivli_last_login_ip', true );

		$login_count = 0;

		if ( class_exists( 'SHIVLI_Login_History' ) ) {
			$login_count = SHIVLI_Login_History::get_login_count( $user->ID );

			$history = SHIVLI_Login_History::get_user_history( $user->ID, 10 );
		} else {
			$history = array();
		} ?>
		<h2>
			<?php esc_html_e( 'Shivora Login Insights', 'shivora-login-insights' ); ?>
		</h2>

		<table class="form-table" role="presentation">
			<tr>
				<th>
					<label>
						<?php esc_html_e( 'Last Login', 'shivora-login-insights' ); ?>
					</label>
				</th>
				<td>
					<?php
					if ( $last_login ) {
						echo esc_html(
							wp_date(
								get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
								absint( $last_login ))
						);
					} else {
						esc_html_e( 'Never', 'shivora-login-insights' );
					} ?>
				</td>
			</tr>

			<tr>
				<th>
					<label>
						<?php esc_html_e( 'Last Login IP', 'shivora-login-insights' ); ?>
					</label>
				</th>
				<td>
					<?php echo $last_ip ? esc_html( $last_ip ) : esc_html__( 'Not available', 'shivora-login-insights' ); ?>
				</td>
			</tr>

			<tr>
				<th>
					<label>
						<?php esc_html_e( 'Total Logins', 'shivora-login-insights' ); ?>
					</label>
				</th>
				<td>
					<strong>
						<?php echo esc_html( number_format_i18n( $login_count ) ); ?>
					</strong>
				</td>
			</tr>
		</table>

		<?php if ( ! empty( $history ) ) : ?>

			<h3>
				<?php esc_html_e( 'Recent Login History', 'shivora-login-insights' ); ?>
			</h3>

			<table class="widefat striped" style="max-width: 900px;">
				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'Date & Time', 'shivora-login-insights' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'IP Address', 'shivora-login-insights' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Browser', 'shivora-login-insights' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Operating System', 'shivora-login-insights' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Device', 'shivora-login-insights' ); ?>
						</th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ( $history as $login ) : ?>
						<tr>
							<td>
								<?php
								echo esc_html(
									wp_date(
										get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
										strtotime( $login->login_time )
									)
								); ?>
							</td>

							<td>
								<?php echo $login->ip_address ? esc_html( $login->ip_address ) : '-'; ?>
							</td>

							<td>
								<?php echo $login->browser ? esc_html( $login->browser ) : '-'; ?>
							</td>

							<td>
								<?php echo $login->os ? esc_html( $login->os ) : '-'; ?>
							</td>

							<td>
								<?php echo $login->device ? esc_html( $login->device ) : '-'; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php endif;
	}
}