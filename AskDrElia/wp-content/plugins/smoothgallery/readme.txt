=== SmoothGallery ===
Contributors: chschenk
Donate link: http://www.christianschenk.org/donation/
Tags: jondesign, smoothgallery, gallery, pictures, images
Requires at least: 2.0
Tested up to: 2.5.1
Stable tag: 1.8.1

Embed JonDesign's SmoothGallery into your posts and pages.

== Description ==

Embed JonDesign's [SmoothGallery](http://smoothgallery.jondesign.net/) into your posts and pages.

It's this simple:

* insert some standards compliant markup
* add a custom field named "smoothgallery"
* watch your gallery ;-)

That's it.

You want to embed a SmoothGallery into your theme? No Problem: read this [page](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/integration-into-your-theme/)
or [watch](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/videos-for-this-plugin/) these videos.

== Installation ==

Before you begin with the installation read the [notes on compatibility](http://wordpress.org/extend/plugins/smoothgallery/other_notes/).

1. Unzip the plugin into your wp-content/plugins directory and activate it
2. [Integrate](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#howto) it into a post or your theme.
3. You don't want to read all the instructions? No problem: [watch](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/videos-for-this-plugin/) these videos.

== Frequently Asked Questions ==

= The visual editor replaces div-tags with p-tags =

If you're using the visual editor the div-tags will be replaced with
p-tags. To keep the markup intact you'll have to disable the visual
editor if you'd like to edit a post/page that contains a SmoothGallery.

To do this, go to Users -> Your profile and uncheck "Use the visual
editor when writing". Once you've saved and published the post/page you
can activate the visual editor again - but make sure to disable it again
if you want to change a post that contains a SmoothGallery. 

= The SmoothGallery doesn't show up at all =

If the CSS and JavaScript for the SmoothGallery aren't included make
sure that the following conditions are met:

1. there's a custom field named "smoothgallery" attached to the post or page that holds the markup for the SmoothGallery
2. your theme has got a call to "wp_head()" in the header and a call to "wp_footer()" in the footer

If you're using JavaScript libraries like Prototype, read the next entry, too.

= The SmoothGallery doesn't show up in Internet Explorer =

SmoothGallery doesn't seem to work with Internet Explorer if you use [Prototype](http://www.prototypejs.org/) on your site.

Read about a solution [here](http://www.christianschenk.org/blog/integrate-smoothgallery-into-wordpress/).
Basically you'll have to make sure that you don't use Prototype and SmoothGallery on the same site.

= What about integrating it into my theme? =

Read about this [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/integration-into-your-theme/).

== Screenshots ==

1. Have a look at my gallery [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#example).

== Changelog ==

1.8.1

* It's possible to specify the color of the border for the gallery.

1.8

* The custom configuration resides in a spearate file ("config.php") now to ease updating.
* A new feature called "recent images box" picks up the images that are attached to the most recent posts and generates a SmoothGallery for you. Read about this [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/recent-images-box/).

1.7

* If you attach images to a post/page the markup will be generated for you. Have a look at the "SmoothGallery" box under "Advanced Options" on the edit screen. Check out this [video](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/videos-for-this-plugin/#markup).  

== Compatibility ==

SmoothGallery doesn't seem to work with Internet Explorer if you use
[Prototype](http://www.prototypejs.org/) on your site. Read about a
solution [here](http://www.christianschenk.org/blog/integrate-smoothgallery-into-wordpress/).

== Licence ==

This plugin is released under the GPL.
