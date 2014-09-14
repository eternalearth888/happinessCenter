<?php
if(is_numeric($_POST['id']))
  {
  $id = $_POST['id'];
  if($_POST['submit'] == 'X')
    {
    $wpdb->query("DELETE FROM `".$wpdb->prefix."comms_groups` WHERE `id` = '$id' LIMIT 1");
    $wpdb->query("DELETE FROM `".$wpdb->prefix."comms_user_groups` WHERE `groupid` = '$id'");
    }
  }
  else if(($_POST['addcontact'] == 'true') && (!is_numeric($_POST['edit_id'])))
    {
    $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_groups` ( `id` , `name`) VALUES ( '' , '".$_POST['name']."');");
    echo "<div class='updated'>Group added!</div>";
    }

if(is_numeric($_POST['edit_id']))
 {
 $wpdb->query("UPDATE `".$wpdb->prefix."comms_groups` SET `name` ='".$wpdb->escape($_POST['name'])."' WHERE `id`='".$_POST['edit_id']."' LIMIT 1");
 echo "<div class='updated'>Group edited!</div>";
 }
?>
<?php
if(is_numeric($_GET['detailid']))
  {
  $id = $_GET['detailid'];
  require_once("projects.php");
  }
  else
  {
?>
<div class="wrap">
<h2>Groups / Campaign Management</h2>
<?php

$rowcount = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."comms_groups`",ARRAY_A);
$firstlinkpart = "admin.php?page=communications/communications.php&amp;search=".$_GET['search'];
if($rowcount != null)
  {/*
  $pages = (count($rowcount)/200);
  $output .= "<br />";
  $output .= "Pages: ";
  for($i=0;$i<=$pages;++$i)
    {
    if($_GET['pagenum'] == $i)
      {
      $selected = "class='selected'";
      }
      else if(!is_numeric($_GET['pagenum']) && ($i == 0))
        {
        $selected = "class='selected'";
        }
        else
          {
          $selected = "class='notselected'";
          }
    $output .= "<a href='$firstlinkpart&amp;pagenum=$i' $selected >".($i+1) . "</a>  ";
    }
  if(is_numeric($_GET['pagenum']) && ($_GET['pagenum'] > 0))
    {
    $startnum = $_GET['pagenum']*200;
    }
    else
      {
      $startnum = 0;
      }
  $output .= "<br />";
  $output .= "<br />";*/
  $output .= "<table class='commsdisplay' id='addgroup'>";
  $output .= "  <tr class='toprow'>\n\r";
  $output .= "    <td></td>\n\r";
  $output .= "    <td>Name</td>\n\r";
  $output .= "    <td>Submit</td>\n\r";
  $output .= "  </tr>\n\r";
  $output .= "  <form name='add_group' method='post' action=''>\n\r";
  $output .= "  <tr >\n\r";
  $output .= "    <td>Add Group <input type='hidden' name='addcontact' value='true' /></td>\n\r";
  $output .= "    <td><input type='text' value='' name='name'></td>\n\r";
  $output .= "    <td><input type='submit' value='Submit' name='submit'></td>\n\r";
  $output .= "  </tr>\n\r";
  $output .= "  </form>\n\r";
  $output .= "</table>\n\r";
  
  $output .= "<table class='commsdisplay' id='editgroup'>";
  $output .= "  <tr class='toprow'>\n\r";
  $output .= "    <td></td>\n\r";
  $output .= "    <td>Name</td>\n\r";
  $output .= "    <td>Submit</td>\n\r";
  $output .= "  </tr>\n\r";
  $output .= "  <form name='add_group' method='post' action=''>\n\r";
  $output .= "  <tr >\n\r";
  $output .= "    <td>Edit Group <input type='hidden' id='edit_id' name='edit_id' value='true' /></td>\n\r";
  $output .= "    <td><input type='text' id='edit_name' value='' name='name'></td>\n\r";
  $output .= "    <td><input type='submit' value='Submit' name='submit'></td>\n\r";
  $output .= "  </tr>\n\r";
  $output .= "  </form>\n\r";
  $output .= "</table>\n\r";
  
  $output .= "<table class='commsdisplay'>";
  $output .= "  <tr class='toprow'>\n\r";
  $output .= "    <td>No.</td>\n\r";
  $output .= "    <td>Name</td>\n\r";
  $output .= "    <td>Delete</td>\n\r";
  $output .= "  </tr>\n\r";
 $list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_groups`",ARRAY_A);


  $num = 0;
  foreach($list as $row)
    {
    $alternate = "";
    $num++;
    if(($num % 2) != 0)
      {
      $alternate = "class='alt'";
      }

    $check_bounced = $wpdb->get_results("SELECT `id`,`bounced`  FROM `".$wpdb->prefix."comms_email_log` WHERE `commsid` = '".$row['id']."' AND `bounced` = '1'",ARRAY_A);

    $output .= "  <tr $alternate>\n\r";
    $output .= "    <td>".$num."</td>\n\r";
    $output .= "    <td><a href='#'  onclick='editgroup(".$row['id'].");return false;' >".stripslashes($row['name'])."</a></td>\n\r";
    $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='X' /></form></td>\n\r";
    $output .= "  </tr>\n\r";
    }
  }
  else
    {
    $output .= "<br />No results found";
    }
    
echo $output;
?>
</table>
</div>
<?php
  }
?>
