<?php
/*
 * We keep the configuration in a separate file to ease updating: if you plan
 * to update the plugin simply put this file aside, copy the new files over the
 * old ones and put this config back into place and your settings won't be lost.
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


# This must be set to true if your site uses "prototype.js", otherwise false
define('SMOOTHGALLERY_USE_NAMESPACED', false);

#
# Extras
# 
# Recent images box
# This enables the code
define('ENABLE_RECENT_IMAGES_BOX', false);
# This enables the filter that may be used inside a posts content
define('ENABLE_RECENT_IMAGES_BOX_FILTER', false);
if (ENABLE_RECENT_IMAGES_BOX) include_once('extra/recent_images_box.php');


/**
 * If you want to integrate the SmoothGallery into your theme, you can
 * implement this method and set the default values for the gallery
 * accordingly.
 *
 * You may want to use "Conditional Tags":
 * -> http://codex.wordpress.org/Conditional_Tags
 *
 * @return mixed false if we don't want to set global parameters for the gallery, otherwise an array
 */
function insertSmoothGallery() {
	return false;
}

?>
