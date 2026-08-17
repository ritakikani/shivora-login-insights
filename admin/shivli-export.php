<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Export functionality.
 *
 * @since 1.0.0
 */
class SHIVLI_Export {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action(
			'admin_post_shivli_export_users',
			array( $this, 'export_users' )
		);

		add_action(
			'admin_post_shivli_export_login_history',
			array( $this, 'export_login_history' )
		);
	}

	/**
	 * Export login history CSV.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function export_login_history() {

		if ( ! current_user_can( 'list_users' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to export login history.',
					'shivora-login-insights'
				)
			);
		}

		check_admin_referer( 'shivli_export_login_history' );

		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		$device = isset( $_GET['device'] ) ? sanitize_text_field( wp_unslash( $_GET['device'] ) ) : '';

		$browser = isset( $_GET['browser'] ) ? sanitize_text_field( wp_unslash( $_GET['browser'] ) ) : '';

		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';

		$date_to = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';

		$history = SHIVLI_Login_History::get_history(
			array(
				'search'    => $search,
				'device'    => $device,
				'browser'   => $browser,
				'date_from' => $date_from,
				'date_to'   => $date_to,
				'limit'     => 100000,
				'offset'    => 0,
			)
		);

		nocache_headers();

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=shivora-login-history-' . gmdate( 'Y-m-d' ) . '.csv' );

		$output = fopen( 'php://output', 'w' );

		fputcsv(
			$output,
			array(
				__( 'User ID', 'shivora-login-insights' ),
				__( 'Username', 'shivora-login-insights' ),
				__( 'Email', 'shivora-login-insights' ),
				__( 'Login Date', 'shivora-login-insights' ),
				__( 'IP Address', 'shivora-login-insights' ),
				__( 'Browser', 'shivora-login-insights' ),
				__( 'Operating System', 'shivora-login-insights' ),
				__( 'Device', 'shivora-login-insights' ),
				__( 'User Agent', 'shivora-login-insights' ),
			)
		);

		foreach ( $history as $login ) {

			$user = get_user_by( 'id', absint( $login->user_id ) );

			fputcsv(
				$output,
				array(
					$login->user_id,
					$user ? $user->user_login : '',
					$user ? $user->user_email : '',
					$login->login_time,
					$login->ip_address,
					$login->browser,
					$login->os,
					$login->device,
					$login->user_agent,
				)
			);
		}

		fclose( $output );

		exit;
	}
}