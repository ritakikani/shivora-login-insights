<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login history class.
 *
 * Responsible for:
 * - Retrieving login history.
 * - Retrieving user login history.
 * - Counting logins.
 * - Deleting login history.
 *
 * @since 1.1.0
 */
class SHIVLI_Login_History {

	/**
	 * Get database table name.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'shivli_login_history';
	}

	/**
	 * Get login history.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return array
	 */
	public static function get_history( $args = array() ) {
		global $wpdb;
		$defaults = array(
			'user_id'  => 0,
			'search'   => '',
			'device'   => '',
			'browser'  => '',
			'date_from' => '',
			'date_to'   => '',
			'limit'    => 20,
			'offset'   => 0,
			'orderby'  => 'login_time',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		$table_name = self::get_table_name();

		$allowed_orderby = array(
			'id',
			'user_id',
			'login_time',
			'ip_address',
			'browser',
			'os',
			'device',
		);

		$orderby = in_array(
			$args['orderby'],
			$allowed_orderby,
			true
		) ? $args['orderby'] : 'login_time';

		$order = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';

		$limit  = max( 1, absint( $args['limit'] ) );
		$offset = absint( $args['offset'] );

		$where  = 'WHERE 1=1';
		$params = array();

		/**
		 * Filter by user ID.
		 */
		if ( ! empty( $args['user_id'] ) ) {
			$where   .= ' AND user_id = %d';
			$params[] = absint( $args['user_id'] );
		}

		/**
		 * Filter by username, email or display name.
		 */
		if ( ! empty( $args['search'] ) ) {
			$user_ids = get_users(
				array(
					'search'         => '*' . $args['search'] . '*',
					'search_columns' => array(
						'user_login',
						'user_email',
						'display_name',
					),
					'fields' => 'ID',
				)
			);

			if ( empty( $user_ids ) ) {
				return array();
			}

			$placeholders = implode(
				',',
				array_fill(
					0,
					count( $user_ids ),
					'%d'
				)
			);

			$where .= " AND user_id IN ({$placeholders})";

			foreach ( $user_ids as $user_id ) {
				$params[] = absint( $user_id );
			}
		}

		/**
		 * Filter by device.
		 */
		if ( ! empty( $args['device'] ) ) {
			$where   .= ' AND device = %s';
			$params[] = sanitize_text_field( $args['device'] );
		}

		/**
		 * Filter by browser.
		 */
		if ( ! empty( $args['browser'] ) ) {
			$where   .= ' AND browser = %s';
			$params[] = sanitize_text_field( $args['browser'] );
		}

		/**
		 * Filter from date.
		 */
		if ( ! empty( $args['date_from'] ) ) {
			$date_from = sanitize_text_field( $args['date_from'] );
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_from ) ) {
				$where   .= ' AND login_time >= %s';
				$params[] = $date_from . ' 00:00:00';
			}
		}

		/**
		 * Filter to date.
		 */
		if ( ! empty( $args['date_to'] ) ) {

			$date_to = sanitize_text_field( $args['date_to'] );

			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_to ) ) {
				$where   .= ' AND login_time <= %s';
				$params[] = $date_to . ' 23:59:59';
			}
		}

		/**
		 * Build query.
		 */
		$sql = "SELECT *
			FROM {$table_name}
			{$where}
			ORDER BY {$orderby} {$order}
			LIMIT %d OFFSET %d";

		$params[] = $limit;
		$params[] = $offset;

		$query = $wpdb->prepare(
			$sql,
			$params
		);

		return $wpdb->get_results( $query );
	}

	/**
	 * Get login history for a specific user.
	 *
	 * @since 1.1.0
	 *
	 * @param int $user_id User ID.
	 * @param int $limit   Number of records.
	 *
	 * @return array
	 */
	public static function get_user_history( $user_id, $limit = 10 ) {

		return self::get_history(
			array(
				'user_id' => absint( $user_id ),
				'limit'   => absint( $limit ),
				'offset'  => 0,
			)
		);
	}

	/**
	 * Get total login count.
	 *
	 * @since 1.1.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return int
	 */
	public static function get_login_count( $user_id = 0 ) {

		global $wpdb;

		$table_name = self::get_table_name();

		if ( $user_id ) {

			return (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(id)
					FROM {$table_name}
					WHERE user_id = %d",
					absint( $user_id )
				)
			);
		}

		return (int) $wpdb->get_var(
			"SELECT COUNT(id) FROM {$table_name}"
		);
	}

	/**
	 * Get total history records.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public static function get_total_count() {

		return self::get_login_count();
	}

	/**
	 * Delete user login history.
	 *
	 * @since 1.1.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return int|false
	 */
	public static function delete_user_history( $user_id ) {

		global $wpdb;

		return $wpdb->delete(
			self::get_table_name(),
			array(
				'user_id' => absint( $user_id ),
			),
			array(
				'%d',
			)
		);
	}

	/**
	 * Delete all login history.
	 *
	 * @since 1.1.0
	 *
	 * @return int|false
	 */
	public static function delete_all_history() {

		global $wpdb;

		return $wpdb->query(
			'DELETE FROM ' . self::get_table_name()
		);
	}

	/**
	 * Delete history older than specified days.
	 *
	 * @since 1.1.0
	 *
	 * @param int $days Number of days.
	 *
	 * @return int|false
	 */
	public static function delete_old_history( $days ) {

		global $wpdb;

		$days = absint( $days );

		if ( $days < 1 ) {
			return false;
		}

		$cutoff = gmdate(
			'Y-m-d H:i:s',
			time() - ( $days * DAY_IN_SECONDS )
		);

		return $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . self::get_table_name() . '
				WHERE login_time < %s',
				$cutoff
			)
		);
	}

	public static function cleanup_old_history( $days ) {

		global $wpdb;

		$days = absint( $days );

		if ( $days < 1 ) {
			return false;
		}

		$cutoff = wp_date(
			'Y-m-d H:i:s',
			current_time( 'timestamp' ) - ( $days * DAY_IN_SECONDS )
		);

		return $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . self::get_table_name() . '
				WHERE login_time < %s',
				$cutoff
			)
		);
	}

	public static function run_cleanup() {

		$settings = SHIVLI_Helper::get_settings();

		$retention_days = isset( $settings['retention'] )
			? absint( $settings['retention'] )
			: 90;

		if ( $retention_days < 1 ) {
			return;
		}

		self::cleanup_old_history( $retention_days );
	}

	/**
	 * Get total number of login history records.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return int
	 */
	public static function get_history_count( $args = array() ) {

		global $wpdb;

		$table_name = self::get_table_name();

		$defaults = array(
			'user_id'   => 0,
			'search'    => '',
			'device'    => '',
			'browser'   => '',
			'date_from' => '',
			'date_to'   => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$where  = 'WHERE 1=1';
		$params = array();

		if ( ! empty( $args['user_id'] ) ) {

			$where   .= ' AND user_id = %d';
			$params[] = absint( $args['user_id'] );
		}
		/*
		* Search users.
		*/
		if ( ! empty( $args['search'] ) ) {

			$user_ids = get_users(
				array(
					'search'         => '*' . $args['search'] . '*',
					'search_columns' => array(
						'user_login',
						'user_email',
						'display_name',
					),
					'fields' => 'ID',
				)
			);

			if ( empty( $user_ids ) ) {
				return 0;
			}

			$placeholders = implode(
				',',
				array_fill(
					0,
					count( $user_ids ),
					'%d'
				)
			);

			$where .= " AND user_id IN ({$placeholders})";

			foreach ( $user_ids as $user_id ) {
				$params[] = absint( $user_id );
			}
		}

		/*
		* Device filter.
		*/
		if ( ! empty( $args['device'] ) ) {

			$where   .= ' AND device = %s';
			$params[] = sanitize_text_field( $args['device'] );
		}

		/*
		* Browser filter.
		*/
		if ( ! empty( $args['browser'] ) ) {

			$where   .= ' AND browser = %s';
			$params[] = sanitize_text_field( $args['browser'] );
		}

		/*
		* Start date.
		*/
		if (
			! empty( $args['date_from'] ) &&
			preg_match(
				'/^\d{4}-\d{2}-\d{2}$/',
				$args['date_from']
			)
		) {
			$where   .= ' AND login_time >= %s';
			$params[] = $args['date_from'] . ' 00:00:00';
		}

		/*
		* End date.
		*/
		if (
			! empty( $args['date_to'] ) &&
			preg_match(
				'/^\d{4}-\d{2}-\d{2}$/',
				$args['date_to']
			)
		) {
			$where   .= ' AND login_time <= %s';
			$params[] = $args['date_to'] . ' 23:59:59';
		}

		$sql = "SELECT COUNT(id)
			FROM {$table_name}
			{$where}";

		if ( ! empty( $params ) ) {
			$sql = $wpdb->prepare(
				$sql,
				$params
			);
		}

		return (int) $wpdb->get_var( $sql );
	}

	/**
	 * Get total number of login events.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public static function get_total_login_count() {

		global $wpdb;

		$table_name = self::get_table_name();

		return (int) $wpdb->get_var(
			"SELECT COUNT(id) FROM {$table_name}"
		);
	}

	/**
	 * Get number of login events today.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public static function get_today_login_count() {

		global $wpdb;

		$table_name = self::get_table_name();

		$start = wp_date(
			'Y-m-d 00:00:00',
			current_time( 'timestamp' )
		);

		$end = wp_date(
			'Y-m-d 23:59:59',
			current_time( 'timestamp' )
		);

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id)
				FROM {$table_name}
				WHERE login_time BETWEEN %s AND %s",
				$start,
				$end
			)
		);
	}

	/**
	 * Get unique login IP count.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public static function get_unique_ip_count() {

		global $wpdb;

		$table_name = self::get_table_name();

		return (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT ip_address)
			FROM {$table_name}
			WHERE ip_address IS NOT NULL
			AND ip_address != ''"
		);
	}

	/**
	 * Get login count by device.
	 *
	 * @since 1.1.0
	 *
	 * @param string $device Device type.
	 *
	 * @return int
	 */
	public static function get_device_login_count( $device ) {

		global $wpdb;

		$table_name = self::get_table_name();

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id)
				FROM {$table_name}
				WHERE device = %s",
				sanitize_text_field( $device )
			)
		);
	}

	/**
	 * Get login count for the last 7 days.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public static function get_last_7_days_login_counts() {

		global $wpdb;

		$table_name = self::get_table_name();

		$results = array();

		for ( $i = 6; $i >= 0; $i-- ) {

			$date = wp_date(
				'Y-m-d',
				current_time( 'timestamp' ) - ( $i * DAY_IN_SECONDS )
			);

			$start = $date . ' 00:00:00';
			$end   = $date . ' 23:59:59';

			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(id)
					FROM {$table_name}
					WHERE login_time BETWEEN %s AND %s",
					$start,
					$end
				)
			);

			$results[] = array(
				'date'  => $date,
				'count' => (int) $count,
			);
		}

		return $results;
	}
}