=== Quick and Easy FAQs ===
Contributors: saqibsarwar
Tags: FAQs, FAQ, FAQs list, accordion FAQs, toggle FAQs, filtered FAQs, grouped FAQs
Requires at least: 4.0
Tested up to: 4.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A quick and easy way to add FAQs to your site.

== Description ==

This plugin provides a quick and easy way add FAQs using custom post type and later on displaying those FAQs using shortcodes. For shortcodes details, Please consult the documentation below.

### Features

* Easily add FAQs using FAQ custom post type.
* Display FAQs in simple list style.
* Display FAQs in toggle ( independent form of accordion ) style.
* Display FAQs in filterable toggle style.
* Settings page to change toggle style text, background and border colors.
* Custom CSS box in settings page to override default styles.
* Translation Ready ( Comes with related pot and po files )
* RTL ( Right to Left Language ) Support
* Support for Visual Composer Plugin

### Documentation

* Display all FAQs in simple list style.
	`[faqs]`

* Display all FAQs in simple list style but separated by groups.
	`[faqs grouped="yes"]`

* Display FAQs in simple list style but filtered by given group slug.
	`[faqs filter="group-slug"]` or `[faqs filter="group-slug,another-group-slug"]`

* Display all FAQs in toggle style using following shortcode.
	`[faqs style="toggle"]`

* Display all FAQs in toggle style but separated by groups.
	`[faqs style="toggle" grouped="yes"]`

* Display FAQs in toggle style but filtered by given group slug.
	`[faqs style="toggle" filter="group-slug"]` or `[faqs style="toggle" filter="group-slug,another-group-slug"]`

* Display all FAQs in filterable toggle style.
	`[faqs style="filterable-toggle"]`

### Links

- [GitHub Repository](https://github.com/saqibsarwar/quick-and-easy-faqs)

== Installation ==

1. Unzip the downloaded package
1. Upload `quick-and-easy-faqs` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==
1. Add FAQ
1. Plugin Settings Page
1. List style FAQs
1. Toggle style FAQs in groups
1. Toggle style FAQs filterable by group

== Changelog ==

= 1.0.3 =
* Tested for WordPress 4.4

= 1.0.2 =
* Tested for WordPress 4.3
* Fixed a syntax error

= 1.0.1 =
* Fixed a spacing issue in filterable FAQs
* Added Visual Composer Support
* Added bottom margin to 'Back to Index' link in simple listing

= 1.0.0 =
* Initial Release