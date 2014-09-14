<?php
require_once('bounce_driver.php');
$bounce = BounceHandler::init_bouncehandler($bounce, 'string');

$mailhost = get_option('comms_mail_host');
$mailport = get_option('comms_mail_port');
$mailmailbox = get_option('comms_mail_box');

$mailuser = get_option('comms_mail_user');
$mailpass = get_option('comms_mail_password');


$mailbox =  @imap_open('{'.$mailhost.':'.$mailport.'/imap/notls}'.$mailmailbox, $mailuser, $mailpass);
if($mailbox !== false)
  {
  $headers = imap_headers($mailbox);
  if(!is_dir("/tmp/csv_script"))
    {
    mkdir("/tmp/csv_script");
    }
  if(!is_dir("/tmp/csv_script/csv"))
    {
    mkdir("/tmp/csv_script/csv");
    }
  foreach($headers as $header)
    {
    preg_match("/[\d]+/", $header, $mail_count);
    $fetch_header = imap_fetchheader($mailbox, $mail_count[0]);
    $header_info = imap_headerinfo($mailbox, $mail_count[0], 256, 256);
    $header_decode = imap_mime_header_decode($header_info->fetchsubject);
    $from_email = $header_info->from[0]->host;
    $body = imap_body($mailbox, $mail_count[0]);
    $msg_structure = imap_fetchstructure($mailbox, $mail_count[0]);
    $image_count = 1;
    if(!empty($msg_structure->parts))
        {
        $head_hash = BounceHandler::parse_head($fetch_header);
        $boundary = $head_hash['Content-type']['boundary'];
        $mime_sections = BounceHandler::parse_body_into_mime_sections($body, $boundary);
        $rpt_hash = BounceHandler::parse_machine_parsable_body_part($mime_sections['machine_parsable_body_part']);
        $body_hash = BounceHandler::parse_returned_message_body_part($mime_sections['returned_message_body_part']);
        $bounce_vars['recipient'] = $body_hash['per_message']['To'];
        $bounce_vars['error_code'] = $rpt_hash['per_recipient'][0]['Diagnostic-code']['text'];
        $bounce_vars['message_subject'] = $body_hash['per_message']['Subject'];
        $bounce_vars['message_body'] = $body_hash['per_message']['Body'];
        $bounce_vars['time_sent'] = $body_hash['per_message']['Date'];
        comms_log_bounced_email($bounce_vars);
      //imap_delete($mailbox, $mail_count[0]);
        }
    }
  //imap_expunge($mailbox);
  imap_close($mailbox);
  }
  else
    {
    $output .= "<strong>Warning:</strong> Bounce notification is not properly set up, and will not work until it is properly set up in the Options page<br /><br />";
    }
?>