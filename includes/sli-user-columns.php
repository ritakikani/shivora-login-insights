<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User columns class.
 *
 * Responsible for:
 * - Adding custom user columns.
 * - Rendering column values.
 * - Making columns sortable.
 * - Handling column sorting.
 *
 * @since 1.0.0
 */
class SLI_User_Columns {

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
		add_filter(
			'manage_users_columns',
			array( $this, 'add_columns' )
		);
		add_filter(
			'manage_users_custom_column',
			array( $this, 'render_columns' ),
			10,
			3
		);
		add_filter(
			'manage_users_sortable_columns',
			array( $this, 'sortable_columns' )
		);
		add_action(
			'pre_get_users',
			array( $this, 'sort_users' )
		);
	}

	/**
	 * Add custom user columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function add_columns( $columns ) {
		$columns['shivora_login_insights'] = __(
			'Last Login',
			'shivora-login-insights'
		);
		$settings = SLI_Helper::get_settings();
		if ( ! empty( $settings['track_ip'] ) ) {
			$columns['sli_last_login_ip'] = __(
				'Login IP',
				'shivora-login-insights'
			);
		}
		return $columns;
	}

	/**
	 * Render custom column values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value       Column value.
	 * @param string $column_name Column name.
	 * @param int    $user_id     User ID.
	 *
	 * @return string
	 */
	public function render_columns($value,	$column_name, $user_id) {
		switch ( $column_name ) {
			case 'shivora_login_insights':
				$timestamp = SLI_Helper::get_last_login(
					$user_id
				);
				return esc_html(
					SLI_Helper::format_login_date(
						$timestamp
					)
				);
			case 'sli_last_login_ip':
				$ip = SLI_Helper::get_last_login_ip(
					$user_id
				);
				return ! empty( $ip )
					? esc_html( $ip )
					: '&mdash;';
		}
		return $value;
	}

	/**
	 * Register sortable columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Sortable columns.
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['shivora_login_insights'] = 'shivora_login_insights';
		return $columns;
	}

	/**
	 * Handle sorting by last login.
	 *
	 * When administrator clicks the
	 * Last Login column header.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User_Query $query User query.
	 *
	 * @return void
	 */
	public function sort_users( $query ) {
		global $pagenow;
		if ( ! is_admin() ) {
			return;
		}
		if ( 'users.php' !== $pagenow ) {
			return;
		}
		$order_by = $query->get( 'orderby' );
		if ( 'shivora_login_insights' !== $order_by ) {
			return;
		}
		$query->set(
			'meta_key',
			'shivora_login_insights'
		);
		$query->set(
			'orderby',
			'meta_value_num'
		);
	}
}