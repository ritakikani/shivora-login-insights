<?php
/**
 * Export.
 *
 * @package Shivora_Login_Insights
 * @since   1.0.0
 */

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
class SLI_Export {

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
			'sli-overview',
			__( 'Export', 'last-login-tracker' ),
			__( 'Export', 'last-login-tracker' ),
			'list_users',
			'sli-export',
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
				<?php esc_html_e(
					'Export Login Activity',
					'last-login-tracker'
				); ?>
			</h1>

			<p>
				<?php esc_html_e(
					'Export all tracked login activity as a CSV file.',
					'last-login-tracker'
				); ?>
			</p>

			<form method="post">

				<?php
				wp_nonce_field(
					'wpll_export_users',
					'wpll_export_nonce'
				);
				?>

				<input
					type="hidden"
					name="wpll_action"
					value="export_users"
				/>

				<?php submit_button(
					__( 'Export CSV', 'last-login-tracker' )
				); ?>

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

		if ( empty( $_POST['wpll_action'] ) ) {
			return;
		}

		if ( 'export_users' !== $_POST['wpll_action'] ) {
			return;
		}

		if ( ! current_user_can( 'list_users' ) ) {
			return;
		}

		check_admin_referer(
			'wpll_export_users',
			'wpll_export_nonce'
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
			'sli-export-%s.csv',
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
					$user->user_login,
					$user->display_name,
					$user->user_email,
					SLI_Helper::format_login_date(
						SLI_Helper::get_last_login(
							$user->ID
						)
					),
					SLI_Helper::get_last_login_ip(
						$user->ID
					),
				)
			);
		}

		fclose( $output );

		exit;
	}
}