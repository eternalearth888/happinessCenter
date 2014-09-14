<?php
/* 
 * Plugin Name:   Different Posts Per Page
 * Version:       1.7.3
 * Plugin URI:    http://www.maxblogpress.com/plugins/dppp/
 * Description:   Show different number of posts in home, category, search or archive page.
 * Author:        MaxBlogPress
 * Author URI:    http://www.maxblogpress.com
 *
 * License:       GNU General Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * Copyright (C) 2007 www.maxblogpress.com
 * 
 */
 
define('DPPP_NAME', 'Different Posts Per Page');  // Name of the Plugin
define('DPPP_VERSION', '1.7.3');	              // Current version of the Plugin
 
/**
 * DPPP - Different Posts Per Page Class
 * Holds all the necessary functions and variables
 */
class DPPP 
{
    /////////////////////////////////////////////////
    // PUBLIC VARIABLES
    /////////////////////////////////////////////////

    /**
     * Different posts per page plugin path
     * @var string
     */
	var $dppp_path    = "";
	
    /**
     * Different posts per page values set by admin
     * @var array
     */
	var $dppp_values  = array();
	
    /**
     * Options available in different posts per page values
     * @var array
     */
	var $dppp_option  = array();
	
    /**
     * Parsed query string variables are stored in this array
     * @var array
     */
	var $dppp_qvars   = array();
	
    /**
     * Holds the post/page type. If its home, single, archive, etc.
     * @var string
     */
	var $dppp_pg_type = "";
	
    /**
     * Holds post category
     * @var string
     */
	var $dppp_cat     = "";
	
    /**
     * Holds query string
     * @var string
     */
	var $dppp_qstr    = "";
	
    /**
     * Holds Post/Get data
     * @var array
     */
	var $dppp_request    = array();
	
    /**
     * Holds wordpress version
     * @var string
     */
	var $dppp_wp_version = "";
	
    /**
     * Holds the default 'posts per page' values
	 * These values will be set while activating the plugin
     * @var array
     */
	var	$default_options = array(
								'is_home' => array(
									'posts_per_page' => 5,
									'what_to_show'   => 'posts',
									'orderby'        => 'date',
									'order'          => 'DESC'
								),
								'is_category' => array(
									'posts_per_page' => 5,
									'what_to_show'   => 'posts',
									'orderby'        => 'date',
									'order'          => 'DESC'
								),
								'is_archive' => array(
									'posts_per_page' => 5,
									'what_to_show'   => 'posts',
									'orderby'        => 'date',
									'order'          => 'DESC'
								),
								'is_search' => array(
									'posts_per_page' => 5,
									'what_to_show'   => 'posts',
									'orderby'        => 'date',
									'order'          => 'DESC'
								),
								'is_tag' => array(
									'posts_per_page' => 5,
									'what_to_show'   => 'posts',
									'orderby'        => 'date',
									'order'          => 'DESC'
								)
							);

    /**
     * Holds the various pages available in wordpress
     * @var array
     */
	var $dppp_pages      = array('is_home'=>'Home', 'is_category'=>'Category', 'is_archive'=>'Archive', 'is_search'=>'Search', 
								'is_author'=>'Author', 'is_paged'=>'Paged', 'is_feed'=>'Feed', 'is_date'=>'Date', 
								'is_year'=>'Year', 'is_month'=>'Month', 'is_day'=>'Day', 'is_time'=>'Time', 'is_tag'=>'Tag');			
	var $dppp_shows      = array('posts');
	var $dppp_orderby    = array('date');
	var $dppp_orders     = array('DESC', 'ASC');

	
	/**
	 * Constructor. Add DPPP plugin actions/filters and gets the user defined options.
	 * @access public
	 */
	function DPPP() {
		global $wp_version;
		$this->dppp_wp_version = $wp_version;
		$default_posts_per_page = get_option("posts_per_page");
		$this->default_options[is_home][posts_per_page] 	= $default_posts_per_page;
		$this->default_options[is_category][posts_per_page] = $default_posts_per_page;
		$this->default_options[is_archive][posts_per_page] 	= $default_posts_per_page;
		$this->default_options[is_search][posts_per_page] 	= $default_posts_per_page;
		$this->default_options[is_tag][posts_per_page]      = $default_posts_per_page;

		$this->dppp_path     = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', __FILE__);
		$this->dppp_path     = str_replace('\\','/',$this->dppp_path);
		
	    add_action('activate_'.$this->dppp_path, array(&$this, 'dpppActivate'));
		add_action('admin_menu', array(&$this, 'dpppAddMenu'));
		
		$this->dppp_activate = get_option('dppp_activate');
		if( !$this->dppp_values = get_option('diff_posts_per_page') ) {
			$this->dppp_values = array();
		}
		
		if ( $this->dppp_activate == 2 ) {
			add_filter('query_string', array(&$this, 'dpppCustomQuery'));
			if ( 
			$this->dppp_values['is_home']['posts_per_page'] > 0 && $this->dppp_values['is_paged']['posts_per_page'] > 0 && 
			($this->dppp_values['is_home']['posts_per_page'] != $this->dppp_values['is_paged']['posts_per_page']) 
			) {
				$this->dppp_the_diff = intval($this->dppp_values['is_paged']['posts_per_page'] - $this->dppp_values['is_home']['posts_per_page']);
				add_filter('post_limits', array(&$this, 'dpppAlterLimits'));
			}
		}
	}
	
	/**
	 * Called when plugin is activated. Adds option_value to the options table.
	 * @access public
	 */
	function dpppActivate() {
		add_option('dppp_activate', 0);
		add_option('diff_posts_per_page', $this->default_options, 'Different posts per page plugin options', 'no');
		return true;
	}
	
	/**
	 * Make changes in the Lower Limit in the LIMIT portion of the query string.
	 * Required if admin has set 'posts_per_page' for is_paged.
	 * Solves the problem of some posts not being displayed.
	 * @param string $limits
	 * @return string $limits
	 * @access public
	 */
	function dpppAlterLimits($limits) {
		if ( trim($limits) ) {
			$dppp_limits_arr = explode(",",trim(strstr(trim($limits), " ")));
			$dppp_limit_from = trim($dppp_limits_arr[0]);
			$dppp_limit_to   = trim($dppp_limits_arr[1]);
			if ( $dppp_limit_from > 0 ) {
				$dppp_limit_from = $dppp_limit_from - $this->dppp_the_diff;
				$limits = " LIMIT ".$dppp_limit_from.", ".$dppp_limit_to;
			}
		}
		return $limits;
	}
	
	/**
	 * Adds user defined query options to the main query string
	 * Rebuilds the query string
	 * @param string $query_string The main query string
	 * @return string The custom query string
	 * @access public
	 */
	function dpppCustomQuery($query_string) {
		$query_str = $query_string;
		parse_str($query_str, $qvars);
		$this->dppp_qstr  = $query_str;
		$this->dppp_qvars = $qvars;
		if ( $this->dppp_values ) {
			$this->dpppGetPageType();
			$this->dpppGetCategory();
		}
		if ( $this->dppp_cat ) {
			$this->dppp_option = $this->dppp_values[$this->dppp_cat];
		} else if ( $this->dppp_pg_type ) {
			$this->dppp_option = $this->dppp_values[$this->dppp_pg_type];
		}
		if ( $this->dppp_option ) {
			$dppp_query_str = array(
				'posts_per_page' => $this->dppp_option['posts_per_page'],
				'what_to_show'   => $this->dppp_option['what_to_show'],
				'orderby'        => $this->dppp_option['orderby'],
				'order'          => $this->dppp_option['order']
				);
			$this->dppp_qvars = array_merge($dppp_query_str,$this->dppp_qvars);
			foreach ( $this->dppp_qvars as $key=>$val ) {
				$queryvars[] = $key.'='.$val;
			}
			$this->dppp_qstr = implode('&', $queryvars);
		}
		return $this->dppp_qstr;
	}
	
	/**
	 * Determines the page type.
	 * Archive page => Author, Category, Date
	 * Date => Time, Day, Month, Year
	 * @access public 
	 */
	function dpppGetPageType() {
		global $wp_query;
		$wp_query->parse_query($this->dppp_qstr);

		if ( $wp_query->is_feed AND $this->dppp_values['is_feed'] ) {
			$this->dppp_pg_type = 'is_feed';
		} else if ( $wp_query->is_paged AND $this->dppp_values['is_paged'] ) {
			$this->dppp_pg_type = 'is_paged';
		} else if ( $wp_query->is_tag AND $this->dppp_values['is_tag'] ) {
			$this->dppp_pg_type = 'is_tag';
		} else if ( $wp_query->is_archive ) {
			if ( $wp_query->is_author AND $this->dppp_values['is_author'] ) {
				$this->dppp_pg_type = 'is_author';
			} else if ( $wp_query->is_category AND $this->dppp_values['is_category'] ) {
				$this->dppp_pg_type = 'is_category';
			} else if ( $wp_query->is_date ) {
				if ( $wp_query->is_time AND $this->dppp_values['is_time'] ) {
					$this->dppp_pg_type = 'is_time';
				} else if ( $wp_query->is_day AND $this->dppp_values['is_day'] ) {
					$this->dppp_pg_type = 'is_day';
				} else if ( $wp_query->is_month AND $this->dppp_values['is_month'] ) {
					$this->dppp_pg_type = 'is_month';
				} else if ( $wp_query->is_year AND $this->dppp_values['is_year'] ) {
					$this->dppp_pg_type = 'is_year';
				} else if ( $this->dppp_values['is_date'] ) {
					$this->dppp_pg_type = 'is_date';
				} else if ( $this->dppp_values['is_archive'] ) {
					$this->dppp_pg_type = 'is_archive';
				}
			}
		} else if ( $wp_query->is_search AND $this->dppp_values['is_search'] ) {
			$this->dppp_pg_type = 'is_search';
		} else if ( function_exists(is_tag) AND is_tag() AND $this->dppp_values['is_tag'] ) {
			$this->dppp_pg_type = 'is_tag';		
		} else if ( $wp_query->is_home AND $this->dppp_values['is_home'] ) {
			$this->dppp_pg_type = 'is_home';
		}
	}
	
	/**
	 * Get the category id from category nice_name.
	 * @access public 
	 */
	function dpppGetCategory() {
		global $wp_query;
		global $wpdb;
		
		if ( $wp_query->is_category ) {
			if ( !($category_id = $wp_query->get('cat')) ) {
				$category    = $wp_query->get('category_name');
				if ( $this->dppp_wp_version < 2.3 ) {
					$sqlstr  = "SELECT cat_ID FROM $wpdb->categories WHERE category_nicename = '". $wpdb->escape($category) ."'";
				} else {
					$sqlstr  = "SELECT term_id FROM $wpdb->terms WHERE slug = '". $wpdb->escape($category) ."'";
				}
				$category_id = (int) $wpdb->get_var($sqlstr);
			}
			if ( $this->dppp_values['cat_'.$category_id] ) {
				$this->dppp_cat = 'cat_'.$category_id;
			}
		}
	}
	
	/**
	 * Get the category_nicename from category id
	 * @param int $catid Category Id.
	 * @return string Category nicename.
	 * @access public 
	 */
	function dpppGetCategoryNicename($catid) {
		global $wpdb;
		if ( $this->dppp_wp_version < 2.3 ) {
			$sqlstr  = "SELECT category_nicename FROM $wpdb->categories WHERE cat_ID = '". $wpdb->escape((int) $catid) ."'";
		} else {
			$sqlstr  = "SELECT name FROM $wpdb->terms WHERE term_id = '". $wpdb->escape((int) $catid) ."'";
		}
		return $wpdb->get_var($sqlstr);
	}
	
	/**
	 * Adds "DiffPostsPerPage" link to admin Options menu
	 * @access public 
	 */
	function dpppAddMenu() {
		add_options_page('Different Posts Per Page', 'DiffPostsPerPage', 'manage_options', $this->dppp_path, array(&$this, 'dpppOptionsPg'));
	}
	
	/**
	 * Displays the page content for "DiffPostsPerPage" Options submenu
	 * Carries out all the operations in Options page
	 * @access public 
	 */
	function dpppOptionsPg() {
		load_plugin_textdomain('DiffPostsPerPage');
		$this->dppp_request = $_REQUEST['dppp'];
		
		$form_1 = 'dppp_reg_form_1';
		$form_2 = 'dppp_reg_form_2';
		// Activate the plugin if email already on list
		if ( trim($_GET['mbp_onlist']) == 1 ) { 
			$this->dppp_activate = 2;
			update_option('dppp_activate', $this->dppp_activate);
			$msg = 'Thank you for registering the plugin. It has been activated'; 
		} 
		// If registration form is successfully submitted
		if ( ((trim($_GET['submit']) != '' && trim($_GET['from']) != '') || trim($_GET['submit_again']) != '') && $this->dppp_activate != 2 ) { 
			update_option('dppp_name', $_GET['name']);
			update_option('dppp_email', $_GET['from']);
			$this->dppp_activate = 1;
			update_option('dppp_activate', $this->dppp_activate);
		}
		if ( intval($this->dppp_activate) == 0 ) { // First step of plugin registration
			$this->dpppRegister_1($form_1);
		} else if ( intval($this->dppp_activate) == 1 ) { // Second step of plugin registration
			$name  = get_option('dppp_name');
			$email = get_option('dppp_email');
			$this->dpppRegister_2($form_2,$name,$email);
		} else if ( intval($this->dppp_activate) == 2 ) { // Options page
			if ( $this->dppp_request['add_pg'] || $this->dppp_request['add_cat'] ) {
				$this->dpppAddOptions();
				$this->dpppShowOptionsPage($msg=1);
			} else if ( $this->dppp_request['delete_checked'] ) {
				$this->dpppDeleteOptions();
				$this->dpppShowOptionsPage($msg=2);
			} else if ( $this->dppp_request['save_all'] ) {
				$this->dpppSaveOptions();
				$this->dpppShowOptionsPage($msg=3);
			} else {
				$this->dpppShowOptionsPage($msg);
			}
		}
	}
	
	/**
	 * Adds and updates 'posts per page' options
	 * @access public 
	 */
	function dpppAddOptions() {
		if ($this->dppp_request['add_cat']) {
			$dppp_options_new = array(
				'cat_'. $_REQUEST['cat'] => array(
					'posts_per_page' => intval($this->dppp_request['category']['posts_per_page']),
					'what_to_show'   => $this->dppp_request['category']['what_to_show'],
					'orderby'        => $this->dppp_request['category']['orderby'],
					'order'          => $this->dppp_request['category']['order']
				));
		}
		else {
			$dppp_options_new = array(
				$this->dppp_request['page'] => array(
					'posts_per_page' => intval($this->dppp_request['posts_per_page']),
					'what_to_show'   => $this->dppp_request['what_to_show'],
					'orderby'        => $this->dppp_request['orderby'],
					'order'          => $this->dppp_request['order']
				));
		}
		$this->dppp_values = array_merge($this->dppp_values, $dppp_options_new);
		update_option('diff_posts_per_page', $this->dppp_values);
	}
	
	/**
	 * Saves the current 'posts per page' options
	 * @access public 
	 */
	function dpppSaveOptions() {
		if ( count($this->dppp_request['option_save']) ) {
			foreach ( $this->dppp_request['option_save'] as $key=>$pg ) {
				$dppp_options_new[$pg] = array(
							'posts_per_page' => intval($this->dppp_request['option_posts_per_page'][$key]),
							'what_to_show'   => 'posts',
							'orderby'        => 'date',
							'order'          => $this->dppp_request['option_order'][$key]
							);
			}
		}
		$this->dppp_values = $dppp_options_new;
		update_option('diff_posts_per_page', $this->dppp_values);
	}
	
	/**
	 * Deletes 'posts per page' options
	 * @access public 
	 */
	function dpppDeleteOptions() {
		if ( count($this->dppp_request['delete']) ) {
			foreach ( $this->dppp_request['delete'] as $pg ) {
				unset($this->dppp_values[$pg]);
			}
		}
		update_option('diff_posts_per_page', $this->dppp_values);
	}
	
	/**
	 * Display the options page
	 * @param string $msg Update/Delete message to be shown
	 * @access public 
	 */
	function dpppShowOptionsPage($msg=0) {
		if ( $msg==1 || $msg==3 ) {
			$msg = "'posts per page' options saved.";
		} else if ( $msg==2 ) {
			$msg = "'posts per page' options deleted.";
		}
		if ( $msg ) {
			echo '<div id="message" class="updated fade"><p><strong>'. __($msg, 'dppp') .'</strong></p></div>';
		}
		?>
		<script>
		//<!--
		function isNumeric(num){
			var the_val = num.value;
			var ret = (/^-?[0-9]*$/.test(the_val));
			if ( ret == false ) {
				alert('Should be a numeric value');
				num.value = the_val.substr(the_val,the_val.length-1);
				return false;
			}
			return true;
		}
		function toggleAll(parent) {
			var now = parent.checked;
			var frm = document.dpppform;
			var len = frm.elements.length;
			for ( i=0; i<len; i++ ) {
				if ( frm.elements[i].name=='dppp[delete][]' ) {
					frm.elements[i].checked=now;
				}
			}
		}//-->
		</script>
		<form name="dpppform" method="post">
		<div class="wrap"><h2> <?php echo DPPP_NAME.' '.DPPP_VERSION; ?></h2><br />
		 <strong><a href="http://www.maxblogpress.com/plugins/dppp/use/" target="_blank"><?php _e('How to use it?', 'dppp'); ?></a> | 
         <a href="http://www.maxblogpress.com/plugins/dppp/comments/" target="_blank"><?php _e('Comments and Suggestions', 'dppp'); ?></a></strong><br /><br />
		 <h3><?php _e('Current "posts per page" options', 'dppp'); ?></h3>
		 <table cellspacing="1" cellpadding="3" width="60%">
		  <tr class="alternate">
		   <td width="10%"><div align="center"><input type="checkbox" name="checkall" onclick="toggleAll(this)"/></div></td>
		   <td><strong><?php _e('Page', 'dppp'); ?></strong></td>
		   <td width="20%"><div align="center"><strong><?php _e('Show', 'dppp'); ?></strong></div></td>
		   <td width="15%"><div align="center"><strong><?php _e('Order', 'dppp'); ?></strong></div></td>
		  </tr>
	
		<?php
		  $i = 0;
		  foreach ( (array) $this->dppp_values as $thepage => $dppp_option ) {
		     if (strpos($thepage, 'cat_') !== false) {
			   $cat_id    = str_replace('cat_', '', $thepage);
			   $cat_name  = $this->dpppGetCategoryNicename(intval($cat_id));
			   $page_name = substr($cat_name,0,45).' (category)';
		     }
		     else {
			   $page_name = $this->dppp_pages[$thepage];
		     }
		?>
		     <tr valign="top" <?php if ($i % 2 != 0) echo 'class="alternate"'; ?>>
			  <td><div align="center"><input type="checkbox" name="dppp[delete][]" value="<?php echo $thepage; ?>" /></div></td>
			  <td><?php echo $page_name; ?><input type="hidden" name="dppp[option_save][]" value="<?php echo $thepage; ?>" /></td>
			  <td><div align="center"><input type="text" name="dppp[option_posts_per_page][]" value="<?php echo $dppp_option['posts_per_page'];?>" size="2" maxlength="5" onkeyup="isNumeric(this);" /> posts</div></td>
		      <td><div align="center"><select name="dppp[option_order][]">
              <?php foreach ($this->dppp_orders as $order) {
			  		$selected='';
			  		if ($dppp_option['order']==$order) $selected='selected';
					echo "<option $selected>$order</option>";
			  } ?>
              </select></div></td>
			 </tr>
		<?php
		    $i++;
		  } // Eof foreach
		?>
		    <tr>
			 <td colspan="4">
				<input type="submit" class="button" name="dppp[save_all]" value="<?php _e('Save All', 'dppp'); ?>" />	 
				<input type="submit" class="button" name="dppp[delete_checked]" value="<?php _e('Delete Checked', 'dppp'); ?>" /></td>
			</tr>
		</table>
	
	    <h3 style="margin-top:4em;"><?php _e('Add more "posts per page" option', 'dppp'); ?></h3>
		<p><?php _e('# Use \'-1\' to show all posts.', 'dppp'); ?></p>
		<table cellspacing="1" cellpadding="3" width="80%">
			<tr class="alternate">
				<td width="14%">&nbsp;</td>
				<td><strong><?php _e('Select', 'dppp'); ?></strong></td>
				<td width="15%"><div align="center"><strong><?php _e('Show', 'dppp'); ?></strong></div></td>
				<td width="10%"><div align="center"><strong><?php _e('Order', 'dppp'); ?></strong></div></td>
				<td width="10%">&nbsp;</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Page', 'dppp'); ?></th>
				<td>
				<select name="dppp[page]">
				<?php
				foreach ($this->dppp_pages as $key=>$val) {
					echo "<option value='".$key."'>$val</option>";
				}
				?>
				</select></td>
				<td><div align="center">
				<input type="text" name="dppp[posts_per_page]" size="3" maxlength="5" onkeyup="isNumeric(this);" /> posts
				<input type="hidden" name="dppp[what_to_show]" value="<?php echo $this->dppp_shows[0];?>" /></div></td>
				<td><div align="center">
				<input type="hidden" name="dppp[orderby]" value="<?php echo $this->dppp_orderby[0];?>" />
				<select name="dppp[order]">
                <?php 
				foreach ($this->dppp_orders as $order) {
					echo "<option>$order</option>";
				}
				?>
                </select></div></td>
				<td><div align="center"><input type="submit" class="button" name="dppp[add_pg]" value="<?php _e('Add', 'dppp'); ?>" /></div></td>
			</tr>
			<tr class="alternate">
				<th scope="row"><?php _e('Category', 'dppp'); ?></th>
				<td>
				<?php dropdown_cats(0, 'All', 'ID', 'asc', 0, 0, 0, FALSE, 0, 0) ?></td>
				<td><div align="center">
				<input type="text" name="dppp[category][posts_per_page]" size="3" maxlength="5" onkeyup="isNumeric(this);" /> posts
				<input type="hidden" name="dppp[category][what_to_show]" value="<?php echo $this->dppp_shows[0];?>" /></div></td>
				<td><div align="center">
				<input type="hidden" name="dppp[category][orderby]" value="<?php echo $this->dppp_orderby[0];?>" />
				<select name="dppp[category][order]">
				<?php 
				foreach ($this->dppp_orders as $order) {
					echo "<option>$order</option>";
				}
				?>
				</select></div></td>
				<td><div align="center"><input type="submit" class="button" name="dppp[add_cat]" value="<?php _e('Add', 'dppp'); ?>" /></div></td>
			</tr>
		</table>
		<p style="text-align:center;margin-top:3em;"><strong><?php echo DPPP_NAME.' '.DPPP_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	    </div>
	    </form>
		<?php
	}
	
	/**
	 * Plugin registration form
	 * @access public 
	 */
	function dpppRegistrationForm($form_name, $submit_btn_txt='Register', $name, $email, $hide=0, $submit_again='') {
		$wp_url = get_bloginfo('wpurl');
		$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
		$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'];
		$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;mbp_onlist=1';
		if ( $hide == 1 ) $align_tbl = 'left';
		else $align_tbl = 'center';
		?>
		
		<?php if ( $submit_again != 1 ) { ?>
		<script><!--
		function trim(str){
			var n = str;
			while ( n.length>0 && n.charAt(0)==' ' ) 
				n = n.substring(1,n.length);
			while( n.length>0 && n.charAt(n.length-1)==' ' )	
				n = n.substring(0,n.length-1);
			return n;
		}
		function dpppValidateForm_0() {
			var name = document.<?php echo $form_name;?>.name;
			var email = document.<?php echo $form_name;?>.from;
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var err = ''
			if ( trim(name.value) == '' )
				err += '- Name Required\n';
			if ( reg.test(email.value) == false )
				err += '- Valid Email Required\n';
			if ( err != '' ) {
				alert(err);
				return false;
			}
			return true;
		}
		//-->
		</script>
		<?php } ?>
		<table align="<?php echo $align_tbl;?>">
		<form name="<?php echo $form_name;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($submit_again!=1){;?>onsubmit="return dpppValidateForm_0()"<?php }?>>
		 <input type="hidden" name="unit" value="maxbp-activate">
		 <input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
		 <input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
		 <input type="hidden" name="meta_adtracking" value="dppp-w-activate">
		 <input type="hidden" name="meta_message" value="1">
		 <input type="hidden" name="meta_required" value="from,name">
	 	 <input type="hidden" name="meta_forward_vars" value="1">	
		 <?php if ( $submit_again == 1 ) { ?> 	
		 <input type="hidden" name="submit_again" value="1">
		 <?php } ?>		 
		 <?php if ( $hide == 1 ) { ?> 
		 <input type="hidden" name="name" value="<?php echo $name;?>">
		 <input type="hidden" name="from" value="<?php echo $email;?>">
		 <?php } else { ?>
		 <tr><td>Name: </td><td><input type="text" name="name" value="<?php echo $name;?>" size="25" maxlength="150" /></td></tr>
		 <tr><td>Email: </td><td><input type="text" name="from" value="<?php echo $email;?>" size="25" maxlength="150" /></td></tr>
		 <?php } ?>
		 <tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php echo $submit_btn_txt;?>" class="button" /></td></tr>
		</form>
		</table>
		<?php
	}
	
	/**
	 * Register Plugin - Step 2
	 * @access public 
	 */
	function dpppRegister_2($form_name='frm2',$name,$email) {
		$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to activate the plugin.';
		if ( trim($_GET['submit_again']) != '' && $msg != '' ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
		}
		?>
		<div class="wrap"><h2> <?php echo DPPP_NAME.' '.DPPP_VERSION; ?></h2>
		 <center>
		 <table width="640" cellpadding="5" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
		  <tr><td align="center"><h3>Almost Done....</h3></td></tr>
		  <tr><td><h3>Step 1:</h3></td></tr>
		  <tr><td>A confirmation email has been sent to your email "<?php echo $email;?>". You must click on the link inside the email to activate the plugin.</td></tr>
		  <tr><td><strong>The confirmation email will look like:</strong><br /><img src="http://www.maxblogpress.com/images/activate-plugin-email.jpg" vspace="4" border="0" /></td></tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr><td><h3>Step 2:</h3></td></tr>
		  <tr><td>Click on the button below to Verify and Activate the plugin.</td></tr>
		  <tr><td><?php $this->dpppRegistrationForm($form_name.'_0','Verify and Activate',$name,$email,$hide=1,$submit_again=1);?></td></tr>
		 </table>
		 <p>&nbsp;</p>
		 <table width="640" cellpadding="5" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
           <tr><td><h3>Troubleshooting</h3></td></tr>
           <tr><td><strong>The confirmation email is not there in my inbox!</strong></td></tr>
           <tr><td>Dont panic! CHECK THE JUNK, spam or bulk folder of your email.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>It's not there in the junk folder either.</strong></td></tr>
           <tr><td>Sometimes the confirmation email takes time to arrive. Please be patient. WAIT FOR 6 HOURS AT MOST. The confirmation email should be there by then.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>6 hours and yet no sign of a confirmation email!</strong></td></tr>
           <tr><td>Please register again from below:</td></tr>
           <tr><td><?php $this->dpppRegistrationForm($form_name,'Register Again',$name,$email,$hide=0,$submit_again=2);?></td></tr>
           <tr><td><strong>Help! Still no confirmation email and I have already registered twice</strong></td></tr>
           <tr><td>Okay, please register again from the form above using a DIFFERENT EMAIL ADDRESS this time.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr>
             <td><strong>Why am I receiving an error similar to the one shown below?</strong><br />
                 <img src="http://www.maxblogpress.com/images/no-verification-error.jpg" border="0" vspace="8" /><br />
               You get that kind of error when you click on &quot;Verify and Activate&quot; button or try to register again.<br />
               <br />
               This error means that you have already subscribed but have not yet clicked on the link inside confirmation email. In order to  avoid any spam complain we don't send repeated confirmation emails. If you have not recieved the confirmation email then you need to wait for 12 hours at least before requesting another confirmation email. </td>
           </tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>But I've still got problems.</strong></td></tr>
           <tr><td>Stay calm. <strong><a href="http://www.maxblogpress.com/contact-us/" target="_blank">Contact us</a></strong> about it and we will get to you ASAP.</td></tr>
         </table>
		 </center>		
		<p style="text-align:center;margin-top:3em;"><strong><?php echo DPPP_NAME.' '.DPPP_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	    </div>
		<?php
	}

	/**
	 * Register Plugin - Step 1
	 * @access public 
	 */
	function dpppRegister_1($form_name='frm1') {
		global $userdata;
		$name  = trim($userdata->first_name.' '.$userdata->last_name);
		$email = trim($userdata->user_email);
		?>
		<div class="wrap"><h2> <?php echo DPPP_NAME.' '.DPPP_VERSION; ?></h2>
		 <center>
		 <table width="620" cellpadding="3" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
		  <tr><td align="center"><h3>Please register the plugin to activate it. (Registration is free)</h3></td></tr>
		  <tr><td align="left">In addition you'll receive complimentary subscription to MaxBlogPress Newsletter which will give you many tips and tricks to attract lots of visitors to your blog.</td></tr>
		  <tr><td align="center"><strong>Fill the form below to register the plugin:</strong></td></tr>
		  <tr><td><?php $this->dpppRegistrationForm($form_name,'Register',$name,$email);?></td></tr>
		  <tr><td align="center"><font size="1">[ Your contact information will be handled with the strictest confidence <br />and will never be sold or shared with third parties ]</font></td></td></tr>
		 </table>
		 </center>
		<p style="text-align:center;margin-top:3em;"><strong><?php echo DPPP_NAME.' '.DPPP_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	    </div>
		<?php
	}
	
} // Eof Class

$DPPP = new DPPP();
?>