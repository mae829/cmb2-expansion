# Changelog
All notable changes to this project will be documented in this file.

## 1.3 - 03-10-2017

### Enhancements
* Added opt-out options to features via the user profile panel

### Misc
* Compressed JS and CSS files via Prepros
* Split files for better organization

## 1.2 - 06-15-2016

### Enhancements
* Use OOP for all features
* Properly checks for CMB2 and deactivates if CMB2 is not found (throws warning alert when deactivation occurs)
* Tabs no longer have FOUC, now have same standard as WP tabs

### Bug Fixes
* Fix for exclusion by slug
* Removed tab nav from Edit Media (Attachment) screen
* Check for Post Type name instead of just the slug
* Only load assets when CMB2 assets are loaded as well (no need for overloading admin when CMB2 was not even used on page/plugin)

## 1.1 - 05-02-2016

### Enhancements
* Check if page/post supports Post Editor for displaying Post Editor Tab
* Add collapse of repeatable groups

### Bug Fixes
* Target appropriate boxes when searching for limit for repeatable groups

## 1.0 - 11-20-2015

* Initial expansion release
