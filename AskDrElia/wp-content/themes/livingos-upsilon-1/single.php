<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
 <div id="wrapper">
	<div id="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="dte"><?php the_time('M jS, Y') ?><span class="author"> by <?php the_author('nickname'); ?></span> | <span class="nocomments"><a href="<?php comments_link(); ?>"><?php comments_number('0', '1', '%', 'number');?></a></span> </div>
				
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
				
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>

				<?php if (function_exists('similar_posts')) { ?>
					<div class="related"><h3>Similar posts:</h3><?php similar_posts(); ?></div>
				<?php } ?>
				
				<div class="postmetadata"><?php edit_post_link('Edit', '', ' | '); ?> Filled under: <?php the_category(', ') ?> <?php if (function_exists('jkeywords_post_tags')) { ?>
					| Tags: <?php jkeywords_post_tags(); ?> <?php } ?> |
				You can follow any responses to this entry through the <?php comments_rss_link('RSS 2.0'); ?> feed.</div>
			</div>
		</div>	
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
		<p>Sorry, no posts matched your criteria.</p>
	
<?php endif; ?>
	</div>
	</div>
<?php include(TEMPLATEPATH."/sidebar1.php");?>
<?php get_footer(); ?>
