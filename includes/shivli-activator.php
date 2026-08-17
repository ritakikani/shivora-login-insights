<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin activation handler.
 *
 * Responsible for:
 * - Creating default plugin settings.
 * - Creating the login history database table.
 * - Running first-time setup tasks.
 *
 * @since 1.0.0
 */
class SHIVLI_Activator {

	/**
	 * Run plugin activation tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate() {

		/**
		 * Create default plugin settings
		 * only if they don't already exist.
		 */
		if ( false === get_option( 'shivli_settings' ) ) {
			add_option(
				'shivli_settings',
				array(
					'track_ip'         => 1,
					'dashboard_widget' => 1,
					'retention'        => 'forever',
				)
			);
		}

		/**
		 * Create login history table.
		 *
		 * The table is created only if it does not already exist.
		 *
		 * Login history will be stored separately from user meta
		 * so the plugin can support multiple login records per user.
		 */
		self::create_login_history_table();

		/**
		 * Store current plugin version.
		 *
		 * Useful for future upgrade routines.
		 */
		update_option(
			'shivli_version',
			SHIVLI_VERSION
		);

		/**
		 * Trigger custom activation hook.
		 *
		 * Allows future modules or add-ons
		 * to perform their own activation tasks.
		 */
		do_action( 'shivli_activate' );

		/**
		 * Flush rewrite rules.
		 *
		 * Not required right now because
		 * we don't register custom post types
		 * or rewrite endpoints yet.
		 *
		 * Keeping it here for future expansion.
		 */
		flush_rewrite_rules();
	}

	/**
	 * Create the login history database table.
	 *
	 * This table stores one record for every successful login.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	private static function create_login_history_table() {

		global $wpdb;

		$table_name      = $wpdb->prefix . 'shivli_login_history';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			login_time datetime NOT NULL,
			ip_address varchar(45) DEFAULT NULL,
			browser varchar(100) DEFAULT NULL,
			os varchar(100) DEFAULT NULL,
			device varchar(50) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY login_time (login_time),
			KEY ip_address (ip_address)
		) {$charset_collate};";

		/**
		 * dbDelta() is required for WordPress database
		 * table creation and future schema updates.
		 */
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}
}