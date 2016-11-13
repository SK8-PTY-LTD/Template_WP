=== Change Permalink Helper ===
Contributors: inpsyde, Bueltge
Tags: redirect, permalink, url, seo, 301
Requires at least: 2.7
Tested up to: 4.5
Stable tag: trunk

It checks the Permalink and redirects to the new URL, if it doesn't exist. It sends the header message "moved permanently 301"

== Description ==
When you change the permalink structure then this is a problem for bookmarks of users and also for search engines to link to your posts. This plugin uses the slug of the new url and search for a ID in the database of WordPress. If it finds a post according to the slug, the Plugin will redirect to the correct post and send a header message "moved permanently 301" to change the url on the index of search engines.

== Installation ==
= Requirements =
* WordPress version 2.8 and later

= Installation =
1. Unpack the download-package
1. Upload the folder and all folder and files includes this to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Thats all

= Licence =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](http://bueltge.de/wunschliste/ "Wishliste and Donate") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

= Translations =
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the .pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows) or plugin for WordPress [Localization](http://wordpress.org/extend/plugins/codestyling-localization/).

= Acknowledgements =
* Lithuanian translation files by [Vincent G](http://www.host1plus.com)
* Turkish translation files by [Selcuk Yahsi](htt://www.eniyiwebhosting.com)
* French translation files by [noaneo](http://noaneo.fr/)
* Polish translation by [Rachela](http://couponmachine.in)

== Changelog ==
= v1.0.0 (05/16/2016) =
* Update constructor for php7 usage.

= v0.1 (10/06/2010) =
* Write a Plugin based on my ideas for customer
* 4free to use with GPL licence
* Upload on WP-Repository
