<?php
/**
 * Login tracker used to record user login time and IP address.
 *
 * @package Shivora_Login_Insights
 */

// Exit if accessed directly, outside of the WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login tracker class used to track user login.
 *
 * Responsible for:
 * - Tracking user login time.
 * - Tracking user login IP.
 * - Updating user meta.
 *
 * @since 1.0.0
 */
class SHIVLI_Login_Tracker {

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

		// Bail out if wp_login fired without a valid user object.
		if ( ! $user instanceof WP_User ) {
			return;
		}

		/**
		 * Save login timestamp.
		 *
		 * Stored as a true Unix (UTC) timestamp so it always compares
		 * correctly against strtotime()-based inactivity calculations,
		 * regardless of the site's UTC offset setting.
		 */
		update_user_meta(
			$user->ID,
			'shivora_login_insights',
			time()
		);

		/**
		 * Check plugin settings before
		 * saving IP address.
		 */
		$settings = SHIVLI_Helper::get_settings();

		if ( empty( $settings['track_ip'] ) ) {
			return;
		}

		/**
		 * Save login IP address.
		 */
		update_user_meta(
			$user->ID,
			'shivli_last_login_ip',
			SHIVLI_Helper::get_user_ip()
		);

		/**
		 * Allow developers to hook into
		 * successful login tracking.
		 */
		do_action(
			'shivli_after_track_login',
			$user->ID,
			$user
		);
	}
}
