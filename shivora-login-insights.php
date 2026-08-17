<?php
/**
 * Plugin Name: Shivora Login Insights
 * Plugin URI: https://wordpress.org/plugins/shivora-login-insights/
 * Description: Track user last login date, login IP address, inactive users and activity reports.
 * Version: 1.1.0
 * Author: Rita Kikani
 * License: GPL v2 or later
 * Text Domain: shivora-login-insights
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class SHIVLI_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var self|null
	 */
	private static $_instance = null;

	/**
	 * Login tracker module.
	 *
	 * @var SHIVLI_Login_Tracker
	 */
	public $login_tracker;

	/**
	 * User columns module.
	 *
	 * @var SHIVLI_User_Columns
	 */
	public $user_columns;

	/**
	 * User profile module.
	 *
	 * @var SHIVLI_User_Profile
	 */
	public $user_profile;

	/**
	 * User filters module.
	 *
	 * @var SHIVLI_User_Filters
	 */
	public $user_filters;

	/**
	 * REST controller module.
	 *
	 * @var SHIVLI_REST_Controller
	 */
	public $rest_controller;

	/**
	 * Admin module.
	 *
	 * @var SHIVLI_Admin
	 */
	public $admin;

	/**
	 * Get plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->define_constants();

		$this->includes();

		$this->init_classes();

		$this->hooks();
	}

	/**
	 * Register plugin hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function hooks() {

		register_activation_hook(
			__FILE__,
			array( $this, 'activate' )
		);

		add_action(
			'plugins_loaded',
			array( $this, 'maybe_upgrade' )
		);

		add_action(
			'shivli_cleanup_login_history',
			array( 'SHIVLI_Login_History', 'run_cleanup' )
		);
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function define_constants() {

		if ( ! defined( 'SHIVLI_VERSION' ) ) {
			define( 'SHIVLI_VERSION', '1.1.0' );
		}

		if ( ! defined( 'SHIVLI_PLUGIN_FILE' ) ) {
			define( 'SHIVLI_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'SHIVLI_PLUGIN_DIR' ) ) {
			define(
				'SHIVLI_PLUGIN_DIR',
				untrailingslashit(
					plugin_dir_path( __FILE__ )
				)
			);
		}

		if ( ! defined( 'SHIVLI_PLUGIN_URL' ) ) {
			define(
				'SHIVLI_PLUGIN_URL',
				untrailingslashit(
					plugin_dir_url( __FILE__ )
				)
			);
		}
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {

		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-activator.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-helper.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-login-tracker.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-login-history.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-user-columns.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-user-profile.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-user-filters.php';
		require_once SHIVLI_PLUGIN_DIR . '/includes/shivli-rest-controller.php';
		
		if ( is_admin() ) {
			require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-admin.php';
		}
	}

	/**
	 * Initialize plugin classes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_classes() {

		$this->login_tracker = new SHIVLI_Login_Tracker();
		$this->user_columns = new SHIVLI_User_Columns();
		$this->user_profile = new SHIVLI_User_Profile();
		$this->user_filters = new SHIVLI_User_Filters();
		$this->rest_controller = new SHIVLI_REST_Controller();

		if ( is_admin() ) {
			$this->admin = new SHIVLI_Admin();
		}
	}

	/**
	 * Plugin activation callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate() {

		SHIVLI_Activator::activate();

		if ( ! wp_next_scheduled( 'shivli_cleanup_login_history' ) ) {

			wp_schedule_event( time(), 'daily', 'shivli_cleanup_login_history' );
		}
	}

	/**
	 * Run database/plugin upgrades.
	 *
	 * This runs when an existing installation is updated
	 * from an older version to the current version.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function maybe_upgrade() {

		$installed_version = get_option(
			'shivli_version',
			'1.0.0'
		);

		if ( version_compare(
			$installed_version,
			SHIVLI_VERSION,
			'<'
		) ) {

			SHIVLI_Activator::activate();
			/*
			* Schedule login history cleanup for existing installations.
			*/
			if ( ! wp_next_scheduled( 'shivli_cleanup_login_history' ) ) {
				wp_schedule_event( time(), 'daily', 'shivli_cleanup_login_history' );
			}
			update_option( 'shivli_version', SHIVLI_VERSION );
		}
	}
}

/**
 * Main plugin instance.
 *
 * @since 1.0.0
 *
 * @return SHIVLI_Plugin
 */
function SHIVLI() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return SHIVLI_Plugin::instance();
}

/**
 * Global plugin object.
 */
$GLOBALS['shivli'] = SHIVLI();