=== DrawIt (draw.io) ===
Contributors: assortedchips
Donate link: http://www.assortedchips.com/lunch.html
Tags: drawit, diagram, block diagram, flow chart, draw, hierarchy, visio, draw.io, chart, network, topology, genealogy, process map, electrical, uml, org chart, bpmn, sitemap, circuit, schematic, floor plan, clipart, family tree
Requires at least: 4.0
Tested up to: 4.5
Stable tag: 1.1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Draw and edit flow charts, diagrams, images and more while editing a post.

== Description ==

DrawIt is a WordPress plugin that interfaces with the draw.io website to easily create beautiful diagrams, flow charts and drawings inline while you are editing a post. This powerful plugin saves the draw.io source code for your diagram and a PNG or SVG version of the image – providing crisp images that you can update without redrawing the diagram. There is also no hassle moving images back and forth between editors on your computer like typically is done without this plugin.

There are multiple ways to add a diagram, use whichever is most convenient for you: through the Media Library, the visual editor or the text editor.

This plugin connects directly to the draw.io website, which is a high-quality diagram and flow chart tool with a feature set on par with other well-known diagram editors (like Microsoft Visio), so the interface will already be intuitive. Best of all, it is **free!**

== Installation ==

1. Go to WP admin > Plugins > Add New
1. Search for "DrawIt" in the search field, press Enter.
1. Click "Install Now"
1. Click "Activate Plugin"

== Frequently Asked Questions ==

= How do I edit a diagram? =

To edit a diagram that you've already created, just select it (e.g., the source code in the text post editor or the image itself in the visual post editor) and then click on the DrawIt button in the editor!

= How do I report a bug or feature request? =

Please report all bugs and feature requests through the [DrawIt support page on WordPress](https://wordpress.org/support/plugin/drawit) or through the [Google+ DrawIt community](https://plus.google.com/communities/112051242587930767153).

= Where is the source code for my diagram saved? =

The source code for the diagram is saved with the image in your WordPress installation. As long as you do not delete the image from your media library, then you will be able to open and edit the image from the post/page editor where it is being used.

= How do I edit a diagram that is only in the media library and not inserted into a post? =

For now, you'll have to insert it into a post to be able to edit it. We'll work on improving this later.

== Screenshots ==

1. Creating/editing a diagram in the plugin
1. Many convenient ways to insert a new diagram
1. Selecting a diagam to edit from the WordPress visual editor
1. Selecting a diagam to edit from the WordPress text editor

== Changelog ==

= 1.1.0 =
* Added option to enable DrawIt in frontend-based editors.
* Other minor options and bug fixes.

= 1.0.15 =
* SVG saving update coinciding with draw.io server now encoding in base64.

= 1.0.14 =
* Workaround for server not responding with 'format' attribute ('no image type was specified' error).
* Better error messages that do not use alert() popup (i.e., can copy/paste message).

= 1.0.12 =
* Fixed problem where version 1.0.10 broke for older versions of php.

= 1.0.10 =
* Update necessary to handle new draw.io SVG handling. Upgrade required or SVG saving will no longer work.
* Added option for selecting temp directory.

= 1.0.9 =
* Fixed temp directory problem for Mac OS installations.

= 1.0.8 =
* Fixed file saving problem for Windows or WAMPServer based installations.

= 1.0.7 =
* Fixed problem where saving SVGs sometimes would be malformed.
* Reduced editor iframe height to get rid of needless scrollbar.

= 1.0.6 =
* Removed references to get_plugin_data() before the function is available.

= 1.0.5 =
* Added Version and F.A.Q. sections to the settings page.
* Shortened DrawIt button text in text editor to take up less space.
* Updated the enqueuing of CSS and JS to use plugin version number so updated plugin won't use old cached CSS and JS.
* Updated plugin name

= 1.0.4 =
* Updated error messages to be more clear/useful

= 1.0.3 =
* Minor aesthetics

= 1.0.2 =
* Minor GUI changes when saving

= 1.0.1 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
Added option to enable DrawIt in frontend-based editors.

= 1.0.15 =
SVG saving update coinciding with draw.io server now encoding in base64. Must update for SVG to continue working.

= 1.0.14 =
Workaround for server not responding with 'format' attribute ('no image type was specified' error).

= 1.0.12 =
Fixed problem where version 1.0.10 broke for older versions of php. Also - previous version has update necessary to handle new draw.io SVG handling. Upgrade required to 1.0.12 or SVG saving will no longer work.

= 1.0.10 =
Update necessary to handle new draw.io SVG handling. Upgrade required or SVG saving will no longer work.

= 1.0.9 =
Fixes temp file problem for Mac OS installations.

= 1.0.8 =
Fixed file saving problem for Windows or WAMPServer based installations.

= 1.0.7 =
Important fix for saving SVG format, please upgrade immediately if you use SVGs.

= 1.0.6 =
Fixes fatal error with get_plugin_data() for some installations, please upgrade.

= 1.0.5 =
CSS and javascript links in header now include the plugin version as a GET variable to prevent old versions' cached copies of the CSS and javascript from being used.

== To-do List ==
* Will add option for saving draw.io XML source in the PNG or SVG directly, instead of only saving the source XML to the WP database.

== Notice ==

This plugin uses the [draw.io website](https://www.draw.io/), but is not affiliated with draw.io.
