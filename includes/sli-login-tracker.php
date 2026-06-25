<?php
/**
 * Login Tracker.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login tracker class.
 *
 * Responsible for:
 * - Tracking user login time.
 * - Tracking user login IP.
 * - Updating user meta.
 *
 * @since 1.0.0
 */
class SLI_Login_Tracker {

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
	 * Keeping hooks in one method makes
	 * future maintenance easier.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function hooks() {

		add_action(
			'wp_login',
			array( $this, 'track_login' ),
			10,
			2
		);
	}

	/**
	 * Track user login.
	 *
	 * Stores:
	 * - Last login timestamp.
	 * - Last login IP address.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user       User object.
	 *
	 * @return void
	 */
	public function track_login( $user_login, $user ) {

		if ( ! $user instanceof WP_User ) {
			return;
		}

		/**
		 * Save login timestamp.
		 */
		update_user_meta(
			$user->ID,
			'wpll_last_login',
			current_time( 'timestamp' )
		);

		/**
		 * Check plugin settings before
		 * saving IP address.
		 */
		$settings = SLI_Helper::get_settings();

		if ( empty( $settings['track_ip'] ) ) {
			return;
		}

		/**
		 * Save login IP address.
		 */
		update_user_meta(
			$user->ID,
			'wpll_last_login_ip',
			SLI_Helper::get_user_ip()
		);

		/**
		 * Allow developers to hook into
		 * successful login tracking.
		 */
		do_action(
			'wpll_after_track_login',
			$user->ID,
			$user
		);
	}
}