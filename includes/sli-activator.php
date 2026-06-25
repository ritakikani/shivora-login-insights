<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin activation handler.
 *
 * Responsible for:
 * - Creating default plugin settings.
 * - Running first-time setup tasks.
 *
 * @since 1.0.0
 */
class SLI_Activator {

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
		if ( false === get_option( 'sli_settings' ) ) {
			add_option(
				'sli_settings',
				array(
					'track_ip'         => 1,
					'dashboard_widget' => 1,
					'retention'        => 'forever',
				)
			);
		}

		/**
		 * Store current plugin version.
		 *
		 * Useful for future upgrade routines.
		 */
		update_option(
			'sli_version',
			SLI_VERSION
		);

		/**
		 * Trigger custom activation hook.
		 *
		 * Allows future modules or add-ons
		 * to perform their own activation tasks.
		 */
		do_action( 'sli_activate' );

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
}