=== Font Awesome Integration ===

Contributors: mcostales84
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VWWTUHZWRS4DG
Tags: font awesome, icons
Requires at least: 3.0.1
Tested up to: 4.5.2
Stable tag: 3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin integrate the Font Awesome library with your wordpress installation.

== Description ==

This plugin just add a simple shortcode to the functions.php file, which embed the latest version of the Font Awesome css (4.6.2). Now using a javascript and the Font Awesome CDN to delivery faster and ligther icons. Now including automatic updates to the icons without even update the plugin!
Once installed, you can go to http://fortawesome.github.io/Font-Awesome/icons/ and check the code of the icon you want to use. Just click on the icon and copy and paste de code wherever you want to use it.

You can also add an icon using the shortcode [fawesome]. This shortcode has four attributes:

- aclass -> classes to use in the 'a' tag
- target -> target of the 'a' tag (_blank, _self, _parent, _top)
- href   -> link to use in the 'a' tag
- iclass -> classes to use in the /i/ tag

** Examples **

[fawesome aclass="" target="" href="" iclass=""]

[fawesome iclass="fa-cog fa-3x fa-spin"]

[fawesome iclass="fa-facebook" href="http://www.fb.com/jumptoweb" target="_blank"]

NOTE: To see more examples you can use in the iclass parameter, check this page http://fortawesome.github.io/Font-Awesome/examples/

== Installation ==

To install this plugin just follow the standard procedure.

or

1. Upload `font-awesome-integration.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Any question? =

Send me an email at mcostales@jumptoweb.com and I will answer you as soon as I can.

== Changelog ==

= 2.1 =
- Enable the ability to put shorcodes into widgets (in case the theme doesn't permit it).

= 2.0 =
- Add shortcode to display font awesome icons.
- Add the parameters to the shortcode to create a link with custom classes.

= 1.1 =
- Add compatibility with other plugins.
- Add restriction to access the plugin directy.

= 1.0 =
- Just launch the plugin!
