<?php
// Exit if accessed directly, outside of the WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper class.
 *
 * Central place for reusable methods used across
 * multiple plugin modules.
 *
 * @since 1.0.0
 */
class SHIVLI_Helper {

	/**
	 * Get plugin settings.
	 *
	 * Returns plugin settings merged with defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_settings() {
		$defaults = array(
			'track_ip'         => 1,
			'dashboard_widget' => 1,
			'retention'        => 'forever',
		);
		$settings = get_option(
			'shivli_settings',
			array()
		);
		return wp_parse_args(
			$settings,
			$defaults
		);
	}

	/**
	 * Get last login timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return int
	 */
	public static function get_last_login( $user_id ) {
		return (int) get_user_meta(
			$user_id,
			'shivora_login_insights',
			true
		);
	}

	/**
	 * Get last login IP.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string
	 */
	public static function get_last_login_ip( $user_id ) {
		return (string) get_user_meta(
			$user_id,
			'shivli_last_login_ip',
			true
		);
	}

	/**
	 * Format login date.
	 *
	 * Converts unix timestamp into site date format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $timestamp Login timestamp.
	 *
	 * @return string
	 */
	public static function format_login_date( $timestamp ) {
		// No login has been recorded for this user yet.
		if ( empty( $timestamp ) ) {
			return __( 'Never', 'shivora-login-insights' );
		}
		return wp_date(
			get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			$timestamp
		);
	}

	/**
	 * Get logged in users count today.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public static function get_logged_in_today_count() {
		$today_start = strtotime(
			'today',
			current_time( 'timestamp' )
		);
		$users       = get_users(
			array(
				'meta_key'     => 'shivora_login_insights',
				'meta_value'   => $today_start,
				'meta_compare' => '>=',
				'fields'       => 'ID',
			)
		);
		return count( $users );
	}

	/**
	 * Get never logged-in users count.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public static function get_never_logged_in_count() {
		$users = get_users(
			array(
				'meta_query' => array(
					array(
						'key'     => 'shivora_login_insights',
						'compare' => 'NOT EXISTS',
					),
				),
				'fields'     => 'ID',
			)
		);
		return count( $users );
	}

	/**
	 * Get inactive users count.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days Number of inactive days.
	 *
	 * @return int
	 */
	public static function get_inactive_users_count( $days = 30 ) {
		$timestamp = strtotime(
			sprintf(
				'-%d days',
				absint( $days )
			)
		);
		$users     = get_users(
			array(
				'meta_key'     => 'shivora_login_insights',
				'meta_value'   => $timestamp,
				'meta_compare' => '<',
				'fields'       => 'ID',
			)
		);
		return count( $users );
	}

	/**
	 * Get current user IP.
	 *
	 * Trusts only REMOTE_ADDR by default, since proxy headers
	 * such as X-Forwarded-For or Client-IP are supplied by the
	 * client and can be spoofed by anyone to falsify the login
	 * IP recorded for audit purposes. Sites that sit behind a
	 * known, trusted proxy or CDN (which overwrites REMOTE_ADDR
	 * with the proxy's own address) can opt in to a specific
	 * header via the `shivli_trusted_ip_header` filter.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_user_ip() {

		$header = apply_filters(
			'shivli_trusted_ip_header',
			'REMOTE_ADDR'
		);

		// Trusted header is not present on this request.
		if ( empty( $_SERVER[ $header ] ) ) {
			return '';
		}

		$ip = explode(
			',',
			sanitize_text_field(
				wp_unslash(
					$_SERVER[ $header ]
				)
			)
		);

		return trim( $ip[0] );
	}
}
