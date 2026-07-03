<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User profile class.
 *
 * Responsible for displaying login
 * information on user profile screens.
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
			'show_user_profile',
			array( $this, 'display_login_information' )
		);

		add_action(
			'edit_user_profile',
			array( $this, 'display_login_information' )
		);
	}

	/**
	 * Display login information.
	 *
	 * Adds a read-only section to
	 * the user profile page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user User object.
	 *
	 * @return void
	 */
	public function display_login_information( $user ) {

		$last_login = SHIVLI_Helper::get_last_login(
			$user->ID
		);

		$last_login_ip = SHIVLI_Helper::get_last_login_ip(
			$user->ID
		);

		$days_since_login = '';

		if ( ! empty( $last_login ) ) {

			$days_since_login = floor(
				( current_time( 'timestamp' ) - $last_login ) / DAY_IN_SECONDS
			);
		} ?>

		<h2>
			<?php esc_html_e('Last Login Information', 'shivora-login-insights'); ?>
		</h2>

		<table class="form-table" role="presentation">
			<tr>
				<th>
					<label>
						<?php esc_html_e('Last Login', 'shivora-login-insights' ); ?>
					</label>
				</th>
				<td>
					<?php echo esc_html(
						SHIVLI_Helper::format_login_date(
							$last_login
						)
					);?>
				</td>
			</tr>

			<?php if ( ! empty( $last_login_ip ) ) : ?>
				<tr>
					<th>
						<label>
							<?php esc_html_e('Last Login IP', 'shivora-login-insights'); ?>
						</label>
					</th>
					<td>
						<?php echo esc_html( $last_login_ip ); ?>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
				<th>
					<label>
						<?php esc_html_e('Days Since Last Login', 'shivora-login-insights'); ?>
					</label>
				</th>
				<td>
					<?php if ( '' === $days_since_login ) {
						esc_html_e(
							'Never Logged In',
							'shivora-login-insights'
						);
					} else {
						echo esc_html(
							$days_since_login
						);
					} ?>
				</td>
			</tr>
		</table>
		<?php
	}
}