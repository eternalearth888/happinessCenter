<?php
/*
Plugin Name: SmoothGallery
Plugin URI: http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/
Description: Embed JonDesign's SmoothGallery into your posts and pages.
Version: 1.8.1
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
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

# Identifier for various actions of this script (e.g. CSS)
define('SMOOTHGALLERY_ACTION', 'smoothgallery_action');
# The CSS file
define('SMOOTHGALLERY_CSS_FILE', dirname(__FILE__).'/css/jd.gallery.css');
# Path to this plugin
define('SMOOTHGALLERY_URL', '/wp-content/plugins/smoothgallery');
# Include the custom configuration
require_once('config.php');


/**
 * Parses the actions
 */
if (!empty($_REQUEST[SMOOTHGALLERY_ACTION])) {
	switch ($_REQUEST[SMOOTHGALLERY_ACTION]) {
		case 'css':
			header('Content-type: text/css');
			$css = file_get_contents(SMOOTHGALLERY_CSS_FILE);
			$css = str_replace('<HEIGHT>', $_REQUEST['height'], $css);
			$css = str_replace('<WIDTH>', $_REQUEST['width'], $css);
			$css = str_replace('<BORDERCOLOR>', $_REQUEST['bordercolor'], $css);
			$css = str_replace('<URL>', $_REQUEST['prefix'].SMOOTHGALLERY_URL, $css);
			echo $css;
			# fall through
		default:
			die();
			break;
	}
}


/**
 * Returns the parameters for the gallery if they're attached to a post or a
 * page, otherwise it will return default values.
 */
function get_smoothgallery_parameters() {
	# default values
	$defaults = array('height' => 345,
	                  'width' => 460,
	                  'timed' => 'false',
	                  'delay' => 9000,
	                  'showInfopane' => 'true',
	                  'showArrows' => 'true',
	                  'showCarousel' => 'false',
	                  'embedLinks' => 'true',
	                  'bordercolor' => '000');

	$globalRet = get_smoothgallery_global_parameters($defaults);
	$metaRet = get_smoothgallery_metadata($defaults);

	if ($globalRet === false and $metaRet === false) return null;

	return $defaults;
}


/**
 * If there're global parameters for the current page we'll use them.
 * If there're also parameters in a custom field they'll overwrite these defaults.
 *
 * @param array $defaults the defaults (passed by reference)
 * @return bool true if there're global parameters, otherwise false
 */
function get_smoothgallery_global_parameters(&$defaults) {
	$parameters = insertSmoothGallery();
	if ($parameters === false) return false;

	foreach ($parameters as $key => $value) {
		$defaults[$key] = $value;
	}

	return true;
}


/**
 * Examines the metadata for the current post or page and changes the default
 * values accordingly.
 *
 * @param array $defaults the defaults (passed by reference)
 * @return bool true if we changed the default values from $defaults, otherwise false
 */
function get_smoothgallery_metadata(&$defaults) {
	global $post;
	if (isset($post) == false) return false;

	# get the post's metadata and change the default values accordingly
	$meta = get_post_meta($post->ID, 'smoothgallery', true);
	if (empty($meta)) return false;

	$meta = strtolower($meta);
	if ($meta != '1' and $meta != 'on') {
		# the user may use these keys
		$metaKeyMap = array('height' => array('h', 'height'),
		                    'width' => array('w', 'width'),
		                    'timed' => array('t', 'timed'),
		                    'delay' => array('d', 'delay'),
		                    'showInfopane' => array('i', 'info', 'showInfopane'),
		                    'showArrows' => array('a', 'arrows', 'showArrows'),
		                    'showCarousel' => array('c', 'carousel', 'showCarousel'),
		                    'embedLinks' => array('l', 'links', 'embedLinks'),
		                    'bordercolor' => array('b', 'border', 'bordercolor'));
		foreach ($metaKeyMap as $key => $value) {
			$param = get_smoothgallery_parameter($meta, $value);
			if ($param !== false) $defaults[$key] = $param;
		}
	}

	return true;
}


/**
 * Returns the value from a string in the following format:
 * '<$param><delimiter><value>        [...]\n
 *  <$anotherparam><delimiter><value> [...]'
 * where delimiter will be either ':' or '='.
 *
 * @param string $data the string with the data
 * @param array $params the parameter we're searching for
 * @return mixed the value from the string, otherwise false
 */
function get_smoothgallery_parameter($data, $params) {
	$metas = explode("\n", $data);
	foreach ($metas as $meta) {
		foreach ($params as $param) {
			if (preg_match('/'.$param.' *[:=]{1} *(\S*)/i', $meta, $matches)) {
				if (empty($matches[1])) continue;
				return rtrim($matches[1]);
			}
		}
	}
	return false;
}


/**
 * Adds a link to the CSS stylesheet in the header.
 */
function add_smoothgallery_css() {
	$parameters = get_smoothgallery_parameters();
	if (empty($parameters)) return;

	$height = $parameters['height'];
	$width = $parameters['width'];
	$bordercolor = $parameters['bordercolor'];
	$url = get_bloginfo('wpurl').SMOOTHGALLERY_URL.'/smoothgallery.php';
	echo '<link rel="stylesheet" type="text/css" href="'.$url.'?'.SMOOTHGALLERY_ACTION.'=css&amp;prefix='.urlencode(get_bloginfo('wpurl')).'&amp;height='.$height.'&amp;width='.$width.'&amp;bordercolor='.$bordercolor.'" />';
}
if (function_exists('add_action')) add_action('wp_head', 'add_smoothgallery_css');


/**
 * This will add the JavaScript to the footer.
 */
function add_smoothgallery_js() {
	$parameters = get_smoothgallery_parameters();
	if (empty($parameters)) return;

	$url = get_bloginfo('wpurl').SMOOTHGALLERY_URL;
	$namespaced = ((SMOOTHGALLERY_USE_NAMESPACED) ? '.namespaced' : '');
	?>

<script src="<?php echo $url; ?>/scripts/mootools<?php echo $namespaced; ?>.js" type="text/javascript"></script>
<script src="<?php echo $url; ?>/scripts/jd.gallery<?php echo $namespaced; ?>.js" type="text/javascript"></script>
<script type="text/javascript">
function startGallery() {
	var myGallery = new gallery($('myGallery'), {
		timed: <?php echo $parameters['timed']; ?>,
		delay: <?php echo $parameters['delay']; ?>,
		showInfopane: <?php echo $parameters['showInfopane']; ?>,
		showArrows: <?php echo $parameters['showArrows']; ?>,
		showCarousel: <?php echo $parameters['showCarousel']; ?>,
		embedLinks: <?php echo $parameters['embedLinks']; ?> });
}
window.addEvent('domready', startGallery);
</script> 

	<?php
}
if (function_exists('add_action')) add_action('wp_footer', 'add_smoothgallery_js');


/* Adds a custom section to the "advanced" Post and Page edit screens */
function smoothgallery_add_custom_box() {
	if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'myplugin_sectionid', 'SmoothGallery', 'smoothgallery_inner_custom_box', 'post', 'advanced' );
		add_meta_box( 'myplugin_sectionid', 'SmoothGallery', 'smoothgallery_inner_custom_box', 'page', 'advanced' );
	} else {
		add_action('dbx_post_advanced', 'smoothgallery_old_custom_box' );
		add_action('dbx_page_advanced', 'smoothgallery_old_custom_box' );
	}
}
if (function_exists('add_action')) add_action('admin_menu', 'smoothgallery_add_custom_box');

/* Prints the inner fields for the custom post/page section */
function smoothgallery_inner_custom_box() {
	# get the markup
	$markup = get_smoothgallery_markup();
	# if the markup starts with a 'T' instead of a '<' then there aren't any images
	$rows = ($markup[0] == 'T') ? 1 : 8;
?>
	<label for="smoothgallery_code">Copy this markup into your content.</label><br/><br/>
	<textarea name="smoothgallery_code" cols="64" rows="<?php echo $rows; ?>"><?php echo $markup; ?></textarea>
<?php
}

/* Prints the edit form for pre-WordPress 2.5 post/page */
function smoothgallery_old_custom_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="smoothgallery_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">SmoothGallery</h3></div>';

	echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

	// output editing form
	smoothgallery_inner_custom_box();

	echo "</div></div></fieldset></div>\n";
}

/**
 * Looks for pictures attached to the current post/page and generates markup for them.
 */
function get_smoothgallery_markup() {
	global $wpdb, $post;
	$sql = 'SELECT post_content AS description, post_title AS title, post_excerpt AS caption, guid AS url
	        FROM '.$wpdb->posts.'
	        WHERE post_parent = '.$post->ID.'
	              AND post_type = "attachment"';
	$images = $wpdb->get_results($sql);
	if (empty($images)) return "There aren't any images attached here.";

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

?>
