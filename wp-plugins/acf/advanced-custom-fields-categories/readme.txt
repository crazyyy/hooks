=== Advanced Custom Fields Categories ===
Contributors: Cubeweb
Tags: Advanced Custom Fields
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Categories/Taxonomies addon for the AFC Wordpress plugin http://www.advancedcustomfields.com

== Description ==

Categories/Taxonomies addon for the AFC Wordpress plugin http://www.advancedcustomfields.com

= Options =
* Post Type
* Child Of
* Parent
* Order By
* Order
* Hide Empty
* Hierarchical
* Taxonomy
* Include Categories
* Exclude Categories
* Multiple Values
* Start State
* Display Posts Count

= Upgrade notice =
**Requires ACF 4.0 and above**

= Usage =
Use get_field function or get_sub_field function if inside a repeater field. Returns taxonomy object.

== Installation ==

* Upload acf-categories folder to the `wp-content/plugins` directory
* Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

Please report requests on https://github.com/cubeweb/acf-categories/issues?state=open

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png

== Changelog ==

= 2.0.0.7 =
* [Updated] Compatibility for WordPress 3.9.1

= 2.0.0.6 =
* [Fixed] Warning thrown on categories-v4 - in_array on line 152, thanks to https://github.com/americandriversafety
* [Added] Category link in post count indicator

= 2.0.0.5 =
* [Updated] Compatibility for WordPress 3.7.1

= 2.0.0.4 =
* [Updated] Compatibility for WordPress 3.6.1

= 2.0.0.3 Beta =
* [Fixed] Sometimes returns false when in repeater field
* [Added] Display Posts Count Option

= 2.0.0.2 Beta =
* [Removed] Chosen plug-in. After a talk with the chosen team I understood that chosen will not work correctly inside the repeater field.
* [Removed] Mp6 admin theme option. Since there is no chosen anymore there is no reason for that option.
* [Added] Multiple checkboxes
* [Added] Multiple: Select All button
* [Added] Multiple: Clear All button
* [Added] Multiple: Select Select Main Categories button
* [Added] Multiple: Show/Hide Categories button

= 2.0.0.1 Beta =
* [Added] mp6 admin theme option

= 2.0.0.0 Beta =
* [Added] Compatibility with ACF 4.0
* [Added] Chosen support

== Upgrade notice ==

**Requires ACF 4.0 and above**

== Arbitrary section ==

*  Requires ACF 4.0 and above**
*  Please report any bugs or requests on https://github.com/cubeweb/acf-categories/issues?state=open**