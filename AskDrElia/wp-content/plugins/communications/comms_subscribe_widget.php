<?php
function widget_comms_subscribe($args)
  {
  global $wpdb;
  extract($args);
  $options = get_option('widget_comms_subscribe');
  $title = empty($options['title']) ? __('Subscribe') : $options['title'];
  echo $before_widget;
  $full_title = $before_title . $title . $after_title;
  echo $full_title;
  if($_POST['emailaddress'] != null)
    {
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."comms` WHERE `email` = '".$wpdb->escape($_POST['emailaddress'])."' LIMIT 1",ARRAY_A);
    if($data == null)
      {
      $wpdb->query("INSERT INTO `".$wpdb->prefix."comms` ( `id` , `email` , `subscribed`, `time` ) VALUES ('' , '".$wpdb->escape($_POST['emailaddress'])."', '1', NOW( ));") ;
      $message = "<strong>Thank you for subscribing.<strong><br /><br />";
      }
      else
        {
        $message = "<strong>You are already subscribed.</strong><br /><br />";
        }
    }
    
  if($_COOKIE['wp_comms_email'] == null)
    {
    $prevemail = $_POST['emailaddress'];
    }
    else
      {
      $prevemail = $_COOKIE['wp_comms_email'];
      }
  echo "
  $message
  <div class='subscribe_widget'>
    <form name='subscribe' method='POST' action='' >
      <input type='text' name='emailaddress' class='emailaddress' value='".$prevemail."' />
      <input type='submit' name='submit' value='Submit' class='subscribe' />
    </form>
  </div>
  ";
  echo $after_widget; 
  }

function widget_comms_subscribe_control()
  {
  $options = $newoptions = get_option('widget_comms_subscribe');
  if ( $_POST["comms_subscribe-submit"] ) {
          $newoptions['title'] = strip_tags(stripslashes($_POST["comms_subscribe-title"]));
  }
  if ( $options != $newoptions ) {
          $options = $newoptions;
          update_option('widget_comms_subscribe', $options);
  }
  $title = htmlspecialchars($options['title'], ENT_QUOTES);
  ?>
  <p><label for="comms_subscribe-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="comms_subscribe-title" name="comms_subscribe-title" type="text" value="<?php echo $title; ?>" /></label></p>
  <input type="hidden" id="comms_subscribe-submit" name="comms_subscribe-submit" value="1" />
  <?php
  }

function widget_comms_subscribe_init()
  {
  if(function_exists('register_sidebar_widget'))
    {
    register_sidebar_widget('Communications Subscribe', 'widget_comms_subscribe');
    register_widget_control('Communications Subscribe', 'widget_comms_subscribe_control', 300, 90);
    }
    else
      {
      add_action('wp_meta', 'comms_subscribe');
      }
  return;
  }
?>