<div class="wrap">
<h2>View Logs</h2>
Please select a log to view:<br />
<ul>
<?php
echo "<li><a href='?page=".$_GET['page']."'>Email</a></li>";
if(is_file(ABSPATH."wp-content/plugins/communications/sms/sms_functions.php"))
  {
  if($sms_active == true)
    {
    echo "<li><a href='?page=".$_GET['page']."&amp;viewsms=true'>SMS/Text</a></li>";
    }
  }
?>
</ul>
<?php
if(is_numeric($_GET['messageid']))
  {
  $output .= "<h2>Email</h2>";
  $id = $_GET['messageid'];
  $message_data = $wpdb->get_results("SELECT `id` , `subject` , `message` , UNIX_TIMESTAMP(`datetime`) AS `timestamp`
    FROM `".$wpdb->prefix."comms_message_log` WHERE `id` = '$id'",ARRAY_A);
  $output .= "<h3>".$message_data[0]['subject']."</h3>";
  $output .= "<p>".$message_data[0]['message']."</p>";
  
  $output .= "<a href='?page=".$_GET['page']."'>Go back to Email Log</a>";
  }
  else if($_GET['viewsms']=='true')
    {
    if(is_file(ABSPATH."wp-content/plugins/communications/sms/sms_functions.php"))
      {
      if($sms_active == true)
        {
        require("sms/sms_log.php");
        }
      }
    }
    else
      {
      $output .= "<h2>Email Log</h2>";
      require_once('readmail.php');
      $output .= "<table class='commsdisplay' id='logdisplay'>";
      $output .= "  <tr class='toprow'>\n\r";
      //$output .= "    <td>No.</td>\n\r";
      $output .= "    <td>Subject</td>\n\r";
      $output .= "    <td>Message</td>\n\r";
      $output .= "    <td>No. Sent</td>\n\r";
      $output .= "    <td>No. Bounced</td>\n\r";
      $output .= "    <td>Date</td>\n\r";
      $output .= "  </tr>\n\r";
      
      $message_data = $wpdb->get_results("SELECT `id` , `subject` , `message` , UNIX_TIMESTAMP(`datetime`) AS `timestamp`
      FROM `".$wpdb->prefix."comms_message_log` WHERE `type` = '0'",ARRAY_A);
      
      if($message_data != null)
        {
        foreach($message_data as $message)
          {
          $sent_count = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."comms_email_log` WHERE `messageid` = '".$message['id']."'",ARRAY_A);
          $bounced_count = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."comms_email_log` WHERE `bounced` = '1' AND `messageid` = '".$message['id']."'",ARRAY_A);
          
          $alternate = "";
          $num++;
          if(($num % 2) != 0)
            {
            $alternate = "class='alt'";
            }
          $output .= "  <tr $alternate>\n\r";
          //$output .= "    <td>".$message['id']."</td>\n\r";
          $output .= "    <td class='subjectcol'><a href='?page=".$_GET['page']."&amp;messageid=".$message['id']."'>".$message['subject']."</td>\n\r";
          $displaymessage = substr($message['message'],0,14);
          if($displaymessage != $message['message'])
            {
            $displaymessage_arr = explode(" ",$displaymessage);
            $lastword = count($displaymessage_arr);
            if($lastword > 1)
              {
              unset($displaymessage_arr[$lastword-1]);
              $displaymessage = '';
              $j = 0;
              foreach($displaymessage_arr as $displaymessage_row)
                {
                $j++;
                $displaymessage .= $displaymessage_row;
                if($j < $lastword -1)
                  {
                  $displaymessage .= " ";
                  }
                }
              }
            $displaymessage .= "...";
            }
          $output .= "    <td class='messagecol'>".$displaymessage."</td>\n\r";
          $output .= "    <td>".$sent_count[0]['count']."</td>\n\r";
          $output .= "    <td>".$bounced_count[0]['count']."</td>\n\r";
          $output .= "    <td>".date("h:i:s A j/n/Y",$message['timestamp'])."</td>\n\r";
          $output .= "  </tr>\n\r";
          }
        }
      $output .= "</table>";
      }
echo $output;
?>
</div>