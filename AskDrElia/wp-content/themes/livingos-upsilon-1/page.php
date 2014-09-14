<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
<div id="wrapper">
  <div id="content">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php /* Lists any subpages*/ if(wp_list_pages("child_of=".$post->ID."&echo=0")) { ?> 
    <div id="pagenav"> 
      <ul>
        <?php wp_list_pages("title_li=&child_of=".$post->ID."&depth=1&sort_column=menu_order&show_date=modified&date_format=$date_format");?> 
      </ul>
    </div>
    <?php } ?> 
    <div class="post" id="post-<?php the_ID(); ?>">
				
      <h2><?php the_title(); ?></h2>
      <div class="entry">
        <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
	
        <?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
      </div>
      <?php if (function_exists('similar_posts')) { ?>
        <div class="related"><h3>Related posts:</h3><?php similar_posts(); ?></div>
      <?php } ?>
			
      <?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
    </div>
    <?php endwhile; endif; ?>
	
  </div>
</div>

<?php include(TEMPLATEPATH."/sidebar1.php");?>

<?php get_footer(); ?>