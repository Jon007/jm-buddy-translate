=== JM's Buddy Translate for WordPress  ===
Plugin Name: JM Buddy Translate
Plugin Slug: jm-buddy-translate
Text Domain: jm-buddy-translate
Domain Path: /languages
Author: Jonathan Moore
Author URI: https://jonmoblog.wordpress.com/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Plugin URI: https://github.com/Jon007jm-buddy-translate/
Assets URI: https://github.com/Jon007jm-buddy-translate/assets/
Description: Adds a translate menu item in the WordPress admin back-end admin and front-end toolbar menus which translates selected text to current locale.
Tags: user, locale, language, translate, back-end, front-end, buddypress
Contributors: jsmoriss
Requires At Least: 4.7
Tested Up To: 4.7.3
Stable Tag: 1.0.3

Quick and easy translate your buddypress messages.

== Description ==

Adds a translate menu item in the WordPress admin back-end admin and front-end toolbar menus which translates selected text to current locale.

Perfect for a multilingual Buddypress site, this tool allows you to translate messages in other languages to your own language.
Other tools translate the whole site but a polyglots and language learner may not want this, for learning it’s best to see the original, and translate only where necessary.
Or the whole site may be already translated to your preferred language but you are getting messages from other users (or from some untranslated plugin) and you want to translate those.

Currently uses Google Translate.

There are no plugin settings - simply install and activate the plugin.

= Power-Users / Developers =

Each element in the popup translation window has a separate id so it can be restyled easily.

= Do you use the BuddyPress plugin? =

If the BuddyPress plugin is active, the tool will attempt to add translate options to the BuddyPress messages.

= Recommended Plugins: =

This tools is tested with and recommended for:
BuddyPress Version 2.8.2
bbPress Version 2.5.12
The tool is intended to add translate links for messages and docs with these tools.
For other inputs, the Admin Bar translate button still allows translation of selected text.

Also highly recommended:
JSM's User Locale Version 1.2.1-1

The translation tool uses User Locale as the target language: if you enable JSM’s User Locale, you can quickly change the front-end language, which also become the target language for translations.


== Installation ==

= Automated Install =

1. Go to the wp-admin/ section of your website.
1. Select the *Plugins* menu item.
1. Select the *Add New* sub-menu item.
1. In the *Search* box, enter the plugin name.
1. Click the *Search Plugins* button.
1. Click the *Install Now* link for the plugin.
1. Click the *Activate Plugin* link.

= Semi-Automated Install =

1. Download the plugin archive file.
1. Go to the wp-admin/ section of your website.
1. Select the *Plugins* menu item.
1. Select the *Add New* sub-menu item.
1. Click on *Upload* link (just under the Install Plugins page title).
1. Click the *Browse...* button.
1. Navigate your local folders / directories and choose the zip file you downloaded previously.
1. Click on the *Install Now* button.
1. Click the *Activate Plugin* link.

== Frequently Asked Questions ==

= Frequently Asked Questions =

* Internet Explorer may warn/fail on being unable to check certficate revocation for googleapis.com
It is possible to accept and continue or to turn off certificate revocation check
* Microsoft Edge may give XMLHttpRequest error for the same reasons

It's preferable to use Chrome, Firefox, Safari, Opera etc to avoid problems with Microsoft browsers.

== Other Notes ==

= Additional Documentation =

**Developer Filters**

TODO: coming soon..

== Screenshots ==

see assets directory

== Changelog ==

= Repositories =

* [GitHub](https://github.com/Jon007jm-buddy-translate/)
* [WordPress.org](TODO: coming soon..)

= Version Numbering Scheme =

Version components: `{major}.{minor}.{bugfix}-{stage}{level}`

* {major} = Major code changes / re-writes or significant feature changes.
* {minor} = New features / options were added or improved.
* {bugfix} = Bugfixes or minor improvements.
* {stage}{level} = dev &lt; a (alpha) &lt; b (beta) &lt; rc (release candidate) &lt; # (production).

Note that the production stage level can be incremented on occasion for simple text revisions and/or translation updates. See [PHP's version_compare()](http://php.net/manual/en/function.version-compare.php) documentation for additional information on "PHP-standardized" version numbering.

= Changelog / Release Notes =
**Version 1.0.1 (2017/04/05)**
* *Bugfixes*
	* removed JS parameter default values for Internet Explorer compatibility

**Version 1.0 (2017/04/05)**

* *New Features*
	* First version
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* None


== Upgrade Notice ==
= 1.0.4 = 
(2017/04/08) Minor fixes:
- updated Settings screen to use WP settings api
- fixes for change to google results format
- improved default message and readability styling
- Languages: updated Spanish, Chinese, added French

= 1.0.2 = 
(2017/04/06) Minor fixes:
- Change to load sequence for admin views due to some "headers already sent" type issues.
- removed https from google translate link to allow use in non-SSL site

= 1.0.1 =
(2017/04/05) Patching for Internet Explorer compatibility.

= 1.0 =
(2017/04/04) Initial release.
