<?php
if(!isset($_GET['contact_list']))
  {
  $contact_list = 'address_book';
  }
  else
    {
    $contact_list = $_GET['contact_list'];
    }
    
    
if($_POST['action'] == 'groups')
  {
  if(is_numeric($_POST['user_id']))
    {   
    $user_id = $_POST['user_id'];
    if(count($_POST['group_id']) > 0)
      {
      foreach($_POST['group_id'] as $group_id)
        {
        if(is_numeric($group_id))
          {
          //echo "INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '$user_id', '$group_id');";
          $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '$user_id', '$group_id');");
          }
        }
      }
    
    $group_member_sql="SELECT `id`,`groupid` FROM `".$wpdb->prefix."comms_user_groups` WHERE `".$wpdb->prefix."comms_user_groups`.`userid` IN ('".$user_id."')";
    $group_members = $wpdb->get_results($group_member_sql,ARRAY_A);
    if(count($group_members) > 0)
      {
      foreach($group_members as $group_member)
        {
        if((count($_POST['group_id']) < 1) || (array_search($group_member['groupid'], $_POST['group_id']) === false))
          {
          $wpdb->query("DELETE FROM `".$wpdb->prefix."comms_user_groups` WHERE `id` = '".$group_member['id']."' LIMIT 1");
          }
        }
      }
    echo "<div class='updated'><p align='center'>Thanks, your changes have been applied.</p></div>";
    $groups_updated == true;
    }
  }
 
if(is_numeric($_POST['id']))
  {
  $id = $_POST['id'];
  if($_POST['submit'] == 'X')
    {
    $wpdb->query("DELETE FROM `".$wpdb->prefix."comms` WHERE `id` = '$id' LIMIT 1");
    }
  if($_POST['submit'] == 'Yes')
    {
    $wpdb->query("UPDATE `".$wpdb->prefix."comms` SET `subscribed` = '0' WHERE `id` = '$id' LIMIT 1");
    }
    else if($_POST['submit'] == 'No')
      {
      $wpdb->query("UPDATE `".$wpdb->prefix."comms` SET `subscribed` = '1' WHERE `id` = '$id' LIMIT 1");
      $wpdb->query("UPDATE `".$wpdb->prefix."comms_email_log` SET `bounced` = '0' WHERE `commsid` = '$id'");
      }
  }
  else if(($_POST['addcontact'] == 'true') && (!is_numeric($_POST['edit_id'])))
    {
    if($_POST['email']!= null)
      {
      $wpdb->query("INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `mobile` , `name` , `title` , `organisation` , `subscribed` , `time` ) VALUES ( '' , '".$_POST['email']."', '".$_POST['mobile']."', '".$_POST['name']."', '".$_POST['jobtitle']."', '".$_POST['organisation']."', '1', NOW( ));");
      echo "<div class='updated'>".$_POST['email']." added!</div>";
      }
      else
        {
        echo "<div class='updated'>Please enter an email address.</div>";
        }
    }

if(is_numeric($_POST['edit_id']))
  {
  $wpdb->query("UPDATE `".$wpdb->prefix."comms` SET `name`='".$wpdb->escape($_POST['name'])."',`email`='".$wpdb->escape($_POST['email'])."',`mobile`='".$wpdb->escape($_POST['mobile'])."',`title`='".$wpdb->escape($_POST['jobtitle'])."',`organisation`='".$wpdb->escape($_POST['organisation'])."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1");
  echo "<div class='updated'>".$_POST['email']." edited!</div>";
  }
 
 
$tables = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix."submited_form_data'",ARRAY_A);
//exit("<pre>".print_r($tables, true)."</pre>");

if($tables != null)
  {
  $shopping_cart_contacts_sql = "SELECT DISTINCT `".$wpdb->prefix."submited_form_data`.`value` AS `email` FROM `".$wpdb->prefix."collect_data_forms` , `".$wpdb->prefix."submited_form_data` , `".$wpdb->prefix."purchase_logs` WHERE `".$wpdb->prefix."collect_data_forms`.`id` = `".$wpdb->prefix."submited_form_data`.`form_id` AND `".$wpdb->prefix."purchase_logs`.`id` = `".$wpdb->prefix."submited_form_data`.`log_id` AND `".$wpdb->prefix."collect_data_forms`.`name` LIKE 'email' AND `".$wpdb->prefix."purchase_logs`.`date` != ''";
  $shopping_cart_contacts = $wpdb->get_results($shopping_cart_contacts_sql,ARRAY_A);
  $check_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups` WHERE `name` = 'WP e-Commerce' LIMIT 1",ARRAY_A);
  if($check_group == null)
    {
    $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_groups` VALUES ('', 'WP e-Commerce', '');");
    $check_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups` WHERE `name` = 'WP e-Commerce' LIMIT 1",ARRAY_A);
    }
  $group_id = $check_group[0]['id'];
  if($shopping_cart_contacts != null)
    {
    foreach($shopping_cart_contacts as $shopping_cart_contact)
      {
      $check_existing = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `email` IN ('".$shopping_cart_contact['email']."') LIMIT 1",ARRAY_A);
      
      if($check_existing != null)
        {
        $check_user_in_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_user_groups` WHERE `userid` IN ('".$check_existing[0]['id']."') AND `groupid` IN ('".$group_id."')  LIMIT 1",ARRAY_A);
        if($check_user_in_group == null)
          {
          $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '$user_id', '$group_id');");
          }
        }
        else
        {        
        if(preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/",$shopping_cart_contact['email']))
          {
          $wpdb->query("INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `mobile` , `name` , `title` , `organisation` , `subscribed` , `e-commerce` , `time` ) VALUES ( '' , '".$shopping_cart_contact['email']."', '', '', '', '', '1', '1', NOW( ));");
          $get_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."comms`",ARRAY_A);
          $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '".$get_id[0]['id']."', '$group_id');");
          }
        }
      }
    }
  }
 
 
unset($group_id);
$user_contacts_sql = "SELECT * FROM `".$wpdb->prefix."users`";
$user_contacts_sql = $wpdb->get_results($user_contacts_sql,ARRAY_A);

//exit("<pre>".print_r($user_contacts_sql, true)."</pre>");
$check_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups` WHERE `name` = 'Wordpress Users' LIMIT 1",ARRAY_A);
if($check_group == null)
  {
  $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_groups` VALUES ('', 'Wordpress Users', '');");
  $check_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups` WHERE `name` = 'Wordpress Users' LIMIT 1",ARRAY_A);
  }
$group_id = $check_group[0]['id'];
if($user_contacts_sql != null)
  {
  foreach($user_contacts_sql as $user_contact)
    {
    $check_existing = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `email` IN ('".$user_contact['user_email']."') LIMIT 1",ARRAY_A);
    
    if($check_existing != null)
      {
      $user_id = $check_existing[0]['id'];
      $check_user_in_group = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_user_groups` WHERE `userid` IN ('".$check_existing[0]['id']."') AND `groupid` IN ('".$group_id."')  LIMIT 1",ARRAY_A);
      if($check_user_in_group == null)
        {
        $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '$user_id', '$group_id');");
        }      
      //$wpdb->query("UPDATE `".$wpdb->prefix."comms` SET `wp-user` = '1' WHERE `id` = '$user_id' LIMIT 1");
      }
      else
      {
      if(preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/",$user_contact['user_email']))
        {
        $profileuser = new WP_User($user_contact['ID']);
        $wpdb->query("INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `mobile` , `name` , `title` , `organisation` , `subscribed` , `e-commerce` ,`wp-user` ,`wp-user-id` , `time` ) VALUES ( '' , '".$user_contact['user_email']."', '', '".$profileuser->first_name." ".$profileuser->last_name."', '', '', '1', '0', '1','".$user_contact['ID']."', NOW( ));");
        $get_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."comms`",ARRAY_A);
        $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_user_groups` ( `id` , `userid` , `groupid` ) VALUES ('', '".$get_id[0]['id']."', '$group_id');");
        unset($profileuser);
        }
      }
    }
  }
  
 
 
if(is_numeric($_GET['detailid']))
  {
  $id = $_GET['detailid'];
  require_once("projects.php");
  }
  else
  {
  if($contact_list == 'address_book')
    {
?>
<div class="wrap" id='addcontact'>
<h2>Add Contact</h2>
<form action='' method='POST' name='subscribe'>
<table class='commsdisplay'>
<tr class='toprow'>
<td>Name</td>
<td>Organisation</td>
<td>Job Title</td>
<td>Email</td>
<td>Mobile Number</td>
<td></td>
</tr>
<tr>
<td><input type='text' name='name' value='' /></td>
<td><input type='text' name='organisation' value='' /></td>
<td><input type='text' name='jobtitle' value='' /></td>
<td><input type='text' name='email' value='' /></td>
<td><input type='text' name='mobile' value='' /></td>
<td>
  <input type='hidden' name='addcontact' value='true' />
  <input type='submit' name='submit' value='Submit' />
</td>
</tr>
</table>
</form>
</div>
<?php
   }
?>
<div class="wrap" id='editcontact'>
<h2>Edit Contact</h2>
<form action='' method='POST' name='subscribe'>
<table class='commsdisplay'>
<tr class='toprow'>
<td>Name</td>
<td>Organisation</td>
<td>Job Title</td>
<td>Email</td>
<td>Mobile Number</td>
<td></td>
</tr>
<tr>
<td><input type='text' name='name' value='' id='edit_name' /></td>
<td><input type='text' name='organisation' value='' id='edit_organisation' /></td>
<td><input type='text' name='jobtitle' value='' id='edit_jobtitle' /></td>
<td><input type='text' name='email' value='' id='edit_email' /></td>
<td><input type='text' name='mobile' value='' id='edit_mobile' /></td>
<td><input type='hidden' name='edit_id' value='' id='edit_id' /><input type='submit' name='submit' value='Submit' /></td>
</tr>
</table>
</form>
</div>

<div class="wrap" id='managegroups'>
<h2>Manage Groups for <span id='group_edit_name'></span></h2>
<form action='' method='POST' name='subscribe'>
<?php
$groups = $wpdb->get_results("SELECT `id`,`name` FROM `".$wpdb->prefix."comms_groups`",ARRAY_A);
foreach($groups as $group)
  {
  echo "<input type='radio' name='spacers' value='null' disabled='true' style='visibility: hidden;' /> <input type='checkbox' name='groups[]' id='groups_".$group['id']."' value='".$group['id']."'><label for='groups_".$group['id']."'>".$group['name']."</label><br />";
  }
?>
  <input type='hidden' name='userid' id='group_edit_id' value='true' />
  <input type='hidden' name='edit_groups' value='true' />
  <input type='submit' name='submit' value='Submit' />
</form>
</div>

<div class="wrap">
<?php

switch($contact_list)
  {
  case 'wp_users':
  echo wp_user_contacts();
  break;
  
  case 'customers':
  echo shopping_cart_contacts();
  break;
  
  case 'address_book':
  default:
  echo address_book();
  break;
  }
?>
</table>
</div>
<?php
  }
?>
