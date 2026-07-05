<?php
// Exit if accessed directly, rather than through WordPress's uninstall process.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option(
	'shivli_settings'
);

$users = get_users(
	array(
		'fields' => 'ids',
	)
);

foreach ( $users as $user_id ) {
	delete_user_meta(
		$user_id,
		'shivora_login_insights'
	);
	delete_user_meta(
		$user_id,
		'shivli_last_login_ip'
	);
}
