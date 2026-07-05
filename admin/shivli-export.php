<?php
// Exit if accessed directly, outside of the WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Export class.
 *
 * Responsible for:
 * - Registering export submenu.
 * - Exporting login activity.
 * - Generating CSV downloads.
 *
 * @since 1.0.0
 */
class SHIVLI_Export {

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
			'admin_menu',
			array(
				$this,
				'register_submenu',
			),
			30
		);
		add_action(
			'admin_init',
			array(
				$this,
				'handle_export',
			)
		);
	}

	/**
	 * Register submenu page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_submenu() {
		add_submenu_page(
			'shivli-overview',
			__( 'Export', 'shivora-login-insights' ),
			__( 'Export', 'shivora-login-insights' ),
			'list_users',
			'shivli-export',
			array(
				$this,
				'render_page',
			)
		);
	}

	/**
	 * Render export page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Export Login Activity', 'shivora-login-insights' ); ?>
			</h1>

			<p>
				<?php esc_html_e( 'Export all tracked login activity as a CSV file.', 'shivora-login-insights' ); ?>
			</p>

			<form method="post">
				<?php wp_nonce_field( 'shivli_export_users', 'shivli_export_nonce' ); ?>
				<input type="hidden" name="shivli_action" value="export_users" />
				<?php submit_button( __( 'Export CSV', 'shivora-login-insights' ) ); ?>
			</form>

		</div>
		<?php
	}

	/**
	 * Handle export request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function handle_export() {

		// No export request submitted on this page load.
		if ( empty( $_POST['shivli_action'] ) ) {
			return;
		}

		// Not our export action; let other handlers deal with it.
		if ( 'export_users' !== $_POST['shivli_action'] ) {
			return;
		}

		// Capability is checked before the nonce so unauthorized users
		// learn nothing about nonce validity from the response.
		if ( ! current_user_can( 'list_users' ) ) {
			return;
		}

		check_admin_referer(
			'shivli_export_users',
			'shivli_export_nonce'
		);

		$this->export_csv();
	}

	/**
	 * Export CSV.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function export_csv() {

		$users = get_users();

		$filename = sprintf(
			'shivli-export-%s.csv',
			wp_date( 'Y-m-d-H-i-s' )
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$output = fopen(
			'php://output',
			'w'
		);

		fputcsv(
			$output,
			array(
				'User ID',
				'Username',
				'Display Name',
				'Email',
				'Last Login',
				'Login IP',
			)
		);

		foreach ( $users as $user ) {

			fputcsv(
				$output,
				array(
					$user->ID,
					self::escape_csv_field( $user->user_login ),
					self::escape_csv_field( $user->display_name ),
					self::escape_csv_field( $user->user_email ),
					SHIVLI_Helper::format_login_date(
						SHIVLI_Helper::get_last_login(
							$user->ID
						)
					),
					SHIVLI_Helper::get_last_login_ip(
						$user->ID
					),
				)
			);
		}

		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Streaming a CSV download via php://output; WP_Filesystem does not support output streams.
		exit;
	}

	/**
	 * Neutralize CSV formula injection.
	 *
	 * Prefixes values that start with a formula trigger
	 * character with a single quote so spreadsheet
	 * applications (Excel, Google Sheets, LibreOffice)
	 * treat them as plain text instead of executing them.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Raw field value.
	 *
	 * @return string
	 */
	private static function escape_csv_field( $value ) {

		$value = (string) $value;

		if ( '' !== $value && false !== strpos( "=+-@\t\r", $value[0] ) ) {
			$value = "'" . $value;
		}

		return $value;
	}
}