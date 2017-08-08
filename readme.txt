=== RestaurantPress ===
Contributors: WPEverest, shivapoudel
Tags: restaurant, appetizer, food, cafe, menu, dining, drink
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 1.3.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Allows you to create awesome restaurant menu for restaurant, bars, cafes in no time.

== Description ==

Allows you to create awesome restaurant menu for restaurant, bars, cafes in no time.

Get free support at https://wpeverest.com/support-forum/

Check the demo at https://demo.wpeverest.com/restaurantpress/

Check the docs at https://wpeverest.com/docs/restaurantpress/

### Features And Options:
* Simple, Clean and Beautiful Designs.
* Single Column Layout
* Two Column Layout
* Grid Layout
* Responsive Design
* Shortcode to embed menu in Posts and Page.
* Supports multiple menus
* Lightbox support for menu Image.

== Installation ==

1. Install the plugin either via the WordPress.org plugin directory, or by uploading the files to your server (in the /wp-content/plugins/ directory).
2. Activate the RestaurantPress plugin through the 'Plugins' menu in WordPress.
3. Go to Menu Items->Add Menu Items and start adding new menu items.
4. For more detail follow this link https://wpeverest.com/docs/restaurantpress/

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= RestaurantPress is awesome! Can I contribute? =

Yes you can! Join in on our [GitHub repository](https://github.com/wpeverest/restaurantpress/) :)

== Screenshots ==

1. One Column Layout
2. Two Column Layout
3. Grid Layout
4. Menu Items
5. Menu Grouping

== Changelog ==

= 1.3.2 - 08/08/2017 =
* Feature - Added RTL support.
* Fix - Updated select2 library.
* Fix - TinyMCE shortcode icon for Group menu.
* Fix - Defer flush so CPT is updated before flush.
* Fix - Undefinded index for key on widget checkbox save.
* Fix - Clear food menu category thumbnail field on submit.
* Tweak - Disable DFW feature pointer.
* Tweak - Remove opacity on non-disabled buttons.
* Tweak - Introduced restaurantpress_queued_js filter.
* Tweak - Check `is_singular` when looking for shortcode content.
* Tweak - Prevent notice in `wpdb_table_fix` if termmeta table is not used.

= 1.3.1 - 05/04/2016 =
* Fix - Load inline styles if user logged out.
* Fix - Hide TinyMCE shortcode for food_menu post type.
* Tweak - Update iconfonts to use dashicons if available.
* Tweak - Removed unused 'view mode' under screen options.
* Tweak - Delete orphan terms and relationships on full uninstall.

= 1.3.0 - 14/03/2016 =
* Feature - Introduced upgrade and theme support notice display.
* Dev - Migrated custom term meta implementation to WP Term Meta.
* Dev - Registered Grunt `js` task.
* Fix - Error handling for screen ids.
* Fix - Select2 library scroll on ios.
* Fix - Missing `global $wpdb` in `uninstall.php`.
* Fix - Save food grouping date with the `term_id`.
* Tweak - Hide the Food Menu category parent field.
* Tweak - Appropriate hook reqd for official FoodHunt theme.
* Deprecated - Added deprecated notice for `rp_shortcode_tag()`.

[See changelog for all versions](https://raw.githubusercontent.com/wpeverest/restaurantpress/master/CHANGELOG.txt).

== Upgrade Notice ==

= 1.3.0 =
1.3.0 is a major update so it is important that you make backups, and ensure themes and extensions are 1.3 compatible before upgrading.
