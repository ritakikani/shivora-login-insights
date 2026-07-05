<?php
// Exit if accessed directly, outside of the WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin loader class.
 *
 * Responsible for:
 * - Loading admin files.
 * - Initializing admin modules.
 *
 * @since 1.0.0
 */
class SHIVLI_Admin {

	/**
	 * Admin menu.
	 *
	 * @var SHIVLI_Admin_Menu
	 */
	public $menu;

	/**
	 * Dashboard widget.
	 *
	 * @var SHIVLI_Dashboard_Widget
	 */
	public $dashboard_widget;

	/**
	 * Settings.
	 *
	 * @var SHIVLI_Settings
	 */
	public $settings;

	/**
	 * Inactive users page.
	 *
	 * @var SHIVLI_Inactive_Users_Page
	 */
	public $inactive_users_page;

	/**
	 * Export.
	 *
	 * @var SHIVLI_Export
	 */
	public $export;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_classes();
	}

	/**
	 * Include admin files.
	 *
	 * @return void
	 */
	private function includes() {
		require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-admin-menu.php';
		require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-dashboard-widget.php';
		require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-settings.php';
		require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-inactive-users-page.php';
		require_once SHIVLI_PLUGIN_DIR . '/admin/shivli-export.php';
	}

	/**
	 * Initialize admin classes.
	 *
	 * @return void
	 */
	private function init_classes() {
		$this->menu                = new SHIVLI_Admin_Menu();
		$this->dashboard_widget    = new SHIVLI_Dashboard_Widget();
		$this->settings            = new SHIVLI_Settings();
		$this->inactive_users_page = new SHIVLI_Inactive_Users_Page();
		$this->export              = new SHIVLI_Export();
	}
}
