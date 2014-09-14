<?php
/*
Template Name: RS Event Page
*/
?>

<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
 <div id="wrapper">
	<div id="content">
    <div class="post archive">
    <h2><?php _e('Upcoming events:'); ?></h3>
    
    <ul>
      
      <?php rs_event_list(); ?>
      
    </ul>
    </div>
	</div>
</div>
<?php include(TEMPLATEPATH."/sidebar1.php");?>

<?php get_footer(); ?>