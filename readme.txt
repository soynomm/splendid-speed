=== Splendid Speed ===
Contributors: splendidwp
Tags: pagespeed, performance, webp, gzip, preload, inline
Requires at least: 5.6
Tested up to: 5.6
Stable tag: 1.3.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Splendid Speed improves your website performance and Google Pagespeed score with converting images to WebP, preloading pages, using Gzip and more.

== Description ==

Splendid Speed is a all-in-one WordPress performance plugin that allows anyone, even those who arent tech-savvy, to vastly improve the performance of their WordPress powered website. Splendid Speed improves your website performance and Google Pagespeed score with converting images to WebP, preloading pages, using Gzip and more.

= CACHE STATIC ASSETS =

Tell browsers to hold onto static assets such as images, stylesheets and scripts for longer. This will make your pages load faster for people who have already visited your website, because all the static assets have already been downloaded.

= CLEAN AUTO-DRAFTS =

Delete all automatically created drafts periodically every week. This helps keep your database size small. Auto-drafts are created automatically while you are editing a post or page, and over time this can clutter your database.

= CLEAN POST REVISIONS =

Delete all post revisions periodically every week. This helps keep your database size small. Note however that by doing so all old versions of content will disappear for good.

= CLEAN TRANSIENTS =

Delete all transient information periodically every week. Transients are used to temporarily store cached information by plugin developers and cleaning them helps keep your database small.

= CLEAN TRASH = 

Delete all trash periodically every week. This helps keep your database size small. Note however that by doing so all deleted content will disappear for good.

= CONVERT IMAGES TO WEBP = 

Convert all of your images to a more performant file format called WebP, which allows browsers to download your images faster.

= ZIP STATIC ASSETS =

Zip static assets with Gzip technology to reduce the size of your static assets, such as images, stylesheets and scripts. Smaller sizes of these assets allow the browser to load your website faster.

= PRELOAD ADMIN PAGES = 

Preload the pages of your WordPress admin panel right before you click on a link. Gives you much faster admin panel loading which enhances user experience.

= PRELOAD WEBSITE PAGES =

Preload the pages of your WordPress website right before you click on a link. Gives you much faster website loading which enhances user experience.

= INLINE CSS =

Inline all of your CSS to save the browser from making trips to each of the individual stylesheet files, thus making the page load faster.

= REMOVE QUERY STRINGS =

Removing query strings from your static assets helps the caching of your website to be more efficient, thus making the website faster.

= DEFER SCRIPTS =

Deferring non-essential scripts will allow your website to become visible even before all of the scripts on your website have loaded, allowing the visitors to start interacting with your website faster.

## Privacy Policy 
Splendid Speed uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users. 

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

== Frequently Asked Questions ==

= Changing my website CSS has no effect =

This is most likely because you have "Inline CSS" module turned on, which caches all CSS for greater performance. I advise that during development you turn that module off and once you've finished with your changes, turn it on again, as that will clear the cache and create it again.

= Converting images to WebP is very slow =

The speed at which the images are being converted to WebP depends entirely on the platform you host your website at, and how much performance capabilities that platform gives to your website. Getting on a more performing hosting plan would make WebP conversions faster.

= Does it really improve the Google PageSpeed score? =

All of the testing I have done concludes that in most cases the Google PageSpeed score improves, because problems like not using next-gen image formats (WebP) or not having good cache expiry settings on static assets are solved by the plugin, but sometimes it doesn't do much do the PageSpeed score, or even reduces it. So the answer is yes and no, in most cases it will improve the score, but sometimes it won't. Regardless of the score, your website is faster with the plugin, but if the PageSpeed score decreases you most likely have problems with your WordPress theme that my plugin simply cannot solve.

= I get an error after using Splendid Speed =

This may happen if you are running multiple performance plugins at the same time. Please deactivate other performance plugins to see which one clashes with Splendid Speed and consider not using it, or not using Splendid Speed.

== Screenshots ==

1. Simple and to the point configuration page.

== Changelog ==

= 1.3.1 =
* Fixed an issue in `alterImageSrc` of "Convert images to WebP".

= 1.3 =
* Fixed: Preload pages: instant.page script breaks other scripts. [#16](https://github.com/codebyflame/splendid-speed/issues/16)
* Implemented: Change details from splendidpress.com to flame.sh. [#15](https://github.com/codebyflame/splendid-speed/issues/15)
* Implemented: Add PHP7ness. [#8](https://github.com/codebyflame/splendid-speed/issues/8)
* Implemented: Remove Appsero. [#12](https://github.com/codebyflame/splendid-speed/issues/12)
* Implemented: Defer scripts with lower quality. [#5](https://github.com/codebyflame/splendid-speed/issues/5)
* Implemented: Delete cache on plugin update. [#7](https://github.com/codebyflame/splendid-speed/issues/7)

= 1.2.3 =
* Fixes an issue in Inline CSS where @import's weren't added to the top, and browsers require all @import's to precede any other CSS rules, so this broke things like imported google fonts or resets and any other stuff like that. 

= 1.2.2 =
* Fixes an issue in Inline CSS where the stylesheets were sometimes fetched in wrong order, resulting in a broken website.

= 1.2.1 =
* Fixes an issue where fetching CSS for Inline CSS would fetch HTML if that CSS no longer existed and redirected to a not found page, thus breaking CSS.
* Added a warning to the tooltip of Inline CSS to let users know it may not work on every site due to a combination of some plugins.
 
= 1.2 =
* Fixes an issue with WebP image conversion where sometimes it got given a image that didn't actually exist, resulting in an error.
* Updated admin page to be more minimal, with further description being in a tooltip now, available via the info icon of each module.
* Added a new module for deferring scripts.
* Added a new module for removing query strings.
* Added a new module for inline CSS. 

= 1.1.8 =
* Added support for caching webfonts and WebP images via "Cache static assets" module, as well as added longer cache expiration date for even better performance.

= 1.1.7 =
* Refactored how images are replaced with WebP in HTML as the HTML parsing method broke things in some custom page builders. This should no longer be an issue.

= 1.1.6 =
* Fixed an issue with "Convert images to WebP" module where it displayed an error trying to update URL's in content that had multiple `div`'s with the same ID, as that is invalid HTML. The plugin now ignores that rule.

= 1.1.5 =
* Fixed an issue with the "Preload admin pages" module showing an error when enabled.

= 1.1.4 = 
* Added Appsero SDK for collecting some analytics about the plugin usage to be able to make an ever better plugin. I respect your privacy so this is entirely opt-in.

= 1.1.3 =
* WebP image conversion now also changes the images to their WebP counterpart when called with `wp_get_attachment_image_src`, which means WooCommerce is also supported now.

= 1.1.2 =
* Replaces the unreliable PHP DOMDocument with Ivo Petkov's HTML5DOMDocument instead, for use within the image replacements in content part of Convert Images to WebP. 

= 1.1.1 =
* Fixes an issue where if WebP image conversions were turned on, all post and page content would end up with a wrong character encoding, thus showing the text in a broken way.

= 1.1 =
* Added a module for periodically cleaning auto-drafts.
* Added a module for periodically cleaning trashed posts.
* Added a module for converting images to WebP format.

= 1.0 =
* Initial release.