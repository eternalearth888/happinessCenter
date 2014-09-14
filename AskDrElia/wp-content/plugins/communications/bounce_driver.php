<?php
/*
 * Title: PHP Bounce Handler Class
 * File name: bounce_driver.class.php
 * Class name: BounceHandler
 * Author: Chris Fortune http://www.spamEater.com
 * Description: Attempts to parse Multipart reports for hard bounces, according to RFC1892 
 * (RFC 1892 - The Multipart/Report Content Type for the Reporting of Mail System Administrative 
 * Messages) and RFC1894 (RFC 1894 - An Extensible Message Format for Delivery Status 
 * Notifications). We can reuse this for any well-formed bounces.
 * Date: 8/Nov/2005
 *
 * If you improve the code, please send me a copy !!  :)
 *
 * License: BSD Open-Source license
 * 
 *******************************************************************************************
 * Redistribution and use in source and binary forms, with or without modification, are permitted 
 * provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list of 
 * conditions and the following disclaimer in the documentation and/or other materials provided 
 * with the distribution.
 * - Neither the name of Fortune Software nor the names of its contributors may be used to 
 * endorse or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY 
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL 
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************************/

class BounceHandler{

	function init_bouncehandler($blob, $format){
    	// takes three types of data: xml array, string, or array
    	// roll your own
	    if($format=='xml_array'){
	        $strEmail = "";
	        for($i=0; $i<$blob; $i++){
	            $out = "";
	            $out = preg_replace("/<HEADER>/i", "", $blob[$i]);
	            $out = preg_replace("/</HEADER>/i", "", $blob[$i]);
	            $out = preg_replace("/<MESSAGE>/i", "", $blob[$i]);
	            $out = preg_replace("/</MESSAGE>/i", "", $blob[$i]);
	            $out = str_replace("\r\n", "\n", $blob[$i]);
	            $out = str_replace("\n", "\r\n", $blob[$i]);
	            $strEmail .= $out;
	        }
	    }
	    else if($format=='string'){
	        $strEmail = str_replace("\r\n", "\n", $blob);
	        $strEmail = str_replace("\n", "\r\n", $blob);
	    }
	    else if($format=='array'){
	        $strEmail = "";
	        for($i=0; $i<$blob; $i++){
	            $out = str_replace("\r\n", "\n", $blob[$i]);
	            $out = str_replace("\n", "\r\n", $blob[$i]);
	            $strEmail .= $out;
	        }
	    }
	    return $strEmail;
	}
	
	function is_RFC1892_multipart_report($head_hash){
	    // if it looks like a bounce and smells like a bounce ...
	    return $head_hash['Content-type'][type]=='multipart/report' 
	       &&  $head_hash['Content-type']['report-type']=='delivery-status' 
	       && $head_hash['Content-type'][boundary]!=='';
	}
	
	function parse_head($headers){
	    if(!is_array($headers)) $headers = explode("\r\n", $headers);
	    $hash = BounceHandler::standard_parser($headers);
	    // get a little more complex
	    $arrRec = explode('|', $hash['Received']);
	    $hash['Received']= $arrRec;
	    if(preg_match('/Multipart\/Report/i', $hash['Content-type'])){
	        $multipart_report = explode (';', $hash['Content-type']);
	        $hash['Content-type']='';
	        $hash['Content-type']['type'] = 'multipart/report';
	        foreach($multipart_report as $mr){
	            if(preg_match('/(.*)=(.*)?/i', $mr, $matches)){
	                $hash['Content-type'][strtolower(trim($matches[1]))]= str_replace('"','',$matches[2]);
	            }
	        }
	    }
	    return $hash;
	}
	
	function parse_body_into_mime_sections($body, $boundary){
	    // bounces come in three sections, separated by double line returns
	    if(!$boundary) return array();
	    if(is_array($body)) $body = implode("\r\n", $body);
	    $body = explode($boundary, $body);
	    $mime_sections['first_body_part'] = $body[1];
	    $mime_sections['machine_parsable_body_part'] = $body[2];
	    $mime_sections['returned_message_body_part'] = $body[3];
	    return $mime_sections;
	}
	
	
	function standard_parser($content){ // associative array orstr
	    // receives email head as array of lines
	    // simple parse (Entity: value\n)
	    if(!is_array($content)) $content = explode("\r\n", $content);
	    foreach($content as $line){
	        if(preg_match('/([^\s.]*):\s(.*)/', $line, $array)){
	            $entity = ucfirst(strtolower($array[1]));
	            if(! $hash[$entity]){
	                $hash[$entity] = $array[2];
	            }
	            else if($hash['Received']){ 
	                // grab extra Received headers :(
	                // pile it on with pipe delimiters, 
	                // oh well, SMTP is broken in this way
	                if ($entity and $array[2] and $array[2] != $hash[$entity]){
	                    $hash[$entity] .= "|" . $array[2];
	                }
	            }
	        }
	        else{
	            if ($entity){
	                $hash[$entity] .= " $line";
	            }
	        }
	    }
	    return $hash;
	}
	
	function parse_machine_parsable_body_part($str){
	    //Per-Message DSN fields
	    $hash = BounceHandler::parse_dsn_fields($str);
	    $hash['mime_header'] = BounceHandler::standard_parser($hash['mime_header']);
	    $hash['per_message'] = BounceHandler::standard_parser($hash['per_message']);
	    if($hash['per_message']['X-postfix-sender']){
	        $arr = explode (';', $hash['per_message']['X-postfix-sender']);
	        $hash['per_message']['X-postfix-sender']='';
	        $hash['per_message']['X-postfix-sender']['type'] = trim($arr[0]);
	        $hash['per_message']['X-postfix-sender']['addr'] = trim($arr[1]);
	    }
	    if($hash['per_message']['Reporting-mta']){
	        $arr = explode (';', $hash['per_message']['Reporting-mta']);
	        $hash['per_message']['Reporting-mta']='';
	        $hash['per_message']['Reporting-mta']['type'] = trim($arr[0]);
	        $hash['per_message']['Reporting-mta']['addr'] = trim($arr[1]);
	    }
	    //Per-Recipient DSN fields
	    for($i=0; $i<count($hash['per_recipient']); $i++){
	        $temp = BounceHandler::standard_parser(explode("\r\n", $hash['per_recipient'][$i]));
	        $arr = explode (';', $temp['Final-recipient']);
	        $temp['Final-recipient']='';
	        $temp['Final-recipient']['type'] = trim($arr[0]);
	        $temp['Final-recipient']['addr'] = trim($arr[1]);
	        $arr = explode (';', $temp['Original-recipient']);
	        $temp['Original-recipient']='';
	        $temp['Original-recipient']['type'] = trim($arr[0]);
	        $temp['Original-recipient']['addr'] = trim($arr[1]);
	        $arr = explode (';', $temp['Diagnostic-code']);
	        $temp['Diagnostic-code']='';
	        $temp['Diagnostic-code']['type'] = trim($arr[0]);
	        $temp['Diagnostic-code']['text'] = trim($arr[1]);
	        $hash['per_recipient'][$i]='';
	        $hash['per_recipient'][$i]=$temp;
	    }
	    return $hash;
	}
	
	function parse_returned_message_body_part($str){
	    //Per-Message DSN fields
	    $hash = BounceHandler::parse_dsn_fields($str);
	    $hash['mime_header'] = BounceHandler::standard_parser($hash['mime_header']);
	    $hash['per_message'] = BounceHandler::standard_parser($hash['per_message']);
	    if($hash['per_message']['X-postfix-sender']){
	        $arr = explode (';', $hash['per_message']['X-postfix-sender']);
	        $hash['per_message']['X-postfix-sender']='';
	        $hash['per_message']['X-postfix-sender']['type'] = trim($arr[0]);
	        $hash['per_message']['X-postfix-sender']['addr'] = trim($arr[1]);
	    }
	    if($hash['per_message']['Reporting-mta']){
	        $arr = explode (';', $hash['per_message']['Reporting-mta']);
	        $hash['per_message']['Reporting-mta']='';
	        $hash['per_message']['Reporting-mta']['type'] = trim($arr[0]);
	        $hash['per_message']['Reporting-mta']['addr'] = trim($arr[1]);
	    }
	    $hash['per_message']['Body'] = trim($hash['per_recipient'][0]);
	    unset($hash['per_recipient']);
	    return $hash;
	}
	
	function get_head_from_returned_message_body_part($mime_sections){
	    // each returned message body part has sub-sections too!
	    // this is partly why normal MIME decoding doesn't work for SMTP bounces
	    $temp = explode("\r\n\r\n", $mime_sections[returned_message_body_part]);
	    $head = BounceHandler::standard_parser($temp[1]);
	    $head['From'] = BounceHandler::extract_address($head['From']);
	    $head['To'] = BounceHandler::extract_address($head['To']);
	    return $head;
	}
	
	function extract_address($str){
	    // could be improved
	    $from_stuff = preg_split('/[ \"\'\<\>:\(\)\[\]]/', $str);
	    foreach ($from_stuff as $things){
	        if (strpos($things, '@')!==FALSE){$from = $things;}
	    }
	    return $from;
	}
	
	function get_recipient($per_rcpt){
	    if($per_rcpt['Original-recipient']['addr'] !== '') return $per_rcpt['Original-recipient']['addr'];
	    else if($per_rcpt['Final-recipient']['addr'] !== '') return $per_rcpt['Final-recipient']['addr'];
	}
	
	function parse_dsn_fields($dsn_fields){
	    if(!is_array($dsn_fields)) $dsn_fields = explode("\r\n\r\n", $dsn_fields);
	    $j = 0;
	    for($i=0; $i<count($dsn_fields); $i++){
	        if($i==0) $hash['mime_header'] = $dsn_fields[0];
	        if($i==1) $hash['per_message'] = $dsn_fields[1];
	        else if($i >=2) {
	            if($dsn_fields[$i] == '--') continue;
	            $hash['per_recipient'][$j] = $dsn_fields[$i];
	            $j++;
	        }
	    }
	    return $hash;
	}
	
	function format_status_code($code){
	    if(preg_match('/([245]\.[01234567]\.[012345678])(.*)/', $code, $matches)){
	        $ret['code'] = $matches[1];
	        $ret['text'] = $matches[2];
	    }
	    else if(preg_match('/([245][01234567][012345678])(.*)/', $code, $matches)){
	        preg_match_all("/./", $matches[1], $out);
	        $ret['code'] = $out[0];
	        $ret['text'] = $matches[2];
	    }
	    return $ret;
	}
	
	function fetch_status_messages($code){
	    include ("rfc1893.error.codes.php");
	    $ret = BounceHandler::format_status_code($code);
	    $arr = explode('.', $ret['code']);
	    $str = "<P><B>". $status_code_classes[$arr[0]]['title'] . "</B> - " .$status_code_classes[$arr[0]]['descr']. "  <B>". $status_code_subclasses[$arr[1].".".$arr[2]]['title'] . "</B> - " .$status_code_subclasses[$arr[1].".".$arr[2]]['descr']. "</P>";
	    return $str;
	}
	
	function decode_diagnostic_code($dcode){
	    if(preg_match("/(\d\.\d\.\d)\s/", $dcode, $array)){
	        return $array[1];
	    }
	    else if(preg_match("/(\d\d\d)\s/", $dcode, $array)){
	        return $array[1];
	    }
	}
}
?>