<?php
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
class SLI_Helper {

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
			'sli_settings',
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
			'sli_last_login_ip',
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
		if ( empty( $timestamp ) ) {
			return __( 'Never', 'shivora-login-insight' );
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
		$users = get_users(
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
				'fields' => 'ID',
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
		$users = get_users(
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
	 * Supports common proxy headers.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_user_ip() {
		$ip_headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR',
		);

		foreach ( $ip_headers as $header ) {
			if ( empty( $_SERVER[ $header ] ) ) {
				continue;
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
		return '';
	}
}