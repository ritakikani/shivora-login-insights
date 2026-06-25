class SLI_Plugin {

	public $login_tracker;
	public $user_columns;
	public $user_profile;
	public $user_filters;
	public $rest_controller;
	public $admin;

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {

		$this->define_constants();

		$this->includes();

		$this->init_classes();

		$this->hooks();
	}

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

	private function init_classes() {

		$this->login_tracker  = new SLI_Login_Tracker();
		$this->user_columns   = new SLI_User_Columns();
		$this->user_profile   = new SLI_User_Profile();
		$this->user_filters   = new SLI_User_Filters();
		$this->rest_controller = new SLI_REST_Controller();

		if ( is_admin() ) {
			$this->admin = new SLI_Admin();
		}
	}

	private function hooks() {

		register_activation_hook(
			SLI_PLUGIN_FILE,
			array( $this, 'activate' )
		);

		add_action(
			'plugins_loaded',
			array( $this, 'load_textdomain' )
		);
	}

	public function activate() {

		SLI_Activator::activate();
	}

	public function load_textdomain() {

		load_plugin_textdomain(
			'last-login-tracker',
			false,
			dirname( plugin_basename( SLI_PLUGIN_FILE ) ) . '/languages'
		);
	}
}