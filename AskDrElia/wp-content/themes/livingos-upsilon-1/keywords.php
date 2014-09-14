<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>

<div id="wrapper">
  <div id="content">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Entries Tagged with "<?php the_search_keytag(); ?>"</h2>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>


		<?php while (have_posts()) : the_post(); ?>
				
		<div class="post">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					
					<div class="dte"><?php the_time('M jS, Y') ?><span class="author"> by <?php the_author('nickname'); ?></span> | <span class="nocomments"><?php comments_popup_link('0', '1', '%'); ?></span> </div>
				
				<div class="entry">
					<?php the_excerpt() ?><a href="<?php the_permalink(); ?>">Read full post...</a>
				</div>
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>
	
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
		
	</div>
</div>
	<?php include(TEMPLATEPATH."/sidebar1.php");?>

<?php get_footer(); ?>