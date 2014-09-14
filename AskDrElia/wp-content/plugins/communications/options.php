<?php
$updates = false;
if($_POST['comms_mail_host'] != null)
  {
  update_option('comms_mail_host', $_POST['comms_mail_host']);
  $updates = true;
  }

if($_POST['comms_mail_port'] != null)
  {
  update_option('comms_mail_port', $_POST['comms_mail_port']);
  $updates = true;
  }

if($_POST['comms_mail_box'] != null)
  {
  update_option('comms_mail_box', $_POST['comms_mail_box']);
  $updates = true;
  }

if($_POST['comms_mail_user'] != null)
  {
  update_option('comms_mail_user', $_POST['comms_mail_user']);
  $updates = true;
  }

if($_POST['comms_mail_password'] != null)
  {
  update_option('comms_mail_password', $_POST['comms_mail_password']);
  $updates = true;
  }

if($_POST['comms_return_email'] != null)
  {
  update_option('comms_return_email', $_POST['comms_return_email']);
  $updates = true;
  }

if($_POST['bounce_return_email'] != null)
  {
  update_option('bounce_return_email', $_POST['bounce_return_email']);
  $updates = true;
  }
if($updates === true)
  {
  echo "<div class='updated'>Thanks, your changes have been applied.</div>";
  }
//add_option('payment_gateway', '', 'the payment gateway to use', 'yes');
?>
<div class="wrap">
<h2>Options</h2>
<br />
<?php
$checked = "checked='true'";
 ?>
<form method='POST'>
<table>
  <tr>
    <td>Display subscribe<br /> form in Sidebar:</td><td><input type='checkbox' <?php echo $checked; ?>  name='sidebar_subscribe' value='true' /></td>
  </tr>
  <tr>
    <td>
    Reply Email:
    </td>
    <td>
    <input class='text' type='text' size='40' value='<?php echo get_option('comms_return_email'); ?>' name='comms_return_email' />
    </td>
  </tr>
  <tr>
    <td></td><td><input type='submit' name='submit' value='Submit' /></td>
  </tr>
</table>

<h2>Bounce Notification Settings</h2>
<table>
  <tr>
    <td>Return Address</td><td><input type='text' name='bounce_return_email' value='<?php echo get_option('bounce_return_email'); ?>' /></td>
  </tr>
  <tr>
    <td>Mail Host</td><td><input type='text' name='comms_mail_host' value='<?php echo get_option('comms_mail_host'); ?>' /></td>
  </tr>
  <tr>
    <td>Mail Port</td><td><input type='text' name='comms_mail_port' value='<?php echo get_option('comms_mail_port'); ?>' /></td>
  </tr>
  <tr>
    <td>Mail Box</td><td><input type='text' name='comms_mail_box' value='<?php echo get_option('comms_mail_box'); ?>' /></td>
  </tr>
  <tr>
    <td>Mail User</td><td><input type='text' name='comms_mail_user' value='<?php echo get_option('comms_mail_user'); ?>' /></td>
  </tr>
  <tr>
    <td>Mail Password</td><td><input type='text' name='comms_mail_password' value='<?php echo get_option('comms_mail_password'); ?>' /></td>
  </tr>
  <tr>
    <td></td><td><input type='submit' name='submit' value='Submit' /></td>
  </tr>
</table>
</form>
</div>