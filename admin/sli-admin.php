<?php
/**
 * Admin Class.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

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
class SLI_Admin {

	/**
	 * Admin menu.
	 *
	 * @var SLI_Admin_Menu
	 */
	public $menu;

	/**
	 * Dashboard widget.
	 *
	 * @var SLI_Dashboard_Widget
	 */
	public $dashboard_widget;

	/**
	 * Settings.
	 *
	 * @var SLI_Settings
	 */
	public $settings;

	/**
	 * Inactive users page.
	 *
	 * @var SLI_Inactive_Users_Page
	 */
	public $inactive_users_page;

	/**
	 * Export.
	 *
	 * @var SLI_Export
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

		require_once SLI_PLUGIN_DIR . '/admin/sli-admin-menu.php';

		require_once SLI_PLUGIN_DIR . '/admin/sli-dashboard-widget.php';

		require_once SLI_PLUGIN_DIR . '/admin/sli-settings.php';

		require_once SLI_PLUGIN_DIR . '/admin/sli-inactive-users-page.php';

		require_once SLI_PLUGIN_DIR . '/admin/sli-export.php';
	}

	/**
	 * Initialize admin classes.
	 *
	 * @return void
	 */
	private function init_classes() {

		$this->menu = new SLI_Admin_Menu();

		$this->dashboard_widget = new SLI_Dashboard_Widget();

		$this->settings = new SLI_Settings();

		$this->inactive_users_page = new SLI_Inactive_Users_Page();

		$this->export = new SLI_Export();
	}
}