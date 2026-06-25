<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User filters class.
 *
 * Responsible for:
 * - Adding inactive user filters.
 * - Filtering users by login activity.
 *
 * @since 1.0.0
 */
class SLI_User_Filters {

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
			'restrict_manage_users',
			array( $this, 'add_filters' )
		);
		add_action(
			'pre_get_users',
			array( $this, 'filter_users' )
		);
	}

	/**
	 * Add user filter dropdown.
	 *
	 * Users → All Users
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_filters() {

		global $pagenow;

		if ( 'users.php' !== $pagenow ) {
			return;
		}

		$current_filter = isset( $_GET['sli_inactive'] )
			? sanitize_text_field( wp_unslash( $_GET['sli_inactive'] ) )
			: ''; ?>

		<select name="sli_inactive">
			<option value="">
				<?php esc_html_e('All Users', 'shivora-login-insights'); ?>
			</option>

			<option	value="30" <?php selected( $current_filter, '30' ); ?>>
				<?php esc_html_e('Inactive 30 Days',	'shivora-login-insights'); ?>
			</option>

			<option	value="60" <?php selected( $current_filter, '60' ); ?>>
				<?php esc_html_e('Inactive 60 Days',	'shivora-login-insights'); ?>
			</option>

			<option	value="90" <?php selected( $current_filter, '90' ); ?>>
				<?php esc_html_e('Inactive 90 Days',	'shivora-login-insights'); ?>
			</option>

			<option	value="never" <?php selected( $current_filter, 'never' ); ?>>
				<?php esc_html_e('Never Logged In', 'shivora-login-insights'); ?>
			</option>
		</select>
		<?php
	}

	/**
	 * Filter users.
	 *
	 * Applies selected inactivity filter.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User_Query $query User query.
	 *
	 * @return void
	 */
	public function filter_users( $query ) {

		global $pagenow;

		if ( ! is_admin() ) {
			return;
		}

		if ( 'users.php' !== $pagenow ) {
			return;
		}

		if ( empty( $_GET['sli_inactive'] ) ) {
			return;
		}

		$inactive_days = sanitize_text_field(
			wp_unslash(
				$_GET['sli_inactive']
			)
		);

		/**
		 * Show users who never logged in.
		 */
		if ( 'never' === $inactive_days ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'     => 'shivora_login_insights',
						'compare' => 'NOT EXISTS',
					),
				)
			);
			return;
		}

		$inactive_days = absint(
			$inactive_days
		);

		if ( empty( $inactive_days ) ) {
			return;
		}

		/**
		 * Calculate inactivity threshold.
		 */
		$timestamp = strtotime(
			sprintf(
				'-%d days',
				$inactive_days
			)
		);

		$query->set(
			'meta_key',
			'shivora_login_insights'
		);

		$query->set(
			'meta_value',
			$timestamp
		);

		$query->set(
			'meta_compare',
			'<'
		);
	}
}