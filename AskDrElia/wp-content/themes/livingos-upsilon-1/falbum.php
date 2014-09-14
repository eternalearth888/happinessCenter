<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
 <div id="wrapper">
	<div id="content">
<!-- FAlbum Start -->

<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/falbum.js"></script>

<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/overlib.js"></script>

<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/prototype.js"></script>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

   <div id="content" class="narrowcolumn">

       <?php $falbum->show_photos(); ?>

   </div>

<!-- FAlbum End-->
</div></div>
<?php include(TEMPLATEPATH."/sidebar1.php");?>
	
<?php get_footer(); ?>