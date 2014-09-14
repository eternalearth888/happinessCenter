<?php
/*
 * Recent images box
 *
 * This code generates a SmoothGallery for the pictures attached to the most
 * recent posts. You can use the function directly somewhere in your theme or
 * use the filter inside a post or page. No matter how you decide, don't forget
 * to implement the "insertSmoothGallery" function OR, if you're using the
 * filter, add a custom field named "smoothgallery" - like always - to the post
 * that contains the identifier tag for the gallery.
 */

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#


/**
 * Looks for pictures attached to the most recent posts and generates markup
 * for them.
 *
 * @param $nr_of_recent_posts we're looking at this many recent posts
 * @returns string the markup or an HTML comment saying that nothing was found
 */
function insert_recent_images_box($nr_of_recent_posts = 3) {
	global $wpdb;
	$sql = 'SELECT post_content AS description, post_title AS title, post_excerpt AS caption, guid AS url
	        FROM '.$wpdb->posts.',
	             (SELECT id
	              FROM '.$wpdb->posts.'
	              WHERE post_type = "post"
	              ORDER BY post_date DESC
	              LIMIT '.$nr_of_recent_posts.') AS parent
	        WHERE parent.id = post_parent
	              AND post_type = "attachment"';
	$images = $wpdb->get_results($sql);
	if (empty($images)) return '<!-- No images found. -->';

	# FIXME: duplicate code...
	$markup .= '<div id="myGallery">'."\n";
	foreach ($images as $image) {
		$markup .= '<div class="imageElement">'."\n".
		           '<h3>'.$image->title.'</h3>'."\n".
		           '<p>'.$image->description.'</p>'."\n".
		           '<p><img src="'.$image->url.'" class="full" alt="'.$image->caption.'"/></p>'."\n".
		           '</div>'."\n";
	}
	$markup .= '</div>';
	return $markup;
}


/*
 * Although the user still needs to implement the "insertSmoothGallery"
 * function we'll make this available as a simple tag in the content. This way
 * it should be pretty easy to put a generated gallery in a post.
 */
function insert_recent_images_box_filter($content) {
	# TODO: maybe switch to shortcode...; although an HTML comment has the
	# advantage of being invisible if the replacement didn't work.
	$identifier = '<!--recent-images-box-->';
	# fail fast
	if (strpos($content, $identifier) === false) return $content;
	# do filtering
	$markup = insert_recent_images_box();
	return str_replace($identifier, $markup, $content);
}
if (function_exists('add_filter') and ENABLE_RECENT_IMAGES_BOX_FILTER) add_filter('the_content', 'insert_recent_images_box_filter');

?>
