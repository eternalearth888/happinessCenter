<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
  <div id="wrapper">
    <div id="content">
    <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
				
    <?php if ( !in_category('40') ): ?>
    <div class="post" id="post-<?php the_ID(); ?>">
      <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Read <?php the_title(); ?>"><?php the_title(); ?></a></h2>
      <div class="dte">
        <?php the_time('M jS, Y') ?>
        <span class="author"> by <?php the_author('nickname'); ?>
        </span> | 
        <span class="nocomments">
          <?php comments_popup_link('0', '1', '%'); ?>
        </span> 
      </div>
      <div class="entry">
        <?php the_content('continue reading &raquo; &raquo;'); ?>
      </div>
				
      <div class="postmetadata">
        <?php edit_post_link('Edit', '', ' | '); ?> Filed under: <?php the_category(', ') ?> 
        <?php if (function_exists('jkeywords_post_tags')) { ?>
        | Tags: <?php jkeywords_post_tags(); ?> <?php } ?> 
      </div>
    </div>
    <?php endif; ?>		
    <?php endwhile; ?>

    <div class="navigation">
      <div class="alignleft"><?php next_posts_link('&laquo; Previous Entry') ?></div>
      <div class="alignright"><?php previous_posts_link('Next Entry &raquo;') ?></div>
    </div>
		
    <?php else : ?>

      <h2 class="center">Not Found</h2>
      <p class="center">Sorry, but you are looking for something that isn't here.</p>
      <?php include (TEMPLATEPATH . "/searchform.php"); ?>

    <?php endif; ?>
	
  </div>
</div>

<?php include(TEMPLATEPATH."/sidebar1.php");?>
	
<?php get_footer(); ?>