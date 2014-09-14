<?php
/*
Plugin Name: Communications
Plugin URI: none
Description: Communications/email newsletter plugin (works with WP e-commerce lite).
Version: 0.9
Author: Thomas Howard
Author URI: none
*/
function comms_install()
  {
  global $wpdb, $user_level;
  $first_install = false;
  $result = mysql_list_tables(DB_NAME);
  $tables = array();
  while($row = mysql_fetch_row($result))
    {
    $tables[] = $row[0];
    }
  if(!in_array($table_name, $tables))
    {
    $first_install = true;
    }
   $commstable_name = $wpdb->prefix . "comms";
   $commstable = "CREATE TABLE ".$commstable_name." (
`id` bigint(20) unsigned NOT NULL auto_increment,
`email` varchar(255) NOT NULL default '',
`mobile` varchar(64) NOT NULL default '',
`name` varchar(255) NOT NULL default '',
`title` varchar(255) NOT NULL default '',
`organisation` TEXT NOT NULL default '',
`subscribed` char(1) NOT NULL default '',
`e-commerce` char(1) NOT NULL default '',
`wp-user` char(1) NOT NULL default '',
`wp-user-id` bigint(20) unsigned NULL default NULL,
`time` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`id`),
KEY `subscribed` (`subscribed`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";

  $emaillogtable_name = $wpdb->prefix . "comms_email_log";
  $emaillogtable = "CREATE TABLE `".$emaillogtable_name."` (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email` VARCHAR( 192 ) NOT NULL ,
`commsid` BIGINT UNSIGNED NOT NULL ,
`messageid` BIGINT UNSIGNED NOT NULL ,
`bounced` VARCHAR( 1 ) NOT NULL ,
`errorcode` VARCHAR( 64 ) NOT NULL ,
`datetime` DATETIME NOT NULL ,
INDEX ( `commsid` , `messageid` )
) TYPE = MYISAM ;";

  $messagelogtable_name = $wpdb->prefix . "comms_message_log";
  $messagelogtable = "CREATE TABLE `".$messagelogtable_name."` (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`subject` TEXT NOT NULL ,
`message` LONGTEXT NOT NULL ,
`type` VARCHAR( 1 ) NOT NULL DEFAULT '0',
`datetime` DATETIME NOT NULL
) TYPE = MYISAM ;";

  $sms_log_table_name = $wpdb->prefix . "sms_log";
  $sms_log_table = "CREATE TABLE ".$sms_log_table_name." (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
`message` TEXT NOT NULL ,
`mobnum` TEXT NOT NULL ,
`time` VARCHAR( 60 ) NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;";

  $project_table_name = $wpdb->prefix . "comms_project";
  $project_table = "CREATE TABLE ".$project_table_name." (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`project_owner` TEXT NOT NULL,
`project_developer` TEXT NOT NULL,
`project_client` TEXT NOT NULL,
`project_notes` TEXT NOT NULL,
`userid` BIGINT UNSIGNED NOT NULL ,
`completed` VARCHAR( 1 ) NOT NULL DEFAULT '0',
`time` DATETIME NOT NULL ,
INDEX ( `name` , `userid`)
) TYPE = MYISAM ;";

  $project_status_table_name = $wpdb->prefix . "comms_project_status";
  $project_status_table = "CREATE TABLE ".$project_status_table_name." (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`status` VARCHAR( 1 ) NOT NULL ,
`notes` LONGTEXT NOT NULL,
`stage` int(10) unsigned NOT NULL,
`projid` BIGINT NOT NULL ,
INDEX ( `projid` )
) TYPE = MYISAM ;";

$groups_table_name = $wpdb->prefix . "comms_groups";
$groups_table = "CREATE TABLE `$groups_table_name` (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`active` VARCHAR( 1 ) NOT NULL
) TYPE = MYISAM ;";

$user_groups_table_name = $wpdb->prefix . "comms_user_groups";
$user_groups_table ="CREATE TABLE `$user_groups_table_name` (
 `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `groupid` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  INDEX ( `userid` ),
  INDEX ( `groupid` )
) TYPE = MYISAM ;";

  require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
  maybe_create_table($commstable_name,$commstable);
  maybe_create_table($emaillogtable_name,$emaillogtable);
  maybe_create_table($messagelogtable_name,$messagelogtable);
  maybe_create_table($sms_log_table_name,$sms_log_table);
  maybe_create_table($project_table_name,$project_table);
  maybe_create_table($project_status_table_name,$project_status_table);
  maybe_create_table($groups_table_name,$groups_table);
  maybe_create_table($user_groups_table_name,$user_groups_table);

  add_option('comms_mail_host', '', 'the mail host', 'yes');
  add_option('comms_mail_port', '', 'the mail port', 'yes');
  add_option('comms_mail_box', '', 'the mail box', 'yes');
  add_option('comms_mail_user', '', 'the mail user', 'yes');
  add_option('comms_mail_password', '', 'the mail password', 'yes');
  
  $e_commerce_col_check  = $wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."comms` LIKE 'e-commerce'",ARRAY_A);
  if($e_commerce_col_check == null)
    {
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."comms` ADD `e-commerce` char(1) NOT NULL AFTER `subscribed`;");
    }

  $wp_user_col_check  = $wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."comms` LIKE 'wp-user'",ARRAY_A);
  if($wp_user_col_check == null)
    {
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."comms` ADD `wp-user` char(1) NOT NULL AFTER `subscribed`;");
    }

  $wp_user_id_col_check  = $wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."comms` LIKE 'wp-user-id'",ARRAY_A);
  if($wp_user_id_col_check == null)
    {
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."comms` ADD `wp-user-id` bigint(20) unsigned NULL default NULL AFTER `wp-user`;");
    }
  }



function comms_css()
  {
  $siteurl = get_option('siteurl');
  ?>
<link rel="stylesheet" href="<?php echo $siteurl; ?>/wp-content/plugins/communications/comms.css" type="text/css" />
  <?php
  }

function comms_admincss()
  {
  $siteurl = get_option('siteurl');
  ?>
<link rel="stylesheet" href="<?php echo $siteurl; ?>/wp-content/plugins/communications/admin.css" type="text/css" />
<script src="<?php echo $siteurl; ?>/wp-content/plugins/communications/ajax.js" type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/communications/admin.js" type="text/javascript"></script>
<script type="text/javascript">


function emailpreview()
  {
  subject = document.getElementById("emailsubject").value;
  body = document.getElementById("emailbody").value;
  subject = subject.replace(/\&/g,'%26');  
  body = body.replace(/\&/g,'%26');
  body = body.replace(/\n/g,"<br />");
  url = "<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=communications/sendmail.php&showmailpreview=true&subject="+subject+"&message="+body;
  window.open(url ,"_blank","toolbar=no, location=no, directories=no, status=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=yes, width=450, height=500");
  }
</script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/communications/js/jquery-latest.pack.js" type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/communications/js/interface.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery.noConflict();
</script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/communications/js/editable_box.js" type="text/javascript"></script>
  <?php
  }


function comms_admin_pages()
  {
  if(function_exists('add_options_page'))
    {
    add_menu_page('Comms', 'Comms', 7, 'communications/address_book.php');
    add_submenu_page('communications/address_book.php', 'Contacts', 'Contacts', 7, 'communications/address_book.php');
    add_submenu_page('communications/address_book.php', 'Groups', 'Groups', 7, 'communications/groups.php');
    add_submenu_page('communications/address_book.php', 'Projects', 'Projects', 7, 'communications/projects.php');
    add_submenu_page('communications/address_book.php', 'Send Email', 'Send Email', 7, 'communications/sendmail.php');
    if(is_file(ABSPATH."wp-content/plugins/communications/sms/sms_functions.php"))
      {
      add_submenu_page('communications/address_book.php', 'Send TXT', 'Send TXT', 7, 'communications/sendtxt.php');
      }
    add_submenu_page('communications/address_book.php', 'Import Contacts', 'Import Contacts', 7, 'communications/importfromcsv.php');
    add_submenu_page('communications/address_book.php', 'View Logs', 'View Logs', 7, 'communications/viewlogs.php');
    add_submenu_page('communications/address_book.php', 'Options', 'Options', 7, 'communications/options.php');
    }
  }

// function comms_gst()
//   {
//   if(function_exists('add_options_page'))
//     {
//     add_submenu_page('communications/address_book.php', 'Tax', 'Tax', 7, 'communications/gst.php');
//     }
//   }

function comms_preview()
  {
  if(($_GET['showmailpreview'] == 'true') && ($_GET['page'] == 'communications/sendmail.php'))
    {
    echo "<html>\n\r";
    echo "<head>\n\r";
    echo "</head>\n\r";
    echo "<body>\n\r";
    echo "Subject: " . stripslashes($_GET['subject']) . "<br /><br />\n\r";
    echo  str_replace("\n", "<br />",stripslashes($_GET['message']));
    echo "</body>\n\r";
    echo "</html>\n\r";
    exit();
    }
  }
  
function comms_subscribe_cookie()
  {
  global $wpdb;
  if($_POST['emailaddress'] != null)
    {
    setcookie('wp_comms_email', $wpdb->escape($_POST['emailaddress']),time()+60*60*24*90);
    }
  }
  
function comms_getajax()
  {
  global $wpdb;
  if(($_POST['ajax'] == "true") && ($_POST['comms'] == "true") && is_numeric($_POST['id']))
    {
    $id = $_POST['id'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `id` ='$id' LIMIT 1",ARRAY_A);
    $comms_data = $data[0];
    $output .= "document.getElementById(\"edit_id\").value = \"".$comms_data['id']."\";\n";
    $output .= "document.getElementById(\"edit_name\").value = \"".$comms_data['name']."\";\n";
    $output .= "document.getElementById(\"edit_email\").value = \"".$comms_data['email']."\";\n";
    $output .= "document.getElementById(\"edit_mobile\").value = \"".$comms_data['mobile']."\";\n";
    $output .= "document.getElementById(\"edit_jobtitle\").value = \"".$comms_data['title']."\";\n";
    $output .= "document.getElementById(\"edit_organisation\").value = '".$comms_data['organisation']."';\n";
    exit($output);
    }
    
  if(($_POST['ajax'] == "true") && ($_POST['comms'] == "true") && is_numeric($_POST['groupid']))
    {
    $id = $_POST['groupid'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups` WHERE `id` ='$id' LIMIT 1",ARRAY_A);
    $comms_data = $data[0];
    $output .= "document.getElementById(\"edit_id\").value = \"".$comms_data['id']."\";\n";
    $output .= "document.getElementById(\"edit_name\").value = \"".$comms_data['name']."\";\n";
    exit($output);
    }
    
  if(($_POST['ajax'] == "true") && ($_POST['manage_groups'] == "true") && is_numeric($_POST['userid']))
    {
    $id = $_POST['userid'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `id` ='$id' LIMIT 1",ARRAY_A);
    $user_group_sql = "SELECT DISTINCT ".$wpdb->prefix."comms_groups.id FROM `".$wpdb->prefix."comms_groups`,`".$wpdb->prefix."comms_user_groups` WHERE ".$wpdb->prefix."comms_groups.id = ".$wpdb->prefix."comms_user_groups.groupid AND ".$wpdb->prefix."comms_user_groups.userid IN (".$id.")";
    $users_groups = $wpdb->get_results($user_group_sql,ARRAY_A);
    
    $groups = $wpdb->get_results("SELECT `id`,`name` FROM `".$wpdb->prefix."comms_groups`",ARRAY_A);
    $comms_data = $data[0];
    $output .= "document.getElementById(\"group_edit_id\").value = \"".$comms_data['id']."\";\n";
    $output .= "document.getElementById(\"group_edit_name\").innerHTML = \"".$comms_data['name']."\";\n";
    $counter = 0;
    $group_sql = '';
    foreach($users_groups as $group)
      {
      if($counter > 0)
        {
        $group_sql .= ',';
        }
        $group_sql .= "'".$group['id']."'";
      $output .= "document.getElementById(\"groups_".$group['id']."\").checked = true;\n";
      $counter++;
      }
    $group_sql = "SELECT DISTINCT ".$wpdb->prefix."comms_groups.id FROM `".$wpdb->prefix."comms_groups`,`".$wpdb->prefix."comms_user_groups` WHERE ".$wpdb->prefix."comms_groups.id = ".$wpdb->prefix."comms_user_groups.groupid AND ".$wpdb->prefix."comms_user_groups.groupid NOT IN ($group_sql)";
    $groups = $wpdb->get_results($group_sql,ARRAY_A);
    foreach($groups as $group)
     {
     $output .= "document.getElementById(\"groups_".$group['id']."\").checked = false;\n";
     }
    exit($output);
    }
    
  if(($_POST['ajax'] == "true") && ($_POST['project'] == "true") && is_numeric($_POST['id']))
    {
    $id = $_POST['id'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `id` ='$id' LIMIT 1",ARRAY_A);
    $comms_data = $data[0];
    $output .= "document.getElementById(\"edit_id\").value = \"".$comms_data['id']."\";\n";
    $output .= "document.getElementById(\"edit_name\").value = \"".$comms_data['name']."\";\n";
    $output .= "document.getElementById(\"edit_client\").value = \"".$comms_data['project_client']."\";\n";
    $output .= "document.getElementById(\"edit_owner\").value = \"".$comms_data['project_owner']."\";\n";
    $output .= "document.getElementById(\"edit_developer\").value = \"".$comms_data['project_developer']."\";\n";
    exit($output);
    }
    
  if(($_POST['ajax'] == "true") && ($_POST['comms'] == "true") && is_numeric($_POST['projectid']))
    {
    $id = $_POST['projectid'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project_status` WHERE `id` ='$id' LIMIT 1",ARRAY_A);
    $comms_project_data = $data[0];
    $project_name = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix."comms_project` WHERE `id` ='".$comms_project_data['projid']."' LIMIT 1",ARRAY_A);
    $name = $project_name[0]['name'] . " - ";

    switch($comms_project_data['stage'])
      {
      case 1:
      $name .= "contact";
      break;
      
      case 2:
      $name .= "contract";
      break;
      
      case 3:
      $name .= "develop";
      break;
      
      case 4:
      $name .= "launch";
      break;
      
      case 5:
      $name .= "invoice";
      break;
      }
      
    $output .= "var project_id = \"".$comms_project_data['id']."\"; ";
    $output .= "var project_notes = \"".nl2br(str_replace("\r","",$comms_project_data['notes']))."\"; ";
    $output .= "var project_title = \"".$name."\"; ";
    exit($output);
    }
    
    
  if(($_POST['ajax'] == "true") && ($_POST['save'] == "true") && is_numeric($_POST['projectid']) && ($_POST['note'] != null))
    {
    
    $id = $_POST['projectid'];
    $note = $wpdb->escape(str_replace("\n","<br />",str_replace("\r","",$_POST['note'])));
    $wpdb->query("UPDATE `wp_comms_project_status` SET `notes` = '$note' WHERE `id` ='$id'");
    exit($output);
    }
  }


function comms_subscribe()
  {
  global $wpdb;
  if($_POST['emailaddress'] != null)
    {
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `email` = '".$wpdb->escape($_POST['emailaddress'])."' LIMIT 1",ARRAY_A);
    if($data == null)
      {
      $wpdb->query("INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `subscribed`, `time` ) VALUES ('' , '".$wpdb->escape($_POST['emailaddress'])."', '1', NOW( ));") ;
      $message = "<strong>Thank you for subscribing.<strong><br /><br />";
      }
      else
        {
        $message = "<strong>You are already subscribed.</strong><br /><br />";
        }
    }
    
  if($_COOKIE['wp_comms_email'] == null)
    {
    $prevemail = $_POST['emailaddress'];
    }
    else
      {
      $prevemail = $_COOKIE['wp_comms_email'];
      }
  echo "</ul>
  <li><h2>Subscribe</h2></li>
  <ul>
  $message
  <form name='subscribe' method='POST' action='' >
    <input type='text' name='emailaddress' class='emailaddress' value='".$prevemail."' />
    <input type='submit' name='submit' value='Submit' class='subscribe' />
  </form>";
  }

function comms_log_bounced_email($bounce_vars)
   {
   global $wpdb;
   $processed_date = date("Y-m-d",strtotime($bounce_vars['timesent']));
   $message_id = $wpdb->get_results("SELECT `id`  FROM `".$wpdb->prefix."comms_message_log` WHERE `datetime` LIKE '".$processed_date."%' AND `subject`='".$bounce_vars['message_subject']."' LIMIT 1",ARRAY_A);
   $message_id = $message_id[0]['id'];
   
   $email_log_id = $wpdb->get_results("SELECT `id`,`bounced`,`commsid`  FROM `".$wpdb->prefix."comms_email_log` WHERE `email` = '".$bounce_vars['recipient']."' AND `messageid`='".$message_id."' LIMIT 1",ARRAY_A);
   $bounced = $email_log_id[0]['bounced'];
   $commsid = $email_log_id[0]['commsid'];
   $email_log_id = $email_log_id[0]['id'];
   if(is_numeric($email_log_id) && ($bounced == 0))
     {
     $wpdb->query("UPDATE `".$wpdb->prefix."comms_email_log` SET `bounced` = '1',`errorcode` = '".$bounce_vars['error_code']."' WHERE `id` ='".$email_log_id."' LIMIT 1");
     $check_for_more = $wpdb->get_results("SELECT `id`,`bounced`  FROM `".$wpdb->prefix."comms_email_log` WHERE `email` = '".$bounce_vars['recipient']."' AND `bounced` = '1'",ARRAY_A);
     if(count($check_for_more) >= 2)
       {
       //exit("UPDATE `".$wpdb->prefix."comms` SET `subscribed` = '0' WHERE `id` ='".$commsid."' LIMIT 1");
       $wpdb->query("UPDATE `".$wpdb->prefix."comms` SET `subscribed` = '0' WHERE `id` ='".$commsid."' LIMIT 1");
       }
     }
   return true;
   }

if(isset($_GET['activate']) && $_GET['activate'] == 'true')
   {
   add_action('init', 'comms_install');
   }
require("address_book_functions.php");
require('comms_subscribe_widget.php');

add_action('init', 'comms_preview');
add_action('init', 'comms_subscribe_cookie');
add_action('init', 'comms_getajax');
add_action('admin_head', 'comms_admincss');
add_action('wp_head', 'comms_css');
add_action('plugins_loaded', 'widget_comms_subscribe_init');

add_action('admin_menu', 'comms_admin_pages');

if(is_file(ABSPATH."wp-content/plugins/communications/sms/sms_functions.php"))
  {
  require("sms/sms_functions.php");
  $sms_active = true;
  }
  else
    {
    $sms_active = false;
    }
?>