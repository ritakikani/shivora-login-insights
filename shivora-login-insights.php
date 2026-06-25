<?php
/**
 * Plugin Name: Shivora Login Insights
 * Plugin URI: https://wordpress.org/plugins/shivora-login-insights/
 * Description: Track user last login date, login IP address, inactive users and activity reports.
 * Version: 1.0.0
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
 * Responsible for:
 * - Defining plugin constants.
 * - Loading required files.
 * - Initializing plugin modules.
 * - Registering activation hooks.
 *
 * @since 1.0.0
 */
class SLI_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var self|null
	 */
	private static $_instance = null;

	/**
	 * Login tracker module.
	 *
	 * @var SLI_Login_Tracker
	 */
	public $login_tracker;

	/**
	 * User columns module.
	 *
	 * @var SLI_User_Columns
	 */
	public $user_columns;

	/**
	 * User profile module.
	 *
	 * @var SLI_User_Profile
	 */
	public $user_profile;

	/**
	 * User filters module.
	 *
	 * @var SLI_User_Filters
	 */
	public $user_filters;

	/**
	 * REST controller module.
	 *
	 * @var SLI_REST_Controller
	 */
	public $rest_controller;

	/**
	 * Admin module.
	 *
	 * @var SLI_Admin
	 */
	public $admin;

	/**
	 * Get plugin instance.
	 *
	 * Ensures only one instance of the plugin is loaded.
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
	 * Load plugin dependencies and initialize modules.
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
			array( $this, 'load_textdomain' )
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

		if ( ! defined( 'SLI_VERSION' ) ) {
			define( 'SLI_VERSION', '1.0.0' );
		}

		if ( ! defined( 'SLI_PLUGIN_FILE' ) ) {
			define( 'SLI_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'SLI_PLUGIN_DIR' ) ) {
			define(
				'SLI_PLUGIN_DIR',
				untrailingslashit(
					plugin_dir_path( __FILE__ )
				)
			);
		}

		if ( ! defined( 'SLI_PLUGIN_URL' ) ) {
			define(
				'SLI_PLUGIN_URL',
				untrailingslashit(
					plugin_dir_url( __FILE__ )
				)
			);
		}
	}

	/**
	 * Include required files.
	 *
	 * Keeping all includes in a single method
	 * makes maintenance easier.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {

		require_once SLI_PLUGIN_DIR . '/includes/sli-activator.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-helper.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-login-tracker.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-user-columns.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-user-profile.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-user-filters.php';
		require_once SLI_PLUGIN_DIR . '/includes/sli-rest-controller.php';

		if ( is_admin() ) {
			require_once SLI_PLUGIN_DIR . '/admin/sli-admin.php';
		}
	}
	/**
	 * Initialize plugin classes.
	 *
	 * Each module is responsible for registering
	 * its own actions and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_classes() {

		$this->login_tracker = new SLI_Login_Tracker();
		$this->user_columns = new SLI_User_Columns();
		$this->user_profile = new SLI_User_Profile();
		$this->user_filters = new SLI_User_Filters();
		$this->rest_controller = new SLI_REST_Controller();

		if ( is_admin() ) {
			$this->admin = new SLI_Admin();
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

		SLI_Activator::activate();
	}

	/**
	 * Load plugin translations.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_textdomain() {

		load_plugin_textdomain(
			'last-login-tracker',
			false,
			dirname(
				plugin_basename(
					__FILE__
				)
			) . '/languages'
		);
	}
}

/**
 * Main plugin instance.
 *
 * @since 1.0.0
 *
 * @return SLI_Plugin
 */
function WPLL() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

	return SLI_Plugin::instance();
}

/**
 * Global plugin object.
 */
$GLOBALS['wpll'] = WPLL();