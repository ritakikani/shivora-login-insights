<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API controller.
 *
 * Responsible for:
 * - Registering REST API routes.
 * - Returning user login activity.
 * - Returning inactive users.
 *
 * @since 1.0.0
 */
class SLI_REST_Controller {
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
			'rest_api_init',
			array( $this, 'register_routes' )
		);
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'sli/v1',
			'/user/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array(
					$this,
					'get_user_activity',
				),
				'permission_callback' => array(
					$this,
					'permissions_check',
				),
			)
		);
		register_rest_route(
			'sli/v1',
			'/inactive-users',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array(
					$this,
					'get_inactive_users',
				),
				'permission_callback' => array(
					$this,
					'permissions_check',
				),
			)
		);
	}

	/**
	 * Check API permissions.
	 *
	 * Only administrators and users
	 * with user management capability
	 * can access endpoints.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function permissions_check() {
		return current_user_can(
			'list_users'
		);
	}

	/**
	 * Get user activity.
	 *
	 * Endpoint:
	 * /wp-json/sli/v1/user/{id}
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_user_activity( $request ) {
		$user_id = absint(
			$request['id']
		);
		$user = get_userdata(
			$user_id
		);
		if ( ! $user ) {
			return new WP_Error(
				'sli_user_not_found',
				__(
					'User not found.',
					'shivora-login-insights'
				),
				array(
					'status' => 404,
				)
			);
		}
		$data = array(
			'id'            => $user->ID,
			'username'      => $user->user_login,
			'display_name'  => $user->display_name,
			'email'         => $user->user_email,
			'last_login'    => SLI_Helper::get_last_login(
				$user->ID
			),
			'last_login_ip' => SLI_Helper::get_last_login_ip(
				$user->ID
			),
		);
		return rest_ensure_response(
			$data
		);
	}

	/**
	 * Get inactive users.
	 *
	 * Endpoint:
	* /wp-json/sli/v1/inactive-users?days=30
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_inactive_users( $request ) {
		$days = absint(
			$request->get_param(
				'days'
			)
		);
		if ( empty( $days ) ) {
			$days = 30;
		}
		$timestamp = strtotime(
			sprintf(
				'-%d days',
				$days
			)
		);
		$users = get_users(
			array(
				'meta_key'     => 'shivora_login_insights',
				'meta_value'   => $timestamp,
				'meta_compare' => '<',
			)
		);
		$response = array();
		foreach ( $users as $user ) {
			$response[] = array(
				'id'           => $user->ID,
				'username'     => $user->user_login,
				'display_name' => $user->display_name,
				'email'        => $user->user_email,
				'last_login'   => SLI_Helper::get_last_login(
					$user->ID
				),
			);
		}
		return rest_ensure_response(
			$response
		);
	}
}