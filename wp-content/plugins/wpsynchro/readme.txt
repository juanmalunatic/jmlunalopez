=== WordPress Migration Plugin DB & Files - WP Synchro ===
Contributors: wpsynchro
Donate link: https://wpsynchro.com/?utm_source=wordpress.org&utm_medium=referral&utm_campaign=donate
Tags: migrate,database,files,media,migration
Requires at least: 4.9
Tested up to: 6.1
Stable tag: 1.9.0
Requires PHP: 7.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0

WordPress migration plugin that migrates database tables, media, plugins, themes and whatever you want.
Fully customizable. Setup once and run as many times as you want.

== Description ==

**Complete Migration Plugin for WP professionals**

The only migration tool you will ever need as a professional WordPress developer.
WP Synchro was created to be the migration plugin for developers, with a need to do customized migrations or just full migrations.
You need it done in a fast and easy way, that can be re-run very quickly without any further manual steps, like after a code update.
You can fully customize which database tables you want to move and in PRO version, which files/dirs you want to migrate.

A classic task that WP Synchro will handle for you, is keeping a local development site synchronized with a production site or a staging site in sync with a production site.
You can also push data from your staging or local development enviornment to your production site.

**WP Synchro FREE gives you:**

*   Pull/push database from one site to another site
*   Search/replace in database data (supports serialized data ofc)
*   Handles migration of database table prefixes between sites
*   Select the specific database tables you want to move or just move all
*   Clear cache after migration for popular cache plugins
*   High security - No other sites and servers are involved and all data is encrypted on transfer
*   Setup once - Run multiple times - Perfect for development/staging/production environments

**In addition to this, the PRO version gives you:**

*   File migration (such as media, plugins, themes or custom files/dirs)
*   Only migrate the difference in files, making it super fast
*   Serves a user confirmation on the added/changed/deleted files, before doing any changes
*   Customize the exact migration you need - Down to a single file
*   Support for basic authentication (.htaccess username/password)
*   Notification email on success or failure to a list of emails
*   Database backup before migration
*   WP CLI command to schedule migrations via cron or other trigger
*   Pretty much the ultimate tool for doing WordPress migrations
*   14 day trial is waiting for you to get started at [WPSynchro.com](https://wpsynchro.com/ "WP Synchro PRO")

**Typical use for WP Synchro:**

 *  Developing websites on local server and wanting to push a website to a live server or staging server
 *  Get a copy of a working production site, with both database and files, to a staging or local site for debugging or development with real data
 *  Generally moving WordPress sites from one place to another, even on a firewalled local network

**WP Synchro PRO version:**

Pro version gives you more features, such as synchronizing files, database backup, notifications, support for basic authentication, WP CLI command and much faster support.
Check out how to get PRO version at [WPSynchro.com](https://wpsynchro.com/ "WP Synchro PRO")
We have a 14 day trial waiting for you and 30 day money back guarantee. So why not try the PRO version?

== Installation ==

**Here is how you get started:**

1. Upload the plugin files to the `/wp-content/plugins/wpsynchro` directory, or install the plugin through the WordPress plugins screen directly
1. Make sure to install the plugin on all the WordPress migrations (it is needed on both ends of the synchronizing)
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Choose if data can be overwritten or be downloaded from migration in menu WP Synchro->Setup
1. Add your first migration from WP Synchro overview page and configure it
1. Run the migration
1. Enjoy
1. Rerun the same migration again next time it is needed and enjoy how easy that was

== Frequently Asked Questions ==

= Do you offer support? =

Yes we do, for both free and PRO version. But PRO version users always get priority support, so support requests for the free version will normally take some time.
Check out how to get PRO version at [WPSynchro.com](https://wpsynchro.com/ "WP Synchro site")

You can contact us at <support@wpsynchro.com> for support. Also check out the "Support" menu in WP Synchro, that provides information needed for the support request.

= Does WP Synchro do database merge? =

No. We do not merge data in database. We only migrate the data and overwrite the current.

= Where can i contact you with new ideas and bugs? =

If you have an idea for improving WP Synchro or found a bug in WP Synchro, we would love to hear from you on:
<support@wpsynchro.com>

= What is WP Synchro tested on? (WP Version, PHP, Databases)=

Currently we do automated testing on more than 300 different hosting environments with combinations of WordPress/PHP/Database versions.

WP Synchro is tested on :
 * MySQL 5.5 up to MySQL 8.0 and MariaDB from 5.5 to 10.7.
 * PHP 7.0 up to latest version
 * WordPress from 4.9 to latest version.

= Do you support multisite? =

No, not at the moment.
We have not done testing on multisite yet, so use it is at own risk.
It is currently planned for one of the next releases to support it.

== Screenshots ==

1. Shows the overview of plugin, where you start and delete the migration jobs
2. Shows the add/edit screen, where you setup a migration job
3. Shows the setup of the plugin
4. WP Synchro doing a database migration

== Changelog ==

= 1.9.0 =
 * Bug: Fix issue where MU plugin did not load properly
 * Improvement: Ensure WP 6.1 and PHP 8.1 compatibility
 * Improvement: Improve the warning message when different versions of WP is used
 * Improvement: Add search/replaces for db for cases where the protocol part of the url is wrong in db
 * Improvement: Add check for MU plugin enabled on the target site, when file migration is done, to ensure performance and result
 * Improvement: Add "resume" button when migrations fail, which can used to attempt resume, in such cases where a simple timeout is the problem

= 1.8.4 =
 * Hotfix: (Only released for FREE version) Fix php warning because of files only present in PRO version.

= 1.8.3 =
 * Hotfix: Fix issue when running one of the sites in a subdir, like http://domain.test/mysite1/. Error was like this: "Cannot read properties of undefined (reading 'dbtables')"

= 1.8.2 =
 * Bug: In some cases no search/replaces were done, when using the pre-configured migrations (the affected migrations will be deleted when updating to this version, to prevent problems)
 * Bug: Certain MySQL version in 8.0.x range gave problems when migrating to MariaDB, which is now fixed by WP Synchro
 * Bug: Table prefix re-write failed, when there was already data in table with that prefix
 * Improvement: License information is now included with log files


** Only showing the last few releases - See rest of changelog in changelog.txt or in menu "Changelog" **