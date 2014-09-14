<?php

// Establish two files to use, one to hold the count and another the ip's
$countFile = "./counter/count.db";
$ipFile    = "./counter/ip.db";

// Read the old count from the count file
$open = fopen($countFile, "r");              // Open the count file
$count = fread($open, filesize($countFile)); // Read the whole file
fclose($open);                               // Close the count file

// Increment the count to get the new hit count 
$newCount = $count+1;

// Open the ip file and read the date
$lines = file($ipFile);
foreach ($lines as $line_num => $line)
{
   // Find the two quote (") delimeters (on either side of the date)
   //echo $line;
   $firstPos = strpos($line,'"');
   $secPos = strpos($line,'"');
   $fileDate = substr($line,$firstPos+1,($secPos-$firstPos)-2);
   break;
}

// Get today's date
$todaysDate = date('d');

// Read the remote ip address
$remoteIp = $_SERVER['REMOTE_ADDR'];

// Compare the date in the file with today's date
if($todaysDate != $fileDate)
{
   // The date in the ip file doesn't match today's date, so erase
   // the whole ip file and start over
   $openIpFile = fopen($ipFile, "w");
   fwrite($openIpFile, "DATE= \"$todaysDate\"");
   fwrite($openIpFile, "\n\n$remoteIp");
   fclose($openIpFile);

   // Write the new count to the count file
   $count = $newCount;
   $openCountFile = fopen($countFile, "w");
   fwrite($openCountFile, $count);
   fclose($openCountFile);
}
else
{
   // The date in the ip file matches today's date.  Search for the
   // ip in the list.
   $openIpFile = fopen($ipFile, "r");
   $ips = fread($openIpFile, filesize($ipFile));  // Read in the whole file
   fclose($openIpFile);

   // If the remote ip is not found in this file...
   if(!strpos($ips, $remoteIp))
   {
      //...record it in the file

      // Write the remote ip to the ip file
      $openIpFile = fopen($ipFile, "a");
      fwrite($openIpFile,"\n\n");
      fwrite($openIpFile, $remoteIp);
      fclose($openIpFile);

      // Write the count to the count file
      $count = $newCount;
      $openCountFile = fopen($countFile, "w");
      fwrite($openCountFile, $count);
      fclose($openCountFile);
   }
}

echo($count);
?>

