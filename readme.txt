=== CMB2 EXPANSION ===
Contributors:      bleumikee
Tags:              cmb2, metaboxes, expansion
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

CMB2 EXPANSION is a plugin which adds features to the plugin [CMB2](https://github.com/WebDevStudios/CMB2).

== Description ==

CMB2 EXPANSION is a plugin which adds features to the plugin [CMB2](https://github.com/WebDevStudios/CMB2).

### Features:

* .
* Responsive Tab Navigation for Metaboxes
* Adds show_on support for slug and front-page
* Adds repeat limit for fields AND groups (partial integration from [CMB2 Snippet Library](https://github.com/WebDevStudios/CMB2-Snippet-Library))
  * Add a repeat limit through when defining a CMB2 metabox or for individual fields/groups

== Installation ==

1. Place the CMB2 EXPANSION directory inside of your plugins directory (typically /wp-content/plugins).
2. Activate plugin through the Plugins Admin page

== Changelog ==

## 1.3 - 03-10-2017

### Enhancements
* Added opt-out options to features via the user profile panel

### Misc
* Compressed JS and CSS files via Prepros
* Split files for better organization

## 1.2.1 - 06-23-2016

### Bug Fixes
* Fix for exclusion by slug
* Removed tab nav from Edit Media (Attachment) screen

### 1.2 - 06-15-2016

#### Enhancements
* Use OOP for all features
* Namespace change
* Properly checks for CMB2 and deactivates if CMB2 is not found (throws warning alert when deactivation occurs)

#### Bug Fixes
* Tabs no longer have FOUC, now have same standard as WP tabs
* Check for Post Type name instead of just the slug
* Only load assets when CMB2 assets are loaded as well (no need for overloading admin when CMB2 wasnt even used on page/plugin)

### 1.1 - 05-02-2016

#### Enhancements
* Check if page/post supports Post Editor for displaying Post Editor Tab
* Add collapse of repeatable groups

#### Bug Fixes
* Target appropriate boxes when searching for limit for repeatable groups

### 1.0 - 11-20-2015

* Initial expansion release
