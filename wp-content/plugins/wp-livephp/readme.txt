=== WP Live.php ===
Contributors: mbence
Donate link: http://bencemeszaros.com/donate/
Tags: developer, live, autorefresh, theme, plugin, refresh, reload, development
Requires at least: 2.6
Tested up to: 3.8
Stable tag: /trunk/

Automatically refresh your browser if you update a post, or change any files in your theme or plugins directory

== Description ==

This plugin was written to make Wordpress theme and plugin developers' life easier.
Inspired by the brilliant live.js script (written by Martin Kool),
this plugin will auto refresh your browser if you change any files in your wp-content/themes
or plugins directories. No need for Alt-Tab and manual refresh anymore.

If you activate the WP Live Php plugin, it adds a small javascript file to your blog.
It will monitor your directories by calling wp-live.php every second. If any file changed
(i.e. has a newer filemtime), the browser will be refreshed.

With this plugin, it is also very easy to check your work in many browsers simultaneously.
Just enable Frontend Monitoring, load the site in all your browsers and the rest goes automatically.

Starting from v1.3 there is an option to enable admin bar integration, to conveniently enable or
disable Live.php monitoring directly on your frontend or backend with just one click.

Since v1.5 with the content update feature, you can auto-update your browser with every post or page save.
For this we create a file in the uploads base folder (wp-content/uploads/), and touch it with every save,
which will trigger a refresh in the client browser. That location must be writable for this to work.

WARNING!
You should never activate this plugin on a live server! It is meant for developer environment only!
Use http://wordpress.org/plugins/reload/ instead, which is created for production environments.

== Installation ==

Upload the WP Live.php plugin to your blog and Activate it.

If you want to use the content updates, make sure that the uploads base folder (wp-content/uploads/) is
writable! (But to use WP you would need this anyway...)

== Frequently Asked Questions ==

== Screenshots ==
1. Settings page
2. Admin bar integration

== Changelog ==
= 1.6.1 =
* A small js timeout fix
= 1.6 =
* Refresh CSS files without reloading the page.
= 1.5 =
* Awesome new feature: content updates!
When you save a post or page in your wp-admin, the visitor side will refresh itself, showing the new content immediately.
= 1.4.2 =
* Updated support forum link
= 1.4.1 =
* File state cache clearing added
= 1.4 =
* Switched to long polling. Now the js will open only one long ajax request every 2 minutes (or as long as the php script is allowed to run).
= 1.3.1 =
* No new features, only some refactoring and code cleaning
= 1.3 =
* Admin bar integration
= 1.2.1 =
* No cache fix for IE
= 1.2 =
* Added Backend (wp-admin) monitoring option
* Settings page improvements - Ajax controls
= 1.1.1 =
* Some minor fixes
= 1.1 =
* Added settings page (Settings/WP Live.php) to enable / disable the  monitoring function
* Some code cleanup
* Updated for WP 3.3
= 1.0 =
* Initial version

== Upgrade Notice ==
= 1.5 =
Awesome new feature: content updates!
= 1.2.1 =
Update to 1.2.1 if you plan to use this plugin on Internet Explorer
= 1.2 =
This version adds wp-admin monitoring and a nice settings page
