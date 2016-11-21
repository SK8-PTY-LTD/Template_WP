=== Advanced Woo Search ===
Contributors: Mihail Barinov
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GSE37FC4Y7CEY
Tags: widget, plugin, woocommerce, search, product search, woocommerce search, ajax search, live search, custom search, ajax, shortcode, better search, relevance search, relevant search, search by sku, search plugin, shop, store, wordpress search, wp ajax search, wp search, wp search plugin, sidebar, ecommerce, merketing, products, category search, instant-search, search highlight, woocommerce advanced search, woocommerce live search, WooCommerce Plugin, woocommerce product search
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.09
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced AJAX search plugin for WooCommerce

== Description ==

Advanced Woo Search - powerful live search plugin for WooCommerce. Just start typing and you will immediately see the products that you search.

= Main Features =

* **Products search** - Search across all your WooCommerce products
* **Settings page** - User-friendly settings page with lot of options
* **Search in** - Search in product title, content, excerpt, categories, tags and sku. Or just in some of them
* **Shortcode** - Use shortcode to place search box anywhere you want
* **Product image** - Each search result contains product image
* **Product price** - Each search result contains product price
* **Terms search** - Search for product categories and tags
* **Smart ordering** - Search results ordered by the priority of source where they were found
* **Fast** - Nothing extra. Just what you need for proper work

= Premium Features =

[Premium Version Demo](https://advanced-woo-search.com/)
	
* Search **results layouts**
* **Filters**. Switch between tabs to show different search results
* **Unlimited** amount of search form instances
* **Anvanced settings page** with lot of options
* **Exclude** spicific products by its categories or tags from search results
* Ability to specify **source of image** for search results: featured image, gallery, product content, product short description or set default image if there is no other images

== Installation ==

1. Upload advanced-woo-search to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the plugin shortcode [aws_search_form] into your template or post-page or just use build-in widget

== Frequently Asked Questions ==

= How to insert search form? =

You can use build-in widget to place plugins search form to your sidebar.

Or just use shortcode for displaying form inside your post/page:

`[aws_search_form]`

Or insert this function inside php file ( often it used to insert form inside page templates files ):

`echo do_shortcode( '[aws_search_form]' );`

== Screenshots ==

1. Front-end view
2. Plugin settings page

== Changelog ==

= 1.09 =
* Make indexing of the products content much more fuster
* Fix several bugs

= 1.08 =
* Update check for active WooCommerce plugin
* Add hungarian translation ( big thanks to hunited! )

= 1.07 =
* Exclude hidden products from search
* Update translatable strings

= 1.06 =
* Cache search results to increase search speed

= 1.05 =
* Improve search speed

= 1.04 =
* Fix issue with SKU search
* Add option to display product SKU in search results

= 1.03 =
* Add search in product terms ( categories, tags )
* Fix issue with not saving settings

= 1.02 =
* Add single page search for 'product' custom post type
* Fix problem with dublicate products in the search results

= 1.01 =
* Fix problem with result block layout

= 1.00 =
* First Release