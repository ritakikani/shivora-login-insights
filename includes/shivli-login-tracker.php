<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login tracker class used to track user login.
 *
 * Responsible for:
 * - Tracking user login time.
 * - Tracking user login IP.
 * - Saving login history.
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
	 * - Login history record.
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
		 * Get current login timestamp.
		 */
		$login_timestamp = current_time( 'timestamp' );

		/**
		 * Get current login IP.
		 */
		$login_ip = SHIVLI_Helper::get_user_ip();

		/**
		 * Save last login timestamp.
		 *
		 * Existing v1.0 functionality is preserved.
		 */
		update_user_meta( $user->ID, 'shivora_login_insights', $login_timestamp );

		/**
		 * Check plugin settings.
		 */
		$settings = SHIVLI_Helper::get_settings();

		/**
		 * Save last login IP when enabled.
		 *
		 * Existing v1.0 functionality is preserved.
		 */
		if ( ! empty( $settings['track_ip'] ) ) {
			update_user_meta( $user->ID, 'shivli_last_login_ip', $login_ip );
		}

		/**
		 * Save login history.
		 *
		 * Login history is stored independently from
		 * the existing last-login user meta.
		 */
		$this->save_login_history( $user, $login_timestamp, $login_ip, $settings );

		/**
		 * Allow developers to hook into
		 * successful login tracking.
		 */
		do_action( 'shivli_after_track_login', $user->ID, $user );
	}

	/**
	 * Save login history record.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_User $user            User object.
	 * @param int     $login_timestamp Login timestamp.
	 * @param string  $login_ip        Login IP address.
	 * @param array   $settings        Plugin settings.
	 *
	 * @return void
	 */
	private function save_login_history( $user, $login_timestamp, $login_ip, $settings ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'shivli_login_history';

		/**
		 * Make sure the history table exists.
		 */
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( $table_name !== $table_exists ) {
			return;
		}

		/**
		 * Detect login environment.
		 */
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		$browser = $this->detect_browser( $user_agent );
		$os      = $this->detect_os( $user_agent );
		$device  = $this->detect_device( $user_agent );

		/**
		 * Convert local timestamp to MySQL datetime.
		 */
		$login_time = wp_date( 'Y-m-d H:i:s', $login_timestamp );

		/**
		 * Respect the Track Login IP setting.
		 */
		if ( empty( $settings['track_ip'] ) ) {
			$login_ip = '';
		}

		/**
		 * Insert login history record.
		 */
		$wpdb->insert(
			$table_name,
			array(
				'user_id'    => $user->ID,
				'login_time' => $login_time,
				'ip_address' => $login_ip,
				'browser'    => $browser,
				'os'         => $os,
				'device'     => $device,
				'user_agent' => $user_agent,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
	}

	/**
	 * Detect browser from user agent.
	 *
	 * @since 1.1.0
	 *
	 * @param string $user_agent User agent.
	 *
	 * @return string
	 */
	private function detect_browser( $user_agent ) {

		if ( empty( $user_agent ) ) {
			return __( 'Unknown', 'shivora-login-insights' );
		}

		if ( false !== stripos( $user_agent, 'Edg/' ) ) {
			return 'Microsoft Edge';
		}

		if ( false !== stripos( $user_agent, 'OPR/' ) ) {
			return 'Opera';
		}

		if ( false !== stripos( $user_agent, 'Chrome/' ) ) {
			return 'Chrome';
		}

		if ( false !== stripos( $user_agent, 'Firefox/' ) ) {
			return 'Firefox';
		}

		if ( false !== stripos( $user_agent, 'Safari/' ) ) {
			return 'Safari';
		}

		if ( false !== stripos( $user_agent, 'MSIE' ) ) {
			return 'Internet Explorer';
		}

		return __( 'Unknown', 'shivora-login-insights' );
	}

	/**
	 * Detect operating system from user agent.
	 *
	 * @since 1.1.0
	 *
	 * @param string $user_agent User agent.
	 *
	 * @return string
	 */
	private function detect_os( $user_agent ) {

		if ( empty( $user_agent ) ) {
			return __( 'Unknown', 'shivora-login-insights' );
		}

		if ( false !== stripos( $user_agent, 'Windows' ) ) {
			return 'Windows';
		}

		if ( false !== stripos( $user_agent, 'iPhone' ) || false !== stripos( $user_agent, 'iPad' ) || false !== stripos( $user_agent, 'iPod' ) ) {
			return 'iOS';
		}

		if ( false !== stripos( $user_agent, 'Android' ) ) {
			return 'Android';
		}

		if ( false !== stripos( $user_agent, 'Mac OS X' ) || false !== stripos( $user_agent, 'Macintosh' ) ) {
			return 'macOS';
		}

		if ( false !== stripos( $user_agent, 'Linux' ) ) {
			return 'Linux';
		}

		return __( 'Unknown', 'shivora-login-insights' );
	}

	/**
	 * Detect device type from user agent.
	 *
	 * @since 1.1.0
	 *
	 * @param string $user_agent User agent.
	 *
	 * @return string
	 */
	private function detect_device( $user_agent ) {

		if ( empty( $user_agent ) ) {
			return __( 'Unknown', 'shivora-login-insights' );
		}

		if ( false !== stripos( $user_agent, 'iPad' ) || false !== stripos( $user_agent, 'Tablet' ) ) {
			return 'Tablet';
		}

		if ( false !== stripos( $user_agent, 'Mobile' ) || false !== stripos( $user_agent, 'Android' ) || false !== stripos( $user_agent, 'iPhone' ) ) {
			return 'Mobile';
		}

		return 'Desktop';
	}
}