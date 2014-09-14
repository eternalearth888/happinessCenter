<div class="wrap">
<h2>Import From CSV</h2>
<br />
<?php
if($_FILES != null)
  {
  $handle = fopen(($_FILES['csvfile']['tmp_name']), "r");
  $row = 0;
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
    //echo nl2br(print_r($data,true));
    if($row == 0)
      {
      if($data[0]=='Title')
        {
        $csvtype = 'outlook';
        }
        else
          {
          $csvtype = 'thunderbird';
          }
      }
      else
        {
        switch($csvtype)
          {
          case 'outlook':
          $name = addslashes($data[1] . " " . $data[3]);
          $email = addslashes($data[57]);
          $mobile = addslashes($data[40]);
          $title = addslashes($data[7]);
          $organisation = addslashes($data[5]);
          break;
  
          case 'thunderbird':
          $name =   addslashes(str_replace('"','',($data[0]." ".$data[1])));
          $email = $data[4];
          $mobile = addslashes(str_replace('"','',$data[10]));
          $title = addslashes(str_replace('"','',$data[23]));
          $organisation = addslashes(str_replace('"','',$data[25]));
          break;
          }
        if($email != null)
          {
          //echo nl2br(print_r($data,true));
          $email = preg_replace("/[\s'\\\"]+/",'',$email);
          $emailexist = $wpdb->get_results("SELECT `email` FROM `".$wpdb->prefix."comms` WHERE `email` IN ('$email') LIMIT 1",ARRAY_A);
          if($emailexist == null)
            {
            $sql = "INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `mobile` , `name` , `title` , `organisation` , `subscribed` , `time` ) VALUES ( '' , '$email', '$mobile', '$name', '$title', '$organisation', '1', NOW( ));";
            $wpdb->query($sql);
            }
          }
        }
    $row++;
    }
  echo "Contacts sucessfully added.";
  }
  else
    {
    ?>
    <form name='submitcsv' method='POST' enctype='multipart/form-data'>
    <table>
      <tr>
        <td>File:</td>
        <td><input type='file' name='csvfile' value='' /></td>
      </tr>
      <tr>
        <td></td>
        <td><input type='submit' name='submit' value='Submit' /></td>
      </tr>
    </table>
    </form>
    <?php
    }
?>
</div>