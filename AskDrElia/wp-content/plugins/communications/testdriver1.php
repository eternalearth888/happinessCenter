<?
require_once("bounce_driver.class.php");


if($_GET['eml']){
    $bounce = file_get_contents("eml/".$_GET['eml']);
    $bounce = BounceHandler::init_bouncehandler($bounce, 'string');
    list($head, $body) = preg_split("/\r\n\r\n/", $bounce, 2);
}
else{
    print "select a bounce email to view the parse";
    if ($handle = opendir('eml')) {
       echo "<P>Files:</P>\n";
    
       /* This is the correct way to loop over the directory. */
       while (false !== ($file = readdir($handle))) {
           if($file=='.' || $file=='..') continue;
           echo "<a href=\"".$_SERVER['PHP_SELF']."?eml=".urlencode($file)."\">$file</a><br>\n";
       }
    
       closedir($handle);
    }
    echo "<HR><P><a href=php.bouncehandler.zip>Download source code</a>";
    exit;
}

echo "<P>Attempts to parse Multipart reports for hard bounces, according to <a href='http://www.faqs.org/rfcs/rfc1892.html'>RFC1892</a> (RFC 1892 - The Multipart/Report Content Type for the Reporting of Mail System Administrative Messages) and <a href='http://www.faqs.org/rfcs/rfc1894.html'>RFC1894</a> (RFC 1894 - An Extensible Message Format for Delivery Status Notifications).  We can reuse this for any well-formed bounces. </P>";


echo "<h2>Here is the parsed head</h2>\n";
$head_hash = BounceHandler::parse_head($head);
echo "<PRE>";
print_r($head_hash);
echo "</PRE>";

if (BounceHandler::is_RFC1892_multipart_report($head_hash) === TRUE){
    print "<h2><font color=red>Looks like an RFC1892 multipart report</font></H2>";
}
else {
    print "<h2><font color=red>Not an RFC1892 multipart report</font></H2>";
    exit;
}


echo "<h2>Here is the parsed report</h2>\n";
echo "<P>Postfix adds an appropriate X- header (X-Postfix-Sender:), so you do not need to create one via phpmailer.  RFC's call for an optional Original-recipient field, but mandatory Final-recipient field is a fair substitute.</P>";
$boundary = $head_hash['Content-type']['boundary'];
$mime_sections = BounceHandler::parse_body_into_mime_sections($body, $boundary);
$rpt_hash = BounceHandler::parse_machine_parsable_body_part($mime_sections['machine_parsable_body_part']);
echo "<PRE>";
print_r($rpt_hash);
echo "</PRE>";



echo "<h2>Here is the error status code</h2>\n";
echo "<P>It's all in the status code, if you can find one.</P>";
for($i=0; $i<count($rpt_hash['per_recipient']); $i++){
    echo "<P>Report #".($i+1)."<BR>\n";
    echo BounceHandler::get_recipient($rpt_hash['per_recipient'][$i]);
    $scode = $rpt_hash['per_recipient'][$i]['Status'];
    echo "<PRE>$scode</PRE>";
    echo BounceHandler::fetch_status_messages($scode);
    echo "</P>\n";
}

echo "<h2>The Diagnostic-code</h2> <P>is not the same as the reported status code, but it seems to be more descriptive, so it should be extracted (if possible).";
for($i=0; $i<count($rpt_hash['per_recipient']); $i++){
    echo "<P>Report #".($i+1)." <BR>\n";
    echo BounceHandler::get_recipient($rpt_hash['per_recipient'][$i]);
    $dcode = $rpt_hash['per_recipient'][$i]['Diagnostic-code']['text'];
    if($dcode){
        echo "<PRE>$dcode</PRE>";
        echo BounceHandler::fetch_status_messages($dcode);
    }
    else{
        echo "<PRE>couldn't decode</PRE>";
    }
    echo "</P>\n";
}

echo "<H2>Grab original To: and From:</H2>\n";
echo "<P>Just in case we don't have an Original-recipient: field, or a X-Postfix-Sender: field, we can retrieve information from the (optional) returned message body part</P>\n";
$head = BounceHandler::get_head_from_returned_message_body_part($mime_sections);
echo "<P>From: ".$head['From']."<br>To: ".$head['To']."<br>Subject: ".$head['Subject']."</P>";


echo "<h2>Here is the body in RFC1892 parts</h2>\n";
echo "<P>Three parts: [first_body_part], [machine_parsable_body_part], and [returned_message_body_part]</P>";
echo "<PRE>";
print_r($mime_sections);
echo "</PRE>";


/*
                $status_code = BounceHandler::format_status_code($rpt_hash['per_recipient'][$i]['Status']);
                $status_code_msg = BounceHandler::fetch_status_messages($status_code['code']);
                $status_code_remote_msg = $status_code['text'];
                $diag_code = BounceHandler::format_status_code($rpt_hash['per_recipient'][$i]['Diagnostic-code']['text']);
                $diag_code_msg = BounceHandler::fetch_status_messages($diag_code['code']);
                $diag_code_remote_msg = $diag_code['text'];
*/
?>