<?php
/*
Plugin Name: WordPress Tweaks
Plugin URI: http://wordpress.jdwebdev.com/plugins/tweaks/
Description: Adds a variety of useful options and settings, accessible at <a href="options-general.php?page=tweaks">Settings &gt; Tweaks</a>.
Version: 1.8
Author: John Lamansky
Author URI: http://wordpress.jdwebdev.com
*/

/*
Copyright (c) 2008 John Lamansky

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/************
* CONSTANTS *
************/

//Set this to 0 to disable CSS styling for the tweak counter in the footer

define("JL_WPT_FOOTER_COUNT_CSS", 1);



/*******************
* JavaScript & CSS *
*******************/

if (isset($_GET['js'])) {
	jl_wpt_javascript();
	die();
} elseif(isset($_GET['css'])) {
	jl_wpt_css();
	die();
} else {
	$jl_wpt = new JL_WordPress_Tweaks();
}



/*************
* MAIN CLASS *
*************/

class JL_WordPress_Tweaks {

	/*******************
	* GLOBAL VARIABLES *
	*******************/

	var $tweaks = array();
	var $oldtweaks = array();

	var $textdomain = "tweaks";
	var $submitted_field_name = "jl_wpt_submitted";

	var $options = array(
		'Admin' =>		array(	'admin_scroll_to_editor' => array(
							'title' => 'Automatically scroll to the post editor',
							'info'  => 'If you&#8217;d like the post content editor to be more front-and-center, enabling this tweak will scroll the page down towards the editor when you load the &#8220;Write Post&#8221; or &#8220;Write Page&#8221; sections. Based on the <a href="http://wordpress.org/extend/plugins/writescroll/" target="_blank">WriteScroll</a> plugin by Dougal Campbell.'),
						'tag_autocomplete_disable' => array(
							'title' => 'Disable tag autocomplete',
							'info'  => 'The tagging autocomplete function can slow you down if you have a large number of tags. This tweak will disable it. (Only applies to WordPress 2.5 and above. Based on the <a href="http://wordpress.org/extend/plugins/tag-uncomplete/" target="_blank">Tag Uncomplete</a> plugin by Alex King.)'),
						'admin_disable_dashboard' => array(
							'title' => 'Disable the Dashboard',
							'info'  => 'Hides and disables the Dashboard for all users except those with the view_dashboard capability.'),
						'admin_disable_flash_uploader' => array(
							'title' => 'Disable the Flash uploader',
							'info'  => 'You can disable the Flash-based media uploader if it&#8217;s giving you problems. (Only applies to WordPress 2.5 and above.)'),
						'admin_remove_maxwidth' => array(
							'title' => 'Remove the width restraint on administration pages',
							'info'  => 'With the new admin interface introduced in WordPress 2.5, the content of the administration webpages is limited to 980 pixels wide. If you&#8217;d like to take advantage of your entire screen real estate, enable this tweak. (Only applies to WordPress 2.5 and above. Based on the <a href="http://wordpress.org/extend/plugins/remove-max-width/" target="_blank">Remove Max Width</a> plugin by Dion Hulse.)')),
		'Comments and Pings' =>	array(	'ping_noself' => array(
							'title' => 'Disable self-pinging',
							'info'  => 'Enable this tweak to stop WordPress from sending pings to your own site. (Based on the <a href="http://wordpress.org/extend/plugins/no-self-ping/" target="_blank">No Self Pings</a> plugin by Michael D. Adams.)'),
						'comment_targetblank' => array(
							'title' => 'Open external comment links in new windows',
							'info'  => 'Searches comments for links to webpages outside your domain name and adds <tt>target="_blank"</tt> using XHTML Strict-compliant JavaScript.'),
						'comment_reverse' => array(
							'title' => 'Show comments in reverse order',
							'info'  => 'Usually this results in the newest comments being shown first. Note that the comment numbering may not be reversed, depending on your theme.')),
		'Posts' =>		array(	'post_targetblank' => array(
							'title' => 'Open external post links in new windows',
							'info'  => 'Searches posts for links to webpages outside your domain name and adds <tt>target=&quot;_blank&quot;</tt> using XHTML Strict-compliant JavaScript.'),
						'post_excerpt_readmore' => 'When displaying post excerpts on archive pages, display a &#8220;Read more &raquo;&#8221; link instead of &#8220;[...]&#8221;'),
		'Nofollow' =>		array(	'comment_popup_link_nofollow' => array(
							'title' => 'Add to post comment links',
							'info'  => '(Requires WordPress 2.5 or above.)'),
						'post_morelink_nofollow' => 'Add to &#8220;Read more&#8221; links',
						'tag_cloud_nofollow' => array(
							'title' => 'Add to tag cloud links',
							'info'  => '(Only applies to WordPress 2.3 or above.)'),
						'meta_nofollow' => array(
							'title' => 'Add to the &#8220;Register&#8221; and &#8220;Login&#8221; links',
							'info'  => 'Enable this tweak to stop needless sitewide PageRank flow to your registration and login pages.'),
						'comment_author_dofollow' => 'Remove from comment author links',
						'comment_body_dofollow' => 'Remove from comment body links'),
		'SEO' =>		array(	'widget_tags_homeonly' => array(
							'title' => 'Only show the tag cloud widget on the homepage',
							'info'  => 'If you have many tags, only showing the tag cloud widget on the homepage can decrease your sitewide links-per-page counts.'),
						'archive_excerpts' => array(
							'title' => 'Show post excerpts (instead of full content) on archive pages',
							'info'  => 'Helps avoid duplicate content issues in search engines. Also decreases the size of your archive pages, which can ease visitor navigation and decrease download times.')),
		'Security' =>		array(	'security_plugins_indexhtml' => array(
							'title' => 'Disable directory listing for my plugins folder',
							'info'  => 'Adds an empty index.html file to the <a href="../wp-content/plugins/" target="_blank">wp-content/plugins/</a> folder, which should prevent the contents of that folder from being listed by the server. The intention is to make it more difficult for hackers to detect exploitable plugins running on your blog. (This tweak may not work with certain server configurations.)'),
						'security_hide_wp_version' => array(
							'title' => 'Hide WordPress&#8217;s version number from my theme and feeds',
							'info'  => 'By default, WordPress advertises your WordPress version number both in your feeds and via hidden header code in your theme. Disabling this behavior may help protect you against some version-specific security flaw exploitations. (Of course, this is no replacement for keeping WordPress up-to-date.)')),
		'Theme and Appearance'=>array(	'theme_favicon_link' => array(
							'title' => 'Add code references to favicon.ico',
							'info'  => 'To enable a <a href="http://en.wikipedia.org/wiki/Favicon" target="_blank">favicon</a> for your blog, upload an icon file named <tt>favicon.ico</tt> to the root of your domain name, and then enable this tweak to add the proper HTML code to your blog. (Only enable this tweak if the favicon has already been uploaded.)'),
						'page_list_nospace' => array(
							'title' => 'Remove white space from pages list',
							'info'  => 'This will remove the space character between each list item that you may see if your pages list is rendered as a horizontal menu.')),
		'WordPress 2.3 Legacy Fixes'=>array('admin_menu_rename_blogroll' => array(
							'title' => 'Rename &#8220;Blogroll&#8221; admin menu item to &#8220;Links&#8221;',
							'info' => 'Enable this if the Blogroll terminology in WordPress 2.3&#8217;s administration menu annoys you. (Issue resolved in WordPress 2.5.)'),
						'fix_blogpage_cpi' => array(
							'title' => 'Fix: current_page_item on page_for_posts',
							'info' => 'When using a static page to show blog posts, use the &#8220;current_page_item&#8221; CSS class when that page is showing. (View this <a href="http://trac.wordpress.org/ticket/2959" target="_blank">bug ticket</a> for more information. The bug was fixed in WordPress 2.5.)')),
		'Plugin Settings' =>	array(	'wpt_footer_count' => array(
							'title' => 'Enable tweaks counter',
							'info'  => 'Show off my tweaks count in my blog&#8217;s footer.'))
	);

	var $toggles = array('security_plugins_indexhtml');

	var $plugin_path;
	var $plugin_url;

	/*******
	* INIT *
	*******/

	function __construct() {
		$this->JL_WordPress_Tweaks();
	}

	function JL_WordPress_Tweaks() {

		$this->load_plugin_data();

		update_option('jl_wpt_version', $this->get_plugin_data('version'));

		$this->plugin_path = PLUGINDIR.'/'.plugin_basename(__FILE__);
		$this->plugin_dir = dirname($this->plugin_path);
		$this->plugin_url = trailingslashit(get_bloginfo('wpurl')) . $this->plugin_path;

		register_activation_hook(__FILE__, array($this, 'activate'));

		if ($this->options_page_submitted()) {
			//Update options
			$option_values = array();
			foreach ($this->options as $options_in_cat) {
				foreach ($options_in_cat as $option_key => $option_text) {
					$option_values[$option_key] = ($_POST[$option_key] == 'on');
				}
			}
			$this->tweaks = $option_values;
		} else {
			$ta = $this->get_tweaks_array();
			if ( !isset($ta['wpt_footer_count']) ) {
				$ta['wpt_footer_count'] = true;
				update_option('jl_wpt_tweaks', serialize($ta));
			}
			$this->tweaks = $ta;
		}

		add_action('init', array($this, 'textdomain_init'));
		add_action('admin_menu', array($this, 'add_the_options_page'));
		add_action('admin_head', array($this, 'options_page_css'));

		$filters =array('admin_disable_flash_uploader' => 'flash_uploader',
				'admin_remove_maxwidth_2' => 'tiny_mce_before_init',
				'archive_excerpts' => 'the_content',
				'comment_author_dofollow' => 'get_comment_author_link',
				'comment_body_dofollow' => 'get_comment_text',
				'comment_popup_link_nofollow' => 'comments_popup_link_attributes',
				'comment_reverse' => 'comments_array',
				'fix_blogpage_cpi' => 'wp_list_pages',
				'meta_nofollow' => array('register', 'loginout'),
				'post_excerpt_readmore' => 'the_excerpt',
				'post_morelink_nofollow' => 'the_content',
				'page_list_nospace' => 'wp_list_pages',
				'tag_cloud_nofollow' => 'wp_tag_cloud',
				'security_hide_wp_version' => 'the_generator',
				'widget_tags_homeonly' => 'option_sidebars_widgets');
		$actions =array('admin_disable_dashboard_1' => 'admin_menu',
				'admin_disable_dashboard_2' => 'load-index.php',
				'admin_menu_rename_blogroll' => '_admin_menu',
				'admin_remove_maxwidth_1' => 'admin_head',
				'admin_scroll_to_editor' => 'admin_head',
				'comment_targetblank' => 'wp_footer',
				'ping_noself' => 'pre_ping',
				'post_targetblank' => 'wp_footer',
				'tag_autocomplete_disable' => 'admin_head',
				'theme_favicon_link' => 'wp_head',
				'wpt_footer_count' => 'wp_footer');
		$direct = array();
		$args = array(	'security_hide_wp_version' => 2);
		$priority = array( 'admin_remove_maxwidth_1' => 99 );

		foreach ($filters as $tweak => $filterArray) {
			if (!is_array($filterArray)) $filterArray = array($filterArray);
			foreach ($filterArray as $filter) {
				if ($this->is_tweak_enabled(rtrim($tweak, "_123"))) {
					if (!($arg = $args[$tweak])) $arg = 1;
					if (!($p = $priority[$tweak])) $p = 10;
					add_filter($filter, array($this, $tweak), $p, $arg);
				}
			}
		}

		foreach ($actions as $tweak => $actionArray) {
			if (!is_array($actionArray)) $actionArray = array($actionArray);
			foreach ($actionArray as $action) {
				if ($this->is_tweak_enabled(rtrim($tweak, "_123"))) {
					if (!($arg = $args[$tweak])) $arg = 1;
					if (!($p = $priority[$tweak])) $p = 10;
					add_action($action, array($this, $tweak), $p, $arg);
				}
			}
		}

		foreach ($direct as $tweak) {
			if ($this->is_tweak_enabled(rtrim($tweak, "_123"))) {
				call_user_func(array($this, $tweak));
			}
		}

		if ( $this->is_tweak_enabled('post_targetblank') || $this->is_tweak_enabled('comment_targetblank') ) {
			add_action('wp_head', array($this, 'include_targetblank_javascript'));
		}

		if ($this->is_tweak_enabled('wpt_footer_count') && JL_WPT_FOOTER_COUNT_CSS == 1) {
			add_action('wp_head', array($this, 'include_footer_count_css'));
		}
	}

	function activate() {
		if (count($this->get_tweaks_array()) == 0) {
			update_option('jl_wpt_tweaks', serialize(array('wpt_footer_count' => true)));
		}
	}


	function textdomain_init() {
		load_plugin_textdomain($this->textdomain, $this->plugin_dir);
	}


	/*****************************
	* WP COMPATIBILITY FUNCTIONS *
	*****************************/

	function is_homepage() {
		if (function_exists('is_front_page'))
			$ifp = is_front_page();
		else
			$ifp = false;

		return (is_home() || $ifp);
	}


	/*********************
	* SETTINGS FUNCTIONS *
	*********************/

	var $plugin_data = array();

	function load_plugin_data() {

		$keys = array('Plugin Name', 'Plugin URI', 'Description', 'Author', 'Author URI', 'Version');
		$data = array();

		$plugin_data = implode('', file(__FILE__));

		foreach ($keys as $key) {
			preg_match("|$key: (.*)|i", $plugin_data, $value);
			$data[strtolower($key)] = $value[1];
		}

		$this->plugin_data = $data;
	}

	function get_plugin_data($key) {
		return $this->plugin_data[strtolower($key)];
	}

	function get_tweaks_array() {
		if ($tweaks = get_option('jl_wpt_tweaks'))
			return maybe_unserialize($tweaks);
		else
			return array();
	}

	function is_tweak_enabled($key) {
		if (isset($this->tweaks[$key]))
			return $this->tweaks[$key];
		else
			return false;
	}

	function was_tweak_enabled($key) {
		if (isset($this->oldtweaks[$key]))
			return $this->oldtweaks[$key];
		else
			return false;
	}

	function get_tweak_count() {
		return count(array_filter($this->tweaks));
	}

	/*********************
	* ADMIN OPTIONS PAGE *
	*********************/

	function add_the_options_page() {
		add_options_page(__('WordPress Tweaks', $this->textdomain), __('Tweaks', $this->textdomain), 'manage_options', 'tweaks', array($this, 'show_options_page'));
	}

	function options_page_css() {
		$this->include_css('admin');
	}

	function show_options_page() {

		if ( !current_user_can('manage_options') ) wp_die(__("Insufficient user privileges.", $this->textdomain));

		if ($this->options_page_submitted()) {

			check_admin_referer('jl-wpt-update-options');

			$this->oldtweaks = $this->get_tweaks_array();

			update_option('jl_wpt_tweaks', serialize($this->tweaks));

			foreach ($this->toggles as $tweak) {
				if ($this->is_tweak_enabled($tweak) !== $this->was_tweak_enabled($tweak)) {
					if ($this->is_tweak_enabled($tweak))
						call_user_func(array($this, $tweak.'_enable'));
					else
						call_user_func(array($this, $tweak.'_disable'));
				}
			}

			echo '<div id="message" class="updated fade"><p><strong>' . __('Options saved.', $this->textdomain) . '</strong></p></div>';
		}
	?>
	<div id="jl_wpt_settings" class="wrap">
	<h2><?php printf(__("WordPress Tweaks %s", $this->textdomain), $this->get_plugin_data('version')); ?></h2>
	<div id="poststuff">
	<form name="jl_wpt_options" method="post" action="">
		<input type="hidden" name="<?php echo $this->submitted_field_name; ?>" value="Y" />
	<?php
		foreach ($this->options as $option_cat => $options_in_cat) {
			echo "\t<h3>".__($option_cat, 'jl_wpt')."</h3>\n\t<table>";

			foreach ($options_in_cat as $option_key => $option_data) {
				echo "\t\t<tr valign='top'>\n";
				echo "\t\t\t<td><input type='checkbox' name='$option_key' id='$option_key' ";
				if ($this->is_tweak_enabled($option_key)) echo "checked='checked' ";
				echo "/></td>\n\t\t\t<td><p>";

				if (is_array($option_data)) {
					$title = __($option_data['title'], $this->textdomain);
					$info  = __($option_data['info'], $this->textdomain);
				} else {
					$title = __($option_data, $this->textdomain);
					$info = '';
				}

				echo "<span class='title'>$title</span>";
				if ($info) echo "<br /><span class='info'>$info</span>";

				echo "</p></td>\t\t</tr>\n";
			}

			echo "\t</table>\n";
		}
	?>
		<p id="jl_wpt_submit_button"><span class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Options', $this->textdomain) ?>" />
		</span></p>
		<?php wp_nonce_field('jl-wpt-update-options'); ?>
	</form>
	</div></div>
	<?php
	}

	function options_page_submitted() {
		return ($_POST[$this->submitted_field_name] == "Y");
	}


	/***************
	* FOOTER COUNT *
	***************/

	function wpt_footer_count() {
		if ($this->is_homepage()) {
			$count = $this->get_tweak_count() - 1; //Don't count the footer count itself in the tweaks count!
			if ($count > 0) {
				$pluginUri = $this->get_plugin_data('Plugin URI');

				$s = 'This blog has been fine-tuned by %1$s %2$sWordPress Tweak';
				$p = $s.'s%3$s';
				$s .= '%3$s';

				$format = __ngettext($s, $p, $count, $this->textdomain);

				printf("<div id='tweaks'>$format</div>", $count, "<a href='$pluginUri'>", "</a>");
			}
		}
	}

	function include_footer_count_css() {
		if ($this->is_homepage()) $this->include_css('footer_count');
	}


	/***************
	* HEADER LINKS *
	***************/

	function include_targetblank_javascript() {
		$this->include_javascript('targetblank');
	}

	//Put JavaScript and CSS links
	function include_javascript($name) {
		echo '<script type="text/javascript" src="'.$this->plugin_url.'?js='.$name.'"></script>'."\n";
	}

	function include_css($name) {
		echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->plugin_url.'?css='.$name.'" />'."\n";
	}


	/*************************
	* SHARED TWEAK FUNCTIONS *
	*************************/


	function add_nofollow($html) {

		// Note: this function is very basic, and assumes that:
		// 1. "nofollow" is only used in the "rel" attribute, and not in the anchor text
		// 2. A "rel" attribute doesn't already exist

		if (strpos($html, 'nofollow') === false)
			return str_replace('<a href=', '<a rel="nofollow" href=', $html);
		else
			return $html;

	}

	function remove_nofollow($html) {
		return eregi_replace(	'<a ([^>]*)rel=(["\'])([a-zA-Z0-9_ -]*)nofollow([a-zA-Z0-9_ -]*)(["\'])([^>]*)>',
					'<a \\1rel=\\2\\3\\4\\5\\6>', $html);
	}

	function indexhtml($path, $enable) {

		$file = trailingslashit($path).'index.html';

		if ($enable) {
			if (!file_exists($file)) {
				$fh = @fopen($file, 'w');
				if ($fh) fclose($fh);
			}
		} else {
			if (file_exists($file) && filesize($file) === 0) {
				unlink($file);
			}
		}
	}


	/******************
	* TWEAK FUNCTIONS *
	******************/

	function admin_disable_dashboard_1() {

		if (!current_user_can('view_dashboard')) {
			global $menu, $submenu;

			if ($menu[0][2] == 'index.php') {
				unset($menu[0]);
				unset($submenu['index.php']);
			}
		}
	}

	function admin_disable_dashboard_2() {

		if (!current_user_can('view_dashboard')) {
			global $menu;
			foreach ($menu as $menuItem) {
				if ($menuItem[2] != 'index.php') {
					wp_redirect(get_option('siteurl') . '/wp-admin/' . $menuItem[2]);
					exit;
				}
			}
		}
	}

	function admin_disable_flash_uploader() {
		return false;
	}

	function admin_menu_rename_blogroll() {
		global $menu, $submenu;
		if ($menu[20] == array(__('Blogroll'), 'manage_links', 'link-manager.php')) {
			$menu[20][0] = __('Links', $this->textdomain);

			if ($submenu['link-manager.php'][5] == array(__('Manage Blogroll'), 'manage_links', 'link-manager.php'))
				$submenu['link-manager.php'][5][0] = __('Manage Links', $this->textdomain);
		}
	}

	function admin_remove_maxwidth_1() {
		$this->include_css('admin_remove_maxwidth');
	}

	function admin_remove_maxwidth_2($init) {
		$init['theme_advanced_resize_horizontal'] = true;
		return $init;
	}

	function admin_scroll_to_editor() {
		global $pagenow;
		if ('post-new.php' == $pagenow || 'post.php' == $pagenow || 'page-new.php' == $pagenow || 'page.php' == $pagenow)
			$this->include_javascript('admin_scroll_to_editor');
	}

	function archive_excerpts($content) {
		if (is_archive()) {
			$content = strip_tags($content);
			$maxWords = 55;

			$words = explode(" ", $content);
			if (count($words) > $maxWords) {
				$excerpt = implode(" ", array_slice($words, 0, $maxWords)) . " [...]";
				if ($this->is_tweak_enabled('post_excerpt_readmore'))
					return $this->post_excerpt_readmore($excerpt);
				else
					return $excerpt;
			}
		}

		return $content;
	}

	function comment_author_dofollow($link) {
		return $this->remove_nofollow($link);
	}

	function comment_body_dofollow($comments) {
		return $this->remove_nofollow($comments);

	}

	function comment_popup_link_nofollow() {
		return ' rel="nofollow" ';
	}

	function comment_reverse($comments) {
		return array_reverse($comments);
	}

	function comment_targetblank() {
		if (is_single()) {
			$this->include_javascript('comment_targetblank');
		}
	}

	function fix_blogpage_cpi($pagesList) {
		//If we're using a static page for blog posts, and if we're there...
		if (get_option('page_for_posts') && is_home()) {

			//Get the page object
			$pageForPosts = get_page(get_option('page_for_posts'));

			//Escape the dots in the URL so they don't mess up the regex
			$safeGuid = str_replace(array('.'), array('\\.'), $pageForPosts->guid);

			//Add the CSS class and return
			return eregi_replace(	'<li([^>]*) class="([a-zA-Z0-9_ -]+)"([^>]*)>([\s]*)<a href="'.$safeGuid.'"',
						'<li\\1 class="\\2 current_page_item"\\3>\\4<a href="'.$safeGuid.'"',
						$pagesList);
		} else {
			return $pagesList;
		}
	}

	function post_excerpt_readmore($excerpt) {
		if (is_archive() || is_search()) {
			global $post;
			$permalink = $post->guid;

			$excerpt = rtrim($excerpt);

			if ($this->is_tweak_enabled('post_morelink_nofollow')) $nofollow=' rel="nofollow"'; else $nofollow='';
			$morelink = '<a href="'.$permalink.'" class="more-link"'.$nofollow.'>'.__('Read more &raquo;', $this->textdomain).'</a>';

			$excerpt = ereg_replace("</p>$", " $morelink</p>", $excerpt);
			$excerpt = ereg_replace("\[&#8230;\] $morelink</p>$", "&#8230; $morelink</p>", $excerpt);
		}

		return $excerpt;
	}

	function meta_nofollow($html) {
		return $this->add_nofollow($html);
	}

	function post_targetblank() {
		$this->include_javascript('post_targetblank');
	}


	// The function below was adapted on June 6, 2008
	// from the following GPL-licensed work:
	// "No Self Pings" (version 0.2) by Michael D. Adams
	// http://wordpress.org/extend/plugins/no-self-ping/

	function ping_noself( &$links ) {
		$home = get_option( 'home' );
		foreach ( $links as $l => $link )
			if ( 0 === strpos( $link, $home ) )
				unset($links[$l]);
	}


	function post_morelink_nofollow($theContent) {
		if (eregi('<a ([^>]*)rel=["\']([a-zA-Z0-9_ -]*)nofollow([a-zA-Z0-9_ -]*)["\']([^>]*) class=["\']more-link["\']([^>]*)>', $theContent)) {

			//It looks like some other plugin has already added nofollow, so we'll return the post as-is

			return $theContent;

		} else {
			if (eregi('<a ([^>]*)rel=["\']([a-zA-Z0-9_ -]*)["\']([^>]*) class=["\']more-link["\']([^>]*)>', $theContent)) {

				//There's already a rel attribute, but not a nofollow, so we'll add it
				return eregi_replace('rel=["\']', 'rel="nofollow ', $theContent);
			} else {

				//No rel attribute, so we'll add one
				return eregi_replace('class=["\']more-link["\']', 'rel="nofollow" class="more-link"', $theContent);
			}
		}
	}
	
	function page_list_nospace($pagesList) {
		return str_replace(array("\r", "\n", "\t"), '', $pagesList);
	}

	function tag_autocomplete_disable() {
		$this->include_javascript('tag_autocomplete_disable');
	}

	function tag_cloud_nofollow($html) {
		return eregi_replace("<a([^>]*) rel=(['\"])tag(['\"])", "<a\\1 rel=\\2nofollow tag\\3", $html);
	}

	function security_hide_wp_version($gen, $type) {

		switch ($type) {
			case 'html':
				$gen = '<meta name="generator" content="WordPress">' . "\n";
				break;
			case 'xhtml':
				$gen = '<meta name="generator" content="WordPress" />' . "\n";
				break;
			case 'atom':
				$gen = '<generator uri="http://wordpress.org/">WordPress</generator>';
				break;
			case 'rss2':
				$gen = '<generator>http://wordpress.org/</generator>';
				break;
			case 'rdf':
				$gen = '<admin:generatorAgent rdf:resource="http://wordpress.org/" />';
				break;
			case 'comment':
				$gen = '<!-- generator="WordPress" -->';
				break;
		}

		return $gen;
	}

	function security_plugins_indexhtml_enable() {
		$this->security_plugins_indexhtml(true);
	}

	function security_plugins_indexhtml_disable() {
		$this->security_plugins_indexhtml(false);
	}

	function security_plugins_indexhtml($enable) {
		$this->indexhtml(ABSPATH.PLUGINDIR, $enable);
	}

	function theme_favicon_link() {
		$url = trailingslashit(get_bloginfo('url'));
		echo "<link rel='shortcut icon' href='{$url}favicon.ico' />\n";
	}

	function widget_tags_homeonly($value) {
		if (!is_home() && !is_admin()) {
			$i=1;
			while (isset($value["sidebar-$i"])) {
				$value["sidebar-$i"] = array_values(
					array_filter($value["sidebar-$i"], array($this, "widget_tags_homeonly_filter"))
				);
				$i++;
			}
		}
		return $value;
	}

	function widget_tags_homeonly_filter($value) {
		return (strcmp($value, 'tag_cloud') !== 0);
	}
}






/*************
* JAVASCRIPT *
*************/

function jl_wpt_javascript() {

	header('Content-type: text/javascript');

	$js = $_GET['js'];

	if ('targetblank' == $js) { ?>

function setLinkTargets(target, elements) {
	var i, j, anchors;

	for (i=0; i<elements.length; i++) {

		anchors = elements[i].getElementsByTagName("a");

		for (j=0; j<anchors.length; j++) {
			if (getDomainName(anchors[j].href) != getDomainName(document.location.href))
				anchors[j].target = target;
		}
	}
}


function getDomainName(url) {
	var schemeSuffix = "://";
	return url.substr(0, url.indexOf("/", url.indexOf(schemeSuffix) + schemeSuffix.length));
}

/*
Function Source:
http://www.robertnyman.com/2005/11/07/the-ultimate-getelementsbyclassname/
*/

function getElementsByClassName(className, tag, elm){
	var testClass = new RegExp("(^|\\\\s)" + className + "(\\\\s|$)");
	var tag = tag || "*";
	var elm = elm || document;
	var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
	var returnElements = [];
	var current;
	var length = elements.length;
	for(var i=0; i<length; i++){
		current = elements[i];
		if(testClass.test(current.className)){
			returnElements.push(current);
		}
	}
	return returnElements;
}

<?php

	} elseif ('post_targetblank' == $js) {

?>

function setPostLinkTargets(target) {

	var i, elements;

	var classNames = new Array();
	classNames[0] = "entry-content";
	classNames[1] = "post-content";
	classNames[2] = "entry";

	i=0;
	while (elements == null) {
		elements = getElementsByClassName(classNames[i], "div", document.getElementById("content"));
		i++;
	}

	setLinkTargets(target, elements);
}

setPostLinkTargets("_blank");

<?php

	} elseif ('comment_targetblank' == $js) {

?>

function setCommentLinkTargets(target) {
	setLinkTargets(target, new Array(document.getElementById("comments")));
}

setCommentLinkTargets("_blank");

<?php

	} elseif ('admin_scroll_to_editor' == $js) {

// The JavaScript below was adapted on June 17, 2008
// from the following GPL-compatibly-licensed work:
// "WriteScroll" (version 1.0) by Dougal Campbell
// http://wordpress.org/extend/plugins/writescroll/

?>

jQuery(document).ready(function() {
	// element to scroll
	var h = jQuery('html');
	// position to scroll to
	var wraptop = jQuery('div.wrap').offset().top;
	var speed = 250; // ms
	h.animate({scrollTop: wraptop}, speed);
});

<?php

	} elseif ('tag_autocomplete_disable' == $js) {

// The JavaScript below was adapted on June 6, 2008
// from the following GPL-licensed work:
// "Tag Uncomplete" (version 1.0) by Alex King
// http://wordpress.org/extend/plugins/tag-uncomplete/

?>

jQuery(document).ready(function() {
	jQuery("#newtag").unbind("keypress");
});

<?php
	}
}


/******
* CSS *
******/

function jl_wpt_css() {
	
	header('Content-type: text/css');

	if ($_GET['css'] == 'footer_count') {


// In the CSS below, I use "div#tweaks" instead of "#tweaks" to make it less
// likely that the CSS rules are accidentally overriden by the theme rules

?>

div#tweaks {
	opacity: .6;
	filter: alpha(opacity=60);
	-moz-opacity: .6;

	background-color: #fff;
	border: 0;
	border-top: 1px solid #999;
	color: #000;
	margin: 1em 0 0 0;
	padding: 0.5em 0;
	text-align: center;
	width: 100%;
}

div#tweaks a, div#tweaks a:hover, div#tweaks a:visited {
	color: #000;
	border: 0;
	padding: 0;
	margin: 0;
	display: inline;
	text-decoration: underline;
}


<?php
	} elseif ($_GET['css'] == 'admin') {
?>

#jl_wpt_settings table td p {
	padding: 0;
	margin: 0 0 1em 0;
}

#jl_wpt_settings #poststuff {
	margin: 1em 0;
	padding: 0;
}

#jl_wpt_settings table td .title {
	font-weight: bold;
}

#jl_wpt_settings h3 {
	cursor: default;
	margin: 2em 0 1em 0;
}

<?php
	} elseif ($_GET['css'] == 'admin_remove_maxwidth') {

// The CSS below was adapted on June 13, 2008
// from the following GPL-compatibly-licensed work:
// "Remove Max Width" (version 1.3) by Dion Hulse
// http://wordpress.org/extend/plugins/remove-max-width/

?>

.wrap, 
.updated,
.error,
#the-comment-list td.comment {
	max-width: none !important;
}

<?php
		global $is_IE;
		if( $is_IE ) {
?>

* html #wpbody { 
 	_width: 99.9% !important;
}

<?php
		}
	}
}
?>