=== Shivora Login Insights ===
Contributors: kikanirita
Tags: users, login, last login, user management, inactive users, admin tools
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track user login activity, IP addresses, inactive users, and export login data directly from your WordPress dashboard.

== Description ==

Shivora Login Insights helps administrators monitor user activity by recording login timestamps and IP addresses. It provides powerful reporting tools, inactive user detection, CSV exports, dashboard insights, and REST API access.

Perfect for membership sites, organizations, intranets, learning platforms, and communities that need visibility into user engagement.

== Features ==

* Track each user's last login date and time
* Record the IP address used during login
* Display login information on user profile pages
* Add sortable Last Login and Login IP columns to the Users screen
* Filter users by login activity
* Identify inactive users using predefined inactivity periods
* Dedicated Inactive Users management page
* Dashboard widget showing recent login activity
* Export user login data to CSV
* REST API endpoints for retrieving user login information
* Settings page for plugin configuration
* Translation-ready and internationalization support
* Automatic cleanup on uninstall

== Dashboard Features ==

=== User Activity Tracking ===

Monitor user activity by recording:

* Last login timestamp
* Login IP address
* User login activity information
* Recent login information

=== Inactive User Management ===

Quickly identify users who have not logged in for:

* 30 days
* 60 days
* 90 days

=== CSV Export ===

Export login activity data for:

* Auditing
* Reporting
* Compliance requirements
* User engagement analysis

== REST API ==

The plugin includes REST API endpoints for retrieving login activity data programmatically.

Developers can integrate login tracking information into custom dashboards, reporting systems, or third-party applications.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/shivora-login-insights/` directory.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Navigate to Users → Shivora Login Insights or the plugin settings page.
4. Configure your preferred settings.
5. Start monitoring user activity.

== Frequently Asked Questions ==

= Does this plugin track existing users? =

The plugin starts recording login activity after activation. Existing login history cannot be recovered.

= Does the plugin store IP addresses? =

Yes. The plugin records the IP address used during successful logins.

= Can I export user activity? =

Yes. Administrators can export login data in CSV format.

= Will data be removed after uninstalling? =

Yes. The plugin includes an uninstall routine that removes plugin data when deleted.

= Is the plugin multisite compatible? =

Compatibility depends on your installation and configuration. Testing is recommended before production deployment.

== Screenshots ==

1. User list with Last Login column
2. User profile login information
3. Inactive Users dashboard
4. Dashboard activity widget
5. Settings page
6. CSV export functionality

== Changelog ==

= 1.0.0 =

* Initial release
* Last login tracking
* Login IP tracking
* User profile integration
* User table columns
* Inactive user management
* Dashboard widget
* CSV export
* REST API support
* Settings page
* Translation support

== Upgrade Notice ==

= 1.0.0 =

Initial public release of Shivora Login Insights.

== Privacy ==

This plugin stores user login timestamps and login IP addresses for administrative purposes.

Site administrators are responsible for ensuring compliance with local privacy regulations such as GDPR and other applicable data protection laws.
