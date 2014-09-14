<?php
require_once(ABSPATH . WPINC . '/class-phpmailer.php');
require_once(ABSPATH . WPINC . '/class-smtp.php');

?>
<div class="wrap">
<h2>Send Email</h2>
<br />
<?php
if(isset($_POST['emailbody']))
  {
  if($_POST['sendto'] == 'subscribers')
    {
    $sql = "SELECT `id`,`email` FROM `".$wpdb->prefix."comms` WHERE `subscribed` = 1";
    }
    else if($_POST['sendto'] == 'groups')
      {
      $counter = 0;
      foreach($_POST['groups'] as $groupid)
        {
        if($counter > 0)
          {
          $group_sql .= ',';
          }
        $group_sql .= "'".$groupid."'";
        $counter++;
        }
      $sql = "SELECT DISTINCT ".$wpdb->prefix."comms.id, ".$wpdb->prefix."comms.email FROM `".$wpdb->prefix."comms`,`".$wpdb->prefix."comms_user_groups` WHERE ".$wpdb->prefix."comms.id = ".$wpdb->prefix."comms_user_groups.userid AND ".$wpdb->prefix."comms_user_groups.groupid IN ($group_sql)";
      }
      else
        {
        $sql = "SELECT `id`,`email` FROM `".$wpdb->prefix."comms`";
        }
  
  $list = $wpdb->get_results($sql,ARRAY_A);
  /*
  echo "<pre>".print_r($list,true)."</pre>";
  exit();
  */
  $curtime = time();
  if($_SESSION['checksentmail'] == '')
    {
    $inc = 0;
    $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_message_log` ( `id`, `subject`, `message`, `type`, `datetime`) VALUES ('', '".$_POST['subject']."', '".$_POST['emailbody']."', '0', NOW( ));");
    $msgid = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."comms_message_log` WHERE `subject` = '".$_POST['subject']."' AND `message` = '".$_POST['emailbody']."' LIMIT 1",ARRAY_A);
    
    if(get_option('bounce_return_email') == null)
      {
      $bounced_mail_address = get_option('comms_return_email');      
      }
      else
      {
      $bounced_mail_address = get_option('bounce_return_email');
      }
     
    
    $mailer = new PHPMailer();
    $mailer->From = get_option('comms_return_email');    
    $mailer->FromName = "";
    $mailer->Sender = $bounced_mail_address;
    $mailer->To = "Instinct Subscriber";
    $mailer->IsSendmail();
    $mailer->IsHTML(true);
    $mailer->Subject = stripslashes($_POST['subject']);
    $mailer->Body = nl2br(stripslashes($_POST['emailbody']));
    
    foreach($list as $email)
      {
      $inc++;
      $mailer->AddAddress($email['email'], "");
      $mailer->Send();
      $mailer->ClearAddresses();
      $wpdb->query("INSERT INTO `".$wpdb->prefix."comms_email_log` ( `id` , `email` , `commsid` , `messageid` , `bounced` , `errorcode` , `datetime` ) VALUES ( '' , '".$email['email']."', '".$email['id']."', '".$msgid[0]['id']."', '0', '', NOW( ));");
      }
    //$_SESSION['checksentmail'] = 'sent';
    }
  echo "Thank you, the email has been sent.";
  }
  else
    {
    $_SESSION['checksentmail'] = '';
?>
<table class='sendmessage'>
<tr><td></td><td><form method='POST' name='sendemail'></td></tr>
<tr><td colspan='2'><input onclick='group_checkboxes("hide");' id='checktrainees' type='radio' name='sendto' value='subscribers' /> <label for='checktrainees'>Send Email to Subscribers</label></td></tr>
<tr><td colspan='2'><input onclick='group_checkboxes("hide");' id='checkemployers' checked='true' type='radio' name='sendto' value='everyone' /> <label for='checkemployers'>Send Email to Everyone</label></td></tr>
<tr><td colspan='2'><input onclick='group_checkboxes("show");' id='checkgroups' type='radio' name='sendto' value='groups' /> <label for='checkgroups'>Send Email to Group / Campaign</label></td></tr>
<tr><td colspan='2'>
<div id='group_checkboxes'>
<?php
$groups = $wpdb->get_results("SELECT `id`,`name` FROM `".$wpdb->prefix."comms_groups`",ARRAY_A);

if($groups != null)
  {
  foreach($groups as $group)
    {
    echo "<input type='radio' name='spacers' value='null' disabled='true' style='visibility: hidden;' /> <input type='checkbox' name='groups[]' id='groups_".$group['id']."' value='".$group['id']."'> <label for='groups_".$group['id']."'>".$group['name']."</label><br />";
    }
  }
  else
    {
    echo "<br />There are no Groups / Campaigns<br />";
    echo "<a href='admin.php?page=communications/groups.php'>Add Group / Campaign</a>";
    }
?></div>
<br /><br /></td></tr>
<tr><td>Subject: </td><td><input id='emailsubject' type='text' name='subject' value='' /></td></tr>
<tr><td>Content: </td><td><textarea id='emailbody' name='emailbody' class='sendemailtextarea' id='sendemailtextarea'></textarea></td></tr>
<tr><td></td><td><input type='submit' name='submit' value='Submit' />&nbsp;&nbsp;<input onclick='emailpreview()' type='button' name='preview' value='Preview' />&nbsp;&nbsp;Warning: This is currently live.</td></tr>
</form>
<?php
    }
?>
</table>
<//div>