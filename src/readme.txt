=== Archives Calendar Widget ===
Contributors: alekart
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4K6STJNLKBTMU
Tags: archives, calendar, widget, sidebar, view, plugin, monthly, daily
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: @@version
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Archives widget that makes your monthly/daily archives look like a calendar on the sidebar.

== Description ==

Archives widget that make your monthly/daily archives look like a calendar on the sidebar. If you have a lot of archives that takes a lot of space on your sidebar this widget is for you. Display your archives as a compact calendar, entirely customizable with CSS.

= **PLEASE FEEDBACK** =
I'm alone to test this plugin before release and I can't test everything with particular configurations or on different versions of WordpPress, **your feedback is precious.**

= Features =

* Theme Editor GUI (external tool)
* Displays monthly archives as a compact year calendar
* Displays daily archives as a compact month calendar
* Show/hide monthly post count
* 8 themes included (with SCSS files)
* 2 Custom themes
* Different theme for each widget
* Show widget with previous/current/next or last available month
* Category select. Show post only from selected categories
* Filter Archives page by categories you set in the widget
* Custom post_type partial* support
* Entirely customizable with CSS
* .PO/.MO Localisation English/Français + OUTDATED: [Deutsch, Español, Portugues, Simplified Chinese, Serbo-Croatian]
* CSS animations

**Custom taxonomies are not supported. If your custom post_type post has no common categories with post it will not be shown in the archives**

= ADDON =
**Popover Addon for Archives Calendar Widget**
[ARCW Popover Addon](https://wordpress.org/plugins/arcw-popover-addon/)

= ARCW THEME EDITOR =
[Create your own theme for the calendar](http://arcw.alek.be/)

= Notes =
Please use the Support section to report issues. **No support will be provided via email.**
**Be as accurate as you can to describe your problem**


== Installation ==

1. Upload `archives-calendar-widget` folder in `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Configure the plugin through "Settings > Archives Calendar" menu in WordPress.
4. Activate and configure the widget in "Appearance > Widgets" menu in WordPress

== Screenshots ==

1. Plugin settings
2. Calendar Theme selector with preview
3. Widget settings
4. Widgets with different themes on the same page

@@changelog

== Upgrade notice ==

= BREAKING CHANGES UPGRADING FROM v1.0.x TO v2.x.x=
* CUSTOM THEMES that were generated with the theme generator tool for the version 1.0.x wont work correctly
because of the changes made to HTML structure of the calendar.

== Frequently asked questions ==

= Custom taxonomies are not supported. =
NO. Currently only default categories are supported.
Custom post_type that do not have common categories with post will not be displayed in the calendar if categories filter is different of "ALL".
Don't ask me if it is possible, i'm thinking about it... need time...


= Can I show a popover with list of posts? =

Yes, with my Popover Addon (beta).
You can also do it with ajax request on day/month mouse over.
I don't want to make my plugin do everything like some softwares do (and that only 10% are used/usefull).