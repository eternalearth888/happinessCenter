<?php
function get_list_count($type)
  {
  global $wpdb;
  switch($type)
    {
    case 'addresses':
    $count = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."comms` WHERE `e-commerce` NOT IN('1') AND `wp-user` NOT IN('1')");
    break;    
    
    case 'customers':
    $count = $wpdb->get_var("SELECT COUNT(*) AS `count`  FROM `".$wpdb->prefix."comms` WHERE `e-commerce` IN('1')");
    break;
    
    case 'users':
    $count = $wpdb->get_var("SELECT COUNT(*) AS `count`  FROM `".$wpdb->prefix."comms` WHERE `wp-user` IN('1') AND `e-commerce` NOT IN('1')");
    break;
    }
  return $count;
  }

function comms_contact_list_tabs()
  {
  $output = "<ul class='address_book_links'>";
  $output .= "<li><a href='admin.php?page=communications/address_book.php&amp;contact_list=address_book'>View Address Book (".get_list_count('addresses').")</a></li>";
  $output .= "<li><a href='admin.php?page=communications/address_book.php&amp;contact_list=customers'>View Customers (".get_list_count('customers').")</a></li>";
  $output .= "<li><a href='admin.php?page=communications/address_book.php&amp;contact_list=wp_users'>View WP Users (".get_list_count('users').")</a></li>"; 
  $output .= "</ul>";
  return $output;
  }

function address_book()
  {    
  global $wpdb;
  $rowcount = $wpdb->get_var("SELECT COUNT(`id`) AS `count` FROM `".$wpdb->prefix."comms` WHERE `e-commerce` NOT IN('1') AND `wp-user` NOT IN('1')");
  $firstlinkpart = "admin.php?page=communications/address_book.php&amp;search=".$_GET['search'];
  
  if($rowcount > 0)
    {
    $pages = ($rowcount/200);
    $output .= "<h2>View Address Book</h2>";
    $output .= comms_contact_list_tabs();
     
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
        
    $list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `e-commerce` NOT IN('1') AND `wp-user` NOT IN('1') LIMIT $startnum, 200",ARRAY_A);
    
    $output .= "<table class='commsdisplay'>";
    $output .= "  <tr class='toprow'>\n\r";
    // $output .= "    <td>Id</td>\n\r";
    $output .= "    <td>Name</td>\n\r";
    $output .= "    <td>Organisation</td>\n\r";
    $output .= "    <td>Job Title</td>\n\r";
    $output .= "    <td>Email</td>\n\r";
    $output .= "    <td>Mobile Number</td>\n\r";
    $output .= "    <td>Subscribed</td>\n\r";
    $output .= "    <td>Delete</td>\n\r";
    $output .= "    <td>Groups</td>\n\r";
    // $output .= "    <td>Manage Groups</td>\n\r";
    $output .= "  </tr>\n\r";
  
  
  
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
  //     $output .= "    <td>".$row['id']."</td>\n\r";
      //$output .= "    <td class='edit'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Edit' onclick='editcontact(".$row['id'].");return false;' /></form></td>\n\r";
      

      
    if($row['e-commerce'] == '1')
      {
      if(($row['name'] == ''))
        {
        $row['name'] = 'e-commerce';
        }
      $output .= "    <td>";
      $output .= "<a href='#'  onclick='editcontact(".$row['id'].");return false;' >".$row['name']."</a>";
      $output .= "<img class='cart_image' src='../wp-content/plugins/communications/images/cart.png' alt='WP e-commerce user' title='WP e-commerce user' />";
      $output .= "</td>\n\r";
      }
      else
        {
        $output .= "    <td><a href='#'  onclick='editcontact(".$row['id'].");return false;' >".stripslashes($row['name'])."</a></td>\n\r";
        }

      $output .= "    <td>".stripslashes($row['organisation'])."</td>\n\r";
      //$output .= "    <td><pre>".print_r($check_bounced,true)."</pre></td>\n\r";
      $output .= "    <td>".stripslashes($row['title'])."</td>\n\r";
      if(count($check_bounced) >= 2)
        {
        $output .= "    <td><span class='bouncedemail'>".$row['email']."</span></td>\n\r";
        }
        else if((count($check_bounced) > 0)&&(count($check_bounced) < 2))
          {
          $output .= "    <td><span class='singlebouncedemail'>".$row['email']."</span></td>\n\r";
          }
          else
            {
            $output .= "    <td>".$row['email']."</td>\n\r";
            }
      $output .= "    <td>".stripslashes($row['mobile'])."</td>\n\r";
      if($row['subscribed'] == 1)
        {
        $output .= "    <td class='yes'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Yes' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='no'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='No' /></form></td>\n\r";
          }
      if($row['e-commerce'] == '1')
        {
        $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input class='disabled' type='submit' name='submit' value='X' disabled='true' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='X' /></form></td>\n\r";
          }
        
        
      $output .= "    <td class='groups'>";
      $output .= "<a href='#' onclick='return manage_user_groups(\"group_box_".$row['id']."\",\"group_expander_icon_".$row['id']."\");'>";
      $output .= "<img class='group_expander_icon' id='group_expander_icon_".$row['id']."' src='../wp-content/plugins/communications/images/icon_window_expand.gif' alt='' title='' />";
      $output .= "<span >Manage Groups</span>";
      $output .= "</a>";
      
      $output .= "    </tr>\n\r";
      
      $output .= "<tr>\n\r";
      $output .= " <td colspan='9' class='group_status'>\n\r";
      $output .= "  <div id='group_box_".$row['id']."' class='group_status' $status_style>\n\r";
      $output .= "  <div>\n\r";
      $output .= "  <strong class='form_group'>Groups</strong>\n\r";
      $output .= "  <form id='form_group_".$row['id']."' method='POST' action='admin.php?page=communications/address_book.php'>\n\r";
      $output .= "  <input type='hidden' name='page' value='".$_GET['page']."' />\n\r";
      $output .= "  <input type='hidden' name='user_id' value='".$row['id']."' />\n\r";
      $output .= "  <input type='hidden' name='action' value='groups' />\n\r";
      $output .= "  <ul>\n\r";
      $group_sql = "SELECT * FROM `".$wpdb->prefix."comms_groups`";
      $groups = $wpdb->get_results($group_sql,ARRAY_A);
      if($groups != null)
        {
        foreach($groups as $group)
          {
          $group_member_sql="SELECT `groupid` FROM `".$wpdb->prefix."comms_user_groups` WHERE `".$wpdb->prefix."comms_user_groups`.`userid` IN ('".$row['id']."') AND `".$wpdb->prefix."comms_user_groups`.`groupid` IN ('".$group['id']."') LIMIT 1";
          $group_member = $wpdb->get_results($group_member_sql,ARRAY_A);
          $selected = '';
          if($group['id'] == $group_member[0]['groupid'])
            {
            $selected = "checked='true'";
            }
          $button_id = "button_".$row['id']."_".$group['id'];
          $output .= "    <li><input type='checkbox' name='group_id[]' $selected value='".$group['id']."' id='".$button_id."'/><label for='$button_id'>".$group['name']."</label>\n\r";
          $counter++;
          }
        }
      $output .= "  </ul>\n\r";
      $output .= "  <input type='submit' name='submit' value='Submit' />\n\r";
      $output .= "  </form>\n\r";
      $output .= "  </div>\n\r";
      $output .= "  </div>\n\r";
      $output .= " </td>\n\r";
      $output .= "</tr>\n\r"; 
      }
    $output .= "</table>\n\r"; 
    }
      
  
  return $output;
  }

function shopping_cart_contacts()
  {  
  global $wpdb;
  $rowcount = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."comms` WHERE `e-commerce` IN('1')",ARRAY_A);
  $firstlinkpart = "admin.php?page=communications/communications.php&amp;search=".$_GET['search'];
  if($rowcount != null)
    {
    $pages = (count($rowcount)/200);
    $output .= "<h2>View E-Commerce Customers</h2>";
    $output .= comms_contact_list_tabs();
    /*
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
        
    $list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` LIMIT $startnum ,200",ARRAY_A);*/
    $list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `e-commerce` IN('1') AND `wp-user` NOT IN('1')",ARRAY_A);
    
    $output .= "<table class='commsdisplay'>";
    $output .= "  <tr class='toprow'>\n\r";
    // $output .= "    <td>Id</td>\n\r";
    $output .= "    <td>Name</td>\n\r";
    $output .= "    <td>Organisation</td>\n\r";
    $output .= "    <td>Job Title</td>\n\r";
    $output .= "    <td>Email</td>\n\r";
    $output .= "    <td>Mobile Number</td>\n\r";
    $output .= "    <td>Subscribed</td>\n\r";
    $output .= "    <td>Delete</td>\n\r";
    $output .= "    <td>Groups</td>\n\r";
    // $output .= "    <td>Manage Groups</td>\n\r";
    $output .= "  </tr>\n\r";
  
  
  
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
  //     $output .= "    <td>".$row['id']."</td>\n\r";
      //$output .= "    <td class='edit'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Edit' onclick='editcontact(".$row['id'].");return false;' /></form></td>\n\r";
      

      
    if($row['e-commerce'] == '1')
      {
      if(($row['name'] == ''))
        {
        $row['name'] = 'e-commerce';
        }
      $output .= "    <td>";
      $output .= "<a href='#'  onclick='editcontact(".$row['id'].");return false;' >".$row['name']."</a>";
      $output .= "<img class='cart_image' src='../wp-content/plugins/communications/images/cart.png' alt='WP e-commerce user' title='WP e-commerce user' />";
      $output .= "</td>\n\r";
      }
      else
        {
        $output .= "    <td><a href='#'  onclick='editcontact(".$row['id'].");return false;' >".stripslashes($row['name'])."</a></td>\n\r";
        }

      $output .= "    <td>".stripslashes($row['organisation'])."</td>\n\r";
      //$output .= "    <td><pre>".print_r($check_bounced,true)."</pre></td>\n\r";
      $output .= "    <td>".stripslashes($row['title'])."</td>\n\r";
      if(count($check_bounced) >= 2)
        {
        $output .= "    <td><span class='bouncedemail'>".$row['email']."</span></td>\n\r";
        }
        else if((count($check_bounced) > 0)&&(count($check_bounced) < 2))
          {
          $output .= "    <td><span class='singlebouncedemail'>".$row['email']."</span></td>\n\r";
          }
          else
            {
            $output .= "    <td>".$row['email']."</td>\n\r";
            }
      $output .= "    <td>".stripslashes($row['mobile'])."</td>\n\r";
      if($row['subscribed'] == 1)
        {
        $output .= "    <td class='yes'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Yes' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='no'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='No' /></form></td>\n\r";
          }
      if($row['e-commerce'] == '1')
        {
        $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input class='disabled' type='submit' name='submit' value='X' disabled='true' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='X' /></form></td>\n\r";
          }
        
        
      $output .= "    <td class='groups'>";
      $output .= "<a href='#' onclick='return manage_user_groups(\"group_box_".$row['id']."\",\"group_expander_icon_".$row['id']."\");'>";
      $output .= "<img class='group_expander_icon' id='group_expander_icon_".$row['id']."' src='../wp-content/plugins/communications/images/icon_window_expand.gif' alt='' title='' />";
      $output .= "<span >Manage Groups</span>";
      $output .= "</a>";
      
      $output .= "    </tr>\n\r";
      
      $output .= "<tr>\n\r";
      $output .= " <td colspan='9' class='group_status'>\n\r";
      $output .= "  <div id='group_box_".$row['id']."' class='group_status' $status_style>\n\r";
      $output .= "  <div>\n\r";
      $output .= "  <strong class='form_group'>Groups</strong>\n\r";
      $output .= "  <form id='form_group_".$row['id']."' method='POST' action='admin.php?page=communications/address_book.php'>\n\r";
      $output .= "  <input type='hidden' name='page' value='".$_GET['page']."' />\n\r";
      $output .= "  <input type='hidden' name='user_id' value='".$row['id']."' />\n\r";
      $output .= "  <input type='hidden' name='action' value='groups' />\n\r";
      $output .= "  <ul>\n\r";
      $group_sql = "SELECT * FROM `".$wpdb->prefix."comms_groups`";
      $groups = $wpdb->get_results($group_sql,ARRAY_A);
      if($groups != null)
        {
        foreach($groups as $group)
          {
          $group_member_sql="SELECT `groupid` FROM `".$wpdb->prefix."comms_user_groups` WHERE `".$wpdb->prefix."comms_user_groups`.`userid` IN ('".$row['id']."') AND `".$wpdb->prefix."comms_user_groups`.`groupid` IN ('".$group['id']."') LIMIT 1";
          $group_member = $wpdb->get_results($group_member_sql,ARRAY_A);
          $selected = '';
          if($group['id'] == $group_member[0]['groupid'])
            {
            $selected = "checked='true'";
            }
          $button_id = "button_".$row['id']."_".$group['id'];
          $output .= "    <li><input type='checkbox' name='group_id[]' $selected value='".$group['id']."' id='".$button_id."'/><label for='$button_id'>".$group['name']."</label>\n\r";
          $counter++;
          }
        }
      $output .= "  </ul>\n\r";
      $output .= "  <input type='submit' name='submit' value='Submit' />\n\r";
      $output .= "  </form>\n\r";
      $output .= "  </div>\n\r";
      $output .= "  </div>\n\r";
      $output .= " </td>\n\r";
      $output .= "</tr>\n\r"; 
      }
    $output .= "</table>\n\r"; 
    }
  return $output;
  }



function wp_user_contacts()
  {    
  global $wpdb;
  $rowcount = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."comms` WHERE `wp-user` IN('1') AND `e-commerce` NOT IN('1')",ARRAY_A);
  $firstlinkpart = "admin.php?page=communications/communications.php&amp;search=".$_GET['search'];
  
  if($rowcount != null)
    {
    $pages = (count($rowcount)/200);
    $output .= "<h2>View WP Users</h2>";
    $output .= comms_contact_list_tabs();
    /* 
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
        $startnum = 0;LIMIT $startnum ,200
        }*/
        
    $list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `wp-user` IN('1') AND `e-commerce` NOT IN('1') ",ARRAY_A);
    
    $output .= "<table class='commsdisplay'>";
    $output .= "  <tr class='toprow'>\n\r";
    // $output .= "    <td>Id</td>\n\r";
    $output .= "    <td>Name</td>\n\r";
    $output .= "    <td>Organisation</td>\n\r";
    $output .= "    <td>Job Title</td>\n\r";
    $output .= "    <td>Email</td>\n\r";
    $output .= "    <td>Mobile Number</td>\n\r";
    $output .= "    <td>Subscribed</td>\n\r";
    $output .= "    <td>Delete</td>\n\r";
    $output .= "    <td>Groups</td>\n\r";
    // $output .= "    <td>Manage Groups</td>\n\r";
    $output .= "  </tr>\n\r";
  
  
  
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
  //     $output .= "    <td>".$row['id']."</td>\n\r";
      //$output .= "    <td class='edit'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Edit' onclick='editcontact(".$row['id'].");return false;' /></form></td>\n\r";
      

      
    if($row['e-commerce'] == '1')
      {
      if(($row['name'] == ''))
        {
        $row['name'] = 'e-commerce';
        }
      $output .= "    <td>";
      $output .= "<a href='#'  onclick='editcontact(".$row['id'].");return false;' >".$row['name']."</a>";
      $output .= "<img class='cart_image' src='../wp-content/plugins/communications/images/cart.png' alt='WP e-commerce user' title='WP e-commerce user' />";
      $output .= "</td>\n\r";
      }
      else
        {
        $output .= "    <td><a href='#'  onclick='editcontact(".$row['id'].");return false;' >".stripslashes($row['name'])."</a></td>\n\r";
        }

      $output .= "    <td>".stripslashes($row['organisation'])."</td>\n\r";
      //$output .= "    <td><pre>".print_r($check_bounced,true)."</pre></td>\n\r";
      $output .= "    <td>".stripslashes($row['title'])."</td>\n\r";
      if(count($check_bounced) >= 2)
        {
        $output .= "    <td><span class='bouncedemail'>".$row['email']."</span></td>\n\r";
        }
        else if((count($check_bounced) > 0)&&(count($check_bounced) < 2))
          {
          $output .= "    <td><span class='singlebouncedemail'>".$row['email']."</span></td>\n\r";
          }
          else
            {
            $output .= "    <td>".$row['email']."</td>\n\r";
            }
      $output .= "    <td>".stripslashes($row['mobile'])."</td>\n\r";
      if($row['subscribed'] == 1)
        {
        $output .= "    <td class='yes'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='Yes' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='no'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='No' /></form></td>\n\r";
          }
      if($row['wp-user'] == '1')
        {
        $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input class='disabled' type='submit' name='submit' value='X' disabled='true' /></form></td>\n\r";
        }
        else
          {
          $output .= "    <td class='delete'><form action='' method='POST'><input type='hidden' name='id' value='".$row['id']."' /><input type='submit' name='submit' value='X' /></form></td>\n\r";
          }
        
        
      $output .= "    <td class='groups'>";
      $output .= "<a href='#' onclick='return manage_user_groups(\"group_box_".$row['id']."\",\"group_expander_icon_".$row['id']."\");'>";
      $output .= "<img class='group_expander_icon' id='group_expander_icon_".$row['id']."' src='../wp-content/plugins/communications/images/icon_window_expand.gif' alt='' title='' />";
      $output .= "<span >Manage Groups</span>";
      $output .= "</a>";
      
      $output .= "    </tr>\n\r";
      
      $output .= "<tr>\n\r";
      $output .= " <td colspan='9' class='group_status'>\n\r";
      $output .= "  <div id='group_box_".$row['id']."' class='group_status' $status_style>\n\r";
      $output .= "  <div>\n\r";
      $output .= "  <strong class='form_group'>Groups</strong>\n\r";
      $output .= "  <form id='form_group_".$row['id']."' method='POST' action='admin.php?page=communications/address_book.php'>\n\r";
      $output .= "  <input type='hidden' name='page' value='".$_GET['page']."' />\n\r";
      $output .= "  <input type='hidden' name='user_id' value='".$row['id']."' />\n\r";
      $output .= "  <input type='hidden' name='action' value='groups' />\n\r";
      $output .= "  <ul>\n\r";
      $group_sql = "SELECT * FROM `".$wpdb->prefix."comms_groups`";
      $groups = $wpdb->get_results($group_sql,ARRAY_A);
      if($groups != null)
        {
        foreach($groups as $group)
          {
          $group_member_sql="SELECT `groupid` FROM `".$wpdb->prefix."comms_user_groups` WHERE `".$wpdb->prefix."comms_user_groups`.`userid` IN ('".$row['id']."') AND `".$wpdb->prefix."comms_user_groups`.`groupid` IN ('".$group['id']."') LIMIT 1";
          $group_member = $wpdb->get_results($group_member_sql,ARRAY_A);
          $selected = '';
          if($group['id'] == $group_member[0]['groupid'])
            {
            $selected = "checked='true'";
            }
          $button_id = "button_".$row['id']."_".$group['id'];
          $output .= "    <li><input type='checkbox' name='group_id[]' $selected value='".$group['id']."' id='".$button_id."'/><label for='$button_id'>".$group['name']."</label>\n\r";
          $counter++;
          }
        }
      $output .= "  </ul>\n\r";
      $output .= "  <input type='submit' name='submit' value='Submit' />\n\r";
      $output .= "  </form>\n\r";
      $output .= "  </div>\n\r";
      $output .= "  </div>\n\r";
      $output .= " </td>\n\r";
      $output .= "</tr>\n\r"; 
      }
    $output .= "</table>\n\r"; 
    }
      
  
  return $output;
  }

?>