<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login history admin page.
 *
 * @since 1.1.0
 */
class SHIVLI_Login_History_Page {

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action(
			'admin_menu',
			array( $this, 'register_submenu' ),
			15
		);
	}

	/**
	 * Register Login History submenu.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_submenu() {

		add_submenu_page(
			'shivli-overview',
			__( 'Login History', 'shivora-login-insights' ),
			__( 'Login History', 'shivora-login-insights' ),
			'list_users',
			'shivli-login-history',
			array(
				$this,
				'render_page',
			)
		);
	}

	/**
	 * Render login history page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function render_page() {

		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
		if ( isset( $_POST['shivli_clear_login_history'] ) ) {

			check_admin_referer( 'shivli_clear_login_history' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'You do not have permission to clear login history.', 'shivora-login-insights' )
				);
			}

			SHIVLI_Login_History::delete_all_history();

			add_settings_error(
				'shivli_messages',
				'shivli_history_cleared',
				__( 'All login history has been deleted.', 'shivora-login-insights' ),
				'updated'
			);
		}

		$device = isset( $_GET['device'] ) ? sanitize_text_field( wp_unslash( $_GET['device'] ) ) : '';

		$browser = isset( $_GET['browser'] ) ? sanitize_text_field( wp_unslash( $_GET['browser'] ) ) : '';

		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';

		$date_to = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';

		if ( ! current_user_can( 'list_users' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to view login history.', 'shivora-login-insights' )
			);
		}

		$search = isset( $_GET['s'] )
			? sanitize_text_field( wp_unslash( $_GET['s'] ) )
			: '';

		$per_page = 20;

		$paged = isset( $_GET['paged'] )
			? max( 1, absint( $_GET['paged'] ) )
			: 1;

		$offset = ( $paged - 1 ) * $per_page;

		$history = SHIVLI_Login_History::get_history(
			array(
				'user_id'   => $user_id,
				'search'    => $search,
				'device'    => $device,
				'browser'   => $browser,
				'date_from' => $date_from,
				'date_to'   => $date_to,
				'limit'     => $per_page,
				'offset'    => $offset,
			)
		);

		$total_items = SHIVLI_Login_History::get_history_count(
			array(
				'user_id'   => $user_id,
				'search'    => $search,
				'device'    => $device,
				'browser'   => $browser,
				'date_from' => $date_from,
				'date_to'   => $date_to,
			)
		);

		$total_pages = ceil( $total_items / $per_page ); ?>

		<div class="wrap">

			<h1>
				<?php
				esc_html_e( 'Login History', 'shivora-login-insights' ); ?>
			</h1>

			<?php settings_errors( 'shivli_messages' ); ?>

			<form method="get">

				<input type="hidden" name="page"	value="shivli-login-history" />

				<p class="search-box">

					<label class="screen-reader-text" for="shivli-login-history-search" >
						<?php esc_html_e( 'Search Users',	'shivora-login-insights' ); ?>
					</label>

					<input type="search" id="shivli-login-history-search" name="s" value="<?php echo esc_attr( $search ); ?>" />

					<?php submit_button( __( 'Search', 'shivora-login-insights' ), '', '', false ); ?>

				</p>

			</form>

			<form method="post" style="margin: 15px 0;">
				<?php wp_nonce_field( 'shivli_clear_login_history' ); ?>

				<input type="hidden" name="shivli_clear_login_history" value="1" />

				<?php
				submit_button(
					__( 'Clear All Login History', 'shivora-login-insights' ),
					'delete',
					'submit',
					false,
					array(
						'onclick' => "return confirm('" .
							esc_js(
								__( 'Are you sure you want to permanently delete all login history?', 'shivora-login-insights' )
							) .
							"');",
					)
				); ?>
			</form>

			<form method="get" style="margin: 15px 0;">
				<input type="hidden" name="page" value="shivli-login-history" />

				<select name="device">
					<option value="">
						<?php esc_html_e( 'All Devices', 'shivora-login-insights' ); ?>
					</option>
					<option value="Desktop" <?php selected( $device, 'Desktop' ); ?>>
						<?php esc_html_e( 'Desktop', 'shivora-login-insights' ); ?>
					</option>
					<option value="Mobile" <?php selected( $device, 'Mobile' ); ?>>
						<?php esc_html_e( 'Mobile', 'shivora-login-insights' ); ?>
					</option>
					<option value="Tablet" <?php selected( $device, 'Tablet' ); ?>>
						<?php esc_html_e( 'Tablet', 'shivora-login-insights' ); ?>
					</option>
				</select>

				<select name="browser">
					<option value="">
						<?php esc_html_e( 'All Browsers', 'shivora-login-insights' ); ?>
					</option>
					<option value="Chrome" <?php selected( $browser, 'Chrome' ); ?>>
						<?php esc_html_e( 'Chrome', 'shivora-login-insights' ); ?>
					</option>
					<option value="Firefox" <?php selected( $browser, 'Firefox' ); ?>>
						<?php esc_html_e( 'Firefox', 'shivora-login-insights' ); ?>
					</option>
					<option value="Safari" <?php selected( $browser, 'Safari' ); ?>>
						<?php esc_html_e( 'Safari', 'shivora-login-insights' ); ?>
					</option>
					<option value="Microsoft Edge" <?php selected( $browser, 'Microsoft Edge' ); ?>>
						<?php esc_html_e( 'Microsoft Edge', 'shivora-login-insights' ); ?>
					</option>
					<option value="Opera" <?php selected( $browser, 'Opera' ); ?>>
						<?php esc_html_e( 'Opera', 'shivora-login-insights' ); ?>
					</option>
				</select>

				<input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>" />

				<input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>" />

				<?php submit_button( __( 'Filter', 'shivora-login-insights' ), '', '', false ); ?>

				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=shivli-login-history' ) ); ?>">
					<?php esc_html_e( 'Reset', 'shivora-login-insights' ); ?>
				</a>

			</form>

			<?php
			$export_url = wp_nonce_url(
				add_query_arg(
					array(
						'action'    => 'shivli_export_login_history',
						's'         => $search,
						'device'    => $device,
						'browser'   => $browser,
						'date_from' => $date_from,
						'date_to'   => $date_to,
					),
					admin_url( 'admin-post.php' )
				),
				'shivli_export_login_history'
			); ?>

			<p>
				<a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary" >
					<?php esc_html_e( 'Export Login History CSV', 'shivora-login-insights' ); ?>
				</a>
			</p>

			<table class="widefat striped">

				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'Login Count', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'User', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Login Date', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'IP Address', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Browser', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'OS', 'shivora-login-insights' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Device', 'shivora-login-insights' ); ?>
						</th>

					</tr>
				</thead>

				<tbody>

					<?php if ( empty( $history ) ) : ?>

						<tr>
							<td colspan="6">
								<?php esc_html_e( 'No login history found.', 'shivora-login-insights' ); ?>
							</td>
						</tr>

					<?php else : ?>

						<?php foreach ( $history as $login ) : ?>

							<?php $user = get_user_by( 'id', absint( $login->user_id ) ); ?>

							<tr>

								<td>
									<?php echo esc_html( number_format_i18n( SHIVLI_Login_History::get_login_count( absint( $login->user_id ) ) ) ); ?>
								</td>

								<td>

									<?php if ( $user ) : ?>

										<a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>" >
											<?php echo esc_html( $user->display_name ); ?>
										</a>
										<br>

										<small>
											<?php echo esc_html( $user->user_email ); ?>
										</small>

										<br>

										<a href="<?php echo esc_url(
												add_query_arg(
													array(
														'page'    => 'shivli-login-history',
														'user_id' => $user->ID,
													),
													admin_url( 'admin.php' )
												)
											); ?>" >
											<?php esc_html_e( 'View User History', 'shivora-login-insights' ); ?>
										</a>

									<?php else : ?>

										<?php esc_html_e( 'Deleted User', 'shivora-login-insights' ); ?>

									<?php endif; ?>

								</td>

								<td>
									<?php
									echo esc_html(
										wp_date(
											get_option( 'date_format' ) . ' ' .
											get_option( 'time_format' ),
											strtotime( $login->login_time )
										)
									);
									?>
								</td>

								<td>
									<?php echo $login->ip_address ? esc_html( $login->ip_address ) : '-'; ?>
								</td>

								<td>
									<?php echo $login->browser ? esc_html( $login->browser ) : '-'; ?>
								</td>

								<td>
									<?php echo $login->os ? esc_html( $login->os ) : '-'; ?>
								</td>

								<td>
									<?php echo $login->device	? esc_html( $login->device ) : '-'; ?>
								</td>

							</tr>

						<?php endforeach;

					endif; ?>

				</tbody>

			</table>

			<?php if ( $total_pages > 1 ) : ?>

				<div class="tablenav bottom">

					<div class="tablenav-pages">

						<?php
						echo wp_kses_post(
							paginate_links(
								array(
									'base'      => add_query_arg(
										'paged',
										'%#%'
									),
									'format'    => '',
									'current'   => $paged,
									'total'     => $total_pages,
									'prev_text' => '&laquo;',
									'next_text' => '&raquo;',
									'type'      => 'plain',
									'add_args'  => array(
										's' => $search,
									),
								)
							)
						);
						?>

					</div>

				</div>

			<?php endif; ?>

		</div>

		<?php
	}
}