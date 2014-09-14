<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
 <div id="wrapper">
<div id="content" >
<div class="post archive">
	<h2>Archives</h2>
	<div class="archivemonth">
		<h3>by Month:</h3>
	  	<ul>
			<?php wp_get_archives('type=monthly'); ?>
	 	</ul>
	</div>
	<div class="archivesubject">
		<h3>by Subject:</h3>
	  	<ul>
		 	<?php // wp_list_cats('sort_column=name&hide_empty=0'); ?>
			<?php wp_list_categories('title_li=&sortby=name&hide_empty=1'); ?>
	  	</ul>
	</div>
	<div class="clear">&nbsp;</div>
	<div class="achiverecent">
		<h3>Most Recent Posts</h3>
		<ul>
			<?php wp_get_archives('type=postbypost&limit=30'); ?>
		</ul>
	</div>
</div></div>
</div>	
<?php include(TEMPLATEPATH."/sidebar1.php");?>
<?php get_footer(); ?>
