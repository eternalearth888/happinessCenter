<?php
/*
will only use one div tag (save on IDs, significantly reduce the complexity of the javascript)
click will load the corresponding content, and, if applicable, unhide it
clicking "x"  will hide the div, and return it to the origin
*/
$user_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `id` = '$id' LIMIT 1",ARRAY_A);
$user = $user_data[0];
if($_POST['addproject'] == 'true')
  {
  if($_POST['name'] != null)
    {
    $now = date("Y-m-d H:i:s");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_project` ( `id` , `name` , `project_owner`, `project_developer`, `project_client`, `userid` , `completed` , `time` ) VALUES ( '' , '".$_POST['name']."', '".$_POST['project_owner']."', '".$_POST['project_developer']."', '".$_POST['project_client']."', '$id', '0', '".$now."')");
    $project_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `time` = '$now' LIMIT 1",ARRAY_A);
    $project_id = $project_data[0]['id'];
    $quantity = 5;
    for($i = 0;$i < 5; ++$i)
      {
      if($i == 0)
        {
        $status = 1;
        }
        else
          {
          $status = 0;
          }
      $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_project_status` ( `id` , `status` , `stage`, `projid`) VALUES ( '' , '".$status."', '".($i+1)."', '$project_id')");
      }
    }
  }

if($_POST['editproject'] == 'true')
  {
  if(is_numeric($_POST['project_id']))
    {
    $wpdb->query("UPDATE `".$wpdb->prefix."comms_project` SET `name` ='".$wpdb->escape($_POST['name'])."',`project_owner`='".$wpdb->escape($_POST['project_owner'])."',`project_developer`='".$wpdb->escape($_POST['project_developer'])."',`project_client`='".$wpdb->escape($_POST['project_client'])."' WHERE `id`='".$_POST['project_id']."' LIMIT 1");
    }
  }

if($_POST['alterstatus'] == 'true')
  {
  if(is_numeric($_POST['project_status_id']))
    {
    $project_status_state = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project_status` WHERE `id` = '".$_POST['project_status_id']."' LIMIT 1",ARRAY_A);

    $project_status_count = $wpdb->get_results("SELECT COUNT(*) as `count` FROM `".$wpdb->prefix."comms_project_status` WHERE `projid` = '".$project_status_state[0]['projid']."'",ARRAY_A);
    $project_status_count = $project_status_count[0]['count'];

    $project_status_completed_count = $wpdb->get_results("SELECT COUNT(*) as `count` FROM `".$wpdb->prefix."comms_project_status` WHERE `projid` = '".$project_status_state[0]['projid']."' AND `status` = '1'",ARRAY_A);
    $project_status_completed_count = $project_status_completed_count[0]['count'];
    
    $project_count_difference = $project_status_count - $project_status_completed_count;
    switch($project_status_state[0]['status'])
      {
      case 0:
      $wpdb->query("UPDATE `".$wpdb->prefix."comms_project_status` SET `status` = '1' WHERE `id` ='".$project_status_state[0]['id']."' LIMIT 1");
      if($project_count_difference == 1)
        {
        $wpdb->query("UPDATE `".$wpdb->prefix."comms_project` SET `completed` = '1' WHERE `id` ='".$project_status_state[0]['projid']."' LIMIT 1");
        }
      break;

      case 1:
      $wpdb->query("UPDATE `".$wpdb->prefix."comms_project_status` SET `status` = '0' WHERE `id` ='".$project_status_state[0]['id']."' LIMIT 1");
      if($project_count_difference == 0)
        {
        $wpdb->query("UPDATE `".$wpdb->prefix."comms_project` SET `completed` = '0' WHERE `id` ='".$project_status_state[0]['projid']."' LIMIT 1");
        }
      break;
      }
    }
  }
?>
<div class="wrap" id='addproject'>
<h2>Add Project</h2>
<form action='' method='POST' name='subscribe'>
<table class='commsdisplay'>
<tr class='toprow'>
<td>Name</td>
<td>Client</td>
<td>Project Owner</td>
<td>Developer</td>
<td></td>
</tr>
<tr>
<td><input type='text' name='name' value='' /></td>
<td><input type='text' name='project_client' value='' /></td>
<td><input type='text' name='project_owner' value='' /></td>
<td><input type='text' name='project_developer' value='' /></td>
<td>
  <input type='hidden' name='addproject' value='true' />
  <input type='submit' name='submit' value='Submit' />
</td>
</tr>
</table>
</form>
</div>

<div class="wrap" id='editproject'>
<h2>Edit Project</h2>
<form action='' method='POST' name='subscribe'>
<table class='commsdisplay'>
<tr class='toprow'>
<td>Name</td>
<td>Client</td>
<td>Project Owner</td>
<td>Developer</td>
<td></td>
</tr>
<tr>
<td><input type='text' name='name' value='' id='edit_name' /></td>
<td><input type='text' name='project_client' value='' id='edit_client'/></td>
<td><input type='text' name='project_owner' value='' id='edit_owner' /></td>
<td><input type='text' name='project_developer' value='' id='edit_developer'/></td>
<td>
  <input type='hidden' name='project_id' value='' id='edit_id'/>
  <input type='hidden' name='editproject' value='true' />
  <input type='submit' name='submit' value='Submit' />
</td>
</tr>
</table>
</form>
</div>

<div class="wrap">
<?php
if(is_numeric($id))
  {
  echo "<h2>Details - ".stripslashes($user['name'])."</h2>";
  }
  else
    {
    echo "<h2>Projects</h2>";
    }
// onmouseover='hide_show_edit("show")' onmouseout='hide_show_edit("hide")'
//<a href='#' onclick='addproject();return null;'>Add Project</a>
?>
<h3>Current Projects</h3>
<div class='detailsdisplay'>
  <div id='eventdata'>
  <div class='topbar'>
    <span id='project_title'>title</span>
    <img id='note_saving_anim' src='../wp-content/plugins/communications/images/progress.gif' title='Saving...' alt='Saving...' />
    <a href='#' onclick='return edit()' id='editevent' alt='Save' title='Save'>Save</a>
    <a href='#' onclick='return closeevent()' id='closeevent' alt='Close' title='Close'>x</a>
  </div>
  <div id='event_menu'>
    <div>
    test text
    </div>
  </div>
  <form name='project_notes'>
  <textarea name='project_note' id='resizing_textarea' rows='8' cols='42' wrap='hard'>
  </textarea>
  <input type='hidden' name='projectid' value='' id='project_note_id'/>
  <input type='hidden' name='ajax' value='true'  id='project_ajax'/>
  </form>
  </div>
<?php
if(is_numeric($id))
  {
  $current_projects = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `userid` = '$id' AND `completed` = '0'",ARRAY_A);
  }
  else
    {
    $current_projects = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `completed` = '0'",ARRAY_A);
    }
if($current_projects != null)
  {
  $output .= "<table class='detailsdisplay'>";
  $output .= "  <tr class='toprow'>\n\r";
  $output .= "    <td class='name'>Name</td>\n\r";
  $output .= "    <td class='name'>Client</td>\n\r";  
  $output .= "    <td class='project_owner'>Project Owner</td>\n\r";
  $output .= "    <td>Developer</td>\n\r";
  $output .= "    <td class='progress' colspan='2'>
  <table class='progress_stages_container'>
  <tr><td>Progress</td></tr>
    <tr>
      <td class='progress_stages_container'>
       <table class='progress_stages'>
         <tr>
          <td class='progress_stages'>contact</td>
          <td class='progress_stages'>contract</td>
          <td class='progress_stages'>develop</td>
          <td class='progress_stages'>launch</td>
          <td class='progress_stages'>invoice</td>
         </tr>
       </table>
      </td>
    </tr>
  </table>
  </td>\n\r";
  $output .= "  </tr>\n\r";
  foreach($current_projects as $project)
    {
    $alternate = "";
    $num++;
    if(($num % 2) != 0)
      {
      $alternate = "class='alt'";
      }
    $output .= "  <tr $alternate>\n\r";
    $output .= "    <td><a href='#' onclick='editproject(".$project['id'].");return false;' >".stripslashes($project['name'])."</a></td>\n\r";
    $output .= "    <td>".stripslashes($project['project_client'])."</td>\n\r";
    $output .= "    <td>".stripslashes($project['project_owner'])."</td>\n\r";
    $output .= "    <td>".stripslashes($project['project_developer'])."</td>\n\r";
    $output .= "    <td class='project_status'>";
    $project_status_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project_status` WHERE `projid` = '".$project['id']."'",ARRAY_A);
    $stage_completed = 0;
    $rowcount = count($project_status_data);
    $percentage = 100/$rowcount;
    $output .= "      <table class='project_status'><tr>";
    foreach($project_status_data as $project_status)
      {
      if($project_status['status'] == 1)
        {
        $output .= "<td class='project_status_done' style='width: $percentage%;'>";
        $output .= "<div class='project_status'>";
        $output .= "  <form action='' method='post'>";
        $output .= "  <input type='hidden' name='alterstatus' value='true'/>";
        $output .= "  <input type='hidden' name='project_status_id' value='".$project_status['id']."'/>";
        $output .= "  <input type='submit' name='submit' value=''/>";
        $output .= "  </form>";
        $output .= "  <a class='notes_image' href='#' onclick='return displaynotes(".$project_status['id'].",event);'><img src='../wp-content/plugins/communications/images/notes.png' title='' alt='' /></a>";
        $output .= "</div>";
        $output .= "</td>";
        $stage_completed++;
        }
        else
        {
        $output .= "<td class='project_status_undone' style='width: $percentage%;'>";
        $output .= "<div class='project_status'>";
        $output .= "  <form action='' method='post'>";
        $output .= "  <input type='hidden' name='alterstatus' value='true'/>";
        $output .= "  <input type='hidden' name='project_status_id' value='".$project_status['id']."'/>";
        $output .= "  <input type='submit' name='submit' value=''/>";
        $output .= "  </form>";
        $output .= "  <a class='notes_image' href='#' onclick='return displaynotes(".$project_status['id'].",event);'><img src='../wp-content/plugins/communications/images/notes.png' title='' alt='' /></a>";
        $output .= "</div>";
        $output .= "</td>";
        }
      }
    $output .= "</tr></table>\n\r";
    $output .= "    </td>\n\r";
    $output .= "    <td class='percentage'>";
    $percent_done = number_format($stage_completed*(100/$rowcount),0)."%";
    $output .= "<span class='percentage'>".$percent_done."</span>";
    $output .= "    </td>\n\r";
    $output .= "  </tr>\n\r";
    }
  $output .= "</table>";
  }
  else
    {
    $output .= "<strong>There are no Current Projects</strong>";
    }
   
echo $output;
$output = '';
$num = 0;
?>
</div>
<h3>Completed Projects</h3>
<div class='detailsdisplay'>
<?php
if(is_numeric($id))
  {
  $current_projects = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `userid` = '$id' AND `completed` = '1'",ARRAY_A);
  }
  else
    {
    $current_projects = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project` WHERE `completed` = '1'",ARRAY_A);
    }
if($current_projects != null)
  {
  $output .= "<table class='detailsdisplay'>";
  $output .= "  <tr class='toprow'>\n\r";
  $output .= "    <td class='name'>Name</td>\n\r";
  $output .= "    <td class='name'>Client</td>\n\r";  
  $output .= "    <td class='project_owner'>Project Owner</td>\n\r";
  $output .= "    <td>Developer</td>\n\r";
  $output .= "    <td class='progress' colspan='2'>
  <table class='progress_stages_container'>
  <tr><td>Progress</td></tr>
    <tr>
      <td class='progress_stages_container'>
       <table class='progress_stages'>
         <tr>
          <td class='progress_stages'>contact</td>
          <td class='progress_stages'>contract</td>
          <td class='progress_stages'>develop</td>
          <td class='progress_stages'>launch</td>
          <td class='progress_stages'>invoice</td>
         </tr>
       </table>
      </td>
    </tr>
  </table>
  </td>\n\r";
  $output .= "  </tr>\n\r";
  foreach($current_projects as $project)
    {
    $alternate = "";
    $num++;
    if(($num % 2) != 0)
      {
      $alternate = "class='alt'";
      }
    $output .= "  <tr $alternate>\n\r";
    $output .= "    <td><a href='#' onclick='editproject(".$project['id'].");return false;' >".stripslashes($project['name'])."</a></td>\n\r";
    $output .= "    <td>".stripslashes($project['project_client'])."</td>\n\r";
    $output .= "    <td class='description'>".stripslashes($project['project_owner'])."</td>\n\r";
    $output .= "    <td class='description'>".stripslashes($project['project_developer'])."</td>\n\r";
    $output .= "    <td>";
    $project_status_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms_project_status` WHERE `projid` = '".$project['id']."'",ARRAY_A);
    $stage_completed = 0;
    $rowcount = count($project_status_data);
    $percentage = 100/$rowcount;
    $output .= "      <table class='project_status'><tr>";
    foreach($project_status_data as $project_status)
      {
      if($project_status['status'] == 1)
        {
        $output .= "<td class='project_status_done_complete' style='width: $percentage%;'>";
        $output .= "<div class='project_status'>";
        $output .= "  <form action='' method='post'>";
        $output .= "  <input type='hidden' name='alterstatus' value='true'/>";
        $output .= "  <input type='hidden' name='project_status_id' value='".$project_status['id']."'/>";
        $output .= "  <input type='submit' name='submit' value=''/>";
        $output .= "  </form>";
        $output .= "  <a class='notes_image' href='#' onclick='return displaynotes(".$project_status['id'].",event);'><img src='../wp-content/plugins/communications/images/notes.png' title='' alt='' /></a>";
        $output .= "</div>";
        $output .= "</td>";
        $stage_completed++;
        }
        else
        {
        $output .= "<td class='project_status_undone' style='width: $percentage%;'>";
        $output .= "<div class='project_status'>";
        $output .= "  <form action='' method='post'>";
        $output .= "  <input type='hidden' name='alterstatus' value='true'/>";
        $output .= "  <input type='hidden' name='project_status_id' value='".$project_status['id']."'/>";
        $output .= "  <input type='submit' name='submit' value=''/>";
        $output .= "  </form>";
        $output .= "  <a class='notes_image' href='#' onclick='return displaynotes(".$project_status['id'].",event);'><img src='../wp-content/plugins/communications/images/notes.png' title='' alt='' /></a>";
        $output .= "</div>";
        $output .= "</td>";
        }
      }
    $output .= "</tr></table>\n\r";
    $output .= "    </td>\n\r";
  
    $output .= "    <td class='percentage'>";
    $percent_done = number_format($stage_completed*(100/$rowcount),0)."%";
    $output .= "<span class='percentage'>".$percent_done."</span>";
    $output .= "    </td>\n\r";
    $output .= "  </tr>\n\r";
    }
  $output .= "</table>";
  }
  else
    {
    $output .= "<strong>There are no Completed Projects.</strong>";
    }

echo $output;
$output = '';
?>
<?php
/*
</div>
<a href='admin.php?page=communications/communications.php'>Go back to Address Book</a>
</div>
*/
?>
