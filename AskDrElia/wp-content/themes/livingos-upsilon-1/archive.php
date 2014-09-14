<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
  <div id="wrapper">
    <div id="content">
      <?php if (have_posts()) : ?>

        <?php 
          $post = $posts[0]; // Hack. Set $post so that the_date() works. 
          $item = 'Entries';

          /* If this is a category archive... */ 
          if (is_category('17')) { 				
            $item = 'Products'; ?>
            <h2 class="pagetitle">Products from Dr. Elia</h2>

        <?php 
          /* If this is a category archive */ 
          } elseif (is_category('18')) {
            $item = 'Questions'; ?>
            <h2 class="pagetitle">Questions and Answers</h2>

        <?php 
          /* If this is a category archive */ 
          } elseif (is_category('3')) {
            $item = 'Articles'; ?>
            <h2 class="pagetitle">Articles</h2>

        <?php 
          /* If this is the testimonial archive */ 
          } elseif (is_category('40')) {
            $item = 'Testimonials'; ?>
            <h2 class="pagetitle">Testimonials</h2>

        <?php 
          /* If this is a generic category archive */ 
          } elseif (is_category()) { ?>				
          <h2 class="pagetitle">Archive for '<?php echo single_cat_title(); ?>'</h2>
		
        <?php 
          /* If this is a daily archive */
          } elseif (is_day()) { ?>
          <h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
		
        <?php 
          /* If this is a monthly archive */
          } elseif (is_month()) { ?>
          <h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>

        <?php 
          /* If this is a yearly archive */
          } elseif (is_year()) { ?>
          <h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
		
        <?php
          /* If this is a search */
          } elseif (is_search()) { ?>
          <h2 class="pagetitle">Search Results</h2>
		
        <?php
          /* If this is an author archive */ 
          } elseif (is_author()) { ?>
          <h2 class="pagetitle">Author Archive</h2>

        <?php 
          /* If this is a paged archive */ 
          } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
          <h2 class="pagetitle">Blog Archives</h2>

	<?php } ?>


        <div class="navigation">
          <div class="alignleft"><?php next_posts_link('&laquo; Previous '.$item ) ?></div>
          <div class="alignright"><?php previous_posts_link('Next '.$item.' &raquo;') ?></div>
        </div>

        <?php while (have_posts()) : the_post(); ?>
        <div class="post">
          <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
          <?php if (!in_category('17')) { ?>
          <div class="dte">
            <?php the_time('M jS, Y') ?>
            <span class="author"> by <?php the_author('nickname'); ?></span> | 
            <span class="nocomments"><?php comments_popup_link('0', '1', '%'); ?></span> 
          </div>
          <?php } else { echo do_shortcode('[gallery]'); } ?>	

          <div class="entry">
            <?php the_content() ?>
          </div>
        </div>
	
      <?php endwhile; ?>

      <div class="navigation">
        <div class="alignleft"><?php next_posts_link('&laquo; Previous '.$item ) ?></div>
        <div class="alignright"><?php previous_posts_link('Next '.$item.' &raquo;') ?></div>
      </div>
	
    <?php else : ?>

      <h2 class="center">Not Found</h2>
      <?php include (TEMPLATEPATH . '/searchform.php'); ?>

    <?php endif; ?>
		
  </div>
</div>

<?php include(TEMPLATEPATH."/sidebar1.php");?>

<?php get_footer(); ?>